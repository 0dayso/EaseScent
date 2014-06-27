<?php
/**
* @file RequestApiUserAction.class.php 	#文件名
*
* @brief  请求 i.wine.cn API		#程序文件描述
*
* Copyright(C) 2010-2015 Easescent.com, Inc. or its affiliates. All Rights Reserved. 	#版权信息
*
* @version $Id$ 		#版本号，由svn功能自动生成，不用修改
* @author Goni, goni@sina.com 		#程序作者
* @date 2013-01-30 				#日期
*/

/**
* @class RequestApiUserAction   	# 类名
* @brief 请求 user.wine.cn API
*
*/
class RequestApiUserAction extends Action{

	/**
	* @brief _initialize	#方法名+描述
	*
	* @param $xxxxx {类型}	#方法参数描述，大括号内注明类型
	*
	* @return $xxxxxx{类型}	#返回值
	*/
	public function _initialize() {
		//dump($_SESSION);
		if($_SESSION['members']['online'] != 1){
			unset($_SESSION['members']);
			$_SESSION['members']["online"] = 0;
		}
	}

	/**
	* @brief popupLogin	弹窗登录	加密action=	XntydnEwTXpuanpsa15vdkpsem0wb3Bvam9TcHh2cQ%3D%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function popupLogin(){
		$data = array('key'=>'login','condition'=>array('username'=>$_GET["username"],'password'=>$_GET["password"]),'limit'=>1);
		$membersInfo = $this->getMembersInfo($data);
		if($membersInfo){
			$this->saveMembersInfo($membersInfo);
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$_SESSION['members']));
	}

	/**
	* @brief jumpLoginBefore	跳转登录前置方法	加密action=	ZUBJTUoLdkFVUUFXUGVUTXFXQVYLTlFJVGhLQ01KZkFCS1ZB	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpLoginBefore(){
		setcookie("_login_back_page",trim($_GET['page']), time()+30);
		header("Location:".C('DOMAIN.USER').'member/auth?continue='.C('DOMAIN.AJAX').'?action=YkdOSk0McUZSVkZQV2JTSnZQRlEMSVZOU29MREpNYkVXRlE%3D');
	}

	/**
	* @brief jumpLoginAfter	跳转登录返回方法	加密action=	YkdOSk0McUZSVkZQV2JTSnZQRlEMSVZOU29MREpNYkVXRlE%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpLoginAfter(){
		$data = array('key'=>'getinfo','condition'=>array('sid'=>trim($_GET['sid'])),'limit'=>1);
		$membersInfo = $this->getMembersInfo($data);
		if($membersInfo){
			$this->saveMembersInfo($membersInfo);
		}
		$_login_back_page = (isset($_COOKIE['_login_back_page']) && trim($_COOKIE['_login_back_page']) != '') ? trim($_COOKIE['_login_back_page']) : C('DOMAIN.WWW');
		header("Location:".$_login_back_page);
	}

	/**
	* @brief jumpRegistBefore	注册前置方法	加密action=	ZEFITEsKd0BUUEBWUWRVTHBWQFcKT1BIVXdAQkxWUWdAQ0pXQA%3D%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpRegistBefore(){
		setcookie("_login_back_page",trim($_GET['page']), time()+30);
		header("Location:".C('DOMAIN.USER').'member/signup?continue='.C('DOMAIN.AJAX').'?action=ZUBJTUoLdkFVUUFXUGVUTXFXQVYLTlFJVHZBQ01XUGVCUEFW');
	}

	/**
	* @brief jumpRegistAfter	注册返回方法	加密action=	ZUBJTUoLdkFVUUFXUGVUTXFXQVYLTlFJVHZBQ01XUGVCUEFW	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpRegistAfter(){
		$data = array('key'=>'getinfo','condition'=>array('sid'=>trim($_GET['sid'])),'limit'=>1);
		$membersInfo = $this->getMembersInfo($data);
		if($membersInfo){
			$this->saveMembersInfo($membersInfo);
		}
		$_login_back_page = (isset($_COOKIE['_login_back_page']) && trim($_COOKIE['_login_back_page']) != '') ? trim($_COOKIE['_login_back_page']) : C('DOMAIN.WWW');
		header("Location:".$_login_back_page);
	}

	/**
	* @brief jumpLoginOutBefore	退出登录前置方法	加密action=	ZkNKTkkIdUJWUkJUU2ZXTnJUQlUITVJKV2tIQE5JaFJTZUJBSFVC	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpLoginOutBefore(){
		setcookie("_login_back_page",trim($_GET['page']), time()+30);
		header("Location:".C('DOMAIN.USER').'member/logout?continue='.C('DOMAIN.AJAX').'?action=Z0JLT0gJdENXU0NVUmdWT3NVQ1QJTFNLVmpJQU9IaVNSZ0BSQ1Q%3D');
	}

	/**
	* @brief jumpLoginOutAfter	退出返回方法	加密action=	Z0JLT0gJdENXU0NVUmdWT3NVQ1QJTFNLVmpJQU9IaVNSZ0BSQ1Q%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpLoginOutAfter(){
		$this->unsetMembersInfo();
		$_login_back_page = (isset($_COOKIE['_login_back_page']) && trim($_COOKIE['_login_back_page']) != '') ? trim($_COOKIE['_login_back_page']) : C('DOMAIN.WWW');
		header("Location:".$_login_back_page);
	}

	/**
	* @brief isLogin	判断是否登录	加密action=	XXhxdXIzTnltaXlvaF1sdUlveW4zdW9Qc3t1cg%3D%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	public function isLogin(){
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$_SESSION['members']));
	}

	/**
	* @brief isLogin	获取sid前置方法（前台iframe加载）	加密action=	YEVMSE8Oc0RQVERSVWBRSHRSRFMORkRVckhFY0RHTlNE	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function getSidBefore(){
		header("Location:".C('DOMAIN.USER').'api/auth?continue='.C('DOMAIN.AJAX').'?action=YURNSU4PckVRVUVTVGFQSXVTRVIPR0VUc0lEYUZURVI%3D');
	}

	/**
	* @brief isLogin	获取sid返回方法（前台iframe加载）	加密action=	YURNSU4PckVRVUVTVGFQSXVTRVIPR0VUc0lEYUZURVI%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function getSidAfter(){
		if(empty($_GET['sid'])){
			$this->unsetMembersInfo(); exit();
		}
		$data = array('key'=>'getinfo','condition'=>array('sid'=>trim($_GET['sid'])),'limit'=>1);
		$membersInfo = $this->getMembersInfo($data);
		if($membersInfo === false){
			$this->unsetMembersInfo($membersInfo); exit();
		}
		$this->saveMembersInfo($membersInfo);
	}

	/**
	* @brief getMembersInfo	获取用户信息	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function getMembersInfo($data){
		if(!$data) return false;
		$signArr = array('id'=>58,'key'=>'OkJV9NOTw8NwpD0EE');
		$signId = $signArr['id'];
		$signKey = $signArr['key'];
		$params_d = base64_encode(json_encode($data));
		$params_s = sha1(json_encode($data).$signKey.date('YmdHi',time()));
		$params = array("d"=>$params_d,"s"=>$params_s);
		$url = C('DOMAIN.USER').'api/call/'.$signId.'?d='.$params_d.'&s='.$params_s;
		$lgData = json_decode(CurlPost($url,$params),true);
		if($lgData['status'] != 1){
			return false;
		}
		return $lgData;
	}

	/**
	* @brief saveMembersInfo	存储用户信息到session	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function saveMembersInfo($membersInfo){
		if(empty($membersInfo) || $membersInfo['status'] !== 1){
			unset($_SESSION['members']); exit();
		}
		$_SESSION['members']["online"]		= 1;
		$_SESSION['members']["sid"]			= $membersInfo["data"]["sid"];
		$_SESSION['members']["mid"]			= $membersInfo["data"]["mid"];
		$_SESSION['members']["username"]	= $membersInfo["data"]["username"];
		$_SESSION['members']["nickname"]	= $membersInfo["data"]["member_nick"];
		$_SESSION['members']["uid"]			= $membersInfo['data']['sns_info']['uid'];

	}

	/**
	* @brief unsetMembersInfo	清除session用户信息	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function unsetMembersInfo(){
		unset($_SESSION['members']);
		//$_SESSION['members']["online"] = 0;
	}

	function echo_exit($arr){
		if(empty($_GET['callback'])){
			echo json_encode($arr);
		}else{
			echo $_GET['callback'].'('.json_encode($arr).')';
		}
		exit();
	}
}
