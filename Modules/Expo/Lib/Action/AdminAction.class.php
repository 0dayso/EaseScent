<?php

/**
 * file:Admin
 * brief:后台管理
 * author:Angf
 * date:2013-5-6
 */
class AdminAction extends AdminCommonAction {



    /**
     * index 默认模块
     */
    public function index(){
        $this->display();
    }


    /**
     * 后台登陆
     */
    public function login(){
        $this->display();
    }


    /**
     * 后台推出
     */
    public function logout(){
        session('admin',null);
        $this->redirect("Admin/login");
    }


   /**
    * 后台登陆 logining
    */
    public function  logining(){
        header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
        $conditions['username'] = $this->_post('username');
        $conditions['password'] = md5($this->_post('password').md5($this->_post('password')));
        $userinfo = M('admin_user')->where($conditions)->find();
        if(!empty($userinfo)){
            session('admin',$userinfo);
            $this->redirect('Admin/index');
        }
        $this->assign('login_error', 1);
        $this->display('login');
    }

    /**
     * 忘记密码
     */
    public function forgot_password(){
        echo "todo...";
    }

}
?>