<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/6/8
 * Time: 12:54 上午
 */


namespace RmTop\RmPay\lib\wxpay\v3;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

class PayClient
{

    protected $merchantId = ''; // 商户号;
    protected $merchantSerialNumber  = ''; // 商户API证书序列号
    protected $apiV3key = '';
    protected $merchantPrivateKey; //商户私钥;
    protected $wechatpayCertificate;  //微信支付平台证书;


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
            $res['Body']=  json_decode($resp->getBody(),true);
            return $res;
        } catch (RequestException $e){
            // 进行错误处理
            if($e){
                $res['StatusCode']=$e->getResponse()->getStatusCode();
                $res['ReasonPhrase']=$e->getResponse()->getReasonPhrase();
                $res['Message']= $e->getMessage();
                return $res;
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
    private  function buildGetHttp(string $url,array $Params){
        $params = http_build_query($Params);
        $getUrl = $url."?".$params;
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
    private  function buildPostHttp(string $url,array $Params): \Psr\Http\Message\ResponseInterface
    {
        return   $this->getClient()->request('POST', $url, [
            'headers' => [ 'Accept' => 'application/json' ],
            'json' => $Params,
        ]);

    }



    /**
     * 加载证书
     */
    function getMerchant(){
        $this->merchantPrivateKey =    PemUtil::loadPrivateKey(dirname(__DIR__).'/prvate/'.'cert'.DIRECTORY_SEPARATOR.'apiclient_key.pem');//商户密钥
    }



    /**
     * @param false $first 是否是第一获取证书，true为是
     * @return Client
     */
    function getClient($first = false): Client
    {
        // 构造一个WechatPayMiddleware
        $this->getMerchant();//获取证书
        $wechatpayMiddleware = WechatPayMiddleware::builder()->withMerchant($this->merchantId, $this->merchantSerialNumber, $this->merchantPrivateKey); // 传入商户相关配置
        if(!$first){
            //如果不是第一次，则调用证书开始验签
            $this->wechatpayCertificate =  PemUtil::loadCertificate(dirname(__DIR__).'/prvate/'.'cert'.DIRECTORY_SEPARATOR.'wechatpay_327B4963545B0215E88749B65404A2E16148F82A.pem'); // 微信支付平台证书 ; // 商户私钥
            $wechatpayMiddleware->withWechatPay([$this->wechatpayCertificate]); // 可传入多个微信支付平台证书，参数类型为array
        }else{
            $wechatpayMiddleware->withValidator(new NoopValidator); // 临时"跳过”应答签名的验证
        }
        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = HandlerStack::create();
        $stack->push($wechatpayMiddleware->build(), 'wechatpay');
        // 创建Guzzle HTTP Client时，将HandlerStack传入
        return new Client(['handler' => $stack]);
    }




    //生成随机字符串
    function randCode(){
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
        $str = str_shuffle($str);
        $str = substr($str,0,32);
        return  $str;
    }


    /**
     * V3加密
     * @param $signParam //加密参数
     * @return string  //返回加密数据
     */
    function getSign($signParam){
        $this->getMerchant();
        openssl_sign($signParam, $raw_sign,$this->merchantPrivateKey, 'sha256WithRSAEncryption');
        return base64_encode($raw_sign);
    }



}