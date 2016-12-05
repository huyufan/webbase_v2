<?php

/* * **************************************************************
 *   @ 2011 OsApi.Net Inc.
 *   $author : LBC
 *   $email  : pochonlee@gmail.com
 *   $Id     : toxml.php 2011/1/21
 * *************************************************************** */

class WebBaseXml {

    static $version = '1.0';
    static $encoding = 'UTF-8';
    static $root = 'root';
    static $xml = null;

    static function toXml($data, $eIsArray = FALSE) {
        if (self::$xml == null) {
            self::$xml = new XMLWriter();
        }
        //  $xml=new XMLWriter();

        if (!$eIsArray) {
            self::$xml->openMemory();
            self::$xml->startDocument(self::$version, self::$encoding);
            self::$xml->startElement(self::$root);
        }
        foreach ($data as $key => $value) {

            if (is_array($value)) {
                self::$xml->startElement($key);
                self::toXml($value, TRUE);
                self::$xml->endElement();
                continue;
            }
            if (is_numeric($key)) {
                $key = 'node';
            }
            self::$xml->writeElement($key, $value);
        }
        if (!$eIsArray) {
            self::$xml->endElement();
            return self::$xml->outputMemory(true);
        }
    }

//    static function safeXmlVaule($value) {
//        $rtn = $value;
//        $rtn = str_replace("&", "&amp;",$rtn);
//        
//        $rtn = str_replace("\"", "&quot;",$rtn);
//        
//        $rtn = str_replace("<", "&lt;",$rtn);
//      
//        $rtn = str_replace(">", "&gt;",$rtn);
//        return $rtn;
//    }

}
