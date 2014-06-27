<?php
//应用趋势-新增用户
class AppAddUserAction extends CommonAction {
    public function index(){                    
        $this->display();
    }

    public function getData(){
        $daily_data = D('daily_data');
        $today      = strtotime("today");   
        $type       = isset ( $_POST['type'] ) ? intval ( $_POST['type'] ) : 1 ;
        $search     = isset ( $_POST['search'] ) ? intval ( $_POST['search'] ) : 1 ;
        if($search == 1){
            $date_time  = isset ( $_POST['date_time'] ) ? $today-3600*24*intval ( $_POST['date_time'] ) : $today-3600*24*7 ;
            $result = $daily_data->field('new_users,date_time')->where('type='.$type.' AND date_time>='.$date_time)->order('date_time desc')->select();
            $sum = $daily_data->field('new_users,date_time')->where('type='.$type.' AND date_time>='.$date_time)->sum('new_users');     
            $num = $_POST['date_time']; 
        }else{
            if(isset($_POST['btime']) && isset($_POST['etime'])){ 
                if(strtotime($_POST['btime'])<strtotime($_POST['etime']))
                {
                    $btime = strtotime($_POST['btime']);
                    $etime = strtotime($_POST['etime']); 
                }else{
                    $btime = strtotime($_POST['etime']);
                    $etime = strtotime($_POST['btime']); 
                }
            } else {
                $btime = $today-3600*24*7;
                $etime = $today;
            }
            $result = $daily_data->field('new_users,date_time')->where('type='.$type.' AND date_time>='.$btime.' AND date_time<='.$etime)->order('date_time desc')->select();
            $sum = $daily_data->field('new_users,date_time')->where('type='.$type.' AND date_time>='.$btime.' AND date_time<='.$etime)->sum('new_users'); 
            $num = ($etime-$btime)/86400;
        }
                
        foreach ($result as $key => $value)
        {
            $quxianArray[] = '['.($value['date_time']*1000).','.$value['new_users'].']';
            $list .= '<tr><td>';
            $list .= date('Y-m-d',$value['date_time']).'</td><td>';
            $list .= $value['new_users'].'</td><td>';
            $list .=  sprintf('%.2f%%',$value['new_users']/$sum*100).'</td></tr>';
        }
        $quxian = '['.implode(',',$quxianArray).']';    
        $res = json_encode(array('quxian'=>$quxian,'list'=>$list,'num'=>$num)) ;
        echo $res;
    }
}