<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 所有项目的公共函数库,需要自动加载
 *
 * @category   Wine
 * @package    Core
 * @author     mengfk<dreamans@163.com>
 */

/**
 * 读取Block区块
 */
function getBlock($bid) {
    $path = $GLOBALS['BLOCK_CPATH'];
    $file = $path .'/'.$bid.'.html';
    if(is_file($file)) {
        include $file;
    }
    return ;
}

/**
 * 拼装Url函数
 */
function Url($url, $var = '', $domain = true) {
	$info =  parse_url($url);
    $url =  !empty($info['path']) ? $info['path'] : ACTION_NAME;
	$urls = explode('/', trim($url, '/'));
	$vars = array();
	$vars[C('VAR_ACTION')] = !empty($urls) ? array_pop($urls) : ACTION_NAME;
    $vars[C('VAR_MODULE')] = !empty($urls) ? array_pop($urls) : MODULE_NAME;
    $vars['app'] = !empty($urls) ? array_pop($urls) : APP_NAME;
	//将数组翻转
	$nvars = array();
	foreach($vars as $k => $v) {
		$tmp = array($k => $v);
		$nvars = array_merge($tmp, $nvars);
	}
	//合并querystring
    if(is_string($var)) {
        parse_str($var,$var);
	} elseif(!is_array($var)) {
		$var = array();
	}
    if(isset($info['query'])) {
        parse_str($info['query'],$param);
        $var = array_merge($param,$var);
    }
    //组装url
    $url = __APP__.'?'.http_build_query($nvars);
    if(!empty($var)) {
        $var = http_build_query($var);
        $url   .= '&'.$var;
	}
	if($domain) {
		$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
		$protocol = ($port == 433) ? 'https://' : 'http://';
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']: '';
		$url = $protocol.$host.(($port != 80) ? ':'.$port: '').$url;
	}
	return $url;
}

/**
 * API方式访不同项目间通信
 * 具体访问算法请参阅http://wiki.quwine.com/index.php?title=%E9%80%B8%E9%A6%99%E7%BD%91%E9%A1%B9%E7%9B%AEAPI%E5%BC%80%E5%8F%91%E8%A7%84%E8%8C%83%28%E4%BB%85%E4%BE%9B%E5%8F%82%E8%80%83%29
 * 错误代码:
 *     200002: sign签名为空
 *     200003: sign签名错误,可能是非法提交
 */
function Api($app, $method, $param = array()) {
    //sign
    $s = '';
    $key = $GLOBALS['APP_KEY'][$app];
    if(is_array($param)) {
        foreach($param as $key => $val) {
            $s .=$key.$val;
        }
    }
    $sign = sha1($s.$key);
    //POST require
    $param['sign'] = $sign;
    $url = Url("{$app}/AppAccess/{$method}");
    $result = CurlPost($url, $param);
    return json_decode($result, true);
}

/**
 * Curl Post
 */
function CurlPost($url, $data=array(), $timeout = 10, $header = "") {
    $ssl = substr($url, 0, 8) == 'https://' ? true : false;
    $post_string = http_build_query($data);  
    $ch = curl_init();
    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    }
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
	curl_setopt($ch, CURLOPT_USERPWD, "thisuser:Gst43sB");
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**
 * Curl Post
 */
function CurlGet($url, $timeout = 10, $header = "") {
    $ssl = substr($url, 0, 8) == 'https://' ? true : false;    
    $post_string = http_build_query($data);  
    $ch = curl_init();
    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
	curl_setopt($ch, CURLOPT_USERPWD, "thisuser:Gst43sB");
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
//登录并获取用户信息
function Login($username,$password,$signkey,$call,$account_url){
	$data = array(
				  'key'=>'login',
				  'condition'=>array(
									 'username'=>$username,
									 'password'=>$password
									 ),
				  'limit'=>1
				  );
	$dataBase64 = base64_encode(json_encode($data));
	$sha1 = sha1(json_encode($data).$signkey.date('YmdHi',time()));
	$md5sign = md5(md5($username.'-'.'JSON').'-'.$signkey.'-'.date('YmdHi'));
	$params = array("d"=>$dataBase64,"s"=>$sha1);
	$url = $account_url.'/api/call/'.$call.'&d='.$dataBase64.'&s='.$sha1;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_exec($ch);
	$lgDataJson = curl_multi_getcontent($ch);
	curl_close($ch);
	$lgData = json_decode($lgDataJson, true);
	if($lgData['status'] == 1){
		$_SESSION["members"]["uid"] = $lgData["data"]["mid"];
		$_SESSION["members"]["username"] = $lgData["data"]["username"];
		$_SESSION["members"]["nickname"] = $lgData["data"]["member_nick"];
		$_SESSION["members"]["sid"] = $lgData["data"]['sid'];
		return array('status'=>1,'data'=>$_SESSION['members']);
	}else{
		unset($_SESSION['members']);
		return array('status'=>0,'data'=>$_SESSION['members']);
	}
}
//判断登录并获取用户信息
function isLogin(){
	if(isset($_SESSION["members"]["uid"]) &&
	   isset($_SESSION["members"]["username"]) &&
	   isset($_SESSION["members"]["nickname"]) &&
	   isset($_SESSION["members"]["sid"])){
		return array('status'=>1,'data'=>$_SESSION['members']);
	}else{
		unset($_SESSION['members']);
		return array('status'=>0,'data'=>$_SESSION['members']);
	}
}

function snsApi($url,$access_token,$token_secret,$type,$params){
	switch($type){
		case 'c1' ://评论array($uid,base64_encode $content,$type);
			$url = $url.'/User/Api/Comment.php?c=Statuses&m=publish&access_token='.$access_token.'&token_secret='.$token_secret;
			$result = CurlPost($url,$params);
		break;
		case 'r1' ://回复
		break;
		case 'dc1'://删除评论
		break;
		case 'dr1'://删除回复
		break;
		case "gc1"://根据用户ID获取评论列表
			$url = $url.'/User/Api/Comment.php?c=Statuses&m=user_timeline&access_token='.$access_token.'&token_secret='.$token_secret.'&uid='.$params[0];
			if(isset($params[1])) $url = $url.'&time='.$params[1];
			if(isset($params[2])) $url = $url.'&count='.$params[2];
			if(isset($params[3])) $url = $url.'&page='.$params[3];
			dump($url);
			$result = file_get_contents($url);
		break;
		case "gc2"://根据关联ID获取评论列表
			$url = $url.'/User/Api/Comment.php?c=Statuses&m=rel&access_token='.$access_token.'&token_secret='.$token_secret.'&type='.$params[0].'&rel='.$params[1];
			if(isset($params[2])) $url = $url.'&time='.$params[2];
			if(isset($params[3])) $url = $url.'&replyCount='.$params[3];
			if(isset($params[4])) $url = $url.'&count='.$params[4];
			if(isset($params[5])) $url = $url.'&page='.$params[5];
			$result = file_get_contents($url);
		break;
		case "gr1"://根据用户ID获取回复列表
		break;
		case "gr2"://根据评论ID获取回复列表
		break;
	}
	return $result;
}

