<?php

/**
 * 
 */
class MfrsModel extends Model {
	/**
	 * 初始化
	 */
	public function _initialize() {
		import('@.ORG.Util.FirstABC');
		import('@.ORG.Util.String');
	}	

	/**
	 * 返回格式化后的生产商列表
	 */
	public function mfrsList() {
		$model = D('Mfrs');
		$list = $model->order('CONVERT(`cname` USING GBK) ASC')->select();
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
