<?php
namespace Omnipay\UnionPay;

class Helper
{

    public static function getCertId($certPath, $password)
    {
        $data = file_get_contents($certPath);
        openssl_pkcs12_read($data, $certs, $password);
        $x509data = $certs ['cert'];
        openssl_x509_read($x509data);
        $certData = openssl_x509_parse($x509data);

        return $certData['serialNumber'];
    }


    public static function getParamsSignature($params, $certPath, $password)
    {
        ksort($params);
        $query = http_build_query($params);
        $query = urldecode($query);

        $params_sha1x16 = sha1($query, false);
        $privateKey     = self::getPrivateKey($certPath, $password);
        openssl_sign($params_sha1x16, $signature, $privateKey, OPENSSL_ALGO_SHA1);

        return base64_encode($signature);
    }


    protected static function getPrivateKey($certPath, $password)
    {
        $data = file_get_contents($certPath);
        openssl_pkcs12_read($data, $certs, $password);

        return $certs['pkey'];
    }


    public static function verify($params, $certDir)
    {
        $publicKey        = self::getPublicKeyByCertId($params['certId'], $certDir);
        $requestSignature = $params ['signature'];
        unset($params['signature']);

        ksort($params);
        $query = http_build_query($params);
        $query = urldecode($query);

        $signature     = base64_decode($requestSignature);
        $paramsSha1x16 = sha1($query, false);
        $isSuccess     = openssl_verify($paramsSha1x16, $signature, $publicKey, OPENSSL_ALGO_SHA1);

        return $isSuccess;
    }


    public static function getPublicKeyByCertId($certId, $certDir)
    {
        $handle = opendir($certDir);
        if ($handle) {
            while ($file = readdir($handle)) {
                //clearstatcache();
                $filePath = rtrim($certDir, '/\\') . '/' . $file;
                if (is_file($filePath) && self::endsWith($filePath, '.cer')) {
                    if (self::getCertIdByCerPath($filePath) == $certId) {
                        closedir($handle);

                        return file_get_contents($filePath);
                    }
                }
            }
            throw new \Exception(sprintf('Can not find certId in certDir %s', $certDir));
        } else {
            throw new \Exception('certDir is not exists');
        }

    }


    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }


    protected static function getCertIdByCerPath($certPath)
    {
        $x509data = file_get_contents($certPath);
        openssl_x509_read($x509data);
        $certData = openssl_x509_parse($x509data);

        return $certData ['serialNumber'];
    }


    public static function sendHttpRequest($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/x-www-form-urlencoded;charset=UTF-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }


    public static function filterData($data)
    {
        $data = array_filter(
            $data,
            function ($v) {
                return $v !== '';
            }
        );

        return $data;
    }
}
