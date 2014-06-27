<?php

/**
 * 
 */
class WineModel extends Model {
	/**
	 * 初始化
	 */
	public function _initialize() {
		import('@.ORG.Util.FirstABC');
		import('@.ORG.Util.String');
	}	

	/**
	 * 返回格式化后的酒款列表
	 */
	public function wineList() {
		$model = D('Wine');
		$list = $model->where(array('is_del'=>'-1'))->order('CONVERT(`cname` USING GBK) ASC')->select();
		foreach($list as $key=>$val){
			$option = FirstABC::getInitial($val['cname']).'&nbsp;&nbsp;'.String::msubstr($val['cname'],0,7).' ╱ '.String::msubstr($val['fname'],0,15);
			$option_title = $val['cname'].' ╱ '.$val['fname'];
			$return[$key]['id'] = $val['id'];
			$return[$key]['option'] = $option;
			$return[$key]['option_title'] = $option_title;
		}
		return $return;
	}
} 
