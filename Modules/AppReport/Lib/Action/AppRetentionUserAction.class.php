<?php
//应用趋势-存留用户
class AppRetentionUserAction extends CommonAction {
    public function index(){
        $this->display();
    }

    public function verlist(){
    	$M = M('LifetimeUsers');
    	$getData = $_GET;
    	$type = $getData['type'] ? $getData['type'] : 1;
    	$timeSection = $getData['timesection'] ? $getData['timesection'] : 0;
    	$where = array();
    	$where = array('type' => $type);
    	switch ($timeSection) {
    		case '7':
    			# code...
    			break;
    			$day7before = mktime(0,0,0,date("m"),date('d')-7,date('Y'));
    			$where['date_time'] = array('between',$day7before.','.time());
    			break;
    		
    		case '30':
    			$day30before = mktime(0,0,0,date("m"),date('d')-30,date('Y'));
    			$where['date_time'] = array('between',$day30before.','.time());
    			break;
    		
    		case '0':
    			break;
    	}
    	$data = $M->where($where)->select();
    	$count = $M->where('type='.$type)->count();
    	import('ORG.Util.Page');
	    $page = new Page($count,5);
	    $show = $page->show();
	    $this->assign('page',$show);
    	$this->assign('list',$data);
    	$this->display();
    }
}