<?php

namespace app\common\middleware;

use think\Response;
use app\common\utils\Json;
use app\common\utils\ResponseCode;
use app\common\utils\Rsa;
use think\Exception;
use think\facade\Cache;

class Basic
{
    use Json;
    public function handle($request, \Closure $next): Response
    {
        try {
            $this->hasToken($request);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
        $response = $next($request);
        return $response;
    }
    private function hasToken($request)
    {
        $controller = substr(strrchr($request->controller, '\\'), 1);
        $key = "$request->app.$controller.$request->action";
        $apiInfo = Cache::get($key);
        if (!$apiInfo) {
            throw new Exception("接口不存在");
        }
        $token = $request->header('Authorization');
        if ($apiInfo->is_login && !$token) {
            throw new Exception("请先登录", ResponseCode::LOGIN);
        }
        if ($token) {
            try {
                $UserDataObj = Rsa::decrypt($token, app_path('certs') . 'rsa_private.pem');
                if ($UserDataObj) {
                    $request->uid = $UserDataObj->uid;
                    $request->mobile = $UserDataObj->mobile;
                    $request->token_time = $UserDataObj->time;
                    if ($apiInfo->is_login) {
                        if (!$request->uid) {
                            throw new Exception("请先登录", ResponseCode::LOGIN);
                        }
                        if ($request->token_time < time() - 3600 * 24) {
                            throw new Exception("登录过期", ResponseCode::LOGIN);
                        }
                    }
                }
            } catch (\Throwable $th) {
            }
        }
    }
}
