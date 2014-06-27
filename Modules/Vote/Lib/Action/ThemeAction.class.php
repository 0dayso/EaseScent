<?php

class ThemeAction extends CommonAction {

    /**
     * 投票主题列表
     */
    public function index(){
        $_pageurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if($this->isPost()){
            foreach($_POST as $key=>$val){
                if(Input::getVar(intval(trim($val))))   $jump_url .= '&' . $key . '=' . $val;
            }
            header('Location: ' . $jump_url);   exit;
        }
        $_GET['keyword'] = Input::getVar(trim($_GET['keyword']));
        $map = array();
        $url = '';
        if($_GET['keyword']) {
            $map_k['name'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['url'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        $count = D('Theme')->where($map)->count();
        import ( "@.ORG.Util.Page" );
        $p = new Page($count, 15);
        $list = D('Theme')->where($map)->limit($p->firstRow . ',' . $p->listRows)->select();
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_pageurl", base64_encode($_pageurl));
        $this->display();
    }

    /**
     * 添加投票主题
     */
    public function add(){
        if($this->isPost()){
            $title = Input::getVar(trim($_POST['title']));
            $btime = Input::getVar(strtotime($_POST['btime']));
            $etime = Input::getVar(strtotime($_POST['etime']));
            if(!$title || !$btime || !$etime || ($btime==$etime))   $this->_jumpGo('添加失败，代入信息缺失', 'error');
            $description = Input::getVar(trim($_POST['description']));
            $url = Input::getVar(trim($_POST['url']));
            $data = array(
                'title' => $title,
                'description' => $description,
                'url' => $url,
                'btime' => ($btime > $etime) ? $etime : $btime,
                'etime' => ($btime > $etime) ? $btime : $etime,
            );
            if(intval($_POST['theme_caps'])){
                $data['theme_caps'] = Input::getVar(intval($_POST['theme_caps']));
                $data['theme_reset'] = (intval($_POST['theme_reset']) == 1) ? 1:0;
            }
            if(intval($_POST['option_caps'])){
                $data['option_caps'] = Input::getVar(intval($_POST['option_caps']));
                $data['option_reset'] = (intval($_POST['option_reset']) == 1) ? 1:0;
            }
            if(intval($_POST['option_num_caps'])){
                $data['option_num_caps'] = Input::getVar(intval($_POST['option_num_caps']));
                $data['option_num_reset'] = (intval($_POST['option_num_reset']) == 1) ? 1:0;
            }
            $res = D('Theme')->add($data);
            if($res)    $this->_jumpGo('添加成功', 'succeed', base64_decode($_REQUEST['_backpage']));
            $this->_jumpGo('添加失败', 'error');
        }
        $this->display();
    }

    /**
     * 修改投票主题
     */
    public function edit(){
        $id = Input::getVar(intval($_REQUEST['id']));
        if(!$id)    $this->_jumpGo('参数错误', 'error', base64_decode($_REQUEST['_backpage']));
        if($this->isPost()){
            $title = Input::getVar(trim($_POST['title']));
            $btime = Input::getVar(strtotime($_POST['btime']));
            $etime = Input::getVar(strtotime($_POST['etime']));
            if(!$title || !$btime || !$etime || ($btime==$etime))   $this->_jumpGo('编辑失败，代入信息缺失', 'error');
            $description = Input::getVar(trim($_POST['description']));
            $url = Input::getVar(trim($_POST['url']));
            $data = array(
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'url' => $url,
                'btime' => ($btime > $etime) ? $etime : $btime,
                'etime' => ($btime > $etime) ? $btime : $etime,
            );
            $data['theme_caps'] = Input::getVar(intval($_POST['theme_caps']));
            $data['theme_reset'] = (intval($_POST['theme_reset']) == 1 && $data['theme_caps']) ? 1:0;
            $data['option_caps'] = Input::getVar(intval($_POST['option_caps']));
            $data['option_reset'] = (intval($_POST['option_reset']) == 1 && $data['option_caps']) ? 1:0;
            $data['option_num_caps'] = Input::getVar(intval($_POST['option_num_caps']));
            $data['option_num_reset'] = (intval($_POST['option_num_reset']) == 1 && $data['option_num_caps']) ? 1:0;
            $res = D('Theme')->save($data);
            if($res)    $this->_jumpGo('编辑成功', 'succeed', base64_decode($_REQUEST['_backpage']));
            $this->_jumpGo('编辑失败', 'error');
        }
        $res = D('Theme')->where(array('id'=>$id))->find();
        $this->assign('res',$res);
        $this->display();
    }
}