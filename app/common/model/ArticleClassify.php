<?php

namespace app\common\model;

use think\Model;

class ArticleClassify extends Model
{
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
}
