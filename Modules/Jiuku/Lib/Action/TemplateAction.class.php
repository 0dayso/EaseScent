<?php
/**
* @file TemplateAction.class.php     #文件名
*
* @brief  模板测试        #程序文件描述
*
* Copyright(C) 2010-2015 Easescent.com, Inc. or its affiliates. All Rights Reserved.     #版权信息
*
* @version $Id$         #版本号，由svn功能自动生成，不用修改
* @author Goni, goni@sina.com         #程序作者
* @date 2013-01-30                 #日期
*/

/**
* @class TemplateAction       # 类名
* @brief 模板测试
*
*/
class TemplateAction extends Action{
    public function _initialize() {
    }
    public function index(){
    }
    public function mingzhuang_index(){
        $honor_res = D('Honor')->field('id,pid,fname,cname')->where(array('status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        $fk_honor_res = array();
        foreach($honor_res as $key=>$val){
            $fk_honor_res[$val['pid']][] = $val;
            $honor_idarr[] = $val['id'];
        }
        $left_list = $fk_honor_res[0];
        foreach($left_list as $key=>$val){
            $left_list[$key]['son'] = $fk_honor_res[$val['id']];
            foreach($left_list[$key]['son'] as $key1=>$val1){
                if($fk_honor_res[$val1['id']]){
                    $left_list[$key]['son'][$key1]['son'] = $fk_honor_res[$val1['id']];
                }else{
                    $left_list[$key]['son'][$key1]['son'] = array($val1);
                }
                foreach($left_list[$key]['son'][$key1]['son'] as $key2=>$val2){
                    $left_list[$key]['son'][$key1]['son'][$key2]['son'] = D()->table('jiuku_join_winery_honor A,jiuku_winery B')->field('B.id,B.fname,B.cname')->where('A.winery_id = B.id AND A.honor_id = '.$val2['id'].' AND B.status = \'1\' AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->select();
                }
            }
        }
        $winery_idarr_res = D('JoinWineryHonor')->where(array('honor_id'=>array('in',$honor_idarr),'is_del'=>'-1'))->getfield('winery_id',true);
        $hot_list = D('Winery')->field('id,fname,cname')->where(array('id'=>array('in',$winery_idarr_res),'status'=>'1','is_del'=>'-1'))->order('queue ASC')->limit(10)->select();
        foreach($hot_list as $key=>$val){
            $hot_list[$key]['img'] = D('WineryImg')->where(array('winery_id'=>$val['id'],'status'=>'1','is_del'=>'-1'))->order('queue ASC')->limit(1)->getfield('filename');
        }
        $this->assign('left_list',$left_list);
        $this->assign('left_list2',$left_list2);
        $this->assign('hot_list',$hot_list);
        $this->assign('seo_t','逸香名庄-包含法国酒庄，波尔多庄园，名庄葡萄酒品鉴');
        $this->assign('seo_k','名庄,酒庄,庄园,波尔多,法国名庄,列级名庄,葡萄酒名庄,葡萄酒庄园,葡萄酒酒庄');
        $this->assign('seo_d','逸香名庄—汇集法国波尔多地区列级名庄信息，频道详解了圣爱美容列级酒庄、格拉夫列级庄园、1855梅多克列级酒庄、苏玳和巴萨克列级酒庄和波美侯酒庄等法国波尔多葡萄酒酒庄历史、人文。并可发布名庄葡萄酒品鉴记录，分享您对圣爱美容列级、格拉夫列级、1855梅多克列级、苏玳和巴萨克列级和波美侯酒庄等列级酒庄葡萄酒的观点。');
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml('index',C('SHTML_SAVE_PATH.MINGZHUANG'),C('TEMP_PATH').'mingzhuang_index.html');
        return C('SHTML_SAVE_PATH.MINGZHUANG').'index'.C('HTML_FILE_SUFFIX');
    }
    public function mingzhuang_detail($id){
        $id = $id ? $id : $_GET['id'];
        $evalparty_res = D('Evalparty')->field('id,sname,fname,cname')->where(array('status'=>'1','is_del'=>'-1'))->order('id ASC')->select();
        foreach($evalparty_res as $key=>$val){
            $evalparty_snamearr[] = $val['sname'];
        }
        $res = D('Winery')->field('id,fname,cname,tel,url,address,acreage,plant_age,g_map,description,content,seo_t,seo_k,seo_d,country_id,hit')->where(array('id'=>$id))->find();
        $res['img'] = D('WineryImg')->field('filename,description,alt')->where(array('winery_id'=>$id,'status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        $res['country'] = D('Country')->field('id,fname,cname')->where(array('id'=>$res['country_id']))->find();
        $res['grape'] = D()->table('jiuku_grape A,jiuku_join_winery_grape B')->field('A.id,A.fname,A.cname,B.grape_percentage')->where('B.winery_id = '.$id.' AND B.grape_id = A.id AND B.is_del = \'-1\' AND A.status = \'1\' AND A.is_del = \'-1\'')->order('B.grape_percentage DESC')->select();
        $honor_idarr = D('JoinWineryHonor')->where(array('winery_id'=>$id,'is_del'=>'-1'))->getfield('honor_id',true);
        foreach($honor_idarr as $key=>$val){
            $honor_id = $val;
            while($honor_id){
                $honor_res = D('Honor')->where(array('id'=>$honor_id,'honorgroup_id'=>1,'status'=>'1','is_del'=>'-1'))->find();
                if($honor_res) $res['honor'][$key][] = array('id' => $honor_res['id'],'fname' => $honor_res['fname'],'cname' => $honor_res['cname']);
                $honor_id = $honor_res['pid'];
            }
            if($res['honor'][$key]) $res['honor'][$key] = array_reverse($res['honor'][$key]);
        }
        $res['wine'] = D()->table('jiuku_wine A,jiuku_join_wine_winery B')->field('A.id,A.fname,A.cname,A.winetype_id')->where('B.winery_id = '.$id.' AND B.wine_id = A.id AND B.is_del = \'-1\' AND A.status = \'1\' AND A.is_del = \'-1\' AND A.merge_id = 0')->select();
        foreach($res['wine'] as $key=>$val){
            $res['wine_idstr'] .= empty($res['wine_idstr']) ? $val['id'] : ','.$val['id'];
            $winetype_id = $val['winetype_id'];
            while($winetype_id){
                $winetype_res = D('Winetype')->where(array('id'=>$winetype_id,'status'=>'1','is_del'=>'-1'))->find();
                if($winetype_res){
                    $res['wine'][$key]['winetype'][] = array('id' => $winetype_res['id'],'fname' => $winetype_res['fname'],'cname' => $winetype_res['cname']);
                }
                $winetype_id = $winetype_res['pid'];
            }
            if($res['wine'][$key]['winetype']) $res['wine'][$key]['winetype'] = array_reverse($res['wine'][$key]['winetype']);
            $res['wine'][$key]['ywine'] = D('Ywine')->field('id,year')->where(array('wine_id'=>$val['id'],'status'=>'1','is_del'=>'-1'))->order('year DESC')->select();
            foreach($res['wine'][$key]['ywine'] as $ky=>$vy){
                foreach($evalparty_res as $ke=>$ve){
                    $res['wine'][$key]['ywine'][$ky]['eval'][] = array('score'=>D('YwineEval')->where(array('ywine_id'=>$vy['id'],'evalparty_id'=>$ve['id'],'is_del'=>'-1'))->getfield('score'),'sname'=>$ve['sname']);
                }
            }
        }
        $this->assign('evalparty_res',$evalparty_res);
        $this->assign('res',$res);
        $this->assign('seo_t',$res['seo_t']);
        $this->assign('seo_k',$res['seo_k']);
        $this->assign('seo_d',$res['seo_d']);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml($id,C('SHTML_SAVE_PATH.MINGZHUANG'),C('TEMP_PATH').'mingzhuang_detail.html');
        return C('SHTML_SAVE_PATH.MINGZHUANG').$id.C('HTML_FILE_SUFFIX');
    }
    public function mingzhuang_comment($id){
        $id = $id ? $id : $_GET['id'];
        $res = D('Winery')->field('id,fname,cname,g_map,seo_t,seo_k,seo_d,hit')->where(array('id'=>$id))->find();
        $wine_idarr_res = D()->table('jiuku_wine A,jiuku_join_wine_winery B')->field('A.id')->where('B.winery_id = '.$id.' AND B.wine_id = A.id AND B.is_del = \'-1\' AND A.status = \'1\' AND A.is_del = \'-1\'')->select();
        foreach($wine_idarr_res as $key=>$val){
            $wine_idarr[] = $val['id'];
        }
        $res['wine_idstr'] = implode(',',$wine_idarr);
        //评论
        $limit = 10;
        $cUrl = C('DOMAIN.I').'?m=api/client/grandcru.get_jiup_by_id&wine_id='.$res['wine_idstr'].'&limit='.$limit.'&page=1';
        $res['comment'] = json_decode(CurlGet($cUrl),true);
        import('@.ORG.Util.Page2');
        $p2 = new Page2($res['comment']['rst']['total_count'], $limit);
        $p2->nowPage = $_GET['page'];
        $res['comment']['pageHtml'] = $p2->pageHtml();
        $this->assign('res',$res);
        $this->assign('seo_t',$res['seo_t']);
        $this->assign('seo_k',$res['seo_k']);
        $this->assign('seo_d',$res['seo_d']);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml($id.'_comment',C('SHTML_SAVE_PATH.MINGZHUANG'),C('TEMP_PATH').'mingzhuang_comment.html');
        return C('SHTML_SAVE_PATH.MINGZHUANG').$id.'_comment'.C('HTML_FILE_SUFFIX');
    }
    public function region_index(){
        $res = D('Country')->field('id,fname,cname,description')->where(array('status'=>'1','is_del'=>'-1'))->order('queue asc')->select();
        foreach($res as $key=>$val){
            $res[$key]['img'] = D('CountryImg')->where(array('country_id'=>$val['id'],'status'=>'1','is_del'=>'-1'))->order('queue asc')->getfield('filename');
            $res[$key]['region'] = D('Region')->field('id,fname,cname')->where(array('country_id'=>$val['id'],'pid'=>0,'pid2'=>0,'status'=>'1','is_del'=>'-1'))->order('queue asc')->select();
            foreach($res[$key]['region'] as $k=>$v){
                $res[$key]['region'][$k]['img'] = D('RegionImg')->where(array('region_id'=>$v['id'],'status'=>'1','is_del'=>'-1'))->order('queue asc')->getfield('filename');
            }
            $res[$key]['json_region'] = $res[$key]['region'] ? json_encode($res[$key]['region']) : '';
        }
        $this->assign('res',$res);
        $this->assign('other_css',array('Jiuku/css/region/index.css'));
        $this->assign('other_js',array('Jiuku/js/region/index.js'));
        $this->assign('seo_t','葡萄酒产区 – 逸香网');
        $this->assign('seo_k','葡萄酒产区,法国葡萄酒,波尔多,意大利葡萄酒,西班牙葡萄酒,葡萄酒产区介绍');
        $this->assign('seo_d','世界各国的葡萄酒产区资料库，法国、意大利、西班牙、德国、澳大利亚、新西兰、美国、智利、阿根廷、南非等知名葡萄产区的详细介绍。');
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml('index',C('SHTML_SAVE_PATH.REGION'),C('TEMP_PATH').'region_index.html');
        return C('SHTML_SAVE_PATH.REGION').'index'.C('HTML_FILE_SUFFIX');
    }
    public function region_detail($id){
        $id = $id ? $id : $_GET['id'];
        $clist = D('Country')->field('id,fname,cname,description')->where(array('status'=>'1','is_del'=>'-1'))->order('queue asc')->select();
        $this->assign('clist',$clist);
        $res = D('Region')->field('id,fname,cname,content,seo_t,seo_k,seo_d,pid,country_id')->where(array('id'=>$id,'status'=>'1','is_del'=>'-1'))->find();
        $res['img'] = D('RegionImg')->field('id,filename,description,alt')->where(array('region_id'=>$id,'status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        $res['belong']['country'] = D('Country')->field('id,fname,cname')->where(array('id'=>$res['country_id'],'status'=>'1','is_del'=>'-1'))->find();
        $res['belong']['region'] = D('Region')->field('id,fname,cname')->where(array('id'=>$res['pid'],'status'=>'1','is_del'=>'-1'))->find();
        $res['region'] = D('Region')->field('id,fname,cname')->where(array('pid'=>$id,'status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        foreach($res['region'] as $key=>$val){
            $res['region'][$key]['img'] = D('RegionImg')->field('id,filename,description,alt')->where(array('region_id'=>$val['id'],'status'=>'1','is_del'=>'-1'))->order('queue ASC')->limit(1)->find();
        }
        $res['redgrape'] = D()->table('jiuku_join_region_grape A,jiuku_grape B')->field('B.id,B.fname,B.cname')->where('A.region_id='.$id.' AND B.color_id = 1 AND A.grape_id = B.id AND A.is_del=\'-1\' AND B.status=\'1\' AND B.is_del=\'-1\'')->select();
        $res['whitegrape'] = D()->table('jiuku_join_region_grape A,jiuku_grape B')->field('B.id,B.fname,B.cname')->where('A.region_id='.$id.' AND B.color_id = 2 AND A.grape_id = B.id AND A.is_del=\'-1\' AND B.status=\'1\' AND B.is_del=\'-1\'')->select();
        $res['countrylevel'] = D('Regionlevel')->where(array('country_id'=>$res['country_id'],'status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        $this->assign('res',$res);
        $this->assign('seo_t',$res['seo_t']);
        $this->assign('seo_k',$res['seo_k']);
        $this->assign('seo_d',$res['seo_d']);
        $this->assign('type','region');
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml($id,C('SHTML_SAVE_PATH.REGION'),C('TEMP_PATH').'region_detail.html');
        return C('SHTML_SAVE_PATH.REGION').$id.C('HTML_FILE_SUFFIX');
    }
    public function country_detail($id){
        $id = $id ? $id : $_GET['id'];
        $clist = D('Country')->field('id,fname,cname,description')->where(array('status'=>'1','is_del'=>'-1'))->order('queue asc')->select();
        $this->assign('clist',$clist);
        $res = D('Country')->field('id,fname,cname,content,seo_t,seo_k,seo_d')->where(array('id'=>$id,'status'=>'1','is_del'=>'-1'))->find();
        $res['img'] = D('CountryImg')->field('id,filename,description,alt')->where(array('country_id'=>$id,'status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        $res['region'] = D('Region')->field('id,fname,cname')->where(array('country_id'=>$id,'pid'=>0,'status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        foreach($res['region'] as $key=>$val){
            $res['region'][$key]['img'] = D('RegionImg')->field('id,filename,description,alt')->where(array('region_id'=>$val['id'],'status'=>'1','is_del'=>'-1'))->order('queue ASC')->limit(1)->find();
        }
        $res['redgrape'] = D()->table('jiuku_join_country_grape A,jiuku_grape B')->field('B.id,B.fname,B.cname')->where('A.country_id='.$id.' AND B.color_id = 1 AND A.grape_id = B.id AND A.is_del=\'-1\' AND B.status=\'1\' AND B.is_del=\'-1\'')->select();
        $res['whitegrape'] = D()->table('jiuku_join_country_grape A,jiuku_grape B')->field('B.id,B.fname,B.cname')->where('A.country_id='.$id.' AND B.color_id = 2 AND A.grape_id = B.id AND A.is_del=\'-1\' AND B.status=\'1\' AND B.is_del=\'-1\'')->select();
        $res['countrylevel'] = D('Regionlevel')->where(array('country_id'=>$id,'status'=>'1','is_del'=>'-1'))->order('queue ASC')->select();
        $this->assign('res',$res);
        $this->assign('seo_t',$res['seo_t']);
        $this->assign('seo_k',$res['seo_k']);
        $this->assign('seo_d',$res['seo_d']);
        $this->assign('type','country');
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml('c'.$id,C('SHTML_SAVE_PATH.REGION'),C('TEMP_PATH').'region_detail.html');
        return C('SHTML_SAVE_PATH.REGION').'c'.$id.C('HTML_FILE_SUFFIX');
    }
    public function region_map_index(){
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml('index',C('SHTML_SAVE_PATH.REGION').'map'.DS,C('TEMP_PATH').'region_map_index.html');
        return C('SHTML_SAVE_PATH.REGION').'map'.DS.'index'.C('HTML_FILE_SUFFIX');
    }
    public function region_map_detail_ini(){
        $country_ids = array(3,5,14,16,21,32,34,39,47,40);
        foreach($country_ids as $id){
            $this->country_map_detail($id);
        }
        $region_ids = array(279,327,1,545);
        foreach($region_ids as $id){
            $this->region_map_detail($id);
        }
    }
    public function country_map_detail($id){
        $id = $id ? $id : $_GET['id'];
        $res = D()->query('SELECT `id`,`map_img` FROM `jiuku_country` WHERE `status` = \'1\' AND `is_del` = \'-1\' AND `id` = '.$id);
        $this->assign('map_img',C('DOMAIN.UPLOAD').'Jiuku/Country/maps/'.$res[0]['map_img']);
        dump($res);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml('c'.$id,C('SHTML_SAVE_PATH.REGION').'map'.DS,C('TEMP_PATH').'region_map_detail.html');
        return C('SHTML_SAVE_PATH.REGION').'map'.DS.'c'.$id.C('HTML_FILE_SUFFIX');
    }
    public function region_map_detail($id){
        $id = $id ? $id : $_GET['id'];
        $res = D()->query('SELECT `id`,`map_img` FROM `jiuku_region` WHERE `status` = \'1\' AND `is_del` = \'-1\' AND `id` = '.$id);
        $this->assign('map_img',C('DOMAIN.UPLOAD').'Jiuku/Region/maps/'.$res[0]['map_img']);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml($id,C('SHTML_SAVE_PATH.REGION').'map'.DS,C('TEMP_PATH').'region_map_detail.html');
        return C('SHTML_SAVE_PATH.REGION').'map'.DS.'c'.$id.C('HTML_FILE_SUFFIX');
    }
    public function region_wap_index(){
        $res = D()->query('SELECT A.`id`,A.`fname`,A.`cname`,B.`cname` AS `lvl_cname`,B.`sname` AS `lvl_sname`,C.`cname` AS `country_cname` FROM `jiuku_region` A LEFT JOIN `jiuku_regionlevel` B ON A.`regionlevel_id` = B.`id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' LEFT JOIN `jiuku_country` C ON A.`country_id` = C.`id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' WHERE A.`id` IN(260,426,327,279,1,1160,603,941,789,1246) AND A.`status` = \'1\' AND A.`is_del` = \'-1\' ORDER BY field(A.`id`,260,426,327,279,1,1160,603,941,789,1246)');
        foreach($res as $key=>$val){
            $img_res = D()->query('SELECT `filename` FROM `jiuku_region_img` WHERE `status` = \'1\' AND `is_del` = \'-1\' AND `region_id` = '.$val['id'].' ORDER BY `queue` ASC');
            if($img_res){
                $res[$key]['img'] = C('DOMAIN.UPLOAD').'Jiuku/Region/images/'.$img_res[0]['filename'];
            }
        }
        $this->assign('res',$res);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml('index',C('SHTML_SAVE_PATH.REGION_WAP'),C('TEMP_PATH').'region_wap_index.html');
        return C('SHTML_SAVE_PATH.REGION_WAP').'index'.C('HTML_FILE_SUFFIX');
    }
    public function region_wap_detail_ini(){
        $ids = array(260,426,327,279,1,1160,603,941,789,1246);
        foreach($ids as $id){
            $this->region_wap_detail($id);
        }
    }
    public function region_wap_detail($id){
        $id = $id ? $id : $_GET['id'];
        $res = D()->query('SELECT A.`id`,A.`cname`,A.`content`,C.`id` AS `country_id`,C.`cname` AS `country_cname` FROM `jiuku_region` A LEFT JOIN `jiuku_country` C ON A.`country_id` = C.`id` AND C.`status` = \'1\' AND C.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`id` = '.$id);
        $res = $res[0];
        $res['img_res'] = D()->query('SELECT CONCAT(\''.C('DOMAIN.UPLOAD').'\',\'Jiuku/Region/images/\',`filename`) AS `url` FROM `jiuku_region_img` WHERE `status` = \'1\' AND `is_del` = \'-1\' AND `region_id` = '.$id.' ORDER BY `queue` ASC');
        $this->assign('res',$res);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml($id,C('SHTML_SAVE_PATH.REGION_WAP'),C('TEMP_PATH').'region_wap_detail.html');
        return C('SHTML_SAVE_PATH.REGION_WAP').$id.C('HTML_FILE_SUFFIX');
    }
    public function grape_wap_index(){
    	$res['red'] = D()->query('SELECT `id`,`fname`,`cname` FROM `jiuku_grape` WHERE `status` = \'1\' AND `is_del` = \'-1\' AND `id` IN (1,7,2,13,70,416,14,32,25,16,10,33) ORDER BY field(id,1,7,2,13,70,416,14,32,25,16,10,33)');
    	$res['white'] = D()->query('SELECT `id`,`fname`,`cname` FROM `jiuku_grape` WHERE `status` = \'1\' AND `is_del` = \'-1\' AND `id` IN (3,6,4,9,11,5,19,96) ORDER BY field(id,3,6,4,9,11,5,19,96)');
        $this->assign('res',$res);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml('index',C('SHTML_SAVE_PATH.GRAPE_WAP'),C('TEMP_PATH').'grape_wap_index.html');
        return C('SHTML_SAVE_PATH.GRAPE_WAP').'index'.C('HTML_FILE_SUFFIX');
    }
    public function grape_wap_detail_ini(){
        $ids = array(1,7,2,13,70,416,14,32,25,16,10,33,3,6,4,9,11,5,19,96);
        foreach($ids as $id){
            $this->grape_wap_detail($id);
        }
    }
    public function grape_wap_detail($id){
        $id = $id ? $id : $_GET['id'];
        $res = D()->query('SELECT `id`,`cname`,`fname`,`content` FROM `jiuku_grape` WHERE `status` = \'1\' AND `is_del` = \'-1\' AND `id` = '.$id);
        $res = $res[0];
        $this->assign('res',$res);
        C('HTML_FILE_SUFFIX','.shtml');
        $this->buildHtml($id,C('SHTML_SAVE_PATH.GRAPE_WAP'),C('TEMP_PATH').'grape_wap_detail.html');
        return C('SHTML_SAVE_PATH.GRAPE_WAP').$id.C('HTML_FILE_SUFFIX');
    }
}
