<?php
class IndexAction extends BaseAction {
    public function index(){
        $this->assign("title",L("title_my_info"));
         // $this->assign("c_home","nav-spa");
    	$qyModel = D("UsersQy");
    	$id = $this->getUid();
    	$qyData = $qyModel->where("id=".$id)->find();
    	$userinfo = array_merge($this->getUserInfo(),$qyData);
    	$this->assign("level",$this->safeLevel($userinfo));
    	$this->assign("random",mt_rand(10000,99999));
    	$this->assign("userinfo",$userinfo);
    	$this->display("index");
    }
    private function safeLevel($userinfo){
    	if($userinfo["comany_valid"]==2){
    		$userinfo["comany_valid"] = 1;
    	}else{
    		$userinfo["comany_valid"] = 0;
    	}
    	$level = $userinfo["comany_valid"]+$userinfo["email_valid"]+$userinfo["moblie_valid"];
    	if($level==0||$level==1){
    		return 1;
    	}else{
    		return $level;
    	}
    	
    }
    public function editpass(){
        $this->assign("title",L("title_edit_pass"));
        $this->assign("c_editpass","nav-spa");
    	$this->display("editpass");
    }
    public function updatepass(){
    	$oldpass = $_POST["oldpass"];
    	$newpass = $_POST["newpass"];
    	$userModel = D("Users");
    	$uid = $this->getUid();
    	$userinfo = $userModel->where("id=".$uid)->find();
    	if($userinfo["password"] == hash_hmac(md5,$oldpass,$userinfo["username"].$userinfo["reg_time"])){
    		$data = array();
    		$data["password"] = hash_hmac(md5,$newpass,$userinfo["username"].$userinfo["reg_time"]);
    		$userModel->where("id=".$uid)->save($data);
    		echo json_encode(array("status"=>1,"message"=>L("pass_edit_success"),"jumpUrl"=>"/index.php/User/loginout"));
    	}else{
    		echo json_encode(array("status"=>10001,"message"=>L("old_pass_error")));
    	}
    }
    /**
     * 测试发送邮件
     * 
     * */
  /* public function send(){
    	$commonAction = A("Common");
    	echo $commonAction->sendsms("13810000714","hello");
    }*/
}
