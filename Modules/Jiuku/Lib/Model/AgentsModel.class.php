<?php

/**
 *
 */
class AgentsModel extends Model {
    /**
     * 初始化
     */
    public function _initialize() {
        import('@.ORG.Util.String');
    }

    /**
     * 返回代理商列表
     */
    public function agentsList($type = false,$map = array()) {
        $return = array();
        $map = array_merge($map,array('is_del'=>'-1'));
        $return = D('Agents')->field('id,fname,cname')->where(array('is_del'=>'-1'))->select();
        if($type == 'keyid'){
            $list = array();
            foreach($return as $key=>$val){
                $list[$val['id']] = $val;
            }
            $return = $list;
        }
        return $return;
    }

    /**
     * 返回网络销售渠道列表
     */
    public function internetSalesList($type = false,$map = array()) {
        $return = array();
        $map = array_merge($map,array('is_del'=>'-1'));
        $return = D('AgentsInternetSales')->field('id,name')->where($map)->select();
        if($type == 'keyid'){
            $list = array();
            foreach($return as $key=>$val){
                $list[$val['id']] = $val;
            }
            $return = $list;
        }
        return $return;
    }

    /**
     * 返回实体销售渠道列表
     */
    public function storeSalesList($type = false,$map = array()) {
        $return = array();
        $map = array_merge($map,array('is_del'=>'-1'));
        $return = D('AgentsStoreSales')->field('id,name')->where($map)->select();
        if($type == 'keyid'){
            $list = array();
            foreach($return as $key=>$val){
                $list[$val['id']] = $val;
            }
            $return = $list;
        }
        return $return;
    }
}
