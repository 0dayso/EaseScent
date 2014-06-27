<?php

/**
 * 
 */
class GrapeModel extends Model {
	/**
	 * 初始化
	 */
	public function _initialize() {
		import('@.ORG.Util.FirstABC');
		import('@.ORG.Util.String');
	}	

	/**
	 * 返回格式化后的葡萄品种列表
	 */
	public function grapeList() {
		$model = D('Grape');
		$list = $model->order('CONVERT(`cname` USING GBK) ASC')->select();
		foreach($list as $key=>$val){
			$option = FirstABC::getInitial($val['cname']).'&nbsp;&nbsp;'.String::msubstr($val['cname'],0,7).' ╱ '.String::msubstr($val['fname'],0,15);
			$option_title = $val['cname'].' ╱ '.$val['fname'];
			$return[$key]['id'] = $val['id'];
			$return[$key]['color_id'] = $val['color_id'];
			$return[$key]['option'] = $option;
			$return[$key]['option_title'] = $option_title;
		}
		return $return;
	}

} 
