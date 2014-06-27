<?php

/**
 * 系统类
 */
class SystemAction extends CommonAction {

    public function clearRuntime() {
		if($this->isPost()) {
			$app = $_POST['app'];
			if(is_array($app)) {
				foreach($app as $appVal) {
					//这里只会删除默认Runtime目录的缓存
					$dir = CODE_RUNTIME_PATH  . DS . 'Modules' . DS .$appVal.DS. 'Runtime'.DS.'Cache';
					if(is_dir($dir)) { 
						$hand = opendir($dir);
						while(($file = readdir($hand)) !== false) {
							if($file !=='.' && $file !== '..') {
								$filepath = $dir.DS.$file;
								if(is_file($filepath)) {
									@unlink($filepath);
								}
							}
						}
					}
				}
			}
			$this->_jumpGo('缓存清除成功', 'succeed', Url('clearRuntime'));
		}
		$this->assign('app',$GLOBALS['MODULES_ALLOW']);
        $this->display();
    }

    /**
     * 管理第三方应用APPKEY
     */
    public function app() {
        $op = isset($_GET['op']) ? $_GET['op']: '';
        //判断是否删除
        if($op == 'del') {
            $appid = isset($_GET['appid']) ? intval($_GET['appid']) : 0;
            if($appid && D('App')->delByAppid($appid)) {
                $this->_jumpGo('删除成功', 'succeed', Url('app'));
            }
            $this->_jumpGo('删除失败', 'error', Url('app'));
        }
        if($this->isPost()) {
            $apps = $_POST['appname'];
            $name = $_POST['name'];
            if(is_array($apps) && !empty($apps)) {
                foreach($apps as $key => $val) {
                    D('App')->updateAppNames(array('name' => $val), array('appid' => $key));
                }
            }
            if($name) {
                //生成Key
                $key = md5((rand().time()));
                $data = array(
                    'name' => $name,
                    'appkey' => $key,
                    'addtime' => time(),
                    'adduser' => $_SESSION['admin_username'],
                ); 
                D('App')->addApps($data);
            }
            $this->_jumpGo('操作成功', 'succeed', Url('app'));
        }
        $list = D('App')->getApps();
        $this->assign('list', $list);
        $this->display();
    }
}
