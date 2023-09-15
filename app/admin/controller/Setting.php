<?php

namespace app\admin\controller;

use app\BaseController;
use think\Request;
use think\helper\Str;
use app\common\setting\Basic;
use app\common\setting\Captcha;
use app\common\setting\Site;
use app\common\setting\Wechat;
use app\common\setting\WechatMini;

class Setting extends BaseController
{
    public function basic()
    {
        $data = [
            Basic::form(),
            Site::form(),
            Captcha::form(),
        ];
        return $this->success('success', $data);
    }
    public function basicEdit(Request $request)
    {
        $data = $request->post();
        if (empty($data['gruop'])) {
            return $this->fail('组名不能为空');
        }
        $name = Str::studly($data['gruop']);
        $class = "app\\common\\setting\\{$name}";
        if (!class_exists($class)) {
            return $this->fail($name . '组名不存在');
        }
        $state = $class::save($data['key'], $data['value']);
        if (!$state) {
            return $this->fail('保存失败');
        }
        return $this->success('success');
    }
    public function wechat()
    {
        $data = [
            Wechat::form(),
            WechatMini::form(),
        ];
        return $this->success('success', $data);
    }
}
