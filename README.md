# Omnipay: UnionPay

**UnionPay driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/lokielse/omnipay-unionpay.png?branch=master)](https://travis-ci.org/lokielse/omnipay-unionpay)
[![Latest Stable Version](https://poser.pugx.org/lokielse/omnipay-unionpay/version.png)](https://packagist.org/packages/lokielse/omnipay-unionpay)
[![Total Downloads](https://poser.pugx.org/lokielse/omnipay-unionpay/d/total.png)](https://packagist.org/packages/lokielse/omnipay-unionpay)

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements UnionPay support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "lokielse/omnipay-unionpay": "dev-master"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:


* Union_Express (Union Express Checkout) 银联全产品网关（PC，APP，WAP支付）
* Union_LegacyMobile (Union Legacy Mobile Checkout) 银联老网关（APP）
* Union_LegacyQuickPay (Union Legacy QuickPay Checkout) 银联老网关（PC）

## Usage

Sandbox Param can be found at: [UnionPay Developer Center](https://open.unionpay.com/ajweb/account/testPara)

### Consume

```php
$gateway    = Omnipay::create('UnionPay_Express');
$gateway->setMerId($config['merId']);
$gateway->setCertPath($config['certPath']); // .pfx file
$gateway->setCertPassword($config['certPassword']);
$gateway->setReturnUrl($config['returnUrl']);
$gateway->setNotifyUrl($config['notifyUrl']);

$order = [
    'orderId'   => date('YmdHis'), //Your order ID
    'txnTime'   => date('YmdHis'), //Should be format 'YmdHis'
    'orderDesc' => 'My order title', //Order Title
    'txnAmt'    => '100', //Order Total Fee
];

$response = $gateway->purchase($order)->send();

$response->getRedirectHtml(); //For PC/Wap
$response->getTradeNo(); //For APP

```

### Return/Notify
```php
$gateway    = Omnipay::create('UnionPay_Express');
$gateway->setMerId($config['merId']);
$gateway->setCertDir($config['certDir']); //The directory contain *.cer files
$response = $gateway->completePurchase(['request_params'=>$_REQUEST])->send();
if ($response->isPaid()) {
    //pay success
}else{
    //pay fail
}
```

### Query Order Status
```php
$response = $gateway->Omnipay::queryStatus([
    'orderId' => '20150815121214', //Your site trade no, not union tn.
    'txnTime' => '20150815121214', //Order trade time
    'txnAmt'  => '200', //Order total fee
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
$response = $gateway->refund([
    'orderId' => '20150815121214', //Your site trade no, not union tn.
    'txnTime' => '20150815121214', //Order trade time
    'txnAmt'  => '200', //Order total fee
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
