<?php

/**
 * 新闻分类操作类
 */
class CategoryAction extends CommonAction {

	/**
	 * 分类列表
	 */
    public function index() {
		//处理分类显示层级
		$models = D('Category');
		$this->assign('list', $models->categoryList());
		$this->display();
	}

	/**
	 * 增加分类
	 */
	public function add() {
		if($this->isPost()) {
			$model = D('Category');
			$this->_insert($model);
			$this->_JumpGO('新闻栏目分类增加成功', 'succeed', Url('index'));
		}
		$models = D('Category');
		$this->assign('cateList', $models->categoryList());
		$this->display();	
	}

	/**
	 * 编辑分类
	 */
	public function edit() {
		$model = D('Category');
		if($this->isPost()) {
			$this->_update($model);
			$this->_JumpGo('栏目分类更新成功', 'succeed', Url('index'));
		}
		$this->assign('cateList', $model->categoryList());
		$this->_edit($model);
    }

    /**
     * 生成列表静态
     */
    public function makeHtml() {
        $this->assign('cateList', D('Category')->categoryList());
        $this->display();
    }

    /**
     * 根据catid 资讯列表生成静态
     */
    public function makeListHtml() {
        import("@.ORG.Util.Page");
        //获取参数
        $catid = Input::getVar($_REQUEST['catid']);
        $listRows = isset($_REQUEST['listRows']) ? Input::getVar($_REQUEST['listRows']) : 20;
        $page = max(1, Input::getVar($_REQUEST['page']));
        $totpage = Input::getVar($_REQUEST['page']);

        //读取数据
        $model = D('Article');
        $map = 'status=1&ishtml=1';
        $count = $model->where($map)->count('aid');
        $p = new Page($count, $listRows);
        $lists = $model->where($map)->order('dateline DESC')->limit($p->firstRow .',' . $p->listRows)->select();
        $page = $p->show();
        //赋值
        $this->assign('page', $page);
        $this->assign('list', $lists);
        $content = $this->fetch('list');
        echo $content;
    }

	/**
	 * 永久删除分类
	 */
	public function del() {
		$model = D('Category');
		$this->_delete($model);
		$this->_JumpGo('分类删除成功', 'succeed', Url('index'));
	}
}
