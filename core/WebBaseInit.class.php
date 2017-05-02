<?php

/**
 * Enter description here ...
 *
 */
class WebBaseInit {

    function header_status($status) {
        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            header('Status: ' . $status, TRUE);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status);
        }
    }
    

    public function dispatch() {
        $baseDir = ROOT . '/' . WebBaseConfig::app('controllerDir');
        $rpath = isset($_GET['webbaseurl']) ? $_GET['webbaseurl'] : "";
        $rpath = substr($rpath, 0, 1) == '/' ? substr($rpath, 1) : $rpath;
        $arPath = explode("/", $rpath);
        $ct = count($arPath);
        $lastPath = array();
        $classPath = "";
        $className = "";
        $method = "default";
        foreach ($arPath as $key => $path) {
            if ($path != '') {
                $lastPath[] = $path;
            } else {
                if ($key == $ct - 1) {
                    $lastPath[] = "default";
                }
            }
        }
        if (count($lastPath) == 1) {
            $classPath = $lastPath[0];
            $className = ucfirst($classPath);
        } else {
            $method = $lastPath[count($lastPath) - 1];
            unset($lastPath[count($lastPath) - 1]);
            $className = ucfirst($lastPath[count($lastPath) - 1]);
            unset($lastPath[count($lastPath) - 1]);
            $classPath = join('/', $lastPath) . '/' . $className;
            $method[0] = strtolower($method[0]);
        }

        $isInclude = false;
        $classPath = $baseDir . '/' . $classPath;
        if (file_exists($classPath . '.class.php')) {

            include_once $classPath . '.class.php';
            $isInclude = true;
        } elseif (file_exists($classPath . '.php')) {
            include_once $classPath . '.php';
            $isInclude = true;
        }
        if (!$isInclude) {
            $this->header_status(404);
            echo "page not found";
            return;
        } else {
            $method = $method . "Action";
            $reflect = new ReflectionClass($className);
            $cls = $reflect->newInstance();
            if ($reflect->hasMethod('onInit')) {
                $cls->onInit();
            } else {
                $this->header_status(404);
                echo "page not fount";
                return;
            }
            if ($reflect->hasMethod($method)) {
                $cls->dir = $classPath;
                $exec = $reflect->getMethod($method);
                $cls->viewInfo = $exec->invoke($cls);
            } else {
                $this->header_status(404);
                echo "page not fount";
                return;
            }

            if ($reflect->hasMethod('pageLoad')) {
                $cls->pageLoad($classPath . '/' . $method);
            } else {
                $this->header_status(404);
                echo "page not fount";
                return;
            }
            
            if ($reflect->hasMethod('show')) {
                $cls->show();
            }
         
        }

        /*
          //url路径
          $rpath = substr($rpath, 0, 1) == '/' ? substr($rpath, 1) : $rpath;
          // echo $rpath;
          $ar_path = explode("/", $rpath);
          //方法体
          $method = "";
          //类名
          $class = "";
          //类路径
          $classPath = "";
          $intPath = sizeof($ar_path);
          $i = 1;

          foreach ($ar_path as $path) {
          if ($intPath > 2) {
          if ($i < $intPath - 1) {
          $classPath.='/' . $path;
          } elseif ($i == $intPath - 1) {
          $class = $path;
          } else {
          $method = $path;
          }
          } else {
          if ($i == 1) {
          $class = $path;
          } else {
          $method = $path;
          }
          }
          $i++;
          }

          if (!empty($class)) {

          $controllerDir = WebBaseConfig::app("controllerDir");
          $method = empty($method) ? 'default' : $method;
          $requirePath = ROOT . '/' . $controllerDir . $classPath . '/' . $class . '.php';

          if (file_exists($requirePath)) {

          require_once $requirePath;
          } else {
          $this->header_status(404);
          echo "page not fount";
          return;
          }


          $reflect = new ReflectionClass($class);
          $cls = $reflect->newInstance();
          if ($reflect->hasMethod('onInit')) {
          $cls->onInit();
          } else {
          $this->header_status(404);
          echo "page not fount";
          return;
          }



          if ($reflect->hasMethod($method . 'Action')) {
          $exec = $reflect->getMethod($method . 'Action');
          $cls->viewInfo = $exec->invoke($cls);
          } else {

          $this->header_status(404);
          echo "page not fount";
          return;
          }

          if ($reflect->hasMethod('pageLoad')) {

          $cls->pageLoad($classPath . '/' . $method);
          } else {
          $this->header_status(404);
          echo "page not fount";
          return;
          }

          if ($reflect->hasMethod('show')) {

          $cls->show();
          }
          } */
    }

    public function __construct() {
        $time = WebBaseConfig::app("time");
        date_default_timezone_set($time);
        $includeAr = WebBaseConfig::auto();
        $includePath = "";
        $count = count($includeAr);
        $i = 1;

        foreach ($includeAr as $path) {

            $sep = PATH_SEPARATOR;
            if ($i == $count) {
                $sep = "";
            }
            $dir = ROOT . SEP . $path;

            if (is_dir($dir)) {
                $includePath.=$dir . $sep;
            } else {
                echo $dir . "检测到目录配置错误";
            }
            $i++;
        }

        $beforeInclude = get_include_path();
        //str_replace(replace, $subject)
        set_include_path($beforeInclude . PATH_SEPARATOR . $includePath);
        //echo get_include_path();
        //die();
    }

}
