<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\AliYun;


use EasySwoole\OAuth\BaseOAuth;

class OAuth extends BaseOAuth
{

    const API_DOMAIN = 'https://openapi.alipay.com';
    const AUTH_DOMAIN = 'https://openauth.alipay.com';

    /** @var Config */
    protected $config;

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
            'app_id'		=>	$this->config->getAppId(),
            'method'		=>	'alipay.system.oauth.token',
            'charset'		=>	$this->config->getCharset(),
            'sign_type'		=>	$this->config->getSignType(),
            'timestamp'		=>	date('Y-m-d H:i:s'),
            'version'		=>	'1.0',
            'grant_type'	=>  $this->config->getGrantType(),
            'code'			=>	$code
        ];


    }

    public function getUserInfo(string $accessToken)
    {
        // TODO: Implement getUserInfo() method.
    }
}