<?php

class BlockAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
		$this->assign('app', $GLOBALS['MODULES_ALLOW']);
		$this->assign('dir', $GLOBALS['HTML_DIR']);
    }

    public function del() {
        $this->_jumpGo('Sorry! 区块不允许删除', 'info');
    }

	/**
	 * 区块列表
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

	public function add() {
		if($this->isPost()) {
            $_POST['dir'] = implode(',',$_POST['dir']);
            $add_data = D('Block')->create();
            $bid = D('Block')->add($add_data);
            if($bid)
                $this->_jumpGo('区块添加成功,开始更新区块数据', 'succeed', Url('update').'&bid='.$bid);
            $this->_jumpGo('区块添加失败', 'error', Url('index'));
		}
        $domainpath = D('AdminDomainPath')->select();
        $this->assign('domainpath', $domainpath);
		$this->display();
	}

	public function edit() {
        $bid = intval($_REQUEST['bid']);
        if(!$bid)   $this->_jumpGo('参数错误', 'error', Url('index'));
		if($this->isPost()) {
            $_POST['dir'] = implode(',',$_POST['dir']);
            D('Block')->create();
            $is = D('Block')->save();
            if($is)
                $this->_jumpGo('区块更新成功,开始更新区块数据', 'succeed', Url('update').'&bid='.$bid);
            $this->_jumpGo('区块更新失败', 'error', Url('index'));
		}
        $domainpath = D('AdminDomainPath')->select();
        $this->assign('domainpath', $domainpath);
        $res = D('Block')->find($bid);
        $this->assign('res', $res);
        $this->display();
    }

    public function update(){
        $bid = intval($_GET['bid']);
        if(!$bid)   $this->_jumpGo('参数错误', 'error', Url('index'));
        $block = D('Block')->find($bid);
        if($block['type'] === 'dynamic'){
            $content = $this->update_fetch('', $block['tpl']);
        }else{
            $content = $this->update_fetch($block['php'], $block['tpl']);
        }
        $domainpath = D('AdminDomainPath')->select();
        $dirarr = explode(',', $block['dir']);
        foreach($domainpath as $key=>$val){
            $block_path = CODE_RUNTIME_PATH . DS . $val['path'] . '_Block/';
            if(in_array($val['id'],$dirarr)){
                $make = new MakeHtml($block_path);
                $res = $make->make($bid.'.html', $content);
            }else{
                @unlink($block_path . $bid . '.html');
            }
        }
        $data = array(
            'bid' => $bid,
            'htmlpath' => '/_Block/'.$bid.'.html',
            'updatetime' => time(),
            'nextupdatetime' => time() + $block['cycle'],

        );
        D('Block')->save($data);
        $this->_jumpGo('区块数据更新成功', 'succeed', Url('index'));
    }

    function update_fetch($_p_h_p_,$_t_p_l_){
        @eval($_p_h_p_);
        return $this->fetch('',$_t_p_l_);
    }
}
