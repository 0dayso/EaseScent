<?php

class UserGroupAcModel extends CommonModel {

    protected $tableName = 'admin_usergroup_ac';

    protected $_validate = array(
        array('gid', 'require', '用户组GID不能为空'),
        array('acid', 'require', '权限ACID不能为空'),
    );

    public function getAcurl($gid) {
        $list = $this->where('gid='.$gid)->select();
        $nlist = array();
        foreach($list as $val) {
            $nlist[] = $val['acid'];
        }
        return $nlist;
    }

}
