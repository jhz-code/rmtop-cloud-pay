<?php

namespace Rmtop\Rmpay\lib\wxpay\v3;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

class WxPay
{


    /**
     * 获取jsapi支付
     * @param $data
     * @return array|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function JsApi($data){
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
        return (new PayClient())->requestParams($url,'POST',self::makeParams($data));
    }


    /**
     * 获取App支付返回参数
     * @param $data
     * @return array|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function AppApi($data){
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';
        return (new PayClient())->requestParams($url,'POST',self::makeParams($data));
    }



    /**
     * 获取H5支付返回参数
     * @param $data
     * @return array|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function H5Api($data){
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';
        return (new PayClient())->requestParams($url,'POST',self::makeParams($data));
    }


    /**
     * 获取原生支付
     * @param $data
     * @return array|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function NativeApi($data){
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';
        return (new PayClient())->requestParams($url,'POST',self::makeParams($data));
    }


    /**
     * 申请退款
     * @return array|void
     * @throws GuzzleException
     */
    function queryRefunds($data){
        $url = "https://api.mch.weixin.qq.com/v3/refund/domestic/refunds";
        return (new PayClient())->requestParams($url,'POST',self::makeParams($data));
    }


    /**
     * @param array $data
     * @return array
     */
   static  function makeParams(array $data): array
    {
        $Params = new Params();
        $Params->setAppid('');
        $Params->setMchid('');
        $Params->setDescription('');
        $Params->setOutTradeNo('');
        $Params->setTimeExpire('');
        $Params->setAttach('');
        $Params->setNotifyUrl('');
        $Params->setGoodsTag('');
        $Params->setAmount([]);
        $Params->setPayer([]);
        $Params->setDetail([]);
        $Params->setSceneInfo([]);
        $Params->setSettleInfo([]);
        return $Params->getParams();
    }







}