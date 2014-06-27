<?php
//用户-用户行为
class UserBehaviorAction extends CommonAction {
    public function index(){
    	$user=M('User_behavior');
    	import('ORG.Util.Page');// 导入分页类
        $count      = $user->where('type=1')->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $user-> where("type=1")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('data',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    
    //最近七天的记录
    public function near_data(){
        
          $type=intval($_GET['type']);
          $time=intval($_GET['time']);
          $time_l=strtotime('today')-($time*24*60*60);
        $user=M('User_behavior');
                $type=$_GET['type']?$_GET['type']:1;
                import('ORG.Util.Page');// 导入分页类
                $count      = $user->where("type=$type and date_time >= $time_l")->count();// 查询满足要求的总记录数
                $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
                $show       = $Page->show();// 分页显示输出
                // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
                $list = $user-> where("type=$type and date_time > $time_l")->limit($Page->firstRow.','.$Page->listRows)->select();
                $this->assign('data',$list);// 赋值数据集
                $this->assign('page',$show);// 赋值分页输出
                $this->display('user_detail');
     
    }
    //根据类型获取数据，
    public function ios_userbeh(){
    	$user=M('User_behavior');
        $type=$_GET['type']?$_GET['type']:1;
        import('ORG.Util.Page');// 导入分页类
        $count      = $user->where("type=$type")->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $user-> where("type=$type")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('data',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display('user_detail');
    }

    //获取指定时间段的记录
    public function get_record(){
        
            $starttime=strtotime($_GET['stime']);
            $endtime=strtotime($_GET['etime']);
            $type=$_GET['type'];
            $user=M('User_behavior');
            //$stime=strtotime('today')-$starttime;
        import('ORG.Util.Page');// 导入分页类
       // $where="type=$type and date_time > $starttime and date_time < $endtime";
        $count      = $user->where("type=$type and date_time > $starttime and date_time < $endtime")->count();// 查询满足要求的总记录数
        $Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $user-> where("type=$type and date_time > $starttime and date_time < $endtime")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('data',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display('user_detail');
      
    }

    
}