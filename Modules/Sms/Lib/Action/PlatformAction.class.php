<?php

class PlatformAction extends CommonAction
{


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
