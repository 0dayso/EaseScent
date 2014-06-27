<?php

class BlockAction extends CommonAction {
    
	/**
	 * 推荐位列表
	 */
    public function index() {
        $model = D('Block');
        $keyword = Input::getVar($_REQUEST['keyword']);
        $map = ' 1 ';
        $url = '';
        if($keyword) {
            $map .= ' AND name LIKE "%'.$keyword.'%"';
            $url .= '&keyword=' . $keyword;
        }
        $list = $this->_list($model, $map, 14, $url);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 增加新推荐位
     */
    public function add() {
        if($this->isPost()) {
            $model = D('Block');
            $this->_insert($model);
			$this->_JumpGO('推荐位增加成功', 'succeed', Url('index'));
        }
        $this->display();
    }

    /**
     * 编辑推荐位
     */
    public function edit() {
		$model = D('Block');
		if($this->isPost()) {
			$this->_update($model);
			$this->_JumpGo('推荐位置更新成功', 'succeed', Url('index'));
		}
		$this->_edit($model);
    }

    /**
     * 更新推荐位静态页面
     */
    public function update() {
        $bid = intval(Input::getVar($_GET['bid']));
        if($this->_createPage($bid)) {
            $this->_JumpGo('更新成功', 'succeed', Url('index'));
        }
        $this->_JumpGo('更新失败', 'error', Url('index'));
    }

    /**
     * 推荐位生成静态
     */
    protected function _createPage($bid) {
        $block = D('Block')->where('`bid`='.$bid)->find();
        $data = D('BlockItem')->getAidsByBid($bid);
        $this->assign('blockdata', $data);
        $tpl = $block['tpl'];
        $content = $this->fetch('', $tpl);
        $path = CODE_RUNTIME_PATH . C('BLOCK_PATH');
        $make = new MakeHtml($path);
        if($make->make($bid.'.html', $content)) {
            return true;
        } else {
            return false;
        }
    }
}
