<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\model\Admin;
use app\common\utils\ResponseCode;
use think\captcha\facade\Captcha;
use think\Exception;
use think\Request;

class Login extends BaseController
{
    protected $notNeedLogin = ['login', 'captcha'];
    public function login(Request $request)
    {
        try {
            $D = $request->post();
            if (!captcha_check($D['captcha'])) {
                throw new Exception("验证码不正确", ResponseCode::CAPTCHA);
            }
            $Admin = Admin::where(['username' => $D['username']])->find();
            if (!$Admin) {
                throw new Exception('密码错误');
            }
            if ($Admin->password !== Admin::createPassword($D['password'], $Admin->password_hash)) {
                throw new Exception('密码错误');
            }
            if (!$Admin->state) {
                throw new Exception('管理员账号异常');
            }
            $now = date('H:i:s');
            if (!($now >= $Admin->allow_time_start && $now <= $Admin->allow_time_end)) {
                throw new Exception("当前不在工作时间");
            }
            if (!in_array(date('w'), json_decode($Admin->allow_week, true))) {
                throw new Exception("今日因该是休息日哦");
            }
            /* 保存登录时间 */
            $Admin->login_time = date('Y-m-d H:i:s');
            if ($Admin->save()) {
                return $this->success('登录成功', Admin::info($Admin->id));
            } else {
                throw new Exception("登录失败");
            }
        } catch (\Throwable $th) {
            return $this->exception($th);
        }
    }
    public function captcha(Request $request)
    {
        return Captcha::create();
    }
}
