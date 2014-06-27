<?php
return array (
		'LOAD_EXT_CONF' => 'common',
		// 数据库配置
		'DB_TYPE' => 'mysql',
		'DB_HOST' => '127.0.0.1',
		'DB_NAME' => 'winesino',
		'DB_USER' => 'root',
		'DB_PWD' => 'root',
		'DB_PORT' => '3306',
		'DB_PREFIX' => 'es_',
		'APP_AUTOLOAD_PATH' => '@.TagLib',
		'TAGLIB_BUILD_IN' => 'Cx,Article',
		'TOPICS_HTML_PATH' => CODE_RUNTIME_PATH.'/Html/Topic',
		'TMPL_PARSE_STRING' => array(
					'__TOPICS__'=>'http://topic.wine.cn',
				 ),
);
?>
