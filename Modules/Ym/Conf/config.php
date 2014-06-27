<?php
/**
 加载数据库配置 
 */
if(is_file(dirname(__FILE__)."/db.php")){
    include dirname(__FILE__)."/db.php";
}
/**
通用配置
*/
/**
通用配置
*/
$config =  array(
    //'配置项'=>'配置值'

    'LOAD_EXT_LIB' => 'BaseAdmin,Api',
    /*****多模板支持*****/
    'TMPL_SWITCH_ON'=>true,
    'TMPL_DETECT_THEME'=>true,
    'DEFAULT_THEME'=>'default',
    /********语言包*********/
    'LANG_SWITCH_ON' => true,
    'DEFAULT_LANG' =>'ym-zh',//默认语言的文件夹是cn     
    'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效     
    'LANG_LIST'        => 'ym-zh,ym-en', // 允许切换的语言列表 用逗号分隔     
    'VAR_LANGUAGE'     => 'l', // 默认语言切换变量


    /****************/
    'DEFAULT_MODULE' => 'Index',
    'URL_CASE_INSENSITIVE'=>1,
    'URL_MODEL'=>2,
    'VAR_FILTERS'=>'htmlspecialchars',//安全过滤 防止xss
    'SESSION_USER_SIGN'=>"Ym_SIGN",
    'NOT_AUTH_MODULE'=>"User",
    'TMPL_ACTION_ERROR'=>"Public:error",
    'TMPL_ACTION_SUCCESS'=>"Public:success",
    'SET_LANG'=>'zh-cn',
    'AUTH_TIME'=>7*24*60*60,
    'COOKIE_DOMAIN'=>'.cn.local',
    //替换系统变量
    'TMPL_PARSE_STRING' => array(
            '__HOST__'    => "http://".$_SERVER["HTTP_HOST"],
            '__PUBLIC__'  => 'http://public.wine.cn.local',
            '__CSS__'     => 'http://public.wine.cn.local/Ym/css',
            '__JS__'      => 'http://public.wine.cn.local/Ym/js',
            '__CJS__'     => 'http://public.wine.cn.local/common/js',
            '__IMG__'     => 'http://public.wine.cn.local/Ym/images',
            '__BIZ__'     => 'http://ibiz2.wine.cn.local',
            '__UPLOAD__'  => 'http://upload.wine.cn.local',
            ),
    'LOAD_EXT_FUNC' => 'common',
    'LOAD_EXT_CONF' => 'redis',
    'MAIL_ADDRESS'=>'user@wine.cn', // 邮箱地址
    'MAIL_SMTP'=>'smtp.ym.163.com', // 邮箱SMTP服务器
    'MAIL_LOGINNAME'=>'user@wine.cn', // 邮箱登录帐号
    'MAIL_PASSWORD'=>'89ty25Rrkq', // 邮箱密码
    'SESSION_OPTIONS' => array(
        'domain' => '.cn.local',
    ),
);
return array_merge($config,$conf_db);
