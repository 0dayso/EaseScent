<?php
// 葡萄品种管理
class GrapeAction extends CommonAction {
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
        if(isset($_GET['color'])){
            if($_GET['color'] != ''){
                $map['color'] = $_GET['color'];
                $url .= '&color=' . $_GET['color'];
            }else{
                unset($_GET['color']);
            }
        }
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
                $map_k['ename'] = array('like', '%'.$_GET['keyword'].'%');
                $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
                $map_k['_logic'] = 'or';
                $map['_complex'] = $map_k;
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
        $count = D()->table('jk_d_grape')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('jk_d_grape')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
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
            dump($_POST);exit;
            if($_POST['fname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['ename'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“英文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”为必填项。','_backurl'=>$_backurl)));
            }
            if(empty($_POST['color'])){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“颜色”为必选项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jk_d_grape')->where(array('fname'=>$_POST['fname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //insert
            D('DGrape')->create();
            if(!$id = D('DGrape')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            //相关图片
            foreach($_POST['imgs'] as $key=>$img){
                $img_adddata[] = array('img'=>$img, 'queue'=>$key, 'fid'=>$id);
            }
            D()->table('jk_d_grape_img')->addAll($img_adddata);
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
            if($_POST['fname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['ename'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“英文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”为必填项。','_backurl'=>$_backurl)));
            }
            if(empty($_POST['color'])){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“颜色”为必选项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jk_d_grape')->where(array('id'=>array('neq',$_POST['id']),'fname'=>$_POST['fname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //update
            D('DGrape')->create();
            if(false === $is = D('DGrape')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            //相关图片
            D()->table('jk_d_grape_img')->where(array('fid'=>$_POST['id']))->delete();
            foreach($_POST['imgs'] as $key=>$img){
                $img_adddata[] = array('img'=>$img, 'queue'=>$key, 'fid'=>$_POST['id']);
            }
            D()->table('jk_d_grape_img')->addAll($img_adddata);
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('jk_d_grape')->where(array('id'=>$_GET['id'],'status'=>array('in',array(2,3))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $res['img_res'] = D()->table('jk_d_grape_img')->where(array('fid'=>$_GET['id']))->order('queue ASC')->select();
        $this->assign('res', $res);
        $this->display();
    }
    public function upload(){
        if($_FILES['updimg']){
            $rootpath = C('UPLOAD_PATH') . 'Grape/images/';
            $rooturl = C('UPLOAD_URL') . 'Grape/images/';
            import('@.ORG.Util.Upload');
            $upload = new Upload();
            $upload->config(array(
                'ext' => 'jpg,jpeg,gif,png',
                'size' => 5000,
                'path' => $rootpath,
            ));
            $res = $upload->uploadFile('updimg');
            if($res['errno']) {
                exit(json_encode(array('error'=>1,'msg'=>$upload->error())));
            }
            exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
        }elseif($_FILES['imgFile']){
            $rootpath = C('UPLOAD_PATH') . 'Grape/uploads/';
            $rooturl = C('UPLOAD_URL') . 'Grape/uploads/';
            import('@.ORG.Util.Upload');
            $upload = new Upload();
            $upload->config(array(
                'ext' => 'jpg,jpeg,gif,png',
                'size' => 5000,
                'path' => $rootpath,
            ));
            $res = $upload->uploadFile('imgFile');
            if($res['errno']) {
                exit(json_encode((array('error'=>1,'message'=>$upload->error()))));
            }
            exit(json_encode((array('error'=>0,'url'=>$rooturl . $res['subpath'] . $res['filename']))));
        }
    }
}