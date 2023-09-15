<?php

/**
 * Here is your custom functions.
 */
function p($d)
{
    echo '<pre>';
    print_r($d);
    echo '</pre>';
}
function pexception($th)
{
    print_r([
        'file' => $th->getFile(),
        'line' => $th->getLine(),
        'msg' => $th->getMessage()
    ]);
}
/**
 * 递归实现无限极分类
 * @param $array 分类数据
 * @param $id 父ID
 * @param $pid 下级id键
 * @return $key 下级键名
 */
function getTree($array, $id = 'id', $pid = 'pid', $key = 'children')
{
    //第一步 构造数据
    $items = array();
    foreach ($array as $value) {
        $items[$value[$id]] = $value;
    }
    //第二部 遍历数据 生成树状结构
    $tree = array();
    foreach ($items as $k => $value) {
        if (isset($items[$value[$pid]])) {
            if (!isset($items[$value[$pid]][$key])) {
                $items[$value[$pid]][$key] = [];
            }
            $items[$value[$pid]][$key][] = &$items[$k];
        }
    }
    // 当所有项遍历完成后再生成树状结构，防止不完整
    foreach ($items as $item) {
        if ($item[$pid] == 0) {
            $tree[] = $item;
        }
    }
    return $tree;
}

/**
 * 删除字符串中空格
 * @param $str 字符串
 * @return string 无空格字符串
 */
function trimall($str)
{
    $qian = array(" ", "　", "\t", "\n", "\r");
    $hou = array("", "", "", "", "");
    return str_replace($qian, $hou, $str);
}
function rgbtoarray($rgb)
{
    if (empty($rgb)) {
        return [];
    }
    $rgb = str_replace('rgb(', '', $rgb);
    $rgb = str_replace(')', '', $rgb);
    $rgb = explode(',', $rgb);
    return $rgb;
}
function arraytorgb($arr)
{
    if (empty($arr) || count($arr) != 3) {
        return '';
    }
    return 'rgb(' . implode(',', $arr) . ')';
}
