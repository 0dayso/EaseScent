<?php

class AcAction extends CommonAction {

    public function index() {
        $model = D('Ac');
        $lists = $model->getAcList();
        $this->assign('list', $lists);
        $this->display();
    }

    /**
     * 增加权限节点
     */
    public function add() {
        $model = D('Ac');
        if($this->isPost()) {
            $this->_insert($model);
            $this->_jumpGo('权限节点增加成功', 'succeed', Url('index'));
        }
        $pid = Input::getVar($_GET['pid']);
        $pac = $model->where('acid='.$pid)->find();
        $this->assign('pac', $pac);
        $this->assign('pid', $pid);
        $this->display();
    }

    /**
     * 编辑权限节点
     */
    public function edit() {
        $model = D('Ac');
        if($this->isPost()) {
            $this->_update($model);
            $this->_jumpGo('节点更新成功', 'succeed', Url('index'));
        }
        $this->_edit($model);
    }

    /**
     * 删除节点
     */
    public function del() {
        $this->_delete(D('Ac'));
        $this->_jumpGo('删除成功', 'succeed', Url('index'));
    }

}
