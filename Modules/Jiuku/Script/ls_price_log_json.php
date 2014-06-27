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
        $_DB = new DB();
        $_String = new String();
        $count = $_DB->linkmysql('SELECT `id` FROM  `jiuku_agents_store_sales_wine`');
        $count = count($count);
        $mit = 1000;
        for($i=0;$i<$count;$i=$i+$mit){
            $res = $_DB->linkmysql('SELECT * FROM  `jiuku_agents_store_sales_wine`  LIMIT '.$i.','.$mit);
            foreach($res as $key=>$val){
                echo ($key+1).' / '.($i).'-'.($i+$mit).' / '.$count.'   ';
                $price_res = $_DB->linkmysql('SELECT * FROM `jiuku_agents_store_sales_wine_price_log` WHERE `store_sales_wine_id`='.$val['id'].' ORDER BY `time` ASC');
                $price_log_arr = array();
                foreach($price_res as $k=>$v){
                    $price_log_arr[] = array('p'=>$v['price'],'t'=>$v['time']);
                }
                $price_log = json_encode($price_log_arr);
                $_DB->linkmysql('UPDATE `jiuku_agents_store_sales_wine` SET `price_log`=\''.$price_log.'\' WHERE `id`='.$val['id']);
                echo "\r\n";
            }
        }

    }
}
?>