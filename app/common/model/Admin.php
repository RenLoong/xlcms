<?php

namespace app\common\model;

use app\common\utils\Config;
use app\common\utils\Rsa;
use think\facade\Cache;
use think\Model;
use think\model\concern\SoftDelete;

class Admin extends Model
{
    use SoftDelete;
    public static function info($uid)
    {
        $Admin = Admin::where(['id' => $uid])->find();
        /* 重组用户信息 */
        $AdminUser = new \stdClass;
        $AdminUser->nickname = base64_decode($Admin->nickname);
        if ($Admin->avatar) {
            $AdminUser->avatar_url = Config::get('xlcms_site.img_url') . $Admin->avatar;
        }
        $AdminUser->username = $Admin->username;
        $AdminUser->mobile = $Admin->mobile;
        $AdminUser->email = $Admin->email;
        /* 生成token */
        $data = new \stdClass;
        $data->uid = $Admin->id;
        $data->admin_role_id = $Admin->admin_role_id;
        $data->username = $Admin->username;
        $data->mobile = $Admin->mobile;
        $data->email = $Admin->email;
        $data->allow_time_start = $Admin->allow_time_start;
        $data->allow_time_end = $Admin->allow_time_end;
        $data->allow_week = $Admin->allow_week;
        $data->is_system = 0;
        if (AdminRole::where(['id' => $Admin->admin_role_id, 'is_system' => 1])->count()) {
            $data->is_system = 1;
        }
        $AdminUser->token = Rsa::encrypt($data, app_path('certs') . 'rsa_public.pem');
        /* 读取权限菜单 */
        $menu = Cache::get("AdminRoleMenu.{$Admin->admin_role_id}");
        if (!$menu) {
            $menu = AdminRole::setCache($Admin->admin_role_id);
        }
        return ['user' => $AdminUser, 'menu' => $menu];
    }
    public static function createPassword($password, $password_hash)
    {
        return md5(sha1(md5("U&{$password}-{$password_hash}*u")));
    }
}
