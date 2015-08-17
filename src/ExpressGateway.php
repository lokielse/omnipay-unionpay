<?php

namespace Omnipay\UnionPay;

use Omnipay\Common\AbstractGateway;

/**
 * Class ExpressGateway
 *
 * @package Omnipay\UnionPay
 */
class ExpressGateway extends AbstractGateway
{

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     */
    public function getName()
    {
        return 'UnionPay Express';
    }


    public function getDefaultParameters()
    {
        return [
            'version'        => '5.0.0',
            'encoding'       => 'utf-8',
            'txnType'        => '01',
            'txnSubType'     => '01',
            'bizType'        => '000201',
            'signMethod'     => '01',
            'channelType'    => '08', //07-PC，08-手机
            'accessType'     => '0',
            'currencyCode'   => '156',
            'orderDesc'      => 'an order',
            'extra'          => '',
            'defaultPayType' => '0001',
            'environment'    => 'sandbox',
        ];
    }


    public function setVersion($value)
    {
        $this->setParameter('version', $value);
    }


    public function getVersion()
    {
        return $this->getParameter('version');
    }


    public function setEncoding($value)
    {
        $this->setParameter('encoding', $value);
    }


    public function getEncoding()
    {
        return $this->getParameter('encoding');
    }


    public function setTxnType($value)
    {
        $this->setParameter('txnType', $value);
    }


    public function getTxnType()
    {
        return $this->getParameter('txnType');
    }


    public function setTxnSubType($value)
    {
        $this->setParameter('txnSubType', $value);
    }


    public function getTxnSubType()
    {
        return $this->getParameter('txnSubType');
    }


    public function setBizType($value)
    {
        $this->setParameter('bizType', $value);
    }


    public function getBizType()
    {
        return $this->getParameter('bizType');
    }


    public function setReturnUrl($value)
    {
        $this->setParameter('returnUrl', $value);
    }


    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }


    public function setNotifyUrl($value)
    {
        $this->setParameter('notifyUrl', $value);
    }


    public function getNotifyUrl()
    {
        return $this->getParameter('notifyUrl');
    }


    public function setSignMethod($value)
    {
        $this->setParameter('signMethod', $value);
    }


    public function getSignMethod()
    {
        return $this->getParameter('signMethod');
    }


    public function setChannelType($value)
    {
        $this->setParameter('channelType', $value);
    }


    public function getChannelType()
    {
        return $this->getParameter('channelType');
    }


    public function setAccessType($value)
    {
        $this->setParameter('accessType', $value);
    }


    public function getAccessType()
    {
        return $this->getParameter('accessType');
    }


    public function setMerId($value)
    {
        $this->setParameter('merId', $value);
    }


    public function getMerId()
    {
        return $this->getParameter('merId');
    }


    public function setCurrencyCode($value)
    {
        $this->setParameter('currencyCode', $value);
    }


    public function getCurrencyCode()
    {
        return $this->getParameter('currencyCode');
    }


    public function setEnvironment($value)
    {
        $this->setParameter('environment', $value);
    }


    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }


    public function setCertDir($value)
    {
        $this->setParameter('certDir', $value);
    }


    public function getCertDir()
    {
        return $this->getParameter('certDir');
    }


    public function setCertPath($value)
    {
        $this->setParameter('certPath', $value);
    }


    public function getCertPath()
    {
        return $this->getParameter('certPath');
    }


    public function setCertPassword($value)
    {
        $this->setParameter('certPassword', $value);
    }


    public function getCertPassword()
    {
        return $this->getParameter('certPassword');
    }


    public function purchase(array $parameters = [ ])
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\ExpressPurchaseRequest', $parameters);
    }


    public function completePurchase(array $parameters = [ ])
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\ExpressCompletePurchaseRequest', $parameters);
    }


    public function query(array $parameters = [ ])
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\ExpressQueryStatusPurchaseRequest', $parameters);
    }


    public function consumeUndo(array $parameters = [ ])
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\ExpressConsumeUndoRequest', $parameters);
    }


    public function refund(array $parameters = [ ])
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\ExpressRefundRequest', $parameters);
    }


    public function fileTransfer(array $parameters = [ ])
    {
        return $this->createRequest('\Omnipay\UnionPay\Message\ExpressFileTransferRequest', $parameters);
    }
}
