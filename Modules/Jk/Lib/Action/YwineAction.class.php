<?php
// 年份酒款管理
class YwineAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $_POST = $this->sanitize($_POST);
        $_GET = $this->sanitize($_GET);
    }

    public function index(){
        if($this->isPost()){
            $this->post_to_get();
        }
        $wine_id = intval($_GET['wine_id']);
        if($wine_res = D()->table('jk_d_wine')->where(array('id'=>$wine_id))->find()){
            $years = array_merge(array('NA','NV'),range(date('Y'),1900));
            $evalpartys = D()->table('jk_d_evalparty')->select();
            $this->assign('wine_res', $wine_res);
            $this->assign('years', $years);
            $this->assign('evalpartys', $evalpartys);
        }
        $this->display();
    }
    function getData(){
        $_backurl = U('index');
        if(!$wine_res = D()->table('jk_d_wine')->where(array('id'=>$_POST['wine_id']))->find()){
            exit(json_encode(array('error'=>1,'msg'=>'获取失败！参数异常或数据不存在，请返回至初始页。','_backurl'=>$_backurl)));
        }
        $res = D()->table('jk_d_ywine')->where(array('wine_id'=>$_POST['wine_id']))->select();
        foreach($res as $key=>$val){
            $res[$key]['eval_res'] = D()->table('jk_d_ywineeval')->where(array('ywine_id'=>$val['id']))->select();
        }
        exit(json_encode(array('error'=>0,'msg'=>'获取成功！','_backurl'=>$_backurl,'result'=>$res)));
    }
    function updateData(){
        $_backurl = U('index');
        if($_POST['type'] == 'score'){
            $wine_id = intval($_POST['wine_id']);
            $year = trim($_POST['year']);
            $evalparty_id = intval($_POST['evalparty_id']);
            $score = trim($_POST['score']);
            if(!$wine_id || !$year || !$evalparty_id){
                exit(json_encode(array('error'=>1,'msg'=>'更新失败！参数异常，请刷新当前页面。','_backurl'=>$_backurl)));
            }
            if(!$ywine_id = D()->table('jk_d_ywine')->where(array('wine_id'=>$wine_id, 'year'=>$year))->getfield('id')){
                $ywine_id = D()->table('jk_d_ywine')->add(array('year'=>$year, 'wine_id'=>$wine_id));
            }
            if(!$ywineeval_res = D()->table('jk_d_ywineeval')->where(array('evalparty_id'=>$evalparty_id, 'ywine_id'=>$ywine_id))->find()){
                D()->table('jk_d_ywineeval')->add(array('evalparty_id'=>$evalparty_id, 'ywine_id'=>$ywine_id, 'score'=>$score));
            }else{
                D()->table('jk_d_ywineeval')->where(array('evalparty_id'=>$evalparty_id, 'ywine_id'=>$ywine_id))->save(array('score'=>$score));
            }
            exit(json_encode(array('error'=>0,'msg'=>'更新成功。','_backurl'=>$_backurl)));
        }elseif($_POST['type'] == 'price'){
            $wine_id = intval($_POST['wine_id']);
            $year = trim($_POST['year']);
            $price = trim($_POST['price']);
            if(!$wine_id || !$year){
                exit(json_encode(array('error'=>1,'msg'=>'更新失败！参数异常，请刷新当前页面。','_backurl'=>$_backurl)));
            }
            if(!$ywine_id = D()->table('jk_d_ywine')->where(array('wine_id'=>$wine_id, 'year'=>$year))->getfield('id')){
                D()->table('jk_d_ywine')->add(array('year'=>$year, 'price'=>$price, 'wine_id'=>$wine_id));
            }else{
                D()->table('jk_d_ywine')->where(array('year'=>$year, 'wine_id'=>$wine_id))->save(array('price'=>$price));
            }
            exit(json_encode(array('error'=>0,'msg'=>'更新成功。','_backurl'=>$_backurl)));
        }else{
            exit(json_encode(array('error'=>1,'msg'=>'更新失败！参数异常，请刷新当前页面。','_backurl'=>$_backurl)));
        }
    }
}