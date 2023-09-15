<?php

namespace app\common\controller;

use app\BaseController;
use app\common\model\Regions as ModelRegions;
use think\Request;

class Regions extends BaseController
{
    public function index(Request $request)
    {
        try {
            $Data = ModelRegions::getCache();
            return $this->success('success', $Data);
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
}
