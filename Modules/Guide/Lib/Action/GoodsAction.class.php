<?php
class GoodsAction extends CommonAction{
	//列表页
	function index()
	{
		$model = D('Goods');
        $map = ' 1 ';
        $url = '';
        $keyword = Input::getVar($_REQUEST['keyword']);
        $is_on_sale = Input::getVar($_REQUEST['is_on_sale']);
        if($is_on_sale){
        	$map .= ' AND is_on_sale="'.$is_on_sale.'"';
            $url .= '&is_on_sale=' . $is_on_sale;
        }
        if($keyword) {
            $map .= ' AND goods_name LIKE "%'.$keyword.'%"';
            $url .= '&keyword=' . $keyword;
        }
        $list = $this->_list($model, $map,9, $url);
        $this->assign('list', $list);
        $this->display();
	}

	//点击上下架
	function show_goods(){

		$goods = D('Goods');
		$goods_id = Input::getVar($_REQUEST['goods_id']);
		$is_on_sale  = Input::getVar($_REQUEST['is_on_sale']);
		if($is_on_sale==1){
			$goods->query("UPDATE  `sg_goods` SET  `is_on_sale` =  '2' WHERE  `goods_id`=".$goods_id);
			
			$res['is_on_sale'] = 2;
		 	
		}
		if($is_on_sale==2){
			$res = $goods->query("UPDATE  `sg_goods` SET  `is_on_sale` =  '1' WHERE  `goods_id`=".$goods_id);
			
			$res['is_on_sale'] = 1;
		}
		echo json_encode($res);
	}
	//酒款添加
	function add()
	{
		$cat_list=$this->getparentcat();
		$goods_id = Input::getVar($_REQUEST['goods_id']);
		if(!empty($goods_id))//添加
		{
			$goods_info=M('Goods')->where("goods_id='".$goods_id."'")->find();

			$this->assign('goods_info',$goods_info);
			$this->assign('goods_id',$goods_id);
			//读取标签
			$c_list=M("goods_cat")->where("goods_id='".$goods_id."'")->field("cat_id")->select();
			$c_lists=array();
			foreach($c_list as $k=>$v)
			{
				$c_lists[$v['cat_id']]=$v;
			}
			foreach($cat_list as $key=>$val)
			{
				$cat_list[$key]['is_checked']=empty($c_lists[$key]['cat_id'])?0:1;
			}
		}

		$this->assign('cat_list',$cat_list);
		if($goods_info['t_goods_url']==1) 	$this->display('manual_add');
		else $this->display();
	}

	//酒款添加处理
	function add_detil()
	{
		$manual =  $_GET['manual']; //表示手工录入
		$goods_id = Input::getVar($_REQUEST['goods_id']);
		$t_goods_id = Input::getVar($_REQUEST['t_goods_id']);
		$t_goods_url = Input::getVar($_REQUEST['t_goods_url']);
		$click_url = Input::getVar($_REQUEST['click_url']);
		$goods_name = Input::getVar($_REQUEST['goods_name']);
		$fname = Input::getVar($_REQUEST['fname']);		
		$goods_price = Input::getVar($_REQUEST['goods_price']);
		$commission = Input::getVar($_REQUEST['commission']);
		$commission_rate = Input::getVar($_REQUEST['commission_rate']);
		$goods_promotion = Input::getVar($_REQUEST['goods_promotion']);
		$promotion_starttime = Input::getVar($_REQUEST['promotion_starttime']);
		$promotion_endtime = Input::getVar($_REQUEST['promotion_endtime']);
		$goods_recommend = Input::getVar($_REQUEST['goods_recommend']);
		$goods_img = Input::getVar($_REQUEST['pic_url']);
		$img_from = Input::getVar($_REQUEST['img_from']);
		$cat_id = Input::getVar($_REQUEST['cat_id']);
		$is_logo = Input::getVar($_REQUEST['is_logo']);
		$sort = Input::getVar($_REQUEST['sort']);
		$add_time = Input::getVar($_REQUEST['createtime']);
		$admin_id = Input::getVar($_REQUEST['createuser']);
		$edit_time = Input::getVar($_REQUEST['createtime']);
		$edit_id = Input::getVar($_REQUEST['createuser']);
		$vote_count = Input::getVar($_REQUEST['vote_count']);

		$category   = Input::getVar($_REQUEST['category']);
		$country    = Input::getVar($_REQUEST['country']);
		$jiuzhang   = Input::getVar($_REQUEST['jiuzhang']);
		$pinzhong   = Input::getVar($_REQUEST['pinzhong']);
		
		if($manual) $t_goods_url =1;
		if(!$manual){
		    if(empty($t_goods_id))
			{
				$this->_jumpGo('未获取到淘宝/天猫商品编号');
			}
			if(empty($t_goods_url))
			{
				$this->_jumpGo('请输入淘宝/天猫商品获取地址');
			}
			if(empty($click_url))
			{
				$this->_jumpGo('未获取到淘宝/天猫商品返利地址');
			}
			if(empty($goods_name))
			{
				$this->_jumpGo('未获取到淘宝/天猫商品名称');
			}
			if(empty($goods_price))
			{
				$this->_jumpGo('未获取到淘宝/天猫商品价格');
			}
			if(empty($goods_recommend))
			{
				$this->_jumpGo('请输入商品推荐理由');
			}
			if(empty($goods_img))
			{
				$this->_jumpGo('未获取到淘宝/天猫商品图片');
			}
			if(empty($cat_id))
			{
				$this->_jumpGo('请选择商品所属标签');
			}
		}
		if(empty($commission))
		{
			$commission=0;
		}
		if(empty($commission_rate))
		{
			$commission_rate=0;
		}
		if(empty($promotion_starttime))
		{
			$promotion_starttime=0;
		}
		else
		{
			$promotion_starttime=$this->str_time($promotion_starttime);
		}
		if(empty($promotion_endtime))
		{
			$promotion_endtime=0;
		}
		else
		{
			$promotion_endtime=$this->str_time($promotion_endtime);
		}
		$goods=M("goods");
		if(empty($goods_id))//添加
		{
			$data['t_goods_id']=$t_goods_id;
			$data['t_goods_url']=$t_goods_url;
			$data['click_url']=$click_url;
			$data['goods_name']=$goods_name;
			$data['fname']= $fname;
			$data['goods_price']=$goods_price;
			$data['commission']=$commission;
			$data['commission_rate']=$commission_rate;
			$data['goods_promotion']=$goods_promotion;
			$data['promotion_starttime']=$promotion_starttime;
			$data['promotion_endtime']=$promotion_endtime;
			$data['goods_recommend']=$goods_recommend;
			$data['goods_img']=$goods_img;
			$data['img_from']=$img_from;
			$data['is_logo']=empty($is_logo)?0:$is_logo;
			$data['sort']=empty($sort)?0:$sort;
			$data['add_time']=$add_time;
			$data['admin_id']=$admin_id;
			$data['edit_time']=$add_time;
			$data['edit_id']=$admin_id;
			$data['vote_count']=$vote_count;

			$data['category'] = $category;
			$data['country']  = $country;
			$data['jiuzhang'] = $jiuzhang;
			$data['pinzhong'] = $pinzhong;
			
			//判断商品是否已经存在
			$count=$goods->where("t_goods_id='".$t_goods_id."'")->count();
			if($count>0 && !$manual)
			{
				$this->_jumpGo('你要添加的商品已存在');
			}
			else
			{
				//判断前六个排序
				if($data['sort']!=0)
				{
					$s_data['sort']=0;
					$goods->where("sort=".$data['sort'])->save($s_data);
				}
			   $goods_id=$goods->add($data);

			   if(!empty($goods_id))
			   {
				   $goods_cat=M('goods_cat');
				   //循环添加标签
				   foreach($cat_id as $k=>$v)
				   {
					   $c_data['goods_id']=$goods_id;
					   $c_data['cat_id']=$v;
					   $goods_cat->add($c_data);
				   }
				   $this->_jumpGo('商品添加成功', 'succeed', Url('Goods/index'));
			   }
			   else
			   {
					  $this->_jumpGo('你要添加的商品失败');
			   }
			}
		}
		else//修改
		{
			$data['t_goods_id']=$t_goods_id;
			$data['t_goods_url']=$t_goods_url;
			$data['click_url']=$click_url;
			$data['goods_name']=$goods_name;			
			$data['fname']=$fname;
			$data['goods_price']=$goods_price;
			$data['commission']=$commission;
			$data['commission_rate']=$commission_rate;
			$data['goods_promotion']=$goods_promotion;
			$data['promotion_starttime']=$promotion_starttime;
			$data['promotion_endtime']=$promotion_endtime;
			$data['goods_recommend']=$goods_recommend;
			$data['goods_img']=$goods_img;
			$data['img_from']=$img_from;
			$data['is_logo']=empty($is_logo)?0:$is_logo;
			$data['sort']=empty($sort)?0:$sort;
			$data['edit_time']=$add_time;
			$data['edit_id']=$admin_id;
			$data['vote_count']=$vote_count;

			$data['category'] = $category;
			$data['country']  = $country;
			$data['jiuzhang'] = $jiuzhang;
			$data['pinzhong'] = $pinzhong;
			

			//判断商品是否已经存在
			$count=$goods->where("goods_id<>'".$goods_id."' and t_goods_id='".$t_goods_id."'")->count();
			if($count>0 && !$manual)
			{
				$this->_jumpGo('你要添加的商品已存在');
			}
			else
			{
				//判断前六个排序
			  if($data['sort']!=0)
			  {
					$s_data['sort']=0;
					$goods->where("sort=".$data['sort'])->save($s_data);
			   }
			   $re=$goods->where("goods_id='".$goods_id."'")->save($data);
			   
			   if($re)
			   {
				   $goods_cat=M('goods_cat');
				   //清除所有标签
				   $goods_cat->where("goods_id='".$goods_id."'")->delete();
				   //循环添加标签
				   foreach($cat_id as $k=>$v)
				   {
					   $c_data['goods_id']=$goods_id;
					   $c_data['cat_id']=$v;
					   $goods_cat->add($c_data);
				   }
				    $this->_jumpGo('商品修改成功', 'succeed', Url('Goods/index'));
			   }
			   else
			   {
					  $this->_jumpGo('修改的商品失败');
			   }
			}
		}
	}
	//删除
	function del()
	{
		$goods=M('goods');
		$goods_id = Input::getVar($_REQUEST['goods_id']);
		if(empty($goods_id))
		{
			$this->_jumpGo('删除失败');
		}
		else
		{
			//删除标签
			 M("goods_cat")->where("goods_id='".$goods_id."'")->delete();
			//删除商品
			$re=$goods->where("goods_id=$goods_id")->delete();
			if($re)
			{
				$this->_jumpGo('商品删除成功', 'succeed', Url('Goods/index'));
			}
			else
			{
				$this->_jumpGo('商品删除失败');
			}
		}
	}
	//读取顶级标签
	function getparentcat()
	{
		$cat=M('cat');
		$cat_list=$cat->field("cat_id,cat_name")->select();
		$cat_lists=array();
		foreach($cat_list as $key=>$c)
		{
			$cat_lists[$c['cat_id']]=$c;
		}
		return $cat_lists;
	}
	//ajax获取淘宝商品信息
	function ajax_getTaoItem()
	{
		$re=array('s'=>0,'id'=>'','msg'=>'未知错误','re'=>'');
		$url=Input::getVar($_REQUEST['url']);
		if($url!='')
		{
			$iid=$this->get_item_id($url);//读取淘宝编号 
			if($iid=='')
			{
		    	$re=array('s'=>0,'msg'=>'获取淘宝ID失败','re'=>'');
				echo json_encode($re);
				exit;
			}
			//读取宝贝信息
			$fields="num_iid,title,pic_url,click_url,price";
			$goods=$this->taobaoke_items_detail_get_response($iid,$fields);
			if(!empty($goods))
			{
				//读取促销信息taobao.ump.promotion.get
				$goods['item']['promotion']=$this->taobao_ump_promotion_get($iid);
				$goods['item']['click_url']=$goods['click_url'];
				$re=array('s'=>1,'id'=>$iid,'msg'=>'获取商品成功','re'=>$goods['item']);
				echo json_encode($re);
				exit;
			}
			else
			{
				$re=array('s'=>0,'id'=>$iid,'msg'=>'获取商品失败或者商品已下架','re'=>$goods['item']);
				echo json_encode($re);
				exit;
			}
		}
		else
		{
			$re=array('s'=>0,'id'=>'','msg'=>'未知错误','re'=>'');
			echo json_encode($re);
			exit;
		}
	}

}
?>