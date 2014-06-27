<?php
/**
 * 网络渠道管理
 */
class InternetSalesAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }
    /**
     * 网络销售渠道列表
     */
    public function index(){
        if($this->isPost()) $this->listpage_post_to_get();
        $_GET['agents_id'] = intval($_GET['agents_id']);
        $map = array();
        $url = '';
        if(isset($_GET['agents_id']) && $_GET['agents_id'] != 0) {
            $map['agents_id'] = $_GET['agents_id'];
            $url .= '&agents_id='.$_GET['agents_id'];
        }
        if(isset($_GET['keyword']) && $_GET['keyword'] != '') {
            $map_k['name'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['url'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        $map['is_del'] = '-1';
        $count = D('InternetSales')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('InternetSales')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $agents_list = D('Agents')->getList('keyid');
        foreach($list as $key=>$val){
            $list[$key]['agents_res'] = $agents_list[$val['agents_id']];
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
    public function add(){
        $_REQUEST['agents_id'] = intval($_REQUEST['agents_id']);
        if($this->isPost()){
            if(!$_REQUEST['agents_id'] || !$_POST['name'] || !$_POST['url'])
                $this->_jumpGo('添加失败，代入信息缺失', 'error');
            $_POST['logo'] = $_POST['img_filename'][0] ? $_POST['img_filename'][0] : '';
            D('InternetSales')->create();
            $id = D('InternetSales')->add();
            if(!$id)
                $this->_jumpGo('添加失败', 'error');
            D('AgentsLog')->savelog('jiuku_agents_internet_sales', $id, 1);//log
            $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $agents_list = D('Agents')->getList();
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 网络销售渠道编辑
     */
    public function edit(){
        $_REQUEST['id'] = intval($_REQUEST['id']);
        if(!$_REQUEST['id'])
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            $_POST['agents_id'] = intval($_POST['agents_id']);
            if(!$_POST['agents_id'] || !$_POST['name'] || !$_POST['url'])
                $this->_jumpGo('编辑失败，代入信息错误', 'error');
            $_POST['logo'] = $_POST['img_filename'][0] ? $_POST['img_filename'][0] : '';
            D('InternetSales')->create();
            $is = D('InternetSales')->save();
            if($is === false)
                $this->_jumpGo('编辑失败', 'error');
            D('AgentsLog')->savelog('jiuku_agents_internet_sales', $_REQUEST['id'], 2);//log
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $res = D('InternetSales')->where(array('id'=>$_REQUEST['id']))->find();
        if(!$res)
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        $agents_list = D('Agents')->getList();
        $this->assign("res", $res);
        $this->assign("agents_list", $agents_list);
        $this->display();
    }

    /**
     * 上传文件
     */
    public function upload(){
        import('@.ORG.Util.Image');
        $image = new Image();
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        if($_FILES['upd_img']){
            $subpath = 'Agents/icon/';
            $cfg = array(
                'ext' => 'jpg,jpeg,png',
                'size' => C('UPLOAD_MAXSIZE'),
                'path' => C('UPLOAD_PATH') . $subpath,
            );
            $upload->config($cfg);
            $rest = $upload->uploadFile('upd_img');
            $size = $image->getImageInfo(C('UPLOAD_URL') . $subpath . $rest['path']);
            if($rest['errno']) {
                $result = array(
                    'error' => 1,
                    'message' => $upload->error(),
                );
            }elseif(($size['width'] != 50) && ($size['height'] != 50)) {
            	$result = array(
                    'error' => 1,
                    'message' => '图片尺寸错误,请上传50*50大小尺寸的图片',
                );
            }else{
                $result = array(
                    'error' => 0,
                    'url' => C('UPLOAD_URL') . $subpath . $rest['path'],
                    'filename' => $rest['path'],
                );
            }
        }else{
            $result = array(
                'error' => 1,
                'message' => '内部错误',
            );
        }
        $this->_ajaxDisplay($result);
    }
}
