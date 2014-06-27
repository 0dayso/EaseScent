<?php

/**
 * 分类模型
 */
class BlockModel extends CommonModel {
	
	protected $tableName = 'admin_block';

	protected $_validate = array(
		array('name', 'require', '区块名称必须填写', 1),
		array('type', 'require', '区块类型必须填写', 1),
		array('cycle', 'checkCycle', '更新周期必须填写', 1, 'callback'),
		array('php', 'checkPhp', '更新所需的PHP语句必须填写', 1, 'callback'),
		array('tpl', 'require', '模板内容必须填写', 1),
    );

    protected function checkCycle() {
        $type = Input::getVar($_POST['type']);
        $cycle = Input::getVar($_POST['cycle']);
        if($type == 'dynamic') {
            return !empty($cycle)?true:false;
        }
    }

    protected function checkPhp() {
        $type = Input::getVar($_POST['type']);
        $php = Input::getVar($_POST['php']);
        if($type == 'dynamic') {
            return !empty($php) ? true: false;
        }
    }

    public function getBlockByBid($bid) {
        return $this->where("bid='{$bid}'")->find();
    }
} 
