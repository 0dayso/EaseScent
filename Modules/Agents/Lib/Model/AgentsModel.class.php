<?php

/**
 *
 */
class AgentsModel extends Model {

    protected $trueTableName = 'jiuku_agents';

    /**
     * 返回代理商列表
     */
    public function getList($type = false,$map = array()) {
        $return = array();
        $map = array_merge($map,array('is_del'=>'-1'));
        $return = $this->field('id,fname,cname')->where($map)->select();
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
