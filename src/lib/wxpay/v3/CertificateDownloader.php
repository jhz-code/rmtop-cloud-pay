<?php


namespace RmTop\RmPay\lib\wxpay\v3;


use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use RmTop\RmPay\core\TopPayConfig;
use RmTop\RmPay\core\TopWxPay;
use think\Exception;
use WechatPay\GuzzleMiddleware\Auth\CertificateVerifier;
use WechatPay\GuzzleMiddleware\Auth\WechatPay2Validator;
use WechatPay\GuzzleMiddleware\Util\AesUtil;

class CertificateDownloader extends PayClient
{

    /**
     * @throws Exception
     * @throws GuzzleException
     * 定时更新证书
     */
    function checkCertificates($data){
        $this->merchantId = $data['merchantId'] ;
        $this->merchantSerialNumber =$data['merchantSerialNumber']  ;
        $this->apiV3key = $data['apiV3key'] ;
        $this->payConfigId = $data['pay_config_id'];
        $client = $this->getClient(true);//第一次获取证书 初始化true  否则为空
        // 接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        $resp = $client->request('GET', 'https://api.mch.weixin.qq.com/v3/certificates', [
            'headers' => [ 'Accept' => 'application/json' ]
        ]);

        //请求失败 抛出异常
        if ($resp->getStatusCode() < 200 || $resp->getStatusCode() > 299) {
            //  echo "download failed, code={$resp->getStatusCode()}, body=[{$resp->getBody()}]\n";
            throw new Exception("failed, code={$resp->getStatusCode()}, body=[{$resp->getBody()}");
        }
        //获取返回的证书
        // return $resp->getBody();
        //解密证书
        $list = json_decode($resp->getBody(), true);
        $plainCerts = [];
        $x509Certs = [];
        $decrypter = new AesUtil($this->apiV3key);
        foreach ($list['data'] as $item) {
            $encCert = $item['encrypt_certificate'];
            $plain = $decrypter->decryptToString($encCert['associated_data'],
                $encCert['nonce'], $encCert['ciphertext']);
            if (!$plain) {
                // echo "encrypted certificate decrypt fail!\n";
                throw new Exception("encrypted certificate decrypt fail!");
                //exit(1);
            }
            // 通过加载对证书进行简单合法性检验
            $cert = \openssl_x509_read($plain); // 从字符串中加载证书
            if (!$cert) {
                throw new Exception("downloaded certificate check fail!\n");
                //echo "downloaded certificate check fail!\n";
                //exit(1);
            }
            $plainCerts[] = $plain;
            $x509Certs[] = $cert;
        }
        // 使用下载的证书再来验证一次应答的签名
        $validator = new WechatPay2Validator(new CertificateVerifier($x509Certs));
        if (!$validator->validate($resp)) {
            throw new Exception("validate response fail using downloaded certificates!");
            //echo "validate response fail using downloaded certificates!";
            //exit(1);
        }
        // 输出证书信息，并保存到文件
        foreach ($list['data'] as $index => $item) {
//            echo "Certificate {\n";
//            echo "    Serial Number: ".$item['serial_no']."\n";
//            echo "    Not Before: ".(new DateTime($item['effective_time']))->format('Y-m-d H:i:s')."\n";
//            echo "    Not After: ".(new DateTime($item['expire_time']))->format('Y-m-d H:i:s')."\n";
//            echo "    Text: \n    ".str_replace("\n", "\n    ", $plainCerts[$index])."\n";
//            echo "}\n";
            $outPath = dirname(__DIR__).'/prvate/'.'cert'.DIRECTORY_SEPARATOR.$item['serial_no'].'.pem';
            file_put_contents($outPath, $plainCerts[$index]);
            //更新证书编号
        }

        TopPayConfig::editConfigSerial_no($this->payConfigId,$item['serial_no']);

    }



}