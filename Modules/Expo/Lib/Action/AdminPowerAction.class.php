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

class AdminPowerAction extends AdminCommonAction {


    /**
     * 获取 用户对应的权限列表
     */
    public function index($uid=0){
        if(intval($uid)){

            //查询用户对应的权限
            $user_power_to_all = M('admin_user_field')->where(array('uid'=>$uid,'power_name'=>'all'))->Count();

            if(!$user_power_to_all){
                $user_power_to_all=0;
                $user_powers = M('admin_user_field')->field('p_id')->where(array('uid'=>$uid))->select();
            }
            //用户权限数据从组
            foreach ($user_powers as $key => $value) {
                $user_power_ids[]= $value['p_id'];
            }

            //权限列表
            $data = M('Power')->order('fid asc , sort_order asc')->select();
            foreach ($data as $key => $value) {
                if($value['fid']==0)
                    $new_key =  $value['id'];
                else
                    $new_key = $value['fid'];

                //判断是否是否全选
                if($user_power_to_all) $value['selected']=1;
                $power_data[$new_key][]=$value;
            }


            $this->assign('condition',array('all'=>$user_power_to_all,'user_checked_powers'=>$user_power_ids));
            $this->assign('power_data',($power_data));
            $this->display('Admin:power_manage');
        }else{
            $this->error('请选择对应的管理员',U('AdminUser/index'));
        }
    }



    /**
     * 用户 添加 修改权限权限
     */
    function powerSubmit(){
        $uid = intval($this->_post('uid'));

        //添加新的数据
        if($uid && $_REQUEST['user_power'] ){

            //先删除用户原有的权限
            M('admin_user_field')->where('uid='.$uid)->delete();

            foreach ($_REQUEST['user_power'] as $key => $value) {
                $data[]=array('power_name'=>$key,'p_id'=>$value,'uid'=>$uid);
            }
            if($_REQUEST['checkall'])  $data =array(array('power_name'=>'all','uid'=>$uid));

            if(M('admin_user_field')->addAll($data))
                $this->success('添加成功');
            else
                $this->error('添加失败');

        }else{
            $this->error('请为用户 选择对应的权限');
        }



    }



}

