<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.cn All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 生成静态页面类
 *
 * @category   Wine
 * @package    Core
 * @author     mengfk<dreamans@163.com>
 */
class MakeHtml {

    /**
     * 相对物理路径
     */
    protected $_path = '';

    public function __construct($path) {
        $this->_path = $path;
    }

    /**
     * 生成静态文件
     */
    public function make($path, $content) {
        //生成绝对路径
        $path = $this->_path . DS . $path;
        $spath = dirname($path);
        if(!self::create($spath)) {
            return false;
        }
        return self::write($path, $content);
    }

    /**
     * 删除内容
     */
    public function delHtml($path) {
        $content = '<script>location.href="/";</script>';
        $path = $this->_path . DS . $path;
        return self::write($path, $content);
    }

    /**
     * 生成目录
     */
    protected static function create($dir) {
        $status = true;
        if(!is_dir($dir)){
            $status = mkdir($dir, 0777 ,true);
        }
        return $status;
    }

    /**
     * 写入内容
     */
    protected static function write($file_path, $data, $mode = 'wb') {
        if(!$fp = @fopen($file_path, $mode)) {
            return false;
        }
        if(flock($fp, LOCK_EX)) {
            fwrite($fp, $data);
            flock($fp, LOCK_UN);
        } else {
            return false;
        }
        fclose($fp);
        return true;
    }
}
