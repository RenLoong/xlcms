<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\Admin;
use app\common\utils\Rsa;
use think\facade\Cache;
use think\Request;

class Index extends BaseController
{
    protected $notNeedLogin = ['index', 'hello'];
    public function index()
    {
        return $this->success('success', ['name' => 'ThinkPHP8']);
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }
}
