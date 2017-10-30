<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\UnionPay\Common\CertUtil;
use Omnipay\UnionPay\Common\StringUtil;

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
        $customer = $this->parse(base64_decode($this->data['customerInfo']));

        if (isset($customer['encryptedInfo'])) {
            $encrypt = $customer['encryptedInfo'];
            unset($customer['encryptedInfo']);

            $data = base64_decode($encrypt);

            $decrypted = $this->decrypt($data);

            parse_str($decrypted, $parsed);

            $customer = array_merge($customer, $parsed);
        }

        return $customer;
    }


    public function getAccNo()
    {
        $acc = base64_decode($this->data['accNo']);

        return $this->decrypt($acc);
    }


    public function getToken()
    {
        return $this->parse($this->data['tokenPayData']);
    }


    public function getOrderId()
    {
        return $this->data['orderId'];
    }


    protected function decrypt($payload)
    {
        $cert       = $this->request->getCertPath();
        $pass       = $this->request->getCertPassword();
        $privateKey = CertUtil::readPrivateKeyFromCert($cert, $pass);
        openssl_private_decrypt($payload, $decrypted, $privateKey);

        return $decrypted;
    }


    protected function parse($payload)
    {
        $query = substr($payload, 1, strlen($payload) - 2);

        return StringUtil::parseFuckStr($query);
    }
}
