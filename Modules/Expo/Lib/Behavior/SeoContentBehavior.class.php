<?php
// +----------------------------------------------------------------------
// | 58.wine.cn [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------

defined('THINK_PATH') or exit();


/**
 * 项目行为扩展：SEO 关键词注入
 * @category ANGF SEO
 * @package  ANGF
 * @subpackage  Behavior
 * @author   Angf <272761906@qq.com>
 */
class SeoContentBehavior extends Behavior {

    // 行为参数定义
    protected $options   =  array(
        'SEO_SWITCH'        => true,   //  行为参数 会转换成SEO_SWITCH配置参数
    );




    // 行为扩展的执行入口必须是run
    public function run(&$SEO){
        if(C('SEO_SWITCH')) {

            $M_A =MODULE_NAME.'_'.ACTION_NAME;
            //查询所有SEO 设置的值
            $seodata = M('pageseo')->cache('seoinfo','7200')->select();
            foreach ($seodata as $key => $value) {
              $seoContent[$value['module_action']] = $value;
            }
            //获取对应SEO 对应可以替换的数据
            $data = $this->get_seodata($M_A);

            //数据重组 换成可以替换的数据结果
            foreach($data as $key=>$value){
                $new_data["{".$key."}"]=$value;
            }
            foreach ($seoContent[$M_A] as $key => $value) {
                 $SEO[$key]  = str_replace(array_keys($new_data),array_values($new_data), $value);
            }

        }
    }


    /**
     *获取对应的 SEO 设置
     *
     **/
    private function get_seodata($M_A){
        switch ($M_A) {
            case 'Goods_goodsShow':
                $data =$this->getGoods_goodsShow($_GET['goods_id']);
                break;

            case 'Inland_index':
                $data =$this->getInland_index($_GET['agent_id']);
                break;

            case 'Inland_product':
                $data =$this->getInland_index($_GET['agent_id']);
                break;

            case 'Inland_info':
                $data =$this->getInland_index($_GET['agent_id']);
                break;

            case 'Inland_integrity':
                $data =$this->getInland_index($_GET['agent_id']);
                break;

            case 'Inland_contact':
                $data =$this->getInland_index($_GET['agent_id']);
                break;

            case 'Inland_company_view':
                $data =$this->getInland_index($_GET['agent_id']);
                break;

            case 'Inland_cooperation':
                $data =$this->getInland_index($_GET['agent_id']);
                break;


            default:
                # code...
                break;
        }
        return $data;

    }






    /**
     *获取对应的 产品详情 SEO 对应数据
     *
     **/
   private function getGoods_goodsShow($goods_id){
       if(intval($goods_id))
           return M('goods')->where(array('goods_id'=>$goods_id))->cache('Goods_goodsShow'.$goods_id,'1800')->find();
   }



    /**
     *获取对应的 企业 SEO 对应数据
     *
     **/
   private function getInland_index($agent_id){
       if(intval($agent_id)){
           $conditions['A.id']  = $agent_id;
           return  M()->Table('expo_agent as A')->field('A.qy_id,Y.*')->join(' ym_users_qy as Y ON Y.id = A.qy_id')->cache('Inland_index'.$agent_id,'1800')->where($conditions)->find();
       }
   }


}


?>