<?php


namespace RmTop\RmPay\lib\alipay;


use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;

class AlipayPayClient
{


    /**
     * //设置参数（全局只需设置一次）
     * @return Config
     */
    function getOptions()
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';
        $options->appId = '';
        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = '';
        // $options->alipayCertPath = '';
        // $options->alipayRootCertPath = '';
        //  $options->merchantCertPath = '';
        //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
        $options->alipayPublicKey = '';
        //可设置异步通知接收服务地址（可选）
        $options->notifyUrl = "";
        //可设置AES密钥，调用AES加解密相关接口时需要（可选）
        $options->encryptKey = "";
        return $options;
    }


    /**
     * 初始化微信支付客户端
     * @return Factory
     */
    function aliClient(){
        return Factory::setOptions($this->getOptions());
    }



}