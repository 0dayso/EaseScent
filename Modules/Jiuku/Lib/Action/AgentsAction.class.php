<?php

/**
 * 代理商管理
 */
class AgentsAction extends CommonAction {

    /**
     * 代理商列表
     */
    public function index() {
        $keyword = Input::getVar($_REQUEST['keyword']);
        $status = Input::getVar($_REQUEST['status']);
        $map = array();
        $url = '';
        if($keyword) {
            $map_k['fname'] = array('like', '%'.$keyword.'%');
            $map_k['cname'] = array('like', '%'.$keyword.'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $keyword;
        }
        if($status) {
            $map['status'] = $status;
            $url .= '&status='.$status;
        }
        $list = $this->_list(D('Agents'), $map, 15, $url);
        foreach($list as $key=>$val){
            $list[$key]['fname_s'] = String::msubstr($val['fname'],0,40);
            $list[$key]['cname_s'] = String::msubstr($val['cname'],0,20);
        }
        $this->assign('list', $list);
        $this->display();
    }
    /**
     * 代理商增加
     */
    public function add() {
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
        if($this->isPost()) {
            $agents_id = $this->_insert(D('Agents'));
            if($agents_id){
                foreach($_POST['img_filename'] as $key=>$val){
                    $img_data = array('agents_id' => $agents_id,'filename' => Input::getVar($_POST['img_filename'][$key]),'description' => Input::getVar($_POST['img_description'][$key]),'alt' => Input::getVar($_POST['img_alt'][$key]),'queue' => Input::getVar($_POST['img_queue'][$key]),'is_del'=>'-1');
                    $this->_insert(D('AgentsImg'),$img_data);
                }
                $this->_jumpGo('代理商添加成功', 'succeed', Url('index').base64_decode($rpp));
            }
            $this->_jumpGo('代理商添加失败', 'error', Url('index').base64_decode($rpp));
        }
        $this->assign('rpp',$rpp);
        $this->display();
    }
    /**
     * 代理商编辑
     */
    public function edit() {
        $id = Input::getVar($_REQUEST['id']);
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
        if(!$id) {
            $this->_jumpGo('参数为空!', 'error');
        }
        if($this->isPost()) {
            $is_success = $this->_update(D('Agents'));
            if($is_success){
                foreach($_POST['img_filename'] as $key=>$val){
                    $img_data = array(
                                      'agents_id' => $id,
                                      'filename' => Input::getVar($_POST['img_filename'][$key]),
                                      'description' => Input::getVar($_POST['img_description'][$key]),
                                      'alt' => Input::getVar($_POST['img_alt'][$key]),
                                      'queue' => Input::getVar($_POST['img_queue'][$key]),
                                      );
                    $this->_insert(D('AgentsImg'),$img_data);
                }
                foreach($_POST['upd_img_id'] as $key=>$val){
                    $upd_img_data = array(
                                          'id' => Input::getVar($_POST['upd_img_id'][$key]),
                                          'agents_id' => $id,
                                          'description' => Input::getVar($_POST['upd_img_description'][$key]),
                                          'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
                                          'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
                                          );
                    $this->_update(D('AgentsImg'),$upd_img_data);
                }
                D('AgentsImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                $this->_jumpGo('代理商编辑成功', 'succeed', Url('index').base64_decode($rpp));
            }
            $this->_jumpGo('代理商编辑失败', 'error', Url('index').base64_decode($rpp));
        }
        $agents_res = D('Agents')->where(array('id'=>$id))->find();
        $agents_res['img_res'] = D('AgentsImg')->where(array('agents_id'=>$id,'is_del'=>'-1'))->select();
        $this->assign('res', $agents_res);
        $this->assign('rpp',$rpp);
        $this->display();
    }

    /**
     * 删除
     */
    public function del() {
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
        //获取id
        $id = Input::getVar($_REQUEST['id']);
        //获取批量删除
        $ids = $_REQUEST['ids'];
        $model = D('Agents');
        if($id) {
            $map = array('id' => $id);
        } elseif($ids) {
            $map = array('id' => array('in', $ids));
        }
        if($map) {
            $data = array(
                          'is_del' => '1',
                          'last_edit_time' => time(),
                          'last_edit_aid' => $_SESSION['admin_uid'],
                          );
            $model->where($map)->save($data);
            $this->_jumpGo('删除成功','succeed', Url('index').base64_decode($rpp));
        }
        $this->_jumpGo('删除失败，参数为空', 'error', Url('index').base64_decode($rpp));
    }

    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
        $id = Input::getVar($_GET['id']);
        $status = Input::getvar($_GET['status']);
        $data = array(
                      'id' => $id,
                      'status' => $status,
                      );
        $this->_update(D('Agents'),$data);
        $this->_jumpGo('ID为'.$id.'的代理商状态更改成功', 'succeed', Url('index').base64_decode($rpp));
    }

    /**
     * 上传文件
     */
    public function upload(){
        $this->_uploads();
    }

    /**
     * 网络销售渠道列表
     */
    public function internet_sales_list(){
        if($this->isPost()){
            $jump_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            foreach($_POST as $key=>$val){
                $jump_url .= '&' . $key . '=' . $val;
            }
            header('Location: ' . $jump_url);   exit;
        }
        $_GET['agents_id'] = Input::getVar(intval($_GET['agents_id']));
        $_GET['keyword'] = Input::getVar(trim($_GET['keyword']));
        $map = array();
        $url = '';
        if($_GET['agents_id']) {
            $map['agents_id'] = $_GET['agents_id'];
            $url .= '&agents_id='.$_GET['agents_id'];
        }
        if($_GET['keyword']) {
            $map_k['name'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['url'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        $map['is_del'] = '-1';
        $count = D('AgentsInternetSales')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 2);
        $list = D('AgentsInternetSales')->where($map)->limit($p->firstRow . ',' . $p->listRows)->select();
        $agents_list = D('Agents')->agentsList('keyid');
        foreach($list as $key=>$val){
            $list[$key]['agents_name'] = $agents_list[$val['agents_id']]['fname'] . ' / ' . $agents_list[$val['agents_id']]['cname'];
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("agents_list", $agents_list);
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }

    /**
     * 网络销售渠道添加
     */
    public function internet_sales_add(){
        $_REQUEST['agents_id'] = Input::getVar(intval($_REQUEST['agents_id']));
        $_REQUEST['_backpage'] = Input::getVar(trim($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['name'] = Input::getVar(trim($_POST['name']));
            $_POST['url'] = Input::getVar(trim($_POST['url']));
            if(!$_REQUEST['agents_id'] || !$_POST['name'] || !$_POST['url'])
                $this->_jumpGo('添加失败，代入信息缺失', 'error');
            D('AgentsInternetSales')->create();
            $id = D('AgentsInternetSales')->add();
            if(!$id)
                $this->_jumpGo('添加失败', 'error');
            $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $agents_list = D('Agents')->agentsList();
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 网络销售渠道修改
     */
    public function internet_sales_edit(){
        $_REQUEST['id'] = Input::getVar(intval($_REQUEST['id']));
        $_REQUEST['_backpage'] = Input::getVar(trim($_REQUEST['_backpage']));
        if(!$_REQUEST['id'])    $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['agents_id'] = Input::getVar(intval($_POST['agents_id']));
            $_POST['name'] = Input::getVar(trim($_POST['name']));
            $_POST['url'] = Input::getVar(trim($_POST['url']));
            if(!$_POST['agents_id'] || !$_POST['name'] || !$_POST['url'])
                $this->_jumpGo('编辑失败，代入信息缺失', 'error');
            D('AgentsInternetSales')->create();
            $is = D('AgentsInternetSales')->save();
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $res = D('AgentsInternetSales')->where(array('id'=>$_REQUEST['id']))->find();
        $agents_list = D('Agents')->agentsList();
        $this->assign("res", $res);
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 实体销售渠道列表
     */
    public function store_sales_list(){
        if($this->isPost()){
            $jump_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            foreach($_POST as $key=>$val){
                $jump_url .= '&' . $key . '=' . $val;
            }
            header('Location: ' . $jump_url);   exit;
        }
        $_GET['agents_id'] = Input::getVar(intval($_GET['agents_id']));
        $_GET['area_id'] = Input::getVar(intval($_GET['area_id']));
        $_GET['keyword'] = Input::getVar(trim($_GET['keyword']));
        $map = array();
        $url = '';
        if($_GET['agents_id']) {
            $map['agents_id'] = $_GET['agents_id'];
            $url .= '&agents_id='.$_GET['agents_id'];
        }
        if($_GET['area_id']) {
            $area_idarr = $this->get_area_all_son_id($_GET['area_id']);
            $_GET['area_f_sort'] = $this->get_area_f_sort($_GET['area_id']);
            $map['area_id'] = array('in',$area_idarr);
            $url .= '&area_id='.$_GET['area_id'];
        }
        if($_GET['keyword']) {
            $map_k['name'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        $map['is_del'] = '-1';
        $count = D('AgentsStoreSales')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('AgentsStoreSales')->where($map)->limit($p->firstRow . ',' . $p->listRows)->select();
        $agents_list = D('Agents')->agentsList('keyid');
        foreach($list as $key=>$val){
            $list[$key]['agents_name'] = $agents_list[$val['agents_id']]['fname'] . ' / ' . $agents_list[$val['agents_id']]['cname'];
            $list[$key]['area_f_sort'] = $this->get_area_f_sort($val['area_id']);
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("agents_list", $agents_list);
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }

    /**
     * 实体销售渠道添加
     */
    public function store_sales_add(){
        $_REQUEST['agents_id'] = Input::getVar(intval($_REQUEST['agents_id']));
        $_REQUEST['_backpage'] = Input::getVar(trim($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['name'] = Input::getVar(trim($_POST['name']));
            $_POST['tel'] = Input::getVar(trim($_POST['tel']));
            if(!$_REQUEST['agents_id'] || !$_POST['name'])
                $this->_jumpGo('添加失败，代入信息缺失', 'error');
            D('AgentsStoreSales')->create();
            $id = D('AgentsStoreSales')->add();
            if(!$id)
                $this->_jumpGo('添加失败', 'error');
            $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $agents_list = D('Agents')->agentsList();
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 实体销售渠道修改
     */
    public function store_sales_edit(){
        $_REQUEST['id'] = Input::getVar(intval($_REQUEST['id']));
        $_REQUEST['_backpage'] = Input::getVar(trim($_REQUEST['_backpage']));
        if(!$_REQUEST['id'])    $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['agents_id'] = Input::getVar(intval($_POST['agents_id']));
            $_POST['name'] = Input::getVar(trim($_POST['name']));
            $_POST['tel'] = Input::getVar(trim($_POST['tel']));
            if(!$_POST['agents_id'] || !$_POST['name'])
                $this->_jumpGo('编辑失败，代入信息缺失', 'error');
            D('AgentsStoreSales')->create();
            $is = D('AgentsStoreSales')->save();
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $res = D('AgentsStoreSales')->where(array('id'=>$_REQUEST['id']))->find();
        $res['area_f_sort'] = $this->get_area_f_sort($res['area_id']);
        $agents_list = D('Agents')->agentsList();
        $this->assign("res", $res);
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 网络销售渠道酒款列表
     */
    public function internet_sales_wine_list(){
        if($this->isPost()){
            $jump_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            foreach($_POST as $key=>$val){
                $jump_url .= '&' . $key . '=' . $val;
            }
            header('Location: ' . $jump_url);   exit;
        }
        $_GET['agents_id'] = Input::getVar(intval($_GET['agents_id']));
        $_GET['internet_sales_id'] = Input::getVar(intval($_GET['internet_sales_id']));
        $_GET['keyword'] = Input::getVar(trim($_GET['keyword']));
        $_GET['status'] = Input::getVar(intval($_GET['status']));
        $agents_res = D('Agents')->where(array('id'=>$_GET['agents_id'],'is_del'=>'-1'))->find();
        $internet_sales_res = D('AgentsInternetSales')->where(array('id'=>$_GET['internet_sales_id'],'is_del'=>'-1'))->find();
        $internet_sales_agents_res = D('Agents')->where(array('id'=>$internet_sales_res['agents_id'],'is_del'=>'-1'))->find();
        if($internet_sales_res && $internet_sales_agents_res){
            $map['internet_sales_id'] = $internet_sales_res['id'];
            $url .= '&internet_sales_id='.$internet_sales_res['id'];
            $this->assign('internet_sales_res',$internet_sales_res);
            $this->assign('agents_res',$internet_sales_agents_res);
            $internet_sales_list = D('Agents')->internetSalesList('keyid',array('agents_id'=>$internet_sales_agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->agentsList('keyid');
            $this->assign('agents_list',$agents_list);
        }elseif($agents_res){
            $map['agents_id'] = $agents_res['id'];
            $url .= '&agents_id='.$agents_res['id'];
            $this->assign('agents_res',$agents_res);
            $internet_sales_list = D('Agents')->internetSalesList('keyid',array('agents_id'=>$agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->agentsList('keyid');
            $this->assign('agents_list',$agents_list);
        }else{
            $agents_list = D('Agents')->agentsList('keyid');
            $this->assign('agents_list',$agents_list);
        }
        if($_GET['keyword']) {
            $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['url'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        if($_GET['status']) {
            $map['status'] = $_GET['status'];
            $url .= '&status=' . $_GET['status'];
        }
        $map['is_del'] = '-1';
        $count = D('AgentsInternetSalesWine')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('AgentsInternetSalesWine')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['agents_res'] = $agents_list[$val['agents_id']];
            $list[$key]['internet_sales_res'] = $internet_sales_list[$val['internet_sales_id']];
            $list[$key]['wine_res'] = D('Wine')->field('id,fname,cname')->where(array('id'=>$val['wine_id'],'is_del'=>'-1','merge_id'=>0))->find();
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("agents_list", $agents_list);
        $this->assign("internet_sales_list", $internet_sales_list);
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }


    function internet_sales_wine_upderror_list(){
        if($id = intval($_GET['id'])){
            if($_GET['type'] == 'pass'){
                D('AgentsInternetSalesWine')->save(array('id'=>$id,'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
            }elseif($_GET['type'] == 'close'){
                D('AgentsInternetSalesWine')->save(array('id'=>$id,'status'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
            }elseif($_GET['type'] == 'del'){
                D('AgentsInternetSalesWine')->save(array('id'=>$id,'is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
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
        $count = D('AgentsInternetSalesWine')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('AgentsInternetSalesWine')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $internet_sales_list = D('Agents')->internetSalesList('keyid');
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
     * 添加网络渠道销售酒款
     */
    public function internet_sales_wine_add(){
        $_backpage = Input::getVar($_REQUEST['_backpage']);//return_page_parameter
        if($this->isPost()) {
            $wine_id = intval(Input::getVar($_POST['wine_id']));
            $caname_id = intval(Input::getVar($_POST['caname_id']));
            $agents_id = intval(Input::getVar($_POST['agents_id']));
            $internet_sales_id = intval(Input::getVar($_POST['internet_sales_id']));
            $wine_key = Input::getVar($_POST['key']);
            $fname = Input::getVar($_POST['fname']);
            $cname = Input::getVar($_POST['cname']);
            $yeararr = explode(',',Input::getVar($_POST['yearstr']));
            $price = Input::getVar($_POST['price']);
            $url = Input::getVar($_POST['url']);
            $wap_url = Input::getVar($_POST['wap_url']);
            if(!$agents_id || !$caname_id || !$internet_sales_id || !$wine_id || !$fname || !$cname || !$url){
                $this->_jumpGo('参数错误', 'error');
            }
            if(!preg_match("/^(-|\+)?\d+$/",$price)/* && !preg_match("/^(-|\+)?\d+\.\d*$/",$price)*/){
                $this->_jumpGo('参数错误', 'error');
            }
            foreach($yeararr as $key=>$val){
                if(!trim($val)){
                    $this->_jumpGo('参数错误', 'error');
                }
                $yeararr[$key] = trim($val);
            }
            $time = time();
            $price_time = (isset($_POST['price_time']) && (intval($_POST['price_time']) > 0)) ? intval($_POST['price_time']) : $time;
            foreach($yeararr as $year){
                $adddata[] = array('key'=>$wine_key,'fname'=>$fname,'cname'=>$cname,'year'=>$year,'price'=>$price,'price_log'=>json_encode(array(array('p'=>$price,'t'=>$price_time))),'url'=>$url,'wap_url'=>$wap_url,'wine_caname_id'=>$caname_id,'wine_id'=>$wine_id,'internet_sales_id'=>$internet_sales_id,'agents_id'=>$agents_id,'add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']);
            }
            foreach($adddata as $val){
                $add_res = D('AgentsInternetSalesWine')->add($val);
                if($add_res){
                    foreach($_POST['img_filename'] as $k=>$v){
                        D('AgentsInternetSalesWineImg')->add(array('filename'=>$v,'description'=>$_POST['img_description'][$k],'alt'=>$_POST['img_alt'][$k],'queue'=>intval($_POST['img_queue'][$k]),'internet_sales_wine_id'=>$add_res,'is_del'=>'-1','add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']));
                        $index_img_filename = D('AgentsInternetSalesWineImg')->where(array('internet_sales_wine_id'=>$add_res,'is_del'=>'-1'))->order('queue asc')->limit(1)->getfield('filename');
                        if($index_img_filename){
                            D('AgentsInternetSalesWine')->save(array('id'=>$add_res,'img'=>$index_img_filename));
                        }
                    }
                }
            }
            //标识中文名表mark
            D('WineCaname')->save(array('id'=>$caname_id,'mark'=>1));
            //更新标准酒款参考价格
            foreach($yeararr as $year){
                $ywine_id = D('Ywine')->where(array('year'=>$year,'wine_id'=>$wine_id,'is_del'=>'-1'))->getfield('id');
                if($ywine_id){
                    D('Ywine')->save(array('id'=>$ywine_id,'price'=>$price));
                }else{
                    D('Ywine')->add(array('year'=>$year,'price'=>$price,'wine_id'=>$wine_id,'add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']));
                }
            }
            //过滤掉抓取酒款
            if(isset($_POST['capture_data_id']) && (intval($_POST['capture_data_id']) > 0)){
                D('CrawlWineData')->save(array('id'=>intval($_POST['capture_data_id']),'is_del'=>'1'));
            }
            //复制图片给标准酒款
            if(intval($_POST['is_copy_img_to_wine']) > 0){
                foreach($_POST['img_filename'] as $k=>$v){
                    $copy_wine_img_adddata[] = array('filename'=>$v,'description'=>$_POST['img_description'][$k],'alt'=>$_POST['img_alt'][$k],'queue'=>intval($_POST['img_queue'][$k]),'wine_id'=>$wine_id,'status'=>'1','is_del'=>'-1','add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']);
                }
                D('WineImg')->addAll($copy_wine_img_adddata);
            }
            //将关联的标准酒款设为已验证
            D('Wine')->save(array('id'=>$wine_id,'is_verify'=>'1'));
            $this->_jumpGo('网络销售酒款添加成功,增加了'.count($adddata).'款', 'succeed', base64_decode($_backpage));
        }
        $_GET['agents_id'] = Input::getVar(intval($_GET['agents_id']));
        $_GET['internet_sales_id'] = Input::getVar(intval($_GET['internet_sales_id']));
        $agents_res = D('Agents')->where(array('id'=>$_GET['agents_id'],'is_del'=>'-1'))->find();
        $internet_sales_res = D('AgentsInternetSales')->where(array('id'=>$_GET['internet_sales_id'],'is_del'=>'-1'))->find();
        $internet_sales_agents_res = D('Agents')->where(array('id'=>$internet_sales_res['agents_id'],'is_del'=>'-1'))->find();
        if($internet_sales_res && $internet_sales_agents_res){
            $this->assign('internet_sales_res',$internet_sales_res);
            $this->assign('agents_res',$internet_sales_agents_res);
            $internet_sales_list = D('Agents')->internetSalesList(false,array('agents_id'=>$internet_sales_agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->agentsList();
            $this->assign('agents_list',$agents_list);
        }elseif($agents_res){
            $this->assign('agents_res',$agents_res);
            $internet_sales_list = D('Agents')->internetSalesList(false,array('agents_id'=>$agents_res['id']));
            $this->assign('internet_sales_list',$internet_sales_list);
            $agents_list = D('Agents')->agentsList();
            $this->assign('agents_list',$agents_list);
        }else{
            $agents_list = D('Agents')->agentsList();
            $this->assign('agents_list',$agents_list);
        }
        $this->assign('_backpage',$_backpage);
        $this->display();
    }

    /**
     * 修改网络渠道销售酒款
     */
    public function internet_sales_wine_edit(){
        $id = intval(Input::getVar($_REQUEST['id']));
        $_backpage = Input::getVar($_REQUEST['_backpage']);//return_page_parameter
        if($this->isPost()) {
            $wine_id = intval(Input::getVar($_POST['wine_id']));
            $caname_id = intval(Input::getVar($_POST['caname_id']));
            $old_caname_id = intval(Input::getVar($_POST['old_caname_id']));
            $agents_id = intval(Input::getVar($_POST['agents_id']));
            $internet_sales_id = intval(Input::getVar($_POST['internet_sales_id']));
            $fname = Input::getVar($_POST['fname']);
            $cname = Input::getVar($_POST['cname']);
            $year = Input::getVar($_POST['year']);
            $price = Input::getVar($_POST['price']);
            $old_price = Input::getVar($_POST['old_price']);
            $old_price_trend = Input::getVar($_POST['old_price_trend']);
            $old_price_log = Input::getVar($_POST['old_price_log']);
            $url = Input::getVar($_POST['url']);
            if(!$agents_id || !$caname_id || !$internet_sales_id || !$wine_id || !$fname || !$cname || !$year || !$url){
                $this->_jumpGo('参数错误', 'error');
            }
            if(!preg_match("/^(-|\+)?\d+$/",$price) && !preg_match("/^(-|\+)?\d+\.\d*$/",$price)){
                $this->_jumpGo('参数错误', 'error');
            }
            $time = time();
            $editdata = array('id'=>$id,'fname'=>$fname,'cname'=>$cname,'year'=>$year,'price'=>$price,'url'=>$url,'wine_caname_id'=>$caname_id,'wine_id'=>$wine_id,'internet_sales_id'=>$internet_sales_id,'agents_id'=>$agents_id,'last_edit_time'=>$time,'last_edit_aid'=>$_SESSION['admin_uid']);
            if(intval(Input::getVar($_POST['is_check_price'])) == 1 || $old_price != $price){
                $price_log_arr = json_decode($old_price_log,true) ? json_decode($old_price_log,true) : array();
                $price_log_push = array('p'=>$price,'t'=>$time);
                array_push($price_log_arr,$price_log_push);
                $editdata['price_log'] = json_encode($price_log_arr);
                $editdata['price_trend'] = $this->get_price_trend($id,$price,$old_price,$old_price_trend);
            }
            if(!D('AgentsInternetSalesWine')->save($editdata))
                $this->_jumpGo('修改失败');
            //图片
            foreach($_POST['img_filename'] as $key=>$val){
                $img_data = array(
                                  'internet_sales_wine_id' => $id,
                                  'filename' => Input::getVar($_POST['img_filename'][$key]),
                                  'description' => Input::getVar($_POST['img_description'][$key]),
                                  'alt' => Input::getVar($_POST['img_alt'][$key]),
                                  'queue' => Input::getVar($_POST['img_queue'][$key]),
                                  'add_time'=>$time,
                                  'add_aid'=>$_SESSION['admin_uid'],
                                  'last_edit_time'=>$time,
                                  'last_edit_aid'=>$_SESSION['admin_uid'],
                                  );
                D('AgentsInternetSalesWineImg')->add($img_data);
            }
            foreach($_POST['upd_img_id'] as $key=>$val){
                $upd_img_data = array(
                                      'id' => Input::getVar($_POST['upd_img_id'][$key]),
                                      'description' => Input::getVar($_POST['upd_img_description'][$key]),
                                      'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
                                      'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
                                      'last_edit_time'=>$time,
                                      'last_edit_aid'=>$_SESSION['admin_uid'],
                                      );
                D('AgentsInternetSalesWineImg')->save($upd_img_data);
            }
            D('AgentsInternetSalesWineImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>$time,'last_edit_aid'=>$_SESSION['admin_uid']));
            //更新标准图片
            $index_img_filename = D('AgentsInternetSalesWineImg')->where(array('internet_sales_wine_id'=>$id,'is_del'=>'-1'))->order('queue asc')->limit(1)->getfield('filename');
            if($index_img_filename){
                D('AgentsInternetSalesWine')->save(array('id'=>$id,'img'=>$index_img_filename));
            }
            //标识中文名表mark
            if($old_caname_id !== $caname_id){
                if($caname_id !== 0){
                    D('WineCaname')->save(array('id'=>$caname_id,'mark'=>'1'));
                }
                if($old_caname_id !== 0){
                    $old_caname_i_count = D('AgentsInternetSalesWine')->where(array('wine_caname_id'=>$old_caname_id,'status'=>'1','is_del'=>'-1'))->count();
                    $old_caname_s_count = D('AgentsStoreSalesWine')->where(array('wine_caname_id'=>$old_caname_id,'status'=>'1','is_del'=>'-1'))->count();
                    if($old_caname_i_count == 0 && $old_caname_s_count == 0){
                        D('WineCaname')->save(array('id'=>$old_caname_id,'mark'=>'0'));
                    }
                }
            }
            //将关联的标准酒款设为已验证
            D('Wine')->save(array('id'=>$wine_id,'is_verify'=>'1'));
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_backpage));
        }
        if(!$res = D('AgentsInternetSalesWine')->where(array('id'=>$id,'is_del'=>'-1'))->find())
            $this->_jumpGo('数据获取异常', 'error', base64_decode($_backpage));
        if($res['wine_res'] = D('Wine')->field('id,fname,cname,country_id')->where(array('id'=>$res['wine_id'],'merge_id'=>0,'is_del'=>'-1'))->find()){
            $wine_caname_list = D('WineCaname')->where(array('cname'=>array('neq',''),'wine_id'=>$res['wine_res']['id']))->select();
            $res['country_res'] = D('Country')->field('id,fname,cname')->where(array('id'=>$res['wine_res']['country_id'],'is_del'=>'-1'))->find();
            foreach(D('JoinWineRegion')->where(array('wine_id'=>$res['wine_res']['id'],'is_del'=>'-1'))->getfield('region_id',true) as $key=>$region_id){
                while($region_id){
                    if($region_res = D('Region')->where(array('id'=>$region_id,'is_del'=>'-1'))->find())
                        $res['region_res'][$key][] = array('id'=>$region_res['id'],'fname'=>$region_res['fname'],'cname'=>$region_res['cname']);
                    $region_id = $region_res['pid'];
                }
                if($res['region_res'][$key]) $res['region_res'][$key] = array_reverse($res['region_res'][$key]);
            }
        }
        $agents_list = D('Agents')->field('id,fname,cname')->where(array('is_del'=>'-1'))->select();
        if(D('Agents')->where(array('id'=>$res['agents_id'],'is_del'=>'-1'))->find()){
            $internet_sales_list = D('AgentsInternetSales')->where(array('agents_id'=>$res['agents_id'],'is_del'=>'-1'))->select();
        }
        $res['img_res'] = D('AgentsInternetSalesWineImg')->field('id,filename,description,alt,queue')->where(array('internet_sales_wine_id'=>$res['id'],'is_del'=>'-1'))->select();
        $this->assign('res',$res);
        $this->assign('wine_caname_list',$wine_caname_list);
        $this->assign('agents_list',$agents_list);
        $this->assign('internet_sales_list',$internet_sales_list);
        $this->assign('_backpage',$_backpage);
        $this->display();
    }


    /**
     * 开启/关闭转变
     */
    public function internet_sales_wine_chgStatus() {
        $id = Input::getVar($_GET['id']);
        $status = Input::getvar($_GET['status']);
        $data = array(
                      'id' => $id,
                      'status' => $status,
                      );
        $this->_update(D('AgentsInternetSalesWine'),$data);
        //caname_mark
        $caname_id = intval($_REQUEST['caname_id']);
        if($caname_id){
            if($status == '1'){
                D('WineCaname')->save(array('id'=>$caname_id,'mark'=>'1'));
            }
            if($status == '-1'){
                $caname_i_count = D('AgentsInternetSalesWine')->where(array('wine_caname_id'=>$caname_id,'status'=>'1','is_del'=>'-1'))->count();
                $caname_s_count = D('AgentsStoreSalesWine')->where(array('wine_caname_id'=>$caname_id,'status'=>'1','is_del'=>'-1'))->count();
                if($caname_i_count == 0 && $caname_s_count == 0){
                    D('WineCaname')->save(array('id'=>$caname_id,'mark'=>'0'));
                }
            }
        }
        $this->_jumpGo('ID为'.$id.'的网络代理酒款状态更改成功', 'succeed', base64_decode($_REQUEST['_backpage']));
    }

    /**
     * 网络销售渠道酒款删除
     */
    public function internet_sales_wine_del() {
        $agents_id = Input::getVar($_REQUEST['agents_id']);
        //获取id
        $id = Input::getVar($_REQUEST['id']);
        //获取批量删除
        $ids = $_REQUEST['ids'];
        $model = D('AgentsInternetSalesWine');
        if($id) {
            $map = array('id' => $id);
        } elseif($ids) {
            $map = array('id' => array('in', $ids));
        }
        if($map) {
            $data = array(
                          'is_del' => '1',
                          'last_edit_time' => time(),
                          'last_edit_aid' => $_SESSION['admin_uid'],
                          );
            $model->where($map)->save($data);
            $this->_jumpGo('删除成功','succeed', base64_decode($_REQUEST['_backpage']));
        }
        $this->_jumpGo('删除失败，参数为空', 'error', base64_decode($_REQUEST['_backpage']));
    }

    /**
     * 上传文件
     */
    public function internet_sales_wine_upload(){
        //缩略图
        import('@.ORG.Util.Image');
        $image = new Image();
        if(isset($_POST['url'])){
            if($filename = A('Common')->GrabImage($_POST['url'],'Wine/images/','jpg')){
                $result = array(
                    'error' => 0,
                    'url' => C('DOMAIN.UPLOAD') . 'Jiuku/Wine/images/'.$filename,
                    'filename' => $filename,
                );
                $file = C('UPLOAD_PATH').'Wine/images/'.$result['filename'];
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
        $type = isset($_GET['type']) ? Input::getVar($_GET['type']).'/' : '';
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        $cfg = array(
            'ext' => C('UPLOAD_ALLOW_EXT'),
            'size' => C('UPLOAD_MAXSIZE'),
            'path' => C('UPLOAD_PATH').$type,
        );
        $upload->config($cfg);
        $rest = $upload->uploadFile('imgFile');
        if($rest['errno']) {
            $result = array(
                'error' => 1,
                'message' => $upload->error(),
            );
            $this->_ajaxDisplay($result);
        }
        $result = array(
            'error' => 0,
            'url' => C('DOMAIN.UPLOAD') . 'Jiuku/' . $type . $rest['path'],
            'filename' => $rest['path'],
        );
        $file = C('UPLOAD_PATH').$type.$result['filename'];
        $image->thumb2($file,$file.'.160.160',160,160,0,60);
        $image->thumb3($file,$file.'.90.120',90,120);
        $image->thumb3($file,$file.'.180.240',180,240);
        $image->thumb3($file,$file.'.600.600',600,600);
        $this->_ajaxDisplay($result);
    }

    /**
     * 抓取数据列表页
     */
    public function capture_data(){
        $internet_sales_res = D('CrawlWineData')->field('internet_sales_name')->where(array('is_del'=>'-1'))->group('internet_sales_name')->select();
        $this->assign('internet_sales_res',$internet_sales_res);
        $internet_sales_name = Input::getVar($_REQUEST['internet_sales_name']);
        $keyword = Input::getVar($_REQUEST['keyword']);
        $status = Input::getVar($_REQUEST['status']);
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
        if($status != '-1') {
            $map['status'] = $status;
            $url .= '&status=' . $status;
        }
        $map['is_del'] = '-1';
        $list = $this->_list(D('CrawlWineData'), $map, 15, $url);
        foreach($list as $key=>$val){
            $list[$key]['date'] = date("Y-m-d H:i:s",$val['time']);
        }
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 实体销售渠道酒款列表
     */
    public function store_sales_wine_list(){
        if($this->isPost()){
            $jump_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            foreach($_POST as $key=>$val){
                $jump_url .= '&' . $key . '=' . $val;
            }
            header('Location: ' . $jump_url);   exit;
        }
        $_GET['agents_id'] = Input::getVar(intval($_GET['agents_id']));
        $_GET['store_sales_id'] = Input::getVar(intval($_GET['store_sales_id']));
        $_GET['keyword'] = Input::getVar(trim($_GET['keyword']));
        $_GET['status'] = Input::getVar(intval($_GET['status']));
        $agents_res = D('Agents')->where(array('id'=>$_GET['agents_id'],'is_del'=>'-1'))->find();
        $store_sales_res = D('AgentsStoreSales')->where(array('id'=>$_GET['store_sales_id'],'is_del'=>'-1'))->find();
        $store_sales_agents_res = D('Agents')->where(array('id'=>$store_sales_res['agents_id'],'is_del'=>'-1'))->find();
        if($store_sales_res && $store_sales_agents_res){
            $map['store_sales_id'] = $store_sales_res['id'];
            $url .= '&store_sales_id='.$store_sales_res['id'];
            $this->assign('store_sales_res',$store_sales_res);
            $this->assign('agents_res',$store_sales_agents_res);
            $store_sales_list = D('Agents')->storeSalesList('keyid',array('agents_id'=>$store_sales_agents_res['id']));
            $this->assign('store_sales_list',$store_sales_list);
            $agents_list = D('Agents')->agentsList('keyid');
            $this->assign('agents_list',$agents_list);
        }elseif($agents_res){
            $map['agents_id'] = $agents_res['id'];
            $url .= '&agents_id='.$agents_res['id'];
            $this->assign('agents_res',$agents_res);
            $store_sales_list = D('Agents')->storeSalesList('keyid',array('agents_id'=>$agents_res['id']));
            $this->assign('store_sales_list',$store_sales_list);
            $agents_list = D('Agents')->agentsList('keyid');
            $this->assign('agents_list',$agents_list);
        }else{
            $agents_list = D('Agents')->agentsList('keyid');
            $this->assign('agents_list',$agents_list);
        }
        if($_GET['keyword']) {
            $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['url'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        if($_GET['status']) {
            $map['status'] = $_GET['status'];
            $url .= '&status=' . $_GET['status'];
        }
        $map['is_del'] = '-1';
        $count = D('AgentsStoreSalesWine')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('AgentsStoreSalesWine')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['agents_res'] = $agents_list[$val['agents_id']];
            $list[$key]['store_sales_res'] = $store_sales_list[$val['store_sales_id']];
            $list[$key]['wine_res'] = D('Wine')->field('id,fname,cname')->where(array('id'=>$val['wine_id'],'is_del'=>'-1','merge_id'=>0))->find();
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("agents_list", $agents_list);
        $this->assign("store_sales_list", $store_sales_list);
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }

    /**
     * 添加实体销售酒款
     */
    public function store_sales_wine_add(){
        $_backpage = Input::getVar($_REQUEST['_backpage']);//return_page_parameter
        if($this->isPost()) {
            $wine_id = intval(Input::getVar($_POST['wine_id']));
            $caname_id = intval(Input::getVar($_POST['caname_id']));
            $agents_id = intval(Input::getVar($_POST['agents_id']));
            $store_sales_id = intval(Input::getVar($_POST['store_sales_id']));
            $fname = Input::getVar($_POST['fname']);
            $cname = Input::getVar($_POST['cname']);
            $yeararr = explode(',',Input::getVar($_POST['yearstr']));
            $price = Input::getVar($_POST['price']);
            if(!$agents_id || !$caname_id || !$store_sales_id || !$wine_id || !$fname || !$cname){
                $this->_jumpGo('参数错误', 'error');
            }
            if(!preg_match("/^(-|\+)?\d+$/",$price) && !preg_match("/^(-|\+)?\d+\.\d*$/",$price)){
                $this->_jumpGo('参数错误', 'error');
            }
            foreach($yeararr as $key=>$val){
                if(!trim($val)){
                    $this->_jumpGo('参数错误', 'error');
                }
                $yeararr[$key] = trim($val);
            }
            $time = time();
            foreach($yeararr as $year){
                $adddata[] = array('fname'=>$fname,'cname'=>$cname,'year'=>$year,'price'=>$price,'price_log'=>json_encode(array(array('p'=>$price,'t'=>$time))),'url'=>$url,'wine_caname_id'=>$caname_id,'wine_id'=>$wine_id,'store_sales_id'=>$store_sales_id,'agents_id'=>$agents_id,'add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']);
            }
            foreach($adddata as $val){
                $add_res = D('AgentsStoreSalesWine')->add($val);
                if($add_res){
                    foreach($_POST['img_filename'] as $k=>$v){
                        D('AgentsStoreSalesWineImg')->add(array('filename'=>$v,'description'=>$_POST['img_description'][$k],'alt'=>$_POST['img_alt'][$k],'queue'=>intval($_POST['img_queue'][$k]),'store_sales_wine_id'=>$add_res,'is_del'=>'-1','add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']));
                        $index_img_filename = D('AgentsStoreSalesWineImg')->where(array('store_sales_wine_id'=>$add_res,'is_del'=>'-1'))->order('queue asc')->limit(1)->getfield('filename');
                        if($index_img_filename){
                            D('AgentsStoreSalesWine')->save(array('id'=>$add_res,'img'=>$index_img_filename));
                        }
                    }
                }
            }
            //更新标准酒款参考价格
            foreach($yeararr as $year){
                $ywine_id = D('Ywine')->where(array('year'=>$year,'wine_id'=>$wine_id,'is_del'=>'-1'))->getfield('id');
                if($ywine_id){
                    D('Ywine')->save(array('id'=>$ywine_id,'price'=>$price));
                }else{
                    D('Ywine')->add(array('year'=>$year,'price'=>$price,'wine_id'=>$wine_id,'add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']));
                }
            }
            //复制图片给标准酒款
            if(intval($_POST['is_copy_img_to_wine']) > 0){
                foreach($_POST['img_filename'] as $k=>$v){
                    $copy_wine_img_adddata[] = array('filename'=>$v,'description'=>$_POST['img_description'][$k],'alt'=>$_POST['img_alt'][$k],'queue'=>intval($_POST['img_queue'][$k]),'wine_id'=>$wine_id,'status'=>'1','is_del'=>'-1','add_time'=>$time,'last_edit_time'=>$time,'add_aid'=>$_SESSION['admin_uid'],'last_edit_aid'=>$_SESSION['admin_uid']);
                }
                D('WineImg')->addAll($copy_wine_img_adddata);
            }
            //将关联的标准酒款设为已验证
            D('Wine')->save(array('id'=>$wine_id,'is_verify'=>'1'));
            $this->_jumpGo('实体销售酒款添加成功,增加了'.count($adddata).'款', 'succeed', base64_decode($_backpage));
        }
        $agents_list = D('Agents')->where(array('is_del'=>'-1'))->select();
        $this->assign('agents_list',$agents_list);

        $agents_id = Input::getVar($_GET['agents_id']);
        $store_sales_id = Input::getVar($_GET['store_sales_id']);
        $agents_res = D('Agents')->where(array('id'=>$agents_id,'is_del'=>'-1'))->find();
        $store_sales_res = D('AgentsStoreSales')->where(array('id'=>$store_sales_id,'is_del'=>'-1'))->find();

        if($store_sales_res && $agents_res = D('Agents')->where(array('id'=>$store_sales_res['agents_id'],'is_del'=>'-1'))->find()){
            $store_sales_list = D('AgentsStoreSales')->where(array('agents_id'=>$store_sales_res['agents_id'],'is_del'=>'-1'))->select();
            $this->assign('agents_res',$agents_res);
            $this->assign('store_sales_list',$store_sales_list);
            $this->assign('store_sales_res',$store_sales_res);
        }elseif($agents_res){
            $store_sales_list = D('AgentsStoreSales')->where(array('agents_id'=>$agents_res['id'],'is_del'=>'-1'))->select();
            $this->assign('agents_res',$agents_res);
            $this->assign('store_sales_list',$store_sales_list);
        }
        $this->assign('_backpage',$_backpage);
        $this->display();
    }


    /**
     * 修改实体渠道销售酒款
     */
    public function store_sales_wine_edit(){
        $id = intval(Input::getVar($_REQUEST['id']));
        $_backpage = Input::getVar($_REQUEST['_backpage']);//return_page_parameter
        if($this->isPost()) {
            $wine_id = intval(Input::getVar($_POST['wine_id']));
            $caname_id = intval(Input::getVar($_POST['caname_id']));
            $old_caname_id = intval(Input::getVar($_POST['old_caname_id']));
            $agents_id = intval(Input::getVar($_POST['agents_id']));
            $store_sales_id = intval(Input::getVar($_POST['store_sales_id']));
            $fname = Input::getVar($_POST['fname']);
            $cname = Input::getVar($_POST['cname']);
            $year = Input::getVar($_POST['year']);
            $price = Input::getVar($_POST['price']);
            $old_price = Input::getVar($_POST['old_price']);
            $old_price_trend = Input::getVar($_POST['old_price_trend']);
            $old_price_log = Input::getVar($_POST['old_price_log']);
            $url = Input::getVar($_POST['url']);
            if(!$agents_id || !$caname_id || !$store_sales_id || !$wine_id || !$fname || !$cname || !$year){
                $this->_jumpGo('参数错误', 'error');
            }
            if(!preg_match("/^(-|\+)?\d+$/",$price) && !preg_match("/^(-|\+)?\d+\.\d*$/",$price)){
                $this->_jumpGo('参数错误', 'error');
            }
            $time = time();
            $editdata = array('id'=>$id,'fname'=>$fname,'cname'=>$cname,'year'=>$year,'price'=>$price,'wine_caname_id'=>$caname_id,'wine_id'=>$wine_id,'store_sales_id'=>$store_sales_id,'agents_id'=>$agents_id,'last_edit_time'=>$time,'last_edit_aid'=>$_SESSION['admin_uid']);
            if(intval(Input::getVar($_POST['is_check_price'])) == 1 || $old_price != $price){
                $price_log_arr = json_decode($old_price_log,true) ? json_decode($old_price_log,true) : array();
                $price_log_push = array('p'=>$price,'t'=>$time);
                array_push($price_log_arr,$price_log_push);
                $editdata['price_log'] = json_encode($price_log_arr);
                $editdata['price_trend'] = $this->get_price_trend($id,$price,$old_price,$old_price_trend);
            }
            if(!D('AgentsStoreSalesWine')->save($editdata))
                $this->_jumpGo('修改失败', 'error');
            //图片
            foreach($_POST['img_filename'] as $key=>$val){
                $img_data = array(
                                  'store_sales_wine_id' => $id,
                                  'filename' => Input::getVar($_POST['img_filename'][$key]),
                                  'description' => Input::getVar($_POST['img_description'][$key]),
                                  'alt' => Input::getVar($_POST['img_alt'][$key]),
                                  'queue' => Input::getVar($_POST['img_queue'][$key]),
                                  'add_time'=>$time,
                                  'add_aid'=>$_SESSION['admin_uid'],
                                  'last_edit_time'=>$time,
                                  'last_edit_aid'=>$_SESSION['admin_uid'],
                                  );
                D('AgentsStoreSalesWineImg')->add($img_data);
            }
            foreach($_POST['upd_img_id'] as $key=>$val){
                $upd_img_data = array(
                                      'id' => Input::getVar($_POST['upd_img_id'][$key]),
                                      'description' => Input::getVar($_POST['upd_img_description'][$key]),
                                      'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
                                      'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
                                      'last_edit_time'=>$time,
                                      'last_edit_aid'=>$_SESSION['admin_uid'],
                                      );
                D('AgentsStoreSalesWineImg')->save($upd_img_data);
            }
            D('AgentsStoreSalesWineImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>$time,'last_edit_aid'=>$_SESSION['admin_uid']));
            //更新标准图片
            $index_img_filename = D('AgentsStoreSalesWineImg')->where(array('store_sales_wine_id'=>$id,'is_del'=>'-1'))->order('queue asc')->limit(1)->getfield('filename');
            if($index_img_filename){
                D('AgentsStoreSalesWine')->save(array('id'=>$id,'img'=>$index_img_filename));
            }
            //标识中文名表mark
            if($old_caname_id !== $caname_id){
                if($caname_id !== 0){
                    D('WineCaname')->save(array('id'=>$caname_id,'mark'=>'1'));
                }
                if($old_caname_id !== 0){
                    $old_caname_i_count = D('AgentsInternetSalesWine')->where(array('wine_caname_id'=>$old_caname_id,'status'=>'1','is_del'=>'-1'))->count();
                    $old_caname_s_count = D('AgentsStoreSalesWine')->where(array('wine_caname_id'=>$old_caname_id,'status'=>'1','is_del'=>'-1'))->count();
                    if($old_caname_i_count == 0 && $old_caname_s_count == 0){
                        D('WineCaname')->save(array('id'=>$old_caname_id,'mark'=>'0'));
                    }
                }
            }
            //将关联的标准酒款设为已验证
            D('Wine')->save(array('id'=>$wine_id,'is_verify'=>'1'));
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_backpage));
        }
        if(!$res = D('AgentsStoreSalesWine')->where(array('id'=>$id,'is_del'=>'-1'))->find())
            $this->_jumpGo('数据获取异常', 'error', base64_decode($_backpage));
        if($res['wine_res'] = D('Wine')->field('id,fname,cname,country_id')->where(array('id'=>$res['wine_id'],'merge_id'=>0,'is_del'=>'-1'))->find()){
            $wine_caname_list = D('WineCaname')->where(array('cname'=>array('neq',''),'wine_id'=>$res['wine_res']['id']))->select();
            $res['country_res'] = D('Country')->field('id,fname,cname')->where(array('id'=>$res['wine_res']['country_id'],'is_del'=>'-1'))->find();
            foreach(D('JoinWineRegion')->where(array('wine_id'=>$res['wine_res']['id'],'is_del'=>'-1'))->getfield('region_id',true) as $key=>$region_id){
                while($region_id){
                    if($region_res = D('Region')->where(array('id'=>$region_id,'is_del'=>'-1'))->find())
                        $res['region_res'][$key][] = array('id'=>$region_res['id'],'fname'=>$region_res['fname'],'cname'=>$region_res['cname']);
                    $region_id = $region_res['pid'];
                }
                if($res['region_res'][$key]) $res['region_res'][$key] = array_reverse($res['region_res'][$key]);
            }
        }
        $agents_list = D('Agents')->field('id,fname,cname')->where(array('is_del'=>'-1'))->select();
        if(D('Agents')->where(array('id'=>$res['agents_id'],'is_del'=>'-1'))->find()){
            $store_sales_list = D('AgentsStoreSales')->where(array('agents_id'=>$res['agents_id'],'is_del'=>'-1'))->select();
        }
        $res['img_res'] = D('AgentsStoreSalesWineImg')->field('id,filename,description,alt,queue')->where(array('store_sales_wine_id'=>$res['id'],'is_del'=>'-1'))->select();
        $this->assign('res',$res);
        $this->assign('wine_caname_list',$wine_caname_list);
        $this->assign('agents_list',$agents_list);
        $this->assign('store_sales_list',$store_sales_list);
        $this->assign('_backpage',$_backpage);
        $this->display();
    }

    /**
     * 开启/关闭转变
     */
    public function store_sales_wine_chgStatus() {
        $_backpage = Input::getVar($_REQUEST['_backpage']);//return_page_parameter
        $id = Input::getVar($_GET['id']);
        $status = Input::getvar($_GET['status']);
        $data = array(
                      'id' => $id,
                      'status' => $status,
                      );
        $this->_update(D('AgentsStoreSalesWine'),$data);
        //caname_mark
        $caname_id = intval($_REQUEST['caname_id']);
        if($caname_id){
            if($status == '1'){
                D('WineCaname')->save(array('id'=>$caname_id,'mark'=>'1'));
            }
            if($status == '-1'){
                $caname_i_count = D('AgentsInternetSalesWine')->where(array('wine_caname_id'=>$caname_id,'status'=>'1','is_del'=>'-1'))->count();
                $caname_s_count = D('AgentsStoreSalesWine')->where(array('wine_caname_id'=>$caname_id,'status'=>'1','is_del'=>'-1'))->count();
                if($caname_i_count == 0 && $caname_s_count == 0){
                    D('WineCaname')->save(array('id'=>$caname_id,'mark'=>'0'));
                }
            }
        }
        $this->_jumpGo('ID为'.$id.'的网络代理酒款状态更改成功', 'succeed', base64_decode($_backpage));
    }

    /**
     * 实体销售酒款删除
     */
    public function store_sales_wine_del() {
        $_backpage = Input::getVar($_REQUEST['_backpage']);//return_page_parameter
        $agents_id = Input::getVar($_REQUEST['agents_id']);
        //获取id
        $id = Input::getVar($_REQUEST['id']);
        //获取批量删除
        $ids = $_REQUEST['ids'];
        $model = D('AgentsStoreSalesWine');
        if($id) {
            $map = array('id' => $id);
        } elseif($ids) {
            $map = array('id' => array('in', $ids));
        }
        if($map) {
            $data = array(
                          'is_del' => '1',
                          'last_edit_time' => time(),
                          'last_edit_aid' => $_SESSION['admin_uid'],
                          );
            $model->where($map)->save($data);
            $this->_jumpGo('删除成功','succeed', Url('store_sales_wine_list').base64_decode($_backpage));
        }
        $this->_jumpGo('删除失败，参数为空', 'error', Url('store_sales_wine_list').base64_decode($_backpage));
    }

    /**
     * 上传文件
     */
    public function store_sales_wine_upload(){
        $type = isset($_GET['type']) ? Input::getVar($_GET['type']).'/' : '';
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        $cfg = array(
            'ext' => C('UPLOAD_ALLOW_EXT'),
            'size' => C('UPLOAD_MAXSIZE'),
            'path' => C('UPLOAD_PATH').$type,
        );
        $upload->config($cfg);
        $rest = $upload->uploadFile('imgFile');
        if($rest['errno']) {
            $result = array(
                'error' => 1,
                'message' => $upload->error(),
            );
            $this->_ajaxDisplay($result);
        }
        $result = array(
            'error' => 0,
            'url' => C('DOMAIN.UPLOAD') . 'Jiuku/' . $type . $rest['path'],
            'filename' => $rest['path'],
        );
        //缩略图
        import('@.ORG.Util.Image');
        $image = new Image();
        $file = C('UPLOAD_PATH').$type.$result['filename'];
        $image->thumb2($file,$file.'.160.160',160,160,0,60);
        $image->thumb3($file,$file.'.90.120',90,120);
        $image->thumb3($file,$file.'.180.240',180,240);
        $image->thumb3($file,$file.'.600.600',600,600);
        $this->_ajaxDisplay($result);
    }


    /**
     * 价格趋势获取
     */
    public function get_price_trend($id,$price,$old_price,$old_price_trend){
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
    function get_area_f_sort($id = 0){
        $arealist = M('ArealevelCn','cmn_')->field('id,name,pid')->select();
        $sort_arr = array();
        $i = 1;
        while($id/* > 1*/ && $i){
            $i = 0;
            foreach($arealist as $val){
                if($val['id'] == $id){
                    $sort_arr[] = $val;
                    $i = 1;
                    $id = $val['pid'];
                    break;
                }
            }
        }
        $sort_arr = array_reverse($sort_arr);
        return $sort_arr;
    }
    function get_area_all_son_id($id = 0){
        if(!$id)    return false;
        $arealist = M('ArealevelCn','cmn_')->field('id,name,pid')->select();
        $i = 0;
        $idarr[$i] = array($id);
        while($idarr[$i]){
            foreach($arealist as $val){
                if(in_array($val['pid'], $idarr[$i])){
                    $idarr[$i+1][] = $val['id'];
                }
            }
            $i++;
        }
        $nidarr = array();
        foreach($idarr as $val){
            foreach($val as $sval){
                $nidarr[] = $sval;
            }
        }
        return $nidarr;
    }
}
