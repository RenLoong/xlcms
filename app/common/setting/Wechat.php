<?php

namespace app\common\setting;

class Wechat extends Base
{
    protected $name = 'wechat';
    protected $label = '微信公众号';
    protected $prefix = 'xlcms_';
    # children：[name:字段,label:字段名称,value:字段值,component:组件名,placeholder:占位符,tips:提示,props:组件属性]
    protected $children = [
        [
            'name' => 'type',
            'label' => '小程序类型',
            'value' => '',
            'component' => 'el-select',
            'children_component' => 'el-option',
            'placeholder' => '请选择小程序类型',
            'tips' => '小程序类型',
            'children' => [
                [
                    'title' => '微信小程序',
                    'value' => 'wechat',
                    'props' => [
                        'border' => true,
                    ]
                ],
                [
                    'title' => '支付宝小程序',
                    'value' => 'alipay',
                    'props' => [
                        'border' => true,
                    ]
                ]
            ]
        ],
        [
            'name' => 'type1',
            'label' => '小程序类型',
            'value' => '',
            'component' => 'el-radio-group',
            'children_component' => 'el-radio',
            'placeholder' => '请选择小程序类型',
            'tips' => '小程序类型',
            'children' => [
                [
                    'title' => '微信小程序',
                    'value' => 'wechat',
                    'props' => [
                        'border' => true,
                    ]
                ],
                [
                    'title' => '支付宝小程序',
                    'value' => 'alipay',
                    'props' => [
                        'border' => true,
                    ]
                ]
            ]
        ],
        [
            'name' => 'type2',
            'label' => '小程序类型',
            'value' => [],
            'value_format' => 'array',
            'component' => 'el-checkbox-group',
            'children_component' => 'el-checkbox',
            'placeholder' => '请选择小程序类型',
            'tips' => '小程序类型',
            'children' => [
                [
                    'title' => '微信小程序',
                    'value' => 'wechat',
                    'props' => [
                        'border' => true,
                    ]
                ],
                [
                    'title' => '支付宝小程序',
                    'value' => 'alipay',
                    'props' => [
                        'border' => true,
                    ]
                ]
            ]
        ],
    ];
}
