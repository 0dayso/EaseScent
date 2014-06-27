<?php
class ImgAction extends BaseAction
{
    public function index(){
        $party = $_GET['id'];
	    $common = A('Common');
        $model = D('eswWinePartyPhoto');
        $photos = $model
            ->where('party_id = '.$party)
            ->order('id desc')
            ->select();
        import("ORG.Util.Image");   //图片类\
		$photo_data = array();
		foreach($photos as $k=>$v){
			$photo_data[$k]['id'] = $v['id'];
			$photo_data[$k]['image'] = $v['image'];
			$photo_data[$k]['dateline'] = $v['dateline'];
			$photo_data[$k]['count'] = $v['count'];
			$photo_data[$k]['description'] = $v['description'];
			$i_info = Image::getImageInfo($_SERVER['SERVER_NAME']."/Common/images/partyImg/".$v['party_id']."/".$v['image']."_s.".$v['description']);
			//var_dump($i_info);
			if($i_info['width'] <=158 and $i_info['height'] >100) {
				$photo_data[$k]['px'] = "height=100px";
			}elseif($i_info['height'] <=100 and $i_info['width'] >158) {
				$photo_data[$k]['px'] = "width=158px";
			}elseif($i_info['height'] >100 and $i_info['width'] >158) {
				if($i_info['width'] > $i_info['height']) {
					$photo_data[$k]['px'] = "width=158px";
				}else {
					$photo_data[$k]['px'] = "height=100px";
				}
			}elseif($i_info['height']==$i_info['width']) {
				$photo_data[$k]['px'] = "height=100px";
			}
        }	
        $seo_title = "酒会图片- 逸香网";
	    $seo_keywords = "酒会,酒会活动,酒会照片,酒会图片";
	    $seo_description = "酒会、酒会活动、酒会照片，酒会图片";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);    
            
        $this->assign('photos', $photo_data);
        $this->assign('photoCount', empty($photos) ? 0 : count($photos));
        $model = D('eswWineParty');
        $partyInfo = $model->where('id ='.$party)->find();
        $this->assign('partyInfo', $partyInfo);
        $this->assign('commentType', "jiuhuiimg");
        $this->display('wine_img');
    } 
    public function img(){
        $party = $_GET['id'];
        if(empty($party)){
            exit;
        }
        $page = $_GET['page'];
        $page = empty($page) ? 1 : $page;
        $model = D('eswWinePartyPhoto');
        $photos = $model
            ->where('party_id = '.$party)
            ->order('id desc')
            ->select();
		import("ORG.Util.Image");   //图片类\
		$photo_data = array();
		foreach($photos as $k=>$v){
			$photo_data[$k]['id'] = $v['id'];
			$photo_data[$k]['image'] = $v['image'];
			$photo_data[$k]['dateline'] = $v['dateline'];
			$photo_data[$k]['count'] = $v['count'];
			$photo_data[$k]['description'] = $v['description'];
			$i_info = Image::getImageInfo($_SERVER['SERVER_NAME']."/Common/images/partyImg/".$v['party_id']."/".$v['image']."_d.".$v['description']);
			//var_dump($i_info);
			if($i_info['width'] <=672 and $i_info['height'] >447) {
				$photo_data[$k]['px'] = "height=447px";
			}elseif($i_info['height'] <=447 and $i_info['width'] >672) {
				$photo_data[$k]['px'] = "width=672px";
			}elseif($i_info['height'] >447 and $i_info['width'] >672) {
				if($i_info['width'] > $i_info['height']) {
					$photo_data[$k]['px'] = "width=672px";
				}else {
					$photo_data[$k]['px'] = "height=447px";
				}
			}elseif($i_info['height']==$i_info['width']) {
				$photo_data[$k]['px'] = "height=447px";
			}
        }	
        $this->assign('photoInfo', $photo_data[$page - 1]);
        $this->assign('party',$party);
        $nextPage = $page + 1;
        $this->assign('nextPage', $nextPage);
        $lastPage = $page - 1;
        $this->assign('lastPage', $lastPage);
        $this->assign('page', $page);
        //得到酒会信息
        $model = D('eswWineParty');
        $partyInfo = $model->where('id = '.$party)->find();
        $this->assign('partyInfo',$partyInfo);
        //$this->assign('photos', $photos);
        $this->assign('photoCount', empty($photos) ? 0 : count($photos));
        
        $seo_title = "酒会图片- 逸香网";
	    $seo_keywords = "酒会,酒会活动,酒会照片,酒会图片";
	    $seo_description = "酒会、酒会活动、酒会照片，酒会图片";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);  

        $model = D('eswWineParty');
        $partyInfo = $model->where('id ='.$party)->find();
        $this->assign('partyInfo', $partyInfo);
	    $common = A("Common");
        $comment = $common->getPicComment($photos[$page - 1]['id']);
        /*foreach($comment as $k=>$v){
            $comment[$k]['comment'] = $common->getPicComment($photos[$page - 1]['id'], $v['id']);
            $comment[$k]['count'] = count($comment[$k]['comment']);
        }
        */
        foreach($comment['data'] as $k=>$v){
            $comment['data'][$k]['childCount'] = count($v['child']);
        } 
        $this->assign('comments', $comment['data']);
        $this->assign('commentCount', $comment['count']);
    	$common->getParam();
        $this->display('wine_img_con');

    }
    public function createImg(){
        $partyId = $_GET['id'];
        $sid = $_GET['sid'];
        if(empty($partyId)){
            echo 111;
            exit;
        }else{
            $model = D('eswWineParty');
            $data = $model->where('add_user ='.$sid.' and id='.$partyId)->find();
            if(empty($data)){
                echo 111;
                exit;
            }
        }
        $status =$this->partyupload("158","100",$info,$sid);
		ob_clean();
		if($status==110){
			echo 110;
		}else{
			if($status==1){
				$uc = A("Uc");
				$userinfo = $uc->getUcUserInfo($sid);
				$uid = $userinfo['data']['mid'];
		//		echo  $uid."/".$info[0]["thumbinfo"]["158*100"];
			}else{
		//		echo $status;
			}
		}
        $extension = $info[0]['extension'];
        $imageName = time();
        $data = array();
        $data['image'] = $imageName;
        $data['dateline'] = time();
        $data['count'] = 0;
        $data['description'] = $extension;
        $data['party_id'] = $partyId;
        $model = D('eswWinePartyPhoto');
        $d = $model->add($data);
        if($d){
            echo 10001;
        }
	    import("ORG.Util.Image");   //图片类
        $image = new Image();
        $partymain = "./Common/images/partyImg";
        $image->thumb('./party_tmp/'.$uid.'/pic.'.$extension ,'./party_tmp/'.$uid.'/s_pic.'.$extension,'',158,100);
        $image->thumb('./party_tmp/'.$uid.'/pic.'.$extension ,'./party_tmp/'.$uid.'/d_pic.'.$extension,'',672,447);
        if($this->mkdirs($partymain)){
    			$userpic = "./Common/images/partyImg/".$partyId;
    			if($this->mkdirs($userpic)){
    				copy("./party_tmp/".$uid."/s_pic.".$extension, $userpic."/".$imageName."_s.".$extension);
    				copy("./party_tmp/".$uid."/d_pic.".$extension, $userpic."/".$imageName."_d.".$extension);
    			}
    		}
        }
    public function deleteimg(){
    	$id = $_POST["id"];
    	if(!$_SESSION["members"]["uid"]){
    		echo 1000;
    		exit;
    	};
    	$photo = D("EswWinePartyPhoto");
    	$info = $photo->table("es_esw_wine_party_photo as pp")
    				  ->join("es_esw_wine_party as wp on wp.id = pp.party_id")
    				  ->where("pp.id=".$id." AND wp.add_user=".$_SESSION["members"]["uid"])
    				  ->limit(1)
    				  ->find();
    	if(!$info){
    		echo 1001;
    		exit;
    	}else{
    		$photo->delete($id);
    		$mypic = dirname(dirname(dirname(__FILE__)))."/Common/images/partyImg/".$info["party_id"]."/".$info["image"]."_s.".$info["description"];
    		if(is_file($mypic)){
    			unlink($mypic);
    			//删除对应大图
    			$bigpic = dirname(dirname(dirname(__FILE__)))."/Common/images/partyImg/".$info["party_id"]."/".$info["image"]."_d.".$info["description"];
    			if(is_file($bigpic)){
    				unlink($bigpic);
    			}
    			echo 1002;
    		}else{
    			echo 1003;
    		}
    	}
    }
    public function partyupload($width, $height, &$info,$sid)
	{
	    header("Content-Type:text/html; charset=utf-8");
		$uc = A("Uc");
		$userinfo = $uc->getUcUserInfo($sid);
	    if($userinfo){
	    	return 110;
	    	exit;
	    }else{
	    	$uid =$userinfo['data']['mid'];
	    }
	    import("ORG.Net.UploadFile");   //导入上传类
	    $upload = new UploadFile();    //实例化上传类 
	    $upload->maxSize = 1*1024*1024; //设置附件上传大小
	    $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); //设置附件上传类型
	    $upload->savePath = './party_tmp/'.$uid."/"; //设置附件上传目录
	    $upload->saveRule = 'pic';//固定名字上传
		//$upload->saveRule = 'uniqid';
	    $upload->thumb = true; //对上传图片进行缩略图处理
	    //$upload->thumbMaxWidth = $width.",672"; //缩略图最大宽度 如'50,200'代表2张缩略图的宽度
	    //$upload->thumbMaxHeight = $height.",447";//缩略图最大高度 如'50,200'代表2张缩略图的高度
	    //$upload->thumbPrefix = ''; //缩略图前缀
		//$upload->thumbSuffix = '_thumb_'.$width.'_'.$height.',_thumb_672_447';//缩略图后缀 如'_thumb,_thumb2'代表生成2张缩略图 
		//$upload->thumbSaveRule = 'uniqid';//缩略图命名规则
	    $upload->uploadReplace = true;  //存在同名文件是否被覆盖
	    if(!$upload->upload()){
		    return $upload->getErrorMsg(); //上传错误，提示错误信息
		}else{
		    $info = $upload->getUploadFileInfo(); 
		    return 1;
		}	
	}
    public function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || mkdir($dir, $mode)) return TRUE;
        if (!$this->mkdirs(dirname($dir), $mode)) return FALSE;
         return mkdir($dir, $mode);
    }
}
