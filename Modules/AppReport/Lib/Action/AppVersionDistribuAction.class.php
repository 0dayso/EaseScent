<?php
//应用趋势-版本分布
class AppVersionDistribuAction extends CommonAction {
    public function index(){
		$Ver = M('DistributionVersion');
		$verno = $Ver->field('version')->group('version')->order('version DESC')->select();
		$this->assign('verno',$verno);
        $this->display();
    }

    

    public function getVersionData(){
    	/*
		 根据版本查询；根据新增用户或者活跃用户查询；根据时间范围查询；
    	*/
		$postData = $_POST;
    	$Ver = M('DistributionVersion');
    	$field = '';
    	$where = array();
    	$type = $postData['type'] ? $postData['type'] : 1;
    	$duration = $postData['timesection'] ? $postData['timesection'] : 0;
    	$cat = $postData['cat'] ? $postData['cat'] : 'newuser';
    	$where = array('type' => $type);
    	switch ($duration) {
    		case '7':
    			$dur = mktime(0,0,0,date("m"),date('d')-7,date('Y'));
    			$where['date_time'] = array('between',$dur.','.time());
    			break;
    		
    		case '30':
    			$dur = mktime(0,0,0,date("m"),date('d')-30,date('Y'));
    			$where['date_time'] = array('between',$dur.','.time());
    			break;
    		
    		case '0':
    			break;
    	}
    	switch ($cat) {
    		case 'newuser':
    			$field = 'new_users';
    			break;
    		
    		case 'activeuser':
    			$field = 'user_starts';
    			break;
    		
    		case 'starttime':
    			$field = 'starts';
    			break;
    		
    		default:
    			$field = 'new_users';
    			break;
    	}
    	$versions = array();
		$verno = $Ver->field('version')->group('version')->order('version DESC')->select(); // version number
		foreach ($verno as $key => $value) {
			$where = array_merge($where,array('version' => $value['version']));
			$data = $Ver->field('version,'.$field.',date_time')->where($where)->order('version ASC')->limit(10)->select();
			$versions[] = $data;
		}
		$info = array('showfield'=>$field,'ver'=>$versions);
		echo json_encode($info);
    }
    public function verlist(){
    	$Ver = M('DistributionVersion');
        $duration = trim($_GET['dur']);
        $type = $_GET['type'];
        $where = array('type' => $type);
        switch ($duration) {
            case '7':
                $dur = mktime(0,0,0,date("m"),date('d')-7,date('Y'));
                $where['date_time'] = array('between',$dur.','.time());
                break;
            
            case '30':
                $dur = mktime(0,0,0,date("m"),date('d')-30,date('Y'));
                $where['date_time'] = array('between',$dur.','.time());
                break;
            
            case '0':
                break;
        }
    	$vData = array();
    	$verno = $Ver->field('version')->group('version')->order('version DESC')->select();
    	foreach ($verno as $key => $value) {
    		$data = $Ver->field('')->where()->order('version ASC')->select();
            $where['version'] = $value['version'];
    		$vData[$value['version']] = array();
    		$vData[$value['version']]['startno'] = $Ver->where($where)->sum('starts');
    		$vData[$value['version']]['startuserno'] = $Ver->where($where)->sum('user_starts');
    		$vData[$value['version']]['newuserno'] = $Ver->where($where)->sum('new_users');
            $vData[$value['version']]['avgusedtime'] = round($Ver->where($where)->avg('use_long'),2);            
    		// $vData[]['avgusedtime'] = $Ver->avg();
    	}
    	$count = $Ver->query('SELECT COUNT(DISTINCT `version`) AS ct FROM __TABLE__');
	    import('ORG.Util.Page');
	    $count = $count[0]['ct'];
	    $page = new Page($count,5);
	    $show = $page->show();
	    $this->assign('page',$show);
    	$this->assign('vdata',$vData);
    	$this->display();
    }
}
