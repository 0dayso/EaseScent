<?php
return array(
	//引入扩展配置
	'LOAD_EXT_CONF' => 'common',
	//引入全局lib库
	'LOAD_EXT_LIB' => 'BaseAdmin',

	//数据库配置
	'DB_TYPE' => 'mysql',
	'DB_HOST' => 'localhost',
	'DB_NAME' => 'jiuku',
	'DB_USER' => 'root',
	'DB_PWD' => 'vertrigo',
	'DB_PORT' => '3306',
	'DB_PREFIX' => '',


	//允许上传扩展名
	'UPLOAD_ALLOW_EXT' => 'jpeg,jpg',
	//上传最大尺寸 单位 KB
	'UPLOAD_MAXSIZE' => 5000,

    //上传物理目录
	'UPLOAD_PATH' => CODE_RUNTIME_PATH . DS . 'Upload' . DS . 'Jiuku' . DS,
    //上传URL目录
	'UPLOAD_URL' => 'http://upload.wine.g/Jiuku/',

	'DEFAULT_AJAX_RETURN' => 'json',
);
