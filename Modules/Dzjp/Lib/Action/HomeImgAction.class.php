<?php
// 首页图片管理
class HomeImgAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $_POST = $this->sanitize($_POST);
        $_GET = $this->sanitize($_GET);
    }

    public function index(){
        if($_FILES){
            if(!(isset($_GET['i']) && in_array($_GET['i'],array(1,2,3)))){
                exit(json_encode(array('error'=>1,'msg'=>'上传失败，参数异常')));
            }
            $rootpath = C('UPLOAD_PATH') . 'Dzjp/HomeImg/';
            $rooturl = C('UPLOAD_URL') . 'Dzjp/HomeImg/';
            import('@.ORG.Util.Upload');
            $upload = new Upload();
            $upload->config(array(
                'ext' => 'jpg,jpeg,gif,png',
                'size' => 5000,
                'path' => $rootpath,
                'filename' => $_GET['i'].'.jpg',
            ));
            $res = $upload->uploadFile('updimg');
            if($res['errno']) {
                exit(json_encode(array('error'=>1,'msg'=>$upload->error())));
            }
            exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
        }
        $this->display();
    }
}
