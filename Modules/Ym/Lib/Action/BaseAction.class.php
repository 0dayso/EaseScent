<?php
class BaseAction extends Action{
    public $email_moblie_suffix = "#ym_account_#username_";
    public $nick_suffix = "#ym_account_#nickname_";
    public $ym_jump_url = "";
   function _initialize(){
        //验证是否登录
       $this->checkLogin(); 
       $ym_user_front = $this->getUserInfo();
        //选择主题
       $this->theme();

       //处理菜单样式
       $this->assign("front_ym_user",$ym_user_front);  
   }
   protected function getHost(){
        return "http://".$_SERVER["HTTP_HOST"];
   }
    /*
    *验证登陆
    *@author kenvinwei
    */
    public function checkLogin()
    {
        if(!in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE'))))
        {//不需要认证的模块除外
            if(empty($_SESSION[C('SESSION_USER_SIGN')]))
            {
                redirect("/index.php/User/login");
            }else{
                $ym_jump_url = $this->ym_jump_url;
                if(!empty($_SESSION["ym_users"]["jump_url"])){
                    $this->ym_jump_url = $_SESSION["ym_users"]["jump_url"];
                    unset($_SESSION["ym_users"]["jump_url"]);
                }
                if(!empty($ym_jump_url)){    
                    redirect($ym_jump_url);
                }
            }
        }
    }
    /**
     * theme 
     * 主题选择
     * @access private
     * @return void
     */
    private function theme(){
       /* if($_GET["theme"]=="english"){
    	    setcookie("ym_theme","english",time()+7*24*3600,"/",$_SERVER['HTTP_HOST']);
        }*/
        if($_COOKIE["think_template"]=="\"english\""){
            cookie('think_language','ym-en',3600);
            //App::init();
           // C("DEFAULT_LANG","ym-en");
        }else{
            cookie('think_language','ym-zh',3600);
        }
    }
    /**
     * 
     */
    public function getUid(){
    	return isset($_SESSION["ym_users"]["uid"])?$_SESSION["ym_users"]["uid"]:false;
    }
    /**
     * Enter description here...
     * 获取用户资料
     */
    public function getUserInfo(){
    	$userinfoModel = D("UsersInfo");
    	$userqyModel = D("UsersQy");
    	if($this->getUid()){
    		$userinfo = $userinfoModel->where("id=".$this->getUid())->find();
            $userinfo["qy_name"] = $userqyModel->where("id=".$this->getUid())->getField("qy_name");   
            $userinfo["b_type"] = $userqyModel->where("id=".$this->getUid())->getField("b_type");   
            $userinfo["comany_valid"] = $userqyModel->where("id=".$this->getUid())->getField("comany_valid");   
    		return $userinfo;
    	}
    	return false;
    }
    /**
     * Enter description here...
     * c 1 验证登录 url 
     */
 	public function cgo($url){
 		if($_SESSION[C('SESSION_USER_SIGN')]){
 			$this->redirect("/index.php");
 		}
 		return false;
 	}
    /**
     * Enter description here...
     * Rdb 操作
     * @return unknown
     */
    public  function Rdb(){
    	$file = dirname(__FILE__);
    	require_once($file.'/RedisDB.php');
    	return RedisDB::getInstance();
    }
    public function Rset($k,$v){
    	$Rdb = $this->Rdb();
    	return $Rdb->set($k,$v);
    }
    public function Rget($k){
    	$Rdb = $this->Rdb();
    	return $Rdb->get($k);
    }
    public function Rdel($k){
    	$Rdb = $this->Rdb();
    	return $Rdb->del($k);
    }
}
?>
