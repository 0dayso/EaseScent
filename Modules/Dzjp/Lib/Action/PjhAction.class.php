<?php
// 品鉴会管理
class PjhAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $_POST = $this->sanitize($_POST);
        $_GET = $this->sanitize($_GET);
    }

    //列表
    public function index(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $map = array();
        $url = '';
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map_k['name'] = array('like', '%'.$_GET['keyword'].'%');
                $map_k['_logic'] = 'or';
                $map['_complex'] = $map_k;
                $url .= '&keyword=' . $_GET['keyword'];
            }else{
                unset($_GET['keyword']);
            }
        }
        if(isset($_GET['type'])){
            if(in_array($_GET['type'], array('1','2','3'))){
                $map['type'] = $_GET['type'];
                $url .= '&type=' . $_GET['type'];
            }else{
                unset($_GET['type']);
            }
        }
        $map['status'] = array('in', array('2','3'));
        if(isset($_GET['status'])){
            if(in_array($_GET['status'], array('2','3'))){
                $map['status'] = $_GET['status'];
                $url .= '&status=' . $_GET['status'];
            }else{
                unset($_GET['status']);
            }
        }
        $count = D()->table('dzjp_pjh')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('dzjp_pjh')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            if($val['type'] == 1){
                $wine_count = D()->query('SELECT count(1) AS `count` FROM (SELECT 1 FROM `dzjp_pjh_wine` A INNER JOIN `jiuku_wine_caname` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`is_merge` = \'-1\' INNER JOIN `jiuku_wine` C ON C.`id` = B.`wine_id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' AND C.`merge_id` = 0 WHERE A.`fid` = '.$val['id'].') view');
            }elseif($val['type'] == 2){
                $wine_count = D()->query('SELECT count(1) AS `count` FROM (SELECT 1 FROM `dzjp_pjh_wine` A INNER JOIN `bjk_wine` B ON B.`id` = A.`wine_id` AND B.`status` = 3 WHERE A.`fid` = '.$val['id'].') view');
            }elseif($val['type'] == 3){
                $wine_count = D()->query('SELECT count(1) AS `count` FROM (SELECT 1 FROM `dzjp_pjh_wine` A INNER JOIN `ljk_wine` B ON B.`id` = A.`wine_id` AND B.`status` = 3 WHERE A.`fid` = '.$val['id'].') view');
            }
            $list[$key]['wine_count'] = $wine_count[0]['count'];
        }
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
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品鉴会名称”为必填项。','_backurl'=>$_backurl)));
            }
            if(!in_array($_POST['type'],array('1','2','3'))){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品鉴会类型”为必选项。','_backurl'=>$_backurl)));
            }
            if($_POST['description'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品鉴会描述”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['logo'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品鉴会logo”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['queue'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“排序”为必填项。','_backurl'=>$_backurl)));
            }
            if(($_POST['type'] == 1 && count($_POST['h_wine_id']) == 0) || ($_POST['type'] == 2 && count($_POST['b_wine_id']) == 0) || ($_POST['type'] == 3 && count($_POST['l_wine_id']) == 0)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“已选酒款列表”为必需项。','_backurl'=>$_backurl)));
            }
            //insert
            $maxid = D()->table('dzjp_pjh')->max('id');
            $insert_id = ($maxid == 0) ? 1 : ($maxid + 1);
            $insert_data = array(
                'id' => $insert_id,
                'name' => $_POST['name'],
                'logo' => $_POST['logo'],
                'description' => $_POST['description'],
                'type' => $_POST['type'],
                'queue' => $_POST['queue'],
                'status' => $_POST['status'],
            );
            if(!$is = D()->table('dzjp_pjh')->add($insert_data)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            if($_POST['type'] == 1){
                foreach($_POST['h_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'fid' => $insert_id,
                        'wine_id' => $val,
                        'queue' => $_POST['h_queue'][$key],
                    );
                }
            }elseif($_POST['type'] == 2){
                foreach($_POST['b_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'fid' => $insert_id,
                        'wine_id' => $val,
                        'queue' => $_POST['b_queue'][$key],
                    );
                }
            }elseif($_POST['type'] == 3){
                foreach($_POST['l_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'fid' => $insert_id,
                        'wine_id' => $val,
                        'queue' => $_POST['l_queue'][$key],
                    );
                }
            }
            D('PjhWine')->addall($wine_data);
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
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
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品鉴会名称”为必填项。','_backurl'=>$_backurl)));
            }
            if(!in_array($_POST['type'],array('1','2','3'))){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品鉴会类型”为必选项。','_backurl'=>$_backurl)));
            }
            if($_POST['description'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品鉴会描述”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['logo'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品鉴会logo”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['queue'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“排序”为必填项。','_backurl'=>$_backurl)));
            }
            if(($_POST['type'] == 1 && count($_POST['h_wine_id']) == 0) || ($_POST['type'] == 2 && count($_POST['b_wine_id']) == 0) || ($_POST['type'] == 3 && count($_POST['l_wine_id']) == 0)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“已选酒款列表”为必需项。','_backurl'=>$_backurl)));
            }
            //update

            $update_data = array(
                'id' => $_POST['id'],
                'name' => $_POST['name'],
                'logo' => $_POST['logo'],
                'description' => $_POST['description'],
                'type' => $_POST['type'],
                'queue' => $_POST['queue'],
                'status' => $_POST['status'],
            );
            if(false === $is = D()->table('dzjp_pjh')->save($update_data)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            D('PjhWine')->where(array('fid'=>$_POST['id']))->delete();
            if($_POST['type'] == 1){
                foreach($_POST['h_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'fid' => $_POST['id'],
                        'wine_id' => $val,
                        'queue' => $_POST['h_queue'][$key],
                    );
                }
            }elseif($_POST['type'] == 2){
                foreach($_POST['b_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'fid' => $_POST['id'],
                        'wine_id' => $val,
                        'queue' => $_POST['b_queue'][$key],
                    );
                }
            }elseif($_POST['type'] == 3){
                foreach($_POST['l_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'fid' => $_POST['id'],
                        'wine_id' => $val,
                        'queue' => $_POST['l_queue'][$key],
                    );
                }
            }
            D('PjhWine')->addall($wine_data);
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('dzjp_pjh')->where(array('id'=>$_GET['id'],'status'=>array('in',array('2','3'))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        if($res['type'] == 1){
            $res['h_wine_res'] = D()->query('SELECT B.`id`,CONCAT(B.`cname`,\' \',C.`fname`) AS `label`,A.`queue` FROM `dzjp_pjh_wine` A INNER JOIN `jiuku_wine_caname` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`is_merge` = \'-1\' INNER JOIN `jiuku_wine` C ON C.`id` = B.`wine_id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' AND C.`merge_id` = 0 WHERE A.`fid` = '.$res['id']);
        }elseif($res['type'] == 2){
            $res['b_wine_res'] = D()->query('SELECT B.`id`,CONCAT(B.`cname`,\' \',B.`ename`) AS `label`,A.`queue` FROM `dzjp_pjh_wine` A INNER JOIN `bjk_wine` B ON B.`id` = A.`wine_id` AND B.`status` = 3 WHERE A.`fid` = '.$res['id']);
        }elseif($res['type'] == 3){
            $res['l_wine_res'] = D()->query('SELECT B.`id`,CONCAT(B.`cname`,\' \',B.`ename`) AS `label`,A.`queue` FROM `dzjp_pjh_wine` A INNER JOIN `ljk_wine` B ON B.`id` = A.`wine_id` AND B.`status` = 3 WHERE A.`fid` = '.$res['id']);
        }
        $this->assign('res', $res);
        $this->display();
    }
    public function upload(){
        $rootpath = C('UPLOAD_PATH') . 'Dzjp/Pjhimg/';
        $rooturl = C('UPLOAD_URL') . 'Dzjp/Pjhimg/';
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
        //获取图片信息
        import('@.ORG.Util.Image');
        $image = new Image();
        $imginfo = $image->getImageInfo($rootpath.$res['subpath'].$res['filename']);
        if($imginfo['width'] != 296 || $imginfo['height'] != 300){
            exit(json_encode(array('error'=>1,'msg'=>'上传失败，尺寸不符合要求,请上传296*300的图片')));
        }
        exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
    }
}
