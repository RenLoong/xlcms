<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\controller\Ads as ControllerAds;
use app\common\model\Ads as ModelAds;
use app\common\model\AdsLayout;
use app\common\validate\Ads as ValidateAds;
use app\common\validate\AdsSeat;
use think\facade\Cache;
use think\facade\Db;
use think\Request;

class Ads extends BaseController
{
    public function list()
    {
    }
    public function listQuery(Request $request)
    {
        try {
            $G = $request->get();
            $per_page = 10;
            if (!empty($G['per_page'])) {
                $per_page = (int)$G['per_page'];
            }
            $where = [];
            if (!empty($G['seat'])) {
                $where[] = ['ads.pid', '=', $G['seat']];
            }
            $field = [
                'ads.*',
                'seat.title as seat_title,seat.seat,seat.alias as seat_alias'
            ];
            $Data = ModelAds::alias('ads')
                ->join('ads_layout seat', 'seat.id=ads.pid')
                ->where($where)
                ->field($field)
                ->paginate($per_page)->each(function ($item) {
                    $item->state_loading = false;
                });
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function listDelete(Request $request)
    {
        try {
            $id = $request->post('id');
            $Find = ModelAds::where(['id' => $id])->find();
            if ($Find) {
                $AdsLayout = AdsLayout::where(['id' => $Find->pid])->find();
                if ($Find->delete()) {
                    Cache::delete('ads.' . $AdsLayout->seat);
                    return $this->success('删除成功');
                }
            }
            return $this->fail('删除失败，请重试');
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function listAdd(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateAds;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Ads = new ModelAds();
            $Ads->pid = $D['pid'];
            $Ads->title = $D['title'];
            $Ads->w = $D['w'];
            $Ads->h = $D['h'];
            $Ads->path = $D['path'];
            $Ads->url = $D['url'];
            $Ads->action = $D['action'];
            $Ads->alias_id = $D['alias_id'];
            $Ads->ads_icon = $D['ads_icon'];
            if (!empty($D['start_time'])) {
                $Ads->start_time = $D['start_time'];
            }
            if (!empty($D['end_time'])) {
                $Ads->end_time = $D['end_time'];
            }
            $Ads->state = $D['state'];
            $Ads->sort = 99;
            if ($Ads->save()) {
                $AdsLayout = AdsLayout::where(['id' => $D['pid']])->find();
                Cache::delete('ads.' . $AdsLayout->seat);
                return $this->success('创建成功');
            } else {
                return $this->fail('创建失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function listEdit(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateAds;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Ads = ModelAds::where(['id' => $D['id']])->find();
            $Ads->pid = $D['pid'];
            $Ads->title = $D['title'];
            $Ads->w = $D['w'];
            $Ads->h = $D['h'];
            $Ads->path = $D['path'];
            $Ads->url = $D['url'];
            $Ads->action = $D['action'];
            $Ads->alias_id = $D['alias_id'];
            $Ads->ads_icon = $D['ads_icon'];
            if (empty($D['start_time'])) {
                $Ads->start_time = null;
            } else {
                $Ads->start_time = $D['start_time'];
            }
            if (empty($D['end_time'])) {
                $Ads->end_time = null;
            } else {
                $Ads->end_time = $D['end_time'];
            }
            $Ads->state = $D['state'];
            $Ads->sort = $D['sort'];
            if ($Ads->save()) {
                $AdsLayout = AdsLayout::where(['id' => $D['pid']])->find();
                Cache::delete('ads.' . $AdsLayout->seat);
                return $this->success('保存成功');
            } else {
                return $this->fail('保存失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function listRestore()
    {
    }
    public function listSetState(Request $request)
    {
        $id = $request->post('id');
        $Ads = ModelAds::where(['id' => $id])->find();
        $Ads->state = $Ads->state ? 0 : 1;
        if (!$Ads->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function listCache()
    {
        $AdsLayout = AdsLayout::select();
        foreach ($AdsLayout as $item) {
            Cache::delete('ads.' . $item->seat);
        }
        return $this->success('缓存已刷新');
    }
    public function seat()
    {
        try {
            $Data = AdsLayout::getCate();
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function seatQuery()
    {
        $Data = AdsLayout::select();
        foreach ($Data as $item) {
            $item->ads_num = ModelAds::where(['pid' => $item->id, 'state' => 1])->where(ControllerAds::getWhereTime())->count();
        }
        return $this->success('success', $Data);
    }
    public function seatDelete(Request $request)
    {
        Db::startTrans();
        try {
            $D = $request->post();
            # 检查是否存在子权限，递归删除
            $AdsLayout = AdsLayout::where(['id' => $D['id']])->find();
            Cache::delete('ads.' . $AdsLayout->seat);
            $AdsLayout->delete();
            $Ads = ModelAds::where(['pid' => $D['id']])->select();
            foreach ($Ads as $item) {
                $item->delete();
            }
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->exception($th);
        }
    }
    public function seatAdd(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new AdsSeat;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $AdsLayout = new AdsLayout;
            $AdsLayout->seat = strtoupper($D['seat']);
            $AdsLayout->title = $D['title'];
            $AdsLayout->alias = $D['alias'];
            $AdsLayout->w = $D['w'];
            $AdsLayout->h = $D['h'];
            $AdsLayout->state = $D['state'];
            if (isset($D['interval'])) {
                $AdsLayout->interval = $D['interval'];
            }
            if (isset($D['duration'])) {
                $AdsLayout->duration = $D['duration'];
            }
            if (isset($D['indicator_color'])) {
                $AdsLayout->indicator_color = $D['indicator_color'];
            }
            if (isset($D['indicator_active_color'])) {
                $AdsLayout->indicator_active_color = $D['indicator_active_color'];
            }
            $AdsLayout->autoplay = $D['autoplay'];
            if ($AdsLayout->save()) {
                Cache::delete('ads.' . $AdsLayout->seat);
                return $this->success('创建成功');
            } else {
                return $this->fail('创建失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function seatEdit(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new AdsSeat;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $AdsLayout = AdsLayout::where(['id' => $D['id']])->find();
            $AdsLayout->seat = strtoupper($D['seat']);
            $AdsLayout->title = $D['title'];
            $AdsLayout->alias = $D['alias'];
            $AdsLayout->w = $D['w'];
            $AdsLayout->h = $D['h'];
            $AdsLayout->state = $D['state'];
            if (isset($D['interval'])) {
                $AdsLayout->interval = $D['interval'];
            } else {
                $AdsLayout->interval = 1000;
            }
            if (isset($D['duration'])) {
                $AdsLayout->duration = $D['duration'];
            } else {
                $AdsLayout->duration = 0;
            }
            if (isset($D['indicator_color'])) {
                $AdsLayout->indicator_color = $D['indicator_color'];
            } else {
                $AdsLayout->indicator_color = null;
            }
            if (isset($D['indicator_active_color'])) {
                $AdsLayout->indicator_active_color = $D['indicator_active_color'];
            } else {
                $AdsLayout->indicator_active_color = null;
            }
            $AdsLayout->autoplay = $D['autoplay'];
            if ($AdsLayout->save()) {
                Cache::delete('ads.' . $AdsLayout->seat);
                return $this->success('保存成功');
            } else {
                return $this->fail('保存失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function seatRestore()
    {
    }
    public function seatSetState(Request $request)
    {
        $id = $request->post('id');
        $AdsLayout = AdsLayout::where(['id' => $id])->find();
        $AdsLayout->state = $AdsLayout->state ? 0 : 1;
        if (!$AdsLayout->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function seatCache()
    {
        $AdsLayout = AdsLayout::select();
        foreach ($AdsLayout as $item) {
            Cache::delete('ads.' . $item->seat);
        }
        return $this->success('缓存已刷新');
    }
}
