<?php

/**
 * API
 */
class ApiAction extends Api{
	public function _initialize() {
		import('@.ORG.Util.Json');
	}
	function linkRedis(){
		if(!extension_loaded('redis')){
			return false;exit();
		}
		$Redis = new Redis();
		$Rediscfg = C('REDIS_CONFIG');
		try{
			$Redis->connect($Rediscfg['host'], $Rediscfg['port']);
		}catch(Exception $e){
			return false;exit();
		}
		return $Redis;
	}
	//获取国家列表
	public function getCountryList(){
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Country')->where($map)->getfield('id,fname,cname',true);
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
	}
	//获取产区列表
	public function getRegionList(){
		$country_id = intval($_POST['country_id']);
		$pid = intval($_POST['pid']);
		if($country_id) $map['country_id'] = $country_id;
		$map['pid'] = $pid;
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Region')->field('id,fname,cname')->where($map)->select();
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res));		
	}
	//获取酒列表
	public function getWineList(){
		if(empty($_POST['winery_id']) && empty($_POST['region_id'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}else{
			if(isset($_POST['winery_id'])) $winery_wineid = D('JoinWineWinery')->where(array('winery_id'=>$winery_id,'is_del'=>'-1'))->getfiled('wine_id',true);
			if(isset($_POST['region_id'])) $region_wineid = D('JoinWineRegion')->where(array('region_id'=>$region_id,'is_del'=>'-1'))->getfiled('wine_id',true);
			$mode = isset($_POST['mode']) ? (($_POST['mode'] == 0) ? 0 : 1) : 1;
			if($mode == 0){
				$map['wine_id'] = array_unique(array_merge($winery_wine_id,$region_wine_id));
			}else{
				$map['wine_id'] = array_intersect($winery_wineid,$region_wineid);
			}
		}
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Wine')->field('id,fname,cname')->where($map)->select();
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res));	
	}
	//酒款类型列表
	public function getWinetypeList(){
		$pid = intval($_POST['pid']);
		$map['pid'] = $pid;
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Winetype')->field('id,fname,cname')->where($map)->select();
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
	}
	//根据关键字搜索酒款列表
	public function searchWine(){
		$return = array();
		if(empty($_POST['kw'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		if($Redis = $this->linkRedis()){
			if(isset($_POST['kw_type']) && trim($_POST['kw_type']) == 'f'){
				$wineid = $Redis->sMembers('jiuku_winefname_wine:'.strtolower(trim($_POST['kw'])));
			}elseif(isset($_POST['kw_type']) && trim($_POST['kw_type']) == 'c'){
				$wineid = $Redis->sMembers('jiuku_winecname_wine:'.strtolower(trim($_POST['kw'])));
			}else{
				$wineid = array_unique(array_merge($Redis->sMembers('jiuku_winefname_wine:'.strtolower(trim($_POST['kw']))),$Redis->sMembers('jiuku_winecname_wine:'.strtolower(trim($_POST['kw'])))));
			}
		}else{
			if(isset($_POST['kw_type']) && trim($_POST['kw_type']) == 'f'){
				$kw_map = array('fname'=>array('like','%'.trim($_POST['kw']).'%'));
			}elseif(isset($_POST['kw_type']) && trim($_POST['kw_type']) == 'c'){
				$kw_map = array('cname'=>array('like','%'.trim($_POST['kw']).'%'));
			}else{
				$kw_map = array('_complex'=>array('fname'=>array('like','%'.trim($_POST['kw']).'%'),'cname'=>array('like','%'.trim($_POST['kw']).'%'),'_logic'=>'or'));
			}
			$wineid = D('Wine')->where($kw_map)->getfield('id',true);
		}
		if(count($wineid) == 0){
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
			exit(); 
		}
		$map['id'] = array('in',$wineid);
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Wine')->field('id,fname,cname')->where($map)->select();
		if(isset($_POST['count'])){
			if(intval($_POST['count']) <= 0){
				$return = $res;
			}else{
				$res_chunk = array_chunk($res,intval($_POST['count']));
				$return = $res_chunk[0];
			}
		}else{
			$res_chunk = array_chunk($res,20);
			$return = $res_chunk[0];
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}
	//根据国家ID获取产区级别列表
	public function getRegionlevelList(){
		if(!empty($_POST['country_id'])){
			if(intval($_POST['country_id']) > 0) $map['country_id'] = intval($_POST['country_id']);
		}
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Regionlevel')->field('id,sname,oname,cname,remark')->where($map)->limit(20)->select();
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res));	
		//echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res));	
	}
	//根据关键字|国家|父产区搜索产区列表
	public function searchRegion(){	
		$kw = trim($_POST['kw']);
		$map_k['fname'] = array('like', '%'.$kw.'%');
		$map_k['cname'] = array('like', '%'.$kw.'%');
		$map_k['_logic'] = 'or';
		$map['_complex'] = $map_k;
		if(!empty($_POST['country_id'])){
			if(intval($_POST['country_id']) > 0) $map['country_id'] = intval($_POST['country_id']);
		}
		if(!empty($_POST['pid'])){
			if(intval($_POST['pid']) > 0) $map['pid'] = intval($_POST['pid']);
		}
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Region')->field('id,fname,cname')->where($map)->limit(20)->select();
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res));		
	}
	//根据ID获取国家基本数据
	public function getCountryBasisData(){
		if(!empty($_POST['id']) && intval($_POST['id']) != 0){
			$map['id'] = intval($_POST['id']);
		}elseif(!empty($_POST['ids']) && is_array(explode(',',$_POST['ids']))){
			$map['id'] = array('in',explode(',',$_POST['ids']));
		}
		if(!$map){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Country')->where($map)->select();
		if($res){
			if(!empty($_POST['id']) && intval($_POST['id']) != 0){
				$return = array('id' => $res[0]['id'],'fname' => $res[0]['fname'],'cname' => $res[0]['cname']);
			}elseif(!empty($_POST['ids']) && is_array(explode(',',$_POST['ids']))){
				foreach($res as $val){
					$return[] = array('id' => $val['id'],'fname' => $val['fname'],'cname' => $val['cname']);
				}
			}
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}
	//根据ID获取产区基本数据
	public function getRegionBasisData(){
		if(empty($_POST['id']) || intval($_POST['id']) == 0){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		$map['id'] = intval($_POST['id']);
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Region')->where($map)->find();
		if($res){
			$return = array('id' => $res['id'],'fname' => $res['fname'],'cname' => $res['cname']);
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}
	//根据ID获取产区全部数据
	public function getRegionFullData(){
		if(empty($_POST['id']) || intval($_POST['id']) == 0){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		$map['id'] = intval($_POST['id']);
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Region')->where($map)->find();
		if($res){
			$return = array('id' => $res['id'],'fname' => $res['fname'],'cname' => $res['cname']);
			$return['country'] = NULL;
			if($country_res = D('Country')->where(array('id'=>$res['country_id'],'status'=>'1','is_del'=>'-1'))->find()){
				$return['country']['id'] = $country_res['id'];
				$return['country']['fname'] = $country_res['fname'];
				$return['country']['cname'] = $country_res['cname'];
			}
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}
	//根据ID获取酒庄的基本数据
	public function getWienryBasisData(){
		//if(empty($_POST['id']))
	}
	//根据ID获取酒款基本数据
	public function getWineBasisData(){
		if(empty($_POST['id']) || intval($_POST['id']) == 0){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		$map['id'] = intval($_POST['id']);
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Wine')->where($map)->find();
		if($res){
			$return = array('id' => $res['id'],'fname' => $res['fname'],'cname' => $res['cname']);
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}
	//根据ID获取酒款全部数据
	public function getWineFullData(){
		if(empty($_POST['id']) || intval($_POST['id']) == 0){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		$map['id'] = intval($_POST['id']);
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Wine')->where($map)->find();
		if($res){
			$return = array('id' => $res['id'],'fname' => $res['fname'],'cname' => $res['cname']);
			$return['winetype'] = NULL;
			if($winetype_res = D('Winetype')->where(array('id'=>$res['winetype_id'],'status'=>'1','is_del'=>'-1'))->find()){
				while($winetype_res){
					$return_winetype = array(
											 'id' => $winetype_res['id'],
											 'fname' => $winetype_res['fname'],
											 'cname' => $winetype_res['cname'],
											 );
					if($winetype_res['pid'] == 0) $return_winetype['pid'] = $winetype_res['pid'];
					if($winetype_res['first'] == '1') $return_winetype['pid'] = $winetype_res['first'];
					$return['winetype'][] = $return_winetype;
					$winetype_res = D('Winetype')->where(array('id'=>$winetype_res['pid'],'status'=>'1','is_del'=>'-1'))->find();
				}
				$return['winetype'] = array_reverse($return['winetype']);
			}
			$return['country'] = NULL;
			if($country_res = D('Country')->where(array('id'=>$res['country_id'],'status'=>'1','is_del'=>'-1'))->find()){
				$return['country']['id'] = $country_res['id'];
				$return['country']['fname'] = $country_res['fname'];
				$return['country']['cname'] = $country_res['cname'];
			}
			$return['region'] = NULL;
			if($region_res = D()->table('jiuku_join_wine_region A,jiuku_region B')->where('A.wine_id = '.$res['id'].' AND A.region_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.cname,B.pid')->find()){
				while($region_res){
					$return_region = array(
										   'id' => $region_res['id'],
										   'fname' => $region_res['fname'],
										   'cname' => $region_res['cname'],
										   );
					if($region_res['pid'] == 0) $return_region['pid'] = $region_res['pid'];
					$return['region'][] = $return_region;
					$region_res = D('Region')->where(array('id'=>$region_res['pid'],'status'=>'1','is_del'=>'-1'))->find();
				}
				$return['region'] = array_reverse($return['region']);
			}
			$return['winery'] = NULL;
			if($winery_res = D()->table('jiuku_join_wine_winery A,jiuku_winery B')->where('A.wine_id = '.$res['id'].' AND A.winery_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.cname')->find()){
				$return['winery']['id'] = $winery_res['id'];
				$return['winery']['fname'] = $winery_res['fname'];
				$return['winery']['cname'] = $winery_res['cname'];
			}
			$return['grape'] = NULL;
			if($grape_res = D()->table('jiuku_join_wine_grape A,jiuku_grape B')->where('A.wine_id = '.$res['id'].' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.cname,A.grape_percentage AS percent')->select()){
				foreach($grape_res as $key=>$val){
					$return['grape'][$key]['id'] = $val['id'];
					$return['grape'][$key]['fname'] = $val['fname'];
					$return['grape'][$key]['cname'] = $val['cname'];
					$return['grape'][$key]['percent'] = $val['percent'];
				}
			}
			$return['ywine_honor'] = NULL;
			$ywine_res = D('Ywine')->where(array('wine_id'=>$res['id']))->order('year DESC')->select();
			foreach($ywine_res as $key=>$val){
				if($honor_res = D()->table('jiuku_join_ywine_honor A,jiuku_honor B')->where('A.ywine_id = '.$val['id'].' AND A.honor_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.cname')->select()){
					$return['ywine_honor'][] = array(
													 'id' => $val['id'],
													 'year' => $val['year'],
													 'honor' => $honor_res,
													 );
				}
			}
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}
	
	//根据ID获取产区级别基本数据
	public function getRegionlevelBasisData(){
		$id = intval($_POST['id']);
		if($id == 0){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		$map['id'] = $id;
		$map['status'] = '1';
		$map['is_del'] = '-1';
		$res = D('Regionlevel')->where($map)->find();
		if($res){
			$return = array('id' => $res['id'],'sname' => $res['sname'],'cname' => $res['cname']);
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}
	//根据OID获取ID
	public function oidtoid(){
		$oid = intval($_POST['oid']);
		if($oid == 0){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
			exit();
		}
		$map['o9kid'] = $oid;
		$id = D('Wine')->where($map)->getfield('id');
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$id));
	}
}
