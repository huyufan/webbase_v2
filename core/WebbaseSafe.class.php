<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WebbaseSafe
 *
 * @author jeff
 */
class WebbaseSafe {

//put your code here
    static function input($strIn) {
        $strIn = str_replace("\n", "", $strIn);
        $strIn = str_replace("\t", "", $strIn);
        $strIn = str_replace("\r", "", $strIn);
        $strIn = str_replace("\b", "", $strIn);
        $strIn = str_replace("\f", "", $strIn);
        $strIn = strip_tags($strIn);
        return $strIn;
    }

}
