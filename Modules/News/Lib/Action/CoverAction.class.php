<?php

class CoverAction extends CommonAction {

    //封面页列表
    public function index() {
        $lists = M('news_cover')->select();
        $this->assign('lists', $lists);
        $this->display();
    }

    public function add() {

        if($this->isPost()) {
            $data = array(
                'title' => Input::getVar($_POST['title']),
                'keywords' => Input::getVar($_POST['keywords']),
                'description' => Input::getVar($_POST['description']),
                'head' => Input::getVar($_POST['head']),
                'template' => Input::getVar($_POST['template']),
                'path' => Input::getVar($_POST['path']),
                'addtime' => time(),
                'adduser' => Session('admin_username'),
            );
            $rst = M('news_cover')->add($data);
            $this->_jumpGo('新封面页添加成功', 'succeed', Url('index'));
        }
        $this->display();
    }

    public function edit() {
        $id = Input::getVar($_GET['id']);
        if($this->isPost()) {
            $data = array(
                'title' => Input::getVar($_POST['title']),
                'keywords' => Input::getVar($_POST['keywords']),
                'description' => Input::getVar($_POST['description']),
                'template' => Input::getVar($_POST['template']),
                'head' => Input::getVar($_POST['head']),
                'path' => Input::getVar($_POST['path']),
                'updatetime' => time(),
                'updateuser' => Session('admin_username'),
            );
            M('news_cover')->where(array('id' => $id))->save($data);
            $this->_jumpGo('新封面页编辑成功', 'succeed', Url('index'));
        }
        $cover = M('news_cover')->where(array('id' => $id))->find();
        $this->assign('cover', $cover);
        $this->display();
    }

    public function del() {
        $id = Input::getVar($_GET['id']);
        M('news_cover')->where(array('id' => $id))->delete();
        $this->_jumpGo('新封面页删除成功', 'succeed', Url('index'));
    }

    /**
     * 更新静态页面
     */
    public function update() {
        $id = Input::getVar($_GET['id']);
        $cover = M('news_cover')->where(array('id' => $id))->find();
        $this->assign('title', $cover['title']);
        $this->assign('keywords', $cover['keywords']);
        $this->assign('description', $cover['description']);
        $this->assign('head', $cover['head']);
        $GLOBALS['makehtml'] = true;
        $content = $this->fetch(CODE_RUNTIME_PATH . '/Modules/News/Tpl/HtmlTpl/'.$cover['template']);
        if($GLOBALS['makehtml'] !== true){

            $this->_jumpGo('新封面页更新失败', 'error', Url('index'));
        }
        $path = CODE_RUNTIME_PATH . '/Html/' . $cover['path'];
        $make = new MakeHtml(dirname($path));
        if($make->make(basename($path), $content)) {
            $this->_jumpGo('新封面页更新成功', 'succeed', Url('index'));
        }
        $this->_jumpGo('新封面页更新失败', 'error', Url('index'));
    }

    public function blockitem() {
        $cid = Input::getVar($_GET['cid']);
        $cover = M('news_cover')->where(array('id' => $cid))->find();
        $block = D('BlockItem')->where(array('cid' => $cid))->select();
        $this->assign('cover', $cover);
        $this->assign('block', $block);
        $this->display();
    }

    public function blockadd() {
        $cid = Input::getVar($_REQUEST['cid']);
        if($this->isPost()) {
            $data = array(
                'name' => Input::getVar($_POST['name']),
                'number' => Input::getVar($_POST['number']),
                'cid' => $cid,
            );
            D('BlockItem')->add($data);
            $this->_jumpGo('区块增加成功', 'succeed', Url('blockitem?cid='.$cid));
        }
        $this->assign('cid', $cid);
        $this->display();
    }

    public function blockedit() {
        $id = Input::getVar($_REQUEST['id']);
        $cid = Input::getVar($_REQUEST['cid']);
        if($this->isPost()) {
            $data = array(
                'name' => Input::getVar($_POST['name']),
                'number' => Input::getVar($_POST['number']),
            );
            M('news_blockitem')->where(array('id' => $id))->save($data);
            $this->_jumpGo('编辑成功', 'succeed', Url('blockitem?cid='.$cid));
        }
        $blockitem = D('BlockItem')->where(array('id' => $id))->find();
        $this->assign('blockitem', $blockitem);
        $this->assign('id', $id);
        $this->display();
    }

    public function blockdel() {
        $id = Input::getVar($_GET['id']);
        $cid = Input::getVar($_GET['cid']);
        D('BlockItem')->where(array('id' => $id))->delete();
        $this->_jumpGo('删除成功', 'succeed', Url('blockitem?cid='.$cid));
    }

    public function blockdata() {
        import('@.ORG.Util.String');
        $op = Input::getVar($_GET['op']);
        $cid = Input::getVar($_GET['cid']);
        $blockid = Input::getVar($_GET['blockid']);
        if($op == 'list') {
            $cover = M('news_cover')->where(array('id' => $cid))->find();
            $block = M('news_blockitem')->where(array('id' => $blockid))->find();
            $blockdata = M('news_blockdata')->where(array('blockid' => $blockid))->order('`display` ASC')->select();
            foreach($blockdata as $key => $val) {
                $blockdata[$key]['description'] = String::msubstr($val['description'], 0, 50);
            }
            $this->assign('cover', $cover);
            $this->assign('blockdata', $blockdata);
            $this->assign('block', $block);
            $this->display('blockdata');
        } elseif($op == 'add') {
            $blockid = Input::getVar($_GET['blockid']);
            $cid = Input::getVar($_GET['cid']);
            if($this->isPost()) {
                $data = array(
                    'blockid' => $blockid,
                    'title' => Input::getVar($_POST['title']),
                    'description' => Input::getVar($_POST['description']),
                    'url' => Input::getVar($_POST['url']),
                    'display' => Input::getVar($_POST['display']),
                    'pic' => 0,
                );
                //处理图片
                if($_FILES['image']['name']) {
                    $rest = $this->uploads();
                    if($rest['errno']) {
                        $this->_jumpGo($rest['message'], 'error');
                    } else {
                        $data['image'] = $rest['path'];
                        $data['pic'] = 1;
                    }
                }

                M('news_blockdata')->add($data);
                $this->_jumpGo("数据添加成功", 'succeed', Url('blockdata?op=list&cid='.$cid.'&blockid='.$blockid));
            }
            $block = M('news_blockitem')->where(array('id' => $blockid))->find();
            $this->assign('blockid', $blockid);
            $this->assign('block', $block);
            $this->assign('cid', $cid);
            $this->display('blockdataadd');
        } elseif($op == 'edit') {
            $id = Input::getVar($_GET['id']);
            $blockid = Input::getVar($_GET['blockid']);
            $cid = Input::getVar($_GET['cid']);
            if($this->isPost()) {
                $data = array(
                    'title' => Input::getVar($_POST['title']),
                    'description' => Input::getVar($_POST['description']),
                    'url' => Input::getVar($_POST['url']),
                    'display' => Input::getVar($_POST['display']),
                );
                if(Input::getVar($_POST['delpic'])) {
                    $data['pic'] = 0;
                    $data['image'] = '';
                }
                if($_FILES['image']['name']) {
                    //处理图片
                    $rest = $this->uploads();
                    if($rest['errno']) {
                        $this->_jumpGo($rest['message'], 'error');
                    } else {
                        $data['image'] = $rest['path'];
                        $data['pic'] = 1;
                    }
                }

                M('news_blockdata')->where(array('id' => $id))->save($data);
                $this->_jumpGo("编辑成功", 'succeed', Url('blockdata?op=list&cid='.$cid.'&blockid='.$blockid));
            }
            $data = M('news_blockdata')->where(array('id' => $id))->find();
            $block = M('news_blockitem')->where(array('blockid' => $id))->find();
            $this->assign('data', $data);
            $this->assign('blockid', $blockid);
            $this->assign('block', $block);
            $this->assign('cid', $cid);
            $this->display('blockdataedit');
        } elseif($op == 'del') {
            $id = Input::getVar($_GET['id']);
            $blockid = Input::getVar($_GET['blockid']);
            $cid = Input::getVar($_GET['cid']);
            M('news_blockdata')->where(array('id' => $id))->delete();
            $this->_jumpGo('数据删除成功', 'succeed', Url('blockdata?op=list&cid='.$cid.'&blockid='.$blockid));
        }

    }

    private function uploads() {
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        $cfg = array(
            'ext' => C('UPLOAD_ALLOW_EXT'),
            'size' => C('UPLOAD_MAXSIZE'),
            'path' => C('UPLOAD_PATH'),
        );
        $upload->config($cfg);
        $rest = $upload->uploadFile('image');
        if($rest['errno']) {
            $rest['message'] = $upload->error();
        }
        return $rest;
    }

}
