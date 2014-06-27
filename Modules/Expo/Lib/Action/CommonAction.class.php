<?php
// +----------------------------------------------------------------------
// | 商机网 [ 58.wine.cn]
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2013 http://58.wine.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------




/**
 * Expo项目的公共控制器
 * 业务底层加载
 * @category   商机底层
 * @subpackage Action
 * @author     Angf <272761906@qq.com>
 */

class CommonAction extends Action {

    //系统初始化信息
    public $Expo_SystemInfo = array();


   /**
    * 系统初始化
    * 加载系统信息
    * 会员信息初始化
    * 模块权限初始化
    */
    public function _initialize() {

        import('@.ORG.Util.Input');
        import('@.ORG.Util.Page');
        import('@.ORG.Util.String');

        //初始系统加载(系统环境)（会员（...）
        $this->Expo_SystemInfo = $this->Expo_System_Initialize();

        //判断用户访问的模块是否需要登陆
        $this->Expo_checkLogin();

        //加载SEO 内容
        $this->Expo_SeoContent();

        //初始化模版变量
        $this->Assign_SystemInfo();


    }



    /**
     * 商机初始化 获取系统和会员信息
     * @return array
     *
     */
    function Expo_System_Initialize(){
        //获取用户信息
        $data['_userInfo']   = $this->Expo_Get_UserInfo();

        return $data;
    }




    /**
     * 用户信息模版展示初始化
     * @return str
     */
    function Assign_SystemInfo(){
        if(!empty($this->Expo_SystemInfo['_userInfo'])){
            $this->assign('Expo_userInfo',$this->Expo_SystemInfo['_userInfo']);
        }
        /**
         *.....其他模版变量 初始化
         */
    }



    /**
     * 获取会员信息缓存
     * @return array cache
     */
    function Expo_Get_UserInfo(){
        $ym_user = session('ym_users');//$_SESSION['ym_users'];
        if($ym_user['uid']){
            $fields = "a.id as agent_id,a.company_type,a.grade,a.serve_open_time,a.serve_end_time,u.*,i.*";
            $condition['u.id'] =  $ym_user['uid'];
            if(!S('_Get_UserInfo'.$ym_user['uid'])){
                M()->Table('ym_users_qy  u')->Field($fields)->join(' ym_users_info as i ON i.id = u.id')->join(' expo_agent as a ON a.qy_id = u.id')->where($condition)->cache('_Get_UserInfo'.$ym_user['uid'],'60')->find();
            }
            $data = S('_Get_UserInfo'.$ym_user['uid']);
            return $data[0]; //thinkphp 需要改进的地方 一位数组缓存之后来 变多维
        }
    }




   /**
    * 验证访问的模块是否是登陆
    * @return
    *
    */
    function Expo_checkLogin(){
        if(in_array(MODULE_NAME,C('NEED_LOGIN'))) {
            if(!session("Ym_SIGN")) {
                $my_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                setcookie("ym_jump_url",$my_url,time()+60*12,"/",".wine.cn");
                redirect( C('YM_DOMAIN')."/index.php/User/login");
            }
        }
    }





  /*本行以下所有函数 都是老版本 以后做删除 替换处理 */

  /**
     * Ym_getQyInfo
     * 获取企业认证基本信息
     * @access public
     * @return void
     */
    public function Ym_getQyInfo($qy_id=0) {
        if(!S('Ym_userinfo'.$qy_id)){
            $map['id']  = $qy_id;
            $qy_info = M()->Table('ym_users_qy')->where($map)->cache('Ym_userinfo'.$qy_id,'60')->find();
        }
        $data = S('Ym_userinfo'.$qy_id);
        return $data[0];//thinkphp 需要改进的地方 一位数组缓存之后来 变多维
    }






    /**
     * 获取所有国家相应信息
     */
    public function getCountry($map="") {
        if(!S('countryList'))  $country = M('country')->where($map)->cache('countryList','3600')->select();//获取所有国家信息
        return S('countryList');
    }



    /**
     * 获取产区相应信息 前台产区子产区的调用
     */
    public function getRegion($map="") {
        $cahce_str = implode(',',$map);
        if(!S('regionList'.$cahce_str))
            M('region')->order('sort_order desc')->where($map)->cache('regionList'.$cahce_str,'3600')->limit(40)->select();
            foreach (S('regionList'.$cahce_str) as $key => $value) {
                $data[] = $value;
            }
        return $data;
    }



    /**
     * 获取所有类型相应信息
     */
    public function getWinetype($map="") {
        if(!S('wineType')) M('wine_type')->where($map)->cache('wineType','3600')->select(); //获取所有类型
        $Cache = Cache::getInstance('File',array('expire'=>'3600'));
         if(!$Cache->get('wineType_row')){
            foreach (S('wineType') as $key => $value) {
                $data[$value['level']][$value['fid']][] = $value;
            }
            $Cache->set('wineType_row',$data);
        }
        return S('wineType_row');
    }


    /**
     * 获取 SEO设置的内容
     *
     **/
    private function Expo_SeoContent(){
         B('SeoContent',$SEO);
         $this->assign("Expo_SeoContent", $SEO );
    }



















































/**
    * 发送HTTP请求并获得响应
    * @param url 请求的url地址
    * @param param 发送的http参数
    */
     function Expo_makeRequest($url, $param, $httpMethod = 'GET') {
            $oCurl = curl_init();
            if (stripos($url, "https://") !== FALSE) {
                    curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            }
            if ($httpMethod == 'GET') {
                    curl_setopt($oCurl, CURLOPT_URL, $url . "?" . http_build_query($param));
                    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            } else {
                    curl_setopt($oCurl, CURLOPT_URL, $url);
                    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($oCurl, CURLOPT_POST, 1);
                    curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($param));
            }

            $sContent = curl_exec($oCurl);
            $aStatus = curl_getinfo($oCurl);
            curl_close($oCurl);
            if (intval($aStatus["http_code"]) == 200) {
                    return $sContent;
            } else {
                    return FALSE;
            }
    }


    /**
     * 上传文件
     */
    public function _uploads() {

        $type = isset($_GET['type']) ? Input::getVar($_GET['type']).'/' : '';//获取图片类型
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        $cfg = array(
            'ext' => C('UPLOAD_ALLOW_EXT'),
            'size' => C('UPLOAD_MAXSIZE'),
            'path' => C('UPLOAD_PATH').$type,
        );
        $upload->config($cfg);
        $rest = $upload->uploadFile('imgFile');

        if($rest['errno']) {
            $result = array(
                'error' => 1,
                'message' => $upload->error(),
            );

            $this->_ajaxDisplay($result);
        }

        /**
        * @brief 记录图片长宽
        *
        */
        if ( file_exists($cfg['path'].$rest['path']) ){
                list($width, $height) = getimagesize($cfg['path'].$rest['path']);
        }

        $result = array(
            'error' => 0,
            'url' => C('UPLOAD_WWWPATH') . $type . $rest['path'],
            'filename' => $rest['path'],
            'path'=>$cfg['path'].$rest['path'],
            'width' => $width,
            'height' => $height

        );
        $this->_ajaxDisplay($result);
    }

    public function _newuploads() {//添加酒庄图片

        $type = isset($_GET['type']) ? Input::getVar($_GET['type']).'/' : '';//获取图片类型
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        $cfg = array(
            'ext' => C('UPLOAD_ALLOW_EXT'),
            'size' => C('UPLOAD_MAXSIZE'),
            'path' => C('UPLOAD_PATH').$type,
        );
        $upload->config($cfg);
        $rest = $upload->uploadFile('imgFile');

        if($rest['errno']) {
            $result = array(
                'error' => 1,
                'message' => $upload->error(),
            );
        }else {
            if ( file_exists($cfg['path'].$rest['path']) ){
                list($width, $height) = getimagesize($cfg['path'].$rest['path']);
            }

            $result = array(
                'error' => 0,
                'url' => C('UPLOAD_WWWPATH') . $type . $rest['path'],
                'filename' => $rest['path'],
                'path'=>$cfg['path'].$rest['path'],
                'width' => $width,
                'height' => $height

            );
        }
        return $result;
    }


    /**
     * ajax返回数据
     */
    protected function _ajaxDisplay($result, $type = '') {
        if(empty($type)) $type = C('DEFAULT_AJAX_RETURN');
        if(strtoupper($type)=='JSON') {
        // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:text/html; charset=utf-8');
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML') {
        // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            exit(xml_encode($result));
        }elseif(strtoupper($type)=='EVAL') {
        // 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            exit($result);
        }
    }


    /**
     * 获取数据条数
     */
    protected function _count($model, $map) {
        $count = $model->where($map)->count();
        return $count;
    }

    /**
     * 生成多条数据
     */
    protected function _list($model,$map, $listRow = 6, $url) {
        //$map['is_del'] = '-1';
        //取得满足条件的记录数
        $count = $model->where($map)->count('*');
        $voList = array();
        import ( "@.ORG.Util.Page" );
        $listRow = 9;
        $p = new Page($count, $listRow);
        //判断是否是连接查询

        //分页查询数据
        $voList = $model->where($map)->order('id DESC')->limit($p->firstRow . ',' . $p->listRows)->select();

        //分页跳转的时候保证查询条件
        if($url) {
            $p->parameter .= $url;
        }
        //分页显示
        $page = $p->show();
        $this->assign("page", $page );
        $this->assign("pageNum",$count/$listRow);
        return $voList;
    }


    /**
     * 新增数据
     */
    protected function _insert($model, $data = '') {
        $insert_data = $model->create($data);
        $list = $model->add($insert_data);
        if ($list !== false) { //保存成功
            return $list;
        }
    }

    /**
     * 更新数据
     */
    protected function _update($model, $data = '') {
        $update_data = $model->create($data);
        if ($update_data === false) {
            $this->_jumpGo($model->getError(), 'error');
        }
        // 更新数据
        $list = $model->save($update_data);
        if ($list !== false) {
            return true;
        } else {
            $this->_jumpGo($model->getError(), 'error');
        }
    }



    public function redisCache() {
        static $redisCache = NULL;
        if(!$redisCache) {
            $redisConfig = C('REDIS_CONFIG');
            $redisCache = Cache::getInstance('Redis', array('host' => $redisConfig['host'], 'port' => $redisConfig['port'], 'expire' => 3600));
        }
        return $redisCache;
    }



    /**
     * 获取国家下子产区
     */
    public function bindCountryRegion($country,$region) {
        foreach($country as $key=>$val) {
            foreach($region as $k=>$reg) {
                if($val['id'] == $reg['country_id']) {
                    $country[$key]['region'][]=$reg; //产区合并到国家数组中
                    unset($region[$k]);
                }
            }
        }
        return $country;
    }





    /**
     * 获取汇率
     */
    public function getMoneyrate() {
        $str = file_get_contents("http://hq.sinajs.cn/list=USDCNY",false,3); //从新浪接口获取汇率
        $str = (explode(",",$str));
        $rate = $str[8];
        return $rate;
    }


     /**
     * 获取session中企业信息
     */
    public function getSessionInfo() {
        $YmApi = A('YmApi'); //获取企业通行证接口
        $qy_id = $YmApi->getUid();      //从通行证获取企业ID
        $map = array(//企业筛选条件
            'id' => $qy_id ,
        );

        $qy_list = M()->Table('ym_users_qy')->where($map)->find();  //获取企业的相关信息
        $user = M()->Table('ym_users_info')->where($map)->find();   //获取企业的相关信息
        $qy_list['nick'] = $user['nick'];
        if(strpos($qy_list["qy_web_address"],"http")!==false){
            $qy_list["qy_web_address"] = str_replace("http://","",$qy_list["qy_web_address"]);
        }
        return $qy_list;
    }

    /**
     * 将国家信息入库
     */
    public function insertCountry($country_cname,$country_fname,$country_jk_id,$other) {
        $country_data = array(
            'fname' => $country_fname ,//获取选中的国家名
            'cname' => $country_cname ,//获取选中的国家名
            'jk_id' => empty($country_jk_id)? '-1':$country_jk_id ,//获取选中的关联国家id
            'other' => $other,
        );
        //查询相应国家是否已入库
        $map = array('cname' => $country_cname);
        $country_id = D('Country')->where($map)->field('id')->find(); //查询国家信息是否存在
        if(!$country_id) {
            $country_id['id'] = $this->_insert(D('Country'),$country_data);
        }
        return  $country_id['id'];
    }
    /**
     * 获取post插入产区和类型信息
     */
    public function insertAllReleat($region,$country_id) {//酒库酒款关联的产区入库
        //获取产区信息
        if($region) {
            $region = explode("|",$region);
            $len = count($region);//获取与酒库关联的产区数量
            $region_id = Array();
            for($i=0;$i<$len-1;$i++) {
                $info = explode(",",$region[$i]);//把产区id，fname，cname分割成数组
                $region_data = array(
                    'jk_id' => $info[0] ,//获取选中的产区关联id
                    'fname' => $info[1] ,//获取选中的产区英文名fname
                    'cname' => $info[2] ,//获取选中的产区中文名cname
                    'country_id' => $country_id,//获取选中的关联国家id
                    'pid' => empty($region_pid)? '-1':$region_pid,//获取选中的上一级产区id
                );
                $map = array('cname' => $info[2]);//判断库中是否存在相关信息
                $region_pid = D('Region')->where($map)->field('id')->find();
                $region_pid = $region_pid['id'];
                if(!$region_pid) {
                    $region_pid  = $this->_insert(D('Region'),$region_data);
                }
                $region_id[$i] = $region_pid;
            }
            return $region_id;
        }
    }

    /**
     * 获取post插入级联类型信息
     */
    public function insertPartRegionReleat($t1,$t2,$country_id) {//选择级联关联，酒库酒款关联的产区入库
        //将一级信息存入数据库
        $region_id = Array();
        $region1_data = array(
            'cname' => $t1['2'] ,//获取选中的产区名
            'fname' => $t1['1'],
            'jk_id' => $t1['0'] ,//获取选中关联的产区id
            'country_id'=> $country_id,
        );
        //查询相应信息是否已入库
        $map = array('cname' => $t1['2']);
        $region_pid = D('Region')->where($map)->field('id')->find();
        $region_pid  = $region_pid['id'];
        if(!$region_pid ) {
            $region_pid = $this->_insert(D('Region'),$region1_data);
        }

        //将二级类型信息存入数据库
        $region2_data = array(
            'cname' => $t2['2'] ,//获取选中的产区名
            'fname' => $t2['1'],
            'jk_id' => $t2['0'] ,//获取选中关联的产区id
            'pid' =>  $region_pid,//获取选中的上一级类型id
            'country_id'=> $country_id,
        );

        //查询二级类型是否已入库
        $map = array('cname' => $t2['2']);
        $region_zid = D('Region')->where($map)->field('id')->find();//查找类型是否存在
        $region_zid = $region_zid['id'];
        if(!$region_zid ) {
            $region_zid  = $this->_insert(D('Region'),$region2_data);//直接将2级类型入库
        }
        $region_id['0'] = $region_pid;//
        $region_id['1'] = $region_zid;//将父子id赋值到数组中
        return  $region_id;
    }


    /*
     * 插入无关联产区信息
     */
    public function insertNoReleat($name,$country_id) {//选择其它，酒库酒款关联的产区入库

        $region_id =Array();
        $region1_data = array(
            'cname' => $name ,//获取选中的国家名
            'fname' => $name ,//获取选中的国家名
            'country_id' => $country_id,
            'other' => 1,
            'pid' => -1,
        );
        //查询相应产区是否已入库
        $map = array('cname' => $name);
        $region_pid = D('Region')->where($map)->field('id')->find();
        $region_pid = $region_pid['id'];
        if(!$region_pid ) {
            $region_pid  = $this->_insert(D('Region'),$region1_data);

        }
        $region_id[0] = $region_pid;//将父id赋值到数
        return $region_id;
    }

    /**
     * 获取post插入酒款关联类型信息
     */
    public function insertAllTypeReleat($type) {//酒库酒款关联的产区入库
        //获取产区信息
        if($type) {
            $type = explode("|",$type);
            $len = count($type);//获取与酒库关联的产区数量
            $type_id = Array();
            for($i=0;$i<$len-1;$i++) {
                $info = explode(",",$type[$i]);//把产区id，fname，cname分割成数组
                $type_data = array(
                    'jk_id' => $info[0] ,//获取选中的产区关联id
                    'fname' => $info[1] ,//获取选中的产区英文名fname
                    'cname' => $info[2] ,//获取选中的产区中文名cname
                    'pid' => empty($type_pid)? '-1':$type_pid,//获取选中的上一级产区id'pid' => //获取选中的上一级产区id
                );
                $map = array('cname' => $info[2]);//判断库中是否存在相关信息
                $type_pid = D('WineType')->where($map)->field('id')->find();
                $type_pid = $type_pid['id'];
                if(!$type_pid) {
                    $type_pid = $this->_insert(D('WineType'),$type_data);
                }
                $type_id[$i] = $type_pid;
            //echo"<pre>"; print_r($this->_insert(D('WineType'),$type_data)); exit;
            }
            return $type_id;
        }
    }
    /**
     * 获取post插入级联类型信息
     */
    public function insertPartTypeReleat($t1,$t2) {//选择级联关联，酒库酒款关联的产区入库
        //将一级信息存入数据库
        $type_id = Array();
        $type1_data = array(
            'cname' => $t1['2'] ,//获取选中的产区名
            'fname' => $t1['1'],
            'jk_id' => $t1['0'] ,//获取选中关联的产区id
        );
        //查询相应信息是否已入库
        $map = array('cname' => $t1['2']);
        $type_pid = D('WineType')->where($map)->field('id')->find();
        $type_pid = $type_pid['id'];
        if(!$type_pid ) {
            $type_pid = $this->_insert(D('WineType'),$type1_data);
        }

        //将二级类型信息存入数据库
        $type2_data = array(
            'cname' => $t2['2'] ,//获取选中的产区名
            'fname' => $t2['1'],
            'jk_id' => $t2['0'] ,//获取选中关联的产区id
            'pid' =>  $type_pid,//获取选中的上一级类型id
        );

        //查询二级类型是否已入库
        $map = array('cname' => $t2['2']);
        $type_zid = D('WineType')->where($map)->field('id')->find();//查找类型是否存在
        $type_zid = $type_zid['id'];
        if(!$type_zid ) {
            $type_zid  = $this->_insert(D('WineType'),$type2_data);//直接将2级类型入库
        }
        $type_id['0'] = $type_pid;//
        $type_id['1'] = $type_zid;//将父子id赋值到数组中
        return  $type_id;
    }
    /**
     * 获取post插入无关联类型信息
     */
    public function insertNoTypeReleat($typename) {//选择其它，酒库酒款关联的产区入库

        $type_id =Array();
        $type_data = array(
            'cname' => $typename ,//获取选中的国家名
            'fname' => $typename ,//获取选中的国家名
            'other' => 1,
            'pid'   => -1,
        );
        //查询相应产区是否已入库
        $map = array('cname' => $name);
        $type_pid = D('WineType')->where($map)->field('id')->find();
        $type_pid = $type_pid['id'];
        if(!$type_pid ) {
            $type_pid  = $this->_insert(D('WineType'),$type_data);
        }
        $type_id[0] = $type_pid;//将父id赋值到数组中
        return $type_id;
    }

    /**
     * @brief  格式化展会相关图片url
     *
     * @params $type {string} wine/agent
     *
     * @return
     */
    public function parse_img_url($data, $type) {
        if (!is_array($data) ) return  C('UPLOAD_WWWPATH').$type.'/'.'images'.'/'.$data;
        foreach ( $data as $key => $dt ) {
            $data[$key]['path'] = $dt['url'];
            $data[$key]['url'] = C('UPLOAD_WWWPATH').$type.'/'.'images'.'/'.$dt['url'];
        }
        return $data;
    }




    /*
     * 过滤葡萄品种比例显示 angf
     *
     */
    public function filter_grape($variety){
       //葡萄品种格式化
       $pattern = "/:%/";
       if(preg_match($pattern, $variety, $match)) {
          $variety = str_replace(":%", "", $variety);
      }
      return $variety;
    }



    /**
    * _empty
    * 空操作提示 angf
    * @access public
    * @return str
    */
    public function _empty($action){
        $this->error(MODULE_NAME.' 模块下【'.$action.'】动作不存在');
    }






















































    /**以下代码 在测试没有问题· 即将删除**/

    /**
     * getQyInfo
     * 获取企业信息   删除
     * @access public
     * @return void
     */
    /*public function getQyInfo($qy_id) {
        $map = array('id' => $qy_id);
        $qy_info = M()->Table('ym_users_qy')->where($map)->find();
        return $qy_info;
    }*/



}
