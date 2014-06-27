<?php

/**
 *
 */
class StoreSalesModel extends Model {

    protected $trueTableName = 'jiuku_agents_store_sales';

    /**
     * 返回实体渠道列表
     */
    public function getList($type = false,$map = array()) {
        $return = array();
        $map = array_merge($map,array('is_del'=>'-1'));
        $return = $this->field('id,name')->where(array('is_del'=>'-1'))->select();
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
