<?php

/**
 * 
 */
class WinetasteModel extends Model {

	/**
	 * 返回格式化后的口感分类列表
	 */
	public function winetasteList() {
		$model = D('Winetaste');
		$list = $model->select();
		foreach($list as $key=>$val){
			$list[$key]['option'] = $val['fdescription'].' ╱ '.$val['cdescription'];
		}
		return $list;
	}

} 
