<?php

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
    protected $_indexfilename;
	protected $_indexpath;

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
    public function uploadFile($filedata) {
        //获取上传文件基本信息
        $info = $this->_getFileInfo($filedata);

        //检查文件的合法性
        $this->_checkFile($info['ext']);

        //检查文件尺寸是否合法
        $this->_checkSize($info['size']);

        //生成文件名
        !$this->_errno && ($filename = $this->_createFilename($info['ext']));

        //创建目录
        !$this->_errno && ($dir = $this->_createDir($filename));


        //移动目录
        !$this->_errno && $this->_movefile($info['tmpname'], $dir['fullpath'] . $filename);

        $newinfo = array();
        if(!$this->_errno) {
            $newinfo = array(
                'rootpath' => $dir['rootpath'],
                'subpath' => $dir['subpath'],
                'filename' => $filename,
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
    protected function _getFileInfo($filedata) {
        $info = array();
        $info['name'] = is_array($_FILES[$filedata]['name']) ? $_FILES[$filedata]['name'][0] : $_FILES[$filedata]['name'];
        $info['tmpname'] = is_array($_FILES[$filedata]['tmp_name']) ? $_FILES[$filedata]['tmp_name'][0] : $_FILES[$filedata]['tmp_name'];
        $info['size'] = is_array($_FILES[$filedata]['size']) ? $_FILES[$filedata]['size'][0] : $_FILES[$filedata]['size'];
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
    protected function _createDir($filename) {
        $dir['rootpath'] = $this->_path;
        $dir['subpath'] = mb_substr($filename, 0, 2, 'utf-8') . '/' . mb_substr($filename, 2, 2, 'utf-8') . '/';
        $dir['fullpath'] = $dir['rootpath'] . $dir['subpath'];
        $status = true;
        if(!is_dir($dir['fullpath'])){
            $status = mkdir($dir['fullpath'], 0777 ,true);
            @touch($dir['fullpath'].'/index.html');
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
    protected function _createFilename($ext) {
        return rand(10000,99999) . substr(md5(time()), 0, 16) . '.' . $ext;
    }
}
