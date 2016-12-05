<?php

function smarty_block_form($params, $content, $template, &$repeat) {
    $form = isset($params["base"]) ? $params["base"] : null;
    $id = isset($params["id"]) ? $params["id"] : null;
    $isSubmit = isset($params["submit"]) ? $params["submit"] : true;
    $clientValid = false;
    $clientValid = isset($params["client"]) ? $params["client"] : true;
    $attrs = "";

    foreach ($params as $key => $p) {
        if ($key != "base" && $key != "client" && $key != "submit") {
            $attrs.=" $key=\"$p\"";
        }
    }

    $start = "<form{$attrs}>";
    $end = "</form>";
    $rtn = $start . $content . $end;
    if (!$form) {
        $rtn = $rtn . "<span style='color:red;'>表单没有初始化对应的验证类！</span>";
    } else {
        if ($clientValid) {
            $js = "<script type='text/javascript'>if(typeof(uk.validator)=='undefined'){alert('未加载验证js');}" . $form->custom($id, $isSubmit) . "</script>";
            $rtn = $rtn . "\n{$js}";
        }
    }
    if (!$id) {
        $rtn = $rtn . "<span style='color:red;'>请为表单设置id属性，否则验证无法起作用！</span>";
    }

    return $rtn;
}
