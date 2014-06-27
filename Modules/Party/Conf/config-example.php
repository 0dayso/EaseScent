<?php

$html = array(
	//'APP_DEBUG'=> true,
	'LOAD_EXT_CONF' => 'common',
	'URL_MODEL' =>3,
	'__APP__'=>'party.wine-cn.com',
	'HTML_FILE_SUFFIX' => '.html',   // 设置静态文件后缀
	'HTML_PATH' => '__ROOT__/html',   //设置静态缓存目录
   	'HTML_CACHE_ON'=>false,
   	'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => 'Public:success', // 默认成功跳转对应的模板文件
	"signKey" => 'ckyr52i4J9zkT3zV75UG',
	'SHOW_PAGE_TRACE' =>true, // 显示页面Trace信息

	

	
    

	'DB_TYPE' => 'mysql',
	'DB_HOST' => 'localhost',
	'DB_NAME' => 'wine_cn',
	'DB_USER' => 'root',
	'DB_PWD' => '',
	'DB_PORT' => '3306',
	'DB_PREFIX' => 'es_',
	'DEFAULT_THEME'         => 'default',
	
	

	);
return $html;
?>
