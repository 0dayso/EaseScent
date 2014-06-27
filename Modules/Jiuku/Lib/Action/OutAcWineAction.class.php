<?php
/**
 * 酒款管理权限外方法
 */
class OutAcWineAction extends OutAcCommonAction {

	function ajaxIndexAutocompleteWine(){
		if(!isset($_POST['kw'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		$kw = strtolower(Input::getVar($_POST["kw"]));
		if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
		$map['is_del'] = '-1';
        $map['merge_id'] = 0;
		$result = array();
		$eq_res = D('Wine')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();

		//echo D('Winery')->getlastSql();
		//die();
		if($eq_res) array_push($result,$eq_res);
		$res = D('Wine')->field('id,fname,cname')->where($map)->select();
		foreach($res as $key=>$val){
			if($eq_res['id'] == $val['id']) continue;
			if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
				array_push($result,$val);
			}
		}
		$result_count = count($result);
		$result_chunk = array_chunk($result,10) ;
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('count'=>$result_count,'res'=>$result_chunk[0]))); exit();
	}
	function ajaxIndexAutocompleteWinery(){
		if(!isset($_POST['kw'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		$kw = strtolower(Input::getVar($_POST["kw"]));
		if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
		$map['is_del'] = '-1';
		$result = array();
		$eq_res = D('Winery')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();

		//echo D('Winery')->getlastSql();
		//die();
		if($eq_res) array_push($result,$eq_res);
		$res = D('Winery')->field('id,fname,cname')->where($map)->select();
		foreach($res as $key=>$val){
			if($eq_res['id'] == $val['id']) continue;
			if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
				array_push($result,$val);
			}
		}
		$result_count = count($result);
		$result_chunk = array_chunk($result,10) ;
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('count'=>$result_count,'res'=>$result_chunk[0]))); exit();
	}
	/**
	 * js酒款中文名英文名脱离input时判断是否符合规则
	 */
	public function ajaxJudgmentRepeat(){
		$field = Input::getVar($_POST['field']);
		$text = Input::getVar($_POST['text']);
		$id = Input::getVar($_POST['id']) ? Input::getVar($_POST['id']) : 0;
		if($id) $map['id'] = array('neq',$id);
		$map[$field] = $text;
		$map['merge_id'] = 0;
		$map['is_del'] = '-1';
		$id = D('Wine')->where($map)->getfield('id');
		if($id){
			echo $id;
		}
	}
	/**
	 * js判断酒款添加修改时是否符合规则
	 */
	public function ajaxCheckSubmit() {
		$id = Input::getVar($_POST['id']) ? Input::getVar($_POST['id']) : 0;
        $fname = Input::getVar($_POST['fname']);
        $cname = Input::getVar($_POST['cname']);
		$fname_res = D('Wine')->where(array('id'=>array('neq',$id),'fname'=>$fname,'merge_id'=>0,'is_del'=>'-1'))->find();
		if($fname_res){
			$return['response'] = '1000001';
			$return['msg'] = '外文名重复！<br />重复ID:'.$fname_res['id'];
			echo json_encode($return);
			exit();
		}
		if($cname){
			$cname_res = D('Wine')->where(array('id'=>array('neq',$id),'cname'=>$cname,'merge_id'=>0,'is_del'=>'-1'))->find();
			if($cname_res){
				$return['response'] = '1000002';
				$return['msg'] = '中文名重复！<br />重复ID:'.$cname_res['id'];
				echo json_encode($return);
				exit();
			}
		}
		$return['response'] = '1';
		echo json_encode($return);
	}


	function ajaxChangeCountryRange(){
		if(!isset($_POST['id'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		if(intval($_POST['id']) == 0){
			$res['region'] = D('Region')->field('id,fname,cname')->where(array('is_del'=>'-1'))->order('fname ASC')->select();
			$res['wine_count'] = D('Wine')->where(array('merge_id'=>0,'is_del'=>'-1'))->count();
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res)); exit();
		}
		$id = intval($_POST['id']);
		$res['region'] = D('Region')->field('id,fname,cname')->where(array('country_id'=>$id,'is_del'=>'-1'))->order('fname ASC')->select();
		$res['wine_count'] = D('Wine')->where(array('country_id'=>$id,'merge_id'=>0,'is_del'=>'-1'))->count();
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res)); exit();
	}
	function ajaxChangeRegionOrWineryRange(){
		if(!isset($_POST['cid']) && !isset($_POST['rid']) && !isset($_POST['wid'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		if(intval($_POST['cid']) != 0){
			$map['country_id'] = intval($_POST['cid']);
		}
		if(intval($_POST['rid']) != 0){
			$map['region_id'] = intval($_POST['rid']);
		}
		if(intval($_POST['wid']) != 0){
			$map['winery_id'] = intval($_POST['wid']);
		}
		$res['wine_count'] = D('ViewWineMerge')->where($map)->count();
		//echo(D('ViewWineMerge')->getlastSql());
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$res)); exit();
	}
	function ajaxAutocompleteWineryRangeShow(){
		if(!isset($_POST['cid']) || !isset($_POST['kw'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		if(intval($_POST['cid']) != 0){
			$map['country_id'] = intval($_POST['cid']);
		}
		$kw = strtolower(Input::getVar($_POST["kw"]));
		if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
		$map['is_del'] = '-1';
		$result = array();
		$eq_res = D('Winery')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();
		if($eq_res) array_push($result,$eq_res);
		$res = D('Winery')->field('id,fname,cname')->where($map)->order('fname ASC')->select();
		foreach($res as $key=>$val){
			if($eq_res['id'] == $val['id']) continue;
			if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
				array_push($result,$val);
			}
		}
		$result_count = count($result);
		$result_chunk = array_chunk($result,10) ;
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('count'=>$result_count,'res'=>$result_chunk[0]))); exit();
	}
	function ajaxFilter(){
		if(
		   !isset($_POST['cid']) && !isset($_POST['rid']) && !isset($_POST['wid']) &&
		   $_POST['region_same'] != 'true' && $_POST['winery_same'] != 'true' && $_POST['winefname_same'] != 'true'
		   ){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		$where = array();
		$field = array('count(*) as count');
		$group = array();
		if($_POST['cid'] != 0){
			array_push($field,'country_id');
			$group[] = 'country_id';
			$where['country_id'] = intval($_POST['cid']);
		}
		if($_POST['rid'] != 0 || $_POST['region_same'] == 'true'){
			array_push($field,'region_id');
			$group[] = 'region_id';
			if($_POST['rid'] != 0){
				$where['region_id'] = intval($_POST['rid']);
			}
			if($_POST['region_same'] == 'true'){
				$where['region_id'] = array('exp','IS NOT NULL');
			}
		}
		if($_POST['wid'] != 0 || $_POST['winery_same'] == 'true'){
			array_push($field,'winery_id');
			$group[] = 'winery_id';
			if($_POST['wid'] != 0){
				$where['winery_id'] = intval($_POST['wid']);
			}
			if($_POST['winery_same'] == 'true'){
				$where['winery_id'] = array('exp','IS NOT NULL');
			}
		}
		if($_POST['winefname_same'] == 'true'){
			$field[] = 'fname';
			$group[] = 'fname';
			$where['fname'] = array('neq','');
		}
		$field = implode(',',$field);
		$group = implode(',',$group);
		$res = D('ViewWineMerge')->field($field)->where($where)->group($group)/*->having('count(*)>1')*/->order('count(*) desc')->select();
		//echo(D('ViewWineMerge')->getlastSql());
		$result_count = count($res);
		$result_chunk = array_chunk($res,20);
		$reult_res = $result_chunk[0];
		foreach($reult_res as $key=>$val){
			if($val['country_id']){
				$country_res = D('Country')->field('fname,cname')->where(array('id'=>$val['country_id']))->find();
				$reult_res[$key]['country_fname'] = $country_res['fname'];
				$reult_res[$key]['country_cname'] = $country_res['cname'];
			}
			if($val['region_id']){
				$region_res = D('Region')->field('fname,cname')->where(array('id'=>$val['region_id']))->find();
				$reult_res[$key]['region_fname'] = $region_res['fname'];
				$reult_res[$key]['region_cname'] = $region_res['cname'];
			}
			if($val['winery_id']){
				$winery_res = D('Winery')->field('fname,cname')->where(array('id'=>$val['winery_id']))->find();
				$reult_res[$key]['winery_fname'] = $winery_res['fname'];
				$reult_res[$key]['winery_cname'] = $winery_res['cname'];
			}
		}
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('count'=>$result_count,'res'=>$reult_res))); exit();
	}
	function ajaxListOC(){
		if($_POST['country_id'] == 'null' && $_POST['region_id'] == 'null' && $_POST['winery_id'] == 'null' && $_POST['fname'] == 'null'){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		if($_POST['country_id'] != 'null'){
			$map['country_id'] = intval($_POST['country_id']);
		}
		if($_POST['region_id'] != 'null'){
			$map['region_id'] = intval($_POST['region_id']);
		}
		if($_POST['winery_id'] != 'null'){
			$map['winery_id'] = intval($_POST['winery_id']);
		}
		if($_POST['fname'] != 'null'){
			$map['fname'] = trim($_POST['fname']);
		}
		$res = D('ViewWineMerge')->field('id,fname,cname')->where($map)->select();
		$result_count = count($res);
		$result_chunk = array_chunk($res,10);
		$result_page_count = count($result_chunk);
		$result_chunk_key = $_POST['page']-1;
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('page_count'=>$result_page_count,'count'=>$result_count,'res'=>$result_chunk[$result_chunk_key]))); exit();
	}
	function ajaxMergeJustWineryGetWine(){
		$id = Input::getVar($_POST['id']);
		$page = Input::getVar($_POST['page']);
		$count = D()->table('jiuku_wine A,jiuku_join_wine_winery B')->field('A.id,A.fname,A.cname')->where('B.winery_id = '.$id.' AND A.id = B.wine_id AND B.is_del = \'-1\' AND A.merge_id = 0 AND A.is_del = \'-1\'')->count();
		$mit = 20;
		$li = ($page-1)*$mit;
		$page_count = ceil($count/$mit);
		$list = D()->table('jiuku_wine A,jiuku_join_wine_winery B')->field('A.id,A.fname,A.cname')->where('B.winery_id = '.$id.' AND A.id = B.wine_id AND B.is_del = \'-1\' AND A.merge_id = 0 AND A.is_del = \'-1\'')->limit($li.','.$mit)->select();

		echo json_encode(array('res'=>$list,'count'=>$count,'page_count'=>$page_count));
		exit;
	}
}
