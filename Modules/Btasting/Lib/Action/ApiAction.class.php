<?php

class ApiAction extends Api {

    public function _initialize() {
        if(!defined('NO_API')) {
            parent::_initialize();
        }
    }

    public function getWinePartyList() {
        $rst = M('wineparty')->field(array('id','dateline', 'type', 'password','name', 'pub', 'experts'))->where(array('status' => 1, 'locked' => 1))->select();
        if(is_array($rst)) {
            foreach($rst as $key => $val) {
                $rst[$key]['experts'] = $this->_experts($val['experts']);
            }
        }
		self::rst($rst ? $rst : "");
    }

    public function getWineParty() {
        $wpid = intval($_POST['id']);
        $rst = M('wineparty')->field(array('id','dateline', 'type', 'password','name', 'pub', 'experts'))->where(array('status' => 1, 'locked' => 1, 'id' => $wpid))->find();
        $rst['experts'] = $this->_experts($rst['experts']);
		self::rst($rst ? $rst : "");
    }

    public function getWines() {
        $wpid = intval($_POST['id']);
        $group = M('wgroups')->field(array('gid', 'wid', 'name', 'item'))->where(array('wid' => $wpid))->select();
        $wines = M('wines')->where(array('wid' => $wpid))->select();
        $rst = array();
        foreach($group as $gkey => $gval) {
            $wineArr = $arr = array();
            foreach($wines as $wine) {
                if($wine['gid'] == $gval['gid']) {
                    $arr['id'] = $wine['id'];
                    $arr['finish'] = $wine['finish'];
                    $arr['item'] = $wine['item'];
                    $arr['id'] = $wine['id'];
                    $wineArr[] = $arr;
                }
            }
            $rst[$gkey] = $gval;
            $rst[$gkey]['wines'] = $wineArr;
        }
		self::rst($rst ? $rst : "");
    }

    public function getWineDetail($jkid = '', $yid = '') {
        if(empty($jkid) || empty($yid)) {
            $id = intval($_POST['id']);
            $wine = M('wines')->where(array('id' => $id))->find();
            $jkid = $wine['jkid'];
            $yid = $wine['yid'];
        }

        if(empty($jkid) || empty($yid)) {
		    self::rst("", "900001", "酒款ID或者年份酒ID为空");
        }

        /**
         * 读取本地酒库数据库调用数据
         */
        $rstArr = array();
        $sql = "SELECT 
                w.fname as fname,
                w.cname as cname, 
                w.content as content,
                c.fname as country_fname, 
                c.ename as country_ename, 
                c.cname as country_cname,
                ty.cname as type,
                c.id as country_id
                FROM `jiuku_wine` w 
                LEFT JOIN `jiuku_country` c ON w.country_id = c.id
                JOIN `jiuku_winetype` ty ON w.winetype_id = ty.id
                WHERE w.id = {$jkid}";
        $wineInfo = M()->query($sql);
        if(isset($wineInfo[0])) {
            $rstArr['fname'] = $wineInfo[0]['fname'];
            $rstArr['cname'] = $wineInfo[0]['cname'];
            $rstArr['content'] = strip_tags($wineInfo[0]['content']);
            $rstArr['type'] = $wineInfo[0]['type'];
            $rstArr['country'] = array(
                'fname' => $wineInfo[0]['country_fname'],
                'cname' => $wineInfo[0]['country_cname'],
                'ename' => $wineInfo[0]['country_ename'],
                'id' => $wineInfo[0]['country_id'],
            );
        }
        
        $sql = "SELECT 
                wy.fname,
                wy.cname,
                wy.id
                FROM `jiuku_join_wine_winery` wj
                LEFT JOIN `jiuku_winery` wy ON wj.winery_id = wy.id
                WHERE wj.wine_id = {$jkid}";
        $wineryInfo = M()->query($sql);
        if(isset($wineryInfo[0])) {
            $rstArr['winery'] = array(
                'fname' => $wineryInfo[0]['fname'],
                'cname' => $wineryInfo[0]['cname'],
                'id' => $wineryInfo[0]['id'],
            );
        }
        
        $sql = "SELECT
                filename
                FROM `jiuku_wine_img`
                WHERE wine_id = {$jkid}
                ORDER BY queue ASC
                LIMIT 1";
        $picInfo = M()->query($sql);
        if(isset($picInfo[0])) {
            $rstArr['img'] = 'http://upload.wine.cn/Jiuku/Wine/images/' .$picInfo[0]['filename'];
        }
        
        $sql = "SELECT 
                year
                FROM `jiuku_ywine`
                WHERE id = {$yid}";
        $yearInfo = M()->query($sql);
        if(isset($yearInfo[0])) {
            $rstArr['year'] = $yearInfo[0]['year'];
        }
        
        $sql = "SELECT
                g.fname,
                g.cname,
                g.id,
                g.color_id as color,
                wg.grape_percentage as percentage
                FROM `jiuku_join_wine_grape` wg
                LEFT JOIN `jiuku_grape` g ON wg.grape_id = g.id
                WHERE wg.wine_id = ".$jkid;
        $grapeInfo = M()->query($sql);
        if($grapeInfo) {
            $rstArr['grape'] = $grapeInfo;
        }

        $regions = M('join_wine_region', 'jiuku_')->where(array('wine_id' => $jkid))->select();
        foreach($regions as $key => $regionData) {
            if($regionData['region_id']) {
                $regionId = $regionData['region_id'];
                while(true) {
                    $regionTmpData = M('region', 'jiuku_')->where(array('id' => $regionId))->find();
                    $rstArr['region'][$key][] = array(
                        'id' => $regionTmpData['id'],    
                        'fname' => $regionTmpData['fname'],    
                        'cname' => $regionTmpData['cname'],    
                    );
                    if($regionTmpData['pid']) {
                        $regionId = $regionTmpData['pid'];
                    } else {
                        break;
                    }
                }

            }
        }

        /**
         * 远程接口调用酒库数据
         *
        $wineurl = "http://ajax.wine.cn/?action=Xn1hf2E7REtVZH07Y316cVJ9enA%3D=&st=1&isf=1&isi=1&isc=1&isr=1&isw=1&isg=1&id=".$jkid;
        $wineInfo = CurlGet($wineurl);
        $wineInfo = json_decode($wineInfo, true);
        $ywineurl = "http://ajax.wine.cn/?action=X3xgfmA6RUpUZXw6bGJ8e3BTfHtx&st=1&isa=1&id=".$yid;
        $ywineInfo = CurlGet($ywineurl);
        $ywineInfo = json_decode($ywineInfo, true);
        $wInfo = array();
        if($wineInfo['errorCode'] == 0) {
            $result = $wineInfo['result'];
            $wInfo['fname'] = $result['fname'];
            $wInfo['cname'] = $result['cname'];
            $wInfo['country'] = $result['country'];
            $wInfo['region'] = $result['region'];
            $wInfo['winery'] = $result['winery'];   
            $wInfo['img'] = $result['img']; 
            $wInfo['grape'] = $result['grape'];   
            $wInfo['content'] = $result['content'];   
            if($ywineInfo['errCode'] == 0) {
                $result = $ywineInfo['result'];
                $wInfo['year'] = $result['year'];
                $wInfo['agents'] = $result['agents'];
            }
        }
        **/
        if(isset($id)) {
            $rstArr['id'] = $id;
		    self::rst($rstArr ? $rstArr : "");
        }
        return $rstArr;
    }

    public function getWineScore() {
        $wpid = intval($_POST['id']);
        if(empty($wpid)) {
            self::rst("", "900002", "参数为空");
        }
        $rst = M('score')->field(array('round(avg(score)) as score', 'wineid'))->where(array('wid' => $wpid))->group('wineid')->select();
		self::rst($rst ? $rst : "");
    }

    public function syncWineScore() {
        $wineid = intval($_POST['wineid']);
        $wpid = intval($_POST['wid']);
        $score = intval($_POST['score']);
        $expert = intval($_POST['mid']);
        $context = isset($_POST['context']) ? $_POST['context']: '';

        if(empty($wineid) || empty($wpid) || empty($score) || empty($expert)) {
		    self::rst("", "900002", "参数为空");
        }
        //检测专家与酒会是否匹配
        $wp = M('wineparty')->where(array('id' => $wpid))->field(array('experts'))->find();
        $exp = M('expert')->where(array('mid' => $expert))->field(array('id'))->find();
        if(!isset($exp['id'])) {
		    self::rst("", "900003", "暂无该专家数据");
        }
        if(!in_array($exp['id'], explode("||", $wp['experts']))) {
		    self::rst("", "900004", "上传专家分数未在此酒会专家团中");
        }
        $exists = M('score')->where(array('wineid' => $wineid, 'wid' => $wpid, 'expert' => $expert))->count();
        if($exists) {
		    self::rst("", "900005", "专家分数已存在");
        }
        $rst = M('score')->add(array(
            'wineid' => $wineid,
            'wid' => $wpid,
            'score' => $score,
            'expert' => $expert,
            'context' => $context,
        ));
        D('Wtasting')->dealScore($wineid);
		self::rst($rst ? $rst : "");
    }

    protected function _experts($eids) {
        if(empty($eids)) {
            return '';
        }
        $eids = trim($eids, '|');
        $eids = str_replace('||', ',', $eids);
        return M('expert')->field(array('mid', 'name', 'description'))->where(' id IN ('.$eids.') ')->select();
    }
}
