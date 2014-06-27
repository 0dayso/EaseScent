<?php

/**
 * 酒款审核管理
 */
class JiumethodAction extends CommonAction {
    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $this->filter_http_data_ini();
    }
    /**
     * 酒款申请列表
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
        $map['au_status'] = $_GET['status'] ? $_GET['status'] : 0;
        $count = D('AgentsWine')->where($map)->count();
        import('ORG.Util.Page');
        $Page       = new Page($count,15);
        $show       = $Page->show();
        //申请酒款的信息
        $list = D('AgentsWine')->where($map)->order('add_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $key => $value) {
            //酒庄信息
            $winery = D('AgentBrandlist')->where('id='.$value['brand_id'])->find();
            $list[$key]['brand_fname'] = $winery['fname'];
            $list[$key]['brand_cname'] = $winery['cname'];

            //国家信息
            $country = D('Country')->where('id='.intval($value['country_id']))->find();
            $list[$key]['country_fname'] = $country['fname'];
            $list[$key]['country_cname'] = $country['cname'];

            //产区信息
            if ($value['region_id'] != null) {
                $rid = explode('::', $value['region_id']);
                if (!empty($rid[0])) {
                    $orid = explode('other-', $rid[0]);
                    if (empty($orid[0])) {
                        $list[$key]['region_cname_one'] = $orid[1];
                    }else{
                        $reg = D('Region')->where("id=$orid[0]")->find();
                        $list[$key]['region_fname_one'] = $reg['fname'];
                        $list[$key]['region_cname_one'] = $reg['cname'];
                        if (!empty($rid[1])) {
                            $orid2 = explode('other-', $rid[1]);
                            if (empty($orid2[0])) {
                                $list[$key]['region_cname_two'] = $$orid2[1];
                            }else{
                                $treg = D('Region')->where("id=$orid2[0]")->find();
                                $list[$key]['region_fname_two'] = $treg['fname'];
                                $list[$key]['region_cname_two'] = $treg['cname'];
                            }
                        }
                    }
                }
            }

            //葡萄品种信息
            $pt = $this->get_grape($value['id']);
            if (!empty($pt)) {
                foreach ($pt as $k => $v) {
                    $list[$key]['grape'][$k]['grape_fname'] = $v['fname'];
                    $list[$key]['grape'][$k]['grape_cname'] = $v['cname'];
                }
            }
            
            //价格信息
            $price = D('AgentsStoreSalesWine')->where('temp_id='.$value['id'])->select();
            $list[$key]['price_arr'] = $price;
        }
        
        $this->assign('list', $list);
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 审核信息
     */
    public function method() {
        $id = Input::getVar($_REQUEST['id']);
        $res = array();

        $res['id'] = $id;

        $agents = $this->get_agents($id);
        $res['agents'] = $agents['cname'];

        $wine = $this->get_wine($id);
        $res['wine_fname'] = $wine['fname'];
        $res['wine_cname'] = $wine['cname'];


        $brand = $this->get_brand($id);
        $res['brand_fname'] = $brand['fname'];
        $res['brand_cname'] = $brand['cname'];

        $region = $this->get_region($id);
        if ($region != null) {
            $rid = explode('::', $region);
            if (!empty($rid[0])) {
                $orid = explode('other-', $rid[0]);
                if (empty($orid[0])) {
                    $res['region_cname_one'] = $orid[1];
                }else{
                    $reg2 = D('Region')->where("id=$orid[0]")->find();
                    $res['region_fname_one'] = $reg2['fname'];
                    $res['region_cname_one'] = $reg2['cname'];
                    if (!empty($rid[1])) {
                        $orid2 = explode('other-', $rid[1]);
                        if (empty($orid2[0])) {
                            $res['region_cname_two'] = $$orid2[1];
                        }else{
                            $treg = D('Region')->where("id=$orid2[0]")->find();
                            $res['region_fname_two'] = $treg['fname'];
                            $res['region_cname_two'] = $treg['cname'];
                        }
                    }
                }
            }
        }

        $country = $this->get_country($id);
        $res['country_fname'] = $country['fname'];
        $res['country_cname'] = $country['cname'];

        $pt = $this->get_grape($id);
        if (!empty($pt)) {
            foreach ($pt as $k => $v) {
                $res['grape'][$k]['grape_fname'] = $v['fname'];
                $res['grape'][$k]['grape_cname'] = $v['cname'];
            }
        }

        $res['price'] = $this->get_wine_sales($id);
        $res['img'] = $this->get_wine_img($id);

        $this->assign('list', $res);
        $this->display();
    }

    /**
     * 审核通过操作
     */
    public function method_do() {
        $id = Input::getVar($_REQUEST['id']);
        $bid = Input::getVar($_REQUEST['bid']);

        $cawine = D('WineCaname')->where('id='.$bid)->find();

        $data['fname'] = $cawine['fname'];
        $data['cname'] = $cawine['cname'];
        $data['au_status'] = 3;
        $data['sale_status'] = 1;
        $data['std_id'] = $bid;
        $res = D('AgentsWine')->where('id='.$id)->save($data);

        $da['wine_id'] = $bid;
        $da['fname'] = $cawine['fname'];
        $da['cname'] = $cawine['cname'];
        $da['status'] = '1';
        $re = D('AgentsStoreSalesWine')->where('temp_id='.$id)->save($da);
        if ($res != false && $re != false) {
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
        $id = Input::getVar($_REQUEST['id']);
        $info = $_REQUEST['info'];

        $data = array('au_status'=>'2');
        $res = D('AgentsWine')->where('id='.$id)->setField($data);
        if ($res) {
            echo 1;
        }else{
            echo 0;
        }
    }

     /**
     * 审核删除操作
     */
     public function del(){
        $id = Input::getVar($_REQUEST['id']);
        $res = D('AgentsWine')->where('id='.$id)->delete();
        if ($res) {
            echo 1;
        }else{
            echo 0;
        }
     }

    //通过酒款ID获取AgentsWine中信息
    public function get_wine($id){
        $res = D('AgentsWine')->where('id='.$id)->find();
        return $res;
    }

    //通过酒款ID获取jiuku_agents_store_sales_wine中售卖信息
    public function get_wine_sales($id){
        $res = D('AgentsStoreSalesWine')->where('temp_id='.$id)->select();
        return $res;
    }

    //通过酒款ID获取jiuku_agents中代理商信息
    public function get_agents($id){
        $wine = $this->get_wine($id);
        $res = D('Agents')->where('id='.$wine['agents_id'])->find();
        return $res;
    }

    //通过酒款ID获取AgentBrandlist中酒庄信息
    public function get_brand($id){
        $wine = $this->get_wine($id);
        $res = D('AgentBrandlist')->where('id='.$wine['brand_id'])->find();
        return $res;
    }

    //通过酒款ID获取jiuku_country中国家信息
    public function get_country($id){
        $wine = $this->get_wine($id);
        $res = D('Country')->where('id='.$wine['country_id'])->find();
        return $res;
    }

    //通过酒款ID获取jiuku_Region中产区信息(No1)
    public function get_region($id){
        $wine = $this->get_wine($id);
        $res = $wine['region_id'];
        return $res;
    }

    //通过酒款ID获取jiuku_grape中葡萄品种信息
    public function get_grape($id){
        $wine = $this->get_wine($id);
        $res = D('Grape')->where(array('id'=>array('in',$wine['grape_id'])))->select();
        return $res;
    }

    //通过酒款ID获取jiuku_agents_store_sales_wine_img中图片信息
    public function get_wine_img($id){
        $wine = $this->get_wine_sales($id);
        foreach ($wine as $k => $v) {
            $res = D('AgentsStoreSalesWine_img')->where('store_sales_wine_id='.$v['id'])->select();
        }
        return $res;
    }

}
