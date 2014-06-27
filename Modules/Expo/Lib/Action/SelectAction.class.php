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
 * Angf 企业类型选择
 * @category 企业类型选择
 * @package  SelectAction.class.php
 * @author   Angf <272761906@qq.com>
 */

class SelectAction extends CommonAction {

 /**
     * index
     * 身份选择
     * @access public
     * @return void
     */
    public function index() {
        $qy_id =  $_SESSION["ym_users"]["uid"];
        $agent_id = M('Agent')->where(array('qy_id'=>$qy_id))->getField('id');
        if($agent_id) $this->redirect('Agent/info?agent_id='.$agent_id);

        //认证跳转
        if($this->isPost() && $qy_id) {
                 $data['qy_id'] =  $qy_id;
                 $data['company_type']  =  $this->_post('Identity');
                 $qyInfo   =  M('Agent')->where(array('qy_id'=>$qy_id))->find();
                 if(!$qyInfo) $agent_id = D('Agent')->data($data)->add();
                 $Cache = Cache::getInstance('File');
                 $Cache->rm('_Get_UserInfo'.$qy_id);
                 $this->redirect('Agent/info?agent_id='.$agent_id);
         }
        $this->display();
    }




}
