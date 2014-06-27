<?php


// +----------------------------------------------------------------------
// | 商机网 [ 58.wine.cn]
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2013 http://58.wine.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------



/**
 * Expo Web前端默认首页模块
 * @category   web 前端
 * @subpackage  Action
 * @author    Angf <272761906@qq.com>
 */


class IndexAction extends CommonAction {



    /*
     *国内企业入口操作
     */
    public function index() {

        $this->display();

    }

      /*
     * 国外企业入口操作
     */
    public function foreign() {

        $this->display();
    }


    /*
     * 收费标准 详情页面
     */
    public function user_grade(){
        $this->display();
    }





}
