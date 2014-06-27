<?php
//概况
class OverviewAction extends CommonAction {
	public function index(){
		$daily_data = D('daily_data');
		$today = strtotime("today");
		$yesterday = $today-3600*24;
		$near_7	   = $today-3600*24*7;
		$near_30   = $today-3600*24*30;
		$type = isset ( $_GET['type'] ) ? intval ( $_GET['type'] ) : 1 ;
		// $result1 = $daily_data->where('type='.$type.' AND date_time='.$today)->select();
		$result2 = $daily_data->where('type='.$type.' AND date_time='.$yesterday)->select();
		$result3 = $daily_data->query('select sum(`new_users`) as new_users , sum(`starts`) as starts , sum(`active_users`) as active_users , sum(`use_long`) as use_long from phone_daily_data where type='.$type.' AND date_time>='.$near_7);
		$result4 = $daily_data->query('select sum(`new_users`) as new_users , sum(`starts`) as starts , sum(`active_users`) as active_users , sum(`use_long`) as use_long from phone_daily_data where type='.$type.' AND date_time>='.$near_30);
		$result5 = $daily_data->query('select sum(`new_users`) as new_users , sum(`starts`) as starts , sum(`active_users`) as active_users , sum(`use_long`) as use_long from phone_daily_data where type='.$type);
		$count = $daily_data->where('type='.$type)->count();
		// var_dump($result5[0]);
		$this->assign('type',$type);
		// $this->assign('list1',$result1[0]);
		$this->assign('list2',$result2[0]);
		$this->assign('list3',$result3[0]);
		$this->assign('list4',$result4[0]);
		$this->assign('list5',$result5[0]);
		$this->assign('everyday_active_users',ceil($result5[0]['active_users']/$count));
		$this->assign('everyday_starts',ceil($result5[0]['starts']/$count));
	    $this->display();
	}
}