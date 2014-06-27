<?php

/**
 * 酒的年份管理
 */
class YwineAction extends CommonAction {
    public function index(){
        $wine_id = intval($_GET['wine_id']);
        if(!$wine_id)
            $this->_jumpGo('参数错误!', 'error');
        $res = D('Wine')->field('id,fname,cname')->where(array('id'=>$wine_id, 'merge_id'=>0, 'is_del'=>'-1'))->find();
        if(!$res)
            $this->_jumpGo('参数错误!', 'error');
        $this->assign('res', $res);
        //年份
        $all_year = range(1900, date('Y'));
        $all_year_chunk = array_chunk($all_year, 10);
        foreach($all_year_chunk as $key=>$val){
            $all_year_chunk[$key] = array_reverse($val);
        }
        $all_year_chunk = array_reverse($all_year_chunk);
        $all_year_chunk[] = array('NV','NOYEAR');
        //end
        $plist = array();
        foreach($all_year_chunk as $key=>$val){
            $plist[$key] = array(
                'page' => $key,
                'title' => $val[0] . '-' . $val[count($val)-1],
            );
        }
        $this->assign('plist',$plist);
        $page = $all_year_chunk[intval($_GET['page'])] ? intval($_GET['page']) : 0;
        $this->assign('page',$page);
        $list = $all_year_chunk[$page];
        $this->assign('list',$list);
        //评价机构列表
        $evalparty_list = D('Evalparty')->getList('keyid');
        $this->assign('evalparty_list', $evalparty_list);
        $this->display();
    }
    function operate(){
        $type = Input::getVar($_POST['type']);
        //获取数据
        if($type == 'getdata'){
	        $wine_id = intval($_POST['wine_id']);
	        $y_str = $_POST['y_str'];
	        if(!$wine_id || !$y_str)
	            exit(json_encode(array('errorCode'=>1,'errorStr'=>'Parameter Error')));
	        $list = D('Ywine')->field('id,year,price,status')->where(array('wine_id'=>$wine_id,'year'=>array('in',$y_str)))->order('year desc')->select();
	        foreach($list as $key=>$val){
	            $list[$key]['eval_res'] = D('YwineEval')->field('id,score,evalparty_id')->where(array('ywine_id'=>$val['id']))->order('evalparty_id ASC')->select();
	        }
	        exit(json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$list)));
        }
        //更新价格
        if($type == 'updprice'){
            $wine_id = intval($_POST['wine_id']);
            $year = Input::getVar($_POST['year']);
            $price = Input::getVar($_POST['price']);
            if(!$wine_id || !$year)
                exit(json_encode(array('errorCode'=>1,'errorStr'=>'Parameter Error')));
            $ywine_id = D('Ywine')->where(array('wine_id'=>$wine_id,'year'=>$year))->getfield('id');
            if($ywine_id)
                D('Ywine')->save(array('id'=>$ywine_id,'price'=>$price));
            else
                D('Ywine')->add(array('price'=>$price,'year'=>$year,'wine_id'=>$wine_id));
            exit(json_encode(array('errorCode'=>0)));
        }
        //更新分数
        if($type == 'updscore'){
            $wine_id = intval($_POST['wine_id']);
            $year = Input::getVar($_POST['year']);
            $evalparty_id = intval($_POST['evalparty_id']);
            $score = Input::getVar($_REQUEST['score']);
            if(!$wine_id || !$year || !$evalparty_id)
                exit(json_encode(array('errorCode'=>1,'errorStr'=>'Parameter Error')));
            $ywine_id = D('Ywine')->where(array('wine_id'=>$wine_id,'year'=>$year))->getfield('id');
            if(!$ywine_id)
                $ywine_id = D('Ywine')->add(array('year'=>$year,'wine_id'=>$wine_id));
            $ywine_eval_id = D('YwineEval')->where(array('ywine_id'=>$ywine_id,'evalparty_id'=>$evalparty_id))->getfield('id');
            if($ywine_eval_id)
                D('YwineEval')->save(array('id'=>$ywine_eval_id,'score'=>$score));
            else
                D('YwineEval')->add(array('score'=>$score,'ywine_id'=>$ywine_id,'evalparty_id'=>$evalparty_id));
            exit(json_encode(array('errorCode'=>0)));
        }
        //更新状态
        if($type == 'updstatus'){
            $wine_id = intval($_POST['wine_id']);
            $year = Input::getVar($_POST['year']);
            $status = trim($_POST['status']) == '1' ? '1' : '-1';
            if(!$wine_id || !$year || !$status)
                exit(json_encode(array('errorCode'=>1,'errorStr'=>'Parameter Error')));
            D('Ywine')->where(array('wine_id'=>$wine_id,'year'=>$year))->save(array('status'=>$status));
            exit(json_encode(array('errorCode'=>0)));
        }
        exit(json_encode(array('errorCode'=>1,'errorStr'=>'Parameter Error')));
    }
}
