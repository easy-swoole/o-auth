<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\AliYun;


use EasySwoole\OAuth\OAuthException;

class Sign
{
    public function rsaSign($content, $privateKey, $signType = OPENSSL_ALGO_SHA256)
    {

        $search = [
            "-----BEGIN RSA PRIVATE KEY-----",
            "-----END RSA PRIVATE KEY-----",
            "\n",
            "\r",
            "\r\n"
        ];

        $privateKey = str_replace($search, "", $privateKey);
        $privateKey = $search[0] . PHP_EOL . wordwrap($privateKey, 64, "\n", true) . PHP_EOL . $search[1];
        $res = openssl_get_privatekey($privateKey);

        if (!$res) {
            throw new OAuthException('私钥格式有误!');
        }

        openssl_sign($content, $sign, $res, $signType);
        openssl_free_key($res);

        $sign = base64_encode($sign);
        return $sign;
    }
}