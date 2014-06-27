<?php

/**
 *
 */
class OAC_getAjaxDataAction extends Action {
    /**
     * 初始化
     */
    public function _initialize() {
        import('@.ORG.Util.Input');
        import('@.ORG.Util.Image');
    }
    function searchWine(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $kw = strtolower(Input::getVar($_POST["kw"]));
        if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
        $map['merge_id'] = 0;
        $map['is_del'] = '-1';
        $result = array();
        if(preg_match("/^(-|\+)?\d+$/",$kw)){
            $res = D('Wine')->field('id,fname,cname')->where(array_merge($map,array('id'=>$kw)))->find();
            array_push($result,$res);
        }else{
            $eq_res = D('Wine')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->select();
            foreach($eq_res as $val){
                array_push($result, array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
                $exist_idarr[] = $val['id'];
            }
            $like_res = D('Wine')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>array('like','%'.$kw.'%'),'cname'=>array('like','%'.$kw.'%'),'_logic'=>'or'))))->limit(10+count($eq_res))->select();
            foreach($like_res as $val){
                if(!in_array($val['id'], $exist_idarr))
                    array_push($result, array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
            }
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function searchCountry(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $kw = strtolower(Input::getVar($_POST["kw"]));
        if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
        $map['is_del'] = '-1';
        $result = array();
        $eq_res = D('Country')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();
        if($eq_res) array_push($result,array('id'=>$eq_res['id'],'fname'=>$eq_res['fname'],'cname'=>$eq_res['cname']));
        $res = D('Country')->field('id,fname,cname')->where($map)->select();
        foreach($res as $key=>$val){
            if($eq_res['id'] == $val['id']) continue;
            if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
                array_push($result,array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
            }
            if(count($result) > 10)    break;
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function searchRegion(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $kw = strtolower(Input::getVar($_POST["kw"]));
        if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
        $map['is_del'] = '-1';
        $result = array();
        $eq_res = D('Region')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();
        if($eq_res) array_push($result,array('id'=>$eq_res['id'],'fname'=>$eq_res['fname'],'cname'=>$eq_res['cname']));
        $res = D('Region')->field('id,fname,cname')->where($map)->select();
        foreach($res as $key=>$val){
            if($eq_res['id'] == $val['id']) continue;
            if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
                array_push($result,array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
            }
            if(count($result) > 10)    break;
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function searchLargeRegion(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $kw = strtolower(Input::getVar($_POST["kw"]));
        if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
        $map['pid'] = '0';
        $map['is_del'] = '-1';
        $result = array();
        $eq_res = D('Region')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();
        if($eq_res) array_push($result,array('id'=>$eq_res['id'],'fname'=>$eq_res['fname'],'cname'=>$eq_res['cname']));
        $res = D('Region')->field('id,fname,cname')->where($map)->select();
        foreach($res as $key=>$val){
            if($eq_res['id'] == $val['id']) continue;
            if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
                array_push($result,array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
            }
            if(count($result) > 10)    break;
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function searchWinery(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $kw = strtolower(Input::getVar($_POST["kw"]));
        if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
        $map['is_del'] = '-1';
        $result = array();
        $eq_res = D('Winery')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();
        if($eq_res) array_push($result,array('id'=>$eq_res['id'],'fname'=>$eq_res['fname'],'cname'=>$eq_res['cname']));
        $res = D('Winery')->field('id,fname,cname')->where($map)->select();
        foreach($res as $key=>$val){
            if($eq_res['id'] == $val['id']) continue;
            if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
                array_push($result,array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
            }
            if(count($result) > 10)    break;
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function searchGrape(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $kw = strtolower(Input::getVar($_POST["kw"]));
        if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
        $map['is_del'] = '-1';
        $result = array();
        $eq_res = D('Grape')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();
        if($eq_res) array_push($result,array('id'=>$eq_res['id'],'fname'=>$eq_res['fname'],'cname'=>$eq_res['cname']));
        $res = D('Grape')->field('id,fname,cname')->where($map)->select();
        foreach($res as $key=>$val){
            if($eq_res['id'] == $val['id']) continue;
            if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
                array_push($result,array('id'=>$val['id'],'fname'=>$val['fname'],'cname'=>$val['cname']));
            }
            if(count($result) > 10)    break;
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function searchHonor(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        $count = 10;
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map['status'] = '1';
        }
        $kw = Input::getVar($_POST["kw"]);
        $map_eq_k['fname'] = $kw;
        $map_eq_k['cname'] = $kw;
        $map_eq_k['_logic'] = 'or';
        $map_eq['_complex'] = $map_eq_k;
        if($map['status']){
            $map_eq['status'] = '1';
        }
        $map_eq['is_del'] = '-1';
        $result = array();
        $eq_res =D('Honor')->where($map_eq)->limit($count)->select();
        foreach($eq_res as $val){
            array_push($result, $val);
        }
        if(($count = $count - count($result)) >0){
            $map_like_k['fname'] = array('like', '%'.$kw.'%');
            $map_like_k['cname'] = array('like', '%'.$kw.'%');
            $map_like_k['_logic'] = 'or';
            $map_like['_complex'] = $map_like_k;
            if($map['status']){
                $map_like['status'] = '1';
            }
            $map_like['is_del'] = '-1';
            $result = array();
            $like_res =D('Honor')->where($map_like)->limit($count)->select();
            foreach($like_res as $val){
                array_push($result, $val);
            }
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function searchHonor2(){
        header("Content-Type:text/html; charset=utf-8");
        if(empty($_POST['kw'])){
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
            exit();
        }
        if(empty($_POST['type']) || $_POST['type'] != 1){
            $map[] = 'A.status=\'1\' AND B.status=\'1\'';
        }
        $kw = strtolower(Input::getVar($_POST["kw"]));
        if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
        $map[] = 'A.is_del=\'-1\' AND B.is_del=\'-1\'';
        $result = array();
        $eq_res = D()->table('jiuku_honor A,jiuku_honorgroup B')->where('A.honorgroup_id = B.id AND '.implode(' AND ',$map).'AND (A.fname = \''.$kw.'\' OR A.Cname = \''.$kw.'\' OR B.fname = \''.$kw.'\' OR B.cname = \''.$kw.'\')')->field('A.id,A.fname,A.cname,B.fname AS groupfname,B.cname AS groupcname')->find();
        if($eq_res) array_push($result,$eq_res);
        $res = D()->table('jiuku_honor A,jiuku_honorgroup B')->where('A.honorgroup_id = B.id AND '.implode(' AND ',$map))->field('A.id,A.fname,A.cname,B.fname AS groupfname,B.cname AS groupcname')->select();
        foreach($res as $key=>$val){
            if($eq_res['id'] == $val['id']) continue;
            if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false || strpos(strtolower($val['groupfname']),$kw) !== false || strpos(strtolower($val['groupcname']),$kw) !== false){
                array_push($result,$val);
            }
            if(count($result) > 10)    break;
        }
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
        exit();
    }
    function getHonorAutocompleteJsData(){
        header("Content-Type:text/html; charset=utf-8");
        if (empty($_GET['term'])) exit ;
        $term = strtolower(Input::getVar($_GET["term"]));
        if (get_magic_quotes_gpc()) $term = stripslashes($term);
        $map['is_del'] = '-1';
        $res = D()->table('jiuku_honor A,jiuku_honorgroup B')->where('A.honorgroup_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,A.fname,A.cname,B.fname AS groupfname,B.cname AS groupcname')->select();
        $result = array();
        foreach($res as $key=>$val){
            $value = '['.$val['groupcname'].']'.$val['cname'];
            if($value == $term){
                array_push($result,array('id'=>$val['id'],'value'=>$value));
            }
        }
        foreach($res as $key=>$val){
            $value = '['.$val['groupcname'].']'.$val['cname'];
            if(strpos(strtolower($value),$term) !== false){
                array_push($result,array('id'=>$val['id'],'value'=>$value));
            }
            if(count($result) > 10)    break;
        }
        echo json_encode($result);
    }
    function getAutocompleteDataJs(){
        header("Content-Type:text/html; charset=utf-8");
        if (empty($_GET['term']) || empty($_GET['dbtable'])) exit ;
        $dbtable = Input::getVar($_GET['dbtable']);
        $dbmap = json_decode($_GET['dbmap'],true);
        $q = strtolower(Input::getVar($_GET["term"]));
        if (get_magic_quotes_gpc()) $q = stripslashes($q);
        $dbmap['is_del'] = '-1';
        if($dbtable == 'Honor'){
            $res = D()->table('jiuku_honor A,jiuku_honorgroup B')->where('A.honorgroup_id = B.id AND A.is_del = \'-1\' AND B.is_del = \'-1\'')->field('A.id,concat(\'[\',B.cname,\']\',A.cname) as value')->select();
        }else{
            $res = D($dbtable)->where($dbmap)->field('`id`,concat(`fname`,\' ╱ \',`cname`) as value')->select();
        }
        $result = array();
        foreach ($res as $key=>$val) {
            if (strtolower($val['value']) == $q) {
                array_push($result, $val);
            }
        }
        foreach ($res as $key=>$val) {
            if (strpos(strtolower($val['value']), $q) !== false) {
                array_push($result, $val);
            }
            if (count($result) > 11)
                break;
        }
        echo json_encode($result);
    }

    function getWinecnameForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        $sql = 'SELECT A.`id`,A.`cname`,B.`fname` FROM `jiuku_wine_caname` A INNER JOIN `jiuku_wine` B ON B.`id` = A.`wine_id` AND B.`status` = \'1\' AND B.`merge_id` = 0 AND B.`is_del` = \'-1\' WHERE A.`status` = \'1\' AND A.`is_merge` = \'-1\' AND A.`is_del` = \'-1\' ';
        if($kw == ''){
            $list = D()->query($sql . 'ORDER BY A.id ASC LIMIT '.$count);
            $result = $list ? $list : array();
        }else{
            $exist_ids = array();
            $list_eq = D()->query($sql . 'AND (A.`cname` = \''.$kw.'\' OR B.`fname` = \''.$kw.'\') ORDER BY A.`id` LIMIT '.$count);
            foreach($list_eq as $key=>$val){
                $result[] = $val;
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
                    $result[] = $val;
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
                    $result[] = $val;
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
}
