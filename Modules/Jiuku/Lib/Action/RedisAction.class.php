<?php

/**
 * Redis方法
 */
class RedisAction extends BaseAdmin {

	/**
	 * 初始化
	 */
	public function _initialize() {
		parent::_initialize();
		//引入输入过滤类
		import('@.ORG.Util.Input');
		import('@.ORG.Util.Page');
		import('@.ORG.Util.String');
	}
	function linkRedis(){
		if(!extension_loaded('redis')){
			return false;exit();
		}
		$Redis = new Redis();
		$Rediscfg = C('REDIS_CONFIG');
		try{
			$Redis->connect($Rediscfg['host'], $Rediscfg['port']);
		}catch(Exception $e){
			return false;exit();
		}
		return $Redis;
	}
	/**
	 * 初始化酒款Redis索引
	 */
	function iniWineRedis(){
        if($this->isPost()){
			if(!$Redis = $this->linkRedis()){
				echo json_encode(array('errorCode'=>1,'errorStr'=>'Redis Error'));
				exit();
			}
        	$limit = Input::getVar($_POST['limit']);
			$row = 100;
			$res = D('Wine')->limit($limit.','.$row)->select();
			foreach($res as $key=>$val){
				$fname_arr = $this->_cfRedisStr($val['fname'],1);
				foreach($fname_arr as $k=>$v){
					$Redis->sAdd('jiuku_winefname_wine:'.strtolower($v),$val['id']);
				}
				$cname_arr = $this->_cfRedisStr($val['cname'],2);
				foreach($cname_arr as $k=>$v){
					$Redis->sAdd('jiuku_winecname_wine:'.strtolower($v),$val['id']);
				}
			}
			$result = array('isover'=>0,'run'=>count($res),'count'=>D('Wine')->count());
			if(count($res)<$row){
				$result['isover'] = 1;
			}
			echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>$result));
			exit();
		}
		$this->assign('count',D('Wine')->count());
		$this->display();
	}
	/**
	 * 拆分Redis字符串为数组$type 1外文 2中文
	 */
	public function _cfRedisStr($str, $type) {
		$return = array();
		if($str){
			$len = mb_strlen($str,'utf-8');
			if($type == 1){
				$arr = explode(' ',$str);
				$i=0;
				foreach($arr as $key=>$val){
					for($j=1;$j<=($len-$i);$j++){
						$return[] = mb_substr($str,$i,$j,'utf-8');
					}
					$i = $i+ mb_strlen($val,'utf-8')+1;
				}
			}
			if($type == 2){
				for($i=0;$i<$len;$i++){
					for($j=1;$j<=($len-$i);$j++){
						$return[] = mb_substr($str,$i,$j,'utf-8');
					}
				}
			}
		}
		return $return;
	}
	
	/**
	 * 批量生成代理商基本Redis索引方法
	 */
	function ajaxAgentsRedisAll(){
		if(trim($_POST['type']) == 'ini'){
			echo D('Agents')->max('id'); exit;
		}
		if(!$Redis = $this->linkRedis()){
			exit();
		}
		$lim = 100;
		$now = intval($_POST['now']);
		$max = intval($_POST['max']);
		for($i=0;$i<$lim;$i++){
			$id = $now + 1;
			if($id > $max) break;
			$redis_res = $this->getAgentsRedisData($id);
			if(!$redis_res){
				$Redis->del('jk:agents:'.$id);
			}else{
				$Redis->set('jk:agents:'.$id,json_encode($redis_res));
			}
			$now++;
		}
		echo $now; exit;
	}
	
	/**
	 * 生成代理商基本Redis索引
	 */
	function ajaxAgentsRedis(){
		if(!$Redis = $this->linkRedis()){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'link redis fail','result'=>NULL));
		}
		$id = intval($_POST['id']);
		if(!$id){
			$this->echo_exit(array('errorCode'=>600002,'errorStr'=>'parameter error','result'=>NULL));
		}
		$redis_res = $this->getAgentsRedisData($id);
		if(!$redis_res){
			$Redis->del('jk:agents:'.$id);
			$this->echo_exit(array('errorCode'=>600003,'errorStr'=>'get data fail','result'=>NULL));
		}else{
			$msg = $Redis->set('jk:agents:'.$id,json_encode($redis_res));
			$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>array('type'=>'set','key'=>'jk:agents:'.$id,'val'=>json_encode($redis_res))));
		}
	}
	function getAgentsRedisData($id){
		$res = D('Agents')->where(array('id'=>$id,'status'=>'1','is_del'=>'-1'))->find();
		if(!$res) return false;
		$redis_res = array(
						   'id'				=> $res['id'],
						   'fname'			=> $res['fname'],
						   'cname'			=> $res['cname'],
						   'aname'			=> $res['aname'],
						   'tel'			=> $res['tel'],
						   'url'			=> $res['url'],
						   'address'		=> $res['address'],
						   'description'	=> $res['description'],
						   'content'		=> $res['content'],
						   'queue'			=> $res['queue'],
						   );
		$res['img_res'] = D('AgentsImg')->where(array('agents_id'=>$id,'status'=>'1','is_del'=>'-1'))->select();
		$redis_res['img_res'] = array();
		foreach($res['img_res'] as $key=>$val){
			$redis_res['img_res'][] = array(
											'id'			=> $val['id'],
											'filename'		=> $val['filename'],
											'description'	=> $val['description'],
											'alt'			=> $val['alt'],
											'queue'			=> $val['queue'],
											);
		}
		$res['i_sales_res'] = D('AgentsInternetSales')->where(array('agents_id'=>$id,'status'=>'1','is_del'=>'-1'))->select();
		$redis_res['i_sales_res'] = array();
		foreach($res['i_sales_res'] as $key=>$val){
			$redis_res['i_sales_res'][] = array(
												'id'	=> $val['id'],
												'name'	=> $val['name'],
												'url'	=> $val['url'],
												);
		}
		$res['s_sales_res'] = D('AgentsStoreSales')->where(array('agents_id'=>$id,'status'=>'1','is_del'=>'-1'))->select();
		$redis_res['s_sales_res'] = array();
		foreach($res['s_sales_res'] as $key=>$val){
			$redis_res['s_sales_res'][] = array(
												'id'		=> $val['id'],
												'name'		=> $val['name'],
												'tel'		=> $val['tel'],
												'address'	=> $val['address'],
												'lat'		=> $val['lat'],
												'lng'		=> $val['lng'],
												'map'		=> $val['map'],
												);
		}
		return $redis_res;
	}
	function echo_exit($arr){
		if(empty($_GET['callback'])){
			echo json_encode($arr);
		}else{
			echo $_GET['callback'].'('.json_encode($arr).')';
		}
		exit();
	}
}
