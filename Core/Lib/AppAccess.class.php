<?php

/**
 * Wine.Cn
 *
 * @author    mengfk<dreamans@163.com>
 * @copyright Copyright (C) 2012 wine.com All rights reserved.
 */

//----------------------------------------------------------------

/**
 * 项目之间数据交互需继承此类,并在global.php中定义APP_KEY
 *
 * @category   Wine
 * @package    Core
 * @author     mengfk<dreamans@163.com>
 */
/**
 * 公共方法，项目之间数据交互需继承此类
 */
class AppAccess extends Action {

	public function _initialize() {
		$this->_checkSign();
	}

	/**
	 * 进行权限验证
	 */
    protected function _checkSign() {
        //取得key
		$key = $GLOBALS['APP_KEY'][APP_NAME];
		$s = '';
		$sign = isset($_POST['sign']) ? $_POST['sign']: '';
		if(!$sign) {
			die(json_encode(array('errorCode' => 200002, 'errorStr' => 'Sign is empty')));
		}
		unset($_POST['sign']);
		if(is_array($_POST)) {
			foreach($_POST as $key => $val) {
				$s .= $key.$val;
			}
		}
		$nsign = sha1($s.$key);
		if($sign != $nsign) {
			die(json_encode(array('errorCode' => 200003, 'errorStr' => 'Sign is wrong')));
		}
    }

	/**
	 * 项目之间进行数据库交互的接口方法
	 * php代码方式调用数据
	 */
	public function block() {
		$php = $_POST['php'];
        $tpl = $_POST['tpl'];
        empty($tpl) && die(json_encode(array('errorCode' => 200004, 'errorStr' => 'block api $_POST["tpl"] is empty')));
		@eval($php);
		$content = $this->fetch('', $tpl);
		die(json_encode($content));
	}

	/**
	 * 静态方式调用数据
	 */
	public function sblock() {
		$tpl = $_POST['tpl'];
		$content = $this->fetch('', $tpl);
		die(json_encode($content));
	}
}
