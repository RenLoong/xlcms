<?php

namespace app\common\utils;

use think\response\Json as Response;

/**
 * JSON响应工具
 */
trait Json
{
    /**
     * 返回成功JSON
     *
     * @param string $msg 消息
     * @param mixed $data 数据
     * @return Response
     */
    public static function success($msg = 'success', $data = [])
    {
        return self::json(['code' => ResponseCode::SUCCESS, 'message' => $msg, 'data' => $data]);
    }
    /**
     * 返回失败JSON
     *
     * @param string $msg 消息
     * @param mixed $data 数据
     * @return Response
     */
    public static function fail(string $msg = 'fail', $data = [])
    {
        return self::json(['code' => ResponseCode::FAIL, 'message' => $msg, 'data' => $data]);
    }
    public static function code($code, $msg = null, $data = [])
    {
        return self::json(['code' => $code, 'message' => $msg, 'data' => $data]);
    }
    /**
     * 返回异常消息
     *
     * @param \Throwable $th 捕获的异常
     * @return Response
     */
    public static function exception($th)
    {
        return self::json(['code' => $th->getCode() ? $th->getCode() : ResponseCode::FAIL, 'message' => $th->getMessage(), 'data' => ['file' => $th->getFile(), 'line' => $th->getLine()]]);
    }
    /**
     * 响应失败
     *
     * @param \Throwable $th 捕获的异常
     * @return Response
     */
    public static function server($th, $http_code = 500)
    {
        return self::json(['code' => $th->getCode() ? $th->getCode() : ResponseCode::FAIL, 'message' => $th->getMessage(), 'data' => ['file' => $th->getFile(), 'line' => $th->getLine()]], $http_code);
    }
    /**
     * 返回JSON
     *
     * @param mixed $data JSON数据
     * @param int|null $options JSON编码
     * @param int $http_code 服务器响应代码
     * @return Response
     */
    public static function json($data, $http_code = 200)
    {
        return json($data, $http_code);
    }
}
