<?php
//程序物理目录(入口必须定义)
define('CODE_RUNTIME_PATH', substr(dirname(__FILE__), 0, -5));
//app_name(入口必须定义)
define('APP_NAME', 'Guide');

//引入运行程序
require CODE_RUNTIME_PATH . '/Core/Common/runtime.php';
