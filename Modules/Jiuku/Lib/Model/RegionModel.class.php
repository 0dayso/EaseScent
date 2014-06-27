<?php

/**
 * 
 */
class RegionModel extends Model {
	/**
	 * 初始化
	 */
	public function _initialize() {
		import('@.ORG.Util.FirstABC');
		import('@.ORG.Util.String');
	}	

	/**
	 * 返回格式化后的产区列表
	 */
	public function regionList($out_id=0,$pidarr=array(0),$l=0) {
		static $return = array();
		$map['id'] = array('neq',$out_id);
		$map['pid'] = array('in',$pidarr);
		$res = D('Region')->where($map)->order('CONVERT(`cname` USING GBK) ASC')->select();
		if($res){
			$pidarr = array();
			foreach($res as $key=>$val){
				$option = FirstABC::getInitial($val['cname']).'&nbsp;&nbsp;'.String::msubstr($val['cname'],0,7).' ╱ '.String::msubstr($val['fname'],0,15);
				$option_title = $val['cname'].' ╱ '.$val['fname'];
				$return[$l][] = array(
										   'id' => $val['id'],
										   'pid' => $val['pid'],
										   'option' => $option,
										   'option_title' => $option_title,
										   );
				$pidarr[] = $val['id'];
			}
			$this->regionList($out_id,$pidarr,$l+1);
		}
		return $return;
	}
	
	public function wineIdGetRegion($wine_id){
		$region_idarr = D('JoinWineRegion')->where(array('wine_id'=>$wine_id,'is_del'=>'-1'))->getfield('region_id',true);
		foreach($region_idarr as $key=>$val){
			$region_id = $val;
			while($region_id){
				$region_map = array('id'=>$region_id,'is_del'=>'-1');
				if($map['status']) $region_map['status'] = '1';
				$region_res = D('Region')->where($region_map)->find();
				if($region_res){
					$return[$key][] = array(
											'id' => $region_res['id'],
											'fname' => $region_res['fname'],
											'cname' => $region_res['cname'],
											);
				}
				$region_id = $region_res['pid'];
			}
			if($return[$key]) $return[$key] = array_reverse($return[$key]);
		}
		return $return;
	}
	/*function get_sort ($parent_id=0,$n=-1) 
	{ 
		global $db; 
		static $sort_list = array (); 
		$sql = "SELECT * FROM ".$db->table('article_sort')." WHERE `parent_id` = '{$parent_id}'"; 
		$res = $db->query ($sql); 
		if ($res) 
		{ 
			$n++; 
			while ($row = $db->fetch_assoc ($res)) 
			{ 
				$sql = "SELECT * FROM ".$db->table('article_sort')." WHERE `parent_id` = '{$row['sort_id']}'"; 
				$children = $db->num_rows ($sql); 
				$row['sort_name'] = str_repeat (' ',$n*4).$row['sort_name']; 
				$row['children'] = $children; 
				$sort_list[] = $row; 
				get_sort ($row['sort_id'],$n); 
			} 
		} 
		return $sort_list; 
	} */

} 
