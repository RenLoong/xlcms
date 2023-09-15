<?php

namespace app\common\setting;

class Site extends Base
{
    protected $name = 'site';
    protected $label = '站点配置';
    protected $prefix = 'xlcms_';
    # children：[name:字段,label:字段名称,value:字段值,component:组件名,placeholder:占位符,tips:提示,props:组件属性]
    protected $children = [
        [
            'name' => 'domain',
            'label' => '站点域名',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入站点域名',
            'tips' => "请填写完整域名，如：http://www.xxx.com，不要以“/”结尾"
        ],
        [
            'name' => 'img_url',
            'label' => '图片域名',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入图片域名',
            'tips' => "请填写完整域名，如：http://www.xxx.com，不要以“/”结尾"
        ]
    ];
}
