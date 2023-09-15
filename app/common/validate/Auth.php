<?php

namespace app\common\validate;

use think\Validate;

class Auth extends Validate
{
    protected $rule =   [
        'title'  => 'require|max:50',
        'url' => 'require',
    ];

    protected $message  =   [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过50个字符',
        'url.require' => '规则不能为空',
    ];
}
