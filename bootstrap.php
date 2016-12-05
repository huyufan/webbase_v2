<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function autoload_webbase($className) {
    @include_once $className . ".class.php";
}

if (!defined('TIMESTART')) {
    define("TIMESTART", microtime());
}

if (!defined('SEP')) {
    define('SEP', DIRECTORY_SEPARATOR);
}
if (!defined("ROOT")) {
    $droot = isset($_SERVER["DOCUMENT_ROOT"]) && !empty($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : dirname(__FILE__) . SEP . "..";
    define("ROOT", $droot);
}
if (!defined("REQUESTURI")) {
    $reqUri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
    define("REQUESTURI", $reqUri);
}
if (!defined("CORE")) {
    $webbase_dir = dirname(__FILE__) . SEP . "core" . SEP;
    define("CORE", $webbase_dir);
}
if (!defined("WEBBASE")) {
    $webbase_dir = dirname(__FILE__) . SEP;
    define("WEBBASE", $webbase_dir);
}

$beforeInclude = get_include_path();
$includePath = CORE . PATH_SEPARATOR;
set_include_path($beforeInclude . PATH_SEPARATOR . $includePath);
spl_autoload_register("autoload_webbase");