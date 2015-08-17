<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\AbstractResponse;

class ExpressResponse extends AbstractResponse
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return isset( $this->data['respCode'] );
    }


    public function isOK()
    {
        return $this->isSuccessful() && $this->data['respCode'] == '00';
    }
}
