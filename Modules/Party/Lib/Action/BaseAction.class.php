<?php
class BaseAction extends Action {
	
	function _initialize(){
		//$members = $this->check_login();
		$this->assign("uid",$_SESSION['members']["uid"]);	
		$this->assign("UcUserName",$_SESSION['members']["username"]);
		$this->assign("UcNickName",$_SESSION['members']["nickname"]);
		$this->assign("UcSid",$_SESSION['members']["sid"]);			
		$this->assign("isLogin",$_SESSION['members']["status"]);
		$this->assign("UcUserMid",$_SESSION['members']["mid"]);
		
		//公共城市列表
		$common = A("Common");
		$citylist = $common->com_CityList();
		$this->assign('pe_letter', $citylist['pe_letter']);//省字母
		$this->assign('pe_order', $citylist['pe_order']);//省列表
		$this->assign('cy_letter', $citylist['cy_letter']);//城市列表
		$this->assign('cy_order', $citylist['cy_order']);//城市列表
		$this->assign('hot_city', $citylist['hot_city']);//热点城市
		//get参数
		$this->assign('c', 0);//城市
		$this->assign('t', 0);//日期
		$this->assign('p', 0);//价格
		$this->assign('c_value', '全国');
		$this->assign('t_value', '日期');
		$this->assign('p_value', '价格区间');
	}
	/**
	 * 分页封装 page by kenvinwei
	 */
	public function setPage($modelName,$condition="",$order=""){
		 $modelInfo = D($modelName);	
    	 $listRows = 20;   //每页多少条
         import("ORG.Util.Page");
         $count = $modelInfo->where($condition)->getField("count(*)");
         $p = new Page($count, $listRows, $param);
		 $data = $modelInfo->where($condition)->order($order)->limit($p->firstRow.','.$p->listRows)->findAll();
		// echo $modelInfo->getLastSql();
		 $page = $p->show();
		 return array("data"=>$data,"page"=>$page);
	}
	
	/*
	*判断授权页面
	*	add by kenvinwei
	*/
	function cando(){
		$self = $_SERVER["PHP_SELF"];
		$str = str_replace("/",",",$self);
		$action = explode(",",$str);
		if(isset($action[2])){
			$action["class"]  = $action[2];
			$action["action"]  = $action[3];
		}else{
			$action = false;
		}
		return $action;
	}

	function getHeadInfo(){
	//  评论新
	    $model = D('eswWinePartyComment');
	    $data = $model
		->where('uid='.$_SESSION['members']['uid'].' and is_new = 1')
		->count();
	    $this->assign('comment_count', $data);

	}
	/**
	 * Enter description here...
	 * 获取sns 头像
	 * mid 通行证返回的id
	 */
	function getUcFace($mid){
		return "http://i.wine.cn/User/Api/User.php?c=User&m=avatar&access_token=".$this->_sns_access_token."&token_secret=".$this->_sns_token_secret."&uid=".$mid;
	}
	function getUid($mid){
		$userModel = D("Users");
		$uid = $userModel->where("mid=".$mid)->getField("uid");
		return $uid;
	}
	function getMid($uid){
		$userModel = D("Users");
		$uid = $userModel->where("uid=".$uid)->getField("mid");
		return $uid;
	}
	/*
	public function check_login()
	{    $members = array();
		 if($_SESSION['members']['uid']){
			 $members["status"] = 1;
			 $members["uid"] = $_SESSION['members']['uid'];
			 $members["username"] = $_SESSION['members']['username'];
		 }else{
			 $members["status"] = 0;
		 }
		 return $members;
	}
	*/

}


?>
