<?php

Class WebBaseUtility {

    public static function GetPageCount($rcdCt, $psize) {
        $iPageCount = 1;
        if ($rcdCt % $psize == 0) {
            $iPageCount = $rcdCt / $psize;
        } else {
            $iPageCount = ceil($rcdCt / $psize);
        }
        return $iPageCount;
    }

    /**
     * convert simplexml object to array sets
     * $array_tags 表示需要转为数组的 xml 标签。例：array('item', '')
     * 出错返回False
     *
     * @param object $simplexml_obj
     * @param array $array_tags
     * @param int $strip_white 是否清除左右空格
     * @return mixed
     */
    static function simplexmlToArray($simplexml_obj, $array_tags = array(), $strip_white = 1) {
        if ($simplexml_obj) {
            if (count($simplexml_obj) == 0)
                return $strip_white ? trim((string) $simplexml_obj) : (string) $simplexml_obj;

            $attr = array();
            foreach ($simplexml_obj as $k => $val) {
                if (!empty($array_tags) && in_array($k, $array_tags)) {
                    $attr[] = self::simplexmlToArray($val, $array_tags, $strip_white);
                } else {
                    $attr[$k] =self::simplexmlToArray($val, $array_tags, $strip_white);
                }
            }
            return $attr;
        }

        return "";
    }

    static function filterStr($strIn, $maxlength = 0) {
        $rtn = "";
        $len = strlen($strIn);
        if ($len > 0) {
            $rtn = self::filterdangerInputStr($strIn);

            if ($maxlength > 0 && $len > $maxlength) {
                $rtn = substr($strIn, 0, $maxlength);
            }
        }
        return $rtn;
    }

    static function is_url($str) {
        return preg_match("/^(http:\/\/)?[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $str);
    }

    static function safeEncoding($string, $outEncoding = 'UTF-8') {
        $encoding = "UTF-8";
        for ($i = 0; $i < strlen($string); $i++) {
            if (ord($string{$i}) < 128)
                continue;

            if ((ord($string{$i}) & 224) == 224) {
                //第一个字节判断通过
                $char = $string{++$i};
                if ((ord($char) & 128) == 128) {
                    //第二个字节判断通过
                    $char = $string{++$i};
                    if ((ord($char) & 128) == 128) {
                        $encoding = "UTF-8";
                        break;
                    }
                }
            }
            if ((ord($string{$i}) & 192) == 192) {
                //第一个字节判断通过
                $char = $string{++$i};
                if ((ord($char) & 128) == 128) {
                    // 第二个字节判断通过
                    $encoding = "GB2312";
                    break;
                }
            }
        }

        if (strtoupper($encoding) == strtoupper($outEncoding))
            return $string;
        else
            return iconv($encoding, $outEncoding, $string);
    }

    static function filterTextAreaStr($strIn, $maxlength = 0) {
        $rtn = "";
        $len = strlen($strIn);
        if ($len > 0) {
            $rtn = WebbaseUtility::filterdangerTextAreaStr($strIn);
            if ($maxlength > 0 && $len > $maxlength) {
                $rtn = substr($strIn, 0, $maxlength);
            }
        }
        return $rtn;
    }

    static function RemoveHtml($strIn) {
        if (empty($strIn)) {
            return $strIn;
        }
        $search = array("'<script[^>]*?>.*?</script>'si", // 去掉 javascript
            "'<style[^>]*?>.*?</style>'si", // 去掉 css
            "'<[/!]*?[^<>]*?>'si", // 去掉 HTML 标记
            "'<!--[/!]*?[^<>]*?>'si", // 去掉 注释标记
            //"'([rn])[s]+'", // 去掉空白字符
            "'&(quot|#34);'i", // 替换 HTML 实体
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(d+);'e"
        ); // 作为 PHP 代码运行

        $replace = array("",
            "",
            "",
            "",
            // "\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\1)"
        );
        //$document为需要处理字符串，如果来源为文件可以$document = file_get_contents($filename);
        $out = preg_replace($search, $replace, $strIn);

        return $out;
    }

    static function GetIntStr($str_In, $defaultVal = 0) {
        $rtn = $defaultVal;
        try {
            $temp = preg_match("/^-?\d+$/", $str_In);
            if ($temp) {
                $rtn = $str_In;
            }
        } catch (Exception $e) {
            //echo $e->getMessage();
        }
        return $rtn;
    }

    static function GetFloatStr($str_In, $defaultVal = 0) {
        $rtn = $defaultVal;

        try {

            $rtn = preg_match("/^\d*$/", $str_In) || preg_match("/^\d*\.\d*$/", $str_In) ? $str_In : $defaultVal;
        } catch (Exception $e) {
            
        }

        return $rtn;
    }

    static function GetBooleanStr($strIn, $defVal = 0) {
        $rtn = $defVal;
        if ($strIn == 1 || $strIn == "1" || strtolower($strIn) == "true" || strtolower($strIn) == "on") {
            $rtn = 1;
        } else {
            $rtn = 0;
        }
        return $rtn;
    }

    static function filterCommon($strIn) {
        $strIn = str_replace("\n", "\\n", $strIn);
        $strIn = str_replace("\t", "\\t", $strIn);
        $strIn = str_replace("\r", "\\r", $strIn);
        $strIn = str_replace("\b", "\\b", $strIn);
        $strIn = str_replace("\f", "\\f", $strIn);
        $strIn = str_replace('"', '\\"', $strIn);
        $strIn = str_replace("'", "\\'", $strIn);
        //$strIn=str_replace("+","\\+",$strIn);
        //$strIn=str_replace("-","\\-",$strIn);
        //$strIn=str_replace("-","\\-",$strIn);
        //$strIn=str_replace(";","\\;",$strIn);
        //$strIn=str_replace("&","\\&",$strIn);
        //$strIn=str_replace("#","\\#",$strIn);
        //防止通过代码以外的方式对数据库进行授权,删除等操作
        $strIn = preg_replace("/\s?;\s?|\s?drop\s|\s?grant\s|^'|\s?--|\s?union\s|\s?delete\s|\s?truncate\s|\s?sysobjects\s?|\s?xp_.*?|\s?syslogins\s?|\s?sysremote\s?|\s?sysusers\s?|\s?sysxlogins\s?|\s?sysdatabases\s?|\s?aspnet_.*?|\s?exec\s?|/", "", $strIn);
        $strIn = preg_replace("/[\\s]{4,}/", "&nbsp;&nbsp;&nbsp;&nbsp;", $strIn);
        return $strIn;
    }

    static function filterdangerInputStr($strIn) {


        $strIn = str_replace("\n", "", $strIn);
        $strIn = str_replace("\t", "", $strIn);
        $strIn = str_replace("\r", "", $strIn);
        $strIn = str_replace("\b", "", $strIn);
        $strIn = str_replace("\f", "", $strIn);

        $strIn = self::RemoveHtml($strIn);
        $strIn = self::filterCommon($strIn);
        return $strIn;
    }

    static function filterdangerTextAreaStr($strIn) {
        $strIn = htmlspecialchars($strIn, ENT_QUOTES, 'UTF-8');
        $strIn = str_replace("\n", "<br/>", $strIn);
        $strIn = str_replace("\t", "<br/>", $strIn);
        $strIn = str_replace("\r", " ", $strIn);
        $strIn = str_replace("\b", " ", $strIn);
        $strIn = str_replace("\f", " ", $strIn);
        $strIn = preg_replace("/[\\s]{4,}/", "&nbsp;&nbsp;&nbsp;&nbsp;", $strIn);
        $strIn = self::filterCommon($strIn);
        return $strIn;
    }

    static function toTextAreaStr($strIn) {
        $strIn = preg_replace('/<br\\s*?\/??>/i', "\n", $strIn);
        $strIn = str_replace("&nbsp;", " ", $strIn);
        return $strIn;
    }

    static function filterdangerFckStr($strIn, $needfilter = true) {
        $strIn = preg_replace("'<script[^>]*?>.*?</script>'si", '', $strIn);
        $strIn = preg_replace("'<style[^>]*?>.*?</style>'si", '', $strIn);
        $strIn = preg_replace("'<iframe[^>]*?>.*?</iframe>'si", '', $strIn);
        $strIn = preg_replace("'<frameset[^>]*?>.*?</frameset>'si", '', $strIn);
        $strIn = preg_replace("' href=([\'\"\"])?[\s\S]*script[^>]*'si", '', $strIn);
        $strIn = preg_replace("'<img[^>]*src=([\'\"\"])?[\s\S]*script[^>]*>'si", '', $strIn);
        $strIn = preg_replace("'<!--[/!]*?[^<>]*?>'si", '', $strIn);
        $strIn = preg_replace("'&#(d+);'e", "", $strIn);


        if ($needfilter) {
            //			echo 1;
            $strIn = self::filterCommon($strIn);
        }

        return $strIn;
    }

    static function genRandomString($len) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);    // 将数组打乱
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    static function getTotalPage($rcdCt, $psize) {

        $iPageCount = 1;
        if ($rcdCt % $psize == 0) {
            $iPageCount = $rcdCt / $psize;
        } else {
            $iPageCount = ceil($rcdCt / $psize);
        }
        return $iPageCount;
    }

    static function guid() {
        if (function_exists('com_create_guid')) {

            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = chr(123)// "{"
                    . substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12)
                    . chr(125); // "}"
            return $uuid;
        }
    }

    static function getIp() {
        static $realip = NULL;

        if ($realip !== NULL) {
            return $realip;
        }

        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);

                    if ($ip != 'unknown') {
                        $realip = $ip;

                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;
    }

    static function arrayClearBlank($arr) {

        function odd($var) {
            return($var <> '');
        }

        $newArr = array_filter($arr, "odd");
        return $newArr;
    }

    static function lastIndexOf($haystack, $needle) {
        $index = -1;

        if ($haystack) {
            if (strrpos($haystack, $needle) > 0) {
                $size = strlen($haystack);
                $pos = strpos(strrev($haystack), strrev($needle));
                $index = $size - $pos - strlen($needle);
            }
        }
        return $index;
    }

    static function startWith($haystack, $needle) {
        $is = false;
        $hayLen = strlen($haystack);
        $needleStart = substr($needle, 0, $hayLen);
        if ($haystack === $needleStart) {
            $is = true;
        }
        return $is;
    }

    static function get_age($birth_year, $birth_month, $birth_date) {
        $now_age = 1; //实际年龄，以出生时为1岁计
        $full_age = 0; //周岁，该变量放着，根据具体情况可以随时修改
        $now_year = date('Y', time());
        $now_date_num = date('z', time()); //该年份中的第几天
        $birth_date_num = date('z', mktime(0, 0, 0, $birth_month, $birth_date, $birth_year));
        $difference = $now_date_num - $birth_date_num;
        if ($difference > 0) {
            $full_age = $now_year - $birth_year;
        } else {
            $full_age = $now_year - $birth_year - 1;
        }
        $now_age = $full_age + 1;
        return $now_age;
    }

    static function array_unique_array($array2D) {
        foreach ($array2D as $v) {
            $v = join(",", $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[] = $v;
        }
        $temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
        foreach ($temp as $k => $v) {
            $temp[$k] = explode(",", $v);   //再将拆开的数组重新组装
        }
        return $temp;
    }

}