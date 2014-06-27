<?php

/**
 * 后台入口类
 */
class IndexAction extends CommonAction {

	/**
	 * 后台管理首页
	 */
    public function index(){
        $this->_leftMenu();
        $this->assign('admin_gname', $_SESSION['admin_gname']);
        $this->assign('admin_username', $_SESSION['admin_username']);
        $this->assign('admin_adminer', $_SESSION['admin_adminer']);
		$this->display();
	}

	/**
	 * 管理首页默认页面
	 */
    public function init() {
		$this->display();
	}

	/**
	 * 修改密码
	 */
    public function changePassword() {
        if($this->isPost()) {
            $model = D('User');
            if(empty($_POST['password'])) {
                $this->_jumpGo('密码不能为空', 'error');
            }
            $this->_update($model);
            $this->_jumpGo('密码修改成功', 'succeed', Url('changePassword'));
        }
        $this->display();
    }

	/**
	 * 查看个人信息
	 */
    public function myInfo() {
        $usermodel = D('User');
        $gid = $_SESSION['admin_gid'];
        $uid = $_SESSION['admin_uid'];
        if($this->isPost()) {
            $data = array(
                'truename' => Input::getVar($_POST['truename']),
                'email' => Input::getVar($_POST['email']),
                'nickname' => Input::getVar($_POST['nickname']),
                'sex' => Input::getVar($_POST['sex']),
                'tel' => Input::getVar($_POST['tel']),
                'uid' => $uid,
            );
            $this->_update($usermodel, $data);
            $this->_jumpGo('个人信息更新成功', 'succeed', Url('myInfo'));
        }

        $acmodel = D('UserGroupAc');
        $acgid = $acmodel->where('gid='.$gid)->select();
        $acgidstr = $doct = '';
        if(!empty($acgid)) {
            foreach($acgid as $acval) {
                $acgidstr .=  $doct . $acval['acid'];
                $doct = ',';
            }
        }
        $aclist = D('Ac')->getAcList();
        $user = $usermodel->where('uid='.$uid)->find();
        $this->assign('user', $user);
        $this->assign('acgid', $acgidstr);
        $this->assign('aclist', $aclist);
        $this->display();
    }
	
	/**
	 * 生成后台操作左侧导航
	 */
	protected function _leftMenu() {
		$apps = $GLOBALS['MODULES_ALLOW'];
		$menu = array();
		foreach($apps as $v) {
			$basepath = CODE_RUNTIME_PATH . DS . 'Modules' . DS . $v . DS;
			//获取左导航数组
			$path = $basepath . 'Conf'. DS . 'adminMenu.php';
			if(is_file($path)) {
				$menu[$v] = include $path;
			}
		}
		//格式化导航
		$newMenu = array();
		foreach($menu as $mv) {
			$newMenu = array_merge($newMenu, $mv);
        }
        //print_r($_SESSION['admin_acurl']);
        //print_r($newMenu);
        //exit();
        $this->assign('acurl', implode(',', $_SESSION['admin_acurl']));
		$this->assign('leftMenu', $newMenu);
	}
}
