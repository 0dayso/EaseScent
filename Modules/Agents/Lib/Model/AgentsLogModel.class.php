<?php

/**
 *
 */
class AgentsLogModel extends Model {

    protected $trueTableName = 'jiuku_agents_log';

    /**
     * 存储代理商相关操作日志
     */
    public function savelog($table='', $tid=0, $type=0, $aid=0, $time=0){
        if(!$table || !$tid || !$type)
            return false;
        $aid = $_SESSION['admin_uid'];
        $time = time();
        $data = array(
            'table' => $table,
            'tid' => $tid,
            'type' => $type,
            'aid' => $aid,
            'time' => $time,
        );
        $this->add($data);
    }
}
