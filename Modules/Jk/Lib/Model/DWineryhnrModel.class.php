<?php

/**
 *
 */
class DWineryhnrModel extends Model {

    protected $trueTableName = 'jk_d_wineryhnr';

    public function getList(){
        static $list;
        if(!$list){
            $list = $this->select();
        }
        return $list;
    }
    public function getTreeList($pid = 0, $prefix = '|--'){
        static $treelist;
        $list = $this->getList();
        foreach($list as $key=>$val){
            if($val['pid'] == $pid){
                $treelist[] = array(
                    'id' => $val['id'],
                    'prefix' => ($prefix != '|--') ? $prefix : '',
                    'value' => $val['fname'].'&nbsp;'.$val['cname'],
                );
                $this->getTreeList($val['id'], '&nbsp;&nbsp;&nbsp;'.$prefix);
            }
        }
        return $treelist;
    }
    public function getSonIds($pid = 0){
        static $sonids;
        $list = $this->getList();
        foreach($list as $key=>$val){
            if($val['pid'] == $pid){
                $sonids[] = $val['id'];
                $this->getSonIds($val['id']);
            }
        }
        return $sonids;
    }
}