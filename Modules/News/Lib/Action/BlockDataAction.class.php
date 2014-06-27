<?php

class BlockDataAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
        $bid = intval(Input::getVar($_GET['bid']));
        $block = D('Block')->where('`bid` = '.$bid)->find();
        $this->assign('block', $block);
    }
    
	/**
	 * 推荐位数据列表
	 */
    public function index() {
        $model = D('BlockItem');
        $bid = intval(Input::getVar($_GET['bid']));
        $list = $model->getAidsByBid($bid);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 编辑数据
     */
    public function edit() {
		$model = D('BlockData');
        if($this->isPost()) {
			$this->_update($model);
			$this->_JumpGo('推荐位数据更新成功', 'succeed', Url('index?bid='.$_REQUEST['bid']));
		}
		$this->_edit($model);
    }

    /**
     * 删除推荐位数据
     */
    public function del() {
        $bid = intval($_GET['bid']);
        $aid = intval($_GET['aid']);
        $model = M('news_blockitem');
        if($model->where(array('bid' => $bid, 'aid' => $aid))->delete()) {
            $this->_jumpGo('推荐数据删除成功', 'succeed', Url('index?bid='.$_REQUEST['bid']));
        }
    }
}
