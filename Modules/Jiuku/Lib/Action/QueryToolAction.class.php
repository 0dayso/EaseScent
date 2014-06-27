<?php

/**
 * 酒库查询工具
 */
class QueryToolAction extends Action {
    public function _initialize() {
    }

    /**
     * 列表页将POST参数改为GET
     */
    function post_to_get(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        foreach($_POST as $key=>$val){
            $url .= '&' . $key . '=' . $val;
        }
        header('Location: ' . $url);
        exit;
    }

	public function index() {
        if($this->isPost()){
            $this->post_to_get();
        }
    	$search_list = array(
    		'1'=>array('text'=>'国家','dbtable'=>'Country'),
    		'2'=>array('text'=>'产区','dbtable'=>'Region'),
    		'3'=>array('text'=>'酒庄','dbtable'=>'Winery'),
    		'4'=>array('text'=>'葡萄品种','dbtable'=>'Grape'),
    		'5'=>array('text'=>'酒款原文名','dbtable'=>'Wine'),
    		'6'=>array('text'=>'酒款中文名','dbtable'=>'WineCaname'),
    	);
    	$this->assign('search_list',$search_list);
    	if(in_array($_GET['type'], array(1,2,3,4,5,6))){
	    	$map = array();
	    	if($_GET['kw'] != ''){
	    		if(preg_match("/^(-|\+)?\d+$/",$_GET['kw'])){
	    			$map['id'] = $_GET['kw'];
	    		}else{
	                $map_k['fname'] = array('like', '%'.$_GET['kw'].'%');
	                $map_k['cname'] = array('like', '%'.$_GET['kw'].'%');
	                $map_k['_logic'] = 'or';
	                $map['_complex'] = $map_k;
	    		}
	    	}
			$count = D($search_list[$_GET['type']]['dbtable'])->where($map)->count();
	    	$res = D($search_list[$_GET['type']]['dbtable'])->where($map)->limit(10)->select();
	    	$this->assign('count',$count);
	    	$this->assign('res',$res);
	    }
		$this->display();
	}
}
