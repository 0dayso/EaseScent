<?php

class DetailAction extends BaseAction
{
    
    public function index(){
		
		
        if(empty($_GET['id']))
            exit;
        else
            $id = $_GET['id'];
        $common = A("Common");
        // 得到单条酒会详细信息
        $model = D();
        $regionModel = D("RegionNew");
        $res = $model
            ->table('es_esw_wine_party')
            ->where('id = '.$id.' and is_show = 1')
            ->limit(1)
            ->find();
        if(empty($res)){
            exit;
        }
        if($res['party_end'] > time()){
            $res['is_end'] = '0';
        }else{
            $res['is_end'] = '1';
        }
        $res['introduce'] = nl2br($res['introduce']); 
        $res['insertUser'] = $common->getpartyUserCount($res['id']);
        $res['pType'] = ($res['p_type'] == 0) ? '酒会' : '展会';
        $res['cityName'] = $common->getCityInfo($res['city_id']);
        $res['markprice'] = ($res['markprice'] == 0) ? '免费' : $res['markprice'].'元/位';
        $res['cityname'] = $regionModel->where("region_id=".$res['city_id'])->getField("region_name");
        if($res['lowerprice']==-1) {
			$res['lowerprice'] = '无';
		}elseif($res['lowerprice'] !=-1 and $res['lowerprice']!=0) {
			$res['lowerprice'] = $res['lowerprice'].'元/位';
		}elseif($res['lowerprice'] == 0) {
			$res['lowerprice'] = '免费';
		}
		//$res['lowerprice'] = ($res['lowerprice'] == 0) ? '免费' : $res['lowerprice'].'元/位';
        //$res['contactperson'] = mb_substr($res['contactperson'], 0, 4, 'utf-8');
        $res['start']  = $common->formartTime($res['party_start']);
        $res['end']  = $common->formartTime($res['party_end']);
		if(file_exists('http://'.$_SERVER['SERVER_NAME'].'/Common/images/partycreate/'.$id.'/d_.jpg')) {
			$this->assign('partyimg', 'http://'.$_SERVER['SERVER_NAME'].'/Common/images/partycreate/'.$id.'/d_.jpg');
		}else {
			$this->assign('partyimg', 'http://'.$_SERVER['SERVER_NAME'].'/Common/images/nopartyimg.jpg');
		}
        $this->assign('partyInfo', $res);

        //参加该酒会用户信息 
        
        $common = A("Common");
        /*
        $model = D('eswWinePartyUser');
        $user_info = $model
            ->table("es_esw_wine_party_user p")
            ->join('es_users u on p.uid = u.uid')
            ->field('u.nick_name,p.uid,p.party_id,p.id,u.user_name')
            ->where('p.party_id = '.$res['id'])
            ->order('p.id desc')
            ->limit(6)
            ->findAll();
        foreach($user_info as $key=>$value){
            if($value['uid'] == $_SESSION['members']['uid'] ){
                $this->assign('is_insert' ,'1');
            }
            $user_info[$key]["face"] = $this->getUcFace($this->getMid($value['uid']));
        }*/
        $params = array(
	                'id' => $id,
	           		'type' =>"jiuhui",
	           		'count' => 6
	                );
	    $commonModel = A("Common");
		$result = $commonModel->apiComment("lfl",$params);
        $result = json_decode($result,true);
        $userinfo = $result["data"]["data"];
       
        foreach ($userinfo as $k=>$v){
        	$userinfo[$k]["uid"] = $this->getUid($v["m_id"]);
        }
        $joincount = $result["data"]["count"];
        $this->assign('userInfo',$userinfo);
        $this->assign('joincount',$joincount);
		//是否参加过
		  $params_1 = array(
		  				'uid'=>	$_SESSION['members']['mid'],
	                	'fid' => $id,
	           			'type' =>"jiuhui"
	                );
		$result_1 = $commonModel->apiComment("isfl",$params_1);
        $result_1 = json_decode($result_1,true);
        if($result_1["data"]){
        	$this->assign('is_insert', 1);
        }
        
        // 得到该酒会的上传图片
        $model = D('eswWinePartyPhoto');
        $photo_info = $model
            ->where('party_id = '.$res['id'])
            ->order('id desc')
            ->limit(8)
            ->select();
        $count = $model
            ->where('party_id = '.$res['id'])
            ->count();
		import("ORG.Util.Image");   //图片类\
        //$image = new Image();
		$photo_data = array();
        foreach($photo_info as $k=>$v){
            $photoCommentInfo = $common->getPicComment($v['id']);
            $photo_info[$k]['commentCount'] = count($photoCommentInfo);
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
		
        $this->assign('photoCount', $count);
        $this->assign('photoInfo', $photo_data);
        /*
        $comment = $common->getCommon($res['id']);
        //评论个数
        //$this->assign('commentCount', count($comment));
        foreach($comment as $k=>$v){
            $comment[$k]['comment'] = $common->getCommon($res['id'], $v['id']);
            $comment[$k]['count'] = count($comment[$k]['comment']);
        }
        */
        $comments = $this->getComment($id);
        foreach($comments['data'] as $k=>$v){
            $comments['data'][$k]['childCount'] = count($v['child']);
        
        }      
        $this->assign('commentCount', $comments['count']);
        //$this->assign('comment', $comment);
        $this->assign('comments', $comments['data']);
        $this->assign('commentType', "jiuhui");

        $seo_title = $res["title"]."- 逸香网";
	    $seo_keywords = $res["title"].",".$res['cityname']."葡萄酒酒会,酒会,葡萄酒酒会";
	    $seo_description = $res["title"].":". str_replace(" ","",mb_substr(strip_tags($res["introduce"]),0, 200, 'utf-8'));
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);
        
	    //广告内容
		$Common = A('Common');
        $this->assign("ad_1607",$Common->getAd(1607));
       	
	    $common->getParam();
	    $partyInCount = $common->getPartyNum($res['id']);
	    $this->assign('partyInCount', $partyInCount);
		//获取城市id
		$c = trim($_GET["c"]);//城市
		if(isset($c) and is_numeric($c) and ($c!=0)) {
			
			$this->assign('c', $c);
			$this->assign('c_value', $this->getcname($c));
			
		}else {
			$this->assign('c', 0);
			$this->assign('c_value', '全国');
		}
      
	    $this->display('index');
    }
    public function getPhone(){
        $common = A("Common");
        $common->createPhone($_GET['phone']); 
    }
    // 删除活动
    public function setShow(){
        $id = $_POST['id'];
        $model = D('eswWinePartyUser');
        $data = $model
            ->where('party_id = '.$id)
            ->find();
        if(!empty($data)){
            $status['status'] = '10000001';
            echo json_encode($status);
            exit;
        }
        
        $model = D('eswWineParty');
        $data['is_show'] = 4;
        $res = $model
            ->where('id = '.$id.' and add_user ='.$_SESSION['members']['uid'])
            ->data($data)
            ->save();
        if($res){
            $status['status'] = '1';
            echo json_encode($status);
        }else{
            $status['status'] = '1000001';   // 关闭酒会失败
            echo json_encode($status);
        }
    }

    // 发表用户评论
    public function insert_comment(){
        $pid = isset($_POST['pid'])?$_POST['pid']:0;
        $verify = $_POST["verify"];
		//echo Session::get('verify');
		if(md5($verify)!=Session::get('verify') && $pid == 0){
			echo "200011";
			exit;
		}
	    $common = A('Common');
        $party_id = $_POST['party_id'];
        $content = $common->makeFace($_POST['content']);
        $img = $_POST['img'];
        $img = empty($img) ? 0 : $img;
        
        $bu = A("UserBlack");
        $st = $bu->filtrate();
        if($st){
        	echo json_encode(array('status'=>1000004,'message'=>'您已经被管理员加入黑名单了!'));
            return true;
        }

        /*$fil = A('FiltrateWords');
        $stat = $fil->filtrate($content, 1);
        if(!$stat){
           echo json_encode(array('status'=>1000003,'message'=>'有违禁词'));
            return true;
        }*/
        //$model = D('eswWinePartyComment');
	    //$data['img'] = empty($img) ? 0 : $img;	
        //$data['content'] = $content;
        //$data['dateline'] = time();
        //$data['pid'] = $pid;
    	//$data['photo_id'] = empty($img) ? 0 : 1;
        //$data['party_id'] = empty($img) ? $party_id : 0;
        //$data['uid'] = $_SESSION['members']['uid'];
        //$data['username'] = $_SESSION['members']['username'];
        //$data['nickname'] = $_SESSION['members']['nickname'];
	    //$data['fuid'] = 0;
	    //$data['c_type'] = 0;
	    /*
        if(empty($img)){
            $auth =  md5('MTMzNzc0NDIxMkpJVUhVSQ==jiuhui');
            $url  = 'http://comment.winesino.com/index.php/jiuhui/set';      
            $type = 'jiuhui';
        }else{
            $auth =  md5('MTMzNzc0NDIxMkpJVUhVSUlNRw==jiuhuiimg');
            $url  = 'http://comment.winesino.com/index.php/jiuhuiimg/set';
            $type = 'jiuhuiimg';
        }
        $params = array(
                'uid'      => $_SESSION['members']['uid'],
                'username' => $_SESSION['members']['username'],
                'nickname' => $_SESSION['members']['nickname'],
                'tousername' => empty($_POST['tousername']) ? '' : $_POST['tousername'],
                'tonickname' => empty($_POST['tonickname']) ? '' : $_POST['tonickname'],
                'touid'    => empty($_POST['touid']) ? 0 : $_POST['touid'],
                'content'  => $content,
                'pid'      => $pid,
                'auth'     => $auth,
                'relation' => empty($img) ? $party_id : $img,
                'sid'      => $_SESSION['members']['sid'],
                'type'     => $type
                );
        $ch = curl_init();                                     
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);  
        curl_exec($ch);
        curl_close($ch);
        exit;*/
	    $commonModel = A("Common");	
	    //$type = $img>0?"jiuhui":"jiuhuiimg";
	    if($img){
	    	$type = "jiuhuiimg";
	    	$relation = $img;
	    }else{
	    	$type = "jiuhui";
	    	$relation = $party_id;
	    }
	    if($pid==0){
	    	//评论api
			$params = array(
	                'uid' => $_SESSION['members']['mid'],
	           		'content' =>base64_encode($content),
	           		'type' => $type,
	           		'relation' => $relation,
	                );
	        $commonModel = A("Common");
			echo $result = $commonModel->apiComment("c",$params);exit;
	    }else{
	    	//评论api
			$params = array(
		            'uid' => $_SESSION['members']['mid'],
		       		'content' =>base64_encode($content),
		       		'id' => $pid,
		            );
		    $commonModel = A("Common");
			echo $result = $commonModel->apiComment("r",$params);exit;
	    }
    }
    /*
    // 得到用户评论
    public function get_comment($party_id , $tid = 0){
            $model = D('es_esw_wine_party_comment');
            $comment = $model
                ->table('es_esw_wine_party_comment c')
                ->join('es_users u on c.uid = u.uid')
                ->field('c.uid,c.pid,c.party_id,c.id,c.dateline,c.content,u.uid,u.user_name,u.nick_name')
                ->where('c.party_id = '.$party_id.' and c.pid = '.$tid)
                ->findAll();
            return $comment;
    }
    */
    //  评论+1  回复不+1
    public function imgCommentCountPlus(){
        $id = $_POST['id'];
        $model = D();
        $model->query('UPDATE `es_esw_wine_party_photo` SET `count` = `count` +1 WHERE `id` ='.$id);
    }

    //酒会评论    
    public function getComment($id,$type="jiuhui"){
    	/*
        $sid = ''; 
        $auth = md5('MTMzNzc0NDIxMkpJVUhVSQ==jiuhui');
        $type = 'jiuhui';
        $relation = $id;
        $data = file_get_contents('http://comment.winesino.com/index.php/jiuhui/get/type/jiuhui/count/9999/auth/'.$auth.'/$relation/'.$relation);
        $data = json_decode($data, true);
        return $data;
        */
    	$params = array(
           		'type' => $type,
           		'rel' => $id,
                );
        $commonModel = A("Common");
		$result = $commonModel->apiComment("sc",$params);
		$commentinfo = json_decode($result,true);
		$commentinfo["count"] = $commentinfo["data"]["count"];
		$commentinfo["data"] = $commentinfo["data"]["data"];
		return $commentinfo;
    }

    function sendWei(){
        $content = $_POST['content'];
        $this->weiboApi($content);
    }
    function weiboApi($content){
    	$common = A("Common");
    	/*if(!$common->checkLogin()){
    		exit("没有登录!");
    	}*/
    	$access_token =  base64_encode('1000000003');
    	$token_secret = base64_encode('92A5hymz0AXTykJfhUr1');
    	$username = base64_encode($_SESSION['members']["username"]);
    	$nickname = base64_encode($_SESSION['members']["nickname"]);
    	$content = base64_encode($content);
    	$url = "http://i.wine.cn/User/Api/Share.php";
    	$params = array(
    		'access_token'=>$access_token,
    		'token_secret'=>$token_secret,
    		'username'=>$username,
    		'nickname'=>$username,
    		'content'=>$content
    	);
    	$this->set_post($url,$params);
    }
	function set_post($url,$params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		//ob_start();
		curl_exec($ch);
		curl_close($ch);
		//ob_clean();	
		return $ch;
	}
	function strReplace($str){
		if(strpos($str,"=")){
			return str_replace("=","%*20*$",$str);
		};
		return $str;
	}
	function insert(){
	    $partyId = $_POST['partyId'];
	    $common = A('Common');
	    $model = D('eswWinePartyUser');
	    $uid = $_SESSION['members']['uid'];
        
	   /*$data = $model->where('uid = '.$uid.' and party_id ='.$partyId)
		    ->find();*/	
	   // 	$data = $common->setMessage($telphone, $message);	
	    	$data['uid'] = $uid;
	    	$data['party_id'] = $partyId;  
    		
	    	/*$stat = $model->data($data)
            	  ->add();
           */
    	 	$params = array(
       			'type' => "jiuhui",
       			'id' => $partyId,
       			"uid"=>$_SESSION['members']['mid']
            );
	        $commonModel = A("Common");
			$result = $commonModel->apiComment("fl",$params);
			$stat = json_decode($result,true);
            if($stat["status"]==1){
                // 参加 +1
                $model = D();
                $model->query('UPDATE `es_esw_wine_party` SET `puser_num` = `puser_num` + 1 WHERE `id`= '.$partyId);
                $status['status'] = 1;
                echo json_encode($status); 
                return true;
            }else{
                $status['status'] = 1000003;
                echo json_encode($status);
                return true;
            }
            
	} 
    //取消参加
    function uninsert(){
        $partyId = $_POST['partyId'];
        $params = array(
       			'type' => "jiuhui",
       			'id' => $partyId,
       			"uid"=>$_SESSION['members']['mid']
            );
        $commonModel = A("Common");
		$result = $commonModel->apiComment("dfl",$params);
		echo $result;
        /*
        $model = D('eswWinePartyUser');
        $uid = $_SESSION['members']['uid'];
   		$params = array(
   			'type' => "jiuhui",
   			'id' => $partyId,
   			"uid"=>$_SESSION['members']['mid']
        );
        $commonModel = A("Common");
		$result = $commonModel->apiComment("dfl",$params);
		$stat = json_decode($result,true);
        if($stat["status"]==1){
            //取消 -1
            $model = D();
            $model->query('UPDATE `es_esw_wine_party` SET `puser_num` = `puser_num` - 1 WHERE `id`= '.$partyId);
            $status['status'] = 1;
            echo json_encode($status);
            return true;     
        }else{
            $status['status'] = 1000004;
            echo json_encode($status);
            return true;
        }
        */
    }
	 /*public function commentPic(){
   		$create = A("Create");
		$status = $create->_partyupload("377","283",&$info);
		$id = $_POST['id'];
		if($status==1){
			$dom = "<script type='text/javascript' src='/Common/js/jquery-1.7.1.js'></script><script type='text/javascript'>parent.$('#comment_img').val('".$info[0]["thumbinfo"]["377*283"]."').attr('data','".$id."')</script>";
				echo str_replace(array("\n", "\r"), array('', ''), $dom);
		}
	}*/
    // 删除评论
     public function delComment(){
        $img = $_POST['img']; 
        $id = $_POST['id'];
        $params = array(
           		'id' => $id,
           		'uid' => $_SESSION["members"]["mid"]
                );
        $commonModel = A("Common");
		echo $result = $commonModel->apiComment("del",$params);
        /*
        if(empty($img)){
            $auth = md5('MTMzNzc0NDIxMkpJVUhVSQ==jiuhui');
            $type = 'jiuhui';
            $url  = 'http://comment.winesino.com/index.php/jiuhui/del';
        }else{
            $auth = md5('MTMzNzc0NDIxMkpJVUhVSUlNRw==jiuhuiimg');
            $type = 'jiuhuiimg';
            $url  = 'http://comment.winesino.com/index.php/jiuhuiimg/del';
        }

   
         $params = array(
                                  'auth' => $auth,
                                  'type' => $type,
                                  'sid'   => $_SESSION['members']['sid'],
                                  'id'   => $id
                   );
         $ch = curl_init();                                     
         curl_setopt($ch, CURLOPT_URL,$url);
         curl_setopt($ch, CURLOPT_POST, 1);                     
         curl_setopt($ch, CURLOPT_POSTFIELDS, $params);         
         curl_exec($ch);
         curl_close($ch);
         */
     }
    public function verify() {  
          import("ORG.Util.Image");  
          Image::buildImageVerify();  
   }    
    public function insertUser(){
    
        if(empty($_GET['id'])){
        
            exit;
        }
        $id = $_GET['id'];
        /*
        $model = D();
        $data = $model
            ->table('es_esw_wine_party_user p')
            ->join('es_users u on u.uid = p.uid')
            ->where('p.party_id = '.$id)
            ->order('p.id desc')
            ->findAll();
        $this->assign('userCount', count($data));
        $this->assign('User',$data);
        	*/
        //partyInfo
        $model = D('eswWineParty');
        $data = $model
            ->where('id = '.$id)
            ->find();
        $this->assign('partyInfo', $data);
        $params = array(
	                'id' => $id,
	           		'type' =>"jiuhui"
	                );
	    $commonModel = A("Common");
		$result = $commonModel->apiComment("lfl",$params);
        $result = json_decode($result,true);
        $userinfo = $result["data"]["data"];
        foreach ($userinfo as $k=>$v){
        	$userinfo[$k]["uid"] = $this->getUid($v["m_id"]);
        }
        $joincount = $result["data"]["count"];
        $this->assign('joincount',$joincount);
        
        $this->assign('userInfo',$userinfo);

        $this->display('insert'); 
    
    }
	//获取城市名称
	function getcname($id) {
		$party = M();
		$c = $party->query("SELECT region_name FROM es_region_new WHERE region_id =$id");
		return $c[0]['region_name'];
	}
	
}

