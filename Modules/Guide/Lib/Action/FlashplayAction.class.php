<?php
class FlashplayAction extends CommonAction {
	public function index(){

		$img = M('img');
		$img_data = $img->order('img_sort')->select();
		$this->assign('img_data',$img_data);
		$this->display();
	}

	public function add(){
		if (empty($_POST['step'])){
			$url = isset($_GET['url']) ? $_GET['url'] : 'http://';
	        $src = isset($_GET['src']) ? $_GET['src'] : '';
	        $sort = 0;
	        $rt = array('act'=>'add','img_url'=>$url,'img_src'=>$src, 'img_sort'=>$sort);
	        $this->assign('rt',$rt);
	        $this->display();
		}elseif($_POST['step']==2){
			$pic_url  = Input::getVar($_REQUEST['pic_url']);
			$yuan_url = Input::getVar($_REQUEST['yuan_url']);
			$img_url  = Input::getVar($_REQUEST['img_url']);
			$img_text = Input::getVar($_REQUEST['img_text']);
			$img_sort = Input::getVar($_REQUEST['img_sort']);
			$status   = Input::getVar($_REQUEST['status']);
			if(empty($pic_url)){
				$this->error('请填上传图片！');
			}else{
				$data['pic_url']  = $pic_url;
				$data['yuan_url'] = $yuan_url;
				$data['img_url']  = $img_url;
				$data['img_text'] = $img_text;
				$data['img_sort'] = $img_sort;
				$data['status']   = $status;
				$img_id = M('img')->add($data);
				if($img_id > 0){
					$this->success('上传成功！');
				}
			}
		}
	}

	public function edit(){
		$img = M('img');
		$img_id = Input::getVar($_REQUEST['img_id']);
		if(empty($_POST['step'])){
			$res = $img->where("id=".$img_id)->find();
			$this->assign('res',$res);
			$this->display();
		}elseif($_POST['step']=='2'){
			$data['pic_url']  = Input::getVar($_REQUEST['pic_url']);
			$data['yuan_url'] = Input::getVar($_REQUEST['yuan_url']);
			$data['img_url']  = Input::getVar($_REQUEST['img_url']);
			$data['img_text'] = Input::getVar($_REQUEST['img_text']);
			$data['img_sort'] = Input::getVar($_REQUEST['img_sort']);
			$data['status']   = Input::getVar($_REQUEST['status']);
			$res = $img->where("id=".$img_id)->save($data);
			if($res > 0){
				$this->_jumpGo('编辑成功', 'succeed', Url('Flashplay/index'));
			}else{
				$this->error('编辑失败！');
			}
		}
	}

	public function del(){
	 $img = M('img');
	 $img_id = Input::getVar($_REQUEST['img_id']);
	 if(empty($img_id)){
	 	$this->error('删除失败');
	 }else{
	 	$rs = $img->where("id=".$img_id)->delete();
	 	if($rs){
	 		$this->success('删除成功！');
	 	}else{
	 		$this->error('删除失败');
	 	}
	 }
	}

}
?>