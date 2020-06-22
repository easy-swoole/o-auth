<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\AliPay;


use EasySwoole\OAuth\BaseConfig;

class Config extends BaseConfig
{
    protected $appId = '';

    protected $scope = 'auth_user';

    protected $signType = 'RSA2';

    protected $charset = 'utf-8';

    protected $appPrivateKeyFile = '';

    protected $appPrivateKey = '';

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     */
    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getSignType(): string
    {
        return $this->signType;
    }

    /**
     * @param string $signType
     */
    public function setSignType(string $signType): void
    {
        $this->signType = $signType;
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     */
    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    /**
     * @return string
     */
    public function getAppPrivateKeyFile(): string
    {
        return $this->appPrivateKeyFile;
    }

    /**
     * @param string $appPrivateKeyFile
     */
    public function setAppPrivateKeyFile(string $appPrivateKeyFile): void
    {
        $this->appPrivateKeyFile = $appPrivateKeyFile;
    }

    /**
     * @return string
     */
    public function getAppPrivateKey(): string
    {
        return $this->appPrivateKey;
    }

    /**
     * @param string $appPrivateKey
     */
    public function setAppPrivateKey(string $appPrivateKey): void
    {
        $this->appPrivateKey = $appPrivateKey;
    }

}