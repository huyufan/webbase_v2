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

class WebBaseData {

    static function delete($tbl, $filter, $con = false) {
        $params = array();
        $sql = "DELETE FROM {$tbl} WHERE 1";
        $sqlFilter = '';
        foreach ($filter as $name => $val) {
            $sqlFilter.=' AND ' . $name . '=:' . $name . ' ';
            $params[$name] = $val;
        }
        $i = 0;
        if ($sqlFilter != '') {
            $sql.=$sqlFilter;
            $i = WebBaseSql::execute($sql, $params, $con);
        }
        return $i;
    }

    static function insert($tbl, $fields, $con = false) {
        $sqlField = "";
        $sqlValue = "";
        $rtn = 0;
        $params = array();
        foreach ($fields as $field => $value) {
            $sqlField.=$field . ',';
            $sqlValue.=":" . $field . ',';
            $params[$field] = $value;
        }
        if (!empty($sqlField)) {
            $sqlField = substr($sqlField, 0, -1);
            $sqlValue = substr($sqlValue, 0, -1);
            $sql = "INSERT  INTO {$tbl}($sqlField) values($sqlValue)";
            $rtn = WebBaseSql::execute($sql, $params, $con);
        }
        return $rtn;
    }

    static function insertId($tbl, $fields, $con = false) {
        $sqlField = "";
        $sqlValue = "";
        $rtn = 0;
        $params = array();
        foreach ($fields as $field => $value) {
            $sqlField.=$field . ',';
            $sqlValue.=":" . $field . ',';
            $params[$field] = $value;
        }
        if (!empty($sqlField)) {
            $sqlField = substr($sqlField, 0, -1);
            $sqlValue = substr($sqlValue, 0, -1);
            $sql = "INSERT  INTO {$tbl}($sqlField) values($sqlValue);select last_insert_id()";
            $rtn = WebBaseSql::getScalar($sql, array($params, array()), $con);
        }
        return $rtn;
    }

    static function update($tbl, $fields, $filter, $con = false) {
        $sqlField = "";
        $sqlFilter = "";
        $rtn = 0;
        $params = array();
        foreach ($fields as $field => $value) {
            $sqlField.=$field . '=:' . $field . ',';
            $params[$field] = $value;
        }

        foreach ($filter as $name => $val) {
            $sqlFilter.=' AND ' . $name . '=:' . $name . ' ';
            $params[$name] = $val;
        }
        if (!empty($filter)) {
            $sqlFilter = substr($sqlFilter, 0, -1);
        }

        if (!empty($sqlField)) {
            $sqlField = substr($sqlField, 0, -1);
            $sql = "UPDATE {$tbl} SET $sqlField WHERE 1 {$sqlFilter}";
            $rtn = WebBaseSql::execute($sql, $params, $con);
        }
        return $rtn;
    }

    static function getOne($tbl, $field, $filter, $con = false) {
        $sql = "SELECT {$field} FROM {$tbl} WHERE 1";
        $sqlFilter = '';
        $params = array();
        foreach ($filter as $name => $val) {
            $sqlFilter.=' AND ' . $name . '=:' . $name . ' ';
            $params[$name] = $val;
        }
        $sql.=$sqlFilter;
        $rtn = WebBaseSql::getScalar($sql, $params, $con);
        return $rtn;
    }

    static function getRow($tbl, $field, $filter, $con = false) {
        $sql = "SELECT {$field} FROM {$tbl} WHERE 1";
        $sqlFilter = '';
        $params = array();
        foreach ($filter as $name => $val) {
            $sqlFilter.=' AND ' . $name . '=:' . $name . ' ';
            $params[$name] = $val;
        }
        $sql.=$sqlFilter;
        $rtn = WebBaseSql::getAll($sql, $params, true, $con);
        return $rtn;
    }

    static function getAll($tbl, $fields, $filter = array(), $con = false) {
        $sql = "SELECT {$fields} FROM {$tbl} WHERE 1";
        $sqlFilter = '';
        $params = array();
        foreach ($filter as $name => $val) {
            $sqlFilter.=' AND ' . $name . '=:' . $name . ' ';
            $params[$name] = $val;
        }
        $sql.=$sqlFilter;
        $rtn = WebBaseSql::getAll($sql, $params, $con);
        return $rtn;
    }

}