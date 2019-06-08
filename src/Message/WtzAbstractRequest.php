<?php

namespace Omnipay\UnionPay\Message;

/**
 * Class AbstractRequest
 * @package Omnipay\UnionPay\Message
 */
abstract class WtzAbstractRequest extends AbstractRequest
{

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

    protected function encrypt($payload)
    {
        openssl_public_encrypt($payload, $encrypted, $this->getEncryptKey());

        return base64_encode($encrypted);
    }


    protected function getEncryptKey()
    {
        $cert = $this->getEncryptCert();

        if (is_file($cert)) {
            return file_get_contents($cert);
        } else {
            return $cert;
        }
    }
    
    protected function getPlainCustomerInfo()
    {
        $data = $this->getCustomerInfo();
        if (empty($data)) {
            return '';
        }
        return base64_encode("{" . urldecode(http_build_query($data)) . "}");
    }

    protected function getEncryptCustomerInfo()
    {
        $data = $this->getCustomerInfo();

        if (empty($data)) {
            return '';
        }

        $toEncrypt = array();
        $protect   = array('phoneNo', 'cvn2', 'expired', 'certifTp', 'certifId');

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
}
