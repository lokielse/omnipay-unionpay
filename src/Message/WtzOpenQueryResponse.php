<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class WtzOpenQueryResponse
 * @package Omnipay\UnionPay\Message
 */
class WtzOpenQueryResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return $this->data['respCode'] == '00' && $this->data['verify_success'];
    }


    public function getActivateStatus()
    {
        return $this->data['activateStatus'];
    }


    public function getPayCardType()
    {
        return $this->data['payCardType'];
    }
}
