<?php

namespace app\common\model;

use think\facade\Cache;
use think\Model;

class Regions extends Model
{
    public static function getCateCache()
    {
        $Data = Cache::get('regions.cate');
        if (!$Data) {
            $select = self::field('id,pid,title,level')->select();
            $Data = getTree($select->toArray());
            Cache::set('regions.cate', $Data);
        }
        return $Data;
    }
    public static function getCache()
    {
        $Data = Cache::get('regions');
        if (!$Data) {
            $select = self::where(['state' => 1])->select();
            $Data = getTree($select->toArray());
            Cache::set('regions', $Data);
        }
        return $Data;
    }
}
