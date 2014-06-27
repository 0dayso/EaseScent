<?php

/**
 * file:Admin
 * brief:后台管理
 * author:Angf
 * date:2013-5-6
 */
class AdminCommonAction extends Action {

    /**
     * _initialize
     * 后台初始化函数
     * @access public
     * @return void
     */
    public $session  = array();
    public $no_checking_login = array('login','logining','forgot_password');
    public $no_checking_power = array('Admin_logout','Admin_login','Admin_index','Admin_logining');


    function _initialize() {
        $this->session  = session('admin');
        C('TMPL_ACTION_ERROR',TMPL_PATH.'Admin/error.html');
        C('TMPL_ACTION_SUCCESS',TMPL_PATH.'Admin/success.html');
        self::check_admin_user();
    }



    /**
     * checking_admin_user
     * 检查用户身份
     * @access public
     * @return void
     */
    protected function check_admin_user(){
        self::check_user_login();
        self::check_user_power();
    }


    /**
     * checking_admin_user
     * 检查用户是否登陆
     * @access public
     * @return void
     */
    protected function check_user_login(){
        if(!session('admin') && (!in_array(ACTION_NAME,$this->no_checking_login))) $this->redirect("Admin/login");
    }


    /**
     * get_user_power
     * 获取用户权限
     * @access public
     * @return void
     */
    protected function check_user_power(){
        $fields=array();
        if($this->session['uid']){
            $user_fields = M('admin_user_field')->field('power_name')->where("uid=".$this->session['uid'])->select();
            foreach($user_fields as $key => $value){
              $fields[] = $value['power_name'];
            }
            $fields            = array_merge($fields,$this->no_checking_power);
            $module_action     = MODULE_NAME.'_'.ACTION_NAME;
            $module_action_act = $this->_post('action') ? $module_action.'_'.$this->_post('action') : $module_action;
            if(!in_array($module_action,$fields) ){
                if(!in_array($module_action_act,$fields) && !in_array('all',$fields)){
                  $this->error('Sorry！没有对应的操作权限 <br><hr> 请开通对应权限代码 :'.$module_action_act.'',U('Admin/index'));
                }
            }
        }


    }

    /***/
    public function operation_logs($type,$rel_id){
        if($type && $rel_id){
            $admin = $this->session;
            $conditions = array('uid'=>$admin['uid'],'type'=>$type,'rel_id'=>$rel_id);
            return  M('operation_logs')->where($conditions)->order('log_id desc')->select();
        }

    }



   /**
    * _empty
    * 空操作提示
    * @access public
    * @return str
    */
    public function _empty($action){
        $this->error(MODULE_NAME.' 模块下【'.$action.'】动作不存在');
    }



}