<?php

namespace app\Index\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return 'Hello word';
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }
}
