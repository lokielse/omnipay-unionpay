<?php

namespace Omnipay\UnionPay;

use Omnipay\Alipay\Message\PurchaseResponse;
use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;

class ExpressGatewayTest extends GatewayTestCase
{

    /**
     * @var ExpressGateway $gateway
     */
    protected $gateway;

    protected $options;


    public function setUp()
    {
        parent::setUp();
        $this->gateway = Omnipay::create('UnionPay_Express');
        $this->gateway->setMerId('123456789');
        $this->gateway->setCertDir(__DIR__ . '/Assets/'); // .pfx file
        $this->gateway->setCertPath(__DIR__ . '/Assets/PM_700000000000001_acp.pfx'); // .pfx file
        $this->gateway->setCertPassword('000000');
        $this->gateway->setReturnUrl('http://example.com/return');
        $this->gateway->setNotifyUrl('http://example.com/notify');

    }


    public function testPurchase()
    {
        $order = array (
            'orderId' => date('YmdHis'), //Your order ID
            'txnTime' => date('YmdHis'), //Should be format 'YmdHis'
            'title'   => 'My order title', //Order Title
            'txnAmt'  => '100', //Order Total Fee
        );

        /**
         * @var PurchaseResponse $response
         */
        $response = $this->gateway->purchase($order)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectHtml());
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


    public function testQuery()
    {
        $options = array (
            'certId'  => '3474813271258769001041842579301293446',
            'orderId' => 'xxxxxxx',
            'txnTime' => date('YmdHis'),
            'txnAmt'  => '100',
        );

        /**
         * @var PurchaseResponse $response
         */
        $response = $this->gateway->query($options)->send();
        $this->assertFalse($response->isSuccessful());
    }


    public function testConsumeUndo()
    {
        $options = array (
            'certId'  => '3474813271258769001041842579301293446',
            'orderId' => 'xxxxxxx',
            'txnAmt'  => '100',
            'queryId' => 'XXXXX',
            'txnTime' => date('YmdHis'),
        );

        /**
         * @var PurchaseResponse $response
         */
        $response = $this->gateway->consumeUndo($options)->send();
        $this->assertFalse($response->isSuccessful());
    }


    public function testRefund()
    {
        $options = array (
            'certId'  => '3474813271258769001041842579301293446',
            'orderId' => '222222',
            'queryId' => '333333',
            'txnTime' => date('YmdHis'),
            'txnAmt'  => '100',
        );

        /**
         * @var PurchaseResponse $response
         */
        $response = $this->gateway->refund($options)->send();
        $this->assertFalse($response->isSuccessful());
    }


    public function testFileTransfer()
    {
        $options = array (
            'certId'     => '3474813271258769001041842579301293446',
            'txnTime'    => date('YmdHis'),
            'fileType'   => '00',
            'settleDate' => '0815',
        );

        /**
         * @var PurchaseResponse $response
         */
        $response = $this->gateway->fileTransfer($options)->send();
        $this->assertFalse($response->isSuccessful());
    }
}
