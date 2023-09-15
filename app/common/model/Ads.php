<?php

namespace app\common\model;

use app\common\utils\Config;
use think\Model;

class Ads extends Model
{
    public static function onAfterRead($model)
    {
        if ($model->path) {
            $model->path_url = Config::get('xlcms_site.img_url') . $model->path;
        }
    }
}
