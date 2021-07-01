# rmtop-cloud-pay

支付管理

云端支付sdk 

### 安装 

~~~
 composer require rmtop/rmsf-cloud-pay
~~~

### 发布支付文件 
~~~

 php think rmtop:publish_pay

 ~~~

### 数据迁移 创建支付相关配置文件

~~~
php think migrate:run
~~~

### 支付配置操作
~~~
 addConfig(array $data) //创建配置
 

editConfig(int $id, array $data)

deleteConfig(int $id)


getConfig(int $id) //获取配置 

~~~

#### 微信支付配置
~~~

$data['merchantId'] = '15196xxxx';
$data['merchantSerialNumber'] = '6A6E499099E0F19FE334Cxxxxxxxx';
$data['apiV3key'] = 'xN5nxxxxxxxxx;
TopPayConfig::addConfig($data);
 
 ~~~

## 微信V3版相关支付

##### jsApi支付 
~~~
$result =    TopWxPay::JsApi([
'configId'=>'1',
'appid'=>'wxc1ee20xxxxx',
'mchid'=>'1519xxxxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/admin/',
'payer'=>array('openid'=>'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o'),
]);
~~~

##### h5支付 

~~~
$result =    TopWxPay::H5Api([
'configId'=>'1',
'appid'=>'wxxxxxxxxxx7',
'mchid'=>'15196xxxxxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/',
'scene_info'=>array('payer_client_ip'=>'http://127.0.0.1:8000/admin/','h5_info'=>array('type'=>'wap')),
]);
~~~


##### app支付 
~~~
$result =    TopWxPay::AppApi([
'configId'=>'1',
'appid'=>'wxc1eexxxxxxxx',
'mchid'=>'1519xxxxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/',
]);
~~~

##### Native 支付 二维码支付 
~~~
 $result =    TopWxPay::NativeApi([
'configId'=>'1',
'appid'=>'wxc1ee20xxxx',
'mchid'=>'1519xxx',
'description'=>'支持测试',
'out_trade_no'=>time(),
'amount'=>array('total'=>100,'currency'=>'CNY'),
'notify_url'=>'http://127.0.0.1:8000/',
]);
~~~

#### 查询订单
~~~
queryByTransactions(string $transaction_id,string $mchid)

queryByOrderId(string $orderId,string $mchid)
~~~
####关闭订单
~~~
closeOrder(string $orderId,string $mchid)
~~~

#### 订单退款
~~~
queryRefundsOrder($out_refund_no)`
~~~

####申请交易账单API
~~~
queryTradeBill(string $bill_date,string $bill_type = 'ALL',string $tar_type = '')
~~~
#### 申请资金账单API

~~~
function fundFloBill(string $bill_date,string $account_type ='BASIC',string $tar_type='')`
~~~

#### 支付回调处理

如果能得到返回数据，则接受数据并验证成功，只需根据参数处理订单即可：
~~~
 $result =  (new PayClient())->Notify(intput());
if($result['out_trade_no']){
  //处理订单
}
exit(json(['code'=>'SUCCESS','message'=>'']));//通知微信支付网关
~~~ 

#### 退款回调处理

~~~
$result =  (new PayClient())->Notify(intput());

$result 为退款回调通知数据

~~~

## 支付宝


~~~ 
支付宝配置

$data['appId'] = '15196xxxx';
$data['merchantPrivateKey'] = '6A6E499099E0F19FE334Cxxxxxxxx';
$data['alipayPublicKey'] = 'xN5nxxxxxxxxx;
$data['notifyUrl'] = 'xN5nxxxxxxxxx;
$data['encryptKey'] = 'xN5nxxxxxxxxx;
TopPayConfig::addConfig($data);


~~~

~~~

支付宝 PC端扫码支付

$configId = 1;
$orderdes = '';
$order_sn = '';
$money = '';
$aliPay = new TopAlipay($configId);
$result =  $aliPay->appPay($orderdes,$order_sn,$money);
return $result;


支付宝 花呗支付
$aliPay = new TopAlipay(2);
$orderDes = '';
$order_sn = '';
$money = '';
$buyerId = '';
$extendParams = '';
$result =  $aliPay->huabeiPay($orderDes,$order_sn,$money,$buyerId,$extendParams);
return $result;


支付宝 扫条形码收款
$aliPay = new TopAlipay(2);
$orderDes = '';
$order_sn = '';
$money = '';
$authCode = '';
$result =  $aliPay->facePay($orderDes,$order_sn,$money,$authCode);
~~~
