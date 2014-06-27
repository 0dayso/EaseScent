<?php
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
        $max = $_DB->linkmysql('SELECT max(id) FROM  `jiuku_wine` WHERE `merge_id`=0 AND `is_del` = \'-1\'');
        $max = $max[0]['max(id)'];
        $mit = 1000;
        for($i=0;$i<$max;$i=$i+$mit){
            $da = $i+$mit;
            $res = $_DB->linkmysql('SELECT * FROM  `jiuku_wine` WHERE `merge_id`=0 AND `is_del` = \'-1\' AND '.$da.' >= `id` AND `id` > '.$i);
            foreach($res as $key=>$val){
                echo $val['id'].' / '.$max.'    ';
                //$val = $String->myReplace($val);
                //判断是否关联代理商酒款
                $is_internet = $_DB->linkmysql('SELECT count(*) FROM `jiuku_agents_internet_sales_wine` WHERE `wine_id` = '.$val['id'].' AND `is_del` = \'-1\'');
                $is_store = $_DB->linkmysql('SELECT count(*) FROM `jiuku_agents_store_sales_wine` WHERE `wine_id` = '.$val['id'].' AND `is_del` = \'-1\'');
                if($is_internet[0]['count(*)'] > 0 || $is_store[0]['count(*)'] > 0){
                    echo 'join_agents   '."\r\n";
                    continue;
                }
                //判断是否为单独we数据
                $is_we = $_DB->linkmysql('SELECT count(*) FROM `jiuku_join_wine_refweb` WHERE `wine_id` = '.$val['id'].' AND `refweb_id` = 1 AND `is_del` = \'-1\'');
                $is_ws = $_DB->linkmysql('SELECT count(*) FROM `jiuku_join_wine_refweb` WHERE `wine_id` = '.$val['id'].' AND `refweb_id` = 2 AND `is_del` = \'-1\'');
                if(!($is_we[0]['count(*)'] > 0 && $is_ws[0]['count(*)'] == 0)){
                    echo 'join_ws   '."\r\n";
                    continue;
                }
                echo 'delete   ';
                $this->deldata($val['id']);
                echo "\r\n";
            }
            //exit;
        }
    }
    public function deldata($id){
        $_DB = new DB();
        $_String = new String();
        //wine
        $wine_res = $_DB->linkmysql('SELECT * FROM `jiuku_wine` WHERE `id` = '.$id);
        foreach($wine_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_wine` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_wine` WHERE `id` = '.$val['id']);
        }
        //wine_caname
        $wine_caname_res = $_DB->linkmysql('SELECT * FROM `jiuku_wine_caname` WHERE `wine_id` = '.$id);
        foreach($wine_caname_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_wine_caname` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_wine_caname` WHERE `id` = '.$val['id']);
        }
        //wine_img
        $wine_img_res = $_DB->linkmysql('SELECT * FROM `jiuku_wine_img` WHERE `wine_id` = '.$id);
        foreach($wine_img_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_wine_img` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_wine_img` WHERE `id` = '.$val['id']);
        }
        //join_wine_grape
        $join_wine_grape_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_wine_grape` WHERE `wine_id` = '.$id);
        foreach($join_wine_grape_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_join_wine_grape` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_join_wine_grape` WHERE `id` = '.$val['id']);
        }
        //join_wine_refweb
        $join_wine_refweb_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_wine_refweb` WHERE `wine_id` = '.$id);
        foreach($join_wine_refweb_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_join_wine_refweb` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_join_wine_refweb` WHERE `id` = '.$val['id']);
        }
        //join_wine_region
        $join_wine_region_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_wine_region` WHERE `wine_id` = '.$id);
        foreach($join_wine_region_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_join_wine_region` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_join_wine_region` WHERE `id` = '.$val['id']);
        }
        //join_wine_winery
        $join_wine_winery_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_wine_winery` WHERE `wine_id` = '.$id);
        foreach($join_wine_winery_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_join_wine_winery` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_join_wine_winery` WHERE `id` = '.$val['id']);
        }
        //ywine
        $ywine_res = $_DB->linkmysql('SELECT * FROM `jiuku_ywine` WHERE `wine_id` = '.$id);
        foreach($ywine_res as $val){
            $val = $_String->myReplace($val);
            $_DB->linkmysql('INSERT INTO `bfwe_jk_ywine` VALUES(\''.implode('\',\'',$val).'\')');
            $_DB->linkmysql('DELETE FROM `jiuku_ywine` WHERE `id` = '.$val['id']);

            $ywine_id = $val['id'];
            //join_ywine_grape
            $join_ywine_grape_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_ywine_grape` WHERE `ywine_id` = '.$ywine_id);
            foreach($join_ywine_grape_res as $v){
                $v = $_String->myReplace($v);
                $_DB->linkmysql('INSERT INTO `bfwe_jk_join_ywine_grape` VALUES(\''.implode('\',\'',$v).'\')');
                $_DB->linkmysql('DELETE FROM `jiuku_join_ywine_grape` WHERE `id` = '.$v['id']);
            }
            //join_ywine_honor
            $join_ywine_honor_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_ywine_honor` WHERE `ywine_id` = '.$ywine_id);
            foreach($join_ywine_honor_res as $v){
                $v = $_String->myReplace($v);
                $_DB->linkmysql('INSERT INTO `bfwe_jk_join_ywine_honor` VALUES(\''.implode('\',\'',$v).'\')');
                $_DB->linkmysql('DELETE FROM `jiuku_join_ywine_honor` WHERE `id` = '.$v['id']);
            }
            //join_ywine_refweb
            $join_ywine_refweb_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_ywine_refweb` WHERE `ywine_id` = '.$ywine_id);
            foreach($join_ywine_refweb_res as $v){
                $v = $_String->myReplace($v);
                $_DB->linkmysql('INSERT INTO `bfwe_jk_join_ywine_refweb` VALUES(\''.implode('\',\'',$v).'\')');
                $_DB->linkmysql('DELETE FROM `jiuku_join_ywine_refweb` WHERE `id` = '.$v['id']);
            }
            //ywine_eval
            $ywine_eval_res = $_DB->linkmysql('SELECT * FROM `jiuku_ywine_eval` WHERE `ywine_id` = '.$ywine_id);
            foreach($ywine_eval_res as $v){
                $v = $_String->myReplace($v);
                $_DB->linkmysql('INSERT INTO `bfwe_jk_ywine_eval` VALUES(\''.implode('\',\'',$v).'\')');
                $_DB->linkmysql('DELETE FROM `jiuku_ywine_eval` WHERE `id` = '.$v['id']);

                $ywine_eval_id = $v['id'];
                //join_ywine_eval_refweb
                $join_ywine_eval_refweb_res = $_DB->linkmysql('SELECT * FROM `jiuku_join_ywine_eval_refweb` WHERE `ywine_eval_id` = '.$ywine_eval_id);
                foreach($join_ywine_eval_refweb_res as $vv){
                    $vv = $_String->myReplace($vv);
                    $_DB->linkmysql('INSERT INTO `bfwe_jk_join_ywine_eval_refweb` VALUES(\''.implode('\',\'',$vv).'\')');
                    $_DB->linkmysql('DELETE FROM `jiuku_join_ywine_eval_refweb` WHERE `id` = '.$vv['id']);
                }
            }
        }
    }
}
?>