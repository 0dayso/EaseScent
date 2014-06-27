<?php

/**
 * 国家管理
 */
class CountryAction extends CommonAction {
	/**
	 * 国家列表
	 */
	public function index() {
		$keyword = Input::getVar($_REQUEST['keyword']);
        $status = Input::getVar($_REQUEST['status']);
		$map = array();
		$url = '';
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
		$list = $this->_list(D('Country'), $map, 15, $url);
		foreach($list as $key=>$val){
			$list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
			$list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
			$list[$key]['aname_s'] = String::msubstr($val['aname'],0,10);
		}
		$this->assign('list', $list);
		$this->display();
	}
	/**
	 * 国家增加
	 */
	public function add() {
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
		if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			$_POST['term'] = str_replace(array('：','；'),array(':',';'),Input::getVar($_POST['term']));
			$_POST['map_img'] = $_POST['map_img'] ? $_POST['map_img'] : '';
			$country_id = $this->_insert(D('Country'));
			if($country_id){
				foreach($_POST['joingrape_grape_id'] as $key=>$val){
					$join_grape_data = array(
											 'country_id' => $country_id,
											 'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
											 'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
											 );
					$this->_insert(D('JoinCountryGrape'),$join_grape_data);
				}
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'country_id' => $country_id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('CountryImg'),$img_data);
				}
				$img_text = $_POST['fname'];
				if($_POST['cname']) $img_text .= '('.$_POST['cname'].')';
				$this->_TextCretaeImg($img_text,C('UPLOAD_PATH').'Country/name/',md5($country_id));
				$this->_jumpGo('国家添加成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('国家添加失败', 'error');
		}
		$this->assign('rpp',$rpp);
		$this->display();
	}
	/**
	 * 国家编辑
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
            $is_success = $this->_update(D('Country'));
			if($is_success){
				foreach($_POST['joingrape_grape_id'] as $key=>$val){
					$join_grape_data = array(
											 'country_id' => $id,
											 'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
											 'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
											 );
					$this->_insert(D('JoinCountryGrape'),$join_grape_data);
				}
				D('JoinCountryGrape')->where(array('id'=>array('in',explode(',',$_POST['del_joingrape_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'country_id' => $id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('CountryImg'),$img_data);
				}
				foreach($_POST['upd_img_id'] as $key=>$val){
					$upd_img_data = array(
										  'id' => Input::getVar($_POST['upd_img_id'][$key]),
										  'country_id' => $id,
										  'description' => Input::getVar($_POST['upd_img_description'][$key]),
										  'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
										  'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
										  );
					$this->_update(D('CountryImg'),$upd_img_data);
				}
				D('CountryImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				$img_text = $_POST['fname'];
				if($_POST['cname']) $img_text .= '('.$_POST['cname'].')';
				$this->_TextCretaeImg($img_text,C('UPLOAD_PATH').'Country/name/',md5($id));
				$this->_jumpGo('国家编辑成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('国家编辑失败', 'error');
        }
        $country_res = D('Country')->where(array('id'=>$id))->find();
		//葡萄品种
		$country_res['join_grape_res'] = D()->table('jiuku_join_country_grape A,jiuku_grape B')->where('A.country_id = '.$id.' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.country_id,A.grape_id,B.cname AS grape_cname,B.fname AS grape_fname,A.grape_percentage')->select();
		//图片
		$country_res['img_res'] = D('CountryImg')->where(array('country_id'=>$id,'is_del'=>'-1'))->select();
		$this->assign('res', $country_res);
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
        $model = D('Country');
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
		$this->_update(D('Country'),$data);
		$this->_jumpGo('ID为'.$aid.'的国家状态更改成功', 'succeed', Url('index'));
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
				'path' => C('UPLOAD_PATH').'Country/uploads/',
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
				'url' => C('DOMAIN.UPLOAD') . 'Jiuku/Country/uploads/' . $rest['path'],
				'filename' => $rest['path'],
			);
			$this->_ajaxDisplay($result);
		}elseif($_GET['type'] == 'images'){
			$cfg = array(
				'ext' => C('UPLOAD_ALLOW_EXT'),
				'size' => C('UPLOAD_MAXSIZE'),
				'path' => C('UPLOAD_PATH').'Country/uploads/',
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
				'url' => C('DOMAIN.UPLOAD') . 'Jiuku/Country/uploads/' . $rest['path'],
				'filename' => $rest['path'],
			);
			//缩略图
			import('@.ORG.Util.Image');
			$image = new Image();
			$file = C('UPLOAD_PATH').'Country/uploads/'.$result['filename'];
			$image->thumb2($file,$file.'.80.60',80,60);
			$image->thumb2($file,$file.'.260.195',260,195);
			$this->_ajaxDisplay($result);
		}elseif($_GET['type'] == 'maps'){
			$cfg = array(
				'ext' => C('UPLOAD_ALLOW_EXT'),
				'size' => C('UPLOAD_MAXSIZE'),
				'path' => C('UPLOAD_PATH').'Country/maps/',
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
				'url' => C('DOMAIN.UPLOAD') . 'Jiuku/Country/maps/' . $rest['path'],
				'filename' => $rest['path'],
			);
			$this->_ajaxDisplay($result);
		}
	}


	public function text(){
	    header("Content-Type:text/html; charset=utf-8");
		$res = D()->table('es_region')->select();
		foreach($res as $key=>$val){
			$newres = D()->table('jiuku_region')->where(array('oid'=>$val['region_id']))->find();
			if(!isset($newres)){
				dump($val['region_id'].' ╱ '.$val['region_name'].' ╱ '.$val['english_name']);
			}
		}
	}
	public function daochuRegion(){
		set_time_limit(0);
			echo '<table border="1">';
		$this->daochuRegionWhile(0,1);
		echo '</table>';
	}
	function daochuRegionWhile($pid,$level){
		$res = D('Region')->where(array('pid'=>$pid))->select();
		if(count($res)>0){
			foreach($res as $key=>$val){
				if($level == 1){
					echo '<tr><td>'.$val['fname'].' '.$val['cname'].'('.$val['id'].')'.$level.'</td><td></td><td></td><td></td><td></td><td></td></tr>';
				}elseif($level == 2){
					echo '<tr><td>|-></td><td>'.$val['fname'].' '.$val['cname'].'('.$val['id'].')'.$level.'</td><td></td><td></td><td></td><td></td></tr>';
				}elseif($level == 3){
					echo '<tr><td></td><td>|-></td><td>'.$val['fname'].' '.$val['cname'].'('.$val['id'].')'.$level.'</td><td></td><td></td><td></td></tr>';
				}elseif($level == 4){
					echo '<tr><td></td><td></td><td>|-></td><td>'.$val['fname'].' '.$val['cname'].'('.$val['id'].')'.$level.'</td><td></td><td></td></tr>';
				}elseif($level == 5){
					echo '<tr><td></td><td></td><td></td><td>|-></td><td>'.$val['fname'].' '.$val['cname'].'('.$val['id'].')'.$level.'</td><td></td></tr>';
				}elseif($level == 6){
					echo '<tr><td></td><td></td><td></td><td></td><td>|-></td><td>'.$val['fname'].' '.$val['cname'].'('.$val['id'].')'.$level.'</td></tr>';
				}elseif($level == 7){
					echo '<tr><td></td><td></td><td></td><td></td><td></td><td>|-></td><td>'.$val['fname'].' '.$val['cname'].'('.$val['id'].')'.$level.'</td></tr>';
				}
				$this->daochuRegionWhile($val['id'],$level+1);
			}
		}
	}
	public function moveSta(){
		set_time_limit(0);
		//代理商
		D()->query('TRUNCATE TABLE `jiuku_agents`');
		D()->query('TRUNCATE TABLE `jiuku_agents_img`');
		D()->query('TRUNCATE TABLE `jiuku_join_agents_wine`');
	    header("Content-Type:text/html; charset=utf-8");
		$this->_jumpGo('初始化结束开始迁移国家数据...', 'succeed', Url('moveCountry'));
	}
	//迁移国家数据
	public function moveCountry() {
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_country`');
		$res = D()
			->table('old_es.es_extend_value')
			->where(array('type_id'=>7))
			->order('`value_id` ASC')
			->select();
		foreach($res as $key=>$val){
			$data = array(
						  'oid' => $val['value_id'],
						  'cname' => $val['value_name'],
						  'status' => 1,
						  );
			$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
			if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
				$data['add_time'] = $data['last_edit_time'] = strtotime($val['add_time']);
				$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
			}
			if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
				$data['last_edit_time'] = strtotime($val['edit_time']);
				$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
			}
			$country_id = D('Country')->add($data);
		}
		$this->_jumpGo('国家迁移成功,开始迁移葡萄品种数据及图片...', 'succeed', Url('moveGrape'));
	}
	//迁移葡萄品种及图片数据
	public function moveGrape() {
		set_time_limit(0);
		$time = time();
		D()->query('TRUNCATE TABLE `jiuku_grape`');
		D()->query('TRUNCATE TABLE `jiuku_grape_img`');
		$this->deldir(C('UPLOAD_PATH').'Grape/');
		$res = D()
			->table('old_es.es_grape_varieties')
			->order('id ASC')
			->select();
		foreach($res as $key=>$val){
			$val['detail'] = $this->formatcontent($val['detail'],'Grape/uploads/');
			$data = array(
						  'oid' => $val['id'],
						  'fname' => $val['name_en'],
						  'cname' => $val['name'],
						  'description' => $val['description'],
						  'content' => $val['detail'],
						  'queue' => $val['position'],
						  'color_id' => ($val['category'] == 'red-grape') ? 1 : (($val['category'] == 'white-grape') ? 2 : 0),
						  'status' => 1,
						  );
			$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
			if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
				$data['add_time'] = $data['last_edit_time'] = $val['add_time'];
				$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
			}
			if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
				$data['last_edit_time'] = $val['edit_time'];
				$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
			}
			$grape_id = D('Grape')->add($data);
			//图片
			if($val['picture']){
				$img_data = array(
								  'grape_id'=>$grape_id,
								  'filename'=>$this->saveimage('http://www.winesino.com/images/'.$val['picture'],C('UPLOAD_PATH').'Grape/images/'),
								  'description' => $val['name_en'].' '.$val['name'],
								  'alt' => $val['name_en'].' '.$val['name'],
								  'status' =>1 ,
								  );
				$img_data['add_time'] =  $data['add_time'];
				$img_data['add_aid'] =  $data['add_aid'];
				$img_data['last_edit_time'] = $data['last_edit_time'];
				$img_data['last_edit_aid'] = $data['last_edit_aid'];
				D('GrapeImg')->add($img_data);
			}
		}
		$this->_jumpGo('葡萄品种及图片数据迁移成功,开始迁移荣誉数据...', 'succeed', Url('moveHonor'));
	}

	//迁移荣誉数据
	public function moveHonor(){
		set_time_limit(0);
	    header("Content-Type:text/html; charset=utf-8");
		D()->query('TRUNCATE TABLE `jiuku_honor');
		$res = D()
			->table('old_es.es_extend_value')
			->where(array('type_id'=>9))
			->select();
		foreach($res as $key=>$val){
			$data = array(
						  'oid' => $val['value_id'],
						  'pid' => 0,
						  'fname' => $val['value_name2'],
						  'cname' => $val['value_name'],
						  'description' => $val['value_desc'],
						  'honorgroup_id' => 1,
						  'status' => ($val['is_open'] == 1) ? '1' : '-1',
						  );
			$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
			if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
				$data['add_time'] = $data['last_edit_time'] = strtotime($val['add_time']);
				$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
			}
			if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
				$data['last_edit_time'] = strtotime($val['edit_time']);
				$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
			}
			$pid = D('Honor')->add($data);
			$sonres = D()->table('old_es.es_extend_value')->where(array('type_id'=>$val['value_id']))->select();
			foreach($sonres as $k=>$v){
				$sondata = array(
							  'oid' => $v['value_id'],
							  'pid' => $pid,
							  'fname' => $v['value_name2'],
							  'cname' => $v['value_name'],
							  'description' => $v['value_desc'],
							  'honorgroup_id' => 1,
							  'status' => ($v['is_open'] == 1) ? '1' : '-1',
							  );
				$sondata['add_time'] = $sondata['last_edit_time'] = $sondata['add_aid'] = $sondata['last_edit_aid'] = 0;
				if($v['add_time'] && $v['add_time'] != '0000-00-00 00:00:00' && $v['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid')){
					$sondata['add_time'] = $sondata['last_edit_time'] = strtotime($v['add_time']);
					$sondata['add_aid'] = $sondata['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid');
				}
				if($v['edit_time'] && $v['edit_time'] != '0000-00-00 00:00:00' && $v['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid')){
					$sondata['last_edit_time'] = strtotime($v['edit_time']);
					$sondata['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid');
				}
				D('Honor')->add($sondata);
			}
		}
		//波尔多正副牌
		/*D('Honor')->add(array('cname'=>'正牌','honorgroup_id'=>'2','status'=>'1'));
		D('Honor')->add(array('cname'=>'副牌','honorgroup_id'=>'2','status'=>'1'));
		D('Honor')->add(array('cname'=>'三军','honorgroup_id'=>'2','status'=>'1'));	*/
		$this->_jumpGo('荣誉数据迁移成功,开始迁移产区数据及图片...', 'succeed', Url('moveRegion'));
	}
	//迁移产区数据
	public function moveRegion() {
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_region`');
		D()->query('TRUNCATE TABLE `jiuku_region_img`');
		D()->query('TRUNCATE TABLE `jiuku_join_region_grape`');
		D()->query('TRUNCATE TABLE `jiuku_join_region_honor`');
		$this->deldir(C('UPLOAD_PATH').'Region/');
		$this->moveRegionWhile(0);
		$this->_jumpGo('产区迁移成功,开始迁移酒庄数据...', 'succeed', Url('moveWinery'));
	}
	function moveRegionWhile($p_id){
		$res = D()
			->table('old_es.es_region')
			->where(array('parent_id'=>$p_id))
			->select();
		if(count($res)> 0){
			foreach($res as $key=>$val){
				$val['region_detail'] = $this->formatcontent($val['region_detail'],'Region/uploads/');
				$data = array(
							  'oid' => $val['region_id'],
							  'pid' => ($p_id == 0) ? 0 : D()->table('jiuku_region')->where(array('oid'=>$p_id))->getfield('id'),
							  'fname' => $val['english_name'],
							  'cname' => $val['region_name'],
							  'soil' => $val['soil'],
							  'climate' => $val['climate'],
							  'term' => $val['term'],
							  'latitude' => $val['latitude'],
							  'longitude' => $val['longitude'],
							  'description' => $val['region_desc'],
							  'content' => $val['region_detail'],
							  'seo_t' => $val['seo_title'],
							  'seo_k' => $val['seo_keywords'],
							  'seo_d' => $val['seo_desc'],
							  'country_id' => D()->table('jiuku_country')->where(array('oid'=>$val['country_id']))->getfield('id'),
							  'status' => ($val['is_open'] == 1) ? '1' : '0',
							  );
				$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
				if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
					$data['add_time'] = $data['last_edit_time'] = strtotime($val['add_time']);
					$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
				}
				if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
					$data['last_edit_time'] = strtotime($val['edit_time']);
					$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
				}
				$region_id = D('Region')->add($data);
				$oldimg = D()->table('old_es.es_article_pic')->where(array('region_id'=>$val['region_id']))->select();
				if(count($oldimg)>0){
					foreach($oldimg as $k=>$v){
						if(isset($v['pic'])){
							$img_data = array(
											  'region_id' => $region_id,
											  'filename' => $this->saveimage('http://www.winesino.com/images/'.$v['pic'],C('UPLOAD_PATH').'Region/images/'),
											  'description' => $val['english_name'].' '.$val['region_name'],
											  'alt' => $val['english_name'].' '.$val['region_name'],
											  'status' =>1
											  );
							$img_data['add_time'] = $img_data['last_edit_time'] = $img_data['add_aid'] = $img_data['last_edit_aid'] = 0;
							if($v['add_time'] && $v['add_time'] != '0000-00-00 00:00:00' && $v['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid')){
								$img_data['add_time'] = $img_data['last_edit_time'] = strtotime($v['add_time']);
								$img_data['add_aid'] = $img_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid');
							}
							if($v['edit_time'] && $v['edit_time'] != '0000-00-00 00:00:00' && $v['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid')){
								$img_data['last_edit_time'] = strtotime($v['edit_time']);
								$img_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid');
							}
							D('RegionImg')->add($img_data);
						}
					}
				}
				$oldregiongrape = D()->table('old_es.es_g_variety')->where(array('type'=>1,'com_id'=>$val['region_id']))->select();
				if(count($oldregiongrape)>0){
					foreach($oldregiongrape as $k=>$v){
						$regiongrape_data = array(
												  'region_id' => $region_id,
												  'grape_id' => D()->table('jiuku_grape')->where(array('oid'=>$v['variety_id']))->getfield('id')
												  );
						$regiongrape_data['add_time'] = $regiongrape_data['last_edit_time'] = $regiongrape_data['add_aid'] = $regiongrape_data['last_edit_aid'] = 0;
						if($v['add_time'] && $v['add_time'] != '0000-00-00 00:00:00' && $v['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid')){
							$regiongrape_data['add_time'] = $regiongrape_data['last_edit_time'] = strtotime($v['add_time']);
							$regiongrape_data['add_aid'] = $regiongrape_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid');
						}
						if($v['edit_time'] && $v['edit_time'] != '0000-00-00 00:00:00' && $v['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid')){
							$regiongrape_data['last_edit_time'] = strtotime($v['edit_time']);
							$regiongrape_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid');
						}
						if($regiongrape_data['region_id'] && $regiongrape_data['grape_id']){
							D('JoinRegionGrape')->add($regiongrape_data);
						}
					}
				}
				$this->moveRegionWhile($val['region_id']);
			}
		}
	}
	//迁移酒庄数据
	public function moveWinery() {
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_winery`');
		D()->query('TRUNCATE TABLE `jiuku_winery_img`');
		D()->query('TRUNCATE TABLE `jiuku_join_winery_region`');
		D()->query('TRUNCATE TABLE `jiuku_join_winery_honor`');
		D()->query('TRUNCATE TABLE `jiuku_join_winery_grape`');
		$this->deldir(C('UPLOAD_PATH').'Winery/');
		$res = D()
			->table('old_es.es_grandcru')
			->select();
		foreach($res as $key=>$val){
			$val['g_detail'] = $this->formatcontent($val['g_detail'],'Winery/uploads/');
			$data = array(
						  'oid' => $val['g_id'],
						  'fname' => $val['english_name'],
						  'cname' => $val['g_name'],
						  'tel' => $val['tel'],
						  'url' => $val['site_url'],
						  'blog_url' => $val['microblog'],
						  'address' => $val['address'],
						  'acreage' => $val['area'],
						  'plant_age' => $val['plant_age'],
						  'yield' => $val['average_yield'],
						  'oak_storage_duration' => $val['store_time'],
						  'g_map_url' => $val['g_map_url'],
						  'g_map' => $val['g_map'] ? $this->saveimage('http://www.winesino.com/images/'.$val['g_map'],C('UPLOAD_PATH').'Winery/map/') : '',
						  'description' => $val['g_desc'],
						  'content' => $val['g_detail'],
						  'seo_t' => $val['seo_title'],
						  'seo_k' => $val['seo_keywords'],
						  'seo_d' => $val['seo_desc'],
						  'country_id' => D()->table('jiuku_country')->where(array('oid'=>$val['country_id']))->getfield('id'),
						  //'region_id' => D()->table('jiuku_region')->where(array('oid'=>$val['region_id']))->getfield('id'),
						  'status' => ($val['is_open'] == 1) ? '1' : '0',
						  );
			$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
			if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
				$data['add_time'] = $data['last_edit_time'] = strtotime($val['add_time']);
				$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
			}
			if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
				$data['last_edit_time'] = strtotime($val['edit_time']);
				$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
			}
			$winery_id = D('Winery')->add($data);
			$join_region_data = array(
									  'winery_id'=>$winery_id,
									  'region_id'=>D()->table('jiuku_region')->where(array('oid'=>$val['region_id']))->getfield('id'),
									  );
			$join_region_data['add_time'] = $join_region_data['last_edit_time'] = $data['last_edit_time'];
			$join_region_data['add_aid'] = $join_region_data['last_edit_aid'] = $data['last_edit_aid'];
			D('JoinWineryRegion')->add($join_region_data);
			$honor_data = array(
								'winery_id'=>$winery_id,
								'honor_id'=>D()->table('jiuku_honor')->where(array('oid'=>$val['level_id']))->getfield('id'),
								);
			$honor_data['add_time'] = $honor_data['last_edit_time'] = $data['last_edit_time'];
			$honor_data['add_aid'] = $honor_data['last_edit_aid'] = $data['last_edit_aid'];
			D('JoinWineryHonor')->add($honor_data);
			$oldimg = D()->table('old_es.es_article_pic')->where(array('g_id'=>$val['g_id']))->select();
			if(count($oldimg)>0){
				foreach($oldimg as $k=>$v){
					if(isset($v['pic'])){
						$img_data = array(
										  'winery_id' => $winery_id,
										  'filename' => $this->saveimage('http://www.winesino.com/images/'.$v['pic'],C('UPLOAD_PATH').'Winery/images/'),
										  'description' => $val['english_name'].' '.$val['g_name'],
										  'alt' => $val['english_name'].' '.$val['g_name'],
										  'status' =>1
										  );
						$img_data['add_time'] = $img_data['last_edit_time'] = $img_data['add_aid'] = $img_data['last_edit_aid'] = 0;
						if($v['add_time'] && $v['add_time'] != '0000-00-00 00:00:00' && $v['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid')){
							$img_data['add_time'] = $img_data['last_edit_time'] = strtotime($v['add_time']);
							$img_data['add_aid'] = $img_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid');
						}
						if($v['edit_time'] && $v['edit_time'] != '0000-00-00 00:00:00' && $v['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid')){
							$img_data['last_edit_time'] = strtotime($v['edit_time']);
							$img_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid');
						}
						D('WineryImg')->add($img_data);
					}
				}
			}
			$oldwinerygrape = D()->table('old_es.es_g_variety')->where(array('type'=>2,'com_id'=>$val['g_id']))->select();
			if(count($oldwinerygrape)>0){
				foreach($oldwinerygrape as $k=>$v){
					$winerygrape_data = array(
											  'winery_id' => $winery_id,
											  'grape_id' => D()->table('jiuku_grape')->where(array('oid'=>$v['variety_id']))->getfield('id'),
											  'grape_percentage' => $v['percent']
											  );
					$winerygrape_data['add_time'] = $winerygrape_data['last_edit_time'] = $winerygrape_data['add_aid'] = $winerygrape_data['last_edit_aid'] = 0;
					if($v['add_time'] && $v['add_time'] != '0000-00-00 00:00:00' && $v['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid')){
						$winerygrape_data['add_time'] = $winerygrape_data['last_edit_time'] = strtotime($v['add_time']);
						$winerygrape_data['add_aid'] = $winerygrape_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid');
					}
					if($v['edit_time'] && $v['edit_time'] != '0000-00-00 00:00:00' && $v['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid')){
						$winerygrape_data['last_edit_time'] = strtotime($v['edit_time']);
						$winerygrape_data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['edit_user']))->getfield('uid');
					}
					D('JoinWineryGrape')->add($winerygrape_data);
				}
			}
		}
		$this->_jumpGo('酒庄迁移成功,开始迁移品牌数据...', 'succeed', Url('moveBrand'));
	}
	//迁移品牌数据
	public function moveBrand()
	{
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_brand`');
		$res =  D()->table('old_9k.es_brand')->where(array('type'=>0))->select();
		foreach($res as $key=>$val){
			$data = array(
						  'oid' => $val['id'],
						  'cname' =>$val['a_name'],
						  );
			$data['add_time'] = $data['last_edit_time'] = $val['c_time'];
			$data['add_aid'] = $data['last_edit_aid'] = 0;
			D('Brand')->add($data);
		}
		$this->_jumpGo('品牌迁移成功,开始迁移逸香网酒数据...', 'succeed', Url('moveEsWine'));
	}
	//迁移逸香网酒数据
	public function moveEsWine()
	{
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_wine`');
		D()->query('TRUNCATE TABLE `jiuku_wine_img`');
		D()->query('TRUNCATE TABLE `jiuku_join_wine_region`');
		D()->query('TRUNCATE TABLE `jiuku_join_wine_winery`');
		D()->query('TRUNCATE TABLE `jiuku_join_wine_brand`');
		D()->query('TRUNCATE TABLE `jiuku_join_wine_mfrs`');
		D()->query('TRUNCATE TABLE `jiuku_join_wine_honor`');
		D()->query('TRUNCATE TABLE `jiuku_join_wine_grape`');
		$this->deldir(C('UPLOAD_PATH').'Wine/');
		$res = D()
			->table('old_es.es_wine')
			->select();
		foreach($res as $key=>$val){
			if(stripos($val['wine_type'],'正牌') !== false){
				$val['wine_type1'] = '正牌';
			}elseif(stripos($val['wine_type'],'副牌') !== false){
				$val['wine_type1'] = '副牌';
			}elseif(stripos($val['wine_type'],'三军') !== false){
				$val['wine_type1'] = '三军';
			}
			$data = array(
						  'oesid' => $val['wine_id'],
						  'fname' => $val['wine_e_name'],
						  'cname' => $val['wine_name'],
						  'cname_type' => $val['wine_type1'],
						  'yield' => $val['wine_annual_output'],
						  'color_id' => (stripos($val['wine_type'],'红') !== false) ? 1 : ((stripos($val['wine_type'],'白') !== false) ? 2 : 0),
						  'country_id' => D()->table('old_es.es_grandcru B,eswine.jiuku_country C')->where('B.g_id = '.$val['g_id'].' AND C.oid = B.country_id')->getfield('id'),
						  'status' => '1',
						  );
			$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
			if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
				$data['add_time'] = $data['last_edit_time'] = strtotime($val['add_time']);
				$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
			}
			if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
				$data['last_edit_time'] = strtotime($val['edit_time']);
				$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
			}
			$wine_id = D('Wine')->add($data);
			if($val['g_id']){
				$wine_winery_data = array(
										  'wine_id' => $wine_id,
										  'winery_id' => D()->table('jiuku_winery')->where(array('oid'=>$val['g_id']))->getfield('id'),
										  );
				$wine_winery_data['add_time'] = $wine_winery_data['last_edit_time'] = $data['last_edit_time'];
				$wine_winery_data['add_aid'] = $wine_winery_data['last_edit_aid'] = $data['last_edit_aid'];
				D('JoinWineWinery')->add($wine_winery_data);
				$wine_region_data_region_id = D()->table('jiuku_join_winery_region')->where(array('winery_id'=>$wine_winery_data['winery_id']))->getfield('region_id');
				$is_wine_region_data_region_id = D('Region')->where(array('pid'=>array('neq',0),'id'=>$wine_region_data_region_id))->find();
				if($is_wine_region_data_region_id){
					$wine_region_data = array(
											  'wine_id' => $wine_id,
											  'region_id' => $wine_region_data_region_id,
											  );
					$wine_region_data['add_time'] = $wine_region_data['last_edit_time'] = $data['last_edit_time'];
					$wine_region_data['add_aid'] = $wine_region_data['last_edit_aid'] = $data['last_edit_aid'];
					D('JoinWineRegion')->add($wine_region_data);
				}else{
					dump($is_wine_region_data_region_id);
				}
			}
			/*if(stripos($val['wine_type'],'正牌') !== false){
				$honor_data_honor_id = D('Honor')->where(array('cname'=>array('like','%正牌%')))->getfield('id');
			}elseif(stripos($val['wine_type'],'副牌') !== false){
				$honor_data_honor_id = D('Honor')->where(array('cname'=>array('like','%副牌%')))->getfield('id');
			}elseif(stripos($val['wine_type'],'三军') !== false){
				$honor_data_honor_id = D('Honor')->where(array('cname'=>array('like','%三军%')))->getfield('id');
			}
			if($honor_data_honor_id){
				$honor_data = array(
									'wine_id' => $wine_id,
									'honor_id' => $honor_data_honor_id,
									);
				D('JoinWineHonor')->add($honor_data);
			}*/
		}
		$this->_jumpGo('逸香网酒数据迁移成功,开始迁移酒库酒数据...', 'succeed', Url('move9kWine'));
	}
	//迁移酒库酒数据
	public function move9kWine(){
	    header("Content-Type:text/html; charset=utf-8");
		set_time_limit(0);
		$res = D()
			->table('old_9k.es_wine')
			->where(array('type'=>0))
			->select();
		foreach($res as $key=>$val){
			$wine_id = D('Wine')->where(array('oesid'=>$val['wine_id'],'fname'=>$val['wine_e_name'],'cname'=>$val['wine_name']))->getfield('id');
			$count = D('Wine')->where(array('oesid'=>$val['wine_id'],'fname'=>$val['wine_e_name'],'cname'=>$val['wine_name']))->count();
			if($count >1){
				dump($val['wine_e_name']);
				dump($val['wine_name']);
				dump($val['wine_annual_output']);
				dump();
			}
			$data = array(
						  'o9kid' => $val['wine_id'],
						  'fname' => $val['wine_e_name'],
						  'cname' => $val['wine_name'],
						  'aname' => $val['wine_alias_name'],
						  'yield' => $val['wine_annual_output'],
						  'color_id' => (stripos($val['wine_type'],'红') !== false) ? 1 : ((stripos($val['wine_type'],'白') !== false) ? 2 : 0),
						  'status' => '1',
						  );
			if($wine_id){
				$data['id'] = $wine_id;
				D('Wine')->save($data);
			}else{
				$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
				if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
					$data['add_time'] = $data['last_edit_time'] = strtotime($val['add_time']);
					$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
				}
				if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
					$data['last_edit_time'] = strtotime($val['edit_time']);
					$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
				}
				if($val['region']){
					$val['regionarr'] = explode('-',$val['region']);
					for($i = count($val['regionarr'])-1;$i >= 0;$i--){
						if(isset($val['regionarr'][$i]) && is_numeric($val['regionarr'][$i])){
							$val['region_id'] = $val['regionarr'][$i];
							break;
						}
					}
				}
				if($val['region_id'] > 0){
					$data['country_id'] = D('Region')->where(array('oid'=>$val['region_id']))->getfield('country_id');
				}
				$wine_id = D('Wine')->add($data);
				if($wine_id){
					if($val['g_id'] != 0){
						$wine_winery_data = array(
												  'wine_id' => $wine_id,
												  'winery_id' => D()->table('jiuku_winery')->where(array('oid'=>$val['g_id']))->getfield('id'),
												  );
						$wine_winery_data['add_time'] = $wine_winery_data['last_edit_time'] = $data['last_edit_time'];
						$wine_winery_data['add_aid'] = $wine_winery_data['last_edit_aid'] = $data['last_edit_aid'];
						D('JoinWineWinery')->add($wine_winery_data);
					}
					if($val['region_id'] > 0){
						$wine_region_data_region_id = D()->table('jiuku_region')->where(array('oid'=>$val['region_id']))->getfield('id');
						$is_wine_region_data_region_id = D('Region')->where(array('pid'=>array('neq',0),'id'=>$wine_region_data_region_id))->find();
						if($is_wine_region_data_region_id){
							$wine_region_data = array(
													  'wine_id' => $wine_id,
													  'region_id' => $wine_region_data_region_id,
													  );
							$wine_region_data['add_time'] = $wine_region_data['last_edit_time'] = $data['last_edit_time'];
							$wine_region_data['add_aid'] = $wine_region_data['last_edit_aid'] = $data['last_edit_aid'];
							D('JoinWineRegion')->add($wine_region_data);
						}
					}
				}
			}
			$jkoldimg = D()->table('old_9k.es_photo')->where(array('wine_id'=>$val['wine_id']))->select();
			if(count($jkoldimg)>0){
				foreach($jkoldimg as $k=>$v){
					if(isset($v['name'])){
						$img_data = array(
										  'wine_id' => $wine_id,
										  'filename' => $this->saveimage('http://i.wine.cn/9k/9k/data/'.$v['name'],C('UPLOAD_PATH').'Wine/images/'),
										  'description' => $val['wine_e_name'].' '.$val['wine_name'],
										  'alt' => $val['wine_e_name'].' '.$val['wine_name'],
										  'status' =>1
										  );
						$img_data['add_time'] = $img_data['last_edit_time'] = $data['add_time'];
						$img_data['add_aid'] = $img_data['last_edit_aid'] = $data['add_aid'];
						D('WineImg')->add($img_data);
					}
				}
			}
			if($val['brand']){
				$wine_brand_data = array(
										 'wine_id' => $wine_id,
										 'brand_id' => D()->table('jiuku_brand')->where(array('oid'=>$val['brand']))->getfield('id'),
										 );
				$wine_brand_data['add_time'] = $wine_brand_data['last_edit_time'] = $data['add_time'];
				$wine_brand_data['add_aid'] = $wine_brand_data['last_edit_aid'] = $data['add_aid'];
				D('JoinWineBrand')->add($wine_brand_data);
			}
		}

		//组合 cname cnametype
		$new_wine_res =D('Wine')->select();
		foreach($new_wine_res as $key=>$val){
			if($val['cname_type']){
				$val['cname'] = $val['cname'].' '.$val['cname_type'];
			}
			D('Wine')->where(array('id'=>$val['id']))->save(array('cname'=>$val['cname']));
		}
		//D()->query('alter table `jiuku_wine` drop `cname_type`');
		$this->_jumpGo('酒库酒数据迁移成功,开始迁移酒的年份数据...', 'succeed', Url('moveYearWine'));
	}
	//迁移酒的年份
	public function moveYearWine()
	{
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_ywine`');
		D()->query('TRUNCATE TABLE `jiuku_evalparty`');
		D()->query('TRUNCATE TABLE `jiuku_ywine_eval`');
		$rp_id = D('Evalparty')->add(array('fname'=>'RobertM·Parker','cname'=>'罗伯特·帕克','sname'=>'RP'));
		$ws_id = D('Evalparty')->add(array('fname'=>'《Wine Spectator》','cname'=>'葡萄酒观察家杂志','sname'=>'WS'));
		$jr_id = D('Evalparty')->add(array('fname'=>'Jancis Mary Robinson','cname'=>'简西斯•玛丽•罗宾逊','sname'=>'JR'));
		$res = D()
			->table('old_es.es_wine_score')
			->select();
		foreach($res as $key=>$val){
			if($val['RP'] || $val['WS'] || $val['JR']){
				$data = array(
							  'oid' => $val['score_id'],
							  'year' => $val['years'],
							  'wine_id' => D('Wine')->where(array('oesid'=>$val['wine_id']))->getfield('id'),
							  'status' => 1,
							  );
				$data['add_time'] = $data['last_edit_time'] = $data['add_aid'] = $data['last_edit_aid'] = 0;
				if($val['add_time'] && $val['add_time'] != '0000-00-00 00:00:00' && $val['add_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid')){
					$data['add_time'] = $data['last_edit_time'] = strtotime($val['add_time']);
					$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['add_user']))->getfield('uid');
				}
				if($val['edit_time'] && $val['edit_time'] != '0000-00-00 00:00:00' && $val['edit_user'] && D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid')){
					$data['last_edit_time'] = strtotime($val['edit_time']);
					$data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$val['edit_user']))->getfield('uid');
				}
				$ywine_id = D('Ywine')->add($data);
				$ywineeval_data = array(
										'ywine_id' => $ywine_id,
										'add_time' => $data['add_time'],
										'add_aid' => $data['add_aid'],
										'last_edit_time' => $data['last_edit_time'],
										'last_edit_aid' => $data['last_edit_aid'],
										);
				if($val['RP']){
					$ywineeval_data['evalparty_id'] = $rp_id;
					$ywineeval_data['score'] = $val['RP'];
					D('YwineEval')->add($ywineeval_data);
				}
				if($val['WS']){
					$ywineeval_data['evalparty_id'] = $ws_id;
					$ywineeval_data['score'] = $val['WS'];
					D('YwineEval')->add($ywineeval_data);
				}
				if($val['JR']){
					$ywineeval_data['evalparty_id'] = $jr_id;
					$ywineeval_data['score'] = $val['JR'];
					D('YwineEval')->add($ywineeval_data);
				}
			}
		}
		dump('over');
		exit();
	}
	//保存url中的图片文件到本地的movieimg文件夹下，名称不变,返回图片的路径和名称
	function saveimage($url,$path,$filename='')
	{
		if($url == "" || $path == ""):return false;endif;
		if(!is_dir($path))
		{
			//检查是否有该文件夹，如果没有就创建，并给予最高权限
			mkdir("$path", 0777 ,true);
		}
		$ext = pathinfo($url, PATHINFO_EXTENSION);
		if($filename == ''){
			$filename = rand(10000,99999) . substr(md5(time()), 0, 16) . '.' . $ext;
		}
		$filepath = $path . $filename;
		$url = str_replace(' ','%20',$url);
		ob_start();
		readfile($url);
		$img = ob_get_contents();
		ob_end_clean();
		$size = strlen($img);
		$fp2=@fopen($filepath, "a");
		fwrite($fp2,$img);
		fclose($fp2);
		return $filename;
	}
	//格式化文章内容
	function formatcontent($content,$imgpath){
		//保存内容内图片到本地
		$regex='/<(img|input)(.+)(src="?)(.+.(jpg|gif|bmp|bnp|png))("?.*\/>)/i';
		preg_match_all($regex,$content,$matches);
		foreach($matches[4] as $key=>$val){
			if(stripos($val,'http://')===false){
				$filename = $this->saveimage('http://www.winesino.com'.$val,C('UPLOAD_PATH').$imgpath);
			}else{
				$filename = $this->saveimage($val,$imgpath);
			}
		}
		//替换图片标签路径
		$content = preg_replace($regex,'<img$2$3'.'/Upload/Jiuku/'.$imgpath.$filename.'$6',$content);
		//替换分页符
		$content = preg_replace('/<div(\s?)+style=\"page-break-after:(\s?)+always(.*)?\">.*<\/div>/','<hr style="page-break-after:always;" class="ke-pagebreak" />',$content);
		return $content;
	}
	//删除文件夹下所有文件及文件夹
	function deldir($dir) {
		//先删除目录下的文件：
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					$this->deldir($fullpath);
				}
			}
		}
		closedir($dh);
		//删除当前文件夹：
		if(rmdir($dir)) {
			return true;
		} else {
			return false;
		}
	}
	function uploadRegionImg(){
	    header("Content-Type:text/html; charset=utf-8");
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_region_img`');
		$oldres = D()->table('old_es.es_region')->where(array('parent_id'=>array('neq',0)))->select();
		foreach($oldres as $key=>$val){
			$newid = D('Region')->where('fname = \''.$val['english_name'].'\' or cname = \''.$val['region_name'].'\' or description = \''.$val['region_desc'].'\'')->getfield('id');
			if($newid){
				$img_res = D()->table('old_es.es_article_pic')->where(array('region_id'=>$val['region_id']))->select();
				foreach($img_res as $k=>$v){
					$filename = $this->saveimage('http://www.wine.cn/images/'.$v['pic'],C('UPLOAD_PATH').'Region/images/');
					$data = array(
								  'region_id' => $newid,
								  'description' => $v['pic_desc'],
								  'alt' => $v['pic_alt'],
								  'filename' => $filename,
								  'queue' => $v['pic_order'],
								  );
					$data['add_time'] = $data['last_edit_time'] = strtotime($v['add_time']);
					$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid');
					D('RegionImg')->add($data);
				}
			}
		}
		dump(count($yes));
		dump(count($no));
		dump($no);
		//dump($oldres);
	}
	function uploadGrapeImg(){
		set_time_limit(0);
		$res = D('Grape')->where(array('oid'=>array('neq',0)))->field('id,oid,content')->select();
		foreach($res as $key=>$val){
			$res[$key]['img'] = D('GrapeImg')->where(array('grape_id'=>$val['id']))->getfield('filename');
			$old_res = D()->table('old_es.es_grape_varieties')->where(array('id'=>$val['oid']))->field('picture,detail')->find();
			$regex='/<(img|input)(.+)(src="?)(.+.(jpg|gif|bmp|bnp|png))("?.*\/>)/i';
			preg_match_all($regex,$old_res['detail'],$old_matches);
			preg_match_all($regex,$val['content'],$matches);
			foreach($old_matches[4] as $k=>$v){
				$name = substr($matches[4][$k],strrpos($matches[4][$k],'/')+1 );
				if(stripos($v,'http://') === false){
					echo 'up:   http://www.wine.cn'.$v;
					$this->saveimage('http://www.wine.cn'.$v,C('UPLOAD_PATH').'Grape/uploads/',$name);
				}else{
					echo 'up:   '.$v;
					$this->saveimage($v,C('UPLOAD_PATH').'Grape/uploads/',$name);
				}
			}
			if($old_res['picture']){
					echo 'im:   http://www.wine.cn/images/'.$old_res['picture'];
				$this->saveimage('http://www.wine.cn/images/'.$old_res['picture'],C('UPLOAD_PATH').'Grape/images/',$res[$key]['img']);
			}
		}

	}
	function uploadWineryImg(){
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_winery_img`');
		$res = D('Winery')->where(array('oid'=>array('neq',0)))->field('id,oid,g_map')->select();
		foreach($res as $key=>$val){
			//$res[$key]['img'] = D('WineryImg')->where(array('winery_id'=>$val['id']))->select();
			$old_res = D()->table('old_es.es_grandcru')->where(array('g_id'=>$val['oid']))->find();
			if($old_res['g_map']){
				$this->saveimage('http://www.wine.cn/images/'.$old_res['g_map'],C('UPLOAD_PATH').'Winery/map/',$val['g_map']);
			}
			$old_img_res = D()->table('old_es.es_article_pic')->where(array('g_id'=>$old_res['g_id']))->select();
			foreach($old_img_res as $k=>$v){
				$filename = $this->saveimage('http://www.wine.cn/images/'.$v['pic'],C('UPLOAD_PATH').'Winery/images/');
				$data = array(
							  'winery_id' => $val['id'],
							  'description' => $v['pic_desc'],
							  'alt' => $v['pic_alt'],
							  'filename' => $filename,
							  'queue' => $v['pic_order'],
							  );
				$data['add_time'] = $data['last_edit_time'] = strtotime($v['add_time']);
				$data['add_aid'] = $data['last_edit_aid'] = D()->table('eswine_admin_user')->where(array('username'=>$v['add_user']))->getfield('uid');
				D('WineryImg')->add($data);

			}
		}
	}
	function excel_region(){
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_region1`');
	    header("Content-Type:text/html; charset=utf-8");
		$res1 = D()
			->table('z_excel_region_db')
			->field('fname1,cname1')
			->where('`fname1` != \'\' OR `cname1` != \'\'')
			->group('fname1,cname1')
			->select();
		foreach($res1 as $key=>$val){
			$data1 = array(
						   'fname' => $val['fname1'],
						   'cname' => $val['cname1'],
						   );
			D()->table('jiuku_region1')->add($data1);
		}
		$res2 = D()
			->table('z_excel_region_db')
			->field('fname1,cname1,fname2,cname2')
			->where('`fname2` != \'\' OR `cname2` != \'\'')
			->group('fname2,cname2')
			->select();
		foreach($res2 as $key=>$val){
			$pid2 = D()->table('jiuku_region1')->where(array('fname'=>$val['fname1'],'cname'=>$val['cname1']))->getfield('id');
			$data2 = array(
						   'fname' => $val['fname2'],
						   'cname' => $val['cname2'],
						   'pid' => $pid2,
						   );
			D()->table('jiuku_region1')->add($data2);
		}
		$res3 = D()
			->table('z_excel_region_db')
			->field('fname1,cname1,fname2,cname2,fname3,cname3')
			->where('`fname3` != \'\' OR `cname3` != \'\'')
			->group('fname3,cname3')
			->select();
		foreach($res3 as $key=>$val){
			$pid3 = D()->table('jiuku_region1')->where(array('fname'=>$val['fname2'],'cname'=>$val['cname2']))->getfield('id');
			if(!$pid3){
				dump($val);
				dump(D());
			}
			$data3 = array(
						   'fname' => $val['fname3'],
						   'cname' => $val['cname3'],
						   'pid' => $pid3,
						   );
			D()->table('jiuku_region1')->add($data3);
		}
	}
	function generate_ios_region_data(){
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_region1_iosdata`');
	    header("Content-Type:text/html; charset=utf-8");
		$res = D('Region1')->where(array('pid'=>0,'is_del'=>'-1'))->select();
		foreach($res as $key=>$val){
			$iosdata = array(
							 'fname' => $val['fname'],
							 'ename' => $val['ename'],
							 'cname' => $val['cname'],
							 'tier' => 1,
							 );
			$iosid = D('Region1Iosdata')->add($iosdata);
			$res2 = D('Region1')->where(array('pid'=>$val['id'],'is_del'=>'-1'))->select();
			foreach($res2 as $key2=>$val2){
				$iosdata2 = array(
								 'fname' => $val2['fname'],
								 'ename' => $val2['ename'],
								 'cname' => $val2['cname'],
								 'tier' => 2,
								 'tpid' => $iosid,
								 );
				$iosid2 = D('Region1Iosdata')->add($iosdata2);
				$res3 = D('Region1')->where(array('pid'=>$val2['id'],'is_del'=>'-1'))->select();
				foreach($res3 as $key3=>$val3){
					$iosdata3 = array(
									 'fname' => $val3['fname'],
									 'ename' => $val3['ename'],
									 'cname' => $val3['cname'],
									 'tier' => 3,
									 'tpid' => $iosid,
									 'pid' => $iosid2,
									 );
					D('Region1Iosdata')->add($iosdata3);
				}

			}
		}

	}
	function chaifenregion(){
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_regionx`');
		D()->query('TRUNCATE TABLE `jiuku_countryx`');
		$res = D('Region1')->where(array('pid'=>0,'is_del'=>'-1'))->select();
		foreach($res as $key=>$val){
			$country_data = array(
								  'fname' => $val['fname'],
								  'ename' => $val['ename'],
								  'cname' => $val['cname'],
								  );
			$country_id = D('Countryx')->add($country_data);
			$region1_res = D('Region1')->where(array('pid'=>$val['id']))->select();
			foreach($region1_res as $k=>$v){
				$region1_data = array(
									  'fname' => $v['fname'],
									  'ename' => $v['ename'],
									  'cname' => $v['cname'],
									  'pid' => 0,
									  'country_id' => $country_id,
									  );
				$region1_id = D('Regionx')->add($region1_data);
				$region2_res = D('Region1')->where(array('pid'=>$v['id']))->select();
				foreach($region2_res as $k1=>$v1){
					$region2_data = array(
										  'fname' => $v1['fname'],
										  'ename' => $v1['ename'],
										  'cname' => $v1['cname'],
										  'pid' => $region1_id,
										  'country_id' => $country_id,
										  );
					D('Regionx')->add($region2_data);
				}
			}
		}

	}
	function regionCountryx(){
		set_time_limit(0);
		D()->query('TRUNCATE TABLE `jiuku_countryx_img`');
		D()->query('TRUNCATE TABLE `jiuku_join_countryx_grape`');
	    header("Content-Type:text/html; charset=utf-8");
		$region_res = D('Region')->where(array('is_del'=>'-1','pid'=>0))->select();
		foreach($region_res as $key=>$val){
			$data = $val;
			unset($data['id'],$data['oid'],$data['pid'],$data['country_id']);
			$is_exist = D('Countryx')->where(array('ename'=>$val['fname']))->getfield('id');
			if(!is_exist){
				$countryx_id = D('Countryx')->add($data);
			}else{
				unset($data['fname'],$data['cname']);
				$countryx_id = $is_exist;
				$data['id'] = $is_exist;
				D('Countryx')->save($data);
			}
			$region_img_res = D('RegionImg')->where(array('region_id'=>$val['id']))->select();
			foreach($region_img_res as $k=>$v){
				$v['country_id'] = $countryx_id;
				D('RegionImg')->where(array('id'=>$v['id']))->save(array('is_del'=>'1'));
				unset($v['id']);
				D('CountryxImg')->add($v);
			}
			$region_grape_res =D('JoinRegionGrape')->where(array('region_id'=>$val['id']))->select();
			foreach($region_grape_res as $k=>$v){
				$v['country_id'] = $countryx_id;
				D('JoinRegionGrape')->where(array('id'=>$v['id']))->save(array('is_del'=>'1'));
				unset($v['id']);
				D('JoinCountryxGrape')->add($v);
			}
		}
		$country_res = D('Country')->select();
		foreach($country_res as $key=>$val){
			$country_id = $val['id'];
			$countryx_id = D('Countryx')->where(array('ename'=>$val['fname']))->getfield('id');
			D('Wine')->where(array('country_id'=>$country_id))->save(array('countryx_id'=>$countryx_id));
			D('Winery')->where(array('country_id'=>$country_id))->save(array('countryx_id'=>$countryx_id));
		}
	}
	function regionRegionx(){
		set_time_limit(0);
	    header("Content-Type:text/html; charset=utf-8");
		$region_res = D('Region')->where(array('is_del'=>'-1','pid'=>array('neq',0)))->select();
		foreach($region_res as $key=>$val){
			$region_id = $val['id'];
			$data = $val;
			unset($data['id'],$data['oid'],$data['pid'],$data['country_id']);
			$is_exist = D('Regionx')->where(array('ename'=>$val['fname']))->getfield('id');
			if(!$is_exist){
				$data['pid'] = -1;
				$regionx_id = D('Regionx')->add($data);
			}else{
				unset($data['fname'],$data['cname']);
				$regionx_id = $is_exist;
				$data['id'] = $is_exist;
				D('Regionx')->save($data);
			}
			D('JoinRegionGrape')->where(array('region_id'=>$region_id))->save(array('regionx_id'=>$regionx_id));
			D('JoinWineryRegion')->where(array('region_id'=>$region_id))->save(array('regionx_id'=>$regionx_id));
			D('JoinWineRegion')->where(array('region_id'=>$region_id))->save(array('regionx_id'=>$regionx_id));
			D('RegionImg')->where(array('region_id'=>$region_id))->save(array('regionx_id'=>$regionx_id));
		}
	}
}
