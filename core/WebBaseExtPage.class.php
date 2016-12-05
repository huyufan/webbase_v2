<?php
/**********************************
 * Copyright (c) 2008,jeffZhongWebBase
 * All rights reserved.
 *
 * 文件名:ExtPage.INC
 * 类名: WebBase
 * 版本号webabse1.0
 * 作者钟钢
 * 初始日期2008-12-21
 *
 * 发行号 version 1.0
 * last update: 2009-01-12
 ***********************************/
abstract class WebBaseExtPage extends WebBase{
	var $Sort="";
	var $Action="";
	var $Limit="";
	var $Start="";
	var $PageIdx=1;
	var $PageSize=10;
	var $Json;
	var $Callback="";
	function __construct(){
		$this->Json=new JSON();
		$this->Action=$this->getQuery("action");
		$this->Limit=$this->getQueryInt("limit",10);
		$this->Start=$this->getQuery("start");

		$this->PageIdx=$this->CalPage($this->Start,$this->Limit);
		$this->PageSize=$this->Limit;
		$this->Sort=trim($this->getQuery("sort")." ".$this->getQuery("dir"));
		$this->Callback=$this->getQuery("callback");
		if(empty($this->Callback)){
			$this->Callback=$this->getForm("callback");
		}
		parent::__construct();
		$this->OnExtLoad($this->Action,$this->PageIdx,$this->Sort);
	}
	public function pageLoad(){
	}
	public function OnExtLoad($act, $pageIdx,$sort)
	{
	}

	public function OnFormPostInit($act){

	}

	private function CalPage($_Start,$_PageSize){
		$cal = 1;
		try
		{
			if($_PageSize>0){
				$start = intval($_Start);
				$pageSize = $_PageSize;
				$cal = intval($start / $pageSize);
			}else{
				$cal=0;
			}
		}
		catch(Exception $e) { }
		return $cal + 1;
	}

	//6000-111.7-300=
	function Json($data,$rcdCount=0,$isSuc=true,$isStr=false){
		$sucStr=1;
		if(!$isSuc){
			$sucStr=0;
		}
		if($rcdCount==""){
			$rcdCount=0;
		}
		if(!$data){
			$data=array();
		}
		$rtn=$this->Callback."({success:".$sucStr.",totalCount:".$rcdCount.",responseCode:".$sucStr.",items:".$this->Json->encode($data)."})";
		if($isStr){
			$rtn=$this->Json->encode($data);
		}
		//@ob_end_clean();
		echo $rtn;
		die();
	}
	function JsonBool($isSuc,$msg="",$clearAll=true){
		$sucStr=1;
		if(!$isSuc){
			$sucStr=0;
		}
		$rtn=$this->Callback."({success:".$sucStr.",errorMsg:'$msg',responseCode:".$sucStr.",items:".$this->Json->encode($msg)."})";
		if($clearAll){
			//@ob_end_clean();
		}
		echo $rtn;
		die();
	}
	
	function JsonObject($obj,$rtn_it=false){
		@ob_end_clean();
		//header('Content-Type: text/javascript; charset=utf8');
		//header('Access-Control-Allow-Origin: http://www.example.com/');
		//header('Access-Control-Max-Age: 3628800');
		//	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
		/*header('Content-Type: text/html; charset=utf8');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");*/
		$rtn=json_encode($obj);
		if(!$rtn_it){
			$rtn="(".$rtn.");";
			echo $rtn;
			die();
		}else{
			return $rtn;
		}
	}



}
?>
