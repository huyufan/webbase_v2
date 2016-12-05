<?php

Class WebBaseObject {

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

    static function Array_ClearBlank($arr) {

        function odd($var) {
            return($var <> '');
        }

        return (array_filter($arr, "odd"));
    }

}

?>