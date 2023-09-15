<?php

namespace app\common\model;

use think\facade\Cache;
use think\Model;

class AdminRole extends Model
{
    public static function getCate($id = null)
    {
        if ($id) {
            $Data = self::where(['pid' => $id])->field('id,name as title')->select();
        } else {
            $Data = self::where(['is_system' => 0])->whereNull('pid')->field('id,name as title')->select();
        }
        foreach ($Data as $item) {
            $item->children = self::getCate($item->id);
        }
        return $Data;
    }
    public static function setCache($admin_role_id = null)
    {
        $result = [];
        $Data = self::select();
        foreach ($Data as $item) {
            if ($item->is_system) {
                $Auth = Auth::where(['is_show' => 1, 'state' => 1])->withAttr('path', function ($v) {
                    return '/' . $v;
                })->field('id,pid,title,icon,url as path')->order('sort ASC,id ASC')->select();
            } elseif ($item->rule && $item->rule !== '[]') {
                Cache::set("AdminRole.{$item->id}", $item->rule);
                $Auth = Auth::whereIn('id', json_decode($item->rule))->where(['is_show' => 1, 'state' => 1])->withAttr('path', function ($v) {
                    return '/' . $v;
                })->field('id,pid,title,icon,url as path')->order('sort ASC,id ASC')->select();
            }
            if ($Auth) {
                $menu = getTree($Auth->toArray());
                Cache::set("AdminRoleMenu.{$item->id}", $menu);
                if ($admin_role_id === $item->id) {
                    $result = $menu;
                }
            }
        }
        return $result;
    }
}
