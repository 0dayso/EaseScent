<?php
// 洋酒|酒款管理
class L_WineAction extends Common2Action {
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
        //获取where
        $map = array();
        if(isset($_GET['region_id'])){
            if($_GET['region_id'] != 0){
                $map[] = '`region_id` = '.$_GET['region_id'];
            }else{
                unset($_GET['region_id']);
            }
        }
        if(isset($_GET['type_id'])){
            if($_GET['type_id'] != 0){
                $map[] = '`type_id` = '.$_GET['type_id'];
            }else{
                unset($_GET['type_id']);
            }
        }
        if(isset($_GET['id'])){
            if($_GET['id'] != ''){
                $map[] = '`id` = '.$_GET['id'];
            }else{
                unset($_GET['id']);
            }
        }
        if(isset($_GET['barcode'])){
            if($_GET['barcode'] != ''){
                $bracode_res = D()->query('SELECT `fid` FROM `ljk_wine_barcode` WHERE `num` LIKE \'%'.$_GET['barcode'].'%\'');
                $ids = array();
                foreach($bracode_res as $val){
                    $ids[] = $val['fid'];
                }
                $map[] = $ids ? '`id` IN ('.implode(',',$ids).')' : '`id` = 0';
            }else{
                unset($_GET['barcode']);
            }
        }
        if(isset($_GET['kw'])){
            if($_GET['kw'] != ''){
                $map[] = '(`cname` LIKE \'%'.$_GET['kw'].'%\' OR `ename` LIKE \'%'.$_GET['kw'].'%\')';    
            }else{
                unset($_GET['kw']);
            }
        }
        $status_map = '`status` IN (2,3)';
        if(isset($_GET['status'])){
            if(in_array($_GET['status'], array(2,3))){
                $status_map = '`status` = '.$_GET['status'];
            }else{
                unset($_GET['status']);
            }
        }
        $map[] = $status_map;
        //list&page
        $count = D()->table('ljk_wine')->where(implode(' AND ',$map))->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('ljk_wine')->where(implode(' AND ',$map))->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $region_list = D()->table('ljk_region')->field('id,cname,ename')->where(array('status'=>3))->select();
        $region_nlist = array();
        foreach($region_list as $val){
            $region_nlist[$val['id']] = $val;
        }
        $type_list = D()->table('ljk_type')->field('id,cname,ename')->where(array('status'=>3))->select();
        $type_nlist = array();
        foreach($type_list as $val){
            $type_nlist[$val['id']] = $val;
        }
        foreach($list as $key=>$val){
            $list[$key]['region_res'] = $region_nlist[$val['region_id']];
            $list[$key]['type_res'] = $type_nlist[$val['type_id']];
        }
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign('region_list', $region_list);
        $this->assign('type_list', $type_list);
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function add(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : Url('index');
        //提交
        if($this->isPost()){
            //INI barcode判断
            if($_POST['type'] == 'ini_barcode'){
                if($is_exist = D()->query('SELECT `fid` FROM `ljk_wine_barcode` WHERE `num` = '.$_POST['barcode'])){
                    exit(json_encode(array('error'=>1,'msg'=>'增加失败！“条形码：'.$_POST['barcode'].'”重复录入，重复ID：' . $is_exist[0]['fid'] . '。<a href="'.Url('index').'&id=' . $is_exist[0]['fid'] . '" target="_blank">去看看</a>')));
                }else{
                    exit(json_encode(array('error'=>0)));
                }
            }
            //POST信息判断
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”为必需项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('ljk_wine')->where(array('cname'=>$_POST['cname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="'.Url('index').'&id=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            if(($_POST['ename'] != '') && ($is_exist = D()->table('ljk_wine')->where(array('ename'=>$_POST['ename'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“英文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="'.Url('index').'&id=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            if(count($_POST['barcodes']) == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“条形码”为必需项。','_backurl'=>$_backurl)));
            }
            foreach($_POST['barcodes'] as $barcode){
                if($is_exist = D()->query('SELECT B.`id` FROM `ljk_wine_barcode` A INNER JOIN `ljk_wine` B ON B.`id` = A.`fid` WHERE A.`num` = '.$barcode)){
                    exit(json_encode(array('error'=>1,'msg'=>'添加失败！“条形码：'.$barcode.'”重复录入，重复ID：' . $is_exist[0]['id'] . '。<a href="'.Url('index').'&id=' . $is_exist[0]['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
                }
            }
            if(!preg_match("/^[0-9]{1,2}(\.[0-9]{0,2})*$/",$_POST['alcohol_degree'])){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“酒精度”为必需项并且为至多2位整数2位小数的正数。','_backurl'=>$_backurl)));
            }
            if($_POST['capacity'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“净含量”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['region_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“所属产地”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['type_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“所属香型”为必需项。','_backurl'=>$_backurl)));
            }
            //insert
            $maxid = D()->table('ljk_wine')->max('id');
            $insert_id = ($maxid == 0) ? 10000000 : ($maxid + 1);
            $insert_data = array(
                'id' => $insert_id,
                'region_id' => $_POST['region_id'],
                'type_id' => $_POST['type_id'],
                'cname' => $_POST['cname'],
                'ename' => $_POST['ename'],
                'alcohol_degree' => $_POST['alcohol_degree'],
                'capacity' => $_POST['capacity'],
                'specificat' => $_POST['specificat'],
                'official_price' => $_POST['official_price'],
                'brew_house' => $_POST['brew_house'],
                'raw_material' => $_POST['raw_material'],
                'description' => $_POST['description'],
                'product_features' => $_POST['product_features'],
                'brand_culture' => $_POST['brand_culture'],
                'brew_process' => $_POST['brew_process'],
                'honor_awards' => $_POST['honor_awards'],
                'status' => $_POST['status'],
            );
            if(!$is = D()->table('ljk_wine')->add($insert_data)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            //图片
            $img_data = array();
            foreach($_POST['imgs'] as $val){
                $img_data[] = array(
                    'fid' => $insert_id,
                    'pic' => $val,
                );
            }
            D()->table('ljk_wine_image')->addall($img_data);
            //条形码
            $barcode_data = array();
            foreach($_POST['barcodes'] as $val){
                $barcode_data[] = array(
                    'fid' => $insert_id,
                    'num' => $val,
                );
            }
            D()->table('ljk_wine_barcode')->addall($barcode_data);
            //品牌
            $brand_data = array();
            foreach($_POST['brand_ids'] as $val){
                $brand_data[] = array(
                    'fid' => $val,
                    'relation_id' => $insert_id,
                );
            }
            D()->table('jiuku_brand_relation')->addall($brand_data);
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
        $region_list = D()->table('ljk_region')->field('id,cname,ename')->where(array('status'=>3))->select();
        $type_list = D()->table('ljk_type')->field('id,cname,ename')->where(array('status'=>3))->select();
        $this->assign('region_list', $region_list);
        $this->assign('type_list', $type_list);
        $this->display();
    }
    public function edit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : Url('index');
        //提交
        if($this->isPost()){
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            //INI barcode判断
            if($_POST['type'] == 'ini_barcode'){
                if($is_exist = D()->query('SELECT `fid` FROM `ljk_wine_barcode` WHERE `num` = '.$_POST['barcode'].' AND `fid` != '.$_POST['id'])){
                    exit(json_encode(array('error'=>1,'msg'=>'增加失败！“条形码：'.$_POST['barcode'].'”重复录入，重复ID：' . $is_exist[0]['fid'] . '。<a href="'.Url('index').'&id=' . $is_exist[0]['fid'] . '" target="_blank">去看看</a>')));
                }else{
                    exit(json_encode(array('error'=>0)));
                }
            }
            //POST信息判断
            if($_POST['cname'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“中文名”为必需项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('ljk_wine')->where(array('id'=>array('neq',$_POST['id']),'cname'=>$_POST['cname'],'status'=>array('in',array(2,3))))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“中文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="'.Url('index').'&id=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            if(($_POST['ename'] != '') && ($is_exist = D()->table('ljk_wine')->where(array('id'=>array('neq',$_POST['id']),'ename'=>$_POST['ename'],'status'=>array('in',array(2,3))))->find())){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“英文名”重复录入，重复ID：' . $is_exist['id'] . '。<a href="'.Url('index').'&id=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
            }
            if(count($_POST['barcodes']) == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“条形码”为必需项。','_backurl'=>$_backurl)));
            }
            foreach($_POST['barcodes'] as $barcode){
                if($is_exist = D()->query('SELECT B.`id` FROM `ljk_wine_barcode` A INNER JOIN `ljk_wine` B ON B.`id` = A.`fid` WHERE A.`num` = '.$barcode.' AND A.`fid` != '.$_POST['id'])){
                    exit(json_encode(array('error'=>1,'msg'=>'添加失败！“条形码：'.$barcode.'”重复录入，重复ID：' . $is_exist['id'] . '。<a href="'.Url('index').'&id=' . $is_exist['id'] . '" target="_blank">去看看</a>','_backurl'=>$_backurl)));
                }
            }
            if(!preg_match("/^[0-9]{1,2}(\.[0-9]{0,2})*$/",$_POST['alcohol_degree'])){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“酒精度”为必需项并且为至多2位整数2位小数的正数。','_backurl'=>$_backurl)));
            }
            if($_POST['capacity'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“净含量”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['region_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“所属产地”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['type_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“所属香型”为必需项。','_backurl'=>$_backurl)));
            }
            //update
            $update_data = array(
                'id' => $_POST['id'],
                'region_id' => $_POST['region_id'],
                'type_id' => $_POST['type_id'],
                'cname' => $_POST['cname'],
                'ename' => $_POST['ename'],
                'alcohol_degree' => $_POST['alcohol_degree'],
                'capacity' => $_POST['capacity'],
                'specificat' => $_POST['specificat'],
                'official_price' => $_POST['official_price'],
                'brew_house' => $_POST['brew_house'],
                'raw_material' => $_POST['raw_material'],
                'description' => $_POST['description'],
                'product_features' => $_POST['product_features'],
                'brand_culture' => $_POST['brand_culture'],
                'brew_process' => $_POST['brew_process'],
                'honor_awards' => $_POST['honor_awards'],
                'status' => $_POST['status'],
            );
            if(false === $is = D()->table('ljk_wine')->save($update_data)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            //图片
            D()->table('ljk_wine_image')->where(array('fid'=>$_POST['id']))->delete();
            $img_data = array();
            foreach($_POST['imgs'] as $key=>$val){
                $img_data[] = array(
                    'fid' => $_POST['id'],
                    'pic' => $val,
                );
            }
            D()->table('ljk_wine_image')->addall($img_data);
            //条形码
            D()->table('ljk_wine_barcode')->where(array('fid'=>$_POST['id']))->delete();
            $barcode_data = array();
            foreach($_POST['barcodes'] as $val){
                $barcode_data[] = array(
                    'fid' => $_POST['id'],
                    'num' => $val,
                );
            }
            D()->table('ljk_wine_barcode')->addall($barcode_data);
            //品牌
            D()->table('jiuku_brand_relation')->where(array('relation_id'=>$_POST['id']))->delete();
            $brand_data = array();
            foreach($_POST['brand_ids'] as $val){
                $brand_data[] = array(
                    'fid' => $val,
                    'relation_id' => $_POST['id'],
                );
            }
            D()->table('jiuku_brand_relation')->addall($brand_data);
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('ljk_wine')->where(array('id'=>$_GET['id'],'status'=>array('in',array(2,3))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $region_list = D()->table('ljk_region')->field('id,cname,ename')->where(array('status'=>3))->select();
        $region_nlist = array();
        foreach($region_list as $val){
            $region_nlist[$val['id']] = $val;
        }
        $type_list = D()->table('ljk_type')->field('id,cname,ename')->where(array('status'=>3))->select();
        $type_nlist = array();
        foreach($type_list as $val){
            $type_nlist[$val['id']] = $val;
        }
        $this->assign('region_list', $region_list);
        $this->assign('type_list', $type_list);
        $res['region_res'] = $region_nlist[$res['region_id']];
        $res['type_res'] = $type_nlist[$res['type_id']];
        $res['img_res'] = D()->table('ljk_wine_image')->where(array('fid'=>$_GET['id']))->select();
        $res['barcode_res'] = D()->table('ljk_wine_barcode')->where(array('fid'=>$_GET['id']))->select();
        $res['brand_res'] = D()->query('SELECT B.`id`,CONCAT(B.`cname`,\' \',B.`fname`) AS `value` FROM `jiuku_brand_relation` A INNER JOIN `jiuku_brand` B ON A.`fid` = B.`id` AND B.`status` = 3 WHERE A.`relation_id` = '.$_GET['id']);
        $this->assign('res', $res);
        $this->display();
    }
    public function upload(){
        if($_FILES['updimg']){
            $rootpath = C('UPLOAD_PATH') . 'Wine/images/';
            $rooturl = C('DOMAIN.UPLOAD') . 'Jiuku/Wine/images/';
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
            import('@.ORG.Util.Image');
            $image = new Image();
            $filepath = $res['rootpath'] . $res['subpath'] . $res['filename'];
            $image->thumb2($filepath,$filepath.'.160.160',160,160,0,60);
            $image->thumb3($filepath,$filepath.'.90.120',90,120);
            $image->thumb3($filepath,$filepath.'.180.240',180,240);
            $image->thumb3($filepath,$filepath.'.600.600',600,600);
            //生成缩略图END
            exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'],'rooturl'=>'')));
        }elseif($_FILES['imgFile']){
        }
    }
}
