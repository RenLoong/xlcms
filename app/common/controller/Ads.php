<?php

namespace app\common\controller;

use app\BaseController;
use app\common\model\Ads as ModelAds;
use app\common\model\AdsLayout;
use think\facade\Cache;
use think\Request;

class Ads extends BaseController
{
    public static function getWhereTime()
    {
        $now = date('Y-m-d H:i:s');
        return "(start_time IS NULL AND end_time IS NULL) OR (start_time < '{$now}' AND end_time IS NULL) OR (end_time > '{$now}' AND start_time IS NULL) OR (start_time < '{$now}' AND end_time > '{$now}')";
    }
    public static function getAds(Request $request)
    {
        try {
            $seat = $request->get('seat');
            $Data = Cache::get('ads.' . $seat);
            if (!$Data) {
                $Data = AdsLayout::where(['seat' => $seat, 'state' => 1])->withoutField('create_time,update_time')->find();
                if (!$Data) {
                    $now = date('Y-m-d H:i:s');
                    $endTTLarr = [];
                    $Data->list = ModelAds::where(['pid' => $Data->id, 'state' => 1])->where(self::getWhereTime())->withoutField('create_time,update_time')->select();
                    foreach ($Data->list as $ads) {
                        if ($ads->start_time)
                            $endTTLarr[] = $ads->start_time;
                        if ($ads->end_time)
                            $endTTLarr[] = $ads->end_time;
                    }
                    sort($endTTLarr);
                    if (empty($endTTLarr)) {
                        Cache::set('ads.' . $seat, $Data);
                    } else {
                        $endTTl = 0;
                        foreach ($endTTLarr as $key => $value) {
                            if ($value > $now) {
                                $endTTl = strtotime($value);
                                break;
                            }
                        }
                        $ttl = $endTTl - time();
                        Cache::set('ads.' . $seat, $Data, $ttl);
                    }
                }
            }
            return self::success('success', $Data);
        } catch (\Throwable $th) {
            return self::exception($th);
        }
    }
}
