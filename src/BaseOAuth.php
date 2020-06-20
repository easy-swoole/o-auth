<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

namespace EasySwoole\OAuth;

abstract class BaseOAuth
{
    protected $config;

    public function __construct(BaseConfig $config)
    {
        $this->config = $config;
    }

    protected function getUrl($url, $params = [])
    {
        return empty($params) ? $url : ($url . '?' . http_build_query($params));
    }

    public function getAccessToken($storeState = null, $state = null, $code = null)
    {

        if (!$this->checkState($storeState, $state)) {
            throw new \OAuthException('state 验证失败');
        }

        return $this->__getAccessToken($state, $code);

    }

    private function checkState($storeState = null, $state = null)
    {
        if (empty($storeState) && empty($state)) {
            return true;
        }

        if ($storeState != $state) {
            return false;
        }

        return true;
    }

    public abstract function getAuthUrl();

    protected abstract function __getAccessToken($state = null, $code = null);

    public abstract function getUserInfo(string $accessToken);

    public abstract function refreshToken(string $refreshToken = null);

    public abstract function validateAccessToken(string $accessToken);
}