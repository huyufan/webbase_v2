<?php

class WebBaseRedis {

    public $redis;

    public function __construct($server = array(array('ip' => '127.0.0.1', "port" => 6379))) {
        $this->redis = new Redis();
        foreach ($server as $srv) {
            $this->redis->connect($srv['ip'], $srv['port']);
        }
    }

    public function push($key, $value, $dir = 'r') {
        if ($dir == 'r') {
            return $this->redis->rPush($key, $value);
        } else {
            return $this->redis->lPush($key, $value);
        }
    }

    public function sAdd($key, $value) {
        return $this->redis->sAdd($key, $value);
    }

    public function sRem($key, $value) {
        return $this->redis->sRem($key, $value);
    }

    public function size($key) {
        return $this->redis->lSize($key);
    }

    public function popUnique($key, $dir = 'r', &$remKey = "") {
        $value = $this->pop($key, $dir);
        if (!empty($value)) {
            $remKey = $key . '_add';
            $this->sRem($key . '_add', $value);
        }
        return $value;
    }

    public function pushUnique($key, $value, $uqValue = '', $dir = 'r') {
        $rtn=false;
        if (empty($uqValue)) {
            $uqValue = $value;
        }
        $added = $this->sAdd($key . '_add', $uqValue);
        if ($added) {
            $rtn = $this->push($key, $value, $dir);
        }
        return $rtn;
    }

    public function pushx($key, $value, $dir = 'r') {
        if (!$this->redis->exists($key)) {
            return $this->push($key, $value, $dir);
        } else {
            if ($dir == 'r') {

                return $i = $this->redis->rPushx($key, $value);
            } else {
                return $this->redis->lPushx($key, $value);
            }
        }
    }

    public function pop($key, $dir = 'r') {
        if ($dir == 'r') {
            return $this->redis->rPop($key);
        } else {
            return $this->redis->lPop($key);
        }
    }

    public function delete($key) {
        $this->redis->delete($key);
    }

    public function getKeys($key = false) {
        $keys = $this->redis->getKeys($key);
        return $keys;
    }

    public function getSorted($key) {
        $keys = $this->redis->sort($key);
        return $keys;
    }

}