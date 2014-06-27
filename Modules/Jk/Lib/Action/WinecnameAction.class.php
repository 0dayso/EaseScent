<?php
// 酒款中文名管理
class WinecnameAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $_POST = $this->sanitize($_POST);
        $_GET = $this->sanitize($_GET);
    }

    public function index(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $map = array();
        $url = '';
        if(isset($_GET['wine_id'])){
            if($_GET['wine_id'] != 0){
                $map['wine_id'] = $_GET['wine_id'];
                $url .= '&wine_id=' . $_GET['wine_id'];
            }else{
                unset($_GET['wine_id']);
            }
        }
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map['cname'] = array('like', '%'.$_GET['keyword'].'%');
                $url .= '&keyword=' . $_GET['keyword'];
            }else{
                unset($_GET['keyword']);
            }
        }
        $map['status'] = array('in', array(2,3));
        if(isset($_GET['status'])){
            if(in_array($_GET['status'], array(2,3))){
                $map['status'] = $_GET['status'];
                $url .= '&status=' . $_GET['status'];
            }else{
                unset($_GET['status']);
            }
        }
        $count = D()->table('jk_d_winecname')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('jk_d_winecname')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['wine_res'] = D()->table('jk_d_wine')->where(array('id'=>$val['wine_id'],'status'=>array('in',array(2,3))))->find();
        }
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function add(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('index');
        //提交
        if($this->isPost()){
            //POST信息判断
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['wine_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“所属酒款”为必选项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jk_d_winecname')->where(array('cname'=>$_POST['cname'],'status'=>array(2,3)))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //主别名唯一性判断操作
            if($_POST['iskey'] == 1){
                D()->table('jk_d_winecname')->where(array('wine_id'=>$_POST['wine_id']))->save(array('iskey'=>0));
            }
            //insert
            D('DWinecname')->create();
            if(!$id = D('DWinecname')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
        $this->display();
    }
    public function edit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('index');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“中文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['wine_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“所属酒款”为必选项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jk_d_winecname')->where(array('id'=>array('neq',$_POST['id']),'cname'=>$_POST['cname'],'status'=>array(2,3)))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //主别名唯一性判断操作
            if($_POST['iskey'] == 1){
                D()->table('jk_d_winecname')->where(array('wine_id'=>$_POST['wine_id']))->save(array('iskey'=>0));
            }
            //update
            D('DWinecname')->create();
            if(false === $is = D('DWinecname')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('jk_d_winecname')->where(array('id'=>$_GET['id'],'status'=>array('in',array(2,3))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $res['wine_res'] = D()->table('jk_d_wine')->where(array('id'=>$res['wine_id'],'status'=>array('in',array(2,3))))->find();
        $this->assign('res', $res);
        $this->display();
    }
}