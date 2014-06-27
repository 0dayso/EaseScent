<?php
//删除酒庄部分
set_time_limit(0);
header("Content-Type:text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

include("common.php");

echo date("Y-m-d H:i:s")."\r\n";
echo 'Start...'."\r\n";
$Index = new Index();
echo 'Over'."\r\n";
echo date("Y-m-d H:i:s")."\r\n";

class Index{
    public function index(){
        $_DB = new DB();
        $_String = new String();
        $max = $_DB->linkmysql('SELECT max(id) FROM  `jiuku_winery` WHERE `is_del` = \'-1\'');
        $max = $max[0]['max(id)'];
        $mit = 10;
        for($i=0;$i<$max;$i=$i+$mit){
            $da = $i+$mit;
            $res = $_DB->linkmysql('SELECT * FROM `jiuku_winery` WHERE `is_del` = \'-1\'  AND '.$da.' >= `id` AND `id` > '.$i);
            foreach($res as $key=>$val){
                echo 'id:'.$val['id'].' /max:'.$max;
                $is_jk = $_DB->linkmysql('SELECT * FROM `jiuku_join_wine_winery` WHERE `winery_id` = '.$val['id'].' AND `is_del` = \'-1\'');
                $is_bf = $_DB->linkmysql('SELECT * FROM `bfwe_jk_join_wine_winery` WHERE `winery_id` = '.$val['id'].' AND `is_del` = \'-1\'');
                if(count($is_jk) == 0 && count($is_bf) > 0){
                    echo '  del';
                    $this->deldata($val['id']);
                }
                echo "\r\n";
            }
        }
    }
    public function deldata($id){
        $_DB = new DB();
        $_String = new String();
        //winery
        $winery_res = $_DB->linkmysql('SELECT * FROM `jiuku_winery` WHERE `id` = '.$id);
        foreach($winery_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_winery` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_winery` WHERE `id` = '.$val['id']);
        }
        //winery_img
        $winery_img_res = $_DB->linkmysql('SELECT * FROM `jiuku_winery_img` WHERE `winery_id` = '.$id);
        foreach($winery_img_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_winery_img` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_winery_img` WHERE `id` = '.$val['id']);
        }
        //join_winery_grape
        $join_winery_grape_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_winery_grape` WHERE `winery_id` = '.$id);
        foreach($join_winery_grape_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_join_winery_grape` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_join_winery_grape` WHERE `id` = '.$val['id']);
        }
        //join_winery_region
        $join_winery_region_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_winery_region` WHERE `winery_id` = '.$id);
        foreach($join_winery_region_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_join_winery_region` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_join_winery_region` WHERE `id` = '.$val['id']);
        }
        //join_winery_honor
        $join_winery_honor_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_winery_honor` WHERE `winery_id` = '.$id);
        foreach($join_winery_honor_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_join_winery_honor` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_join_winery_honor` WHERE `id` = '.$val['id']);
        }
    }
}
?>