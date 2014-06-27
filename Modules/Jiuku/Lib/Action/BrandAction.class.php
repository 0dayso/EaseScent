<?php
// 品牌管理
class BrandAction extends Common2Action {
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
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
                $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
                $map_k['_logic'] = 'or';
                $map['_complex'] = $map_k;
            }else{
                unset($_GET['keyword']);
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
        $count = D()->table('jiuku_brand')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('jiuku_brand')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
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
            if(($_POST['fname'] == '') && ($_POST['cname'] == '')){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”或“中文名”至少添加一项。','_backurl'=>$_backurl)));
            }
            if(($_POST['fname'] != '') && ($is_exist = D()->table('jiuku_brand')->where(array('fname'=>$_POST['fname'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“外文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            if(($_POST['fname'] != '') && ($is_exist = D()->table('jiuku_brand')->where(array('cname'=>$_POST['cname'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //insert
            $maxid = D()->table('jiuku_brand')->max('id');
            $insert_data = array(
                'id' => $maxid + 1,
                'fname' => $_POST['fname'],
                'cname' => $_POST['cname'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
            );
            if(!$is = D()->table('jiuku_brand')->add($insert_data)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            //相关图片
            foreach($_POST['imgs'] as $key=>$img){
                $img_data[] = array('img'=>$img,'queue'=>$key,'fid'=>$insert_data['id']);
            }
            D()->table('jiuku_brand_img')->addAll($img_data);
            //关联数据
            foreach($_POST['relation_id'] as $relation_id){
                $relation_data[] = array('fid'=>$insert_data['id'],'relation_id'=>$relation_id);
            }
            D()->table('jiuku_brand_relation')->addAll($relation_data);
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
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
            if(($_POST['fname'] == '') && ($_POST['cname'] == '')){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“外文名”或“中文名”至少添加一项。','_backurl'=>$_backurl)));
            }
            if(($_POST['fname'] != '') && ($is_exist = D()->table('jiuku_brand')->where(array('id'=>array('neq',$_POST['id']),'fname'=>$_POST['fname'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“外文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            if(($_POST['fname'] != '') && ($is_exist = D()->table('jiuku_brand')->where(array('id'=>array('neq',$_POST['id']),'cname'=>$_POST['cname'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //update
            $update_data = array(
                'id' => $_POST['id'],
                'fname' => $_POST['fname'],
                'cname' => $_POST['cname'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
            );
            if(false === $is = D()->table('jiuku_brand')->save($update_data)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            //相关图片
            D()->table('jiuku_brand_img')->where(array('fid'=>$update_data['id']))->delete();
            foreach($_POST['imgs'] as $key=>$img){
                $img_data[] = array('img'=>$img,'queue'=>$key,'fid'=>$update_data['id']);
            }
            D()->table('jiuku_brand_img')->addAll($img_data);
            //关联数据
            D()->table('jiuku_brand_relation')->where(array('fid'=>$update_data['id']))->delete();
            foreach($_POST['relation_id'] as $relation_id){
                $relation_data[] = array('fid'=>$update_data['id'],'relation_id'=>$relation_id);
            }
            D()->table('jiuku_brand_relation')->addAll($relation_data);
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('jiuku_brand')->where(array('id'=>$_GET['id'],'status'=>array('in',array(2,3))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $res['img_res'] = D()->table('jiuku_brand_img')->where(array('fid'=>$_GET['id']))->order('queue ASC')->select();
        $relation_ids_res = D()->query('SELECT `relation_id` FROM `jiuku_brand_relation` WHERE `fid` = '.$_GET['id']);
        $relation1_ids = $relation2_ids = $relation3_ids = array();
        foreach($relation_ids_res as $val){
            if($val['relation_id'] < 10000000){
                $relation1_ids[] = $val['relation_id'];
            }elseif($val['relation_id'] < 20000000){
                $relation2_ids[] = $val['relation_id'];
            }elseif($val['relation_id'] < 30000000){
                $relation3_ids[] = $val['relation_id'];
            }
        }
        if($relation1_ids){
            $res['relation1_res'] = D()->query('SELECT A.`id`,CONCAT(A.`cname`,\' \',B.`fname`) AS `name` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_merge` = \'-1\' AND A.`is_del` = \'-1\' AND A.`id` IN ('.implode(',',$relation1_ids).')');
        }
        if($relation2_ids){
            $res['relation2_res'] = D()->query('SELECT `id`,CONCAT(`cname`,\' \',`ename`) AS `name` FROM `bjk_wine` WHERE `status` = 3 AND `id` IN ('.implode(',',$relation2_ids).')');
        }
        if($relation3_ids){
            $res['relation3_res'] = D()->query('SELECT `id`,CONCAT(`cname`,\' \',`ename`) AS `name` FROM `ljk_wine` WHERE `status` = 3 AND `id` IN ('.implode(',',$relation3_ids).')');
        }
        $this->assign('res', $res);
        $this->display();
    }
    public function upload(){
        if($_FILES['updimg']){
            $rootpath = C('UPLOAD_PATH') . 'Brand/images/';
            $rooturl = C('DOMAIN.UPLOAD') . 'Jiuku/Brand/images/';
            import('@.ORG.Util.Upload2');
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
            //import('@.ORG.Util.Image');
            //$image = new Image();
            //$filepath = $res['rootpath'] . $res['subpath'] . $res['filename'];
            //$image->thumb2($filepath, $res['rootpath'] . '160_160/' . $res['subpath'] . $res['filename'], 160, 160, 0, 60);
            //$image->thumb3($filepath, $res['rootpath'] . '90_120/' . $res['subpath'] . $res['filename'], 90, 120);
            //$image->thumb3($filepath, $res['rootpath'] . '180_240/' . $res['subpath'] . $res['filename'], 180, 240);
            //$image->thumb3($filepath, $res['rootpath'] . '600_600/' . $res['subpath'] . $res['filename'], 600, 600);
            //生成缩略图END
            exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
        }elseif($_FILES['imgFile']){
        }
    }
    /*function getRelationForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($_POST['type'] == 1){
            $sql = 'SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_merge` = \'-1\' AND A.`is_del` = \'-1\' ';
            if(preg_match("/^(-|\+)?\d+$/",$kw)){
                if($list = D()->query($sql . 'AND A.`id` = '.$kw)){
                    $result[] = array(
                        'id' => $list[0]['id'],    
                        'name' => $list[0]['cname'] . ' ['.$list[0]['fname'].']',
                    );
                }
            }elseif($kw == ''){
                $list = D()->query($sql . 'ORDER BY A.id ASC LIMIT '.$count);
                foreach($list as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'name' => $val['cname'] . ' ['.$val['fname'].']',
                    );
                }
            }else{
                $exist_ids = array();
                $list_eq = D()->query($sql . 'AND (A.`cname` = \''.$kw.'\' OR B.`fname` = \''.$kw.'\') ORDER BY A.`id` LIMIT '.$count);
                foreach($list_eq as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'name' => $val['cname'] . ' ['.$val['fname'].']',
                    );
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
                if($count > 0){
                    $leftlike_sql = 'AND (A.`cname` like \''.$kw.'%\' OR B.`fname` like \''.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $leftlike_sql .= 'AND A.`id` NOT IN ('.implode(',',$exist_ids).') ';
                    }
                    $leftlike_sql .= 'ORDER BY A.`id` LIMIT '.$count;
                    $list_leftlike = D()->query($sql . $leftlike_sql);
                    foreach($list_leftlike as $key=>$val){
                        $result[] = array(
                            'id' => $val['id'],
                            'name' => $val['cname'] . ' ['.$val['fname'].']',
                        );
                        $exist_ids[] = $val['id'];
                    }
                    $count = $count - count($result);
                }
                if($count > 0){
                    $like_sql = 'AND (A.`cname` like \'%'.$kw.'%\' OR B.`fname` like \'%'.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $like_sql .= 'AND A.`id` NOT IN ('.implode(',',$exist_ids).') ';
                    }
                    $like_sql .= 'ORDER BY A.`id` LIMIT '.$count;
                    $list_like = D()->query($sql . $like_sql);
                    foreach($list_like as $key=>$val){
                        $result[] = array(
                            'id' => $val['id'],
                            'name' => $val['cname'] . ' ['.$val['fname'].']',
                        );
                    }
                    $count = $count - count($result);
                }
            }
        }elseif($_POST['type'] == 2){
            sleep(1);
        }else{
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }*/
}
