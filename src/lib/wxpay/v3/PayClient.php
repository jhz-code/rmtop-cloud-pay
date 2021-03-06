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
use RmTop\RmPay\core\TopPayConfig;
use think\Exception;
use WechatPay\GuzzleMiddleware\Util\AesUtil;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

class PayClient
{

    protected $merchantId = ''; // 商户号;
    protected $merchantSerialNumber  = ''; // 商户API证书序列号
    protected $apiV3key = '';
    protected $merchantPrivateKey; //商户私钥;
    protected $wechatpayCertificate;  //微信支付平台证书;
    protected $payConfigId = ''; //支付配置ID
    protected $cerficateName='';//证书名称


    /**
     * 设置支付相关参数
     */
    function setParams($params){
        $this->merchantId = $params['pay_config']['merchantId'] ;
        $this->merchantSerialNumber =$params['pay_config']['merchantSerialNumber']  ;
        $this->apiV3key = $params['pay_config']['apiV3key'] ;
        $this->payConfigId = $params['pay_config_id'];
        $this->cerficateName = $params['pay_config']['serial_no'] ;
    }


    /**
     * 用Guzzle发起API请求
     * @param string $url
     * @param string $type
     * @param $params
     * @return array|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function requestParams(string $url,string $type,$params){
        // 接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        $this->setParams($params);
        unset($params['pay_config']);//删除配置项
        unset($params['pay_config_id']);//删除配置项
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
     * @return Client
     */
    function getClient($first = false): Client
    {
        // 构造一个WechatPayMiddleware
        $this->getMerchant();//获取证书
        $wechatpayMiddleware = WechatPayMiddleware::builder()->withMerchant($this->merchantId, $this->merchantSerialNumber, $this->merchantPrivateKey); // 传入商户相关配置
        if($first){
            //如果不是第一次，则调用证书开始验签
            $wechatpayMiddleware->withValidator(new NoopValidator); // 临时"跳过”应答签名的验证
        }else{
            $this->wechatpayCertificate =  PemUtil::loadCertificate(dirname(__DIR__).'/prvate/'.'cert'.DIRECTORY_SEPARATOR.$this->cerficateName.'.pem'); // 微信支付平台证书 ; // 商户私钥
            $wechatpayMiddleware->withWechatPay([$this->wechatpayCertificate]); // 可传入多个微信支付平台证书，参数类型为array
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


    /**
     * 支付回调
     * 订单回调数据处理
     * @param  $request
     * @return mixed
     * @throws Exception
     */
    function Notify(string $apiV3key,$request){
        $decrypted = new AesUtil($apiV3key);
        $resource = $request['resource']??"";
        if(!$resource)throw new Exception("not found");
        $plain = $decrypted->decryptToString(
            $resource['associated_data'],
            $resource['nonce'],
            $resource['ciphertext']
        );
        if (!$plain) {
            throw new Exception("校验失败!");
        }else{
            $result = json_decode($plain,true);
            if($result['trade_state']){
                return $result;  //返回支付成功的信息
                //   exit(json(['code'=>'SUCCESS','message'=>'']));
            }else{
                throw new Exception('单未支付成功,拒绝处理');
            }
        }

    }


    /**
     *  退款回调参数
     * @return mixed
     * @throws Exception
     */
    function refundNotify($request){
        $decrypted = new AesUtil($this->apiV3key);
        $resource = $request['resource'];
        $plain = $decrypted->decryptToString(
            $resource['associated_data'],
            $resource['nonce'],
            $resource['ciphertext']
        );
        if (!$plain) {
            throw new Exception("校验失败!");
        }else{
            return json_decode($plain,true);
        }
    }




}