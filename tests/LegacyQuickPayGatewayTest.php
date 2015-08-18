<?php

namespace Omnipay\UnionPay;

use Omnipay\Alipay\Message\PurchaseResponse;
use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;

class LegacyQuickPayGatewayTest extends GatewayTestCase
{

    /**
     * @var LegacyMobileGateway $gateway
     */
    protected $gateway;

    protected $options;


    public function setUp()
    {
        parent::setUp();
        $this->gateway = Omnipay::create('UnionPay_LegacyQuickPay');
        $this->gateway->setMerId('123456789');
        $this->gateway->setSecretKey('xxxxxxx');
        $this->gateway->setReturnUrl('http://example.com/return');
        $this->gateway->setNotifyUrl('http://example.com/notify');
        $this->gateway->setEnvironment('production');

    }


    public function testPurchase()
    {
        $order = array (
            'orderNumber' => date('YmdHis'), //Your order ID
            'orderTime'   => date('YmdHis'), //Should be format 'YmdHis'
            'title'       => 'My order title', //Order Title
            'orderAmount' => '100', //Order Total Fee
        );

        /**
         * @var PurchaseResponse $response
         */
        $response = $this->gateway->purchase($order)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
    }


    public function testCompletePurchase()
    {
        $options = array (
            'request_params' => array (
                'certId'    => '3474813271258769001041842579301293446',
                'signature' => 'xxxxxxx'
            ),
        );

        /**
         * @var PurchaseResponse $response
         */
        $response = $this->gateway->completePurchase($options)->send();
        $this->assertFalse($response->isSuccessful());
    }
}
