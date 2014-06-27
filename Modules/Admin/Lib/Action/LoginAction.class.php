<?php

class LoginAction extends Action {
	
	/**
	 * 判断是否登录
	 */
	public function _initialize() {
		if(isset($_SESSION['admin_uid'])) {
			header('Location:'.Url('Admin/Index/index'));
		}
	}
    
    /**
     * 登录
     */
    public function index() {
        if($this->isPost()) {
            import('@.ORG.Util.Input');
            $data = array(
                'error' => false,
                'message' => '',
            );
            $username = Input::getVar($_POST['username']);
            $password = Input::getVar($_POST['password']);
            if(empty($username)) {
                $data['message'] = '用户名为空';
            } elseif(empty($password)) {
                $data['message'] = '密码不能为空';
            } else{
                $model = D('User');
                $rst = $model->where(array('username' => $username,'password' => md5($password)))->find();
                if(empty($rst)) {
                    $data['message'] = '用户名或密码错误';
                } elseif($rst['status'] != 1 && !$rst['adminer']) {
                    $data['message'] = '该用户已被锁定，禁止登录';
                } else {
                    //登录成功，写入SESSION信息
                    $_SESSION['admin_uid'] = $rst['uid'];
                    $_SESSION['admin_username'] = $rst['username'];
                    $_SESSION['admin_gid'] = $rst['gid'];
                    $_SESSION['admin_adminer'] = $rst['adminer'];
                    $gname = D('UserGroup')->where('gid='.$rst['gid'])->find();
                    $_SESSION['admin_gname'] = $gname['name'];
                    //允许的操作写入SESSION
                    //如果adminer = 1 允许进行所有操作
                    if($rst['adminer']) {
                        $acurl = D('Ac')->select();
                    } else {
                        $ac = D('UserGroupAc')->getAcurl($rst['gid']);
                        $acurl = D('Ac')->where('acid IN ('.implode(',', $ac).')')->select();
                    }
                    $nacurl = array();
                    foreach($acurl as $k => $v) {
                        $nacurl[] = $v['url'];
                    }
                    $_SESSION['admin_acurl'] = $nacurl;

                    //写入资讯频道SESSION
                    $newsAuth = D('NewsAc')->getAuth2($rst['gid']);
                    $_SESSION['news_auth'] = $newsAuth;
                }
            }
            if(!empty($data['message'])) {
                $data['error'] = true;
            }
            self::_rest($data);
        }
        $this->display('login');
    }

    public function logout() {
        //清除SESSION,跳转到登录页
        unset($_SESSION['admin_uid']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_gid']);
        header('Location:'.Url('Admin/Login/index'));
    }

    private static function _rest($data) {
        header('Content-Type:text/html; charset=utf-8');
        exit(json_encode($data));
    }
}
