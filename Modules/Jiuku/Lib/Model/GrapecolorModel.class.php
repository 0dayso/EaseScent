<?php

/**
 * 
 */
class GrapecolorModel extends Model {
	/**
	 * 初始化
	 */
	public function _initialize() {
	}	

	/**
	 * 返回格式化后的颜色分类列表
	 */
	public function grapecolorList() {
		$model = D('Grapecolor');
		$list = $model->select();
		foreach($list as $key=>$val){
			$list[$key]['option'] = $val['fdescription'].' ╱ '.$val['cdescription'];
		}
		return $list;
	}

} 
