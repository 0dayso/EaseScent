<?php
class CreateAction extends BaseAction{
	function index(){
      //  if(!$_SESSION['members']['uid'] || !($_SESSION['members']['email_valid'] == 1 || $_SESSION['members']['moblie_valid'] == 1)){
      //       $this->assign("jumpUrl","/index.php");
    		// $this->error("没有登录或者邮件没验证!") ;
      //   }

        $_SESSION['members']['uid']=223;
        $seo_title = "创建酒会- 逸香网";
	    $seo_keywords = "酒会,酒会活动,创建酒会";
	    $seo_description = "酒会、酒会活动、创建酒会";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);
		
		$province = $this->getRegion(1,'province_id','getcity',2);
		$city = $this->getRegion(2,'city_id','getarea',52);
		$area = $this->getRegion(52,'area_id','',500);
		$this->assign("province",$province);
		$this->assign("city",$city);
		$this->assign("area",$area);
		//我的地址
		$this->myaddress();
		$this->assign("action","insert");
		$this->display("index");
	}
	function myaddress(){
		$regionModel = D("PartyAddress");
		$regionNewModel = D("RegionNew");
		$regionAll = $regionModel->where("uid=".$_SESSION['members']['uid'])->select();
		foreach ($regionAll as $k=>$v){
			$regionAll[$k]["province_name"] = $regionNewModel->where("region_id=".$v["province_id"])->getField("region_name");
			$regionAll[$k]["city_name"] = $regionNewModel->where("region_id=".$v["city_id"])->getField("region_name");
			$regionAll[$k]["area_name"] = $regionNewModel->where("region_id=".$v["area_id"])->getField("region_name");
		}
		$this->assign("regionAll",$regionAll);
	}
	function changeAddress(){
		$regionModel = D("PartyAddress");
		$id = $_POST["id"];
		$regionAll = $regionModel->where("uid=".$_SESSION['members']['uid']." and id=".$id)->find();
		$province = $this->getRegion(1,'province_id','getcity',$regionAll["province_id"]);
		$city = $this->getRegion($regionAll["province_id"],'city_id','getarea',$regionAll["city_id"]);
		$area= $this->getRegion($regionAll["city_id"],'area_id','',$regionAll["area_id"]);
		echo json_encode(array("selectinfo"=>$province.'<span class="rad">'.$city.'<span class="rad">'.$area.'</span></span>',
								"addressinfo"=>$regionAll["address_info"]
								)); 
	}
	public function insert(){
		//判断所属
		 // if(!$_SESSION['members']['uid'] || !($_SESSION['members']['email_valid'] == 1 || $_SESSION['members']['moblie_valid'] == 1)){
   //          $this->assign("jumpUrl","/index.php");
   //  		$this->error("没有登录或者邮件没验证!") ;
   //      }
        $verify = $_POST["verify_qt"];
		//echo $_SESSION["verify"];
		if(md5($verify)!=$_SESSION["verify"]){
			$this->error("输入的验证码错误") ;
		}
		$data = array();
    	$ewpModel = D("EswWineParty");
    	$data['title'] = $_POST["title"];
    	//$data['person_sum'] = $_POST["person_sum"];
    	$data['p_type'] = 0;
    	$data["date_type"] = $_POST["date_type"];
    	if($_POST['date_type']==1){
    		$party_start = $_POST["party_start1_date"].' '.$_POST["party_start1_time"];
    		$party_end = $_POST["party_start1_date"].' '.$_POST["party_end1_time"];
    	}elseif ($_POST['date_type']==2){
    		$party_start = $_POST["party_start2_date"].' '.$_POST["party_start2_time"];
    		$party_end = $_POST["party_end2_date"].' '.$_POST["party_end2_time"];
    	}
    	$data['party_start'] = strtotime($party_start);
    	$data['party_end'] = strtotime($party_end);
    	$data['province_id'] = $_POST["province_id"];
    	/*if($_POST["province_id"]==3440){
    		$data['city_id'] = 3440;
    	}else{
    	*/
    		$data['city_id'] = $_POST["city_id"];
    	//}
    	$data['area_id'] = $_POST["area_id"];
    	$data['address_info'] = $_POST["address_info"];
    	$data['mapaddress'] = $_POST["mapaddress"];
    	$data['ismoney'] = $_POST["ismoney"];
    	$data['lowerprice'] = isset($_POST["lowerprice"])?-1:$_POST["lowerprice"];
    	$data['markprice'] = $_POST["markprice"];
    	$data['contactperson'] = $_POST["contactperson"];
    	$data['contactphone'] = $_POST["contactphone"];
    	$data['introduce'] = htmlspecialchars($_POST["introduce"]);
    	$data['isbar'] = !isset($_POST["isbar"])?0:1;
    	$data["is_show"] = 1;
    	$pictype = explode(".",$_POST["pictype"]);
    	$data["pictype"] = $pictype[1];
    	$data["add_user"] = $_SESSION["members"]["uid"];
		$data["add_time"] = date("Y-m-d H:i:s");
		$data["update_time"] = date("Y-m-d H:i:s");
        $data['address_last'] = $_POST['address_last'];
        //过滤词
        $fil = A('FiltrateWords');
        $stat = $fil->filtrate($data['introduce'], 1);
        if(!$stat){
        	$this->error("内容介绍还有非法词，请检查!");
        }
        
		$id = $ewpModel->add($data);
      // 通过返回的id来复制图片到
       if($id){
            // $partymain = "__UPLOAD__/party/images/temp/";
            // if($this->mkdirs($partymain)){
            //     $userpic = "__UPLOAD__/party/images/".$id;
            //     if($this->mkdirs($userpic)){
                    $dir=CODE_RUNTIME_PATH.'/upload/party/images/';
                    $dirpath=$dir.$id;
                    mkdir($dirpath);
                    copy($dir.'temp/s_'.$_POST['pictype'], $dirpath.'/s_.'.$pictype[1]);

                    copy($dir.'temp/d_'.$_POST['pictype'], $dirpath.'/d_.'.$pictype[1]);
                    
    				// copy("./party_tmp/".$_SESSION['members']["uid"]."/pic_thumb_318_212.".$pictype[1], $userpic."/pic_thumb_318_212.".$pictype[1]);
    				// copy("./party_tmp/".$_SESSION['members']["uid"]."/pic_thumb_345_217.".$pictype[1], $userpic."/pic_thumb_345_217.".$pictype[1]);
    			// }
    		 //  }
    		//保存地图
    		/*
    		$common = A("Common");
    		$path = "./Common/images/partycreate/".$id;
    		$mapaddress = $data['mapaddress'];
    		$common->DownMap($mapaddress,$path."/googleMap.jpg");
    		*/
    		
    		//同步酒会地址
    		$addressModel = D("PartyAddress");
    		$Condition["province_id"] = $data['province_id'];
    		$Condition["city_id"] = $data['city_id'];
    		$Condition["area_id"] = $data['area_id'];
    		$Condition["address_info"] = $data['address_info'];
    		$info = $addressModel->where($Condition)->find();
    		if(!$info){
    			$addressModel->add($Condition);	
    		}
			$this->assign("jumpUrl","/index.php/User/my");
    		$this->success("发布成功!");
    	}else{
    		$this->error("添加失败，请重试!");
    	}
		
	}
	public function edit(){
		$id = $_GET["id"];
		//判断所属
		if(!$_SESSION['members']["uid"]){
    		$this->assign("jumpUrl","/index.php");
    		$this->error("很抱歉您还没有登录，请先登录!");
    	}
		$ewpModel = D("EswWineParty");
		$info = $ewpModel->where("id=".$id." and add_user=".$_SESSION["members"]["uid"])->find();
		if(!$info){
			$this->assign("jumpUrl","/index.php/User/My");
    		$this->error("无权操作!");
		}
		
    	$common = A("Common");
		$province = $this->getRegion(1,'province_id','getcity',$info["province_id"]);
    	if($info["city_id"]>0){
			$city = $this->getRegion($info["province_id"],'city_id','getarea',$info["city_id"]);
    	}
    	if($info["area_id"]>0){
			$area= $this->getRegion($info["city_id"],'area_id','',$info["area_id"]);
    	}
    	$info = $ewpModel->where("id=".$id)->find();
    	$info["party_start1_date"] = date("Y-m-d",$info["party_start"]);
    	$info["party_end1_date"] = date("Y-m-d",$info["party_end"]);
    	$info["party_start_time"] = date("H:i",$info["party_start"]);
    	$info["party_end_time"] = date("H:i",$info["party_end"]);
    	
    	$info["partypic"] = $id."/s_.".$info['pictype'];
		$info["pic_type"] = "s_.".$info['pictype'];
   
    	$seo_title = "修改酒会- 我的酒会";
	    $seo_keywords = "酒会,酒会活动,创建酒会";
	    $seo_description = "酒会、酒会活动、创建酒会";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);
        
    	$this->assign("info",$info);
        $this->assign("province",$province);
        $this->assign("city",$city);
        $this->assign("area",$area);
		$this->assign("id",$id);
		$this->assign("action","update");
		$this->display("index");
	}
	public function update(){
		$id = $_POST["id"];
		//判断所属
		if(!$_SESSION['members']["uid"]){
    		$this->assign("jumpUrl","/index.php");
    		$this->error("很抱歉您还没有登录，请先登录!");
    	}
		$ewpModel = D("EswWineParty");
		$info = $ewpModel->where("id=".$id." and add_user=".$_SESSION["members"]["uid"])->find();
		if(!$info){
			$this->assign("jumpUrl","/index.php/User/My");
    		$this->error("无权操作!");
		}
		//$common = A("Common");
		//$sum = $common->getpartyUserCount($id);
        $commonModel = A("Common"); 
        //是否参加过
        $params_1 = array( 
                'uid'=> $_SESSION['members']['mid'],
                'fid' => $id, 
                'type' =>"jiuhui"
                ); 
        $result_1 = $commonModel->apiComment("isfl",$params_1);
        $result_1 = json_decode($result_1,true);
		if($result_1["data"]){
			$this->assign("jumpUrl","/index.php/User/My");
    		$this->error("很抱歉，您的酒会已经有人关注，不能自行修改，请联系工作人员!");
		}
		$verify = $_POST["verify_qt"];
		//echo $_SESSION["verify"];
		if(md5($verify)!=$_SESSION["verify"]){
			$this->error("输入的验证码错误") ;
		}
		$data = array();
    	$ewpModel = D("EswWineParty");
		$data['title'] = $_POST["title"];
    	//$data['person_sum'] = $_POST["person_sum"];
    	$data['p_type'] = 0;
    	$data["date_type"] = $_POST["date_type"];
    	if($_POST['date_type']==1){
    		$party_start = $_POST["party_start1_date"].' '.$_POST["party_start1_time"];
    		$party_end = $_POST["party_start1_date"].' '.$_POST["party_end1_time"];
    	}elseif ($_POST['date_type']==2){
    		$party_start = $_POST["party_start2_date"].' '.$_POST["party_start2_time"];
    		$party_end = $_POST["party_end2_date"].' '.$_POST["party_end2_time"];
    	}
    	$data['party_start'] = strtotime($party_start);
    	$data['party_end'] = strtotime($party_end);
    	$data['province_id'] = $_POST["province_id"];
    	/*if($_POST["province_id"]==3440){
    		$data['city_id'] = 3440;
    	}else{
    	*/
    		$data['city_id'] = $_POST["city_id"];
    	//}
    	$data['area_id'] = $_POST["area_id"];
    	$data['address_info'] = $_POST["address_info"];
    	$data['mapaddress'] = $_POST["mapaddress"];
    	$data['ismoney'] = $_POST["ismoney"];
    	$data['lowerprice'] = isset($_POST["lowerprice"])?-1:$_POST["lowerprice"];
    	$data['markprice'] = $_POST["markprice"];
    	$data['contactperson'] = $_POST["contactperson"];
    	$data['contactphone'] = $_POST["contactphone"];
    	$data['introduce'] = htmlspecialchars($_POST["introduce"]);
    	$data['isbar'] = !isset($_POST["isbar"])?0:1;
    	$data["is_show"] = 1;
    	$pictype = explode(".",$_POST["pictype"]);
    	$data["pictype"] = $pictype[1];
    	$data["add_user"] = $_SESSION["members"]["uid"];
		$data["add_time"] = date("Y-m-d H:i:s");
		$data["update_time"] = date("Y-m-d H:i:s");
        $data['address_last'] = $_POST['address_last'];
		if($_FILES["partypic"]["name"]!=""){
    		$UploadPic = A('UploadPic');
			//$UploadPic->savePath("./images/books/");
			//$UploadPic->_newupload("377","283",&$info,"wineparty");
			$UploadPic->_partyupload("377","283",$info);
			$data["party_pic"] = $info[0]["thumbinfo"]["377*283"];
		}
		//过滤词
        $fil = A('FiltrateWords');
        $stat = $fil->filtrate($data['introduce'], 1);
        if(!$stat){
        	$this->error("内容介绍还有非法词，请检查!");
        }
    	if($ewpModel->where("id=".$id)->save($data)){
    		// $partymain = "./Common/images/partycreate";
    		// if($this->mkdirs($partymain)){
    		// 	$userpic = "./Common/images/".$id;
    		// 	if($this->mkdirs($userpic)){
      //               copy("./party_tmp/".$_SESSION['members']["uid"]."/s_".$_POST['pictype'], $userpic."/s_.".$pictype[1]);
    		// 		copy("./party_tmp/".$_SESSION['members']["uid"]."/d_".$_POST['pictype'], $userpic."/d_.".$pictype[1]);
    		// 	}
             $dir=CODE_RUNTIME_PATH.'/upload/party/images/';
                    $dirpath=$dir.$id;
                    mkdir($dirpath);
                    copy($dir.'temp/s_'.$_POST['pictype'], $dirpath.'/s_.'.$pictype[1]);

                    copy($dir.'temp/d_'.$_POST['pictype'], $dirpath.'/d_.'.$pictype[1]);
    		//}
    		//保存地图
    		/*
    		$common = A("Common");
    		$path = "./Common/images/partycreate/".$id;
    		$mapaddress = $data['mapaddress'];
    		$common->DownMap($mapaddress,$path."/googleMap.jpg");
    		*/
    		//同步酒会地址
    		$addressModel = D("PartyAddress");
    		$Condition["province_id"] = $data['province_id'];
    		$Condition["city_id"] = $data['city_id'];
    		$Condition["area_id"] = $data['area_id'];
    		$Condition["address_info"] = $data['address_info'];
    		$info = $addressModel->where($Condition)->find();
    		if(!$info){
    			$addressModel->add($Condition);	
    		}
			$this->assign("jumpUrl","/index.php/User/My");
    		$this->success("修改成功!");
    	}else{
    		$this->error("修改失败，请重试!");
    	}
	}
	function checkIntroduce(){
		$introduce = $_POST["introduce"];
		$fil = A('FiltrateWords');
        $stat = $fil->filtrate($introduce, 1);
        if(!$stat){
        	echo 1;
        }
        echo 0;
	}
  /*  public function partypic(){
		$sid = $_GET["sid"];//取得sid 解决swf 不支持session失效问题
		$status = $this->_partyupload("158","100",$info,$sid);
        ob_clean();
		if($status==110){
		    $stat['extension'] = 110;
            $stat['savename'] = '';
            echo json_encode($stat);
		}else{
			if($status==1){
                $extension = $info[0]["extension"];
		    	$uc = A("Uc");
				$userinfo = $uc->getUcUserInfo($sid);
				$uid = $userinfo['data']['mid'];
                import("ORG.Util.Image");   //图片类\
                $image = new Image();
               	$image->thumb("__PUBLIC__/party/upload/".$uid.'/'.$info[0]['savename'] ,"__PUBLIC__/party/upload/".$uid.'/s_'.$info[0]['savename'],'',418,358);
               //	$image->thumb('./party_tmp/'.$uid.'/'.$info[0]['savename'] ,'./party_tmp/'.$uid.'/sb_'.$info[0]['savename'],'',418,358);
                $stat['extension'] = $extension;
                $stat['savename'] = $info[0]['savename'];
                $stat['show_pic'] = "__PUBLIC__/party/upload/".$uid.'/sb_'.$info[0]['savename'];
                echo json_encode($stat);
			}else{
				echo $stat;
			}
		}
		//$data["party_pic"] = $info[0]["thumbinfo"]["377*283"];
	}*/
    public function save(){
        $name = time();
        $srcX = $_POST['x'];
	    $srcY = $_POST['y'];
	    $srcWidth = $_POST['width'];
	    $srcHeight = $_POST['height'];
	    $srcFile = $_POST['pic'];
        $imagePath = "__PUBLIC__/party/upload/".$_SESSION["members"]["uid"].'/';
        if($this->mkdirs($imagePath)){
		   $src ="__PUBLIC__/party/upload/".$_SESSION["members"]["uid"].'/'.$srcFile;
		   $dst = $imagePath.'d_'.$srcFile;
	        $a = $this->makeThumb($src,$dst,max(345,min(345,$srcWidth)),217,0,0,$srcX,$srcY,$srcWidth,$srcHeight);
            if($a){ 
                $s_dst = $imagePath.'s_'.$srcFile;
                $b = $this->makeThumb($src,$s_dst,158,100,0,0,$srcX,$srcY,$srcWidth,$srcHeight);
            }
        }
        if(!empty($a) && !empty($b)){
            echo basename($b);
        }else{
            echo 1000011;
        }
        
    
    
    }
	/*public function partypic(){
		$sid = $_GET["sid"];//取得sid 解决swf 不支持session失效问题
		$status = $this->_partyupload("158","100",&$info,$sid);
		ob_clean();
		if($status==110){
			echo 110;
		}else{
			if($status==1){
				$uc = A("Uc");
				$userinfo = $uc->getUcUserInfo($sid);
				$uid = $userinfo['data']['mid'];
				echo  $uid."/".$info[0]["thumbinfo"]["158*100"];
			}else{
				echo $status;
			}
		}
		//$data["party_pic"] = $info[0]["thumbinfo"]["377*283"];
	}
  */  
	public function map(){
		$this->display("map");
	}
	public function getcitylist(){
        $id = $_POST["province_id"];
        $common = A("Common");
        $citylist = $common->getRegion($id,"city_id","getarea");
        echo $citylist;
    }
    public function getarealist(){
        $id = $_POST["city_id"];
        $common = A("Common");
        $arealist = $common->getRegion($id,"area_id");
        echo $arealist;
    }
    public function verify() {  
        import("ORG.Util.Image"); 
          Image::buildImageVerify(); 

   }
    public function _partyupload($width, $height, &$info,$sid)
	{
	    header("Content-Type:text/html; charset=utf-8");
	    $uc = A("Uc");
	    $userinfo = $uc->getUcUserInfo($sid);
	    if(!$userinfo){
	        return 110;
	    }else{
	    	$uid =$userinfo['data']['mid'];
	    }
	    import("ORG.Net.UploadFile");   //导入上传类
	    $upload = new UploadFile();    //实例化上传类 
	    $upload->maxSize = 1*1024*1024; //设置附件上传大小
	    $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); //设置附件上传类型
	    $upload->savePath = "__PUBLIC__/party/upload/". $uid .'/'; //设置附件上传目录
        $this->mkdirs($upload->savePath);
	    //$upload->saveRule = 'pic';//固定名字上传
		$upload->saveRule = 'uniqid';
	    $upload->thumb = true; //对上传图片进行缩略图处理
	    /*$upload->thumbMaxWidth = $width.",345,318"; //缩略图最大宽度 如'50,200'代表2张缩略图的宽度
	    $upload->thumbMaxHeight = $height.",217,212";//缩略图最大高度 如'50,200'代表2张缩略图的高度
	    $upload->thumbPrefix = ''; //缩略图前缀
		$upload->thumbSuffix = '_thumb_'.$width.'_'.$height.',_thumb_345_217,_thumb_318_212';//缩略图后缀 如'_thumb,_thumb2'代表生成2张缩略图 
        */
		$upload->thumbSaveRule = 'uniqid';//缩略图命名规则
	    $upload->uploadReplace = true;  //存在同名文件是否被覆盖
	    if(!$upload->upload()){
		    return $upload->getErrorMsg(); //上传错误，提示错误信息
		}else{
		    $info = $upload->getUploadFileInfo(); 
		    return 1;
		}
	}
	public function getRegion($id,$tagid,$call="",$city_id=-1)
	{
		$Model_city=D();
		$sql_city="SELECT region_id,region_name FROM es_region_new  WHERE parent_id='".$id."'";
		$res_city=$Model_city->query($sql_city);
        switch($tagid){
            case 'province_id':
                $name = '请选择省';
                break;
            case 'city_id':
                $name = '请选择城市';
                break;
            case 'area_id':
                $name = '请选择地区';
                break; 
        }
        if(!empty($call)) $call = "onchange='".$call."()'";
		$html='<select name="'.$tagid.'" '.$call.'  id="'.$tagid.'"><option value="0">--'.$name.'--</option>';
		foreach($res_city as $key=>$val)
		{
			$html.='<option value="'.$val['region_id'].'"'.(($val['region_id']==$city_id)?("selected=selected"):"").'>'.$val['region_name'].'</option>';
		}
		$html.='</select>';
	    return $html;
	}
    public function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || mkdir($dir, $mode)) return TRUE;
        if (!$this->mkdirs(dirname($dir), $mode)) return FALSE;
         return mkdir($dir, $mode);
    }
   /* 
     //保存裁剪的图片
   public function save(){
        $name = time();
        $srcX = $_POST['x'];
	    $srcY = $_POST['y'];
	    $srcWidth = $_POST['width'];
	    $srcHeight = $_POST['height'];
	    $srcFile = $_POST['pic'];
        $imagePath = $_SERVER['DOCUMENT_ROOT'].'/party_tmp/'.$_SESSION["members"]["uid"].'/';
        if($this->mkdirs($imagePath)){
		    $src = $_SERVER['DOCUMENT_ROOT'].'/party_tmp/'.$_SESSION["members"]["uid"].'/'.$srcFile;
		    $dst = $imagePath.'d_'.$srcFile;
	        $a = $this->makeThumb($src,$dst,max(318,min(345,$srcWidth)),211,0,0,$srcX,$srcY,$srcWidth,$srcHeight);
            if($a){ 
                $s_dst = $imagePath.'s_'.$srcFile;
                $b = $this->makeThumb($src,$s_dst,158,100,0,0,$srcX,$srcY,$srcWidth,$srcHeight);
            }
        }
        if(!empty($a) && !empty($b)){
            echo 1;
        }else{
            echo 1000011;
        }
        
    
    
    }
    */
    public function makeThumb($srcfile,$dstfile,$thumbwidth,$thumbheight,$maxthumbwidth=0,$maxthumbheight=0,$src_x=0,$src_y=0,$src_w=0,$src_h=0) {
		if (!is_file($srcfile)) {
			return '';
		}
		$tow = (int) $thumbwidth;
		$toh = (int) $thumbheight;
		if($tow < 30) {
			$tow = 30;
		}
		if($toh < 30) {
			$toh = 30;
		}

		$make_max = 0;
		$maxtow = (int) $maxthumbwidth;
		$maxtoh = (int) $maxthumbheight;
		if($maxtow >= 300 && $maxtoh >= 300){
			$make_max = 1;
		}
		$im = '';
		if($data = getimagesize($srcfile)) {
			if($data[2] == 1) {
				$make_max = 0;
				if(function_exists("imagecreatefromgif")) {
					$im = imagecreatefromgif($srcfile);
				}
			} elseif($data[2] == 2) {
				if(function_exists("imagecreatefromjpeg")) {
					$im = imagecreatefromjpeg($srcfile);
				}
			} elseif($data[2] == 3) {
				if(function_exists("imagecreatefrompng")) {
					$im = imagecreatefrompng($srcfile);
				}
			}
		}
		if(!$im) return '';

		$srcw = ($src_w ? $src_w : imagesx($im));
		$srch = ($src_h ? $src_h : imagesy($im));

		$towh = $tow/$toh;
		$srcwh = $srcw/$srch;
		if($towh <= $srcwh){
			$ftow = $tow;
			$ftoh = round($ftow*($srch/$srcw),2);
		}
		else{
			$ftoh = $toh;
			$ftow = round($ftoh*($srcw/$srch),2);
		}
		
		if($make_max){
			$maxtowh = $maxtow/$maxtoh;
			if($maxtowh <= $srcwh){
				$fmaxtow = $maxtow;
				$fmaxtoh = round($fmaxtow*($srch/$srcw),2);
			}
			else{
				$fmaxtoh = $maxtoh;
				$fmaxtow = round($fmaxtoh*($srcw/$srch),2);
			}

			if($srcw <= $maxtow && $srch <= $maxtoh){
				$make_max = 0;    	
			}
		}
		$maxni = '';
		//if($srcw >= $tow || $srch >= $toh) {
			if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && ($ni = imagecreatetruecolor($ftow, $ftoh))) {
				imagecopyresampled($ni, $im, 0, 0, $src_x, $src_y, $ftow, $ftoh, $srcw, $srch);
							if($make_max && ($maxni = imagecreatetruecolor($fmaxtow, $fmaxtoh))) {
					imagecopyresampled($maxni, $im, 0, 0, $src_x, $src_y, $fmaxtow, $fmaxtoh, $srcw, $srch);
				}
			} elseif(function_exists("imagecreate") && function_exists("imagecopyresized") && ($ni = imagecreate($ftow, $ftoh))) {
				imagecopyresized($ni, $im, 0, 0, $src_x, $src_y, $ftow, $ftoh, $srcw, $srch);
							if($make_max && ($maxni = imagecreate($fmaxtow, $fmaxtoh))) {
					imagecopyresized($maxni, $im, 0, 0, $src_x, $src_y, $fmaxtow, $fmaxtoh, $srcw, $srch);
				}
			} else {
                
				return '';
			}
			if(function_exists('imagejpeg')) {
				imagejpeg($ni, $dstfile, 100);
							if($make_max && $maxni) {
					imagejpeg($maxni, $srcfile, 100);
				}
			} elseif(function_exists('imagepng')) {
				imagepng($ni, $dstfile);
							if($make_max && $maxni) {
					imagepng($maxni, $srcfile);
				}
			}
			imagedestroy($ni);
			if($make_max && $maxni) {
				imagedestroy($maxni);
			}
		//}
		imagedestroy($im);
		if(!is_file($dstfile)) {
			return '';
		} else {
			return $dstfile;
		}
	}	
	
    public function uploadpicture(){

           import("ORG.Net.UploadFile");
        //导入上传类
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize = 3292200;
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
       // $upload->savePath = "../upload/party/images/temp/".$_SESSION['members']["uid"].'/';
         $upload->savePath = "../upload/party/images/temp/";
        //设置需要生成缩略图，仅对图像文件有效
         $upload->thumb = true;
        // // 设置引用图片类库包路径
         $upload->imageClassPath = 'ORG.Util.Image';
        // //设置需要生成缩略图的文件后缀
        $upload->thumbPrefix = 'd_,s_';  //生产2张缩略图
        // //设置缩略图最大宽度
         $upload->thumbMaxWidth = '343,158';
        // //设置缩略图最大高度
         $upload->thumbMaxHeight = '217,100';
         
         //$uplod->thumbFile='_';
        // //设置上传文件规则
         $upload->saveRule ='uniqid';
        //删除原图
       $upload->thumbRemoveOrigin = true;
        if (!$upload->upload()) {
            //捕获上传异常
            $this->error($upload->getErrorMsg());
        } else {
            //取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
             //$this->ajaxReturn($uploadList,"新增成功！",1,'json');
        //$str='<img src="'.$uploadList[0]['savepath'].$uploadList[0]['savename'].'" value="'.$uploadList[0]['savename'].'"/>';
            //$msg['name']=$uploadList[0]['savename'];
            //$msg['path']=$uploadList[0]['savepath'];
          $str=$uploadList[0]['savename'];
           //var_dump(CODE_RUNTIME_PATH);
            echo $str;
            
        }
        
        
       
    }



}
?>
