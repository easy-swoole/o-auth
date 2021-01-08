<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\AliPay;


use EasySwoole\HttpClient\HttpClient;
use EasySwoole\OAuth\BaseOAuth;
use EasySwoole\OAuth\OAuthException;
use Swoole\Coroutine\System;

class OAuth extends BaseOAuth
{

    const API_DOMAIN = 'https://openapi.alipay.com';
    const AUTH_DOMAIN = 'https://openauth.alipay.com';

    /** @var Config */
    protected $config;

    protected $openid;

    public function getAuthUrl()
    {
        $params = [
            'app_id' => $this->config->getAppId(),
            'scope' => $this->config->getScope(),
            'redirect_uri' => $this->config->getRedirectUri(),
            'state' => $this->config->getState()
        ];
        return $this->getUrl(self::AUTH_DOMAIN . '/oauth2/publicAppAuthorize.htm', $params);
    }

    protected function __getAccessToken($state = null, $code = null)
    {
        $params = [
            'app_id' => $this->config->getAppId(),
            'method' => 'alipay.system.oauth.token',
            'charset' => $this->config->getCharset(),
            'sign_type' => $this->config->getSignType(),
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'grant_type' => 'authorization_code',
            'code' => $code
        ];
        $params['sign'] = $this->sign($params);
        $client = (new HttpClient(self::API_DOMAIN . '/gateway.do'))
            ->setQuery($params)
            ->get();

        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取AccessToken失败！');

        $result = \json_decode($body, true);
        $this->accessTokenResult = $result;

        if (!isset($result['alipay_system_oauth_token_response']) && isset($result['error_response'])) {
            throw new OAuthException(sprintf('%s %s', $result['error_response']['msg'], $result['error_response']['sub_msg']), $result['error_response']['code']);
        }

        $responseData = $result['alipay_system_oauth_token_response'];
        if (isset($responseData['code'])) {
            throw new OAuthException(sprintf('%s %s', $responseData['msg'], $responseData['sub_msg']), $responseData['code']);
        }

        $this->openid = $responseData['user_id'];

        return $responseData['access_token'];
    }


    public function getUserInfo(string $accessToken)
    {
        $params = [
            'app_id' => $this->config->getAppId(),
            'method' => 'alipay.user.info.share',
            'charset' => $this->config->getCharset(),
            'sign_type' => $this->config->getSignType(),
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'auth_token' => $accessToken,
        ];
        $params['sign'] = $this->sign($params);
        $client = (new HttpClient(self::API_DOMAIN . '/gateway.do'))
            ->setQuery($params)
            ->get();

        $body = $client->getBody();
        if (!$body) throw new OAuthException('获取用户信息失败！');

        $result = \json_decode($body, true);

        if (!isset($result['alipay_user_info_share_response']) && isset($result['error_response'])) {
            throw new OAuthException(sprintf('%s %s', $result['error_response']['msg'], $result['error_response']['sub_msg']), $result['error_response']['code']);
        }

        $responseData = $result['alipay_user_info_share_response'];
        if (isset($responseData['code']) && 10000 != $responseData['code']) {
            throw new OAuthException(sprintf('%s %s', $responseData['msg'], $responseData['sub_msg']), $responseData['code']);
        }
        return $responseData;
    }

    private function sign(array $params)
    {
        $content = $this->parseSignData($params);

        if (!in_array($this->config->getSignType(), ['RSA', 'RSA2'])) {
            throw new OAuthException("未知的加密方式: {$this->config->getSignType()}");
        }

        if ($this->config->getSignType() == 'RSA') {
            $signType = OPENSSL_ALGO_SHA1;
        } else {
            $signType = OPENSSL_ALGO_SHA256;
        }

        if ($this->config->getAppPrivateKeyFile()) {
            $privateKey = System::readFile($this->config->getAppPrivateKeyFile());
        } else if ($this->config->getAppPrivateKey()) {
            $privateKey = $this->config->getAppPrivateKey();
        } else {
            throw new OAuthException('私钥文件不存在');
        }


        return $this->rsaSign($content, $privateKey, $signType);
    }

    private function parseSignData($params)
    {
        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        \ksort($params);
        $content = '';
        foreach ($params as $k => $v) {
            if ($v !== '' && $v !== null && !is_array($v)) {
                $content .= $k . '=' . $v . '&';
            }
        }
        return trim($content, '&');
    }

    public function refreshToken(string $refreshToken = null)
    {
        $params = [
            'app_id' => $this->config->getAppId(),
            'method' => 'alipay.system.oauth.token',
            'charset' => $this->config->getCharset(),
            'sign_type' => $this->config->getSignType(),
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        $params['sign'] = $this->sign($params);
        $client = (new HttpClient(self::API_DOMAIN . '/gateway.do'))
            ->setQuery($params)
            ->get();

        $body = $client->getBody();

        if (!$body) return false;
        $result = \json_decode($body, true);
        $this->refreshTokenResult = $result;

        if (!isset($result['alipay_system_oauth_token_response']) && isset($result['error_response'])) {
            throw new OAuthException(sprintf('%s %s', $result['error_response']['msg'], $result['error_response']['sub_msg']), $result['error_response']['code']);
        }

        $responseData = $result['alipay_system_oauth_token_response'];
        if (isset($responseData['code'])) {
            throw new OAuthException(sprintf('%s %s', $responseData['msg'], $responseData['sub_msg']), $responseData['code']);
        }

        $this->openid = $responseData['user_id'];

        return true;
    }

    public function validateAccessToken(string $accessToken)
    {
        try {
            $this->getUserInfo($accessToken);
            return true;
        } catch (OAuthException $exception) {
            return false;
        }
    }

    private function rsaSign($content, $privateKey, $signType = OPENSSL_ALGO_SHA256)
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

    /**
     * @param mixed $openid
     */
    public function setOpenid($openid): void
    {
        $this->openid = $openid;
    }
}
