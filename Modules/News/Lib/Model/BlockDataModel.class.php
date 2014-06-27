<?php

class BlockDataModel extends CommonModel {

    protected $tableName = 'news_blockdata';

    public function getData($blockid) {
        $limit = D('NewsBlockitem')->where(array('id'=>$blockid))->getfield('number');
        $data = $this->where(array('blockid' => $blockid))->order('`display` ASC')->limit($limit)->select();
        foreach($data as $key=>$val){
            $data[$key]['image'] = C('TMPL_PARSE_STRING.__UPLOAD__').'News'.$val['image'];
        }
        return !empty($data) ? $data : array();
    }
}