<?php

namespace app\common\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule =   [
        'admin_role_id'  => 'require',
        'nickname' => 'require',
        'username' => 'require|unique:admin',
        'password' => 'require',
        'allow_time_start' => 'require',
        'allow_time_end' => 'require',
        'allow_week' => 'require',
    ];

    protected $message  =   [
        'admin_role_id.require' => '请选择角色',
        'nickname.require' => '昵称不能为空',
        'username.require' => '用户名不能为空',
        'username.unique' => '用户名已存在',
        'password.require' => '密码不能为空',
        'allow_time_start.require' => '请选择上班时间',
        'allow_time_end.require' => '请选择下班时间',
        'allow_week.require' => '请选择工作周',
    ];

    protected $scene  =   [
        'edit' => ['admin_role_id', 'nickname', 'username', 'allow_time_start', 'allow_time_end', 'allow_week'],
        'edit_self' => ['nickname', 'username'],
    ];
}
