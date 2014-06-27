<?php

/**
 * 酒管理
 */
class WineAction extends CommonAction {

    /**
     * 酒款列表
     */
    public function index() {
        $winery_id = Input::getVar($_REQUEST['winery_id']);
        $keyword = Input::getVar($_REQUEST['keyword']);
        $is_verify = Input::getVar($_REQUEST['is_verify']);
        $status = Input::getVar($_REQUEST['status']);
        $map = array();
        $url = '';
        if($winery_id) {
            $map_idarr = D('JoinWineWinery')->where(array('winery_id'=>$winery_id,'is_del'=>'-1'))->getfield('wine_id',true);
            $map_idarr = (count($map_idarr) > 0) ? $map_idarr : array(0);
            $map['id'] = array('in',$map_idarr);
            $url .= '&winery_id='.$winery_id;
            $winery_res = D('Winery')->field('id,fname,cname')->where(array('id'=>$winery_id,'is_del'=>'-1'))->find();
            $this->assign('winery_res',$winery_res);
        }
        if($keyword) {
            if(preg_match("/^(-|\+)?\d+$/",$keyword)){
                $map['id'] = $keyword;
            }else{
                $map_k['fname'] = array('like', '%'.$keyword.'%');
                $map_k['cname'] = array('like', '%'.$keyword.'%');
                $map_k['_logic'] = 'or';
                $map['_complex'] = $map_k;
            }
            $url .= '&keyword=' . $keyword;
        }
        if($status) {
            $map['status'] = $status;
            $url .= '&status='.$status;
        }
        if($is_verify) {
            $map['is_verify'] = $is_verify;
            $url .= '&is_verify='.$is_verify;
        }
        $map['merge_id'] = 0;
        $list = $this->_list(D('Wine'), $map, 15, $url);
        foreach($list as $key=>$val){
            if($val['winetype_id'] != 0){
                $list[$key]['winetype'] = D('Winetype')->where(array('id'=>$val['winetype_id']))->getfield('cname');
            }
            $list[$key]['join_region_res'] = D('Region')->wineIdGetRegion($val['id']);
            $list[$key]['join_winery_res'] = D()->table('jiuku_join_wine_winery A,jiuku_winery B')->where('A.wine_id = '.$val['id'].' AND A.winery_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('B.fname AS winery_fname,B.cname AS winery_cname')->select();
        }
        foreach($list as $key=>$val){
            $list[$key]['fname_s'] = String::msubstr($val['fname'],0,15);
            $list[$key]['cname_s'] = String::msubstr($val['cname'],0,7);
        }
        $this->assign('list', $list);
        //错误提示
        /*$error_map['is_del'] = '-1';
        $error_map['merge_id'] = 0;
        $error_map_s['ename'] = array(array('like','%'.$keyword),array('like',$keyword.'%'),'or');
        $error_map['']
        $error = D('Wine')->*/
        $this->display();
    }
    /**
     * 酒款增加
     */
    public function add() {
        $backpage = Input::getVar($_REQUEST['backpage']);//return_page_parameter
        if($this->isPost()) {
            preg_replace('/\s+/',' ',$data['production']);
            $_POST['fname'] = preg_replace('/\s+/',' ',Input::getVar($_POST['fname']));
            $_POST['ename'] = preg_replace('/\s+/',' ',Input::getVar($_POST['ename']));
            $wine_id = $this->_insert(D('Wine'));
            if($wine_id){
                $this->saveWineRedis($wine_id);
                foreach($_POST['joinregion_region_id'] as $key=>$val){
                    $join_region_data = array(
                                              'wine_id' => $wine_id,
                                              'region_id' => Input::getVar($_POST['joinregion_region_id'][$key]),
                                              );
                    $this->_insert(D('JoinWineRegion'),$join_region_data);
                }
                foreach($_POST['joinwinery_winery_id'] as $key=>$val){
                    $join_winery_data = array(
                                              'wine_id' => $wine_id,
                                              'winery_id' => Input::getVar($_POST['joinwinery_winery_id'][$key]),
                                              );
                    $this->_insert(D('JoinWineWinery'),$join_winery_data);
                }
                foreach($_POST['joingrape_grape_id'] as $key=>$val){
                    $join_grape_data = array(
                                               'wine_id' => $wine_id,
                                               'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
                                               'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
                                               );
                    $this->_insert(D('JoinWineGrape'),$join_grape_data);
                }
                if($_POST['into_dzjp_winelabel_scan_record_id']){//大众酒评酒标扫描记录代入信息
                    $into_dzjp_winelabel_scan_record_img = $this->GrabImage($_POST['into_dzjp_winelabel_scan_record_img'],'Wine/labels/');
                    if($into_dzjp_winelabel_scan_record_img){
                        import('@.ORG.Util.Image');
                        $image = new Image();
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/50_50/'.$into_dzjp_winelabel_scan_record_img,'jpg',50,50);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/100_100/'.$into_dzjp_winelabel_scan_record_img,'jpg',100,100);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/150_150/'.$into_dzjp_winelabel_scan_record_img,'jpg',150,150);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/200_200/'.$into_dzjp_winelabel_scan_record_img,'jpg',200,200);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/250_250/'.$into_dzjp_winelabel_scan_record_img,'jpg',250,250);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/300_300/'.$into_dzjp_winelabel_scan_record_img,'jpg',300,300);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/350_350/'.$into_dzjp_winelabel_scan_record_img,'jpg',350,350);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/400_400/'.$into_dzjp_winelabel_scan_record_img,'jpg',400,400);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/450_450/'.$into_dzjp_winelabel_scan_record_img,'jpg',450,450);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/500_500/'.$into_dzjp_winelabel_scan_record_img,'jpg',500,500);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/550_550/'.$into_dzjp_winelabel_scan_record_img,'jpg',550,550);
                        $image->thumb(C('UPLOAD_PATH').'Wine/labels/'.$into_dzjp_winelabel_scan_record_img,C('UPLOAD_PATH').'Wine/labels/600_600/'.$into_dzjp_winelabel_scan_record_img,'jpg',600,600);
                        D()->table('dzjp_winelabel_scan_record')->save(array('id'=>$_POST['into_dzjp_winelabel_scan_record_id'],'wine_id'=>$wine_id,'status'=>3));
                    }
                    $uid = D()->table('dzjp_winelabel_scan_record')->where(array('id'=>$_POST['into_dzjp_winelabel_scan_record_id']))->getfield('uid');
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
                    $into_label_data = array(
                        'wine_id' => $wine_id,
                        'filename' => $into_dzjp_winelabel_scan_record_img,
                        'year' => Input::getVar($_POST['into_dzjp_winelabel_scan_record_year']),
                    );
                    $this->_insert(D('WineLabel'),$into_label_data);
                }
                foreach($_POST['label_filename'] as $key=>$val){
                    $label_data = array(
                                      'wine_id' => $wine_id,
                                      'filename' => Input::getVar($_POST['label_filename'][$key]),
                                      'year' => Input::getVar($_POST['label_year'][$key]),
                                      );
                    $this->_insert(D('WineLabel'),$label_data);
                }
                foreach($_POST['img_filename'] as $key=>$val){
                    $img_data = array(
                                      'wine_id' => $wine_id,
                                      'filename' => Input::getVar($_POST['img_filename'][$key]),
                                      'description' => Input::getVar($_POST['img_description'][$key]),
                                      'alt' => Input::getVar($_POST['img_alt'][$key]),
                                      'queue' => Input::getVar($_POST['img_queue'][$key]),
                                      );
                    $this->_insert(D('WineImg'),$img_data);
                }
                //添加中文别名
                foreach(explode(';',$_POST['caname']) as $val){
                    $val = preg_replace('/\s+/',' ',Input::getVar($val));
                    if($val)    $caname_data[] = $val;
                }
                $caname_data = array_unique($caname_data);
                foreach($caname_data as $key=>$val){
                    if($key == 0){
                        D('Wine')->save(array('id'=>$wine_id,'cname'=>$val));
                        D('WineCaname')->add(array('cname'=>$val,'fname'=>$_POST['fname'],'ename'=>$_POST['ename'],'wine_id'=>$wine_id,'wine_hid'=>$wine_id,'status'=>$_POST['status']));
                    }else{
                        D('WineCaname')->add(array('cname'=>$val,'fname'=>$_POST['fname'],'ename'=>$_POST['ename'],'wine_id'=>$wine_id,'status'=>$_POST['status']));
                    }
                }
                if($_POST['jump_url'] != ''){
                    $this->_jumpGo('酒款添加成功,跳转至添加该款酒的年份数据', 'succeed', $_POST['jump_url'].$wine_id);
                }
                $this->_jumpGo('酒款添加成功', 'succeed', base64_decode($backpage));
            }
            $this->_jumpGo('酒款添加失败', 'error', base64_decode($backpage));
        }
        $id = Input::getVar($_GET['id']);//复制并新建功能
        if($id) {
            $wine_res = D('Wine')->where(array('id'=>$id))->find();
            //所属国家
            $wine_res['country_res'] = D('Country')->where(array('id'=>$wine_res['country_id'],'is_del'=>'-1'))->find();
            //产区
            $wine_res['join_region_res'] = D()->table('jiuku_join_wine_region A,jiuku_region B')->where('A.wine_id = '.$id.' AND A.region_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.wine_id,A.region_id,B.cname AS region_cname,B.fname AS region_fname')->select();
            //庄园
            $wine_res['join_winery_res'] = D()->table('jiuku_join_wine_winery A,jiuku_winery B')->where('A.wine_id = '.$id.' AND A.winery_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.wine_id,A.winery_id,B.cname AS winery_cname,B.fname AS winery_fname')->select();
            //葡萄品种
            $wine_res['join_grape_res'] = D()->table('jiuku_join_wine_grape A,jiuku_grape B')->where('A.wine_id = '.$id.' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.wine_id,A.grape_id,B.cname AS grape_cname,B.fname AS grape_fname,A.grape_percentage')->select();
            //酒标
            $wine_res['label_res'] = D('WineLabel')->where(array('wine_id'=>$id,'is_del'=>'-1'))->select();
            //图片
            $wine_res['img_res'] = D('WineImg')->where(array('wine_id'=>$id,'is_del'=>'-1'))->select();
            //中文别名
            $wine_res['caname_res'] = D('WineCaname')->where(array('wine_id'=>$id,'is_merge'=>'-1','is_del'=>'-1'))->select();
            $this->assign('res', $wine_res);
        }
        $into_dzjp_winelabel_scan_record_id = Input::getVar($_GET['into_dzjp_winelabel_scan_record_id']);//大众酒评酒标扫描记录代入信息
        if($into_dzjp_winelabel_scan_record_id) {
            $into_dzjp_winelabel_scan_record_res = D()->table('dzjp_winelabel_scan_record')->where(array('id'=>$into_dzjp_winelabel_scan_record_id,'status'=>2))->find();
            if(!$into_dzjp_winelabel_scan_record_res)    $this->_jumpGo('代入酒标状态错误', 'error', base64_decode($backpage));
            $this->assign('into_dzjp_winelabel_scan_record_res',$into_dzjp_winelabel_scan_record_res);
        }
        $this->assign('winetypeList', D('Winetype')->winetypeList());
        $this->assign('backpage',$backpage);
        $this->display();
    }
    /**
     * 酒款编辑
     */
    public function edit() {
        $id = Input::getVar($_REQUEST['id']);
        $backpage = Input::getVar($_REQUEST['backpage']);//return_page_parameter
        if(!$id) {
            $this->_jumpGo('参数为空!', 'error');
        }
        if($this->isPost()) {
            $_POST['fname'] = preg_replace('/\s+/',' ',Input::getVar($_POST['fname']));
            $_POST['ename'] = preg_replace('/\s+/',' ',Input::getVar($_POST['ename']));
            $is_success = $this->_update(D('Wine'));
            if($is_success){
                $this->saveWineRedis($id);
                foreach($_POST['joinregion_region_id'] as $key=>$val){
                    $join_region_data = array(
                                              'wine_id' => $id,
                                              'region_id' => Input::getVar($_POST['joinregion_region_id'][$key]),
                                              );
                    $this->_insert(D('JoinWineRegion'),$join_region_data);
                }
                D('JoinWineRegion')->where(array('id'=>array('in',explode(',',$_POST['del_joinregion_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                foreach($_POST['joinwinery_winery_id'] as $key=>$val){
                    $join_winery_data = array(
                                              'wine_id' => $id,
                                              'winery_id' => Input::getVar($_POST['joinwinery_winery_id'][$key]),
                                              );
                    $this->_insert(D('JoinWineWinery'),$join_winery_data);
                }
                D('JoinWineWinery')->where(array('id'=>array('in',explode(',',$_POST['del_joinwinery_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                foreach($_POST['joingrape_grape_id'] as $key=>$val){
                    $join_grape_data = array(
                                             'wine_id' => $id,
                                             'grape_id' => Input::getVar($_POST['joingrape_grape_id'][$key]),
                                             'grape_percentage' => Input::getVar($_POST['joingrape_grape_percentage'][$key]),
                                             );
                    $this->_insert(D('JoinWineGrape'),$join_grape_data);
                }
                foreach($_POST['upd_joingrape_id'] as $key=>$val){
                    $upd_join_grape_data = array(
                                                 'id' => Input::getVar($_POST['upd_joingrape_id'][$key]),
                                                 'grape_percentage' => Input::getVar($_POST['upd_joingrape_grape_percentage'][$key]),
                                                 );
                    $this->_update(D('JoinWineGrape'),$upd_join_grape_data);
                }
                D('JoinWineGrape')->where(array('id'=>array('in',explode(',',$_POST['del_joingrape_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                foreach($_POST['label_filename'] as $key=>$val){
                    $label_data = array(
                                      'wine_id' => $id,
                                      'filename' => Input::getVar($_POST['label_filename'][$key]),
                                      'year' => Input::getVar($_POST['label_year'][$key]),
                                      );
                    $this->_insert(D('WineLabel'),$label_data);
                }
                foreach($_POST['upd_label_id'] as $key=>$val){
                    $upd_label_data = array(
                        'id' => Input::getVar($_POST['upd_label_id'][$key]),
                        'year' => Input::getVar($_POST['upd_label_year'][$key]),
                    );
                    $this->_update(D('WineLabel'),$upd_label_data);
                }
                D('WineLabel')->where(array('id'=>array('in',explode(',',$_POST['del_label_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                foreach($_POST['img_filename'] as $key=>$val){
                    $img_data = array(
                                      'wine_id' => $id,
                                      'filename' => Input::getVar($_POST['img_filename'][$key]),
                                      'description' => Input::getVar($_POST['img_description'][$key]),
                                      'alt' => Input::getVar($_POST['img_alt'][$key]),
                                      'queue' => Input::getVar($_POST['img_queue'][$key]),
                                      );
                    $this->_insert(D('WineImg'),$img_data);
                }
                foreach($_POST['upd_img_id'] as $key=>$val){
                    $upd_img_data = array(
                                          'id' => Input::getVar($_POST['upd_img_id'][$key]),
                                          'description' => Input::getVar($_POST['upd_img_description'][$key]),
                                          'alt' => Input::getVar($_POST['upd_img_alt'][$key]),
                                          'queue' => Input::getVar($_POST['upd_img_queue'][$key]),
                                          );
                    $this->_update(D('WineImg'),$upd_img_data);
                }
                D('WineImg')->where(array('id'=>array('in',explode(',',$_POST['del_img_idstr']))))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                $this->_jumpGo('酒款编辑成功', 'succeed', base64_decode($backpage));
            }
            $this->_jumpGo('酒款编辑失败', 'error');
        }
        $wine_res = D('Wine')->where(array('id'=>$id))->find();
        //所属国家
        $wine_res['country_res'] = D('Country')->where(array('id'=>$wine_res['country_id'],'is_del'=>'-1'))->find();
        //产区
        $wine_res['join_region_res'] = D()->table('jiuku_join_wine_region A,jiuku_region B')->where('A.wine_id = '.$id.' AND A.region_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.wine_id,A.region_id,B.cname AS region_cname,B.fname AS region_fname')->select();
        //庄园
        $wine_res['join_winery_res'] = D()->table('jiuku_join_wine_winery A,jiuku_winery B')->where('A.wine_id = '.$id.' AND A.winery_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.wine_id,A.winery_id,B.cname AS winery_cname,B.fname AS winery_fname')->select();
        //葡萄品种
        $wine_res['join_grape_res'] = D()->table('jiuku_join_wine_grape A,jiuku_grape B')->where('A.wine_id = '.$id.' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.wine_id,A.grape_id,B.cname AS grape_cname,B.fname AS grape_fname,A.grape_percentage')->select();
        //酒标
        $wine_res['label_res'] = D('WineLabel')->where(array('wine_id'=>$id,'is_del'=>'-1'))->select();
        //图片
        $wine_res['img_res'] = D('WineImg')->where(array('wine_id'=>$id,'is_del'=>'-1'))->select();
        //中文别名
        $wine_res['caname_res'] = D('WineCaname')->where(array('cname'=>array('neq',''),'wine_id'=>$id,'is_merge'=>'-1','is_del'=>'-1'))->select();
        $this->assign('res', $wine_res);
        $this->assign('winetypeList', D('Winetype')->winetypeList());
        $this->assign('backpage',$backpage);
        $this->display();
    }

    /**
     * 中文别名管理
     */
    public function caname(){
        if($this->isPost()){
            $wine_id = Input::getVar($_POST['wine_id']);
            if(!$wine_res = D('Wine')->where(array('id'=>$wine_id,'is_del'=>'-1','merge_id'=>0))->find())
                $this->_jumpGo('参数错误', 'error');
            if($_POST['fromtype'] == 'add'){
                if(!($cname = preg_replace('/\s+/',' ',Input::getVar($_POST['cname']))))
                    $this->_jumpGo('参数错误', 'error');
                if(D('WineCaname')->where(array('cname'=>$cname,'wine_id'=>$wine_id,'is_del'=>'-1','is_merge'=>'-1'))->find())
                    $this->_jumpGo('该中文名已存在', 'error');
                D('WineCaname')->add(array('cname'=>$cname,'fname'=>$wine_res['fname'],'ename'=>$wine_res['ename'],'wine_id'=>$wine_id));
                $this->_jumpGo('添加成功', 'succeed', Url('caname').'&wine_id='.$wine_id);
            }elseif($_POST['fromtype'] == 'edit'){
                if(!($id = Input::getVar($_POST['id'])) || !($cname = preg_replace('/\s+/',' ',Input::getVar($_POST['cname']))))
                    $this->_jumpGo('参数错误', 'error');
                if(D('WineCaname')->where(array('cname'=>$cname,'wine_id'=>$wine_id,'id'=>array('neq',$id),'is_del'=>'-1','is_merge'=>'-1'))->find())
                    $this->_jumpGo('该中文名已存在', 'error');
                $old_res = D('WineCaname')->where(array('id'=>$id))->find();
                if($old_res['wine_hid'] !== 0)
                    D('Wine')->save(array('id'=>$old_res['wine_hid'],'cname'=>$cname));
                D('WineCaname')->save(array('id'=>$id,'cname'=>$cname,'fname'=>$wine_res['fname'],'ename'=>$wine_res['ename']));
                $this->_jumpGo('修改成功', 'succeed', Url('caname').'&wine_id='.$wine_id);
            }elseif($_POST['fromtype'] == 'del'){
                if(!($id = Input::getVar($_POST['id'])) || !($tid = Input::getVar($_POST['tid'])))
                    $this->_jumpGo('参数错误', 'error');
                if($id == $tid)
                    $this->_jumpGo('将要删除的别名不能与要替换关系的别名相同', 'error');
                $res = D('WineCaname')->where(array('id'=>$id))->find();
                if($res['wine_hid'] != 0)
                    $this->_jumpGo('主中文名不可直接删除', 'error');
                D('AgentsInternetSalesWine')->where(array('wine_caname_id'=>$id))->save(array('wine_caname_id'=>$tid,'wine_id'=>$wine_id));
                D('AgentsStoreSalesWine')->where(array('wine_caname_id'=>$id))->save(array('wine_caname_id'=>$tid,'wine_id'=>$wine_id));
                M('9pWineExperience','sns_')->where(array('wine_id'=>$id))->save(array('wine_id'=>$tid));
                D('WineCaname')->save(array('id'=>$id,'is_del'=>'1'));
                $this->_jumpGo('删除成功', 'succeed', Url('caname').'&wine_id='.$wine_id);
            }elseif($_POST['fromtype'] == 'key'){
                if(!$id = Input::getVar($_POST['id']))
                    $this->_jumpGo('参数错误', 'error');
                D('WineCaname')->where(array('wine_id'=>$wine_id))->save(array('wine_hid'=>0));
                D('WineCaname')->where(array('id'=>$id))->save(array('wine_hid'=>$wine_id));
                $hcname = D('WineCaname')->where(array('wine_hid'=>$wine_id))->getfield('cname');
                D('Wine')->save(array('id'=>$wine_id,'cname'=>$hcname));
                $this->_jumpGo('设置成功', 'succeed', Url('caname').'&wine_id='.$wine_id);
            }elseif($_POST['fromtype'] == 'status'){
                if(!$id = Input::getVar($_POST['id']))
                    $this->_jumpGo('参数错误', 'error');
                D('WineCaname')->where(array('wine_id'=>$wine_id))->save(array('status'=>Input::getVar($_POST['status'])));
                $this->_jumpGo('状态更改成功', 'succeed', Url('caname').'&wine_id='.$wine_id);
            }else{
                $this->_jumpGo('未知操作', 'error');
            }
        }
        $wine_id = Input::getVar($_REQUEST['wine_id']);
        if(!$wine_id)
            $this->_jumpGo('参数错误。即将关闭窗口', 'error', 'javascript:parent.caname_manage(0)');
        if(!$wine_res = D('Wine')->where(array('id'=>$wine_id,'is_merge'=>0,'is_del'=>'-1'))->find())
            $this->_jumpGo('没有该酒款,可能已被删除或合并。即将关闭窗口', 'error', 'javascript:parent.caname_manage(0)');
        $list = D('WineCaname')->where(array('wine_id'=>$wine_id,'is_merge'=>'-1','is_del'=>'-1'))->select();
        $this->assign('wine_res',$wine_res);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 合并前筛选
     */
    public function merge() {
        /*if($_SERVER['SERVER_NAME'] == 'admin.wine.cn'){
            $this->_jumpGo('暂不开放', 'succeed', Url('index').base64_decode(Input::getVar($_REQUEST['rpp'])));
        }*/
        $is_view_exist = D()->query('SHOW TABLES like \'jiuku_view_wine_merge\'');
        if(!$is_view_exist){
            $create_view_sql = 'CREATE VIEW jiuku_view_wine_merge AS  ';
            $create_view_sql .= 'SELECT A.id, A.fname, A.cname,A.country_id, B.region_id,C.winery_id ';
            $create_view_sql .= 'FROM `jiuku_wine` A ';
            $create_view_sql .= 'LEFT JOIN `jiuku_join_wine_region` B ON A.id = B.wine_id ';
            $create_view_sql .= 'LEFT JOIN `jiuku_region` BA ON BA.id = B.region_id ';
            $create_view_sql .= 'LEFT JOIN `jiuku_join_wine_winery` C ON A.id = C.wine_id ';
            $create_view_sql .= 'LEFT JOIN `jiuku_winery` CA ON CA.id = C.winery_id ';
            $create_view_sql .= 'WHERE A.merge_id = 0 AND A.is_del = \'-1\' AND B.is_del =\'-1\' AND BA.is_del =\'-1\' AND C.is_del =\'-1\' AND CA.is_del =\'-1\' ';
            D()->query($create_view_sql);
        }
        $this->assign('country_list',D('Country')->where(array('is_del'=>'-1'))->order('fname ASC')->select());
        $this->assign('region_list',D('Region')->where(array('is_del'=>'-1'))->order('fname ASC')->select());
        $this->assign('wine_count',D('Wine')->where(array('merge_id'=>0,'is_del'=>'-1'))->count());
        $this->display();
    }

    /**
     * 合并操作
     */
    public function merge2() {
        if($this->isPost()) {
            if(!$_POST['idarr'] || !$_POST['id']){
                $this->_jumpGo('参数错误', 'error');
            }
            $is_error = D('Wine')->where(array('id'=>array('in',$_POST['idarr']),'merge_id'=>array('neq',0),'is_del'=>'-1'))->select();
            if($is_error){
                $this->_jumpGo('合并参考项非法，请重新操作', 'error');
            }
            $is_success = $this->_update(D('Wine'));
            foreach($_POST['idarr'] as $key=>$val){
                if($val != $_POST['id']){
                    D('Wine')->where(array('id'=>$val))->save(array('merge_id'=>$_POST['id'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    //中文别名表
                    D('WineCaname')->where(array('wine_id'=>$val))->save(array('is_merge'=>'1'));
                }
            }
            if($is_success){
                //产区
                D('JoinWineRegion')->where(array('wine_id'=>$_POST['id']))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                if(isset($_POST['region_idarr']) && count($_POST['region_idarr']) > 0){
                    foreach($_POST['region_idarr'] as $key=>$val){
                        D('JoinWineRegion')->add(array('wine_id'=>$_POST['id'],'region_id'=>$val,'add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                }
                //酒庄
                D('JoinWineWinery')->where(array('wine_id'=>$_POST['id']))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                if(isset($_POST['winery_idarr']) && count($_POST['winery_idarr']) > 0){
                    foreach($_POST['winery_idarr'] as $key=>$val){
                        D('JoinWineWinery')->add(array('wine_id'=>$_POST['id'],'winery_id'=>$val,'add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                }
                //葡萄品种
                D('JoinWineGrape')->where(array('wine_id'=>$_POST['id']))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                if(isset($_POST['grape_idarr']) && count($_POST['grape_idarr']) > 0){
                    foreach($_POST['grape_idarr'] as $key=>$val){
                        D('JoinWineGrape')->add(array('wine_id'=>$_POST['id'],'grape_id'=>$val,'add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                }
                //图片
                D('WineImg')->where(array('wine_id'=>$_POST['id']))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                if(isset($_POST['img_filenamearr']) && count($_POST['img_filenamearr']) > 0){
                    foreach($_POST['img_filenamearr'] as $key=>$val){
                        D('WineImg')->add(array('wine_id'=>$_POST['id'],'filename'=>$val,'status'=>'1','add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                }
                //年份基本
                D('Ywine')->where(array('wine_id'=>$_POST['id']))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                $ywine_idarr = D('Ywine')->where(array('wine_id'=>$_POST['id']))->getfield('id',true);
                if($ywine_idarr){
                    D('YwineEval')->where(array('ywine_id'=>array('in',$ywine_idarr)))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    D('JoinYwineHonor')->where(array('ywine_id'=>array('in',$ywine_idarr)))->save(array('is_del'=>'1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                }
                foreach($_POST['ywine_yeararr'] as $key=>$val){
                    $ywine_res = D('Ywine')->where(array('wine_id'=>$_POST['id'],'year'=>$val))->find();
                    if($ywine_res){
                        $ywine_id = $ywine_res['id'];
                        D('Ywine')->where(array('id'=>$ywine_id))->save(array('price'=>$_POST['ywine_pricearr'][$key],'status'=>'1','is_del'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }else{
                        D('Ywine')->add(array('year'=>$val,'wine_id'=>$_POST['id'],'price'=>$_POST['ywine_pricearr'][$key],'status'=>'1','is_del'=>'-1','add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                }
                //年份评价
                foreach($_POST['ywine_eval_yeararr'] as $key=>$val){
                    $ywine_res = D('Ywine')->where(array('wine_id'=>$_POST['id'],'year'=>$val))->find();
                    if($ywine_res){
                        $ywine_id = $ywine_res['id'];
                        D('Ywine')->where(array('id'=>$ywine_id))->save(array('status'=>'1','is_del'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }else{
                        $ywine_id = D('Ywine')->add(array('year'=>$val,'wine_id'=>$_POST['id'],'status'=>'1','is_del'=>'-1','add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                    $ywine_eval_res = D('YwineEval')->where(array('ywine_id'=>$ywine_id,'evalparty_id'=>$_POST['ywine_eval_evalparty_idarr'][$key]))->find();
                    if($ywine_eval_res){
                        $ywine_eval_id = $ywine_eval_res['id'];
                        D('YwineEval')->where(array('id'=>$ywine_eval_id))->save(array('score'=>$_POST['ywine_eval_scorearr'][$key],'is_del'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }else{
                        $ywine_eval_id = D('YwineEval')->add(array('ywine_id'=>$ywine_id,'evalparty_id'=>$_POST['ywine_eval_evalparty_idarr'][$key],'score'=>$_POST['ywine_eval_scorearr'][$key],'is_del'=>'-1','add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                    $join_ywine_eval_refweb_res = D('JoinYwineEvalRefweb')->where(array('ywine_eval_id'=>$ywine_eval_id,'refweb_id'=>$_POST['ywine_eval_refweb_idarr'][$key]))->find();
                    if($join_ywine_eval_refweb_res){
                        $join_ywine_eval_refweb_id = $join_ywine_eval_refweb_res['id'];
                        D('JoinYwineEvalRefweb')->where(array('id'=>$join_ywine_eval_refweb_id))->save(array('ywine_eval_id'=>$ywine_eval_id,'refweb_id'=>$_POST['ywine_eval_refweb_idarr'][$key],'refweb_url'=>$_POST['ywine_eval_refweb_urlarr'][$key],'is_del'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }else{
                        D('JoinYwineEvalRefweb')->add(array('ywine_eval_id'=>$ywine_eval_id,'refweb_id'=>$_POST['ywine_eval_refweb_idarr'][$key],'refweb_url'=>$_POST['ywine_eval_refweb_urlarr'][$key],'is_del'=>'-1','add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                }
                //年份荣誉
                foreach($_POST['ywine_honor_yeararr'] as $key=>$val){
                    $ywine_res = D('Ywine')->where(array('wine_id'=>$_POST['id'],'year'=>$val))->find();
                    if($ywine_res){
                        $ywine_id = $ywine_res['id'];
                        D('Ywine')->where(array('id'=>$ywine_id))->save(array('status'=>'1','is_del'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }else{
                        $ywine_id = D('Ywine')->add(array('year'=>$val,'wine_id'=>$_POST['id'],'status'=>'1','is_del'=>'-1','add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                    $join_ywine_honor_res = D('JoinYwineHonor')->where(array('ywine_id'=>$ywine_id,'honor_id'=>$_POST['ywine_honor_honor_idarr'][$key]))->find();
                    if($join_ywine_honor_res){
                        $join_ywine_honor_id = $join_ywine_honor_res['id'];
                        D('JoinYwineHonor')->where(array('id'=>$join_ywine_honor_id))->save(array('is_del'=>'-1','last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }else{
                        $join_ywine_honor_id = D('JoinYwineHonor')->add(array('ywine_id'=>$ywine_id,'honor_id'=>$_POST['ywine_honor_honor_idarr'][$key],'is_del'=>'-1','add_time'=>time(),'add_aid'=>$_SESSION['admin_uid'],'last_edit_time'=>time(),'last_edit_aid'=>$_SESSION['admin_uid']));
                    }
                }
                $this->_jumpGo('酒款合并成功,正在关闭窗口', 'succeed', 'javascript:parent.selected_merge_close();');
            }
            $this->_jumpGo('酒款合并失败', 'error');
        }
        $idstr = Input::getVar($_GET['idstr']);
        if(!$idstr) {
            $this->_jumpGo('参数为空!', 'error');
        }
        $evalparty_res = D('Evalparty')->where(array('is_del'=>'-1'))->order('id ASC')->select();
        $this->assign('evalparty_res', $evalparty_res);
        $all_idarr = D('Wine')->where(array('id'=>array('in',explode(',',$idstr)),'merge_id'=>0,'is_del'=>'-1'))->getfield('id',true);
        //$all_idarr = explode(',',$idstr);
        foreach($all_idarr as $key=>$val){
            $res[$key] = D('Wine')->where(array('id'=>$val))->find();
            foreach($all_idarr as $k=>$v){
                if($val !== $v) $res[$key]['idarr'][] = $v;
            }
            $res[$key]['idstr'] = implode(',',$res[$key]['idarr']);
            $winetype_id = $res[$key]['winetype_id'];
            while($winetype_id){
                $winetype_res = D('Winetype')->where(array('id'=>$winetype_id,'is_del'=>'-1'))->find();
                if($winetype_res){
                    $res[$key]['winetype_res'][] = array('id' => $winetype_res['id'],'fname' => $winetype_res['fname'],'cname' => $winetype_res['cname']);
                }
                $winetype_id = $winetype_res['pid'];
            }
            if($res[$key]['winetype_res']) $res[$key]['winetype_res'] = array_reverse($res[$key]['winetype_res']);
            $res[$key]['country_res'] = D('Country')->field('id,fname,cname')->where(array('id'=>$res[$key]['country_id'],'is_del'=>'-1'))->find();
            $region_idarr = D('JoinWineRegion')->where(array('wine_id'=>$val,'is_del'=>'-1'))->getfield('region_id',true);
            foreach($region_idarr as $k=>$v){
                $region_id = $v;
                while($region_id){
                    $region_res = D('Region')->where(array('id'=>$region_id,'is_del'=>'-1'))->find();
                    if($region_res){
                        $res[$key]['region_res'][$k][] = array('id' => $region_res['id'],'fname' => $region_res['fname'],'cname' => $region_res['cname']);
                    }
                    $region_id = $region_res['pid'];
                }
                if($res[$key]['region_res'][$k]) $res[$key]['region_res'][$k] = array_reverse($res[$key]['region_res'][$k]);
            }
            $res[$key]['winery_res'] = D()->table('jiuku_join_wine_winery A,jiuku_winery B')->where('A.wine_id = '.$val.' AND A.winery_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.winery_id AS id,B.cname,B.fname')->select();
            $res[$key]['grape_res'] = D()->table('jiuku_join_wine_grape A,jiuku_grape B')->where('A.wine_id = '.$val.' AND A.grape_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.grape_id AS id,B.cname,B.fname,A.grape_percentage AS percent')->select();
            //$res[$key]['honor_res'] = D()->table('jiuku_join_wine_honor A,jiuku_honor B,jiuku_honorgroup C')->where('A.wine_id = '.$val.' AND A.honor_id = B.id AND B.honorgroup_id = C.id AND A.is_del = \'-1\' AND B.is_del = \'-1\' AND C.is_del = \'-1\'')->field('A.honor_id AS id,B.cname,C.cname AS group_cname')->select();
            $res[$key]['img_res'] = D('WineImg')->where(array('wine_id'=>$val,'is_del'=>'-1'))->select();
            $res[$key]['ywine_res'] = D('Ywine')->field('id,year,price')->where(array('wine_id'=>$res[$key]['id'],'is_del'=>'-1'))->order('year DESC')->select();
            foreach($res[$key]['ywine_res'] as $k=>$v){
                foreach($evalparty_res as $key_e=>$val_e){
                    $res[$key]['ywine_res'][$k]['eval'][$key_e]['evalparty_id'] = $val_e['id'];
                    $res[$key]['ywine_res'][$k]['eval'][$key_e]['evalparty_sname'] = $val_e['sname'];
                    $ywine_eval_res = D('YwineEval')->field('id,score')->where(array('ywine_id'=>$v['id'],'evalparty_id'=>$val_e['id'],'is_del'=>'-1'))->find();
                    $res[$key]['ywine_res'][$k]['eval'][$key_e]['id'] = $ywine_eval_res['id'];
                    $res[$key]['ywine_res'][$k]['eval'][$key_e]['score'] = $ywine_eval_res['score'];
                    $join_ywine_eval_refweb_res = D('JoinYwineEvalRefweb')->field('id,refweb_id,refweb_url')->where(array('ywine_eval_id'=>$res[$key]['ywine_res'][$k]['eval'][$key_e]['id'],'is_del'=>'-1'))->find();
                    $res[$key]['ywine_res'][$k]['eval'][$key_e]['refweb_id'] = $join_ywine_eval_refweb_res['refweb_id'];
                    $res[$key]['ywine_res'][$k]['eval'][$key_e]['refweb_url'] = $join_ywine_eval_refweb_res['refweb_url'];
                }
                $res[$key]['ywine_res'][$k]['honor'] = D()->table('jiuku_join_ywine_honor A, jiuku_honor B, jiuku_honorgroup C')->field('B.id, B.fname, B.cname, C.id AS honorgroup_id, C.fname AS honorgroup_fname, C.cname AS honorgroup_cname')->where('A.ywine_id = '.$v['id'].' AND A.honor_id = B.id AND B.honorgroup_id = C.id AND  A.is_del = \'-1\' AND B.is_del = \'-1\' AND C.is_del = \'-1\' AND B.status = \'1\' AND C.status = \'1\'')->select();
            }
        }
        $this->assign('res', $res);
        $this->display();
    }

    /**
     * 删除
     */
    public function del() {
        $backpage = Input::getVar($_REQUEST['backpage']);//return_page_parameter
        //获取id
        $id = Input::getVar($_REQUEST['id']);
        //获取批量删除
        $ids = $_REQUEST['ids'];
        $model = D('Wine');
        if($id) {
            $map = array('id' => $id);
            $caname_map = array('wine_id'=>$id);
        } elseif($ids) {
            $map = array('id' => array('in', $ids));
            $caname_map = array('wine_id' => array('in', $ids));
        }
        if($map && $caname_map) {
            $data = array(
                          'is_del' => '1',
                          'last_edit_time' => time(),
                          'last_edit_aid' => $_SESSION['admin_uid'],
                          );
            $model->where($map)->save($data);
            D('WineCaname')->where($caname_map)->save(array('is_del'=>'1'));
            $this->_jumpGo('删除成功','succeed', base64_decode($backpage));
        }
        $this->_jumpGo('删除失败，参数为空', 'error', base64_decode($backpage));
    }

    /**
     * 开启/关闭转变
     */
    public function chgStatus() {
        $backpage = Input::getVar($_REQUEST['backpage']);//return_page_parameter
        $id = Input::getVar($_GET['id']);
        $status = Input::getvar($_GET['status']);
        $data = array(
                      'id' => $id,
                      'status' => $status,
                      );
        $this->_update(D('Wine'),$data);
        D('WineCaname')->where(array('wine_id'=>$id))->save(array('status'=>$status));
        $this->_jumpGo('ID为'.$id.'的酒款状态更改成功', 'succeed', base64_decode($backpage));
    }

    /**
     * 验证状态转变
     */
    public function chgIsVerify() {
        $backpage = Input::getVar($_REQUEST['backpage']);//return_page_parameter
        $id = Input::getVar($_GET['id']);
        $is_verify = Input::getvar($_GET['is_verify']);
        $data = array(
                      'id' => $id,
                      'is_verify' => $is_verify,
                      );
        $this->_update(D('Wine'),$data);
        $this->_jumpGo('ID为'.$id.'的酒款验证状态更改成功', 'succeed', base64_decode($backpage));
    }

    /**
     * 上传文件
     */
    public function upload(){
        $type = isset($_GET['type']) ? Input::getVar($_GET['type']).'/' : '';
        $filesnamearr = array_keys($_FILES);
        $filesname = $filesnamearr[0];
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        $cfg = array(
            'ext' => C('UPLOAD_ALLOW_EXT'),
            'size' => C('UPLOAD_MAXSIZE'),
            'path' => C('UPLOAD_PATH').$type,
        );
        if($filesname == 'winelabelFile' || $filesname == 'wineimgFile')
            $cfg['ext'] = 'jpeg,jpg,png';
        $upload->config($cfg);
        $rest = $upload->uploadFile($filesname);
        if($rest['errno']) {
            $result = array(
                'error' => 1,
                'message' => $upload->error(),
            );
            $this->_ajaxDisplay($result);
        }
        $result = array(
            'error' => 0,
            'url' => C('DOMAIN.UPLOAD') . 'Jiuku/' . $type . $rest['path'],
            'filename' => $rest['path'],
        );
        //缩略图
        import('@.ORG.Util.Image');
        $image = new Image();
        $file = C('UPLOAD_PATH') . $type . $rest['path'];
        $file_dirname = C('UPLOAD_PATH') . $type;
        $file_basename = $rest['path'];
        if($filesname == 'winelabelFile'){
            $image->thumb1($file,$file_dirname.'50_50/'.$file_basename,50,50);
            $image->thumb1($file,$file_dirname.'100_100/'.$file_basename,100,100);
            $image->thumb1($file,$file_dirname.'150_150/'.$file_basename,150,150);
            $image->thumb1($file,$file_dirname.'200_200/'.$file_basename,200,200);
            $image->thumb1($file,$file_dirname.'250_250/'.$file_basename,250,250);
            $image->thumb1($file,$file_dirname.'300_300/'.$file_basename,300,300);
            $image->thumb1($file,$file_dirname.'350_350/'.$file_basename,350,350);
            $image->thumb1($file,$file_dirname.'400_400/'.$file_basename,400,400);
            $image->thumb1($file,$file_dirname.'450_450/'.$file_basename,450,450);
            $image->thumb1($file,$file_dirname.'500_500/'.$file_basename,500,500);
            $image->thumb1($file,$file_dirname.'550_550/'.$file_basename,550,550);
            $image->thumb1($file,$file_dirname.'600_600/'.$file_basename,600,600);
        }
        if($filesname == 'wineimgFile'){
            $image->thumb2($file,$file.'.160.160',160,160,0,60);
            $image->thumb3($file,$file.'.90.120',90,120);
            $image->thumb3($file,$file.'.180.240',180,240);
            $image->thumb3($file,$file.'.600.600',600,600);
        }
        $this->_ajaxDisplay($result);
    }
    function saveWineRedis($id){
        if($Redis = A('Redis')->linkRedis()){
            if($_POST['oldfname']){
                $oldfname_arr = A('Redis')->_cfRedisStr($_POST['oldfname'],1);
                foreach($oldfname_arr as $key=>$val){
                    $Redis->sRem('jiuku_winefname_wine:'.strtolower($val),$id);
                }
            }
            if($_POST['oldcname']){
                $oldcname_arr = A('Redis')->_cfRedisStr($_POST['oldcname'],2);
                foreach($oldcname_arr as $key=>$val){
                    $Redis->sRem('jiuku_winecname_wine:'.strtolower($val),$id);
                }
            }
            if($_POST['fname']){
                $fname_arr = A('Redis')->_cfRedisStr($_POST['fname'],1);
                foreach($fname_arr as $key=>$val){
                    $Redis->sAdd('jiuku_winefname_wine:'.strtolower($val),$id);
                }
            }
            if($_POST['cname']){
                $cname_arr = A('Redis')->_cfRedisStr($_POST['cname'],2);
                foreach($cname_arr as $key=>$val){
                    $Redis->sAdd('jiuku_winecname_wine:'.strtolower($val),$id);
                }
            }
            $Redis->bgsave();
        }
    }
}
