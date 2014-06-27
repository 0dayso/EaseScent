<?php

class UserAction extends CommonAction {

    /**
     * 用户列表
     */
    public function index() {
        $model = D('User');
        $map = ' 1 ';
        $url = '';
        $keyword = Input::getVar($_REQUEST['keyword']);
        if($keyword) {
            $map .= ' AND (username LIKE \'%'.$keyword.'%\' OR `truename` LIKE \'%'.$keyword.'%\' OR `nickname` LIKE \'%'.$keyword.'%\' OR `email` LIKE \'%'.$keyword.'%\')';
            $url .= '&keyword=' . $keyword;
        }
        $list = $this->_list($model, $map, 14, $url);
        $ugroup = D('UserGroup')->getList();
        $this->assign('list', $list);
        $this->assign('group', $ugroup);
        $this->display();
    }
    
    /**
     * 增加用户
     */
    public function add() {
        if($this->isPost()) {
            $this->_insert(D('User'));
            $this->_jumpGo('用户添加成功', 'succeed', Url('Admin/User/index'));
        }
        $this->assign('userGroup', D('UserGroup')->getList());
        $this->display();
    }

    /**
     * 修改状态
     */
    public function chgStatus() {
        $uid = Input::getVar($_GET['uid']);
        $status = Input::getVar($_GET['status']);
        D('User')->where('uid='.$uid)->save(array('status' => $status));
        $this->_jumpGo('用户状态修改完成', 'succeed', Url('index'));
    }

    /**
     * 删除用户
     */
    public function del() {
        $this->_delete(D('User'));
        $this->_jumpGo('用户删除成功', 'succeed', Url('index'));
    }

    /**
     * 编辑用户信
     */
    public function edit() {
        $model = D('User');
        if($this->isPost()) {
            $this->_update($model);
            $this->_jumpGo('用户更新成功', 'succeed', Url('index'));
        }
        $this->assign('userGroup', D('UserGroup')->getList());
        $this->_edit($model);
    }
}
