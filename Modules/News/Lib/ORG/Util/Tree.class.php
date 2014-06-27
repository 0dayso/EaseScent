<?php

/**
 * Tree类
 */

/**
 * 通用的树型类
 */
class Tree {
 
    /**
     * id pid name
     */
    protected $_arr = array();

    /**
     * 排好序的数组 
     */
    protected $_res = array();
    
    /**
     * key
     */
    protected $_id;

    /**
     * pid
     */
    protected $_pid;

    /**
     * 传入数组 
     */
    public function setArray($arr, $id = 'id', $pid = 'pid') {
        $this->_arr = is_array($arr) ? $arr : array();
        $this->_res = array();
        $this->_id = $id;
        $this->_pid = $pid;
    }

    /**
     * 获取排序后的数组
     */
    public function getTree($id = 0, $level = 1) {
        $number = $level;
        $level++;
        $arr = $this->_getChild($id);
        if(is_array($arr)) {
            $count = count($arr);
            foreach($arr as $k => $v) {
                $v['level'] = $number;
                $this->_res[] = $v;
                $this->getTree($v[$this->_id], $level);
            }
        }
    }
    
    /**
     * 判断一个ID是否在另一个ID的child里
     */
    public function getChildId($id) {
        $res = $this->_getChild($id);
        if(is_array($res)) {
            foreach($res as $v) {
                $this->_res[] = $v[$this->_id];
                $this->getChildId($v[$this->_id]);
            }
        }
    }

    /**
     * 返回结果
     */
    public function getRes() {
        return $this->_res;
    }

    /**
     * 返回下级数组集
     */
    protected function _getChild($id) {
        $arrs = array();
        foreach($this->_arr as $k => $v) {
            if($v[$this->_pid] == $id) {
                $arrs[] = $v;
            }
        }
        return empty($arrs) ? false : $arrs;
    }
}
