<?php
class RedisDB {
    public $kv;
    public $expire = 3600;
    private static $instance;
    private function __construct() {
        if(!class_exists('Redis')) die('Need Memcache extension');
        $this->kv = new Redis();
        try {
            $re = $this->kv->connect(C("redis_config.host"),C("redis_config.port"));
            if($re == false) {
                die('Key/Value DB Connected failure');
            }
        $this->kv->setOption(Redis::OPT_SERIALIZER,Redis::SERIALIZER_NONE);
        } catch(Exception $e){
            echo $e;
        }
    }
    public function set($key,$value, $expire =null) {
        if($expire === null) {
            return $this->kv->set($key,$value);
        } else {
            return $this->kv->setex($key, $expire,$value);
        }
    }
    public function exists($key) {
        return $this->kv->exists($key);
    }
    public function get($key) {
        return $this->kv->get($key);
    }
    public function del($key) {
        $re = $this->kv->delete($key);
        return $re;
    }
    public function getsize() {
    }
    public function rename($src, $dest) {
        return $this->kv->rename($src,$dest);
    }
    public function get_all_key() {
        return $this->kv->keys('*');
    }
    public function get_db_size() {
        return $this->kv->dbSize();
    }
    public function lastSave() {
        return $this->kv->lastSave();
    }
    public static function getInstance(){
    	if(self::$instance == null)
        {
            self::$instance = new RedisDB();
        }
        return self::$instance;
    }
}
?>
