<?php

namespace app\common\utils;

use think\facade\Cache;

class Config
{

    public static function get(string $key)
    {
        try {
            $data = Cache::get($key);
            if ($data) {
                return $data;
            }
        } catch (\Throwable $th) {
        }
        return config($key);
    }
}
