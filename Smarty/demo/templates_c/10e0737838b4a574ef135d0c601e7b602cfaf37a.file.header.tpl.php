<?php /* Smarty version Smarty-3.0.5, created on 2010-11-25 06:42:45
         compiled from ".\templates\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:174494cee0565d4a6a9-33339695%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '10e0737838b4a574ef135d0c601e7b602cfaf37a' => 
    array (
      0 => '.\\templates\\header.tpl',
      1 => 1290660712,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '174494cee0565d4a6a9-33339695',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_popup_init')) include 'E:\WorkSpace\elinc_sec\include\webbase\Smarty\plugins\function.popup_init.php';
?><HTML>
<HEAD>
<?php echo smarty_function_popup_init(array('src'=>"/javascripts/overlib.js"),$_smarty_tpl);?>

<TITLE><?php echo $_smarty_tpl->getVariable('title')->value;?>
 - <?php echo $_smarty_tpl->getVariable('Name')->value;?>
</TITLE>
</HEAD>
<BODY bgcolor="#ffffff">
