<?php

if(PHP_SAPI != 'cli') {
    die('Can not runing in non-cli mode');
}
if( !extension_loaded('pcntl') ){
    dl('pcntl.so');
}

require_once('user_config.php');
$db_cfg = array(
        'db_host' => DB_HOST, 
        'db_port' => DB_PORT,
        'db_user' => DB_USER, 
        'db_pass' => DB_PASS, 
        'db_name' => DB_NAME,
        'db_charset' => DB_CHARSET, 
        'db_persist' => DB_PERSIST, 
        'table_pre' => TABLE_PREFIX,
        );
$redis_cfg = array(
        'host' => REDIS_HOST,
        'port' => REDIS_PORT,
        'key'  => REDIS_QUEUE_PREFIX,
        );

require_once('db.vdr.php');
require_once('BayouSmsSender.php');
$time_c = 0;
$queueO = new rQueue($redis_cfg);

while(true) {

    $pid = pcntl_fork();
    if($pid == -1) {
        exit("pid fork error");
    }
    if($pid) {
        static $execute = 0;
        $execute++;
        if($execute >= PROCESS_NUM) {
            pcntl_wait($status);
            $execute--;
        }
    } else {
        while(true) {
            $message = $queueO->pop_queue($redis_cfg['key']);
            $time_c ++;
            if ( $message ) {
                echo "log >> pop queue -> $message \n";
                echo "log >> deal with sms queue.\n";
                $data = json_decode($message, true);                  
                if ( !is_array($data) ) {sms_log('Data error : redis sms data decrypt error.'); exit(0);}
                if ( !isset($data['msg_id']) ) {sms_log('Data error : msg_id not defined.'); exit(0);}
                if ( !isset($data['client_id']) ) {sms_log('Data error : client_id not defined.'); exit(0);}
                if ( !isset($data['content']) ) {sms_log('Data error : content not defined.'); exit(0);}
                if ( !isset($data['tel_list']) ) {sms_log('Data error : tel_list not defined.'); exit(0);}
                
                // db op initalize.
                db::vendor_init($db_cfg);
                
                // get sms platfroms account info.
                $rst = db::fetch_one("SELECT 
                                sp.`id` as `platform_id`,
                                sp.`account`, 
                                sp.`password` 
                                FROM `sms_platform` sp
                                JOIN `sms_clients` sc ON sc.`platform_id`=sp.`id`
                                WHERE sc.`id`={$data['client_id']}");
                
                
                // update status to 正在发送
                db::save(array('status'=>1, 'send_time'=>time()), $data['msg_id'], 'msgs');

                // call Bayou API 
                $sender=new BayouSmsSender();
                $count_tel_arr = count($data['tel_list']);
                $str_tel_list = implode(',', $data['tel_list']);
                $rec = $sender->sendsms($rst['account'], md5($rst['password']), $str_tel_list, $data['content']);
                //$rec = array('status'=>1);
                
                sms_log("Sms sending : msg_id {$data['msg_id']} client_id {$data['client_id']} ");
                echo "log >> sms send is in progress.\n";
                // 发送成功
                if ( isset($rec['status']) && $rec['status'] == 1) {
                    db::save(array('status'=>2, 'receive_time'=>time()), $data['msg_id'], 'msgs');
                    // 更新成功发送
                    db::query("UPDATE `".DB_NAME."`.`".TABLE_PREFIX."platform` 
                                SET `sms_success`=`sms_success`+{$count_tel_arr}
                                WHERE `id`={$rst['platform_id']}");
                    /*            
                    db::query("UPDATE `".DB_NAME."`.`".TABLE_PREFIX."clients` 
                                SET `sms_success`=`sms_success`+{$count_tel_arr}
                                WHERE `id`={$data['client_id']}");
                    */
                    echo "log >> sms send succes.\n";
                    sms_log("Sms success : msg_id {$data['msg_id']} client_id {$data['client_id']} ");
                } else {
                    echo "log >> sms send failed.\n";
                    sms_log("Sms failed : msg_id {$data['msg_id']} client_id {$data['client_id']} ");
                    db::save(array('status'=>3, 'receive_time'=>time()), $data['msg_id'], 'msgs');
                }
            }

            /**
            * @brief  计数超过redis队列的激活时间，写入队列
            */
            if ( $time_c >= ACTIVE_TIME ) {
                $queueO->in_queue($redis_cfg['key'], 0);
                $time_c = 0;
                echo "log >> redis queue active time loop again.\n";
            }

            sleep(1);
        }
        exit(0);
    }
}

/**
* @brief  获得写入日志时间
*
* @return 
*/
function get_log_time() {
    $start_time = microtime();
    list($msec, $sec) = explode(' ',$start_time);
    list($zo, $msec) = explode('.',$msec);
    return date('Y-m-d H:i:s',$sec) . '.' . $msec;
}

/**
* @brief  写入日志
*
* @params $dir
* @params $str
*
* @return 
*/
function sms_log($msg) {
    $logdir = date('Ym');
    $sms_log_dir = LOGDIR . $logdir;
    if(!is_dir(LOGDIR.$logdir)) {
        mkdir($sms_log_dir);
    }
    $sms_log_file = $sms_log_dir . '/'. date('d');

    file_put_contents($sms_log_file, get_log_time() . "-- SMS_CENTER:$msg\n", FILE_APPEND);
}


class rQueue {
    
    public $redis = NULL;
    public function __construct($cfg) {

        try {
            $this->redis = new redis();
            $this->redis->pconnect($cfg['host'], $cfg['port']);
            if(isset($cfg['auth'])) {
                $this->redis->auth($cfg['auth']);
            }
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        return $this->redis;
    }

    public function pop_queue($key) {
        return $this->redis->rpop($key);
    }

    public function in_queue($key, $val) {
        return $this->redis->lpush($key, $val);
    }

}

