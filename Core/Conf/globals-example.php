<?php

/**
 * 系统初始化全局变量
 */

/*-------------------------------------/GLOBALS/--------------------------------------*/

/**
 * 允许访问的项目名称数组，若不存在，ThinkPHP会自动生成项目文件在Modulesm目录中
 */
$GLOBALS['MODULES_ALLOW'] = array('Admin', 'News', 'Jiuku');

/**
 * 定义每个项目的APPKEY,用户项目间API通信,每个APPKEY不应少于32个字符
 */
$GLOBALS['APP_KEY'] = array(
    'Admin' => 'abcd',
    'News' => 'dded',
);

/**
 * ./Html目录中存放静态页面的目录列表
 */
$GLOBALS['HTML_DIR'] = array('News', 'Region', 'Www');

/**
 * Redis 存储应用访问Token过期时间
 */
$GLOBALS['API_TOKEN_TIMEOUT'] = 1800;

/**
 * Redis 存储应用访问Token Key前缀
 */
$GLOBALS['API_TOKEN_KEYPR'] = 'wine_api_';
