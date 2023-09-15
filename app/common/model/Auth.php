<?php

namespace app\common\model;

use think\Model;

class Auth extends Model
{
    const SON_AUTH = [
        'Query' => ['action' => 'Query', 'name' => '查询'],
        'Delete' => ['action' => 'Delete', 'name' => '删除'],
        'Add' => ['action' => 'Add', 'name' => '创建'],
        'Edit' => ['action' => 'Edit', 'name' => '编辑'],
        'Restore' => ['action' => 'Restore', 'name' => '恢复'],
        'SetState' => ['action' => 'SetState', 'name' => '设置状态'],
        'Cache' => ['action' => 'Cache', 'name' => '缓存'],
    ];
    public static function getCate($id = null)
    {
        if ($id) {
            $Data = self::where(['pid' => $id])->field('id,title')->order('sort ASC,id ASC')->select();
        } else {
            $Data = self::whereNull('pid')->field('id,title')->order('sort ASC,id ASC')->select();
        }
        foreach ($Data as $item) {
            $item->children = self::getCate($item->id);
        }
        return $Data;
    }
    public static function getTree($id = null)
    {
        if ($id) {
            $Data = self::where(['pid' => $id])->field('id,title,son_auth')->select();
        } else {
            $Data = self::whereNull('pid')->field('id,title,son_auth')->select();
        }
        foreach ($Data as $item) {
            $auth = [];
            if ($item->son_auth) {
                $temp = json_decode($item->son_auth);
                foreach ($temp as $val) {
                    if (!empty(self::SON_AUTH[$val])) {
                        $auth[] = [
                            'id' => $item->id . '' . $val,
                            'title' => self::SON_AUTH[$val]['name']
                        ];
                    }
                }
                $item->son_auth = 1;
            }
            $children = self::getTree($item->id)->toArray();
            array_unshift($children, ...$auth);
            $item->children = $children;
        }
        return $Data;
    }
}
