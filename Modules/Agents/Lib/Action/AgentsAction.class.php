<?php
/**
 * 代理商管理
 */
class AgentsAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }

    /**
     * 代理商列表
     */
    public function index() {
        if($this->isPost()) $this->listpage_post_to_get();
        $map = array();
        $url = '';
        if($_GET['keyword']) {
            $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        if($_GET['status']) {
            $map['status'] = $_GET['status'];
            $url .= '&status='.$_GET['status'];
        }
        $map['is_del'] = '-1';
        $count = D('Agents')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('Agents')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }
    /**
     * 代理商增加
     */
    public function add() {
        if($this->isPost()){
            $_POST['content'] = Input::safeHtml($_POST['content']);
            if(!$_POST['fname'] || !$_POST['cname'])
                $this->_jumpGo('添加失败，代入信息缺失', 'error');
            $_POST['img'] = $_POST['img_filename'][0] ? $_POST['img_filename'][0] : '';
            D('Agents')->create();
            $id = D('Agents')->add();
            if(!$id)
                $this->_jumpGo('添加失败', 'error');
            D('AgentsLog')->savelog('jiuku_agents', $id, 1);//log
            $img_data = array();
            foreach($_POST['img_filename'] as $key => $val){
                $img_data[] = array(
                    'filename' => $val,
                    'queue' => $key,
                    'description' => $_POST['img_description'][$key],
                    'alt' => $_POST['img_alt'][$key],
                    'agents_id' => $id,
                );
            }
            M('AgentsImg','jiuku_')->addall($img_data);
            $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $this->display();
    }
    /**
     * 代理商编辑
     */
    public function edit() {
        $_REQUEST['id'] = intval($_REQUEST['id']);
        if(!$_REQUEST['id'])
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            if(!$_POST['fname'] || !$_POST['cname'])
                $this->_jumpGo('编辑失败，代入信息错误', 'error');
            $_POST['content'] = Input::safeHtml($_POST['content']);
            $_POST['img'] = $_POST['img_filename'][0] ? $_POST['img_filename'][0] : '';
            D('Agents')->create();
            $is = D('Agents')->save();
            if($is === false)
                $this->_jumpGo('编辑失败', 'error');
            D('AgentsLog')->savelog('jiuku_agents', $_REQUEST['id'], 2);//log
            M('AgentsImg','jiuku_')->where(array('agents_id'=>$_REQUEST['id']))->delete();
            $img_data = array();
            foreach($_POST['img_filename'] as $key => $val){
                $img_data[] = array(
                    'filename' => $val,
                    'queue' => $key,
                    'description' => $_POST['img_description'][$key],
                    'alt' => $_POST['img_alt'][$key],
                    'agents_id' => $_REQUEST['id'],
                );
            }
            M('AgentsImg','jiuku_')->addall($img_data);
            $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
        }
        $res = D('Agents')->where(array('id'=>$_REQUEST['id']))->find();
        if(!$res)
            $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        $res['img_res'] = M('AgentsImg','jiuku_')->where(array('agents_id'=>$_REQUEST['id']))->order('queue asc')->select();
        $this->assign("res", $res);
        $this->display();
    }

    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $id = intval($_GET['id']);
        $is = D('Agents')->save(array('id'=>$id,'status'=>$_GET['status']));
        if($is === false)
            $this->_jumpGo('更改失败', 'error', base64_decode($_REQUEST['_backpage']));
        D('AgentsLog')->savelog('jiuku_agents', $id, 2);//log
        $this->_jumpGo('更改成功', 'succeed', base64_decode($_REQUEST['_backpage']));
    }

    /**
     * 上传文件
     */
    public function upload(){
        import('@.ORG.Util.Image');
        $image = new Image();
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        if($_FILES['imgFile']){
            $subpath = 'Agents/uploads/';
            $cfg = array(
                'ext' => C('UPLOAD_ALLOW_EXT'),
                'size' => C('UPLOAD_MAXSIZE'),
                'path' => C('UPLOAD_PATH') . $subpath,
            );
            $upload->config($cfg);
            $rest = $upload->uploadFile('imgFile');
            if($rest['errno']) {
                $result = array(
                    'error' => 1,
                    'message' => $upload->error(),
                );
            }else{
                $result = array(
                    'error' => 0,
                    'url' => C('UPLOAD_URL') . $subpath . $rest['path'],
                    'filename' => $rest['path'],
                );
            }
        }elseif($_FILES['upd_img']){
            $subpath = 'Agents/images/';
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