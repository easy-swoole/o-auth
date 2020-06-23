<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


class Weibo extends \EasySwoole\Http\AbstractInterface\Controller
{
    public function index()
    {
        $config = new \EasySwoole\OAuth\Weibo\Config();
        $config->setClientId('clientid');
        $config->setState('easyswoole');
        $config->setRedirectUri('redirect_uri');

        $oauth = new \EasySwoole\OAuth\Weibo\OAuth($config);
        $url = $oauth->getAuthUrl();

        return $this->response()->redirect($url);
    }

    public function callback()
    {
        $params = $this->request()->getQueryParams();

        $config = new \EasySwoole\OAuth\Weibo\Config();
        $config->setClientId('clientid');
        $config->setClientSecret('secret');
        $config->setRedirectUri('redirect_uri');

        $oauth = new \EasySwoole\OAuth\Weibo\OAuth($config);
        $accessToken = $oauth->getAccessToken('easyswoole', $params['state'], $params['code']);

        $userInfo = $oauth->getUserInfo($accessToken);
        var_dump($userInfo);

        if (!$oauth->validateAccessToken($accessToken)) echo 'access_token 验证失败！' . PHP_EOL;
    }
}