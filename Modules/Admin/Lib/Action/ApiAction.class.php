<?php

class ApiAction extends Api {
    
	/**
	 * 用appid, appkey获取访问令牌
	 * 如果redis中存在访问令牌直接返回
	 * 不存在则创建
	 */
    public function accessToken() {
        $appid = isset($_POST['appid']) ? intval($_POST['appid']): '';
        $appkey = isset($_POST['appkey']) ? $_POST['appkey']: '';
		if(!$appid || !$appkey) {
			self::rst('', 400001, 'Appid or appkey empty');
		}
		//连接数据库进行匹配
		$app = D('App')->getAppByIdKey($appid, $appkey);
		if(empty($app)) {
			self::rst('', 400007, 'App dos not exist or appkey error');
		}
        $key = $this->_redisPre.$appid;
		$token = $this->_redis()->get($key);
		//令牌不存在,创建
		if(empty($token)) {
			$token = md5(rand().time());
			$this->_redis()->setex($key, $this->_timeout, $token);
            if(isset($app['name']) && !empty($app['name'])) {
                $this->_redis()->set($key.':name', $app['name']);
            }
		}
		self::rst($token);
    }
}
