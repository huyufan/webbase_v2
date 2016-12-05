
<?php
Class WebBasePager {

    var $nextlink = 10;
    var $RecordCount = 0;
    var $CurPage = 1;
    var $TotalPage = 0;
    var $Url = "";
    var $PageQueryStr;
    var $pageSize = 10;
    var $PageStr = "";
    

    public function WebBasePager($rcdCount, $pgsize, $pbutonCount=5, $disabledClass="", $currentClass="", $showTotal=false, $prevStr="&lt; 上一页", $nextStr="下一页 &gt;", $alwaysShow=false, $pgquerystr="page", $url=null) {
        $curpage = isset($_GET[$pgquerystr]) ? $_GET[$pgquerystr] : 1;
        $curpage = webbase_utility::GetIntStr($curpage, 1);
        if ($curpage < 0) {
            $curpage = 1;
        }
        $this->CurPage = $curpage;

        $theURL = "";
        $this->getUrl();
        if ($url) {
            $this->Url = $url;
        }
        $this->PerCount = $pbutonCount;
        $pgSep = "?" . $pgquerystr . "=";
        $pgsepAmp = "&" . $pgquerystr . "=";

        $haveStr = "";
        $haveAmpStr = "";
        $isHaveAskstr = strpos($this->Url, "?");

        $haveStr = strchr($this->Url, "?" . $pgquerystr . "=");
        $haveAmpStr = strchr($this->Url, "&" . $pgquerystr . "=");
        if ($haveAmpStr == "" && $haveStr == "") {
            if ($isHaveAskstr == "") {
                $theURL = $this->Url . "?page={pageidicate}";
            } else {
                $theURL = $this->Url . "&page={pageidicate}";
            }
        } else if ($haveStr != "") {
            $pq = "?" . $pgquerystr . "=" . $this->CurPage;
            $rq = "?" . $pgquerystr . "={pageidicate}";
            $theURL = str_replace($pq, $rq, $this->Url);
        } else if ($haveAmpStr) {
            $theURL = str_replace("&" . $pgquerystr . "=" . $this->CurPage, "&" . $pgquerystr . "={pageidicate}", $this->Url);
        }
        $outstr = "";
        $this->TotalPage = $this->getTotalPage($rcdCount, $pgsize);
        $two = $this->TotalPage - 1;
        $one = $this->TotalPage;
        $lastTwo1 = " <a class='ep_href' href='" . str_replace("{pageidicate}", $this->TotalPage - 1, $theURL) . "'>$two</a>\n ";
        $lastTwo2 = " <a class='ep_href' href='" . str_replace("{pageidicate}", $this->TotalPage, $theURL) . "'>$one</a>\n ";

        $fstTwo1 = " <a class='ep_href' href='" . str_replace("{pageidicate}", 1, $theURL) . "'>1</a>\n ";
        $fstTwo2 = " <a class='ep_href' href='" . str_replace("{pageidicate}", 2, $theURL) . "'>2</a>\n ";
        if ($this->CurPage > $this->TotalPage) {
            $this->CurPage = $this->TotalPage;
        }
        if ($this->TotalPage <= $pbutonCount) {
            for ($count = 1; $count <= $this->TotalPage; $count+=1) {

                if ($count != $this->CurPage) {
                    $outstr .=" <a class='ep_href' href='" . str_replace("{pageidicate}", $count, $theURL) . "'>" . $count . "</a> ";
                } else {

                    $outstr .=" <span $currentClass >" . $count . "</span> ";
                }
            }
        } else if ($this->TotalPage > $pbutonCount) {
            $firstIf = intval(($this->CurPage - 1) / $pbutonCount);
            $countStart = intval(($this->CurPage - 1) / $this->PerCount) * $this->PerCount + 1;
            if ($firstIf == 0) {
                for ($count = 1; $count <= $this->PerCount; $count+=1) {

                    if ($count != $this->CurPage) {
                        $outstr.= " <a class='ep_href' href='" . str_replace("{pageidicate}", $count, $theURL) . "'>" . $count . "</a> \n";
                    } else {
                        $outstr .= " <span $currentClass>" . $count . "</span>\n";
                    }
                }
                $outstr.=" <a class='ep_href' href='" . str_replace("{pageidicate}", $this->PerCount + 1, $theURL) . "' >...</a>\n ";
            } else if ($countStart - 1 + $this->PerCount >= $this->TotalPage) {

                $outstr.="$fstTwo1 <a class='ep_href' href='" . str_replace("{pageidicate}", $countStart - 1, $theURL) . "'>...</a>\n";
                for ($count = $countStart; $count <= $this->TotalPage; $count+=1) {

                    if ($count != $this->CurPage) {
                        $outstr .=" <a class='ep_href' href='" . str_replace("{pageidicate}", $count, $theURL) . "'>" . $count . "</a>\n ";
                    } else {
                        $outstr .=" <span $currentClass>" . $count . "</span> \n";
                    }
                }
            } else {

                $nextlink = intval(($this->CurPage - 1) / $this->PerCount) * $this->PerCount;
                $outstr .="$fstTwo1<a class='ep_href' href='" . str_replace("{pageidicate}", $nextlink, $theURL) . "'>...</a>\n ";
                $countMax = intval(($this->CurPage - 1) / $this->PerCount) * $this->PerCount + $this->PerCount;
                for ($count = $countStart; $count <= $countMax; $count+=1) {
                    if ($count != $this->CurPage) {
                        $outstr .=" <a class='ep_href' href='" . str_replace("{pageidicate}", $count, $theURL) . "'>" . $count . "</a> \n";
                    } else {
                        $outstr .=" <span $currentClass>" . $count . "</span> \n";
                    }
                }
                if ($countMax == $two) {
                    $lastTwo1 = "";
                }
                $outstr.="<a class='ep_href' href='" . str_replace("{pageidicate}", $count, $theURL) . "'>...</a>\n $lastTwo1$lastTwo2";
            }
        }

        $nextPage = $this->CurPage + 1;
        $prevPage = $this->CurPage - 1;
        $prevPageStr = "";
        $nextPageStr = "";
        if ($nextPage > $this->TotalPage) {
            //echo 1;
            $nextPageStr = " <span $disabledClass>$nextStr</span> ";
        } else {
            $nextPageStr = " <a class='ep_href' href='" . str_replace("{pageidicate}", $nextPage, $theURL) . "'>$nextStr </a>\n ";
        }
        if ($this->CurPage != 1) {
            $prevPageStr = " <a class='ep_href' href='" . str_replace("{pageidicate}", $prevPage, $theURL) . "' >$prevStr</a> \n";
        } else {
            $prevPageStr = " <span " . $disabledClass . ">$prevStr</span> ";
        }

        if (!$alwaysShow && $this->TotalPage <= 1) {
            $rtn = "";
        } else {
            $totalstr = "";
            if ($showTotal) {
                $totalstr = "<span>共" . $rcdCount . "记录|共" . $this->TotalPage . "页|当前第" . $this->CurPage . "页</span>\n";
            }
            $rtn = $totalstr . $prevPageStr . " " . $outstr . $nextPageStr;
        }
        if (!empty($rtn)) {
            $rtn.="&nbsp;<input type='text' style='width:40px;' value='" . $this->CurPage . "'/><input type='button' onclick=\"
            var href=document.location.href;
            var val=$(this).prev().val();
            var reg=new RegExp('page=\\d+','gmi');
            var pagestr='page='+val;
           if(href.indexOf('page=')<0&&href.indexOf('?')>0){href=href+'&page=1'}
           if(href.indexOf('?')<0){href=href+'?page=1'}
            href=href.replace('page=$this->CurPage',pagestr);
            document.location=href\" value='跳转'/>";
        }
        $this->PageStr = $rtn;
        $this->RecordCount = $rcdCount;
        $this->PageQueryStr = $pgquerystr;
    }

    private function getUrl() {
        //var_dump($_SERVER);
        $this->Url = $_SERVER["REQUEST_URI"];
    }

    public function getTotalPage($rcdCt, $psize) {

        $iPageCount = 1;
        if ($rcdCt % $psize == 0) {
            $iPageCount = $rcdCt / $psize;
        } else {
            $iPageCount = ceil($rcdCt / $psize);
        }
        return $iPageCount;
    }

}

