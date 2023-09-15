<?php

namespace app\common\setting;

class WechatMini extends Base
{
    protected $name = 'wechat_mini';
    protected $label = '微信小程序';
    protected $prefix = 'xlcms_';
    # children：[name:字段,label:字段名称,value:字段值,component:组件名,placeholder:占位符,tips:提示,props:组件属性]
    protected $children = [
        [
            'name' => 'appid',
            'label' => '小程序AppID',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入小程序AppID',
        ],
        [
            'name' => 'appsecret',
            'label' => '小程序AppSecret',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入小程序AppSecret',
        ],
    ];
}
