<?php
// 管理员用户上传文件目录权限设定
class PathAcAction extends CommonAction {
    public function index(){
        if($this->isPost()){
            $uid = intval($_POST['uid']);
            $pid = $_POST['pid'];
            if(!$uid)   $this->_jumpGo('获取参数错误', 'error');
            M('UploadAc')->where(array('uid'=>$uid))->delete();
            foreach($pid as $pathid){
                $add_data[] = array('uid'=>$uid,'pathid'=>$pathid);
            }
            M('UploadAc')->addall($add_data);
            $this->_jumpGo('权限设定成功', 'succeed', Url('index').'&uid='.$uid);
        }
        $uid = $_GET['uid'] = max(1,intval($_GET['uid']));
        $upac = M('UploadAc')->where(array('uid'=>$uid))->getfield('pathid',true);
        $this->assign('upac',$upac);
        $ulist = M('AdminUser','eswine_')->where(array('status'=>'1'))->order('username asc')->select();
        $this->assign('ulist',$ulist);
        $plist = M('DomainPath','eswine_admin_')->select();
        $this->assign('plist',$plist);
        $this->display();
    }
}