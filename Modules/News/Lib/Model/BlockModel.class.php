<?php

/**
 * 分类模型
 */
class BlockModel extends Model {
	
	protected $tableName = 'news_block';

	protected $_validate = array(
		array('name', 'require', '区块名称必须填写', 1),
		array('tpl', 'require', '模板内容必须填写', 1),
    );
} 
