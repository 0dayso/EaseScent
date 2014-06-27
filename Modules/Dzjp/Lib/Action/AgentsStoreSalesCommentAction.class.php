<?php
// 代理商实体店评论管理
class AgentsStoreSalesCommentAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }
    /**
     * 代理商实体店评论列表
     */
    public function index(){
        if($this->isPost()) $this->listpage_post_to_get();
        $map = array();
        $url = '';
        if($_GET['keyword']) {
            $map['comment'] = array('like', '%'.$_GET['keyword'].'%');
            $url .= '&keyword=' . $_GET['keyword'];
        }
        if($_GET['status']) {
            $map['status'] = $_GET['status'];
            $url .= '&status='.$_GET['status'];
        }else{
            $map['status'] = array('in',array(2,3));
        }
        $count = D('AgentsStoreSalesComment')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('AgentsStoreSalesComment')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['scomment'] = $this->mystrcut($val['comment'],24,'...');
            $list[$key]['store_sales_name'] = D('AgentsStoreSales')->where(array('id'=>$val['store_sales_id']))->getfield('name');
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }
    /**
     * 代理商实体店评论编辑
     */
    public function edit() {
        $_REQUEST['id'] = intval($_REQUEST['id']);
        if(!$_REQUEST['id'])
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['c_score'] = intval($_POST['c_score']);
            $_POST['e_score'] = intval($_POST['e_score']);
            $_POST['s_score'] = intval($_POST['s_score']);
            $_POST['comment'] = trim($_POST['comment']);
            $_POST['status'] = intval($_POST['status']);
            if(!($_POST['c_score'] > 0 && $_POST['c_score'] <= 10)
                || !($_POST['e_score'] > 0 && $_POST['e_score'] <= 10)
                || !($_POST['s_score'] > 0 && $_POST['s_score'] <= 10)
                || !$_POST['comment']
                || !in_array($_POST['status'], array(1,2,3)))
                $this->_jumpGo('编辑失败，代入信息错误', 'error');
            D('AgentsStoreSalesComment')->create();
            $is = D('AgentsStoreSalesComment')->save();
            if($is === false)
                $this->_jumpGo('编辑失败', 'error');
            $store_sales_id = D('AgentsStoreSalesComment')->where(array('id'=>$_REQUEST['id']))->getfield('store_sales_id');
            $this->upd_comment_count($store_sales_id);
            $this->upd_avg_score($store_sales_id);
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $res = D('AgentsStoreSalesComment')->where(array('id'=>$_REQUEST['id']))->find();
        if(!$res)
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        $res['store_sales_name'] = D('AgentsStoreSales')->where(array('id'=>$res['store_sales_id']))->getfield('name');
        $this->assign("res", $res);
        $this->display();
    }

    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $id = intval($_GET['id']);
        $is = D('AgentsStoreSalesComment')->save(array('id'=>$id,'status'=>$_GET['status']));
        if($is === false)
            $this->_jumpGo('更改失败', 'error', base64_decode($_REQUEST['_backpage']));
        $store_sales_id = D('AgentsStoreSalesComment')->where(array('id'=>$id))->getfield('store_sales_id');
        $this->upd_comment_count($store_sales_id);
            $this->upd_avg_score($store_sales_id);
        $this->_jumpGo('更改成功', 'succeed', base64_decode($_REQUEST['_backpage']));
    }

    /**
     * 删除
     */
    public function del() {
        $id = intval($_GET['id']);
        $is = D('AgentsStoreSalesComment')->save(array('id'=>$id,'status'=>1));
        if($is === false)
            $this->_jumpGo('删除失败', 'error', base64_decode($_REQUEST['_backpage']));
        $store_sales_id = D('AgentsStoreSalesComment')->where(array('id'=>$id))->getfield('store_sales_id');
        $this->upd_comment_count($store_sales_id);
            $this->upd_avg_score($store_sales_id);
        $this->_jumpGo('删除成功', 'succeed', base64_decode($_REQUEST['_backpage']));
    }

    function upd_comment_count($id){
        $comment_count = D('AgentsStoreSalesComment')->where(array('store_sales_id'=>$id,'status'=>3))->count();
        D('AgentsStoreSales')->save(array('id'=>$id,'comment_count'=>$comment_count));
    }
    function upd_avg_score($id){
        $avg_score = D('AgentsStoreSalesComment')->field('round(avg(c_score),1) AS avg_c_score,round(avg(e_score),1) AS avg_e_score,round(avg(s_score),1) AS avg_s_score')->where(array('store_sales_id'=>$id,'status'=>3))->find();
        D('AgentsStoreSales')->save(array('id'=>$id,'c_score'=>$avg_score['avg_c_score'],'e_score'=>$avg_score['avg_e_score'],'s_score'=>$avg_score['avg_s_score']));
    }
}