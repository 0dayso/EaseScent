<?php

class PlatformModel extends CommonModel
{
    protected $tableName = 'platform';

    public function getList()
    {
        return $this->order('id ASC')->select();
    }

    public function addPlatform($data)
    {
        return $this->add($data);
    }

    public function update($data, $where)
    {
        $opt['where'] = $where;
        return $this->save($data, $opt);
    }

    public function deletePlatformById($id)
    {
        return $this->where("`id`={$id}")->delete();
    }
}
