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
        $res = $_DB->linkmysql('SELECT * FROM `jiuku_agents_internet_sales_wine` WHERE `internet_sales_id` IN (1,2,3,4) AND `status` = \'1\' AND `is_del` = \'-1\' ORDER BY `last_script_request_time` ASC LIMIT 100');
        foreach($res as $key=>$val){
            echo ($key+1).' / 100'.'    $id='.$val['id']."\r\n";
            $_DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `last_script_request_time` = \''.constant('TIME').'\' WHERE `id` = '.$val['id']);
            if(strpos($val['url'],'www.yesmywine.com') !== false){
                $this->yesmywine($val);
            }elseif(strpos($val['url'],'www.winenice.com') !== false){
                $this->winenice($val);
            }elseif(strpos($val['url'],'www.wangjiu.com') !== false){
                $this->wangjiu($val);
            }elseif(strpos($val['url'],'www.wine9.com') !== false){
                $this->wine9($val);
            }else{
                echo ' url x';
            }
            echo "\r\n\r\n";
        }
    }
    public function index1(){
        $_DB = new DB();
        $_String = new String();
        $time = constant('TIME') - 7200;
        $res = $_DB->linkmysql('SELECT * FROM  `jiuku_agents_internet_sales_wine` WHERE `url` like \'%www.yesmywine.com%\' AND `script_edit_time` < '.$time.' AND `status` = \'1\' AND `is_del` = \'-1\' ORDER BY `script_edit_time` ASC LIMIT 100');
        foreach($res as $key=>$val){
            echo 'yesmywine.com  '.($key+1).' / 100'."\r\n";
            $this->yesmywine($val);
            echo "\r\n\r\n";
        }
        $res = $_DB->linkmysql('SELECT * FROM  `jiuku_agents_internet_sales_wine` WHERE `url` like \'%www.winenice.com%\' AND `script_edit_time` < '.$time.' AND `status` = \'1\' AND `is_del` = \'-1\' ORDER BY `script_edit_time` ASC LIMIT 100');
        foreach($res as $key=>$val){
            echo 'winenice.com  '.($key+1).' / 100'."\r\n";
            $this->winenice($val);
            echo "\r\n\r\n";
        }
        $res = $_DB->linkmysql('SELECT * FROM  `jiuku_agents_internet_sales_wine` WHERE `url` like \'%www.wangjiu.com%\' AND `script_edit_time` < '.$time.' AND `status` = \'1\' AND `is_del` = \'-1\' ORDER BY `script_edit_time` ASC LIMIT 100');
        foreach($res as $key=>$val){
            echo 'wangjiu.com  '.($key+1).' / 100'."\r\n";
            $this->wangjiu($val);
            echo "\r\n\r\n";
        }
        $res = $_DB->linkmysql('SELECT * FROM  `jiuku_agents_internet_sales_wine` WHERE `url` like \'%www.wine9.com%\' AND `script_edit_time` < '.$time.' AND `status` = \'1\' AND `is_del` = \'-1\' ORDER BY `script_edit_time` ASC LIMIT 100');
        foreach($res as $key=>$val){
            echo 'wine9.com  '.($key+1).' / 100'."\r\n";
            $this->wine9($val);
            echo "\r\n\r\n";
        }
    }
    /*public function index(){
        $_DB = new DB();
        $_String = new String();
        $time = constant('TIME') - 7200;
        $count = $_DB->linkmysql('SELECT `id` FROM  `jiuku_agents_internet_sales_wine` WHERE `script_edit_time` < '.$time.' ORDER BY `internet_sales_id` ASC');
        $count = count($count);
        $mit = 1000;
        for($i=0;$i<$count;$i=$i+$mit){
            $res = $_DB->linkmysql('SELECT * FROM  `jiuku_agents_internet_sales_wine` WHERE `script_edit_time` < '.$time.' ORDER BY `internet_sales_id` ASC LIMIT '.$i.','.$mit);
            foreach($res as $key=>$val){
                echo ($key+1).' / '.($i).'-'.($i+$mit).' / '.$count."\r\n";
                if(strpos($val['url'],'www.yesmywine.com') !== false){
                    $this->yesmywine($val);
                }elseif(strpos($val['url'],'www.winenice.com') !== false){
                    $this->winenice($val);
                }elseif(strpos($val['url'],'www.wangjiu.com') !== false){
                    $this->wangjiu($val);
                }elseif(strpos($val['url'],'www.wine9.com') !== false){
                    $this->wine9($val);
                    sleep(3);
                }else{
                    echo ' url x';
                }
                echo "\r\n\r\n";
            }
        }

    }*/
    public function yesmywine($data){
        $_DB = new DB();
        $_Curl = new Curl();
        echo $data['url']."\r\n";
        $i = 1;
        do{
            $html = $_Curl->curl_i($data['url']);
            $i++;
        }while(!$html && $i<5);
        if(!$html){
            echo '  curl x';
        }else{
            echo '  curl o';
            if(preg_match('#您的专享价:<b class="myPrice">&yen<em>(.*)<\/em><\/b>#siU',$html,$matches) || preg_match('#也买价:<b class="ymPrice">&yen<em>(.*)<\/em><\/b>#siU',$html,$matches1)){
                $price = $matches ? $matches[1] : $matches1[1];
                if(preg_match("/^(-|\+)?\d+$/",$price) || preg_match("/^(-|\+)?\d+\.\d*$/",$price)){
                    $price = floor($price);
                    echo ' preg o '.$data['price'].' -> '.$price;

                    $price_trend = $this->price_trend($price,$data['price'],$data['price_trend']);
                    $price_log = json_decode($data['price_log'],true) ? json_decode($data['price_log'],true) : array();
                    $price_log[] = array('p'=>trim($price),'t'=>trim(constant('TIME')));
                    $price_log = json_encode($price_log);
                    $_DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `price` = \''.$price.'\',`price_trend` = \''.$price_trend.'\',`price_log` = \''.$price_log.'\',`last_script_response_time` = \''.constant('TIME').'\' WHERE `id` = '.$data['id']);

                    echo ' update o';
                }else{
                    echo ' update x';
                }
            }else{
                echo ' preg x';
            }
        }
    }
    public function winenice($data){
        $_DB = new DB();
        $_Curl = new Curl();
        echo $data['url']."\r\n";
        $i = 1;
        do{
            $html = $_Curl->curl_i($data['url']);
            $i++;
        }while(!$html && $i<5);
        if(!$html){
            echo '  curl x';
        }else{
            echo '  curl o';
            $html = mb_convert_encoding($html,"utf-8","gb2312");
            if(preg_match('#￥<strong>(.*)</strong>#siU',$html,$matches)){
                $price = $matches[1];
                if(preg_match("/^(-|\+)?\d+$/",$price) || preg_match("/^(-|\+)?\d+\.\d*$/",$price)){
                    $price = floor($price);
                    echo ' preg o '.$data['price'].' -> '.$price;

                    $price_trend = $this->price_trend($price,$data['price'],$data['price_trend']);
                    $price_log = json_decode($data['price_log'],true) ? json_decode($data['price_log'],true) : array();
                    $price_log[] = array('p'=>trim($price),'t'=>trim(constant('TIME')));
                    $price_log = json_encode($price_log);
                    $_DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `price` = \''.$price.'\',`price_trend` = \''.$price_trend.'\',`price_log` = \''.$price_log.'\',`last_script_response_time` = \''.constant('TIME').'\' WHERE `id` = '.$data['id']);

                    echo ' update o';
                }else{
                    echo ' update x';
                }
            }else{
                echo ' preg x';
            }
        }
    }
    public function wangjiu($data){
        $_DB = new DB();
        $_Curl = new Curl();
        echo $data['url']."\r\n";
        $i = 1;
        do{
            $html = $_Curl->curl_i($data['url']);
            $i++;
        }while(!$html && $i<5);
        if(!$html){
            echo '  curl x';
        }else{
            echo '  curl o';
            if(preg_match('#_mvq\.push\(\[\'\$addGoods\', \'.*\', \'.*\', \'.*\', \'.*\', \'(.*)\'#siU',$html,$matches)){
                $price = $matches[1];
                if(preg_match("/^(-|\+)?\d+$/",$price) || preg_match("/^(-|\+)?\d+\.\d*$/",$price)){
                    $price = floor($price);
                    echo ' preg o '.$data['price'].' -> '.$price;

                    $price_trend = $this->price_trend($price,$data['price'],$data['price_trend']);
                    $price_log = json_decode($data['price_log'],true) ? json_decode($data['price_log'],true) : array();
                    $price_log[] = array('p'=>trim($price),'t'=>trim(constant('TIME')));
                    $price_log = json_encode($price_log);
                    $_DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `price` = \''.$price.'\',`price_trend` = \''.$price_trend.'\',`price_log` = \''.$price_log.'\',`last_script_response_time` = \''.constant('TIME').'\' WHERE `id` = '.$data['id']);

                    echo ' update o';
                }else{
                    echo ' update x';
                }
            }else{
                echo ' preg x';
            }
        }
    }
    public function wine9($data){
        $_DB = new DB();
        $_Curl = new Curl();
        echo $data['url']."\r\n";
        $urlid = str_replace(array('http://www.wine9.com/','.html'),'',$data['url']);
        $curl_url = 'http://www.wine9.com/product.php?sku='.$urlid.'&m=loadinfo';
        $i = 1;
        do{
            $html = $_Curl->curl_i($curl_url);
            $i++;
        }while(!$html && $i<1);
        if(!$html){
            echo '  curl x';
        }else{
            echo '  curl o';
            if($html = json_decode($html,true)){
                $price = $html['promprice'];
                if(preg_match("/^(-|\+)?\d+$/",$price) || preg_match("/^(-|\+)?\d+\.\d*$/",$price)){
                    $price = floor($price);
                    echo ' decode o '.$data['price'].' -> '.$price;

                    $price_trend = $this->price_trend($price,$data['price'],$data['price_trend']);
                    $price_log = json_decode($data['price_log'],true) ? json_decode($data['price_log'],true) : array();
                    $price_log[] = array('p'=>trim($price),'t'=>trim(constant('TIME')));
                    $price_log = json_encode($price_log);
                    $_DB->linkmysql('UPDATE `jiuku_agents_internet_sales_wine` SET `price` = \''.$price.'\',`price_trend` = \''.$price_trend.'\',`price_log` = \''.$price_log.'\',`last_script_response_time` = \''.constant('TIME').'\' WHERE `id` = '.$data['id']);

                    echo ' update o';
                }else{
                    echo ' update x';
                }
            }else{
                echo ' decode x';
            }
        }
    }
    public function price_trend($price,$old_price,$old_price_trend){
        if($old_price_trend == 0 || $old_price_trend == 1){
            if($old_price == $price){
                $price_trend = 1;
            }elseif($old_price > $price){
                $price_trend = 3;
            }else{
                $price_trend = 2;
            }
        }elseif($old_price_trend == 2){
            if($old_price <= $price){
                $price_trend = 2;
            }else{
                $price_trend = 4;
            }
        }elseif($old_price_trend == 3){
            if($old_price >= $price){
                $price_trend = 3;
            }else{
                $price_trend = 4;
            }
        }else{
            $price_trend = 4;
        }
        return $price_trend;
    }
}
?>