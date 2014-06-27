<?php

/**
 * 总代理审核管理
 */
class MethodAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }
    
    /**
     * 代理商申请列表
     */
    public function index() {
        if($this->isPost()) $this->listpage_post_to_get();
        $map = array();
        $url = '';
        if($_GET['keyword']) {
            $map_k['fname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['cname'] = array('like', '%'.$_GET['keyword'].'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $_GET['keyword'];
        }
        if($_GET['status']) {
            $map['status'] = $_GET['status'];
            $url .= '&status='.$_GET['status'];
        }
        $map['status'] = $_GET['status'] ? $_GET['status'] : 1;
        $res = D('AgentsApplication')->where($map)->group('agent_id,type_id')->select();
        import('ORG.Util.Page');
        $count      = count($res);
        $Page       = new Page($count,15);
        $show       = $Page->show();
        $list = D('AgentsApplication')->where($map)->group('agent_id,type_id')->order('add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        if (is_array($list)) {
        	foreach ($list as $k => $v) {
	            $agents = D('Agents')->field('cname,tel')->where('id='.$v['agent_id'])->find();
	            $list[$k]['agent_name'] = $agents['cname'];
	            $list[$k]['tel'] = $agents['tel'];
	        }
        }
        
        $this->assign('list', $list);
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 代理商审核和查看详情操作
     */
    public function method() {
        $aid = Input::getVar($_REQUEST['aid']);
        $type = Input::getVar($_REQUEST['type']);
        $type_id = Input::getVar($_REQUEST['type_id']);
        $mode = M();

        if ($type == 1) {
            $method = D('AgentsApplication')->where('type_id='.$type_id.' AND agent_id='.$aid)->find();
            $old = D('AgentsMethod')->where('winery_id ='.$type_id)->find();

            if ($old) {
            	$abne = D('Jiuku_Winery')->where('id='.$type_id)->find();
            	// foreach ($abn as $key => $value) {
            	// 	$abne[] = D('Wine')->where('id='.$value['wine_id'])->select();
            	// }
            }
        }else{
            $method = D('AgentsApplication')->where('type_id='.$type_id.' AND agent_id='.$aid)->find();
            $old = D('AgentsMethod')->where('wine_id ='.$type_id)->find();
            if ($old) {
            	$abne = D('Wine')->where('id='.$type_id)->find();
            }
        }

        $agent = D('Agents')->field('id,cname,tel')->where('id='.$aid)->find();
        $acc = $mode->table('ym_users')->where('id='.$aid)->find();
        if (!empty($abne)) {
        	 $old_ag = $mode->table('jiuku_agents')->where('id='.$old['agent_id'])->find();
        }
        
        $this->assign('abn', $abne);
        $this->assign('old', $old_ag);
        $this->assign('mt', $method);
        $this->assign('ag', $agent);
        $this->assign('ac', $acc);
        $this->display();
    }
    /**
     * 审核通过
     */
    public function method_do() {
        $abn = Input::getVar($_REQUEST['abn']);
        $data['agent_id'] = Input::getVar($_REQUEST['agent_id']);
        $data['type'] = Input::getVar($_REQUEST['type']);
        $data['type_id'] = Input::getVar($_REQUEST['type_id']);
        $data['method_time'] = time();
        
        if ($data['type'] == 1) {
            $data['winery_id'] = $data['type_id'];
            $tid = D('AgentsApplication')->field('wine_id')->where('type_id='.$data['type_id'].' AND agent_id='.$data['agent_id'])->select();
            foreach ($tid as $key => $value) {
                $data['wine_id'] = $value['wine_id'];
                $method = D('AgentsMethod')->add($data);
            }
            if (!empty($data)) {
            	$method_applocation_up = D('AgentsApplication')-> where('type_id='.$data['type_id'].' AND agent_id='.$data['agent_id'])->setField('status','2');
            }else{
            	echo '更新条件为空！';
            	exit;
            }
            

        }else{
            $data['wine_id'] = $data['type_id'];
            $method_applocation = D('AgentsApplication')->where('type_id='.$data['type_id'].' AND agent_id='.$data['agent_id'])->find();
            $data['winery_id'] = $method_applocation['winery_id'];
            $method = D('AgentsMethod')->add($data);
            if (!empty($data)) {
            	$method_applocation_up = D('AgentsApplication')-> where('type_id='.$data['type_id'].' AND agent_id='.$data['agent_id'])->setField('status','2');
            }else{
            	echo '更新条件为空！';
            	exit;
            }
        }
        if ($method && $method_applocation_up) {
            echo 1;
        }else{
            echo 0;
        }
    }

    /**
     * 驳回原因
     */
    public function back(){
        $type = Input::getVar($_REQUEST['type']);
        $type_id = Input::getVar($_REQUEST['type_id']);
        $aid = Input::getVar($_REQUEST['aid']);
        $this->assign('t', $type);
        $this->assign('tid', $type_id);
        $this->assign('aid', $aid);
        $this->display();
    }

    /**
     * 驳回操作
     */
    public function back_do(){
        $aid = Input::getVar($_REQUEST['aid']);
        $type = Input::getVar($_REQUEST['type']);
        $type_id = Input::getVar($_REQUEST['type_id']);
        $info = Input::getVar($_REQUEST['info']);
        $time = time();

        $data = array('status'=>'3','last_edit_time'=>$time,'back'=>$info);
        if (!empty($data)) {
        	if ($type == 1) {
	            $back = D('AgentsApplication')->where('type_id='.$type_id.' AND agent_id='.$aid)->setField($data);
	        }else{
	            $back = D('AgentsApplication')->where('wine_id='.$type_id.' AND agent_id='.$aid)->setField($data);
	        }
	        
	        if ($back) {
	            echo 1;
	        }else{
	            echo 0;
	        }
        }else{
        	echo '更新条件为空！';
        	exit;
        }   
    }
}
