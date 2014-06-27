<?php

/**
 *
 */
class EvalpartyModel extends Model {
    /**
     * 初始化
     */
    public function _initialize() {
    }

    /**
     * 返回评价机构列表
     */
    public function getList($type = false,$map = array()) {
        $return = array();
        $map = array_merge($map,array('is_del'=>'-1'));
        $return = $this->field('id,sname,fname,cname')->where($map)->select();
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
