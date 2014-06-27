<?php
return array(
	//引入扩展配置
	'LOAD_EXT_CONF' => 'common,redis',
	
	//数据库配置
	'DB_TYPE' => 'mysql',
	'DB_HOST' => '192.168.1.251',
	'DB_NAME' => 'eswine',
	'DB_USER' => 'testmysql',
	'DB_PWD' => '123456789',
	'DB_PORT' => '3306',
	'DB_PREFIX' => 'sms_',
	
    // 每条短信费用
    'TEXT_PRICE' => 0.6,

	//引入全局lib库
	'LOAD_EXT_LIB' => 'BaseAdmin,Api',
);
