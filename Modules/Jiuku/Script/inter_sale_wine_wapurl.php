<?php
set_time_limit(0);
header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

include("common.php");

//echo &date("Y-m-d H:i:s")."\r\n";
echo 'Start...'."\r\n";
$Index = new Index();
echo 'Over'."\r\n";
//echo &date("Y-m-d H:i:s")."\r\n";

class Index{
	public function index(){
		//网酒网
		$this->wjw();
		//酒美网
		$this->jmw();
	}

	public function wjw(){
        $_DB = new DB();
		$res = $_DB->linkmysql('SELECT *  FROM  `jiuku_agents_internet_sales_wine` WHERE `url` like \'%http://www.wangjiu.com/product/product-pid-%\'');
		foreach($res as $key=>$val){
			echo 'wangjiu:	'.($key+1).'	/'.count($res)."\r\n";
			$mark = str_replace(array('http://www.wangjiu.com/product/product-pid-','.html'),'',$val['url']);
			$wap_url = 'http://wap.wangjiu.com/productinfo.html?pid='.$mark;
			$_DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `wap_url`=\''.$wap_url.'\' WHERE `id`='.$val['id']);
		}

	}
	public function jmw(){
        $_DB = new DB();
		$res = $_DB->linkmysql('SELECT *  FROM  `jiuku_agents_internet_sales_wine` WHERE `url` like \'%http://www.winenice.com/product/pro_detail_-%\'');
		foreach($res as $key=>$val){
			echo 'winenice:	'.($key+1).'	/'.count($res)."\r\n";
			$mark = str_replace(array('http://www.winenice.com/product/pro_detail_-','.shtml'),'',$val['url']);
			$wap_url = 'http://m.winenice.com/Product/Product_Information-'.$mark.'.html';
			$_DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `wap_url`=\''.$wap_url.'\' WHERE `id`='.$val['id']);
		}

	}
}
?>