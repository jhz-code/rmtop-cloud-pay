<?php


namespace Rmtop\Rmpay\lib\wxpay\v3;


use GuzzleHttp\Exception\GuzzleException;

class WxQuery
{


    /**
     *查询订单API
     * @param string $transaction_id //微信支付系统生成的订单号
     * @param string $mchid  //商户号
     * @return array|void
     * @throws GuzzleException
     */
    function queryByTransactions(string $transaction_id,string $mchid){
        $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/id/{$transaction_id}";
        return (new PayClient())->requestParams($url,'get',['mchid'=>$mchid]);
    }


    /**
     *查询订单API
     * @param string $orderId 商户订单号查询
     * @param string $mchid //商户号
     * @return array|void
     * @throws GuzzleException
     */
    function queryByOrderId(string $orderId,string $mchid){
        $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/{$orderId}";
        return (new PayClient())->requestParams($url,'get',['mchid'=>$mchid]);
    }


    /**
     * 关闭待支付的订单
     * @param string $orderId
     * @param string $mchid
     * @return array|void
     * @throws GuzzleException
     */
    function closeOrder(string $orderId,string $mchid){
        $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/{$orderId}/close";
        return (new PayClient())->requestParams($url,'POST',['mchid'=>$mchid]);
    }


    /**
     * 查询支付后的订单
     * @return array|void
     * @throws GuzzleException
     *
     */
    function queryRefundsOrder($out_refund_no){
        $url = "https://api.mch.weixin.qq.com/v3/refund/domestic/refunds/{$out_refund_no}";
        return (new PayClient())->requestParams($url,'get',[]);
    }


    /**
     *      * 申请交易账单API
     * @return array|void
     * @throws GuzzleException
     * @param string $bill_date 格式YYYY-MM-DD
    仅支持三个月内的账单下载申请。
    示例值：2019-06-11
     * @param string $bill_type
     *  不填则默认是ALL
    枚举值：
    ALL：返回当日所有订单信息（不含充值退款订单）
    SUCCESS：返回当日成功支付的订单（不含充值退款订单）
    REFUND：返回当日退款订单（不含充值退款订单）
     * @param string $tar_type
     *  不填则默认是数据流
    枚举值：
    GZIP：返回格式为.gzip的压缩包账单
     */
    function queryTradeBill(string $bill_date,string $bill_type,string $tar_type){
        $url = "https://api.mch.weixin.qq.com/v3/bill/tradebill";
        $data['bill_date'] =$bill_date;
        $data['bill_type'] = $bill_type;
        $data['tar_type'] = $tar_type;
        return (new PayClient())->requestParams($url,'get',$data);
    }



    /**
     * 申请资金账单API
     * @param string $bill_date  格式YYYY-MM-DD
     * @param string $account_type   不填则默认是BASIC
     枚举值：
     BASIC：基本账户
     OPERATION：运营账户
     FEES：手续费账户
     * @param string $tar_type
     *  不填则默认是数据流
    枚举值：
    GZIP：返回格式为.gzip的压缩包账单
     * @return array|void
     * @throws GuzzleException
     */
    function fundFloBill(string $bill_date,string $account_type,string $tar_type){
        $url = "https://api.mch.weixin.qq.com/v3/bill/fundflowbill";
        $data['bill_date'] = $bill_date;
        $data['account_type'] = $account_type;
        $data['tar_type'] = $tar_type;
        return (new PayClient())->requestParams($url,'get',$data);
    }




}