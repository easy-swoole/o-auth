<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\WeiXin;


use EasySwoole\HttpClient\HttpClient;
use EasySwoole\OAuth\BaseOAuth;
use EasySwoole\OAuth\OAuthException;

class OAuth extends BaseOAuth
{
    const API_DOMAIN = 'https://api.weixin.qq.com';

    const OPEN_DOMAIN = 'https://open.weixin.qq.com';

    /** @var Config */
    protected $config;

    protected $openId;

    public function getAuthUrl()
    {
        $params = [
            'appid' => $this->config->getAppId(),
            'redirect_uri' => $this->config->getRedirectUri(),
            'response_type' => $this->config->getResponseType(),
            'scope' => $this->config->getScope(),
            'state' => $this->config->getState(),
        ];
        return $this->getUrl(self::OPEN_DOMAIN . '/connect/qrconnect', $params) . '#wechat_redirect';
    }

    protected function __getAccessToken($state = null, $code = null)
    {
        $client = (new HttpClient(self::API_DOMAIN . '/sns/oauth2/access_token'))
            ->setQuery([
                'appid' => $this->config->getAppId(),
                'secret' => $this->config->getSecret(),
                'code' => $code,
                'grant_type' => $this->config->getGrantType(),
            ])->get();

        $body = $client->getBody();
        if (!$body) throw new OAuthException('获取AccessToken失败！');

        $result = \json_decode($body, true);

        if (isset($result['errcode']) && 0 != $result['errcode']) {
            throw new OAuthException($result['errmsg'], $result['errcode']);
        }

        $this->openId = $result['openid'];
        return $result['access_token'];
    }

    public function getUserInfo(string $accessToken)
    {
        $client = (new HttpClient(self::API_DOMAIN . '/sns/userinfo'))
            ->setQuery([
                'access_token' => $accessToken,
                'openid' => $this->openId,
                'lang' => $this->config->getLang(),
            ])
            ->get();

        $body = $client->getBody();
        if (!$body) throw new OAuthException('获取用户信息失败！');

        $result = \json_decode($body, true);

        if (isset($result['errcode']) && 0 != $result['errcode']) {
            throw new OAuthException($result['errmsg'], $result['errcode']);
        }

        return $result;
    }
}