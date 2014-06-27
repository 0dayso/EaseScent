<?php

/**
 * News项目的公共控制器
 */
class CommonAction extends BaseAdmin {
	/**
	 * 初始化
	 */
	public function _initialize() {
		parent::_initialize();
		//引入输入过滤类
		import('@.ORG.Util.Input');
		import('@.ORG.Util.Page');
		$this->start_taobao_jsdk();
	}
	//初始化淘宝jsdk
	function start_taobao_jsdk()
	{
		$app_key =C("appKey");/*填写appkey */
		$appSecret=C("appSecret");/*填入Appsecret'*/
		$timestamp=time()."000";
		$message = $appSecret.'app_key'.$app_key.'timestamp'.$timestamp.$appSecret;
		$mysign=strtoupper(hash_hmac("md5",$message,$appSecret));
		setcookie("timestamp",$timestamp);
		setcookie("sign",$mysign);
	}
	/**
	 * ajax返回数据
	 */
	protected function _ajaxDisplay($result, $type = '') {
		if(empty($type)) $type = C('DEFAULT_AJAX_RETURN');
		if(strtoupper($type)=='JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
			header('Content-Type:text/html; charset=utf-8');
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML'){
            // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            exit(xml_encode($result));
        }elseif(strtoupper($type)=='EVAL'){
            // 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            exit($result);
        }
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
			import ( "@.ORG.Util.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = Input::getVar($_REQUEST ['listRows']);
			} else {
				$listRows = $listRow;
			}
			$p = new Page($count, $listRows);
			//分页查询数据
			$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
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
			$this->assign('numPerPage', $p->listRows );
			$this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		}
		return $voList;
	}

	/**
	 * 生成where查询条件
	 */
    protected function _search($model) {
		//生成查询条件
		$map = array ();
		foreach($model->getDbFields () as $key => $val ) {
			if(isset($_REQUEST[$val]) && $_REQUEST[$val] != '') {
				$map[$val] = Input::getVar($_REQUEST[$val]);
			}
		}
		return $map;
	}

	/**
	 * edit通用方法
	 */
    protected function _edit($model) {
		$id = Input::getVar($_REQUEST[$model->getPk()]);
		$vo = $model->find($id);
		$this->assign('vo',$vo);
		$this->display();
	}

	/**
	 * delete通用方法
	 */
	protected function _delete($model) {
		if(!empty($model)) {
			$pk = $model->getPk ();
			$id = Input::getVar($_REQUEST[$pk]);
			if(isset($id)) {
				$condition = array($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where($condition)->delete()) {
					return true;
				}
			}
		}
		$this->_jumpGo('删除失败','error');
	}

	/**
	 * 新增数据
	 */
	protected function _insert($model, $data = '', $replace = false, $options = array()) {
		if (false === $model->create($data)) {
			$this->_jumpGo($model->getError(), 'error');
		}
		//保存当前数据对象
		$list=$model->add($data, $options, $replace);
		if ($list!==false) { //保存成功
			return $list;
		} else {
			$this->_jumpGo($model->getError(), 'error');
		}
	}

	/**
	 * 更新数据
	 */
	protected function _update($model, $data = '') {
		if (false === $model->create($data)) {
			$this->_jumpGo($model->getError(), 'error');
		}
		// 更新数据
		$list = $model->save();
		if (false !== $list) {
			return true;
		} else {
			$this->_jumpGo($model->getError(), 'error');
		}
	}

	/**
	 * 后台通用跳转函数
	 */
	protected function _jumpGo($message, $mode = 'info', $url = 'javascript:history.go(-1)', $time=1) {
		$html = '<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Refresh" contect="'.$time.';url='.$url.'"><meta http-equiv="Content-Type"content="text/html; charset=utf-8"/><title>提示信息</title><style type="text/css">body{background-color:#fff;margin:40px;font:13px/20px Arial,sans-serif;color:#4F5155}a{color:#00F;background-color:transparent;font-weight:normal;text-decoration:none}h1{color:#444;background-color:transparent;border-bottom:1px solid#D0D0D0;font-size:19px;font-weight:normal;margin:0 0 14px 0;padding:14px 15px 10px 15px}#body{margin:0 15px 0 15px}p.footer{text-align:right;font-size:11px;border-top:1px solid#D0D0D0;line-height:32px;padding:0 10px 0 10px;margin:20px 0 0 0}#container{margin:10px;border:1px solid#D0D0D0;-webkit-box-shadow:0 0 8px#D0D0D0}h1 span{height:27px;line-height:27px;vertical-align:middle;padding-left:32px;display:block}.error{background:url(/Public/admin/images/error.gif)no-repeat}.succeed{background:url(/Public/admin/images/succeed.gif)no-repeat}.info{background:url(/Public/admin/images/info.gif)no-repeat}</style></head><body><div id="container"><h1><span class="'.$mode.'">'.$message.'……</span></h1><div id="body"><p><a href="'.$url.'">如果您的浏览器没有自动跳转，请点击此链接...</a></p></div><p class="footer">&copy 2012 wine.cn</p></div><script>var code = \'location.href="'.$url.'";\'; setTimeout(code, 1000);</script></body></html>';
        $this->display('','','',$html);
        exit();
	}
	//2013-03-31 23:59:59
	function str_time($str)
	{
		$d=explode(' ',$str);
		$ymd=explode('-',$d[0]);
		$his=explode(':',$d[1]);
		$unixtime=mktime($his[0],$his[1],$his[2],$ymd[1],$ymd[2],$ymd[0]);
		return $unixtime;
	}
	//淘宝数据调用相关函数开始
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
		获取淘宝url中的商品编号
	*/
	function get_item_id($url){	
		//http://detail.tmall.com/item.htm?id=16273210599&ali_trackid=2:mm_35642088_0_0:1374228797_6k2_1136569787&spm=2014.21403739.1.0	
		$re=array('s'=>0,'id'=>'','msg'=>'未知错误');
		//验证是否是淘宝或天猫的链接
		if(preg_match('/(taobao\.com|tmall\.com)/',$url)!=1){
			return $re=array('s'=>0,'id'=>'','msg'=>'请输入淘宝或者天猫链接');
		}
		$tao_id_arr=C('tao_id_arr');
		if(empty($tao_id_arr)){
			$tao_id_arr=array('id');//未配置tao_id_arr
		}
		$ids=implode('|',$tao_id_arr);
		preg_match('/[&|?]('.$ids.')=(\d+)/',$url,$b);
		
		if($b[2]=='')
		{
			preg_match('#/(\d+)\.htm#',$url,$b);
			return $b[1];
		}
		else
		{
			return $b[2];
		}
	}
    /*
	   调用接口：taobao.taobaoke.items.detail.get
	   说明： 查询淘宝客推广商品详细信息
	   API用户授权类型：不需要
	   参考文档：http://api.taobao.com/apidoc/api.htm?path=cid:38-apiId:339
	*/
	function taobaoke_items_detail_get_response($num_iids,$fields='click_url,shop_click_url,seller_credit_score,num_iid,title,nick,pic_url')
	{
		//参数数组
		$paramArr = array(
     						'app_key' => C("appKey"),
     						'method' => 'taobao.taobaoke.items.detail.get',
     						'format' => C("format"),
     						'v' => C("v"),
     						'sign_method'=>C("sign_method"),
     						'timestamp' => date('Y-m-d H:i:s'),
     						'fields' => $fields,
     						'num_iids' => $num_iids,
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
			if($result['taobaoke_items_detail_get_response']['total_results']>0)
			{
				$result=$result['taobaoke_items_detail_get_response']['taobaoke_item_details']['taobaoke_item_detail'][0];
			}
			else
			{
				$result=array();
			}
		}
		else
		{
			$result=array();
		}
		return $result;
	}

	/*
	   调用接口：taobao.taobaoke.items.detail.get
	   说明： 查询淘宝客推广商品详细信息
	   API用户授权类型：不需要
	   参考文档：http://api.taobao.com/apidoc/api.htm?path=cid:38-apiId:339
	*/
	function taobao_ump_promotion_get($num_iids)
	{
		//参数数组
		$paramArr = array(
     						'app_key' => C("appKey"),
     						'method' => 'taobao.ump.promotion.get',
     						'format' => C("format"),
     						'v' => C("v"),
     						'sign_method'=>C("sign_method"),
     						'timestamp' => date('Y-m-d H:i:s'),
     						'item_id' => $num_iids
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
			if(!empty($result['ump_promotion_get_response']['promotions']['promotion_in_item']['promotion_in_item']))
			{
				$result=$result['ump_promotion_get_response']['promotions']['promotion_in_item']['promotion_in_item'][0];
			}
			else
			{
				$result=array();
			}
		}
		else
		{
			$result=array();
		}
		return $result;
	}
}
