<?php
class OpenApiAction extends FrontAction{
	
	public function index()
	{
		header("Location:/");
		exit;
	}
	public function get_wine()
	{
		$limit=empty($_REQUEST['limit'])?'6':intval($_REQUEST['limit']);
		$list=M('goods')->where(" 1=1 ")->field("click_url,fname,goods_name,goods_price,goods_recommend,goods_img,img_from,goods_promotion,promotion_starttime,promotion_endtime")->order('sort')->limit($limit)->select();
		foreach($list as $k=>$v)
		{
			if($v['img_from']==1)
			{
				$list[$k]['goods_img']=C("img_url").$v['goods_img'];
			}
			unset($list[$k]['img_from']);
		}
		die(json_encode($list));
	}
}
?>