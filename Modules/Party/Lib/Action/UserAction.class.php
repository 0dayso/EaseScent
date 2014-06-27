<?php
class UserAction extends BaseAction
{
    
   public function index(){
    	if(!$_SESSION['members']["uid"]){
    		$this->assign("jumpUrl","/index.php");
    		$this->error("很抱歉您还没有登录，请先登录!");
    	}
        exit;
    }
     public function myInsert(){
    	if(!$_SESSION['members']["uid"]){
    		$this->assign("jumpUrl","/index.php");
    		$this->error("很抱歉您还没有登录，请先登录!");
    	}
        $uid = $_SESSION['members']['uid'];
        // 用户发布的酒会
        $model = D('eswWineParty');
        $count = $model
            ->where('add_user = '.$uid.' and (is_show = 1 or is_show = 0) and p_type = 0')
            ->count();
        $this->assign('myInPartyCount', $count);
        

     	$userModel = D("Users");
    	$userinfo = $userModel->where("uid=".$uid)->find();
    	$common = A("Common");
    	$city = $common->getCityId();
    	$userinfo["city"] = $city["region_name"];
    	$this->assign("userinfo",$userinfo);
    	$model = D('eswWineParty');
        /*
        // 我参加的酒会
        import("ORG.Util.Page");
        $listRows = 10;
       
        $count = $model
            ->table('es_esw_wine_party_user u')
            ->join('es_esw_wine_party p  on p.id = u.party_id')
            ->where('u.uid = '.$uid.' and p.p_type = 0 and p.is_show = 1 ')
            ->count();
        $p = new Page($count, $listRows, $param);				
        $data = $model
            ->table('es_esw_wine_party_user u')
            ->join('es_esw_wine_party p  on p.id = u.party_id')
            ->where('u.uid = '.$uid.' and p.p_type = 0 and p.is_show = 1 ')
            ->limit($p->firstRow.','.$p->listRows)
            ->select();
          */
         $params = array(
	                'uid' => $_SESSION['members']["mid"],
	           		'type' =>"jiuhui",
	           		'count' => 6
	                );
	    $commonModel = A("Common");
		$result = $commonModel->apiComment("ffts",$params);
        $result = json_decode($result,true);
        $data = $result["data"]["data"];
        $page =  $commonModel->setPage($result["data"]["count"]);
        $this->assign('joincount', $result["data"]["count"]);
	     foreach($data as $k=>$v){
	     	$p_info =  $model->where("id=".$v["follow"])->find();
            if($p_info['markprice'] == 0){
                $data[$k]['markprice'] = '免费';
            }else{
                $data[$k]['markprice'] = $p_info['markprice'].'元/位';
            }
            $data[$k]['start'] = $common->formartTime($p_info['party_start']);
            $data[$k]['end'] = $common->formartTime($p_info['party_end']);
            $data[$k]['id'] = $p_info['id'];
            $data[$k]['pictype'] = $p_info['pictype'];
            $data[$k]['title'] = $p_info['title'];
            $data[$k]['address_info'] = $p_info['address_info'];
            $data[$k]['ismoney'] = $p_info['ismoney'];
           // $data[$k]['markprice'] = $p_info['markprice'];
            //$data[$k]['insertUser'] = $common->getPartyUserCount($v['id']);
        }
        
       // $myPage = $p->show();
        $this->assign('myInParty', $data);
        $mid = $this->getMid($uid);
        $myface = $this->getUcFace($mid);
        $this->assign('myface', $myface);
       
        $seo_title = "我的酒会- 逸香网";
	    $seo_keywords = "酒会,酒会活动,城市酒会,酒会美女";
	    $seo_description = "酒会、酒会活动、城市酒会，酒会美女";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description); 

        $this->assign('partyCount', count($other_wine));
        $this->assign('mypage', $page);
        $this->assign('userInCount', $common->getInCount($uid));
    	$common->getParam();
    	$this->display("my_insert");
    }
    public function my(){
    	/*if(!$_SESSION['members']["uid"]){
    		$this->assign("jumpUrl","/index.php");
    		$this->error("很抱歉您还没有登录，请先登录!");
    	}*/
        $uid = $_SESSION['members']['uid'];
     	$userModel = D("Users");
    	$userinfo = $userModel->where("uid=".$uid)->find();
    	$common = A("Common");
    	$city = $common->getCityId();
    	$userinfo["city"] = $city["region_name"];
    	$this->assign("userinfo",$userinfo);
        
        
        //我发布过的酒会 
        import("ORG.Util.Page");
        $listRows = 10;
        $model = D('eswWineParty');
        $count = $model
            ->where('add_user = '.$uid.' and (is_show = 1 or is_show = 0) and p_type = 0')
            ->count();
    	$p = new Page($count, $listRows, $param);				
        $data = $model
            ->where('add_user = '.$uid.' and (is_show = 1 or is_show = 0) and p_type = 0')
            ->order('party_end desc')
    		->limit($p->firstRow.','.$p->listRows)
            ->select();
        foreach($data as $k=>$v){
            if($v['markprice'] == 0){
                $data[$k]['markprice'] = '免费';
            }else{
                $data[$k]['markprice'] = $v['markprice'].'元/位';
            }

            $data[$k]['weigui'] = ($v['is_show'] == 0) ? '未通过审核' : '';
            $data[$k]['start'] = $common->formartTime($v['party_start']);
            $data[$k]['end'] = $common->formartTime($v['party_end']);
            $data[$k]['insertUser'] = $common->getPartyUserCount($v['id']);
            $data[$k]['stop'] = ($v['party_start'] < time()) ? 1 : 0;
        }
        $myPage = $p->show();
        $this->assign('myPage', $myPage);
        $this->assign('myParty', $data);
        $this->assign('myPartyCount', $count);
        
        $mid = $this->getMid($uid);
        $myface = $this->getUcFace($mid);
        $this->assign('myface', $myface);  
        
        $seo_title = "我的酒会- 逸香网";
	    $seo_keywords = "酒会,酒会活动,城市酒会,酒会美女";
	    $seo_description = "酒会、酒会活动、城市酒会，酒会美女";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description); 
		$this->assign('class', 'my_wine_this');
        $this->assign('partyCount', count($other_wine));
            $params = array(
	                'uid' => $_SESSION['members']["mid"],
	           		'type' =>"jiuhui",
	           		'count' => 6
	                );
	    $commonModel = A("Common");
		$result = $commonModel->apiComment("ffts",$params);
        $result = json_decode($result,true);
        $this->assign('joincount', $result["data"]["count"]);
    	$common->getParam();
    	$this->display("my");
    }
    public function Other(){
        $uid = $_GET['uid'];
        if(empty($uid)){
            exit('非法访问');
        }
        $this->assign('oUid', $uid);
     	$userModel = D("Users");
    	$userinfo = $userModel->where("uid=".$uid)->find();
    	$common = A("Common");
    	$city = $common->getCityId();
    	$userinfo["city"] = $city["region_name"];
    	$this->assign("userinfo",$userinfo);
        
        
        //ta发布过的酒会 
        import("ORG.Util.Page");
        $listRows = 10;
        $model = D('eswWineParty');
        $count = $model
            ->where('add_user = '.$uid.' and is_show = 1 and p_type = 0')
    		->limit($p->firstRow.','.$p->listRows)
            ->count();

    	$p = new Page($count, $listRows, $param);				
        $data = $model
            ->where('add_user = '.$uid.' and is_show = 1 and p_type = 0')
            ->order('party_end desc')
    		->limit($p->firstRow.','.$p->listRows)
            ->select();
        foreach($data as $k=>$v){
            if($v['markprice'] == 0){
                $data[$k]['markprice'] = '免费';
            }else{
                $data[$k]['markprice'] = $v['markprice'].'元/位';
            }
            $data[$k]['start'] = $common->formartTime($v['party_start']);
            $data[$k]['end'] = $common->formartTime($v['party_end']);
            $data[$k]['insertUser'] = $common->getPartyUserCount($v['id']);
            $data[$k]['stop'] = ($v['party_start'] < time()) ? 1 : 0;
        }
        $myPage = $p->show();
        $this->assign('myPage', $myPage);
        $this->assign('myParty', $data);
        $this->assign('myPartyCount', count($data));
        
         $mid = $this->getMid($uid);
        $myface = $this->getUcFace($mid);
        $this->assign('myface', $myface);      
        
        $seo_title = "我的酒会- 逸香网";
	    $seo_keywords = "酒会,酒会活动,城市酒会,酒会美女";
	    $seo_description = "酒会、酒会活动、城市酒会，酒会美女";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description); 

        $this->assign('partyCount', count($other_wine));
        $this->assign('userInCount', $common->getInCount($uid));
    	$common->getParam();
    	$this->display("other");
    }
    public function otherInsert(){
        if(empty($_GET['uid'])){
        
            exit('非法访问');
        }
        $uid = $_GET['uid'];
        $this->assign('oUid',$uid);

     	$userModel = D("Users");
    	$userinfo = $userModel->where("uid=".$uid)->find();
    	$common = A("Common");
    	$city = $common->getCityId();
    	$userinfo["city"] = $city["region_name"];
    	$this->assign("userinfo",$userinfo);
        
        
        // 他参加的酒会
        import("ORG.Util.Page");
        $listRows = 10;
        $model = D('eswWineParty');
        $count = $model
            ->table('es_esw_wine_party_user u')
            ->join('RIGHT JOIN es_esw_wine_party p on p.id = u.party_id')
            ->where('u.uid = '.$uid.' and p.p_type = 0   and p.is_show = 1')
            ->count();
        $p = new Page($count, $listRows, $param);				
        $data = $model
            ->table('es_esw_wine_party_user u')
            ->join('RIGHT JOIN es_esw_wine_party p on p.id = u.party_id')
            ->where('u.uid = '.$uid.' and p.p_type = 0 and p.is_show = 1')
            ->limit($p->firstRow.','.$p->listRows)
            ->select();
	     foreach($data as $k=>$v){
             if($v['markprice'] == 0){
                $data[$k]['markprice'] = '免费';
            }else{
                $data[$k]['markprice'] = $v['markprice'].'元/位';
            }
            $data[$k]['start'] = $common->formartTime($v['party_start']);
            $data[$k]['end'] = $common->formartTime($v['party_end']);
            $data[$k]['insertUser'] = $common->getPartyUserCount($v['id']);
        }
        $myPage = $p->show();
        $this->assign('myInParty', $data);
        $this->assign('myInPartyCount', count($data));
       
        $mid = $this->getMid($uid);
        $myface = $this->getUcFace($mid);
        $this->assign('myface', $myface);  
        
        $seo_title = "我的酒会- 逸香网";
	    $seo_keywords = "酒会,酒会活动,城市酒会,酒会美女";
	    $seo_description = "酒会、酒会活动、城市酒会，酒会美女";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description); 

        $this->assign('partyCount', count($other_wine));
        $this->assign('userInCount', $common->getInCount($uid));
    	$common->getParam();
    	$this->display("other_insert");
    }


    public function getPhone(){
        $common = A("Common");
        $common->createPhone($_GET['phone']); 
    }

    public function aaa(){
    ob_clean();
     $callback = $_GET['callback'];
     $arr = array('uname'=>'lidong');
            echo "{$callback}(".json_encode($arr).")";
    
    }
}
