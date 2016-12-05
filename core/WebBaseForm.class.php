<?php

class WebBaseForm extends WebBase {

    public $fields = array();
    public $formId = "";
    public $error = "";

    public function __construct($_fields) {
        $this->fields = $_fields;
    }

    public function minMax($val, $min, $max) {
        $rtn = false;
        $clength = mb_strlen($val, 'utf-8');

        if ($clength >= $min && $clength <= $max) {
            $rtn = true;
        }
        return $rtn;
    }

    public function validate() {
        foreach ($this->fields as $i => $field) {
            $this->fields[$i]["isValid"] = false;
            $fname = $field["name"];
            $isRequired = isset($field["required"]) ? $field["required"] : false;
            $val = $this->getForm($fname);
            $isFieldEqFocusValue = isset($field["focus"]) && $val == $field["focus"];
            $val = $isFieldEqFocusValue ? "" : $val;
            if ($isFieldEqFocusValue) {
                $_POST[$fname] = "";
            }
            //$val = $isFieldEqFocusValue ? "" : $val;
            $isOk = false;
            if ($isRequired) {
                if ($val != "" && !$isFieldEqFocusValue) {
                    $this->fields[$i]["isValid"] = true;
                } else {
                    $field["msg"] = isset($field["msg"]) ? $field["msg"] : $fname . " 不能为空";
                    $this->error.=$field["msg"];
                }
            }
            if (isset($field["min"])) {
                $isOk = $this->minMax($val, $field["min"], $field["max"]);
                $isOk = !$isFieldEqFocusValue && $isOk;
                if (!$isRequired && $val == "") {
                    $isOk = true;
                }
                if ($isOk) {
                    $this->fields[$i]["isValid"] = true;
                } else {
                    $field["msg"] = isset($field["msg"]) ? $field["msg"] : $fname . " 长度范围应该在 {$field["min"]}-{$field["max"]}之间";
                    $this->error.=$field["msg"];
                }
            } elseif (isset($field["express"])) {
                preg_match($field["express"], $val, $result);
                $isOk = count($result) > 0;
                $isOk = !$isFieldEqFocusValue && $isOk;
                if (!$isRequired && $val == "") {
                    $isOk = true;
                }
                if ($isOk) {
                    $this->fields[$i]["isValid"] = true;
                } else {

                    $field["msg"] = isset($field["msg"]) ? $field["msg"] : $fname . " 不符合规范！";
                    $this->error.=$field["msg"];
                }
            } elseif (isset($field["compare"])) {
                $sval = $this->getForm($field["compare"]);
                $isOk = $val == $sval;
                $isOk = !$isFieldEqFocusValue && $isOk;
                if (!$isRequired && $val == "") {
                    $isOk = true;
                }
                if ($isOk) {
                    $this->fields[$i]["isValid"] = true;
                } else {

                    $field["msg"] = isset($field["msg"]) ? $field["msg"] : $fname . " 不符合规范！";
                    $this->error.=$field["msg"];
                }
            }
        }
    }

    public function custom($formId, $isSubmit = true) {
        $fieldValids = "";
        foreach ($this->fields as $field) {
            $vinfo = json_encode($field);
            $fieldValids.="v.add({$vinfo});\n";
        }
        $this->formId = $formId;
        $submitStr = "";
        if ($isSubmit) {
            $submitStr = "$(\"#{$formId}\").bind(\"submit\",function(){
                    var isValid=me.isValid();
                    if(isValid){
                        return true;
                    }
                    else{
                       return false;
                    };   
                 });";
        }
        $js = <<<HTML
        \nbase{$formId}={
             validator:false,
             init:function(){
                var v=new uk.validator('{$formId}');
                this.validator=v;
                {$fieldValids}
                this.event();
             },
             isValid:function(){
                return this.validator.isValid();
             },
             event:function(){
                var me=this;
                {$submitStr}
             }
        }
        base{$formId}.init();\n
HTML;

        return $js;
    }

    public function isValid() {
        $this->validate();
        $isValid = true;
        foreach ($this->fields as $field) {
            if (!$field["isValid"]) {
                $isValid = false;
                break;
            }
        }
        return $isValid;
    }

}
