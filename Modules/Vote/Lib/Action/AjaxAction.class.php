<?php

class AjaxAction extends Action {

    /**
     * 投票
     */
    public function castVote(){
        $time = time();
        $theme_id = isset($_REQUEST['theme']) && intval($_REQUEST['theme']) ? intval($_REQUEST['theme']) : 0;
        $mark = isset($_REQUEST['mark']) && intval($_REQUEST['mark']) ? intval($_REQUEST['mark']) : 0;
        if(!$theme_id || !$mark)
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'投票失败，参数错误','mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//参数错误

        $ip = $this->get_client_ip();
        if($ip == "unknown")
            $this->echo_exit(array('errorCode'=>600003,'errorStr'=>'投票失败，ip地址非法','mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//ip非法

        $this->ip_frequency($ip,$time);

        $theme_res = D('Theme')->where(array('theme_id'=>$theme_id,'btime'=>array('lt',$time),'etime'=>array('gt',$time)))->find();
        if(!$theme_res)
            $this->echo_exit(array('errorCode'=>600002,'errorStr'=>'投票失败，投票功能处于关闭状态','mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//NULL

        //判断添加投票选项
        if(!$option_id = D('Option')->where(array('theme_id'=>$theme_id,'mark'=>$mark))->getfield('id'))
            $option_id = D('Option')->add(array('theme_id'=>$theme_id,'mark'=>$mark));

        //判断是否超出主题投票上限
        if($theme_res['theme_caps'] != 0){
            $theme_caps_log_res = D('ThemeCapsLog')->where(array('ip'=>$ip,'theme_id'=>$theme_id))->find();
            $theme_caps_log_votes = $theme_caps_log_res ? $theme_caps_log_res['votes'] : 0;
            if($theme_caps_log_votes >= $theme_res['theme_caps']){
                $theme_caps_desc = ($theme_res['theme_reset'] == 1) ? '投票失败，您本日对该次投票活动的投票已达到上限' : '投票失败，您对该次投票活动的投票已达到上限';
                $this->echo_exit(array('errorCode'=>600005,'errorStr'=>$theme_caps_desc,'mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//theme_caps
            }
        }
        //判断是否超出选项投票上限
        if($theme_res['option_caps'] != 0){
            $option_caps_log_res = D('OptionCapsLog')->where(array('ip'=>$ip,'option_id'=>$option_id))->find();
            $option_caps_log_votes = $option_caps_log_res ? $option_caps_log_res['votes'] : 0;
            if($option_caps_log_votes >= $theme_res['option_caps']){
                $option_caps_desc = ($theme_res['option_reset'] == 1) ? '投票失败，您本日对该选项的投票已达到上限' : '投票失败，您对改选项的投票已达到上限';
                $this->echo_exit(array('errorCode'=>600006,'errorStr'=>$option_caps_desc,'mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//theme_caps
            }
        }
        //判断是否超出选项数上限
        if($theme_res['option_num_caps'] != 0){
            $option_num_caps_option_idarr = D('OptionNumCapsLog')->where(array('ip'=>$ip,'theme_id'=>$theme_id))->getfield('option_id',true);
            if(!in_array($option_id, $option_num_caps_option_idarr) && (count($option_num_caps_option_idarr) >= $theme_res['option_num_caps'])){
                $option_num_caps_desc = ($theme_res['option_num_reset'] == 1) ? '投票失败，您本日可投票的选项数已达到上限' : '投票失败，您可投票的选项数已达到上限';
                $this->echo_exit(array('errorCode'=>600006,'errorStr'=>$option_num_caps_desc,'mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//theme_caps
            }
        }

        //
        $res = D('VoteLog')->add(array('ip'=>$ip,'option_id'=>$option_id,'theme_id'=>$theme_id,'time'=>$time));
        if(!$res)   $this->echo_exit(array('errorCode'=>600099,'errorStr'=>'投票失败，内部错误','mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//执行错误
        //dump($option_id);
        D('Option')->where(array('id'=>$option_id))->setInc('votes',1);
        if($theme_res['theme_caps'] != 0){
            if($theme_caps_log_res){
                D('ThemeCapsLog')->where(array('id'=>$theme_caps_log_res['id']))->setInc('votes',1);
            }else{
                D('ThemeCapsLog')->add(array('ip'=>$ip,'theme_id'=>$theme_id,'votes'=>1,'is_reset'=>$theme_res['theme_reset']));
            }
        }
        if($theme_res['option_caps'] != 0){
            if($option_caps_log_res){
                D('OptionCapsLog')->where(array('id'=>$option_caps_log_res['id']))->setInc('votes',1);
            }else{
                D('OptionCapsLog')->add(array('ip'=>$ip,'option_id'=>$option_id,'votes'=>1,'is_reset'=>$theme_res['option_reset']));
            }
        }
        if($theme_res['option_num_caps'] != 0){
            if(!in_array($option_id, $option_num_caps_option_idarr)){
                D('OptionNumCapsLog')->add(array('ip'=>$ip,'option_id'=>$option_id,'theme_id'=>$theme_id,'is_reset'=>$theme_res['option_num_reset']));
            }
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'投票成功','mark'=>array('theme'=>$theme_id,'mark'=>$mark)));//执行错误
    }

    /**
     * 获取票数
     */
    public function getVotes(){
        $theme_id = isset($_REQUEST['theme']) && intval($_REQUEST['theme']) ? intval($_REQUEST['theme']) : 0;
        $markarr = isset($_REQUEST['marks']) ? explode(',',$_REQUEST['marks']) : array();
        if(!$theme_id || count($markarr)==0)
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));//参数错误

        $is_theme = D('Theme')->where(array('theme_id'=>$theme_id))->find();
        if(!$is_theme)
            $this->echo_exit(array('errorCode'=>600002,'errorStr'=>'No Data'));//NULL
        $return = array();
        $sum_votes = 0;
        foreach($markarr as $mark){
            if($is = D('Option')->where(array('theme_id'=>$theme_id,'mark'=>$mark))->find()){
                $votes = $is['votes'];
            }else{
                D('Option')->add(array('theme_id'=>$theme_id,'mark'=>$mark));
                $votes = 0;
            }
            $return[] = array('mark'=>$mark,'votes'=>$votes);
            $sum_votes = $sum_votes + $votes;
        }
        //计算百分比
        foreach($return as $key=>$val){
            $return[$key]['percent'] = ($sum_votes == 0) ? 0 : round($val['votes']/$sum_votes,4);
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$return));
    }


    //ip投票频率过高限制判断
    function ip_frequency($ip,$time){
        $res = D('IpRestrict')->where(array('ip'=>$ip))->find();
        if($res && $res['otime'] >= $time){
            $this->echo_exit(array('errorCode'=>600004,'errorStr'=>'投票失败，投票过于频繁，为防止恶意刷票，您的ip:'.$ip.'已被冻结投票功能，解冻时间为：'.date("Y-m-d H:i:s",$res['otime'])));//ip投票访问频率过高受限
        }
        $is = 0;
        $time_ago1 = $time-(60*1);//1分钟60次
        $vote_count1 = D('VoteLog')->where(array('ip'=>$ip,'time'=>array('gt',$time_ago1)))->count();
        if($vote_count1 >= 60){
            $is = 1;
        }else{
            $time_ago2 = $time-(60*10);//十分钟300次
            $vote_count2 = D('VoteLog')->where(array('ip'=>$ip,'time'=>array('gt',$time_ago2)))->count();
            if($vote_count2 >= 300){
                $is = 1;
            }else{
                $time_ago3 = $time-(60*60*1);//1小时1000次
                $vote_count3 = D('VoteLog')->where(array('ip'=>$ip,'time'=>array('gt',$time_ago3)))->count();
                if($vote_count3 >= 1000){
                    $is = 1;
                }
            }
        }
        if($is == 1){
            $restrict = $res ?  $res['restrict']+1 : 1;
            $otime = $time + ($restrict*30*60);
            if($res){
                D('IpRestrict')->save(array('id'=>$res['id'],'restrict'=>$restrict,'otime'=>$otime));
            }else{
                D('IpRestrict')->add(array('ip'=>$ip,'restrict'=>$restrict,'otime'=>$otime));
            }
            $this->echo_exit(array('errorCode'=>600004,'errorStr'=>'投票失败，投票过于频繁，为防止恶意刷票，您的ip:'.$ip.'已被冻结投票功能，解冻时间为：'.date("Y-m-d H:i:s",$otime)));//ip投票访问频率过高受限
        }
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

    function echo_exit($arr){
        if(empty($_GET['callback']))
            echo json_encode($arr);
        else
            echo $_GET['callback'].'('.json_encode($arr).')';
        exit();
    }

}