<?php

class UserGroupAction extends CommonAction {
    
    public function index() {
        $app = $GLOBALS['MODULES_ALLOW'];
        $this->assign('list', D('UserGroup')->getList());
		$this->assign('app', $app);
        $this->display();
    }

    /**
     * 增加用户组
     */
    public function add() {
        if($this->isPost()) {
            $model = D('UserGroup');
            $gid = $this->_insert($model);
            //写入用户组对应权限信息
            $this->_dealAcUserGroup($gid);
            $this->_jumpGo('用户组增加成功', 'succeed', Url('index'));
        }
        $ac = D('Ac');
        $aclist = $ac->getAcList();
        $this->assign('aclist', $aclist);
        $this->display();
    }

    /**
     * 编辑用户组
     */
    public function edit() {
        $model = D('UserGroup');
        $gid = Input::getVar($_REQUEST['gid']);
        if($this->isPost()) {
            $this->_update($model);
            $this->_dealAcUserGroup($gid);
            $this->_jumpGo('用户组更新成功', 'succeed', Url('index'));
        }
        $acmodel = D('UserGroupAc');
        $acgid = $acmodel->where('gid='.$gid)->select();
        $acgidstr = $doct = '';
        if(!empty($acgid)) {
            foreach($acgid as $acval) {
                $acgidstr .=  $doct . $acval['acid'];
                $doct = ',';
            }
        }
        $aclist = D('Ac')->getAcList();

        $this->assign('acgid', $acgidstr);
        $this->assign('aclist', $aclist);
        $this->_edit($model);
    }

    /**
     * 用户组删除
     */
    public function del() {
        $model = D('UserGroup');
        $this->_delete($model);
        //删除用户组对应的权限信息
        $acmodel = D('UserGroupAc');
        //$this->_delete();
        $this->_jumpGo('用户组删除成功', 'succeed' , Url('index'));
    }

    /**
     * 处理输入的权限与用户组绑定
     */
    private function _dealAcUserGroup($gid) {
        $acmodel = D('UserGroupAc');
        $ac = Input::getVar($_POST['ac']);
        $acmodel->where('gid='.$gid)->delete();
        if(!empty($ac)) {
            $acArr = explode(',', $ac);
            //插入之前删除以前数据
            foreach($acArr as $key => $val) {
                $data = array(
                    'gid' => $gid,
                    'acid' => $val
                );
                $this->_insert($acmodel, $data);
            }
        }
    }
}
