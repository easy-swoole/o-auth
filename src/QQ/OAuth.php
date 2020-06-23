<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\QQ;


use EasySwoole\HttpClient\HttpClient;
use EasySwoole\OAuth\BaseOAuth;
use EasySwoole\OAuth\OAuthException;

class OAuth extends BaseOAuth
{
    const API_DOMAIN = 'https://graph.qq.com';

    /** @var Config */
    protected $config;

    protected $openId;

    public function getAuthUrl()
    {
        $params = [
            'response_type' => $this->config->getResponseType(),
            'client_id' => $this->config->getAppId(),
            'redirect_uri' => $this->config->getRedirectUri(),
            'state' => $this->config->getState(),
            'scope' => $this->config->getScope(),
            'display' => $this->config->getDisplay(),
        ];

        return $this->getUrl(self::API_DOMAIN . '/oauth2.0/authorize', $params);
    }

    protected function __getAccessToken($state = null, $code = null)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->config->getAppId(),
            'client_secret' => $this->config->getAppKey(),
            'code' => $code,
            'state' => $state,
            'redirect_uri' => $this->config->getRedirectUri()
        ];
        $client = (new HttpClient(self::API_DOMAIN . '/oauth2.0/token'))
            ->setQuery($params)
            ->get();

        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取AccessToken失败！');

        $responseData = $this->jsonp_decode($body, true);
        if ($responseData) {
            throw new OAuthException($responseData['error_description'], $responseData['error']);
        }

        parse_str($body, $result);
        $this->accessTokenResult = $result;

        if (isset($result['code']) && 0 != $result['code']) {
            throw new OAuthException($result['msg'], $result['code']);
        }

        return $result['access_token'];
    }

    public function validateAccessToken(string $accessToken)
    {
        try {
            $this->getOpenId($accessToken);
            return true;
        } catch (OAuthException $exception) {
            return false;
        }
    }

    public function refreshToken(string $refreshToken = null)
    {
        $params = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->config->getAppId(),
            'client_secret' => $this->config->getAppKey(),
            'refresh_token' => $refreshToken
        ];

        $client = (new HttpClient(self::API_DOMAIN . '/oauth2.0/token'))
            ->setQuery($params)
            ->get();

        $body = $client->getBody();
        if (!$body) return false;

        $responseData = $this->jsonp_decode($body, true);
        if ($responseData) {
            return false;
        }

        parse_str($body, $result);
        $this->refreshTokenResult = $result;

        return !isset($result['code']);
    }

    public function getUserInfo(string $accessToken)
    {
        if (!$this->openId) {
            $this->getOpenId($accessToken);
        }

        $params = [
            'access_token' => $accessToken,
            'oauth_consumer_key' => $this->config->getAppId(),
            'openid' => $this->openId,
        ];

        $client = (new HttpClient(self::API_DOMAIN . '/user/get_user_info'))
            ->setQuery($params)
            ->get();

        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取用户信息失败！');

        $result = \json_decode($body, true);

        if (isset($result['ret']) && 0 != $result['ret']) {
            throw new OAuthException($result['msg'], $result['ret']);
        }
        return $result;
    }

    public function getOpenId(string $accessToken)
    {
        $params = [
            'access_token' => $accessToken
        ];

        if ($this->config::UNION_ID == $this->config->getOpenIdMode()) {
            $params['unionid'] = $this->config->getOpenIdMode();
        }

        $client = (new HttpClient(self::API_DOMAIN . '/oauth2.0/me'))
            ->setQuery($params)
            ->get();


        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取OpenId失败！');

        $result = $this->jsonp_decode($body, true);

        if (isset($result['error'])) {
            throw new OAuthException($result['error_description'], $result['error']);
        }

        $this->openId = $result['openid'];

        if ($this->config::UNION_ID == $this->config->getOpenIdMode()) {
            return $result['unionid'];
        }

        return $this->openId;
    }

    /**
     * @param mixed $openId
     */
    public function setOpenId($openId): void
    {
        $this->openId = $openId;
    }

}