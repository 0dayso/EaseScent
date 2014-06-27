<?php
// 白酒|产地管理
class B_RegionAction extends Common2Action {
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
        if(isset($_GET['kw'])){
            if($_GET['kw'] != ''){
                if(preg_match("/^(-|\+)?\d+$/",$_GET['kw'])){
                }else{
                    $map_k['cname'] = array('like', '%'.$_GET['kw'].'%');
                    $map_k['ename'] = array('like', '%'.$_GET['kw'].'%');
                    $map_k['_logic'] = 'or';
                    $map['_complex'] = $map_k;
                }
            }else{
                unset($_GET['kw']);
            }
        }
        $map['status'] = array('in', array(2,3));
        if(isset($_GET['status'])){
            if(in_array($_GET['status'], array(2,3))){
                $map['status'] = $_GET['status'];
            }else{
                unset($_GET['status']);
            }
        }
        $count = D()->table('bjk_region')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('bjk_region')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function add(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : Url('index');
        //提交
        if($this->isPost()){
            //POST信息判断
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”为必需项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('bjk_region')->where(array('cname'=>$_POST['cname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="Url(\'index\')&kw=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            if(($_POST['ename'] != '') && ($is_exist = D()->table('bjk_region')->where(array('ename'=>$_POST['ename'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“英文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="Url(\'index\')&kw=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            //insert
            $maxid = D()->table('bjk_region')->max('id');
            $insert_id = ($maxid == 0) ? 10000000 : ($maxid + 1);
            $insert_data = array(
                'id' => $insert_id,
                'cname' => $_POST['cname'],
                'ename' => $_POST['ename'],
                'status' => $_POST['status'],
            );
            if(!$is = D()->table('bjk_region')->add($insert_data)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        $this->display();
    }
    public function edit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : Url('index');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“中文名”为必需项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('bjk_region')->where(array('id'=>array('neq',$_POST['id']),'cname'=>$_POST['cname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="Url(\'index\')&kw=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            if(($_POST['ename'] != '') && ($is_exist = D()->table('bjk_region')->where(array('id'=>array('neq',$_POST['id']),'ename'=>$_POST['ename'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“英文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="Url(\'index\')&kw=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            //update
            $update_data = array(
                'id' => $_POST['id'],
                'cname' => $_POST['cname'],
                'ename' => $_POST['ename'],
                'status' => $_POST['status'],
            );
            if(false === $is = D()->table('bjk_region')->save($update_data)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('bjk_region')->where(array('id'=>$_GET['id'],'status'=>array('in',array(2,3))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $this->assign('res', $res);
        $this->display();
    }
}
