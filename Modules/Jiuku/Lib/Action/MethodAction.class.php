<?php

/**
 * 总代理审核管理
 */
class MethodAction extends CommonAction {

    /**
     * 代理商申请列表
     */
    public function index() {
        $keyword = Input::getVar($_REQUEST['keyword']);
        $status = Input::getVar($_REQUEST['status']);
        $type = Input::getVar($_REQUEST['type']);
        $map = array();
        $url = '';
        if($keyword) {
            $map_k['fname'] = array('like', '%'.$keyword.'%');
            $map_k['cname'] = array('like', '%'.$keyword.'%');
            $map_k['_logic'] = 'or';
            $map['_complex'] = $map_k;
            $url .= '&keyword=' . $keyword;
        }
        if($status) {
            $map['status'] = $status;
            $url .= '&status='.$status;
        }
        $map['status'] = !empty($status) ? $status : 1;
        if($type) {
            $map['type'] = $type;
            $url .= '&type='.$type;
        }
        $map['type'] = !empty($type) ? $type : 1;
        if ($$map['type'] = 1) {
            $group = 'type_id';
            $list = $this->_list(D('Agents_application'), $map, 15, $url, $group);
        }else{
            $list = $this->_list(D('Agents_application'), $map, 15, $url);
        }
        
        foreach ($list as $key => $value) {
            $agent = D('Agents')->field('fname,cname,tel')->where('id='.$value['agent_id'])->select();
            foreach ($agent as $k => $v) {
                $list[$key]['agent_fname'] = $v['fname']; 
                $list[$key]['agent_cname'] = $v['cname']; 
                $list[$key]['tel'] = $v['tel']; 
            }
        }

        //$this->ajaxReturn($list,'JSON');
        //dump($list);
        $this->assign('list', $list);
        $this->display();
    }
    /**
     * 代理商审核和查看详情操作
     */
    public function method() {
        $aid = Input::getVar($_REQUEST['aid']);
        $type = Input::getVar($_REQUEST['type']);
        $type_id = Input::getVar($_REQUEST['type_id']);

        if ($type == 1) {
            $method = D('Agents_application')->group('type_id')->where('type_id='.$type_id)->find();
            $wine_id = D('Agents_application')->field('wine_id')->where('type_id='.$type_id)->select();
            foreach ($wine_id as $key => $value) {
                $abn = D('Agents_method')->where('type_id='.$value['wine_id'])->find();
                if (!empty($abn)) {
                    $abne[] = $abn;
                }
            }
        }else{
            $method = D('Agents_application')->where('type_id='.$type_id)->find();
        }

        $agent = D('Agents')->where('id='.$aid)->find();
        $list = array_merge($method, $agent);
        $this->assign('abn', $abne);
        $this->assign('list', $list);
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
            $tid = D('Agents_application')->field('wine_id')->where('type_id='.$data['type_id'].' AND agent_id='.$data['agent_id'])->select();
            foreach ($tid as $key => $value) {
                $data['wine_id'] = $value['wine_id'];
                $method = D('Agents_method')->add($data);
            }
            $method_applocation_up = D('Agents_application')-> where('type_id='.$data['type_id'])->setField('status','2');
        }else{
            $data['wine_id'] = $data['type_id'];
            $method_applocation = D('Agents_application')->where('type_id='.$data['type_id'].' AND agent_id='.$data['agent_id'])->find();
            $data['winery_id'] = $method_applocation['winery_id'];
            $method = D('Agents_method')->add($data);
            $method_applocation_up = D('Agents_application')-> where('type_id='.$data['type_id'])->setField('status','2');
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
        $tid = Input::getVar($_REQUEST['type_id']);
        $this->assign('tid', $tid);
        $this->display();
    }

    /**
     * 驳回操作
     */
    public function back_do(){
        $tid = Input::getVar($_REQUEST['type_id']);
        $info = Input::getVar($_REQUEST['info']);

        $data = array('status'=>'3','back'=>$info);
        $back = D('Agents_application')->where('type_id='.$tid)->setField($data);
        //$sql = D('Agents_application')->getLastSql();dump($sql);die();
        if ($back) {
            echo 1;
        }else{
            echo 0;
        }
    }

    /**
     * 查看详情
     */
    public function detail(){
        $aid = Input::getVar($_REQUEST['aid']);
        $type = Input::getVar($_REQUEST['type']);
        $type_id = Input::getVar($_REQUEST['type_id']);

        if ($type == 1) {
            $method = D('Agents_application')->group('type_id')->where('type_id='.$type_id)->find();
            $wine_id = D('Agents_application')->field('wine_id')->where('type_id='.$type_id)->select();
            foreach ($wine_id as $key => $value) {
                $abn = D('Agents_method')->where('type_id='.$value['wine_id'])->find();
                if (!empty($abn)) {
                    $abne[] = $abn;
                }
            }
        }else{
            $method = D('Agents_application')->where('type_id='.$type_id)->find();
        }

        $agent = D('Agents')->where('id='.$aid)->find();
        $list = array_merge($method, $agent);
        $this->assign('abn', $abne);
        $this->assign('list', $list);
        $this->display();
    }
}