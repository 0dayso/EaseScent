<?php
class UserAction extends BaseAction{
	public $email_moblie_suffix = "#ym_account_#username_";
	public $nick_suffix = "#ym_account_#nickname_";
	public $token_suffix = "#ym_token_#login_";
    public function login(){
        if(!empty($_SERVER["HTTP_REFERER"])&&!strpos($_SERVER["HTTP_REFERER"],"loginout")){
            if(empty($_SESSION["ym_users"]["jump_url"])){
                $_SESSION["ym_users"]["jump_url"] = $_SERVER["HTTP_REFERER"];
            }         
        }
        $this->assign("title",L("title_login"));
        $this->assign("username",$_COOKIE['ym_get_last_user']);
    	$this->autoLogin();
    	$this->cgo();
        $this->display("login");
    }
    public function register(){
        $this->assign("title",L("title_register"));
    	$this->cgo();
    	$this->display("register");
    }
    function service(){
        $this->assign("title","逸香企业通行证用户服务协议");
        $this->display("service");
    }
    function repass(){
        $title = "逸香企业会员中心忘记密码";
        $this->assign("title",$title);
        $this->display("repass");
    }
    /**
     * repassword_do 
     * 重置密码
     * @access public
     * @return void
     */
    function repassword_do(){
        $verify = $_POST["verify"];
        $username = $_POST["username"];
    	if(!$this->check_verify($verify)){
            $this->assign("message",L("verify_code_error"));
            $this->error();
        }
    	$commonAction = A("Common");
    	if(!($commonAction->is_email($username)||$commonAction->is_moblie($username))){
            $this->assign("message",L("user_name_check"));
            $this->error();
        }
        if(!$this->Rget($this->email_moblie_suffix.$username)){
            $this->assign("message","不存在此用户");
            $this->error();
        }
        $_SESSION["ym_users"]["r_username"] = $username;
        /*******************email****************/
        if($commonAction->is_email($username)){
        	$Rstr = md5(time().$commonAction->getRandStr(6));
		    $_SESSION["ym_email_repass_valid_code"] = $Rstr;
		    $vurl = "http://". $_SERVER['HTTP_HOST']."/index.php/User/UcValid/code/".$_SESSION["ym_email_repass_valid_code"];
		    $message = "<div><h3>".L("send_email_hello")."</h3>
            <div>点击一下链接重置密码</div>
            <div>".$vurl."</div>
            </div>";
            $title = "逸香企业会员中心忘记密码";
            $myemail = substr(strchr($username,"@"),1);
            /***email 替换****/
            switch($myemail){
                case "eswine.com":
			        $myemail = "http://ym.163.com";
                break;
                default:
			        $myemail = "http://email.".$myemail;
                break;
            }
			$this->assign("myemail",$myemail);
            $this->assign("username",$username);
		    $commonAction->sendMail($username,$title,$message);
            $this->display("e_send");
        }else{
          /***************手机******************/
            $Rstr = mt_rand(100000,999999);
		    $_SESSION["ym_moblie_repass_valid_code"] = $Rstr;
            $msg = "您好，您刚申请了手机忘记密码，您此次的验证码是:".$Rstr;
            $commonAction->sendsms($username,$msg);
            $this->assign("username",$username);
            $this->display("t_send");
        }
    }
    
    /**
     * UcValid 
     * 设置修改密码访问权限
     * @access public
     * @return void
     */
    function UcValid(){
       $code = $_GET["code"];
       if($code==$_SESSION["ym_email_repass_valid_code"]||$code==$_SESSION["ym_moblie_repass_valid_code"]){
            $this->display("set_pass");
       }else{
            die("验证码已经过期!");
       }
    }
    /**
     * reSetPass 
     * 重置密码
     * @access public
     * @return void
     */
    function reSetPass(){
        $username = $_SESSION["ym_users"]["r_username"]; 
        $uid = $this->Rget($this->email_moblie_suffix.$username);
        $pass = $_POST["pass"];
        if(strlen($pass)<7){
            $this->assign("message","密码格式错误，长度0～6位!");
            $this->success();
        }
        $userinfo = array();
        if($uid){
            $userModel = D("ym_users");
            $reg_time = $userModel->where("id=".$uid)->getField("reg_time");
            $userinfo["password"] == hash_hmac(md5,$pass,$username.$reg_time);
            $userModel->where("id=".$uid)->save($userinfo);
            //清除状态
            unset($_SESSION["ym_email_repass_valid_code"]);
            unset($_SESSION["ym_moblie_repass_valid_code"]);
            $this->assign("message","密码修改成功!");
            $this->success();
        }else{
            $this->assign("message","未授权");
            $this->error();
        }
    }
    public function verify() {  
        import('@.ORG.Util.Image');
        Image::buildImageVerify();  
    }
    public function check_username($username=""){
    	$is_ajax = $_POST["is_ajax"];
    	if($is_ajax==1){
    		$username = $_POST["username"];
    	}
        $username = trim($username);
    	$commonAction = A("Common");
    	if(!($commonAction->is_email($username)||$commonAction->is_moblie($username))){
    		$data = json_encode(array("status"=>10001,"message"=>L("user_name_check")));
    		$st = false;
    	}else{
    		$uid = $this->Rget($this->email_moblie_suffix.$username);
    		if($uid){
    			$data = json_encode(array("status"=>10002,"message"=>L("user_name_exit")));
    			$st = false;
    		}else{
    			$data = json_encode(array("status"=>1,"message"=>L("user_name_can_use")));
    			$st = true;
    		}
    	}
    	if($is_ajax==1){
			echo $data;
		}else{
			return $st;
		}
    }
     /**
	 * Enter description here...
	 * 检测手机1.格式正确，2.存在否
	 */
	public function check_moblie(){
		$commonAction = A("Common");
		$moblie = trim($_POST["moblie"]);
		if(!$commonAction->is_moblie($moblie)){
			 $data = json_encode(array("status"=>10001,"message"=>L("moblie_check_error")));
		}else{
			$uid = $this->Rget($this->email_moblie_suffix.$moblie);
			if($uid){
    			$data = json_encode(array("status"=>10002,"message"=>L("user_name_exit")));
    		}else{
    			$data = json_encode(array("status"=>1,"message"=>L("moblie_can_use")));
    		}
		}
		echo $data;exit;
	}
	/**
	 * 检测邮箱1.格式正确，2.存在否
	 *
	 * @param unknown_type $nickname
	 * @return unknown
	 */
	public function  check_email(){
		$commonAction = A("Common");
		$email = trim($_POST["email"]);
		if(!$commonAction->is_email($email)){
			 $data = json_encode(array("status"=>10001,"message"=>L("email_check_error")));
		}else{
			$uid = $this->Rget($this->email_moblie_suffix.$email);
			if($uid){
    			$data = json_encode(array("status"=>10002,"message"=>L("user_name_exit")));
    		}else{
    			$data = json_encode(array("status"=>1,"message"=>L("email_can_use")));
    		}
		}
		echo $data;exit;
	}
    public function check_nickname($nickname=""){
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
	    		}elseif ($this->Rget($this->nick_suffix.$nickname)){
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
    
   
    public function check_company_tel($tel=""){
    	$is_ajax = $_POST["is_ajax"];
    	if($is_ajax==1){
    		$tel = $_POST["company_tel"];
    	}
        $tel = trim($tel);
    	$commonAction = A("Common");
    	if(!($commonAction->CheckTelephone($tel)||$commonAction->is_moblie($tel))){
    		$data = json_encode(array("status"=>10001,"message"=>L("qy_moblie_error")));
    		$st = false;
    	}else{
    		$data = json_encode(array("status"=>1,"message"=>L("qy_moblie_can_use")));
    		$st = true;
    	}
    	if($is_ajax==1){
			echo $data;
		}else{
			return $st;
		}
    }
    public function check_verify($verify=""){
    	import('@.ORG.Util.Session');
    	$is_ajax = $_POST["is_ajax"];
    	if($is_ajax==1){
    		$verify = $_POST["verify"];
    	}
    	if(md5($verify)!=Session::get('verify')){
				$data = json_encode(array("status"=>10001,"message"=>L("verify_code_error")));
				$st = false;
		}else{
				$data =  json_encode(array("status"=>1,"message"=>L("verify_code_success")));
				$st = true;
		}
		if($is_ajax==1){
			echo $data;
		}else{
			return $st;
		}
    }
    function add(){
    	$username = trim($_POST["username"]);
    	$password = $_POST["password"];
    	//$nickname = trim($_POST["nickname"]);
    	$company_name = trim($_POST["company_name"]);
    	$company_tel = trim($_POST["company_tel"]);
    	/*******************验证数据合法性*************************/
    	$verify = $_POST["verify"];
    	$commonAction = A("Common");
    	if(!$this->check_verify($verify,0)){
    		echo $data = json_encode(array("status"=>10001,"message"=>L("verify_code_error"),"dom"=>"verify"));
    		exit;
    	}
    	if(empty($username)){
    		echo $data = json_encode(array("status"=>10002,"message"=>L("user_name_empty"),"dom"=>"username"));
    		exit;
    	}
    	if(!($commonAction->is_email($username)||$commonAction->is_moblie($username))){
    		echo $data = json_encode(array("status"=>10003,"message"=>L("user_name_check"),"dom"=>"username"));
    		exit;
    	}
    	if($this->Rget($this->email_moblie_suffix.$username)){
    		echo $data = json_encode(array("status"=>100031,"message"=>L("user_name_exit"),"dom"=>"username"));
    		exit;
    	}
    	if(empty($password)&&(strlen($password)<5||strlen($password)>21)){
    		echo $data = json_encode(array("status"=>10004,"message"=>L("pass_length_error"),"dom"=>"username"));
    		exit;
    	}
    	/*if(empty($nickname)&&($commonAction->getLength($nickname)<2||$commonAction->getLength($nickname)>21)){
    		echo $data = json_encode(array("status"=>10005,"message"=>L("nick_length_limit"),"dom"=>"nickname"));
    		exit;
    	}
    	if(!$commonAction->checknick($nickname)){
    		echo $data = json_encode(array("status"=>100051,"message"=>L("nick_check"),"dom"=>"nickname"));
    		exit;
    	}
    	if($this->Rget($this->nick_suffix.$nickname)){
    		echo $data = json_encode(array("status"=>100052,"message"=>L("nick_exit"),"dom"=>"nickname"));
    		exit;
    	}*/
    	if(empty($company_name)){
    		echo $data = json_encode(array("status"=>10006,"message"=>L("company_name_empty"),"dom"=>"company_name"));
    		exit;
    	}
    	if(empty($company_tel)){
    		echo $data = json_encode(array("status"=>10007,"message"=>L("company_moblie_empty"),"dom"=>"company_tel"));
    		exit;
    	}
    	
    	if(!($commonAction->CheckTelephone($company_tel)||$commonAction->is_moblie($company_tel))){
    		echo $data = json_encode(array("status"=>10008,"message"=>L("company_moblie_check"),"dom"=>"company_tel"));
    		exit;
    	}
    	/**********************开始写入数据************************/
    	
   		$status = $this->insertUser($username,$password,$nickname,$company_name,$company_tel);
   		if($status){
   			/*
   			$message = '<div class="email-con-t">
				恭喜您！注册成功！
			</div>
			<div class="email-con-c">
			</div>
			<div class="email-sub">
				<input type="button" value="返 回" class="login-sub">
			</div>';
   			$this->assign("jumpUrl","/index.php");
   			$this->success($message);
   			*/
   			$commonAction = A("Common");
   			if($commonAction->is_moblie($username)){
   				$confirmAction = A("Confirm");
   				$confirmAction->sendMoblieValid($username);
   				echo $data = json_encode(array("status"=>1,"message"=>L("register_success"),"url"=>"/index.php/Confirm/moblie"));
    			exit;
   			}else{
   				$confirmAction = A("Confirm");
   				$confirmAction->sendEmailValid($username);
   				echo $data = json_encode(array("status"=>1,"message"=>L("register_success"),"url"=>"/index.php/Confirm/email"));
    			exit;
   			}
   		}
    }
    function doLogin(){
    	$is_ajax = $_POST["is_ajax"];
    	$username = trim($_POST["l_username"]);
    	$password = $_POST["l_password"];
    	$remb = $_POST["remb"];
    	$verify   = trim($_POST["l_verify"]);
    	import('@.ORG.Util.Session');
        if($_SESSION["ym_users"]["login_error_num"]>3){
    	    if(md5($verify)!=Session::get('verify')){
			    echo json_encode(array("status"=>10001,"message"=>L("verify_code_error"),"error_sum"=>$_SESSION["ym_users"]["login_error_num"]));
                exit;
		    }
        }
        if (empty($username)||empty($password)){
    		echo json_encode(array("status"=>10002,"message"=>L("login_empty")));
    		exit;
    	}else{
    		$uid = $this->loginStats($username,$password);
    		if(!$uid){
               $_SESSION["ym_users"]["login_error_num"] = !isset($_SESSION["ym_users"]["login_error_num"])?1:$_SESSION["ym_users"]["login_error_num"]+1;
    			echo json_encode(array("status"=>10003,"message"=>L("login_error"),"error_sum"=>$_SESSION["ym_users"]["login_error_num"]));
    			exit;
    		}else{
    			if($is_ajax){
    				$this->setToken($remb);
                    $callUrl = "/index.php";
                    //清除错误数量
                    unset($_SESSION["ym_users"]["login_error_num"]);
                    if(!empty($_SESSION["ym_users"]["jump_url"])){
                       $callUrl = $_SESSION["ym_users"]["jump_url"];
                       unset($_SESSION["ym_users"]["jump_url"]);
                    }
    				echo json_encode(array("status"=>1,"message"=>L("login_success"),"jumpUrl"=>$callUrl));
    				exit;
    			}
    		}
    	}
    }
    private function insertUser($username,$password,$nickname,$company_name,$company_tel){
    	$userModel = D("Users");
    	$userinfoModel = D("UsersInfo");
    	$userqyModel = D("UsersQy");
    	$commonAction = A("Common");
    	$userdata = array();
    	$time = time();
    	$userdata["username"] = $username;
    	$userdata["password"] = hash_hmac(md5,$password,$username.$time);
    	$userdata["reg_time"] = $time;
    	$userdata["source"] = 0;
    	$userdata["ip"] = get_client_ip();
    	$uid = $userModel->add($userdata);
    	$userinfodata = array();
    	$userinfodata["id"] = $uid;
    	//$userinfodata["nick"] = $nickname;
    	if($commonAction->is_moblie($username)){
    		$userinfodata["moblie"] = $username;
    	}elseif ($commonAction->is_email($username)){
    		$userinfodata["email"] = $username;
    	}
    	if($userinfoModel->add($userinfodata)){
    		$this->Rset($this->email_moblie_suffix.$username,$uid);
    		//$this->Rset($this->nick_suffix.$nickname,$uid);
    	}
    	$userqydata = array();
    	$userqydata["id"] = $uid;
    	$userqydata["qy_name"] = $company_name;
    	$userqydata["qy_moblie"] = $company_tel;
    	$userqyModel->add($userqydata);
    	
	  	if($this->loginStats($username,$password)){
	    		return true;
	    	}else {
	    		return false;
	    }
    }
    /**
     * Enter description here...
     * 处理登录
     * @param unknown_type $username
     * @param unknown_type $password
     */
    private function loginStats($username,$password){
    	$commonAction = A("Common");
    	$userModel = D("Users");
    	$userinfodata = array();
    	if($commonAction->is_moblie($username)){
    		$userinfodata["moblie"] = $username;
    	}elseif ($commonAction->is_email($username)){
    		$userinfodata["email"] = $username;
    	}
    	//$uid = $userinfoModel->where($userinfodata)->getField("uid");
    	//$uid = $Rdb = $this->Rget($this->email_moblie_suffix.$username);
        $sql = "SELECT `id` FROM `ym_users` WHERE `username` = '".$username."'";
        $uid = D('Users')->where("username = '".$username."'")->getfield('id');

    	if(!$uid){
    		return false;
    	}
    	$userinfo = $userModel->where("id=".$uid)->find();
    	if($userinfo["password"] == hash_hmac(md5,$password,$userinfo["username"].$userinfo["reg_time"])){
            $userQInfo = D('UsersQy')->where("id=" .$uid)->find();
            $userCValid = $userQInfo['comany_valid'] == 2 ? 1 : 0;

    		$_SESSION[C('SESSION_USER_SIGN')] = 1;
    		$_SESSION["ym_users"]["name"] = $username;
    		$_SESSION["ym_users"]["uid"] = $userinfo["id"];
    	    setcookie("ym_get_last_user",$username,time()+3600*24*7,"/",C("COOKIE_DOMAIN"));
    	    setcookie("RU",$username, time()+3600*24*7, "/", C("COOKIE_DOMAIN"));
    	    setcookie("ym_user_uid",$userinfo['id'], time()+3600*24*7, "/", C("COOKIE_DOMAIN"));
    	    setcookie("CN",$userQInfo['qy_name'], time()+3600*24*7, "/", C("COOKIE_DOMAIN"));
    	    setcookie("BP",$userQInfo['qy_moblie'], time()+3600*24*7, "/", C("COOKIE_DOMAIN"));
    	    setcookie("EC",$userCValid, time()+3600*24*7, "/", C("COOKIE_DOMAIN"));
            //header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
            header('P3P: CP="CAO DSP COR CUR ADM DEV TAI PSA PSD IVAi IVDi CONi TELo OTPi OUR DELi SAMi OTRi UNRi PUBi IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE GOV"');
    		return $uid;
    	}
    	return false;
    }
    /**
     * Enter description here...
     * 生成sid
     */
    private  function setToken($remb=0){
    	$id = $this->getUid();
    	$userModel = D("Users");
    	$userinfo = $userModel->where("id=".$id)->find();
    	$atime = time()+C("AUTH_TIME");
    	$token = $this->unionStr();
        if($remb){
            $atime = $atime;
        }else{
            $atime = time()+60*60*12;
        }
    	$this->Rset($this->token_suffix.$userinfo["username"],$token,$atime);
    	header("P3P: CP='NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM'");
    	setcookie("ym_token",$token,$atime,"/",C("COOKIE_DOMAIN"));
    	setcookie("ym_users",$userinfo["username"],$atime,"/",C("COOKIE_DOMAIN"));
    	return true;
    }
    private function unionStr(){
    	$commonAction = A("Common");
    	$token = $commonAction->getRandStr(20);
    	if($this->Rget($this->Rget($this->token_suffix.$token))){
    		$this->unionStr();
    		return false;
    	}
    	return $token;
    }
    /**
     * 
     */
    function autoLogin(){
    	$token = $_COOKIE["ym_token"];
    	if(empty($token)) return false;
    	$username = $_COOKIE["ym_users"];
    	if(empty($username)) return false;
    	$Rtoken = $this->Rget($this->token_suffix.$username);
    	if(empty($Rtoken)) return false;
    	$userModel = D("Users");
    	//验证是否id相同，相同则授权登录
    	if($Rtoken==$token){
    		$userinfo = $userModel->where("username='".$username."'")->find();
    		$_SESSION[C('SESSION_USER_SIGN')] = 1;
    		$_SESSION["ym_users"]["name"] = $userinfo["username"];
    		$_SESSION["ym_users"]["uid"] = $userinfo["id"];
            $callUrl = "/index.php";
                    if(!empty($_COOKIE["ym_jump_url"])){
                       $callUrl = $_COOKIE["ym_jump_url"];
 			           header("Location:".$callUrl);
                       exit;
                    }
    		$this->cgo($callUrl);
    	}
    	return false;
    }
    public function getLoginStatus(){
        echo $_SESSION[C('SESSION_USER_SIGN')];
    }
    /**
     * Enter description here...
     * 处理退出
     */
    public function loginout(){
    	unset($_SESSION[C('SESSION_USER_SIGN')]);
    	unset($_SESSION["ym_users"]);
    	
    	setcookie("ym_token","",time()-3600,"/",C("COOKIE_DOMAIN"));
    	setcookie("ym_users","",time()-3600,"/",C("COOKIE_DOMAIN"));
    	setcookie("ym_user_uid","",time()-3600,"/",C("COOKIE_DOMAIN"));
    	setcookie("RU","",time()-3600,"/",C("COOKIE_DOMAIN"));
    	setcookie("CN","",time()-3600,"/",C("COOKIE_DOMAIN"));
    	setcookie("BP","",time()-3600,"/",C("COOKIE_DOMAIN"));
    	setcookie("EC","",time()-3600,"/",C("COOKIE_DOMAIN"));
    	if(!empty($_SERVER["HTTP_REFERER"])){
             $jumpUrl = $_SERVER["HTTP_REFERER"]; 
        }else{
    	    $jumpUrl = "/index.php/User/login";
        }
    	$this->assign("jumpUrl",$jumpUrl);
    	$this->success(L("loginout_success"));
    }
}
