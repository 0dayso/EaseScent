<?php
// +----------------------------------------------------------------------
// | 后台操作权限管理 [ 58.wine.cn]
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2013 http://58.wine.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Angf <272761906@qq.com>
// +----------------------------------------------------------------------




/**
 * Expo项目的 后台权限管理
 * @category   后台操作权限管理
 * @subpackage Action
 * @author     Angf <272761906@qq.com>
 */

class AdminUserAction extends AdminCommonAction {


    /**
     * Angf 后台管理员 管理
     * @category index
     * @package  GoodsAction.class.php
     * @author   Angf <272761906@qq.com>
     */
    public function index(){

        import('ORG.Util.Page');
        $agent = M('admin_user');

        //搜索提交
        $keyword = $this->_REQUEST('keyword');
        if($keyword)   $conditions['username']  = array('like','%'.$keyword.'%');

        $count      = $agent->where($conditions)->count();
        $Page       = new Page($count,15);
        if($keyword || $grade!='') $Page->parameter.= "&keyword=".$keyword;

        $Page->setConfig('theme',"<li><a>%totalRow% %header% %nowPage%/%totalPage% 页</a></li> <li>%upPage%</li> <li>%downPage%</li> <li>%first%</li> <li>%prePage%</li> <li>%linkPage%</li> <li>%nextPage%</li> <li>%end%</li>");
        $pageShow  = $Page->show();
        $data= $agent->where($conditions)->order('uid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('Page',$pageShow);
        $this->assign('adminusers',$data);

        $this->display('Admin:admin_user');

    }



    /**
     * Angf 获取商机 会员
     * @category member
     * @package  AdminUserAction.class.php
     * @author   Angf <272761906@qq.com>
     */
    public function member(){

        import('ORG.Util.Page');
        $member_type_rows=$this->getMemberType();
        $agent = M('agent');

        //搜索提交
        $keyword = $this->_REQUEST('keyword');
        $grade   = $this->_REQUEST('member_grade');
        if($keyword)   $conditions['qy_name']  = array('like','%'.$keyword.'%');
        if($grade!='') $conditions['grade']    = $grade ;

        $count      = $agent->where($conditions)->count();
        $Page       = new Page($count,15);
        if($keyword || $grade!='') $Page->parameter.= "&keyword=".$keyword.'&member_grade='.$grade;

        $Page->setConfig('theme',"<li><a>%totalRow% %header% %nowPage%/%totalPage% 页</a></li> <li>%upPage%</li> <li>%downPage%</li> <li>%first%</li> <li>%prePage%</li> <li>%linkPage%</li> <li>%nextPage%</li> <li>%end%</li>");
        $data['pageShow']  = $Page->show();
        $data['list'] = $agent->where($conditions)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $data['member_type'] = $member_type_rows;
        $this->assign('Page',$data['pageShow']);
        $this->assign('members',$data);
        $this->display('Admin:members');
    }


    /**
     * Angf 商机会员开通 会员服务
     * @category member open service
     * @package  AdminUserAction.class.php
     * @author   Angf <272761906@qq.com>
     */
    public function open_service($agent_id=0){

        //用户会员等级编辑
        if($this->isPost() && $agent_id){

            //更新会员信息
            $data['grade']           = $this->_post('grade');
            $data['serve_open_time'] = (strtotime($this->_post('star_time')) < 0) ? time() : strtotime($this->_post('star_time'));
            $data['serve_end_time']  = (strtotime($this->_post('end_time'))  < 0) ? time() : strtotime($this->_post('end_time'));
            M('agent')->where('id='.$this->_post('agent_id'))->save($data);

            //用户操作日志
            $logs['text']   = $this->_post('operate_log');
            $logs['uid']    = $this->session['uid'];
            $logs['rel_id'] = $this->_post('agent_id');
            $logs['uname']  = $this->session['username'];
            $logs['create_time'] = time();
            $logs['type']   = "member_open_service";
            M('operation_logs')->add($logs);

            //更新对应的产品 等级
            M('goods')->where('agent_id='.$logs['rel_id'])->data(array('agent_grade'=>$data['grade']))->save();
            $this->success('服务-编辑成功',U('AdminUser/open_service').'&agent_id='.$logs['rel_id']);exit;
       }

        //获取用户信息
        $member_type_rows=$this->getMemberType();
        if($agent_id)
            $agent_info = M()->field('a.*,a.id as agent_id ,y.*')->Table('expo_agent as a')->join(' ym_users_qy as y ON y.id = a.qy_id')->where("a.id=".$agent_id)->find();
        $agent_info['member_type'] = $member_type_rows;
        $this->assign('agent_info',$agent_info);


        //获取对应的 操作日志
        $operation_logs = AdminCommonAction::operation_logs('member_open_service',$agent_info['agent_id']);
        $this->assign('operation_logs',$operation_logs);
        $this->display('Admin:open_service');
    }


    /**
     * Angf 查出会员类型
     * @category getMemberType
     * @package  AdminUserAction.class.php
     * @author   Angf <272761906@qq.com>
     */
    protected function getMemberType(){
        //查出会员类型 type =100 表示会员类型 类
        $member_type = M('system_config')->cache('member_type_rows','7200')->where('type=100')->select();
        foreach ($member_type as $key => $value) {
            $data[$value['value']] = $value['view_value'];
        }
        return $data;
    }




}