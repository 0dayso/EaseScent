<?php

/**
 * 品牌/酒庄审核管理
 */
class ZhuangmethodAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }
    /**
     * 品牌/酒庄申请列表
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
        $map['status'] = $_GET['status'] ? $_GET['status'] : 0;
        $map['is_standard'] = 0;
        $res = D('AgentBrandlist')->where($map)->order('id DESC')->select();
        import('ORG.Util.Page');
        $count      = count($res);
        $Page       = new Page($count,15);
        $show       = $Page->show();
        //申请的品牌信息
        $list = D('AgentBrandlist')->where($map)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $key => $value) {
            //代理商信息
            $agent = D('Agents')->field('id,cname,tel')->where('id='.$value['agents_id'])->find();
            $list['agent_name'] = $agent['cname']; 
            $list['tel'] = $agent['tel']; 
            
            //所包含酒款信息
            $wine = D('AgentsWine')->where('brand_id='.$value['id'])->select();
            $list[$key]['wine'] = $wine;
            
            //国家信息
            $country = D('Country')->where('id='.$value['country_id'])->find();
            $list[$key]['country_fname'] = $country['fname'];
            $list[$key]['country_cname'] = $country['cname'];

            //产区信息
            if ($value['region_id_big'] != 0) {
                $reg = D('Region')->where('id='.$value['region_id_big'])->find();
                $list[$key]['region_one_fname'] = $reg['fname']; 
                $list[$key]['region_one_cname'] = $reg['cname'];
                
                if (!empty($value['region_id_small'])) {
                    $treg = D('Region')->where('pid='.$value['region_id_big'])->find();
                    $list[$key]['region_two_fname'] = $treg['fname']; 
                    $list[$key]['region_two_cname'] = $treg['cname'];
                }
            }
        }
        
        $this->assign('list', $list);
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 品牌/酒庄审核通过操作
     */
    public function method() {
        $wid = Input::getVar($_REQUEST['wid']);
        $bid = Input::getVar($_REQUEST['bid']);

        $brand = D('Winery')->where('id='.$wid)->find();

        $data['fname'] = $brand['fname'];
        $data['cname'] = $brand['cname'];
        $data['status'] = 1;
        $data['winery_id'] = $wid;
        $res = D('AgentBrandlist')->where('id='.$bid)->save($data);
        if ($res != false) {
            echo 1;
        }else{
            echo 0;
        }
    }

    /**
     * 审核驳回操作
     */
    public function back(){
        $id = Input::getVar($_REQUEST['id']);
        $this->assign('id', $id);
        $this->display();
    }

    public function back_do(){
        $bid = Input::getVar($_REQUEST['id']);
        $info = $_REQUEST['info'];

        $data['status'] = 2;
        $res = D('AgentBrandlist')->where('id='.$bid)->save($data);
        if ($res) {
            echo 1;
        }else{
            echo 0;
        }
    }

}
