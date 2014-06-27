<?php

/**
 * 权限控制外公共方法
 */
class OutAcCommonAction extends Action {
	/**
	 * 初始化
	 */
	public function _initialize() {
		import('@.ORG.Util.Input');
        import('@.ORG.Util.Image');
	}
	
    public function verify()
	{
        Image::buildImageVerify();
	}
	
	public function judgmentRepeat(){
		$dbtable = Input::getVar($_POST['dbtable']);
		$fieldname = Input::getVar($_POST['fieldname']);
		$text = Input::getVar($_POST['text']);
		$id = Input::getVar($_POST['id']) ? Input::getVar($_POST['id']) : 0;
		if($id) $map['id'] = array('neq',$id);
		$map[$fieldname] = $text;
		$map['is_del'] = '-1';
		$id = D($dbtable)->where($map)->getfield('id');
		if($id){
			echo $id;
		}
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
        $wine_id = Input::getVar($_POST['wine_id']);
        $year = Input::getVar($_POST['year']);
		$map['wine_id'] = $wine_id;
		$map['year'] = $year;
		$map['is_del'] = '-1';
		$res = D('Ywine')->where($map)->find();
		if(!$res){
			$return = array('errorCode'=>0,'errorStr'=>'');
		}else{
			$return = array('errorCode'=>20001,'errorStr'=>'年份重复！<br />重复ID:'.$res['id']);
		}
		echo json_encode($return);
	}
	/*function searchCountry(){
	    header("Content-Type:text/html; charset=utf-8");
		if (empty($_GET['term'])) exit ;
		$term = strtolower(Input::getVar($_GET["term"]));
		if (get_magic_quotes_gpc()) $term = stripslashes($term);
		$map['is_del'] = '-1';
		$res = D('Country')->field('id,fname,cname')->where($map)->select();
		$result = array();
		foreach($res as $key=>$val){
			if(strpos(strtolower($val['fname']),$term) !== false || strpos(strtolower($val['cname']),$term) !== false){
				array_push($result,array('id'=>$val['id'],'value'=>$val['fname'].' ╱ '.$val['cname']));
			}
			if(count($result) > 10)	break;
		}
		if(count($result) > 0) echo json_encode($result);
		exit();
	}
	function searchLargeRegion(){
	    header("Content-Type:text/html; charset=utf-8");
		if (empty($_GET['term'])) exit ;
		$term = strtolower(Input::getVar($_GET["term"]));
		if (get_magic_quotes_gpc()) $term = stripslashes($term);
		$map['pid'] = '0';
		$map['is_del'] = '-1';
		$res = D('Region')->field('id,fname,cname')->where($map)->select();
		$result = array();
		foreach($res as $key=>$val){
			if(strpos(strtolower($val['fname']),$term) !== false || strpos(strtolower($val['cname']),$term) !== false){
				array_push($result,array('id'=>$val['id'],'value'=>$val['fname'].' ╱ '.$val['cname']));
			}
			if(count($result) > 10)	break;
		}
		if(count($result) > 0) echo json_encode($result);
		exit();
	}
	function getHonorAutocompleteJsData(){
	    header("Content-Type:text/html; charset=utf-8");
		if (empty($_GET['term'])) exit ;
		$term = strtolower(Input::getVar($_GET["term"]));
		if (get_magic_quotes_gpc()) $term = stripslashes($term);
		$map['is_del'] = '-1';
		$res = D()->table('jiuku_honor A,jiuku_honorgroup B')->where('A.honorgroup_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.fname,A.cname,B.fname AS groupfname,B.cname AS groupcname')->select();
		$result = array();
		foreach($res as $key=>$val){
			$value = '['.$val['groupcname'].']'.$val['cname'];
			if(strpos(strtolower($value),$term) !== false){
				array_push($result,array('id'=>$val['id'],'value'=>$value));
			}
			if(count($result) > 10)	break;
		}
		echo json_encode($result);
	}
	function getAutocompleteDataJs(){
	    header("Content-Type:text/html; charset=utf-8");
		if (empty($_GET['term']) || empty($_GET['dbtable'])) exit ;
		$dbtable = Input::getVar($_GET['dbtable']);
		$dbmap = json_decode($_GET['dbmap'],true);
		$q = strtolower(Input::getVar($_GET["term"]));
		if (get_magic_quotes_gpc()) $q = stripslashes($q);
		$dbmap['is_del'] = '-1';
		if($dbtable == 'Honor'){
			$res = D()->table('jiuku_honor A,jiuku_honorgroup B')->where('A.honorgroup_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,concat(\'[\',B.cname,\']\',A.cname) as value')->select();
		}else{
			$res = D($dbtable)->where($dbmap)->field('`id`,concat(`fname`,\' ╱ \',`cname`) as value')->select();
		}
		$result = array();
		foreach ($res as $key=>$val) {
			if (strpos(strtolower($val['value']), $q) !== false) {
				array_push($result, $val);
			}
			if (count($result) > 11)
				break;
		}
		echo json_encode($result);
	}*/
	
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
