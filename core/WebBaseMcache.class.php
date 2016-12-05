<?php

class WebBaseMcache {

    public $mem = null;

    public function __construct() {
        if (class_exists("Memcache")) {
            $this->mem = new Memcache();
            //$memcached = webbase_config::xpath('memcached');
            //var_dump($memcached);
            //die();
            //$this->mem->addserver($host, $port);
            $issuc = @$this->mem->connect("127.0.0.1", 11211);
            if (!$issuc) {
                $this->mem = null;
            }
        }
    }

    public function get($key) {
        if (!$this->mem) {
            return false;
        }
        $key = base64_encode($key);
        $rtn = $this->mem->get($key);
        return $rtn;
    }

    /**
     * 
     * @param string $key 
     * @param mixed $value
     * @param time $expire 如果 isMin 为true 则以分钟为单位 如果 false 则 为秒
     * @param type $isMin true 以分钟为单位 false 以秒为单位
     * @return bool
     */
    public function set($key, $value, $expire = 360, $isMin = true) {//默认缓存6个小时
        if (!$this->mem) {
            return false;
        }
        $key = base64_encode($key);
        $min = $expire;
        if ($isMin) {
            $min = 60 * $expire; //
        }
        $cached = $this->mem->get($key);
        if ($cached) {
            return $this->mem->set($key, $value, MEMCACHE_COMPRESSED, $min);
        } else {
            return $this->mem->add($key, $value, MEMCACHE_COMPRESSED, $min);
        }
    }

    /*     * *****
     * 增加缓存
     * @param key string 写入值的名字
     * @param value string 写入值的值
     * @param expire int 分钟
     * ****** */

    public function add($key, $value, $expire = 360) {
        if (!$this->mem) {
            return false;
        }
        $key = base64_encode($key);
        $min = 60 * $expire; //
        return $this->mem->add($key, $value, MEMCACHE_COMPRESSED, $min);
    }

    public function delete($key) {
        if (!$this->mem) {
            return false;
        }
        $key = base64_encode($key);
        return $this->mem->delete($key);
    }

    public function flush() {
        if (!$this->mem) {
            return false;
        }
        return $this->mem->flush();
    }

}
