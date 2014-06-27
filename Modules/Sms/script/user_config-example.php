<?php
/**
* @file user_config.php
* @brief 
* @author tiger <ji.xiaod@gmail.com>
* @date 2013-03-07
*/

define('LOGDIR', '/opt/var/logs/sms/'); // 日志文件目录
define('PROCESS_NUM', 1); // 子进程个数

// db config
define( 'DB_HOST', '192.168.1.251');
define( 'DB_PORT', '3306');
define( 'DB_USER', 'testmysql');
define( 'DB_PASS', '123456789');
define( 'DB_NAME', 'eswine'); 
define( 'DB_CHARSET', 'utf-8');
define( 'DB_PERSIST', false );
define( 'TABLE_PREFIX', 'sms_');


// redis config
define( 'REDIS_HOST', '192.168.1.251');
define( 'REDIS_PORT', '6379');
define( 'REDIS_QUEUE_PREFIX', '#sms_platform_sending_queue_#');
