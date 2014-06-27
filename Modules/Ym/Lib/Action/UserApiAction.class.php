<?php
if(!defined("ym_api")) exit("can not permission!");
class UserApiAction extends ClientApiAction{
    public $token_suffix = "#ym_token_#login_";

    /**
     * login 
     * 处理登录
     * @access public
     * @return void
     */
     function login($param){
        if(empty($param["username"])) return json_encode(array("status"=>50001,"message"=>"用户名为空"));
        if(empty($param["password"])) return json_encode(array("status"=>50002,"message"=>"密码为空"));
        $UserAction = A("User");
        $uid = $this->doLogin($param["username"],$param["password"]);
        if($uid){//登录成功
            $userModel = D("Users");
            $userinfoModel = D("UsersInfo");
            $userqyModel = D("UsersQy");
            $userinfo = $userinfoModel->where("id=".$uid)->find();
            $userinfo["comany_valid"] = $userqyModel->where("id=".$uid)->getField("comany_valid");
            $faceUrl = "";
            if(!empty($userinfo["face_type"])){
                $faceUrl = "http://".$_SERVER["HTTP_HOST"]."/Upload/Ym/face/u_".$uid."/s_myface.".$userinfo["face_type"]; 
            }
            $userinfo["user_face"] = $faceUrl; 
            return json_encode($userinfo);
        }else{
            return json_encode(array("status"=>50003,"message"=>"用户名或密码错误"));
        }
    }
    private function doLogin($username,$password){
        $uid =  $this->Rget($this->email_moblie_suffix.$username);
        if(!$uid) return false;
        $userModel = D("Users");
        $userinfo = $userModel->where("id=".$uid)->find();
        if($userinfo["password"] == hash_hmac(md5,$password,$username.$userinfo["reg_time"])){
            return $uid;
        }
        return false;
    }
    /**
    *自动检测登录
    */
    function auth($p){
        $token = $p["ym_token"];
    	if(empty($token)) return json_encode(array("status"=>50005,"message"=>"没有token!"));
    	$username = $p["ym_users"];
    	if(empty($username)) return json_encode(array("status"=>50006,"message"=>"没有用户名!"));
    	$Rtoken = $this->Rget($this->token_suffix.$username);
    	if(empty($Rtoken)) return json_encode(array("status"=>50007,"message"=>"token 失效!"));
    	$userModel = D("Users");
    	//验证是否id相同，相同则授权登录
    	if($Rtoken==$token){
    		$userinfo = $userModel->where("username='".$username."'")->find();
    	}else{
            return json_encode(array("status"=>50004,"message"=>"检测失败，未登录!"));    
         }

        $uid = $userinfo["id"];
        if($uid){ //登录成功
            $userModel = D("Users");
            $userinfoModel = D("UsersInfo");
            $userqyModel = D("UsersQy");
            $userinfo = $userinfoModel->where("id=".$uid)->find();
            $userinfo["qiyeinfo"] = $userqyModel->where("id=".$uid)->find();
            $faceUrl = "";
            if(!empty($userinfo["face_type"])){
                $faceUrl = "http://".$_SERVER["HTTP_HOST"]."/Upload/Ym/face/u_".$uid."/s_myface.".$userinfo["face_type"]; 
            }            
            $userinfo["user_face"] = $faceUrl; 
            return json_encode($userinfo);
        }else{
            return json_encode(array("status"=>50006,"message"=>"用户名获取失败"));
        }
    }
    function getUserInfo($p){
        $uid = $p["uid"];
        $ym_uid = $_SESSION["ym_users"]["uid"];
        if($uid!=$ym_uid){
            return json_encode(array("status"=>50008,"message"=>"session 不同步!"));
        }
        if($uid){ //登录成功
            $userModel = D("Users");
            $userinfoModel = D("UsersInfo");
            $userqyModel = D("UsersQy");
            $userinfo = $userinfoModel->where("id=".$uid)->find();
            $userinfo["qiyeinfo"] = $userqyModel->where("id=".$uid)->find();
            $faceUrl = "";
            if(!empty($userinfo["face_type"])){
                $faceUrl = "http://".$_SERVER["HTTP_HOST"]."/Upload/Ym/face/u_".$uid."/s_myface.".$userinfo["face_type"]; 
            }               
            $userinfo["user_face"] = $faceUrl; 
            return json_encode($userinfo);
        }else{
            return json_encode(array("status"=>50006,"message"=>"用户名获取失败"));
        }
    }
    /**
     * updateUserInfo 
     * 修改用户信息
     * @access public
     * @return void
     */
    function updateUserInfo($p){
        $uid = $p["uid"];
        $ym_uid = $_SESSION["ym_users"]["uid"];
        if($uid!=$ym_uid){
            return json_encode(array("status"=>50008,"message"=>"session 不同步!"));
        }
        if($uid){
            $userqyModel = D("UsersQy");
            $logModel = D("Logs");
            $userqyModel->where("id=".$uid)->save($p["data"]);
            //更新日志
            $data = array();
            $data["uid"] = $uid;
            $data["time"] = time();
            $data["action"] = "接口修改用户资料";
            $logModel->add($data);
            return json_encode(array("status"=>1,"message"=>"修改成功"));
        }else{
            return json_encode(array("status"=>50006,"message"=>"用户名获取失败"));
        }
    }
}
?>
