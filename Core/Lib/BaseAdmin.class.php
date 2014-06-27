<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 所有后台操作的Action都必须继承此类
 * 此类要设置成自动加载才能被继承
 * 主要用于后台登录用户操作验证
 *
 * @category   Wine
 * @package    Core
 * @author     mengfk<dreamans@163.com>
 */
class BaseAdmin extends Action {

    /**
     * 当前登录的UID
     * 用来判断是否已经登录
     */
    public $uid;

	public function _initialize() {
		$this->_checkAdmin();
	}

	/**
	 * 进行权限验证
	 */
    protected function _checkAdmin() {
        //验证是否登录
        $this->uid = isset($_SESSION['admin_uid']) ? $_SESSION['admin_uid']: 0;
        if(!$this->uid) {
            $this->_go('尚未登录，跳转到登录页面', Url('Admin/Login/index'));
        }
        //验证是否有权限
        $acurl = $_SESSION['admin_acurl'];
        $adminer = $_SESSION['admin_adminer'];
        $url = APP_NAME.'/'.MODULE_NAME.'/'.ACTION_NAME;
        if($acurl && (in_array($url, $acurl) || $adminer)) {
            $this->assign('admin_gname', $_SESSION['admin_gname']);
            $this->assign('admin_username', $_SESSION['admin_username']);
            $this->assign('admin_uid', $_SESSION['admin_uid']);
            $this->assign('admin_adminer', $_SESSION['admin_adminer']);
        } else {
            $this->display('','','','权限限制，不允许的操作[<a href="{:Url("Admin/Login/index")}">重新登录</a>]');
            exit();
        }
    }

    /**
     * 跳转
     */
    private function _go($message, $url = 'javascript:history.go(-1)') {
        $html = '<html><head><meta http-equiv="refresh" contect="1;URL='.$url.'"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>提示信息</title><script>var code = \'location.href="'.$url.'";\'; setTimeout(code, 1000);</script></head><body style="font-size:14px;">'.$message.'... [<a href="'.$url.'">手动跳转</a>]</body></html>';
        $this->display('','','',$html);
        exit();
    }

}
