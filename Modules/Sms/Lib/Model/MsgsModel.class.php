<?php

class MsgsModel extends CommonModel
{
    
    protected $tableName = 'msgs';

    public function countSmsFailed($client_id = '')
    {
        $condition = array();
        $condition['status'] = 3;
        if (!empty($client_id))
            $condition['client_id'] = $client_id;
 
        return $this->where($condition)->count();
    }

    public function countSmsSuccess($client_id = '')
    {
        $condition = array();
        $condition['status'] = 2;
        if (!empty($client_id))
            $condition['client_id'] = $client_id;

        return $this->where($condition)->count();
    }

    public function countSmsTotal($client_id = '')
    {
        $condition = array();
        if (!empty($client_id))
            $condition['client_id'] = $client_id;
        return $this->where($condition)->count();
    }

    public function addMsg($data)
    {
        return  $this->add($data);
    }
}
