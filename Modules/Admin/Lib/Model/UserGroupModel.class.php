<?php

class UserGroupModel extends CommonModel {

    protected $tableName = 'admin_usergroup';

    protected $_validate = array(
        array('name', 'require', '用户组名称不能为空'),
        array('name', '', '用户组名称已经存在', 0, 'unique', 1),
    );

    public function getList() {
        $list = $this->order('display ASC')->select();
        $nlist = array();
        foreach($list as $key => $val) {
            $nlist[$val['gid']] = $val;
        }
        return $nlist;
    }
}
