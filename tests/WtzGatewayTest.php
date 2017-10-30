<?php

namespace Omnipay\UnionPay\Tests;

use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\UnionPay\WtzGateway;

class WtzGatewayTest extends GatewayTestCase
{

    /**
     * @var WtzGateway $gateway
     */
    protected $gateway;

    protected $options;


    public function setUp()
    {
        parent::setUp();
        $this->gateway = Omnipay::create('UnionPay_Wtz');
        $this->gateway->setMerId(UNIONPAY_MER_ID);
        $this->gateway->setEncryptCert(UNIONPAY_TWZ_ENCRYPT_CERT);
        $this->gateway->setMiddleCert(UNIONPAY_TWZ_MIDDLE_CERT);
        $this->gateway->setRootCert(UNIONPAY_TWZ_ROOT_CERT);
        $this->gateway->setCertPath(UNIONPAY_TWZ_SIGN_CERT);
        $this->gateway->setCertPassword(UNIONPAY_CERT_PASSWORD);
        $this->gateway->setReturnUrl('http://example.com/return');
        $this->gateway->setNotifyUrl('http://example.com/notify');
    }


    public function testFrontOpen()
    {
        date_default_timezone_set('PRC');

        $params = array(
            'orderId'      => date('YmdHis'),
            'txnTime'      => date('YmdHis'),
            'trId'         => '62000000001',
            'accNo'        => '6226090000000048',
            'customerInfo' => array(
                'phoneNo'    => '18100000000', //Phone Number
                'certifTp'   => '01', //ID Card
                'certifId'   => '510265790128303', //ID Card Number，15位身份证不校验尾号，18位会校验尾号，请务必在前端写好校验代码
                'customerNm' => '张三', // Name
                //'cvn2'       => '248', //cvn2
                //'expired'    => '1912', // format YYMM
            ),
            'payTimeout'   => date('YmdHis', strtotime('+15 minutes'))
        );

        /**
         * @var \Omnipay\UnionPay\Message\WtzFrontOpenResponse $response
         */
        $response = $this->gateway->frontOpen($params)->send();
        $form     = $response->getRedirectForm();
        $this->assertTrue($response->isSuccessful());
        $this->open($form);
    }
}
