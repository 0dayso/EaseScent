<?php
/*
类名：WebQzeon
描述：QQ空间嵌入web页
*/
class AppQqAction extends FrontAction{
	//商品列表
	public function index()
	{		
		//获取参数中的openid/openkey
		$parameter="?".$_SERVER["QUERY_STRING"];//?qz_height=14100&openid=79402E42DDFD7C4B724F1AD3C80A2309&openkey=CC2440E5E2FC05780132B1944D10CF98&pf=qzone&pfkey=a6d38610daf97171434642d23cc7f23d&qz_ver=6&appcanvas=1&params=&via=QZ.APPWALL.abtest0.0
		if(!empty($parameter))
		{
			$openinfo['openid']=$this->parameter($parameter,'openid');
			$openinfo['openkey']=$this->parameter($parameter,'openkey');
			$openinfo['pf']=$this->parameter($parameter,'pf');
			$this->assign("appid",C("qq_appid"));
			$this->assign("pf",empty($openinfo['pf'])?'qzone':$openinfo['pf']);
			if($openinfo['openid']=='' || $openinfo['openkey']=='')//登陆超时或者失败
			{
				$this->assign("login",1);
				$this->display();
				exit;
			}
			else
			{
				$this->assign("login",0);
			}
		}
		$this->title();
		$qq_user_info=$this->get_qq_user_info($openinfo);
		if($qq_user_info['ret']==0)
		{
			//没有过期则对openkey有效期进行续期（一次调用续期2小时）
			$is_login=$this->qq_user_is_login($openinfo);
			$this->assign('qq_user',$qq_user_info);
			//标签
			$m=D('Index');
			$cat_list=$m->cat_list();
			$this->assign("cat_list",$cat_list);
			$cat_lists=$this->get_cat();			
			$map='';
			$goods_list=$this->get_goods($map,$limit='50',$url);
			$this->assign("goods_list",$goods_list);
			//循环分类商品
			$goods_cat_list=array();
			foreach($cat_lists as $key=>$val)
			{
				$cat_ids=M("goods_cat")->where("cat_id='".$val['cat_id']."'")->field("goods_id")->select();
				
				$ids='';
				foreach($cat_ids as $c)
				{
					if(!empty($goods_list[$c['goods_id']]))
					{
						$goods_cat_list[$val['cat_id']][]=$goods_list[$c['goods_id']];
					}
				}
			}
			$this->assign('goods_cat_list',$goods_cat_list);
			//定义输出
			$this->assign("per_max","10");
			$this->display();
		}
		else
		{
			die("CODE:".$qq_user_info['ret']."ERROR_MSG:".$qq_user_info['msg']);
		}
    }
	function get_goods($map='',$limit='16',$url='')
	{
		$goods=M("goods");
		$goods_list=$this->_list($goods,$map,$limit,$url,"sort",false);
		$goods_lists=array();
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
			
			$goods_lists[$val['goods_id']]=$goods_list[$key];
		}
		return $goods_lists;
	}
	//读取分类标签
	function get_cat()
	{
		$cat=M("cat");	
		$cat_list=$cat->field("cat_id,cat_name,p_id")->select();
		$list=array();
		foreach($cat_list as $key=>$val)
		{
			$list[$val['cat_id']]=$val;
		}
		return $list;
	}
	//读取QQ用户信息
	private function get_qq_user_info($openinfo)
	{
		//载入QQ类
		import('@.ORG.Appqq.OpenApiV3');
		$sdk = new OpenApiV3(C("qq_appid"),C("qq_appkey"));
		$sdk->setServerName(C("qq_server_name"));
		$params = array(
						'openid' =>$openinfo['openid'],
						'openkey' =>$openinfo['openkey'],
						'pf' => $openinfo['pf'],
					   );
		/*$params = array(
						'openid' =>'3ED9DFDBE98285EA22E58DCC8C142CA7',
						'openkey' =>'4CBF7766CDF0B39CD827E7E4DFB9191F',
						'pf' => $openinfo['pf'],
					   );*/
		$script_name = '/v3/user/get_info';
		return $sdk->api($script_name, $params,'post');
	}
	//读取openid/openkey
	private function parameter($parameter,$name)
	{
		preg_match_all("/(\\?|#|&)".$name."=([^&#]*)(&|#|$)/i",$parameter,$arr);
        return !$arr?"":$arr[2][0];
	}
	//用户登陆续期v3/user/is_login
	private function qq_user_is_login($openinfo)
	{
		//载入QQ类
		import('@.ORG.Appqq.OpenApiV3');
		$sdk = new OpenApiV3(C("appid"),C("appkey"));
		$sdk->setServerName(C("qq_server_name"));
		$params = array(
						'openid' =>$openinfo['openid'],
						'openkey' =>$openinfo['openkey'],
						'pf' => $openinfo['pf'],
					   );
		/*$params = array(
						'openid' =>'3ED9DFDBE98285EA22E58DCC8C142CA7',
						'openkey' =>'4CBF7766CDF0B39CD827E7E4DFB9191F',
						'pf' => $openinfo['pf'],
					   );*/
		$script_name = '/v3/user/is_login';
		$is_success=$sdk->api($script_name, $params,'post');
		if($is_success['ret']==0)
		{
			return true;
		}
		else
		{
			return false;	
			//return $this->qq_user_is_login($openinfo);
		}
	}
}
?>