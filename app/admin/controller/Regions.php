<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\Regions as ModelRegions;
use app\common\validate\Regions as ValidateRegions;
use think\facade\Cache;
use think\facade\Db;
use think\Request;

class Regions extends BaseController
{
    public function list()
    {
        try {
            $Data = ModelRegions::getCateCache();
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function listQuery(Request $request)
    {
        try {
            $G = $request->get();
            if (empty($G['id'])) {
                $Data = ModelRegions::whereNull('pid')->select();
            } else {
                $Data = ModelRegions::where(['pid' => $G['id']])->select();
            }
            foreach ($Data as $item) {
                $item->hasChildren = ModelRegions::where(['pid' => $item->id])->count() ? 1 : 0;
            }
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function listDelete(Request $request)
    {
        Db::startTrans();
        try {
            $D = $request->post();
            # 检查是否存在子权限，递归删除
            $Regions = ModelRegions::where(['id' => $D['id']])->find();
            $this->recursionDelete($Regions->id);
            $Regions->delete();
            Db::commit();
            return $this->success('删除成功');
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->exception($th);
        }
    }
    private function recursionDelete($id)
    {
        $Regions = ModelRegions::where(['pid' => $id])->select();
        foreach ($Regions as $item) {
            $this->recursionDelete($item->id);
            $item->delete();
        }
    }
    public function listEdit(Request $request)
    {
        try {
            $D = $request->post();
            $validate = new ValidateRegions;
            if (!$validate->check($D)) {
                return $this->fail($validate->getError());
            }
            $Regions = ModelRegions::where(['id' => $D['id']])->find();
            if (!$Regions) {
                $Regions = new ModelRegions;
            }
            if (!empty($D['pid'])) {
                $Regions->pid = $D['pid'];
            }
            $Regions->id = $D['id'];
            $Regions->title = $D['title'];
            if (!empty($D['pinyin_prefix'])) {
                $Regions->pinyin_prefix = $D['pinyin_prefix'];
            }
            $Regions->level = $D['level'];
            $Regions->state = $D['state'];
            if ($Regions->save()) {
                Cache::delete('regions.cate');
                Cache::delete('regions');
                return $this->success('保存成功');
            } else {
                return $this->fail('保存失败');
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function listSetState(Request $request)
    {
        $id = $request->post('id');
        $Regions = ModelRegions::where(['id' => $id])->find();
        $Regions->state = $Regions->state ? 0 : 1;
        if (!$Regions->save()) {
            return $this->fail('设置失败');
        }
        return $this->success('状态已更改');
    }
    public function listCache()
    {
        try {
            Cache::delete('regions.cate');
            Cache::delete('regions');
            ModelRegions::getCache();
            return $this->success('缓存已更新');
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
}
