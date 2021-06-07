<?php
/**
 * Created by YnRmsf.
 * User: zhuok520@qq.com
 * Date: 2021/6/8
 * Time: 12:52 上午
 */


namespace RmTop\RmPay\lib\wxpay\v3;


class Params
{


    public string $appId = ''; //必填
    public string $mchid = '';  //必填
    public string $description = '';  //必填
    public string $out_trade_no = '';  //必填
    public string $time_expire = '';
    public string $attach = '';
    public string $notify_url = '';  //必填
    public string $goods_tag = '';
    public array  $amount = [];  //必填
    public array $payer = [];  //必填
    public array $detail = [];
    public array $scene_info = [];
    public array $settle_info = [];

    // -------------
    public string $transaction_id = '';
    public string $out_refund_no = '';
    public string $funds_reason = '';
    public string $funds_account = '';
    public  $funds_amount =[];
    public  $funds_goods_detail = [];


    /**
     * @return array
     * 获取提交参数
     */
    function getParams(): array
    {
        return  $params =  [
            'appid'=>$this->appId,
            'mchid'=>$this->mchid,
            'description'=>$this->description,
            'out_trade_no'=>$this->out_trade_no,
            'time_expire'=>$this->time_expire,
            'attach'=>$this->attach,
            'notify_url'=>$this->notify_url,
            'goods_tag'=>$this->goods_tag,
            'amount'=>$this->amount,
            'payer'=>$this->payer,
            'detail'=>$this->detail,
            'scene_info'=>$this->scene_info,
            'settle_info'=>$this->settle_info,
        ];
    }


    /**
     * 由微信生成的应用ID，全局唯一。请求基础下单接口时请注意APPID的应用属性，例如公众号场景下，需使用应用属性为公众号的APPID
     * @param string $appId
     */
    function setAppid(string $appId){
        $this->appId =$appId;
    }


    /**
     * 商品描述
    示例值：Image形象店-深圳腾大-QQ公仔
     * 直连商户的商户号，由微信支付生成并下发。
    示例值：1230000109
     * @param string $mchid
     */
    function setMchid(string $mchid){
        $this->mchid =$mchid;
    }


    /**
     * @param string $description
     *商品描述
    示例值：Image形象店-深圳腾大-QQ公仔
     */
    function setDescription(string $description){
        $this->description =$description;
    }


    /**
     * @param string $out_trade_no
     *  商户系统内部订单号，只能是数字、大小写字母_-*且在同一个商户号下唯一
    示例值：1217752501201407033233368018
     */
    function setOutTradeNo(string $out_trade_no){
        $this->out_trade_no =$out_trade_no;
    }

    /**
     * @param string $time_expire
     * 订单失效时间，遵循rfc3339标准格式，格式为YYYY-MM-DDTHH:mm:ss+TIMEZONE，YYYY-MM-DD表示年月日，T出现在字符串中，表示time元素的开头，HH:mm:ss表示时分秒，TIMEZONE表示时区（+08:00表示东八区时间，领先UTC 8小时，即北京时间）。例如：2015-05-20T13:29:35+08:00表示，北京时间2015年5月20日 13点29分35秒。
    示例值：2018-06-08T10:34:56+08:00
     */
    function setTimeExpire(string $time_expire){
        $this->time_expire =$time_expire;
    }

    /**
     * @param string $Attach
     * 附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用
    示例值：自定义数据
     */
    function setAttach(string $Attach){
        $this->attach =$Attach;
    }

    /**
     * @param string $notify_url
     * 通知URL必须为直接可访问的URL，不允许携带查询串，要求必须为https地址。
     */
    function setNotifyUrl(string $notify_url){
        $this->notify_url =$notify_url;
    }

    /**
     * @param string $goods_tag
     * 订单优惠标记 示例值：WXG
     */
    function setGoodsTag(string $goods_tag){
        $this->notify_url =$goods_tag;
    }

    /**
     * 订单金额信息
     * @param array $amount
     */
    function setAmount(array $amount){
        $this->amount = $amount;
    }

    /**
     * 支付者信息
     * @param array $payer
     */
    function setPayer(array $payer){
        $this->payer = $payer;
    }


    /**
     * @param array $detail
     * 优惠功能
     */
    function setDetail(array $detail){
        $this->detail = $detail;
    }


    /**
     * 支付场景描述
     * @param array $scene_info
     */
    function setSceneInfo(array $scene_info){
        $this->scene_info= $scene_info;
    }


    /**
     * 结算信息
     * @param array $settle_info
     */
    function setSettleInfo(array $settle_info){
        $this->settle_info= $settle_info;
    }




    /**
     * 原支付交易对应的微信订单号
     * 设置$transaction_id
     * @param $transaction_id
     */
    function setTransactionId($transaction_id){
        $this->transaction_id = $transaction_id;
    }


    /**
     * 原支付交易对应的商户订单号
     * @param string $out_refund_no
     */
    function setOutRefundNo(string $out_refund_no){
        $this->out_refund_no = $out_refund_no;
    }


    /**
     * 若商户传入，会在下发给用户的退款消息中体现退款原因
     * 设置退款理由
     * @param string $funds_reason
     */
    function setFundsReason(string $funds_reason){
        $this->funds_reason = $funds_reason;
    }


    /**
     * 若传递此参数则使用对应的资金账户退款，否则默认使用未结算资金退款（仅对老资金流商户适用）
     * @param $funds_account
     */
    function setFundsAccount($funds_account){
        $this->funds_account = $funds_account;
    }


    /**
     * 订单金额信息
     * @param $funds_amount
     */
    function setFundsAmount($funds_amount){
        $this->funds_amount = $funds_amount;
    }


    /**
     * 指定商品退款需要传此参数，其他场景无需传递
     * @param $funds_goods_detail
     */
    function setFundsGoodsDetail($funds_goods_detail){
        $this->funds_goods_detail = $funds_goods_detail;
    }


    /**
     *
     */
    function getRefundsParams(): array
    {
        return  $params =  [
            'transaction_id'=>$this->transaction_id,
            'out_trade_no'=>$this->out_trade_no,
            'out_refund_no	'=>$this->out_refund_no	,
            'reason'=>$this->funds_reason,
            'notify_url'=>$this->notify_url,
            'funds_account'=>$this->funds_account,
            'amount'=>$this->amount,
            'goods_detail'=>$this->funds_goods_detail,
        ];
    }



}