<?php

/**
 * 权限控制外公共方法
 */
class AppAccessAction extends AppAccess {
	/**
	 * 初始化
	 */
	public function _initialize() {
		import('@.ORG.Util.Input');
	}
	
	/**
	 * js判断是否重复数据
	 */
	public function checkSubmit() {
		$table_name = Input::getVar($_POST['table_name']);
		$id = Input::getVar($_POST['id']) ? Input::getVar($_POST['id']) : 0;
        $fname = Input::getVar($_POST['fname']);
        $cname = Input::getVar($_POST['cname']);
		$fname_res = D($table_name)->where(array('id'=>array('neq',$id),'fname'=>$fname,'is_del'=>'-1'))->find();
		if($fname_res){
			$return['response'] = '1000001';	
			$return['msg'] = '外文名重复！<br />重复ID:'.$fname_res['id'];
			echo json_encode($return);
			exit();
		}
		if($cname){
			$cname_res = D($table_name)->where(array('id'=>array('neq',$id),'cname'=>$cname,'is_del'=>'-1'))->find();
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
	public function checkYwineSubmit() {
		$table_name = Input::getVar($_POST['table_name']);
        $wine_id = Input::getVar($_POST['wine_id']);
        $year = Input::getVar($_POST['year']);
		$res = D($table_name)->where(array('wine_id'=>$wine_id,'year'=>array('in',$year),'is_del'=>'-1'))->select();
		if(!$res){
			$return['response'] = '1';
		}else{
			$exist_yeararr = array();
			$return['response'] = '1000001';
			foreach($res as $key=>$val){
				$exist_yeararr[] = $val['year'];
			}
			$return['msg']['yearstr'] = implode(',',$exist_yeararr);
		}
		echo json_encode($return);
	}
	
	function getAutocompleteDataJs(){
	    header("Content-Type:text/html; charset=utf-8");
		import('@.ORG.Util.Json');
		if (empty($_GET['term']) || empty($_GET['dbtable'])) exit ;
		$dbtable = Input::getVar($_GET['dbtable']);
		$dbmap = json_decode($_GET['dbmap'],true);
		$q = strtolower(Input::getVar($_GET["term"]));
		if (get_magic_quotes_gpc()) $q = stripslashes($q);
		$dbmap['is_del'] = '-1';
		$model = D($dbtable);
		$res = $model->where($dbmap)->field('`id`,concat(`fname`,\' - \',`cname`) as value')->select();
		
		$result = array();
		foreach ($res as $key=>$val) {
			if (strpos(strtolower($val['value']), $q) !== false) {
				array_push($result, $val);
			}
			if (count($result) > 11)
				break;
		}
		echo Json::json_encode_cn($result);
	}
	
	/**
	 * 清除Runtime缓存
	 */
	public function clearRuntime() {
		dump(C('UPLOAD_PATH'));
		$path = 'Modules/Jiuku/Runtime';
		if(file_exists($path)){
			echo $this->clearFile($path);exit;
		}
	}
	function clearFile($dir){
		$dh=opendir($dir);
		while ($file=readdir($dh)){
			if($file!="." && $file!=".."){
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				}else{
					$this->clearFile($fullpath);
				}
			}
		}
		closedir($dh);
		return '1';
	}
	
	
	
	/**
     * 匹配旧产区
     */
	public function goniCountry(){
		$list = D('Countryx')->select();
		$this->assign('list', $list);
		$this->display();		
	}
	public function goniRegion(){
		$list = D('Regionx')->select();
		$this->assign('list', $list);
		$this->display();		
	}
	function goniCountrybtn(){
		if(empty($_POST['id']) || $_POST['oid'] == ''){
			echo 1001;
			exit();
		}
		if(!is_numeric($_POST['oid'])){
			echo 1002;
			exit();
		}
		$is_exist = D('Region')->where(array('id'=>$_POST['oid'],'is_del'=>'-1'))->find();
		if(!is_exist){
			echo 1003;
			exit();
		}
		$res = D('Countryx')->save(array('id'=>$_POST['id'],'oid'=>$_POST['oid']));
		if($res !== false){
			echo 1;
			exit();
		}
		echo 0;
	}
	function goniRegionbtn(){
		if(empty($_POST['id']) || $_POST['oid'] == ''){
			echo 1001;
			exit();
		}
		if(!is_numeric($_POST['oid'])){
			echo 1002;
			exit();
		}
		$is_exist = D('Region')->where(array('id'=>$_POST['oid'],'is_del'=>'-1'))->find();
		if(!is_exist){
			echo 1003;
			exit();
		}
		$res = D('Regionx')->save(array('id'=>$_POST['id'],'oid'=>$_POST['oid']));
		if($res !== false){
			echo 1;
			exit();
		}
		echo 0;
	}
	
	
	
	function regionIsExist(){
	    header("Content-Type:text/html; charset=utf-8");
		$region_res = D('Region')->where(array('is_del'=>'-1'))->select();
		foreach($region_res as $key=>$val){
			if($val['pid'] == 0){
				$data = $val;
				unset($data['id'],$data['oid'],$data['pid'],$data['country_id']);
				$is_exist = D('Countryx')->where(array('ename'=>$val['fname']))->getfield('id');
				if(!$is_exist){
					$not_exist[] = array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']);
				}else{
					$exist[] = array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']);
				}
			}else{
				$region_id = $val['id'];
				$data = $val;
				unset($data['id'],$data['oid'],$data['pid'],$data['country_id']);
				$is_exist = D('Regionx')->where(array('ename'=>$val['fname']))->getfield('id');
				if(!$is_exist){
					$not_exist[] = array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']);
				}else{
					$exist[] = array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']);
				}
			}
		}
		$this->assign('exist', $exist);
		$this->assign('not_exist', $not_exist);
		$this->display();
	}
}
