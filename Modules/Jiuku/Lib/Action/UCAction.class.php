<?php

/**
 * 通行证相关方法
 */
class UCAction extends Action {
	/**
	 * 初始化
	 */
	public function _initialize() {
	}
	public $_account_url = 'http://account.quwine.com';
	public $_account_signkey = '905L052b9LzNw8DVGONI';
	public $_account_call = 71;
	/**
	 * 登录
	 */
	public function Login() {
		$username = $_POST["username"];
		$password = $_POST["password"];
		$username = 'chinaguo1986@sina.com';
		$password = 'shenmi123';
		$status = Login($username,$password,$this->_account_signkey,$this->_account_call,$this->_account_url);
		echo json_encode($status);
	}
	public function isLogin(){
		echo json_encode(isLogin());
	}
	
	//public $_sns_url = "http://weibo.quwine.com";
	public $_sns_url = "http://192.168.1.88";
	public $_sns_access_token = "1000000003";
	public $_sns_token_secret = "92A5hymz0AXTykJfhUr1";
	function getCommont(){
		$type = $_POST['type'];
		$id = $_POST['id'];
		$result = snsApi($this->_sns_url,$this->_sns_access_token,$this->_sns_token_secret,'gc2',array($type,$id));
		//$result = snsApi($this->_sns_url,$this->_sns_access_token,$this->_sns_token_secret,'gc1',array('1951558116'));
		echo $result;
	}
}
