<?php

/**
 * 
 */
class WinetypeModel extends Model {
	
	/**
	 * 返回酒类型列表
	 */
	public function winetypeList(){
		$res = D('Winetype')->where(array('pid'=>0,'is_del'=>'-1'))->select();
		foreach($res as $key=>$val){
			$res[$key]['son_res'] = D('Winetype')->where(array('pid'=>$val['id'],'is_del'=>'-1'))->select();
			foreach($res[$key]['son_res'] as $k=>$v){
				$res[$key]['son_res'][$k]['son_res'] = D('Winetype')->where(array('pid'=>$v['id'],'is_del'=>'-1'))->select();
			}
		}
		return $res;
	}

} 
