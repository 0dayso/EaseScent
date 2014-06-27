<?php
// 酒款中文名精准词同义词管理
class SearchWordAction extends Common2Action {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $_POST = $this->sanitize($_POST);
        $_GET = $this->sanitize($_GET);
    }
    public function index(){
        $this->display();
    }

    public function jzcindex(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $map = array();
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map['name'] = array('like', '%'.$_GET['keyword'].'%');
            }else{
                unset($_GET['keyword']);
            }
        }
        $map['relation_type'] = array('in', array(1,2));
        if(isset($_GET['relation_type'])){
            if(in_array($_GET['relation_type'], array(1,2))){
                $map['relation_type'] = $_GET['relation_type'];
            }else{
                unset($_GET['relation_type']);
            }
        }
        $map['status'] = array('in', array(2,3));
        if(isset($_GET['status'])){
            if(in_array($_GET['status'], array(2,3))){
                $map['status'] = $_GET['status'];
            }else{
                unset($_GET['status']);
            }
        }
        $count = D()->table('jiuku_accurate_word')->where($map)->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('jiuku_accurate_word')->where($map)->order('id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            if($val['relation_type'] == 1){
                $relation_res = D()->query('SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_merge` = \'-1\' AND A.`is_del` = \'-1\' AND A.`id` = '.$val['relation_id']);
                $list[$key]['relation_res'] = array(
                    'id' => $relation_res[0]['id'],
                    'name' => $relation_res[0]['cname'] . ' ['.$relation_res[0]['fname'].']',
                );
            }
        }
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function jzcadd(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('jzcindex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“精准词名”为必填项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jiuku_accurate_word')->where(array('name'=>$_POST['name']))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“精准词名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            if($_POST['relation_type'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“精准词所属”为必选项。','_backurl'=>$_backurl)));
            }
             if($_POST['relation_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“精准词所关联”为必需项。','_backurl'=>$_backurl)));
            }
            //insert
            $maxid = D()->table('jiuku_accurate_word')->max('id');
            $insert_data = array(
                'id' => $maxid + 1,
                'name' => $_POST['name'],
                'relation_type' => $_POST['relation_type'],
                'relation_id' => $_POST['relation_id'],
                'status' => $_POST['status'],
            );
            if(!$is = D()->table('jiuku_accurate_word')->add($insert_data)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        $this->display();
    }
    public function jzcedit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('jzcindex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(!isset($_POST['id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if($_POST['name'] == ''){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“精准词名”为必填项。','_backurl'=>$_backurl)));
            }
            if($is_exist = D()->table('jiuku_accurate_word')->where(array('id'=>array('neq',$_POST['id']),'name'=>$_POST['name']))->find()){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“精准词名”重复录入，重复ID：' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            if($_POST['relation_type'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“精准词所属”为必选项。','_backurl'=>$_backurl)));
            }
             if($_POST['relation_id'] == 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“精准词所关联”为必需项。','_backurl'=>$_backurl)));
            }
            //update
            $update_data = array(
                'id' => $maxid + 1,
                'name' => $_POST['name'],
                'relation_type' => $_POST['relation_type'],
                'relation_id' => $_POST['relation_id'],
                'status' => $_POST['status'],
            );
            if(false === $is = D()->table('jiuku_accurate_word')->save($update_data)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。' . $is_exist['id'] . '。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('jiuku_accurate_word')->where(array('id'=>$_GET['id']))->find()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'error', $_backurl);
        }
        switch($res['relation_type']){
            case 1:
                $relation_res = D()->query('SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_merge` = \'-1\' AND A.`is_del` = \'-1\' AND A.`id` = '.$res['relation_id']);
                $res['relation_res'] = array(
                    'id' => $relation_res[0]['id'],
                    'name' => $relation_res[0]['cname'] . ' ['.$relation_res[0]['fname'].']',
                );
            break;
            case 2:
                $res['relation_res'] = null;
            break;
            default:
                $res['relation_res'] = null;
        }
        $this->assign('res', $res);
        $this->display();
    }
    public function jzcchgstatus(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('jzcindex');
        if(!isset($_GET['id']) || !in_array($_GET['status'],array(2,3))){
            $this->_jumpGo('修改失败！参数异常。正在返回至列表页...', 'error', $_backurl);
        }
        if(false === $is = D()->table('jiuku_accurate_word')->save(array('id'=>$_GET['id'],'status'=>$_GET['status']))){
            $this->_jumpGo('修改失败！数据库异常。在返回至列表页...', 'error', $_backurl);
        }
        $this->_jumpGo('修改成功！', 'succeed', $_backurl);
    }
    public function tycindex(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $map = array();
        if(isset($_GET['keyword'])){
            if($_GET['keyword'] != ''){
                $map['name'] = array('like', '%'.$_GET['keyword'].'%');
            }else{
                unset($_GET['keyword']);
            }
        }
        $count = D()->table('jiuku_synonym_word')->where($map)->group('group_id')->count();
        import ("@.ORG.Util.Page");
        $p = new Page($count, 15);
        $list = D()->table('jiuku_synonym_word')->field('group_id')->where($map)->group('group_id')->order('group_id desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['names'] = D()->table('jiuku_synonym_word')->where(array('group_id'=>$val['group_id']))->getfield('name',true);
        }
        $this->assign('list', $list);
        $this->assign("page", $p->show());
        $this->assign("_listurl", base64_encode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $this->display();
    }
    public function tycadd(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : Url('tycindex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(isset($_POST['_ini'])){
                if($is_exist = D()->table('jiuku_synonym_word')->where(array('name'=>$_POST['name']))->find()){
                    exit(json_encode(array('error'=>1,'msg'=>'增加失败！“同义词：'.$is_exist['name'].'”在其他“同义词组”已被添加。<a href="'.Url('tycindex').'&keyword='.$is_exist['name'].'" target="_blank" >去看看</a>')));
                }
                exit(json_encode(array('error'=>0)));
            }
            if(count($_POST['names']) <= 0){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！“同义词组”为必须项。','_backurl'=>$_backurl)));
            }
            foreach($_POST['names'] as $val){
                if($val == ''){
                    exit(json_encode(array('error'=>1,'msg'=>'添加失败！“同义词”不能为空。','_backurl'=>$_backurl)));
                }
                if($is_exist = D()->table('jiuku_synonym_word')->where(array('name'=>$val))->find()){
                    exit(json_encode(array('error'=>1,'msg'=>'增加失败！“同义词：'.$is_exist['name'].'”在其他“同义词组”已被添加。<a href="'.Url('tycindex').'&keyword='.$is_exist['name'].'" target="_blank" >去看看</a>','_backurl'=>$_backurl)));
                }
            }
            //insert
            $max_group_id = D()->table('jiuku_synonym_word')->max('group_id');
            foreach($_POST['names'] as $val){
                $insert_data[] = array(
                    'name' => $val,
                    'group_id' => $max_group_id + 1,
                );
            }
            if(!$is = D()->table('jiuku_synonym_word')->addall($insert_data)){
                exit(json_encode(array('error'=>1,'msg'=>'添加失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'添加成功！','_backurl'=>$_backurl)));
        }
        $this->display();
    }
    public function tycedit(){
        $_backurl = $_REQUEST['_backurl'] ? base64_decode($_REQUEST['_backurl']) : U('tycindex');
        //提交
        if($this->isPost()){
            //POST信息判断
            if(isset($_POST['_ini'])){
                if($is_exist = D()->table('jiuku_synonym_word')->where(array('name'=>$_POST['name'],'group_id'=>array('neq',$_POST['group_id'])))->find()){
                    exit(json_encode(array('error'=>1,'msg'=>'增加失败！“同义词：'.$is_exist['name'].'”在其他“同义词组”已被添加。<a href="'.Url('tycindex').'&keyword='.$is_exist['name'].'" target="_blank" >去看看</a>')));
                }
                exit(json_encode(array('error'=>0)));
            }
            if(!isset($_POST['group_id'])){
                exit(json_encode(array('error'=>1,'msg'=>'参数异常！请返回至列表页。','_backurl'=>$_backurl)));
            }
            if(count($_POST['names']) <= 0){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！“同义词组”为必须项。','_backurl'=>$_backurl)));
            }
            foreach($_POST['names'] as $val){
                if($val == ''){
                    exit(json_encode(array('error'=>1,'msg'=>'修改失败！“同义词”不能为空。','_backurl'=>$_backurl)));
                }
                if($is_exist = D()->table('jiuku_synonym_word')->where(array('group_id'=>array('neq',$_POST['group_id']),'name'=>$val))->find()){
                    exit(json_encode(array('error'=>1,'msg'=>'修改失败！“同义词：'.$is_exist['name'].'”在其他“同义词组”已被添加。<a href="'.Url('tycindex').'&keyword='.$is_exist['name'].'" target="_blank" >去看看</a>','_backurl'=>$_backurl)));
                }
            }
            //update
            foreach($_POST['names'] as $val){
                $insert_data[] = array(
                    'name' => $val,
                    'group_id' => $_POST['group_id'],
                );
            }
            D()->table('jiuku_synonym_word')->where(array('group_id'=>$_POST['group_id']))->delete();
            if(!$is = D()->table('jiuku_synonym_word')->addall($insert_data)){
                exit(json_encode(array('error'=>1,'msg'=>'修改失败！数据库异常。','_backurl'=>$_backurl)));
            }
            exit(json_encode(array('error'=>0,'msg'=>'修改成功！','_backurl'=>$_backurl)));
        }
        //页面
        if(!$res = D()->table('jiuku_synonym_word')->where(array('group_id'=>$_GET['group_id']))->select()){
            $this->_jumpGo('参数异常或数据不存在！正在返回至列表页...', 'error', $_backurl);
        }
        $this->assign('res', $res);
        $this->display();
    }
    function getRealtionForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($_POST['type'] == 1){
            $sql = 'SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_merge` = \'-1\' AND A.`is_del` = \'-1\' ';
            if(preg_match("/^(-|\+)?\d+$/",$kw)){
                if($list = D()->query($sql . 'AND A.`id` = '.$kw)){
                    $result[] = array(
                        'id' => $list[0]['id'],    
                        'name' => $list[0]['cname'] . ' ['.$list[0]['fname'].']',
                    );
                }
            }elseif($kw == ''){
                $list = D()->query($sql . 'ORDER BY A.id ASC LIMIT '.$count);
                foreach($list as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'name' => $val['cname'] . ' ['.$val['fname'].']',
                    );
                }
            }else{
                $exist_ids = array();
                $list_eq = D()->query($sql . 'AND (A.`cname` = \''.$kw.'\' OR B.`fname` = \''.$kw.'\') ORDER BY A.`id` LIMIT '.$count);
                foreach($list_eq as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'name' => $val['cname'] . ' ['.$val['fname'].']',
                    );
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
                if($count > 0){
                    $leftlike_sql = 'AND (A.`cname` like \''.$kw.'%\' OR B.`fname` like \''.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $leftlike_sql .= 'AND A.`id` NOT IN ('.implode(',',$exist_ids).') ';
                    }
                    $leftlike_sql .= 'ORDER BY A.`id` LIMIT '.$count;
                    $list_leftlike = D()->query($sql . $leftlike_sql);
                    foreach($list_leftlike as $key=>$val){
                        $result[] = array(
                            'id' => $val['id'],
                            'name' => $val['cname'] . ' ['.$val['fname'].']',
                        );
                        $exist_ids[] = $val['id'];
                    }
                    $count = $count - count($result);
                }
                if($count > 0){
                    $like_sql = 'AND (A.`cname` like \'%'.$kw.'%\' OR B.`fname` like \'%'.$kw.'%\') ';
                    if(count($exist_ids) > 0){
                        $like_sql .= 'AND A.`id` NOT IN ('.implode(',',$exist_ids).') ';
                    }
                    $like_sql .= 'ORDER BY A.`id` LIMIT '.$count;
                    $list_like = D()->query($sql . $like_sql);
                    foreach($list_like as $key=>$val){
                        $result[] = array(
                            'id' => $val['id'],
                            'name' => $val['cname'] . ' ['.$val['fname'].']',
                        );
                    }
                    $count = $count - count($result);
                }
            }
        }elseif($_POST['type'] == 2){
            sleep(1);
        }else{
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
}
