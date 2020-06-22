<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


class AliPay extends \EasySwoole\Http\AbstractInterface\Controller
{
    public function index()
    {
        $config = new \EasySwoole\OAuth\AliPay\Config();
        $config->setState('easyswoole');
        $config->setAppId('appid');
        $config->setRedirectUri('redirect_uri');

        $oauth = new \EasySwoole\OAuth\AliPay\OAuth($config);
        $url = $oauth->getAuthUrl();
        return $this->response()->redirect($url);
    }

    public function callback()
    {
        $params = $this->request()->getQueryParams();

        $config = new \EasySwoole\OAuth\AliPay\Config();
        $config->setAppId('appid');
        $config->setAppPrivateKey('私钥');

        $oauth = new \EasySwoole\OAuth\AliPay\OAuth($config);
        $accessToken = $oauth->getAccessToken('easyswoole', $params['state'], $params['auth_code']);
        $refreshToken = $oauth->getAccessTokenResult()['alipay_system_oauth_token_response']['refresh_token'];

        $userInfo = $oauth->getUserInfo($accessToken);
        var_dump($userInfo);

        if (!$oauth->validateAccessToken($accessToken)) echo 'access_token 验证失败！' . PHP_EOL;
        var_dump($oauth->getAccessTokenResult());

        if (!$oauth->refreshToken($refreshToken)) echo 'access_token 续期失败！' . PHP_EOL;
        var_dump($oauth->getRefreshTokenResult());
    }
}