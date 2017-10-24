<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class CreateOrderResponse
 *
 * @package Omnipay\UnionPay\Message
 * @throws  \Exception
 */
class CreateOrderResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        if ('00' !== $this->data['respCode']) {
            throw new \Exception(sprintf('Err: code %s, message %s', $this->data['respCode'], $this->data['respMsg']));
        }

        return isset($this->data['tn']);
    }


    public function getTradeNo()
    {
        return isset($this->data['tn']) ? $this->data['tn'] : null;
    }
}
