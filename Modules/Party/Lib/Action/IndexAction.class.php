<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends BaseAction{
	public function index(){
		header("Content-Type:text/html; charset=utf-8");
		//$this->getIpCity();

		$party = M();
		$where = '';
		//城市
		$c = $_COOKIE["city_id"];
		if(isset($c) and is_numeric($c) and $c!=0) {
			$where.= " and ep.city_id = $c";
			$this->assign('c', $c);
			$this->assign('c_value', $this->getcname($c));
		}else {
			$this->assign('c', 0);
			$this->assign('c_value', '全国');
		}
		
		if(empty($where)) {
			//ip定位
			$ipinfo = $this->getIpCity();
			if($ipinfo !='') {
				$ret = $party->query("select region_id from es_region_new where region_name='$ipinfo'");
				if($ret) {
					$re_id = $ret[0]['region_id'];
					$where.= " and ep.city_id=$re_id";
					$c = $re_id;
					$this->assign('c', $ret[0]['region_id']);
					$this->assign('c_value', $ipinfo);
				}
			}
		}
		
		
		//import("ORG.Util.Page");
		//$p = new Page(count($party->query("select id from es_esw_wine_party where is_show=1 order by isbar desc")), 12);
		//按条件获取酒会信息
		$ptinfo = $party->query("select ep.id,left(title,20) title,party_start,party_end,left(address_info,20) address_info,
				puser_num,markprice,ismoney,isbar,p_type,pictype,date_type,region_name,region_id from es_esw_wine_party ep left join es_region_new en on ep.city_id=en.region_id 
				where ep.is_show=1 and ep.p_type=0 $where order by isbar desc ");
		//$p_user_num = $this->getPartyUser(2);
		//$page = $p->show();
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
				$ptdata[$k]['image'] = '__UPLOAD__/party/images/default.jpg';
			}else {
				$ptdata[$k]['image'] = "__UPLOAD__/party/images/".$v['id']."/s_.".$v['pictype'];
			}
			/*if(empty($v['pictype']) or $v['id']==220) {
				$ptdata[$k]['image'] = '__PUBLIC__/party/images/default.jpg';
			}else {
				$ptdata[$k]['image'] = "__PUBLIC__/party/Common/images/partycreate/".$v['id']."/s_.".$v['pictype'];
			}*/
		}
		//print_r($ptdata);
		
		//公共城市列表
		$common = A("Common");
		$citylist = $common->com_CityList($c);
		$this->assign('ptdata', $ptdata);
		$this->assign('pt_sum', count($ptdata));
		$this->assign('curr_ptdata', $citylist['curr_ptdata']);
		$this->assign('pe_letter', $citylist['pe_letter']);//省字母
		$this->assign('pe_order', $citylist['pe_order']);//省列表
		$this->assign('cy_letter', $citylist['cy_letter']);//城市列表
		$this->assign('cy_order', $citylist['cy_order']);//城市列表
		$this->assign('hot_city', $citylist['hot_city']);//热点城市
		$seo_title = "葡萄酒酒会 – 逸香网";
	    $seo_keywords = "酒会,葡萄酒酒会,品酒会,葡萄酒,品鉴会";
	    $seo_description = "逸香网葡萄酒酒会新闻频道，介绍红酒酒会信息，第一时间为你发布葡萄酒品鉴会信息，这些葡萄酒品酒会信息汇集北京、上海、广州等国内外主要城市，通过红酒品鉴会共同享受美酒带来的快乐。";
		/*******友情链接模块*********/
        //$Links = A('Linksurl');
        
        $this->assign('links_list',$this->getLinks(array(),324));
	    $this->assign('seo_title', $seo_title);
	    $this->assign('seo_keywords', $seo_keywords);
	    $this->assign('seo_description', $seo_description);
		$this->display('wine_index');
		
		
    }
	function getIpCity() {
		import("ORG.Net.IpLocation");
		$ip = new IpLocation("qqwry.dat");
		$getip = $ip->get_client_ip();
        $ipinfo = $ip->getlocation($getip);
		//print_r($ipinfo);exit;
		$ia = '';
		$cy = iconv("GBK", "UTF-8", $ipinfo['country']);
		if (strpos($cy,'省')) {
			$cyarr = explode('省',$cy);
			$ia = substr(trim($cyarr[1]),0,6);
		}elseif ($cy=='北京市' or $cy=='重庆市' or $cy=='上海市' or $cy=='天津市') {
			$ia = substr(trim($cy),0,6);
		}elseif (strpos($cy,'市')) {
			$ia = substr($cy,6,-3);
		}
		
		return $ia;
	}
	//获取城市名称
	function getcname($id) {
		$party = M();
		$c = $party->query("SELECT region_name FROM es_region_new WHERE region_id =$id");
		return $c[0]['region_name'];
	}
	//其他页面获取友链列表（es_links表获取）
	public function getLinks($links_sort_arr,$links_seat){
		$Common = A('Common');
		if($links_sort_arr){
			$where['links_sort']=array('in',$links_sort_arr);
		}
		if($links_seat){
			$where['links_seat']=array('like','%|'.$links_seat.'|%');
		}
		$where['is_open']=1;
		$Model=D();
		$res=$Model
			->table('es_links')
			->where($where)
			->order('links_order DESC')
			->select();
		foreach($res as $key=>$val){
			$res[$key]['slinks_name'] = $Common->mystrcut($val['links_name'],7,$etc='');
		}
		return $res;
		
	}
   
}
?>
