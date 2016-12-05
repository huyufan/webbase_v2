<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of webbase_cacheable
 *
 * @author jeff
 */
abstract class WebBaseCacheable {
    /* 缓存对象 */
    
    var $cache= null; //= new webbase_cacheable();

    function __construct() {
        $this->cache = new WebBaseMcache();
    }

    /**
     * 读取是否存在缓存 如果没有则返回false
     * @param $args array 请调用function_get_args
     * @param $method 方法的名字,一定不能错哦
     * */
    public function genKey($args, $method, $key=false) {
        $obj = new ReflectionObject($this);
        $className = $obj->getName();
        $strArgs = $key;
        if (!$key) {
            $strArgs = join('_', $args);
        }
        $genkey = base64_encode($className . $method . $strArgs);
        return $genkey;
    }

}

?>
