<?php
return array(
	//引入扩展配置
	'LOAD_EXT_CONF' => 'common',
	
	//数据库配置
	'DB_TYPE' => 'mysql',
	'DB_HOST' => 'localhost',
	'DB_NAME' => 'eswine-wine-cn',
	'DB_USER' => 'root',
	'DB_PWD' => 'admin',
	'DB_PORT' => '3306',
	'DB_PREFIX' => 'jiuku_',
	
	
	//引入全局lib库
	'LOAD_EXT_LIB' => 'BaseAdmin,AppAccess',
    //上传物理目录
    'UPLOAD_PATH' => CODE_RUNTIME_PATH . DS . 'Upload' . DS . 'Jiuku' . DS,
);
