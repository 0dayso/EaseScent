<?php

/**
 * file:Search
 * brief:酒款搜索类
 * author:zhuyl
 * date:2013-1.5
 */

class SearchAction extends CommonAction {
/**
 * index
 * 搜索列表首页
 * @access public
 * @return void
 */

  //请求酒库国家列表
  public $Api_getCountry_url = "http://ajax.wine.cn/?action=U3Bscmw2SUZYaXA2enZsd21rYEp8dXx6bQ%3D%3D";



  public function index() {
    $Goods = M('goods');


        //用户提交的search 参数
        if($this->isGet() || $this->isPost())
        {
           $keyword = trim($this->_REQUEST('keyword'));
           $conditions['_string']=1;
           if($keyword)
               $conditions['_string'] = '((g.fname like "%'.$keyword.'%") OR (g.cname like "%'.$keyword.'%")) ';
           if($this->_get('cid') && $this->_get('country_name'))
               $conditions['_string'].=' AND ((g.country_id = "'.$this->_get('cid').'") OR (g.country_name like "%'.$this->_get('country_name').'%" )) ';
           if($this->_get('rid') && $this->_get('region_name'))
               $conditions['_string'].=' AND ((g.region_id = "'.$this->_get('rid').'") OR (g.region_name like "%'.$this->_get('region_name').'%" )) ';
           if($this->_get('tid') && $this->_get('typename'))
               $conditions['_string'].=' AND ((g.typeid = "'.$this->_get('tid').'") OR (g.typename like "%'.$this->_get('typename').'%" )) ';
           if($this->_get('s_tid') && $this->_get('s_typename'))
               $conditions['_string'].=' AND ((g.second_typeid = "'.$this->_get('s_tid').'") OR (g.second_typename like "%'.$this->_get('s_typename').'%" )) ';
           if($this->_get('mon') && $this->_get('mon_t'))
               $conditions['_string'].=' AND (g.goods_price >= '.intval($this->_get('mon')).' and g.goods_price <= '.intval($this->_get('mon_t')).' ) ';
           if($this->_get('company_winery')==1)
               $conditions['_string'].=' AND (g.company_type = "1") ';
        }


        $conditions['g.is_delete']   = 0;
		    $conditions['g.sale_status'] = 1;

        $count      = M()->Table('expo_goods g')->where($conditions)->count();
        $Page       = new Page($count,C('GOODS_PAGE_NUM'));
        if($keyword){$Page->parameter.=   "&keyword=".$keyword;}
        $show       = $Page->show();

        $Cache_conditions =  md5($keyword.implode(',', $_GET).$this->_get('p'));
        $fieldStr='g.goods_id,g.variety,g.goods_price,g.agent_id,g.pic_url,g.typename,g.cname,g.fname,g.price_type,g.goods_price,g.currency,g.sale_status,i.qy_name,i.qy_qq,i.qy_skype,i.qy_moblie,i.qy_address,i.id,i.qy_country,i.qy_province,i.qy_city,g.agent_grade';
        $list = M()->Table('expo_goods g')->field($fieldStr)->join(' ym_users_qy as i ON i.id = g.uid')->where($conditions)->order('g.agent_grade desc , g.goods_id desc')->cache('_SearchkList_Goods'.$Cache_conditions,'60')->limit($Page->firstRow.','.$Page->listRows)->select();

        //模板赋值
        $countryList = $this->getCountry();
        $condition   = array('country_id'=>$this->_get('cid'),'cname'=>array('NEQ',''),'sort_order'=>array('GT',0));
        $regionList  = $this->getRegion($condition);
        $winesType   = $this->getWinetype();
        $this->assign('countryList',$countryList);
        $this->assign('regionList',$regionList);
        $this->assign('winesType',$winesType);
        $this->assign('list',$list);    // 赋值数据集
        $this->assign('page',$show);    // 赋值分页输出
        $this->display();
      }



}
