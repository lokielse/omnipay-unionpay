<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\UnionPay\Common\DecryptHelper;

/**
 * Class WtzCompleteFrontOpenResponse
 * @package Omnipay\UnionPay\Message
 */
class WtzCompleteFrontOpenResponse extends AbstractResponse
{
    /**
     * @var WtzCompleteFrontOpenRequest
     */
    protected $request;


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data['verify_success'];
    }


    public function getCustomerInfo()
    {
        $cert = $this->request->getCertPath();
        $pass = $this->request->getCertPassword();

        return DecryptHelper::decrypt($this->data['customerInfo'], $cert, $pass);
    }


    public function getAccNo()
    {
        $acc = base64_decode($this->data['accNo']);

        return $this->decrypt($acc);
    }


    public function getToken()
    {
        return DecryptHelper::parse($this->data['tokenPayData']);
    }


    public function getOrderId()
    {
        return $this->data['orderId'];
    }


    protected function decrypt($payload)
    {
        $cert = $this->request->getCertPath();
        $pass = $this->request->getCertPassword();

        return DecryptHelper::decrypt($payload, $cert, $pass);
    }
}
