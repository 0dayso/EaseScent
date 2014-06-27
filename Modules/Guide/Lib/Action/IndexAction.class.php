<?php
/*
类名：IndexAction
描述：导购首页
*/
class IndexAction extends FrontAction {
/*
方法名：index
描述：导购首页
*/
    public function index()
	{
		$cat_id=Input::getVar($_REQUEST['cat_id']);
		if(!empty($cat_id))
		{
			$cat_ids=M("goods_cat")->where("cat_id='".$cat_id."'")->field("goods_id")->select();
			$ids='';
			foreach($cat_ids as $c)
			{
				if($ids=='')
				{
					$ids=$c['goods_id'];
				}
				else
				{
					$ids.=",".$c['goods_id'];
				}
			}

			$map['goods_id']=array("in",$ids);
			$map['is_on_sale'] = 1;
			$url .= 'cat_id_' . $cat_id;
			$this->title($cat_id);
		}
		else
		{
			$url.="cat_id_0";
			$map['is_on_sale'] = 1;
			$this->title();
		}

		$m=D('Index');

		//标签
		$cat_list=$m->cat_list();
		$this->assign("cat_list",$cat_list);
		$goods=M("goods");
		$goods_list=$this->_list($goods,$map,10,$url,"sort",false);

		foreach($goods_list as $key=>$val)
		{
			if($val['goods_promotion']>0)//判断是否有促销价格
			{
				if($val['promotion_starttime']<time() && $val['promotion_endtime']>time())
				{
					$price=explode('.',$val['goods_promotion']);
					$goods_list[$key]['price_integer']=$price[0];
					$goods_list[$key]['price_decimal']=$price[1];
				}
				else
				{
					$price=explode('.',$val['goods_price']);
					$goods_list[$key]['price_integer']=$price[0];
					$goods_list[$key]['price_decimal']=$price[1];
				}
			}
			else
			{
				$price=explode('.',$val['goods_price']);
				$goods_list[$key]['price_integer']=$price[0];
				$goods_list[$key]['price_decimal']=$price[1];
			}
			if($val['goods_recommend'])
			{
				$goods_list[$key]['goods_recommend_list']=explode("\r\n",$val['goods_recommend']);
			}
		}

		$this->assign("goods_list",$goods_list);
		//轮换图
		$img = M('img');
		$img_data = $img->where("status=1")->order('img_sort')->select();

		$this->assign('img_data',$img_data);
		$this->display();
    }
/*
方法名：search
描述：搜索
*/
	public function search()
	{
		$keyword=Input::getVar($_REQUEST['keyword']);
		
		if($keyword=='')
		{
			echo "<script type='text/javascript'>alert('请输入您要搜索的关键词');window.history.back();</script>";
			exit;
		}
		$go_url=$this->taobao_taobaoke_listurl_get($keyword);
		if($go_url=='')
		{
			echo "<script type='text/javascript'>alert('未查询到您要搜素的关键词');window.history.back();</script>";
			exit;
		}
		else
		{
			echo "<script type='text/javascript'>window.location.href='".$go_url."';</script>";
			exit;
		}
	}


	/**
	 *接口调用类 临时测试
	 */
	function PostApi(){

		$appid  = '6';
		$appkey = '1b29b03a615a0113bd915abec8e6da71';

		$curlPost = 'appid='.$appid.'&appkey='.$appkey;
		$ch = curl_init();//初始化curl
		//curl_setopt($ch, CURLOPT_URL,'http://api.guide.wine.com/?Guide/ApiMobile/Goodslist/');//抓取指定网页
		curl_setopt($ch, CURLOPT_URL, C('API_URL').'/?Admin/Api/accessToken');//抓取指定网页 http://api.guide.wine.com/?Admin/Api/accessToken
		curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$Token = curl_exec($ch);//运行curl
		curl_close($ch);
		$Token = json_decode($Token)->result;

		if($Token){
			$accessToken  = $Token;
			$pagenum = 3;
			$curlPost = 'appid='.$appid.'&accessToken='.$accessToken.'&p='.$pagenum;
			$ch = curl_init();//初始化curl
			curl_setopt($ch, CURLOPT_URL,C('API_URL').'/?Guide/ApiMobile/Goodslist/');//抓取指定网页
			curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
			curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			$data = curl_exec($ch);//运行curl
			curl_close($ch);
			print_R($data);
		}else{
			echo "Token is empty";
		}
	}



}