<?php 
class BaseApiAction extends Api{
   /**
     * _initialize 
     * 应用初始化
     * @access protected
     * @return void
     */
    function _initialize(){
        $this->getParam();
    }
    /**
     * getParam 
     * 处理 conditon core
     * @access private
     * @return void
     */
    private function getParam(){
        $condition = base64_decode($_POST["conditon"]);
        $condition = json_decode($condtion);
        foreach($condition as $k=>$v){
            $_POST[$k] = $v;
        }
    }
}
?>
