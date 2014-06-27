<?php

/**
 * 友情链接管理控制器类
 *
 * @author    mengfk<mengfk@eswine.com>
 * @copyright Copyright (C) 2013 wine.cn All rights reserved.
 */


class LinksAction extends CommonAction {

    /**
     * 默认控制方法,友情链接列表展示
     * 
     * @return void
     */
    public function index() {
        $cid = Input::getVar($_GET['cid']);
        $where = !empty($cid) || $cid === '0' ? array('cid' => $cid) : '1' ;
        $links = M('news_links')->where($where)->select();
        $cate = M('news_links_cate')->select();
        $sort = array(
            0 => array('name' => '未分类', 'id' => 0),
        );
        foreach($cate as $c) {
            $sort[$c['id']] = $c;
        }
        $this->assign('cid', $cid);
        $this->assign('links', $links);
        $this->assign('cate', $sort);
        $this->display(); 
    }

    /**
     * 编辑友情链接内容
     *
     * @return void
     */
    public function edit() {
        $id = Input::getVar($_GET['id']);
        if($this->isPost()) {
            $data = array(
                'cid' => Input::getVar($_POST['cid']),    
                'title' => Input::getVar($_POST['title']),    
                'name' => Input::getVar($_POST['name']),    
                'url' => Input::getVar($_POST['url']),    
            );
            M('news_links')->where(array('id' => $id))->save($data);
            $this->_jumpGo("友情链接编辑成功", 'succeed', Url('index'));
        }
        $links = M('news_links')->where(array('id' => $id))->find();
        $cate = M('news_links_cate')->select();
        $this->assign('links', $links);
        $this->assign('cate', $cate);
        $this->display();
    }

    /**
     * 删除友情链接
     *
     * @return void
     */
    public function del() {
        $id = $_GET['id'];
        M('news_links')->where(array('id' => $id))->delete();
        $this->_jumpGo("友情链接删除成功", 'succeed', Url('index'));
    }

    /**
     * 增加友情链接
     *
     * @return void
     */
    public function add() {
        if($this->isPost()) {
            $data = array(
                'cid' => Input::getVar($_POST['cid']),
                'title' => Input::getVar($_POST['title']),    
                'name' => Input::getVar($_POST['name']),  
                'url' => Input::getVar($_POST['url']),  
            );
            M('news_links')->add($data);
            $this->_jumpGo('友情链接添加成功', 'succeed', Url('index'));
        }
        $cate = M('news_links_cate')->select();
        $this->assign('cate', $cate);
        $this->display();
    }

    /**
     * 友情链接分类管理
     *
     * @return void
     */
    public function cate() {
        $cate = M('news_links_cate')->select();
        $this->assign('cate', $cate);
        $this->display('cate');
    }

    /**
     * 增加友情链接分类
     *
     * @return void
     */
    public function addCate() {
        if($this->isPost()) {
            $name = Input::getVar($_POST['name']);
            if(empty($name)) {
                $this->_jumpGo('分类名称不能为空', 'error');
            }
            M('news_links_cate')->add(array('name' => $name));
            $this->_jumpGo("分类添加成功", 'succeed', Url('cate'));
        }

        $this->display();
    }

    /**
     * 编辑友情链接分类
     *
     * @return void
     */
    public function editCate() {
        $id = intval($_GET['id']);
        if($this->isPost()) {
            $name = Input::getVar($_POST['name']);
            if(empty($name)) {
                $this->_jumpGo('分类名称不能为空', 'error');
            }
            M('news_links_cate')->where(array('id' => $id))->save(array('name' => $name));
            $this->_jumpGo('分类名称修改成功', 'succeed', Url('cate'));
        }
        $cate = M('news_links_cate')->where(array('id' => $id))->find();
        $this->assign('cate', $cate);
        $this->display();
    }

    /**
     * 删除友情链接
     * 
     * 删除该分类后，所属该分类的友情链接将变成未分组状态
     *
     * @return void
     */
    public function delCate() {
        $id = intval($_GET['id']);
        M('news_links_cate')->where(array('id' => $id))->delete();
        M('news_links')->where(array('cid' => $id))->save(array('cid' => 0));
        $this->_jumpGo('分类名称删除成功', 'succeed', Url('cate'));
    }
}
