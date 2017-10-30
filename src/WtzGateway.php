<?php

namespace Omnipay\UnionPay;

/**
 * Class WtzGateway
 * @package Omnipay\UnionPay
 */
class WtzGateway extends ExpressGateway
{

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'UnionPay_Wtz';
    }


    public function getDefaultParameters()
    {
        $params = parent::getDefaultParameters();

        $params['version'] = '5.1.0';

        return $params;
    }


    /**
     * @return mixed
     */
    public function getEncryptCert()
    {
        return $this->getParameter('encryptCert');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setEncryptCert($value)
    {
        return $this->setParameter('encryptCert', $value);
    }


    /**
     * @return mixed
     */
    public function getMiddleCert()
    {
        return $this->getParameter('middleCert');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setMiddleCert($value)
    {
        return $this->setParameter('middleCert', $value);
    }


    /**
     * @return mixed
     */
    public function getRootCert()
    {
        return $this->getParameter('rootCert');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setRootCert($value)
    {
        return $this->setParameter('rootCert', $value);
    }


    public function frontOpen(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\WtzFrontOpenRequest', $parameters);
    }


    public function completeFrontOpen(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\WtzCompleteFrontOpenRequest', $parameters);
    }


    public function openQuery(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\WtzOpenQueryRequest', $parameters);
    }


    public function smsConsume(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\WtzSmsConsumeRequest', $parameters);
    }
}
