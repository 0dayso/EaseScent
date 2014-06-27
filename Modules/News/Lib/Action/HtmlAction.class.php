<?php

/**
 * 控制生成静态页面类
 *
 * @author mengfk<mengfk@eswine.com>
 */
class HtmlAction extends CommonAction {

    public function _initialize() {
		parent::_initialize();
		import('@.ORG.Util.NewsPage');
		import('@.ORG.Util.String');
    }

    public function index() {

        $this->assign('cate', D('Category')->categoryList());
        $this->display();
    }

    /**
     * 资讯频道列表页静态页面更新
     *
     * 按栏目更新
     *
     */
    public function listsHtml() {
        //获取需要更新的栏目IDs
        if(Input::getVar($_POST['catid'])) {
            $catid = Input::getVar($_POST['catid']);
        } else {
            $catid = explode('|', Input::getVar($_GET['catid']));
        }
        //生成深度,若不存在 全部生成
        $pages = Input::getVar($_REQUEST['pages']);

        //每个栏目总页数
        $totpage = Input::getVar($_GET['totpage']);

        //当前要生成的页面
        $thispg = max(1, Input::getVar($_GET['thispg']));

        //数据总条数
        $total = Input::getVar($_GET['total']);

        //每页显示数据条数
        $prpage = 42;

        //过滤空分类ID
        $catid = array_filter($catid);

        if(!empty($catid)) {
            $tcat = $catid[0];
            $cat = D('Category')->where(array('catid' => $tcat))->find();
            //查找该分类的所有子分类
            $sCatIds = D('Category')->getSonsCatID($tcat);
            array_push($sCatIds, $tcat);
            //查找该分类的所属父类队列
            $pCats = D('Category')->getParentsCats($tcat);
            if(!$totpage || !$total) {
                $total = D('Article')->where(" `catid` IN (". implode(',', $sCatIds) .") AND `status` = 1 AND `dateline` < ".time())->count('*');
                $totpage = ceil($total/$prpage);
            }

            //获取真实所需的生成深度
            $tcpages = (!empty($pages) && $pages <= $totpage) ? $pages : $totpage ;

            if($tcpages >= $thispg) {
                $this->_createListHtml($prpage, $thispg, $total, $cat, $sCatIds, $pCats);
            } else {
                array_shift($catid);
                $thispg = $totpage = 0;
            }
            $newcatid = implode('|', $catid);
            $url = Url('News/Html/listsHtml?thispg='.($thispg + 1).'&catid='.$newcatid.'&pages='.$pages.'&totpage='.$totpage.'&total='.$total);
            $this->_jumpGo('生成静态', 'info', $url, 0);
        }
        $this->_jumpGo('生成完成！', 'succeed', Url('News/Html/index'));
    }

    /**
     * 资讯内容页静态化更新
     */
    public function pubHtml() {
        $op = Input::getVar($_REQUEST['op']);
        if($op == 'run') {
            $bt = Input::getVar($_REQUEST['btime']);
            $et = Input::getVar($_REQUEST['etime']);
            $catid = Input::getVar($_REQUEST['catid']);
            $page = max(1, Input::getVar($_REQUEST['page']));
            $limit = Input::getVar($_REQUEST['limit']);
            $begin = ($page-1) * $limit;
            $total = Input::getVar($_REQUEST['total']);
            $btime = $bt ? strtotime($bt): 0;
            $etime = $et ? strtotime($et): time();
            $map = 'status=1'.($catid ? ' AND catid='.$catid: '').($btime ? ' AND dateline > '.$btime: '').($etime ? ' AND dateline < '.$etime: '');
            if(!$total) {
                $tot = D('Article')->where($map)->field('COUNT(*) as num')->find();
                $total = $tot['num'];
            }
            $res = D('Article')->where($map)->order('aid DESC')->limit($begin, $limit)->select();
            $make = new MakeHtml(CODE_RUNTIME_PATH.'/Html');
            //取出cate中设置的文章生成目录
            $cate = D('Category')->lists();
            $articleContentModel = D('ArticleContent');
            $resultInfo = '';
            $succeedNum = 0;
            $failedNum = 0;
            foreach($res as $k => $val) {
                $articleContent = $articleContentModel->where('aid='.$val['aid'])->find();
                $art = array_merge($val, $articleContent);
                $art['tags'] = D('Tags')->where(array('aid' => $val['aid']))->select();
                //分页切割
                $artPages = explode('<hr style="page-break-after:always;" class="ke-pagebreak" />', $art['content']);
                if(is_array($artPages)) {
                    $artPagesCount = count($artPages);
                    list($url,$path) = A('Pub')->_createPathUrl($art['aid'], $art['dateline'], $art['catid']);
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
                $resultInfo .= $val['aid'] . ',';
                if($rst) {
                    D('Article')->where('aid='.$val['aid'])->save(array('path' => $path, 'url' => $url));
                    $succeedNum++;
                } else {
                    $failedNum++;
                }
            }
            if(($page * $limit) > $total) {
                $this->_jumpGo('文章生成静态完成,共生成'.$total.'页,成功'.$succeedNum.'页，失败'.$failedNum.'页', 'info', Url('index'), '5');
            } else {
                $page++;
            }
            $this->_jumpGo($resultInfo.'正在生成第'.$begin.'到'.($begin+$limit).'页...', 'info', Url("pubHtml?op=run&btime={$bt}&etime={$et}&catid={$catid}&page={$page}&limit={$limit}&total={$total}"));
        } else {
            $cateModel = D('Category');
            $this->assign('cateList', $cateModel->categoryList());
            $this->display();
        }
    }

    /**
     * 生成列表文件
     *
     * @param $prpage 每页显示多少条资讯
     * @param $cat 栏目信息
     * @param $thispg 当前页
     * @param $totpage 总共页数
     *
     * @return boolean
     */
    protected function _createListHtml($prpage, $thispg, $totpage, $cat, $catIds, $pCats) {
        $begin = ($thispg -1) * $prpage + 2;
        $webPath = 'list-';
        $p = new NewsPage($totpage, $prpage, $webPath, $thispg);
        $pages = $p->show();
        $lists = D('Article')
                 ->where(" `catid` IN (". implode(',', $catIds).") AND `status` = 1 AND `dateline` < ".time())
                 ->limit($begin, $prpage)
                 ->order("`dateline` DESC")
                 ->select();
        $newsLists = $this->_formatNewsUrl($lists);
        //每个列表页调用当前分类前两篇文章
        $topTwoNews = D('Article')
                      ->where("`catid` IN (".implode(',', $catIds).") AND `status` = 1 AND `dateline` < ".time())
                      ->limit(0,2)
                      ->order("`dateline` DESC")
                      ->select();
        $topTwoNews = $this->_formatNewsUrl($topTwoNews);

        $this->assign('cat', $cat);
        $this->assign('pcats', $pCats);
        $this->assign('pages', $pages);
        $this->assign('lists', $newsLists);
        $this->assign('top2news', $topTwoNews);
        $content = $this->fetch(CODE_RUNTIME_PATH . '/Modules/News/Tpl/HtmlTpl/list.html');
        $dir = CODE_RUNTIME_PATH . '/Html/' .$cat['path_prefix'];
        $make = new MakeHtml($dir);
        //$filename = ($thispg == 1) ? 'index.shtml' : $thispg.'.shtml';
        $filename = 'list-'.$thispg.'.shtml';
        if($make->make($filename, $content)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 格式化资讯链接
     */
    protected function _formatNewsUrl($lists) {
        $newsLists = array();
        if(is_array($lists)) {
            foreach($lists as $k => $v) {
                $newsLists[] = array(
                    'title' => $v['title'],
                    'dateline' => date('m-d', $v['dateline']),
                    'url' => $v['url'],
                    'description' => String::msubstr($v['description'], 0, 100)
                );
            }
        }
        return $newsLists;
    }

}
