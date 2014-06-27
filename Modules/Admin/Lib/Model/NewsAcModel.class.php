<?php

class NewsAcModel extends CommonModel {

    protected $tableName = 'admin_newsac';

    public function getAuth($gid) {
        $auth = $this->where('`gid` = '.$gid)->select();
        $result = array();
        foreach($auth as $val) {
            $result[$val['catid']][$val['auth']] = true;
        }
        return $result;
    }

    public function getAuth2($gid) {
        $auth = $this->where('`gid` = '.$gid)->select();
        $result = array();
        $au = array('see', 'add', 'edit', 'del', 'html', 'status');
        foreach($auth as $val) {
            foreach($au as $auval) {
                if($val['auth'] == $auval) {
                    $result[$auval][] = $val['catid'];
                }
            }
        }
        return $result;
    }
}
