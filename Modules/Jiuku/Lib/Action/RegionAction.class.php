<?php

/**
 * 产区管理
 */
class RegionAction extends CommonAction {

	/**
	 * 产区列表
	 */
	public function index() {
        $country_id = Input::getVar($_REQUEST['country_id']);
        $keyword = Input::getVar($_REQUEST['keyword']);
        $status = Input::getVar($_REQUEST['status']);
		$map = array();
		$url = '';
        if($country_id) {
            $map['country_id'] = $country_id;
            $url .= '&country_id='.$country_id;
        }
        if($keyword) {
            $map_k['fname'] = array('like', '%'.$keyword.'%');
            $map_k['cname'] = array('like', '%'.$keyword.'%');
            $map_k['aname'] = array('like', '%'.$keyword.'%');
            $map_k['_logic'] = 'or';
			$map['_complex'] = $map_k;
            $url .= '&keyword=' . $keyword;
        }
        if($status) {
            $map['status'] = $status;
            $url .= '&status='.$status;
        }
		$list = $this->_list(D('Region'), $map, 15, $url);
		foreach($list as $key=>$val){
			//所属国家
			if($val['country_id'] != 0){
				$list[$key]['country_res'] = D('Country')->where(array('id'=>$val['country_id'],'is_del'=>'-1'))->find();
			}
			//所属产区
			if($val['pid'] > 0){
				$list[$key]['pregion_res'] = D('Region')->where(array('id'=>$val['pid'],'is_del'=>'-1'))->find();
			}
			//产区级别
			if($val['regionlevel_id']){
				$list[$key]['regionlevel_res'] = D('Regionlevel')->where(array('id'=>$val['regionlevel_id'],'is_del'=>'-1'))->find();
			}
		}
		foreach($list as $key=>$val){
			$list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
			$list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
		}
		$this->assign('list', $list);
		$this->assign('countryList', D('Country')->countryList());
		$this->display();
	}
	/**
	 * 产区增加
	 */
	public function add() {
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
		if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			$_POST['term'] = str_replace(array('：','；'),array(':',';'),Input::getVar($_POST['term']));
			$_POST['map_img'] = $_POST['map_img'] ? $_POST['map_img'] : '';
			$region_id = $this->_insert(D('Region'));
			if($region_id){
				foreach($_POST['joingrape_grape_id'] as $key=>$val){
					$join_grape_data = array(
											 'region_id' => $region_id,
											 'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
											 'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
											 );
					$this->_insert(D('JoinRegionGrape'),$join_grape_data);
				}
				foreach($_POST['joinhonor_honor_id'] as $key=>$val){
					$join_honor_data = array(
											 'region_id' => $region_id,
											 'honor_id' => Input::getVar($_POST['joinhonor_honor_id'][$key]),
											 );
					$this->_insert(D('JoinRegionHonor'),$join_honor_data);
				}
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'region_id' => $region_id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('RegionImg'),$img_data);
				}
				$img_text = $_POST['fname'];
				if($_POST['cname']) $img_text .= '('.$_POST['cname'].')';
				$this->_TextCretaeImg($img_text,C('UPLOAD_PATH').'Region/name/',md5($region_id));
				$this->_jumpGo('产区添加成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('产区添加失败', 'error');
		}
		$this->assign('regionlevelList',D('Regionlevel')->regionlevelList());
		$this->assign('rpp',$rpp);
		$this->display();
	}
	/**
	 * 产区编辑
	 */
	public function edit() {
        $id = Input::getVar($_REQUEST['id']);
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
        if(!$id) {
            $this->_jumpGo('参数为空!', 'error');
        }
        if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			$_POST['term'] = str_replace(array('：','；'),array(':',';'),Input::getVar($_POST['term']));
			$_POST['map_img'] = $_POST['map_img'] ? $_POST['map_img'] : '';
			$is_success = $this->_update(D('Region'));
			if($is_success){
				foreach($_POST['joingrape_grape_id'] as $key=>$val){
					$join_grape_data = array(
											 'region_id' => $id,
											 'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
											 'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
											 );
					$this->_insert(D('JoinRegionGrape'),$join_grape_data);
				}
				D('JoinRegionGrape')->where(array('id'=>array('in',explode(',',$_POST['del_joingrape_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				foreach($_POST['joinhonor_honor_id'] as $key=>$val){
					$join_honor_data = array(
											 'region_id' => $id,
											 'honor_id' => Input::getVar($_POST['joinhonor_honor_id'][$key]),
											 );
					$this->_insert(D('JoinRegionHonor'),$join_honor_data);
				}
				foreach($_POST['upd_img_id'] as $key=>$val){
					$upd_img_data = array(
										  'id' => Input::getVar($_POST['upd_img_id'][$key]),
										  'region_id' => $id,
										  'description' => Input::getVar($_POST['upd_img_description'][$key]),
										  'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
										  'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
										  );
					$this->_update(D('RegionImg'),$upd_img_data);
				}
				D('RegionImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				$img_text = $_POST['fname'];
				if($_POST['cname']) $img_text .= '('.$_POST['cname'].')';
				$this->_TextCretaeImg($img_text,C('UPLOAD_PATH').'Region/name/',md5($id));
				$this->_jumpGo('产区编辑成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('产区编辑失败', 'error');
        }
        $region_res = D('Region')->where(array('id'=>$id))->find();
		//所属国家
		$region_res['country_res'] = D('Country')->where(array('id'=>$region_res['country_id'],'is_del'=>'-1'))->find();
		//所属产区
		$region_res['pregion_res'] = D('Region')->where(array('id'=>$region_res['pid'],'is_del'=>'-1'))->find();
		//跨区
		$region_res['pregion2_res'] = D('Region')->where(array('id'=>$region_res['pid2'],'is_del'=>'-1'))->find();
		//葡萄品种
		$region_res['join_grape_res'] = D()->table('jiuku_join_region_grape A,jiuku_grape B')->where('A.region_id = '.$id.' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.region_id,A.grape_id,B.cname AS grape_cname,B.fname AS grape_fname,A.grape_percentage')->select();
		//图片
		$region_res['img_res'] = D('RegionImg')->where(array('region_id'=>$id,'is_del'=>'-1'))->select();
		$this->assign('res', $region_res);
		$this->assign('show_regionlevelList',D('Regionlevel')->regionlevelList($region_res['country_id']));
		$this->assign('regionlevelList',D('Regionlevel')->regionlevelList());
		$this->assign('rpp',$rpp);
        $this->display();
	}

	/**
	 * 删除
	 */
	public function del() {
        //获取id
        $id = Input::getVar($_REQUEST['id']);
        //获取批量删除
        $ids = $_REQUEST['ids'];
        $model = D('Region');
        if($id) {
            $map = array('id' => $id);
        } elseif($ids) {
            $map = array('id' => array('in', $ids));
        }
        if($map) {
			$data = array(
						  'is_del' => '1',
						  'last_edit_time' => time(),
						  'last_edit_aid' => $_SESSION['admin_uid'],
						  );
            $model->where($map)->save($data);
            $this->_jumpGo('删除成功','succeed', Url('index'));
        }
        $this->_jumpGo('删除失败，参数为空', 'error');
	}

    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $id = Input::getVar($_GET['id']);
        $status = Input::getvar($_GET['status']);
		$data = array(
					  'id' => $id,
					  'status' => $status,
					  );
		$this->_update(D('Region'),$data);
		$this->_jumpGo('ID为'.$id.'的产区状态更改成功', 'succeed', Url('index'));
    }

	/**
     * 上传文件
     */
	public function upload(){
		import('@.ORG.Util.Upload');
		$upload = new Upload();
		if($_GET['type'] == 'uploads'){
			$cfg = array(
				'ext' => C('UPLOAD_ALLOW_EXT'),
				'size' => C('UPLOAD_MAXSIZE'),
				'path' => C('UPLOAD_PATH').'Region/uploads/',
			);
			$upload->config($cfg);
			$rest = $upload->uploadFile('imgFile');
			if($rest['errno']) {
				$result = array(
					'error' => 1,
					'message' => $upload->error(),
				);
				$this->_ajaxDisplay($result);
			}
			$result = array(
				'error' => 0,
				'url' => C('DOMAIN.UPLOAD') . 'Jiuku/Region/uploads/' . $rest['path'],
				'filename' => $rest['path'],
			);
			$this->_ajaxDisplay($result);
		}elseif($_GET['type'] == 'images'){
			$cfg = array(
				'ext' => C('UPLOAD_ALLOW_EXT'),
				'size' => C('UPLOAD_MAXSIZE'),
				'path' => C('UPLOAD_PATH').'Region/uploads/',
			);
			$upload->config($cfg);
			$rest = $upload->uploadFile('imgFile');
			if($rest['errno']) {
				$result = array(
					'error' => 1,
					'message' => $upload->error(),
				);
				$this->_ajaxDisplay($result);
			}
			$result = array(
				'error' => 0,
				'url' => C('DOMAIN.UPLOAD') . 'Jiuku/Region/uploads/' . $rest['path'],
				'filename' => $rest['path'],
			);
			//缩略图
			import('@.ORG.Util.Image');
			$image = new Image();
			$file = C('UPLOAD_PATH').'Region/uploads/'.$result['filename'];
			$image->thumb2($file,$file.'.80.60',80,60);
			$image->thumb2($file,$file.'.260.195',260,195);
			$this->_ajaxDisplay($result);
		}elseif($_GET['type'] == 'maps'){
			$cfg = array(
				'ext' => C('UPLOAD_ALLOW_EXT'),
				'size' => C('UPLOAD_MAXSIZE'),
				'path' => C('UPLOAD_PATH').'Region/maps/',
			);
			$upload->config($cfg);
			$rest = $upload->uploadFile('mapimgFile');
			if($rest['errno']) {
				$result = array(
					'error' => 1,
					'message' => $upload->error(),
				);
				$this->_ajaxDisplay($result);
			}
			$result = array(
				'error' => 0,
				'url' => C('DOMAIN.UPLOAD') . 'Jiuku/Region/maps/' . $rest['path'],
				'filename' => $rest['path'],
			);
			$this->_ajaxDisplay($result);
		}
	}

	/**
     * 产区结构
     */
	public function regionStruct(){
		$type = intval($_GET['type']);
		$html = '';
		$json_res = S('jiuku_country_and_region_struct_json_data');
		$res_html = S('jiuku_country_and_region_struct_data_html');
		if(!$json_res || !$res_html || $type == 1){
			$res = D('Country')->field('id,fname,cname')->where(array('is_del'=>'-1'))->select();
			$html .= '<table border="1">';
			foreach($res as $key=>$val){
				$res[$key]['son'] = D('Region')->field('id,fname,cname')->where(array('country_id'=>$val['id'],'pid'=>0,'is_del'=>'-1'))->select();
				$res[$key]['son_count'] = count($res[$key]['son']);
				$html .= '<tr><td width="200px"><a href="'.Url('Jiuku/Country/edit').'&id='.$val['id'].'">'.$val['fname'].'╱'.$val['cname'].'('.$res[$key]['son_count'].')</a></td><td>';
				$html .= '<table border="1" style="width:100%;">';
				foreach($res[$key]['son'] as $k=>$v){
					$this->regionStruct1(&$res[$key]['son'][$k],&$html,$color_mark);
				}
				$html .= '</table>';
				$html .= '</td></tr>';
			}
			$html .= '</table>';
			S('jiuku_country_and_region_struct_json_data',json_encode($res));
			S('jiuku_country_and_region_struct_data_html',$html);
		}else{
			$res = json_decode($json_res,true);
			$html = $res_html;
		}
		$this->assign('res', $res);
		$this->assign('html', $html);
        $this->display();
	}
	function regionStruct1($val,$html,$color_mark){
		$val['son'] = D('Region')->field('id,fname,cname')->where(array('pid'=>$val['id'],'is_del'=>'-1'))->select();
		$val['son_count'] = count($val['son']);
		$html .= '<tr><td width="500px"><a href="'.Url('edit').'&id='.$val['id'].'">'.$val['fname'].'╱'.$val['cname'].'('.$val['son_count'].')</a></td><td>';
		$html .= '<table border="1" style="width:100%;">';
		foreach($val['son'] as $k=>$v){
			$this->regionStruct1(&$val['son'][$k],&$html,$color_mark);
		}
		$html .= '</table>';
		$html .= '</td></tr>';
	}
	function regionStructCache(){

	}

	/**
     * 产区级别结构
     */
	public function regionLevelStruct(){
	    header("Content-Type:text/html; charset=utf-8");
		$country_res = D('Country')->where(array('is_del'=>'-1'))->select();
		foreach($country_res as $key=>$val){
			if($level_res = D('Regionlevel')->where(array('country_id'=>$val['id'],'is_del'=>'-1'))->select()){
				$res[] = array(
							   'cname' => $val['cname'],
							   'fname' => $val['fname'],
							   'son_res' => $level_res,
							   );
			}
		}
		$this->assign('res',$res);
        $this->display();
	}
	public function iosregiondata(){
		D()->query('TRUNCATE TABLE `jiuku_ios_region`');
    	$country_res  = D('Country')->where(array('status'=>'1','is_del'=>'-1'))->field('id,fname,ename,cname')->select();
		foreach($country_res as $key=>$val){
			$country_data = array(
								  'fname' => $val['fname'],
								  'ename' => $val['ename'],
								  'cname' => $val['cname'],
								  'wid' => $val['id'],
								  'tier' => 1,
								  'tpid' => 0,
								  'pid' =>0,
								  );
    	    $country_id = D('IosRegion')->add($country_data);
			$region1_res = D('Region')->where(array('status'=>'1','is_del'=>'-1','country_id'=>$val['id'],'pid'=>0))->field('id,fname,ename,cname')->select();
    	    foreach($region1_res as $k=>$v){
					$region1_data = array(
										  'fname' => $v['fname'],
										  'ename' => $v['ename'],
										  'cname' => $v['cname'],
										  'wid' => $v['id'],
										  'tier' => 2,
										  'tpid' => $country_id,
										  'pid' =>0,
										  );
					$region1_id = D('IosRegion')->add($region1_data);
    	        	$region2_res = D('Region')->where(array('status'=>'1','is_del'=>'-1','country_id'=>$val['id'],'pid'=>$v['id']))->field('id,fname,ename,cname')->select();
            		foreach($region2_res as $k2=>$v2){
						$region2_data = array(
										  	'fname' => $v2['fname'],
										  	'ename' => $v2['ename'],
										  	'cname' => $v2['cname'],
										  	'wid' => $v2['id'],
										  	'tier' => 3,
										  	'tpid' => $country_id,
										  	'pid' =>$region1_id,
										  	);
                		D('IosRegion')->add($region2_data);
            	}
        	}
		}
	}

	/**
     * 生成静态页
     */
	public function html(){
		if($_POST['how'] === 'ini'){
			$all_cid = range(1,D('Country')->max('id'));
			$all_rid = range(1,D('Region')->max('id'));
			$cyid = D('Country')->where(array('status'=>'1','is_del'=>'-1'))->getfield('id',true);
			$ryid = D('Region')->where(array('status'=>'1','is_del'=>'-1'))->getfield('id',true);
			$cnid = array_diff($all_cid,$cyid);
			$rnid = array_diff($all_rid,$ryid);
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('cyc'=>count($cyid),'cnc'=>count($cnid),'cyid'=>implode(',',$cyid),'cnid'=>implode(',',$cnid),'ryc'=>count($ryid),'rnc'=>count($rnid),'ryid'=>implode(',',$ryid),'rnid'=>implode(',',$rnid)))); exit();
		}elseif($_POST['how'] === 'cyid'){
			$id = explode(',',$_POST['id']);
			foreach($id as $key=>$val){
				$filename = A('Template')->country_detail($val);
				if(file_exists($filename)){
					$sid[] = $val;
				}else{
					$fid[] = $val;
				}
			}
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('scount'=>count($sid),'fcount'=>count($fid),'sid'=>implode(',',$sid),'fid'=>implode(',',$fid)))); exit();
		}elseif($_POST['how'] === 'ryid'){
			$id = explode(',',$_POST['id']);
			foreach($id as $key=>$val){
				$filename = A('Template')->region_detail($val);
				if(file_exists($filename)){
					$sid[] = $val;
				}else{
					$fid[] = $val;
				}
			}
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('scount'=>count($sid),'fcount'=>count($fid),'sid'=>implode(',',$sid),'fid'=>implode(',',$fid)))); exit();
		}elseif($_POST['how'] === 'index'){
			$filename = A('Template')->region_index();
			$is_exist = file_exists($filename) ? 1 : 0;
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('is_exist'=>$is_exist))); exit();
		}elseif($_POST['how'] === 'cnid'){
			$id = explode(',',$_POST['id']);
			foreach($id as $key=>$val){
				$filename = C('SHTML_SAVE_PATH.REGION').'c'.$val.C('HTML_FILE_SUFFIX');
				if(file_exists($filename)){
					unlink($filename);
				}
				if(file_exists($filename)){
					$fid[] = $val;
				}else{
					$sid[] = $val;
				}
			}
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('scount'=>count($sid),'fcount'=>count($fid),'sid'=>implode(',',$sid),'fid'=>implode(',',$fid)))); exit();
		}elseif($_POST['how'] === 'rnid'){
			$id = explode(',',$_POST['id']);
			foreach($id as $key=>$val){
				$filename = C('SHTML_SAVE_PATH.REGION').$val.C('HTML_FILE_SUFFIX');
				if(file_exists($filename)){
					unlink($filename);
				}
				if(file_exists($filename)){
					$fid[] = $val;
				}else{
					$sid[] = $val;
				}
			}
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('scount'=>count($sid),'fcount'=>count($fid),'sid'=>implode(',',$sid),'fid'=>implode(',',$fid)))); exit();
		}else{
        	$this->display();
		}
	}
}