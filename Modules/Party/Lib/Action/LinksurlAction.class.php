<?php
/*
 *名称:LinksurlAction.class.php
 *作用:新闻展示
 *开发：王亚龙 
 *时间:2011-3-4
 *更新:2011-5-10
 */
class LinksurlAction extends BaseAction {
	public function _empty(){
		$this->show();
	}
    public function show()
	{
	    header("Content-Type:text/html; charset=utf-8");
		$zhlink = $this->getLinks(array(298),false);//综合网站
		$this->assign('zhlink',$zhlink);
		$hylink = $this->getLinks(array(299),false);//行业网站
		$this->assign('hylink',$hylink);
		$xhlink = $this->getLinks(array(300),false);//协会网站
		$this->assign('xhlink',$xhlink);
		$grlink = $this->getLinks(array(301),false);//个人网站
		$this->assign('grlink',$grlink);
		$qtlink = $this->getLinks(array(302),false);//其他
		$this->assign('qtlink',$qtlink);
		
		$seo_title = '友情链接';
		$seo_keywords = '友情链接';
		$seo_description = '友情链接';
		$this->assign('seo_title', $seo_title);
		$this->assign('seo_keywords', $seo_keywords);
		$this->assign('seo_description', $seo_description);		
	    $this->display('index');	
	}
	//其他页面获取友链列表（es_links表获取）
	public function getLinks($links_sort_arr,$links_seat)
	{
		$Common = A('Common');
		if($links_sort_arr){
			$where['links_sort']=array('in',$links_sort_arr);
		}
		if($links_seat){
			$where['links_seat']=array('like','%|'.$links_seat.'|%');
		}
		$where['is_open']=1;
		$Model=D();
		$res=$Model
			->table('es_links')
			->where($where)
			->order('links_order DESC')
			->findall();
		foreach($res as $key=>$val){
			$res[$key]['slinks_name'] = $Common->mystrcut($val['links_name'],7,$etc='');
		}
		return $res;
		
	}

}
?>