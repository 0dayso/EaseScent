<?php

/**
 * 
 */
class RegionlevelModel extends Model {

	/**
	 * 返回产区级别列表
	 */
	public function regionlevelList($country_id = false) {
		$model = D('Regionlevel');
		$map['is_del'] = '-1';
		if($country_id !== false){
			$map['country_id'] = $country_id;
		}
		$list = $model->where($map)->select();
		return $list;
	}
} 
