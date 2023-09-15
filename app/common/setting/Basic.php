<?php

namespace app\common\setting;

class Basic extends Base
{
    # name：配置名称，用于标识配置，不可重复，命名规则为当前类名的_下划线写法
    # 驼峰转下划线，Str::snake($value, $delimiter  =  '_')
    # 下划线转驼峰(首字母大写)，Str::studly($value)
    protected $name = 'basic';
    protected $label = '站点信息';
    protected $prefix = 'xlcms_';
    # children：[name:字段,label:字段名称,value:字段值,component:组件名,placeholder:占位符,tips:提示,props:组件属性]
    protected $children = [
        [
            'name' => 'title',
            'label' => '站点标题',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入站点标题',
            'tips' => "例如：XLCMS后台管理系统\n站点名称将显示在浏览器窗口标题等位置，请务必填写准确的信息，方便搜索引擎收录。\n站点名称一般不超过80个字符。",
            'props' => [
                'maxlength' => 80
            ],
        ],
        [
            'name' => 'logo',
            'label' => 'LOGO',
            'value' => '',
            'component' => 'x-uploads',
            'tips' => "请上传2M以内格式为“jpg，png”的图片",
            'props' => [
                'action' => 'x-upload/upload'
            ],
        ],
        [
            'name' => 'keywords',
            'label' => '站点关键字',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入站点关键字',
            'tips' => "站点关键词一般不超过120个字符",
            'props' => [
                'type' => 'textarea',
                'maxlength' => 120,
                'autosize' => ['minRows' => 5],
                'show-word-limit' => true
            ],
        ],
        [
            'name' => 'descriptions',
            'label' => '站点描述',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入站点描述',
            'tips' => "站点描述一般不超过250个字符",
            'props' => [
                'type' => 'textarea',
                'maxlength' => 250,
                'autosize' => ['minRows' => 5],
                'show-word-limit' => true
            ],
        ],
        [
            'name' => 'icp',
            'label' => '备案信息',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入备案信息',
            'tips' => "ICP备案信息，例如：黔ICP备xxxxxxx号",
            'props' => [
                'maxlength' => 80
            ],
        ],
        [
            'name' => 'beian',
            'label' => '公网安备',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入公网安备',
            'tips' => "公网安备案信息，例如：贵公网安备 xxxxxxxxx号",
            'props' => [
                'maxlength' => 80
            ],
        ],
        [
            'name' => 'beian_code',
            'label' => '公网安备号',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入公网安备号',
            'tips' => "公网安备案信息中的数字，例如：12345678",
            'props' => [
                'maxlength' => 80
            ],
        ]
    ];
}
