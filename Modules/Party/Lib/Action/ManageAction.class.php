<?php
class ManageAction extends BaseAction{
  
  //酒会列表
  public function index(){
   $User = M('esw_wine_party'); 
  import('ORG.Util.Page');
  $count      = $User->count();
  $Page       = new Page($count,20);
  $show       = $Page->show();
  
  $list = $User->order('party_start desc')->limit($Page->firstRow.','.$Page->listRows)->select();
  
  $this->assign('list',$list);
  $this->assign('page',$show);
  $this->display('index'); 
  }
	//添加酒会
  public function add(){
    $area=M('esw_area');
     $msg=$area->where('levelID=1')->select();
    $this->assign('msg',$msg);
    //var_dump($msg);
    $this->display('add');
  }
  //获取省列表
  public function privoce(){
    $area=M('esw_area');
     $msg=$area->where('levelID=1')->select();
     echo json_encode($msg);
  }
	//删除酒会操作
  public function del(){
    $id=$this->_get('aid');
    $User = M('esw_wine_party');
    if($User->where("id=$id")->delete()){
      echo "<script>alert('删除成功');location.href='index.php?app=Party&m=Manage&a=index';</script>";
    }else{
      echo "<script>alert('删除失败');location.href='index.php?app=Party&m=Manage&a=index';</script>";
    }

  
  }
	//酒会修改操作
  public function modify(){
    $id=$this->_get('aid');
    $party=M('esw_wine_party');
    $data=$party->where("id=$id")->find();
    $data['privoce']=$this->countcity($data['province_id']);
     $data['city']=$this->countcity($data['city_id']);
     $data['county']=$this->countcity($data['area_id']);
    $this->assign('data',$data);
   $this->display('');
  }
  //搜索方法，返回数组
  public function seach(){
    $party_start=$this->_post("time");
    $party=strtotime($party_start);
    $info=M('esw_wine_party');
    $data=$info->where("party_start >= $party")->select();
    echo json_encode($data);
  }
	//
  public function check(){
    $id=$this->_post('id');
    $data['add_user']=$_SESSION['admin_uid'];
    $data['update_user']=$this->_post('update_user');
    //$data['add_time']=date('Y-m-d H:i:s',time());;
    $data['update_time']=date('Y-m-d H:i:s',time());
    $data['title']=$this->_post('title');
    $data['party_start']=strtotime($this->_post('party_start'));
    $data['party_end']=strtotime($this->_post('party_end'));
    $data['province_id']=$this->_post('privoce');
    $data['city_id']=$this->_post('city');
    $pictype = explode(".",$_POST["pictype"]);
    $data["pictype"] = $pictype[1];
    $data['area_id']=$this->_post('county');
    $data['ismoney']=$this->_post('ismoney');
    $data['contactperson']=$this->_post('contactperson');
    $data['contactphone']=$this->_post('contactphone');
    $data['markprice']=$this->_post('markprice');
    $data['lowerprice']=$this->_post('lowerprice');
    $data['isbar']=$this->_post('isbar');
    $data['is_show']=$this->_post('is_show');
    $data['puser_num']=$this->_post('number');
    $data['address_info']=$this->_post('address_info');
    $data['introduce']=addslashes($this->_post('introduce'));
    $party=M('esw_wine_party');
    $info=$party->where("id=$id")->save($data);
    if($info){
      //echo "<script>alert('修改成功');window.loction.go(-1);</script>";
      echo "<script>alert('修改成功');window.location.href='index.php?app=Party&m=Manage&a=index'</script>";

    }else{
      echo "<script>alert('修改失败');window.location.href='index.php?app=Party&m=Manage&a=index'</script>";
    }
    //var_dump($data);
  }
   
   //通过ajax提交过来的id号。来查询地区
  public function getcity(){
    $id=$this->_post('id');
    $area=M('esw_area');
    $msg=$area->where("ParentID=$id")->select();
    $data=json_encode($msg);
    echo $data;         
  }
//查看详情
  public function select(){
     $id=$this->_get('aid');
     $info=M('esw_wine_party');
     $data=$info->where("id=$id")->find();
      $data['introduce']=htmlspecialchars_decode(($data['introduce']));
     //$data['introduce']=addslashes($data['introduce']);
     $data['privoce']=$this->countcity($data['province_id']);
     $data['city']=$this->countcity($data['city_id']);
     $data['county']=$this->countcity($data['area_id']);

     //var_dump($data);
     $this->assign('data',$data);
     $this->display('show');
  }
  //通过id获取城市
  public function countcity($id){
        $area=M('esw_area');
        $data=$area->where("AreaID=$id")->getField('AreaName');
        return $data;
  }
  // 添加酒会信息
  public function insert(){
    $data['add_user']=$_SESSION['admin_uid'];
    $data['update_user']=$this->_post('update_user');
    $data['add_time']=date('Y-m-d H:i:s',time());;
    $data['update_time']=date('Y-m-d H:i:s',time());
    $data['title']=$this->_post('title');
    $data['party_start']=strtotime($this->_post('party_start'));
    $data['party_end']=strtotime($this->_post('party_end'));
    $data['province_id']=$this->_post('privoce');
    $data['city_id']=$this->_post('city');
    $data['area_id']=$this->_post('county');
    $data['ismoney']=$this->_post('ismoney');
    $data['contactperson']=$this->_post('contactperson');
    $data['contactphone']=$this->_post('contactphone');
    $data['markprice']=$this->_post('markprice');
    $data['lowerprice']=$this->_post('lowerprice');
    $data['isbar']=$this->_post('isbar');
    $data['is_show']=$this->_post('is_show');
    $data['puser_num']=$this->_post('number');
    $data['address_info']=$this->_post('address_info');
    $data['introduce']=addslashes($this->_post('introduce'));
    array_filter($data);
      $party=M('esw_wine_party');
      $info=$party->data($data)->add();
      if($info){
       //$this->imageload($info);
        echo "<script>alert('添加成功');window.location.href='index.php?app=Party&m=Manage&a=index'</script>";
      }else{
        echo "<script>alert('添加失败');window.location.href='index.php?app=Party&m=Manage&a=add'</script>";
      }
    
    
  }
   //图片上传
  public function imageload($id){ 
       import("ORG.Net.UploadFile");
        //导入上传类
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize = 3292200;
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = "/upload/party/images/". $_SESSION['members']['uid'].'/';
        //设置需要生成缩略图，仅对图像文件有效
         $upload->thumb = true;
        // // 设置引用图片类库包路径
         $upload->imageClassPath = 'ORG.Util.Image';
        // //设置需要生成缩略图的文件后缀
         $upload->thumbPrefix = 's_';  //生产2张缩略图
        // //设置缩略图最大宽度
         $upload->thumbMaxWidth = '158';
        // //设置缩略图最大高度
         $upload->thumbMaxHeight = '100';
        // //设置上传文件规则
         $upload->saveRule = 'uniqid';
        //删除原图
       $upload->thumbRemoveOrigin = false;
        if (!$upload->upload()) {
            //捕获上传异常
            $this->error($upload->getErrorMsg());
        } else {
            //取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
          //$str=$uploadList[0]['savepath'].'s_'.$uploadList[0]['savename'];
            $str=$uploadList[0]['savename'];
            echo $str;
            
        }

    }
}

?>