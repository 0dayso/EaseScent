<?php
/**
 * Enter description here...
 * 用户管理
 */
class AdminUserAction extends AdminAction  {
	/**
	 * Enter description here...
	 * 企业通行证会员列表
	 */
	function index(){
        $this->assign("title","用户管理-逸香企业");
		$UsersModle = D("Users");
        //加载分页类
        import('@.ORG.Util.Page');
        $pageCount = 20;
        $count = $UsersModle->count();
        $p = new Page($count, $pageCount); 
		$userinfo = $UsersModle->table("ym_users as yu")
					->join("ym_users_info as  ui on yu.id=ui.id")
					->field("yu.id,yu.username,ui.nick,ui.email,ui.moblie,ui.email_valid,ui.moblie_valid,yu.reg_time")
					->order("yu.id DESC")
                    ->Limit($p->firstRow . ',' . $p->listRows)
                    ->select();
        $page = $p->show();
        $this->assign("userinfo",$userinfo);
        $this->assign("page",$page);
        $this->display("index");
	}
}


?>
