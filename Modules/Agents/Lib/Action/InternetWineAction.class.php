<?php
/**
 * 网络酒款管理
 */
class InternetWineAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }

    /**
     * 网络酒款列表
     */
    public function index(){
        if($this->isPost()) $this->listpage_post_to_get();
        $_GET['agents_id'] = intval($_GET['agents_id']);
        $_GET['internet_sales_id'] = intval($_GET['internet_sales_id']);
        $agents_res = D('Agents')->where(array('id'=>$_GET['agents_id'],'is_del'=>'-1'))->find();
        $internet_sales_res = D('InternetSales')->where(array('id'=>$_GET['internet_sales_id'],'is_del'=>'-1'))->find();
        $internet_sales_agents_res = D('Agents')->where(array('id'=>$internet_sales_res['agents_id'],'is_del'=>'-1'))->find();
        $map = array();
        $url = '';
        if($internet_sales_res && $internet_sales_agents_res){
            $_GET['internet_sales_id'] = $internet_sales_res['id'];
            $_GET['agents_id'] = $internet_sales_agents_res['id'];
            $map['internet_sales_id'] = $internet_sales_res['id'];
            $url .= '&internet_sales_id='.$internet_sales_res['id'];
            $internet_sales_list = D('InternetSales')->getList('keyid',array('agents_id'=>$internet_sales_agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->getList('keyid');
            $this->assign('agents_list',$agents_list);
        }elseif($agents_res){
            unset($_GET['internet_sales_id']);
            $_GET['agents_id'] = $agents_res['id'];
            $map['agents_id'] = $agents_res['id'];
            $url .= '&agents_id='.$agents_res['id'];
            $internet_sales_list = D('InternetSales')->getList('keyid',array('agents_id'=>$agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->getList('keyid');
            $this->assign('agents_list',$agents_list);
        }else{
            unset($_GET['internet_sales_id']);
            unset($_GET['agents_id']);
            $agents_list = D('Agents')->getList('keyid');
            $this->assign('agents_list',$agents_list);
        }
        if(isset($_GET['keyword']) && $_GET['keyword'] != '') {
            $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['url'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        if(isset($_GET['status']) && $_GET['status'] != '') {
            $map['status'] = $_GET['status'];
            $url .= '&status=' . $_GET['status'];
        }
        $map['is_del'] = '-1';
        $count = D('InternetWine')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('InternetWine')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $internet_sales_list = D('InternetSales')->getList('keyid');
        foreach($list as $key=>$val){
            $list[$key]['agents_res'] = $agents_list[$val['agents_id']];
            $list[$key]['internet_sales_res'] = $internet_sales_list[$val['internet_sales_id']];
            $list[$key]['wine_res'] = M('Wine','jiuku_')->field('id,fname,cname')->where(array('id'=>$val['wine_id'],'is_del'=>'-1','merge_id'=>0))->find();
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }

    /**
     * 添加网络酒款
     */
    public function add(){
        if($this->isPost()) {
            $_POST['agents_id'] = intval($_POST['agents_id']);
            $_POST['internet_sales_id'] = intval($_POST['internet_sales_id']);
            $_POST['wine_id'] = intval($_POST['wine_id']);
            $_POST['wine_caname_id'] = intval($_POST['wine_caname_id']);
            if(!$_POST['agents_id'] || !$_POST['internet_sales_id'] || !$_POST['wine_id'] || !$_POST['wine_caname_id'] || !$_POST['fname'] || !$_POST['cname'] || !$_POST['url'])
                $this->_jumpGo('添加失败，代入信息错误', 'error');
            if(!preg_match("/^(-|\+)?\d+$/",$_POST['price']))
                $this->_jumpGo('添加失败，代入信息错误', 'error');
            $yeararr = explode(',',$_POST['yearstr']);
            $price_time = (intval($_POST['price_time']) > 0) ? intval($_POST['price_time']) : time();
            $_POST['price_log'] = json_encode(array(array('p'=>(int)$_POST['price'],'t'=>(int)$price_time)));
            $_POST['img'] = $_POST['img_filename'][0];
            $_POST['last_edit_time'] = time();
            $img_data = array();
            foreach($yeararr as $_POST['year']){
                D('InternetWine')->create();
                $id = D('InternetWine')->add();
                if(!$id)
                    continue;
                D('AgentsLog')->savelog('jiuku_agents_internet_sales_wine', $id, 1);//log
                foreach($_POST['img_filename'] as $k=>$v){
                    $img_data[] = array(
                        'filename'=>$v,
                        'description'=>$_POST['img_description'][$k],
                        'alt'=>$_POST['img_alt'][$k],
                        'queue'=>$k,
                        'internet_sales_wine_id'=>$id
                    );
                }
            }
            M('AgentsInternetSalesWineImg','jiuku_')->addall($img_data);
            //过滤掉抓取酒款
            if(isset($_POST['capture_data_id']) && (intval($_POST['capture_data_id']) > 0)){
                M('CrawlWineData','jiuku_')->save(array('id'=>intval($_POST['capture_data_id']),'is_del'=>'1'));
            }
            $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $_GET['agents_id'] = intval($_GET['agents_id']);
        $_GET['internet_sales_id'] = intval($_GET['internet_sales_id']);
        $agents_res = D('Agents')->where(array('id'=>$_GET['agents_id'],'is_del'=>'-1'))->find();
        $internet_sales_res = D('InternetSales')->where(array('id'=>$_GET['internet_sales_id'],'is_del'=>'-1'))->find();
        $internet_sales_agents_res = D('Agents')->where(array('id'=>$internet_sales_res['agents_id'],'is_del'=>'-1'))->find();
        if($internet_sales_res && $internet_sales_agents_res){
            $this->assign('internet_sales_res',$internet_sales_res);
            $this->assign('agents_res',$internet_sales_agents_res);
            $internet_sales_list = D('InternetSales')->getList(false,array('agents_id'=>$internet_sales_agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->getList();
            $this->assign('agents_list',$agents_list);
        }elseif($agents_res){
            $this->assign('agents_res',$agents_res);
            $internet_sales_list = D('InternetSales')->getList(false,array('agents_id'=>$agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->getList();
            $this->assign('agents_list',$agents_list);
        }else{
            $agents_list = D('Agents')->getList();
            $this->assign('agents_list',$agents_list);
        }
        $this->display();
    }

    /**
     * 编辑网络酒款
     */
    public function edit(){
        if(!$_REQUEST['id'] = intval($_REQUEST['id']))
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()) {
            $_POST['agents_id'] = intval($_POST['agents_id']);
            $_POST['internet_sales_id'] = intval($_POST['internet_sales_id']);
            $_POST['wine_id'] = intval($_POST['wine_id']);
            $_POST['wine_caname_id'] = intval($_POST['wine_caname_id']);
            if(!$_POST['agents_id'] || !$_POST['internet_sales_id'] || !$_POST['wine_id'] || !$_POST['wine_caname_id'] || !$_POST['fname'] || !$_POST['cname'] || !$_POST['url'] || !$_POST['year'])
                $this->_jumpGo('编辑失败，代入信息错误', 'error');
            if(!preg_match("/^(-|\+)?\d+$/",$_POST['price']))
                $this->_jumpGo('编辑失败，代入信息错误', 'error');
            if($_POST['old_price'] != $_POST['price']){
                $price_log_arr = json_decode($_POST['old_price_log'],true) ? json_decode($_POST['old_price_log'],true) : array();
                array_push($price_log_arr,array((int)'p'=>$_POST['price'],'t'=>(int)time()));
                $_POST['price_log'] = json_encode($price_log_arr);
                $_POST['price_trend'] = $this->get_price_trend($_POST['price'],$_POST['old_price'],$_POST['old_price_trend']);
            }
            $_POST['img'] = $_POST['img_filename'][0];
            $_POST['last_edit_time'] = time();
            D('InternetWine')->create();
            $is = D('InternetWine')->save();
            if($is = false)
                $this->_jumpGo('编辑失败'. 'error');
            D('AgentsLog')->savelog('jiuku_agents_internet_sales_wine', $_REQUEST['id'], 2);//log
            //图片
            M('AgentsInternetSalesWineImg','jiuku_')->where(array('internet_sales_wine_id'=>$_REQUEST['id']))->delete();
            $img_data = array();
            foreach($_POST['img_filename'] as $key=>$val){
                $img_data[] = array(
                    'filename' => $_POST['img_filename'][$key],
                    'description' => $_POST['img_description'][$key],
                    'alt' => $_POST['img_alt'][$key],
                    'queue' => $key,
                    'internet_sales_wine_id' => $_REQUEST['id'],
                );
            }
            M('AgentsInternetSalesWineImg','jiuku_')->addall($img_data);
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        if(!$res = D('InternetWine')->where(array('id'=>$_REQUEST['id'],'is_del'=>'-1'))->find())
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($res['wine_res'] = M('Wine','jiuku_')->field('id,fname,cname')->where(array('id'=>$res['wine_id'],'merge_id'=>0,'is_del'=>'-1'))->find()){
            $wine_caname_list = M('WineCaname','jiuku_')->where(array('cname'=>array('neq',''),'wine_id'=>$res['wine_res']['id'],'status'=>'1','is_del'=>'-1'))->select();
        }
        $agents_list = D('Agents')->field('id,fname,cname')->where(array('is_del'=>'-1'))->select();
        if(D('Agents')->where(array('id'=>$res['agents_id'],'is_del'=>'-1'))->find()){
            $internet_sales_list = D('InternetSales')->where(array('agents_id'=>$res['agents_id'],'is_del'=>'-1'))->select();
        }
        $res['img_res'] = M('AgentsInternetSalesWineImg','jiuku_')->field('id,filename,description,alt')->where(array('internet_sales_wine_id'=>$res['id'],'is_del'=>'-1'))->select();
        $this->assign('res',$res);
        $this->assign('wine_caname_list',$wine_caname_list);
        $this->assign('agents_list',$agents_list);
        $this->assign('internet_sales_list',$internet_sales_list);
        $this->display();
    }

    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $id = intval($_GET['id']);
        $is = D('InternetWine')->save(array('id'=>$id,'status'=>$_GET['status']));
        if($is === false)
            $this->_jumpGo('状态更改失败', 'error', base64_decode($_REQUEST['_backpage']));
        D('AgentsLog')->savelog('jiuku_agents_internet_sales_wine', $id, 2);//log
        $this->_jumpGo('状态更改成功', 'succeed', base64_decode($_REQUEST['_backpage']));
    }

    /**
     * 上传文件
     */
    public function upload(){
        //缩略图
        import('@.ORG.Util.Image');
        $image = new Image();
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        $subpath = 'Wine/images/';
        if(isset($_POST['_img_url'])){
            if($filename = A('Common')->GrabImage($_POST['_img_url'],$subpath,'jpg')){
                $result = array(
                    'error' => 0,
                    'url' => C('UPLOAD_URL') . $subpath . $filename,
                    'filename' => $filename,
                );
                $file = C('UPLOAD_PATH') . $subpath . $filename;
                $image->thumb2($file,$file.'.160.160',160,160,0,60);
                $image->thumb3($file,$file.'.90.120',90,120);
                $image->thumb3($file,$file.'.180.240',180,240);
                $image->thumb3($file,$file.'.600.600',600,600);
                $this->_ajaxDisplay($result);
            }else{
                $result = array(
                    'error' => 1,
                    'message' => '链接错误',
                );
                $this->_ajaxDisplay($result);
            }
        }
        $cfg = array(
            'ext' => C('UPLOAD_ALLOW_EXT'),
            'size' => C('UPLOAD_MAXSIZE'),
            'path' => C('UPLOAD_PATH') . $subpath,
        );
        $upload->config($cfg);
        $rest = $upload->uploadFile('upd_img');
        if($rest['errno']) {
            $result = array(
                'error' => 1,
                'message' => $upload->error(),
            );
            $this->_ajaxDisplay($result);
        }else{
            $result = array(
                'error' => 0,
                'url' => C('UPLOAD_URL') . $subpath . $rest['path'],
                'filename' => $rest['path'],
            );
            $file = C('UPLOAD_PATH') . $subpath . $rest['path'];
            $image->thumb2($file,$file.'.160.160',160,160,0,60);
            $image->thumb3($file,$file.'.90.120',90,120);
            $image->thumb3($file,$file.'.180.240',180,240);
            $image->thumb3($file,$file.'.600.600',600,600);
            $this->_ajaxDisplay($result);
        }
    }


    function errorlist(){
        if($id = intval($_GET['id'])){
            if($_GET['type'] == 'pass'){
                D('InternetWine')->save(array('id'=>$id,'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
            }elseif($_GET['type'] == 'close'){
                D('InternetWine')->save(array('id'=>$id,'status'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
            }elseif($_GET['type'] == 'del'){
                D('InternetWine')->save(array('id'=>$id,'is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
            }else{
                $this->_jumpGo('参数错误', 'error');
            }
            $this->_jumpGo('操作成功', 'succeed', base64_decode($_GET['_backpage']));
        }
        $time_threshold = 3*24*60*60;//3天
        $map['last_edit_time'] = array('lt',(time()-$time_threshold));
        $map['last_script_time'] = array('lt',(time()-$time_threshold));
        $map['internet_sales_id'] = array('in',array(1,2,3,4));
        $map['status'] = '1';
        $map['is_del'] = '-1';
        $count = D('InternetWine')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('InternetWine')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $internet_sales_list = D('InternetSales')->getList('keyid');
        foreach($list as $key=>$val){
            $list[$key]['internet_sales_res'] = $internet_sales_list[$val['internet_sales_id']];
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }

    /**
     * 抓取数据列表页
     */
    public function crawllist(){
        $internet_sales_res = M('CrawlWineData','jiuku_')->field('internet_sales_name')->where(array('is_del'=>'-1'))->group('internet_sales_name')->select();
        $this->assign('internet_sales_res',$internet_sales_res);
        $internet_sales_name = $_REQUEST['internet_sales_name'];
        $keyword = $_REQUEST['keyword'];
        $status = $_REQUEST['status'];
        $map = array();
        $url = '';
        if($internet_sales_name) {
            $map['internet_sales_name'] = $internet_sales_name;
            $url .= '&internet_sales_name='.$internet_sales_name;
        }
        if($keyword) {
            $map_k['fname'] = array('like', '%'.$keyword.'%');
            $map_k['cname'] = array('like', '%'.$keyword.'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $keyword;
        }
        if(in_array($status,array('0','1','2','3','4'))) {
            $map['status'] = $status;
            $url .= '&status=' . $status;
        }
        $map['is_del'] = '-1';
        $count = M('CrawlWineData','jiuku_')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = M('CrawlWineData','jiuku_')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['date'] = date("Y-m-d H:i:s",$val['time']);
        }
        $this->assign('list',$list);
        $this->assign("page", $p->show());
        $this->display();
    }

    /**
     * 价格趋势获取
     */
    public function get_price_trend($price,$old_price,$old_price_trend){
        if($old_price_trend == 0 || $old_price_trend == 1){
            if($old_price == $price){
                $price_trend = 1;
            }elseif($old_price > $price){
                $price_trend = 3;
            }else{
                $price_trend = 2;
            }
        }elseif($old_price_trend == 2){
            if($old_price <= $price){
                $price_trend = 2;
            }else{
                $price_trend = 4;
            }
        }elseif($old_price_trend == 3){
            if($old_price >= $price){
                $price_trend = 3;
            }else{
                $price_trend = 4;
            }
        }else{
            $price_trend = 4;
        }
        return $price_trend;
    }
}