<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 企业通行证入口文件
 *
 * @category   Wine
 * @package    Ym
 * @author     mengfk<dreamans@163.com>
 */
//程序物理目录(入口必须定义)
define('CODE_RUNTIME_PATH', substr(dirname(__FILE__),0, -3));

//app_name(入口必须定义)
define('APP_NAME', 'Ym');

//引入运行程序
require CODE_RUNTIME_PATH . '/Core/Common/runtime.php';
