<?php

class ArticleContentModel extends CommonModel {
	
	protected $tableName = 'news_article_content';
	
	protected $_validate = array(
		array('aid','number','新闻主表插入异常',1),
	);
}
