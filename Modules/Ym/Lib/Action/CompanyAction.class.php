<?php
class CompanyAction extends BaseAction {
	function index($type="index"){
		if($type=="index"){
        	//$this->assign("title","编辑个人信息-逸香企业");
        	$this->assign("title",L("title_my_edit"));
        	$this->assign("c_home","nav-spa");
		}else{
			//$this->assign("title","身份认证-逸香企业");
			$this->assign("title",L("title_company"));
        	$this->assign("c_shenfen","nav-spa");
		}

		$qyModel = D("UsersQy");
    	$id = $this->getUid();
    	$qyData = $qyModel->where("id=".$id)->find();
    	$userinfo = array_merge($this->getUserInfo(),$qyData);
    	
    	//行业
    	foreach (L("HANGYE") as $hk=>$hv){
    		if($hk==$userinfo["qy_hangye"]){
    			$select_1 = "selected";
    		}else{
    			$select_1 = "";
    		}
    		$hy_option .="<option $select_1 value=".$hk.">".$hv."</option>"; 
    	}
    	//性质
    	foreach (L("XINGZHI") as $xk=>$xv){
    		if($xk==$userinfo["qy_xingzhi"]){
    			$select_2 = "selected";
    		}else{
    			$select_2 = "";
    		}
    		$xz_option .="<option $select_2 value=".$xk.">".$xv."</option>"; 
    	}
    	//规模
    	foreach (L("GUIMO") as $gk=>$gv){
    		if($gk==$userinfo["qy_guimo"]){
    			$select_3 = "selected";
    		}else{
    			$select_3 = "";
            }
    		$gm_option .="<option $select_3 value=".$gk.">".$gv."</option>"; 
    	}
		$this->assign("hy_option",$hy_option);
		$this->assign("xz_option",$xz_option);
		$this->assign("gm_option",$gm_option);
		$this->assign("userinfo",$userinfo);
		$this->assign("en_country",L("country"));
		$this->assign("type",$type);
		$this->display("index");
	}
	function apply(){
		$this->index("other");
	}
	function update(){
		$qy_name = trim($_POST["qy_name"]);
		$qy_moblie = trim($_POST["qy_moblie"]);
		$qy_hangye = $_POST["qy_hangye"];
		$qy_xingzhi = $_POST["qy_xingzhi"];
		$qy_guimo = $_POST["qy_guimo"];
		$qy_web_address = trim($_POST["qy_web_address"]);
		$qy_introduction = trim($_POST["qy_introduction"]);
		$qy_province = $_POST["qy_province"];
		$qy_city = $_POST["qy_city"];
		$qy_address = trim($_POST["qy_address"]);
		$qy_contacts = trim($_POST["qy_contacts"]);
		$type=$_POST["type"];
		$nick = trim($_POST["nick_name"]);
        $qy_reg_province = $_POST["reg_province"];
        $qy_reg_city = $_POST["reg_city"];
        $qy_reg_address = $_POST["qy_reg_address"];
        $qy_qq = trim($_POST["qq"]);
        $qy_skype = trim($_POST["skype"]);
        $qy_country = trim($_POST["country"]);
		$commonAction = A("Common");
		//验证昵称
    	$nickname_len = $commonAction->getLength($nick);
    	if($nickname_len <2 || $nickname_len>21) { 
	    	$data = json_encode(array("status"=>10004,"message"=>L("nick_length_limit")));
	    	$st = false;
    	}else{
    		if($commonAction->is_number($nick)){
	    		$data = json_encode(array("status"=>10001,"message"=>L("nick_all_number")));
	    		$st = false;
	    	}else{
	    		if(!$commonAction->checknick($nick)){
	    			$data = json_encode(array("status"=>10002,"message"=>L("nick_check")));
	    			$st = false;
	    		}elseif ($this->Rget($this->nick_suffix.$nick) && $this->Rget($this->nick_suffix.$nick)!=$this->getUid()){
	    				$data = json_encode(array("status"=>10003,"message"=>L("nick_exit")));
	    				$st = false;
	    		}else{
	    			$data = json_encode(array("status"=>1,"message"=>L("nick_can_use")));
	    			$st = true;
	    		}
	    	}
    	}
    	if($st == true){
    		$this->update_nick($nick);
    	}else{
    		echo $data;
    		exit;
    	}
		
		$qyModel = D("UsersQy");
		$id = $this->getUid();
		$data =array();
		$data["qy_name"] = $qy_name;
		$data["qy_moblie"] = $qy_moblie;
		$data["qy_hangye"] = $qy_hangye;
		$data["qy_xingzhi"] = $qy_xingzhi;
		$data["qy_guimo"] = $qy_guimo;
		$data["qy_web_address"] = $qy_web_address;
		$data["qy_introduction"] = $qy_introduction;
		$data["qy_province"] = $qy_province;
		$data["qy_city"] = $qy_city;
		$data["qy_address"] = $qy_address;
		$data["qy_reg_province"] = $qy_reg_province;
		$data["qy_reg_city"] = $qy_reg_city;
		$data["qy_reg_address"] = $qy_reg_address;
		$data["qy_qq"] = $qy_qq;
		$data["qy_country"] = $qy_country;
		$data["qy_skype"] = $qy_skype;
		$data["qy_contacts"] = $qy_contacts;
		$qyModel->where("id=".$id)->save($data);
        setcookie("BP",$data['qy_moblie'],time()+3600*24*7,"/",C("COOKIE_DOMAIN"));
        setcookie("CN",$data['qy_name'],time()+3600*24*7,"/",C("COOKIE_DOMAIN"));
		if($type=="下一步"){
            
            $data = array();
            $data["b_type"] = $_SESSION["ym_users"]["bupload_type"];
            $data["comany_valid"] = $_SESSION["ym_users"]["comany_valid"];
            $qyModel = D("UsersQy");
            $qyModel->where("id=".$id)->save($data);
            
            unset($_SESSION["ym_users"]["bupload_type"]);
            unset($_SESSION["ym_users"]["comany_valid"]);
			$jumpUrl = "/index.php/Confirm/bss";
		}else{
			$jumpUrl = "/index.php";
		}
		echo json_encode(array("status"=>1,"message"=>L("edit_success"),"jumpUrl"=>$jumpUrl));
	}
	/**
	 * Enter description here...
	 * 修改昵称 1.数据库修改 2.删除Rdb原来昵称 3.写入Rdb新昵称
	 */
	private function update_nick($nick){
		$userinfo = $this->getUserInfo();
		$old_nick = $userinfo["nick"];
		$this->Rdel($this->nick_suffix.$old_nick);
		$this->Rset($this->nick_suffix.$nick,$this->getUid());
		$userinfoModel = D("UsersInfo");
		$data = array();
		$data["nick"] = $nick;
		$userinfoModel->where("id=".$this->getUid())->save($data);
		return true;
	}
	/**
	 * 检验昵称除去自身
	 *
	 * @param unknown_type $nickname
	 * @return unknown
	 */
	public function check_nickname_out_self($nickname=""){
    	$is_ajax = $_POST["is_ajax"];
    	if($is_ajax==1){
    		$nickname = $_POST["nickname"];
    	}
        $nickname = trim($nickname);
    	$commonAction = A("Common");
    	$nickname_len = $commonAction->getLength($nickname);
    	if($nickname_len <2 || $nickname_len>21) { 
	    	$data = json_encode(array("status"=>10004,"message"=>L("nick_length_limit")));
	    	$st = false;
    	}else{
    		if($commonAction->is_number($nickname)){
	    		$data = json_encode(array("status"=>10001,"message"=>L("nick_all_number")));
	    		$st = false;
	    	}else{
	    		if(!$commonAction->checknick($nickname)){
	    			$data = json_encode(array("status"=>10002,"message"=>L("nick_check")));
	    			$st = false;
	    		}elseif ($this->Rget($this->nick_suffix.$nickname) && $this->Rget($this->nick_suffix.$nickname)!=$this->getUid()){
	    				$data = json_encode(array("status"=>10003,"message"=>L("nick_exit")));
	    				$st = false;
	    		}else{
	    			$data = json_encode(array("status"=>1,"message"=>L("nick_can_use")));
	    			$st = true;
	    		}
	    	}
    	}
    	if($is_ajax==1){
			echo $data;
		}else{
			return $st;
		}
    }
}
?>
