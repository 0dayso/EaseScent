<?php
//终端分布
class TerminalDistribuAction extends CommonAction {
    public function index(){
        $this->display();
    }

    //版本切换
    public function banben(){
        $type = $_GET['type'];
        $res = D('Terminal_distribution')->where('type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->select();
        echo json_encode($res);
    }

    public function fbanben(){
        $type = $_GET['type'];
        $res = D('Resolution_distribution')->where('type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->select();
        echo json_encode($res);
    }

    //点击日期
    public function zt(){
        $type = $_GET['type'];
        $t = strtotime('today')-3600*24;

        $list = D('Terminal_distribution')->field('model')->where('date_time >='.$t.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Terminal_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe');
    }

    public function fzt(){
        $type = $_GET['type'];
        $t = strtotime('today')-3600*24;

        $list = D('Resolution_distribution')->field('model')->where('date_time >='.$t.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Resolution_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        
        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe1');
    }

    public function week(){
        $type = $_GET['type'];
        $t = strtotime('today')-3600*24*7;
        $list = D('Terminal_distribution')->field('model')->where('date_time >='.$t.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Terminal_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe');
    }

    public function fweek(){
        $type = $_GET['type'];
        $t = strtotime('today')-3600*24*7;
        $list = D('Resolution_distribution')->field('model')->where('date_time >='.$t.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Resolution_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe1');
    }

    public function mounth(){
        $type = $_GET['type'];
        $t = strtotime('today')-3600*24;
        $list = D('Terminal_distribution')->field('model')->where('date_time >='.$t.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Terminal_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe');
    }

    public function fmounth(){
        $type = $_GET['type'];
        $t = strtotime('today')-3600*24*30;
        $list = D('Resolution_distribution')->field('model')->where('date_time >='.$t.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Resolution_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe1');
    }

    public function ajaxjx(){
        $type = $_GET['type']?$_GET['type']:1;
        $time = $_GET['t'];
        if ($time == 'week') {
            $t = strtotime('today')-3600*24*7;
        }elseif ($time == 'mounth') {
            $t = strtotime('today')-3600*24*30;
        }else{
            $t = strtotime('today')-3600*24;
        }

        $pic = D('Terminal_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->select();

        echo json_encode($pic);
    }

    public function ajaxfbl(){
        $type = $_GET['type']?$_GET['type']:1;
        $time = $_GET['t'];
        if ($time == 'week') {
            $t = strtotime('today')-3600*24*7;
        }elseif ($time == 'mounth') {
            $t = strtotime('today')-3600*24*30;
        }else{
            $t = strtotime('today')-3600*24;
        }

        $pic = D('Resolution_distribution')->where('date_time >='.$t.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->select();

        echo json_encode($pic);
    }

    //时间搜索
    public function jsh(){
        $type = $_GET['type']?$_GET['type']:1;
        $stime = strtotime($_GET['stime']);
        $etime = strtotime($_GET['etime']);
        $list = D('Terminal_distribution')->field('model')->where('date_time >='.$stime.' AND date_time<='.$etime.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Terminal_distribution')->where('date_time >='.$stime.' AND date_time <='.$etime.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe');
    }

    public function fsh(){
        $type = $_GET['type']?$_GET['type']:1;
        $stime = strtotime($_GET['stime']);
        $etime = strtotime($_GET['etime']);
        $list = D('Resolution_distribution')->field('model')->where('date_time >='.$stime.' AND date_time<='.$etime.' AND type='.$type)->group('model')->select();
        import('ORG.Util.Page');
        $count      = count($list);
        $Page       = new Page($count,3);
        $show       = $Page->show();
        $new = D('Resolution_distribution')->where('date_time >='.$stime.' AND date_time <='.$etime.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list', $new);
        $this->display('iframe1');

    }

    public function ajaxjsh(){
        $type = $_GET['type']?$_GET['type']:1;
        $stime = strtotime($_GET['stime']);
        $etime = strtotime($_GET['etime']);
        $res = D('Terminal_distribution')->where('date_time >='.$stime.' AND date_time <='.$etime.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->select();
        echo json_encode($res);
    }

    public function ajaxfsh(){
        $type = $_GET['type']?$_GET['type']:1;
        $stime = strtotime($_GET['stime']);
        $etime = strtotime($_GET['etime']);
        $res = D('Resolution_distribution')->where('date_time >='.$stime.' AND date_time <='.$etime.' AND type='.$type)->field("model,sum(`new_users`) AS new_users,avg(`new_user_ratio`) AS new_user_ratio,sum(`starts`) AS starts,avg(`start_ratio`) AS start_ratio")->group('model')->order('new_users DESC')->select();
        echo json_encode($res);
    }
}