<?php
define("TIME",time());
//server,user,pwd
define("DB_SERVER",'localhost');
define("DB_USER",'root');
define("DB_PASSWORD",'vertrigo');
define("DB_DATABASE",'wine-cn');



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
class Curl{
    //Curl Post
    function curl_i($url, $data=array(), $timeout = 10, $header = array()) {
        $ip = rand(1,255).'.'.rand(1,255).'.'.rand(1,255).'.'.rand(1,255);
        $header = $header ? $header : array('CLIENT-IP:'.$ip, 'X-FORWARDED-FOR:'.$ip, );
        $ssl = substr($url, 0, 8) == 'https://' ? true : false;
        $post_string = http_build_query($data);
        $ch = curl_init();
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $SERVER_HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        }else{
            $SERVER_HTTP_USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) '.rand(1,9999).'/28.0.1500.95 Safari/537.36';
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $SERVER_HTTP_USER_AGENT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        //print_r(curl_getinfo($ch));echo "\r\n\r\n";
        curl_close($ch);
        return $result;
    }
    function curl_multi_i($connomains,$timeout=100){
        $mh = curl_multi_init();

        foreach($connomains as $i=>$url){
            $conn[$i]=curl_init($url);
            curl_setopt($conn[$i],CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
            curl_setopt($conn[$i],CURLOPT_RETURNTRANSFER,1);
            curl_setopt($conn[$i],CURLOPT_TIMEOUT, $timeout);
            curl_multi_add_handle($mh,$conn[$i]);
        }

        $active = null;
        do{
            $mrc = curl_multi_exec($mh,$active);
            $info = curl_multi_info_read($mh,$msgs_in_queue);
            usleep(250000);
        }while($mrc === CURLM_CALL_MULTI_PERFORM || $active);

        foreach($connomains as $i=>$url) {
            $res[$i] = curl_multi_getcontent($conn[$i]);
            //print_r(curl_getinfo($conn[$i]));echo "\r\n\r\n";
            curl_close($conn[$i]);
        }
        return $res;
    }
}
class DB{
    function linkmysql($sql){
        $connect=mysql_connect(constant('DB_SERVER'),constant('DB_USER'),constant('DB_PASSWORD'));
        if(!$connect)
        {
            die(''.mysql_error());
            return false;
        }
        $db_selected=mysql_select_db(constant('DB_DATABASE'),$connect);
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