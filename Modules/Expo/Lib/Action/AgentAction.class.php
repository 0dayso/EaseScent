<?php

/**
 * file:Agent
 * brief:代理商的相关类
 * author:zhuyl
 * date:2013-1.5
 */
class AgentAction extends YmApiAction {



    /**
     * _initialize
     * 代理商初始化函数
     * @access public
     * @return void
     */
    function _initialize() {
       parent::_initialize();
        C('TMPL_ACTION_ERROR',TMPL_PATH.'Common/user_error.html');
        C('TMPL_ACTION_SUCCESS',TMPL_PATH.'Common/user_dispatch.html');
        $this->uid =$this->Expo_SystemInfo['_userInfo']['id'];
        $this->agent_id = $this->Expo_SystemInfo['_userInfo']['agent_id'];
        $this->agent_init_data = $this->agent_inti();
   }




   /*会员中心默认页面*/
   public function index(){
        $this->assign('agent_init', $this->agent_init_data);
        $this->display();
   }


    /**
     * release
     * 代理商酒款发布
     * @access public
     * @return void
     */
    public function release() {
        $this->display('addGoods');
    }


    /**
     * release
     * 发布酒具
     * @access public
     * @return void
     */
    public function release_winetool() {
        $this->display('winetool');
    }



    /**
     * addGoods
     * 代理商发布商品
     * @access public
     * @return void
     */

    public function addGoods(){
        $data = array();
        if($this->isPost()){
            //产品名称
            if( stripos($this->_post("winename"),",")){
                $winename = explode(",",$this->_post("winename"));
                $data['wineid'] = $winename['0'];
                $data['fname']  = $winename['1'];
                $data['cname']  = $winename['2'];
            }else{
                $data['cname']     = $this->_post("winename");
            }


            //酒类型选择其状态 为 select，other， replace
            if($this->_post('wineTypeList_input')){
                $data['typename']   =  $this->_post('wineTypeList_input');               //other 类型
            }elseif($this->_post('winetype_input')){
                $relevance_row = explode('|',$this->_post('winetype_input')) ;         //关联类型
                if(stripos($relevance_row[0],",")){
                    $winetype_row = explode(",",$relevance_row[0]);
                    $data['typeid']    = $winetype_row['0'];
                    $data['typename']  = $winetype_row['1'].','.$winetype_row['2'];
                }
                if(stripos($relevance_row[1],",")){
                    $secondtype_row = explode(",",$relevance_row[1]);
                    $data['second_typeid']   = $secondtype_row['0'];
                    $data['second_typename'] = $secondtype_row['1'].",".$secondtype_row['2'];
                }
            }else{
                $winetype     = $this->_post("winetype");                    //select 选择
                if(stripos($winetype,"|")){
                    $winetype_row = explode("|",$winetype);
                    $data['typeid']    = $winetype_row['0'];
                    $data['typename']  = $winetype_row['1'].','.$winetype_row['2'];
                }
                $secondtype = $this->_post("type");
                if(stripos($secondtype,"|")){
                    $secondtype_row = explode("|",$secondtype);
                    $data['second_typeid']   = $secondtype_row['0'];
                    $data['second_typename'] = $secondtype_row['1'].",".$secondtype_row['2'];
                }

            }



            // 葡萄品种/比例
            $inp = $inp_percent = array();
            $inp = $_REQUEST['inp'];
            $inp_percent = $_REQUEST['inp_percent'];

            if(!empty($inp[0]) && !empty($inp_percent[0])){
                foreach($inp as $k=>$v){
                   if($v){
                       if($k!=0)   $data['variety'].=',';
                       $data['variety'].=  $v.':'.$inp_percent[$k];
                   }
                }
            }

            //国家select
            $country = $this->_post('country');
            if(stripos($country,"|") && $country && $country!="other"){         //select 选择
                $country_row = explode("|", $country);
                if($country_row){
                    $data['country_id']   = $country_row[0];
                    $data['country_name'] = $country_row[1].','.$country_row[2];
                }
            }elseif($country=="other"){                                        //其他 other

                $data['country_name'] = $this->_post('countryList_input');
            }


            // 国家 hidden input
            $country_input = $this->_post('country_input');
            if(stripos($country_input,",") && $country_input && $country!="other"){
                $country_input_exp    = explode(",", $country_input);
                $data['country_id']   = $country_input_exp[0];
                $data['country_name'] = $country_input_exp[1].','.$country_input_exp[2];


            }


            //地区 hidden input
            if($this->_post('region_input')){                                  //关联
                $country_info = $this->_post('region_input');
                if(stripos($country_info,"|")){
                    $country_info_exp = explode("|", $country_info);
                    $region_row = explode(',', $country_info_exp[1]);//地区
                    $data['region_id']   = $region_row[0];
                    $data['region_name'] = $region_row[1].",".$region_row[2];

                }
            }



            //地区select
            $region = $this->_post("region");
            if($region && ($region != "other")){
               if(stripos($region,"|")){
                   $region_exp = explode("|", $region);
                   $data['region_id'] = $region_exp[0];
                   $data['region_name'] = $region_exp[1].','.$region_exp[2];
               }
            }elseif($region=="other"){
                   $data['region_name'] = $this->_post('regionListTd_input');
            }

            //处理图片 产品图片入库
            $img_file   = isset($_REQUEST['img_file']) ? $_REQUEST['img_file'] : array();
            $img_queue  = isset($_REQUEST['img_queue']) ? $_REQUEST['img_queue'] : array();



            //其他选项
            $data['company_type'] = $this->Expo_SystemInfo['_userInfo']['company_type'];
            $data['awards']     = $this->_post('awards');
            $data['strength']   = $this->_post('strength');
            $data['volume']     = $this->_post('volume');
            $data['price_type'] = $this->_post('price_type'); //1=面谈 0=其他
            $data['goods_price']= $this->_post('wine_price');
            $data['currency']   = $this->_post('currency');  //0= 人民币 1=美元
            $data['minimum']    = $this->_post('minimum');
            $data['uid']        = $this->uid;
            $data['agent_id']   = $this->agent_id;
            $data['create_time']= time();
            $data['pic_url']    = $img_file[0];
            $data['sale_status']= 1;
            $data['agent_grade']= $this->Expo_SystemInfo['_userInfo']['grade'];
            if($_POST['wine_tool']) $data['goods_type']=1;

            if($this->agent_init_data['goodsUp_num'] >= C('goodsUp_num') && $this->Expo_SystemInfo['_userInfo']['grade'] < 1) $data['sale_status']= 0;
            $description        = $this->_post('description','daddslashes');
            $goods_id = M("goods")->add($data);

            //更新产区展示的权重
            if($data['region_id']) M('region')->where('jk_id='.$data['region_id'])->setInc('sort_order');

            //处理图片 产品图片入库
            if($goods_id){
                foreach ($img_file as $key => $file) {

                    $fileSepeator = explode('.', $file);
                    $desDir       = C('UPLOAD_PATH') . 'Wine' . DS . 'images' . $file;
                    $desDirSmall  = C('UPLOAD_PATH') . 'Wine' . DS . 'images' . $fileSepeator[0] . '_' . '100' . "_thumb." . $fileSepeator[1];
                    $desDirBig    = C('UPLOAD_PATH') . 'Wine' . DS . 'images' . $fileSepeator[0] . '_' . '400' . "_thumb." . $fileSepeator[1];

                    if (!is_dir(dirname($desDir))) {
                        mkdir(dirname($desDir), 0755, true);
                    }

                    rename(C('UPLOAD_PATH').'tmp'. $fileSepeator[0] . '_' . '100' . "_thumb." . $fileSepeator[1], $desDirSmall);
                    rename(C('UPLOAD_PATH').'tmp'. $fileSepeator[0] . '_' . '400' . "_thumb." . $fileSepeator[1], $desDirBig);
                    rename(C('UPLOAD_PATH').'tmp'.$file, $desDir);

                    list($width,$height) =  getimagesize($desDir);  //获取图片宽高
                    $img_data = array(
                        'img_url'    => $file,
                        'sort_order' => $img_queue[$key],
                        'goods_id'   => $goods_id,
                        'img_width'  => $width,
                        'img_height' => $height
                    );
                    $this->_insert(D('goods_img'), $img_data);
                }


                //描述分开存储
                $data=array();
                $data['relevance_id'] = $goods_id;
                $data['desc_type']    = 0;   //产品描述类型为0
                $data['description']  = $description;
                //添加产品内容
                if(M("description")->add($data)){
                        if ($this->_POST('submit_and_add')) {//判断是否继续发布酒款，选择跳转页面
                             $this->redirect("Agent/release");
                        }elseif($this->_POST('submit_and_add_tool')){
                             $this->redirect("Agent/release_winetool");
                        } else {
                            $this->success('添加成功',U('Agent/goodsManage'));
                        }
                }else{
                          $this->error('添加失败');

                }
            }
        }
    }



    /**
     * addGoods
     * 企业形象
     * @access public
     * @return void
     */
    function company_view(){
		if($this->Expo_SystemInfo['_userInfo']['grade']==0) $this->error('你的用户等级 暂时不能使用此功能',U('Index/user_grade'));
        $sundry =M('sundry');
        $company_view = $sundry->where("rel_id=".$this->agent_id." and type='company_view'")->find();
        if($this->isPost()){
          $data['rel_id']  = $this->agent_id;
          $data['tinyint'] = $this->_post('tinyint');
          $data['varchar'] = $this->_post('varchar');
          $data['text']    = $this->_post('description','daddslashes');
          $data['type']    = 'company_view';
          if($company_view){
              $sundry->where("rel_id=".$this->agent_id." and type='company_view'")->save($data);
          }else{
              $sundry->add($data);
          }
          $this->success('添加成功',U('Agent/company_view'));exit;
        }
        $this->assign('data', $company_view);
        $this->display();
    }




    /**
     * addGoods
     * 招商代理
     * @access public
     * @return void
     */
    function cooperation(){
	   	if($this->Expo_SystemInfo['_userInfo']['grade']==0) $this->error('你的用户等级 暂时不能使用此功能',U('Index/user_grade'));
        $sundry =M('sundry');
        $cooperation = $sundry->where("rel_id=".$this->agent_id." and type='cooperation'")->find();
        if($this->isPost()){
          $data['rel_id']  = $this->agent_id;
          $data['tinyint'] = $this->_post('tinyint');
          $data['varchar'] = $this->_post('varchar');
          $data['text']    = $this->_post('description','daddslashes');
          $data['type']    = 'cooperation';
          if($cooperation){
              $sundry->where("rel_id=".$this->agent_id." and type='cooperation'")->save($data);
          }else{
              $sundry->add($data);
          }
          $this->success('添加成功',U('Agent/cooperation'));exit;
        }
        $this->assign('data', $cooperation);
        $this->display();
    }




    /**
     * goodsManage
     * 代理商酒款管理
     * @access public
     * @return void
     */
    public function goodsManage() {
        $Goods = M('goods');
        import('ORG.Util.Page');

        //搜索提交

        $keyword = $this->_REQUEST('keyword');
        if($keyword){
            $sreach['fname']     = array('like',$keyword.'%');
            $sreach['cname']     = array('like',$keyword.'%');
            $sreach['_logic']    = 'or';
            $conditions['_complex'] = $sreach;
        }

        if($this->_REQUEST('goods_type')) $conditions['goods_type'] = $this->_REQUEST('goods_type'); //为酒具预留出来的

        $conditions['uid']         = $this->uid;
        $conditions['is_delete']   =0;
        $count      = $Goods->where($conditions)->count();
        $Page       = new Page($count,C('GOODS_PAGE_NUM'));
        if($keyword) $Page->parameter.=   "&keyword=".$keyword;

        $show       = $Page->show();// 分页显示输出
        $fieldStr='goods_id,agent_id,pic_url,typename,cname,fname,price_type,goods_price,currency,sale_status';
        $list = $Goods->field($fieldStr)->where($conditions)->order('goods_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        //echo $Goods->getLastSql();    //获取上一个sql
        $this->assign('list',$list);    // 赋值数据集
        $this->assign('page',$show);    // 赋值分页输出
        $this->assign('keyword',$keyword);
        if($this->Expo_SystemInfo['_userInfo']['grade'] < 1)
             $this->assign('agent_init', $this->agent_init_data);
        $this->display();               // 输出模板
    }





    /**
     * del
     * 代理商酒款删除
     * @access public
     * @return void
     */
    public function goodsDel() {
        $conditions['uid']      = $this->uid;
        $conditions['goods_id'] = $this->_get('goods_id');
        $data['is_delete']      = 1;
        $Goods = M("goods");
		//删除
        if($Goods->where($conditions)->save($data)){
            $this->success('产品删除成功');
        }else{
            $this->error('产品删除失败');
        }
		

    }






    /**
     * up
     * 商品 上下架选择
     * @access public
     * @return void
     */
    public function changeGoodsStatus() {

        $status=0; $val = '上';
        $status = $this->_get('status');
        if($status==0) $val = '下';

        if(!$this->Expo_SystemInfo['_userInfo']['grade'] &&  $this->agent_init_data['goodsUp_num'] >= C('goodsUp_num') && $status==1 )
            $this->error('sorry 未付费用户暂且 只能上架 '.C('goodsUp_num').' 酒款');

        $goods_ids=array();
        if($this->isPost() || $this->isGet()){
            $goods_ids =$_REQUEST['goods_ids'];
            if(!$goods_ids)  $this->error('请选择你要上架的酒款');
            if(!is_array($goods_ids)){
                $goods_ids =array($goods_ids) ;
            }

            //处理批量上传 超过15款
            if((count($goods_ids)+$this->agent_init_data['goodsUp_num']) > C('goodsUp_num') && $status==1)
                 $this->error('sorry 未付费用户暂且 只能上架 '.C('goodsUp_num').' 酒款');
            if(!empty($goods_ids) && is_array($goods_ids)){
                $goods_id = implode(',', $goods_ids);
                $Goods = M("goods");
                $conditions['goods_id'] = array('in',$goods_id);
                $conditions['uid']      = $this->uid;
                $data['sale_status']=$status;
                $Goods->where($conditions)->save($data);
                $this->success('商品已经'.$val.'架');
            }else{
                $this->error('请选择你要'.$val.'架的产品');

            }
        }

    }






   /**
     * info
     * 代理商基本信息
     * @access public
     * @return void
     */
    public function info() {
        $qy_list = parent::getQyInfo(); //获取企业的相关信息
        $qy_id = $qy_list['id'];
        $map = array('qy_id' => $qy_id);
        $agent = M('Agent')->where($map)->find(); //通过企业ID查询代理商ID
        $agent_id = $agent['id'];
        $img_condition = array('agent_id' => $agent_id); //获取企业图片
        $img_list = D('AgentImg')->where($img_condition)->order('queue ASC')->select();
        $img_count = D('AgentImg')->where($img_condition)->count();

        if ($this->isPost()) {

            //获取企业通行证信息
            $qy_info = array(
                'qy_name' => $_POST['qy_name'],
                'qy_introduction' => $_POST['qy_introduction'],
                'qy_reg_address' => $_POST['qy_reg_address'],
                'qy_address' => $_POST['qy_address'],
                'qy_skype' => $_POST['qy_skype'],
                'qy_qq' => $_POST['qy_qq'],
                'qy_moblie' => $_POST['qy_moblie'],
                'qy_web_address' => $_POST['qy_web_address'],
                'qy_reg_province' => $_POST['qy_reg_province'],
                'qy_reg_city' => $_POST['qy_reg_city'],
                'qy_province' => $_POST['qy_province'],
                'qy_city' => $_POST['qy_city'],
            );
            $map = array(
                'id' => $qy_list['id'],
            );
            $data = M()->Table('ym_users_qy')->where($map)->save($qy_info); //修改企业通行证信息
            //将邮箱等信息插入Agent表
            $agent_info = array(
                'email' => $_POST['agent_email'],
                'qy_id' => $qy_id,
                'qy_name' => $_POST['qy_name'], //企业的id，name存入Agent表
                'pic_url' => $_REQUEST['img_file'][0], //首图存入Agent表
            );
            $agent_map = array(
                'qy_id' => $qy_id,
            );

            M("Agent")->where($agent_map)->save($agent_info); //修改相关信息

            /**
             * @brief  保存企业照片
             */
            $img_file = isset($_REQUEST['img_file']) ? $_REQUEST['img_file'] : array();
            $img_queue = isset($_REQUEST['img_queue']) ? $_REQUEST['img_queue'] : array();

            M("AgentImg")->where("agent_id=$agent_id")->delete();

            foreach ($img_file as $key => $file) {
                $fileSepeator = explode('.', $file);
                $desDir = C('UPLOAD_PATH') . 'Agent' . DS . 'images' . $file;
                $desDir200 =  C('UPLOAD_PATH') . 'Agent' . DS . 'images' . $fileSepeator[0] . '_' . '200' . "_thumb." . $fileSepeator[1];
                $desDir500 =  C('UPLOAD_PATH') . 'Agent' . DS . 'images' . $fileSepeator[0] . '_' . '500' . "_thumb." . $fileSepeator[1];
                if (!is_dir(dirname($desDir))) {
                    mkdir(dirname($desDir), 0755, true);
                }
              rename(C('UPLOAD_PATH').'tmp'. $fileSepeator[0] . '_' . '200' . "_thumb." . $fileSepeator[1], $desDir200);
              rename(C('UPLOAD_PATH').'tmp'. $fileSepeator[0] . '_' . '500' . "_thumb." . $fileSepeator[1], $desDir500);
              rename(C('UPLOAD_PATH').'tmp'.$file, $desDir);


                $img_data = array(
                    'url' => $file,
                    'queue' => $img_queue[$key],
                    'agent_id' => $agent_id,
                );
                $id = $this->_insert(D('AgentImg'), $img_data);
            }

            //添加信息成功
            $this->redirect("Agent/introduce?agent_id=$agent_id");
        }

        $this->assign('qy_list', $qy_list);
        $this->assign('agent_id', $agent_id);
        $this->assign('agent', $agent);
        $img_list = $this->parse_img_url($img_list, 'Agent'); //print_r($img_list);exit;  //吐血·····
        $this->assign('img_list', $img_list);
        $this->assign('img_count', $img_count);
        $this->display();
    }





    /**
     * @brief  格式化展会相关图片url
     *
     * @params $type {string} wine/agent
     *
     * @return
     */
    public function parse_img_url($data, $type) {
        if (!is_array($data))
            return C('UPLOAD_WWWPATH') . $type . '/' . 'images' . '/' . $data;
        foreach ($data as $key => $dt) {
            $data[$key]['path'] = $dt['url'];
            $data[$key]['url'] = C('UPLOAD_WWWPATH') . $type . '/' . 'images' . '/' . $dt['url'];
        }
        return $data;
    }




    /**
     * introduce
     * 代理商公司介绍
     * @access public
     * @return void
     */
    public function introduce() {
        $condition['id']  = $this->agent_id;
        $description      = M('Agent')->where($condition)->getField('description');
        if ($this->isPost()) {
            $data['description'] =  $this->_post('description','daddslashes');
            M("Agent")->where($condition)->save($data); //修改相关信息
            $this->redirect("Agent/release");
        }
        $this->assign('description', $description);
        $this->display();
    }








    /*
     * 上传图片
     */

    public function upload() {
        $this->_uploads();
    }


    /**
     * uploadWine
     * 上传酒款图片
     * @access public
     * @return void
     */
    public function uploadWine() {
        $wine_id = $_REQUEST['wine_id'];
        $result = $this->_newuploads();
        if ($result['error'] == 0) {
            $img_data = array(
                'wine_id' => $wine_id,
                'url' => $result['url'],
                'queue' => -1,
                'img_width' => $result['width'],
                'img_height' => $result['height'],
                'create_time' => time()
            );

            $condition = array(
                'wine_id' => $wine_id,
            );
            $id = $this->_insert(D('WineImg'), $img_data);
            $result['id'] = $id;
        }
        echo json_encode($result);
    }

    /**
     * delImg
     * 删除代理商图片
     * @access public
     * @return void
     */
    public function delImg() {
        $id = $_REQUEST['id'];
        $map = array(
            'id' => $id,
        );
        $img_data = M('AgentImg')->where($map)->find(); //查询代理商图片
        if ($img_data['queue'] == 1) { //判断删除的图片是否为第一张
            M('AgentImg')->where($map)->delete(); // 删除相应id的图片数据
            $allImg = $_REQUEST['allImg'];
            if ($allImg) {
                $allImg = explode(",", $allImg);
                sort($allImg);
                $value = array(
                    'queue' => 1,
                );
                M('AgentImg')->where("id=$allImg[0]")->save($value); // 将第2张图片设置为第首图
            }
        }
        M('AgentImg')->where($map)->delete(); // 删除相应id的图片数据
    }



    /**
     * delWineImg
     * 删除酒款图片
     * @access public
     * @return void
     */
    public function delWineImg() {
        $id = $_REQUEST['id'];
        $map = array(
            'id' => $id,
        );
        $img_data = M('WineImg')->where($map)->find(); //查询酒款图片
        if ($img_data['queue'] == 1) {//判断删除的图片是否为第一张
            M('WineImg')->where($map)->delete(); // 删除相应id的图片
            $allImg = $_REQUEST['allImg'];
            if ($allImg) {
                $allImg = explode(",", $allImg);
                sort($allImg); //将图片进行排序
                $value = array(
                    'queue' => 1,
                );
                M('WineImg')->where("id=$allImg[0]")->save($value); // 将第2张图片设置为第首图
            }
        }
        M('WineImg')->where($map)->delete(); // 删除相应id的图片数据
    }


    /*企业信息产品信息 初始化*/
    public function agent_inti(){
		//企业商品个数
        $map['agent_id']= $this->agent_id;
		$map['is_delete'] = 0;
        $agent_init['goods_num'] = M('goods')->where($map)->count();
	    //企业已经上架的商品个数
        $map['sale_status'] = 1;
        $agent_init['goodsUp_num'] = M('goods')->where($map)->count();
        return $agent_init;
    }

}
