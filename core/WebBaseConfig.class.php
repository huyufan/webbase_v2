<?php

if (!defined("ROOT")) {
    define("ROOT", isset($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : "");
}
if (!defined('SEP')) {
    define('SEP', DIRECTORY_SEPARATOR);
}

class WebBaseConfig {

    static $xml = false;

    /**
     * 加载根目录下的web.xml
     * @return xml|null
     */
    static function load() {

        if (!self::$xml) {
            $path = ROOT . SEP . "webbase.xml";
            if (file_exists($path)) {
                self::$xml = simplexml_load_file($path, "SimpleXMLElement");
            }
        }
        return self::$xml;
    }

    /**
     * 读取smarty下的key配置
     * @param string $key 读取smarty 的key配置节
     * @return string|false
     */
    static function smarty($key) {
        self::load();
        $value=false;
        if (self::$xml) {
            $value = self::$xml->xpath("smarty/add[@key='$key']");
            $value = isset($value[0]) ? (object) $value[0] : false;
            if ($value) {
                $value = isset($value["value"]) ? (string) $value["value"] : false;
            }
        }
        return $value;
    }

    /**
     * 读取app下的key配置
     * @param string $key 读取app 的key配置节
     * @return string|false
     */
    static function app($key) {
        $value = false;
        try {
            self::load();
            if (self::$xml) {
                $value = self::$xml->xpath("app/add[@key='$key']");
                $value = isset($value[0]) ? (object) $value[0] : false;
                if ($value) {
                    $value = isset($value["value"]) ? (string) $value["value"] : false;
                }
            }
        } catch (Exception $e) {
            
        }
        return $value;
    }

    /**
     * 读取error下的key配置
     * @param string $key 读取error 的key配置节
     * @return string|false
     */
    static function error($key) {
        self::load();
        $value = false;
        if (self::$xml) {
            $value = self::$xml->xpath("error/add[@key='$key']");
            $value = isset($value[0]) ? (object) $value[0] : false;
            if ($value) {
                $value = isset($value["value"]) ? (string) $value["value"] : false;
            }
        }
        return $value;
    }

    /**
     * 自定义读取web.xml的路径配置
     * @param string $path 读取xml路径
     * @param string $key  配置节的前缀
     * @param string $value  配置节的值
     * @return string|false
     */
    static function xpath($path, $key = false, $value = false) {
        self::load();
        $value = false;
        if (self::$xml) {
            if ($key) {
                $value = self::$xml->xpath($path . "[@" . $key . "='$value']");
                $value = isset($value[0]) ? (object) $value[0] : false;
                if ($value) {
                    $value = $value = isset($value[$key]) ? (string) $value[$key] : false;
                }
            } else {
                $value = self::$xml->xpath($path);
            }
        }
        return $value;
    }

    static function auto() {
        self::load();
        $rtn = false;
        if (self::$xml) {
            $value = self::$xml->xpath("auto/add");
            $rtn = array();
            if ($value) {
                foreach ($value as $map) {
                    $attr = $map->attributes();
                    $rtn[] = (string) $attr["path"];
                }
            }
        }
        return $rtn;
    }

    /**
     * 自定义读取web.xml的路径配置
     * @param string $path 读取xml路径
     * @param string $key  配置节的前缀
     * @param string $value  配置节的值
     * @return arrray|false
     */
    static function router() {
        self::load();
        $path = "router";
        $value = self::$xml->xpath($path);
        $maps = isset($value[0]->map) ? $value[0]->map : false;
        $rtn = array();
        if (self::$xml) {
            if ($maps) {
                foreach ($maps as $map) {
                    $hash = array();
                    foreach ($map->attributes() as $key => $value) {
                        $hash[$key] = (string) $value;
                    }
                    $rtn[] = $hash;
                }
            }
        }
        return $rtn;
    }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
