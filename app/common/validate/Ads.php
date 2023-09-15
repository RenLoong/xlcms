<?php

namespace app\common\validate;

use think\Validate;

class Ads extends Validate
{
    protected $rule =   [
        'pid'  => 'require'
    ];

    protected $message  =   [
        'pid.require' => '请选择广告位',
    ];
}
