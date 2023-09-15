<?php

namespace app\common\model;

use think\Model;

class AdsLayout extends Model
{
    public static function getCate()
    {
        $Data = self::field('id,title')->select();
        return $Data;
    }
}
