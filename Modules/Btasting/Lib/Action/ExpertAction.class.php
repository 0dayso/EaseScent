<?php

/**
 * 品酒会专家团管理Action
 *
 * @author mengfk@eswine.com
 * @since 20130401
 */

class ExpertAction extends BaseAdmin {

    public function _initialize() {
        parent::_initialize();
		import('@.ORG.Util.Input');
		import('@.ORG.Util.Page');
    }
	
    public function index() {
        $keyword = Input::getVar($_REQUEST['keyword']);
        $account = Input::getVar($_REQUEST['account']);
        $page = Input::getVar($_GET[C('VAR_PAGE')]);
        $page = max(intval($page), 1);

        $map = ' 1 ';
        $keyword && $map .= " AND `name` LIKE '%{$keyword}%'";
        $account && $map .= " AND `account` LIKE '%{$account}%'";
        $order = " `id` DESC ";

        $count = M('expert')->where($map)->count('*');
        if($count > 0) {
			$p = new Page($count, 20);
			$voList = M('expert')->where($map)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
			$p->parameter .= Url('Btasting/Expert/index?keyword='.$keyword.'&account='.$account);
            $page = $p->show();
            $this->assign('volist', $voList);
            $this->assign('page', $page);
        }
		$this->display();
	}

	public function add() {
		if($this->isPost()) {
            if(isset($_GET['op']) && $_GET['op'] == 'avatarUpload') {
                $this->_dealExpertAvatar();
            } else {
                $data = $this->_dealExpertUser();
                M('expert')->create($data);
                $id = M('expert')->add();
                $result = array('errno' => 0, 'message' => '添加成功！');
                exit(json_encode($result));
            }
		}
		$this->display();
    }

    public function del() {
        $id = intval($_GET['id']);
        M('expert')->where(array('id' => $id))->delete();
        header("Location:".Url('Btasting/Expert/index'));        
    }

    public function edit() {
        $id = intval($_GET['id']);
		if($this->isPost()) {
            if(isset($_GET['op']) && $_GET['op'] == 'avatarUpload') {
                $this->_dealExpertAvatar();
            } else {
                $data = $this->_dealExpertUser($id);
                $data['id'] = $id;
                M('expert')->create($data);
                M('expert')->save();
                //头像绑定uid
                M('avatar')->where(array('id' => intval($_POST['avatar'])))->save(array('uid' => $id));
                $result = array('errno' => 0, 'message' => '编辑成功！');
                exit(json_encode($result));
            }
        }
        $expert = M('expert')->where(array('id' => $id))->find();
        $this->assign('expert', $expert);
		$this->display();
    }

	public function avatarDel() {
        $avatarid = intval($_GET['avatar']);
        $avatar = M('avatar')->where(array('id' => $avatarid))->find();
        $path = C('AVATAR_PATH'). $avatar['path'];
        if(is_file($path)) {
            @unlink($path);
        }
        return M('avatar')->where(array('id' => $avatarid))->delete();
    }

    protected function _dealExpertAvatar() {
		import('@.ORG.Util.Upload');
        $upload = new Upload;
        $upload->config(array(
							  'ext' => C('AVATAR_EXT'),
                              'size' => C('AVATAR_MSIZE'),
							  'path' => C('AVATAR_PATH'),
        ));
		$rst = $upload->uploadFile('uploadfile');
		if($rst['errno']) {
			$this->show("<script>window.parent.uploadAvatarCallBack(false, '" . $upload->error($rst['errno']) ."')</script>");
			exit();
		}
		$data = array('path' => $rst['path'], 'ext' => $rst['ext'], 'size' => $rst['size']);
		M('avatar')->create($data);
		$avatid = M('avatar')->add();
		$this->show("<script>window.parent.uploadAvatarCallBack(true, ". $avatid .")</script>");
		exit();
    }

    protected function _dealExpertUser($id = 0) {
        header('Content-Type:text/html; charset=utf-8');
        $data = array(
            'name' => Input::getVar($_POST['name']),    
            'account' => Input::getVar($_POST['account']),    
            'avatar' => Input::getVar($_POST['avatar']),    
            'description' => Input::getVar($_POST['description']),    
        );
        if(empty($data['name'])) {
            $result = array('errno' => 1, 'message' => '请填写专家姓名');
            exit(json_encode($result));
        }
        if(empty($data['avatar'])) {
            $result = array('errno' => 1, 'message' => '请上传专家头像');
            exit(json_encode($result));
        }
        if(empty($data['description'])) {
            $result = array('errno' => 1, 'message' => '请填写专家介绍');
            exit(json_encode($result));
        }
        if($this->_checkNameRepeat($id, $data['name'])) {
            $result = array('errno' => 1, 'message' => '同名专家已经存在，请重填');
            exit(json_encode($result));
        }
        //请求通行证
        $param = '{"key":"get_user_by_email_or_mobile","condition":{"logid":"'.$data['account'].'"},"limit":1}';
        $d = base64_encode($param);
        $s = sha1($param.C('ACCOUNT_KEY').date('YmdHi', time()));
        $url = C('ACCOUNT_API').C('ACCOUNT_APID').'?d='.$d.'&s='.$s;
        $accountrst = CurlGet($url);
        $userinfo = json_decode($accountrst, true);
        if($userinfo['status']) {
            $data['mid'] = $userinfo['data']['mid'];
        } else {
            $msg = $userinfo['message'] ? $userinfo['message'] : '个人通行证未知错误';
            $result = array('errno' => 1, 'message' => $msg);
            exit(json_encode($result));
        }
        if($this->_checkMidRepeat($id, $data['mid'])) {
            $result = array('errno' => 1, 'message' => '该通行证ID已经被其他专家绑定，请重填');
            exit(json_encode($result));
        }
        return $data;
    }

    protected function _checkNameRepeat($id, $name) {
        return M('expert')->where(" `id` != '{$id}' AND `name` = '{$name}'")->count('*');
    }

    protected function _checkMidRepeat($id, $mid) {
        return M('expert')->where(" `id` != '{$id}' AND `mid` = '{$mid}'")->count('*');
    }
}
