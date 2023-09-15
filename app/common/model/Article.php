<?php

namespace app\common\model;

use app\common\utils\Config;
use think\Model;
use think\model\concern\SoftDelete;

class Article extends Model
{
    use SoftDelete;
    public static function onAfterRead($model)
    {
        if ($model->thumb) {
            $model->thumb = json_decode($model->thumb, true);
            $thumb_url = [];
            foreach ($model->thumb as $key => $value) {
                $thumb_url[] = Config::get('xlcms_site.img_url') . $value;
            }
            $model->thumb_url = $thumb_url;
        }
    }
}
