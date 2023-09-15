<?php

namespace app\common\validate;

use think\Validate;

class Layout extends Validate
{
    protected $rule =   [
        'alias'  => 'require'
    ];

    protected $message  =   [
        'alias.require' => '请选择展示方式',
    ];
}
