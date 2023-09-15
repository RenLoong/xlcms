<?php

namespace app\admin\controller;

use app\BaseController;

class Config extends BaseController
{
    protected $notNeedLogin = ['index'];
    public function index()
    {
        $data = [
            'siteinfo' => [
                'name' => 'Xlcms',
                'title' => 'XLCMS',
                'description' => 'XLCMS',
                'copyright' => 'Renloong',
                'icp' => '',
                'beian' => '',
                'beian_code' => '',
                'logo_use' => 'svg',
                'logo_white' => '',
                'logo_dark' => '',
            ],
            'header_nav' => [],
            'footer_nav' => [],
            'public_api' => [],
            'enum' => [],
        ];
        return $this->success(null, $data);
    }
}
