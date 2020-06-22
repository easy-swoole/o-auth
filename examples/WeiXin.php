<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


class WeiXin extends \EasySwoole\Http\AbstractInterface\Controller
{
    public function index()
    {
        $config = new \EasySwoole\OAuth\WeiXin\Config();
        $config->setAppId('appid');
        $config->setState('easyswoole');
        $config->setRedirectUri('redirect_uri');

        $oauth = new \EasySwoole\OAuth\WeiXin\OAuth($config);
        $url = $oauth->getAuthUrl();

        return $this->response()->redirect($url);
    }

    public function callback()
    {
        $params = $this->request()->getQueryParams();

        $config = new \EasySwoole\OAuth\WeiXin\Config();
        $config->setAppId('appid');
        $config->setSecret('secret');

        $oauth = new \EasySwoole\OAuth\WeiXin\OAuth($config);
        $accessToken = $oauth->getAccessToken('easyswoole', $params['state'], $params['code']);
        $refreshToken = $oauth->getAccessTokenResult()['refresh_token'];

        $userInfo = $oauth->getUserInfo($accessToken);
        var_dump($userInfo);

        if (!$oauth->validateAccessToken($accessToken)) echo 'access_token 验证失败！' . PHP_EOL;


        if (!$oauth->refreshToken($refreshToken)) echo 'access_token 续期失败！' . PHP_EOL;

    }
}