<?php

/**
 * 生产商管理
 */
class MfrsAction extends CommonAction {
	
	/**
	 * 生产商列表
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
		$list = $this->_list(D('Mfrs'), $map, 15, $url);
		foreach($list as $key=>$val){
			$list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
			$list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
			$list[$key]['aname_s'] = String::msubstr($val['aname'],0,10);
		}
		$this->assign('list', $list);
		$this->display();
	}
	/**
	 * 生产商增加
	 */
	public function add() {
		if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			$mfrs_id = $this->_insert(D('Mfrs'));
			if($mfrs_id){
				foreach($_POST['joinhonor_honor_id'] as $key=>$val){
					$join_honor_data = array(
											 'mfrs_id' => $mfrs_id,
											 'honor_id' => Input::getVar($_POST['joinhonor_honor_id'][$key]),
											 );
					$this->_insert(D('JoinMfrsHonor'),$join_honor_data);
				}
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'mfrs_id' => $mfrs_id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('MfrsImg'),$img_data);
				}
				$this->_jumpGo('生产商添加成功', 'succeed', Url('index'));
			}
			$this->_jumpGo('生产商添加失败', 'error');
		}
		$this->display();
	}
	/**
	 * 生产商编辑
	 */
	public function edit() {
        $id = Input::getVar($_REQUEST['id']);
        if(!$id) {
            $this->_jumpGo('参数为空!', 'error');
        }
        if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			$is_success = $this->_update(D('Mfrs'));
			if($is_success){
				foreach($_POST['joinhonor_honor_id'] as $key=>$val){
					$join_honor_data = array(
											 'mfrs_id' => $id,
											 'honor_id' => Input::getVar($_POST['joinhonor_honor_id'][$key]),
											 );
					$this->_insert(D('JoinMfrsHonor'),$join_honor_data);
				}
				D('JoinMfrsHonor')->where(array('id'=>array('in',explode(',',$_POST['del_joinhonor_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'mfrs_id' => $id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('MfrsImg'),$img_data);
				}
				foreach($_POST['upd_img_id'] as $key=>$val){
					$upd_img_data = array(
										  'id' => Input::getVar($_POST['upd_img_id'][$key]),
										  'mfrs_id' => $id,
										  'description' => Input::getVar($_POST['upd_img_description'][$key]),
										  'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
										  'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
										  );
					$this->_update(D('MfrsImg'),$upd_img_data);
				}
				D('MfrsImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				$this->_jumpGo('生产商编辑成功', 'succeed', Url('index'));
			}
			$this->_jumpGo('生产商编辑失败', 'error');
        }
        $mfrs_res = D('Mfrs')->where(array('id'=>$id))->find();
		//荣誉
		$mfrs_res['join_honor_res'] = D()->table('jiuku_join_mfrs_honor A,jiuku_honor B,jiuku_honorgroup C')->where('A.mfrs_id = '.$id.' AND A.honor_id = B.id AND B.honorgroup_id = C.id AND A.is_del = \'-1\' AND B.is_del = \'-1\' AND C.is_del = \'-1\'')->field('A.id,A.mfrs_id,A.honor_id,B.cname AS honor_cname,C.cname AS honorgroup_cname')->select();
		//图片
		$mfrs_res['img_res'] = D('MfrsImg')->where(array('mfrs_id'=>$id,'is_del'=>'-1'))->select();
		$this->assign('res', $mfrs_res);
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
        $model = D('Mfrs');
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
		$this->_update(D('Mfrs'),$data);
		$this->_jumpGo('ID为'.$id.'的生产商状态更改成功', 'succeed', Url('index'));
    }
	
	/**
     * 上传文件
     */
	public function upload(){
		$this->_uploads();
	}
	
	
	
}
