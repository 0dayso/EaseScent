<?php
class CommonAction extends Action{
	public $_sns_access_token = "1000000003";
	public $_sns_token_secret = "92A5hymz0AXTykJfhUr1"; 
 public function get_real_ip(){
	$ip=false;
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$ip=$_SERVER["HTTP_CLIENT_IP"];
	}
	if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips=explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip){
			array_unshift($ips,$ip);
			$ip=FALSE;
		}
		for($i=0;$i<count($ips);$i++){
			if (!eregi("^(10��172.16��192.168).",$ips[$i])){
				$ip=$ips[$i];
				break;
			}
		}
	}
	$ip=$ip?$ip:$_SERVER['REMOTE_ADDR'];
	list($ip1,$ip2,$ip3,$ip4)=explode(".",$ip); 
	return $ip1*pow(256,3)+$ip2*pow(256,2)+$ip3*256+$ip4;
 }
 public function getUserArea(){
 	$ipModel = D("IpArea");
 	$myip = $this->get_real_ip();
 	$city = $ipModel->where("start<=".$myip)->order("start DESC")->getField("city");
 	return $city;
 }
 public function getCityId(){
 	$regionModel = D("RegionNew");
 	$cityname = $this->getUserArea();
 	$region = $regionModel->where("region_name like '%".$cityname."'")->find();
 	if(!$region){
 		$region["region_id"] = 2;//默认北京ID
 		$region["region_name"] = "北京";//默认北京ID
 	}
 	return $region;
 }
 public function getPartyinfo($city_id){
 	$regionModel = D("RegionNew");
 	$partyModel = D("EswWineParty");
 	$regioninfo = $regionModel->where("region_id=".$city_id)->find();
 	if($regioninfo["region_type"]>0){
 		$info = $partyModel->where("(province_id=".$city_id."||city_id=".$city_id."||area_id=".$city_id.") and is_show=1")->count();
 		if($info>0){
 			return $city_id;
 		}else{
 			return $this->getPartyinfo($regioninfo["parent_id"]);
 		}
 	}else{
 		return 2;
 	}
 	return false;
 }
 public function getParentCity($city_id){
 	$regionModel = D("RegionNew");
 	$regioninfo = $regionModel->where("region_id=".$city_id);
 	if($regioninfo["region_type"]>1){
 		return $regioninfo["parent_id"];
 	}
 	return false;
 }
 public function getCityInfo($id){
 	$regionModel = D("RegionNew");
 	$region = $regionModel->where("region_id=".$id)->find();
 	return $region;
 }
 public function serDefaultCity(){
 	$region = array();
 	$region["region_id"] = 2;//默认北京ID
 	$region["region_name"] = "北京";//默认北京ID
 	return $region;
 }
 public function checkLogin(){
 	$status = false;
 	if($_SESSION["members"]["uid"]){
 		$status = true;
 	}
 	return $status;
 }
 public function createPhone($string){
    	import("ORG.Util.Image");
    	$image = new Image();
    	$image->buildString($string,'','','png',1,false,'white');
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
 public function getCityName(){
    $id = $_POST['id'];
    $model = D();
    $sql = "SELECT region_id,region_name FROM es_region_new WHERE parent_id='".$id."'";
    echo $sql;
    $res = $model->query($sql);
    $html = '';
    foreach($res as $k=>$v){
        $html .= '<option value="'.$v['region_id'].'">'.$v['region_name'].'</option>';
    }
    echo $html;
 }
 public function formartTime($str){
    $other_wine = array();
    $other_wine['data'] = date('m月d日', $str);
    $other_wine['weak'] = substr(date('l',$str),0,3);
    $other_wine['hour'] = date('H:i', $str);
    return $other_wine;
 }
 public function getSignId($city){
    $Model_city = D('regionNew');
    $city = $Model_city
        ->where(" region_name = '".$city."'")
        ->find();
    return $city['region_id'];

 }
// 酒会评论
 public function getCommon($party_id , $tid = 0){
    $model = D('es_esw_wine_party_comment');
    $comment = $model
                ->table('es_esw_wine_party_comment c')
                ->join('es_users u on c.uid = u.uid')
                ->join('es_esw_wine_party_photo p on p.id = c.photo_id')
                ->field('c.uid,c.pid,c.party_id,c.id,c.dateline,c.content,u.uid,u.user_name,u.nick_name,p.image,c.is_show')
                ->where('c.party_id = '.$party_id.' and c.pid = '.$tid.' and c.photo_id =0')
                ->order('id desc')
                ->select();
    return $comment;   
 }
// 图片评论
public function getPicComment($id){ 
    /*
    $model = D('es_esw_wine_party_comment');
    $comment = $model
                ->table('es_esw_wine_party_comment c')
                ->join('es_users u on c.uid = u.uid')
                ->field('c.uid,c.pid,c.party_id,c.id,c.dateline,c.content,u.uid,u.user_name,u.nick_name,c.img,c.is_show')
                ->where('c.img = '.$img.' and c.pid = '.$tid.' and c.photo_id = 1')
                ->order('id desc')
                ->select();
    return $comment;
    */
    /*
    $sid = ''; 
    $auth = md5('MTMzNzc0NDIxMkpJVUhVSUlNRw==jiuhuiimg');
    $type = 'jiuhuiimg';
    $relation = $id;
    $data = file_get_contents('http://comment.winesino.com/index.php/jiuhuiimg/get/type/jiuhuiimg/auth/'.$auth.'/$relation/'.$relation);
    $data = json_decode($data, true);
    return $data;
    */
    $dAction = A("Detail");
    $comments = $dAction->getComment($id,"jiuhuiimg");
    return $comments;  
}

public function getMessage($uid, $tid = 0){
    $model = D('es_esw_wine_party_comment');
    $comment = $model
                ->table('es_esw_wine_party_comment c')
                ->join('es_users u on c.uid = u.uid')
                ->field('c.uid,c.pid,c.party_id,c.id,c.dateline,c.content,u.uid,u.user_name,u.nick_name,c.img')
                ->where('party_id = '.$uid.' and c.pid = '.$tid.' and c.photo_id = 2')
                ->order('id desc')
                ->select();
    return $comment;
}
/**
    d得到酒会参加人数  
  */
public function getpartyUserCount($id){
    $model = D('eswWinePartyUser');
    $data = $model
        ->where('party_id = '.$id)
        ->select();
    $count = count($data);
    return  $count;
}
 public function getInCount($uid){
     $model = D('eswWineParty');
     $data = $model
         ->where('add_user = '.$uid.' and is_show = 1 and p_type = 0')
         ->select();
     $user['create'] = count($data);

     $model = D();
     $data = $model
        ->table('es_esw_wine_party_user u')
        ->join('es_esw_wine_party p  on p.id = u.party_id')
        ->where('u.uid = '.$uid.' and p.p_type = 0 and p.is_show = 1 ')
        ->select();
     $user['insert'] = count($data);

     return $user;
}
 /*
 *短信发送接口
 *i=$client_id&t=$time&d=$data&v=$IV
 *$ID: 9
 *密钥：Dem3UcKXZz3
 *加密方式 DES
 *加密模式 cbc
 */
 public function setMessage($tel,$message){
 	$url = "http://sms.eswine.com/send.php";
 	$key = "Dem3UcKXZz3";
 	$key = md5($key . date('Y-m-d-H'));
 	$id=9;
 	$message = $message;
 	$time = time();
 	$mcrypt_create_iv = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CBC);
 	
 	$text = json_encode(array(
 		'id'=>$id,
 	    'time'=>$time,
 	    'message'=>$message,
        'tellist'=>array($tel)
 	));

 	$crypttext = mcrypt_encrypt(MCRYPT_DES, $key, $text,MCRYPT_MODE_CBC , $mcrypt_create_iv);
 	$mcrypt_create_iv = base64_encode($mcrypt_create_iv);
 	$crypttext =base64_encode($crypttext);
 	$params = array(
 		'i'=>'9',
 		't'=>$time,
 		'd'=>$crypttext,
 		'v'=>$mcrypt_create_iv,
 	);
 	$this->set_post($url,$params);
 	
 }
public function set_post($url,$params)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	//ob_start();
	echo curl_exec($ch);
	curl_close($ch);
	//ob_clean();	
	return $ch;

 
 }
 public function getPartyNum($id){
    $model = D('eswWinePartyUser');
    $data = $model
	->where('party_id = '.$id)
	->select();
    return count($data);


 }
 
	public function addHitIp($id,$type,$timeLimit=1){
		
		$field = $type."_id";
		$hitModel = D('HitStat');
	    //$hitList = $hitModel->field("ip")->where("LEFT(datetime,10)='".date("Y-m-d")."'")->select();
	    if($timeLimit){
	    	$hitList = $hitModel->field("ip")->where("LEFT(datetime,10)='".date("Y-m-d")."' and $field='".$id."'")->select();
	    }else{
	    	$hitList = $hitModel->field("ip")->where("$field='".$id."'")->select();
	    }
	  	foreach ($hitList as $key=>$value){
	    	$hitLists[] = $value["ip"];
	    }
	   $ip =  $this->get_client_ip();
	    if(!in_array($ip,$hitLists)){
	    	$data["ip"] = $ip;
	    	$data[$field] = $id;
	    	$data["datetime"] = date("Y-m-d H:i:s");
	    	$hitModel->add($data);
	    	return true;
	    }
	    return false;
	}
	function get_client_ip() {
		if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
			$ip = getenv ( "HTTP_CLIENT_IP" );
		else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
			$ip = getenv ( "REMOTE_ADDR" );
		else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
			$ip = $_SERVER ['REMOTE_ADDR'];
		else
			$ip = "unknown";
		return ($ip);
	}
	//获取广告
	/*
	public function getAd($ad_site)
	{
	    header("Content-Type:text/html; charset=utf-8");
		$where['ad_site']=$ad_site;
		$Model = D('Ad');

		$ad_list = $Model
						 ->where($where)
						 ->order('ad_id')
						 ->select();
		return $ad_list;
		//dump($ad_list);
		//dump($Model);
	}
	*/
	public function getAd($id){
		header("Content-Type:text/html; charset=utf-8");
		$adModel = D('Ad');
		//获取当前时间
		//$id = 1301;
		$curDate = strtotime(date("Y-m-d"));
		$adinfo = $adModel->where($where)->find($id);
		$ad_deid = $adinfo["ad_deid"];
		if($adinfo["is_open"]==0){//正常显示部分
			if($adinfo["sta_date"]<$curDate&&$adinfo["end_date"]>=$curDate){//广告在有效期内
					//不做处理
			}else{
				if($adinfo["ad_pid"]>0){
					$where["ad_id"] = $adinfo["ad_pid"];
					$adinfo = $adModel->where($where)->find();
					if($adinfo["is_open"]==0){
						if($adinfo["sta_date"]<$curDate&&$adinfo["end_date"]>=$curDate){//广告在有效期内
							//不做处理
						}else{//默认广告
							$adinfo = $this->getDefaultAd($ad_deid);
						}
					}else{//默认广告
						$adinfo = $this->getDefaultAd($ad_deid);
					}
				}else{//默认广告
					$adinfo = $this->getDefaultAd($ad_deid);
				}
			}
		}else{//显示默认广告
			$adinfo = $this->getDefaultAd($ad_deid);
		}
		if($adinfo){
			$adinfo['ad_path'] = str_replace("./images","/images",$adinfo['ad_path']);
			//转化url 进行点击量统计
			
			if(!empty($adinfo['ad_url'])){
				$adinfo['ad_url'] = "/index.php/Common/goAd/url/".base64_encode(urlencode($adinfo['ad_url']))."/cid/".$id;
			}else{
				$adinfo['ad_url'] = "";
			}
			/*
			$adHtml = "<div>
						<a href='".$adinfo['ad_url']."' target='_black'><img width='".$adinfo["ad_width"]."px' height='".$adinfo["ad_height"]."px' src='".$adinfo["ad_path"]."'></a>
				  	  </div>";
			*/
			
		}
		return	$adinfo;	
	}
	function getDefaultAd($deid){
		$adModel = D('Ad');
		$info = $adModel->find($deid);
		if($info){
			return $info;
		}
		return false;
	}
	//统计广告 点击量
	public function goAd(){
		$url = $_GET["url"];
		$id = $_GET["cid"];
		$where["ad_id"] = $id;
		$adModel = D('Ad');
		$adinfo = $adModel->where($where)->find();
		//验证cookie
		if($_COOKIE["_winesino_ad_".$id]){
			
		}else{
			if($this->addHitIp($id,"ad",0)){
				//更新点击量
				$data["hit_count"] = $adinfo["hit_count"]+1;
				$adModel->where("ad_id=".$id)->save($data);
			}
			SetCookie("_winesino_ad_".$id,1,$adinfo["end_date"]);
		}
		$url = urldecode(base64_decode($url));
		redirect($url);
	}
public function getParam(){
	/*
        $get = $_GET;
        foreach($get as $k=>$v){
            $a .= $k.'/'.$v.'/';  
        }
        $model = D('eswWinePartyCity');
        $data = $model->select();
        foreach($data as $k=>$v){
            $param['city'][$k]['name'] = $v['name'];
            $param['city'][$k]['city'] = $v['cid'];
        }
        $param['price'][0]['name'] = '不限';
        $param['price'][0]['price'] = 'unlimited';
        $param['price'][1]['name'] = '0-100';
        $param['price'][1]['price'] = '0-100';
        $param['price'][2]['name'] = '101-200';
        $param['price'][2]['price'] = '101-200';
        $param['price'][3]['name'] = '201-300';
        $param['price'][3]['price'] = '201-300';
        $param['price'][4]['name'] = '301-400';
        $param['price'][4]['price'] = '301-400';
        $param['price'][5]['name'] = '401-500';
        $param['price'][5]['price'] = '401-500';
        $param['price'][6]['name'] = '501以上';
        $param['price'][6]['price'] = '501-0';
        foreach($param as $k=>$v){
            foreach($v as $key=>$val){
                $aa  = array_replace($get,$val);
                unset($aa['name']);
                $a = '/index.php/PartyList/index/';
                foreach($aa as $t=>$d){
                    $a .= $t.'/'.$d.'/';
                    $param[$k][$key]['url'] = $a;
                }
            }
        }
    $cityData = $this->getCityInfo($get['city']);
    $this->assign('pCity', $param['city']);
    $this->assign('pPrice', $param['price']);
  //    $this->assign('pLower', $param['lower']);
    //得到cityName

	$this->assign('cityName', (empty($cityData) ? '北京' : $cityData['region_name']));
   //得到省级列表 
 	$Model_city = D();
	$sql = "SELECT region_id,region_name FROM es_region_new  WHERE parent_id='1'";
	$res = $Model_city->query($sql);
	*/
    $this->assign('proInfo', $res);
}

/*
    验证身份证  
 */
public function checkPic(){
    $id = $_POST['id'];
    $data = file_get_contents('http://www.youdao.com/smartresult-xml/search.s?jsFlag=true&type=id&q='.$id);
    echo $data;
}

 public function makeFace($content)
    {
        if (false !== strpos($content, '[')){
            if (false === strpos($content, '#[')){
                $face = $this->_getFaceImage();

                if (preg_match_all('~\[(.+?)\]~', $content, $match)){
                    foreach($match[0] as $k => $v){
						// 如果不存在[*] *号的表情，不替换.
                        if(isset($face[$match[1][$k]])){
							if($imageSrc = $face[$match[1][$k]]){
								$content = str_replace($v, '<img src="/'.$imageSrc.'" border="0"/>', $content);
							}
						}
                    }
                }
            }
        }
        return $content;
    }
	
	
	/**
	*  用于替换表情
	*
	* @return 
	*/
	private function _getFaceImage()
	{
		return array (
  
			'微笑'=> 'Common/images/face/weixiao.gif',

			'撇嘴'=> 'Common/images/face/pizui.gif',

			'色'=> 'Common/images/face/se.gif',

			'发呆'=> 'Common/images/face/fadai.gif',

			'得意'=> 'Common/images/face/deyi.gif',

			'流泪'=> 'Common/images/face/liulei.gif',

			'害羞'=> 'Common/images/face/haixiu.gif',
	 
			'闭嘴'=> 'Common/images/face/bizui.gif',
	 
			'睡觉'=> 'Common/images/face/shuijiao.gif',
			
			'睡'=> 'Common/images/face/shuijiao.gif',

			'大哭'=> 'Common/images/face/daku.gif',

			'尴尬'=> 'Common/images/face/gangga.gif',
			
			'大怒'=> 'Common/images/face/danu.gif',
			
			'发怒'=> 'Common/images/face/danu.gif',
	 
			'调皮'=> 'Common/images/face/tiaopi.gif',
	 
			'呲牙'=> 'Common/images/face/ciya.gif',
	 
			'惊讶'=> 'Common/images/face/jingya.gif',

			'难过'=> 'Common/images/face/nanguo.gif',

			'酷'=> 'Common/images/face/ku.gif',

			'冷汗'=> 'Common/images/face/lenghan.gif',

			'抓狂'=> 'Common/images/face/zhuakuang.gif',

			'吐'=> 'Common/images/face/tu.gif',

			'偷笑'=> 'Common/images/face/touxiao.gif',

			'可爱'=> 'Common/images/face/keai.gif',

			'白眼'=> 'Common/images/face/baiyan.gif',

			'傲慢'=> 'Common/images/face/aoman.gif',

			'饥饿'=> 'Common/images/face/er.gif',

			'困'=> 'Common/images/face/kun.gif',

			'惊恐'=> 'Common/images/face/jingkong.gif',

			'流汗'=> 'Common/images/face/liuhan.gif',

			'憨笑'=> 'Common/images/face/haha.gif',

			'大兵'=> 'Common/images/face/dabing.gif',

			'奋斗'=> 'Common/images/face/fendou.gif',

			'咒骂'=> 'Common/images/face/ma.gif',

			'骂'=> 'Common/images/face/ma.gif',

			'疑问'=> 'Common/images/face/wen.gif',

			'问'=> 'Common/images/face/wen.gif',

			'嘘'=> 'Common/images/face/xu.gif',

			'晕'=> 'Common/images/face/yun.gif',

			'折磨'=> 'Common/images/face/zhemo.gif',

			'衰'=> 'Common/images/face/shuai.gif',

			'骷髅'=> 'Common/images/face/kulou.gif',

			'敲打'=> 'Common/images/face/da.gif',
			
			'打'=> 'Common/images/face/da.gif',

			'再见'=> 'Common/images/face/zaijian.gif',
	 
			'擦汗'=> 'Common/images/face/cahan.gif',

			'挖鼻'=> 'Common/images/face/wabi.gif',

			'抠鼻'=> 'Common/images/face/wabi.gif',

			'鼓掌'=> 'Common/images/face/guzhang.gif',

			'糗大了'=> 'Common/images/face/qioudale.gif',

			'坏笑'=> 'Common/images/face/huaixiao.gif',

			'左哼哼'=> 'Common/images/face/zuohengheng.gif',

			'右哼哼'=> 'Common/images/face/youhengheng.gif',
	 
			'哈欠'=> 'Common/images/face/haqian.gif',

			'鄙视'=> 'Common/images/face/bishi.gif',

			'委屈'=> 'Common/images/face/weiqu.gif',

			'哭了'=> 'Common/images/face/ku.gif',
			
			'快哭了'=> 'Common/images/face/kuaikule.gif',

			'阴险'=> 'Common/images/face/yinxian.gif',
	 
			'亲亲'=> 'Common/images/face/qinqin.gif',

			'示爱'=> 'Common/images/face/kiss.gif',

			'亲吻'=> 'Common/images/face/kiss.gif',

			'吓'=> 'Common/images/face/xia.gif',
	  
			'可怜'=> 'Common/images/face/kelian.gif',
	  
			'菜刀'=> 'Common/images/face/caidao.gif',

			'西瓜'=> 'Common/images/face/xigua.gif',
	 
			'啤酒'=> 'Common/images/face/pijiu.gif',
			
			'篮球'=> 'Common/images/face/lanqiu.gif',
			
			'乒乓'=> 'Common/images/face/pingpang.gif',

			'咖啡'=> 'Common/images/face/kafei.gif',

			'饭'=> 'Common/images/face/fan.gif',

			'猪头'=> 'Common/images/face/zhutou.gif',

			'花'=> 'Common/images/face/hua.gif',

			'玫瑰'=> 'Common/images/face/hua.gif',

			'凋谢'=> 'Common/images/face/diaoxie.gif',

			'爱心'=> 'Common/images/face/love.gif',
	  
			'心碎'=> 'Common/images/face/xinsui.gif',
	  
			'蛋糕'=> 'Common/images/face/dangao.gif',
	  
			'闪电'=> 'Common/images/face/shandian.gif',
		
			'地雷'=> 'Common/images/face/zhadan.gif',
			
			'炸弹'=> 'Common/images/face/zhadan.gif',

			'刀'=> 'Common/images/face/dao.gif',
			
			'足球'=> 'Common/images/face/qiu.gif',	
	  
			'虫'=> 'Common/images/face/chong.gif',

			'瓢虫'=> 'Common/images/face/chong.gif',
		
			'便便'=> 'Common/images/face/dabian.gif',
	  
			'月亮'=> 'Common/images/face/yueliang.gif',
		
			'太阳'=> 'Common/images/face/taiyang.gif',
		
			'礼物'=> 'Common/images/face/liwu.gif',
	  
			'拥抱'=> 'Common/images/face/yongbao.gif',
		
			'强'=> 'Common/images/face/qiang.gif',
		
			'弱'=> 'Common/images/face/ruo.gif',
		
			'握手'=> 'Common/images/face/woshou.gif',
		
			'胜利'=> 'Common/images/face/shengli.gif',
		
			'佩服'=> 'Common/images/face/peifu.gif',

			'抱拳'=> 'Common/images/face/peifu.gif',

			'勾引'=> 'Common/images/face/gouyin.gif',
		
			'拳头'=> 'Common/images/face/quantou.gif',
		
			'差劲'=> 'Common/images/face/chajin.gif',
			
			'干杯'=> 'Common/images/face/cheer.gif',
		
			'no'=> 'Common/images/face/no.gif',
	  
			'ok'=> 'Common/images/face/ok.gif',

			'NO'=> 'Common/images/face/no.gif',
	  
			'OK'=> 'Common/images/face/ok.gif',
		
			'给力'=> 'Common/images/face/geili.gif',
		
			'飞吻'=> 'Common/images/face/feiwen.gif',
		
			'跳跳'=> 'Common/images/face/tiao.gif',

			'跳'=> 'Common/images/face/tiao.gif',

			'发抖'=> 'Common/images/face/fadou.gif',
		
			'怄火'=> 'Common/images/face/dajiao.gif',
			
			'大叫'=> 'Common/images/face/dajiao.gif',

			'转圈'=> 'Common/images/face/zhuanquan.gif',
		
			'磕头'=> 'Common/images/face/ketou.gif',
		
			'回头'=> 'Common/images/face/huitou.gif',
		
			'跳绳'=> 'Common/images/face/tiaosheng.gif',
		
			'挥手'=> 'Common/images/face/huishou.gif',
		
			'激动'=> 'Common/images/face/jidong.gif',
		
			'街舞'=> 'Common/images/face/tiaowu.gif',
		
			'献吻'=> 'Common/images/face/xianwen.gif',
		
			'左太极'=> 'Common/images/face/youtaiji.gif',
		
			'右太极'=> 'Common/images/face/zuotaiji.gif',
 
		);
	
	}
	//author shiwei add
	function com_CityList($cityid='') {
		$party = M();
		//全部酒会
		$curr_time = time();
		$curr_pt = $party->query("select id,region_name,parent_id,fistchar,region_id,left(title,10) title,party_start,party_end
				from es_esw_wine_party ep left join es_region_new en on ep.city_id=en.region_id where is_show=1 and p_type=0 order by isbar desc ");
		//print_r($curr_pt);
		//全国正在举办的酒会
		if(empty($cityid) or !is_numeric($cityid)) {
			$where = '';
		}elseif(!empty($cityid) and is_numeric($cityid)) {
			$where = " and ep.city_id != $cityid";
		}
		$pting = $party->query("select id,region_name,parent_id,fistchar,region_id,left(title,20) title,party_start,party_end,p_type,date_type 
				from es_esw_wine_party ep left join es_region_new en on ep.city_id=en.region_id where is_show=1 and p_type=0 $where  order by isbar desc ");
		$tempa = array();
		foreach($pting as $k=>$v) {
			$tempa[$k]['id']  = $v['id'];
			$tempa[$k]['title']  = $v['title'];
			$tempa[$k]['party_start']  = $v['party_start'];
			$tempa[$k]['party_end']  = $v['party_end'];
			$tempa[$k]['city_name']  = $v['region_name'];
			$tempa[$k]['region_id']  = $v['region_id'];
			$tempa[$k]['date_type']  = $v['date_type'];
			
		}//array_multisort()
		foreach ($tempa as $key => $row) {
			$volume1[$key]  = $row['party_end'];
			$edition[$key] = $row['id'];
		}
		array_multisort($volume1, SORT_ASC, $tempa);
		//全国正在举办的酒会
		$curr_ptdata = array();
		foreach($tempa as $k=>$v) {
			if($v['party_end'] > $curr_time) {
				$curr_ptdata[$k]['id']  = $v['id'];
				$curr_ptdata[$k]['title']  = $v['title'];
				$curr_ptdata[$k]['party_start']  = date('m月d日 H:i',$v['party_start']);
				$curr_ptdata[$k]['party_end']  = $v['date_type']==1 ? date('H:i',$v['party_end']):date('m月d日 H:i',$v['party_end']);
				$curr_ptdata[$k]['city_name']  = $v['city_name'];
				$curr_ptdata[$k]['region_id']  = $v['region_id'];
			}
			
		}
		//全部省份
		$curr_pe = $party->query("select region_id,region_name,fistchar from es_region_new where region_type=1");
		$pe_info = array();
		foreach($curr_pe as $k=>$v) {
			$pe_info[$v['region_id']]['region_name'] = $v['region_name'];
			$pe_info[$v['region_id']]['fistchar'] = $v['fistchar'];
			
		}
		//获取所有酒会的举办城市
		$allcity = array();
		foreach($curr_pt as $k=>$v)	{
			if($v['parent_id'] != 1) {
				$allcity[$v['region_id']]['fistchar'] = $v['fistchar'];
				$allcity[$v['region_id']]['region_name'] = $v['region_name'];
				$allcity[$v['region_id']]['parent_id'] = $v['parent_id'];
				$allcity[$v['region_id']]['region_id'] = $v['region_id'];
			}
		}
		//print_r($allcity);
		//城市A-Z排序
		$cy_order = array();
		foreach (range('A', 'Z') as $letter) {
			foreach($allcity as $k=>$v) {
				if($v['fistchar']==$letter) {
					$cy_order[$letter]['city'][] = array('cname'=>$allcity[$k]['region_name'],'id'=>$allcity[$k]['region_id']);
					$cy_order[$letter]['letter'] = $letter;
					$cy_order[$letter]['id'] = $v['region_id'];
				}
			}
		} 
		//print_r($cy_order);

		//获取所有酒会的举办省
		$allpes = array();
		foreach($allcity as $k=>$v)	{
			if($v['region_name'] != '北京' and $v['region_name'] != '重庆' and $v['region_name'] != '天津' and $v['region_name'] != '上海') {
				$allpes[$v['parent_id']]['region_id'] = $v['parent_id'];
				$allpes[$v['parent_id']]['region_name'] = $pe_info[$v['parent_id']]['region_name'];
				$allpes[$v['parent_id']]['fistchar'] = $pe_info[$v['parent_id']]['fistchar'];
				$allpes[$v['parent_id']]['city'][] = array('cname'=>$v['region_name'],'id'=>$v['region_id']);
				//$allpes[$v['parent_id']]['city'][]['id'] = $v['region_id'];
			}
		}
		//print_r($allpes);
		//省A-Z排序
		$pe_order = array();
		foreach (range('A', 'Z') as $letter) {
			foreach($allpes as $k=>$v) {
				if($v['fistchar']==$letter) {
					$pe_order[$v['region_id']] = $allpes[$v['region_id']];
				}
			}
		}//print_r($pe_order);
		//获取酒会热点城市
		$hot_city = $party->query("select id,name,cid from es_esw_wine_party_city");
		//print_r($hot_city);
		return array('curr_ptdata'=>$curr_ptdata,'pe_letter'=>$pe_order,'pe_order'=>$pe_order,'cy_letter'=>$cy_order,'cy_order'=>$cy_order,'hot_city'=>$hot_city);

	}
	/**
	 * 获取远程服务器HTTP状态
	 *
	 * @param unknown_type $url
	 * @return string
	 */
	function GetHttpStatusCode($url){ 
	         $curl = curl_init();
	         $url = $url."&language=zh-cn";
	         curl_setopt($curl,CURLOPT_URL,$url);//获取内容url 
	         curl_setopt($curl,CURLOPT_HEADER,1);//获取http头信息 
	         curl_setopt($curl,CURLOPT_NOBODY,1);//不返回html的body信息 
	         curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//返回数据流，不直接输出 
	         curl_setopt($curl,CURLOPT_TIMEOUT,5); //超时时长，单位秒 
	         curl_exec($curl);
	         $rtn= curl_getinfo($curl,CURLINFO_HTTP_CODE);
	         curl_close($curl);
	         return  $rtn;
	}
	//字符截取
	function mystrcut($string,$length,$etc='')
	{
		$result= '';
		$string = html_entity_decode(trim(strip_tags($string)),ENT_QUOTES,'UTF-8');
		$strlen = strlen($string);
		for($i=0; (($i<$strlen)&& ($length> 0));$i++)
		{
			$number=strpos(str_pad(decbin(ord(substr($string,$i,1))), 8, '0', STR_PAD_LEFT), '0');
			if($number)
			{
				if($length   <   1.0)
				{
					break;
				}
				$result   .=   substr($string, $i, $number);
				$length   -=   1.0;
				$i   +=   $number   -   1;
			}
			else
			{
				$result   .=   substr($string, $i, 1);
				$length   -=   0.5;
			}
		}
		$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
		if($i<$strlen)
		{
			$result   .=   $etc;
		}
		return   $result;
	}
	/**
	 * 下载Google地图
	 *
	 */
	function DownMap($url,$path){
		$url = $url."&language=zh-cn";
		$http_status = $this->GetHttpStatusCode($url);
		if($http_status==200){
			// Create a stream
		$opts = array(                 
		'http'=>array( 
		'method'=>"GET", 
		'header'=>"Accept-language: zh-cn"                
		 )             
		 ); 
		$context = stream_context_create($opts); 
		$content = file_get_contents($url,false,$context);
			if(is_file($path)){
				unlink($path);
			}
			$map = fopen($path,'w+');
			fwrite($map,$content);
			fclose($map);
			return true;
		}else {
			echo "this map no visit!";
		}
		return false;
	}
	 /**
    *api for i.wine.cn
    *author@david
    *$params 参数
    */
	function apiCommentini(){//js
		$type = $_POST['ctype'];
		foreach($_POST as $key=>$val){
			if($key != 'ctype'){
				if($key == 'content'){
					$params[$key] = base64_encode($val);
				}else{
					$params[$key] = $val;
				}
			}
		}
		echo $this->apiComment($type,$params);
	}
	function apiComment($type,$params){
        $params["access_token"] = $this->_sns_access_token;
        $params["token_secret"] = $this->_sns_token_secret;
		$baseUrl = C('TMPL_PARSE_STRING.__API_COMMENT__').'?';
		$baseUser = C('TMPL_PARSE_STRING.__API_USER__').'?';
        switch ($type){
        	case "c"://comment
        		 $url = $baseUrl."c=Statuses&m=publish";
        	break;
        	case "r"://replay
        		$url = $baseUrl."c=Comments&m=publish";
        	break;
        	case "d"://delete
        		$url = $baseUrl."c=Statuses&m=destroy";
        	break;
        	case "s"://select
        		$url = $baseUrl."c=Statuses&m=rel";
        		$url .= $this->getParams($params);
        		$url .= $str; 
        		break;
        	case "sr": //select replay
        		$url = $baseUrl."c=Comments&m=id_all";
        		$url .= $this->getParams($params);
        		break;
        	case "sc": //select comment_all
        		$url = $baseUrl."c=Statuses&m=rel_child";
        		$url .= $this->getParams($params);
        		break;
        	case "ac":
        		$url = $baseUrl."c=Statuses&m=type";
        		$url .= $this->getParams($params);
        		break;
        	case "uc":
        		$url = $baseUrl."c=Statuses&m=user_type";
        		$url .= $this->getParams($params); 
        		break;
        	case "del":
        		$url = $baseUrl."c=Statuses&m=destroy";
        		break;
        	case "delr":
        		$url = $baseUrl."c=Comments&m=id_del";
        		break;
        	case "el"://获取指定类型的所有评论
        		$url = $baseUrl."c=Statuses&m=type_rel";
        		$url .= $this->getParams($params);
        		break;
        	case "fl"://关注
        		$url = $baseUser."c=Follows&m=create_thing";
        		break;	
        	case "dfl"://关注
        		$url = $baseUser."c=Follows&m=destroy_thing";
        		break;
        	case "isfl"://是否关注
        		$url = $baseUser."c=Follows&m=is_follow";
        		$url .= $this->getParams($params);
        		break;
        	case "lfl":
        		$url = $baseUser."c=Follows&m=fans_thing";
        		$url .= $this->getParams($params);
        		break;
        	case "ffs":
        		$url = $baseUser."c=Follows&m=follows";
        		$url .= $this->getParams($params);
        		break;
        	case "ffts":
        		$url = $baseUser."c=Follows&m=follow_thing";
        		$url .= $this->getParams($params);
        		break;
        }
        $result = $this->setPost($url,$params);
        return $result;
        
    }
    function getParams($params){
    	foreach ($params as $k=>$v){
    		$str .="&".$k."=".$v;
    	}
        return $str;
    }
    function setPage($count,$listRows=20,$param){
		import("ORG.Util.Page");
		$p = new Page($count, $listRows, $param);
		return  $p->show();
    }
    /**
    *curl for post
    *author@david
    */
    function setPost($url,$params){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_exec($ch);
        $result = curl_multi_getcontent($ch);
        curl_close($ch);
        return $result;
    }

}
?>
