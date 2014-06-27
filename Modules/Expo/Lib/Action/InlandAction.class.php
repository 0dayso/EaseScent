<?php

/**
 * file:Indland
 * brief:国内馆的相关类
 * author:zhuyl
 * date:2013-1.5
 */
class InlandAction extends CommonAction {


    /**
     * 系统默认加载 企业相关的信息
     */
    public function _initialize() {
        parent::_initialize();

        $agent_id   = intval($this->_get("agent_id"));                             //通过搜索页面，获取代理商的ID
        if($agent_id){
            $agent_info = $this->getAgentInfo($agent_id);                              //根据代理商的ID，获取相关信息
            if(empty($agent_info) || !$agent_info) $this->error('没有找到相关的企业');
            $qy_info    = $this->getQyInfo($agent_info['qy_id']);
            $qy_info['email'] = $agent_info['email'];
            $this->assign('agent_info', $agent_info);
            $this->assign('qy_info', $qy_info);
        }
    }




    /**
     * company_view
     * 企业形象
     * @access public
     * @return void
     */
    public function company_view($agent_id=0){
        $company_view = M('sundry')->where("rel_id=".intval($agent_id)." and type='company_view'")->find();
        $company_view['text'] = htmlspecialchars_decode($company_view['text']);
        $this->assign('data',$company_view);
        $this->display();
    }


    /**
     * cooperation
     * 招商代理
     * @access public
     * @return void
     */
    public function cooperation($agent_id=0){
        $cooperation = M('sundry')->where("rel_id=".$agent_id." and type='cooperation'")->find();
        $cooperation['text'] = htmlspecialchars_decode($cooperation['text']);
        $this->assign('data',$cooperation);
        $this->display();
    }



    /**
     * index
     * 国内馆酒款首页
     * @access public
     * @return void
     */
    public function index() {
        $goodsList = $this->getGoodsList($this->agent_info['qy_id']);
        $this->assign('goodsList', $goodsList);
        $this->display();
    }



    /**
     * product
     * 国内馆产品
     * @access public
     * @return void
     */
    public function product() {
        $goodsList = $this->getGoodsList($this->agent_info['qy_id']);
        $this->assign('goodsList', $goodsList);
        $this->display();
    }




    /**
     * info
     * 企业信息
     * @access public
     * @return void
     */
    public function info() {
        $this->agent_info['description'] =htmlspecialchars_decode($this->agent_info['description']);
        $this->assign('agent_info', $this->agent_info);
        $this->display();
    }





    /**
     * integrity
     * 诚信档案
     * @access public
     * @return void
     */
    public function integrity() {
        $this->display();
    }



    /**
     * contact
     * 联系方式
     * @access public
     * @return void
     */
    public function contact($agent_id=0) {
        $this->display();
    }



    /**
     * business
     * 关于逸香
     * @access public
     * @return void
     */
    public function business(){
        $this->display();
    }


    /**
     * Angf 获取商家商品机列表
     * @category wine
     * @package  GoodsAction.class.php
     * @author   Angf <272761906@qq.com>
     */
    function getGoodsList($uid=0){
        $data  = array();
        $uid   = intval($uid);
        if($uid<1) return $data;

        $Goods = M('goods');
        import('ORG.Util.Page');
        //搜索提交
        $keyword = $this->_REQUEST('keyword');
        if($keyword){
            $sreach['fname']     = array('like','%'.$keyword.'%');
            $sreach['cname']     = array('like','%'.$keyword.'%');
            $sreach['_logic']    = 'or';
            $conditions['_complex'] = $sreach;
        }
        $conditions['uid']         = $uid;
        $conditions['is_delete']   = 0;
        $conditions['sale_status'] = 1;
        $count      = $Goods->where($conditions)->count();
        $Page       = new Page($count,C('GOODS_PAGE_NUM'));
        if($keyword) $Page->parameter.=   "&keyword=".$keyword;
        $data['pageShow']       = $Page->show();// 分页显示输出
        $fieldStr='goods_id,agent_id,pic_url,typename,cname,variety,fname,price_type,goods_price,currency,sale_status,country_name,region_name,awards,volume,minimum';
        $data['list'] = $Goods->field($fieldStr)->where($conditions)->order('goods_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        return $data;
    }




    /**
     * getQyInfo
     * 获取企业信息
     * @access public
     * @return void
     */
    public function getQyInfo($qy_id) {
        $map = array('id' => $qy_id);
        $qy_info = M()->Table('ym_users_qy')->where($map)->find();
        return $qy_info;
    }


    /**
     * getAgentInfo
     * 获取代理商信息
     * @access public
     * @return void
     */
    public function getAgentInfo($agent_id) {
        $map['id'] =  $agent_id;
        $agent_info = M('Agent')->where($map)->find();
        return $agent_info;
    }







    /**
     * getWineInfo
     * 获取相应代理商酒款信息，
     * @access public
     * @return void
     */
    /*
    public function getWineInfo($agent_id) {
        //获取缓存中的类型
        $url .= '&agent_id=' . $agent_id;

        $map = array(//企业筛选条件
            'agent_id' => $agent_id,
            'down_shelf' => '0' //获取上架酒款
        );
        $wine_list = $this->_list(D('Wine'), $map, 9, $url);
        foreach ($wine_list as $key => $val) {
            $img = D('WineImg')->where(array('wine_id' => $val['id']))->order('queue ASC')->limit(1)->select();
            if (!empty($img)) {
                $imglist = $this->parse_img_url($img, 'Wine');
               // list($width,$height) = getimagesize($imglist[0]['url']);
                $wine_list[$key]['pic_url'] = $imglist[0]['url'];
                $wine_list[$key]["width"] = $img[0]["img_width"];
                $wine_list[$key]["height"] = $img[0]["img_height"];
            } else {
                $wine_list[$key]['pic_url'] = '';
            }
            if($wine_list[$key]['cname']==$wine_list[$key]['fname']){
                $wine_list[$key]['eq'] = 1;
            }
        }

        return $wine_list;
    }
    */



}

