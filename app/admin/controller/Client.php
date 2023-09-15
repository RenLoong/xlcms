<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\Api;
use app\common\validate\Api as ValidateApi;
use think\facade\Cache;
use think\Request;

class Client extends BaseController
{
    public function api(Request $request)
    {
        try {
            $G = $request->get();
            $per_page = 10;
            if (!empty($G['per_page'])) {
                $per_page = (int)$G['per_page'];
            }
            $where = [];
            $Data = Api::where($where)
                ->order('id desc')
                ->paginate($per_page)->each(function ($item) {
                    $item->state_loading = false;
                    $item->is_login_loading = false;
                });
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function apiQuery()
    {
    }
    public function apiDelete(Request $request)
    {
        try {
            $id = $request->post('id');
            $Api = Api::where(['id' => $id])->find();
            if ($Api) {
                if ($Api->delete()) {
                    return $this->success('删除成功');
                }
            }
            return $this->fail('删除失败');
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function apiAdd(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateApi;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $ModelApi = new Api;
            $ModelApi->name = $D['name'];
            $ModelApi->app = $D['app'];
            $ModelApi->controller = $D['controller'];
            $ModelApi->action = $D['action'];
            $ModelApi->url = $D['url'];
            $ModelApi->state = $D['state'];
            $ModelApi->state_msg = $D['state_msg'];
            $ModelApi->is_login = $D['is_login'];
            if ($ModelApi->save()) {
                return $this->success('创建成功');
            } else {
                return $this->fail('创建失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function apiEdit(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateApi;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $ModelApi = Api::where(['id' => $D['id']])->find();
            $ModelApi->name = $D['name'];
            $ModelApi->app = $D['app'];
            $ModelApi->controller = $D['controller'];
            $ModelApi->action = $D['action'];
            $ModelApi->url = $D['url'];
            $ModelApi->state = $D['state'];
            $ModelApi->state_msg = $D['state_msg'];
            $ModelApi->is_login = $D['is_login'];
            if ($ModelApi->save()) {
                return $this->success('保存成功');
            } else {
                return $this->fail('保存失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function apiRestore()
    {
    }
    public function apiSetState(Request $request)
    {
        $id = $request->post('id');
        $Api = Api::where(['id' => $id])->find();
        $Api->state = $Api->state ? 0 : 1;
        if (!$Api->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function apiSetLogin(Request $request)
    {
        $id = $request->post('id');
        $Api = Api::where(['id' => $id])->find();
        $Api->is_login = $Api->is_login ? 0 : 1;
        if (!$Api->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function apiCache(Request $request)
    {
        try {
            $appPath = app_path();
            $Data = Api::select();
            if ($Data->isEmpty()) {
                return $this->fail('没有数据');
            }
            $routeContent = "<?php\nuse think\\facade\\Route;\n";
            foreach ($Data as $item) {
                # 创建路由规则
                if ($item->controller && $item->action && $item->url) {
                    $auth = [
                        'id' => $item->id,
                        'name' => $item->name,
                        'state' => $item->state,
                        'state_msg' => $item->state_msg,
                        'is_login' => $item->is_login
                    ];
                    Cache::set("{$item->app}.{$item->controller}.{$item->action}", $auth);
                    # 创建路由规则
                    $routeContent .= "//{$item->name}\nRoute::any('/{$item->app}/{$item->url}', '{$item->app}/{$item->controller}/{$item->action}');\n";
                    # 检查是否存在控制器
                    $appController = $appPath . "{$item->app}/controller/{$item->controller}.php";
                    if (!file_exists($appController)) {
                        $file = fopen($appController, 'w');
                        if ($file) {
                            $appControllerContent = "<?php\nnamespace app\\{$item->app}\\controller;\nuse app\BaseController;\nclass {$item->controller} extends BaseController\n{\n}";
                            fwrite($file, $appControllerContent);
                            fclose($file);
                        }
                    }
                }
            }
            $routePath = root_path('route') . 'app.php';
            $file = fopen($routePath, 'w');
            if ($file) {
                fwrite($file, $routeContent);
                fclose($file);
                return $this->success('缓存成功');
            } else {
                return $this->fail('路由文件更新失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
}
