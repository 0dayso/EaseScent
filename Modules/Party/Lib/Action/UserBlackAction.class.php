<?php
class UserBlackAction extends Action
{
	function getBlackUsers($type){
		$u = D("UserMg");
		$info =$u->where("uid=".$_SESSION['members']["uid"]." AND status=1 AND type=".$type)->Limit(1)->find();
		return $info;
	}
	function filtrate(){
		$u = $this->getBlackUsers(1);
		if($u){
			return true;
		}
		return false;
	}
}

?>