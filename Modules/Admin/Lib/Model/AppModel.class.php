<?php

class AppModel extends CommonModel {

    protected $tableName = 'admin_app';

    public function getAppByIdKey($appid, $appkey) {
        return $this->where(array('appid' => $appid, 'appkey' => $appkey))->find();
    }    

    public function getApps() {
        return $this->order('appid ASC')->select();
    }

    public function updateAppNames($data, $where) {
        $opt['where'] = $where;
        return $this->save($data, $opt);
    }

    public function addApps($data) {
        return $this->add($data);
    }

    public function delByAppid($appid) {
        return $this->where('appid="'.$appid.'"')->delete();
    }
}
