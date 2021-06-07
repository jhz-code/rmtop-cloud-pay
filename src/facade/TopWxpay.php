<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/6/7
 * Time: 9:18 下午
 */
namespace RmTop\RmPay\facade;
use think\Facade;

/**
 * Class TopWxpay
 * @package RmTop\RmPay\facade
 *@method static  JsApi($data)  js支付
 *@method static  AppApi($data) app支付
 *@method static  H5Api($data)  h5支付
 *@method static  NativeApi($data) Native支付
 *@method static  queryRefunds($data)  h5支付
 */

class TopWxpay extends Facade
{


    protected static function getFacadeClass()
    {
        return 'RmTop\Rmpay\core\TopWxPay';
    }
}