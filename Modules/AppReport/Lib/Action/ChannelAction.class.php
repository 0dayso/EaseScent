<?php
//渠道分析
class ChannelAction extends CommonAction {
    public function index(){
        $type = $_GET['type'] ? $_GET['type'] : 1;
        $res = D('Channel_data')->where('type='.$type)->field("channel,sum(`new_users`) AS new_users,sum(`start_users`) AS start_users,sum(`start`) AS start,avg(`use_long`) AS use_long")->group('channel')->order('start DESC')->select();

        foreach ($res as $key => $value) {
            $res[$key]['use_long'] = gmstrftime('%H:%M:%S',$value['use_long']);
        }

        $this->assign('list', $res);
		$this->display();
    }
    
    public function ajax(){
        $type = $_GET['type'] ? $_GET['type'] : 1;
        $res = D('Channel_data')->where('type='.$type)->field("channel,sum(`new_users`) AS new_users,sum(`start_users`) AS start_users,sum(`start`) AS start,avg(`use_long`) AS use_long")->group('channel')->order('start DESC')->select();

        foreach ($res as $key => $value) {
            $res[$key]['use_long'] = gmstrftime('%H:%M:%S',$value['use_long']);
        }

        echo json_encode($res);
    }

    public function ajaxclick(){
        $type = $_GET['type'] ? $_GET['type'] : 1;
        $time = $_GET['t'] ? $_GET['t'] : 1;
        $t = strtotime("today")-3600*24*$time;
        $res = D('Channel_data')->where('date_time >='.$t.' AND type='.$type)->field("channel,sum(`new_users`) AS new_users,sum(`start_users`) AS start_users,sum(`start`) AS start,avg(`use_long`) AS use_long")->group('channel')->order('start DESC')->select();
        foreach ($res as $key => $value) {
            $res[$key]['use_long'] = gmstrftime('%H:%M:%S',$value['use_long']);
        }

        echo json_encode($res);
    }
}