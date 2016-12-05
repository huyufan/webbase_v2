<?php

function getPagerLink($idx, $rewrite, $param, $text = false, $type = "a", $attr = "", &$href = "", $isNum = true) {

    $rtn = "";
    $text = empty($text) ? $idx : $text;
    if($text=='null'){
        $text='';
    }
    if ($type == "span") {
        $rtn = "<span $attr>$text</span>";
    } elseif ($type == "a") {
        $queryUrl = $_SERVER['REQUEST_URI'];
        $link = "";
        if (empty($rewrite)) {
            if (strpos($queryUrl, $param . "=") > 0) {
                $link = preg_replace("/$param=(\d+)?/ixm", "$param=$idx", $queryUrl);
            } else {
                $add = strpos($queryUrl, "?") > 0 ? "&$param=$idx" : "?$param=$idx";
                $link = $queryUrl . $add;
            }
        } else {
            if (isset($_GET["webbaseurl"])) {
                unset($_GET["webbaseurl"]);
            }
            unset($_GET[$param]);
            $link = str_replace("[0]", $idx, $rewrite);
            $link = vsprintf($link, $_GET);
        }
        $href = $link;
        if (!$isNum) {
            $text = '';
        }
        $rtn = "<a $attr href=\"$link\">$text</a>";
    }
    return $rtn;
}

function smarty_function_pager($params, &$smarty) {
    error_reporting(-1);
    $count = isset($params['count']) ? $params['count'] : 0;
    $rewrite = isset($params['rewrite']) ? $params['rewrite'] : false;
    $curPage = isset($params['pindex']) ? $params['pindex'] : -1;
    $isJump = isset($params['isjump']) ? $params['isjump'] : false;
    $pageSize = isset($params['size']) ? $params['size'] : 10;
    $buttonCount = isset($params['buttoncount']) ? $params['buttoncount'] : 5;
    $nextInner = isset($params['next']) ? $params['next'] : '下一页';
    $prevInner = isset($params['prev']) ? $params['prev'] : '上一页';
    $lastInner = isset($params['last']) ? $params['last'] : '末页';
    $firstInner = isset($params['first']) ? $params['first'] : '第一页';
    $nextAttr = isset($params['nextvar']) ? $params['nextvar'] : '';
    $prevAttr = isset($params['prevvar']) ? $params['prevvar'] : '';
    $lastAttr = isset($params['lastvar']) ? $params['lastvar'] : '';
    $curPageAttr = isset($params['curvar']) ? $params['curvar'] : '';
    $numAttr = isset($params['numvar']) ? $params['numvar'] : '';
    $totalAttr = isset($params['totalvar']) ? $params['totalvar'] : '';

    $firstAttr = isset($params['firstvar']) ? $params['firstvar'] : '';
    $pageCount = ceil($count / $pageSize);
    $pageCount = $pageCount == 0 ? 1 : $pageCount;
    $pageParam = isset($params['page']) ? $params['page'] : 'page';

    if ($curPage < 0) {
        $curPage = isset($_GET[$pageParam]) && intval($_GET[$pageParam]) > 0 ? intval($_GET[$pageParam]) : 1; //获取当前页码
    }
    $curPage = $curPage > $pageCount ? $pageCount : $curPage;
    $ever = isset($params['ever']) ? $params['ever'] : false;




    $htmls = "";
    $previewStr = $curPage > 1 ? getPagerLink($curPage - 1, $rewrite, $pageParam, $prevInner, 'a', $prevAttr) : getPagerLink(0, false, false, $prevInner, "span", $prevAttr);
    $nextStr = $curPage < $pageCount ? getPagerLink($curPage + 1, $rewrite, $pageParam, $nextInner, 'a', $nextAttr) : getPagerLink(0, false, false, $nextInner, "span", $nextAttr);
    $firstStr = getPagerLink(1, $rewrite, $pageParam, $firstInner, 'a', $firstAttr);
    $lastStr = getPagerLink($pageCount, $rewrite, $pageParam, $lastInner, 'a', $lastAttr);
    $leftpadding = ceil(($buttonCount - 1) / 2);
    $rightPadding = $buttonCount - $leftpadding - 1;
    if ($pageCount > $buttonCount) {
        $min = $curPage > $leftpadding + 1 ? $curPage - $leftpadding : 1;
        $max = $curPage > $rightPadding + 1 ? $curPage + $rightPadding : $buttonCount;
        if ($max > $pageCount) {
            $max = $pageCount;
            $min = $pageCount - $buttonCount + 1;
        }
        //$firstStr = "";
    } else {
        $min = 1;
        $max = $pageCount;
    }
    for ($i = $min; $i <= $max; $i++) {
        $linkStr = $i != $curPage ? getPagerLink($i, $rewrite, $pageParam, false, 'a', $numAttr) : getPagerLink($i, false, false, false, "span", $curPageAttr);
        $htmls.= $linkStr;
    }
    $totalStr = "<span $totalAttr>共{$pageCount} 当前第{$curPage}页</span>";

    $jumpStr = "";
    if ($isJump && $pageCount > 1) {
        $options = "";
        $jumpStr = "\n<span class='pager_jump'>" .
                "<script type='text/javascript'>" .
                "(function(){var param=/{$pageParam}=(\d+)?/g;" .
                "var pageCount={$pageCount};" .
                "var curpage={$curPage};var purl='{$rewrite}';" .
                "document.write('<select onchange=\"document.location=this.options[this.selectedIndex].attributes[\'a\'].value;\">');" .
                "for(var ipager=1;ipager<=pageCount;ipager++)" .
                "{var link=document.location.href;" .
                "if(purl!=''){link=purl.replace('[0]',ipager)}else{link=link.replace(param,'{$pageParam}='+ipager)};" .
                "var selected=curpage==ipager?'selected=\"selected\"':'';" .
                "document.write('<option value=\"'+ipager+'\"  a=\"'+link+'\" '+selected+'>第'+ipager+'页</option>');}" .
                "document.write('</select>');})();</script></span>";
    }
    $htmls = $firstStr . $previewStr . $htmls . $nextStr . $lastStr . $jumpStr . $totalStr;

    if (!$ever && $pageCount == 1) {
        $htmls = ""; 
    }

    return $htmls;
}
