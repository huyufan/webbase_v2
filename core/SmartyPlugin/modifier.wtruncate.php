<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty truncate modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *               optionally splitting in the middle of a word, and
 *               appending the $etc string or inserting $etc into the middle.
 * 
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php truncate (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com> 
 * @param string $string input string
 * @param integer $length lenght of truncated text
 * @param string $etc end string
 * @param boolean $break_words truncate at word boundary
 * @param boolean $middle truncate in the middle of text
 * @return string truncated string
 */
function smarty_modifier_wtruncate($string, $length = 80, $etc = '...') {
    $str = str_replace("\r\n", " ", $string);
    $str = stripslashes($str);
    $targetLen = mb_strlen($str, 'utf-8');
    if ($targetLen > $length) {
        $newStr = mb_substr($string, 0, $length, 'utf-8');
        $newStr.=$etc;
        return $newStr;
    }
    return $string;
}

?>