<?php
class CommonModel extends Model {
	//读取分类标签
	function get_cat()
	{
		$cat=M("cat");	
		$cat_list=$cat->field("cat_id,cat_name,p_id")->select();
		$list=array();
		foreach($cat_list as $key=>$val)
		{
			$list[$val['cat_id']]=$val;
		}
		foreach($list as $k=>$v)
		{
			if($v['p_id']!=0)
			{
				$list[$v['p_id']]['children'][]=$v;
				unset($list[$k]);
			}
		}
		return $list;
	}
}
