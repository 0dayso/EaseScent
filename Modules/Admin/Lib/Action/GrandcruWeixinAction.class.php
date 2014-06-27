<?php

class GrandcruWeixinAction extends Api {

    var $toUserName;

    var $fromUserName;

    var $keyword;

    var $msgType;

    var $event;

    var $isweb = false;

    public function _initialize() {}

    public function config() {
        $this->_logs();
        if($GLOBALS["HTTP_RAW_POST_DATA"]) {
            $this->_response();
        } else {
            $this->_checkSignature();
        }
    }

    //XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D
    public function wineryList() {
        $this->isweb = true;
        $type = $_GET['type'];   
        $id = $_GET['id'];   
        $title = "逸香名庄列表";
        $nav = "";
        if($type == 'item') {
            $data = $this->_getWineryData($id);
            switch($id) {
                case 2:
                    $title = '圣爱美容列级一级特等酒庄A级';
                    break;
                case 3:
                    $title = '圣爱美容列级一级特等酒庄B级';
                    break;
                case 4:
                    $title = '格拉夫列级酒庄';
                    break;
                case 10:
                    $title = '1855梅多克列级酒庄第一级';
                    break;
                case 6:
                    $title = '1855梅多克列级酒庄第二级';
                    break;
                case 7:
                    $title = '1855梅多克列级酒庄第三级';
                    break;
                case 8:
                    $title = '1855梅多克列级酒庄第四级';
                    break;
                case 9:
                    $title = '1855梅多克列级酒庄第五级';
                    break;
                case 12:
                    $title = '苏玳和巴萨克列级优等一级酒庄';
                    break;
                case 13:
                    $title = '苏玳和巴萨克列级一级酒庄';
                    break;
                case 14:
                    $title = '苏玳和巴萨克列级二级酒庄';
                    break;
            }
            if(in_array($id, array('6','7','8','9','10'))) {
                $nav = "
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=10&type=item'>1855梅多克列级酒庄第一级</a></li>
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=6&type=item'>1855梅多克列级酒庄第二级</a></li>
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=7&type=item'>1855梅多克列级酒庄第三级</a></li>
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=8&type=item'>1855梅多克列级酒庄第四级</a></li>
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=9&type=item'>1855梅多克列级酒庄第五级</a></li>";
            } elseif(in_array($id, array('2','3'))) {
                $nav = "
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=2&type=item'>圣爱美容列级一级特等酒庄A级</a></li>
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=3&type=item'>圣爱美容列级一级特等酒庄B级</a></li>";
            } elseif(in_array($id, array('12','13','14'))) {
                $nav = "
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=12&type=item'>苏玳和巴萨克列级优等一级酒庄</a></li>
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=13&type=item'>苏玳和巴萨克列级一级酒庄</a></li>
                    <li><a href='http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&id=14&type=item'>苏玳和巴萨克列级二级酒庄</a></li>";
            }
        } else {
            $data = $this->_getSearchwineryData($id);
            $title = "\"{$id}\"搜索结果";
        }
        $this->assign('data', $data);
        $this->assign('title', $title);
        $this->assign('nav', $nav);
        $this->assign('type', $type);
        $this->display('list');
    }

    //YEVMSE8OZlNAT0VCU1R2REhZSE8OVkhPRFNYZURVQEhN
    public function wineryDetail() {
        $id = intval($_GET['id']);
        $data = $this->_getWineryInfo($id);
        $this->assign('pic', $data['pic']);
        $this->assign('winery', $data['winery']);
        $this->assign('class', $data['class']);
        $this->assign('grape', $data['grape']);
        $this->assign('wines', $data['wines']);
        $this->assign('country', $data['country']);
        $this->display('content');
    }

    //XHlwdHMyWm98c3l%2Bb2hKeHRldHMyanRzeFF0bmk%3D
    public function wineList() {
        $this->isweb = true;
        $year = $_GET['year'];
        $keyword = $_GET['keyword'];
        $data = $this->_searchScoreDataResult($year, $keyword);
        $this->assign('wine', $data);
        $this->assign('year', $year);
        $this->assign('keyword', $keyword);
        $this->display('sclist');
    }

    protected function _response() {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->toUserName = $postObj->ToUserName;
            $this->fromUserName = $postObj->FromUserName;
            $this->keyword = trim($postObj->Content);
            $this->msgType = $postObj->MsgType;
            $this->event = $postObj->Event;
            $this->_weChat();
        } else {
            echo "Input something...";
        }
    }

    protected function _weChat() {
        switch($this->msgType) {
            case 'event':
                $this->_event();
                break;
            case 'text':
                $this->_text();
                break;
            default:
                $this->_firstFollow();
                break;
        }
    }

    protected function _event() {
        if($this->event == 'subscribe') {
            $this->_firstFollow();
        }
    }

    protected function _text() {
        if(in_array($this->keyword, array('1', '2', '3', '4', '5'))) {
            $this->_responseWinery();
        } else {
            //搜索
            $keyArr = array_filter(explode(' ', $this->keyword));
            asort($keyArr);
            $count = count($keyArr);
            if($count == 1) {
                $keyword = array_shift($keyArr);
                $this->_searchKeyword($keyword);
            } elseif($count == 2) {
                $year = array_shift($keyArr);
                $keyword = array_shift($keyArr);
                $year = intval($year);
                if($year < 70 && $year > -1) {
                    $year = 2000 + $year;
                } elseif($year >= 70 && $year < 100) {
                    $year = 1900 + $year;
                } elseif($year > 2020 || $year < 1970) {
                    $this->_firstFollow();
                }
                if(false == $this->_searchScore($year, $keyword)) {
                    $this->_firstFollow();
                }
            } else {
                $this->_firstFollow();
            }
        }
    }

    //搜索酒评分
    protected function _searchScoreDataResult($year, $keyword) {
        if(!$year || !$keyword) {
            return false;
        }
        $limit = $this->isweb == false ? 'LIMIT 11' : '';
        $sql = "SELECT w.`id` as id, yw.`id` as yid, w.`cname`, yw.`year` FROM `jiuku_wine` as w,`jiuku_ywine` as yw WHERE w.`id` = yw.`wine_id` AND yw.`year` = {$year} AND (w.`cname` LIKE '%{$keyword}%' OR w.`fname` LIKE '%{$keyword}%') ".$limit;
        $data = M()->query($sql);
        $ids = $yids = array();
        foreach($data as $key => $val) {
            $ids[] = $val['id'];
            $yids[] = $val['yid'];
        }
        $pics = $scores = $wineryIds = $wineryPicsResult = $wineryPics = array();
        if($ids) {
            $winerySql = "SELECT wine_id,winery_id  FROM `jiuku_join_wine_winery` WHERE is_del = '-1' AND wine_id IN(". implode(',', $ids) .")";
            $wineryData = M()->query($winerySql);
            foreach($wineryData as $val) {
                $wineryIds[$val['wine_id']] = $val['winery_id'];
            }
            if($wineryIds) {
                $picSql = "SELECT i.filename,i.winery_id FROM `jiuku_winery_img` i WHERE winery_id IN (" . implode(',', $wineryIds) .") AND i.id = (SELECT ii.id FROM `jiuku_winery_img` ii WHERE ii.winery_id = i.winery_id ORDER BY queue ASC LIMIT 1)";
                $wineryPicsResult = M()->query($picSql);
            }
            foreach($wineryPicsResult as $key => $val) {
                $wineryPics[$val['winery_id']] = $val['filename'];
            }
        }
        foreach($wineryIds as $key => $val) {
            $pics[$key] = isset($wineryPics[$val]) ? $wineryPics[$val] : '';
        }
        if($yids) {
            $ySql = "SELECT score,evalparty_id,ywine_id FROM `jiuku_ywine_eval` WHERE ywine_id IN(". implode(',', $yids) .")";
            $tmp = M()->query($ySql);
            foreach($tmp as $val) {
                $key = $val['ywine_id'].'_'.$val['evalparty_id'];
                $scores[$key] = $val['score'];
            }
        }
        $newData = array();
        foreach($data as $key => $val) {
            if($this->_wineryPic($pics[$val['id']], 80, 80)) {
                $picurl = 'http://upload.wine.cn/Jiuku/Winery/images/'.$pics[$val['id']].'.80.80';
            } else {
                $picurl = '';
            }
            $scoreRP = $val['yid'].'_1';
            $scoreWS = $val['yid'].'_2';
            $scoreJR = $val['yid'].'_3';
            $scoreWE = $val['yid'].'_4';
            
            //无评分、无关联酒庄的数据丢弃
            if((!$scoreRP && !$scoreWS && !$scoreJR) || !isset($wineryIds[$val['id']]) || (!$val['cname'] && !$val['fname'])) {
                continue;
            }
            $newData[] = array(
                'title' => !empty($val['cname']) ? $val['cname']: $val['fname'],
                'picurl' => $picurl,
                'description' => '',
                'url' => 'http://ajax.wine.cn/?action=YEVMSE8OZlNAT0VCU1R2REhZSE8OVkhPRFNYZURVQEhN&id='. $wineryIds[$val['id']],
                'year' => $val['year'],
                'rp' => isset($scores[$scoreRP]) ? $scores[$scoreRP]: '',
                'ws' => isset($scores[$scoreWS]) ? $scores[$scoreWS]: '',
                'jr' => isset($scores[$scoreJR]) ? $scores[$scoreJR]: '',
            );
        }
        return $newData;
    }


    //微信搜酒评分
    protected function _searchScore($year, $keyword) {
        $data = $this->_searchScoreDataResult($year, $keyword);
        if(empty($data)) {
            $this->_firstFollow();
        }
        $newData = array();
        $newData[] = array(
            'title' => '逸香名庄微信版',
            'picurl' => 'http://public.wine.cn/grandcruWeixin/g.jpg',
            'description' => '',
            'url' => 'http://ajax.wine.cn/?action=XHlwdHMyWm98c3l%2Bb2hKeHRldHMyanRzeFF0bmk%3D&year='.$year.'&keyword='.$keyword,
        );
        $count = count($data);
        foreach($data as $key => $val) {
            if($count > 9 && $key > 7) {
                $newData[] = array(
                    'title' => '还有很多结果，点我查看更多',
                    'picurl' => '',
                    'description' => '',
                    'url' => 'http://ajax.wine.cn/?action=XHlwdHMyWm98c3l%2Bb2hKeHRldHMyanRzeFF0bmk%3D&year='.$year.'&keyword='.$keyword,
                );
                break;
            }
            $desc  = !empty($val['rp']) ? 'RP:'.$val['rp'] .' ': '' ;
            $desc .= !empty($val['ws']) ? 'WS:'.$val['ws'] .' ': '' ;
            $desc .= !empty($val['jr']) ? 'JR:'.$val['jr'] .' ': '' ;
            $newData[] = array(
                'title' => $val['title']. ' ' .$val['year'].'年' . "\n". $desc,
                'picurl' => $val['picurl'],
                'description' => $val['description'],
                'url' => $val['url'],
            );
        }
        $this->_responseItem($newData);
    }

    //返回列级名庄
    protected function _responseWinery() {
        switch($this->keyword) {
            case 1:
                $this->_medoc();
                break;
            case 2:
                $this->_grave();
                break;
            case 3:
                $this->_saintEmilion();
                break;
            case 4:
                $this->_sauternesBarsac();
                break;
            case 5:
                $this->_pomerol();
                break;
        }
    }

    //梅多克
    protected function _medoc() {
        $data = M('honor', 'jiuku_')->where(array('pid' => 5))->order('queue ASC')->select();
        $newData = array();
        $newData[] = array(
            'title' => '1855梅多克列级酒庄',
            'description' => '',
            'picurl' => 'http://public.wine.cn/grandcruWeixin/medoc.jpg',
            'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&type=item&id=10',
        );
        foreach($data as $key => $val) {
            $newData[] = array(
                'title' => $val['cname'],
                'description' => $val['description'],
                'picurl' => 'http://public.wine.cn/grandcruWeixin/'.($key + 1).'.png',
                'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&type=item&id='.$val['id'],
            );
        }
        $this->_responseItem($newData);
    }

    //saintEmilion
    protected function _saintEmilion() {
        $data = M('honor', 'jiuku_')->where(array('pid' => 1))->order('queue ASC')->select();
        $newData = array();
        $newData[] = array(
            'title' => '圣爱美容列级酒庄',
            'description' => '',
            'picurl' => 'http://public.wine.cn/grandcruWeixin/se.jpg',
            'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&type=item&id=2',
        );
        foreach($data as $key => $val) {
                $picurl = "http://public.wine.cn/grandcruWeixin/".(($key == 0) ? 'A' : 'B').".png";
                $newData[] = array(
                    'title' => $val['cname'],
                    'description' => $val['description'],
                    'picurl' => $picurl,
                    'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&type=item&id='.$val['id'],
                );
        }
        $this->_responseItem($newData);
    }

    //sauternesBarsac
    protected function _sauternesBarsac() {
        $data = M('honor', 'jiuku_')->where(array('pid' => 11))->order('queue ASC')->select();
        $newData = array();
        $newData[] = array(
            'title' => '苏玳和巴萨克列级酒庄',
            'description' => '',
            'picurl' => 'http://public.wine.cn/grandcruWeixin/sb.jpg',
            'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&type=item&id=12',
        );
        foreach($data as $val) {
                $picurl = "http://public.wine.cn/grandcruWeixin/".(($key == 0) ? 'AA' : ($key == 1 ? 'A' : 'B')).".png";
                $newData[] = array(
                    'title' => $val['cname'],
                    'description' => $val['description'],
                    'picurl' => $picurl,
                    'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D&type=item&id='.$val['id'],
                );
        }
        $this->_responseItem($newData);
    }

    //格拉夫列级酒庄
    protected function _grave() {
        $newData = $this->_getWineryData(4, 'grave.jpg', '格拉夫列级酒庄');
        $this->_responseItem($newData);
    }

    //波尔多波美侯名庄
    protected function _pomerol() {
        $newData = $this->_getWineryData(15, 'pom.jpg', '波美侯知名酒庄');
        $this->_responseItem($newData);
    }

    //根据关键词搜索名庄
    protected function _searchKeyword($keyword) {
        $newData = $this->_getSearchwineryData($keyword, 'g.jpg');
        if($newData) {
            $this->_responseItem($newData);
        } else {
            $this->_firstFollow();
        }
    }

    //根据hid检索名庄
    protected function _getWineryData($hid, $bigpic = '', $title = '') {
        $sql = "SELECT h.winery_id, h.honor_id, w.id, w.cname,w.fname, i.winery_id, i.filename FROM `jiuku_join_winery_honor` as h LEFT JOIN `jiuku_winery` as w ON h.winery_id = w.id LEFT JOIN `jiuku_winery_img` as i ON h.winery_id = i.winery_id WHERE h.`honor_id` = {$hid} AND w.is_del = '-1' AND i.id = (SELECT img.id FROM `jiuku_winery_img` as img WHERE img.winery_id = h.winery_id ORDER BY img.`queue` ASC LIMIT 1)";
        $data = M()->query($sql);
        $newData = $this->_wineryData($data, 'item', $hid, $bigpic, $title);
        return $newData;
    }

    //处理搜索名庄数据
    protected function _getSearchwineryData($keyword, $bigpic) {
        $limit = $this->isweb == false ? 'LIMIT 11' : '';
        $sql = "SELECT w.cname, w.id, i.winery_id, i.filename FROM `jiuku_winery` as w LEFT JOIN `jiuku_winery_img` as i ON w.id = i.winery_id WHERE w.is_del = '-1' AND ( w.`cname` LIKE '%{$keyword}%' OR w.`fname` LIKE '%{$keyword}%' ) AND i.id = (SELECT img.id FROM `jiuku_winery_img` as img WHERE img.winery_id = w.id ORDER BY `queue` ASC LIMIT 1) ".$limit;
        $data = M()->query($sql);
        $newData = $this->_wineryData($data, 'search', $keyword, $bigpic, '逸香名庄微信版');
        return $newData;
    }

    //获取单一名庄详细信息
    protected function _getWineryInfo($id) {
        $pic = M('winery_img', 'jiuku_')->where(array('winery_id' => $id))->order('queue ASC')->find();
        $winery = M('winery', 'jiuku_')->where(array('id' => $id, 'is_del' => '-1'))->find();
        $class = M()->query("SELECT h.cname as sname, hh.cname as fname FROM `jiuku_honor` h LEFT JOIN `jiuku_join_winery_honor` w ON w.honor_id = h.id LEFT JOIN `jiuku_honor` as hh ON h.pid = hh.id  WHERE w.winery_id = {$id} ");
        $grape = M()->query("SELECT g.cname, wg.grape_percentage FROM `jiuku_join_winery_grape` wg, `jiuku_grape` g WHERE wg.winery_id = {$id} AND wg.grape_id = g.id ");
        $wines = M()->query("SELECT w.id, w.cname, w.fname FROM `jiuku_join_wine_winery` wy LEFT JOIN `jiuku_wine` w ON wy.wine_id = w.id WHERE wy.winery_id = {$id} AND wy.is_del = '-1' AND w.is_del = '-1' AND w.cname <> ''");
        $country = M('country', 'jiuku_')->where(array('id' => $winery['country_id']))->find();
        foreach($wines as $key => $val) {
            $years = M()->query("SELECT id, year, wine_id FROM `jiuku_ywine` WHERE wine_id = ".$val['id']." AND is_del = '-1' ORDER BY `year` DESC");
            $yids = $score = $yscore = $scores = array();
            foreach($years as $val) {
                $yids[] = $val['id'];
            }
            if($yids) {
                $score = M()->query("SELECT score, ywine_id, evalparty_id FROM `jiuku_ywine_eval` WHERE ywine_id IN (". implode(',', $yids).") AND is_del = '-1'");
            }
            foreach($score as $k => $v) {
                $ykey = $v['ywine_id'].'_'.$v['evalparty_id'];
                $yscore[$ykey] = $v['score'];
            }
            foreach($years as $k => $v) {
                $rp = $v['id'].'_1';
                $ws = $v['id'].'_2';
                $jr = $v['id'].'_3';
                $scores[] = array(
                    'year' => $v['year'],
                    'rp' => $yscore[$rp],
                    'ws' => $yscore[$ws],
                    'jr' => $yscore[$jr],
                );
            }
            $wines[$key]['score'] = $scores;
        }
        $picurl = '';
        if($pic) {
            $this->_wineryPic($pic['filename'], '620', '320');
            $picurl = 'http://upload.wine.cn/Jiuku/Winery/images/'.$pic['filename'].'.620.320';
        }
        $grapes = '';
        foreach($grape as $gval) {
            $grapes .= $gval['grape_percentage'].'%'.$gval['cname']. ' ';
        }
        return array(
            'pic' => $picurl,
            'winery' => $winery,
            'class' => isset($class[0]) ? $class[0] : array(),
            'grape' => $grapes,
            'wines' => $wines,
            'country' => $country,
        );
    }

    //处理名庄数据
    protected function _wineryData($data, $type = 'item', $keyword = '', $bigpic = '', $title = '') {
        $newData = array();
        $count = count($data);
        if(!$count) {
            return false;
        }
        if($this->isweb == false) {
            $exturl = "&type={$type}&id={$keyword}";
            $newData[] = array(
                'title' => $title,
                'picurl' => 'http://public.wine.cn/grandcruWeixin/'.$bigpic,
                'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D'.$exturl,
                'description' => ''
            );
            foreach($data as $key => $val) {
                if($key > 7 && $count > 9) {
                    $newData[] = array(
                        'title' => "还有很多结果，点我查看更多",
                        'picurl' => '',
                        'url' => 'http://ajax.wine.cn/?action=XntydnEwWG1%2BcXt8bWpIenZndnEwaHZxem1mU3Zsaw%3D%3D'.$exturl,
                        'description' => ''
                    );
                    break;
                } else {
                    //生成缩略图
                    $w = $h = 80;
                    $this->_wineryPic($val['filename'], $w, $h);
                    $newData[] = array(
                        'title' => $val['cname'],
                        'picurl' => 'http://upload.wine.cn/Jiuku/Winery/images/'.$val['filename'].'.'.$w.'.'.$h,
                        'url' => 'http://ajax.wine.cn/?action=YEVMSE8OZlNAT0VCU1R2REhZSE8OVkhPRFNYZURVQEhN&id='.$val['id'],
                        'description' => ''
                    );
                }
            }
        } else {
            foreach($data as $val) {
                $w = $h = 180;
                $this->_wineryPic($val['filename'], $w, $h);
                $newData[] = array(
                    'title' => $val['cname'],    
                    'fname' => $val['fname'],    
                    'picurl' => 'http://upload.wine.cn/Jiuku/Winery/images/'.$val['filename'].'.'.$w.'.'.$h,
                    'url' => 'http://ajax.wine.cn/?action=YEVMSE8OZlNAT0VCU1R2REhZSE8OVkhPRFNYZURVQEhN&id='.$val['id'],
                );
            }
        }
        return $newData;
    }

    //第一次关注发消息
    protected function _firstFollow() {
        $html  = "欢迎您关注逸香名庄微信平台！我们将会一如既往的给您提供优质的信息服务。您只需要回复相应数字，便可以查看对应列级下的酒庄\n";
        $html .= "1.1855梅多克列级酒庄\n";
        $html .= "2.格拉夫列级酒庄\n";
        $html .= "3.圣爱美容列级酒庄\n";
        $html .= "4.苏玳和巴萨克列级酒庄\n";
        $html .= "5.法国波尔多波美侯名庄\n";
        $html .= "此外，您还可以直接输入酒庄名称查询。例如：“拉菲”，“白马庄园”。您还可以直接查询评分，例如：“拉菲古堡 98”，“拉菲  98”。";
        $this->_responseMsg($html);
    }

    //回复图文模板
    protected function _responseItem($data) {
        $tpl  = "<xml>";
        $tpl .= "<ToUserName><![CDATA[{$this->fromUserName}]]></ToUserName>";
        $tpl .= "<FromUserName><![CDATA[{$this->toUserName}]]></FromUserName>";
        $tpl .= "<CreateTime>".time()."</CreateTime>";
        $tpl .= "<MsgType><![CDATA[news]]></MsgType>";
        $tpl .= "<ArticleCount>".count($data)."</ArticleCount>";
        $tpl .= "<Articles>";
        foreach($data as $val) {
            $tpl .= "<item>";
            $tpl .= "<Title><![CDATA[".$val['title']."]]></Title>";
            $tpl .= "<Description><![CDATA[".$val['description']."]]></Description>";
            $tpl .= "<PicUrl><![CDATA[".$val['picurl']."]]></PicUrl>";
            $tpl .= "<Url><![CDATA[".$val['url']."]]></Url>";
            $tpl .= "</item>";
        }
        $tpl .= "</Articles>";
        $tpl .= "<FuncFlag>1</FuncFlag>";
        $tpl .= "</xml>";
        echo $tpl;
        exit();
    }

    //回复文本消息模板
    protected function _responseMsg($msg) {
        $textTpl  = "<xml>";
        $textTpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
        $textTpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
        $textTpl .= "<CreateTime>%s</CreateTime>";
        $textTpl .= "<MsgType><![CDATA[%s]]></MsgType>";
        $textTpl .= "<Content><![CDATA[%s]]></Content>";
        $textTpl .= "<FuncFlag>1</FuncFlag>";
        $textTpl .= "</xml>";
        $resultStr = sprintf($textTpl, $this->fromUserName, $this->toUserName, time(), 'text', $msg);
        echo $resultStr;
        exit();
    }

    //名庄图片生成
    protected function _wineryPic($filename, $width = 120, $height = 120) {
        $path = COMMON_UPLOAD_PATH.'Jiuku/Winery/images/'.$filename;
        if(is_file($path)) {
            $this->_createImage($path, $width, $height);
            return true;
        } else {
            return false;
        }
    }

    //生成酒图片
    protected function _winePic($filename, $width = 120, $height = 120) {
        $path = COMMON_UPLOAD_PATH.'Jiuku/Wine/images/'.$filename;
        if(is_file($path)) {
            $this->_createImage($path, $width, $height);
            return true;
        } else {
            return false;
        }
    }

    //图片生成
    protected function _createImage($im, $width='', $height='', $quality=95){
        $outfile = $im . '.'.$width.'.'.$height;
        if(is_file($outfile)) {
            return;
        }
	    $file = basename($im);
	    $tempArr = explode(".", $file);
	    $fileExt = array_pop($tempArr);
	    $fileExt = trim($fileExt);
        $ext = strtolower($fileExt);

	    if(!in_array($ext,array('gif','jpg','jpeg','png'))){
            return false;
	    }
        
        //取得当前图片信息
	    $picSize=getimagesize($im);
	    $imgType = array(1=>'gif', 2=>'jpeg', 3=>'png');
        $iwidth = $picSize[0];
	    $iheight = $picSize[1];
    	$itype    = $imgType[$picSize[2]];
	    $funcCreate = "imagecreatefrom".$itype;
	    if (!function_exists ($funcCreate) && !function_exists('imagecreatetruecolor') && !function_exists('imagecopyresampled')) {
	        return '';
        }
        if($width == '' && $height != '') {
            //等比缩，以高为基准
            $x = 0;
            $y = 0;
            $h = $iheight;
            $w = $iwidth;
            if($height > $iheight) {
                $width = $iwidth;
                $height = $iheight;
            } else {
                $width = round($height/$iheight) * $iwidth;
            }
        } elseif($height == '' && $width != '') {
            //等比缩，以宽为基准
            $x = 0;
            $y = 0;
            $h = $iheight;
            $w = $iwidth;
            if($width > $iwidth) {
                $width = $iwidth;
                $height = $iheight;
            } else {
                $height = round(($width/$iwidth) *$iheight);
            }
        } else {
	        //智能生成缩略图宽高
	        $ws = $iwidth/$width;
	        $hs = $iheight/$height;
	        if($ws>=1 && $hs>=1){
                if($ws>$hs){
		            $w = round($width * $hs);
		            $h = $iheight;
		            $x = round(($iwidth - $w)/2);
		            $y = 0;
                } else {
                    $w = $iwidth;
		            $h = round($height * $ws);
		            $x = 0;
		            $y = round(($iheight - $h)/2);
                }
    	    } elseif($ws<1 && $hs<1) {
                $w = $iwidth;
                $h = $iheight;
                $x = 0;
                $y = 0;
                $width = $iwidth;
                $height = $iheight;
            } elseif($ws>1 && $hs<1) {
                $w = $width;
                $h = $iheight;
                $height = $iheight;
                $x = ($iwidth - $w)/2;
                $y = 0;
	        } else {
                $w = $iwidth;
                $h = $height;
                $width = $iwidth;
                $x = 0;
                $y = ($iheight - $h)/2;
	        }
        }
	    //获取图像
	    $img = @$funcCreate($im);
        //新建图像
	    $thumb = imagecreatetruecolor($width, $height);
	    //复制图像
	    imagecopyresampled($thumb, $img, 0, 0, $x ,$y, $width, $height, $w, $h);
	    $funcOut = "image" . $itype;
        $funcOut($thumb, $outfile);
    }

    //服务器验证
    protected function _checkSignature() {
        $signature = $_GET['signature'];
        $data = array(
            "IKeodf8974JiekLOeed93G",
            $_GET['timestamp'],
            $_GET['nonce'],
        );
        asort($data);
        $sign = sha1(implode('', $data));
        if($sign == $signature) {
            die($_GET['echostr']);
        }
    }

    //访问日志
    protected function _logs() {
        file_put_contents('/share/wine.cn/weixin.log', date('Y-m-d H:i:s  ').$_SERVER['REMOTE_ADDR']. '  ' .$_SERVER['QUERY_STRING']. '  '. $GLOBALS["HTTP_RAW_POST_DATA"] . "\n", FILE_APPEND);
    }
}
