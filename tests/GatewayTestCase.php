<?php

namespace Omnipay\UnionPay\Tests;

use Omnipay\Tests\GatewayTestCase as Base;
use Omnipay\UnionPay\WtzGateway;

class GatewayTestCase extends Base
{

    /**
     * @var WtzGateway $gateway
     */
    protected $gateway;

    protected $options;


    protected function open($content)
    {
        $file = sprintf('./%s.html', md5(uniqid()));
        $fh   = fopen($file, 'w');
        fwrite($fh, $content);
        fclose($fh);

        exec(sprintf('open %s -a "/Applications/Google Chrome.app" && rm %s', $file, $file));
    }
}
