<?php
/**
* @file RequestUserApiAction.class.php 	#文件名                                                                                                                                             
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
* @class RequestUserApiAction   	# 类名           
* @brief 请求 user.wine.cn API
* 		
*/
class RequestUserApiAction extends Action{
	
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
	* @brief popupLogin	弹窗登录	加密action=	VXZqdGowTXpuanpsa0psem1eb3Ywb3Bvam9TcHh2cQ%3D%3D	#方法名+描述
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
	* @brief jumpLoginBefore	跳转登录前置方法	加密action=	bk1RT1ELdkFVUUFXUHFXQVZlVE0LTlFJVGhLQ01KZkFCS1ZB	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpLoginBefore(){
		$_SESSION['backpage'] = (isset($_GET['page']) && trim($_GET['page']) != '') ? trim($_GET['page']) : C('DOMAIN.WWW');
		header("Location:".C('DOMAIN.USER').'member/auth?continue='.C('DOMAIN.AJAX').'?action=aUpWSFYMcUZSVkZQV3ZQRlFiU0oMSVZOU29MREpNYkVXRlE%3D');
	}
	
	/**
	* @brief jumpLoginAfter	跳转登录返回方法	加密action=	aUpWSFYMcUZSVkZQV3ZQRlFiU0oMSVZOU29MREpNYkVXRlE%3D	#方法名+描述
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
		$backpage = $_SESSION['backpage'];
		unset($_SESSION['backpage']);
		header("Location:".$backpage);
	}
	
	/**
	* @brief jumpRegistBefore	注册前置方法	加密action=	b0xQTlAKd0BUUEBWUXBWQFdkVUwKT1BIVXdAQkxWUWdAQ0pXQA%3D%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpRegistBefore(){
		$_SESSION['backpage'] = (isset($_GET['page']) && trim($_GET['page']) != '') ? trim($_GET['page']) : C('DOMAIN.WWW');
		header("Location:".C('DOMAIN.USER').'member/signup?continue='.C('DOMAIN.AJAX').'?action=bk1RT1ELdkFVUUFXUHFXQVZlVE0LTlFJVHZBQ01XUGVCUEFW');
	}
	
	/**
	* @brief jumpRegistAfter	注册返回方法	加密action=	bk1RT1ELdkFVUUFXUHFXQVZlVE0LTlFJVHZBQ01XUGVCUEFW	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpRegistAfter(){
		//header("Location:".C('DOMAIN.AJAX').'?action=bk1RT1ELdkFVUUFXUHFXQVZlVE0LTlFJVGhLQ01KZkFCS1ZB&page='.$_SESSION['backpage']);
		$data = array('key'=>'getinfo','condition'=>array('sid'=>trim($_GET['sid'])),'limit'=>1);
		$membersInfo = $this->getMembersInfo($data);
		if($membersInfo){
			$this->saveMembersInfo($membersInfo);
		}
		$backpage = $_SESSION['backpage'];
		unset($_SESSION['backpage']);
		header("Location:".$backpage);
	}
	
	/**
	* @brief jumpLoginOutBefore	退出登录前置方法	加密action=	bU5STFIIdUJWUkJUU3JUQlVmV04ITVJKV2tIQE5JaFJTZUJBSFVC	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpLoginOutBefore(){
		$_SESSION['backpage'] = (isset($_SERVER['HTTP_REFERER']) && trim($_SERVER['HTTP_REFERER']) != '') ? $_SERVER['HTTP_REFERER'] : C('DOMAIN.WWW');
		header("Location:".C('DOMAIN.USER').'member/logout?continue='.C('DOMAIN.AJAX').'?action=bE9TTVMJdENXU0NVUnNVQ1RnVk8JTFNLVmpJQU9IaVNSZ0BSQ1Q%3D');
	}
	
	/**
	* @brief jumpLoginOutAfter	退出返回方法	加密action=	bE9TTVMJdENXU0NVUnNVQ1RnVk8JTFNLVmpJQU9IaVNSZ0BSQ1Q%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function jumpLoginOutAfter(){
		$this->unsetMembersInfo();
		$backpage = $_SESSION['backpage'];
		unset($_SESSION['backpage']);
		header("Location:".$backpage);
	}
	
	/**                                                                                                                                                        
	* @brief isLogin	判断是否登录	加密action=	VnVpd2kzTnltaXlvaElveW5dbHUzdW9Qc3t1cg%3D%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	public function isLogin(){
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$_SESSION['members']));
	}
	
	/**                                                                                                                                                        
	* @brief isLogin	获取sid前置方法（前台iframe加载）	加密action=	a0hUSlQOc0RQVERSVXRSRFNgUUgORkRVckhFY0RHTlNE	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return 	#返回值
	*/
	public function getSidBefore(){
		header("Location:".C('DOMAIN.USER').'api/auth?continue='.C('DOMAIN.AJAX').'?action=aklVS1UPckVRVUVTVHVTRVJhUEkPR0VUc0lEYUZURVI%3D');
	}
	
	/**                                                                                                                                                        
	* @brief isLogin	获取sid返回方法（前台iframe加载）	加密action=	aklVS1UPckVRVUVTVHVTRVJhUEkPR0VUc0lEYUZURVI%3D	#方法名+描述
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
		$signArr = C('USER_PARAME.MINGZHUANG');
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
