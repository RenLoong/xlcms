<?php

namespace app\admin\middleware;

use app\common\model\Admin;
use app\common\utils\ResponseCode;
use app\common\utils\Rsa;
use think\Exception;
use think\facade\Cache;
use think\Response;
use app\common\utils\Json;

class AdminAuth
{
    use Json;
    public function handle($request, \Closure $next): Response
    {
        try {
            $this->hasAuth($request);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
        $response = $next($request);
        return $response;
    }
    private function hasAuth($request)
    {
        $token = $request->header('Authorization');
        $controller = $request->controller();
        $namespace = __NAMESPACE__;
        # 命名空间返回上一级
        $namespace = substr($namespace, 0, strrpos($namespace, '\\'));
        # 反射获取当前请求控制器类
        $controllerClass = new \ReflectionClass("{$namespace}\\controller\\{$controller}");
        $properties = $controllerClass->getDefaultProperties();
        # 是否强制登录
        $isForceLogin = true;
        if (isset($properties['notNeedLogin']) && in_array($request->action(), $properties['notNeedLogin'])) {
            $isForceLogin = false;
        }
        if ($isForceLogin && !$token) {
            throw new \Exception('请先登录', ResponseCode::LOGIN);
        }
        if ($token) {
            try {
                $AdminUserObj = Rsa::decrypt($token, app_path('certs') . 'rsa_private.pem');
            } catch (\Throwable $th) {
                throw new \Exception($th->getMessage(), ResponseCode::DELETE_LOGIN);
            }
            if (!$AdminUserObj->is_system) {
                $key = app('http')->getName() . ".$controller.{$request->action()}";
                $auth = Cache::get($key);
                if (!$auth) {
                    throw new Exception("权限不存在");
                }
                if (empty($auth->state)) {
                    throw new Exception("权限未开启，请联系管理员");
                }
                if (!$AdminUserObj->admin_role_id) {
                    throw new Exception("请重新登录，无任何管理权限", ResponseCode::DELETE_LOGIN);
                }
                $rule = Cache::get("AdminRole.{$AdminUserObj->admin_role_id}");
                if (!$rule) {
                    throw new Exception("角色缓存不存在");
                }
                if (!in_array($auth->id, $rule)) {
                    throw new Exception("无权访问");
                }
                $now = date('H:i:s');
                if (!($now >= $AdminUserObj->allow_time_start && $now <= $AdminUserObj->allow_time_end)) {
                    throw new Exception("当前不在工作时间");
                }
                if (!in_array(date('w'), json_decode($AdminUserObj->allow_week, true))) {
                    throw new Exception("今日因该是休息日哦");
                }
            }
            $Admin = Admin::where(['id' => $AdminUserObj->uid])->field('state')->find();
            if (!$Admin) {
                throw new Exception("管理员不存在", ResponseCode::DELETE_LOGIN);
            }
            if (!$Admin->state) {
                throw new Exception("管理员状态异常", ResponseCode::DELETE_LOGIN);
            }
            $request->uid = $AdminUserObj->uid;
            $request->admin_role_id = $AdminUserObj->admin_role_id;
        }
    }
}
