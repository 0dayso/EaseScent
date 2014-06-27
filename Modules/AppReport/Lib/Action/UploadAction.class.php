<?php
//上传文件
class UploadAction extends CommonAction {
    public function index(){
        $path = C('UPLOAD_PATH') . 'data/';
        if(!is_dir($path)){
            mkdir($path, 0777 ,true);
        }
        $this->display();
    }
    function getfiles(){
        $path = C('UPLOAD_PATH') . 'data/';
        $files_arr = array();
        if(is_dir($path)){
            $handle = opendir($path);
            while(false !== ($file=readdir($handle))){
                if(is_dir($path.$file))  continue;
                $fileinfo = pathinfo($path.$file);
                if($fileinfo['extension'] != 'xls')   continue;
                $files_arr[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i:s',filemtime($path.$file)),
                    'url' => C('UPLOAD_URL').'data/'.$file,
                );
            }
        }
        die(json_encode(array('error'=>0,'result'=>$files_arr)));
    }
    function upload(){
        $date = date("Y-m-d H:i:s");
        $path = C('UPLOAD_PATH') . 'data/';
        if(!$file = $_FILES['file']){
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，上传文件获取失败', 'filename'=>'', 'date'=>$date)));
        }
        $file_name = array_shift(explode('.', $file['name']));
        if(!$is_table_exist = D()->query('show tables like \''.$file_name.'\'')){
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，文件名不合法', 'filename'=>$file['name'], 'date'=>$date)));
        }
        $file_ext = array_pop(explode('.', $file['name']));
        if($file_ext != 'xls'){
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，后缀名不合法', 'filename'=>$file['name'], 'date'=>$date)));
        }
        $cfg = array(
            'uploadReplace' => true,
            'savePath' => $path,
            'saveRule'=> $tablename,
        );
        import('@.ORG.Net.UploadFile');
        $upload = new UploadFile($cfg);
        if(!$upload->upload()){
            die(json_encode(array('error'=>1, 'msg'=>$upload->getErrorMsg())));
        }
        $uploadinfo = $upload->getUploadFileInfo();
        if(false === $data = $this->ExcelToArr($uploadinfo[0]['savepath'], $uploadinfo[0]['savename'])){
            unlink($uploadinfo[0]['savepath'].$uploadinfo[0]['savename']);
            die(json_encode(array('error'=>1, 'msg'=>'上传失败文件格式不合法', 'filename'=>$file['name'], 'date'=>$date)));
        }
        die(json_encode(array('error'=>0, 'msg'=>'上传成功', 'filename'=>$file['name'], 'date'=>$date, 'result'=>$uploadinfo)));
    }
    //http://ajax.wine.cn/?action=WWhoSn1od2psN01odHd5fDd7d259alx6
    function coverDb(){
        $path = C('UPLOAD_PATH') . 'data/';
        $tablelist = D()->query('show tables like \'phone%\'');
        foreach($tablelist as $key=>$val){
            sort($val);
            $table = $val[0];
            $filename = $table.'.xls';
            if(!is_file($path.$filename) || !$filename){
                continue;
            }
            if(false === $data = $this->ExcelToArr($path, $filename)){
                continue;
            }
            $sql_arr = array();
            foreach($data as $dkey=>$dval){
                $sql_arr[$dkey] = '(';
                $sql_arr_val = array('NULL');
                foreach($dval as $v){
                    if(preg_match("/^\d{4}-\d{1,2}-\d{1,2}$/",$v)){
                        $v = strtotime($v);
                    }
                    $sql_arr_val[] = '\''.$v.'\'';
                }
                $sql_arr[$dkey] .= implode(',',$sql_arr_val);
                $sql_arr[$dkey] .= ')';
            }
            $sql_str = implode(',',$sql_arr);
            if(!$sql_str){
                continue;
            }
            D()->query('TRUNCATE TABLE '.array_shift(explode('.',$filename)));
            D()->query('INSERT INTO '.array_shift(explode('.',$filename)).' VALUES '.$sql_str);
        }
        echo 1;
    }
    function ExcelToArr($filepath, $filename){
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $inputFileName = $filepath . $filename;
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        if(!in_array(strtolower($inputFileType), array('excel2007', 'excel5'))){
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
        return $nsheetData;
    }
    /*public function index(){
        $datapath = C('UPLOAD_PATH') . 'data/';
        if(!is_dir($datapath)){
            mkdir($datapath, 0777 ,true);
        }
        $list = D()->query('show tables like \'phone%\'');
        $this->assign('list', $list);
        $this->display();
    }
    function getfiles(){
        $path = C('UPLOAD_PATH') . 'data/';
        $files_arr = array();
        if(is_dir($path)){
            $handle = opendir($path);
            while(false !== ($file=readdir($handle))){
                if(is_dir($path.$file))  continue;
                $fileinfo = pathinfo($path.$file);
                if($fileinfo['extension'] != 'xls')   continue;
                $files_arr[] = array(
                    'name' => $file,
                    'time' => date('Y/m/d H:i:s',filemtime($path.$file)),
                );
            }
        }
        die(json_encode(array('error'=>0,'result'=>$files_arr)));
    }
    function upload(){
        if(!$filename = $_GET['filename']){
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，请选择文件名')));
        }
        if(!$file = $_FILES['file']){
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，上传文件获取失败')));
        }
        $file_ext = array_pop(explode('.', $file['name']));
        if($file_ext != 'xls'){
            die(json_encode(array('error'=>1, 'msg'=>'上传失败，后缀名不合法')));
        }
        $path = C('UPLOAD_PATH') . 'data/';
        $cfg = array(
            'uploadReplace' => true,
            'savePath' => $path,
            'saveRule'=> $filename,
        );
        import('@.ORG.Net.UploadFile');
        $upload = new UploadFile($cfg);
        if(!$upload->upload()){
            die(json_encode(array('error'=>1, 'msg'=>$upload->getErrorMsg())));
        }
        $uploadinfo = $upload->getUploadFileInfo();
        if(false === $data = $this->ExcelToArr($uploadinfo[0]['savepath'], $uploadinfo[0]['savename'])){
            unlink($uploadinfo[0]['savepath'].$uploadinfo[0]['savename']);
            die(json_encode(array('error'=>1, 'msg'=>'上传失败文件格式不合法')));
        }
        die(json_encode(array('error'=>0, 'msg'=>'上传成功', 'result'=>$uploadinfo)));
    }
    function insertDb(){
        $path = C('UPLOAD_PATH') . 'data/';
        $filename = $_POST['filename'];
        if(!is_file($path.$filename) || !$filename)
            die(json_encode(array('error'=>1, 'msg'=>'文件不存在')));
        if(false === $data = $this->ExcelToArr($path, $filename))
            die(json_encode(array('error'=>1, 'msg'=>'文件格式不合法')));
        $add_data = array();
        foreach($data as $key=>$val){
            $add_data[$key] = '(';
            $add_data_val = array('NULL');
            foreach($val as $k=>$v){
                $add_data_val[] = '\''.$v.'\'';
            }
            $add_data[$key] .= implode(',',$add_data_val);
            $add_data[$key] .= ')';
        }
        $add_data_string = implode(',',$add_data);
        if(!$add_data_string)
            die(json_encode(array('error'=>1, 'msg'=>'写入失败，xls表数据为空')));
        D()->query('TRUNCATE TABLE '.array_shift(explode('.',$filename)));
        D()->query('INSERT INTO '.array_shift(explode('.',$filename)).' VALUES '.$add_data_string);
        die(json_encode(array('error'=>0, 'msg'=>'写入成功')));
    }
    function ExcelToArr($filepath, $filename){
        Vendor('PHPExcel.PHPExcel.IOFactory');
        $inputFileName = $filepath . $filename;
        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
        if(!in_array(strtolower($inputFileType), array('excel2007', 'excel5'))){
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
        return $nsheetData;
    }*/
}