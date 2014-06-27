<?php
// +----------------------------------------------------------------------
// | 58.wine.cn [ 商机网 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://wine.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------

/**
 * Angf 商品信息类库
 * @category wine
 * @package  GoodsAction.class.php
 * @author   Angf <272761906@qq.com>
 */

class GoodsAction extends CommonAction {

    /**
     * 数据初始化
     * @return void
     */
    function _initialize() {
       parent::_initialize();
       C('TMPL_ACTION_SUCCESS',TMPL_PATH.'Common/user_dispatch.html');
    }




    /**
     * 商品展示
     * @param int     $goods_id 商品ID0
     * @return void
     */
    function goodsShow($goods_id=0){
        $goods_id = $this->_get('goods_id','intval');
        $data=array();
        if($goods_id){
            $data  = $this->getGoodsDetails($goods_id);
            $Ym_UserInfo = $this->Ym_getQyInfo($data['goodsInfo']['uid']);
            $Ym_UserInfo['email'] = M('agent')->where(array('qy_id'=>$data['goodsInfo']['uid']))->getField('email'); //数据库设计有问题 —_——！
            $this->assign('Ym_UserInfo',$Ym_UserInfo);
            $this->assign('goodsData',$data);
        }
        if(empty($data['goodsInfo']))  $this->error('没有找到相关的产品');
        if($data['goodsInfo']['goods_type']==1){
            $this->display('Goods:goodsShowWineTool');
        }else{
            $this->display();
        }
    }




    /**
     * 编辑商品
     * @param int     $goods_id 商品ID0
     * @return Bool
     */
    function editGoods($goods_id=0){
        $goods_id  = $this->_get('goods_id','intval');

        if($goods_id){

            $condition['uid'] = $this->Expo_SystemInfo['_userInfo']['id'];
            $condition['goods_id'] = $goods_id;
            $goodsIsMe = M('goods')->where($condition)->getField('goods_id');
            if(!$goodsIsMe || $goodsIsMe==""){
                $this->error('此商品不是你的产品 请勿非法操作');
                return;
            }

            $data = $this->getGoodsDetails($goods_id);
            if(empty($data['goodsInfo'])) $this->error('没有找到相关的产品');

            /*处理酒的成份比例*/
            if(stripos($data['goodsInfo']['variety'],',')){
                $variety = explode(',',$data['goodsInfo']['variety']);
                foreach ($variety  as $value) {
                    $variety_row[] = explode(':', $value);
                }
            }elseif(stripos($data['goodsInfo']['variety'],':')){
                $explode = explode(':', $data['goodsInfo']['variety']);
                $variety_row[0][] =$explode[0];
                $variety_row[0][] =$explode[1];
            }else{
                $variety_row = $data['goodsInfo']['variety'];
            }
            $data['goodsInfo']['variety'] = $variety_row;
            $this->assign('goodsData',$data);
            $this->assign('description',$data['description']); //统一模版内容变量的规则

        }

        if($data['goodsInfo']['goods_type']==1){
           $this->display('Goods:editGoodsWineTool');
        }else{
         $this->display();
        }
    }




    /**
     * 获取商品详情
     * @param int     $goods_id 商品ID0
     * @return array
     */
    function getGoodsDetails($goods_id=0){
        $goods_id = $this->_get('goods_id','intval');
        $data = array();
        if($goods_id){
             $conditions['goods_id']    =$goods_id;
             $conditions['is_delete']   =0;
             $Goods = M("Goods");
             $data['goodsInfo'] = $Goods->where($conditions)->find();
             /*产品描述*/
             $GoodsDescription    = M("description");
             $data['description'] =htmlspecialchars_decode($GoodsDescription->where('relevance_id ='.$goods_id)->getField('description'));
             /*产品图片*/
             $GoodsImg         =  M("goods_img");
             $data['imgs_url'] =  $GoodsImg->field('id,img_url,sort_order')->where('goods_id='.$goods_id)->order('sort_order asc')->select();
        }
        return $data;
    }





    /**
     * 商品编辑
     * @param int     $goods_id 商品ID0
     * @return array
     */
    public function updateGoods(){
        $data = array();
        if($this->isPost()){
            //产品名称
            if( stripos($this->_post("winename"),",")){
                $winename = explode(",",$this->_post("winename"));
                $data['wineid'] = $winename['0'];
                $data['fname']  = $winename['1'];
                $data['cname']  = $winename['2'];
            }elseif(stripos($this->_post("winename"),"/")){
                $winename = explode("/",$this->_post("winename"));
                $data['fname']  = $winename['0'];
                $data['cname']  = $winename['1'];
            }else{
                $data['cname']     = $this->_post("winename");
            }

            //酒类型选择其状态 为 select，other， replace
            if($this->_post('wineTypeList_input')){
                $data['typename']   =  $this->_post('wineTypeList_input');               //other 类型
            }elseif($this->_post('winetype_input')){
                $relevance_row = explode('|',$this->_post('winetype_input')) ;          //关联类型
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




            //国家
            $country = $this->_post('country');
            if(stripos($country,"|") && $country && $country!="other"){         //select 选择
                $country_row = explode("|", $country);
                if($country_row){
                    $data['country_id']   = $country_row[0];
                    $data['country_name'] = $country_row[1].','.$country_row[2];
                }
            }elseif($country=="other"){                                        //其他 other

                $data['country_name'] = $this->_post('countryList_input');
                $data['country_id'] = 0;

            }



            // 国家 hidden input
            $country_input = $this->_post('country_input');
            if(stripos($country_input,",") && $country_input && $country!="other"){
                $country_input_exp    = explode(",", $country_input);
                $data['country_id']   = $country_input_exp[0];
                $data['country_name'] = $country_input_exp[1].','.$country_input_exp[2];


            }


            //地区
            $region = $this->_post("region");
            if($region && ($region != "other")){
               if(stripos($region,"|")){
                   $region_exp = explode("|", $region);
                   $data['region_id'] = $region_exp[0];
                   $data['region_name'] = $region_exp[1].','.$region_exp[2];
               }
            }elseif($region=="other"){
                   $data['region_name'] = $this->_post('regionListTd_input');
                   $data['region_id'] =0;
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


            if($this->_post('regionList_input')){
                $data['region_id']=0;
                $data['region_name']  = $this->_post('regionList_input');
            }


            //处理图片 产品图片入库
            $img_file   = isset($_REQUEST['img_file']) ? $_REQUEST['img_file'] : array();
            $img_queue  = isset($_REQUEST['img_queue']) ? $_REQUEST['img_queue'] : array();




            //其他选项
            $data['awards']     = $this->_post('awards');
            $data['strength']   = $this->_post('strength');
            $data['volume']     = $this->_post('volume');
            $data['price_type'] = $this->_post('price_type'); //1=面谈 0=其他
            $data['goods_price'] = $this->_post('wine_price');
            $data['currency']   = $this->_post('currency');  //0= 人民币 1=美元
            $data['minimum']    = $this->_post('minimum');
            $data['create_time']= time();
            $data['pic_url']    = reset($img_file);
            //$data['sale_status']= 1;
            $description        = $this->_post('description','daddslashes');
            $goods_id           = $this->_post('goods_id');

            $condition['goods_id'] = $goods_id;
            $condition['uid']      = $this->Expo_SystemInfo['_userInfo']['id'];
            $sure = M("goods")->where($condition)->save($data);
            if(!$sure) $this->error('请不要尝试 非法操作');



            //处理图片 产品图片入库
            if($goods_id){

                $goodsImgs = M("goods_img")->field('id,goods_id,img_url')->where('goods_id='.$goods_id)->order('id ASC')->select();

                /*
                 * 思想 当前台有数据过来 就和数据库原有的数据进行反比 not in
                 * 当 前台没有数据过来 就说明 用户删除所有图片这时候 就删除全部图片
                 */
                //删除图片(数据库) 思想是取反
                $imgs_id = implode(',',$_REQUEST['img_id']);


                if($imgs_id){
                    $g_condition['id']         = array('not in',$imgs_id);
                    $g_condition['goods_id']   = $goods_id;
                    M("goods_img")->where($g_condition)->delete();
                    //echo M("goods_img")->getLastSql();
                }elseif($imgs_id=="" || !$imgs_id){
                    $g_condition['goods_id']   = $goods_id;
                    M("goods_img")->where($g_condition)->delete();
                }


                /*
                 *思想 当前台的数据过来 和数据库的数据 不在数据库 就说明被删除
                 *同事把在数据的数据 unset($img_file[对应的ID]) 方便下面 图片上传操作
                 *
                 */
                //删除图片(文件)
                foreach ($goodsImgs as $key => $value) {

                    if(!in_array($value['id'],$_REQUEST['img_id'])){
                        $fileSepeator = explode('.', $value['img_url']);
                        unlink(C('UPLOAD_PATH') . 'Wine' . DS . 'images'.$value['img_url']);
                        unlink(C('UPLOAD_PATH') . 'Wine' . DS . 'images'.$fileSepeator['0'].'_100_thumb.'.$fileSepeator['1']);
                        unlink(C('UPLOAD_PATH') . 'Wine' . DS . 'images'.$fileSepeator['0'].'_400_thumb.'.$fileSepeator['1']);
                    }else{

                        unset($img_file[$value['id']]);
                    }
                }



                if(!empty($img_file)){

                    foreach ($img_file as $key => $file) {
                        /*$imgExist =  M("goods_")->where($condition)->save($data);*/
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
                }



                //描述分开存储
                $data=array();
                $condition ="";
                $condition['relevance_id'] = $goods_id;
                $data['desc_type']    = 0;   //产品描述类型为0
                $data['description']  = $description;

                //添加产品内容
                M("description")->where($condition)->save($data);
                if ($this->_POST('submit_and_add')) {//判断是否继续发布酒款，选择跳转页面
                     $this->redirect("Agent/release");
                } else {
                     $this->success('更新成功');

                }



            }
        }
    }







}
