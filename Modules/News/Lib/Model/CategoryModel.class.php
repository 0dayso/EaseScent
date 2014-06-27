<?php

/**
 * 分类模型
 */
class CategoryModel extends Model {

	protected $tableName = 'news_category';

	protected $_validate = array(
		array('pid','number','父分类必须选择',1),
		array('name', 'require', '栏目分类名称必须填写', 1),
	);

	/**
	 * 返回格式化后的栏目分类列表
	 */
	public function categoryList() {
		import('@.ORG.Util.Tree');
		$tree = new Tree();
		$tree->setArray($this->select(), 'catid', 'pid');
		$tree->getTree();
		$res = $tree->getRes();
		$rows = array();
	    foreach($res as $v) {
            $name = str_repeat('　　', $v['level'] -1 ). (($v['level'] -1) ? '|--' : '') . $v['name'];
            $v['name'] = $name;
            $rows[] = $v;
		}
		return $rows;
	}

    /**
     * 返回以catid为键名的分类数组
     */
    public function lists() {
        $res = $this->select();
        $nres = array();
        foreach($res as $key => $val) {
            $nres[$val['catid']] = $val;
        }
        return $nres;
    }

    /**
     * 查找该分类的所有子类
     */
    public function getSonsCatID($catid, $mode = true) {
        static $cates = array(), $ids = array();
        if(!$cates) {
            $cates = $this->lists();
        }
        if($mode) {
            $ids = array();
        }
        foreach($cates as $key => $cat) {
            if($cat['pid'] == $catid) {
                $ids[] = $key;
                $this->getSonsCatID($key, false);
            }
        }
        return $ids;
    }

    /**
     * 查找该分类的所属父类队列
     */
    public function getParentsCats($catid, $catpid) {
        static $cates = array(), $return = array();
        if(!$cates) {
            $cates = $this->lists();
        }
        if($catid) {
            $catpid = $cates[$catid]['pid'];
            $return = array($cates[$catid]);
        }
        if($catpid){
            foreach($cates as $key => $cat) {
                if($cat['catid'] == $catpid) {
                    $return[] = $cat;
                    $this->getParentsCat(false, $cat['pid']);
                }
            }
        }
        return array_reverse($return);
    }

}
