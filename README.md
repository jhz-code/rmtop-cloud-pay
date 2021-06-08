# rmtop-cloud-pay


支付管理

云端支付sdk




## 微信V3版相关支付

####jsApi支付 <br><br>

`$result =    TopWxPay::JsApi([
'appid'=>'wxc1ee20xxxxx',
'mchid'=>'1519xxxxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/admin/',
'payer'=>array('openid'=>'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o'),
]);
`

####h5支付 <br><br>

`$result =    TopWxPay::H5Api([
'appid'=>'wxxxxxxxxxx7',
'mchid'=>'15196xxxxxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/',
'scene_info'=>array('payer_client_ip'=>'http://127.0.0.1:8000/admin/','h5_info'=>array('type'=>'wap')),
]);`



#####app支付 <br><br>
`$result =    TopWxPay::AppApi([
'appid'=>'wxc1eexxxxxxxx',
'mchid'=>'1519xxxxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/',
]);`


##### Native 支付 二维码支付 <br><br>

`$result =    TopWxPay::NativeApi([
'appid'=>'wxc1ee20xxxx',
'mchid'=>'1519xxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/',
]);
`

#### 查询订单 
`queryByTransactions(string $transaction_id,string $mchid)
`

`queryByOrderId(string $orderId,string $mchid)`


####关闭订单

`closeOrder(string $orderId,string $mchid)`


#### 订单退款
`queryRefundsOrder($out_refund_no)`


####申请交易账单API
`queryTradeBill(string $bill_date,string $bill_type = 'ALL',string $tar_type = '')`

#### 申请资金账单API
`function fundFloBill(string $bill_date,string $account_type ='BASIC',string $tar_type='')`