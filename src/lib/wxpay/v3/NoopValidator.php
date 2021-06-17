<?php


namespace RmTop\RmPay\lib\wxpay\v3;


class NoopValidator implements \WechatPay\GuzzleMiddleware\Validator
{


    public function validate(\Psr\Http\Message\ResponseInterface $response)
    {
        return true;
    }


}