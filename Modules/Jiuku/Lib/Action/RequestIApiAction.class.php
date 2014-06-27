<?php
/**
* @file RequestIApiAction.class.php 	#文件名
*
* @brief  请求 i.wine.cn API		#程序文件描述
*
* Copyright(C) 2010-2015 Easescent.com, Inc. or its affiliates. All Rights Reserved. 	#版权信息
*
* @version $Id$ 		#版本号，由svn功能自动生成，不用修改
* @author Goni, goni@sina.com 		#程序作者
* @date 2013-01-30 				#日期
*/

/**
* @class RequestIApiAction   	# 类名
* @brief 请求 i.wine.cn API
*
*/
class RequestIApiAction extends Action{

	/**
	* @brief _initialize	#方法名+描述
	*
	* @param $xxxxx {类型}	#方法参数描述，大括号内注明类型
	*
	* @return $xxxxxx{类型}	#返回值
	*/
	public function _initialize() {
	}

	/**
	* @brief grandcru_get_new_jiuping	得到最新酒评	加密action=	YENfQV8FeE9bX09ZXmNrWkMFTVhLRE5JWF91TU9edURPXXVAQ19aQ0RN	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	//
	public function grandcru_get_new_jiuping(){
		$limit = 10;
		$url = C('DOMAIN.I').'?m=api/client/grandcru.get_new_jiuping&bgw='.$_GET['bgw'].'&limit='.$limit.'&page='.$_GET['page'];
		if(!($data = CurlGet($url))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($return = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		import('@.ORG.Util.Page1');
		$p = new Page1($return['rst']['total_count'], $limit);
		$p->nowPage = $_GET['page'];
		$return['rst']['pageHtml'] = $p->pageHtml();
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}

	/**
	* @brief grandcru_get_jiup_by_id	得到最新酒评	加密action=	Y0BcQlwGe0xYXExaXWBoWUAGTltIR01KW1x2TkxddkNAXFl2S1B2QE0%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	//
	public function grandcru_get_jiup_by_id(){
		$limit = $_GET['page_limit'];
		$caname_idarr = D('WineCaname')->where(array('wine_id'=>array('in',$_GET['wine_idstr']),'status'=>'1','is_del'=>'-1'))->getfield('id',true);
		$cUrl = C('DOMAIN.I').'?m=api/client/grandcru.get_jiup_by_id&wine_id='.implode(',',$caname_idarr).'&limit='.$limit.'&page='.$_GET['page'];
		if(!($data = CurlGet($cUrl))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($return = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		import('@.ORG.Util.Page1');
		$p = new Page1($return['rst']['total_count'], $limit);
		$p->nowPage = $_GET['page'];
		$return['rst']['pageHtml'] = $p->pageHtml();

		import('@.ORG.Util.Page2');
		$p2 = new Page2($return['rst']['total_count'], $limit);
		$p2->nowPage = $_GET['page'];
		$return['rst']['pageHtml2'] = $p2->pageHtml();

		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
	}

	/**
	* @brief grandcru_is_attention	是否关注庄园	加密action=	bU5STFIIdUJWUkJUU25mV04IQFVGSUNEVVJ4TlR4RlNTQklTTkhJ	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	//
	public function grandcru_is_attention(){
		if($_SESSION["members"]["online"] == 0){
			$this->echo_exit(array('errorCode'=>600003,'errorStr'=>'No Login'));
		}
		$cUrl = C('DOMAIN.I').'User/Api/User.php?c=Follows&m=is_follow';
		$parame = C('I_PARAME.MINGZHUANG');
		$parame ['type'] = 'winery';
		$parame ['uid'] = $_SESSION['members']["mid"];
		$parame ['fid'] = $_GET['id'];
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlGet($cUrl))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));
	}

	/**
	* @brief grandcru_attention	关注庄园	加密action=	bk1RT1ELdkFVUUFXUG1lVE0LQ1ZFSkBHVlF7RVBQQUpQTUtK	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	//
	public function grandcru_attention(){
		if($_SESSION["members"]["online"] == 0){
			$this->echo_exit(array('errorCode'=>600003,'errorStr'=>'No Login'));
		}
		$cUrl = C('DOMAIN.I').'User/Api/User.php?c=Follows&m=create_thing';
		$parame = C('I_PARAME.MINGZHUANG');
		$parame ['type'] = 'winery';
		$parame ['uid'] = $_SESSION['members']["mid"];
		$parame ['id'] = $_GET['id'];
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlPost($cUrl,$parame))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));
	}

	/**
	* @brief grandcru_unattention	取消关注庄园	加密action=	bE9TTVMJdENXU0NVUm9nVk8JQVRHSEJFVFN5U0hHUlJDSFJPSUg%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	//
	public function grandcru_unattention(){
		if($_SESSION["members"]["online"] == 0){
			$this->echo_exit(array('errorCode'=>600003,'errorStr'=>'No Login'));
		}
		$cUrl = C('DOMAIN.I').'User/Api/User.php?c=Follows&m=destroy_thing';
		$parame = C('I_PARAME.MINGZHUANG');
		$parame ['type'] = 'winery';
		$parame ['uid'] = $_SESSION['members']["mid"];
		$parame ['id'] = $_GET['id'];
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlPost($cUrl,$parame))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));
	}

	/**
	* @brief grandcru_attention_user	关注庄园	加密action=	Y0BcQlwGe0xYXExaXWBoWUAGTltIR01KW1x2SF1dTEddQEZHdlxaTFs%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	//
	public function grandcru_attention_user(){
		$cUrl = C('DOMAIN.I').'User/Api/User.php?c=Follows&m=fans_thing';
		$parame = C('I_PARAME.MINGZHUANG');
		$parame ['type'] = 'winery';
		$parame ['id'] = $_GET['id'];
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlPost($cUrl,$parame))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));
	}

	/**
	* @brief country_comment	执行国家评论	加密action=	a0hUSlQOc0RQVERSVWhgUUgOQk5UT1VTWH5CTkxMRE9V	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	public function country_comment(){
		$cUrl = C('DOMAIN.I').'User/Api/Comment.php?c=Statuses&m=publish';
		$parame = C('I_PARAME.REGION');
		$parame['type'] = 'country';
		$parame['relation'] = $_GET['id'];
		$parame['uid'] = $_SESSION['members']["mid"];
		$parame['content'] = base64_encode(urldecode($_GET['content']));
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlPost($cUrl,$parame))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));

	}

	/**
	* @brief region_comment	执行产区评论	加密action=	aklVS1UPckVRVUVTVGlhUEkPUkVHSU9Of0NPTU1FTlQ%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	public function region_comment(){
		$cUrl = C('DOMAIN.I').'User/Api/Comment.php?c=Statuses&m=publish';
		$parame = C('I_PARAME.REGION');
		$parame['type'] = 'region';
		$parame['relation'] = $_GET['id'];
		$parame['uid'] = $_SESSION['members']["mid"];
		$parame['content'] = base64_encode(urldecode($_GET['content']));
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlPost($cUrl,$parame))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));
	}

	/**
	* @brief get_country_comment	获取国家评论	加密action=	b0xQTlAKd0BUUEBWUWxkVUwKQkBRekZKUEtRV1x6RkpISEBLUQ%3D%3D	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	public function get_country_comment(){
		$cUrl = C('DOMAIN.I').'User/Api/Comment.php?c=Statuses&m=rel';
		$parame = C('I_PARAME.REGION');
		$parame['type'] = 'country';
		$parame['rel'] = $_GET['id'];
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlPost($cUrl,$parame))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));
	}

	/**
	* @brief get_region_comment	获取产区评论	加密action=	bk1RT1ELdkFVUUFXUG1lVE0LQ0FQe1ZBQ01LSntHS0lJQUpQ	#方法名+描述
	*
	* @param 	#方法参数描述，大括号内注明类型
	*
	* @return echo{json}	#返回值
	*/
	public function get_region_comment(){
		$cUrl = C('DOMAIN.I').'User/Api/Comment.php?c=Statuses&m=rel';
		$parame = C('I_PARAME.REGION');
		$parame['type'] = 'region';
		$parame['rel'] = $_GET['id'];
		foreach($parame as $key=>$val){
			$cUrl .='&'.$key.'='.$val;
		}
		if(!($data = CurlPost($cUrl,$parame))){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Curl Fail'));
		}
		if(!($data = json_decode($data,true))){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Json Decode Fail'));
		}
		$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$data));
	}

	function echo_exit($arr){
		if(empty($_GET['callback'])){
			echo json_encode($arr);
		}else{
			echo $_GET['callback'].'('.json_encode($arr).')';
		}
		exit();
	}
}
