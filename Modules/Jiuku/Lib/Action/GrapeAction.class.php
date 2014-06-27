<?php

/**
 * 葡萄品种管理
 */
class GrapeAction extends CommonAction {
	
	/**
	 * 葡萄品种列表
	 */
	public function index() {		
        $color_id = Input::getVar($_REQUEST['color_id']);
        $keyword = Input::getVar($_REQUEST['keyword']);
        $status = Input::getVar($_REQUEST['status']);
		$map = array();
		$url = '';
        if($color_id) {
            $map['color_id'] = $color_id;
            $url .= '&color_id='.$color_id;
        }
        if($keyword) {
            $map_k['fname'] = array('like', '%'.$keyword.'%');
            $map_k['cname'] = array('like', '%'.$keyword.'%');
            $map_k['_logic'] = 'or';
			$map['_complex'] = $map_k;
            $url .= '&keyword=' . $keyword;
        }
        if($status) {
            $map['status'] = $status;
            $url .= '&status='.$status;
        }
		$list = $this->_list(D('Grape'), $map, 15, $url);
		foreach($list as $key=>$val){
			$color_map['id'] = $val['color_id'];
			$list[$key]['color'] = D('Grapecolor')->where($color_map)->find();
		}
		foreach($list as $key=>$val){
			$list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
			$list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
			$list[$key]['aname_s'] = String::msubstr($val['aname'],0,10);
		}
		$this->assign('list', $list);
		$this->assign('colorList', D('Grapecolor')->grapecolorList());
		$this->display();
	}
	/**
	 * 葡萄品种增加
	 */
	public function add() {
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
		if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
			$grape_id = $this->_insert(D('Grape'));
			if($grape_id){
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'grape_id' => $grape_id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('GrapeImg'),$img_data);
				}
				$this->_jumpGo('葡萄品种添加成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('葡萄品种添加失败', 'error');
		}
		$this->assign('colorList', D('Grapecolor')->grapecolorList());
		$this->assign('rpp',$rpp);
		$this->display();
	}
	/**
	 * 葡萄品种编辑
	 */
	public function edit() {
        $id = Input::getVar($_REQUEST['id']);
        $rpp = Input::getVar($_REQUEST['rpp']);//return_page_parameter
        if(!$id) {
            $this->_jumpGo('参数为空!', 'error');
        }
        if($this->isPost()) {
			$_POST['aname'] = str_replace('；',';',Input::getVar($_POST['aname']));
            $is_success = $this->_update(D('Grape'));
			if($is_success){
				foreach($_POST['img_filename'] as $key=>$val){
					$img_data = array(
									  'grape_id' => $id,
									  'filename' => Input::getVar($_POST['img_filename'][$key]),
									  'description' => Input::getVar($_POST['img_description'][$key]),
									  'alt' => Input::getVar($_POST['img_alt'][$key]),
									  'queue' => Input::getVar($_POST['img_queue'][$key]),
									  );
					$this->_insert(D('GrapeImg'),$img_data);
				}
				foreach($_POST['upd_img_id'] as $key=>$val){
					$upd_img_data = array(
										  'id' => Input::getVar($_POST['upd_img_id'][$key]),
										  'grape_id' => $id,
										  'description' => Input::getVar($_POST['upd_img_description'][$key]),
										  'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
										  'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
										  );
					$this->_update(D('GrapeImg'),$upd_img_data);
				}
				D('GrapeImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
				$this->_jumpGo('葡萄品种编辑成功', 'succeed', Url('index').base64_decode($rpp));
			}
			$this->_jumpGo('葡萄品种编辑失败', 'error');
        }
        $grape_res = D('Grape')->where(array('id'=>$id))->find();
		//图片
		$grape_res['img_res'] = D('GrapeImg')->where(array('grape_id'=>$id,'is_del'=>'-1'))->select();
		$this->assign('colorList', D('Grapecolor')->grapecolorList());
		$this->assign('res', $grape_res);
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
        $model = D('Grape');
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
		$this->_update(D('Grape'),$data);
		$this->_jumpGo('ID为'.$aid.'的葡萄品种状态更改成功', 'succeed', Url('index'));
    }
	
	/**
     * 上传文件
     */
	public function upload(){
		$this->_uploads();
	}
}
