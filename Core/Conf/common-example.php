<?php

if (!defined('THINK_PATH')) exit();

/**
 * 系统公共配置文件，可通过LOAD_EXT_CONFIG引入
 */
return array(
	//默认模块
	'DEFAULT_MODULE' => 'Index',

	//确保项目正常访问，此设置请保持为0
	'URL_MODEL' => 0,

	'SESSION_AUTO_START' => true,
    
	'DEFAULT_CHARSET' => 'utf-8',

	//替换系统变量
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__'  => 'Public',
		'__UPLOAD__'  => 'http://upload.wien.cn',
	),

	//项目参数GET中关键字
	'VAR_APPNAME' => 'app',

	//引入全局函数库
	'LOAD_EXT_FUNC' => 'common',
	
	//session中Cookie作用域
    'SESSION_OPTIONS' => array(
        'domain' => '.wine.branches',
    ),
);
