<?php

class ListAction extends Action {

    public function index() {
		import('@.ORG.Util.Input');
		import('@.ORG.Util.Page');
        $catid = intval(Input::getVar($_GET['catid']));
        $page = max(1,Input::getVar($_GET['page']));
        $limit = 20;
        $begin = ($page-1)*$limit;
        $model = D('Article');
        $map = array(
                'status' => 1,
                'catid' => $catid,
                'dateline' > time(),
            );
        $lists = $model->where($map)->limit($begin, $limit)->select();
        $this->assign('lists', $lists);
        $this->display();
    }
}
