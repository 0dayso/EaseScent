<?php
return array(
	//引入扩展配置
    'LOAD_EXT_CONF' => 'common,db',

    'LOAD_EXT_LIB' => 'BaseAdmin,MakeHtml',

	'DEFAULT_AJAX_RETURN' => 'json',

	//允许上传扩展名
	'UPLOAD_ALLOW_EXT' => 'jpeg,jpg,png,gif,bmp',

	//上传最大尺寸 单位 KB
	'UPLOAD_MAXSIZE' => 5000,

    //上传物理目录
    'UPLOAD_PATH' => CODE_RUNTIME_PATH . DS . 'Upload' . DS . 'News' . DS,

    //存放文章的物理路径 此处的
    'NEWS_PATH' => CODE_RUNTIME_PATH .DS . 'Html' . DS,

    //上传web目录分配
    'UPLOAD_WWWPATH' => 'http://upload.wine.branches/News/',
);
