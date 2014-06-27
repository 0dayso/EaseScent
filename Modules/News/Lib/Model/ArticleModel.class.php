<?php

class ArticleModel extends CommonModel {

	protected $tableName = 'news_article';

    protected $_validate = array(
		array('catid','number','分类必须选择',1),
		array('title', 'require', '文章标题不能为空', 1),
	);
	
	protected $_auto = array(
		array('dateline', 'strtotime', 3, 'function'),	
	);
}
