<?php
/**********************************
 * Copyright (c) 2008,jeffZhongWebBase
 * All rights reserved.
 *
 * 文件名:WebBaseControl.INC
 * 类名: WebBaseControl
 * 版本号webabse1.0
 * 作者钟钢
 * 初始日期2008-12-21
 *
 * 发行号 version 1.0
 * last update: 2009-05-03
 ***********************************/
 
abstract Class WebBaseControl{
	var $Context=null;
	public function NewSmarty(){
		// $smarty=new Smarty();
		// $smarty->tem	plate_dir=Config::$SmartyTemplate;
		// $smarty->compile_dir=Config::$SmartyTemplateCompiled;
		// $smarty->left_delimiter = Config::$SmartyLeftIndicate;
		// $smarty->right_delimiter = Config::$SmartyRightIndicate;
		// $smarty->assign("this",(Array)$this);
		// return $smarty;
	}
	function ControlLoad(){	
	}
	function __construct(){
		$this->Context=Context::$Current;
		$this->ControlLoad();
		
	}
}
?>