<?php
/**
 * 实体渠道管理
 */
class StoreSalesAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }

    /**
     * 实体渠道列表
     */
    public function index(){
        if($this->isPost()) $this->listpage_post_to_get();
        $_GET['agents_id'] = intval($_GET['agents_id']);
        $_GET['area_id'] = intval($_GET['area_id']);
        $map = array();
        $url = '';
        if(isset($_GET['agents_id']) && $_GET['agents_id'] != 0) {
            $map['agents_id'] = $_GET['agents_id'];
            $url .= '&agents_id='.$_GET['agents_id'];
        }
        if(isset($_GET['area_id']) && $_GET['area_id'] != 0) {
            $area_idarr = $this->get_area_all_son_id($_GET['area_id']);
            $_GET['area_f_sort'] = $this->get_area_f_sort($_GET['area_id']);
            $map['area_id'] = array('in',$area_idarr);
            $url .= '&area_id='.$_GET['area_id'];
        }
        if(isset($_GET['keyword']) && $_GET['keyword'] != '') {
            $map_k['name'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['tel'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        $map['is_del'] = '-1';
        $count = D('StoreSales')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('StoreSales')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $agents_list = D('Agents')->getList('keyid');
        foreach($list as $key=>$val){
            $list[$key]['agents_res'] = $agents_list[$val['agents_id']];
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
    public function add(){
        $_REQUEST['agents_id'] = intval($_REQUEST['agents_id']);
        if($this->isPost()){
            if(!$_REQUEST['agents_id'] || !$_POST['name'])
                $this->_jumpGo('添加失败，代入信息缺失', 'error');
            $_POST['img'] = $_POST['img_filename'][0] ? $_POST['img_filename'][0] : '';
            D('StoreSales')->create();
            $id = D('StoreSales')->add();
            if(!$id)
                $this->_jumpGo('添加失败', 'error');
            D('AgentsLog')->savelog('jiuku_agents_store_sales', $id, 1);//log
            foreach($_POST['img_filename'] as $key => $val){
                $img_data[] = array(
                    'filename' => $val,
                    'queue' => $key,
                    'description' => $_POST['img_description'][$key],
                    'alt' => $_POST['img_alt'][$key],
                    'store_sales_id' => $id,
                );
            }
            M('AgentsStoreSalesImg','jiuku_')->addall($img_data);
            $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $agents_list = D('Agents')->getList();
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 实体销售渠道编辑
     */
    public function edit(){
        $_REQUEST['id'] = intval($_REQUEST['id']);
        if(!$_REQUEST['id'])
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['agents_id'] = intval($_POST['agents_id']);
            $_POST['weekend_rest'] = intval($_POST['weekend_rest']);
            $_POST['is_sales_1'] = intval($_POST['is_sales_1']);
            $_POST['is_sales_2'] = intval($_POST['is_sales_2']);
            if(!$_POST['agents_id'] || !$_POST['name'])
                $this->_jumpGo('编辑失败，代入信息错误', 'error');
            $_POST['img'] = $_POST['img_filename'][0] ? $_POST['img_filename'][0] : '';
            D('StoreSales')->create();
            $is = D('StoreSales')->save();
            if($is === false)
                $this->_jumpGo('编辑失败', 'error');
            D('AgentsLog')->savelog('jiuku_agents_store_sales', $_REQUEST['id'], 2);//log
            M('AgentsStoreSalesImg','jiuku_')->where(array('store_sales_id'=>$_REQUEST['id']))->delete();
            foreach($_POST['img_filename'] as $key => $val){
                $img_data[] = array(
                    'filename' => $val,
                    'queue' => $key,
                    'description' => $_POST['img_description'][$key],
                    'alt' => $_POST['img_alt'][$key],
                    'store_sales_id' => $_REQUEST['id'],
                );
            }
            M('AgentsStoreSalesImg','jiuku_')->addall($img_data);
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $res = D('StoreSales')->where(array('id'=>$_REQUEST['id']))->find();
        if(!$res)
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        $res['img_res'] = M('AgentsStoreSalesImg','jiuku_')->where(array('store_sales_id'=>$_REQUEST['id'],'is_del'=>'-1'))->order('queue asc')->select();
        $res['area_f_sort'] = $this->get_area_f_sort($res['area_id']);
        $agents_list = D('Agents')->where(array('id'=>intval($res['agents_id'])))->find();
        $this->assign("res", $res);
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 实体销售渠道上传图片
     */
    public function upload(){
        $subpath = 'Agents/store_sales/images/';
        //缩略图
        import('@.ORG.Util.Image');
        $image = new Image();
        import('@.ORG.Util.Upload');
        $upload = new Upload();
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
        }
        $result = array(
            'error' => 0,
            'url' => C('UPLOAD_URL') . $subpath . $rest['path'],
            'filename' => $rest['path'],
        );
        $file = C('UPLOAD_PATH') . $subpath . $rest['path'];
        $image->thumb1($file,$file.'.800',800);
        $image->thumb1($file,$file.'.640',640);
        $image->thumb3($file,$file.'.180.196',180,196);
        $image->thumb3($file,$file.'.240.120',240,120);
        $this->_ajaxDisplay($result);
    }
    function get_area_f_sort($id = 0){
        if(!$id)    return false;
        static  $narealist;
        if(!$narealist){
            $arealist = M('ArealevelCn','cmn_')->field('id,name,pid')->select();
            $narealist = array();
            foreach($arealist as $val){
                $narealist[$val['id']] = $val;
            }
        }
        $sort_arr = array();
        while($narealist[$id]){
            $sort_arr[] = $narealist[$id];
            $id = $narealist[$id]['pid'];
        }
        $sort_arr = array_reverse($sort_arr);
        return $sort_arr;
    }
    function get_area_all_son_id($id = 0){
        if(!$id)    return false;
        static $arealist;
        if(!$arealist)
            $arealist = M('ArealevelCn','cmn_')->field('id,name,pid')->select();
        $pidarr = $idarr = array($id);
        while($pidarr){
            $npidarr = array();
            foreach($arealist as $val){
                if(in_array($val['pid'], $pidarr))
                    $npidarr[] = $idarr[] = $val['id'];
            }
            $pidarr = $npidarr;
        }
        return $idarr;
    }

}