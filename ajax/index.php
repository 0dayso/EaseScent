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
 * 逸香网Ajax接口提交入口
 * 接口请求方式:
 *   HTTP POST
 * 请求参数：
 *   action={经过加密的字符串},参数还原后是这种形式 项目英文名/控制器/控制方法 例如(News/Comment/lists);
 *   其他参数以POST方式请求,程序会过滤接口$_GET全局变量
 * 数据返回：
 *   在这里对返回的数据做下规范：
 *   1.返回JSON串
 *   2.返回格式类似{"errorCode":0, "errorStr":"", "result":""}
 * 错误代码返回：
 *   300001: 提交的Action错误
 *
 * @category   Wine
 * @package    Ajax
 * @author     mengfk<dreamans@163.com>
 */

//程序物理目录
define('CODE_RUNTIME_PATH', substr(dirname(__FILE__),0, -5));
//echo urlencode(WineAjaxAuthEncode("55"));
//exit();
/**
 * 对参数进行解析过滤
 */
$action = (isset($_GET['action']) && !empty($_GET['action'])) ? $_GET['action']:'';
$action = WineAjaxAuthDecode($action);
$actArr = explode('/', $action);
if(count($actArr) != 3) {
	die(json_encode(array('errorCode' => 300001, 'errorStr' => 'Action is wrong')));
}
//unset($_GET);
$appName = $actArr[0];
$_GET['m'] = $actArr[1];
$_GET['a'] = $actArr[2];

//app_name
define('APP_NAME', $appName);

//引入运行程序
require CODE_RUNTIME_PATH . '/Core/Common/runtime.php';

/**
 * 解密API参数
 * 加密算法：
 * 取得参数长度，参数ascii分别与长度进行异或操作取得新字符串，进行base64编码
 * 解密方法与加密方法相反
 */
function WineAjaxAuthDecode($auth) {
	$auth = base64_decode($auth);
	$len = strlen($auth);
	$string = '';
	for($i = 0; $i < $len; $i++) {
		$char = $auth[$i];
		$string .= chr(ord($char)^$len);
	}
	return $string;
}

/**
 * 加密函数参考
 */
function WineAjaxAuthEncode($string) {
	$len = strlen($string);
	$auth = '';
	for($i = 0; $i < $len; $i++) {
		$char = $string[$i];
		$auth .= chr(ord($char)^$len);
	}
	return base64_encode($auth);
}
