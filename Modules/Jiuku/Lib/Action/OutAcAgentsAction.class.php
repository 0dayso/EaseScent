<?php
/**
 * 代理商管理权限外方法
 */
class OutAcAgentsAction extends OutAcCommonAction {
    function ajaxInternetGetDetail(){
        if(!isset($_POST['id']) && intval($_POST['id']) == 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        $id = intval($_POST['id']);
        $res = D('AgentsInternetSalesWine')->where(array('id'=>$id,'is_del'=>'-1'))->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $res['internet_sales_res'] = D('AgentsInternetSales')->where(array('id'=>$res['internet_sales_id'],'is_del'=>'-1'))->find();
        $res['agents_res'] = D('Agents')->where(array('id'=>$res['agents_id'],'is_del'=>'-1'))->find();
        $res['wine_res'] = D('Wine')->where(array('id'=>$res['wine_id'],'merge_id'=>0,'is_del'=>'-1'))->find();
        $res['wine_caname_res'] = D('WineCaname')->where(array('id'=>$res['wine_caname_id'],'merge_id'=>0,'is_del'=>'-1'))->find();
        $res['img_res'] = D('AgentsInternetSalesWineImg')->where(array('internet_sales_wine_id'=>$res['id'],'is_del'=>'-1'))->order('queue asc')->select();
        //价格日志
        foreach(json_decode($res['price_log'],true) as $key=>$val){
            $res['price_res'][] = array('price'=>$val['p'],'date'=>date('Y-m-d H:i:s',$val['t']));
            if(count($res['price_res']) == 5) break;
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    function ajaxStoreGetDetail(){
        if(!isset($_POST['id']) && intval($_POST['id']) == 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        $id = intval($_POST['id']);
        $res = D('AgentsStoreSalesWine')->where(array('id'=>$id,'is_del'=>'-1'))->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $res['store_sales_res'] = D('AgentsStoreSales')->where(array('id'=>$res['store_sales_id'],'is_del'=>'-1'))->find();
        $res['agents_res'] = D('Agents')->where(array('id'=>$res['agents_id'],'is_del'=>'-1'))->find();
        $res['wine_res'] = D('Wine')->where(array('id'=>$res['wine_id'],'merge_id'=>0,'is_del'=>'-1'))->find();
        $res['wine_caname_res'] = D('WineCaname')->where(array('id'=>$res['wine_caname_id'],'merge_id'=>0,'is_del'=>'-1'))->find();
        $res['img_res'] = D('AgentsStoreSalesWineImg')->where(array('store_sales_wine_id'=>$res['id'],'is_del'=>'-1'))->order('queue asc')->select();
        //价格日志
        foreach(json_decode($res['price_log'],true) as $key=>$val){
            $res['price_res'][] = array('price'=>$val['p'],'date'=>date('Y-m-d H:i:s',$val['t']));
            if(count($res['price_res']) == 5) break;
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    function ajaxGetWineData(){
        if(!isset($_POST['id']) && intval($_POST['id']) == 0){
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        }
        $id = intval($_POST['id']);
        $res = D('Wine')->field('id,fname,ename,country_id')->where(array('id'=>$id,'merge_id'=>0,'is_del'=>'-1'))->find();
        if(!$res){
            $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>NULL));
        }
        $res['country_res'] = D('Country')->field('id,fname,cname')->where(array('id'=>$res['country_id'],'is_del'=>'-1'))->find();
        foreach(D('JoinWineRegion')->where(array('wine_id'=>$id,'is_del'=>'-1'))->getfield('region_id',true) as $key=>$region_id){
            while($region_id){
                if($region_res = D('Region')->where(array('id'=>$region_id,'is_del'=>'-1'))->find())
                    $res['region_res'][$key][] = array('id'=>$region_res['id'],'fname'=>$region_res['fname'],'cname'=>$region_res['cname']);
                $region_id = $region_res['pid'];
            }
            if($res['region_res'][$key]) $res['region_res'][$key] = array_reverse($res['region_res'][$key]);
        }
        $caname_res = D('WineCaname')->where(array('cname'=>array('neq',''),'wine_id'=>$id))->select();
        foreach($caname_res as $key=>$val){
            if(trim($val['cname']) != '') $res['caname_res'][] = array('id'=>$val['id'],'cname'=>trim($val['cname']));
        }
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    function ajaxAddWineCaname(){
        if(!isset($_POST['id']) || intval($_POST['id']) == 0 || !isset($_POST['name']) || trim($_POST['name'] == ''))
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $wine_res = D('Wine')->where(array('id'=>$id,'merge_id'=>0,'is_del'=>'-1'))->find();
        if(!$wine_res)
            $this->echo_exit(array('errorCode'=>600002,'errorStr'=>'Wine Invalid'));
        $is_exist = D('WineCaname')->where(array('cname'=>$name,'wine_id'=>$id,'merge_id'=>0,'is_del'=>'-1'))->find();
        if($is_exist)
            $this->echo_exit(array('errorCode'=>600003,'errorStr'=>'Name Exist'));
        $is_hid = D('WineCaname')->where(array('wine_hid'=>$id,'is_del'=>'-1'))->find();
        $add_data = array('cname'=>$name,'fname'=>$wine_res['fname'],'ename'=>$wine_res['ename'],'wine_id'=>$id);
        if(!$is_hid)
            $add_data['wine_hid'] = $id;
        $res = D('WineCaname')->add($add_data);
        if(!$res)
            $this->echo_exit(array('errorCode'=>600004,'errorStr'=>'add Fail'));
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    function ajaxGetInternetSales(){
        if(!isset($_POST['id']) && intval($_POST['id']) == 0)
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        $id = intval($_POST['id']);
        $res = D('AgentsInternetSales')->field('id,name')->where(array('agents_id'=>$id,'is_del'=>'-1'))->select();
        if(!$res)
            $this->echo_exit(array('errorCode'=>600002,'errorStr'=>'No Data'));
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    function ajaxGetStoreSales(){
        if(!isset($_POST['id']) && intval($_POST['id']) == 0)
            $this->echo_exit(array('errorCode'=>600001,'errorStr'=>'Parameter Error'));
        $id = intval($_POST['id']);
        $res = D('AgentsStoreSales')->field('id,name')->where(array('agents_id'=>$id,'is_del'=>'-1'))->select();
        if(!$res)
            $this->echo_exit(array('errorCode'=>600002,'errorStr'=>'No Data'));
        $this->echo_exit(array('errorCode'=>0,'errorStr'=>'','result'=>$res));
    }
    function echo_exit($arr){
        if(empty($_GET['callback'])){
            echo json_encode($arr);
        }else{
            echo $_GET['callback'].'('.json_encode($arr).')';
        }
        exit();
    }
    function ajaxDropCapturData(){
        $id = intval(Input::getVar($_POST['id']));
        $res = D('CrawlWineData')->where(array('id'=>$id))->save(array('is_del'=>'1'));
        if($res){
            echo 1;
        }else{
            echo 0;
        }
    }
}
