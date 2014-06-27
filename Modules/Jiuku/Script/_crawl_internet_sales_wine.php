<?php
header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
define("LIMIT",2);
define("DB_TABLE",'_crawl_internet_sales_wine');
$_DB = new DB();
$_DB->linkmysql('TRUNCATE TABLE `'.constant('DB_TABLE').'`');
//echo &date("Y-m-d H:i:s")."\r\n";
echo 'Start...'."\r\n";
$Index = new Index();
echo 'Over'."\r\n";
//echo &date("Y-m-d H:i:s")."\r\n";

class Index{
    public function index(){
        //品尚红酒
        echo 'wine9: RUN'."\r\n";
        echo date("Y-m-d H:i:s")."\r\n";
        $wine9 = new wine9();
        $wine9->saveLdata();
        echo 'wine9: OVER'."\r\n";
        echo '------------------------------------------------'."\r\n";
        //抓网酒网
        echo 'wangjiu: RUN'."\r\n";
        echo date("Y-m-d H:i:s")."\r\n";
        $wangjiu = new wangjiu();
        $wangjiu->saveLdata();
        echo 'wangjiu: OVER'."\r\n";
        echo '------------------------------------------------'."\r\n";
        //抓酒美网
        echo 'winenice RUN'."\r\n";
        echo date("Y-m-d H:i:s")."\r\n";
        $winenice = new winenice();
        $winenice->saveLdata();
        echo 'winenice OVER'."\r\n";
        echo '------------------------------------------------'."\r\n";
        //抓取也买酒
        echo 'yesmywine: RUN'."\r\n";
        echo date("Y-m-d H:i:s")."\r\n";
        $yesmywine = new yesmywine();
        $yesmywine->saveLdata();
        echo 'yeamywine: OVER'."\r\n";
        echo '------------------------------------------------'."\r\n";
    }
}
class winenice{
    function saveLdata(){
        echo 'winenice list start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $time = time();
        $Qurl = 'http://www.winenice.com/p-list/t1-o4-p';
        $Hurl = '.shtml';
        $page = 1;
        do{
            $is = false;
            $url = $Qurl.$page.$Hurl;
            echo 'winenice list, url: '.$url;

            $i = 1;
            do{
                $html = $_Curl->curl_i($url);
                $i++;
                if(!$html) echo ' retry';
            }while(!$html && $i<10);

            $html = mb_convert_encoding($html,"utf-8","gb2312");
            if(preg_match_all('#<div class="gn_m_list_a">(.*<div class="gn_m_list_b">.*<div class="gn_m_list_abt">.*<div class="gn_m_list_price">.*<div class="gn_m_list_btn">.*<div class="gn_m_list_sold clearfix">.*<div class="gn_m_list_review ttc">.*)<\/div>#siU',$html,$matches)){
                foreach($matches[0] as $key=>$boxhtml){
                    $data = array('url'=>'','cname'=>'','fname'=>'','price'=>'');
                    if(preg_match('#href="(.*)"#siU',$boxhtml,$url))
                        $data['url'] = 'http://www.winenice.com'.$url[1];
                    if(preg_match('#<div class="gn_m_list_abt">.*<p>.*title="(.*)".*<p>.*<\/div>#siU',$boxhtml,$cname))
                        $data['cname'] = $cname[1];
                    if(preg_match('#div class="gn_m_list_abt">.*<p>.*<br />(.*)<\/a>.*<p>.*<\/div>#siU',$boxhtml,$fname))
                        $data['fname'] = $fname[1];
                    if(preg_match('#<span class="font18">(.*)</span>#siU',$boxhtml,$price))
                        $data['price'] = $price[1];
                    $data = $_String->zl_data($data);
                    $_DB->linkmysql('INSERT INTO `'.constant('DB_TABLE').'` (`internet_sales_name`,`url`,`cname`,`fname`,`price`,`time`) VALUES (\'酒美网\',\''.$data['url'].'\',\''.$data['cname'].'\',\''.$data['fname'].'\',\''.$data['price'].'\','.$time.')');
                }
                echo '  OK'."\r\n";
                $is = true;
            }else{
                echo '  NULL'."\r\n";
            }
            $page++;
        }while($is);
        echo 'winenice list over'."\r\n";
        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'酒美网\'');
        if($count[0]['count(*)'] == 0){
            echo 'winenice list error'."\r\n".'---------STOP---------'."\r\n";
            exit;
        }
        $this->saveDdata();
    }
    function saveDdata(){
        echo 'winenice detail start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $limit = constant('LIMIT');

        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'酒美网\' AND `d_mark` = 0');
        $count = $count[0]['count(*)'];

        while($count > 0){
            //echo 'winenice detail, count: '.$count."\r\n";
            $res = $_DB->linkmysql('SELECT * FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'酒美网\' AND `d_mark` = 0 LIMIT 0,'.$limit);
            $idarr = $urlarr = $htmlarr = $dataarr = array();
            foreach($res as $key=>$val){
                $idarr[$key] = $val['id'];
                $urlarr[$key] = $val['url'];
            }
            echo 'winenice detail, idstr: '.implode(' ',$idarr);
            $htmlarr = $_Curl->curl_multi_i($urlarr);
            foreach($htmlarr as $key=>$val){
                $htmlarr[$key] = mb_convert_encoding($val,"utf-8","gb2312");
            }
            foreach($urlarr as $key=>$val){
                if($htmlarr[$key]){
                    $data = array('year'=>'','img'=>'');
                    if(preg_match('#<a href="javascript:;"><strong>(.*)</strong><span class="actt"></span></a>#siU', $htmlarr[$key], $year)){
                        $data['year'] = $year[1];
                    }
                    if(preg_match_all('#arr5.push\("(.*)"\)#siU', $htmlarr[$key], $imgs)){
                        $img_arr = array();
                        foreach($imgs[1] as $img){
                            $img_arr[] = trim($img);
                        }
                        $data['img'] = implode(',',$img_arr);
                    }
                    $data = $_String->zl_data($data);
                    $_DB->linkmysql('UPDATE `'.constant('DB_TABLE').'` SET `year` = \''.$data['year'].'\',`img` = \''.$data['img'].'\',`html` = \''.str_replace(array('\'','\\'),array('\'\'','\\\\'),$htmlarr[$key]).'\',`d_mark` = 1 WHERE `id` = '.$idarr[$key]);
                    echo ' _OK';
                }else{
                    echo ' _NULL';
                }
            }
            echo "\r\n";
            $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'酒美网\' AND `d_mark` = 0');
            $count = $count[0]['count(*)'];
        }
        echo 'winenice detail over'."\r\n";
    }
}
//也买酒
class yesmywine{
    function saveLdata(){
        echo 'yesmywine list start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $time = time();
        $Qurl = 'http://list.yesmywine.com/z2';
        $Hurl = '/#a_goods_top';
        $page = 1;
        do{
            $is = false;
            $url = ($page == 1) ? $Qurl.$Hurl : $Qurl.'-p'.$page.$Hurl;
            echo 'yesmywine list, url: '.$url;

            $i = 1;
            do{
                $html = $_Curl->curl_i($url);
                $i++;
                if(!$html) echo ' retry';
            }while(!$html && $i<10);

            //$html = mb_convert_encoding($html,"utf-8","gb2312");
            if(preg_match_all('#<li class=".*" data-goodsId=".*">(.*<dl.*<dt.*dd.*)<\/li>#siU',$html,$matches)){
                foreach($matches[0] as $key=>$boxhtml){
                    $data = array('url'=>'','cname'=>'','fname'=>'','price'=>'','year'=>'');
                    if(preg_match('#href="(.*)"#siU',$boxhtml,$url))
                        $data['url'] = $url[1];
                    if(preg_match('#class="pname" title="(.*)"#siU',$boxhtml,$cname))
                        $data['cname'] = $cname[1];
                    if(preg_match('#class="en" title="(.*)"#siU',$boxhtml,$fname))
                        $data['fname'] = $fname[1];
                    if(preg_match('#<span class="minprice">.*<strong>(.*)<\/strong>#siU',$boxhtml,$price))
                        $data['price'] = $price[1];
                    if(preg_match('#<span class="year">(.*)</span>#siU',$boxhtml,$year))
                        $data['year'] = $year[1];
                    $data = $_String->zl_data($data);
                    $_DB->linkmysql('INSERT INTO `'.constant('DB_TABLE').'` (`internet_sales_name`,`url`,`cname`,`fname`,`price`,`year`,`time`) VALUES (\'也买酒\',\''.$data['url'].'\',\''.$data['cname'].'\',\''.$data['fname'].'\',\''.$data['price'].'\',\''.$data['year'].'\','.$time.')');
                }
                echo '  OK'."\r\n";
                $is = true;
            }else{
                echo '  NULL'."\r\n";
            }
            $page++;
        }while($is);
        echo 'yesmywine list over'."\r\n";
        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'也买酒\'');
        if($count[0]['count(*)'] == 0){
            echo 'yesmywine list error'."\r\n".'---------STOP---------'."\r\n";
            exit;
        }
        $this->saveDdata();
    }
    function saveDdata(){
        echo 'yesmywine detail start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $limit = constant('LIMIT');

        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'也买酒\' AND `d_mark` = 0');
        $count = $count[0]['count(*)'];

        while($count > 0){
            //echo 'yesmywine detail, count: '.$count."\r\n";
            $res = $_DB->linkmysql('SELECT * FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'也买酒\' AND `d_mark` = 0 LIMIT 0,'.$limit);
            $idarr = $urlarr = $htmlarr = $dataarr = array();
            foreach($res as $key=>$val){
                $idarr[$key] = $val['id'];
                $urlarr[$key] = $val['url'];
            }
            echo 'yesmywine detail, idstr: '.implode(' ',$idarr);
            $htmlarr = $_Curl->curl_multi_i($urlarr);
            /*foreach($htmlarr as $key=>$val){
                $htmlarr[$key] = mb_convert_encoding($val,"utf-8","gb2312");
            }*/
            foreach($urlarr as $key=>$val){
                if($htmlarr[$key]){
                    $data = array('img'=>'');
                    if(preg_match_all('#longdesc="(.*)"#siU', $htmlarr[$key], $imgs)){
                        $img_arr = array();
                        foreach($imgs[1] as $img){
                            $img_arr[] = trim($img);
                        }
                        $data['img'] = implode(',',$img_arr);
                    }
                    $data = $_String->zl_data($data);
                    $_DB->linkmysql('UPDATE `'.constant('DB_TABLE').'` SET `img` = \''.$data['img'].'\',`html` = \''.str_replace(array('\'','\\'),array('\'\'','\\\\'),$htmlarr[$key]).'\',`d_mark` = 1 WHERE `id` = '.$idarr[$key]);
                    echo ' _OK';
                }else{
                    echo ' _NULL';
                }
            }
            echo "\r\n";
            $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'也买酒\' AND `d_mark` = 0');
            $count = $count[0]['count(*)'];
        }
        echo 'yesmywine detail over'."\r\n";
    }
}

//网酒网
class wangjiu{
    function saveLdata(){
        echo 'wangjiu list start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $time = time();
        $Qurl = 'http://www.wangjiu.com/product/shoppingmall-product_type-1-start-';
        $Hurl = '-pagecount-12.html';
        $page = 1;
        do{
            $is = false;
            $url = $Qurl.$page.$Hurl;
            echo 'wangjiu list, url: '.$url;

            $i = 1;
            do{
                $html = $_Curl->curl_i($url);
                $i++;
                if(!$html) echo ' retry';
            }while(!$html && $i<10);

            //$html = mb_convert_encoding($html,"utf-8","gb2312");
            if(preg_match('#<div class="main">(.*)<div class="recom">#siU', $html, $bigbox_html)){
                if(preg_match_all('#<dl.*>.*(<dt>.*<dd>.*class="add addToCart".*)<\/dl>#siU',$bigbox_html[1],$matches)){
                    foreach($matches[0] as $key=>$boxhtml){
                        $data = array('url'=>'','cname'=>'','fname'=>'','price'=>'');
                        if(preg_match('#href="(.*)"#siU',$boxhtml,$url))
                            $data['url'] = 'http://www.wangjiu.com'.$url[1];
                        if(preg_match('#<dd>.*<em>.*target="_blank">(.*)<\/a><\/em>.*<em>.*<em>#siU',$boxhtml,$cname))
                            $data['cname'] = $cname[1];
                        if(preg_match('#<dd>.*<em>.*<em>.*target="_blank">(.*)<\/a><\/em>.*<em>#siU',$boxhtml,$fname))
                            $data['fname'] = $fname[1];
                        if(preg_match('#<dd>.*<em>.*<em>.*<em>.*<b>(.*)<\/b>#siU',$boxhtml,$price))
                            $data['price'] = str_replace(array('网酒价 ￥',','),'',$price[1]);
                        $data = $_String->zl_data($data);
                        $_DB->linkmysql('INSERT INTO `'.constant('DB_TABLE').'` (`internet_sales_name`,`url`,`cname`,`fname`,`price`,`time`) VALUES (\'网酒网\',\''.$data['url'].'\',\''.$data['cname'].'\',\''.$data['fname'].'\',\''.$data['price'].'\','.$time.')');
                    }
                    echo '  OK'."\r\n";
                    $is = true;
                }else{
                    echo '  NULL'."\r\n";
                }
            }else{
                echo '  NULL'."\r\n";
            }
            $page++;
        }while($is);
        echo 'wangjiu list over'."\r\n";
        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'网酒网\'');
        if($count[0]['count(*)'] == 0){
            echo 'wangjiu list error'."\r\n".'---------STOP---------'."\r\n";
            exit;
        }
        $this->saveDdata();
    }
    function saveDdata(){
        echo 'wangjiu detail start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $limit = constant('LIMIT');

        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'网酒网\' AND `d_mark` = 0');
        $count = $count[0]['count(*)'];

        while($count > 0){
            //echo 'wangjiu detail, count: '.$count."\r\n";
            $res = $_DB->linkmysql('SELECT * FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'网酒网\' AND `d_mark` = 0 LIMIT 0,'.$limit);
            $idarr = $urlarr = $htmlarr = $dataarr = array();
            foreach($res as $key=>$val){
                $idarr[$key] = $val['id'];
                $urlarr[$key] = $val['url'];
            }
            echo 'wangjiu detail, idstr: '.implode(' ',$idarr);
            $htmlarr = $_Curl->curl_multi_i($urlarr);
            /*foreach($htmlarr as $key=>$val){
                $htmlarr[$key] = mb_convert_encoding($val,"utf-8","gb2312");
            }*/
            foreach($urlarr as $key=>$val){
                if($htmlarr[$key]){
                    $data = array('year'=>'','img'=>'');
                    if(preg_match('#<p class="d_itm07"><span>.*</span>(.*)</p>#siU', $htmlarr[$key], $year))
                        $data['year'] = $year[1];
                    if(preg_match_all('#largeimage:\'(.*)\'}#siU', $htmlarr[$key], $imgs)){
                        $img_arr = array();
                        foreach($imgs[1] as $img){
                            $img_arr[] = trim($img);
                        }
                        $data['img'] = implode(',',$img_arr);
                    }
                    $data = $_String->zl_data($data);
                    $_DB->linkmysql('UPDATE `'.constant('DB_TABLE').'` SET `year` = \''.$data['year'].'\',`img` = \''.$data['img'].'\',`html` = \''.str_replace(array('\'','\\'),array('\'\'','\\\\'),$htmlarr[$key]).'\',`d_mark` = 1 WHERE `id` = '.$idarr[$key]);
                    echo ' _OK';
                }else{
                    echo ' _NULL';
                }
            }
            echo "\r\n";
            $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'网酒网\' AND `d_mark` = 0');
            $count = $count[0]['count(*)'];
        }
        echo 'wangjiu detail over'."\r\n";
    }
}
class wine9{
    function saveLdata(){
        echo 'wine9 list start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $time = time();
        $Qurl = 'http://www.wine9.com/72-';
        $Hurl = '-1-4-0-0-1---------.html';
        $page = 1;
        do{
            $is = false;
            $url = $Qurl.$page.$Hurl;
            echo 'wine9 list, url: '.$url;

            $i = 1;
            do{
                $html = $_Curl->curl_i($url);
                $i++;
                if(!$html) echo ' retry';
            }while(!$html && $i<10);

            $html = mb_convert_encoding($html,"utf-8","gb2312");
            if(preg_match('#class="proinfo_list"(.*)class="fenye"#siU', $html, $bigbox_html)){
                if(preg_match_all('#<li>.*<div class="pic">.*<div class="text".*<\/li>#siU',$bigbox_html[0],$matches)){
                    foreach($matches[0] as $key=>$boxhtml){
                        $data = array('url'=>'','cname'=>'','price'=>'');
                        if(preg_match('#href="(.*)"#siU',$boxhtml,$url))
                            $data['url'] = 'http://www.wine9.com'.$url[1];
                        if(preg_match('#<h2 class="title"><a href=".*">(.*)<\/a><b>.*<\/b></h2>#siU',$boxhtml,$cname))
                            $data['cname'] = $cname[1];
                        if(preg_match('#<ins>(.*)<\/ins>#siU',$boxhtml,$price))
                            $data['price'] = $price[1];
                        $data = $_String->zl_data($data);
                        $_DB->linkmysql('INSERT INTO `'.constant('DB_TABLE').'` (`internet_sales_name`,`url`,`cname`,`price`,`time`) VALUES (\'品尚红酒\',\''.$data['url'].'\',\''.$data['cname'].'\',\''.$data['price'].'\','.$time.')');
                    }
                    echo '  OK'."\r\n";
                    $is = true;
                }else{
                    echo '  NULL'."\r\n";
                }
            }else{
                echo '  NULL'."\r\n";
            }
            $page++;
        }while($is);
        echo 'wine9 list over'."\r\n";
        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'品尚红酒\'');
        if($count[0]['count(*)'] == 0){
            echo 'wine9 list error'."\r\n".'---------STOP---------'."\r\n";
            exit;
        }
        $this->saveDdata();
    }
    function saveDdata(){
        echo 'wine9 detail start'."\r\n";
        $_DB = new DB();
        $_Curl = new Curl();
        $_String = new String();
        $limit = constant('LIMIT');

        $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'品尚红酒\' AND `d_mark` = 0');
        $count = $count[0]['count(*)'];

        while($count > 0){
            //echo 'wine9 detail, count: '.$count."\r\n";
            $res = $_DB->linkmysql('SELECT * FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'品尚红酒\' AND `d_mark` = 0 LIMIT 0,'.$limit);
            $idarr = $urlarr = $htmlarr = $dataarr = array();
            foreach($res as $key=>$val){
                $idarr[$key] = $val['id'];
                $urlarr[$key] = $val['url'];
                $cnamearr[$key] = $val['cname'];
            }
            echo 'wine9 detail, idstr: '.implode(' ',$idarr);
            $htmlarr = $_Curl->curl_multi_i($urlarr);
            foreach($htmlarr as $key=>$val){
                $htmlarr[$key] = mb_convert_encoding($val,"utf-8","gb2312");
            }
            foreach($urlarr as $key=>$val){
                if($htmlarr[$key]){
                    $data = array('fname'=>'','year'=>'','img'=>'');
                    if(preg_match('#<h1 class="title_left">(.*)</h1>#siU', $htmlarr[$key], $fname)){
                        $data['fname'] = str_replace($cnamearr[$key],'',$fname[1]);
                    }
                    if(preg_match('#<li><b>年份&nbsp;<\/b>(.*)<\/li>#siU', $htmlarr[$key], $year)){
                        $data['year'] = $year[1];
                    }
                    if(preg_match_all('#largeimage:\s*\'(.*)\'}">#siU', $htmlarr[$key], $imgs)){
                        $img_arr = array();
                        foreach($imgs[1] as $img){
                            $img_arr[] = trim($img);
                        }
                        $data['img'] = implode(',',$img_arr);
                    }
                    $data = $_String->zl_data($data);
                    $_DB->linkmysql('UPDATE `'.constant('DB_TABLE').'` SET `fname` = \''.$data['fname'].'\',`year` = \''.$data['year'].'\',`img` = \''.$data['img'].'\',`html` = \''.str_replace(array('\'','\\'),array('\'\'','\\\\'),$htmlarr[$key]).'\',`d_mark` = 1 WHERE `id` = '.$idarr[$key]);
                    echo ' _OK';
                }else{
                    echo ' _NULL';
                }
            }
            echo "\r\n";
            $count = $_DB->linkmysql('SELECT count(*) FROM `'.constant('DB_TABLE').'` WHERE `internet_sales_name` = \'品尚红酒\' AND `d_mark` = 0');
            $count = $count[0]['count(*)'];
        }
        echo 'wine9 detail over'."\r\n";
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
    function zl_data($data){
        $f = array('\'');
        $r = array('\'\'');
        foreach($data as $key=>$val){
            $data[$key] = trim(str_replace($f,$r,$val));
        }
        //中文名
        if(isset($data['cname'])){
            $data['cname'] = trim(preg_replace('/\s+/',' ',$data['cname']));
        }
        //外文名
        if(isset($data['fname'])){
            $data['fname'] = trim(preg_replace('/\s+/',' ',$data['fname']));
        }
        //价格
        if(isset($data['price'])){
            $data['price'] = floor($data['price']);
        }
        //年份
        if(isset($data['year'])){
            $year_arr = array();
            if(!trim($data['year']) || stristr($data['year'],'无') !== false){
                $data['year'] = 'NOYEAR';
            }elseif(stristr($data['year'],'N') !== false){
                $data['year'] = 'NV';
            }else{
                $data['year'] = str_replace(array(' ','，','/','\\','(',')','、','|'),',',$data['year']);
                foreach(explode(',',$data['year']) as $k=>$v){
                    if(preg_match("/^\d{4}$/",trim($v)))
                        $year_arr[] = trim($v);
                }
                if($year_arr){
                    $data['year'] = implode(',',$year_arr);
                }else{
                    $data['year'] = 'NOYEAR';
                }
            }
        }
        //图片
        if(isset($data['img'])){
            $data['img'] = trim(preg_replace('/\s+/','',$data['img']));
        }
        //链接地址
        if(isset($data['url'])){
            $data['url'] = trim(preg_replace('/\s+/','',$data['url']));
        }
        return $data;
    }
}
class Curl{
    //Curl Post
    function curl_i($url, $data=array(), $timeout = 10, $header = "") {
        $ssl = substr($url, 0, 8) == 'https://' ? true : false;
        $post_string = http_build_query($data);
        $ch = curl_init();
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $SERVER_HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        }else{
            $SERVER_HTTP_USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36';
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