<?php


namespace RmTop\RmPay\core;


use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use RmTop\RmPay\lib\alipay\AlipayPayClient;
use think\Exception;

class TopAlipay extends AlipayPayClient
{


    protected $aliClient;

    /**
     * AliPay constructor.
     */
    public function __construct()
    {
        $this->aliClient = $this->aliClient();
    }


    /**
     *
     * @param $orderDes //订单描述
     * @param $order_sn //订单号
     * @param $money //订单金额
     * @return string
     * @throws Exception
     */
    function appPay($orderDes,$order_sn,$money){
        try{
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
            $result = $this->aliClient::payment()->app()->pay($orderDes,$order_sn,$money);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {;
                return $result->body;
            } else {
                throw new Exception($result->msg."，".$result->subMsg);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * 支付宝 pc端
     * @param $orderDes
     * @param $order_sn
     * @param $money
     * @return string
     * @throws Exception
     */
    function pagePay($orderDes,$order_sn,$money){
        try{
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
            $result = $this->aliClient::payment()->page()->pay($orderDes,$order_sn,$money,'');
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {;
                return $result->body;
            } else {
                throw new Exception($result->msg."，".$result->subMsg);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * 面对面支付
     * @param $orderDes
     * @param $order_sn
     * @param $money
     * @param $authCode
     * @return mixed
     * @throws Exception
     */
    function facePay($orderDes,$order_sn,$money,$authCode){
        try{
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
            $result = $this->aliClient::payment()->faceToFace()->pay($orderDes,$order_sn,$money,$authCode);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {;
                return $result->body;
            } else {
                throw new Exception($result->msg."，".$result->subMsg);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * 花呗支付
     * @param $orderDes
     * @param $order_sn
     * @param $money
     * @param $buyerId
     * @param $extendParams
     * @return mixed
     * @throws Exception
     */
    function huabeiPay($orderDes,$order_sn,$money, $buyerId, $extendParams){
        try{
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
            $result = $this->aliClient::payment()->huabei()->create($orderDes,$order_sn,$money, $buyerId, $extendParams);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {;
                return $result->body;
            } else {
                throw new Exception($result->msg."，".$result->subMsg);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * @param $content
     * @return string
     * @throws \Exception
     * 支付回调
     */
    function notify($content){
        // Log::write(json_encode($content)."__________");
        // Log::write($this->aliClient::payment()->common()->verifyNotify($content)."***");
        unset($content['fuc']);
        if($this->aliClient::payment()->common()->verifyNotify($content)){
            //处理数据
            exit("success");
        }else{
            exit("fail");
        }
    }








}