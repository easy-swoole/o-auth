<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

namespace EasySwoole\OAuth;

use EasySwoole\HttpClient\HttpClient;

abstract class BaseOAuth
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public abstract function getAuthUrl();

    public function getAccessToken($storeState = null, $state = null, $code = null)
    {

        if (!$this->checkState($storeState, $state)) {
            throw new \InvalidArgumentException('state 验证失败');
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

    protected abstract function __getAccessToken($state = null, $code = null);

    public abstract function getUserInfo(string $accessToken);
}