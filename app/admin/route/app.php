<?php
use think\facade\Route;
Route::group('', function () {
//控制台
Route::any('index', 'admin/Index/index');
//权限
Route::any('auth/index', 'admin/Auth/index');
/**权限子权限*/
Route::any('auth/index/Query', 'admin/Auth/indexQuery');
Route::any('auth/index/Delete', 'admin/Auth/indexDelete');
Route::any('auth/index/Add', 'admin/Auth/indexAdd');
Route::any('auth/index/Edit', 'admin/Auth/indexEdit');
Route::any('auth/index/SetState', 'admin/Auth/indexSetState');
Route::any('auth/index/Cache', 'admin/Auth/indexCache');
/**权限子权限*/
//设置是否显示
Route::any('auth/index/SetShow', 'admin/Auth/indexSetShow');
//角色
Route::any('role/index', 'admin/Role/index');
/**角色子权限*/
Route::any('role/index/Query', 'admin/Role/indexQuery');
Route::any('role/index/Delete', 'admin/Role/indexDelete');
Route::any('role/index/Add', 'admin/Role/indexAdd');
Route::any('role/index/Edit', 'admin/Role/indexEdit');
Route::any('role/index/Restore', 'admin/Role/indexRestore');
Route::any('role/index/SetState', 'admin/Role/indexSetState');
Route::any('role/index/Cache', 'admin/Role/indexCache');
/**角色子权限*/
//管理员
Route::any('admin/index', 'admin/Admin/index');
/**管理员子权限*/
Route::any('admin/index/Query', 'admin/Admin/indexQuery');
Route::any('admin/index/Delete', 'admin/Admin/indexDelete');
Route::any('admin/index/Add', 'admin/Admin/indexAdd');
Route::any('admin/index/Edit', 'admin/Admin/indexEdit');
Route::any('admin/index/Restore', 'admin/Admin/indexRestore');
Route::any('admin/index/SetState', 'admin/Admin/indexSetState');
/**管理员子权限*/
//上传文件
Route::any('uploads', 'admin/Uploads/index');
//文章列表
Route::any('article/index', 'admin/Article/index');
/**文章列表子权限*/
Route::any('article/index/Query', 'admin/Article/indexQuery');
Route::any('article/index/Delete', 'admin/Article/indexDelete');
Route::any('article/index/Add', 'admin/Article/indexAdd');
Route::any('article/index/Edit', 'admin/Article/indexEdit');
Route::any('article/index/Restore', 'admin/Article/indexRestore');
Route::any('article/index/SetState', 'admin/Article/indexSetState');
Route::any('article/index/Cache', 'admin/Article/indexCache');
/**文章列表子权限*/
//文章分类
Route::any('article/classify', 'admin/Article/classify');
/**文章分类子权限*/
Route::any('article/classify/Query', 'admin/Article/classifyQuery');
Route::any('article/classify/Delete', 'admin/Article/classifyDelete');
Route::any('article/classify/Add', 'admin/Article/classifyAdd');
Route::any('article/classify/Edit', 'admin/Article/classifyEdit');
Route::any('article/classify/Restore', 'admin/Article/classifyRestore');
Route::any('article/classify/SetState', 'admin/Article/classifySetState');
Route::any('article/classify/Cache', 'admin/Article/classifyCache');
/**文章分类子权限*/
//协议
Route::any('article/agree', 'admin/Article/agree');
//公告
Route::any('article/notice', 'admin/Article/notice');
//广告列表
Route::any('ads/list', 'admin/Ads/list');
/**广告列表子权限*/
Route::any('ads/list/Query', 'admin/Ads/listQuery');
Route::any('ads/list/Delete', 'admin/Ads/listDelete');
Route::any('ads/list/Add', 'admin/Ads/listAdd');
Route::any('ads/list/Edit', 'admin/Ads/listEdit');
Route::any('ads/list/Restore', 'admin/Ads/listRestore');
Route::any('ads/list/SetState', 'admin/Ads/listSetState');
Route::any('ads/list/Cache', 'admin/Ads/listCache');
/**广告列表子权限*/
//广告位置
Route::any('ads/seat', 'admin/Ads/seat');
/**广告位置子权限*/
Route::any('ads/seat/Query', 'admin/Ads/seatQuery');
Route::any('ads/seat/Delete', 'admin/Ads/seatDelete');
Route::any('ads/seat/Add', 'admin/Ads/seatAdd');
Route::any('ads/seat/Edit', 'admin/Ads/seatEdit');
Route::any('ads/seat/Restore', 'admin/Ads/seatRestore');
Route::any('ads/seat/SetState', 'admin/Ads/seatSetState');
Route::any('ads/seat/Cache', 'admin/Ads/seatCache');
/**广告位置子权限*/
//基本信息
Route::any('setting/basic', 'admin/Setting/basic');
/**基本信息子权限*/
Route::any('setting/basic/Edit', 'admin/Setting/basicEdit');
/**基本信息子权限*/
//API
Route::any('client/api', 'admin/Client/api');
/**API子权限*/
Route::any('client/api/Query', 'admin/Client/apiQuery');
Route::any('client/api/Delete', 'admin/Client/apiDelete');
Route::any('client/api/Add', 'admin/Client/apiAdd');
Route::any('client/api/Edit', 'admin/Client/apiEdit');
Route::any('client/api/Restore', 'admin/Client/apiRestore');
Route::any('client/api/SetState', 'admin/Client/apiSetState');
Route::any('client/api/Cache', 'admin/Client/apiCache');
/**API子权限*/
//微信配置
Route::any('setting/wechat', 'admin/Setting/wechat');
//地区
Route::any('regions/list', 'admin/Regions/list');
/**地区子权限*/
Route::any('regions/list/Query', 'admin/Regions/listQuery');
Route::any('regions/list/Delete', 'admin/Regions/listDelete');
Route::any('regions/list/Edit', 'admin/Regions/listEdit');
Route::any('regions/list/SetState', 'admin/Regions/listSetState');
Route::any('regions/list/Cache', 'admin/Regions/listCache');
Route::any('regions/list/Add', 'admin/Regions/listAdd');
/**地区子权限*/
//站点信息
Route::any('Config/index', 'admin/Config/index');
//管理员信息
Route::any('Admin/info', 'admin/Admin/info');
//未读消息
Route::any('Message/getUnreadCount', 'admin/Message/getUnreadCount');
//登录
Route::any('login/login', 'admin/Login/login');
//验证码
Route::any('login/captcha', 'admin/Login/captcha');
//设置权重
Route::any('auth/index/SetSort', 'admin/Auth/indexSetSort');
//设置是否需要登录
Route::any('client/api/SetLogin', 'admin/Client/apiSetLogin');
//修改自身用户信息
Route::any('admin/editSelf', 'admin/Admin/editSelf');
//文章快速排序
Route::any('article/index/SetSort', 'admin/Article/indexSetSort');
})->middleware([\app\admin\middleware\AdminAuth::class]);