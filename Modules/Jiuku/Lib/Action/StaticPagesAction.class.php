<?php

/**
 * 静态页面管理
 */
class StaticPagesAction extends CommonAction {
	
	public function index(){
		$a = D('Winery')->getfield('id',true);
		$this->display();
	}
	public function generate(){
		$what = $_GET['what'];
		switch($what){
			case 'winery':
				if(!is_dir(C('HTML_PATH').'Winery/index')){
					mkdir(C('HTML_PATH').'Winery/index', 0777 ,true);
				}
				if(!is_dir(C('HTML_PATH').'Winery/list')){
					mkdir(C('HTML_PATH').'Winery/list', 0777 ,true);
				}
				if(!is_dir(C('HTML_PATH').'Winery/detail')){
					mkdir(C('HTML_PATH').'Winery/detail', 0777 ,true);
				}
				$_SESSION['winery_idarr'] = D('Winery')->getfield('id',true);
				$_SESSION['winery_pointer'] = D('Winery')->max('id');
				$res['title'] = '酒庄';
				$res['parameter'] = 'Winery';
				$res['btn'][] = array('value'=>'生成首页','parameter'=>'index');
				$res['btn'][] = array('value'=>'生成列表页','parameter'=>'list');
				$res['btn'][] = array('value'=>'生成详情页','parameter'=>'detail');
			break;
			default:
			echo '获取参数错误！';
			exit();
		}
		$this->assign('res',$res);
		$this->display();
	}
	function winerydetail(){
		$id = &$_SESSION['winery_pointer'];
		if(!$id)	die();
		for($i=0;$i<20;$i++,$id--){
			if(!$id)	break;
			$re = $this->winerydetailreturn($id);
			$re_id[] = $re;
		}
		echo implode(',',$re_id);
	}
	function winerydetailreturn($id=1){
		$htmlpath = C('HTML_PATH').'Winery/detail/';
		$res = D('Winery')->where(array('id'=>$id))->find();
		if(!$res){
			if(file_exists($htmlpath.$id.'.html'))	unlink($htmlpath.$id.'.html');
			return '<span style="color:#666;">'.$res['id'].'</span>';
		}
		if($res['status']=='-1'){
			if(file_exists($htmlpath.$id.'.html'))	unlink($htmlpath.$id.'.html');
			return '<span style="color:#F60;">'.$res['id'].'</span>';
		}
		if($res['is_del']=='1'){
			if(file_exists($htmlpath.$id.'.html'))	unlink($htmlpath.$id.'.html');
			return '<span style="color:#F00;">'.$res['id'].'</span>';
		}
		$this->assign('res',$this->winerydetaildata($res));
		$this->buildHtml($id,$htmlpath,C('TEMP_PATH').'wienry_detail_templates.html');
		if(file_exists($htmlpath.$id.'.html')){
			return '<span style="color:#390;">'.$res['id'].'</span>';
		}
		return '<span style="color:#60F;">'.$res['id'].'</span>';
	}
	function winerydetaildata($res){
	    header("Content-Type:text/html; charset=utf-8");
		$res['region_res'] = D()->table('jiuku_join_winery_region A,jiuku_region B')->where('A.winery_id = '.$res['id'].' AND A.region_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.ename,B.cname')->select();
		$res['honor_res'] = D()->table('jiuku_join_winery_honor A,jiuku_honor B')->where('A.winery_id = '.$res['id'].' AND A.honor_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.cname')->select();
		$grape_res = D()->table('jiuku_join_winery_grape A,jiuku_grape B')->where('A.winery_id = '.$res['id'].' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.cname,A.grape_percentage AS percent,B.color_id')->order('A.grape_percentage DESC')->select();
		foreach($grape_res as $key=>$val){
			if($val['color_id'] == 1){
				$res['grape_red_res'][] = $val;
			}elseif($val['color_id'] == 2){
				$res['grape_white_res'][] = $val;
			}else{
				$res['grape_none_res'][] = $val;
			}
		}
		$res['img_res'] = D()->table('jiuku_winery_img')->where('winery_id = '.$res['id'].' AND is_del = \'-1\'')->field('filename,description,alt')->select();
		$res['wine_res'] = D()->table('jiuku_join_wine_winery A, jiuku_wine B')->where('A.winery_id = '.$res['id'].' AND A.wine_id = B.id AND A.is_del = \'-1\' AND B.status = \'1\' AND B.is_del = \'-1\'')->field('B.id,B.fname,B.cname,B.yield')->select();
		foreach($res['wine_res'] as $key=>$val){
			$res['wine_res'][$key]['ywine_res'] = D()->table('jiuku_ywine')->where('wine_id = '.$val['id'].' AND status =\'1\' AND is_del = \'-1\'')->order('year DESC')->select();
			foreach($res['wine_res'][$key]['ywine_res'] as $k=>$v){
				$res['wine_res'][$key]['ywine_res'][$k]['rp'] = D()->table('jiuku_ywine_eval')->where('ywine_id = '.$v['id'].' AND evalparty_id = 1 AND is_del = \'-1\'')->getfield('score');
				$res['wine_res'][$key]['ywine_res'][$k]['ws'] = D()->table('jiuku_ywine_eval')->where('ywine_id = '.$v['id'].' AND evalparty_id = 2 AND is_del = \'-1\'')->getfield('score');
				$res['wine_res'][$key]['ywine_res'][$k]['jr'] = D()->table('jiuku_ywine_eval')->where('ywine_id = '.$v['id'].' AND evalparty_id = 3 AND is_del = \'-1\'')->getfield('score');
			}
		}
		dump($res['wine_res']);
		return $res;
	}
}
