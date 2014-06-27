<?php
class ProductAction extends FrontAction{
	
	function index()
	{
		$id=empty($_REQUEST['id'])?'':intval($_REQUEST['id']);
		if($id!='')
		{
			$goods=M("goods")->where("goods_id='".$id."'")->field("click_url")->find();
			if(!empty($goods['click_url']))
			{
				header("Location:".$goods['click_url']);
			}
			else
			{
				header("Location:/");
			}
		}
		else
		{
			header("Location:/");
		}
	}
}
?>