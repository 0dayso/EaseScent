<?php

class NewsAuthAction extends CommonAction {
    
    public function index() {
        if($this->isPost()) {
            $auth = $_POST['auth'];
            $gid = intval($_POST['gid']);
            $data = array();
            if(is_array($auth)) {
                foreach($auth as $catid => $au) {
                    foreach($au as $auval) { 
                        $data[] = array(
                            'gid' =>  $gid,
                            'catid' => $catid,
                            'auth' => $auval
                        );
                    }
                }
            }
            D('NewsAc')->where(array('gid' => $gid))->delete();
            foreach($data as $value) {
                D('NewsAc')->add($value);
            }
            $this->_jumpGo('资讯栏目权限设定成功', 'succeed', Url('NewsAuth/index?gid='.$gid));
        }
        $gid = Input::getVar($_GET['gid']);
        //读取已有权限
        $auth = D('NewsAc')->getAuth($gid);
        $group = D('UserGroup')->where('`gid` = '.$gid)->find();
        //读取资讯频道栏目列表
        $cate = Api('News', 'categoryList');
        $this->assign('category', $cate);
        $this->assign('auth', $auth);
        $this->assign('group', $group);
        $this->display();
    }
}
