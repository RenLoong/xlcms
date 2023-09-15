<?php

namespace app\admin\controller;

use app\BaseController;
use app\common\utils\Config;
use app\common\utils\ResponseCode;
use think\Exception;
use think\facade\Filesystem;
use think\Request;

class Uploads extends BaseController
{
    public function index(Request $request)
    {
        $action = $request->post('action');
        try {
            $file = $request->file('file');
            if ($file && $file->isValid()) {
                switch ($action) {
                    case 'admin/avatar':
                        break;
                    case 'ads/image':
                        break;
                    case 'goods/covers':
                        break;
                    case 'goods/content':
                        break;
                    case 'article/thumb':
                        break;
                    case 'editor/image':
                        break;
                    case 'x-upload/upload':
                        break;
                    default:
                        throw new Exception("ACTION NOT ALLOWED");
                }
                $filePath = Filesystem::putFile($action, $file);
                return $this->json([
                    'errno' => 0,
                    'code' => ResponseCode::SUCCESS,
                    'data' => [
                        'action' => $action,
                        'url' => Config::get('xlcms_site.img_url') . '/uploads/' . $filePath,
                        'path' => '/uploads/' . $filePath
                    ]
                ]);
            }
            throw new Exception("上传失败");
        } catch (\Throwable $th) {
            return $this->json([
                'errno' => ResponseCode::FAIL,
                'code' => ResponseCode::FAIL,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
