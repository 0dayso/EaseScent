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
	function get_wine_caname(){
		$DB = new DB();
		$String = new String();
		$count = $DB->linkmysql('SELECT count(*) AS count FROM `jiuku_wine`');
		$count = $count[0]['count'];
		$mit = 1000;
		for($i=0;$i<$count;$i=$i+$mit){
			$res = $DB->linkmysql('SELECT * FROM `jiuku_wine` LIMIT '.$i.','.$mit);
			foreach($res as $key=>$val){
				echo '	'.($key+1).' / '.($i).'-'.($i+$mit).' / '.$count.'	'."\r\n";
				$val = $String->myReplace($val);
				$val['is_merge'] = $val['merge_id'] ? '1' : '-1';
				$DB->linkmysql('INSERT INTO `jiuku_wine_caname` (`cname`,`fname`,`ename`,`wine_id`,`wine_hid`,`mark`,`status`,`is_del`,`is_merge`) VALUES (\''.$val['cname'].'\',\''.$val['fname'].'\',\''.$val['ename'].'\','.$val['id'].','.$val['id'].',\'0\',\''.$val['status'].'\',\''.$val['is_del'].'\',\''.$val['is_merge'].'\')');
				if($val['aname']){
					$aname_arr = explode(';',$val['aname']);
					foreach($aname_arr as $k=>$v){
						$DB->linkmysql('INSERT INTO `jiuku_wine_caname` (`cname`,`fname`,`ename`,`wine_id`,`wine_hid`,`mark`,`status`,`is_del`,`is_merge`) VALUES (\''.$v.'\',\''.$val['fname'].'\',\''.$val['ename'].'\','.$val['id'].','.$val['id'].',\'0\',\''.$val['status'].'\',\''.$val['is_del'].'\',\''.$val['is_merge'].'\')');
					}
				}
			}
		}
	}
	function get_agents_i_wine_caname(){
		$DB = new DB();
		$String = new String();
		$count = $DB->linkmysql('SELECT count(*) AS count FROM `jiuku_agents_internet_sales_wine`');
		$count = $count[0]['count'];
		$mit = 1000;
		for($i=0;$i<$count;$i=$i+$mit){
			$res = $DB->linkmysql('SELECT * FROM `jiuku_agents_internet_sales_wine` LIMIT '.$i.','.$mit);
			foreach($res as $key=>$val){
				echo '	'.($key+1).' / '.($i).'-'.($i+$mit).' / '.$count.'	'."\r\n";
				$val = $String->myReplace($val);
				$is_cname_exist = $DB->linkmysql('SELECT * FROM `jiuku_wine_caname` WHERE `wine_id` = '.$val['wine_id'].' AND `cname` = \''.$val['cname'].'\'');
				$wine_res = $DB->linkmysql('SELECT * FROM `jiuku_wine` WHERE `id` = '.$val['wine_id'].'');
				$wine_res[0]['is_merge'] = $wine_res[0]['merge_id'] ? '1' : '-1';
				if(count($is_cname_exist) == 0){
					$is_nullcname_exist = $DB->linkmysql('SELECT * FROM `jiuku_wine_caname` WHERE `wine_id` = '.$val['wine_id'].' AND `cname` = \'\'');
					if(count($is_nullcname_exist) == 0){
						$DB->linkmysql('INSERT INTO `jiuku_wine_caname` (`cname`,`fname`,`ename`,`wine_id`,`mark`,`status`,`is_del`,`is_merge`) VALUES (\''.$val['cname'].'\',\''.str_replace('\'','\'\'',$wine_res[0]['fname']).'\',\''.str_replace('\'','\'\'',$wine_res[0]['ename']).'\','.$wine_res[0]['id'].',\'1\',\''.$wine_res[0]['status'].'\',\''.$wine_res[0]['is_del'].'\',\''.$wine_res[0]['is_merge'].'\')');
						$caname_id = $DB->linkmysql('SELECT MAX(id) as id FROM `jiuku_wine_caname`');
						$caname_id = $caname_id[0]['id'];
					}else{
						$DB->linkmysql('UPDATE `jiuku_wine_caname` SET `cname` = \''.$val['cname'].'\',`mark` = \'1\' WHERE `id` = '.$is_nullcname_exist[0]['id']);
						$caname_id = $is_nullcname_exist[0]['id'];
					}
				}else{
					$DB->linkmysql('UPDATE `jiuku_wine_caname` SET `mark` = \'1\' WHERE `id` = '.$is_cname_exist[0]['id']);
					$caname_id = $is_cname_exist[0]['id'];
				}
				$DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `wine_caname_id` = '.$caname_id.' WHERE `id` = '.$val['id']);
			}
		}
	}
	public function index(){
		$DB = new DB();
		$String = new String();
		//获取标准酒款中文别名
		echo 'get wine caname	';
		$this->get_wine_caname();
		echo "\r\n";
		//获取代理商网络销售酒款中文别名
		echo 'get agents i wine caname	';
		$this->get_agents_i_wine_caname();
		echo "\r\n";
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