<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 逸香网API接口处理入口
 * 接口请求方式:
 *   HTTP POST
 * 授权方式：
 *   1.服务器端为客户端分配appid和appkey
 *   2.客户端用appid appkey 向服务器请求,获取Access Token
 *   3.客户端利用Access Token和appid去访问指定接口
 *   注意：Access Token过期时间为100秒
 * 数据返回：
 *   在这里对返回的数据做下规范：
 *   1.返回JSON串
 *   2.返回格式类似{"errorCode":0, "errorStr":"", "result":""}
 * --------------------------------------------------------------
 * 错误信息对照表：
 * errorCode : 
 *    400001: appid或appkey错误,可能提交了空参数
 *    400002: accessToken或appid请求时为空
 *    400003: accessToken超时
 *    400004: 服务器Redis扩展不存在
 *    400005: 连接Redis服务器配置信息不存在
 *    400006: accessToken错误
 *    400007: 指定appid应用不存在或者appkey错误
 *
 * @category   Wine
 * @package    Api
 * @author     mengfk<dreamans@163.com>
 */

//程序物理目录
define('CODE_RUNTIME_PATH', substr(dirname(__FILE__),0, -4));
//注销掉外部GET
unset($_GET);
$action = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING']: 'Admin/Api/accessToken';
$act = explode('/', trim($action, '/'));

$appName = !empty($act) ? array_shift($act) : exit();
$_GET['m'] = !empty($act) ? array_shift($act) : exit();
$_GET['a'] = !empty($act) ? array_shift($act) : exit();

//app_name
define('APP_NAME', $appName);

//引入运行程序
require CODE_RUNTIME_PATH . '/Core/Common/runtime.php';
