<?php
return array(
	//引入全局lib库
	'LOAD_EXT_LIB' => 'BaseAdmin,AppAccess,Api',
	//引入扩展配置
	'LOAD_EXT_CONF' => 'common,redis',

	//数据库配置
	'DB_TYPE' => 'mysql',
	'DB_HOST' => 'localhost',
	'DB_NAME' => 'jk',
	'DB_USER' => 'root',
	'DB_PWD' => 'vertrigo',
	'DB_PORT' => '3306',
	'DB_PREFIX' => 'jk_',

    //上传物理目录
	'UPLOAD_PATH' => CODE_RUNTIME_PATH . DS . 'Upload' . DS . 'Jk' . DS,
	//上传WEB目录
	'UPLOAD_URL' => 'http://upload.wine.g/Jk/',
);
