<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\Admin;
use app\common\model\AdminRole;
use app\common\model\Auth;
use app\common\validate\Role as ValidateRole;
use think\Exception;
use think\facade\Cache;
use think\facade\Db;
use think\Request;

class Role extends BaseController
{
    public function index(Request $request)
    {
        try {
            $Data = AdminRole::getCate();
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
                $Data = AdminRole::where(['is_system' => 0])->whereNull('pid')->select();
            } else {
                $Data = AdminRole::where(['pid' => $G['id'], 'is_system' => 0])->select();
            }
            $auth_sum = Auth::sum('num');
            foreach ($Data as $item) {
                $item->auth_sum = $auth_sum;
                if ($item->rule) {
                    $item->rule = json_decode($item->rule);
                } else {
                    $item->rule = [];
                }
                $item->hasChildren = AdminRole::where(['pid' => $item->id])->count() ? 1 : 0;
                $item->admin_num = Admin::where(['admin_role_id' => $item->id])->count();
            }
            return $this->success('success', $Data);
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
            $AdminRole = AdminRole::where(['id' => $D['id']])->find();
            if ($AdminRole->is_system) {
                throw new Exception('系统角色不可删除');
            }
            $this->recursionDelete($AdminRole->id);
            $AdminRole->delete();
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->exception($th);
        }
    }
    private function recursionDelete($id)
    {
        $AdminRole = AdminRole::where(['pid' => $id])->select();
        foreach ($AdminRole as $item) {
            $this->recursionDelete($item->id);
            $item->delete();
        }
    }
    public function indexAdd(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateRole;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $AdminRole = new AdminRole;
            if (!empty($D['pid'])) {
                $AdminRole->pid = $D['pid'];
            }
            $AdminRole->name = $D['name'];
            $AdminRole->rule = json_encode($D['rule']);
            if ($AdminRole->save()) {
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
            $validate = new ValidateRole;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $AdminRole = AdminRole::where(['id' => $D['id'], 'is_system' => 0])->find();
            if (empty($D['id'])) {
                $AdminRole = new AdminRole;
            }
            if (!empty($D['pid'])) {
                $AdminRole->pid = $D['pid'];
            }
            $AdminRole->name = $D['name'];
            $AdminRole->rule = json_encode($D['rule']);
            if ($AdminRole->save()) {
                return $this->success('保存成功');
            } else {
                return $this->fail('保存失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexRestore(Request $request)
    {
        return $this->success();
    }
    public function indexSetState(Request $request)
    {
        $id = $request->post('id');
        $AdminRole = AdminRole::where(['id' => $id])->find();
        $AdminRole->state = $AdminRole->state ? 0 : 1;
        if (!$AdminRole->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function indexCache(Request $request)
    {
        try {
            $menu = AdminRole::setCache($request->admin_role_id);
            return $this->success('缓存成功', $menu);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
}
