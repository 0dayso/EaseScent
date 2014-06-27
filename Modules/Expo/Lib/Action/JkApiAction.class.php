<?php

/**
 * file:JkApi
 * brief:酒库接口类
 * author:zhuyl
 * date:2013-1.5
 */
class JkApiAction extends CommonAction {



//测试机

	public $api_url    = "http://api.wine.cn/?Jiuku/Api/";
    public $token_url  = "http://api.wine.cn/?Admin/Api/accessToken";
    public $appid      = 1;
    public $appkey     = "f57d38044417390250f5211863e5d882";
    public $jk_api_mod = array(
                                 //酒mod
                                'j_list'    => 'SearchWine',
                                'j_baseInfo'=> 'getWineBasisData',
                                'j_allinfo' => 'getWineFullData',

                                'CL'        => 'getCountryList',
                              );

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
     * 获取Token
     * @access public
     * @return json
     */
    function getToken() {
        if(session('token')){
            return $token = session('token');
        }else{
            $post = array('appid' => $this->appid,'appkey' => $this->appkey );
            $jGet = CurlPost($this->token_url, $post);
            return session('token',json_decode($jGet,true));
        }

    }

     /**
     * getData
     * 根据搜索条件从酒库获取信息
     * @access public
     * @return json
     */
    function getData() {

        $keyword = $this->_request('keyword');//获取搜索关键词

        /* 没有用的代码 Angf
        $map = array();
        $url = '';
        if($keyword) {
            $map_k['fname'] = array('like', '%'.$keyword.'%');
            $map_k['cname'] = array('like', '%'.$keyword.'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;

        }
        */
        //token
        $token = $this->getToken();
        $posts = array('appid' => 1,  'accessToken' => $token["result"],    "kw" =>$keyword,);
         /*酒库接口*/
        $list = $this->cPost($this->api_url.$this->jk_api_mod['j_list'],$posts);//根据关键字获取数据

        if($list["errorCode"]==0) {



            echo(json_encode($list['result']));
        }
    }

   /**
     * getFullData
     * 从酒库获取相应酒款所有信息
     * @access public
     * @return json
     */
    function getFullData() {
        $id = Input::getVar($_REQUEST['id']);//获取搜索关键词
        //$list = $this->getFullById($id);
        $c = array(
            "mod"=>"getWineFullData",
            "condition"=>array(
            "id"=>$id
            )
        );
        //token
        $token = $this->getToken();
        $posts = array(
            'appid' => 1,
            'accessToken' => $token["result"],
            "id" =>$c['condition']['id']
        );
         /*酒库接口*/
        $list = $this->cPost($this->api_url.$c['mod'],$posts);



        if($list["errorCode"]==0) {
            $list = $list['result'];
            $info['id'] = $list['id'];
            $info['fname'] = $list['fname'];
            $info['cname'] = $list['cname'];
            $info['countryid'] = $list['country']['id'];
            $info['countryfname'] = $list['country']['fname'];
            $info['countrycname'] = $list['country']['cname'];
            $info['region'] = $list['region'];
            //获取产区信息
            foreach($list['region'] as $key=>$val) {
                $info['region'][$key]['id'] = $val['id'];
                $info['region'][$key]['cname'] = $val['cname'];
                $info['region'][$key]['fname'] = $val['fname'];
            }
            //获取品种比例信息
            foreach($list['grape'] as $key=>$val) {
                $info['grape'][$key]['id'] = $val['id'];
                $info['grape'][$key]['cname'] = $val['cname'];
                $info['grape'][$key]['fname'] = $val['fname'];
                $info['grape'][$key]['percent'] = $val['percent'];
            }
            //获取酒款类型信息
            foreach($list['winetype'] as $key=>$val) {
                $info['winetype'][$key]['id'] = $val['id'];
                $info['winetype'][$key]['cname'] = $val['cname'];
                $info['winetype'][$key]['fname'] = $val['fname'];
            }
            echo(json_encode($info));
        }
    }

    /**
     * getCountry
     * 从酒库获取国家信息
     * @access public
     * @return json
     */
    function getCountry() {
        //token
        $token = $this->getToken();

        $c = array(
            "mod"=>"getCountryList",
            "condition"=>array(
            )
        );

        $posts = array(
            'appid' => 1,
            'accessToken' => $token["result"],
        );
         /*酒库接口*/
        $list = $this->cPost($this->api_url.$c['mod'],$posts);//酒库获取国家列表
        if($list["errorCode"]==0) {
            $info = $list['result'];
            foreach($info as $key=>$val) {
                $info[$key]['id'] = $val['id'];
                $info[$key]['cname'] = $val['cname'];
                $info[$key]['fname'] = $val['fname'];
            }
            echo(json_encode($info));
        }
    }

    /**
     * getRegion
     * 从酒库获取产区信息
     * @access public
     * @return json
     */
    function getRegion() {

        $country_id = Input::getVar($_REQUEST['country_id']);//获取搜索关键词
        $pid = Input::getVar($_REQUEST['pid']);//获取搜索关键词
        $c = array(
            "mod"=>"getRegionList",
            "condition"=>array(
            "country_id"=>$country_id,
            "pid"=>$pid
            )
        );
        //token
        $token = $this->getToken();
        $posts = array(
            'appid' => 1,
            'accessToken' => $token["result"],
            "country_id" =>$c['condition']['country_id'],
            "pid" =>$c['condition']['pid'],
        );
         /*酒库接口*/
        $list = $this->cPost($this->api_url.$c['mod'],$posts);//从酒库获取数据
        if($list["errorCode"]==0) {
            $info = $list['result'];
            foreach($info as $key=>$val) {
                $info[$key]['id'] = $val['id'];
                $info[$key]['cname'] = $val['cname'];
                $info[$key]['fname'] = $val['fname'];
            }
            echo(json_encode($info));
        }
    }

     /**
     * getType
     * 从酒库获取类型信息
     * @access public
     * @return json
     */
    function getType() {

        $pid = Input::getVar($_REQUEST['pid']);//获取搜索关键词
        $c = array(
            "mod"=>"getWinetypeList",
            "condition"=>array(
            "pid"=>$pid
            )
        );
        //token
        $token = $this->getToken();
        $posts = array(
            'appid' => 1,
            'accessToken' => $token["result"],
            "pid" =>$c['condition']['pid'],
        );
         /*酒库接口*/
        $list = $this->cPost($this->api_url.$c['mod'],$posts);
        if($list["errorCode"]==0) {
            $info = $list['result'];
            foreach($info as $key=>$val) {
                $info[$key]['id'] = $val['id'];
                $info[$key]['cname'] = $val['cname'];
                $info[$key]['fname'] = $val['fname'];
            }
            echo(json_encode($info));
        }
    }

}
?>
