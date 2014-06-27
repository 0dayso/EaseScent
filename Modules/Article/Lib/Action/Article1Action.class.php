<?php
// 本类由系统自动生成，仅供测试用途
class Article1Action extends CommonAction {
    public function index(){
        $uid = $_SESSION['admin_uid'];
        $upac = M()->table('article_upload_ac A,eswine_admin_domain_path B')->field('B.id,B.domain,B.path')->where('A.uid = '.$uid.' AND A.pathid = B.id')->select();
        $this->assign('upac',$upac);
        $pid = intval($_GET['pid']);
        if($pid){
            $isac = M('UploadAc')->where(array('uid'=>$uid,'pathid'=>$pid))->find();
            if(!$isac)  $this->_jumpGo('你没有权限', 'error', Url('index'));
            $pres = M('DomainPath','eswine_admin_')->where(array('id'=>$pid))->find();
            $rootpath = CODE_RUNTIME_PATH . DS;
            if(!is_dir($rootpath . $pres['path'])){
                @mkdir($rootpath . $pres['path'], 0777 ,true);
                $this->log('操作人：系统；操作行为：生成目录“'. $pres['path'].'”');
            }
            $this->assign('pres',$pres);
            $subpath = str_replace('\\', '/',trim($_GET['subpath']));
            if($subpath){
                $subpath = (substr($subpath, -1, 1) === '/') ? $subpath : $subpath.'/';
                $allpath = $rootpath . $pres['path'] . $subpath;
                if(!is_dir($allpath))  $this->_jumpGo('参数错误', 'error', Url('index'));
                $this->assign('subpath',$subpath);
            }
        }
        $this->display();
    }

    //文件夹操作
    function folder(){
        $rootpath = CODE_RUNTIME_PATH . DS;
        if($this->isPost()){
            if($_POST['type'] === 'create_folder'){//创建文件夹
                $pid = intval($_POST['pid']);
                $subpath = trim($_POST['subpath']);
                $name = trim($_POST['name']);
                if(!preg_match("/^[A-Za-z0-9]+$/", $name)){
                    echo json_encode(array('errorCode'=>600001,'errorStr'=>'文件名不合法')); exit;
                }
                $pres = M('DomainPath','eswine_admin_')->where(array('id'=>$pid))->find();
                if(!$pres){
                    echo json_encode(array('errorCode'=>600001,'errorStr'=>'参数错误')); exit;
                }
                $isac = M('UploadAc')->where(array('uid'=>$_SESSION['admin_uid'],'pathid'=>$pid))->find();
                if(!$isac){
                    echo json_encode(array('errorCode'=>600002,'errorStr'=>'没有权限')); exit;
                }
                $fullpath = $rootpath . $pres['path'] . $subpath;
                if(!is_dir($fullpath)){
                    echo json_encode(array('errorCode'=>600003,'errorStr'=>'获取目录异常')); exit;
                }
                $create_folder = $fullpath . $name;
                if(!is_dir($create_folder)){
                    if(@mkdir($create_folder, 0777 ,true)){
                        $this->log('操作人：管理员'.$_SESSION['admin_uid'].'；操作行为：创建目录“'.$pres['path'] . $subpath. $name.'”');
                        echo json_encode(array('errorCode'=>0,'result'=>''));   exit;
                    }else{
                        echo json_encode(array('errorCode'=>600003,'errorStr'=>'文件夹创建失败！'));   exit;
                    }
                }else{
                    echo json_encode(array('errorCode'=>600002,'errorStr'=>'文件夹名已存在！'));    exit;
                }
            }
            echo json_encode(array('errorCode'=>600001,'errorStr'=>'请求异常！'));    exit;
        }
        $pid = intval($_GET['pid']);
        $pres = M('DomainPath','eswine_admin_')->where(array('id'=>$pid))->find();
        if(!$pres)  die('参数错误');
        $dbpath = $pres['path'];
        $dburl = $pres['domain'];
        $isac = M('UploadAc')->where(array('uid'=>$_SESSION['admin_uid'],'pathid'=>$pid))->find();
        if(!$isac)  die('没有权限');
        $subpath = str_replace('\\', '/',trim($_GET['subpath']));
        if($subpath)    $subpath = (substr($subpath, -1, 1) === '/') ? $subpath : $subpath.'/';
        $path = $rootpath . $dbpath . $subpath;
        if(!is_dir($path))  die('参数错误');
        $handle = opendir($path);
        while(false !== ($file=readdir($handle))){
            if(is_dir($path.$file) && preg_match("/^[A-Za-z0-9]+$/", $file)){
                $filearr[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i',filemtime($path.$file)),
                    'subpath' => $subpath . $file . '/',
                );
            }
        }
        if($subpath){
            $subwz = explode('/', $subpath);
            array_pop($subwz);
            foreach($subwz as $key=>$val){
                $lj = '';
                foreach($subwz as $k=>$v){
                    if($k<=$key) $lj .= $v.'/';
                }
                $subwz1[] = array($val,$lj);
            }
        }
        if($_GET['type'] == 'select')
            $button = array('<input type="button" value="新建文件夹" onclick="create_folder(\'ini\');" />','<input type="button" value="选择" onclick="select_folder();" style="color:blue;" />');
        $this->assign('filearr',$filearr);
        $this->assign('subwz',$subwz1);
        $this->assign('spath',$dbpath);
        $this->assign('surl',$dburl);
        $this->assign('button',$button);
        $this->display();
    }
    function upload(){
        $rootpath = CODE_RUNTIME_PATH . DS;
        $dbsubpath = str_replace('\\', '/',trim($_GET['dbsubpath']));
        if($dbsubpath)  $dbsubpath = (substr($dbsubpath, -1, 1) === '/') ? $dbsubpath : $dbsubpath.'/';
        $updfile = $_FILES['upd_file'];
        if(!$updfile)
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，上传文件获取失败', 'file'=>$updfile)));
        if(!is_dir($rootpath . $dbsubpath))
            die(json_encode(array('error'=>1, 'errorStr'=>'上传失败，上传目录不存在', 'file'=>$updfile)));
        $file_name_ext = $updfile['name'][0];
        $file_name_ext_arr = explode('.', $file_name_ext);
        $file_ext = array_pop($file_name_ext_arr);
        $file_name = implode('.', $file_name_ext_arr);
        if(!preg_match("/^[A-Za-z0-9_]+$/", $file_name))
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，文件名不合法', 'file'=>$updfile)));
        $html_ext = array('html','shtml');
        $data1_ext = array('xls', 'xlsx');
        $data2_ext = array('json');
        $img_ext = array('jpg', 'jpeg', 'png', 'bmp', 'gif');
        $css_ext = array('css');
        $js_ext = array('js');
        if(in_array($file_ext, $html_ext)){
        }elseif(in_array($file_ext, array_merge($data1_ext, $data2_ext))){
            $dbsubpath .= '_data/';
        }elseif(in_array($file_ext, $img_ext)){
            $dbsubpath .= '_img/';
        }elseif(in_array($file_ext, $css_ext)){
            $dbsubpath .= '_css/';
        }elseif(in_array($file_ext, $js_ext)){
            $dbsubpath .= '_js/';
        }else{
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，后缀名不合法', 'file'=>$updfile)));
        }
        $cfg = array(
            'uploadReplace' => true,
            'savePath' => $rootpath. $dbsubpath,
            'saveRule'=>'',
        );
        import('@.ORG.Net.UploadFile');
        $upload = new UploadFile($cfg);
        if(!$upload->upload())
            die(json_encode(array('error'=>1, 'msg'=>$upload->getErrorMsg(), 'file'=>$updfile)));
        $uploadinfo = $upload->getUploadFileInfo();
        $this->log('操作人：管理员'.$_SESSION['admin_uid'].'；操作行为：上传文件“'.$dbsubpath.$file_name_ext.'”');
        if(in_array($file_ext, $data1_ext)){//处理excel文件
            $is_json = $this->ExcelToJson($uploadinfo[0]['savepath'], $uploadinfo[0]['savename']);
            if($is_json){
                die(json_encode(array('error'=>0, 'msg'=>'上传成功，生成.json数据文件成功', 'file'=>$updfile)));
            }else{
                die(json_encode(array('error'=>1, 'msg'=>'上传失败，excel文件不合法或数据文件生成错误', 'file'=>$updfile)));
            }
        }else{
            die(json_encode(array('error'=>0, 'msg'=>'上传成功', 'file'=>$updfile , 'result'=>$uploadinfo)));
        }
    }
    function ExcelToJson($filepath, $filename){
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $inputFileName = $filepath . $filename;
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        if(!in_array(strtolower($inputFileType), array('excel2007', 'excel5'))){
            unlink($inputFileName);
            return false;
        }
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        array_shift($sheetData);
        $nsheetData = array();
        foreach($sheetData as $val){
            if($val['A'])
                $nsheetData[] = $val;
        }
        $jsonData = json_encode($nsheetData);
        $outfile = $filepath . array_shift(explode('.',$filename)) . '.json';
        $handle = fopen($outfile, "w");
        fwrite($handle, $jsonData);
        fclose($handle);
        if(!file_exists($outfile)){
            //unlink($inputFileName);
            return false;
        }
        $this->log('操作人：系统；操作行为：生成文件“'.$outfile.'”');
        return true;
    }
    function getfiles(){
        $path = str_replace('\\', '/',trim($_POST['path']));
        if($path)   $path = (substr($path, -1, 1) === '/') ? $path : $path.'/';
        $path = CODE_RUNTIME_PATH . DS . $path;
        //html
        $html_file = array();
        if(is_dir($path)){
            $handle = opendir($path);
            while(false !== ($file=readdir($handle))){
                if(is_dir($path.$file) || !preg_match("/^[A-Za-z0-9_]+.{1}[A-Za-z0-9]+$/", $file))  continue;
                $fileinfo = pathinfo($path.$file);
                if(!in_array($fileinfo['extension'], array('html','shtml')))   continue;
                $html_file[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i',filemtime($path.$file)),
                    'size' => filesize($path.$file),
                );
            }
        }
        //_data
        $data_file = array();
        $data_path = $path . '_data/';
        if(is_dir($data_path)){
            $handle = opendir($data_path);
            while(false !== ($file=readdir($handle))){
                if(is_dir($data_path.$file) || !preg_match("/^[A-Za-z0-9_]+.{1}[A-Za-z0-9]+$/", $file)) continue;
                $fileinfo = pathinfo($data_path.$file);
                if(!in_array($fileinfo['extension'], array('xlsx','xls','json')))   continue;
                $data_file[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i',filemtime($data_path.$file)),
                    'size' => filesize($data_path.$file),
                );
            }
        }
        //img
        $img_file = array();
        $img_path = $path . '_img/';
        if(is_dir($img_path)){
            $handle = opendir($img_path);
            while(false !== ($file=readdir($handle))){
                if(is_dir($img_path.$file) || !preg_match("/^[A-Za-z0-9_]+.{1}[A-Za-z0-9]+$/", $file)) continue;
                $fileinfo = pathinfo($img_path.$file);
                if(!in_array($fileinfo['extension'], array('jpg','jpeg','png','gif','bmp')))   continue;
                $img_file[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i',filemtime($img_path.$file)),
                    'size' => filesize($img_path.$file),
                );
            }
        }
        //css
        $css_file = array();
        $css_path = $path . '_css/';
        if(is_dir($css_path)){
            $handle = opendir($css_path);
            while(false !== ($file=readdir($handle))){
                if(is_dir($css_path.$file) || !preg_match("/^[A-Za-z0-9_]+.{1}[A-Za-z0-9]+$/", $file)) continue;
                $fileinfo = pathinfo($css_path.$file);
                if($fileinfo['extension'] !== 'css')   continue;
                $css_file[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i',filemtime($css_path.$file)),
                    'size' => filesize($css_path.$file),
                );
            }
        }
        //js
        $js_file = array();
        $js_path = $path . '_js/';
        if(is_dir($js_path)){
            $handle = opendir($js_path);
            while(false !== ($file=readdir($handle))){
                if(is_dir($js_path.$file) || !preg_match("/^[A-Za-z0-9_]+.{1}[A-Za-z0-9]+$/", $file)) continue;
                $fileinfo = pathinfo($js_path.$file);
                if($fileinfo['extension'] !== 'js')   continue;
                $js_file[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i',filemtime($js_path.$file)),
                    'size' => filesize($js_path.$file),
                );
            }
        }
        //dump($html_file);
        echo json_encode(array('errorCode'=>0,'errorStr'=>'','result'=>array('html'=>$html_file,'data'=>$data_file,'img'=>$img_file,'css'=>$css_file,'js'=>$js_file)));    exit;
    }
    function log($log){
        $log = str_replace('\\', '/', $log);
        M('UploadLog')->add(array('log'=>$log,'time'=>time()));
    }
}