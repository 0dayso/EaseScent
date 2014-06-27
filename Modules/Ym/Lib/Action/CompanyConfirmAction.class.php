<?php
class CompanyConfirmAction extends BaseAdmin{ 
    /**
     * index 
     * 公司认证列表
     * @access public
     * @return void
     */
    function index(){
        $this->assign("title","企业审核-逸香企业");
        $usersQyModel = D("UsersQy");
        $where = 1;
        $comany = $_POST["comany"];
        $name = trim($_POST["name"]);
        if($comany==""){
            $where .= " and comany_valid=1";
        }elseif($comany>0){
            $where .= " and comany_valid=".$comany;
        }
        if($name!=""){
            $where .= " and qy_name like'%".$name."%'";
        }
        import('@.ORG.Util.Page');
        $pageCount = 20;
        $count = $usersQyModel->where($where)->count();
        $p = new Page($count, $pageCount); 
        $list = $usersQyModel
            ->where($where)
			->Limit($p->firstRow . ',' . $p->listRows)
            ->order("id DESC")
            ->select();
        Load('extend');
        foreach($list as $k=>$v){
          $list[$k]['qy_introduction'] =  msubstr($v['qy_introduction'],0,10,'utf-8',true);  
        }
        $page = $p->show();
        $this->assign("page",$page);
        $this->assign("list",$list);
        $this->display("index");
    }
    /**
     * info 
     * 公司详细页
     * @access public
     * @return void
     */
    function info(){
        $usersQyModel = D("UsersQy");
        $id = $_GET["id"];    	
        //加载语言包
    	if (C("SET_LANG")!=""){
    		 include APP_PATH."Lang/".C("SET_LANG").".php";
    	}
 
        $info = $usersQyModel->where("id=".$id)->find();
        $info["qy_hangye"] = $Lang["HANGYE"][$info["qy_hangye"]];
        $info["qy_xingzhi"] = $Lang["XINGZHI"][$info["qy_xingzhi"]];
        $info["qy_guimo"] = $Lang["GUIMO"][$info["qy_guimo"]];
        $this->assign("info",$info);
        $this->display("info");
    }
    function apply(){
        $id = $_POST["id"];
        $type = $_POST["type"];
        $usersQyModel = D("UsersQy");
        $data["comany_valid"] = $type;
        $usersQyModel->where("id=".$id)->save($data);

        echo json_encode(array("status"=>1,"message"=>"处理成功!"));
    }
}
?>
