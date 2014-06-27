<?php
/**
 * 实体酒款管理
 */
class StoreWineAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }

    /**
     * 实体酒款列表
     */
    public function index(){
        if($this->isPost()) $this->listpage_post_to_get();
        $_GET['agents_id'] = intval($_GET['agents_id']);
        $_GET['store_sales_id'] = intval($_GET['store_sales_id']);
        $agents_res = D('Agents')->where(array('id'=>$_GET['agents_id'],'is_del'=>'-1'))->find();
        $store_sales_res = D('StoreSales')->where(array('id'=>$_GET['store_sales_id'],'is_del'=>'-1'))->find();
        $store_sales_agents_res = D('Agents')->where(array('id'=>$store_sales_res['agents_id'],'is_del'=>'-1'))->find();
        $map = array();
        $url = '';
        if($store_sales_res && $store_sales_agents_res){
            $_GET['store_sales_id'] = $store_sales_res['id'];
            $_GET['agents_id'] = $store_sales_agents_res['id'];
            $map['store_sales_id'] = $store_sales_res['id'];
            $url .= '&store_sales_id='.$store_sales_res['id'];
            $store_sales_list = D('StoreSales')->getList('keyid',array('agents_id'=>$store_sales_agents_res['id']));
            $this->assign('store_sales_list',$store_sales_list);
            $agents_list = D('Agents')->getList('keyid');
            $this->assign('agents_list',$agents_list);
        }elseif($agents_res){
            unset($_GET['store_sales_id']);
            $_GET['agents_id'] = $agents_res['id'];
            $map['agents_id'] = $agents_res['id'];
            $url .= '&agents_id='.$agents_res['id'];
            $store_sales_list = D('StoreSales')->getList('keyid',array('agents_id'=>$agents_res['id']));
            $this->assign('store_sales_list',$store_sales_list);
            $agents_list = D('Agents')->getList('keyid');
            $this->assign('agents_list',$agents_list);
        }else{
            unset($_GET['store_sales_id']);
            unset($_GET['agents_id']);
            $agents_list = D('Agents')->getList('keyid');
            $this->assign('agents_list',$agents_list);
        }
        if(isset($_GET['keyword']) && $_GET['keyword'] != '') {
            $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        if(isset($_GET['status']) && $_GET['status'] != '') {
            $map['status'] = $_GET['status'];
            $url .= '&status=' . $_GET['status'];
        }
        $map['is_del'] = '-1';
        $count = D('StoreWine')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('StoreWine')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $store_sales_list = D('StoreSales')->getList('keyid');
        foreach($list as $key=>$val){
            $list[$key]['agents_res'] = $agents_list[$val['agents_id']];
            $list[$key]['store_sales_res'] = $store_sales_list[$val['store_sales_id']];
            $list[$key]['wine_res'] = M('Wine','jiuku_')->field('id,fname,cname')->where(array('id'=>$val['wine_id'],'is_del'=>'-1','merge_id'=>0))->find();
        }
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }

    /**
     * 添加实体酒款
     */
    public function add(){
        if($this->isPost()) {
            $_POST['agents_id'] = intval($_POST['agents_id']);
            $_POST['store_sales_id'] = intval($_POST['store_sales_id']);
            $_POST['wine_id'] = intval($_POST['wine_id']);
            $_POST['wine_caname_id'] = intval($_POST['wine_caname_id']);
            if(!$_POST['agents_id'] || !$_POST['store_sales_id'] || !$_POST['wine_id'] || !$_POST['wine_caname_id'] || !$_POST['fname'] || !$_POST['cname'])
                $this->_jumpGo('添加失败，代入信息错误', 'error');
            if(!preg_match("/^(-|\+)?\d+$/",$_POST['price']))
                $this->_jumpGo('添加失败，代入信息错误', 'error');
            $yeararr = explode(',',$_POST['yearstr']);
            $_POST['price_log'] = json_encode(array(array('p'=>(int)$_POST['price'],'t'=>(int)$price_time)));
            $_POST['img'] = $_POST['img_filename'][0];
            $img_data = array();
            foreach($yeararr as $_POST['year']){
                D('StoreWine')->create();
                $id = D('StoreWine')->add();
                if(!$id)
                    continue;
                D('AgentsLog')->savelog('jiuku_agents_store_sales_wine', $id, 1);//log
                foreach($_POST['img_filename'] as $k=>$v){
                    $img_data[] = array(
                        'filename'=>$v,
                        'description'=>$_POST['img_description'][$k],
                        'alt'=>$_POST['img_alt'][$k],
                        'queue'=>$k,
                        'store_sales_wine_id'=>$id
                    );
                }
            }
            M('AgentsStoreSalesWineImg','jiuku_')->addall($img_data);
            $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $_GET['agents_id'] = intval($_GET['agents_id']);
        $_GET['store_sales_id'] = intval($_GET['store_sales_id']);
        $agents_res = D('Agents')->where(array('id'=>$_GET['agents_id'],'is_del'=>'-1'))->find();
        $store_sales_res = D('StoreSales')->where(array('id'=>$_GET['store_sales_id'],'is_del'=>'-1'))->find();
        $store_sales_agents_res = D('Agents')->where(array('id'=>$store_sales_res['agents_id'],'is_del'=>'-1'))->find();
        if($store_sales_res && $store_sales_agents_res){
            $this->assign('store_sales_res',$store_sales_res);
            $this->assign('agents_res',$store_sales_agents_res);
            $store_sales_list = D('StoreSales')->getList(false,array('agents_id'=>$store_sales_agents_res['id']));
            $this->assign('store_sales_list',$store_sales_list);
            $agents_list = D('Agents')->getList();
            $this->assign('agents_list',$agents_list);
        }elseif($agents_res){
            $this->assign('agents_res',$agents_res);
            $store_sales_list = D('StoreSales')->getList(false,array('agents_id'=>$agents_res['id']));
            $this->assign('store_sales_list',$store_sales_list);
            $agents_list = D('Agents')->getList();
            $this->assign('agents_list',$agents_list);
        }else{
            $agents_list = D('Agents')->getList();
            $this->assign('agents_list',$agents_list);
        }
        $this->display();
    }

    /**
     * 编辑实体酒款
     */
    public function edit(){
        if(!$_REQUEST['id'] = intval($_REQUEST['id']))
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()) {
            $_POST['agents_id'] = intval($_POST['agents_id']);
            $_POST['store_sales_id'] = intval($_POST['store_sales_id']);
            $_POST['wine_id'] = intval($_POST['wine_id']);
            $_POST['wine_caname_id'] = intval($_POST['wine_caname_id']);
            if(!$_POST['agents_id'] || !$_POST['store_sales_id'] || !$_POST['wine_id'] || !$_POST['wine_caname_id'] || !$_POST['fname'] || !$_POST['cname'] || !$_POST['year'])
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
            D('StoreWine')->create();
            $is = D('StoreWine')->save();
            if($is = false)
                $this->_jumpGo('编辑失败'. 'error');
            D('AgentsLog')->savelog('jiuku_agents_store_sales_wine', $_REQUEST['id'], 2);//log
            //图片
            M('AgentsStoreSalesWineImg','jiuku_')->where(array('store_sales_wine_id'=>$_REQUEST['id']))->delete();
            $img_data = array();
            foreach($_POST['img_filename'] as $key=>$val){
                $img_data[] = array(
                    'filename' => $_POST['img_filename'][$key],
                    'description' => $_POST['img_description'][$key],
                    'alt' => $_POST['img_alt'][$key],
                    'queue' => $key,
                    'store_sales_wine_id' => $_REQUEST['id'],
                );
            }
            M('AgentsStoreSalesWineImg','jiuku_')->addall($img_data);
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        if(!$res = D('StoreWine')->where(array('id'=>$_REQUEST['id'],'is_del'=>'-1'))->find())
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($res['wine_res'] = M('Wine','jiuku_')->field('id,fname,cname')->where(array('id'=>$res['wine_id'],'merge_id'=>0,'is_del'=>'-1'))->find()){
            $wine_caname_list = M('WineCaname','jiuku_')->where(array('cname'=>array('neq',''),'wine_id'=>$res['wine_res']['id'],'status'=>'1','is_del'=>'-1'))->select();
        }
        $agents_list = D('Agents')->field('id,fname,cname')->where(array('is_del'=>'-1'))->select();
        if(D('Agents')->where(array('id'=>$res['agents_id'],'is_del'=>'-1'))->find()){
            $store_sales_list = D('StoreSales')->where(array('agents_id'=>$res['agents_id'],'is_del'=>'-1'))->select();
        }
        $res['img_res'] = M('AgentsStoreSalesWineImg','jiuku_')->field('id,filename,description,alt')->where(array('store_sales_wine_id'=>$res['id'],'is_del'=>'-1'))->select();
        $this->assign('res',$res);
        $this->assign('wine_caname_list',$wine_caname_list);
        $this->assign('agents_list',$agents_list);
        $this->assign('store_sales_list',$store_sales_list);
        $this->display();
    }

    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $id = intval($_GET['id']);
        $is = D('StoreWine')->save(array('id'=>$id,'status'=>$_GET['status']));
        if($is === false)
            $this->_jumpGo('状态更改失败', 'error', base64_decode($_REQUEST['_backpage']));
        D('AgentsLog')->savelog('jiuku_agents_store_sales_wine', $id, 2);//log
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