<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\ResponseInterface;
use Omnipay\UnionPay\Common\Signer;

/**
 * Class WtzCompleteFrontOpenRequest
 * @package Omnipay\UnionPay\Message
 */
class WtzCompleteFrontOpenRequest extends WtzAbstractRequest
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
        $this->validate('request_params');

        return $this->getRequestParams();
    }


    /**
     * @return mixed
     */
    public function getRequestParams()
    {
        return $this->getParameter('request_params');
    }


    /**
     * @param $value
     *
     * @return $this
     */
    public function setRequestParams($value)
    {
        return $this->setParameter('request_params', $value);
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
        $data['verify_success'] = $this->verify($data);

        return $this->response = new WtzCompleteFrontOpenResponse($this, $data);
    }


    public function getIdentitiesFromCert($certInfo)
    {

        $cn      = $certInfo['subject'];
        $cn      = $cn['CN'];
        $company = explode('@', $cn);

        if (count($company) < 3) {
            return null;
        }

        return $company[2];
    }


    protected function verify($data)
    {
        $strCert = $data['signPubKeyCert'];

        openssl_x509_read($strCert);
        $certInfo = openssl_x509_parse($strCert);

        $cn    = $this->getIdentitiesFromCert($certInfo);
        $union = '中国银联股份有限公司';

        if ($this->getEnvironment() == 'sandbox') {
            if (! in_array($cn, array('00040000:SIGN', $cn))) {
                return false;
            }
        } else {
            if ($cn != $union) {
                return false;
            }
        }

        if ($data['respCode'] !== '00') {
            return false;
        }

        $from = date_create('@' . $certInfo ['validFrom_time_t']);
        $to   = date_create('@' . $certInfo ['validTo_time_t']);
        $now  = date_create(date('Ymd'));

        $interval1 = $from->diff($now);
        $interval2 = $now->diff($to);

        if ($interval1->invert || $interval2->invert) {
            return false;
        }

        $result = openssl_x509_checkpurpose(
            $strCert,
            X509_PURPOSE_ANY,
            array($this->getRootCert(), $this->getMiddleCert())
        );

        if ($result === true) {
            $signer = new Signer($data);
            $signer->setIgnores(array('signature'));

            $hashed    = hash('sha256', $signer->getPayload());
            $signature = base64_decode($data['signature']);

            $isSuccess = openssl_verify($hashed, $signature, $strCert, OPENSSL_ALGO_SHA256);

            return boolval($isSuccess);
        } else {
            return false;
        }
    }
}
