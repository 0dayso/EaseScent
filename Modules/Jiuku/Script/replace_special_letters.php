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
		$count = $DB->linkmysql('SELECT count(*) as count FROM  `jiuku_wine`');
		$count = $count[0]['count'];
		$mit = 1000;
		for($i=0;$i<$count;$i=$i+$mit){
			$res = $DB->linkmysql('SELECT `fname`,`id` FROM  `jiuku_wine` LIMIT '.$i.','.$mit);
			foreach($res as $key=>$val){
				echo ($key+1).' / '.($i).'-'.($i+$mit).' / '.$count.'	';
				$find		= array('â','é','è','Â','É','À','ë','á','í','ô','ñ','ó','ç','ã','ü','à','ä','ö','ì','ú','Í','Á','ň','Ó','È','ù','š','ž','Ü','ï','ê','ò','î','õ','¡','Ã','ÿ','û','Ö','å',' ','ß' ,'č','ǎ','œ' ,'Ñ','Ú','ć','Š','Ò','Ç','Ê','Ë','Ô','Î','Ä','Û','Ÿ','Ï','Õ','Œ', 'Č','Ù','Å','ý','Ì','（','）','‘'   ,'’'   ,'\''  ,'”','“','！','，','‐','–','´','­');
				$replace	= array('a','e','e','A','E','A','e','a','i','o','n','o','c','a','u','a','a','o','i','u','I','A','n','O','E','u','s','z','U','i','e','o','i','o','i','A','y','u','O','a',' ','ss','c','a','oe','N','U','c','S','O','C','E','E','O','I','A','U','Y','I','O','OE','C','U','A','y','I','(',')','\'\'','\'\'','\'\'','"','"','!',',','-','-','`','');
				$ename = str_replace($find,$replace,$val['fname']);
				$DB->linkmysql('UPDATE `jiuku_wine` SET `ename`=\''.$ename.'\' WHERE `id` = '.$val['id'].';');
				echo "\r\n";
			}
		}
	}
	public function _cfRedisStr($str) {
		$return = array();
		if($str){
			$len = mb_strlen($str,'utf-8');
			for($i=0;$i<$len;$i++){
				$return[] = mb_substr($str,$i,1,'utf-8');
			}
		}
		return $return;
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