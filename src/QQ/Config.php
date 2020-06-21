<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\OAuth\QQ;


use EasySwoole\OAuth\BaseConfig;

class Config extends BaseConfig
{
    const OPEN_ID = 1;

    const UNION_ID = 2;

    protected $appId = '';

    protected $appKey = '';

    protected $responseType = 'code';

    protected $scope = '';

    protected $display = '';

    protected $openIdMode = self::OPEN_ID;

    /**
     * @return string
     */
    public function getAppId()
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
     * @return string
     */
    public function getDisplay(): string
    {
        return $this->display;
    }

    /**
     * @param string $display
     */
    public function setDisplay(string $display): void
    {
        $this->display = $display;
    }

    /**
     * @return string
     */
    public function getAppKey(): string
    {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     */
    public function setAppKey(string $appKey): void
    {
        $this->appKey = $appKey;
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