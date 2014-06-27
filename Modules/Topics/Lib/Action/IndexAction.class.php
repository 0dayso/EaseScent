<?php
class IndexAction extends Action {
	public function index() {
		header("content-type:text/html;charset=utf-8");
		$this->display ( 'list' );
	}
	public function listAll() {
		header("content-type:text/html;charset=utf-8");
		$this->readTopicsDir();
		$this->display ( 'list' );
	}
	public function add() {
		$model = M ();
		$t_type = $model->table ( 'es_topicstype' )->field ( 'id,name' )->select ();
        $this->assign ( 'ttype', $t_type );
        $a_sort = $model->table('es_article_sort')->field('sort_id,sort_name')->order('sort_id ASC')->select();
        $this->assign('asort',$a_sort);
        $this->readTopicsDir();
        $this->fieldList();
		$this->display ();
	}
	public function edit() {
		header("content-type:text/html;charset=utf-8");
		$dr = $this->_get('dr');
		if(!$dr) $this->error('参数错误',Url('Topics/Index/listAll'));
		$fn = $this->_get('fn');
		$url = $dr .'/'. $fn;	// 即专题url
		$model = M();
		$t_type = $model->table ( 'es_topicstype' )->field ( 'id,name' )->select ();
        $this->assign ( 'ttype', $t_type );
        $a_sort = $model->table('es_article_sort')->field('sort_id,sort_name')->order('sort_id ASC')->select();
        $this->assign('asort',$a_sort);
		$tp = $model->table('es_newtopics')->where('url=\''.$url.'\'')->find();
		$tp['dirnm'] = $dr;
		$tp['pgnm'] = $fn;
		//dump($tp);
		$aStatics = $this->getStaticFiles($tp['dirnm']);
		//dump($aStatics);
		$this->assign('tp',$tp);
		$this->assign('aStatics',$aStatics);
        $this->readTopicsDir();
		$this->fieldList();
		$this->display();
	}
	public function addData() {
		$data = $this->_post();
		$typeid = $data ['tid'];
		$dirnm = $this->gset ( $data ['dirnm'] );
		$pgname = $this->gset($data['pgnm']);
		$url = $dirnm.'/'.$pgname;
		$cnm = $this->gset ( $data ['cnm'] );
		$enm = $this->gset ( $data ['enm'] );
		$des = $this->gset ( $data ['des'] );
		$view = $this->upload();
		$seot = $this->gset ( $data ['seot'] );
		$seok = $this->gset ( $data ['seok'] );
		$seod = $this->gset ( $data ['seod'] );
		$cont = $data['cont'];
		$is_open = $data ['isopen'];
		$is_top = $data ['istop'];
		$is_rec = $data ['isrec'];
		$ct = strtotime($data ['ct']);
		$author = $_SESSION['admin_username'];
		
		$update = $this->gset($data['update'],FALSE); //判断更新还是增加
		
		if(($dirnm == '') || ($pgname == '')) {
			echo json_encode(array('st' => 0));
			exit();
		}
		$model = M ();
		$content ['typeid'] = $typeid;
		$content ['url'] = $url;
		$content ['cnm'] = $cnm;
		$content ['enm'] = $enm;
		$content ['des'] = $des;
		$content ['view'] = $view;
		$content ['seot'] = $seot;
		$content ['seok'] = $seok;
		$content ['seod'] = $seod;
		$content ['cont'] = $cont;
		$content ['is_open'] = $is_open;
		$content ['is_top'] = $is_top;
		$content ['is_rec'] = $is_rec;
		$content ['ct'] = $ct;
		$content ['author'] = $author;
		$content ['lmt'] = time ();
		if($update){
			$rel = $model->table ( 'es_newtopics' )->where('url=\''.$url.'\'')->save ( $content );
		} else {
			$rel = $model->table ( 'es_newtopics' )->add ( $content );
		}
		//echo $model->getLastSql();
		if(!$rel){
			echo json_encode(array('st' => 0));
			return;
		}
		$statics = $this->getStaticFiles($dirnm);
		$jsArr = $statics['js'];
		$cssArr = $statics['css'];
		$tmpl = $this->topicstmplPutContents($cont);
		$this->assign('seo_title',$seot);
		$this->assign('seo_keywords',$seok);
		$this->assign('seo_description',$seod);
		$this->assign('loadcss',$cssArr);
		$this->assign('loadjs',$jsArr);
		$this->assign('dirnm',$dirnm);
		$this->buildHtml($pgname,TOPICS_HTML_PATH.'/'.$dirnm.'/',$tmpl);
		echo json_encode(array('st' => 1,'view'=>$view));
	}
	public function gset($var, $defaultVal = '') {
		return isset ( $var ) ? trim ( $var ) : trim ( $defaultVal );
	}
    public function upload() {
		$sub_1 = $this->_post('subdir');//单独上传图片至指定专题
		$sub_2 = $this->_post('dirnm');//上传或修改当前专题展示图片
		if($sub_1) {
			$subDir = $sub_1;
		} elseif($sub_2) {
            if(!$_FILES['topicsView']){
                return $this->_post('oldView');
            }
			$subDir = $sub_2;
		} else {
			return false;
		}
		import ( 'ORG.Net.UploadFile' );
		$upload = new UploadFile ();
		$__files = $_FILES;
		$ext = null;
		foreach ($__files as $key => $val) {
			$pathinfo = pathinfo($val['name']);
			$ext = $pathinfo['extension'];
		}
		switch ($ext) {
			case 'js':
				$saveDir = '/js/';
				break;
			case 'css':
				$saveDir = '/css/';
				break;
			default:
				$saveDir = '/images/';
        }
                    
		$upload->saveRule = '';
		$upload->uploadReplace = true;
		$upload->maxSize = 1000000;
		$upload->allowExts = array ('jpg','jpeg','png','gif','js','css',);
		$upload->savePath = CODE_RUNTIME_PATH . '/Html/Topic/' . $subDir . $saveDir;
		if (! $upload->upload () ) {
			if($sub_1){
				echo json_encode(array('st' => 0,'msg'=>$upload->getErrorMsg()));
			}else{
				return NULL;
			}
		} else {
			$info = $upload->getUploadFileInfo ();
			if($sub_1){
				$io = array_merge(array('st' => 1),array('cat' => $info[0]['extension'],'file' => $info[0]['savename']));
				echo json_encode($io);
			}else{
				return $info[0]['savename'];
			}
		}
	}
    public function fileManager() {
        $this->readTopicsDir();
        $this->display('fileManager');
    }

    public function UploadHandler() {
		import ( 'ORG.Net.UploadFile' );
        $subDir = $this->_get('upload_dir') ? $this->_get('upload_dir') : NULL;
        if(!$subDir){
            //return false;
        }
		$upload = new UploadFile ();
		$ext = null;
		foreach ($_FILES as $key => $val) {
			$pathinfo = pathinfo($val['name']);
			$ext = $pathinfo['extension'];
		}
		switch ($ext) {
			case 'js':
				$saveDir = '/js/';
				break;
			case 'css':
				$saveDir = '/css/';
				break;
			default:
				$saveDir = '/images/';
        }
                    
		$upload->saveRule = '';
		$upload->uploadReplace = true;
		$upload->maxSize = 1000000;
		$upload->allowExts = array ('jpg','jpeg','png','gif','js','css',);
		$upload->savePath = CODE_RUNTIME_PATH . '/Html/Topic/' . $subDir . $saveDir;
		if (! $upload->upload () ) {
            echo json_encode(array('st' => 0));
		} else {
            $info = $upload->getUploadFileInfo ();
            echo json_encode(array('name' => $info[0]['savename'],'size' => $info[0]['size'],'st' => 1));
		}
	}
	/**
	 * 生成专题模板 TOPICS_TMPL ,传入参数为主体内容部分｛头部和尾部除外｝TOPICS_TMPL常量在本项目/Common/common.php中定义。
	 * topicsTmpl.html
	 * 	header
	 *  body
	 *  footer 三个主体
	 * @return TOPICS_TMPL 路径
	 **/
	public function topicstmplPutContents($data_body) {
		$th = $_POST['th'];
		if(!$th) {
			$this->error('请确认专题头部已经添加，请联系管理员');
		}
		switch ($th) {
			case 1 :
					$data_header = '<include file="Common:topicsheader"/>';
					$data_footer = '<include file="Common:topicsfooter"/>';
					$data = $data_header.$data_body.$data_footer;
					$status = file_put_contents(TOPICS_TMPL, $data);
				break;
			case 2 :
					$data_header = '<include file="Common:header20130312"/>';
					$data_footer = '<include file="Common:footer20130312"/>';
					$data = $data_header.$data_body.$data_footer;
					$status = file_put_contents(TOPICS_TMPL, $data);
				break;
			default:
				$this->error('请确认专题头部已经添加，请联系管理员');
		}
		return TOPICS_TMPL;
	}

    public function generateUrl(){
        $idstr = $_POST['item'];
        if(!$idstr){
            echo json_encode(array('st'=>0));
            return false;
        }
        $model = M();
        $rel = $model->table('es_article a')
                    ->join('es_article_pic b ON a.article_id = b.article_id')
                    ->field('a.article_id,a.add_time,b.pic')
                    ->where('a.article_id IN ('.$idstr.')')
                    ->select();
        foreach($rel as $k => $v) {
            $rel[$k]['htmldir'] = date('Ym',strtotime($rel[0]['add_time']));
        }
        $rel['st'] = 1;
        echo json_encode($rel);
    }

    public function table() {
        header("content-type:text/html;charset=utf-8");
		$model = M ();
        import('ORG.Util.Page');
        ($sort_id = $this->_get('sort')) ? $where = array('a.sort_id'=>$sort_id) : $where = '';
		$total = $model->table('es_article a')->join('es_article_sort b ON a.sort_id = b.sort_id')->where($where)->count();
		$pager = new Page($total,15);
        //all articles and sorts
		$limit = $pager->firstRow.','.$pager->listRows;
		$all = $model->table('es_article a')
						->join('es_article_sort b ON a.sort_id = b.sort_id')
						->field('a.article_id,a.title,a.sort_id,b.sort_name')
                        ->where($where)
                        ->order('a.article_id DESC')
						->limit($limit)
						->select();
        $pager->setConfig('theme','%nowPage%/%totalPage% 页 %upPage% %downPage% %first% %prePage% %linkPage% %nextPage% %end%');
		$show = $pager->show();
		$list = $all;
		$this->assign('articlelist',$list);
		$this->assign('pagebar',$show);
        $this->display('article');
    }

    public function readTopicsDir(){
    	$dir = TOPICS_HTML_PATH;
    	$filesTree = $this->pathTraversal($dir);
    	$this->assign('treeShow',$filesTree);
    }
    
    public function getStaticFiles($topicDir){
    	$jsFolder = C('TOPICS_HTML_PATH') . '/' . $topicDir . '/js/';
    	$cssFolder = C('TOPICS_HTML_PATH') . '/' . $topicDir . '/css/';
    	$imagesFolder = C('TOPICS_HTML_PATH') . '/' . $topicDir . '/images/';
    	$temp = array('js'=>$jsFolder,'css'=>$cssFolder,'image'=>$imagesFolder);
    	$aStatics = array();
    	foreach($temp as $key => $val){
    		$aStatics[$key] = $this->pathTraversal($val);
    	}
    	return $aStatics;
    }
    
    public function pathTraversal($pathName) {
    	$results = array();
    	$temp = array();
    	if (!is_dir($pathName) && !is_readable($pathName)){
    		return null;
    	}
    	$allFiles = scandir($pathName);
    	foreach ($allFiles as $fileName) {
    		if(in_array($fileName, array('.','..')))	continue;
    		$fullPath = $pathName.'/'.$fileName;
    		if (is_dir($fullPath) && is_readable($fullPath)) {
    			$results[$fileName] = $this->pathTraversal($fullPath);
    		} else {
    			$temp[] = $fileName;
    		}
    	}
    	foreach ($temp as $f) {
    		$results[] = $f;
    	}
    	return $results;
    }
    
    public function delStatics() {
    	$topicName = $this->_post('dirnm');
    	$type = $_GET['type'];
    	$file = $_GET['fn'];
    	$fileName = C('TOPICS_HTML_PATH') . '/' . $topicName . '/'.$type.'/' . $file;
    	if(file_exists($fileName)){
    		$result = @unlink($fileName);//删除硬盘文件,ture on success,false on failure.
    		switch($type){
    			case 'image':
		    		//更新数据库文件
		    		$model = M('Newtopics');
		    		$topicPage = $this->_post('pgnm');
		    		$url = $topicName.'/'.$topicPage;
		    		$find = $model->where('url=\''.$url .'\' AND view=\''.$file.'\'')->find();
		    		if($find){
		    			$model->where('url=\''.$url .'\'AND view=\''.$file.'\'')->setField('view',NULL);
		    		}
    				break;
    			default:
    				break;
    		}
    		if(!$result){
    			echo 0;
    			return;
    		}
    		echo 1;
    	} else {
    		echo 0;
    	}
    	return;
    }
    
    public function fieldList() {
    	$fields = M('Article')->getDbFields();
    	$this->assign('fields',$fields);
    }
}
