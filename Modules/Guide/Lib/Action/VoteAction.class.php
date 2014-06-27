<?php

class VoteAction extends FrontAction {
	function index(){
		$goods_id = !empty($_POST['goods_id'])?$_POST['goods_id']:'';
		$ip_address = $this->real_ip();
		$this->get_vote_count($goods_id);
		 if($this->vote_already_submited($goods_id, $ip_address)){
		 	$res['errors']   = 1;
     		$res['message'] = '已经喜欢过了';
		 }else{
			$this->save_vote($goods_id, $ip_address);
			$res['vote_count'] = $this->get_vote_count($goods_id);
			$res['message'] = '成功';
		 }
		echo json_encode($res);
	}
	//检查是否已经提交过投票
	function vote_already_submited($goods_id, $ip_address){
	    $count=M("vote_log")->where("ip_address='$ip_address' and goods_id='$goods_id'")->Count();
	    return ($count > 0);
	}
	//保存投票结果信息
	function save_vote($goods_id, $ip_address){
		$data['goods_id']    = $goods_id;
		$data['ip_address']  = $ip_address;
		$data['vote_time']   = $this->gmtime();
		M("vote_log")->add($data);
	    
	    //更新投票的数量 
	    M('goods')->query("UPDATE  sg_goods SET vote_count = vote_count + 1 where goods_id=".$goods_id);
	}

	//获取投票总数
	function get_vote_count($goods_id){

		$counts =  M("goods")->where("goods_id='$goods_id'")->getField('vote_count');
		return $counts;
	}

	//获得当前格林威治时间的时间戳
	function gmtime(){
    	return (time() - date('Z'));
	}
}
?>