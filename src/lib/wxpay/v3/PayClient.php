<?php


namespace Rmtop\Rmpay\lib\wxpay\v3;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use think\Exception;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

class PayClient
{


    /**
     * 用Guzzle发起API请求
     * @param string $url
     * @param string $type
     * @param $params
     * @return array|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function requestParams(string $url,string $type = 'post',$params){
// 接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        try{
             if($type == "get"){
                 $resp =  $this->buildGetHttp($url,$params);
             }else{
                 $resp =  $this->buildPostHttp($url,$params);
             }
            $res['StatusCode']= $resp->getStatusCode();
            $res['ReasonPhrase']= $resp->getReasonPhrase();
            $res['Body']= $resp->getBody();
            return $res;
        } catch (RequestException $e){
            // 进行错误处理
            echo $e->getMessage()."\n";
            if ($e->hasResponse()) {
                echo $e->getResponse()->getStatusCode().' '.$e->getResponse()->getReasonPhrase()."\n";
                echo $e->getResponse()->getBody();
            }
            return;
        }
    }

    /**
     * 执行get请求
     * @param string $url
     * @param array $Params
     * @return array|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function buildGetHttp(string $url,array $Params){
            $params = http_build_query($Params);
            $getUrl = $url.$params;
             return $this->getClient()->request('GET', $getUrl, [
                'headers' => [ 'Accept' => 'application/json' ]
            ]);
    }


    /**
     * post 请求
     * @param string $url
     * @param array $Params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function buildPostHttp(string $url,array $Params): \Psr\Http\Message\ResponseInterface
    {
         return   $this->getClient()->request('POST', $url, [
                'headers' => [ 'Accept' => 'application/json' ],
                'json' => [json_encode($Params) ],
            ]);


    }


    /**
     * @return Client
     */
    function getClient(){
        // 商户相关配置
        $merchantId = '1000100'; // 商户号
        $merchantSerialNumber = 'XXXXXXXXXX'; // 商户API证书序列号
        $merchantPrivateKey = PemUtil::loadPrivateKey('/path/to/mch/private/key.pem'); // 商户私钥
        // 微信支付平台配置
        $wechatpayCertificate = PemUtil::loadCertificate('/path/to/wechatpay/cert.pem'); // 微信支付平台证书
        // 构造一个WechatPayMiddleware
        $wechatpayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
            ->withWechatPay([ $wechatpayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
            ->build();
// 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');
        return new Client(['handler' =>$stack]);
    }



}