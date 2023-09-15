<?php

namespace app\common\setting;

class Captcha extends Base
{
    protected $name = 'captcha';
    protected $label = '验证码';
    protected $prefix = '';
    # children：[name:字段,label:字段名称,value:字段值,component:组件名,placeholder:占位符,tips:提示,props:组件属性]
    protected $children = [
        [
            'name' => 'length',
            'label' => '验证码位数',
            'value' => 0,
            'value_format' => 'int',
            'component' => 'el-input-number',
            'placeholder' => '请输入验证码位数',
            'tips' => "验证码位数，范围4-8位",
            'props' => [
                'min' => 4,
                'max' => 8,
                'controls' => false
            ],
        ],
        [
            'name' => 'codeSet',
            'label' => '验证码字符集合',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入验证码字符集合',
        ],
        [
            'name' => 'useCurve',
            'label' => '是否画混淆曲线',
            'value' => true,
            'value_format' => 'bool',
            'component' => 'el-switch',
        ],
        [
            'name' => 'useNoise',
            'label' => '是否添加杂点',
            'value' => true,
            'value_format' => 'bool',
            'component' => 'el-switch',
        ],
        [
            'name' => 'useImgBg',
            'label' => '是否使用背景图',
            'value' => false,
            'value_format' => 'bool',
            'component' => 'el-switch',
        ],
        [
            'name' => 'fontSize',
            'label' => '验证码字体大小',
            'value' => 0,
            'value_format' => 'int',
            'component' => 'el-input-number',
            'placeholder' => '请输入验证码字体大小',
            'props' => [
                'min' => 20,
                'max' => 30,
                'controls' => false
            ],
        ],
        [
            'name' => 'expire',
            'label' => '验证码过期时间',
            'value' => 0,
            'value_format' => 'int',
            'component' => 'el-input-number',
            'placeholder' => '请输入验证码过期时间',
            'tips' => '单位：秒',
            'props' => [
                'min' => 60,
                'max' => 3600,
                'controls' => false
            ],
        ],
        [
            'name' => 'useZh',
            'label' => '是否使用中文验证码',
            'value' => false,
            'value_format' => 'bool',
            'component' => 'el-switch'
        ],
        [
            'name' => 'zhSet',
            'label' => '中文验证码字符串',
            'value' => '',
            'component' => 'el-input',
            'placeholder' => '请输入中文验证码字符串',
            'props' => [
                'type' => 'textarea',
                'maxlength' => 250,
                'autosize' => ['minRows' => 5],
                'show-word-limit' => true
            ],
        ],
        [
            'name' => 'math',
            'label' => '是否使用算术验证码',
            'value' => false,
            'value_format' => 'bool',
            'component' => 'el-switch',
        ],
        [
            'name' => 'bg',
            'label' => '验证码背景颜色',
            'value' => '',
            'value_convert' => 'arraytorgb',
            'value_format' => 'rgbtoarray',
            'component' => 'el-color-picker',
            'placeholder' => '请选择验证码背景颜色',
            'props' => [
                'color-format' => 'rgb',
            ]
        ]
    ];
}
