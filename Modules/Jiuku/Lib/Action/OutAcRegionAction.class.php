<?php

/**
 * 权限控制外产区方法
 */
class OutAcRegionAction extends Action {
    /**
     * 初始化
     */
    public function _initialize() {
        import('@.ORG.Util.Input');
        import('@.ORG.Util.Image');
    }
	/**
	 * 关键字搜索产区
	 *
	 */
	function ajaxKeywordSearchRegion(){
		if(!isset($_POST['kw'])){
			echo json_encode(array('errorCode'=>600001,'errorStr'=>'Parameter Error')); exit();
		}
		$kw = strtolower(Input::getVar($_POST["kw"]));
		if (get_magic_quotes_gpc()) $kw = stripslashes($kw);
		$map['is_del'] = '-1';
        //是否获取关闭状态的数据 1获取 其他 不获取
        if(empty($_GET['st']) || intval($_GET['st']) !== 1){
            $map['status'] = '1';
        }
		$result = array();
		if(preg_match("/^(-|\+)?\d+$/",$kw)){
            $res = D('Region')->field('id,fname,cname')->where(array_merge($map,array('id'=>$kw)))->find();
            array_push($result,$res);
        }else{
			$eq_res = D('Region')->field('id,fname,cname')->where(array_merge($map,array('_complex'=>array('fname'=>$kw,'cname'=>$kw,'_logic'=>'or'))))->find();
			if($eq_res) array_push($result,$eq_res);
			$res = D('Region')->field('id,fname,cname')->where($map)->select();
			foreach($res as $key=>$val){
				if($eq_res['id'] == $val['id']) continue;
				if(strpos(strtolower($val['fname']),$kw) !== false || strpos(strtolower($val['cname']),$kw) !== false){
					array_push($result,$val);
				}
			}
		}
		$result_count = count($result);
		$result_chunk = array_chunk($result,10) ;
		echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('count'=>$result_count,'res'=>$result_chunk[0]))); exit();
	}
	/**
	 * 点击+1
	 * X3xgfmA6WmBhVHZCfHtwZ2w6fXxh
	 */
	public function hit(){
		$id = intval($_GET['id']);
		if(!id){
			$this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
		}
		if($Redis = $this->linkRedis()){
			$res = $Redis->hgetall('jk:region_hit:'.$id.':');
			if(!$res){
				$Redis->hmset('jk:region_hit:'.$id.':',array('add'=>1,'all'=>1,'time'=>time()));
			}else{
				$Redis->hmset('jk:region_hit:'.$id.':',array('add'=>$res['add']+1,'all'=>$res['all']+1,'time'=>$res['time']));
				if((time()-$res['time']) > 7200){
					D()->query('UPDATE `jiuku_region` SET `hit` = `hit` +'.($res['add']+1).' WHERE `id` = '.$id);
					$s_res = D('Region')->where(array('id'=>$id))->getfield('hit');
					$Redis->hmset('jk:region_hit:'.$id.':',array('add'=>0,'all'=>$s_res,'time'=>time()));
				}
			}
			$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res['all']+1));
		}else{
			D()->query('UPDATE `jiuku_region` SET `hit` = `hit` +1 WHERE `id` = '.$id);
			$res = D('Region')->where(array('id'=>$id))->getfield('hit');
			$this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
		}
	}
	function echo_exit($arr){
		if(empty($_GET['callback'])){
			echo json_encode($arr);
		}else{
			echo $_GET['callback'].'('.json_encode($arr).')';
		}
		exit();
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
}
