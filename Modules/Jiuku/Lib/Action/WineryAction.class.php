<?php

/**
 * 酒庄管理
 */
class WineryAction extends CommonAction {
	/**
	 * 酒庄列表
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
		$list = $this->_list(D('Winery'), $map, 15, $url);
		foreach($list as $key=>$val){
			$list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
			$list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
		}
		$this->assign('list', $list);
		$this->assign('countryList', D('Country')->countryList());
		$this->display();
	}
	/**
	 * 酒庄增加
	 */
	public function add() {
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
		if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			$_POST['g_map'] = A('Common')->GrabImage($_POST['g_map_url'],'Winery/map/','png');
			$winery_id = $this->_insert(D('Winery'));
			if($winery_id){
				foreach($_POST['joingrape_grape_id'] as $key=>$val){
					$join_grape_data = array(
											 'winery_id' => $winery_id,
											 'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
											 'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
											 );
					$this->_insert(D('JoinWineryGrape'),$join_grape_data);
				}
				foreach($_POST['joinhonor_honor_id'] as $key=>$val){
					$join_honor_data = array(
											 'winery_id' => $winery_id,
											 'honor_id' => Input::getVar($_POST['joinhonor_honor_id'][$key]),
											 );
					$this->_insert(D('JoinWineryHonor'),$join_honor_data);
				}
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'winery_id' => $winery_id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('WineryImg'),$img_data);
				}
				$img_text = $_POST['fname'];
				if($_POST['cname']) $img_text .= '('.$_POST['cname'].')';
				$this->_TextCretaeImg($img_text,C('UPLOAD_PATH').'Winery/name/',md5($winery_id));
				$this->_jumpGo('酒庄添加成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('酒庄添加失败', 'error');
		}
		$this->display();
		$this->assign('rpp',$rpp);
	}
	/**
	 * 酒庄编辑
	 */
	public function edit() {
        $id = Input::getVar($_REQUEST['id']);
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
        if(!$id) {
            $this->_jumpGo('参数为空!', 'error');
        }
        if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			if($_POST['g_map_url']){
				$_POST['g_map'] = A('Common')->GrabImage($_POST['g_map_url'],'Winery/map/','png');
			}else{
				unset($_POST['g_map_url']);
			}
			$is_success = $this->_update(D('Winery'));
			if($is_success){
				foreach($_POST['joingrape_grape_id'] as $key=>$val){
					$join_grape_data = array(
											 'winery_id' => $id,
											 'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
											 'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
											 );
					$this->_insert(D('JoinWineryGrape'),$join_grape_data);
				}
				foreach($_POST['upd_joingrape_id'] as $key=>$val){
					$upd_join_grape_data = array(
												 'id' => Input::getVar($_POST['upd_joingrape_id'][$key]),
												 'grape_percentage' => Input::getVar($_POST['upd_joingrape_grape_percentage'][$key]),
												 );
					$this->_update(D('JoinWineryGrape'),$upd_join_grape_data);
				}
				D('JoinWineryGrape')->where(array('id'=>array('in',explode(',',$_POST['del_joingrape_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				foreach($_POST['joinhonor_honor_id'] as $key=>$val){
					$join_honor_data = array(
											 'winery_id' => $id,
											 'honor_id' => Input::getVar($_POST['joinhonor_honor_id'][$key]),
											 );
					$this->_insert(D('JoinWineryHonor'),$join_honor_data);
				}
				D('JoinWineryHonor')->where(array('id'=>array('in',explode(',',$_POST['del_joinhonor_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'winery_id' => $id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('WineryImg'),$img_data);
				}
				foreach($_POST['upd_img_id'] as $key=>$val){
					$upd_img_data = array(
										  'id' => Input::getVar($_POST['upd_img_id'][$key]),
										  'winery_id' => $id,
										  'description' => Input::getVar($_POST['upd_img_description'][$key]),
										  'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
										  'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
										  );
					$this->_update(D('WineryImg'),$upd_img_data);
				}
				D('WineryImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				$img_text = $_POST['fname'];
				if($_POST['cname']) $img_text .= '('.$_POST['cname'].')';
				$this->_TextCretaeImg($img_text,C('UPLOAD_PATH').'Winery/name/',md5($id));
				$this->_jumpGo('酒庄编辑成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('酒庄编辑失败', 'error');
        }
        $winery_res = D('Winery')->where(array('id'=>$id))->find();
		//葡萄品种
		$winery_res['join_grape_res'] = D()->table('jiuku_join_winery_grape A,jiuku_grape B')->where('A.winery_id = '.$id.' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.winery_id,A.grape_id,B.cname AS grape_cname,B.fname AS grape_fname,A.grape_percentage')->select();
		//荣誉
		$winery_res['join_honor_res'] = D()->table('jiuku_join_winery_honor A,jiuku_honor B,jiuku_honorgroup C')->where('A.winery_id = '.$id.' AND A.honor_id = B.id AND B.honorgroup_id = C.id AND A.is_del = \'-1\' AND B.is_del = \'-1\' AND C.is_del = \'-1\'')->field('A.id,A.winery_id,A.honor_id,B.cname AS honor_cname,C.cname AS honorgroup_cname')->select();
		//图片
		$winery_res['img_res'] = D('WineryImg')->where(array('winery_id'=>$id,'is_del'=>'-1'))->select();
		$this->assign('res', $winery_res);
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
        $model = D('Winery');
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
		$this->_update(D('Winery'),$data);
		$this->_jumpGo('ID为'.$id.'的酒庄状态更改成功', 'succeed', Url('index'));
    }

	/**
     * 上传文件
     */
	public function upload(){
		$type = isset($_GET['type']) ? Input::getVar($_GET['type']).'/' : '';
		import('@.ORG.Util.Upload');
		$upload = new Upload();
		$cfg = array(
			'ext' => C('UPLOAD_ALLOW_EXT'),
			'size' => C('UPLOAD_MAXSIZE'),
			'path' => C('UPLOAD_PATH').$type,
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
			'url' => C('DOMAIN.UPLOAD') . 'Jiuku/' . $type . $rest['path'],
			'filename' => $rest['path'],
		);
		//缩略图
		import('@.ORG.Util.Image');
		$image = new Image();
		$file = C('UPLOAD_PATH').$type.$result['filename'];
		$image->thumb2($file,$file.'.300.225',300,225);
		$this->_ajaxDisplay($result);
	}

	/**
     * 生成静态页
     */
	public function html(){
		if($_POST['how'] === 'ini'){
			$all_id = range(1,D('Winery')->max('id'));
			if($_POST['what'] === 'mingzhuang'){
				$res = D()->table('jiuku_join_winery_honor A')->join('jiuku_winery B on A.winery_id=B.id')->field('B.id')->where('A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->select();
				foreach($res as $key=>$val){
					$yid[] = $val['id'];
				}
				$nid = array_diff($all_id,$yid);
			}else{
				$yid = D('Winery')->where(array('is_del'=>'-1'))->getfield('id',true);
				$nid = array_diff($all_id,$yid);
			}
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('ycount'=>count($yid),'ncount'=>count($nid),'yid'=>implode(',',$yid),'nid'=>implode(',',$nid)))); exit();
		}elseif($_POST['how'] === 'yid'){
			if($_POST['what'] === 'mingzhuang'){
				$id = explode(',',$_POST['id']);
				foreach($id as $key=>$val){
					$filename = A('Template')->mingzhuang_detail($val);
					$filename2 = A('Template')->mingzhuang_comment($val);
					if(file_exists($filename) && file_exists($filename2)){
						$sid[] = $val;
					}else{
						$fid[] = $val;
					}
				}
				echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('scount'=>count($sid),'fcount'=>count($fid),'sid'=>implode(',',$sid),'fid'=>implode(',',$fid)))); exit();
			}else{
				//普通庄园
			}
		}elseif($_POST['how'] === 'index'){
			if($_POST['what'] === 'mingzhuang'){
				$filename = A('Template')->mingzhuang_index();
				$is_exist = file_exists($filename) ? 1 : 0;
				echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('is_exist'=>$is_exist))); exit();
			}else{
				//普通庄园
			}
		}elseif($_POST['how'] === 'nid'){
			if($_POST['what'] === 'mingzhuang'){
				$id = explode(',',$_POST['id']);
				foreach($id as $key=>$val){
					$filename = C('SHTML_SAVE_PATH.MINGZHUANG').$val.C('HTML_FILE_SUFFIX');
					$filename2 = C('SHTML_SAVE_PATH.MINGZHUANG').$val.'_comment'.C('HTML_FILE_SUFFIX');
					if(file_exists($filename)){
						unlink($filename);
					}
					if(!file_exists($filename) && !file_exists($filename2)){
						$sid[] = $val;
					}else{
						$fid[] = $val;
					}
				}
				echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('scount'=>count($sid),'fcount'=>count($fid),'sid'=>implode(',',$sid),'fid'=>implode(',',$fid)))); exit();
			}else{
				//普通庄园
			}
		}else{
        	$this->display();
		}
	}
}
