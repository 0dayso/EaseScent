<?php
/**
 * file:YmApi
 * brief:通行证接口类
 * author:zhuyl
 * date:2013-1.5
 */
class YmApiAction extends CommonAction {

    public $ym_host = '';//企业通行证域名
    public $api_url = '';
    public $token_url = '';//token地址
    public $appid = 1;
    public $appkey = "f57d38044417390250f5211863e5d882";


    function _initialize() {
       parent::_initialize();
        import('@.ORG.Util.Input');
        import('@.ORG.Util.Page');
        import('@.ORG.Util.String');
        $this->ym_host = C('YM_DOMAIN');
        $this->api_url = C('API_DOMAIN').'/?Ym/ClientApi/doCore';
        $this->token_url = C('API_DOMAIN').'/?Admin/Api/accessToken';
        if(!$_SESSION["Ym_SIGN"]) {
            $this->checkLogin(); //验证是否登录
        }
   }







    /*
    *验证登陆
    *@author kenvinwei
     *
    */
    function checkLogin() {

        if(!in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {//不需要认证的模块除外
            if(empty($_SESSION["Ym_SIGN"])) {
                $my_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                $_SESSION["ym_user"]["jump_url"] = $my_url;
                redirect( C('YM_DOMAIN')."/index.php/User/login");
            }else {
                $this->choseIdentity();
            }
        }

    }
   /*
    * 身份选择
    * @author:zhuyl
    */
    public function choseIdentity() {
        $qy_id = $this->getUid();
        $condition = array(
            'qy_id' => $qy_id,
        );
        $agent_data = M('Agent')->where($condition)->find();//通过企业id，查询代理商id
        $winery_data = M('Winery')->where($condition)->find();//通过企业id，查询代理商id

        $qy_condition = array(
            'id' => $qy_id,
        );
        $qy_rz = D()->table('ym_users_qy')->where($qy_condition)->find();     //获取代理商身份认证结果

        if($agent_data) {//如果企业身份是代理商
            if($qy_rz['comany_valid'] == 2) {//判断身份认证是否通过
                $agent_id = $agent_data['id'];
                $this->redirect("Agent/info?agent_id=$agent_id");
            }else {
                $this->redirect('Index/prompt');//跳转到企业认证
            }
        }else if($winery_data) {//如果企业身份是酒庄
                $winery_id = $winery_data['id'];
                $this->redirect("Winery/info?winery_id=$winery_id");
            }else {//企业身份还未确认
                $this->redirect('Select/index');//跳转到身份选择页面
            }
    }

    /**
     * getUid
     * 获取ID
     * @access public
     * @return int
     */
    function getUid() {
        return $_SESSION["ym_users"]["uid"];
    }
    /**
     * getUserInfo
     * 获取用户信息
     * @access public
     * @return array
     */
    function getUserInfo() {
        $uid = $this->getUid();
         /*通行证接口*/
        $c = array(
            "mod"=>"User_getUserInfo",
            "condition"=>array(
            "uid"=>$uid
            )
        );
        $c = base64_encode(json_encode($c));
        //token
        $token = $this->getToken();
        $posts = array(
            'appid' => 1,
            'accessToken' => $token["result"],
            "condition" =>$c
        );

         /*通行证接口*/
        $u = $this->cPost($this->api_url,$posts);
        return $u;
    }
    /**
     * updateUser
     * 修改用户资料
     * @access public
     * @return void
     */
    function updateUser() {
        $uid = $this->getUid();
    }
      /**
     * cPost
     *
     * @access public
     * @return json
     */
    function cPost($url,$param) {
        $data = CurlPost($url,$param);
        return json_decode($data,true);
    }

    /**
     * getToken
     *
     * @access public
     * @return json
     */
    function getToken() { //获取token
        $post = array('appid' => $this->appid, 'appkey' => $this->appkey);
        $jGet = CurlPost($this->token_url, $post);
        return json_decode($jGet,true);
    }

     /**
     * getQyInfo
     * 获取企业信息
     * @access public
     * @return void
     */
    public function getQyInfo() {
        $YmApi     = A('YmApi');                                         //获取企业通行证接口
        $qy_id     = $YmApi->getUid();                                   //从通行证获取企业ID
        $map['id'] = $qy_id;                                         //企业筛选条件
        $qy_list   = M()->Table('ym_users_qy')->where($map)->find();   //获取企业的相关信息
        $user      = M()->Table('ym_users_info')->where($map)->find();    //获取企业的相关信息
        $qy_list['nick'] = $user['nick'];
        return $qy_list;
    }

      /**
     * skipQy
     * 企业通行证跳转展会公司前台
     * @access public
     * @return void
     */
    public function skipQy() {	
		 if($this->Expo_SystemInfo['_userInfo']['agent_id'])
            $this->redirect("Inland/index?agent_id=".$this->Expo_SystemInfo['_userInfo']['agent_id']);//跳转到代理商后台
		 $this->redirect("Select/index");//否则跳转到完善信息页面
    }

}


