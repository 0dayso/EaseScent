<?php

class IndexModel extends CommonModel {

    protected $tableName = 'goods';
	//读取标签
	function cat_list()
	{
		return $this->get_cat();	
	}
	//读取商品列表
	function goods_list($map)
	{
		
		$list=$this->where($map)->field("click_url,goods_name,goods_price,goods_recommend,goods_img,is_logo,sort")->order(" sort desc ")->select();
		return $list;
	}
}
?>