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
        $count = $DB->linkmysql('SELECT * FROM  `jiuku_agents_internet_sales_wine` ');
        $count = count($count);
        $mit = 1000;
        for($i=0;$i<$count;$i=$i+$mit){
            $res = $DB->linkmysql('SELECT * FROM  `jiuku_agents_internet_sales_wine`   LIMIT '.$i.','.$mit);
            foreach($res as $key=>$val){
                //$val = $String->myReplace($val);
                echo ($key+1).' / '.($i).'-'.($i+$mit).' / '.$count.'  ';
                $crawl_res = $DB->linkmysql('SELECT * FROM `jiuku_crawl_wine_data` WHERE `url` = \''.$val['url'].'\'');
                if($crawl_res){
                    $wine_img_res = $DB->linkmysql('SELECT * FROM `jiuku_agents_internet_sales_wine_img` WHERE `internet_sales_wine_id`='.$val['id']);
                    $this->index2($val,$wine_img_res,$crawl_res[0]);
                }
                echo "\r\n";
            }
        }
    }
    public function index2($wine_res,$img_res,$crawl_res){
        $DB = new DB();
        $img_str = $crawl_res['img'];
        $addimg = array();
        foreach(explode(',',$img_str) as $key=>$val){
            $filename = isset($img_res[$key]) ? $img_res[$key]['filename'] : '';
            $grab_info = $this->GrabImage($val,$filename);
            if($grab_info[0] === false){
                $DB->linkmysql('INSERT INTO `error_img` (`url`,`img`) VALUES (\''.$grab_info[1].'\',\''.$val.'\')');
            }
            if(isset($img_res[$key])){
                $DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine_img` SET `is_del`=\'-1\',`filename`=\''.$grab_info[1].'\' WHERE `id`='.$img_res[$key]['id']);
            }else{
                $DB->linkmysql('INSERT INTO `jiuku_agents_internet_sales_wine_img` (`filename`,`internet_sales_wine_id`) VALUES (\''.$grab_info[1].'\',\''.$wine_res['id'].'\')');
            }
        }

    }
    function GrabImage($url='',$filenamestr,$ext='jpg') {
        if($filenamestr){
            $filenamearr = explode('/',$filenamestr);
            $filename = $filenamearr[count($filenamearr)-1];
        }else{
            $filename= rand(10000,99999) . substr(md5(time()), 0, 16).'.'.$ext;
        }
        $filenamepath = mb_substr($filename,0,2,'utf-8').'/'.mb_substr($filename,2,2,'utf-8').'/';
        $fullpath = 'img/'.$filenamepath;//http://upload.wine.cn/Jiuku/Wine/images/
        if(!is_dir($fullpath)){
            mkdir($fullpath, 0777 ,true);
            @touch($fullpath.'/index.html');
        }
        do{
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10000);
            $img = curl_exec($ch);
        }while(($img === false) || (stripos($img,'错误') !== false));
        $fullname = $fullpath.$filename;
        $fp=@fopen($fullname, "w");
        fwrite($fp,$img);
        fclose($fp);
        $is_exist = file_exists($fullname);
        $is_img = getimagesize($fullname);
        if($is_exist && $is_img){
            $is = true;
        }else{
            $is = false;
        }
        return array($is,$filenamepath.$filename);
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
        $db_selected=mysql_select_db('wine-cn',$connect);
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