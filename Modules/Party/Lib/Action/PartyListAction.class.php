<?php
class PartyListAction extends BaseAction{
	public function index(){
		header("Content-Type:text/html; charset=utf-8");
		//$this->getIpCity();
		$c = trim($_GET["c"]);//城市
		$t = trim($_GET["t"]);//时间
		$p = trim($_GET["p"]);//价格
		$party = M();
		$where = '';
		//城市
		if(isset($c) and is_numeric($c) and ($c!=0)) {
			$where.= " and ep.city_id=$c";
			$this->assign('c', $c);
			$this->assign('c_value', $this->getcname($c));
			setcookie("city_id",$c,time()+6048000,"/",'jiuhui.wine.cn'); 
			$seo_city = $this->getcname($c);
		}else {
			$this->assign('c', 0);
			$this->assign('c_value', '全国');
			$seo_city = '全国';
		}
		//时间
		if(isset($t) and is_numeric($t) and ($t!=0)) {
			$currtime = time();
			if($t < $currtime) {
				$where.= " and ep.party_end >= $t and ep.party_end <= $currtime";
				$this->assign('t_value', '最近一周');
				$this->assign('t', $t);
			}elseif($t > $currtime) {
				$btime = mktime(0, 0, 0, date('d'), date('m'), date('Y'))+86400;
				$where.= " and ep.party_end > $btime and ep.party_end < $t";
				$this->assign('t_value', '明天');
				$this->assign('t', $t);
			}	
			
		}elseif(isset($t) and is_string($t) and ($t!=0)) {
			$tarray = explode('-',$t);
			$btime = mktime(0, 0, 0, $tarray[1], $tarray[2], $tarray[0]);
			$etime = mktime(0, 0, 0, $tarray[1], $tarray[2], $tarray[0])+86400;
			$where.= " and ep.party_end > $btime and ep.party_end < $etime";
			$this->assign('t_value', $t);
			$this->assign('t', $t);	
		}elseif(isset($t) and is_string($t) and ($t=='all')) {
			$this->assign('t_value', '全部时间');
			$this->assign('t', $t);
		}else {
			$this->assign('t_value', '日期');
			$this->assign('t', 0);
		}
		//价格
		if(isset($p) and is_string($p) and ($p!=0)) {
			if($p=='1|1') {
				$where.= " and ep.markprice <= 0";
				$this->assign('p_value', '免费');
				$this->assign('p', $p);
			}elseif($p=='1|300' or $p=='300|500') {
				$parray = explode('|', $p);
				$where.= " and ep.markprice >= $parray[0] and ep.markprice <= $parray[1]";
				$this->assign('p_value', $p);
				$this->assign('p', $p);
			}elseif($p=='500+') {
				$where.= " and ep.markprice > 500";
				$this->assign('p_value', '500以上');
				$this->assign('p', '500+');
			}else {
				$this->assign('p_value', '全部');
				$this->assign('p', 'all');
			}
		}else {
			$this->assign('p_value', '价格区间');
			$this->assign('p', 0);
		}
		import("ORG.Util.Page");
		$p = new Page(count($party->query("select ep.id from es_esw_wine_party ep where ep.is_show=1 and ep.p_type=0 $where order by ep.isbar desc")), 12);
		//按条件获取酒会信息
		
        $a = "select id from es_esw_wine_party where is_show=1 and p_type=0 $where order by isbar desc";
		$ptinfo = $party->query("select ep.id,left(title,20) title,party_start,party_end,left(address_info,20) address_info,
				puser_num,markprice,ismoney,isbar,p_type,pictype,date_type,region_name,region_id from es_esw_wine_party ep left join es_region_new en on ep.city_id=en.region_id 
				where ep.is_show=1 and ep.p_type=0 $where order by isbar desc ");

		$page = $p->show();
		
		//构造数组
		$tempa = $tempa1 = $tempa2 = array();
		foreach($ptinfo as $k=>$v) {
			if($v['isbar'] == 1) {
				$tempa[$k]['id']  = $v['id'];
				$tempa[$k]['title']  = $v['title'];
				$tempa[$k]['party_start']  = $v['party_start'];
				$tempa[$k]['party_end']  = $v['party_end'];
				$tempa[$k]['address_info']  = $v['address_info'];
				$tempa[$k]['markprice']  = $v['markprice'];
				$tempa[$k]['ismoney']  = $v['ismoney'];
				$tempa[$k]['puser_num']  = $v['puser_num'];
				$tempa[$k]['isbar']  = $v['isbar'];
				$tempa[$k]['pictype']  = $v['pictype'];
				$tempa[$k]['city_name']  = $v['region_name'];
				$tempa[$k]['region_id']  = $v['region_id'];
				$tempa[$k]['date_type']  = $v['date_type'];
			}elseif($v['party_end']-time() >0) {
				$tempa1[$k]['id']  = $v['id'];
				$tempa1[$k]['title']  = $v['title'];
				$tempa1[$k]['party_start']  = $v['party_start'];
				$tempa1[$k]['party_end']  = $v['party_end'];
				$tempa1[$k]['address_info']  = $v['address_info'];
				$tempa1[$k]['markprice']  = $v['markprice'];
				$tempa1[$k]['ismoney']  = $v['ismoney'];
				$tempa1[$k]['puser_num']  = $v['puser_num'];
				$tempa1[$k]['isbar']  = $v['isbar'];
				$tempa1[$k]['pictype']  = $v['pictype'];
				$tempa1[$k]['city_name']  = $v['region_name'];
				$tempa1[$k]['region_id']  = $v['region_id'];
				$tempa1[$k]['date_type']  = $v['date_type'];
			}elseif($v['party_end']-time() <0) {
				$tempa2[$k]['id']  = $v['id'];
				$tempa2[$k]['title']  = $v['title'];
				$tempa2[$k]['party_start']  = $v['party_start'];
				$tempa2[$k]['party_end']  = $v['party_end'];
				$tempa2[$k]['address_info']  = $v['address_info'];
				$tempa2[$k]['markprice']  = $v['markprice'];
				$tempa2[$k]['ismoney']  = $v['ismoney'];
				$tempa2[$k]['puser_num']  = $v['puser_num'];
				$tempa2[$k]['isbar']  = $v['isbar'];
				$tempa2[$k]['pictype']  = $v['pictype'];
				$tempa2[$k]['city_name']  = $v['region_name'];
				$tempa2[$k]['region_id']  = $v['region_id'];
				$tempa2[$k]['date_type']  = $v['date_type'];
			}
		}
		//推荐的
		foreach ($tempa as $key => $row) {
			$volume[$key]  = $row['party_end'];
		}
		array_multisort($volume, SORT_ASC, $tempa);
		//正在进行的
		foreach ($tempa1 as $key => $row) {
			$volume1[$key]  = $row['party_end'];
		}
		array_multisort($volume1, SORT_ASC, $tempa1);
		//已经结束的
		foreach ($tempa2 as $key => $row) {
			$volume2[$key]  = $row['party_end'];
		}
		array_multisort($volume2, SORT_DESC, $tempa2);
		//合并
		$result = array_merge($tempa, $tempa1, $tempa2);
		//最终数组
		$ptdata = array();
		foreach($result as $k=>$v) {
			$ptdata[$k]['id']  = $v['id'];
			$ptdata[$k]['title']  = $v['title'];
			$ptdata[$k]['party_start']  = date('m月d日 H:i',$v['party_start']);
			$ptdata[$k]['party_end']  = $v['date_type']==1 ? date('H:i',$v['party_end']):date('m月d日 H:i',$v['party_end']);
			$ptdata[$k]['address_info']  = $v['address_info'];
			$ptdata[$k]['markprice']  = $v['markprice'];
			$ptdata[$k]['ismoney']  = $v['ismoney'];
			$ptdata[$k]['puser_num']  = $v['puser_num'];
			$ptdata[$k]['end']  = $v['party_end']-time() > 0 ? 0:1;//1 结束
			$ptdata[$k]['isbar']  = $v['isbar'];
			$ptdata[$k]['pictype']  = $v['pictype'];
			$ptdata[$k]['city_name']  = $v['city_name'];
			$ptdata[$k]['region_id']  = $v['region_id'];
			if(empty($v['pictype']) or $v['id']==220) {
				$ptdata[$k]['image'] = "__UPLOAD__/party/images/default.jpg";
			}else {
				$ptdata[$k]['image'] = "__UPLOAD__/party/images/".$v['id']."/s_.".$v['pictype'];
			}
		}
		//print_r($ptdata);
		$spage = $p->totalPages;
		if(isset($_GET['page']) && is_numeric($_GET['page']) && ($_GET['page']!=0) && ($_GET['page']<=$spage)) {
			$brow = ($_GET['page']-1) * 12;
			$retinfo = array_slice($ptdata,$brow,12);
		}else {
			$retinfo = array_slice($ptdata,0,12);
		}
		//公共城市列表
		$common = A("Common");
		$citylist = $common->com_CityList($c);
		$this->assign('ptdata', $retinfo);
		$this->assign('pt_sum', count($ptdata));
		$this->assign('curr_ptdata', $citylist['curr_ptdata']);
		$this->assign('pe_letter', $citylist['pe_letter']);//省字母
		$this->assign('pe_order', $citylist['pe_order']);//省列表
		$this->assign('cy_letter', $citylist['cy_letter']);//城市列表
		$this->assign('cy_order', $citylist['cy_order']);//城市列表
		$this->assign('hot_city', $citylist['hot_city']);//热点城市
		$this->assign('page', $page);
		$seo_title = $seo_city."葡萄酒酒会 – 逸香网";
	    $seo_keywords = $seo_city."酒会,".$seo_city."葡萄酒酒会,".$seo_city."品酒会,".$seo_city."品鉴会,".$seo_city."鸡尾酒会,".$seo_city."高端酒会,".$seo_city."红酒酒会,".$seo_city."酒会信息,".$seo_city."酒会新闻";
	    $seo_description = "逸香网".$seo_city."葡萄酒酒会新闻频道，介绍".$seo_city."红酒酒会信息，第一时间为你发布".$seo_city."葡萄酒品鉴会信息，这些葡萄酒品酒会信息汇集".$seo_city."所有葡萄酒酒会信息，通过红酒品鉴会共同享受美酒带来的快乐。";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);
		$this->display("list");
	}

    public function search(){
		header("Content-Type:text/html; charset=utf-8");
        $key = $_POST['searchkey'];
        $region = D('eswWineParty');
        import("ORG.Util.Page");
        $listRows = 10;
        $count = $region->where('title like "%'.$key.'%"')
    					->limit($p->firstRow.','.$p->listRows)
    					->count();
    	$p = new Page($count, $listRows, $param);				
    	$list = $region->where('title like "%'.$key.'%"')
    					->limit($p->firstRow.','.$p->listRows)
    					->findAll();
    	$page = $p->show();
        $common = A("Common");
    	if($list){
    		$regionModel = D("RegionNew");
    		foreach ($list as $key=>$value){
    			$list[$key]["party_start_date"] = date("m-d",$value["party_start"]);
    			$list[$key]["party_start_time"] = date("H:i",$value["party_start"]);
    			$list[$key]["party_end_date"] = date("m-d",$value["party_end"]);
    			$list[$key]["party_end_time"] = date("H:i",$value["party_end"]);
    			$list[$key]["party_start_weak"] = date("l",$value["party_start"]);
    			$list[$key]["party_end_weak"] = date("l",$value["party_end"]);
			$area = $regionModel->where("region_id=".$value["area_id"])->getField("region_name");
			$list[$key]["address"] = $area.$value["address_info"];
			$list[$key]['person_count'] = $common->getPartyNum($value['id']);
    		}
    	}
       
    	$seo_title = "葡萄酒酒会 – 逸香网";
	    $seo_keywords = "酒会,酒会活动,城市酒会,酒会美女";
	    $seo_description = "酒会、酒会活动、城市酒会，酒会美女";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);  
    	
        $common->getParam();
        $this->assign('city',$city);
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->display('list'); 
    }
    public function userParty(){
		header("Content-Type:text/html; charset=utf-8");
        $uid = $_GET['uid'];
        $region = D('eswWineParty');
        import("ORG.Util.Page");
        $listRows = 10;
        $count = $region->where('add_user = '.$uid)
    					->limit($p->firstRow.','.$p->listRows)
    					->count();
    	$p = new Page($count, $listRows, $param);				
    	$list = $region->where('add_user = '.$uid)
    					->limit($p->firstRow.','.$p->listRows)
    					->findAll();
    	$page = $p->show();
        $common = A("Common");
    	if($list){
    		$regionModel = D("RegionNew");
    		foreach ($list as $key=>$value){
    			$list[$key]["party_start_date"] = date("m-d",$value["party_start"]);
    			$list[$key]["party_start_time"] = date("H:i",$value["party_start"]);
    			$list[$key]["party_end_date"] = date("m-d",$value["party_end"]);
    			$list[$key]["party_end_time"] = date("H:i",$value["party_end"]);
    			$list[$key]["party_start_weak"] = date("l",$value["party_start"]);
    			$list[$key]["party_end_weak"] = date("l",$value["party_end"]);
			$area = $regionModel->where("region_id=".$value["area_id"])->getField("region_name");
			$list[$key]["address"] = $area.$value["address_info"];
			$list[$key]['person_count'] = $common->getPartyNum($value['id']);
    		}
    	}
       	$seo_title = "我的酒会- 逸香网";
	    $seo_keywords = "酒会,酒会活动,城市酒会,酒会美女";
	    $seo_description = "酒会、酒会活动、城市酒会，酒会美女";
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);  
	    
        $common->getParam();
        $this->assign('city',$city);
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->display('list'); 
    }
	public function getCityParty($region_id){
    	$common = A('common');
    	$region = D("EswWineParty");
    	$data = " (province_id=".$region_id."||city_id=".$region_id."||area_id=".$region_id.")";
    	$data .= " and is_show = 1";
    	$data .= " and isbar  = 0"; 
	    $price = $_GET['price'];
        if(isset($price)){
            switch($price){
                case 'unlimited':
                    break;
                default:
                    $price = explode('-',$price);
                    if($price[1] != 0){
                        $data .= " and lowerprice >=".$price[0]." and lowerprice <= ".$price[1];
                    }else{
                        $data .= " and lowerprice >=".$price[0]; 
                    }
                    break;
            }
        }
    	$listRows = 10;   //每页多少条
	    if($_GET['old']){
		    $data .= " and party_end < ".time();	
	    }
	    if($_GET['me'] == 1){
		    $data .= " and  add_user=".$_SESSION['members']['uid'];
	    }
	    $order = $_GET['order'];
	    if(!empty($order)){
		    $order = "(markprice div lowerprice)/markprice desc , ";
	    }
        import("ORG.Util.Page");
        $count = $region->where($data)
    					->order("add_time DESC".$order)
    					->count();
    	$p = new Page($count, $listRows, $param);				
    	$list = $region->where($data)
    					->order($order."(party_start - ".time().") DESC")
    					->limit($p->firstRow.','.$p->listRows)
    					->findAll();
    	$page = $p->show();
    	if($list){
    		$regionModel = D("RegionNew");
    		foreach ($list as $key=>$value){
    			$list[$key]["party_start_date"] = date("m-d",$value["party_start"]);
    			$list[$key]["party_start_time"] = date("H:i",$value["party_start"]);
    			$list[$key]["party_end_date"] = date("m-d",$value["party_end"]);
    			$list[$key]["party_end_time"] = date("H:i",$value["party_end"]);
    			$list[$key]["party_start_weak"] = date("l",$value["party_start"]);
    			$list[$key]["party_end_weak"] = date("l",$value["party_end"]);
			$area = $regionModel->where("region_id=".$value["area_id"])->getField("region_name");
			$list[$key]["address"] = $area.$value["address_info"];
			$list[$key]['person_count'] = $common->getPartyNum($value['id']);
    		}
    	}
    	return array('data'=>$list,'page'=>$page);
    }
	//获取城市名称
	function getcname($id) {
		$party = M();
		$c = $party->query("SELECT region_name FROM es_region_new WHERE region_id =$id");
		return $c[0]['region_name'];
	}	
}
?>
