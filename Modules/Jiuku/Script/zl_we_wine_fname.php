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
		$count = $DB->linkmysql('SELECT `wine_id` FROM  `jiuku_join_wine_refweb`  WHERE  `refweb_id` = 1 GROUP BY `wine_id`');
		$count = count($count);
		$mit = 1000;
		for($i=0;$i<$count;$i=$i+$mit){
			$res = $DB->linkmysql('SELECT `wine_id` FROM  `jiuku_join_wine_refweb`  WHERE  `refweb_id` = 1 GROUP BY `wine_id` LIMIT '.$i.','.$mit);
			foreach($res as $key=>$val){
				echo ($i).'-'.($i+$mit).' / '.$count.'	';
				$val = $String->myReplace($val);
				echo 'id:'.$val['wine_id'].':	get wine_fname';
				$wine_res = $DB->linkmysql('SELECT `id`,`fname` FROM `jiuku_wine` WHERE `id` = '.$val['wine_id']);
				if(!$wine_res) continue;
				$wine_fname = $wine_res[0]['fname'];
				echo '	get winery_fname';
				$jion_region_res = $DB->linkmysql('SELECT `region_id` FROM `jiuku_join_wine_region` WHERE `wine_id` = '.$val['wine_id']);
				if(!$jion_region_res) continue;
				$region_id = $jion_region_res[0]['region_id'];
				$region_res = $DB->linkmysql('SELECT `fname` FROM `jiuku_region` WHERE `id` = '.$region_id);
				if(!$region_res) continue;
				$region_fname = $region_res[0]['fname'];
				if(trim($region_fname) == '' || trim($region_fname) == '') continue;
				$new_wine_fname = $wine_fname.' '.$region_fname;
				if(count(explode($region_fname,$wine_fname)) > 1){
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