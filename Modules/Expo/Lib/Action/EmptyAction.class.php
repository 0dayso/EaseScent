<?php
// +----------------------------------------------------------------------
// | 58.wine.cn [ 商机网 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://wine.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------

/**
 * Angf 空模块处理
 * @category EmptyAction
 * @package  EmptyAction.class.php
 * @author   Angf <272761906@qq.com>
 */




class EmptyAction extends Action{



    /**
     * 空模块处理
     * @return error
     */
    public function index(){
        $this->error(' 没有找到 【'.MODULE_NAME.'】 此模块');

    }


    /**
     * _empty
     * 空操作提示
     * @access public
     * @return str
     */
     public function _empty($action){
        $this->error(MODULE_NAME.' 模块下【'.$action.'】动作不存在');
     }

}
?>