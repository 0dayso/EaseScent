<?php
// 代理商实体店信息报错管理
class AgentsStoreSalesErrorReportAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
        $_error_type = array(
            '1' =>'地理位置',
            '2' =>'实体店信息',
            '3' =>'实体店关闭',
            '4' =>'其他',
        );
        $this->assign('_error_type',$_error_type);
    }
    /**
     * 代理商实体店评论列表
     */
    public function index(){
        if($this->isPost()) $this->listpage_post_to_get();
        $map = array();
        $url = '';
        if($_GET['uname']) {
            $map['uname'] = array('like', '%'.$_GET['uname'].'%');
            $url .= '&uname=' . $_GET['uname'];
        }
        if($_GET['error_type']) {
            $map['error_type'] = $_GET['error_type'];
            $url .= '&error_type='.$_GET['error_type'];
        }
        if(in_array($_GET['have_feedback'], array('1','0'))) {
            $map['have_feedback'] = $_GET['have_feedback'];
            $url .= '&have_feedback='.$_GET['have_feedback'];
        }
        $count = D('AgentsStoreSalesErrorReport')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('AgentsStoreSalesErrorReport')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['sother'] = $this->mystrcut($val['other'],15,'...');
            $list[$key]['store_sales_res'] = D('AgentsStoreSales')->where(array('id'=>$val['store_sales_id']))->find();
            $list[$key]['user_res'] = M('User','sns_')->where(array('id'=>$val['uid']))->find();
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
    public function feedback() {
        $_REQUEST['id'] = intval($_REQUEST['id']);
        if(!$_REQUEST['id'])
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['uid'] = intval($_POST['uid']);
            $_POST['feedback'] = trim($_POST['feedback']);
            if(!$_POST['uid'] || !$_POST['feedback'])
                $this->_jumpGo('反馈失败，代入信息错误', 'error');
            if(!$token_res = CurlGet(C('DOMAIN.I_API').'index.php?m=api/oauth/token.get&uid='.$_POST['uid']))
                $this->_jumpGo('反馈失败，token获取错误', 'error');
            if(!$token_res = json_decode($token_res,true))
                $this->_jumpGo('反馈失败，token解析错误', 'error');
            $token = $token_res['rst'];
            $sixin_data = array('token'=>$token,'uid'=>$_POST['uid'],'msg'=>$_POST['feedback']);
            CurlPost(C('DOMAIN.I_API').'index.php?m=api/oauth/msg.publish_msg',$sixin_data);
            D('AgentsStoreSalesErrorReport')->save(array('id'=>$_POST['id'],'have_feedback'=>1));
            $this->_jumpGo('反馈成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $res = D('AgentsStoreSalesErrorReport')->where(array('id'=>$_REQUEST['id']))->find();
        if(!$res)
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        $res['store_sales_res'] = D('AgentsStoreSales')->where(array('id'=>$res['store_sales_id']))->find();
        $this->assign("res", $res);
        $this->display();
    }
}