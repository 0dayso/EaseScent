<?php

/**
 * 资讯发布,管理等操作
 */
class PubAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
        $this->auth = $_SESSION['news_auth'];
    }

    /**
     * 新闻列表
     */
    public function index() {
        $model = D('Article');
        $cateModel = D('Category');
        $map = ' 1 ';
        $url = '';
        $catid = Input::getVar($_REQUEST['catid']);
        $keyword = Input::getVar($_REQUEST['keyword']);
        $adduser = Input::getVar($_REQUEST['adduser']);
        $btime = Input::getVar($_REQUEST['btime']);
        $etime = Input::getVar($_REQUEST['etime']);
        $status = Input::getVar($_REQUEST['status']);
        if($catid) {
            $map .= ' AND `catid`=' . $catid;
            $url .= '&catid='.$catid;
        }
        if($keyword) {
            $map .= ' AND `title` LIKE "%'.$keyword.'%"';
            $url .= '&keyword=' . $keyword;
        }
        if($adduser) {
            $map .= ' AND `createuser` LIKE "%'.$adduser.'%"';
            $url .= '&adduser=' . $adduser;
        }
        if($btime) {
            $bdatetime = strtotime($btime);
            $map .= ' AND `createtime` > "'.$bdatetime.'"';
            $url .= '&btime=' . $btime;
        }
        if($etime) {
            $edatetime = strtotime($etime);
            $map .= ' AND `createtime` < "'.$edatetime.'"';
            $url .= '&etime=' . $etime;
        }
        if($status) {
            $map .= ' AND status="'.$status.'"';
            $url .= '&status='.$status;
        }

        //显示有权限的栏目
        $catin = $this->auth['see'] ? implode(',', $this->auth['see']): 0;
        $map .= ' AND catid IN ('.$catin.')';

        $list = $this->_list($model, $map, 14, $url);
        $this->assign('list', $list);
        $this->assign('cateList', $cateModel->categoryList());
        $this->assign('category', $cateModel->lists());
        $this->display();
    }

    /**
     * 审核/待审核转变
     */
    public function chgStatus() {
        $aid = Input::getVar($_GET['aid']);
        if(!$this->_checkNewsAuth($aid, 'status')) {
            $this->_jumpGo('你无权审核此篇文章', 'error');
        }

        $status = Input::getvar($_GET['status']);
        D('Article')->where('aid='.$aid)->save(array('status' => $status));
        $this->_jumpGo('ID为'.$aid.'的文章状态更改成功,准备生成静态内容', 'succeed', Url('makeHtml?aid='.$aid));
    }

    /**
     * 新闻增加
     */
    public function add() {
        if($this->isPost()) {
            if(!$this->_checkNewsAuth(0, 'add', $_POST['catid'])) {
                $this->_jumpGo('文章发表失败，你所在的用户组无该权限', 'error');
            }
            $artid = $this->_insert(D('Article'));
            //内容中图片加水印
            preg_match_all("/<img.*?src=[\\\'| \\\"](.*?(?:[\.gif|\.jpg|\.png|\.bmp|\.jpeg]))[\\\'|\\\"].*?[\/]?>/", $_POST["content"], $imgs);
            foreach ($imgs[1] as $key => $value) {
                $img = parse_url($value);
                $url = C('UPLOAD_PATH').(str_replace('/News/', '', $img["path"]));
                if ($_POST["contentwater"] == 1) {
                    import('@.ORG.Util.Image.ThinkImage'); 
                    $wconf = $this->_get_water_conf();
                    $img = new ThinkImage(THINKIMAGE_GD, $url);
                    if ($wconf['markType'] === 'image') {
                        $img->open($url)->water($wconf['markImagePath'], $wconf['position'])->save($url);
                    }elseif ($wconf['markType'] === 'string') {
                        $img->open($url)->text($wconf['markString'], $wconf['fontstyle'],$wconf['fontSize'], $wconf['fontcolor'], $wconf['position'])->save($url);
                    }
                }
            }
            $_POST['aid'] = $artid;
            $this->_insert(D('ArticleContent'));
            //写入Tags
            D('Tags')->saveTags($_POST['keywords'], $artid);
            $this->_JumpGo('新闻发布成功,准备生成静态内容', 'succeed', Url('makeHtml?aid='.$artid));
        }

        $cate = D('Category')->categoryList();
        $newCate = array();
        foreach($cate as $cval) {
            if($this->_checkNewsAuth(0, 'add', $cval['catid'])) {
                $newCate[] = $cval;
            }
        }

        $this->assign('cateList', $newCate);
        $this->display();
    }

    /**
     * 资讯生成静态,单个生成
     */
    public function makeHtml() {

        $aid = Input::getVar($_GET['aid']);
        if(!$this->_checkNewsAuth($aid, 'html')) {
            $this->_jumpGo('你无权静态化此篇文章', 'error', Url('index'));
        }

        $article = D('Article')->where(array('aid' => $aid))->find();
        $articleContent = D('ArticleContent')->where('aid='.$aid)->find();
        $art = array_merge($article, $articleContent);
        $art['tags'] = D('Tags')->where(array('aid' => $aid))->select();
        //分页切割
        $artPages = explode('<hr style="page-break-after:always;" class="ke-pagebreak" />', $art['content']);
        if(is_array($artPages)) {
            $artPagesCount = count($artPages);
            list($url,$path) = A('Pub')->_createPathUrl($article['aid'], $article['dateline'], $article['catid']);
            $pglist = array(
                1 => $path
            );
            $pgKey = 1;
            list($fpath, $epath) = explode('.', $path);
            foreach($artPages as $artPnum) {
                if($pgKey > 1) {
                    $pglist[$pgKey] =  $fpath . '_' .$pgKey.'.'.$epath;
                }
                $pgKey++;
            }

            $make = new MakeHtml(CODE_RUNTIME_PATH.'/Html');
            $artKey = 1;
            foreach($artPages as $artPageVal) {
                $art['content'] = $artPageVal;
                $this->assign('vo', $art);
                $this->assign('page', $artKey);
                $this->assign('pglist', $pglist);
                $this->assign('title', $art['title']);
                $content = $this->fetch(CODE_RUNTIME_PATH . '/Modules/News/Tpl/HtmlTpl/article.html');
                $newsfile = $pglist[$artKey];
                $rst = $make->make( $newsfile, $content);
                $artKey++;
            }
        }
        if($rst) {
            //生成静态成功
            D('Article')->where('aid='.$aid)->save(array('path' => $path, 'url' => $url));
            $this->_jumpGo('文章生成静态成功', 'succeed', Url('index'));
        } else {
            $this->_jumpGo('文章生成静态失败', 'error', Url('index'));
        }
    }

    /**
     * 新闻编辑
     */
    public function edit() {
        $aid = Input::getVar($_REQUEST['aid']);
        if(!$this->_checkNewsAuth($aid, 'edit')) {
            $this->_jumpGo('你无权限编辑此篇文章', 'error');
        }
        if(!$aid) {
            $this->_jumpGo('参数为空!', 'error');
        }

        $model = D('Article');
        $modelC = D('ArticleContent');

        if($this->isPost()) {
            $this->_update($model);
            $modelC->create();
            $modelC->where('aid='.$aid)->save();
            //写入Tags
            D('Tags')->upTags($_POST['keywords'], $aid);
            $this->_jumpGo('文章更新成功,准备生成静态内容', 'succeed', Url('makeHtml?aid='.$aid));
        }
        $article = $model->where('aid='.$aid)->find();
        $content = $modelC->where('aid='.$aid)->find();
        $cate = D('Category')->categoryList();
        $newCate = array();
        foreach($cate as $cval) {
            if($this->_checkNewsAuth(0, 'edit', $cval['catid'])) {
                $newCate[] = $cval;
            }
        }
        $this->assign('cateList', $newCate);
        $this->assign('vo', array_merge($article, $content));
        $this->display();
    }

    /**
     * 新闻删除
     */
    public function del() {
        //获取aid
        $aid = Input::getVar($_REQUEST['aid']);
        //获取批量删除
        $aids = Input::getVar($_REQUEST['aids']);

        $model = D('Article');
        $modelC = D('ArticleContent');
        if($aid) {
            if(!$this->_checkNewsAuth($aid, 'del')) {
                $this->_jumpGo('你无权限删除此篇文章', 'error');
            }
            $map = 'aid=' . $aid;
        } elseif($aids) {
            //检出不能删除的文章ID
            $disallowAid = '';
            foreach($aids as $oneAid) {
                if(!$this->_checkNewsAuth($oneAid, 'del')) {
                    $disallowAid .= '|'.$oneAid;
                }
            }
            if($disallowAid) {
                $this->_jumpGo('你无权限删除这些文章：'.$disallowAid, 'error');
            }
            $map = 'aid IN ('.implode(',', $aids).')';
        }
        if($map) {
            $model->where($map)->delete();
            $modelC->where($map)->delete();
            D('News_tag_relationships')->where($map)->delete();
            $this->_jumpGo('删除成功,'. $map,'succeed', Url('News/Pub/index'));
        }
        $this->_jumpGo('删除失败，参数为空', 'error');
    }

    /**
     * 上传文件
     */
    public function uploads() {
        import('@.ORG.Util.Upload');
        $upload = new Upload();
        import('@.ORG.Util.Thumb');
        $thumb = new Thumb();
        if($_FILES['imgFile']){
            $cfg = array(
                'ext' => C('UPLOAD_ALLOW_EXT'),
                'size' => C('UPLOAD_MAXSIZE'),
                'path' => C('UPLOAD_PATH'),
            );
            $upload->config($cfg);
            $rest = $upload->uploadFile('imgFile');
            if($rest['errno']) {
                $result = array(
                    'error' => 1,
                    'message' => $upload->error(),
                );
                $this->_ajaxDisplay($result);
            }
            $result = array(
                'error' => 0,
                'url' => C('UPLOAD_WWWPATH').$rest['path'],
                'filename' => $rest['path'],
            );
            $this->_ajaxDisplay($result);
        }elseif($_FILES['imgFile2']){
            $cfg = array(
                'ext' => C('UPLOAD_ALLOW_EXT'),
                'size' => C('UPLOAD_MAXSIZE'),
                'path' => C('UPLOAD_PATH'),
            );
            $upload->config($cfg);
            $rest = $upload->uploadFile('imgFile2');
            if($rest['errno']) {
                $result = array(
                    'error' => 1,
                    'message' => $upload->error(),
                );
                $this->_ajaxDisplay($result);
            }
            $img = $thumb->createThumb($rest['realpath'], 300, 300);

            if ($_GET[w] === '1') {
                import('@.ORG.Util.Image.ThinkImage'); 
                $imgurl = explode('/', $rest['path']);
                $imgurl[count($imgurl)-1] = $img;
                $url = C('UPLOAD_PATH').implode('/', $imgurl);
                $wconf = $this->_get_water_conf();
                $img = new ThinkImage(THINKIMAGE_GD, $url);
                if ($wconf['markType'] === 'image') {
                    $img->open($url)->water($wconf['markImagePath'], $wconf['position'])->save($url);
                }elseif ($wconf['markType'] === 'string') {
                    $img->open($url)->text($wconf['markString'], $wconf['fontstyle'],$wconf['fontSize'], $wconf['fontcolor'], $wconf['position'])->save($url);
                }
                $turl = C('UPLOAD_WWWPATH').implode('/', $imgurl);
            }else{
                $turl = C('UPLOAD_WWWPATH').$rest['path'];
            }
            
            $result = array(
                'error' => 0,
                'url' => $turl,
                'filename' => $rest['path'],
            );

            echo "<script>
                parent.document.getElementById('imglist').innerHTML = \"<img src='".$result["url"]."' width='80' height='80'/><a href='###' onclick='delPic()'>删除缩略图</a>\";
                parent.document.getElementById('imglist').style.display='block';
                parent.document.getElementById('picval').value = '".$result["filename"]."';
                </script>";
        }
    }
    
    /**
     * 生成html生成路径
     */
    public function _createPathUrl($id, $dateline, $catid) {
        $cate = M('news_category')->where(array('catid' => $catid))->find();
        $rule = C('ARTICLE_SUBPATH_RULE');
        $search_replace = array(
            '{y}' => date('Y', $dateline),
            '{m}' => date('m', $dateline),
            '{d}' => date('d', $dateline),
            '{id}' => $id,
            '{catid}' => $catid,
        );
        foreach($search_replace as $key => $val) {
            $rule = str_replace($key , $val, $rule);
        }
        return array($cate['url_prefix'].$rule,$cate['path_prefix'].$rule);
    }

    /**
     * 检测该登录用户对指定aid文章是否有相应操作权限
     */
    private function _checkNewsAuth($aid, $auth, $catid = 0) {
        if(!$catid) {
            $article = D('Article')->where('`aid` = '.$aid)->field('catid')->find();
            $catid = $article['catid'];
        }
        if(in_array($catid, $this->auth[$auth])) {
            return true;
        }
        return false;
    }

    /**
     * 标题分词
     */
    public function _sptitle($keywords) {
        //导入类库
        import("@.ORG.Util.SplitWord");
        $sp = new SplitWord();
        $k = $sp->SplitRMM($keywords);
        //析放资源
        $sp->Clear();
        return $k;
    }

    /**
     * 切割分词后的关键词，用","分割，取前四个
     */
    public function _spwords($words) {
        $res = explode(' ',$this->_sptitle($words));
        foreach ($res as $key => $value) {
            $v = strlen($value); 
            if ($v > 3) {
                $resu[] = $value;
                $r = array_slice($resu,0,4);
                $result = implode(',',$r); 
            }
        }
        return $result;
    }

    /**
     * 得到分词结果，ajax返回
     */
    public function get_split_words(){
        $words = $_POST["words"];
        $result = $this->_spwords($words);
        if(empty($result)){
            $result = $words;
        }
        echo $result;
    }

    /**
     * 获取水印配置
     */
    public function _get_water_conf() {

        $path = dirname(__FILE__);
        $config = explode( ",", file_get_contents($path.'/watermark.config') );
        foreach( $config as $key=>$value ){
            $config[$key] = substr( strstr( $value, '='), 1 ); 
        }

        $config = array(
            'markType'      => $config[0],
            'markString'    => $config[1],
            'fontSize'      => $config[2],
            'position'      => $config[3],
            'markImagePath' => CODE_RUNTIME_PATH.$config[4],
            'fontcolor'     => $config[5],
            'fontstyle'     => CODE_RUNTIME_PATH.$config[6],
        );

        return $config;
    }
}
