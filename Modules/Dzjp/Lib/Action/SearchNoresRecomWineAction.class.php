<?php
// 搜索无结果推荐酒款管理
class SearchNoresRecomWineAction extends CommonAction {
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
            if(intval($_POST['wine_cname_id']) == 0){
                exit(json_encode(array('error'=>1,'msg'=>'保存失败！没有选择任何酒款中文名。')));
            }
            $data = array(
                'id' => $_POST['id'],
                'wine_cname_id' => $_POST['wine_cname_id'],
                'status' => $_POST['status']
            );
            $is_exist = D('SearchNoresRecomWine')->where(array('id'=>$_POST['id']))->find();
            if($is_exist){
                if(false === $is = D('SearchNoresRecomWine')->save($data)){
                    exit(json_encode(array('error'=>1,'msg'=>'保存失败！数据库异常。')));
                }
            }else{
                if(!$is = D('SearchNoresRecomWine')->add($data)){
                    exit(json_encode(array('error'=>1,'msg'=>'保存失败！数据库异常。')));
                }
            }
            exit(json_encode(array('error'=>0,'msg'=>'保存成功！')));
        }
        $list = D()->query('SELECT A.`id`,B.`id` AS `wine_cname_id`,B.`cname`,A.`status` FROM `dzjp_search_nores_recom_wine` A INNER JOIN `jiuku_wine_caname` B ON B.`id` = A.`wine_cname_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`is_merge` = \'-1\' INNER JOIN `jiuku_wine` C ON C.`id` = B.`wine_id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' AND C.`merge_id` = 0 WHERE A.`id` < 7 AND A.`status` IN (2,3)');
        $nlist = array();
        foreach($list as $val){
            $nlist[$val['id']] = $val;
        }
        $this->assign('list',$nlist);
        $this->display();
    }
    function getData(){
        if($_POST['type'] == 'id'){
            $res = D()->query('SELECT A.id,A.cname,B.fname FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`is_merge` = \'-1\' AND A.`id` = '.intval($_POST['id']));
            if(!$res){
                exit(json_encode(array('error'=>1)));
            }
            exit(json_encode(array('error'=>0, result=>$res[0])));
        }elseif($_POST['type'] == 'search'){
            $kw = trim($_POST['kw']);
            $result = array();
            $count = 20;
            if($kw == ''){
                $list = D()->query('SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`is_merge` = \'-1\' LIMIT '.$count);
                $result = $list ? $list : array();
            }elseif(preg_match("/^[1-9][0-9]{0,}$/", $kw)){
                $list = D()->query('SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`is_merge` = \'-1\' AND A.`id` = '.$kw.' LIMIT '.$count);
                $result = $list ? $list : array();
            }else{
                $exist_ids = array();
                $list_eq = D()->query('SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`is_merge` = \'-1\' AND A.`cname` = \''.$kw.'\' LIMIT '.$count);
                foreach($list_eq as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
                if($count > 0){
                    $where_leftlike = 'B.`fname` LIKE \''.$kw.'%\' AND A.`cname` LIKE \''.$kw.'%\'';
                    if(count($exist_ids) > 0){
                        $where_leftlike .= ' AND A.`id` NOT IN ('.implode(',',$exist_ids).')';
                    }
                    $list_leftlike = D()->query('SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`is_merge` = \'-1\' AND '.$where_leftlike.' LIMIT '.$count);
                    foreach($list_leftlike as $key=>$val){
                        $result[] = $val;
                        $exist_ids[] = $val['id'];
                    }
                    $count = $count - count($result);
                }
                if($count > 0){
                    $where_leftlike = 'B.`fname` LIKE \'%'.$kw.'%\' AND A.`cname` LIKE \'%'.$kw.'%\'';
                    if(count($exist_ids) > 0){
                        $where_leftlike .= ' AND A.`id` NOT IN ('.implode(',',$exist_ids).')';
                    }
                    $list_like = D()->query('SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`is_merge` = \'-1\' AND '.$where_leftlike.' LIMIT '.$count);
                    foreach($list_like as $key=>$val){
                        $result[] = $val;
                    }
                    $count = $count - count($result);
                }
            }
            exit(json_encode(array('error'=>0, 'result'=>$result)));
        }else{

        }
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
            /*$list[$key]['brand_res'] = D()->table('jiuku_winery')->field('id,fname,cname')->where(array('status'=>'1','is_del'=>'-1','id'=>$val['brand_id']))->find();
            if($list[$key]['brand_res']){*/
                $wine_count = D()->query('SELECT count(1) AS `count` FROM (SELECT 1 FROM `dzjp_winery_top_wine` A INNER JOIN `jiuku_wine_caname` B ON B.`id` = A.`wine_cname_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`is_merge` = \'-1\' INNER JOIN `jiuku_wine` C ON C.`id` = B.`wine_id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' AND C.`merge_id` = 0 WHERE A.`status` = 1 AND A.`brand_id` = '.$val['id'].') view');
                $list[$key]['wine_count'] = $wine_count[0]['count'];
            /*}*/
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
            /*if($_POST['brand_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“酒库酒庄”为必选项。','_backurl'=>$_backurl)));
            }*/
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“品牌名称”为必填项。','_backurl'=>$_backurl)));
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
            if(count($_POST['wine_id']) == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“已选酒款列表”为必需项。','_backurl'=>$_backurl)));
            }
            $_POST['create_time'] = time();
            //insert
            D('Brand')->create();
            if(!$id = D('Brand')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            foreach($_POST['wine_id'] as $key=>$val){
                $wine_data[] = array(
                    'brand_id' => $id,
                    'wine_cname_id' => $val,
                    'sort_num' => $_POST['wine_sortnum'][$key],
                    'status' => 1,
                    'create_time' => $_POST['create_time'],
                );
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
            /*if($_POST['brand_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“酒库酒庄”为必选项。','_backurl'=>$_backurl)));
            }*/
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“品牌名称”为必填项。','_backurl'=>$_backurl)));
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
            if(count($_POST['wine_id']) == 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“已选酒款列表”为必需项。','_backurl'=>$_backurl)));
            }
            $_POST['update_time'] = time();
            //update
            D('Brand')->create();
            if(false === $is = D('Brand')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            D('WineryTopWine')->where(array('brand_id'=>$_POST['id']))->delete();
            foreach($_POST['wine_id'] as $key=>$val){
                $wine_data[] = array(
                    'brand_id' => $_POST['id'],
                    'wine_cname_id' => $val,
                    'sort_num' => $_POST['wine_sortnum'][$key],
                    'status' => 1,
                    'create_time' => $_POST['update_time'],
                );
            }
            D('WineryTopWine')->addall($wine_data);
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('dzjp_brand')->where(array('id'=>$_GET['id'],'status'=>array('in',array('1','0'))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $res['sel_wine_list'] = D()->query('SELECT B.`id`,C.`fname`,B.`cname`,A.`sort_num` FROM `dzjp_winery_top_wine` A INNER JOIN `jiuku_wine_caname` B ON B.`id` = A.`wine_cname_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`is_merge` = \'-1\' INNER JOIN `jiuku_wine` C ON C.`id` = B.`wine_id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' AND C.`merge_id` = 0 WHERE A.`status` = 1 AND A.`brand_id` = '.$res['id']);
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
    function grapeGetData(){
        if($_POST['type'] == 'searchWinery'){
            $kw = trim($_POST['kw']);
            $result = array();
            $count = 20;
            if($kw == ''){
                $map_nil['status'] = '1';
                $map_nil['is_del'] = '-1';
                $list = D()->table('jiuku_winery')->field('id,fname,cname')->where($map_nil)->order('id ASC')->limit($count)->select();
                $result = $list ? $list : array();
            }else{
                $exist_ids = array();
                $where_eq['fname'] = $kw;
                $where_eq['cname'] = $kw;
                $where_eq['_logic'] = 'or';
                $map_eq['_complex'] = $where_eq;
                $map_eq['status'] = '1';
                $map_eq['is_del'] = '-1';
                $list_eq = D()->table('jiuku_winery')->field('id,fname,cname')->field('id,fname,cname')->where($map_eq)->limit($count)->select();
                foreach($list_eq as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
                if($count > 0){
                    $where_leftlike['fname'] = array('like',$kw.'%');
                    $where_leftlike['cname'] = array('like',$kw.'%');
                    $where_leftlike['_logic'] = 'or';
                    $map_leftlike['_complex'] = $where_leftlike;
                    $map_leftlike['status'] = '1';
                    $map_leftlike['is_del'] = '-1';
                    if(count($exist_ids) > 0){
                        $map_leftlike['id'] = array('NOT IN', implode(',',$exist_ids));
                    }
                    $list_leftlike = D()->table('jiuku_winery')->field('id,fname,cname')->field('id,fname,cname')->where($map_leftlike)->limit($count)->select();
                    foreach($list_leftlike as $key=>$val){
                        $result[] = $val;
                        $exist_ids[] = $val['id'];
                    }
                    $count = $count - count($result);
                }
                if($count > 0){
                    $where_like['fname'] = array('like','%'.$kw.'%');
                    $where_like['cname'] = array('like','%'.$kw.'%');
                    $where_like['_logic'] = 'or';
                    $map_like['_complex'] = $where_like;
                    $map_like['status'] = '1';
                    $map_like['is_del'] = '-1';
                    if(count($exist_ids) > 0){
                        $map_like['id'] = array('NOT IN', implode(',',$exist_ids));
                    }
                    $list_like = D()->table('jiuku_winery')->field('id,fname,cname')->field('id,fname,cname')->where($map_like)->limit($count)->select();
                    foreach($list_like as $key=>$val){
                        $result[] = $val;
                    }
                    $count = $count - count($result);
                }
            }
            exit(json_encode(array('error'=>0, 'result'=>$result)));
        }elseif($_POST['type'] == 'getWineList'){
            $winery_id = $_POST['winery_id'];
            $list = D()->query('SELECT C.`id`,C.`cname`,B.`fname` FROM `jiuku_join_wine_winery` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 INNER JOIN `jiuku_wine_caname` C ON C.`wine_id` = B.`id` AND C.`status` =\'1\' AND C.`is_del` = \'-1\' AND C.`is_merge` = \'-1\' WHERE A.`is_del` = \'-1\' AND A.`winery_id` = '.$winery_id);
            exit(json_encode(array('error'=>0, 'result'=>$list)));
        }else{
            exit(json_encode(array('error'=>1)));
        }
    }
}
