<?php

class UserModel extends CommonModel {

    protected $tableName = 'admin_user';

    protected $updateFields = array();

    protected $_validate = array(
        array('username', 'require', '用户名不能为空',1, '', 1),
        array('username', '', '用户名重复', 0, 'unique', 3),
        array('password', 'require', '密码不能为空', 1, '', 1),
        array('password2', 'require', '确认密码不能为空', 1, '', 1),
        array('password', 'password2', '两次密码输入不相同', 0, 'confirm'),
        array('email', 'email', '邮箱格式不正确', 2),
    );

    protected $_auto = array();

    public function _initialize() {
        if(!empty($_POST['password'])) {
            $this->_auto = array(
                array('password', 'md5', 3, 'function')
            );
        } else {
            unset($_POST['password']);
        }
    }

}
