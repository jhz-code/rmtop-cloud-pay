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
     * 申请交易账单API
     * @return array|void
     * @throws GuzzleException
     */
    function queryTradeBill(){
        $url = "https://api.mch.weixin.qq.com/v3/bill/tradebill";
        $data['bill_date'] = '';
        $data['bill_type'] = '';
        $data['tar_type'] = '';
        return (new PayClient())->requestParams($url,'get',$data);
    }



    /**
     *  申请资金账单API
     * @return array|void
     * @throws GuzzleException
     */
    function fundFloBill(){
        $url = "https://api.mch.weixin.qq.com/v3/bill/fundflowbill";
        $data['bill_date'] = '';
        $data['account_type'] = '';
        $data['tar_type'] = '';
        return (new PayClient())->requestParams($url,'get',$data);
    }









}