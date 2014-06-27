<?php
header("Content-Type:text/html; charset=utf-8");
set_time_limit(0);
echo 'Start...'."\r\n";
$ZlData = new ZlData();
$ZlData->index();
echo 'Over'."\r\n";
class ZlData{
	public function __construct(){
		$DB = new DB();
		$String = new String();
	}
	public function index(){
		$DB = new DB();
		$String = new String();
		$count = $DB->linkmysql('SELECT `wine_id` FROM  `jiuku_join_wine_refweb`  WHERE  `refweb_id` = 2 GROUP BY `wine_id`');
		$count = count($count);
		$mit = 1000;
		for($i=0;$i<$count;$i=$i+$mit){
			$res = $DB->linkmysql('SELECT `wine_id` FROM  `jiuku_join_wine_refweb`  WHERE  `refweb_id` = 2 GROUP BY `wine_id` LIMIT '.$i.','.$mit);
			foreach($res as $key=>$val){
				echo ($i).'-'.($i+$mit).' / '.$count.'	';
				$val = $String->myReplace($val);
				echo 'id:'.$val['wine_id'].':	get wine_fname';
				$wine_res = $DB->linkmysql('SELECT `id`,`fname` FROM `jiuku_wine` WHERE `id` = '.$val['wine_id']);
				if(!$wine_res) continue;
				$wine_fname = $wine_res[0]['fname'];
				echo '	get winery_fname';
				$jion_winery_res = $DB->linkmysql('SELECT `winery_id` FROM `jiuku_join_wine_winery` WHERE `wine_id` = '.$val['wine_id']);
				if(!$jion_winery_res) continue;
				$winery_id = $jion_winery_res[0]['winery_id'];
				$winery_res = $DB->linkmysql('SELECT `fname` FROM `jiuku_winery` WHERE `id` = '.$winery_id);
				if(!$winery_res) break;
				$winery_fname = $winery_res[0]['fname'];
				if(trim($winery_fname) == '' || trim($wine_fname) == '') continue;
				$new_wine_fname = $winery_fname.' '.$wine_fname;
				if(count(explode($winery_fname,$wine_fname)) > 1){
					echo '	no'."\r\n";
					//echo '	'.$wine_fname."\r\n	".$new_wine_fname."\r\n";
					continue;
				}
				echo '	merge';
				$winery_res = $DB->linkmysql('UPDATE `jiuku_wine` SET `fname`=\''.trim(str_replace('\'','\'\'',$new_wine_fname)).'\' WHERE `id`='.$val['wine_id']);
				echo "\r\n";
				//echo '	'.$wine_fname."\r\n	".$new_wine_fname."\r\n";
			}
		}
	}
}
class String{
	function myReplace($array){
		$f = array('\'');
		$r = array('\'\'');
		foreach($array as $key=>$val){
			$array[$key] = trim(str_replace($f,$r,$val));
		}
		return $array;
	}
}
class DB{
	function linkmysql($sql){
		$connect=mysql_connect('localhost','root','vertrigo');
		if(!$connect)
		{
			die(''.mysql_error());
			return false;
		}
		$db_selected=mysql_select_db('eswine_ww',$connect);
		if(!$db_selected)
		{
			die(''.mysql_error());
			return false;
		}
		mysql_query("set names utf8"); 
		$result=mysql_query($sql,$connect);
		if(!$result)
		{
			echo $sql;
			die('sql Error!'.mysql_error());
			return false;
		}
		if($result === true){
			$return1 = true;
		}else{
			$return1 = array();
			while($rs=mysql_fetch_array($result,MYSQL_ASSOC)){
			$return1[]=$rs;
			}
		}
		mysql_close($connect);
		usleep(20000);
		return $return1;
	}
}
?>