<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 逸香网后台管理平台入口文件
 * 可对此进行口令加密
 *
 * @category   Wine
 * @package    Admin
 * @author     mengfk<dreamans@163.com>
 */

//程序物理目录(入口必须定义)
define('CODE_RUNTIME_PATH', substr(dirname(__FILE__),0, -6));

$appName = (isset($_GET['app']) && !empty($_GET['app'])) ? $_GET['app'] : 'Admin';

//app_name(入口必须定义)
define('APP_NAME', $appName);

//引入运行程序
require CODE_RUNTIME_PATH . '/Core/Common/runtime.php';
