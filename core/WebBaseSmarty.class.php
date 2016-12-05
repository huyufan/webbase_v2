<?php

require_once(WEBBASE . 'Smarty' . SEP . 'Smarty.class.php'); //包含smarty类文件

class WebBaseSmarty extends Smarty {

    
    function WebBaseSmarty($is_config = false) {
        parent::__construct();
        $this->error_reporting = true;
        $this->registerResource('str', array(
            array(&$this, 'str_get_template'),
            array(&$this, 'str_get_timestamp'),
            array(&$this, 'str_get_secure'),
            array(&$this, 'str_get_trusted'))
        );
        if ($is_config) {
            $tpl = WebBaseConfig::smarty("tpl");
            $tpl = $tpl ? $tpl : "view";
            $left = WebBaseConfig::smarty("lt");
            $right = WebBaseConfig::smarty("rt");
            $isCache = WebBaseConfig::smarty("isCache");

            $isCache = $isCache ? $isCache == "1" : false;
            $cachTime = WebBaseConfig::smarty("cacheTime");
            $cachTime = $cachTime ? $cachTime : 0;
            $forceCompile = WebBaseConfig::smarty("forceCompile");
            $forceCompile = $forceCompile ? $forceCompile == "1" : false;
            $debugging = WebBaseConfig::smarty("debugging");
            $debugging = $debugging ? $debugging == "1" : false;
            $allowPhpTag = WebBaseConfig::smarty("allowPhpTag");
            $allowPhpTag = $allowPhpTag ? $allowPhpTag == "1" : false;
            $compiled = WebBaseConfig::smarty("compiled");
            $compiled = $compiled ? $compiled : "view/compiled";
            $cache = WebBaseConfig::smarty("cache");
            $pluginsDir = WebBaseConfig::smarty('pluginDir');
            $cache = $cache ? $cache : "view/cache";
            if ($left && $right) {
                $this->left_delimiter = $left;
                $this->right_delimiter = $right;
            }
            $this->setTemplateDir(ROOT . SEP . $tpl);
            $this->compile_dir = ROOT . SEP . $compiled;
            $this->cache_dir = ROOT . SEP . $cache;
            $this->cache_lifetime = $cachTime;
            $this->caching = $isCache;

            if (!empty($pluginsDir)) {
                $this->addPluginsDir(ROOT . SEP . $pluginsDir);
            }
            $this->addPluginsDir(CORE . "SmartyPlugin" . SEP);

            $this->force_compile = $forceCompile;
            $this->debugging = $debugging;
            $this->allow_php_tag = $allowPhpTag;
        }
    }

    function fetchStr($tpl) {
        //$tplFileTpl = $tplDir . SEP . $ptpl;
        $tpl = $this->template_dir[0] . "/" . $tpl;
        if ($tpl) {
            return $this->fetch($tpl);
        } else {
            return "模板不存在";
        }
    }

    function str_get_template($tpl_name, &$tpl_source, &$smarty_obj) {
        $tpl_source = $tpl_name;
        return true;
    }

    function str_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj) {
        $tpl_timestamp = time();
        return true;
    }

    function str_get_secure($tpl_name, &$smarty_obj) {
        return true;
    }

    function str_get_trusted($tpl_name, &$smarty_obj) {
        return true;
    }

}