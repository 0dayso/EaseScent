<?php 
define("ym_api",1.0);
class ClientApiAction extends Api{
    public $email_moblie_suffix = "#ym_account_#username_";
    public $nick_suffix = "#ym_account_#nickname_";
   /**
     * _initialize 
     * 应用初始化
     * @access protected
     * @return void
     */
    function _initialize(){
        parent::_initialize();
    }
    /**
     * getParam 
     * 处理 conditon core
     * @access private
     * 参数规范  array(
     *                  "mod"=>{ActionName_Fun},
     *                   "conditon"=>array(
     *                       "aa"=>"bb"
     *                   )
     *               )
     * @return void
     */
    function test(){
        $c = array(
                    "mod"=>"User_auth",
                    "condition"=>array(
                            "username"=>"695936703@qq.com",
                            "password"=>"123456"
                            )
                    );
        echo base64_encode(json_encode($c));
    }
    private function getParam(){
        $condition = base64_decode($_POST["condition"]);
        $param = json_decode($condition,true);
        return $param;
    }
    private function getMod($name){
        $name = explode("_",$name);
        $len = count($name);
        if($len!=2) return false;
        return $name;
    }
    private function getAction($name){
        $mod = $this->getMod($name);
        if($mod[0]){
            return ucfirst($mod[0]);
        }
        return false;
    }
    private function getMothod($name){
        $mod = $this->getMod($name);  
        return empty($mod[1])?false:$mod[1];
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
//testUrl : http://ym.account.com/index.php/ClientApi/doCore/condition/eyJtb2QiOiJVc2VyX2xvZ2luIiwiY29uZGl0aW9uIjp7InVzZXJuYW1lIjoiNjk1OTM2NzAzQHFxLmNvbSIsInBhc3N3b3JkIjoiMTIzNDU2In19
    /**
     * do 
     * 接口核心处理程序
     * @access public
     * @return void
     */
    function doCore(){
        $param = $this->getParam();
        $Action = $this->getAction($param["mod"]);
        $fun = $this->getMothod($param["mod"]);
        //模拟  A("UserApi");
        $c = A($Action."Api");
        echo  $c->$fun($param["condition"]);
    }
}
?>
