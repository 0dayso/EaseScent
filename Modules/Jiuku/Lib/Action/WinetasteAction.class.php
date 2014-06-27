<?php

/**
 * 酒口感管理
 */
class WinetasteAction extends CommonAction {
	
	/**
	 * 列表
	 */
	public function index() {
		$list = $this->_list(D('Winetaste'));
		foreach($list as $key=>$val){
			$list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
			$list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
			$list[$key]['aname_s'] = String::msubstr($val['aname'],0,10);
		}
		$this->assign('list', $list);
		$this->display();
	}
	
	/**
	 * 添加
	 */
	public function add() {
		if($this->isPost()) {
			$artid = $this->_insert(D('Winetaste'));
			$this->_jumpGo('酒口感添加成功', 'succeed', Url('index'));
		}
		$this->display();
	}
	
	/**
	 * 编辑
	 */
	public function edit() {
        $id = Input::getVar($_REQUEST['id']);
        if(!$id) {
            $this->_jumpGo('参数为空!', 'error');
        }
        $model = D('Winetaste');
        if($this->isPost()) {
            $this->_update($model);
			$this->_jumpGo('酒口感编辑成功', 'succeed', Url('index'));
        }
        $wine_taste_res = $model->where(array('id' => $id))->find();
        $this->assign('vo', $wine_taste_res);
        $this->display();
	}
	
	/**
	 * 删除
	 */
	public function del() {
        //获取id
        $id = Input::getVar($_REQUEST['id']);
        //获取批量删除
        $ids = $_REQUEST['ids'];
        $model = D('Winetaste');
        if($id) {
            $map = array('id' => $id);
        } elseif($ids) {
            $map = array('id' => array('in', $ids));
        }
        if($map) {
			$data = array(
						  'is_del' => '1',
						  'last_edit_time' => time(),
						  'last_edit_aid' => $_SESSION['admin_uid'],
						  );
            $model->where($map)->save($data);
            $this->_jumpGo('删除成功','succeed', Url('index'));
        }
        $this->_jumpGo('删除失败，参数为空', 'error');
	}
	
    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $id = Input::getVar($_GET['id']);
        $status = Input::getvar($_GET['status']);
		$data = array(
					  'id' => $id,
					  'status' => $status,
					  );
		$this->_update(D('Winetaste'),$data);
		$this->_jumpGo('ID为'.$aid.'的酒口感状态更改成功', 'succeed', Url('index'));
    }
}
