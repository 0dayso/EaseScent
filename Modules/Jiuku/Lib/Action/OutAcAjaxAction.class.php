<?php

/**
 * 权限外ajax调用
 */
class OutAcAjaxAction extends Action {

    /**
     * 初始化
     */
    public function _initialize() {
    }
    
    function getBrandForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        $sql = 'SELECT `id`,CONCAT(`cname`,\' \',`fname`) AS `value` FROM `jiuku_brand` WHERE `status` = 3 ';
        if(preg_match("/^(-|\+)?\d+$/",$kw)){
            if($list = D()->query($sql . ' AND `id` = '.$kw)){
                $result[] = array(
                    'id' => $list[0]['id'],    
                    'value' => $list[0]['value'],
                );
            }
        }elseif($kw == ''){
            $list = D()->query($sql . ' ORDER BY id ASC LIMIT '.$count);
            foreach($list as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
            }
        }else{
            $exist_ids = array();
            $list_eq = D()->query($sql . ' AND (`cname` = \''.$kw.'\' OR `fname` = \''.$kw.'\') ORDER BY `id` LIMIT '.$count);
            foreach($list_eq as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $leftlike_sql = ' AND (`cname` like \''.$kw.'%\' OR `fname` like \''.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $leftlike_sql .= ' AND `id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $leftlike_sql .= ' ORDER BY `id` LIMIT '.$count;
                $list_leftlike = D()->query($sql . $leftlike_sql);
                foreach($list_leftlike as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $like_sql = ' AND (`cname` like \'%'.$kw.'%\' OR `fname` like \'%'.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $like_sql .= ' AND `id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $like_sql .= ' ORDER BY `id` LIMIT '.$count;
                $list_like = D()->query($sql . $like_sql);
                foreach($list_like as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }

    function getHWineForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        $sql = 'SELECT A.`id`,CONCAT(A.`cname`,\' \',B.`fname`) AS `value` 
                FROM `jiuku_wine_caname`A 
                INNER JOIN `jiuku_wine` B 
                ON A.`wine_id` = B.`id` AND B.`status` = \'1\' AND B.`is_del` = \'-1\' AND B.`merge_id` = 0 
                WHERE A.`status` = \'1\' AND A.`is_del` = \'-1\' AND A.`is_merge` = \'-1\'';
        if(preg_match("/^(-|\+)?\d+$/",$kw)){
            if($list = D()->query($sql . ' AND `id` = '.$kw)){
                $result[] = array(
                    'id' => $list[0]['id'],    
                    'value' => $list[0]['value'],
                );
            }
        }elseif($kw == ''){
            $list = D()->query($sql . ' ORDER BY A.`id` ASC LIMIT '.$count);
            foreach($list as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
            }
        }else{
            $exist_ids = array();
            $list_eq = D()->query($sql . ' AND (A.`cname` = \''.$kw.'\' OR B.`fname` = \''.$kw.'\') ORDER BY A.`id` LIMIT '.$count);
            foreach($list_eq as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $leftlike_sql = ' AND (A.`cname` like \''.$kw.'%\' OR B.`fname` like \''.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $leftlike_sql .= ' AND A.`id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $leftlike_sql .= ' ORDER BY A.`id` LIMIT '.$count;
                $list_leftlike = D()->query($sql . $leftlike_sql);
                foreach($list_leftlike as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $like_sql = ' AND (A.`cname` like \'%'.$kw.'%\' OR B.`fname` like \'%'.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $like_sql .= ' AND A.`id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $like_sql .= ' ORDER BY A.`id` LIMIT '.$count;
                $list_like = D()->query($sql . $like_sql);
                foreach($list_like as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }

    function getBWineForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        $sql = 'SELECT `id`,CONCAT(`cname`,\' \',`ename`) AS `value` FROM `bjk_wine` WHERE `status` = 3 ';
        if(preg_match("/^(-|\+)?\d+$/",$kw)){
            if($list = D()->query($sql . ' AND `id` = '.$kw)){
                $result[] = array(
                    'id' => $list[0]['id'],    
                    'value' => $list[0]['value'],
                );
            }
        }elseif($kw == ''){
            $list = D()->query($sql . ' ORDER BY id ASC LIMIT '.$count);
            foreach($list as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
            }
        }else{
            $exist_ids = array();
            $list_eq = D()->query($sql . ' AND (`cname` = \''.$kw.'\' OR `ename` = \''.$kw.'\') ORDER BY `id` LIMIT '.$count);
            foreach($list_eq as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $leftlike_sql = ' AND (`cname` like \''.$kw.'%\' OR `ename` like \''.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $leftlike_sql .= ' AND `id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $leftlike_sql .= ' ORDER BY `id` LIMIT '.$count;
                $list_leftlike = D()->query($sql . $leftlike_sql);
                foreach($list_leftlike as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $like_sql = ' AND (`cname` like \'%'.$kw.'%\' OR `ename` like \'%'.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $like_sql .= ' AND `id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $like_sql .= ' ORDER BY `id` LIMIT '.$count;
                $list_like = D()->query($sql . $like_sql);
                foreach($list_like as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
    
    function getLWineForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        $sql = 'SELECT `id`,CONCAT(`cname`,\' \',`ename`) AS `value` FROM `ljk_wine` WHERE `status` = 3 ';
        if(preg_match("/^(-|\+)?\d+$/",$kw)){
            if($list = D()->query($sql . ' AND `id` = '.$kw)){
                $result[] = array(
                    'id' => $list[0]['id'],    
                    'value' => $list[0]['value'],
                );
            }
        }elseif($kw == ''){
            $list = D()->query($sql . ' ORDER BY id ASC LIMIT '.$count);
            foreach($list as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
            }
        }else{
            $exist_ids = array();
            $list_eq = D()->query($sql . ' AND (`cname` = \''.$kw.'\' OR `ename` = \''.$kw.'\') ORDER BY `id` LIMIT '.$count);
            foreach($list_eq as $key=>$val){
                $result[] = array(
                    'id' => $val['id'],
                    'value' => $val['value'],
                );
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $leftlike_sql = ' AND (`cname` like \''.$kw.'%\' OR `ename` like \''.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $leftlike_sql .= ' AND `id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $leftlike_sql .= ' ORDER BY `id` LIMIT '.$count;
                $list_leftlike = D()->query($sql . $leftlike_sql);
                foreach($list_leftlike as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $like_sql = ' AND (`cname` like \'%'.$kw.'%\' OR `ename` like \'%'.$kw.'%\') ';
                if(count($exist_ids) > 0){
                    $like_sql .= ' AND `id` NOT IN ('.implode(',',$exist_ids).') ';
                }
                $like_sql .= ' ORDER BY `id` LIMIT '.$count;
                $list_like = D()->query($sql . $like_sql);
                foreach($list_like as $key=>$val){
                    $result[] = array(
                        'id' => $val['id'],
                        'value' => $val['value'],
                    );
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
}
