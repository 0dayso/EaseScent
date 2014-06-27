<?php

/**
 * 公共控制器
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
	 * 后台通用跳转函数
	 */
	protected function _jumpGo($message, $mode = 'info', $url = 'javascript:history.go(-1)', $time=1) {
		$html = '<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Refresh" contect="'.$time.';url='.$url.'"><meta http-equiv="Content-Type"content="text/html; charset=utf-8"/><title>提示信息</title><style type="text/css">body{background-color:#fff;margin:40px;font:13px/20px Arial,sans-serif;color:#4F5155}a{color:#00F;background-color:transparent;font-weight:normal;text-decoration:none}h1{color:#444;background-color:transparent;border-bottom:1px solid#D0D0D0;font-size:19px;font-weight:normal;margin:0 0 14px 0;padding:14px 15px 10px 15px}#body{margin:0 15px 0 15px}p.footer{text-align:right;font-size:11px;border-top:1px solid#D0D0D0;line-height:32px;padding:0 10px 0 10px;margin:20px 0 0 0}#container{margin:10px;border:1px solid#D0D0D0;-webkit-box-shadow:0 0 8px#D0D0D0}h1 span{height:27px;line-height:27px;vertical-align:middle;padding-left:32px;display:block}.error{background:url('.C('TMPL_PARSE_STRING.__PUBLIC__').'admin/images/error.gif)no-repeat}.succeed{background:url('.C('TMPL_PARSE_STRING.__PUBLIC__').'admin/images/succeed.gif)no-repeat}.info{background:url(/public/admin/images/info.gif)no-repeat}</style></head><body><div id="container"><h1><span class="'.$mode.'">'.$message.'……</span></h1><div id="body"><p><a href="'.$url.'">如果您的浏览器没有自动跳转，请点击此链接...</a></p></div><p class="footer">&copy 2012 wine.cn</p></div><script>var code = \'location.href="'.$url.'";\'; setTimeout(code, 1000);</script></body></html>';
        $this->display('','','',$html);
        exit();
	}
	function baidumap(){
		$this->assign('id',$_GET['id']);
		$this->display();
	}
	//中国地图层级结构
	function arealevelCn(){
		$list = M('ArealevelCn','cmn_')->order('fc ASC')->select();
		$nlist = array();
		foreach($list as $val){
			$nlist[$val['id']] = $val;
		}
		$id = max(1,intval($_GET['id']));
        $_GET['showlev'] = in_array($_GET['showlev'], array('0','1','2','3')) ? $_GET['showlev'] : 3;
        $_GET['selectlev'] = in_array($_GET['selectlev'], array('0','1','2','3')) ? $_GET['selectlev'] : 0;
		$res = $nlist[$id];
		$list = array();
		foreach($nlist as $val){
			if(($val['pid'] == $id) && ($val['lev'] <= $_GET['showlev'])){
				$list[] = $val;
			}
		}
		$sort = array();
		$mark = $id;
		while($mark != 0){
			$sort[] = $nlist[$mark];
			$mark = $nlist[$mark]['pid'];
		}
		//$break_res = $sort[1];
		$sort = array_reverse($sort);
		$this->assign('res',$res);
		//$this->assign('break_res',$break_res);
		$this->assign('sort',$sort);
		$this->assign('list',$list);
		$this->assign('mark',$_GET['mark']);
		$this->display();
	}
	function GrabImage($url='',$path='',$ext='jpg',$delimg=''){
		if($url == '' || $path == '') return '';
		$im = @ImageCreateFromjpeg($url);
		if($im === false)	return '';
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
}
