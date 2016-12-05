<?php

class WebBaseCookie {

    static function setCookie($prefix, $value, $expire = 0, $host = null) {

        $maxCookie = 4000;
        $lenOfVal = strlen($value);
        self::delCookie($prefix);
        $frequency = round($lenOfVal / $maxCookie, 0) + 1;

        if (strlen($value) > $maxCookie) {

            for ($i = 0; $i < $frequency; $i++) {
                $str = substr($value, $maxCookie * $i, $maxCookie);
                setcookie($prefix . "_" . $i, "");
                setcookie($prefix . "_" . $i, $str, $expire, null, $host);
                $_COOKIE[$prefix . "_" . $i] = $str;
            }
        } else {
            setcookie($prefix, "");
            setcookie($prefix, $value, $expire, null, $host);
            $_COOKIE[$prefix] = $value;
        }
    }

    static function getCookie($prefix) {

        // require_once $_SERVER["DOCUMENT_ROOT"] . '/include/webbase/WbObject.inc.php';

        $cookiear = array();
        $moreThanOne = false;
        $rtn = "";

        foreach ($_COOKIE as $key => $cookie) {
            $isPrefix = WebBaseObject::StartWith($prefix, $key);
            $pos = strpos($key, $prefix . "_");
            if ($pos !== false) {
                $moreThanOne = true;
                $idx = substr($key, strlen($prefix . "_"));
                $cookiear[$idx] = $cookie;
            }
        }
        for ($i = 0; $i < count($cookiear); $i++) {
            $rtn.=$cookiear[$i];
        }
        if (!$moreThanOne) {
            $rtn = isset($_COOKIE[$prefix]) ? $_COOKIE[$prefix] : null;
        }

        return $rtn;
    }

    static function delCookie($prefix, $domain=null) {
        //  require_once $_SERVER["DOCUMENT_ROOT"] . '/include/webbase/WbObject.inc.php';
        foreach ($_COOKIE as $key => $cookie) {
            $isPrefix = WebBaseUtility::startWith($prefix, $key);
            if ($isPrefix) {
                setcookie($key, "", time() - 3600, null, $domain);
                $_COOKIE[$key] = null;
            }
        }
    }

}
