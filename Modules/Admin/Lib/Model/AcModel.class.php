<?php

class AcModel extends CommonModel {
    
    protected $tableName = 'admin_ac';

    protected $_validate = array(
        array('name', 'require', '节点名称不能为空'),
        array('url', 'require', '节点URL不能为空'),
    );

    public function getAcList() {
        import('@.ORG.Util.Tree');
        $tree = new Tree();
		$tree->setArray($this->select(), 'acid', 'pid');
		$tree->getTree();
		return $tree->getRes();
    }

}
