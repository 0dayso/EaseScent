<?php
import ( "TagLib" );
class TagLibArticle extends TagLib {
	protected $tags = array (
			'article' => array (
					'attr' => 'type,table,field,where,aid,order,limit,id,key,mod,datefmt',
					'close' => 1 
			),
			'pic' => array (
					'attr' => 'type,id,where,order,limit',
					'close' => 0 
			) 
	);
	public function _article($attr, $content) {
		$tag = $this->parseXmlAttr ( $attr );
		$type = $tag ['type'];
		$table = isset ( $tag ['table'] ) ? $tag ['table'] : 'es_article';
		$field = isset ( $tag ['field'] ) ? $tag ['field'] : 'article_id,title,description,link_url,add_time';
		$aid = isset ( $tag ['aid'] ) ? $tag ['aid'] : '';
		$mod = isset ( $tag ['mod'] ) ? $tag ['mod'] : '2';
		$key = isset ( $tag ['key'] ) ? $tag ['key'] : 'i';
		$id = isset ( $tag ['id'] ) ? $tag ['id'] : 'article';
		$val = $id;
		$datefmt = isset ( $tag ['datefmt'] ) ? $tag ['datefmt'] : 'Y-m-d';
		if ($aid) {
			unset ( $type );
			$queryStr = 'M()->table(\'' . $table . '\')->';
			$queryStr .= 'field(\'' . $field . '\')->';
			$queryStr .= 'where(\'article_id IN (' . $aid . ')\')->';
			$queryStr .= 'select();';
		}
		if ($type) {
			$queryStr = 'M()->table(\'' . $table . '\')->';
			$queryStr .= 'field(\'' . $field . '\')->';
			$queryStr .= $tag ['where'] ? 'where(\'' . $tag ['where'] . '\')->' : 'where(\'sort_id = ' . $type . '\')->';
			$queryStr .= $tag ['order'] ? 'order(\'' . $tag ['order'] . '\')->' : 'order(\'article_id DESC\')->';
			$queryStr .= $tag ['limit'] ? 'limit(' . $tag ['limit'] . ')->' : '';
			$queryStr .= 'select();';
		}
		$parseStr = '<?php ';
		$parseStr .= '$_result = ' . $queryStr;
		$parseStr .= 'if($_result):$' . $key . '=0;';
		$parseStr .= 'foreach($_result as $key=>$' . $val . '):';
		$parseStr .= '$picInfo = getArticlePic($' . $val . '[\'article_id\']);';
		$parseStr .= '$mod = ($' . $key . '%' . $mod . ');';
		$parseStr .= '++$' . $key . ';';
		$parseStr .= '$' . $val . '[\'url\'] = \'http://wine.cn/html/\'.date("Ym",strtotime($' . $val . '[\'add_time\'])).\'/\'.$' . $val . '[\'article_id\'].\'html\';';
		$parseStr .= '$' . $val . '[\'picname\'] = $picInfo[\'pic\'];';
		$parseStr .= '$' . $val . '[\'picdesc\'] = $picInfo[\'pic_desc\'];';
		$parseStr .= '$' . $val . '["atime"] = date(\'' . $datefmt . '\',strtotime($' . $val . '["add_time"])); ?>';
		$parseStr .= $content;
		$parseStr .= '<?php endforeach; endif; ?>';
		return $parseStr;
	}
}
