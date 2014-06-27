<?php

/**
 * Jiuku项目的公共控制器
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
		import('@.ORG.Util.String');
	}
	function linkRedis(){
		if(!extension_loaded('redis')){
			return false;exit();
		}
		$Redis = new Redis();
		$Rediscfg = C('REDIS_CONFIG');
		try{
			$Redis->connect($Rediscfg['host'], $Rediscfg['port']);
		}catch(Exception $e){
			return false;exit();
		}
		return $Redis;
	}

	/**
	 * 上传文件
	 */
	public function _uploads() {
		$type = isset($_GET['type']) ? Input::getVar($_GET['type']).'/' : '';
		import('@.ORG.Util.Upload');
		$upload = new Upload();
		$cfg = array(
			'ext' => C('UPLOAD_ALLOW_EXT'),
			'size' => C('UPLOAD_MAXSIZE'),
			'path' => C('UPLOAD_PATH').$type,
		);
		$upload->config($cfg);
		$rest = $upload->uploadFile('imgFile');
		if($rest['errno']) {
			$result = array(
				'error' => 1,
				'message' => $upload->error(),
			);
			$this->_ajaxDisplay($result);
		}
		$result = array(
			'error' => 0,
			'url' => C('DOMAIN.UPLOAD') . 'Jiuku/' . $type . $rest['path'],
			'filename' => $rest['path'],
		);
		$this->_ajaxDisplay($result);
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
	 * 生成多条数据
	 */
	protected function _list($model, $map, $listRow = 15, $url, $order = 'id DESC') {
		//if(!is_object($model))
		//dump($model);exit;
		$map['is_del'] = '-1';
		//取得满足条件的记录数
		$count = $model->where($map)->count('*');
		$voList = array();
		import ( "@.ORG.Util.Page" );
		$p = new Page($count, $listRow);
		//分页查询数据
		$voList = $model->where($map)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
		//分页跳转的时候保证查询条件
		if($url) {
			$p->parameter .= $url;
		}
		//分页显示
		$page = $p->show();
		$page_parameter = $p->parameter.'&p='.$p->nowPage;
		$backpage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$page_parameter;
		if($this->isPost()){
			header('Location: '.$backpage);	exit;
		}
		$this->assign("backpage",base64_encode($backpage));
		$this->assign("page_parameter_base64",base64_encode($page_parameter));
		$this->assign("page", $page );
		return $voList;
	}
	protected function _parse_url($url){
		$url_arr = explode('&',$url);
		array_shift($url_arr);
		foreach($url_arr as $val){
			$site = strpos($val,'=');
			$_GET[substr($val,0,$site)] = substr($val,$site+1,strlen($val));
		}
	}

	/**
	 * 新增数据
	 */
	protected function _insert($model, $data = '') {
		$insert_data = $model->create($data);
		if ($insert_data === false) {
			$this->_jumpGo($model->getError(), 'error');
		}
		$insert_data['add_time'] = $insert_data['last_edit_time'] = time();
		$insert_data['add_aid'] = $insert_data['last_edit_aid'] = $_SESSION['admin_uid'];
		//保存当前数据对象
		$list = $model->add($insert_data);
		if ($list !== false) { //保存成功
			return $list;
		} else {
			$this->_jumpGo($model->getError(), 'error');
		}
	}

	/**
	 * 更新数据
	 */
	protected function _update($model, $data = '') {
		$update_data = $model->create($data);
		if ($update_data === false) {
			$this->_jumpGo($model->getError(), 'error');
		}
		$update_data['last_edit_time'] = time();
		$update_data['last_edit_aid'] = $_SESSION['admin_uid'];
		// 更新数据
		$list = $model->save($update_data);
		if ($list !== false) {
			return true;
		} else {
			$this->_jumpGo($model->getError(), 'error');
		}
	}

	/**
	 * 后台通用跳转函数
	 */
	protected function _jumpGo($message, $mode = 'info', $url = 'javascript:history.go(-1)', $time=1) {
		$html = '<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Refresh" contect="'.$time.';url='.$url.'"><meta http-equiv="Content-Type"content="text/html; charset=utf-8"/><title>提示信息</title><style type="text/css">body{background-color:#fff;margin:40px;font:13px/20px Arial,sans-serif;color:#4F5155}a{color:#00F;background-color:transparent;font-weight:normal;text-decoration:none}h1{color:#444;background-color:transparent;border-bottom:1px solid#D0D0D0;font-size:19px;font-weight:normal;margin:0 0 14px 0;padding:14px 15px 10px 15px}#body{margin:0 15px 0 15px}p.footer{text-align:right;font-size:11px;border-top:1px solid#D0D0D0;line-height:32px;padding:0 10px 0 10px;margin:20px 0 0 0}#container{margin:10px;border:1px solid#D0D0D0;-webkit-box-shadow:0 0 8px#D0D0D0}h1 span{height:27px;line-height:27px;vertical-align:middle;padding-left:32px;display:block}.error{background:url('.C('TMPL_PARSE_STRING.__PUBLIC__').'admin/images/error.gif)no-repeat}.succeed{background:url('.C('TMPL_PARSE_STRING.__PUBLIC__').'admin/images/succeed.gif)no-repeat}.info{background:url(/public/admin/images/info.gif)no-repeat}</style></head><body><div id="container"><h1><span class="'.$mode.'">'.$message.'……</span></h1><div id="body"><p><a href="'.$url.'">如果您的浏览器没有自动跳转，请点击此链接...</a></p></div><p class="footer">&copy 2012 wine.cn</p></div><script>var code = \'location.href="'.$url.'";\'; setTimeout(code, 1000);</script></body></html>';
        $this->display('','','',$html);
        exit();
	}

	function gmap(){
		$this->assign('input_id',$_GET['input_id']);
		$this->assign('lat_id',$_GET['lat_id']);
		$this->assign('lng_id',$_GET['lng_id']);
		$this->assign('img_id',$_GET['img_id']);
		$this->display();
	}
	function GrabImage($url='',$path='',$ext='jpg',$delimg='')
	{
		if($url == '' || $path == '') return '';
		$filename=rand(10000,99999) . substr(md5(time()), 0, 16).'.'.$ext;
		$filenamepath = mb_substr($filename,0,2,'utf-8').'/'.mb_substr($filename,2,2,'utf-8').'/';
		$fullpath = C('UPLOAD_PATH').$path.$filenamepath;
		if(!is_dir($fullpath)){
            mkdir($fullpath, 0777 ,true);
            @touch($fullpath.'/index.html');
        }
		ob_start();
		if(readfile($url)== false) return '';
		$img = ob_get_contents();
		ob_end_clean();

		$fp=@fopen($fullpath.$filename, "w");
		fwrite($fp,$img);
		fclose($fp);
		if(file_exists($fullpath.$filename)){
			unlink($path.$delimg);
			return $filenamepath.$filename;
		}else{
			return '';
		}
	}

    /**
     * http传递参数post变get
     */
    function listpage_post_to_get(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        foreach($_POST as $key=>$val){
            $url .= '&' . $key . '=' . $val;
        }
        header('Location: ' . $url);
        exit;
    }

	//字符截取
	function mystrcut($string,$length,$etc='')	{
		$result= '';
		$string = html_entity_decode(trim(strip_tags($string)),ENT_QUOTES,'UTF-8');
		$strlen = strlen($string);
		for($i=0; (($i<$strlen)&& ($length> 0));$i++){
			$number=strpos(str_pad(decbin(ord(substr($string,$i,1))), 8, '0', STR_PAD_LEFT), '0');
			if($number){
				if($length   <   1.0){
					break;
				}
				$result   .=   substr($string, $i, $number);
				$length   -=   1.0;
				$i   +=   $number   -   1;
			}
			else{
				$result   .=   substr($string, $i, 1);
				$length   -=   0.5;
			}
		}
		$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
		if($i<$strlen){
			$result   .=   $etc;
		}
		return   $result;
	}

    /**
     * 过滤post，get
     */
	function filter_http_data_ini(){
        $_POST = $this->filter_http_data($_POST);
        $_GET = $this->filter_http_data($_GET);
    }
    function filter_http_data($data){
        foreach($data as $key=>$val){
            if(is_array($val))
                $data[$key] = $this->filter_http_data($val);
            else
                $data[$key] = get_magic_quotes_gpc() ? trim(stripslashes($val)) : trim($val);
        }
        return $data;
    }

    /**
     * 列表页将POST参数改为GET
     */
    function post_to_get(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        foreach($_POST as $key=>$val){
            $url .= '&' . $key . '=' . $val;
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * 字符窜过滤
     */
    function sanitize($input) {
        if (is_array($input)) {
            foreach($input as $var=>$val) {
                $output[$var] = $this->sanitize($val);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            $search = array(
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
            );
            $input = preg_replace($search, '', $input);
            //$input = mysql_real_escape_string($input);
            $output = trim($input);
        }
        return $output;
    }

}
