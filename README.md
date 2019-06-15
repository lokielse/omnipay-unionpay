# Omnipay: UnionPay

在 `[lokielse/omnipay-unionpay](https://github.com/lokielse/omnipay-unionpay)` 的基础上更新

[![Build Status](https://travis-ci.org/kongkx/omnipay-unionpay.png?branch=master)](https://travis-ci.org/kongkx/omnipay-unionpay)
[![Latest Stable Version](https://poser.pugx.org/kongkx/omnipay-unionpay/version.png)](https://packagist.org/packages/kongkx/omnipay-unionpay)
[![Total Downloads](https://poser.pugx.org/kongkx/omnipay-unionpay/d/total.png)](https://packagist.org/packages/kongkx/omnipay-unionpay)

**UnionPay driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 7.1+. This package implements UnionPay support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
  "require": {
    "kongkx/omnipay-unionpay": "^0.4"
  }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

- Union_Wtz (Union No Redirect Payment) 银联无跳转支付（alpha）
- Union_Express (Union Express Payment) 银联全产品网关（PC，APP，WAP 支付）
- Union_LegacyMobile (Union Legacy Mobile Payment) 银联老网关（APP）
- Union_LegacyQuickPay (Union Legacy QuickPay Payment) 银联老网关（PC）

## Express Gateway Usage

### Usage

Sandbox Param can be found at: [UnionPay Developer Center](https://open.unionpay.com/ajweb/account/testPara)

### Prepare

How to get `PrivateKey`, `PublicKey`, `Cert ID`:

```
0. Prepare cert.pfx and its password, verify_sign_acp.cer

1. Get Private Key
$ openssl pkcs12 -in cert.pfx  -nocerts -nodes | openssl rsa -out private_key.pem

2. Public key is verify_sign_acp.cer

3. Get Cert ID
$ openssl pkcs12 -in cert.pfx -clcerts -nokeys | openssl x509 -serial -noout // result hex eg: XXXXXXXXXX
$ visit https://lokielse.github.io/hex2dec //Convert hex to decimal online
```

### Consume

```php
$gateway    = Omnipay::create('UnionPay_Express');
$gateway->setMerId($config['merId']);
$gateway->setCertId($config['certId']);
$gateway->setPrivateKey($config['privateKey']); // path or content
$gateway->setReturnUrl($config['returnUrl']);
$gateway->setNotifyUrl($config['notifyUrl']);

$order = [
    'orderId'   => date('YmdHis'), //Your order ID
    'txnTime'   => date('YmdHis'), //Should be format 'YmdHis'
    'orderDesc' => 'My order title', //Order Title
    'txnAmt'    => '100', //Order Total Fee
];

//For PC/Wap
$response = $gateway->purchase($order)->send();
$response->getRedirectHtml();

//For APP
$response = $gateway->createOrder($order)->send();
$response->getTradeNo();

```

### Return/Notify

Handle Return/Notification from UnionPay.

```php
$gateway    = Omnipay::create('UnionPay_Express');
$gateway->setMerId($config['merId']);
$gateway->setPublicKey($config['publicKey']); // path or content

$response = $gateway->completePurchase(['request_params'=>$_REQUEST])->send();

if ($response->isPaid()) {
    //pay success
}else{
    //pay fail
}
```

### Query Order Status

```php
$response = $gateway->query([
    'orderId' => '20150815121214', //Your site trade no, not union tn.
    'txnTime' => '20150815121214', //Order trade time
    'txnAmt'  => '200', //Order total fee (cent)
])->send();

var_dump($response->isSuccessful());
var_dump($response->getData());
```

### Consume Undo

```php
$response = $gateway->consumeUndo([
    'orderId' => '20150815121214', //Your site trade no, not union tn.
    'txnTime' => date('YmdHis'), //Regenerate a new time
    'txnAmt'  => '200', //Order total fee
    'queryId' => 'xxxxxxxxx', //Order total fee
])->send();

var_dump($response->isSuccessful());
var_dump($response->getData());
```

### Refund

```php
// 注意：
1. 银联退款时，必须加上 queryId,
2. 作为商户生成的订单号orderId与退款时的订单号是不一样的。也就意味着退款时的订单号必须重新生成。
3. txnAmt 这个参数银联是精确到分的。直接返回元为单位的值，将会出现报错信息。
// get the queryId first
$response = $gateway->query([
    'orderId' => '20150815121214', //Your site trade no, not union tn.
    'txnTime' => '20150815121214', //Order trade time
    'txnAmt'  => 200 * 100, //Order total fee; notice that: you should multiply the txnAmt by 100 with the Unionpay gateway. Such as 200 * 100;
])->send();
$queryId = ($response->getData())['queryId'];
$response = $gateway->refund([
    'orderId' => '20150815121214', //Your site trade no, not union tn. notice: this orderId must not be the same with the order's created orderId.
    'txnTime' => date('YmdHis'), //Order trade time
    'txnAmt'  => 200 * 100, //Order total fee; notice that: you should multiply the txnAmt by 100 with the Unionpay gateway. Such as 200 * 100;
    'queryId' => $queryId
])->send();

var_dump($response->isSuccessful());
var_dump($response->getData());
```

### File Transfer

```php
$response = $gateway->fileTransfer([
    'txnTime'    => '20150815121214', //Order trade time
    'settleDate' => '0119', //Settle Date
    'fileType'   => '00', //File Type
])->send();

var_dump($response->isSuccessful());
var_dump($response->getData());
```

For general usage instructions, please see the main [Omnipay](https://github.com/omnipay/omnipay)
repository.

## Wtz Gateway

Examples below work with wtz standard version.

**Note: You may checkout test cases for usage refrenece. 你可以通过查阅测试用例，了解接口之间的依赖关系**

### Init Gateway

```php
$config = config('services.unionpay');

$gateway = Omnipay::create('UnionPay_Wtz');

$gateway->setMerId($config['merId']);
$gateway->setBizType('000301'); // 000301: Standard Version; 000902: Token Version;
$gateway->setEncryptSensitive(true); // base on merchance config;

// Sandbox
$gateway->setCertPassword($config['signPassword']);
$gateway->setCertPath($config['signPfx']);

// production
// $gateway->setCertId($config['certId']);
// $gateway->setPrivateKey($config['privateKey']);
// $gateway->setEnvironment('production');

$gateway->setEncryptCert($config['encryptKey']);
$gateway->setRootCert($config['rootKey']);
$gateway->setMiddleCert($config['middleKey']);

return $gateway;
```

### Open Query

```php
// 通过账号查询
$params = array(
    'orderId' => date('YmdHis'),
    'txnTime' => date('YmdHis'),
    'txnSubType' => '00',
    'accNo'  => '6226090000000048',
);

$response = $this->gateway->openQuery($params)->send();
$customerInfo = $response->getCustomerInfo(); // 包含 phoneNo，可用于请求消费短信
var_dump($response);
var_dump($customerInfo);
```

### Front Open

```php
// 获取模版
$params = array(
    'orderId'    => date('YmdHis'),
    'txnTime'    => date('YmdHis'),
    'accNo'      => '6226090000000048',
    'payTimeout' => date('YmdHis', strtotime('+15 minutes'))
);
$request = $this->gateway->frontOpen($params)
$response = $request->send();
$form = $response->getRedirectForm();
var_dump($response->getData());
var_dump($form);
```

### Sms Consume

```php
$params = array(
    'orderId' => date('YmdHis'),
    'txnTime' => date('YmdHis'),
    'txnAmt'  => 100,
    'accNo'   => '6226388000000095',
    'customerInfo' => [
        'phoneNo' => '18100000000',
    ]
);

$request = $this->gateway->smsConsume($params)
$response = $request->send();
var_dump($request->getData());
var_dump($response->getData());
```

### Consume

```php
$params = array(
    'orderId' => date('YmdHis'),
    'txnTime' => date('YmdHis'),
    'txnAmt'  => 100,
    'accNo'   => '6226388000000095',
    'customerInfo' => [
        'smsCode' => '111111',  // 除了123456和654321固定反失败，其余固定成功。
        // 'smsCode' => '123456',
    ]
);

$request = $this->gateway->consume($params);
$response = $request->send();

var_dump($request->getData());
var_dump($response->getData());
```

### Query

```php
$params = array(
    'orderId' => $preData['orderId'],
    'txnTime' => $preData['txnTime'],
);
$request = $this->gateway->query($params);
$response = $request->send();

var_dump($request->getData());
var_dump($response->getData());
```

### Refund

```php
$params = array(
    'orderId'   => date('YmdHis'), // 全新订单号
    'txnTime'   => date('YmdHis'),
    'origQryId' => $origQryId, // 查询/消费接口 返回的 queryId
    'txnAmt'    => 100,
);

$request = $this->gateway->refund($params)
$response = $request->send();

var_dump($request->getData());
var_dump($response->getData());

```

## Related

- [Laravel-Omnipay](https://github.com/ignited/laravel-omnipay)
- [Omnipay-Alipay](https://github.com/lokielse/omnipay-alipay)
- [Omnipay-WechatPay](https://github.com/lokielse/omnipay-wechatpay)

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/lokielse/omnipay-unionpay/issues),
or better yet, fork the library and submit a pull request.

## About Test

- `WtzTokenGatewayTest` only test request data is received by unionpay gateway with no "6100030" (报文格式错误) error code in response.
