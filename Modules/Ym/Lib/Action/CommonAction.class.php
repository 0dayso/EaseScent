<?php 
    class CommonAction extends Action{
    	/**
    	 * Enter description here...
    	 * 验证邮箱地址
    	 * @param unknown_type $email
    	 * @return unknown
    	 */
        public function is_email($email) { 
            $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}$/i";
            if (strpos($email, '@') !== false && strpos($email, '.') !== false)
            {
                    return preg_match($chars, $email) ? true : false;
            }else {
                return false;
            }
        }
        /**
         * Enter description here...
         * 验证手机号
         * @param unknown_type $moblie
         * @return unknown
         */
        public function is_moblie($moblie) {
                return preg_match('/^1[35689]{1}[0-9]{9}$/i',$moblie)? true : false;
        }
        /**
         * Enter description here...
         * 验证电话号码
         * @param unknown_type $str
         * @return unknown
         */
        function CheckTelephone($C_telephone)     
		{     
			 if(strlen($C_telephone)>6&&ereg("^[+]?[0-9]+([xX-][0-9]+)*$", $C_telephone)){
				return true;
			}
			return false;
		}
		public function is_number($str){
			return preg_match('/^\d*$/i',$str)?true:false;
		}
		/**
		 * Enter description here...
		 * 检测昵称 只能是 数字 字母 — 中文
		 */
		public function checknick($str){
			return preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u',$str)?true:false;
		}
		/**
		 * Enter description here...
		 * 获取随机字符
		 * @param unknown_type $length
		 * @param unknown_type $mode
		 * @return unknown
		 */
		function getRandStr($length = 32, $mode = 0)
   		{
	       switch ($mode) {
	           case '1':
	               $str = '1234567890';
	           break;
	           case '2':
	               $str = 'abcdefghijklmnopqrstuvwxyz';
	           break;
	           case '3':
	               $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	           break;
	           case '4':
	               $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	           break;
	           case '5':
	               $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	           break;
	           case '6':
	               $str = 'abcdefghijklmnopqrstuvwxyz1234567890';
	           break;
	           default:
	               $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	           break;
	       }

		$result = '';
		$max = strlen($str) - 1;
		mt_srand((double)microtime() * 1000000);
		for($i = 0;$i < $length;$i ++){
		   $result .= $str[mt_rand(0, $max)];
		}
			return $result;
   		}
   		/**
   		 * Enter description here...
   		 * 发送邮件
   		 * @param unknown_type $address
   		 * @param unknown_type $title
   		 * @param unknown_type $message
   		 * @return unknown
   		 */
		public function sendMail($address,$title,$message){
            $redis = $this->Rdb();   
            $data = json_encode(array(
                'to_email' => $address,
                'subject' => $title,
                'msg' => $message));
            return $redis->kv->rPush('#_account_send_mail_#', $data);

            /*
			import('@.ORG.Util.phpmailer');
			$mail=new PHPMailer();          // 设置PHPMailer使用SMTP服务器发送Email
	        $mail->IsSMTP(); // 设置邮件的字符编码，若不指定，则为'UTF-8'
	       // $mail->SMTPDebug = 1;               
	        $mail->CharSet='UTF-8';         // 添加收件人地址，可以多次使用来添加多个收件人
            $mail->Timeout = 2;
	        $mail->AddAddress($address);    // 设置邮件正文
	        $mail->Body=$message;           // 设置邮件头的From字段。
	        $mail->From=C('MAIL_ADDRESS');  // 设置发件人名字
	        $mail->FromName='逸香通行证';  // 设置邮件标题
	        $mail->Subject=$title;          // 设置SMTP服务器。
	        $mail->Host=C('MAIL_SMTP');     // 设置为"需要验证" ThinkPHP 的C方法读取配置文件
	        $mail->SMTPAuth=true;           // 设置用户名和密码。
	        $mail->Username=C('MAIL_LOGINNAME');
	        $mail->Password=C('MAIL_PASSWORD'); // 发送邮件。
	        $mail->MsgHTML($message);
	        return $mail->Send();
            */
		}
		/**
		 * Enter description here...
		 * 发送短信
		 */
        /*
		public function sendsms($tel, $msg){
			$time = time();
		    $key = md5('iuRliUCCN78' . date('Y-m-d-H'));
		    $iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
		    $IV = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		    $str = json_encode(array('id'=>6,'time'=>$time,'message'=>$msg,'tellist'=>array($tel)));
		    $data = base64_encode(mcrypt_encrypt(MCRYPT_CAST_256, $key, $str, MCRYPT_MODE_CFB, $IV));
		    $IV = base64_encode($IV);
		    $data = urlencode($data);
		    $IV = urlencode($IV);
		    $url = "http://sms.eswine.com/send.php?i=6&t=$time&d=$data&v=$IV";
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		    $re = curl_exec($ch);
		    curl_close($ch);
		    $arr = json_decode($re, true);
		    if(empty($arr['success'])) {
		        return false;
		    } else {
		        return true;
		    }
		}*/
        function sendsms($tellist,$msg) {
            $arr_sms = array('message'=>$msg,'tellist'=>array($tellist));
            $data = $this->sms_encrypt($arr_sms, "d8f552fc66b5447a0cf4785085736e16");
            $token = $this->getToken();
            $posts = array(
                    'appid' => 4,
                    'accessToken' => $token["result"],
                    'ci' => 8,
                    'd'=>$data,
                );
            $url = "http://api.wine.cn/?Sms/Api/send";
            return $this->CurlPost($url, $posts);
        }
        function sms_encrypt($arr, $key) {
            $string = json_encode($arr);
            srand((double) microtime() * 1000000); //for sake of MCRYPT_RAND
            $key = md5($key); //to improve variance
            /* Open module, and create IV */
            $td = mcrypt_module_open('des', '','cfb', '');
             $key = substr($key, 0, mcrypt_enc_get_key_size($td));
            $iv_size = mcrypt_enc_get_iv_size($td);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            /* Initialize encryption handle */
            if (mcrypt_generic_init($td, $key, $iv) != -1) {
            /* Encrypt data */
            $c_t = mcrypt_generic($td, $string);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            $c_t = $iv.$c_t;
            return $c_t;
            } //end if
        }
        function CurlPost($url, $data=array(), $timeout = 1, $header = "") {
            $ssl = substr($url, 0, 8) == 'https://' ? true : false;
            $post_string = http_build_query($data);  
            $ch = curl_init();
            if ($ssl) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
            }   
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
            curl_setopt($ch, CURLOPT_USERPWD, "thisuser:Gst43sB");
            //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
        function getToken() {
            $token_url = "http://api.wine.cn/?Admin/Api/accessToken";
            $post = array('appid' => '4', 'appkey' => '3b28c97bfeef9c07dfb053cb4d7972fe');
            $jGet = CurlPost($token_url, $post);
            return json_decode($jGet,true);
        } 

        function getLength($str){
            return strlen($str);
        }
        public  function Rdb(){
    	    $file = dirname(__FILE__);
    	    require_once($file.'/RedisDB.php');
    	    return RedisDB::getInstance();
        }
    }
?>
