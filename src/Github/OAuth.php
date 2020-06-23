<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\Github;


use EasySwoole\HttpClient\HttpClient;
use EasySwoole\OAuth\BaseOAuth;
use EasySwoole\OAuth\OAuthException;

class OAuth extends BaseOAuth
{
    const AUTH_DOMAIN = 'https://github.com';

    const API_DOMAIN = 'https://api.github.com';

    /** @var Config */
    protected $config;

    public function getAuthUrl()
    {
        $params = [
            'client_id' => $this->config->getClientId(),
            'redirect_uri' => $this->config->getRedirectUri(),
            'login' => $this->config->getLogin(),
            'scope' => $this->config->getScope(),
            'state' => $this->config->getState(),
            'allow_signup' => $this->config->getAllowSignUp(),
        ];
        return $this->getUrl(self::AUTH_DOMAIN . '/login/oauth/authorize', $params);
    }

    protected function __getAccessToken($state = null, $code = null)
    {
        $client = (new HttpClient(self::AUTH_DOMAIN . '/login/oauth/access_token'))->post([
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
            'code' => $code,
            'redirect_uri' => $this->config->getRedirectUri(),
            'state' => $state,
        ]);

        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取AccessToken失败！');

        parse_str($body, $result);
        $this->accessTokenResult = $result;

        if (isset($result['error'])) {
            throw new OAuthException($result['error']);
        }


        return $result['access_token'];
    }


    public function getUserInfo(string $accessToken)
    {
        $client = (new HttpClient(self::API_DOMAIN . '/user'))
            ->setHeader('Authorization', ' token ' . $accessToken)
            ->get();

        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取用户信息失败！');

        $result = \json_decode($body, true);

        if (isset($result['message'])) {
            throw new OAuthException($result['message']);
        }

        return $result;
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

    public function refreshToken(string $refreshToken = null)
    {
        return false;
    }
}