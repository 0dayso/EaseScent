<?php
// 酒标扫描记录管理
class WinelabelScanRecordAction extends CommonAction {
    /**
     * 扫描记录列表
     */
    public function index(){
        $btime = Input::getVar($_REQUEST['btime']);
        $etime = Input::getVar($_REQUEST['etime']);
        $status = Input::getVar($_REQUEST['status']);
        $map = array();
        $url = '';
        if($btime) {
            $btime = intval($btime) ? $btime : strtotime($btime);
            $_REQUEST['btime'] = date("Y-m-d H:i:s",$btime);
            $map['cteate_time'] = array('egt',$btime);
            $url .= '&btime='.strtotime($btime);
        }
        if($etime) {
            $etime = intval($etime) ? $etime : strtotime($etime);
            $_REQUEST['etime'] = date("Y-m-d H:i:s",$etime);
            $map['cteate_time'] = array('elt',$etime);
            $url .= '&etime='.strtotime($etime);
        }
        if($status) {
            $map['status'] = $status;
            $url .= '&status='.$status;
        }
        $list = $this->_list(D('WinelabelScanRecord'), $map, 5, $url);
        foreach($list as $key=>$val){
            $list[$key]['into'] = base64_encode(json_encode(array('Winelabel'=>array(C('DOMAIN.UPLOAD').'/Jiuku/Wine/images/'.$val['filename']))));
        }
        $this->assign('list', $list);
        $this->display();
    }
    function test(){
        $this->getallson(M('Winetype','jiuku_'),array('is_del'=>'-1'),1,'pid');
    }
    function getallson($model,$map,$id,$pfield){
        $return[] = $model->where(array_merge($map,array('id'=>$id)))->find();
        $while_idarr = array($id);
        $i = 0;
        do{
            $res = $model->where(array_merge($map,array($pfield=>array('in',$while_idarr))))->select();
            $while_idarr = array();
            foreach($res as $val){
                $return[] = $val;
                $while_idarr[] = $val['id'];
            }
        }while(count($while_idarr)>0);
        return $return;
    }

    /**
     * 关联酒款
     */
    public function linkWine() {
        //关联
        $wine_id = Input::getVar($_GET['wine_id']);
        if($wine_id){
            $bbackpage = Input::getVar($_GET['bbackpage']);//return_page_parameter
            $scan_record_id = Input::getVar($_GET['scan_record_id']);
            $scan_record_res = D('WinelabelScanRecord')->where(array('id'=>$scan_record_id,'status'=>2))->find();
            if(!$scan_record_res)   $this->_jumpGo('代入酒标状态错误', 'error', base64_decode($bbackpage));

            $wine_res = M('Wine','jiuku_')->where(array('id'=>$wine_id,'merge_id'=>0,'is_del'=>'-1'))->find();
            if(!$wine_res)   $this->_jumpGo('关联酒款状态错误', 'error');
            $wine_label = $this->GrabImage(C('SCAN_WINE_LABEL_URL_PATH').$scan_record_res['filename'],'Jiuku/Wine/labels/');
            if(!$wine_label)    $this->_jumpGo('酒标图片解析错误', 'error');
            import('@.ORG.Util.Image');
            $image = new Image();
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/50_50/'.$wine_label,'jpg',50,50);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/100_100/'.$wine_label,'jpg',100,100);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/150_150/'.$wine_label,'jpg',150,150);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/200_200/'.$wine_label,'jpg',200,200);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/250_250/'.$wine_label,'jpg',250,250);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/300_300/'.$wine_label,'jpg',300,300);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/350_350/'.$wine_label,'jpg',350,350);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/400_400/'.$wine_label,'jpg',400,400);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/450_450/'.$wine_label,'jpg',450,450);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/500_500/'.$wine_label,'jpg',500,500);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/550_550/'.$wine_label,'jpg',550,550);
            $image->thumb(C('UPLOAD_PATH').'Jiuku/Wine/labels/'.$wine_label,C('UPLOAD_PATH').'Jiuku/Wine/labels/600_600/'.$wine_label,'jpg',600,600);
            M('WineLabel','jiuku_')->add(array('wine_id' => $wine_id,'filename'=>$wine_label));
            D('WinelabelScanRecord')->save(array('id'=>$scan_record_id,'wine_id'=>$wine_id,'status'=>3));

            $uid = $scan_record_res['uid'];
            if(intval($uid)){
                //删缓存
                if($Redis = A('Redis')->linkRedis())    $Redis->delete('#sns_app_#sacn_record_last_3_'.$uid);
                //--//发私信
                $uid = 1000147;
                $msg = '测试信息';
                if($token_res = CurlGet(C('DOMAIN.I_API').'index.php?m=api/oauth/token.get&uid='.$uid)){
                    if($token_res = json_decode($token_res,true)){
                        $token = $token_res['rst'];
                        $sixin_data = array('token'=>$token,'uid'=>$uid,'msg'=>$msg);
                        CurlPost(C('DOMAIN.I_API').'index.php?m=api/oauth/msg.publish_msg',$sixin_data);
                    }
                }
            }
            $this->_jumpGo('操作成功', 'succeed', base64_decode($bbackpage));
        }
        //关联END
        $backpage = Input::getVar($_GET['backpage']);//return_page_parameter
        $scan_record_id = Input::getVar($_GET['scan_record_id']);
        $scan_record_res = D('WinelabelScanRecord')->where(array('id'=>$scan_record_id,'status'=>2))->find();
        if(!$scan_record_res)   $this->_jumpGo('代入酒标状态错误', 'error', base64_decode($backpage));
        $this->assign('bbackpage', $backpage);
        $this->assign('scan_record_id', $scan_record_id);
        $this->assign('scan_record_res',$scan_record_res);

        $keywords = Input::getVar($_REQUEST['keywords']);
        $winetype = Input::getVar($_REQUEST['winetype']);
        $country = Input::getVar($_REQUEST['country']);
        $region = Input::getVar($_REQUEST['region']);
        $winery = Input::getVar($_REQUEST['winery']);
        $grape = Input::getVar($_REQUEST['grape']);
        $map = array();
        $map_idarr = array();
        $url = '';
        if($keywords) {
            $map_k['fname'] = array('like', '%'.$keywords.'%');
            $map_k['ename'] = array('like', '%'.$keywords.'%');
            $map_k['cname'] = array('like', '%'.$keywords.'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keywords=' . $keywords;
        }
        if($winetype) {
            $winetype_arr = $this->getallson(M('Winetype','jiuku_'),array('is_del'=>'-1'),$winetype,'pid');
            foreach($winetype_arr as $val){
                $winetype_idarr[] = $val['id'];
            }
            $map['winetype_id'] = count($winetype_idarr) ? array('in',$winetype_idarr) : 0;
            $url .= '&winetype=' . $winetype;
        }
        if($country) {
            $this->assign('country_res',M('Country','jiuku_')->where(array('id'=>$country,'is_del'=>'-1'))->find());
            $map['country_id'] = $country;
            $url .= '&country='.$country;
        }
        if($region || $winery || $grape)    $map_idarr = array();
        if($region) {
            $this->assign('region_res',M('Region','jiuku_')->where(array('id'=>$region,'is_del'=>'-1'))->find());
            $region_arr = $this->getallson(M('Region','jiuku_'),array('is_del'=>'-1'),$region,'pid');
            foreach($region_arr as $val){
                $region_idarr[] = $val['id'];
            }
            if($region_idarr){
                $region_map_idarr = M('JoinWineRegion','jiuku_')->where(array('region_id'=>array('in',$region_idarr),'is_del'=>'-1'))->getfield('wine_id',true);
            }
            //$region_map_idarr = $region_map_idarr ? $region_map_idarr : array(0);
            $map_idarr = count($map_idarr) ? array_intersect($map_idarr,$region_map_idarr) : $region_map_idarr;
            $url .= '&region=' . $region;
        }
        if($winery) {
            $this->assign('winery_res',M('Winery','jiuku_')->where(array('id'=>$winery,'is_del'=>'-1'))->find());
            $winery_map_idarr = M('JoinWineWinery','jiuku_')->where(array('winery_id'=>$winery,'is_del'=>'-1'))->getfield('wine_id',true);
            //$winery_map_idarr = $winery_map_idarr ? $winery_map_idarr : array(0);
            $map_idarr = count($map_idarr) ? array_intersect($map_idarr,$winery_map_idarr) : $winery_map_idarr;
            $url .= '&winery=' . $winery;
        }
        if($grape) {
            $this->assign('grape_res',M('Grape','jiuku_')->where(array('id'=>$grape,'is_del'=>'-1'))->find());
            $grape_map_idarr = M('JoinWineGrape','jiuku_')->where(array('grape_id'=>$grape,'is_del'=>'-1'))->getfield('wine_id',true);
            //$grape_map_idarr = $grape_map_idarr ? $grape_map_idarr : array(0);
            $map_idarr = count($map_idarr) ? array_intersect($map_idarr,$grape_map_idarr) : $grape_map_idarr;
            $url .= '&grape=' . $grape;
        }
        if($region || $winery || $grape)    $map['id'] = array('in',$map_idarr);

        $map['merge_id'] = 0;
        $map['is_del'] = '-1';
        $list = $this->_list(M('Wine','jiuku_'), $map, 10, $url);
        $winetype_list = D()->table('jiuku_winetype')->where(array('pid'=>0,'is_del'=>'-1'))->select();
        $this->assign('winetype_list',$winetype_list);
        /*foreach($list as $key=>$val){
            if($val['winetype_id'] != 0){
                $list[$key]['winetype_res'] = M('Winetype','jiuku_')->where(array('id'=>$val['winetype_id']))->find();
            }
            if($val['country_id'] != 0){
                $list[$key]['country_res'] = M('Country','jiuku_')->where(array('id'=>$val['country_id']))->find();
            }
            $list[$key]['join_region_res'] = M()->table('jiuku_join_wine_region A,jiuku_region B')->where('A.wine_id = '.$val['id'].' AND A.region_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('B.fname AS region_fname,B.cname AS region_cname')->select();
            $list[$key]['join_winery_res'] = M()->table('jiuku_join_wine_winery A,jiuku_winery B')->where('A.wine_id = '.$val['id'].' AND A.winery_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('B.fname AS winery_fname,B.cname AS winery_cname')->select();
        }
        foreach($list as $key=>$val){
            $list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
            $list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
        }
        dump($list);*/
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 状态更改
     */
    public function chgStatus() {
        $backpage = Input::getVar($_REQUEST['backpage']);//return_page_parameter
        $id = Input::getVar($_GET['id']);
        $status = Input::getvar($_GET['status']);
        $data = array('id' => $id,'status' => $status,);
        $this->_update(D('WinelabelScanRecord'),$data);
        D('WinelabelScanRecord')->where(array('wine_id'=>$id))->save(array('status'=>$status));
        $this->_jumpGo('ID为'.$id.'的酒款状态更改成功', 'succeed', base64_decode($backpage));
    }
}