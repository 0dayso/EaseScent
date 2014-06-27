<?php
/**
 * 通行证登录
 *author kenvinwei
 * add by 2011-11-22
 */
class UcAction extends BaseAction{
	/*
	*获取通行证数据
	*data条件参数 type方式 1为ajax 2为php
	*/
	function getUcData($data,$type,$mode=0,$goto=""){
		
		$signKey = C("signKey");
		$uc_api_url =C("TMPL_PARSE_STRING.__ACCOUNT__");
		$dataBase64 = base64_encode(json_encode($data));

		$sha1 = sha1(json_encode($data).$signKey.date('YmdHi',time()));
		$md5Sign = md5(md5($username.'-'.'JSON').'-'.$signKey.'-'.date('YmdHi'));
	/*	echo C("TMPL_PARSE_STRING.__ACCOUNT_CALL_URL__")."?d=".$dataBase64."&s=".$sha1;
		echo date("Y-m-d H:i:s");exit;*/
        $lgDataJson = file_get_contents(C("TMPL_PARSE_STRING.__ACCOUNT_CALL_URL__")."?d=".$dataBase64."&s=".$sha1);
		$lgData = json_decode($lgDataJson, true);
		if($lgData['status'] == 1){
			$uid = $lgData['data']['mid'];
			$username = $lgData['data']['username'];
			$user_data["mid"] = $uid;
			$userModel = D("users");
			$userinfo = $userModel->where($user_data)->getField("uid");
			$user_data["user_name"] = $username ;
			$user_data["email"] = $lgData['data']['email'];
			$user_data["nick_name"] = $lgData['data']['member_nick'];
			$user_data["sex"] = $lgData['data']['member_sex'];
			$user_data["current_login_ip"] = $lgData['data']['current_login_ip'];
			if(empty($userinfo)){
				$uid = $userModel->add($user_data);
			}else{
				$userModel->save($user_data);
				$uid = $userinfo;
			}
			$_SESSION["members"]["uid"] = $uid;
			$_SESSION["members"]["mid"] = $user_data["mid"];
			$_SESSION["members"]["username"] = $username;
			$_SESSION["members"]["nickname"] = $lgData["data"]["member_nick"];
			$_SESSION["members"]["email_valid"] = $lgData["data"]["email_valid"];
			$_SESSION["members"]["moblie_valid"] = $lgData["data"]["moblie_valid"];
			if(empty($_SESSION["members"]["sid"])){
				$_SESSION["members"]["sid"] = $lgData["data"]['sid'];
			}
			$_SESSION["members"]["online"] = 1;
			if($type==1){
				return json_encode($_SESSION["members"]);
			}elseif ($type==2){
				if($mode!=1){
					if($goto){
						header("Location:$goto");
					}else{
						header("Location:/index.php");
					}
				}
			}
		}else{
				$_SESSION["members"]["online"] = 0;
				return json_encode($_SESSION["members"]);
		}
	}
	function Login(){
		//$username = '123456@123.com';
	    //$password = '123456';
		$username = $_POST["username"];
		$password = $_POST["password"];
		$data = array(
			'key'=>'login',
			'condition'=>array(
								'username'=>$username,
								'password'=>$password
			),
			'limit'=>1
		);

		echo $this->getUcData($data,1);
	}
	
	function checklogin(){
		
		if(isset($_SESSION["members"]["uid"])&&!empty($_SESSION["members"]["username"])){
			$_SESSION["members"]["online"] = 1;
			echo json_encode($_SESSION["members"]);
		}else{
			$_SESSION["members"]["online"] = 0;
			echo json_encode($_SESSION["members"]);
		}
		
	}
	function register(){
		$sid = $_GET["sid"];
		$goto = $_GET["goto"];
		$mode = empty($_GET["mode"])?0:$_GET["mode"];
		$data = array(
			'key'=>'getinfo',
			'condition'=>array(
								'sid'=>$sid,
							),
			'limit'=>1
		);
		$_SESSION["members"]["sid"] = $sid;
		if(!empty($goto)){
			$goto = $this->codeurl($goto);
		}
		$this->getUcData($data,2,$mode,$goto);
	}
	function is_register(){
		if(!empty($_GET["sid"])){
			header("Location:/index.php/Uc/register/sid/".$_GET["sid"]."/mode/1");
		}
	}
	function loginout(){
		unset($_SESSION["members"]);
	}
	function codeurl($str){
		if(strpos($str,"-")===false){
				return 0;
		}else{
			return "http://".str_replace("-","/",$str);
		}
	}
	/*
	*获取登录用户信息
	*/
	function getUcUserInfo($sid){
		
		$signKey = C("signKey");
		$uc_api_url =C("TMPL_PARSE_STRING.__ACCOUNT__");
		$data = array(
			'key'=>'getinfo',
			'condition'=>array(
								'sid'=>$sid,
							),
			'limit'=>1
		);
		$dataBase64 = base64_encode(json_encode($data));

		$sha1 = sha1(json_encode($data).$signKey.date('YmdHi',time()));
		$md5Sign = md5(md5($username.'-'.'JSON').'-'.$signKey.'-'.date('YmdHi'));
		//echo C("TMPL_PARSE_STRING.__ACCOUNT_CALL_URL__")."?d=".$dataBase64."&s=".$sha1;
		/*echo date("Y-m-d H:i:s");exit;*/
        $lgDataJson = file_get_contents(C("TMPL_PARSE_STRING.__ACCOUNT_CALL_URL__")."?d=".$dataBase64."&s=".$sha1);
		$lgData = json_decode($lgDataJson, true);
		if($lgData['status'] == 1){
			$userModel = D("Users");
			$lgData['data']["mid"] = $userModel->where("mid=".$lgData['data']['mid'])->getField("uid");
			return $lgData; 	
		}else{
			return false;
		}
	}
	//用户昵称表
	function update_nickname(){
		$uid = $_GET["uid"];
		$nickname = $_GET["nickname"];
		$key = $_GET["key"];
		$temkey  = md5($uid.$nickname.'ddddsdfjedjdj222*&^');
		if($key==$temkey) {
			$party = M();
			if($uid and $nickname) {
				$ret = $party->query("select uid from es_users where uid=$uid");
				if($ret) {
					$party->query("update es_users set nick_name='{$nickname}' where uid=$uid");
			}
			
			}
		}else {
			die;
		}
		
	}
}



?>
