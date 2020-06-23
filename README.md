# o-auth

## 介绍

基于`easyswoole/http-client`对第三方登录授权的SDK。

## 安装

```
composer require easyswoole/o-auth
```

## 代码

公共的方法：
- `getAuthUrl()` 获取授权地址
- `getAccessToken($storeState = null, $state = null, $code = null)` 获取AccessToken（只返回access_token）
- `getAccessTokenResult()` 执行`getAccessToken`方法后，此方法获取原结果
- `getUserInfo(string $accessToken)` 获取用户信息
- `validateAccessToken(string $accessToken)` 验证token是否有效
- `refreshToken(string $refreshToken = null)` 刷新token 返回`bool`
- `getRefreshTokenResult()` 执行`refreshToken`方法后，此方法获取原结果


## 示例代码

[微信](./examples/WeiXin.php)

[QQ](./examples/QQ.php)

[微博](./examples/Weibo.php)

[支付宝](./examples/AliPay.php)

[Github](./examples/Github.php)

[Gitee](./examples/Gitee.php)