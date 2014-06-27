<?php
/**
* @file ClientAction.class.php
* 
* @brief  短信平台发送客户端管理
* Copyright(C) 2010-2015 Easescent.com, Inc. or its affiliates. All Rights Reserved.
* 
* @version $Id$
* @author Tiger, ji.xiaod@gmail.com
* @date 2013-03-11
*/

class ClientAction extends CommonAction
{

    public function index()
    {
        $list = D('Client')->getClientList();
        $platforms = D('Platform')->getList();
        $nlist = array();
        foreach($platforms as $key => $val) {
            $nlist[$val['id']] = $val;
        }
        //return $nlist;
        $this->assign('list', $list);
        $this->assign('platforms', $nlist);
        $this->display();

    }

    public function add()
    {
        if ( $this->isPost() ) {
            $client_name = $_POST['client_name'];       
            $platform_id = $_POST['platform_id'];
            $skey        = md5((rand().time())); 
            $text_suffix = $_POST['text_suffix'];

            $data = array(
                            'client_name'=>$client_name,
                            'platform_id'=>$platform_id,
                            'skey'=>$skey,
                            'text_suffix'=>$text_suffix,
                            'create_time'=>time(),
                            'create_user'=>$_SESSION['admin_username'],
                            'update_time'=>time(),
                            'update_user'=>$_SESSION['admin_username']
            );
            D('Client')->addClient($data);

            $this->_jumpGo('客户端添加成功', 'succeed', Url('Sms/Client/index'));
        }

        $this->assign('platforms', D('Platform')->getList());
        $this->display();
    }
    
    public function edit()
    {
        if ( $this->isPost() ) {
            $id          = $_POST['client_id'];
            $client_name = $_POST['client_name'];       
            $platform_id = $_POST['platform_id'];
            $text_suffix = $_POST['text_suffix'];

            $data = array(
                            'client_name'=>$client_name,
                            'platform_id'=>$platform_id,
                            'text_suffix'=>$text_suffix,
                            'update_time'=>time(),
                            'update_user'=>$_SESSION['admin_username']
            );
            D('Client')->updateClient($data, array('id' => $id));

            $this->_jumpGo('客户端更新成功', 'succeed', Url('Sms/Client/index'));
        }
        $id = $_GET['cid'];
        $vo = D('Client')->getClientById($id);
        $this->assign('platforms', D('Platform')->getList());
        $this->assign('vo',$vo);
        //var_dump($vo);exit;
        $this->display();
    }

    public function del()
    {
    
    }
    public function manage()
    {
        $op = isset($_GET['op']) ? $_GET['op']: '';
        //判断是否删除
        if($op == 'del') {
            $id = isset($_GET['platformid']) ? intval($_GET['platformid']) : 0;
            if($id && D('Platform')->deletePlatformById($id)) {
                $this->_jumpGo('删除成功', 'succeed', Url('manage'));
            }
            $this->_jumpGo('删除失败', 'error', Url('manage'));
        }

        if ( $this->isPost() ) {
            $name = $_POST['name'];
            $account = $_POST['account'];
            $passwd = $_POST['passwd'];

            $platform_name = $_POST['platform_name'];
            $platform_account = $_POST['platform_account'];
            $platform_passwd = $_POST['platform_passwd'];
                
            if(is_array($platform_name) && !empty($platform_name)) {
                foreach($platform_name as $key => $val) {


                    $data = array(
                                    'name' => $platform_name[$key], 
                                    'account' => $platform_account[$key],
                                    'password' => $platform_passwd[$key],
                                    'update_user' => $_SESSION['admin_username'],
                                    'update_time' => time()
                                 );
                    D('Platform')->update($data, array('id' => $key));
                }
            }

            if ( $name ) {
                $data = array(
                        'name' => $name,
                        'account' => $account,
                        'passwd'  => $passwd,
                        'create_time' => time(),
                        'create_user' => $_SESSION['admin_username'],
                        'update_user' => $_SESSION['admin_username'],
                        'update_time' => time()
                        );  
                D('Platform')->addPlatform($data);
            }

            $this->_jumpGo('操作成功', 'succeed', Url('manage'));

        }       
        
        
        /**
        * 第三方平台列表
        */
        $list = D('Platform')->getList();
        $this->assign('list', $list);
        $this->display();
    }
}
