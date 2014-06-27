<?php
/**
* @file ApiAction.class.php
* 
* @brief  短信平台接口
* Copyright(C) 2010-2015 Easescent.com, Inc. or its affiliates. All Rights Reserved.
* 
* @version $Id$
* @author Tiger, ji.toobe@gmail.com
* @date 2013-03-07
*/

define('MAX_SEND_PER_TIME', 20); // 每次同时最大发送短信数量
define('SYSTEM_TIME', time()); //当前系统时间
define('SMS_REDES_QUEUE_PREFIX', '#sms_platform_sending_queue_#');


/**
* ERROR CODE DEFINED
*/
define('UNKNOW_ERROR'       , 100);
define('POST_DATA_EMPTY'    , 101);
define('POST_CI_EMPTY'      , 102);
define('CLIENT_NOT_EXIST'   , 103);
define('DATA_DECRYPT_ERROR' , 104);
define('MESSAGE_CANNOT_EMPTY', 105);
define('TELLIST_CANNOT_EMPTY', 106);
define('TELNUBMER_ALL_INVALID', 107);
define('REDIS_CONFIG_ERROR', 108);
define('SMS_IN_QUEUE_ERROR', 109);
/**
* @class ApiAction
* 
* @brief  短信平台接口类
* 
*/

class ApiAction extends Api
{
    public function send() {
        $d = isset($_POST['d']) ? $_POST['d'] : ''; // encrypt data.
        $ci = isset($_POST['ci']) ? $_POST['ci'] : ''; //client_id.
        $suffix = isset($_POST['suffix']) ? $_POST['suffix'] : true; // if use message suffix.
        
        // check post param d.
        if ( empty($d) ) die(json_encode(RST('', POST_DATA_EMPTY, 'POST_DATA_EMPTY')));
        if ( empty($ci)) die(json_encode(RST('', POST_CI_EMPTY, 'POST_CI_EMPTY')));
       
        $client = D('Client')->getClientById($ci);
        if ( empty($client) && !is_array($client)) die(json_encode(RST('', CLIENT_NOT_EXIST, 'CLIENT_NOT_EXIST')));
        
        // decrypt data {message, tellist}
        $data = $this->sms_decrypt($d, $client['skey']);
        if ( !is_array($data) ) die(json_encode(RST('', DATA_DECRYPT_ERROR, 'DATA_DECRYPT_ERROR')));
        if ( empty($data['message']) ) die(json_encode(RST('', MESSAGE_CANNOT_EMPTY, 'MESSAGE_CANNOT_EMPTY')));
        if ( empty($data['tellist']) ) die(json_encode(RST('', TELLIST_CANNOT_EMPTY, 'TELLIST_CANNOT_EMPTY')));
        if ($suffix) {
            $content = str_replace(array('"',"'",'\\'), '',$data['message'] . $client['text_suffix']);
        } else {
            $content = str_replace(array('"',"'",'\\'), '',$data['message']);
        }

        /**
        * @brief  验证手机号码
        */
        $tel_arr = $tel_invalid = array();
        foreach($data['tellist'] as $tel) {
            $number = trim($tel);
            if(preg_match('/^(13|15|18|14)\d{9}$/i', $number)) {
                $tel_arr[] = $number;
            } else {
                $tel_invalid[] = $number;
            }
        }

        $tel_arr_count = count($tel_arr);
        
        if ( $tel_arr_count > 0 ) {
            
            // 更新短信平台和客户端sms_total
            D('Client')->where("`id` = {$ci}")->setInc('sms_total', $tel_arr_count);
            D('Platform')->where("`id` = {$client['platform_id']}")->setInc('sms_total', $tel_arr_count);

            
            if($tel_arr_count > MAX_SEND_PER_TIME) {
                $chunk_arr = array_chunk($tel_arr, MAX_SEND_PER_TIME);
                foreach($chunk_arr as $tel_sub_arr) {
                    $tellist = json_encode($tel_sub_arr);
                    $id = D('Msgs')->addMsg(array(
                                'content' => $content,
                                'tel_list' => $tellist,
                                'client_id' => $ci,
                                'status' => 0,
                                'inqueue_time' => SYSTEM_TIME
                                ));
                    // in sms queue 
                    if ( !isset($_POST['debug']) ) {
                        $sms_encode_str = json_encode(array(
                            'msg_id' => $id,
                            'client_id'=>$ci,
                            'tel_list' => $tel_sub_arr,
                            'content' => $content
                        ));      
                        $this->sms_in_queue($sms_encode_str);
                    }
                } //end for loop
            } else {
                $tellist = json_encode($tel_arr);
                $id = D('Msgs')->addMsg(array(
                            'content' => $content,
                            'tel_list' => $tellist,
                            'client_id' => $ci,
                            'status' => 0,
                            'inqueue_time' => SYSTEM_TIME
                            ));
                // in sms queue 
                if ( !isset($_POST['debug']) ) {
                    $sms_encode_str = json_encode(array(
                                'msg_id' => $id,
                                'client_id'=>$ci,
                                'tel_list' => $tel_arr,
                                'content' => $content
                                ));      
                    $this->sms_in_queue($sms_encode_str);
                }
            }
            die(json_encode(RST(array('success'=>$tel_arr,'failed'=>$tel_invalid))));
        } else {
            die(json_encode(RST('', TELNUBMER_ALL_INVALID, 'TELNUBMER_ALL_INVALID')));
        }

    }

    private function sms_in_queue($message)
    {
        // redis connection.
        $cfg = C('REDIS_CONFIG');
        if(empty($cfg)) {
            die(json_encode(RST('', REDIS_CONFIG_ERROR, 'REDIS_CONFIG_ERROR')));
        }
        $redis = new Redis();
        $redis->connect($cfg['host'], $cfg['port']);

        $llen = $redis->lpush(SMS_REDES_QUEUE_PREFIX, $message);  

        if ( !$llen ) {
            $llen = $redis->lpush(SMS_REDES_QUEUE_PREFIX, $message);  

            if( !$llen )
                die(json_encode(RST('', SMS_IN_QUEUE_ERROR, 'SMS_IN_QUEUE_ERROR')));
        } 

    }

    private function sms_decrypt($string,$key) 
    {
        $key = md5($key); //to improve variance
        /* Open module, and create IV */
        $td = mcrypt_module_open('des', '','cfb', '');
        $key = substr($key, 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = substr($string,0,$iv_size);
        $string = substr($string,$iv_size);
        /* Initialize encryption handle */
        if (mcrypt_generic_init($td, $key, $iv) != -1) {

            /* Encrypt data */
            $c_t = mdecrypt_generic($td, $string);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
            $c_t = json_decode($c_t, true);
            return $c_t;
        } //end if
    }

    private function sms_encrypt($arr, $key) 
    {
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

}

function RST($rst, $errno=0, $err='', $level=0, $log=''){                                                                                                      
    return array('rst'=>$rst, 'errno'=>$errno*1, 'error'=>$err, 'level'=>$level, 'log'=>$log);
}


