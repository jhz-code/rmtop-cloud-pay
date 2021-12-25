<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/6/7
 * Time: 9:20 下午
 */
namespace RmTop\RmPay\core;
use app\rmcore\business\store\OrdersBusiness;
use app\rmcore\business\store\StoreBusiness;
use app\rmcore\business\TopSystemBusiness;
use app\rmcore\model\store\StoreOrders;
use app\rmcore\robot\local\OrdersQueue;
use GuzzleHttp\Exception\GuzzleException;
use RmTop\RmPay\lib\wxpay\v3\CertificateDownloader;
use RmTop\RmPay\lib\wxpay\v3\Params;
use RmTop\RmPay\lib\wxpay\v3\PayClient;
use think\Exception;
use think\facade\Queue;

class TopWxPay
{


//    /**
//     * 下载微信支付证书
//     * @param $data
//     * @throws Exception
//     * @throws GuzzleException
//     */
//    static function CertificateDownloader($data){
//        (new CertificateDownloader())->checkCertificates(self::makeParams($data));
//    }

    /**
     * 获取jsapi支付
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
    static   function JsApi($data)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
        $result = (new PayClient())->requestParams($url, 'post', self::makeParams($data));
        if($result['StatusCode'] == 200){
            $pay['appid'] = $data['appid'];
            $pay['noncestr'] = (new PayClient())->randCode();
            $pay['signType'] = "RSA";
            $pay['partnerid'] = $data['mchid'];
            $pay['prepayid'] = "prepay_id=".$result['Body']['prepay_id'];
            $pay['timestamp'] = (string)time();
            //app 支付签名
            $message = $pay['appid']."\n".$pay['timestamp']."\n".$pay['noncestr']."\n".$pay['prepayid']."\n";
            $pay['sign'] =  (new PayClient())->getSign($message);
            return $pay;
        }else{
            if($result['StatusCode'] == '400'){
                return 'repeat';
            }
            throw new Exception($result);
        }
    }


    /**
     * 获取App支付返回参数
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
    static function AppApi($data)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';
        $result = (new PayClient())->requestParams($url, 'POST', self::makeParams($data));
        if($result['StatusCode'] == 200){
            $pay['appid'] = $data['appid'];
            $pay['noncestr'] = (new PayClient())->randCode();
            $pay['package'] = "Sign=WXPay";
            $pay['partnerid'] = $data['mchid'];
            $pay['prepayid'] = $result['Body']['prepay_id'];
            $pay['timestamp'] = time();
            //app 支付签名
            $message = $pay['appid']."\n".$pay['timestamp']."\n".$pay['noncestr']."\n".$pay['prepayid']."\n";
            $pay['sign'] =  (new PayClient())->getSign($message);
            return $pay;
        }else{
            return $result;
        }
    }


    /**
     * 获取H5支付返回参数
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
    static function H5Api($data)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/h5';
        $result =  (new PayClient())->requestParams($url, 'POST', self::makeParams($data));
        if($result['StatusCode'] == 200){
            $pay['h5_url'] = $result['Body']['h5_url'];
            return $pay;
        }else{
            return $result;
        }
    }


    /**
     * 获取原生支付 二维码支付
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
    static  function NativeApi($data)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/native';
        $result = (new PayClient())->requestParams($url, 'POST', self::makeParams($data));
        if($result['StatusCode'] == 200){
            $pay['code_url'] = $result['Body']['code_url'];
            return $pay;
        }else{
            return $result;
        }
    }



    /**
     * 申请退款
     * @return array|void
     * @throws GuzzleException
     */
    static function queryRefunds($data)
    {
        $url = "https://api.mch.weixin.qq.com/v3/refund/domestic/refunds";
        return (new PayClient())->requestParams($url, 'POST', self::makeParams($data));
    }


    /**
     * 查询订单
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
    static function queryFindOrder($order_sn){
        $where[] = ['order_sn','=',$order_sn];
        $orderInfo = StoreOrders::where($where)->find();
        if(!$orderInfo)return true;//订单不存,无需轮询
        if($orderInfo['ispay'] == 1)return true;//已经支付，无需轮询
        $wx_pay_id = StoreBusiness::getStoreInfo($orderInfo['store_id'],'wx_pay_id')['wx_pay_id'];
        $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/{$order_sn}";
        $data['configId'] = $wx_pay_id;
        $data['mchid'] =TopPayConfig::getConfig($wx_pay_id)['merchantId'];
        $result = (new PayClient())->requestParams($url, 'get', self::makeParams($data));
        if($result['StatusCode'] == 200){
            if($result['Body']['trade_state'] == 'SUCCESS'){
                TopSystemBusiness::recordSystemRun(date("YmdHis"),'订单编号'.$order_sn."支付轮询-".$result['Body']['trade_state_desc'],json_encode($result['Body']),1);
                return OrdersBusiness::deskWxPaySuccess($order_sn,$result['Body']);//轮询成功，更新订单  返回true，中断轮询
            }
        }
    }



    /**
     * @param array $data
     * @return array
     */
    static  function makeParams(array $data): array
    {
        $Params = new Params();
        $Params->setConfigId($data['configId']);
        $Params->setAppid($data['appid'] ?? '');
        $Params->setMchid($data['mchid'] ?? '');
        $Params->setDescription($data['description'] ?? '');
        $Params->setOutTradeNo($data['out_trade_no'] ?? '');
        $Params->setTimeExpire($data['time_expire'] ?? '');
        $Params->setAttach($data['attach'] ?? '');
        $Params->setNotifyUrl($data['notify_url'] ?? '');
        $Params->setGoodsTag($data['goods_tag'] ?? '');
        $Params->setAmount($data['amount'] ??[]);
        $Params->setPayer($data['payer'] ?? []);
        $Params->setDetail($data['detail'] ??[]);
        $Params->setSceneInfo($data['scene_info'] ??[]);
        $Params->setSettleInfo($data['settle_Info'] ??[]);
        $Params->setTransactionId($data['transaction_id'] ?? '');
        $Params->setOutRefundNo($data['out_refund_no'] ?? '');
        $Params->setFundsReason($data['funds_reason'] ?? '');
        $Params->setFundsAccount($data['funds_account'] ?? '');
        $Params->setFundsGoodsDetail($data['funds_goods_detail'] ?? '');
        return array_filter($Params->getParams());
    }



}




