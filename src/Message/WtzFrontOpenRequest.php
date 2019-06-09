<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\UnionPay\Common\CertUtil;

/**
 * Class WtzFrontOpenRequest
 * @package Omnipay\UnionPay\Message
 */
class WtzFrontOpenRequest extends WtzAbstractRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $bizType = $this->getBizType();
        $encryptSensitive = $this->getEncryptSensitive();
        $this->validate('orderId', 'txnTime', 'accNo');


        $data = array(
            'version'       => $this->getVersion(),  //版本号
            'encoding'      => $this->getEncoding(),  //编码方式
            'certId'        => $this->getTheCertId(),    //证书ID
            'signMethod'    => $this->getSignMethod(),  //签名方法
            'txnType'       => '79',        //交易类型
            'txnSubType'    => '00',        //交易子类
            'bizType'       => $bizType,    //业务类型
            'accessType'    => $this->getAccessType(),         //接入类型
            'channelType'   => $this->getChannelType(), //05:语音 07:互联网 08:移动
            'encryptCertId' => CertUtil::readX509CertId($this->getEncryptKey()),
            'merId'         => $this->getMerId(),     //商户代码
            'orderId'       => $this->getOrderId(),     //商户订单号，填写开通并支付交易的orderId
            'txnTime'       => $this->getTxnTime(),    //订单发送时间
            'accNo'         => $encryptSensitive ? $this->encrypt($this->getAccNo()) : $this->getAccNo(), //银行卡号
            'customerInfo'  => $encryptSensitive ?
                $this->getEncryptCustomerInfo() :
                $this->getPlainCustomerInfo(), //标记请求者 trId,
            'frontUrl'      => $this->getReturnUrl(), //前台通知地址
            'backUrl'       => $this->getNotifyUrl(), //后台通知地址
            'payTimeout'    => $this->getPayTimeout(), //订单超时时间
        );

        if ($bizType === '000902') {
            $data['tokenPayData'] = sprintf('{trId=%s&tokenType=01}', $this->getTrId()); //标记请求者 trId
        }

        if ($reserved = $this->getReserved()) {
            $data['reserved'] = $reserved;
        }

        $data = $this->filter($data);

        $data['signature'] = $this->sign($data, 'RSA2');

        return $data;
    }


    /**
     * @return mixed
     */
    public function getTrId()
    {
        return $this->getParameter('trId');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setTrId($value)
    {
        return $this->setParameter('trId', $value);
    }

    /**
     * @return mixed
     */
    public function getReserved()
    {
        return $this->getParameter('reserved');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setReserved($value)
    {
        return $this->setParameter('reserved', $value);
    }


    /**
     * @return mixed
     */
    public function getPayTimeout()
    {
        return $this->getParameter('payTimeout');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setPayTimeout($value)
    {
        return $this->setParameter('payTimeout', $value);
    }


    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     *
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this->response = new WtzFrontOpenResponse($this, $data);
    }
}
