<?php

/**
 * 
 */
class WinecolorModel extends Model {

	/**
	 * 返回格式化后的颜色分类列表
	 */
	public function winecolorList() {
		$model = D('Winecolor');
		$list = $model->select();
		foreach($list as $key=>$val){
			$list[$key]['option'] = $val['fdescription'].' ╱ '.$val['cdescription'];
		}
		return $list;
	}

} 
