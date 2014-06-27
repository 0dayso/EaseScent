<?php
//用户-地域分布
class UserAreaDistribuAction extends CommonAction {
    public function index(){
        $this->display();
    }

    //top10 新增用户数据 
    public function users_data(){
    	$type=$_GET['type']?$_GET['type']:1;
       $user=M("Geographical_distribution");
       $sql="select area,new_users from phone_geographical_distribution where type=$type order by new_users desc limit 0,10";
       $data=$user->query($sql);
       //var_dump($data);
       $datas['length']=count($data);
       $datas['data_all']=$data;
       echo json_encode($datas);
       
    }

    //top10 启动次数数据  ios
    public function start_data(){
    	$type=$_GET['type']?$_GET['type']:1;
       $user=M("Geographical_distribution");
       $sql="select area,starts from phone_geographical_distribution where type=$type order by starts desc limit 0,10";
       $data=$user->query($sql);
       $datas['length']=count($data);
       $datas['data_all']=$data;
       echo json_encode($datas);
    }

    
    //地域数据明细 ios
    public function user_detail(){
    	$type=$_GET['type']?$_GET['type']:1;
      $desc=$_GET['od']?$_GET['od']:'new_users';
    	$User = M('Geographical_distribution'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count      = $User->where("type=$type")->count();// 查询满足要求的总记录数
		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User-> where("type=$type")->order("$desc desc")->limit($Page->firstRow.','.$Page->listRows)->select();
    for($k=0;$k<count($list);$k++){
      $list[$k]['new_user_ratio']=$list[$k]['new_user_ratio'].'%';
      $list[$k]['start_ratio']=$list[$k]['start_ratio'].'%';
    }
		$this->assign('data_list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
    	$this->display("user_area_detail");
    }
}