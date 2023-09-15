<?php

namespace app\common\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule =   [
        'cid'  => 'requireWithout:classify_alias',
        'classify_alias'  => 'requireWithout:cid',
        'title'  => 'require',
        'content' => 'require',
        'alias' => 'unique:article',
    ];

    protected $message  =   [
        'cid.requireWithout' => '分类必须',
        'classify_alias.requireWithout' => '分类必须',
        'title.require' => '标题必须',
        'content.require' => '内容必须',
        'alias.unique' => '别名已存在',
    ];
}
