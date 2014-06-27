<?php
/*
 *	Class WatermarkAction 
 *	@author liuzm <liuzm@eswine.com>
 *	@date	2013-11-20
 *	@Original tiger <ji.xiaod@gmail.com>
 *	@Original date 2011.06.13
 *	
 * */
class WaterAction extends CommonAction{
	public function index() {
		$path = dirname(__FILE__);
		$config = explode( ",", file_get_contents($path.'/watermark.config') );
		foreach( $config as $key=>$value ){
			$config[$key] = substr( strstr( $value, '='), 1 ); 
		}

		$this->assign('path', $path);
		$this->assign('config',$config);
		$this->display();
	}

	public function saveWatermarkConfig() {
		$type = !empty($_POST['type']) ? $_POST['type']:'';
		$markstring = !empty($_POST['markstring']) ? $_POST['markstring']:'';
		$fontsize = !empty($_POST['fontsize']) ? $_POST['fontsize']:'';
		$position = !empty($_POST['position']) ? $_POST['position']:'';
		$markimage = !empty($_POST['markimage']) ? $_POST['markimage']:'';
        $fontcolor = !empty($_POST['fontcolor']) ? $_POST['fontcolor']:'';
        $fontstyle = !empty($_POST['fontstyle']) ? $_POST['fontstyle']:'';

		$path = dirname(__FILE__);
		$config = explode( ",", file_get_contents($path.'/watermark.config') );
		file_put_contents($path.'/watermark.config', 'type='.$type.',markstring='.$markstring.',fontsize='.$fontsize.',position='.$position.',markimage='.$markimage.',fontcolor='.$fontcolor.',fontstyle='.$fontstyle, FILE_USE_INCLUDE_PATH);
        echo '1';
	}
}
?>