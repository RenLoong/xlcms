<?php

namespace app\common\validate;

use think\Validate;

class Regions extends Validate
{
    protected $rule =   [
        'id' => 'require',
        'title'  => 'require|max:50'
    ];

    protected $message  =   [
        'id.require' => '地区代码不能为空',
        'title.require' => '名称不能为空',
        'title.max' => '名称长度在50个字符以内',
    ];
}
