<?php

namespace Omnipay\UnionPay\Tests;

use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\UnionPay\ExpressGateway;
use Omnipay\UnionPay\Message\CreateOrderResponse;
use Omnipay\UnionPay\Message\ExpressPurchaseResponse;

class PPExpressGatewayTest extends GatewayTestCase
{

    /**
     * @var ExpressGateway $gateway
     */
    protected $gateway;

    protected $options;

    protected $merId = '123456789';

    protected $certId = '6860234080247374318';

    protected $privateKey = '/Assets/private_key.pem';

    protected $publicKey = '/Assets/verify_sign_acp.cer';


    public function setUp()
    {
        parent::setUp();
        $this->gateway = Omnipay::create('UnionPay_Express');
        $this->gateway->setMerId($this->merId);
        $this->gateway->setCertId($this->certId);
        $this->gateway->setPrivateKey(file_get_contents(__DIR__ . $this->privateKey));
        $this->gateway->setPublicKey(file_get_contents(__DIR__ . $this->publicKey));
        $this->gateway->setReturnUrl('http://example.com/return');
        $this->gateway->setNotifyUrl('http://example.com/notify');
    }


    public function testPurchase()
    {
        $order = array(
            'orderId' => date('YmdHis'), //Your order ID
            'txnTime' => date('YmdHis'), //Should be format 'YmdHis'
            'title'   => 'My order title', //Order Title
            'txnAmt'  => '100', //Order Total Fee
        );

        /**
         * @var ExpressPurchaseResponse $response
         */
        $response = $this->gateway->purchase($order)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertNotEmpty($response->getRedirectHtml());
    }


    public function testCreateOrder()
    {
        $order = array(
            'orderId' => date('YmdHis'), //Your order ID
            'txnTime' => date('YmdHis'), //Should be format 'YmdHis'
            'title'   => 'My order title', //Order Title
            'txnAmt'  => '100', //Order Total Fee
        );

        /**
         * @var CreateOrderResponse $response
         */
        $response = $this->gateway->createOrder($order)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }


    public function testCompletePurchase()
    {
        $options = array(
            'request_params' => array(
                'certId'    => '3474813271258769001041842579301293446',
                'signature' => 'xxxxxxx'
            ),
        );

        /**
         * @var ExpressPurchaseResponse $response
         */
        $response = $this->gateway->completePurchase($options)->send();
        $this->assertFalse($response->isSuccessful());
    }


    public function testQuery()
    {
        $options = array(
            'certId'  => '3474813271258769001041842579301293446',
            'orderId' => 'xxxxxxx',
            'txnTime' => date('YmdHis'),
            'txnAmt'  => '100',
        );

        /**
         * @var ExpressPurchaseResponse $response
         */
        $response = $this->gateway->query($options)->send();
        $this->assertFalse($response->isSuccessful());
    }


    public function testConsumeUndo()
    {
        $options = array(
            'certId'  => '3474813271258769001041842579301293446',
            'orderId' => 'xxxxxxx',
            'txnAmt'  => '100',
            'queryId' => 'XXXXX',
            'txnTime' => date('YmdHis'),
        );

        /**
         * @var ExpressPurchaseResponse $response
         */
        $response = $this->gateway->consumeUndo($options)->send();
        $this->assertFalse($response->isSuccessful());
    }


    public function testRefund()
    {
        $options = array(
            'certId'  => '3474813271258769001041842579301293446',
            'orderId' => '222222',
            'queryId' => '333333',
            'txnTime' => date('YmdHis'),
            'txnAmt'  => '100',
        );

        /**
         * @var ExpressPurchaseResponse $response
         */
        $response = $this->gateway->refund($options)->send();
        $this->assertFalse($response->isSuccessful());
    }


    public function testFileTransfer()
    {
        $options = array(
            'certId'     => '3474813271258769001041842579301293446',
            'txnTime'    => date('YmdHis'),
            'fileType'   => '00',
            'settleDate' => '0815',
        );

        /**
         * @var ExpressPurchaseResponse $response
         */
        $response = $this->gateway->fileTransfer($options)->send();
        $this->assertFalse($response->isSuccessful());
    }
}
