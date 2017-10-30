<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Class WtzFrontOpenRequest
 * @package Omnipay\UnionPay\Message
 */
class WtzFrontOpenRequest extends WtzAbstractRequest
{
    var $orderId;


    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('orderId', 'txnTime', 'accNo', 'customerInfo', 'payTimeout');

        $data = array(
            'version'       => $this->getVersion(),  //版本号
            'encoding'      => $this->getEncoding(),  //编码方式
            'certId'        => $this->getTheCertId(),    //证书ID
            'signMethod'    => $this->getSignMethod(),  //签名方法
            'txnType'       => '79',        //交易类型
            'txnSubType'    => '00',        //交易子类
            'bizType'       => '000902',    //业务类型
            'accessType'    => '0',         //接入类型
            'channelType'   => '07',        //渠道类型 05:语音 07:互联网 08:移动 $this->getChannelType()
            'encryptCertId' => $this->getTheEncryptCertId(),
            'merId'         => $this->getMerId(),     //商户代码
            'orderId'       => $this->getOrderId(),     //商户订单号，填写开通并支付交易的orderId
            'txnTime'       => $this->getTxnTime(),    //订单发送时间
            'tokenPayData'  => sprintf('{trId=%s&tokenType=01}', $this->getTrId()), //标记请求者 trId
            'accNo'         => $this->encrypt($this->getAccNo()), //银行卡号
            'customerInfo'  => $this->getEncryptCustomerInfo(), //标记请求者 trId,
            'frontUrl'      => $this->getReturnUrl(), //前台通知地址
            'backUrl'       => $this->getNotifyUrl(), //后台通知地址
            'payTimeout'    => $this->getPayTimeout(), //订单超时时间, 超过此时间后，除网银交易外，其他交易银联系统会拒绝受理，提示超时
        );

        $data = $this->filter($data);

        $data['signature'] = $this->sign($data, 'RSA2');

        var_dump(json_encode($data));

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
    public function getAccNo()
    {
        return $this->getParameter('accNo');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setAccNo($value)
    {
        return $this->setParameter('accNo', $value);
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
     * @return mixed
     */
    public function getCustomerInfo()
    {
        return $this->getParameter('customerInfo');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setCustomerInfo($value)
    {
        return $this->setParameter('customerInfo', $value);
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


    protected function getEncryptCustomerInfo()
    {
        $data = $this->getCustomerInfo();

        if (empty($data)) {
            return '';
        }

        $toEncrypt = array();
        $protect   = array('phoneNo', 'cvn2', 'expired');

        foreach ($data as $key => $value) {
            if (in_array($key, $protect)) {
                $toEncrypt[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        if (count($toEncrypt) > 0) {
            $payload               = urldecode(http_build_query($toEncrypt));
            $data['encryptedInfo'] = $this->encrypt($payload);
        }

        return base64_encode("{" . urldecode(http_build_query($data)) . "}");
    }


    protected function getTheEncryptCertId()
    {
        $x509data = $this->getEncryptKey();

        $certData = openssl_x509_parse($x509data);

        return $certData['serialNumber'];
    }
}
