<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 初始化运行时必须的代码
 *
 * @category   Wine
 * @package    Core
 * @author     mengfk<dreamans@163.com>
 */

if(!defined('APP_NAME')) die('APP_NAME undefined!');

//目录分隔符
define('DS', DIRECTORY_SEPARATOR);

//通用配置文件目录
define('COMMON_CONF_PATH', CODE_RUNTIME_PATH . DS . 'Core' . DS . 'Conf' . DS);
//公共函数文件目录
define('COMMON_FUNC_PATH', CODE_RUNTIME_PATH . DS . 'Core' . DS .'Common' . DS);
//公共类库目录
define('COMMON_LIB_PATH', CODE_RUNTIME_PATH . DS . 'Core' . DS .'Lib' . DS);
//HTML物理目录
define('COMMON_HTML_PATH', CODE_RUNTIME_PATH . DS . 'Html' . DS);
//上传物理目录
define('COMMON_UPLOAD_PATH', CODE_RUNTIME_PATH . DS . 'Upload' . DS);

//引入初始化全局变量和启动所需函数
require COMMON_CONF_PATH . 'globals.php';

//判断app是否合法
if(!in_array(APP_NAME, $GLOBALS['MODULES_ALLOW'])) {
    header("Content-Type:text/html; charset=utf-8");
    die('Wine.cn Error: 404 not found.(这只是个临时错误信息,系统上线后会替换成真正的404 error,看到这个错误说明你要访问的项目不存在,请检查项目名或./Core/Conf/globals.php配置文件)');
}

//debug
define('APP_DEBUG', true);

/**
 * 解决Session传递问题
 * 可通过GET方式传递sessionId
 */
$phpSessId = session_name();
if(isset($_REQUEST[$phpSessId])) {
    session_id($_REQUEST[$phpSessId]);
}

//应用路径
$appPath = CODE_RUNTIME_PATH  . DS . 'Modules' . DS . APP_NAME . DS;

//app_path
define('APP_PATH', $appPath);

require CODE_RUNTIME_PATH . DS . 'Core/TP/ThinkPHP.php';
