<?php

namespace app\common\validate;

use think\Validate;

class AdsSeat extends Validate
{
    protected $rule =   [
        'seat'  => 'require|alphaDash',
        'title'  => 'require',
        'alias'  => 'require'
    ];

    protected $message  =   [
        'seat.require' => '广告位标识不能为空',
        'seat.alphaDash' => '广告位标识只能是字母、数字、下划线和破折号（ -_ ）',
        'title.require' => '广告位名称不能为空',
        'alias.require' => '请选择广告位排版'
    ];
}
