<?php
/**
 * 导购前台公共控制器
 */
class FrontAction extends Action {
	/**
	 * 初始化
	 */
	public function _initialize() {
		//parent::_initialize();
		//引入输入过滤类
		import('@.ORG.Util.Input');
		import('@.ORG.Util.Page');
	}

	/**
	 * 生成列表
	 * $_REQUEST['listRows'] = 每页显示条数
	 */
	protected function _list($model, $map, $listRow = 15, $url = '',$sortBy = '', $asc = false) {

		//排序字段 默认为主键名
		if(isset($_REQUEST ['order'])) {
			$order = Input::getVar($_REQUEST['order']);
		} else {
			$order = !empty($sortBy) ? $sortBy : $model->getPk();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if(isset($_REQUEST ['sort'])) {
			$sort = Input::getVar($_REQUEST['sort']) == 'asc' ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = $model->where($map)->count('*');
		$voList = array();
		if ($count > 0) {
			import ( "@.ORG.Util.FrontPage" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = Input::getVar($_REQUEST ['listRows']);
			} else {
				$listRows = $listRow;
			}
			$p = new FrontPage($count, $listRows);
			//分页查询数据

			$voList = $model->where($map)->order( "`" . $order . "` " . $sort." , `edit_time` desc ")->limit($p->firstRow . ',' . $p->listRows)->select ( );
			//echo $model->getlastsql();exit;
            //分页跳转的时候保证查询条件
            if($url) {
			    $p->parameter .= $url;
            }

			//分页显示
			$page = $p->show();
			//模板赋值显示
			$this->assign('sort', $sort );
			$this->assign('order', $order );
			$this->assign("page", $page );
			$this->assign('totalCount', $count );
			$this->assign('numPerPage', $p->listRows);
			$this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		}
		
		return $voList;
	}
	public function title($cat_id='0')
	{
		if($cat_id!='0')
		{
			$cat=M("cat");
			$cat_info=$cat->where("cat_id=".$cat_id)->field("cat_name")->find();
			$cat_name=$cat_info['cat_name'];
			$title=$cat_name."葡萄酒 - 逸香导购";
            $keywords=$cat_name."红酒,".$cat_name."葡萄酒,".$cat_name."进口红酒,".$cat_name."进口葡萄酒";
		    $description="逸香导购：经过专家精心挑选，为您提供最优质的".$cat_name."红酒,".$cat_name."进口葡萄酒";
		}
		else
		{
			$title="逸香导购 - 中国最大的葡萄酒导购平台";
        	$keywords="红酒,葡萄酒,导购,葡萄酒导购,红酒导购,红酒网购,葡萄酒购买,葡萄酒网购,逸香导购";
			$description="买好葡萄酒，到逸香导购！逸香导购专业葡萄酒网购平台。每一款进口红酒，均通过专家推荐、名品甄选、物超所值等角度给予用户推荐，用户更可以通过场合、节日、性别、配餐等精准需求进行归类筛选。";
		}
		$this->assign("title",$title);
		$this->assign("keywords",$keywords);
		$this->assign("description",$description);
	}
	//淘宝数据调用相关函数开始============================================================
	//淘宝签名函数
	function createSign($paramArr){
		 $appSecret=C("appSecret");
		 $sign = $appSecret;
		 ksort($paramArr);
		 foreach ($paramArr as $key => $val) {
			 if ($key != '' && $val != '') {
				 $sign .= $key.$val;
			 }
		 }
		 $sign.=$appSecret;
		 $sign = strtoupper(md5($sign));
		 return $sign;
	}
	//组参函数
	function createStrParam ($paramArr) {
		 $strParam = '';
		 foreach ($paramArr as $key => $val) {
		 if ($key != '' && $val != '') {
				 $strParam .= $key.'='.urlencode($val).'&';
			 }
		 }
		 return $strParam;
	}
	/*
	   调用接口： taobao.taobaoke.listurl.get
	   说明： 淘宝客关键词搜索URL
	   API用户授权类型：不需要
	   参考文档：http://api.taobao.com/apidoc/api.htm?spm=0.0.0.0.T2hdYf&path=cid:38-apiId:135
	*/
	function taobao_taobaoke_listurl_get($keyword='红酒')
	{
		//参数数组
		$paramArr = array(
     						'app_key' => C("appKey"),
     						'method' => 'taobao.taobaoke.listurl.get',
     						'format' => C("format"),
     						'v' => C("v"),
     						'sign_method'=>C("sign_method"),
     						'timestamp' => date('Y-m-d H:i:s'),
     						'q' => $keyword,
     						'nick' =>'',
							'outer_code'=>'',
							'pid'=>C("pid")
						);
		//生成签名
		$sign = $this->createSign($paramArr);
		//组织参数
		$strParam = $this->createStrParam($paramArr);
		$strParam .= 'sign='.$sign;
		//访问服务
		$url = C('url').$strParam; //调用地址
		$result = file_get_contents($url);
		$result = json_decode($result,true);
		if(is_array($result) && !isset($result['error_response']))
		{
			$result=$result['taobaoke_listurl_get_response']['taobaoke_item']['keyword_click_url'];
		}
		else
		{
			$result=array();
		}
		return $result;
	}


//获得用户的真实IP地址
function real_ip()
{
    static $realip = NULL;

    if ($realip !== NULL)
    {
        return $realip;
    }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);

                if ($ip != 'unknown')
                {
                    $realip = $ip;

                    break;
                }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR']))
            {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
            else
            {
                $realip = '0.0.0.0';
            }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_CLIENT_IP'))
        {
            $realip = getenv('HTTP_CLIENT_IP');
        }
        else
        {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}



}
?>