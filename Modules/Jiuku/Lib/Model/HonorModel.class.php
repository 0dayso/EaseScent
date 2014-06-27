<?php

/**
 * 
 */
class HonorModel extends Model {

	/**
	 * 返回荣誉组列表
	 */
	public function honorGroupList() {
		$model = D('Honorgroup');
		$list = $model->where(array('is_del'=>'-1'))->order('`fname` ASC')->select();
		foreach($list as $key=>$val){
			$return[$key]['id'] = $val['id'];
			$return[$key]['option'] = $val['cname'];
		}
		return $return;
	}
	/**
	 * 返回荣誉列表
	 */
	public function honorList($out_id=0,$pidarr=array(0),$l=0) {
		static $honor_list = array();
		$map['id'] = array('neq',$out_id);
		$map['pid'] = array('in',$pidarr);
		$res = D('Honor')->where($map)->order('`fname` ASC')->select();
		if($res){
			$pidarr = array();
			foreach($res as $key=>$val){
				$honor_list[$l][] = array(
										  'id' => $val['id'],
										  'pid' => $val['pid'],
										  'honorgroup_id' => $val['honorgroup_id'],
										  'option' => $val['cname']
										  );
				$pidarr[] = $val['id'];
			}
			$this->honorList($out_id,$pidarr,$l+1);
		}
		return $honor_list;
	}

} 
