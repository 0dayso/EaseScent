<?php

return array(

     //加载 权限配置
    'LOAD_EXT_CONFIG' => 'power_check_config,seo_config,goods_config,user_config',
     //引入全局函数库
    'LOAD_EXT_FUNC'         => 'common',
    'URL_MODEL'             => 0,
    'SESSION_AUTO_START'    => true,
    'DEFAULT_CHARSET'       => 'utf-8',
    //项目参数GET中关键字
    'VAR_APPNAME'           => 'app',
    //session中Cookie作用域
    'SESSION_OPTIONS'       => array(
        'domain'            => 'expo_s.com',
    ),


    'LANG_SWITCH_ON'   => true,
    'LANG_AUTO_DETECT' => true,            // 自动侦测语言 开启多语言功能后有效
    'DEFAULT_LANG'     => 'zh-cn',         // 默认语言
    'LANG_LIST'        => 'zh-cn,en-us',   // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'     => 'l',             // 默认语言切换变量


    //前后台 入口判断
    'DEFAULT_MODULE'  => defined('EXPO_ADMIN') ? 'Admin' : 'Index',
    'URL_HTML_SUFFIX' =>'.html',
    'DEFAULT_ACTION'  => 'index',
    'DB_TYPE'         => 'mysql',
    'DB_HOST'         => 'localhost',
    'DB_NAME'         => 'eswine',
    'DB_USER'         => 'root',
    'DB_PWD'          => '',
    'DB_PORT'         => '3306',
    'DB_PREFIX'       => 'expo_',
    'DEFAULT_AJAX_RETURN' => 'json',
    //'URL_MODEL' => 0,
    ////允许上传扩展名
    'UPLOAD_ALLOW_EXT' => 'jpeg,jpg,png,gif,bmp',
    //上传最大尺寸 单位 KB
    'UPLOAD_MAXSIZE' => 5000,
    //上传物理目录
    'UPLOAD_PATH' => CODE_RUNTIME_PATH . DS . 'Upload' . DS . 'Expo' . DS,

    //上传WEB目录
    'UPLOAD_WWWPATH' => 'http://upload.expo_s.com/Expo/',

    'APP_DEBUG'=>true,
    //替换系统变量
    'TMPL_PARSE_STRING' => array(
    '__PUBLIC__'  => 'http://root.expo_s.com/public',
    '__WEBSITE__' => 'http://www.expo_s.com',

    ),

    /*Editor 上传路径 物理路径*/
    'EXPO_EDITOR_PATH'   => CODE_RUNTIME_PATH.DS.'Upload'.DS.'Expo'.DS.'attached'.DS,
    /*Editor 上传路径 url路径*/
    'EXPO_EDITOR_IMGURL' => 'http://upload.expo_s.com/Expo/attached/',
    'GOODS_PAGE_NUM'=>9,


    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => TMPL_PATH.'Common/error.html',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => TMPL_PATH.'Common/dispatch_jump.html',
    'SHOW_PAGE_TRACE'=>true,



    'WEBSITE' => 'http://www.expos.com',
    //redis前缀
    'RedisName' => 'Wine:Expo',

    'VAR_FILTERS'=>'htmlspecialchars',//安全过滤 防止xss

    //'YM_ROOT' => "/Upload/bfile/b_用户ID/m_pic.后缀",//企业通行证图片

    'YM_DOMAIN' => 'http://user.expo_s.com',

    'API_DOMAIN' => 'http://api.expo_s.com',

    'UPLOAD_DOMAIN'  =>'http://upload.expo_s.com',

    'YMUPLOAD_WWWPATH' => 'http://upload.expo_s.com/Ym/',

);
