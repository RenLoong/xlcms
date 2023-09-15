<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\Admin as ModelAdmin;
use app\common\model\AdminRole;
use app\common\utils\Config;
use app\common\validate\Admin as ValidateAdmin;
use think\Request;
use think\helper\Str;

class Admin extends BaseController
{
    public function index(Request $request)
    {
        return $this->fail();
    }
    public function info(Request $request)
    {
        return $this->success(null, ModelAdmin::info($request->uid));
    }
    public function indexQuery(Request $request)
    {
        try {
            $G = $request->get();
            $per_page = 10;
            if (!empty($G['per_page'])) {
                $per_page = (int)$G['per_page'];
            }
            $where = [];
            $field = [
                'admin.id,admin.admin_role_id,admin.nickname,admin.avatar,admin.username,admin.mobile,admin.email,admin.state,admin.allow_time_start,admin.allow_time_end,admin.allow_week,admin.update_time,admin.create_time,admin.login_time,admin.online_time',
                'role.name as role_name,role.is_system'
            ];
            $Data = ModelAdmin::alias('admin')
                ->join('admin_role role', 'role.id=admin.admin_role_id')
                ->where($where)
                ->field($field)
                ->paginate($per_page)->each(function ($item) {
                    $item->nickname = base64_decode($item->nickname);
                    if ($item->avatar)
                        $item->avatar_url = Config::get('xlcms_site.img_url') . $item->avatar;
                    $item->allow_week = json_decode($item->allow_week);
                    $item->state_loading = false;
                });
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexDelete(Request $request)
    {
        try {
            $id = $request->post('id');
            $Admin = ModelAdmin::where(['id' => $id])->find();
            if ($Admin) {
                $AdminRole = AdminRole::where(['id' => $Admin->admin_role_id])->find();
                if ($AdminRole->is_system) {
                    return $this->fail('系统管理员不可删除');
                }
                if ($Admin->delete()) {
                    return $this->success('删除成功');
                }
            }
            return $this->fail('删除失败');
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function indexAdd(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateAdmin;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Admin = new ModelAdmin();
            $Admin->admin_role_id = $D['admin_role_id'];
            $Admin->nickname = base64_encode($D['nickname']);
            $Admin->avatar = $D['avatar'];
            $Admin->username = $D['username'];
            $Admin->state = $D['state'];
            $Admin->allow_time_start = $D['allow_time_start'];
            $Admin->allow_time_end = $D['allow_time_end'];
            $Admin->allow_week = json_encode($D['allow_week']);
            $Admin->password_hash = Str::random();
            $Admin->password = ModelAdmin::createPassword($D['password'], $Admin->password_hash);
            $Admin->save();
            if ($Admin->save()) {
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
            $validate = new ValidateAdmin;
            $validate->scene('edit');
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Admin = ModelAdmin::where(['id' => $D['id']])->find();
            if (!$Admin) {
                $Admin = new ModelAdmin;
            }
            $Admin->admin_role_id = $D['admin_role_id'];
            $Admin->nickname = base64_encode($D['nickname']);
            $Admin->avatar = $D['avatar'];
            $Admin->username = $D['username'];
            $Admin->state = $D['state'];
            $Admin->allow_time_start = $D['allow_time_start'];
            $Admin->allow_time_end = $D['allow_time_end'];
            $Admin->allow_week = json_encode($D['allow_week']);
            if (!empty($D['password'])) {
                $Admin->password_hash = Str::random();
                $Admin->password = ModelAdmin::createPassword($D['password'], $Admin->password_hash);
            }
            $Admin->save();
            if ($Admin->save()) {
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
        $Admin = ModelAdmin::where(['id' => $id])->find();
        $Admin->state = $Admin->state ? 0 : 1;
        if (!$Admin->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function editSelf(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateAdmin;
            $validate->scene('edit_self');
            $D['id'] = $request->uid;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Admin = ModelAdmin::where(['id' => $D['id']])->find();
            if (!$Admin) {
                return $this->fail('用户不存在');
            }
            $Admin->nickname = base64_encode($D['nickname']);
            if (!empty($D['avatar'])) {
                $Admin->avatar = $D['avatar'];
            }
            if (!empty($D['password'])) {
                $Admin->password_hash = Str::random();
                $Admin->password = ModelAdmin::createPassword($D['password'], $Admin->password_hash);
            }
            if (!empty($D['mobile'])) {
                $Admin->mobile = $D['mobile'];
            }
            if (!empty($D['email'])) {
                $Admin->email = $D['email'];
            }
            if ($Admin->save()) {
                return $this->success('保存成功');
            } else {
                return $this->fail('保存失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
}
