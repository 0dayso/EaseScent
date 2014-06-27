<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * Api基类,进行接口验证,各个项目Api控制器需继承此类
 *
 * @category   Wine
 * @package    Core
 * @author     mengfk<dreamans@163.com>
 */
class Api extends Action {

	/**
	 * redis前缀
	 */
	protected $_redisPre;

	/**
	 * accessToken过期时间
	 */
    protected $_timeout;

    /**
     * 应用名称
     */
    public $appName;

    public function _initialize() {
        $this->_timeout = isset($GLOBALS['API_TOKEN_TIMEOUT']) ? $GLOBALS['API_TOKEN_TIMEOUT'] : 18000;
        $this->_redisPre = isset($GLOBALS['API_TOKEN_KEYPR']) ? $GLOBALS['API_TOKEN_KEYPR'] : 'wine_api_';
		$this->_checkSign();
	}

	protected function _redis() {
		static $redis = NULL;
		if(!$redis) {
			//检测Redis扩展和Redis配置信息是否存在
			if(!class_exists('Redis')) {
				self::rst('', 400004, 'Redis extends does not exist');
			}
			$cfg = C('REDIS_CONFIG');
			if(empty($cfg)) {
				self::rst('', 400005, 'Redis connect configure information does not exist');
			}
			$redis = new Redis();
			$redis->connect($cfg['host'], $cfg['port']);
		}
		return $redis;
	}

	/**
	 * 进行权限验证
	 */
    protected function _checkSign() {
		//不对令牌生成接口做限制
		if (ACTION_NAME == 'accessToken') return;

		$appid = isset($_POST['appid']) ? intval($_POST['appid']): '';

		$accessToken = isset($_POST['accessToken']) ? $_POST['accessToken']: '';

		if(!$appid || !$accessToken) {
			self::rst('', 400002, 'AccessToken or appid empty');
		}
		$key = $this->_redisPre.$appid;

		$token = $this->_redis()->get($key);

		//不存在
		if(!$token) {
			self::rst('', 400003, 'AccessToken timeou');
		}
		//存在token,验证是否相同
		if($token != $accessToken) {
			self::rst('', 400006, 'AccessToken is wrong');
		}
		//验证通过,更新redis存储token时间
        $this->_redis()->setex($key, $this->_timeout, $token);
        //获取应用名称
        $this->appName = $this->_redis()->get($key.':name');
    }

	/**
	 * 格式化结果
	 */
	protected static function rst($result, $errCode = 0, $errStr = '') {
		$rst = array('errorCode' => $errCode, 'errorStr' => $errStr, 'result' => $result);
		die(json_encode($rst));
	}

}
