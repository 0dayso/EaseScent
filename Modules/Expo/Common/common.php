<?php
// +----------------------------------------------------------------------
// | 58.wine.cn [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------

/**
 * wine 基础函数库
 * @category   wine
 * @package    Common
 * @author     Angf <272761906@qq.com>
 */



/**
 * Search_parm_url
 * 使用方法:
 * @param param   获取$_GET所有参数
 * @param linkurl 当前链接的参数 例如array('id'=>1,name='3')
 * @return string
 */

  function createSearchUrl($param=array(),$linkurl=array()){
      $str  = '';
      $data = array_merge($param,$linkurl);
      foreach($data as $key=>$value)   $str.='&'.$key.'='.$value;
      return $str;
  }

?>