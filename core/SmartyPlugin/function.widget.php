<?php

function smarty_function_widget($params, &$smarty) {
    $root = $_SERVER["DOCUMENT_ROOT"];
    $tpl = isset($params["tpl"]) ? $params["tpl"] : "";

    $php = isset($params["file"]) ? $root.$params["file"] : "";
    $html = "";
    if ($php != "" && $tpl != "") {
        require_once $php;

        $newSmarty = new webbase_smarty(true);
        $idx = webbase_utility::LastIndexOf($php, '/');
        $item = substr($php, $idx + 1);
        $indexOfDot = strpos($item, ".");
        $className = substr($item, 0, $indexOfDot);
        $class = new $className($params);
        $rtn = $class->load();
        foreach ($rtn as $key => $value) {
            $newSmarty->assign($key, $value);
        }
        $html = $newSmarty->fetchStr($tpl);
    }
    return $html;
}
