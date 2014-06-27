<?php

/**
 * 定时执行脚本控制器
 *
 * @author mengfk<mengfk@eswine.com>
 */

class CliAction {

    public function index() {

    }

    /**
     * 老逸香网资讯迁移，临时用
     */
    public function trans() {
        $op = $_GET['op'];
        $total = $_GET['total'];
        $page = max(1, intval($_GET['page']));
        if($op == 'run') {
            if(!$total) {
                $sql = "SELECT COUNT(*) as num FROM `es_article`";
                $rst = M()->query($sql);
                $total = $rst[0]['num'];
            }
            $begin = ($page -1) * 200;

            if($begin > $total) {
                echo "全部导入完成";
                exit(0);
            }

            $cate = D('Category')->select();
            $newCate = array();
            foreach($cate as $val) {
                $newCate[$val['catid']] = $val;
            }

            $limit = $begin .', 200';
            $sql = "SELECT art.* ,cont.* FROM `es_article` as art, `es_article_content` as cont WHERE art.article_id = cont.article_id ORDER BY art.article_id ASC LIMIT ".$limit;
            $rest = M()->query($sql);
            foreach($rest as $value) {
                $catid = $this->catidConv($value['sort_id']);
                if(!$catid) $catid = 0;
                $_POST['aid'] = $value['article_id'];
                $_POST['catid'] = $catid;
                $_POST['title'] = $value['title'];
                $_POST['stitle'] = $value['optimize_title'];
                $_POST['author'] = $value['author'];
                $_POST['article_from'] = $value['article_from'];
                $_POST['keywords'] = $value['keywords'];
                $_POST['description'] = $value['description'];
                $_POST['views'] = $value['hits'];
                $_POST['dateline'] = strtotime($value['upd_date']);
                $_POST['status'] = $value['is_open'];
                $_POST['createuser'] = $value['add_user'];
                $_POST['createtime'] = strtotime($value['add_time']);
                $_POST['updateuser'] = $value['edit_user'];
                $_POST['updatetime'] = strtotime($value['edit_time']);
                list($_POST['url'],$_POST['path']) = A('Pub')->_createPathUrl($_POST['aid'], $_POST['dateline'], $_POST['catid']);
                $_POST['type'] = ($value['original'] == 'original') ? 1 : ($value['original'] == 'exclusive' ? 2 : ($value['original'] == 'attention' ? 3 : 0));
                if(D('Article')->create()) {
                    D('Article')->add('', '', true);
                }
                $_POST['content'] = $value['content'];
                $_POST['allow_comment'] = 1;
                if(D('ArticleContent')->create()) {
                    D('ArticleContent')->add('', '', true);
                }
            }
            header('Refresh: 1; url='.Url('News/Cli/trans?op=run&page='.($page+1).'&total='.$total));
            echo "本200篇文章导入完成，继续导入下200篇，到达点({$begin})..";
        } else {
            echo "<a href='".Url('News/Cli/trans?op=run')."'>开始迁移</a>";
        }
    }

    /**
     * 新老分类id转换
     */
    private function catidConv($oldid) {
        static $ids = array(
            2 => 3,   //国际资讯
            3 => 4,   //政策法规
            4 => 81,  //国内资讯
            5 => 171, //深度报道
            6 => 193,   //行业聚焦
            7 => 192,     //高端访问
            8 => 72,    //葡萄酒博览
            10 => 157, //葡萄酒娱乐
            11 => 160, //葡萄酒常识
            12 => 161, //产区与旅游
            13 => 164, //葡萄酒养生
            14 => 168, //礼仪与品鉴
            15 => 220, //美酒与美食
            16 => 219, //葡萄酒营销
            19 => 82, //葡萄酒营销
            17 => 74, //葡萄酒酒庄
            18 => 75, //葡萄酒名家
            20 => 158, //投资与收藏
            21 => 159, //葡萄酒品牌
            22 => 198, //葡萄酒酒具
            23 => 223, //葡萄酒导购
            22 => 198, //葡萄酒酒具
            24 => 224, //葡萄酒商机
            25 => 190, //烈酒
            26 => 209, //金酒
            27 => 210, //威士忌
            28 => 211, //白兰地
            29 => 212, //伏特加
            30 => 213, //朗姆酒
            31 => 214, //龙舌兰
            32 => 215, //产业信息
            33 => 216, //鸡尾酒
            34 => 225, //高端访谈
            35 => 226, //展会
            36 => 93,//展会信息
            37 => 87, //国内展会
            38 => 88, //国际展会
            39 => 227, //展会活动
            40 => 228, //展会访谈
            41 => 229,//网络展览馆
            42 => 230, //搭展推荐
            43 => 231, //展会新品
        );
        return array_search($oldid, $ids);
    }

    /**
     * 逸香网资讯频道24小时排行静态区块文件更新脚本
     *
     * 此脚本由计划任务触发，触发地址：http://domain/index.php?app=News&m=Cli&a=update24News
     *
     */
    public function update24News() {
        $cats = array('IN (5,6,7)', '= 4', '= 2');
        $dataHtml = '';
        $count = count($cats);
        foreach($cats as $key => $catids) {
            $data = D('Article')
                    ->where("`catid` {$catids} AND `status` = 1 AND `dateline` < ".time()." AND `dateline` > ".(time() - 86400))
                    ->limit(0, 10)
                    ->order("`views` DESC")
                    ->select();
            $dataHtml .= ' <ul class="l-ul-mod2 '. ( $count == $key + 1 ? 'l-ul-mod2-show' : '') .'">';
            foreach($data as $v) {
                $dataHtml .= "<li><a href='". C('TMPL_PARSE_STRING.__NEWS__'). "{$v['htmlpath']}' target='_blank' title='{$v['title']}'>{$v['title']}</a></li>";
            }
            $dataHtml .= '</ul>';
        }
        $dir = C('NEWS_PATH').'News/_Block/';
        $make = new MakeHtml($dir);
        $make->make('24hours.html', $dataHtml);
    }

    /**
     * 逸香网资讯列表页葡萄酒营销区块更新
     */
    public function wineMarking() {
        $data = D('Article')
                ->where("`catid` IN(16,17,18,19,20,21,22,23,24) AND `status` = 1 AND `dateline` < ".time())
                ->limit(0, 10)
                ->order("`dateline` DESC")
                ->select();
        $html = '';
        foreach($data as $v) {
            $html .= "<li><a href='". C('TMPL_PARSE_STRING.__EXPO__') ."{$v['htmlpath']}' target='_blank' title='{$v['title']}'>{$v['title']}</a></li>";
        }
        $dir = C('NEWS_PATH').'News/_Block/';
        $make = new MakeHtml($dir);
        $make->make('winemarketing.html', $html);
    }

    /**
     * 逸香网资讯频道葡萄酒博览图片列表
     */
    public function wineExpo() {
        $cats = array(10, 13, 11, 12, 17, 18);
        $dataHtml = '';
        foreach($cats as $key => $catid) {
            $data = D('Article')
                    ->where("`catid` = {$catid} AND `status` = 1 AND `dateline` < ".time(). " AND pic <> ''")
                    ->limit(0, 10)
                    ->order("`dateline` DESC")
                    ->select();
            $dataHtml .= ' <ul class="l-ul-mod4 '. ( $key == 0 ? 'l-ul-mod2-show' : '') .'">';
            foreach($data as $v) {
                $dataHtml .= "<li><a href='". C('TMPL_PARSE_STRING.__EXPO__') ."{$v['htmlpath']}' target='_blank' title='{$v['title']}'><img src='". C('UPLOAD_WWWPATH') ."{$v['pic']}.thumb.132.82' alt='{$v['title']}' /></a><span class=''><a href='". C('TMPL_PARSE_STRING.__EXPO__')."{$v['htmlpath']}' target='_blank' title='{$v['title']}'>{$v['title']}</a></span></li>";
            }
            $dataHtml .= '</ul>';
        }
        $dir = C('NEWS_PATH').'News/_Block/';
        $make = new MakeHtml($dir);
        $make->make('wineexpo.html', $dataHtml);
    }

    /**
     * 逸香网资讯频道葡萄酒投资与收藏
     */
    public function investmentAndcollection() {
        $data = D('Article')
                ->where("`catid` = 20 AND `status` = 1 AND `dateline` < ".time())
                ->limit(0, 10)
                ->order("`dateline` DESC")
                ->select();
        $html = '';
        foreach($data as $v) {
            $html .= "<li><a href='". C('TMPL_PARSE_STRING.__EXPO__') ."{$v['htmlpath']}' target='_blank' title='{$v['title']}'>{$v['title']}</a></li>";
        }
        $dir = C('NEWS_PATH').'News/_Block/';
        $make = new MakeHtml($dir);
        $make->make('investmentandcollection.html', $html);
    }
}
