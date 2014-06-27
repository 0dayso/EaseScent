<?php
/**
 * 标签操作（新增保存和编辑保存）
 * $keywords 传入的标签，支持（标签1，标签2,标签3）格式
 * $newsid 传入的文章id，唯一值
 */

class TagsModel extends CommonModel {
	
	protected $tableName = 'news_tags';

    public function saveTags($keywords, $newsid) {
        $tags = str_replace('，', ',', $keywords);
        $tags = explode(',', $tags);
        $tags = array_filter($tags);
        foreach($tags as $key => $v) {

            $tagname = $this->where('`name` = "'.$v.'"')->select();
            if (empty($tagname)) {
                $ta['name'] = $v;
                $ta['tags_count'] = 1;
                $tagid = $this->add($ta);

                $tag['tag_id'] = $tagid;
                $tag['name'] = $ta['name'];
                $tag['via'] = $tagid;
                $tid = $this->where('`tag_id` = '.$tagid)->save($tag);

                $r['tag_id'] = $tagid;
                $r['aid'] = $newsid;
                D('News_tag_relationships')->add($r);
            }else{
                $t['name'] = $v;
                $t['tag_id'] = $tagname[0]['tag_id'];

                $this->where('`tag_id` = "'.$t['tag_id'].'"')->save($t);
                $this->where('`tag_id` = "'.$t['tag_id'].'"')->setInc('tags_count');

                $r['tag_id'] = $tagname[0]['tag_id'];
                $r['aid'] = $newsid;

                $shipid = D('News_tag_relationships')->add($r);
            }    
        }    
    }

    public function upTags($keywords, $newsid) {
        $tags = str_replace('，', ',', $keywords);
        $tags = explode(',', $tags);
        $tags = array_filter($tags);
        foreach($tags as $key => $v) {
            $relation = D('News_tag_relationships');
            $tagname = $this->where('`name` ="'.$v.'"')->select();
            if (empty($tagname)) {
                $ta['name'] = $v;
                $ta['tags_count'] = 1;
                $tagid = $this->add($ta);
                $t['via'] = $tagid;
                $tid = $this->where('`tag_id` = "'.$t['via'].'"')->save($t);

                $r['tag_id'] = $tagid;
                $r['aid'] = $newsid;
                
                $shipid = $relation->where('`aid` = "'.$newsid.'"')->add($r);
            }else{
                $r['tag_id'] = $tagname[0]['tag_id'];
                $r['aid'] = $newsid;
                $shipid = $relation->where('`aid` = "'.$newsid.'"')->save($r);
            }
        }
    }
}
