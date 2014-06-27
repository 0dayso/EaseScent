<?php

class ClientModel extends CommonModel
{
    protected $tableName = 'clients';

    public function getClientList()
    {
        return $this->order('id ASC')->select();
    }

    public function getClientById($id)
    {
        return $this->where("`id`={$id}")->find();
    }

    public function addClient($data)
    {
        return $this->add($data);
    }

    public function updateClient($data, $where)
    {
        $opt['where'] = $where;
        return $this->save($data, $opt);
    }
}
