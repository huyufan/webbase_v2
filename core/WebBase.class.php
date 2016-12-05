<?php

ob_start();
header("Content-Type: text/html; charset=utf8");
?>
<?php

/* * ********************************
 * Copyright (c) 2008,jeffZhongWebBase
 * All rights reserved.
 *
 * 文件名:WebBase.INC
 * 类名: WebBase
 * 版本号webabse1.0
 * 作者钟钢
 * 初始日期2008-12-21
 *
 * 发行号 version 1.
 * last update: 2009-05-03
 * ********************************* */

class WebBaseContext {

    static $page = null;
    static $global = array();

}

abstract Class WebBase {

    public $isFormPost;

    /**
     * 用户来源地址
     * @var string
     */
    public $urlReferer = "";
    public $cacheId = false;
    public $title = "";
    public $host = "";
    public $domain = "";
    public $tplDir = "";
    private $dir = array();

    /**
     * smarty对象
     * @var smarty
     */
    public $page;
    public $pageTpl;
    public $isCached = false;
    public $viewInfo;
    public $isJson = false;
    public $debugger = false;

    public function onInit() {
        
    }

    public function pageLoad($method) {

        if (!$this->isJson) {
            if (!$this->cacheId) {
                $this->cacheId = base64_encode(REQUESTURI);
            }

            if (is_array($this->viewInfo)) {
                foreach ($this->viewInfo as $keyView => $info) {
                    $this->page->assign($keyView, $info);
                }
            }
            $this->debugIfRequired();
        } else {
            header("Content-type:application/json;charset=UTF-8");
            echo json_encode($this->viewInfo);
            $this->debugIfRequired();
            die();
        }
    }

    function debugIfRequired() {
        if (isset($this->debugger) && $this->debugger) {
            $now = microtime();
            $eplisis = $now - TIMESTART;
            $viewDetail = var_export($this->viewInfo, true);
            echo "<div style='background:#fff;display:none;'>
            页面执行时间:{$eplisis}秒
            <br/>
            页面模板信息:{$viewDetail}
            </div>";
        }
    }

    function __construct() {
        $acceptType = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : "";
        $arType = explode(',', $acceptType);

        $this->isJson = isset($arType[0]) && strtolower($arType[0]) == "application/json";
        $this->urlReferer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null;
        $this->host = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "";
        $match = false;
        preg_match("/[a-zA-Z0-9\-]{0,100}(\.com|\.com\.cn|\.net|\.net\.cn)/i", $this->host, $match);
        if (isset($match[0])) {
            $this->domain = $match[0];
        } else {
            $this->domain = null;
        }
        $this->page = new WebBaseSmarty(true);
        WebBaseContext::$page = $this->page;
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "";
        if (strtolower($method) == 'post') {
            $this->isFormPost = 1;
        } else {
            $this->isFormPost = 0;
        }
    }

    function setTplDir($dir) {
        $this->dir[] = $dir;
    }

    public function show() {
        if (!empty($this->pageTpl)) {
            if (!empty($this->tplDir)) {
                $this->page->template_dir = null;
                $this->page->setTemplateDir($this->tplDir);
            }
            if ($this->pageTpl) {
                if (count($this->dir) > 0) {
                    $this->page->setTemplateDir($this->dir);
                }
                header("Content-type:text/html;charset=UTF-8");
                $this->page->display($this->pageTpl);
            }
        }
    }

    #region*******************获取数据*************************

    public function getQuery($queryName, $maxLength = 0, $isTextArea = 0) {
        //$temp=$HTTP_GET_VARS[$queryName];
        $temp = isset($_GET[$queryName]) ? urldecode($_GET[$queryName]) : "";
        $temp = trim($temp);
        if (!$isTextArea) {
            $temp = WebBaseUtility::filterStr($temp, $maxLength);
        } else {
            $temp = WebBaseUtility::filterTextAreaStr($temp, $maxLength);
        }
        //echo $fromId;
        return $temp;
    }

    public function getForm($queryfromName, $maxLength = 0, $isTextArea = 0) {
        //$temp= $_POST[$queryfromName];
        $temp = isset($_POST[$queryfromName]) ? $_POST[$queryfromName] : "";
        $temp = trim($temp);
        if (!$isTextArea) {

            $temp = WebBaseUtility::filterStr($temp, $maxLength);
        } else {
            $temp = WebBaseUtility::filterTextAreaStr($temp, $maxLength);
        }
        //echo $fromId;
        return $temp;
    }

    //此方法接受参数不安全,未进行安全过滤,请根据接受数组维度对数据进行安全检查
    /**
     * <input type="hidden" name="test[]"/><input type="hidden" name="test[]"/><input type="hidden" name="test[]"/>
     * 此方法接受参数不安全,未进行安全过滤,请根据接受数组维度对数据进行安全检查 返回form提交数组
     * @param unknown_type $queryfromName
     * @param unknown_type $maxLength
     * @param unknown_type $isTextArea
     * @return Ambigous <multitype:, NULL, unknown>
     */
    public function getFormArray($queryfromName, $maxLength = 0, $isTextArea = 0) {
        //$temp= $_POST[$queryfromName];
        $temp = isset($_POST[$queryfromName]) ? $_POST[$queryfromName] : array();
        if (is_string($temp)) {
            $temp = array();
        }
        //echo $fromId;
        return $temp;
    }

    public function getFormHtml($queryfromName, $needFilter = true) {
        $temp = isset($_POST[$queryfromName]) ? $_POST[$queryfromName] : "";
        $rplAr = array("\r\n", "\r\t", "\t", "\n");
        $temp = str_replace($rplAr, '', $temp);
        //echo $temp;
        $temp = trim($temp);
        //$temp = WebBaseUtility::filterdangerFckStr($temp, $needFilter);
        $safe = new WebbaseSafeHtml();
        $temp = $safe->parse($temp);
        //echo $fromId;
        return $temp;
    }

    public function getFormUrl($queryfromName) {
        $temp = isset($_POST[$queryfromName]) ? $_POST[$queryfromName] : "";
        $temp = trim($temp);
        $is_url = WebBaseUtility::is_url($temp);
        $temp = $is_url ? $temp : "";
        return $temp;
    }

    public function getQueryUrl($queryfromName) {
        $temp = isset($_GET[$queryfromName]) ? $_GET[$queryfromName] : null;
        $temp = trim($temp);
        $is_url = WebBaseUtility::is_url($temp);
        $temp = $is_url ? $temp : "";
        return $temp;
    }

    public function getQueryInt($queryName, $defaultVal = 0) {
        //$temp=$HTTP_GET_VARS[$queryName];
        $rtn = $defaultVal;
        $temp = isset($_GET[$queryName]) ? $_GET[$queryName] : null;
        $temp = trim($temp);
        $rtn = WebBaseUtility::GetIntStr($temp, $defaultVal);
        return $rtn;
    }

    public function getFormInt($query_intfromName, $defaultVal = 0) {
        $rtn = 0;
        $temp_formval = isset($_POST[$query_intfromName]) ? $_POST[$query_intfromName] : null;
        $temp_formval = trim($temp_formval);
        $rtn = WebBaseUtility::GetIntStr($temp_formval, $defaultVal);
        return $rtn;
    }

    public function getQueryBool($queryName) {
        //$temp=$HTTP_GET_VARS[$queryName];
        $rtn = 0;
        $temp = isset($_GET[$queryName]) ? $_GET[$queryName] : false;
        $temp = trim($temp);
        $rtn = WebBaseUtility::GetBooleanStr($temp);
        return $rtn;
    }

    public function getFormBool($queryName) {
        //$temp=$HTTP_GET_VARS[$queryName];
        $rtn = 0;
        $temp = isset($_POST[$queryName]) ? $_POST[$queryName] : 0;
        $temp = trim($temp);
        $rtn = WebBaseUtility::GetBooleanStr($temp);
        return $rtn;
    }

    public function getQueryFloat($queryName) {
        //$temp=$HTTP_GET_VARS[$queryName];
        $rtn = 0;
        $temp = isset($_GET[$queryName]) ? $_GET[$queryName] : 0;
        $rtn = WebBaseUtility::GetFloatStr($temp);
        return $rtn;
    }

    public function getFormFloat($queryName) {
        //$temp=$HTTP_GET_VARS[$queryName];
        $rtn = 0;
        $temp = isset($_POST[$queryName]) ? $_POST[$queryName] : 0;

        $rtn = WebBaseUtility::GetFloatStr(trim($temp));
        if ($rtn == "") {
            $rtn = 0;
        }
        return $rtn;
    }

    public function toJson($data, $rcdCount = 0, $isSuc = true, $isStr = false) {
        //$json=new JSON();
        // $sucStr=1;
        // if(!$isSuc){
        // $sucStr=0;
        // }
        // $dt=
        @ob_end_clean();
        echo "(" . json_encode($data) . ");";
        die();
        //$rtn=$this->Callback."({success:".$sucStr.",totalCount:".$rcdCount.",responseCode:".$sucStr.",items:".$this->Json->encode($data)."})";
    }

    #endregion
    #region*****************弹出信息*************************
    /// <summary>
    /// 发送javascript alert();
    /// </summary>
    /// <param name="msg"></param>

    public function alert($msg, $status = 1, $extrascript = "", $refresh = true) {
        if (!$this->isJson) {
            $refreshstr = "document.location=document.location.href;";
            if (!$refresh) {
                $refreshstr = "";
            }
            $lastScript = "<script type='text/javascript'>alert('" . $msg . "');" . $extrascript . $refreshstr . "</script>";
            echo $lastScript;
            if ($refresh) {
                die();
            }
        } else {
            $arWrite = array("status" => $status, "msg" => $msg, "extraScript" => $extrascript, "refresh" => $refresh);
            echo $arWrite;
            die();
        }
    }

    /// <summary>
    /// 发送javascript alert();
    /// </summary>
    /// <param name="msg"></param>
    public function alertRedirect($href, $msg = "", $extrascript = "") {
        $refreshstr = "document.location='" . $href . "'";
        $lastScript = "<script type='text/javascript'>alert('" . $msg . "');" . $extrascript . $refreshstr . "</script>";
        echo $lastScript;
        die();
    }

    public function redirect($href, $post_data = array()) {
        if (count($post_data) > 0) {
            @ob_end_clean();
            $str = "<html><head><title>loading...</title></head><body>
			<form method='post' action='$href' id='form1'>";
            foreach ($post_data as $key => $data) {
                $str.="<input type='hidden' name='$key' value='$data'/>";
            }
            $str.="</form><script type='text/javascript'>document.getElementById('form1').submit();</script></body></html>";

            echo $str;
            die();
        }
        header("Location:" . $href);
        die();
    }

    #endregion*******************************************
}