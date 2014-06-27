<?php
// redis config
define( 'REDIS_HOST', '192.168.1.251');
define( 'REDIS_PORT', '6379');
define( 'REDIS_QUEUE_PREFIX', '#_1234_#');

$redis_cfg = array(
        'host' => REDIS_HOST,
        'port' => REDIS_PORT,
        'key'  => REDIS_QUEUE_PREFIX,
        );

$llen = in_queue($redis_cfg, '123413123211');
if ( !$llen ) echo 'in queue error' ;
else echo 'success';
echo $llen;

function pop_queue($cfg) {
    return dredis($cfg)->rpop($cfg['key']);
}

function in_queue($cfg, $message) {
    return dredis($cfg)->lpush($cfg['key'], $message);   
}

function dredis($cfg) {
    static $redis = NULL;
    if (!$redis) {
        try {
            $redis = new redis();
            $redis->connect($cfg['host'], $cfg['port']);
            if(isset($cfg['auth'])) {
                $redis->auth($cfg['auth']);
            }
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    return $redis;
}

