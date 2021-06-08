<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/6/7
 * Time: 9:20 下午
 */
namespace RmTop\RmPay\core;
use GuzzleHttp\Exception\GuzzleException;
use RmTop\RmPay\lib\wxpay\v3\Params;
use RmTop\RmPay\lib\wxpay\v3\PayClient;

class TopWxPay
{

    /**
     * 获取jsapi支付
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
   static   function JsApi($data)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
        return (new PayClient())->requestParams($url, 'post', self::makeParams($data));
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
        return (new PayClient())->requestParams($url, 'POST', self::makeParams($data));
    }


    /**
     * 获取H5支付返回参数
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
    static function H5Api($data)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';
        return (new PayClient())->requestParams($url, 'POST', self::makeParams($data));
    }


    /**
     * 获取原生支付
     * @param $data
     * @return array|void
     * @throws GuzzleException
     */
    static  function NativeApi($data)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';
        return (new PayClient())->requestParams($url, 'POST', self::makeParams($data));
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
     * @param array $data
     * @return array
     */
    static  function makeParams(array $data): array
    {
        $Params = new Params();
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




