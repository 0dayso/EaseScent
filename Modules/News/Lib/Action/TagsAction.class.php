<?php

/**
 * 标签管理
 * news_tags数据表字段整理为：
 * 1	tag_id	int(11)			UNSIGNED			否	无	AUTO_INCREMENT	 标签ID
 * 2	name    varchar(200)	utf8_general_ci		否	无		 			 标签名
 * 3	via		int(11)			UNSIGNED			否	无		 			 手动排序（默认为tag_id的值）
 * 4	tags_count	int(11)							否	无		 			 关联文章数量
 *
 * news_tag_relationships数据表字段整理为；  
 * 1	id			int(11)		UNSIGNED	否	无	AUTO_INCREMENT	 关联自增ID
 * 2	tag_id		int(11)		UNSIGNED	否	无		 			 标签ID
 * 3	aid			int(11)		UNSIGNED	否	无		 			 文章ID
 *
 * 废弃news_hot_tags表，合并到news_tags数据表中的via字段中
 */
class TagsAction extends CommonAction {

    public function _initialize() {
        parent::_initialize();
        $this->auth = $_SESSION['news_auth'];
    }

	/**
	 * 标签列表
	 */
    public function index() {
        $model = D('Tags');
        $map = '';
        $url = '';
        $keyword = Input::getVar($_REQUEST['keyword']);
        if($keyword) {
            $map .= '`name` LIKE "%'.$keyword.'%"';
            $url .= '&keyword=' . $keyword;
        }else{
            $map .= '`via` > 0';
        }

        $vialist = $this->_list($model, 'via = 0', 14, $url);
        $vlist = $this->_list($model, $map, '', $url);
        $list = array_merge($vialist, $vlist);
        
        $this->assign('taglist', $list);
        $this->display();
    }

    
	/**
	 * 标签新增
	 */
	public function add() {
		$this->display();
    }

    public function doadd() {

        $tags = D('Tags');
        $tag  = D('News_tag_relationships');

        $data['name'] = $_POST['name'];
        $data['via'] = $_POST['via'];
        $data['tags_count'] = count(explode(',', $_POST['aid']));
        $tid = $tags->add($data);

        $this->_JumpGo('标签新增成功', 'succeed', Url('index'));
    }


	/**
	 * 标签编辑
	 */
    public function edit() {
       
        $tags = D('Tags');
        $tag_relationships = D('News_tag_relationships');

        $tid = $_GET['tag_id'];
        $t = $tags->where('`tag_id` = "'.$tid.'"')->find();
        $relation = $tag_relationships->where("`tag_id` = " . $tid)->select();
        foreach ($relation as $key => $value) {
        	$raid[] = $value['aid'];
        }
        $r = implode(',', $raid);
        
        $this->assign('re', $r);
        $this->assign('r', $relation);
        $this->assign('taglist', $t);
        $this->display();
	}
    
    public function uptag(){

        $tags = D('Tags');

        $t['tid'] = $_POST['tid'];
        $t['name'] = $_POST['name'];
        $t['via'] = $_POST['via'];
        $t['tags_count'] = count(explode(',', $_POST['aid'])); 
        
        $tags->where('`tag_id` = ' . $t['tid'])->save($t);

        $this->_JumpGo('标签修改成功', 'succeed', Url('index'));
    }

	/**
	 * 标签删除
	 */
	public function del() {
        //获取tid
        $tid = Input::getVar($_REQUEST['tag_id']);
        //获取批量删除
        $tids = Input::getVar($_REQUEST['tag_ids']);

        $tags = D('Tags');
        $tag  = D('News_tag_relationships');
        if($tid) {
            $map = 'tag_id = ' . $tid;
        } elseif($tids) {
            $map = 'tag_id IN ('.implode(',', $tids).')';
        }
        if($map) {
            $tags->where($map)->delete();
            $tag->where($map)->delete();
            $this->_jumpGo('删除成功,'. $map,'succeed', Url('News/Tags/index'));
        }
        $this->_jumpGo('删除失败，参数为空', 'error');
	}

    /**
     * 添加到热门标签
     */

    public function inhot() {

        $tag = D('Tags');
        $data['tag_id'] = $_GET['id']; 
        $data['name'] = $_GET['n'];
        $data['via']  = 0;
        $data['tags_count'] = $_GET['count'];
        $this->_update($tag, $data);
        
        $this->_JumpGo('成功加入热门关键词', 'succeed', Url('index'));
    }

}
