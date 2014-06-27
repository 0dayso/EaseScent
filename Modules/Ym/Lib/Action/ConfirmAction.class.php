<?php
/**
 * Enter description here...
 * 个人信息认证 
 */
class ConfirmAction extends BaseAction {
	public $validtime = 120;
	public $email_moblie_suffix = "#ym_account_#username_";
    public $nick_suffix = "#ym_account_#nickname_";
    public $valid_email_suffix = "#ym_valid_#email_#id_";
	/**
	 * 邮箱认证
	 */
	function email($type="register"){
		if(!$this->is_valid(1)){
            $this->assign("title",L("title_email_valid"));
            $this->assign("c_email","nav-spa");
			$userinfo = $this->getUserInfo();
            if($_SESSION["ym_users"]["update_email"]){
                $userinfo["email"] = $_SESSION["ym_users"]["update_email"];
            }
			$myemail = substr(strchr($userinfo["email"],"@"),1);
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
			$this->assign("userinfo",$userinfo);
            if($_GET["isAjax"]) $type=$_GET["isAjax"];
            if(!empty($_GET["do"])){
                $this->assign("do",$_GET["do"]);
            }
			$this->assign("type",$type);
			$this->display("email");
		}else{
			$this->vafter(1);
		}
	}
		/**
	 * 手机认证
	 */
	function moblie($type="register"){
		if(!$this->is_valid(2)){
            $this->assign("title",L("title_moblie_valid"));
            $this->assign("c_moblie","nav-spa");
			$userinfo = $this->getUserInfo();
			$_SESSION["ym_moblie_valid_send_time"]  = !isset($_SESSION["ym_moblie_valid_send_time"])?time():$_SESSION["ym_moblie_valid_send_time"];
			$timeout = ($_SESSION["ym_moblie_valid_send_time"]+$this->validtime)-time();
			$timeout = $timeout>0?$timeout:"";
			$this->assign("timeout",$timeout);
			$this->assign("userinfo",$userinfo);
            if($_GET["isAjax"]) $type=$_GET["isAjax"];
			$this->assign("type",$type);
			$this->display("moblie");
		}else{
			$this->vafter(2);
		}
	}
	function add_email(){
		$this->assign("title",L("title_add_email"));
        $this->assign("c_email","nav-spa");
		$this->display("add_email");
	}
	function add_moblie(){
		$this->assign("title",L("title_add_moblie"));
        $this->assign("c_moblie","nav-spa");
		$this->display("add_moblie");
	}
	/**
	 * Enter description here...
	 * 企业营业执照
	 */
	function bphoto(){
        $this->assign("title",L("title_b_upload"));
        $this->assign("c_shenfen","nav-spa");
		$this->display("bphoto");
	}
	/**
	 * Enter description here...
	 * 上传营业执照
	 */
	function bupload(){
        header("Content-Type:text/html; charset=utf-8");
		//完成与thinkphp相关的，文件上传类的调用
		$uid = $this->getUid();   
        import('@.ORG.Net.UploadFile');//将上传类UploadFile.class.php拷到Lib/Org文件夹下
        $upload=new UploadFile();
        $upload->maxSize='1000000';//默认为-1，不限制上传大小
        if(!is_dir(dirname($_SERVER['DOCUMENT_ROOT']) ."/Upload/Ym/bfiles/b_".$uid."/")){
        	mkdir(dirname($_SERVER['DOCUMENT_ROOT']) ."/Upload/Ym/bfiles/b_".$uid."/",0777,true);
        }
        $upload->savePath=dirname($_SERVER['DOCUMENT_ROOT']) ."/Upload/Ym/bfiles/b_".$uid."/";//保存路径建议与主文件平级目录或者平级目录的子目录来保存   
       // $upload->saveRule=uniqid;//上传文件的文件名保存规则
        $upload->saveRule="pic";//上传文件的文件名保存规则
        $upload->uploadReplace=true;//如果存在同名文件是否进行覆盖
        $upload->allowExts=array('jpg','jpeg','png','gif');//准许上传的文件类型
        //$upload->allowTypes=array('image/png','image/jpg','image/jpeg','image/gif');//检测mime类型
        $upload->thumb=true;//是否开启图片文件缩略图
        $upload->thumbMaxWidth='500';
        $upload->thumbMaxHeight='400';
        $upload->thumbPrefix='m_';//缩略图文件前缀
        $upload->thumbRemoveOrigin=0;//如果生成缩略图，是否删除原图
       
        if($upload->upload()){
            $info=$upload->getUploadFileInfo();
            //上传图片成功，修改成未认证，保存图片后缀名称
            $fileParts = pathinfo($_FILES['upfile']['name']);
            /*
            $data = array();
            $data["b_type"] = $fileParts['extension'];
            $data["comany_valid"] = 1;
            $qyModel = D("UsersQy");
            $qyModel->where("id=".$uid)->save($data);
            */
            $_SESSION["ym_users"]["bupload_type"] = $fileParts['extension'];
            $_SESSION["ym_users"]["comany_valid"] = 1;
            $moban = C("TMPL_PARSE_STRING");
            echo "<script type='text/javascript'>
                    alert('".L("upload_success")."');
            parent.document.getElementById('show_upload').src='".$moban["__UPLOAD__"]."/bfiles/b_".$uid."/m_pic.".$fileParts['extension']."?id=".mt_rand(10000,99999)."';parent.document.getElementById('show_upload').style.display='block';parent.document.getElementById('myfile_upload_btn').disabled=false;
            parent.document.getElementById('myfile_upload_btn').className='login-sub';
                </script>";

        }else{
            $error = $upload->getErrorMsg();//专门用来获取上传的错误信息的 
             echo "<script type='text/javascript'>alert('".$error."')</script>";  
        }   
	}
	/**
	 * Enter description here...
	 * 菜单 验证入口 
	 */
	function emailing(){
        $this->assign("title",L("title_email_confirmation"));
        $this->assign("c_email","nav-spa");
		$userinfo = $this->getUserInfo();
		if(empty($userinfo["email"])){
			$this->redirect("/index.php/Confirm/add_email");
		}else{
			$this->assign("userinfo",$userinfo);
			$this->display("emailing");
		}
	}
	function bss(){
        $this->assign("title",L("title_company"));
        $this->assign("c_shenfen","nav-spa");
		$qyModel = D("UsersQy");
		$userinfo = $qyModel->where("id=".$this->getUid())->find();
		$this->assign("userinfo",$userinfo);
		$this->assign("message_1",L("upload_wait"));
		$this->assign("message_2",L("upload_qy_name").$userinfo["qy_name"]);
		$this->display("bss");
	}
	function sssing(){
        $this->assign("title",L("title_company"));
        $this->assign("c_shenfen","nav-spa");
		$qyModel = D("UsersQy");
		$userinfo = $qyModel->where("id=".$this->getUid())->find();
        $qyModel = D("UsersQy");
	    $id = $this->getUid();
	    $qyData = $qyModel->where("id=".$id)->find();
	    $userinfo = array_merge($this->getUserInfo(),$qyData);
	    	//加载语言包
	    	if (C("SET_LANG")!=""){
	    		 include APP_PATH."Lang/".C("SET_LANG").".php";
	    	}
	    $userinfo["qy_hangye"] = $Lang["HANGYE"][$userinfo["qy_hangye"]];
		$this->assign("userinfo",$userinfo);
		if($userinfo["comany_valid"]==0){
			$this->redirect("/index.php/Confirm/bphoto");
		}elseif ($userinfo["comany_valid"]==1){
			//$this->success(L("upload_wait"));
            $this->assign("message",L("upload_wait"));
            $this->display("sssing");
		}elseif ($userinfo["comany_valid"]==2){
            $this->assign("message",L("renzheng_success"));
			$this->display("sssing");
		}elseif ($userinfo["comany_valid"]==3){
			$this->assign("message",L("renzheng_error"));
			//$this->error(L("renzheng_error"));
			$this->display("sssing");
		}
	}
	/**
	 * Enter description here...
	 *  手机验证入口
	 */
	function mobliing(){
        $this->assign("title",L("title_moblie_confirmation"));
        $this->assign("c_moblie","nav-spa");
		$userinfo = $this->getUserInfo();
		if(empty($userinfo["moblie"])){
			$this->redirect("/index.php/Confirm/add_moblie");
		}else{
			$this->assign("userinfo",$userinfo);
			$this->display("mobliing");
		}
	}
	function mobliing_next(){
        $this->assign("title",L("title_moblie_confirmation"));
        $this->assign("c_moblie","nav-spa");
		$userinfo = $this->getUserInfo();
		$this->sendMoblieValid($userinfo["moblie"]);
		$this->moblie("yanzheng");
		
	}
	function emailing_next(){
        $this->assign("title",L("title_moblie_confirmation"));
        $this->assign("c_email","nav-spa");
		$userinfo = $this->getUserInfo();
		$this->sendEmailValid($userinfo["email"]);
		$this->email("yanzheng");
	}
	public function vafter($type){
		if($type==1){
			$message = '<div class="email-con-t">
				'.L("email_verify").'
				</div>
				<div class="email-con-c">
				</div>
				<div class="email-sub">
					<input type="button" onclick="window.location.href=\'/index.php\';" value="返 回" class="login-sub">
			</div>';
   			$this->assign("jumpUrl","/index.php");
   			$this->assign("message",$message);
   			$this->error($message);
		}else{
			$message = '<div class="email-con-t">
				你的手机已经验证！
				</div>
				<div class="email-con-c">
				</div>
				<div class="email-sub">
					<input type="button" value="返 回" class="login-sub">
			</div>';
   			$this->assign("jumpUrl","/index.php");
   			$this->assign("message",$message);
   			$this->error($message);
		}
	}
	/**
	 * 是否验证
	 * 1 邮箱 2手机
	 */
	public function is_valid($type=1){
		$userinfoModel = D("UsersInfo");
		$uid = $this->getUid();
		$data = array();
		$data["id"] = $uid;
		if($type==1){
			$data["email_valid"] = 1;
		}else{
			$data["moblie_valid"] = 1;
		}
		return $userinfoModel->where($data)->find();
	}
	public function sendEmailValid($username){
		if($this->is_valid(1)){
			$this->vafter(1);
		}
		$commonAction = A("Common");
		$uid = $this->getUid();
        $userinfo = $this->getUserInfo();
		$Rstr = md5(time().$commonAction->getRandStr(6));
		//$_SESSION["ym_email_valid_code"] = $Rstr;
        $this->Rset($this->valid_email_suffix.$uid,$Rstr);
		$vurl = "http://". $_SERVER['HTTP_HOST']."/index.php/Confirm/emailValid/code/".$Rstr;
		$message = "<div><h3>".L("send_email_hello").":".$userinfo["nick"]."</h3>
            <div>".L("send_email_msg1")."</div>
            <div><a href='".$vurl."' target='_blank'>".$vurl."</a></div>
            <div>".L("send_email_msg2")."</div>
            <div>".L("send_email_msg3")."</div>
            <div>".L("send_email_from")."</div>
            </div>";
        $title = L("send_email_title");
		$commonAction->sendMail($username,$title,$message);
	}
	public function emailValid(){
		if($this->is_valid(1)){
			$this->vafter(1);
		}
		$code = $_GET["code"];
		$qyModel = D("UsersQy");
    	$id = $this->getUid();
    	$qyData = $qyModel->where("id=".$id)->find();
    	$userinfo = array_merge($this->getUserInfo(),$qyData);
    	//echo $_SESSION["ym_email_valid_code"];
        $validCode = $this->Rget($this->valid_email_suffix.$id);
		if($validCode == $code){
				$this->cvalid(1);
				$message = '<div class="email-con-t">
				'.L("email_verify_success").'
				</div>
				<div class="email-con-c">
					<p>邮箱账号：'.$userinfo["email"].'</p>
					<p>企业全称：'.$userinfo["qy_name"].'</p>
					<p>企业电话：'.$userinfo["qy_moblie"].'</p>
				</div>
				<div class="email-sub">
					<input type="button" value="返 回" class="login-sub">
			</div>';
            //删除授权
            $this->Rdel($this->valid_email_suffix.$id);
   			$this->assign("jumpUrl","/index.php");
   			$this->success($message);
		}else{
			echo "验证码已经失效";exit;
		}
	}
	public function sendMoblieValid($username){
		$commonAction = A("Common");
		$uid = $this->getUid();
		$Rstr = $commonAction->getRandStr(4);
		$_SESSION["ym_moblie_valid_code"] = $Rstr;
		$_SESSION["ym_moblie_valid_send_time"] = time();
        $message = "您的校验码是:".$Rstr."，请将该号码输入后即可验证成功。";
		$commonAction->sendsms($username,$message);
	}
	public function smcode(){
			$_SESSION["ym_moblie_valid_send_time"]  = !isset($_SESSION["ym_moblie_valid_send_time"])?time():$_SESSION["ym_moblie_valid_send_time"];
			$timeout = ($_SESSION["ym_moblie_valid_send_time"]+$this->validtime)-time();
			$timeout = $timeout>0?$timeout:"";
			if($timeout==""){
				$this->sendMoblieValid($_SESSION["ym_users"]["name"]);
				$_SESSION["ym_moblie_valid_send_time"]=time();
				echo json_encode(array("status"=>1,"message"=>"发送成功!"));
				exit;
			}else{
				echo json_encode(array("status"=>10002,"message"=>"授权失效，请刷新页面!"));
				exit;
			}
	}
	public function mobileValid(){
		if($this->is_valid(2)){
			echo json_encode(array("status"=>10002,"message"=>"此用户已经验证手机"));
			exit;
		}
		$code = $_POST["code"];
		if($_SESSION["ym_moblie_valid_code"] == $code){
			$this->cvalid(0);
			echo json_encode(array("status"=>1,"message"=>"验证成功"));
			exit;
		}else{
			echo json_encode(array("status"=>10001,"message"=>"验证码已经失效"));
			exit;
		}
	}
	private function cvalid($type){
		$uid = $this->getUid();
		$userinfoModel = D("UsersInfo");
		if($type){
			$data["email_valid"] = 1;
            if(!empty($_SESSION["ym_users"]["update_email"])){
                $u = $this->getUserInfo();
                $email = $_SESSION["ym_users"]["update_email"];
                $data["email"] = $email;
                $this->Rset($this->email_moblie_suffix.$email,$u["id"]);
                //删除redis 中旧用户                              
                $this->Rdel($this->email_moblie_suffix.$u["email"]);
                unset($_SESSION["ym_users"]["update_email"]);
            }
		}else{
			$data["moblie_valid"] = 1;
		}
		$userinfoModel->where("id=".$uid)->save($data);	
		return true;
	}
	/**
	 * Enter description here...
	 * 修改邮箱
	 */
	public function update_email(){
        $this->assign("title",L("title_edit_email"));
        $this->assign("c_email","nav-spa");
		$userinfo = $this->getUserInfo();
		$this->assign("userinfo",$userinfo);
		$this->display("update_email");
	}
	public function update_email_do(){
		$email = $_POST["email"];
		$u = $this->getUserInfo();
		$commonAction = A("Common");
		if(!$commonAction->is_email($email)){
			 $data = json_encode(array("status"=>10001,"message"=>L("email_check_error")));
		}else{
			$uid = $this->Rget($this->email_moblie_suffix.$email);
			if($uid){
    			$data = json_encode(array("status"=>10002,"message"=>L("user_name_exit")));
    		}else{
    			/* 注：邮件必须确认后才进行修改
                $data = array();
    			$data["email"] = $email;
    			$data["email_valid"] = 0;
    			$userinfoModel = D("UsersInfo");
    			$userinfoModel->where("id=".$this->getUid())->save($data);
    			$this->Rset($this->email_moblie_suffix.$email,$u["id"]);
    			//删除redis 中旧用户
    			$this->Rdel($this->email_moblie_suffix.$u["email"]);*/
                $_SESSION["ym_users"]["update_email"] = $email;
    			$this->sendEmailValid($email);
    			$data = json_encode(array("status"=>1,"message"=>L("edit_success"),"jumpUrl"=>"/index.php/Confirm/email/isAjax/1/do/email"));
    		}
		}
		echo $data;exit;
	}
	/**
	 * Enter description here...
	 * 修改手机
	 */
	public function update_moblie(){
        $this->assign("title",L("title_edit_moblie"));
        $this->assign("c_moblie","nav-spa");
		$userinfo = $this->getUserInfo();
		$this->assign("userinfo",$userinfo);
		$this->display("update_moblie");
	}
	public function update_moblie_do(){
		$moblie = $_POST["moblie"];
		$commonAction = A("Common");
		$u = $this->getUserInfo();
		if(!$commonAction->is_moblie($moblie)){
			 $data = json_encode(array("status"=>10001,"message"=>"手机格式不正确"));
		}else{
			$uid = $this->Rget($this->email_moblie_suffix.$moblie);
			if($uid){
    			$data = json_encode(array("status"=>10002,"message"=>"此用户已经存在"));
    		}else{
    			$data = array();
    			$data["moblie"] = $moblie;
    			$data["moblie_valid"] = 0;
    			$userinfoModel = D("UsersInfo");
    			$userinfoModel->where("id=".$this->getUid())->save($data);
    			$this->sendMoblieValid($moblie);
    			$this->Rset($this->email_moblie_suffix.$moblie,$u["id"]);
    			//删除redis 中旧用户
    			$this->Rdel($this->email_moblie_suffix.$u["moblie"]);
    			$data = json_encode(array("status"=>1,"message"=>"邮箱修改成功","jumpUrl"=>"/index.php/Confirm/moblie/isAjax/1"));
    		}
		}
		echo $data;exit;
	}
}


?>
