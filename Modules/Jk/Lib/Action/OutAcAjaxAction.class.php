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

    function getCountryForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($kw == ''){
            $list = D('DCountry')->order('id ASC')->limit($count)->select();
            $result = $list ? $list : array();
        }else{
            $exist_ids[] = array();
            $where_eq['fname'] = $kw;
            $where_eq['cname'] = $kw;
            $where_eq['_logic'] = 'or';
            $map_eq['_complex'] = $where_eq;
            $list_eq = D('DCountry')->where($map_eq)->limit($count)->select();
            foreach($list_eq as $key=>$val){
                $result[] = $val;
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $where_leftlike['fname'] = array('like',$kw.'%');
                $where_leftlike['cname'] = array('like',$kw.'%');
                $where_leftlike['_logic'] = 'or';
                $map_leftlike['_complex'] = $where_leftlike;
                if(count($exist_ids) > 0){
                    $map_leftlike['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_leftlike = D('DCountry')->where($map_leftlike)->limit($count)->select();
                foreach($list_leftlike as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $where_like['fname'] = array('like','%'.$kw.'%');
                $where_like['cname'] = array('like','%'.$kw.'%');
                $where_like['_logic'] = 'or';
                $map_like['_complex'] = $where_like;
                if(count($exist_ids) > 0){
                    $map_like['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_like = D('DCountry')->where($map_like)->limit($count)->select();
                foreach($list_like as $key=>$val){
                    $result[] = $val;
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
    function getRegionForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($kw == ''){
            $list = D('DRegion')->order('id ASC')->limit($count)->select();
            $result = $list ? $list : array();
        }else{
            $exist_ids[] = array();
            $where_eq['fname'] = $kw;
            $where_eq['cname'] = $kw;
            $where_eq['_logic'] = 'or';
            $map_eq['_complex'] = $where_eq;
            $list_eq = D('DRegion')->where($map_eq)->limit($count)->select();
            foreach($list_eq as $key=>$val){
                $result[] = $val;
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $where_leftlike['fname'] = array('like',$kw.'%');
                $where_leftlike['cname'] = array('like',$kw.'%');
                $where_leftlike['_logic'] = 'or';
                $map_leftlike['_complex'] = $where_leftlike;
                if(count($exist_ids) > 0){
                    $map_leftlike['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_leftlike = D('DRegion')->where($map_leftlike)->limit($count)->select();
                foreach($list_leftlike as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $where_like['fname'] = array('like','%'.$kw.'%');
                $where_like['cname'] = array('like','%'.$kw.'%');
                $where_like['_logic'] = 'or';
                $map_like['_complex'] = $where_like;
                if(count($exist_ids) > 0){
                    $map_like['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_like = D('DRegion')->where($map_like)->limit($count)->select();
                foreach($list_like as $key=>$val){
                    $result[] = $val;
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
    function getWineryForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($kw == ''){
            $list = D('DWinery')->order('id ASC')->limit($count)->select();
            $result = $list ? $list : array();
        }else{
            $exist_ids[] = array();
            $where_eq['fname'] = $kw;
            $where_eq['cname'] = $kw;
            $where_eq['_logic'] = 'or';
            $map_eq['_complex'] = $where_eq;
            $list_eq = D('DWinery')->where($map_eq)->limit($count)->select();
            foreach($list_eq as $key=>$val){
                $result[] = $val;
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $where_leftlike['fname'] = array('like',$kw.'%');
                $where_leftlike['cname'] = array('like',$kw.'%');
                $where_leftlike['_logic'] = 'or';
                $map_leftlike['_complex'] = $where_leftlike;
                if(count($exist_ids) > 0){
                    $map_leftlike['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_leftlike = D('DWinery')->where($map_leftlike)->limit($count)->select();
                foreach($list_leftlike as $key=>$val){
                    $result[] = $val;
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $where_like['fname'] = array('like','%'.$kw.'%');
                $where_like['cname'] = array('like','%'.$kw.'%');
                $where_like['_logic'] = 'or';
                $map_like['_complex'] = $where_like;
                if(count($exist_ids) > 0){
                    $map_like['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_like = D('DWinery')->where($map_like)->limit($count)->select();
                foreach($list_like as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
    function getWineForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($kw == ''){
            $list = D('DWine')->order('id ASC')->limit($count)->select();
            $result = $list ? $list : array();
        }else{
            $exist_ids[] = array();
            $map_eq['fname'] = $kw;
            $list_eq = D('DWine')->where($map_eq)->limit($count)->select();
            foreach($list_eq as $key=>$val){
                $result[] = $val;
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $map_leftlike['fname'] = array('like',$kw.'%');
                $list_leftlike = D('DWine')->where($map_leftlike)->limit($count)->select();
                foreach($list_leftlike as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $map_like['fname'] = array('like','%'.$kw.'%');
                $list_like = D('DWine')->where($map_like)->limit($count)->select();
                foreach($list_like as $key=>$val){
                    $result[] = $val;
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
    function getGrapeForKw(){
        $kw = trim($_POST['kw']);
        $result = array();
        $count = 20;
        if($kw == ''){
            $list = D('DGrape')->order('id ASC')->limit($count)->select();
            $result = $list ? $list : array();
        }else{
            $exist_ids[] = array();
            $where_eq['fname'] = $kw;
            $where_eq['cname'] = $kw;
            $where_eq['_logic'] = 'or';
            $map_eq['_complex'] = $where_eq;
            $list_eq = D('DGrape')->where($map_eq)->limit($count)->select();
            foreach($list_eq as $key=>$val){
                $result[] = $val;
                $exist_ids[] = $val['id'];
            }
            $count = $count - count($result);
            if($count > 0){
                $where_leftlike['fname'] = array('like',$kw.'%');
                $where_leftlike['cname'] = array('like',$kw.'%');
                $where_leftlike['_logic'] = 'or';
                $map_leftlike['_complex'] = $where_leftlike;
                if(count($exist_ids) > 0){
                    $map_leftlike['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_leftlike = D('DGrape')->where($map_leftlike)->limit($count)->select();
                foreach($list_leftlike as $key=>$val){
                    $result[] = $val;
                    $exist_ids[] = $val['id'];
                }
                $count = $count - count($result);
            }
            if($count > 0){
                $where_like['fname'] = array('like','%'.$kw.'%');
                $where_like['cname'] = array('like','%'.$kw.'%');
                $where_like['_logic'] = 'or';
                $map_like['_complex'] = $where_like;
                if(count($exist_ids) > 0){
                    $map_like['id'] = array('NOT IN', implode(',',$exist_ids));
                }
                $list_like = D('DGrape')->where($map_like)->limit($count)->select();
                foreach($list_like as $key=>$val){
                    $result[] = $val;
                }
                $count = $count - count($result);
            }
        }
        exit(json_encode(array('error'=>0, 'result'=>$result)));
    }
    function getRegionlvlForCountryid(){
        $country_id = intval($_POST['country_id']);
        if(!$country_res = D('DCountry')->where(array('country_id'=>$country_id))->find()){
            exit(json_encode(array('error'=>1, 'msg'=>'参数异常')));
        }
        if(!$list = D('DRegionlvl')->where(array('country_id'=>$country_id))->select()){
            exit(json_encode(array('error'=>1, 'msg'=>'没有数据')));
        }
        exit(json_encode(array('error'=>0, 'result'=>$list)));
    }
}
