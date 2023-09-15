<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\AdminRole;
use app\common\model\Auth as ModelAuth;
use app\common\validate\Auth as ValidateAuth;
use think\facade\Cache;
use think\facade\Db;
use think\Request;

class Auth extends BaseController
{
    public function auth(Request $request)
    {
        return $this->success();
    }
    public function index(Request $request)
    {
        try {
            if ($request->get('action') == 'tree') {
                $Data = ModelAuth::getTree();
            } else {
                $Data = ModelAuth::getCate();
            }
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexQuery(Request $request)
    {
        try {
            $G = $request->get();
            if (empty($G['id'])) {
                $Data = ModelAuth::whereNull('pid')->order('sort')->select();
            } else {
                $Data = ModelAuth::where(['pid' => $G['id']])->order('sort')->select();
            }
            foreach ($Data as $item) {
                $item->hasChildren = ModelAuth::where(['pid' => $item->id])->count() ? 1 : 0;
                if ($item->son_auth) {
                    $item->son_auth = json_decode($item->son_auth);
                } else {
                    $item->son_auth = [];
                }
            }
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexAdd(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateAuth;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Auth = new ModelAuth;
            if (!empty($D['pid'])) {
                $Auth->pid = $D['pid'];
            }
            $Auth->title = $D['title'];
            $Auth->icon = $D['icon'];
            $Auth->url = $D['url'];
            $Auth->is_show = $D['is_show'];
            $Auth->state = $D['state'];
            $Auth->sort = $D['sort'];
            if (!empty($D['controller'])) {
                $Auth->controller = $D['controller'];
            }
            if (!empty($D['action'])) {
                $Auth->action = $D['action'];
            }
            $Auth->num = 1;
            if (!empty($D['son_auth'])) {
                $Auth->num = count($D['son_auth']) + 1;
                $Auth->son_auth = json_encode($D['son_auth'], JSON_UNESCAPED_UNICODE);
            }
            if ($Auth->save()) {
                return $this->success('创建成功');
            } else {
                return $this->fail('创建失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexEdit(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateAuth;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Auth = ModelAuth::where(['id' => $D['id']])->find();
            if (empty($D['id'])) {
                $Auth = new ModelAuth;
            }
            if (!empty($D['pid'])) {
                $Auth->pid = $D['pid'];
            }
            $Auth->title = $D['title'];
            $Auth->icon = $D['icon'];
            $Auth->url = $D['url'];
            $Auth->is_show = $D['is_show'];
            $Auth->state = $D['state'];
            $Auth->sort = $D['sort'];
            $Auth->controller = null;
            $Auth->action = null;
            if (!empty($D['controller'])) {
                $Auth->controller = $D['controller'];
            }
            if (!empty($D['action'])) {
                $Auth->action = $D['action'];
            }
            $Auth->num = 1;
            if (!empty($D['son_auth'])) {
                $Auth->num = count($D['son_auth']) + 1;
                $Auth->son_auth = json_encode($D['son_auth'], JSON_UNESCAPED_UNICODE);
            }
            if ($Auth->save()) {
                return $this->success('保存成功');
            } else {
                return $this->fail('保存失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexDelete(Request $request)
    {
        Db::startTrans();
        try {
            $D = $request->post();
            # 检查是否存在子权限，递归删除
            $Auth = ModelAuth::where(['id' => $D['id']])->find();
            $this->recursionDelete($Auth->id);
            $Auth->delete();
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->exception($th);
        }
    }
    private function recursionDelete($id)
    {
        $Auth = ModelAuth::where(['pid' => $id])->select();
        foreach ($Auth as $item) {
            $this->recursionDelete($item->id);
            $item->delete();
        }
    }
    public function indexSetState(Request $request)
    {
        $id = $request->post('id');
        $Auth = ModelAuth::where(['id' => $id])->find();
        $Auth->state = $Auth->state ? 0 : 1;
        if (!$Auth->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function indexSetShow(Request $request)
    {
        $id = $request->post('id');
        $Auth = ModelAuth::where(['id' => $id])->find();
        $Auth->is_show = $Auth->is_show ? 0 : 1;
        if (!$Auth->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function indexSetSort(Request $request)
    {
        $id = $request->post('id');
        $sort = $request->post('sort');
        $Auth = ModelAuth::where(['id' => $id])->find();
        $Auth->sort = $sort;
        if (!$Auth->save()) {
            return $this->fail('设置失败');
        }
        return $this->success();
    }
    public function indexCache(Request $request)
    {
        try {
            $adminAppName = 'admin';
            $adminPath = app_path();
            $Data = ModelAuth::select();
            $routeContent = "<?php\nuse think\\facade\\Route;\nRoute::group('', function () {\n";
            foreach ($Data as $item) {
                # 创建路由规则
                if ($item->controller && $item->action && $item->url) {
                    $auth = [
                        'id' => $item->id,
                        'title' => $item->title,
                        'state' => $item->state
                    ];
                    Cache::set("{$adminAppName}.{$item->controller}.{$item->action}", $auth);
                    # 创建路由规则
                    $routeContent .= "//{$item->title}\nRoute::any('{$item->url}', '{$adminAppName}/{$item->controller}/{$item->action}');\n";
                    if ($item->son_auth) {
                        $son_auth = json_decode($item->son_auth);
                        $routeContent .= "/**{$item->title}子权限*/\n";
                        foreach ($son_auth as $son) {
                            $routeContent .= "Route::any('{$item->url}/{$son}', '{$adminAppName}/{$item->controller}/{$item->action}{$son}');\n";
                            $auth = [
                                'id' => $item->id . $son,
                                'title' => $item->title . "【{$son}】",
                                'state' => $item->state
                            ];
                            Cache::set("{$adminAppName}.{$item->controller}.{$item->action}{$son}", $auth);
                        }
                        $routeContent .= "/**{$item->title}子权限*/\n";
                    }
                    # 检查是否存在控制器
                    $appController = $adminPath . "/controller/{$item->controller}.php";
                    if (!file_exists($appController)) {
                        $file = fopen($appController, 'w');
                        if ($file) {
                            $appControllerContent = "<?php\nnamespace app\\{$adminAppName}\\controller;\nuse app\\BaseController;\nclass {$item->controller} extends BaseController\n{\n}";
                            fwrite($file, $appControllerContent);
                            fclose($file);
                        }
                    }
                }
            }
            $routeContent .= "})->middleware([\\app\\{$adminAppName}\\middleware\\AdminAuth::class]);";
            $routePath = $adminPath . '/route/app.php';
            $file = fopen($routePath, 'w');
            if ($file) {
                fwrite($file, $routeContent);
                fclose($file);
                $AdminRoleController = new AdminRole();
                $AdminRoleController->cache();
                $menu = Cache::get("AdminRoleMenu.{$request->admin_role_id}");
                return $this->success('缓存成功', $menu);
            } else {
                return $this->fail('路由文件更新失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
}
