<?php
/**
* @file uploadify.php
* 
* @brief  处理uploadify上传图片文件
* Copyright(C) 2010-2015 Easescent.com, Inc. or its affiliates. All Rights Reserved.
* 
* @version $Id$
* @author Tiger, ji.xiaod@gmail.com
* @date 2013-01-24
*/
include 'config.php';
//include 'thumb.php';
define('DS', '/');
define('TMP_PATH',   DS . 'Guide' . DS . 'goods');
$cfg = array(
        'ext' => 'jpeg,jpg,png,gif',
        //上传最大尺寸 单位 KB
        'size' => 1000,
        //上传物理目录
        'path' => CODE_RUNTIME_PATH . DS . 'Upload'. TMP_PATH
);
if (!empty($_FILES))
{                                                                                                                                     
    //$rootPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
    $upload = new Upload();
    $upload->config($cfg);
    $file_info = $upload->_getFileInfo("Filedata");
    $filename = $upload->_createFilename($file_info['ext']);	
    //$rest = $upload->makeThumb('Filedata',$filename, 294,294);
	//$rest =$upload->ImageResize($file_info['tmpname'],294,294,$dir['realpath'].$new_filename,$file_info['ext']);
    $rest = $upload->uploadFile('Filedata', $filename);
    // $rest = $upload->uploadFile('Filedata');
    if ($rest['errno'])
	{
        $result = array(
                'error' => 1,
                'message' => $upload->error(),
                );
    }
	//缩略图
	$new_filename=dirname(dirname(dirname(dirname(__FILE__)))) ."/Upload".TMP_PATH.str_replace($filename,'',$rest['path']).str_replace(strrchr($filename, '.'), '', $filename) . '_thumb.'.$file_info['ext'];
	$upload->RatioAdjuct($rest['realpath'], 620,320,true,"#FFFFFF",$new_filename,$file_info['ext']);
    if ( file_exists($rest['realpath']) ) {
        list($width, $height) = getimagesize($rest['realpath']);
    }   
    $restSepartor = explode('.',$rest['path']);
    $result = array(
            'error' => 0,
            'url' => UPLOAD_DOMAIN . TMP_PATH . $rest['path'],
            'filename' => $rest['path'],
            'width' => $width,
            'height' => $height,
			'thumb_name' =>TMP_PATH.str_replace($filename,'',$rest['path']).str_replace(strrchr($filename, '.'), '', $filename) . '_thumb.'.$file_info['ext'],


            'thumb_url' => UPLOAD_DOMAIN . TMP_PATH . str_replace($filename,'',$rest['path']).str_replace(strrchr($filename, '.'), '', $filename) . '_thumb.'.$file_info['ext']

            );

    echo json_encode($result);
}



/**
 * Upload上传类
 */

/**
 * 上传文件工具类
 */
class Upload {
    
    /**
     * 允许上传的扩展名
     * @var string
     */
    protected $_allow_ext;
    
    /**
     * 允许上传的最大尺寸
     * @var int
     */
    protected $_maxsize;
    
    /**
     * 上传附件根目录
     * @var string 
     */
    protected $_path;
    
    /**
     * 错误信息 
     * 0 - 无错误
     * 1 - 扩展名不合法
     * 2 - 目录创建失败
     * 3 - 文件移动失败
     * 4 - 文件尺寸过大
     * @var int
     */
	protected $_errno = 0;

	/**
	 * 错误信息
	 */
	protected $_error = array(
		0 => '上传成功',
		1 => '扩展名不合法',
		2 => '目录创建失败',
		3 => '文件移动失败',
		4 => '文件尺寸过大',
	);

    /**
     * 配置信息
     * 
     * @param string $ext
     * @param int $size 
     * @return void
     */
    public function config($cfg) {
        $this->_allow_ext = $cfg['ext'];
        $this->_maxsize = $cfg['size'];
        $this->_path = $cfg['path'];
    }

    /**
     * 上传文件主方法
     * 
     * @param string $filedata
     * @return array 
     */
    public function uploadFile($filedata, $filename) {

        //获取上传文件基本信息
        $info = $this->_getFileInfo($filedata);

        //检查文件的合法性
        $this->_checkFile($info['ext']);
        
        //检查文件尺寸是否合法
        $this->_checkSize($info['size']);
        
        //创建目录
        !$this->_errno && ($dir = $this->_createDir());

        //生成文件
        //!$this->_errno && ($filename = $this->_createFilename($info['ext']));
        
        //移动目录
        !$this->_errno && $this->_movefile($info['tmpname'], $dir['realpath'] . $filename);
        
        $newinfo = array();
        if(!$this->_errno) {
            $newinfo = array(
                'ext' => $info['ext'],
                'realpath' => $dir['realpath'] . $filename,
                'path' => $dir['path'] . $filename,
                'size' => $info['size'],
                'errno' => $this->_errno,
            );
        }else{
            $newinfo['errno'] = $this->_errno;
        }
        return $newinfo;
	}
        
        /**
     * 上传文件主方法 缩略图
     * 
     * @param string $filedata
     * @return array 
     */
    public function makeThumb($filedata,$filename,$width,$height) {
 //获取上传文件基本信息
        $info = $this->_getFileInfo($filedata);

        //检查文件的合法性
        $this->_checkFile($info['ext']);
        
        //检查文件尺寸是否合法
        $this->_checkSize($info['size']);
        
        //创建目录
        !$this->_errno && ($dir = $this->_createDir());
        //生成文件
        //!$this->_errno && ($filename = $this->_createFilename($info['ext']));
        
        //移动目录
       // !$this->_errno && $this->_movefile($info['tmpname'], $dir['realpath'] . $filename);
           if($info['ext'] == "jpg"){
                $im = imagecreatefromjpeg($_FILES[$filedata]['tmp_name']);
            }elseif($info['ext'] == "png"){
                $im = imagecreatefrompng($_FILES[$filedata]['tmp_name']);
            }elseif($info['ext'] == "gif"){
                $im = imagecreatefromgif($_FILES[$filedata]['tmp_name']);
            } 
         
        $fileSepeator = explode('.',$filename);
        $targetFile_lue = $dir['realpath'] .$fileSepeator[0].'_'.$width."_thumb.".$fileSepeator[1];
	$this->ResizeImage($im,$width,$height,$targetFile_lue);
	ImageDestroy ($im);
        $newinfo = array();
        if(!$this->_errno) {
            $newinfo = array(
                'ext' => $info['ext'],
                'realpath' => $dir['realpath'] . $filename,
                'path' => $dir['path'].$filename,
                'size' => $info['size'],
                'errno' => $this->_errno,
            );
        }else{
            $newinfo['errno'] = $this->_errno;
        }
        return $newinfo;
	}

	/**
	 * 返回错误信息
	 */
	public function error() {
		return isset($this->_error[$this->_errno]) ? $this->_error[$this->_errno]: 'unknown error';
	}
    
    /**
     * 移动文件到目标目录
     * 
     * @param string $tmpfile
     * @param string $newfile
     * @return void 
     */
    protected function _movefile($tmpfile, $newfile) {
        if (function_exists('move_uploaded_file') && move_uploaded_file($tmpfile, $newfile)) {
            
        } elseif (@rename($tmpfile, $newfile)){
            
        } elseif (copy($tmpfile, $newfile)){
            
        } else {
            $this->_errno = 3;
        }
	@unlink($tmpfile);
    }
    
    /**
     * 检查上传文件尺寸
     * 
     * @param int $size 
     * @return void
     */
    protected function _checkSize($size) {

        if(($size > $this->_maxsize * 1024) || $size/1024/1024 > intval(ini_get('upload_max_filesize'))) {
            $this->_errno = 4;
        }
    }

    /**
     * 获取上传文件相关信息
     * 
     * @param string $filedata 
     * @return array
     */
    public function _getFileInfo($filedata) {
        
        $info = array();
        $info['name'] = $_FILES[$filedata]['name'];
        $info['tmpname'] = $_FILES[$filedata]['tmp_name'];
        $info['size'] = $_FILES[$filedata]['size'];
        $info['ext'] = pathinfo($info['name'], PATHINFO_EXTENSION);
        return $info;
    }

    /**
     * 检查上传文件是否是图片
     * 此方法需要php_exif模块支持
     * 若图片格式为gif、jpg、png、bmp时返回true，负责返回false
     * 
     * @param string $filename
     * @return boolean 
     */
    public function checkImg($filename) {
        $isimage = false;
        $pic_info = exif_imagetype($filename);
        if(in_array($pic_info, array('IMAGETYPE_GIF','IMAGETYPE_JPEG','IMAGETYPE_PNG','IMAGETYPE_BMP'))) $isimage = true;
        return $isimage;
    }

    /**
     * 检查文件扩展名是否合法
     * 
     * @param string $ext
     * @param boolean $isimage
     * @return boolean 
     */
    protected function _checkFile($ext) {
        $allow_ext = explode(',', $this->_allow_ext);
        if(in_array(strtolower($ext), $allow_ext)) {
            return true;
        }else{
            $this->_errno = 1;
        }
    }
    
    /**
     * 创建目录
     * 
     * @param boolean $index
     * @return array 
     */
    protected function _createDir($index=true) {
        $dir = array();
         $dir['path'] = date("/Y/m/d/",time());
         $dir['realpath'] = dirname(dirname(dirname(dirname(__FILE__)))) ."/Upload".TMP_PATH. $dir['path'];
        $status = true;
        if(!is_dir($dir['realpath'])){
            echo $dir['realpath'];
            $status = mkdir($dir['realpath'], 0777 ,true);
            $index && @touch($dir['realpath'].'/index.html');
        }
        if(!$status){
            $this->_errno = 2;
        }
        return $dir;
    }
    
    /**
     * 生成文件名
     * 
     * @param string $ext
     * @return string 
     */
    public function _createFilename($ext) {
        return rand(10000,99999) . substr(md5(time()), 0, 16) . '.' . $ext;
    }
    
    /**生成缩略图
     * 
     */
    public function ResizeImage($im,$maxwidth,$maxheight,$name)
	{
		$width = imagesx($im);
		$height = imagesy($im);
		if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight))
		{
			if($maxwidth && $width > $maxwidth){
				$widthratio = $maxwidth/$width;
				$RESIZEWIDTH=true;
			}
			if($maxheight && $height > $maxheight){
				$heightratio = $maxheight/$height;
				$RESIZEHEIGHT=true;
			}
			if(@$RESIZEWIDTH && @$RESIZEHEIGHT)
			{
				if($widthratio < $heightratio){
					$ratio = $widthratio;
				}
				else
				{
					$ratio = $heightratio;
				}
			}
			elseif($RESIZEWIDTH)
			{
				$ratio = $widthratio;
			}
			elseif($RESIZEHEIGHT)
			{
				$ratio = $heightratio;
			}
		$newwidth = $width * $ratio;
		$newheight = $height * $ratio;
		if(function_exists("imagecopyresampled"))
		{
			$newim = imagecreatetruecolor($newwidth, $newheight);
			imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
		else
		{
			$newim = imagecreate($newwidth, $newheight);
			imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
			//ImageJpeg ($newim,$name . ".jpg");
			ImageJpeg ($newim,$name);
			ImageDestroy ($newim);
		}
		else
		{
			//ImageJpeg ($im,$name . ".jpg");
			ImageJpeg ($im,$name);
		}
	}
		/*
	* 图片大小比例调整
	*
	* @param $filename        图片路径
	* @param $w　             目标宽度
	* @param $h　　           目标高度
	* @param $override       是否覆盖原文件
	* @param $background     是否产生背景, 如果要求产生背景则产生图像是指定的大小，　图片内容居中
	* @param $color          背影色
	* @param $tofile         新产生的文件名
	* @param $tojpg          是否让缩略图只生成JPG格式
	*/
	function RatioAdjuct($filename, $w, $h, $background, $color,$tofile,$tojpg){
		list($imgWidth,$imgHeight)=getImageSize($filename);                     
		$ratioX = $imgWidth / $w;
		$ratioY = $imgHeight / $h;
		if ($ratioX > $ratioY || $ratioX == $ratioY)
		{
			$dst_w = $w;
			$dst_h = ceil($imgHeight / $ratioX);
		}
		elseif($ratioY > $ratioX)
		{
			$dst_h = $h;
			$dst_w = ceil($imgWidth / $ratioY);
		}  
		//判断图片类型
		switch (strtolower(strrchr($filename, '.')))
		{
			case '.jpg':
			case '.jpeg':
				  $im = imageCreateFromJpeg($filename);
					break;                        
			case '.gif' :
				 $im = imageCreateFromGif($filename);
				break;
			case '.png' :
				$im = imageCreateFromPng($filename);        
		}
		
		//是否有背景色
		if (null !== $background) 
		{
			//将背景色转换为十进制的红绿蓝值
			$dec = hexdec($color);
			$red =  0xFF & ($dec >> 0x10);
			$green = 0xFF & ($dec >> 0x8);
			$blue = 0xFF & $dec;                                   
			//居中定位并复制
			$dst_pos = array ('d_x' => 0, 'd_y' => 0);
			($dst_w == $w) ? ( $dst_pos['d_y'] = (($h - $dst_h) / 2) ) : ($dst_pos['d_x'] = (($w - $dst_w) / 2));
			$imBox = imageCreateTrueColor($w, $h);
			$color_bg = imageColorAllocate($imBox, $red, $green, $blue);
			imageFill($imBox, 0, 0, $color_bg);
			imagecopyresampled($imBox,$im,$dst_pos['d_x'],$dst_pos['d_y'], 0, 0,$dst_w,$dst_h,$imgWidth,$imgHeight);
		}
		else
		{
			$imBox = imageCreateTrueColor($dst_w, $dst_h);
			imagecopyresampled($imBox, $im, 0, 0, 0, 0, $dst_w, $dst_h, $imgWidth, $imgHeight);
		}              
		//不替换源图片
		/*if (null === $override)
		$filename = str_replace(strrchr($filename, '.'), '', $filename) . '_thumb.png';                   
		return imagejpeg($imBox, $filename) ? $filename : false;
		*/
		if($tojpg=='')
		{
			switch (strtolower(strrchr($filename, '.')))
			{
				case '.jpg' :
				case '.jpeg' :
					imagejpeg($imBox,$tofile);
				break;                        
				case '.gif' :
					imagegif($imBox,$tofile);
				break;                               
				case '.png' :
					imagepng($imBox,$tofile);       
			}
		}
		else
		{
			imagejpeg($imBox,$tofile);            
		}
		imagedestroy($imBox);
		imagedestroy($im);
	}

}

