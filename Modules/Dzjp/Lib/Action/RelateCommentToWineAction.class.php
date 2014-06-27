<?php
/**
 * 酒评与酒款关联处理
 */
class RelateCommentToWineAction extends CommonAction {
	public function index(){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
		$this->display();
	}

	public function doRelate(){
        $wine_id = $_POST['wid'];
        $exp_id = $_POST['expid'];
        $do = $_POST['relate'];
        $notice = 'SUCCESS!';
        if (!isset($wine_id) || !is_numeric($wine_id)) {
            if (!isset($exp_id) || !is_numeric($exp_id)) {
                $notice =  'unexpected error';
                return false;
            }
        }
        $m = M();
        switch ($do) {
            case 'related':
            $data = array();
                $data['wine_id'] = $wine_id;
                $action = $m->table('sns_9p_wine_experience')->where('id='.$exp_id)->save($data);
                if ($action) {
                    $ct = $m->table('dzjp_relate_comment_to_wine')->where('exp_id='.$exp_id)->count();
                    if ($ct) {
                        $subData = array('result' => 'related');
                        $subAction = $m->table('dzjp_relate_comment_to_wine')->where('exp_id='.$exp_id)->save($subData);
                    } else {
                        $subData = array();
                        $subData['result'] = 'related';
                        $subData['exp_id'] = $exp_id;
                        $subData['operator'] = $_SESSION['admin_uid'];
                        $subData['operate_time'] = time();
                        $subAction = $m->table('dzjp_relate_comment_to_wine')->add($subData);
                        if ($subAction == false) {
                            $notice =  'Unexpected error with code #2048';
                        }
                    }
                } else {
                    $notice =  'Unexpected error with code #1024';
                }
                break;
            
            case 'ignored':
                $ct = $m->table('dzjp_relate_comment_to_wine')->where('exp_id='.$exp_id)->count();
                if ($ct) {
                    $subData = array('result' => 'ignored');
                    $subAction = $m->table('dzjp_relate_comment_to_wine')->where('exp_id='.$exp_id)->save($subData);
                } else {
                    $subData = array();
                    $subData['result'] = 'ignored';
                    $subData['exp_id'] = $exp_id;
                    $subData['operator'] = $_SESSION['admin_uid'];
                    $subData['operate_time'] = time();
                    $subAction = $m->table('dzjp_relate_comment_to_wine')->add($subData);
                    if ($subAction == false) {
                        $notice =  'Unexpected error with code #2048#2';
                    }
                }
                break;
        }
        echo $notice;
	}

    public function getComments($sort){
        $pData = $sort;
        if (is_null($pData)) {
            $pData = 1;
        }
        $m = M();
        import('ORG.Util.Page');// 导入分页类
        $joinTb = 'dzjp_relate_comment_to_wine';
        $field  = 'A.id,A.uid,A.tid,A.image_id,A.image_id1,A.image_id2,A.image_id3,A.image_id4,';
        $field .= 'A.wine_id,A.wine_name,A.wine_e_name,A.wine_year,A.create_time,B.result,B.exp_id';
        switch ($pData) {
            case '1':   // 未关联
                $count = $m->table('sns_9p_wine_experience A')
                            ->join($joinTb . ' B ON A.id = B.exp_id')
                            ->where('A.wine_id = 0')
                            ->count();
                $Page =  new Page($count, 20);
                $show = $Page->show();
                $res = $m->table('sns_9p_wine_experience A')
                            ->join($joinTb . ' B ON A.id = B.exp_id')
                            ->field($field)
                            ->where('A.wine_id = 0 AND A.show < 5 AND isnull(B.exp_id)')
                            ->order('A.create_time DESC')
                            ->limit($Page->firstRow.','.$Page->listRows)
                            ->select();
                break;
            case '2':   // 用户已关联
                $count = $m->table('sns_9p_wine_experience A')
                            ->where('A.wine_id > 0')
                            ->count();
                $Page = new Page($count, 20);
                $show = $Page->show();
                $field  = 'A.id,A.uid,A.tid,A.image_id,A.image_id1,A.image_id2,A.image_id3,A.image_id4,';
                $field .= 'A.wine_id,A.wine_name,A.wine_e_name,A.wine_year,A.create_time';
                $res = $m->table('sns_9p_wine_experience A')
                            ->field($field)
                            ->where('A.wine_id > 0 AND A.show < 5')
                            ->order('A.create_time DESC')
                            ->limit($Page->firstRow.','.$Page->listRows)
                            ->select();
                break;
            case '3':   // 已处理
                $count = $m->table('sns_9p_wine_experience A')
                            ->join('INNER JOIN ' . $joinTb . ' B ON A.id = B.exp_id')
                            ->where('A.wine_id > 0')
                            ->count();
                $Page = new Page($count, 20);
                $show = $Page->show();
                $res = $m->table('sns_9p_wine_experience A')
                            ->join('INNER JOIN ' . $joinTb . ' B ON A.id = B.exp_id')
                            ->field($field)
                            ->where('A.wine_id > 0 AND A.show < 5 AND B.result = "related"')
                            ->order('A.create_time DESC')
                            ->limit($Page->firstRow.','.$Page->listRows)
                            ->select();
                break;
            case '4':   // 已忽略
                $count = $m->table('sns_9p_wine_experience A')
                            ->join('INNER JOIN ' . $joinTb . ' B ON A.id = B.exp_id')
                            ->where('A.wine_id = 0 AND B.result = "ignored"')
                            ->count();
                $Page = new Page($count, 20);
                $show = $Page->show();
                $res = $m->table('sns_9p_wine_experience A')
                            ->join('INNER JOIN ' . $joinTb . ' B ON A.id = B.exp_id')
                            ->field($field)
                            ->where('A.wine_id = 0 AND A.show < 5 AND B.result = "ignored"')
                            ->order('A.create_time DESC')
                            ->limit($Page->firstRow.','.$Page->listRows)
                            ->select();
                break;
        }
        // echo $m->getlastsql();
        foreach ($res as $key => $value) {
            $res[$key]['jp_content'] = $this->getCommentContent($value['tid']);
            $sImg = json_decode($this->getCommentImage($value['image_id']));
            $res[$key]['jp_image'] = $sImg[0];
            $res[$key]['jp_creater'] = $this->getUserName($value['uid']);
            // $alias = $this->getRelatedWineName($value['wine_id']);
            // $cname = $alias['cname'];
            // $fname = $alias['fname'];
            $mulity_image_id = $value['image_id'] . ',' .$value['image_id1'] . ',' .$value['image_id2'] . ',' .$value['image_id3'] . ',' .$value['image_id4'];
            $res[$key]['jp_all_image'] = $this->getCommentImage($mulity_image_id);
            // if ($value['wine_id'] == 0) {
            //     $cname = '未关联';
            // }
            // $res[$key]['jp_fname'] = $fname;
            // $res[$key]['jp_cname'] = $cname;
            $res[$key]['jp_create_time'] = date('c',$value['create_time']);
        }
        // dump($res);
        $this->assign('list',$res);
        $this->assign('page',$show);
    }

    public function listTable() {
        Load('extend'); 
        $sort = $_GET['status'];
        $this->getComments($sort);
        $this->display();
    }

    public function getCommentContent($tid) {
        $m = M();
        $content = $m->table('sns_topic')->field('content')->where('tid='.$tid)->find();
        return $content['content'];
    }

    public function getCommentImage($mulity_image_id) {
        $m = M();
        $where = array('id'=>array('IN',$mulity_image_id));
        $images = $m->table('sns_9p_wine_image')->field('image_filename,file_ext')->where($where)->select();
        $clearData = array();
        foreach ($images as $key => $value) {
            if (!empty($value['image_filename'])) {
                $clearData[] = $value;
            }
        }
        if (count($clearData) == 0) {
            return false;
        }
        $output = array();
        foreach ($clearData as $k => $v) {
            $r = $v['image_filename'];
            $p1 = $r[1].$r[2].$r[3];
            $p2 = $r[4].$r[5].$r[6];  
            $p3 = $r[7].$r[8].$r[9];
            $p = $p1 . '/' . $p2 . '/' . $p3 . '/' . $r;  
            $p = $p.'_w100.'.$v['file_ext']; 
            $output[] = $p;
        }
        return json_encode($output);
    }

    public function getUserName($uid) {
        $m = M();
        $user = $m->table('sns_user')->field('nickname')->where('uid='.$uid)->find();
        return $user['nickname'];
    }
    /**
     * get related wine's name
     * @param  int $wine_id     wine_caname_id's id
     * @return string          name
     */
    public function getRelatedWineName($wine_id) {
        $m = M();
        if ($wine_id < 0){
            return false;
        }
        $wine = $m->table('jiuku_wine_caname')->field('cname,fname')->where('status=1 AND is_del="-1" AND id='.$wine_id)->find();
        return $wine;
    }

    public function getOriginalName($wine_id) {
        if (is_null($wine_id)) {
            return false;
        }
        $m = M();
        $orig = $m->table('jiuku_wine')->field('cname,fname')->where('status=1 AND is_del="-1" AND id='.$wine_id)->find();
        return $orig;
    }

    public function getWineImage($wine_id=false){
        $id = $_POST['wid'];
        if (isset($id) && is_numeric($id)) {
            $wine_id = $id;
        } elseif (isset($wine_id) && is_numeric($winie_id)) {
            $wine_id = $wine_id;
        } else {
            return false;
        }
        $m = M();
        $image = $m->table('jiuku_wine_label')->field('filename')->where('is_del="-1" AND wine_id='.$wine_id)->find();
        echo json_encode($image);
        return $image['filename'];
    }
    public function getFnameList(){
        $m = M();
        $request = $_POST['request'];
        if (!isset($request) || $request == false) {
            return false;
        }
        $where = array('status'=>1,'is_del'=>'-1');
        $where['fname'] = array('like','%'.$request.'%');
        $info = $m->table('jiuku_wine')->field('id,cname,fname')->where($where)->limit(10)->select();
        // echo $m->getlastsql();
        echo json_encode($info);
        return $info;
    }

    public function getCnameList(){
        $m = M();
        $method = $_POST['method'];
        $request = $_POST['request'];
        if (!isset($method) || $method == false) {
            return false;
        }
        if (!isset($request) || $request == false) {
            return false;
        }
        $where = array('status'=>1,'is_del'=>'-1');
        switch ($method) {
            case 's-by-ename':
                $where['fname'] = $request;
                break;
            
            case 's-by-id':
                $where['id'] = $request;
                break;
        }
        $info = $m->table('jiuku_wine_caname')->field('id,cname,fname')->where($where)->select();
        // echo $m->getlastsql();
        echo json_encode($info);
        return $info;
    }

    public function getStdInfo() {
        $wine_id = $_POST['wid'];
        if (!isset($wine_id) || !is_numeric($wine_id)) {
            return false;
        }
        $m = M();
        $winery = $m->table('jiuku_join_wine_winery A ')
                    ->join('jiuku_winery B ON A.winery_id = B.id')
                    ->field('B.fname,B.cname')
                    ->where('A.is_del = "-1" AND B.is_del = "-1" AND B.status = 1 AND A.wine_id='.$wine_id)
                    ->find();

        $region = $m->table('jiuku_join_wine_region A')
                    ->join('jiuku_region B ON A.region_id = B.id')
                    ->field('B.fname,B.cname')
                    ->where('A.is_del="-1" AND A.wine_id='.$wine_id.' AND B.status=1 AND B.is_del="-1"')
                    ->find();
        $image = $m->table('jiuku_wine_label')->field('filename')->where('is_del="-1" AND wine_id='.$wine_id)->find();
        $result = array($winery,$region,$image);
        echo json_encode($result);
        return $result;
    }
}
?>
