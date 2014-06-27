<?php

/**
 * file:Foreign
 * brief:国外馆的相关类
 * author:zhuyl
 * date:2013-1.5
 */

class ForeignAction extends CommonAction {
/**
     * index
     * 国外馆主页
     * @access public
     * @return void
     */
    public function index() {
        $Inland = A('Inland'); //获取国内馆接口
        if($_SESSION["Ym_SIGN"]) {
            $qySessionInfo = $this->getSessionInfo();//判断企业通行证是否登录
        }
        $allType = parent::getType();//从缓存中获取所有类型信息
        $winery_id = Input::getVar($_REQUEST['winery_id']);//获取酒庄的ID
        $winery_info = $Inland->getWineryInfo($winery_id);//根据酒庄的ID，获取相关信息
        //获取酒庄的图片
        $map = array(
            'winery_id' => $winery_id,
        );
        //获取代理商的图片
        $img = D('WineryImg')->where($map)->order('id ASC')->select(); //获取酒庄图片，并根据id排序

        if(!empty($img)) {
            $img = $this->parse_img_url($img, 'Winery');
            foreach ($img as $key => $val) {
                $img[$key]['url'] = $val['url'];
                $img[$key]['queue'] = $val['queue'];
            }
        }else {
            $img[$key]['url'] = '';
        }
        //根据企业ID，去通行证获取企业信息
        $qy_id = $winery_info['qy_id'];
        $qy_info = $Inland->getQyInfo($qy_id);
        //获取酒款相关信息
        $wine_list = $this->getWineInfo($winery_id);//获取酒款信息

        $this->assign('allType', $allType);
        $this->assign('img',$img);
        $this->assign('wine_list', $wine_list);
        $this->assign('winery_info', $winery_info);
        $this->assign('qy_info', $qy_info);
        $this->assign('winery_id',$winery_id);
        $this->assign('qySessionInfo', $qySessionInfo);
        $this->display();
    }

    /**
     * product
     * 国外馆产品页
     * @access public
     * @return void
     */
    public function product() {
         $Inland = A('Inland'); //获取国内馆接口
        if($_SESSION["Ym_SIGN"]) {
            $qySessionInfo = $this->getSessionInfo();//判断企业通行证是否登录
        }
        $allType = parent::getType();//从缓存中获取所有类型信息
        $allCountry = parent::getCountry();//获取所有国家信息
        $allRegion = parent::getRegion();//获取所有产区信息
        $winery_id = Input::getVar($_REQUEST['winery_id']);//获取酒庄的ID
        $winery_info = $Inland->getWineryInfo($winery_id);//根据酒庄的ID，获取相关信息

        //根据企业ID，去通行证获取企业信息
        $qy_id = $winery_info['qy_id'];
        $qy_info = $Inland->getQyInfo($qy_id); //获取企业信息
        //酒款的相关信息
        $wine_list = $this->getWineInfo($winery_id); //获取酒款信息
        $this->assign('allType', $allType);
        $this->assign('allCountry', $allCountry);
        $this->assign('allRegion', $allRegion);
        $this->assign('winery_info', $winery_info);
        $this->assign('wine_list', $wine_list);
        $this->assign('qy_info', $qy_info);
        $this->assign('winery_id',$winery_id);
        $this->assign('qySessionInfo', $qySessionInfo);
        $this->display();
    }

    /**
     * info
     * 国外馆企业信息
     * @access public
     * @return void
     */
    public function info() {

         $Inland = A('Inland'); //获取国内馆接口
        if($_SESSION["Ym_SIGN"]) {
            $qySessionInfo = $this->getSessionInfo();//判断企业通行证是否登录
        }
        $winery_id = Input::getVar($_REQUEST['winery_id']);//获取酒庄的ID
        $winery_info = $Inland->getWineryInfo($winery_id);//根据酒庄的ID，获取相关信息
        //$winery_info["description"] = stripslashes($winery_info["description"]);
        //获取代理商的图片
        $map = array(
            'winery_id' => $winery_id,
        );
        $img = D('WineryImg')->where($map)->select();

        if(!empty($img)) {
            $img = $this->parse_img_url($img, 'Winery');
            foreach ($img as $key => $val) {
                $img[$key]['url'] = $val['url'];
                $img[$key]['queue'] = $val['queue'];
            }
        }else {
            $img[$key]['url'] = '';
        }
        //根据企业ID，去通行证获取企业信息
        $qy_id = $winery_info['qy_id'];
        $qy_info = $Inland->getQyInfo($qy_id);//获取企业信息

        $this->assign('winery_info', $winery_info);
        $this->assign('qy_info', $qy_info);
        $this->assign('winery_id',$winery_id);
        $this->assign('qySessionInfo', $qySessionInfo);
        $this->display();
    }

      /**
     * wine
     * 国外馆酒款详情
     * @access public
     * @return void
     */
    public function wine() {
        $Inland = A('Inland'); //获取国内馆接口
        if($_SESSION["Ym_SIGN"]) {
            $qySessionInfo = $this->getSessionInfo();//判断企业通行证是否登录
        }
        $allType = parent::getType();//从缓存中获取所有类型信息
        $allCountry = parent::getCountry();//获取所有国家信息
        $allRegion = parent::getRegion();//获取所有产区信息
        $wine_id = Input::getVar($_REQUEST['wine_id']);//获取酒款ID
        $winery_id = Input::getVar($_REQUEST['winery_id']);//获取代理商ID
        $map = array(
            'id' => $wine_id,
            'winery_id' => $winery_id,
        );
        $wine_list = D('Wine')->where($map)->find(); //获取酒款信息

        $winery_info = $Inland->getWineryInfo($winery_id);//根据酒庄的ID，获取相关信息
        $qy_id =  $winery_info['qy_id'];
        $qy_info = $Inland->getQyInfo($qy_id);//获取企业信息

        //根据酒款ID搜索酒款图片
        $img_map = array('wine_id' => $wine_id,);
        $img = D('WineImg')->where($img_map)->order('id ASC')->select(); //获取酒款图片并根据ID排序

         if(!empty($img)) {
            $img = $this->parse_img_url($img, 'Wine');
            foreach ($img as $key => $val) {
                $img[$key]['url'] = $val['url'];
                $img[$key]['queue'] = $val['queue'];
            }
        }else {
            $img[$key]['url'] = '';
        }
        $this->assign('allType', $allType);
        $this->assign('allCountry', $allCountry);
        $this->assign('allRegion', $allRegion);
        $this->assign('wine_list', $wine_list);
        $this->assign('winery_info', $winery_info);
        $this->assign('qy_info', $qy_info);
        $this->assign('list2', $list);
        $this->assign('img', $img);
        $this->assign('winery_id', $winery_id);
        $this->assign('qySessionInfo', $qySessionInfo);
        $this->display();
    }

     /**
     * honor
     * 国外馆酒庄荣誉
     * @access public
     * @return void
     */
    public function honor() {
        $Inland = A('Inland'); //获取国内馆接口
        if($_SESSION["Ym_SIGN"]) {
            $qySessionInfo = $this->getSessionInfo();//判断企业通行证是否登录
        }
        $winery_id = Input::getVar($_REQUEST['winery_id']);//获取酒庄的ID
        $winery_info = $Inland->getWineryInfo($winery_id);//根据代理商的ID，获取相关信息

        //根据企业ID，去通行证获取企业信息
        $qy_id =  $winery_info['qy_id'];
        $qy_info = $Inland->getQyInfo($qy_id);

        //获取荣誉信息
        $map = array(
            'winery_id' => $winery_id,
        );
        $status_info = M('WineryStatus')->where($map)->select();  //获取荣誉信息
        $this->assign('qy_info', $qy_info);
        $this->assign('winery_id',$winery_id);
        $this->assign('winery_info', $winery_info);
        $this->assign('qySessionInfo', $qySessionInfo);
        $this->assign('status_info', $status_info);
        $this->display();
    }

     /**
     * contact
     * 酒庄联系方式
     * @access public
     * @return void
     */
    public function contact() {
        $Inland = A('Inland'); //获取国内馆接口
        if($_SESSION["Ym_SIGN"]) {
            $qySessionInfo = $this->getSessionInfo();//判断企业通行证是否登录
        }
        $winery_id = Input::getVar($_REQUEST['winery_id']);//获取酒庄的ID
        $winery_info = $Inland->getWineryInfo($winery_id);//根据代理商的ID，获取相关信

        $qy_id =  $winery_info['qy_id'];//根据企业ID，去通行证获取企业信息
        $qy_info = $Inland->getQyInfo($qy_id);  //获取企业信息

        $this->assign('qy_info', $qy_info);
        $this->assign('winery_id',$winery_id);
        $this->assign('winery_info', $winery_info);
        $this->assign('qySessionInfo', $qySessionInfo);
        $this->display();
    }

     /**
     * getWineInfo
     * 获取相应酒庄酒款信息，
     * @access public
     * @return void
     */
    public function getWineInfo($winery_id) {
    //获取缓存中的类型
        $url .= '&winery_id=' . $winery_id;

        $map = array(//企业筛选条件
            'winery_id' => $winery_id ,
            'down_shelf' => '0' //获取上架酒款
        );
        $wine_list = $this->_list(D('Wine'), $map, 4, $url);
         foreach($wine_list as $key=>$val) {
           $img = D('WineImg')->where(array('wine_id'=> $val['id']))->order('queue ASC')->limit(1)->select();
           if(!empty($img)){
                   $imglist = $this->parse_img_url($img, 'Wine');
                   $wine_list[$key]['pic_url'] = $imglist[0]['url'];
            }else{
                 $wine_list[$key]['pic_url'] = '';
            }
         }
         //echo "<pre>";print_r($wine_list);exit;
        return $wine_list;
    }
}

