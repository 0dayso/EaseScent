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
		'__PUBLIC__'		=> 'http://public.wine.cn.local/',
		'__UPLOAD__'		=> 'http://upload.wine.cn.local/',
		'__AJAX__'		=> 'http://ajax.wine.cn.local/',
		'__API__'		=> 'http://api.wine.cn.local/',
		'__WWW__'		=> 'http://www.wine.cn.local/',
		'__USER__'		=> 'http://user.wine.cn.local/',
		'__NEWS__'		=> 'http://news.wine.cn.local/',
		'__COLUMN__'		=> 'http://column.wine.cn.local/',
		'__EXPO__'		=> 'http://expo.wine.cn.local/',
		'__EN__'		=> 'http://en.wine.cn.local/',
		'__JIUHUI__'		=> 'http://jiuhui.wine.cn.local/',
		'__WINESHOW__'		=> 'http://wineshow.wine.cn.local/',
		'__PEIXUN__'		=> 'http://peixun.wine.cn.local/',
		'__TUAN__'		=> 'http://tuan.wine.cn.local/',
		'__I__'			=> 'http://i.wine.cn.local/',
		'__JOB__'		=> 'http://job.wine.cn.local/',
		'__MINGZHUANG__'	=> 'http://mingzhuang.wine.cn.local/',
		'__REGION__'		=> 'http://region.wine.cn.local/',
		'__BOOK__'		=> 'http://book.wine.cn.local/',
		'__D__'			=> 'http://d.wine.cn.local/',
		'__BUY__'		=> 'http://buy.wine.cn.local/',
		'__58__'		=> 'http://58.wine.cn.local/',
		'__BBS__'		=> 'http://bbs.wine.cn.local/',
		'__WWW_ESWINE__'	=> 'http://www.eswine.cn.local/',
	),

	'DOMAIN' => array(
		'PUBLIC'		=> 'http://public.wine.cn.local/',
		'UPLOAD'		=> 'http://upload.wine.cn.local/',
		'AJAX'			=> 'http://ajax.wine.cn.local/',
		'API'			=> 'http://api.wine.cn.local/',
		'WWW'			=> 'http://www.wine.cn.local/',
		'USER'			=> 'http://user.wine.cn.local/',
		'NEWS'			=> 'http://news.wine.cn.local/',
		'COLUMN'		=> 'http://column.wine.cn.local/',
		'EXPO'			=> 'http://expo.wine.cn.local/',
		'EN'			=> 'http://en.wine.cn.local/',
		'JIUHUI'		=> 'http://jiuhui.wine.cn.local/',
		'WINESHOW'		=> 'http://wineshow.wine.cn.local/',
		'PEIXUN'		=> 'http://peixun.wine.cn.local/',
		'TUAN'			=> 'http://tuan.wine.cn.local/',
		'I'			=> 'http://i.wine.cn.local/',
		'I_API'			=> 'http://api.i.wine.cn.local/',
		'JOB'			=> 'http://job.wine.cn.local/',
		'MINGZHUANG'		=> 'http://mingzhuang.wine.cn.local/',
		'REGION'		=> 'http://region.wine.cn.local/',
		'BOOK'			=> 'http://book.wine.cn.local/',
		'D'			=> 'http://d.wine.cn.local/',
		'BUY'			=> 'http://buy.wine.cn.local/',
		'58'			=> 'http://58.wine.cn.local/',
		'BBS'			=> 'http://bbs.wine.cn.local/',
		'WWW_ESWINE'		=> 'http://www.eswine.com/',
		'ACTIVITY'		=> 'http://activity.wine.cn.local/',
		'TOPIC' 		=> 'http://topic.wine.cn.local',
	),

	//项目参数GET中关键字
	'VAR_APPNAME' => 'app',

	//引入全局函数库
	'LOAD_EXT_FUNC' => 'common',
	
	//session中Cookie作用域
    'SESSION_OPTIONS' => array(
        'domain' => '.cn.local',
    ),
);

