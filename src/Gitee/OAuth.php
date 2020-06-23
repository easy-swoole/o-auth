<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

namespace EasySwoole\OAuth\Gitee;

use EasySwoole\HttpClient\HttpClient;
use EasySwoole\OAuth\BaseOAuth;
use EasySwoole\OAuth\OAuthException;

class OAuth extends BaseOAuth
{
    const API_DOMAIN = 'https://gitee.com';

    /** @var Config */
    protected $config;

    public function getAuthUrl()
    {
        $params = [
            'client_id' => $this->config->getClientId(),
            'redirect_uri' => $this->config->getRedirectUri(),
            'state' => $this->config->getState(),
            'response_type' => $this->config->getResponseType(),
        ];
        return $this->getUrl(self::API_DOMAIN . '/oauth/authorize', $params);
    }

    public function getUserInfo(string $accessToken)
    {
        $client = (new HttpClient(self::API_DOMAIN . '/api/v5/user'))
            ->setQuery(['access_token' => $accessToken])
            ->get(['User-Agent' => '']);
        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取用户信息失败！');
        $result = \json_decode($body, true);

        if (!isset($result['id'])) {
            throw new OAuthException($result['message']);
        }

        return $result;
    }

    protected function __getAccessToken($state = null, $code = null)
    {
        $client = (new HttpClient(self::API_DOMAIN . '/oauth/token'))
            ->post([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => $this->config->getClientId(),
                'redirect_uri' => $this->config->getRedirectUri(),
                'client_secret' => $this->config->getClientSecret(),
            ], ['User-Agent' => '']);
        $body = $client->getBody();

        if (!$body) throw new OAuthException('获取AccessToken失败！');

        $result = \json_decode($body, true);
        $this->accessTokenResult = $result;

        if (isset($result['error'])) {
            throw new OAuthException($result['error_description']);
        }

        return $result['access_token'];
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