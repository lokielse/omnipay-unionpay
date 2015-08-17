<?php

namespace Omnipay\UnionPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\UnionPay\Helper;

/**
 * Buckaroo Purchase Response
 */
class ExpressPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

    public function isSuccessful()
    {
        return false;
    }


    public function isRedirect()
    {
        return true;
    }


    public function getRedirectUrl()
    {

    }


    public function getRedirectMethod()
    {
        return 'GET';
    }


    public function getRedirectData()
    {
        return $this->data;
    }


    public function getRedirectHtml()
    {
        $action = $this->getRequest()->getEndpoint('front');
        $fields = $this->getFormFields();

        $html = <<<eot
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body  onload="javascript:document.pay_form.submit();">
    <form id="pay_form" name="pay_form" action="{$action}" method="POST">
        {$fields}
    </form>
</body>
</html>
eot;

        return $html;
    }


    public function getFormFields()
    {
        $html = '';
        foreach ($this->data as $key => $value) {
            $html .= "<input type='hidden' name='{$key}' value='{$value}'/>\n";
        }

        return $html;
    }


    public function getTradeNo()
    {
        $endpoint = $this->getRequest()->getEndpoint('app');

        $result = Helper::sendHttpRequest($endpoint, $this->data);

        parse_str($result, $data);

        if (is_array($data) && isset( $data['tn'] )) {
            return $data['tn'];
        } else {
            return null;
        }
    }

}
