<?php

/* * ********************************
 * Copyright (c) 2008,jeffZhongWebBase
 * All rights reserved.
 *
 * 文件名称：WebBase.INC
 * 文件标识：E:\WorkSpace\PhPWorkSpace\CookNewsCom\DBHelper.inc.php
 * 摘    要：webbase框架 数据库操作类 for mysql
 * 当前版本：1.0
 * 作    者：钟钢
 * 完成日期：2008年11月8日
 *
 * 取代版本：无
 * 原作者  ：
 * 完成日期：2008年11月8日
 * ********************************* */

class WebBaseSql {

    public static $con;
    public static $debug = false;
    private static $con_str = "";
    private static $host = "";
    private static $user = "";
    private static $password = "";
    private static $database = "";
    private static $port = "";

    public static function resetConn() {
        self::getConnection(WebBaseConfig::app("con"), true);
    }

    public static function getConnection($connectString = "", $reconnect = false) {

        try {
            $charset = "utf8";
            if (!$reconnect && !is_a(self::$con, "PDO")) {
                $reconnect = true;
                $connectString = WebBaseConfig::app("con");
            }

            if ($reconnect) {
                if (self::$con_str != $connectString || !is_a(self::$con, "PDO")) {
                    self::getConStr($connectString);
                    $database = self::$database;
                    $host = self::$host;
                    $port = self::$port;
                    $user = self::$user;
                    $password = self::$password;
                    self::$con = new PDO("mysql:dbname=$database;host=$host;port=$port", $user, $password, array(PDO::ATTR_PERSISTENT => false));
                    self::$con->exec("SET NAMES  utf8");
                }
            }
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    private static function getConStr($connectStr = "") {
        if (empty($connectStr)) {
            $connectStr = WebBaseConfig::app("con");
        }
        if (empty($connectStr)) {
            die("no connection");
        }
        $arCon = explode("|", $connectStr); //split("[|]", $connectStr);//base64_decode($connectStr));
        self::$con_str = $connectStr;
        self::$host = $arCon[0];
        self::$user = $arCon[1];
        self::$password = $arCon[2];
        self::$database = $arCon[3];
        self::$port = $arCon[4];
    }

    public static function mysql_autoid($id, $table) {
        $query = 'SELECT MAX(' . $id . ') AS last_id FROM ' . $table;
        $result = self::ExecuteScalar($query);
        return $result + 1;
    }

    public static function getStartRow($pageIndex, $pageSize) {
        return ($pageIndex - 1) * $pageSize;
    }

    public static function getPage($idx, $size, $tbl, $field, $idkey, $where = "", $orderby = "", &$rcdCount = 0, $params = false, $con = "", $debugger = false) {
        $sfield = trim($field);
        $pos = substr($sfield, 0, 8); //intval(strpos($field,"distinct "));
        $distinct = $pos == "distinct" ? "distinct" : "";
        $start = self::GetStartRow($idx, $size);
        $sql = "SELECT $field from $tbl where (1=1) $where $orderby limit $start,$size;";
        $sqlCount = "select count(*) as rcdCount FROM $tbl where 1 $where";
        if ($distinct != "") {
            $sqlCount = "select count(distinct $idkey) as rcdCount FROM $tbl where(1=1) $where";
        }
        $sql.=$sqlCount;

        if ($debugger) {
            echo $sql;
            die();
        }
        if ($params) {
            $e_params = array();
            $e_params[] = $params;
            $e_params[] = $params;
            $data = self::getAll($sql, $e_params, false, $con, false);
        } else {
            $data = self::getAll($sql, array(), false, $con, false);
        }
        //var_dump($data);
        $rcdCount = $data[1][0]["rcdCount"];
        $data = $data[0];
        return $data;
    }

    public static function array_filter($ar) {
        $rtn = array();
        foreach ($ar as $r) {
            if ($r != "") {
                $rtn[] = $r;
            }
        }
        return $rtn;
    }

    public static function getAll($sql, $params = array(), $isrow = false, $con = "", $isProcedure = false) {
        $stmt = null;
        $rtn = array();
        if (!empty($con)) {
            self::getConnection($con, true);
        } else {
            self::getConnection($con, false);
        }
        if ($isProcedure) {
            if (count($params > 0))
                $stmt = self::$con->prepare("CALL($sql())");
        }
        else {
            $arCmd = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
            $arCmd = self::array_filter($arCmd);
            if (count($arCmd) > 1) {
                $i = -1;
                foreach ($arCmd as $cmd) {

                    if ($cmd != "") {
                        $i++;
                        $param = isset($params[$i]) ? $params[$i] : false;
                        $param = !$param ? $params : $param;
                        $stmt = self::$con->prepare($cmd, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $issuc = $stmt->execute($param);
                        if (!$issuc && self::$debug) {
                            echo $cmd;
                            var_dump($stmt->errorInfo());
                            throw new Exception;
                        }
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $rtn[] = $data;
                        if ($isrow) {
                            if (count($data) > 0) {
                                $rtn = $data[0];
                            }
                        }
                    }
                }
            } else {
                $param = isset($params[0]) ? $params[0] : false;
                $param = !$param ? $params : $param;
                $stmt = self::$con->prepare($arCmd[0], array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $issuc = $stmt->execute($param);
                if (!$issuc && self::$debug) {

                    echo $arCmd[0];
                    var_dump($stmt->errorInfo());
                    throw new Exception;
                }
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $rtn = $data;
                if ($isrow) {
                    if (count($data) > 0) {
                        $rtn = $data[0];
                    }
                }
            }
        }
        return $rtn;
    }

    public static function getScalar($sql, $params = array(), $con = "", $isProcedure = false) {
        $stmt = null;
        $rtn = null;
        if (!empty($con)) {
            self::getConnection($con, true);
        } else {
            self::getConnection($con, false);
        }
        if ($isProcedure) {
            if (count($params > 0))
                $stmt = self::$con->prepare("CALL($sql())");
        }
        else {
            $arCmd = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
            $arCmd = self::array_filter($arCmd);
            if (count($arCmd) > 1) {
                $i = -1;
                foreach ($arCmd as $cmd) {
                    if ($cmd != "") {
                        $i++;
                        $param = isset($params[$i]) ? $params[$i] : array();
                        $stmt = self::$con->prepare($cmd, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $issuc = $stmt->execute($param);

                        if (!$issuc && self::$debug) {
                            echo $cmd;
                            var_dump($param);
                            var_dump($stmt->errorInfo());
                            throw new Exception;
                        }
                        $data = $stmt->fetch(PDO::FETCH_NUM);
                        $rtn = $data;
                    }
                }
            } else {
                $stmt = self::$con->prepare($arCmd[0], array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

                $param = isset($params[0]) ? $params[0] : false;
                $param = !$param ? $params : $param;
                $issuc = $stmt->execute($param);
                if (!$issuc && self::$debug) {
//                    var_dump($stmt->errorInfo());
//                    throw new Exception;
                }
                $data = $stmt->fetch(PDO::FETCH_NUM);
                $rtn = $data;
            }
        }
        if (isset($rtn)) {

            if (is_array($rtn)) {
                $rtn = $rtn[0];
            }
        }
        return $rtn;
    }

    public static function execute($sql, $params = array(), $con = false, $isProcedure = false) {
        $stmt = null;
        $rtn = 0;
        if (!empty($con)) {
            self::getConnection($con, true);
        } else {
            self::getConnection($con, false);
        }
        if ($isProcedure) {
            if (count($params > 0))
                $stmt = self::$con->prepare("CALL($sql())");
        }
        else {

            $arCmd = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $sql);
            $arCmd = self::array_filter($arCmd);
            if (count($arCmd) > 1) {
                $i = -1;
                foreach ($arCmd as $cmd) {
                    if ($cmd != "") {
                        $i++;
                        $param = isset($params[$i]) ? $params[$i] : false;
                        $param = !$param ? $params : $param;
                        $stmt = self::$con->prepare($cmd, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                        $issuc = $stmt->execute($param);
                        if (!$issuc && self::$debug) {
                            var_dump($stmt->errorInfo());
                            var_dump($cmd);
                            throw new Exception;
                        }
                        $rtn+=$stmt->rowCount();
                    }
                }
            } else {

                $stmt = self::$con->prepare($arCmd[0], array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $param = isset($params[0]) ? $params[0] : false;
                $param = !$param ? $params : $param;
                $issuc = $stmt->execute($param);

                if (!$issuc && self::$debug) {
                    echo $arCmd[0];
                    var_dump($param);
                    var_dump($stmt->errorInfo());
                    throw new Exception;
                }
                $rtn = $stmt->rowCount();
            }
        }
        if (!$rtn) {
            $rtn = 0;
        }
        return $rtn;
    }

}