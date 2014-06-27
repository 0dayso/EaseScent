<?php
return array(
    //引入全局lib库
    'LOAD_EXT_LIB' => 'BaseAdmin,AppAccess',
    //引入扩展配置
    'LOAD_EXT_CONF' => 'common',
    
    //数据库配置
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'wine_cn',
    'DB_USER' => 'root',
    'DB_PWD' => 'root',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'dzjp_',
    
    //大众酒评酒标扫描记录目录URL
    'SCAN_WINE_LABEL_URL_PATH' => 'http://api.weibo.quwine.com/var/uploads/scanimages/',
    //上传物理目录
    'UPLOAD_PATH' => CODE_RUNTIME_PATH . DS . 'Upload' . DS,
    //上传web目录
    'UPLOAD_URL' => 'http://upload.wine.cn.local/',
    
);
