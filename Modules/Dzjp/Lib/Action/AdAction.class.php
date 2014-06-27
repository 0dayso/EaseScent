<?php
// 广告
class AdAction extends CommonAction {
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
    public function guideIndex(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $map = array();
        $url = '';
        if(isset($_GET['type'])){
            if($_GET['type'] != 0){
                $map['type'] = $_GET['type'];
                $url .= '&type=' . $_GET['type'];
            }else{
                unset($_GET['type']);
            }
        }
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map_k['guide_title'] = array('like', '%'.$_GET['keyword'].'%');
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
        $count = D()->table('dzjp_ad_guide')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('dzjp_ad_guide')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $type_list = D()->table('dzjp_ad_guide_type')->select();
        $ntype_list = array();
        foreach($type_list as $key=>$val){
            $ntype_list[$val['id']] = $val;
        }
        foreach($list as $key=>$val){
            $list[$key]['type_res'] = $ntype_list[$val['type']];
            $list[$key]['winecname_res'] = D()->table('jiuku_wine_caname')->field('id,cname,fname')->where(array('id'=>$val['wine_id'],'is_merge'=>'-1','is_del'=>'-1'))->find();
        }
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->assign('type_list',$type_list);
        $this->display();
    }
    public function guideAdd(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('guideIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if($_POST['type'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“所属分类”为必选项。','_backurl'=>$_backurl)));
            }
            if($_POST['wine_id'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“关联酒款中文名ID”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['origin_img'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“广告图片”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['thum_img'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“广告图片”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['ad_customer'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“广告客户”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['guide_title'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“导购标题”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['recommend_reason'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“推荐理由”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['discount_price'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“现价/原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['origin_price'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“现价/原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['sales_num'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“现价/原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['outofdate_time'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“下架时间”为必填项。','_backurl'=>$_backurl)));
            }
            $_POST['outofdate_time'] = strtotime($_POST['outofdate_time']);
            $_POST['create_time'] = time();
            if($_POST['outofdate_time'] <= $_POST['create_time']){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“下架时间”小于当前时间。','_backurl'=>$_backurl)));
            }
            //insert
            D('AdGuide')->create();
            if(!$id = D('AdGuide')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
        $type_list = D()->table('dzjp_ad_guide_type')->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }
    public function guideEdit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('guideIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['type'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“所属分类”为必选项。','_backurl'=>$_backurl)));
            }
            if($_POST['wine_id'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“关联酒款中文名ID”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['origin_img'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“广告图片”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['thum_img'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“广告图片”为必需项。','_backurl'=>$_backurl)));
            }
            if($_POST['ad_customer'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“广告客户”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['guide_title'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“导购标题”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['recommend_reason'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“推荐理由”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['discount_price'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“现价/原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['origin_price'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“现价/原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['sales_num'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“现价/原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['outofdate_time'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“下架时间”为必填项。','_backurl'=>$_backurl)));
            }
            $_POST['outofdate_time'] = strtotime($_POST['outofdate_time']);
            $_POST['update_time'] = time();
            if($_POST['outofdate_time'] <= $_POST['update_time']){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“下架时间”小于当前时间。','_backurl'=>$_backurl)));
            }
            //update
            D('AdGuide')->create();
            if(false === $is = D('AdGuide')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('dzjp_ad_guide')->where(array('id'=>$_GET['id'],'status'=>array('in',array('1','0'))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $this->assign('res', $res);
        $type_list = D()->table('dzjp_ad_guide_type')->select();
        $this->assign('type_list',$type_list);
        $this->display();
    }
    public function guideUpload(){
        $rootpath = C('UPLOAD_PATH') . 'Dzjp/Guide/images/';
        $rooturl = C('UPLOAD_URL') . 'Dzjp/Guide/images/';
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
        if($imginfo['width'] != 640 || $imginfo['height'] != 370){
            exit(json_encode(array('error'=>1,'msg'=>'上传失败，尺寸不符合要求')));
        }
        exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
    }
    public function guidetypeIndex(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('guideIndex');
        if($this->isPost()){
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['type_name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“广告分类名称”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['pic'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“图片”为必需项。','_backurl'=>$_backurl)));
            }
            $_POST['update_time'] = time();
            //update
            D('AdGuideType')->create();
            if(false === $is = D('AdGuideType')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        $res = D()->table('dzjp_ad_guide_type')->select();
        $this->assign('res',$res);
        $this->display();
    }
    public function guidetypeUpload(){
        $rootpath = C('UPLOAD_PATH') . 'Dzjp/Guidetype/images/';
        $rooturl = C('UPLOAD_URL') . 'Dzjp/Guidetype/images/';
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
        if($imginfo['width'] != 622 || $imginfo['height'] != 174){
            exit(json_encode(array('error'=>1,'msg'=>'上传失败，尺寸不符合要求')));
        }
        exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
    }
    public function bannerIndex(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $map = array();
        $url = '';
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map_k['customer_name'] = array('like', '%'.$_GET['keyword'].'%');
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
        $count = D()->table('dzjp_banner')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('dzjp_banner')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function bannerAdd(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('bannerIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if($_POST['customer_name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“广告客户”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['url'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“url链接”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['pic_url'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“广告图片”为必需项。','_backurl'=>$_backurl)));
            }
            $_POST['create_time'] = time();
            //insert
            D('Banner')->create();
            if(!$id = D('Banner')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
        $this->display();
    }
    public function bannerEdit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('bannerIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['customer_name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“广告客户”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['url'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“url链接”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['pic_url'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“广告图片”为必需项。','_backurl'=>$_backurl)));
            }
            $_POST['update_time'] = time();
            //update
            D('Banner')->create();
            if(false === $is = D('Banner')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('dzjp_banner')->where(array('id'=>$_GET['id'],'status'=>array('in',array('1','0'))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $this->assign('res', $res);
        $this->display();
    }
    public function bannerUpload(){
        $rootpath = C('UPLOAD_PATH') . 'Dzjp/Banner/images/';
        $rooturl = C('UPLOAD_URL') . 'Dzjp/Banner/images/';
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
        if($imginfo['width'] != 640 || $imginfo['height'] != 300){
            exit(json_encode(array('error'=>1,'msg'=>'上传失败，尺寸不符合要求')));
        }
        exit(json_encode(array('error'=>0,'msg'=>$upload->error(),'img'=>$res['subpath'].$res['filename'])));
    }
    public function recomwineIndex(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $map = array();
        $url = '';
        /*if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map_k['guide_title'] = array('like', '%'.$_GET['keyword'].'%');
                $map_k['_logic'] = 'or';
                $map['_complex'] = $map_k;
                $url .= '&keyword=' . $_GET['keyword'];
            }else{
                unset($_GET['keyword']);
            }
        }*/
        $map['status'] = array('in', array('0','1'));
        if(isset($_GET['status'])){
            if(in_array($_GET['status'], array('0','1'))){
                $map['status'] = $_GET['status'];
                $url .= '&status=' . $_GET['status'];
            }else{
                unset($_GET['status']);
            }
        }
        $count = D()->table('dzjp_recommend_wine')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('dzjp_recommend_wine')->field('id,origin_price,status,agents_wine_id')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['agents_internet_sales_wine_res'] = D()->table('jiuku_agents_internet_sales_wine')->field('id,fname,cname,price,agents_id')->where(array('id'=>$val['agents_wine_id'],'status'=>'1','is_del'=>'-1'))->find();
            $list[$key]['agents_res'] = D()->table('jiuku_agents')->field('id,fname,cname')->where(array('id'=>$list[$key]['agents_internet_sales_wine_res']['agents_id'],'status'=>'1','is_del'=>'-1'))->find();
        }
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function recomwineAdd(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('recomwineIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if($_POST['agents_wine_id'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“关联代理商网络销售酒款”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['origin_price'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['sales_num'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“已售数量”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['promotion_content'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“已促销内容”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['outofdate_time'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“关闭时间”为必填项。','_backurl'=>$_backurl)));
            }
            $_POST['outofdate_time'] = strtotime($_POST['outofdate_time']);
            $_POST['create_time'] = time();
            if($_POST['outofdate_time'] <= $_POST['create_time']){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“关闭时间”小于当前时间。','_backurl'=>$_backurl)));
            }
            //insert
            D('RecommendWine')->create();
            if(!$id = D('RecommendWine')->add()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        //页面
        $this->display();
    }
    public function recomwineEdit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('recomwineIndex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['agents_wine_id'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“关联代理商网络销售酒款”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['origin_price'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“原价”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['sales_num'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“已售数量”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['promotion_content'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“已促销内容”为必填项。','_backurl'=>$_backurl)));
            }
            if($_POST['outofdate_time'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“关闭时间”为必填项。','_backurl'=>$_backurl)));
            }
            $_POST['outofdate_time'] = strtotime($_POST['outofdate_time']);
            $_POST['update_time'] = time();
            if($_POST['outofdate_time'] <= $_POST['update_time']){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“关闭时间”小于当前时间。','_backurl'=>$_backurl)));
            }
            //update
            D('RecommendWine')->create();
            if(false === $is = D('RecommendWine')->save()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('dzjp_recommend_wine')->where(array('id'=>$_GET['id'],'status'=>array('in',array('1','0'))))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'succeed', $_backurl);
        }
        $res['wine_res'] = D()->table('jiuku_agents_internet_sales_wine A')->join('INNER JOIN `jiuku_agents` B ON A.`agents_id` = B.`id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\'')->field('A.id AS id,A.fname AS fname,A.cname AS cname,A.price AS price,A.year AS year,A.wine_caname_id AS wine_caname_id,A.url AS url,B.fname AS agents_fname,B.cname AS agents_cname')->where(array('A.id'=>$res['agents_wine_id'],'A.status'=>'1','A.is_del'=>'-1'))->find();
        $this->assign('res', $res);
        $this->display();
    }
    function getAgentsInternetWine(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($kw == ''){
            $map_nil['A.status'] = '1';
            $map_nil['A.is_del'] = '-1';
            $list = D()->table('jiuku_agents_internet_sales_wine A')->join('INNER JOIN `jiuku_agents` B ON A.`agents_id` = B.`id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\'')->field('A.id AS id,A.fname AS fname,A.cname AS cname,A.price AS price,A.year AS year,A.wine_caname_id AS wine_caname_id,A.url AS url,B.fname AS agents_fname,B.cname AS agents_cname')->where(array('A.status'=>'1','A.is_del'=>'-1'))->order('id ASC')->limit($count)->select();
            $result = $list ? $list : array();
        }else{
            $exist_ids = array();
            $where_eq['A.fname'] = $kw;
            $where_eq['A.cname'] = $kw;
            $where_eq['_logic'] = 'or';
            $map_eq['_complex'] = $where_eq;
            $map_eq['A.status'] = '1';
            $map_eq['A.is_del'] = '-1';
            $list_eq = D()->table('jiuku_agents_internet_sales_wine A')->join('INNER JOIN `jiuku_agents` B ON A.`agents_id` = B.`id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\'')->field('A.id AS id,A.fname AS fname,A.cname AS cname,A.price AS price,A.year AS year,A.wine_caname_id AS wine_caname_id,A.url AS url,B.fname AS agents_fname,B.cname AS agents_cname')->where($map_eq)->limit($count)->select();
            foreach($list_eq as $key=>$val){
                $result[] = $val;
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $where_leftlike['A.fname'] = array('like',$kw.'%');
                $where_leftlike['A.cname'] = array('like',$kw.'%');
                $where_leftlike['_logic'] = 'or';
                $map_leftlike['_complex'] = $where_leftlike;
                $map_leftlike['A.status'] = '1';
                $map_leftlike['A.is_del'] = '-1';
                if(count($exist_ids) > 0){
                    $map_leftlike['A.id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_leftlike = D()->table('jiuku_agents_internet_sales_wine A')->join('INNER JOIN `jiuku_agents` B ON A.`agents_id` = B.`id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\'')->field('A.id AS id,A.fname AS fname,A.cname AS cname,A.price AS price,A.year AS year,A.wine_caname_id AS wine_caname_id,A.url AS url,B.fname AS agents_fname,B.cname AS agents_cname')->where($map_leftlike)->limit($count)->select();
                foreach($list_leftlike as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $where_like['A.fname'] = array('like','%'.$kw.'%');
                $where_like['A.cname'] = array('like','%'.$kw.'%');
                $where_like['_logic'] = 'or';
                $map_like['_complex'] = $where_like;
                $map_like['A.status'] = '1';
                $map_like['A.is_del'] = '-1';
                if(count($exist_ids) > 0){
                    $map_like['A.id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_like = D()->table('jiuku_agents_internet_sales_wine A')->join('INNER JOIN `jiuku_agents` B ON A.`agents_id` = B.`id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\'')->field('A.id AS id,A.fname AS fname,A.cname AS cname,A.price AS price,A.year AS year,A.wine_caname_id AS wine_caname_id,A.url AS url,B.fname AS agents_fname,B.cname AS agents_cname')->where($map_like)->limit($count)->select();
                foreach($list_like as $key=>$val){
                    $result[] = $val;
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
}
