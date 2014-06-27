<?php
// 产区管理
class WineryAction extends CommonAction {
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
        if(isset($_GET['country_id'])){
            if($_GET['country_id'] != 0){
                $map['country_id'] = $_GET['country_id'];
                $url .= '&country_id=' . $_GET['country_id'];
            }else{
                unset($_GET['country_id']);
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
        $count = D()->table('jk_d_winery')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('jk_d_winery')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['country_res'] = D()->table('jk_d_country')->where(array('id'=>$val['country_id'],'status'=>array('in',array(2,3))))->find();
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
            if($_POST['fname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['ename'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“英文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jk_d_winery')->where(array('fname'=>$_POST['fname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //insert
            D('DWinery')->create();
            if(!$id = D('DWinery')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            //葡萄品种
            foreach($_POST['grapes'] as $key=>$grape){
                $grape_adddata[] = array('grape_id'=>$grape, 'winery_id'=>$id, 'percent'=>intval($_POST['grapes_percent'][$key]));
            }
            D()->table('jk_r_winery_grape')->addAll($grape_adddata);
            //酒庄荣誉
            foreach($_POST['wineryhnrs'] as $key=>$wineryhnr){
                $wineryhnr_adddata[] = array('wineryhnr_id'=>$wineryhnr, 'winery_id'=>$id);
            }
            D()->table('jk_r_winery_wineryhnr')->addAll($wineryhnr_adddata);
            //相关图片
            foreach($_POST['imgs'] as $key=>$img){
                $img_adddata[] = array('img'=>$img, 'queue'=>$key, 'fid'=>$id);
            }
            D()->table('jk_d_winery_img')->addAll($img_adddata);
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
        $this->assign('wineryhnr_treelist',D('DWineryhnr')->getTreeList());
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
            if($_POST['fname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“外文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['ename'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“英文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“中文名”为必填项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jk_d_winery')->where(array('id'=>array('neq',$_POST['id']),'fname'=>$_POST['fname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“外文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //update
            D('DWinery')->create();
            if(false === $is = D('DWinery')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            //葡萄品种
            D()->table('jk_r_winery_grape')->where(array('winery_id'=>$_POST['id']))->delete();
            foreach($_POST['grapes'] as $key=>$grape){
                $grape_adddata[] = array('grape_id'=>$grape, 'winery_id'=>$_POST['id'], 'percent'=>intval($_POST['grapes_percent'][$key]));
            }
            D()->table('jk_r_winery_grape')->addAll($grape_adddata);
            //酒庄荣誉
            D()->table('jk_r_winery_wineryhnr')->where(array('winery_id'=>$_POST['id']))->delete();
            foreach($_POST['wineryhnrs'] as $key=>$wineryhnr){
                $wineryhnr_adddata[] = array('wineryhnr_id'=>$wineryhnr, 'winery_id'=>$_POST['id']);
            }
            D()->table('jk_r_winery_wineryhnr')->addAll($wineryhnr_adddata);
            //相关图片
            D()->table('jk_d_winery_img')->where(array('fid'=>$_POST['id']))->delete();
            foreach($_POST['imgs'] as $key=>$img){
                $img_adddata[] = array('img'=>$img, 'queue'=>$key, 'fid'=>$_POST['id']);
            }
            D()->table('jk_d_winery_img')->addAll($img_adddata);
            exit(json_encode(array('error'=>0,'msg'=>'修改成功','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('jk_d_winery')->where(array('id'=>$_GET['id'],'status'=>array('in',array(2,3))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $res['country_res'] = D()->table('jk_d_country')->where(array('id'=>$res['country_id'],'status'=>array('in',array(2,3))))->find();
        $res['grape_res'] = D()->table('jk_r_winery_grape A')->join('jk_d_grape B ON A.grape_id=B.id')->field('B.id,B.fname,B.cname,A.percent')->where(array('A.winery_id'=>$_GET['id'],'B.status'=>array('in',array(2,3))))->select();
        $res['wineryhnr_res'] = D()->table('jk_r_winery_wineryhnr A')->join('jk_d_wineryhnr B ON A.wineryhnr_id=B.id')->where(array('A.winery_id'=>$_GET['id'],'B.status'=>array('in',array(2,3))))->select();
        $res['img_res'] = D()->table('jk_d_winery_img')->where(array('fid'=>$_GET['id']))->order('queue ASC')->select();
        $this->assign('res', $res);
        $this->assign('wineryhnr_treelist',D('DWineryhnr')->getTreeList());
        $this->display();
    }
    public function upload(){
        if($_FILES['updimg']){
            $rootpath = C('UPLOAD_PATH') . 'Winery/images/';
            $rooturl = C('UPLOAD_URL') . 'Winery/images/';
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
            //生成缩略图
            import('@.ORG.Util.Image');
            $image = new Image();
            $filepath = $res['rootpath'] . $res['subpath'] . $res['filename'];
            $image->thumb2($filepath, $res['rootpath'] . '300_225/' . $res['subpath'] . $res['filename'], 300, 225);
            //生成缩略图END
            exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
        }elseif($_FILES['imgFile']){
            $rootpath = C('UPLOAD_PATH') . 'Winery/uploads/';
            $rooturl = C('UPLOAD_URL') . 'Winery/uploads/';
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