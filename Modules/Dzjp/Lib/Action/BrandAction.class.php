<?php
// 品牌馆管理
class BrandAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $_POST = $this->sanitize($_POST);
        $_GET = $this->sanitize($_GET);
    }

    public function index(){
        $this->display();
    }
    //葡萄酒管理
    public function grapeIndex(){
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
        $map['status'] = array('in', array('0','1'));
        if(isset($_GET['status'])){
            if(in_array($_GET['status'], array('0','1'))){
                $map['status'] = $_GET['status'];
                $url .= '&status=' . $_GET['status'];
            }else{
                unset($_GET['status']);
            }
        }
        $count = D()->table('dzjp_brand')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('dzjp_brand')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            if($val['type'] == 1){
                $wine_count = D()->query('SELECT count(1) AS `count` FROM (SELECT 1 FROM `dzjp_winery_top_wine` A INNER JOIN `jiuku_wine_caname` B ON B.`id` = A.`wine_cname_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`is_merge` = \'-1\' INNER JOIN `jiuku_wine` C ON C.`id` = B.`wine_id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' AND C.`merge_id` = 0 WHERE A.`status` = 1 AND A.`brand_id` = '.$val['id'].') view');
            }elseif($val['type'] == 3){
                $wine_count = D()->query('SELECT count(1) AS `count` FROM (SELECT 1 FROM `dzjp_winery_top_wine` A INNER JOIN `bjk_wine` B ON B.`id` = A.`wine_cname_id` AND B.`status` = 3 WHERE A.`status` = 1 AND A.`brand_id` = '.$val['id'].') view');
            }elseif($val['type'] == 2){
                $wine_count = D()->query('SELECT count(1) AS `count` FROM (SELECT 1 FROM `dzjp_winery_top_wine` A INNER JOIN `ljk_wine` B ON B.`id` = A.`wine_cname_id` AND B.`status` = 3 WHERE A.`status` = 1 AND A.`brand_id` = '.$val['id'].') view');
            }
            $list[$key]['wine_count'] = $wine_count[0]['count'];
        }
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function grapeAdd(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('regionlvlIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品牌名称”为必填项。','_backurl'=>$_backurl)));
            }
            if(!in_array($_POST['type'],array('1','2','3'))){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品牌类型”为必选项。','_backurl'=>$_backurl)));
            }
            if($_POST['description'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品牌描述”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['logo'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品牌logo”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['sort_num'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“排序”为必填项。','_backurl'=>$_backurl)));
            }
            if(($_POST['type'] == 1 && count($_POST['h_wine_id']) == 0) || ($_POST['type'] == 3 && count($_POST['b_wine_id']) == 0) || ($_POST['type'] == 2 && count($_POST['l_wine_id']) == 0)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“已选酒款列表”为必需项。','_backurl'=>$_backurl)));
            }
            $_POST['create_time'] = time();
            //insert
            D('Brand')->create();
            if(!$id = D('Brand')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            if($_POST['type'] == 1){
                foreach($_POST['h_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'brand_id' => $id,
                        'wine_cname_id' => $val,
                        'sort_num' => $_POST['h_wine_sortnum'][$key],
                        'status' => 1,
                        'create_time' => $_POST['create_time'],
                    );
                }
            }elseif($_POST['type'] == 3){
                foreach($_POST['b_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'brand_id' => $id,
                        'wine_cname_id' => $val,
                        'sort_num' => $_POST['b_wine_sortnum'][$key],
                        'status' => 1,
                        'create_time' => $_POST['create_time'],
                    );
                }
            }elseif($_POST['type'] == 2){
                foreach($_POST['l_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'brand_id' => $id,
                        'wine_cname_id' => $val,
                        'sort_num' => $_POST['l_wine_sortnum'][$key],
                        'status' => 1,
                        'create_time' => $_POST['create_time'],
                    );
                }
            }
            D('WineryTopWine')->addall($wine_data);
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
        $this->display();
    }
    public function grapeEdit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('regionlvlIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品牌名称”为必填项。','_backurl'=>$_backurl)));
            }
            if(!in_array($_POST['type'],array('1','2','3'))){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品牌类型”为必选项。','_backurl'=>$_backurl)));
            }
            if($_POST['description'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品牌描述”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['logo'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品牌logo”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['sort_num'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“排序”为必填项。','_backurl'=>$_backurl)));
            }
            if(($_POST['type'] == 1 && count($_POST['h_wine_id']) == 0) || ($_POST['type'] == 3 && count($_POST['b_wine_id']) == 0) || ($_POST['type'] == 2 && count($_POST['l_wine_id']) == 0)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“已选酒款列表”为必需项。','_backurl'=>$_backurl)));
            }
            $_POST['update_time'] = time();
            //update
            D('Brand')->create();
            if(false === $is = D('Brand')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            D('WineryTopWine')->where(array('brand_id'=>$_POST['id']))->delete();
            if($_POST['type'] == 1){
                foreach($_POST['h_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'brand_id' => $_POST['id'],
                        'wine_cname_id' => $val,
                        'sort_num' => $_POST['h_wine_sortnum'][$key],
                        'status' => 1,
                        'create_time' => $_POST['create_time'],
                    );
                }
            }elseif($_POST['type'] == 3){
                foreach($_POST['b_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'brand_id' => $_POST['id'],
                        'wine_cname_id' => $val,
                        'sort_num' => $_POST['b_wine_sortnum'][$key],
                        'status' => 1,
                        'create_time' => $_POST['create_time'],
                    );
                }
            }elseif($_POST['type'] == 2){
                foreach($_POST['l_wine_id'] as $key=>$val){
                    $wine_data[] = array(
                        'brand_id' => $_POST['id'],
                        'wine_cname_id' => $val,
                        'sort_num' => $_POST['l_wine_sortnum'][$key],
                        'status' => 1,
                        'create_time' => $_POST['create_time'],
                    );
                }
            }
            D('WineryTopWine')->addall($wine_data);
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('dzjp_brand')->where(array('id'=>$_GET['id'],'status'=>array('in',array('1','0'))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        if($res['type'] == 1){
            $res['h_wine_res'] = D()->query('SELECT B.`id`,CONCAT(B.`cname`,\' \',C.`fname`) AS `label`,A.`sort_num` FROM `dzjp_winery_top_wine` A INNER JOIN `jiuku_wine_caname` B ON B.`id` = A.`wine_cname_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`is_merge` = \'-1\' INNER JOIN `jiuku_wine` C ON C.`id` = B.`wine_id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' AND C.`merge_id` = 0 WHERE A.`status` = 1 AND A.`brand_id` = '.$res['id']);
        }elseif($res['type'] == 3){
            $res['b_wine_res'] = D()->query('SELECT B.`id`,CONCAT(B.`cname`,\' \',B.`ename`) AS `label`,A.`sort_num` FROM `dzjp_winery_top_wine` A INNER JOIN `bjk_wine` B ON B.`id` = A.`wine_cname_id` AND B.`status` = 3 WHERE A.`status` = 1 AND A.`brand_id` = '.$res['id']);
        }elseif($res['type'] == 2){
            $res['l_wine_res'] = D()->query('SELECT B.`id`,CONCAT(B.`cname`,\' \',B.`ename`) AS `label`,A.`sort_num` FROM `dzjp_winery_top_wine` A INNER JOIN `ljk_wine` B ON B.`id` = A.`wine_cname_id` AND B.`status` = 3 WHERE A.`status` = 1 AND A.`brand_id` = '.$res['id']);
        }
        $this->assign('res', $res);
        $this->display();
    }
    public function grapeUpload(){
        $rootpath = C('UPLOAD_PATH') . 'Dzjp/BrandLogo/';
        $rooturl = C('UPLOAD_URL') . 'Dzjp/BrandLogo/';
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
    function getWine(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($_POST['type'] == 'h'){
            if($kw == ''){
                $list = D()->query('SELECT A.`id`,B.`fname`,A.`cname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' ORDER BY A.`id` ASC LIMIT '.$count);
                $result = $list ? $list : array();
            }else{
                $exist_ids = array();
                $where_eq = ' AND (A.`cname` = \''.$kw.'\' OR B.`fname` = \''.$kw.'\') ';
                $list_eq = D()->query('SELECT A.`id`,B.`fname`,A.`cname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' '.$where_eq.' ORDER BY A.`id` ASC LIMIT '.$count);
                foreach($list_eq as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
                if($count > 0){
                    $where_leftlike = ' AND (A.`cname` LIKE \''.$kw.'%\' OR B.`fname` LIKE \''.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $where_leftlike .= ' AND A.`id` NOT IN ('.implode(',',$exist_ids).')';   
                    }
                    $list_leftlike = D()->query('SELECT A.`id`,B.`fname`,A.`cname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' '.$where_leftlike.' ORDER BY A.`id` ASC LIMIT '.$count);
                    foreach($list_leftlike as $key=>$val){
                        $result[] = $val;
                        $exist_ids[] = $val['id'];
                    }
                    $count = $count - count($result);
                }
                if($count > 0){
                    $where_like = ' AND (A.`cname` LIKE \'%'.$kw.'%\' OR B.`fname` LIKE \'%'.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $where_like .= ' AND A.`id` NOT IN ('.implode(',',$exist_ids).')';   
                    }
                    $list_like = D()->query('SELECT A.`id`,B.`fname`,A.`cname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' '.$where_like.' ORDER BY A.`id` ASC LIMIT '.$count);
                    foreach($list_like as $key=>$val){
                        $result[] = $val;
                    }
                    $count = $count - count($result);
                }
            }
        }elseif($_POST['type'] == 'b'){
            if($kw == ''){
                $list = D()->query('SELECT `id`,`cname`,`ename` FROM `bjk_wine` WHERE `status` = 3 ORDER BY `id` ASC LIMIT '.$count);
                $result = $list ? $list : array();
            }else{
                $exist_ids = array();
                $where_eq = ' AND (`cname` = \''.$kw.'\' OR `ename` = \''.$kw.'\') ';
                $list_eq = D()->query('SELECT `id`,`cname`,`ename` FROM `bjk_wine` WHERE `status` = 3 '.$where_eq.' ORDER BY `id` ASC LIMIT '.$count);
                foreach($list_eq as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
                if($count > 0){
                    $where_leftlike = ' AND (`cname` LIKE \''.$kw.'%\' OR `ename` LIKE \''.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $where_leftlike .= ' AND `id` NOT IN ('.implode(',',$exist_ids).')';   
                    }
                    $list_leftlike = D()->query('SELECT `id`,`cname`,`ename` FROM `bjk_wine` WHERE `status` = 3 '.$where_leftlike.' ORDER BY `id` ASC LIMIT '.$count);
                    foreach($list_leftlike as $key=>$val){
                        $result[] = $val;
                        $exist_ids[] = $val['id'];
                    }
                    $count = $count - count($result);
                }
                if($count > 0){
                    $where_like = ' AND (`cname` LIKE \'%'.$kw.'%\' OR `ename` LIKE \'%'.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $where_like .= ' AND `id` NOT IN ('.implode(',',$exist_ids).')';   
                    }
                    $list_like = D()->query('SELECT `id`,`cname`,`ename` FROM `bjk_wine` WHERE `status` = 3 '.$where_like.' ORDER BY `id` ASC LIMIT '.$count);
                    foreach($list_like as $key=>$val){
                        $result[] = $val;
                    }
                    $count = $count - count($result);
                }
            }
        }elseif($_POST['type'] == 'l'){
            if($kw == ''){
                $list = D()->query('SELECT `id`,`cname`,`ename` FROM `ljk_wine` WHERE `status` = 3 ORDER BY `id` ASC LIMIT '.$count);
                $result = $list ? $list : array();
            }else{
                $exist_ids = array();
                $where_eq = ' AND (`cname` = \''.$kw.'\' OR `ename` = \''.$kw.'\') ';
                $list_eq = D()->query('SELECT `id`,`cname`,`ename` FROM `ljk_wine` WHERE `status` = 3 '.$where_eq.' ORDER BY `id` ASC LIMIT '.$count);
                foreach($list_eq as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
                if($count > 0){
                    $where_leftlike = ' AND (`cname` LIKE \''.$kw.'%\' OR `ename` LIKE \''.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $where_leftlike .= ' AND `id` NOT IN ('.implode(',',$exist_ids).')';   
                    }
                    $list_leftlike = D()->query('SELECT `id`,`cname`,`ename` FROM `ljk_wine` WHERE `status` = 3 '.$where_leftlike.' ORDER BY `id` ASC LIMIT '.$count);
                    foreach($list_leftlike as $key=>$val){
                        $result[] = $val;
                        $exist_ids[] = $val['id'];
                    }
                    $count = $count - count($result);
                }
                if($count > 0){
                    $where_like = ' AND (`cname` LIKE \'%'.$kw.'%\' OR `ename` LIKE \'%'.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $where_like .= ' AND `id` NOT IN ('.implode(',',$exist_ids).')';   
                    }
                    $list_like = D()->query('SELECT `id`,`cname`,`ename` FROM `ljk_wine` WHERE `status` = 3 '.$where_like.' ORDER BY `id` ASC LIMIT '.$count);
                    foreach($list_like as $key=>$val){
                        $result[] = $val;
                    }
                    $count = $count - count($result);
                }
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
}
