<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\WeiXin;


use EasySwoole\OAuth\BaseConfig;

class Config extends BaseConfig
{
    const OPEN_ID = 1;

    const UNION_ID = 2;

    protected $appId = '';

    protected $secret = '';

    protected $responseType = 'code';

    protected $scope = 'snsapi_login';

    protected $lang = 'zh_CN';

    protected $openIdMode = self::OPEN_ID;

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

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
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getResponseType(): string
    {
        return $this->responseType;
    }

    /**
     * @param string $responseType
     */
    public function setResponseType(string $responseType): void
    {
        $this->responseType = $responseType;
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
     * @return int
     */
    public function getOpenIdMode(): int
    {
        return $this->openIdMode;
    }

    /**
     * @param int $openIdMode
     */
    public function setOpenIdMode(int $openIdMode): void
    {
        $this->openIdMode = $openIdMode;
    }
}