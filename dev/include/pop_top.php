<?php
// 페이지캐쉬 삭제
//header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    
//header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");   
//header ("Cache-Control: no-cache, must-revalidate");   
//header ("Pragma: no-cache");   
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=HEAD_TITLE?></title>
<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">

<link rel="stylesheet" href="/css/common.css" />
<link rel="stylesheet" href="/css/style_20170203.css" />
<link rel="stylesheet" href="/css/jquery-ui.css" />
<style type="text/css">
	#ui-datepicker-div { padding:0 30px 0 30px; border:1px solid #000; }
	.ui-widget-content { border:0px; }
	.ui-widget-header { border:0px; }
	.ui-icon, .ui-widget-content .ui-icon { background-image: url("/img/ui-icons_222222_256x240.png"); }
	.ui-widget-header .ui-icon { background-image: url("/img/ui-icons_222222_256x240.png"); }
	.ui-state-hover .ui-icon, .ui-state-focus .ui-icon { background-image: url("/img/ui-icons_454545_256x240.png"); }
	.ui-state-active .ui-icon { background-image: url("/img/ui-icons_454545_256x240.png"); }
	.ui-datepicker-header { margin-top:9px; height:35px; }
	.ui-datepicker-calendar thead { border-top:1px solid #000; border-bottom:1px solid #000; }
	.ui-datepicker-calendar tbody { border-bottom:12px solid #fff; }
	.ui-datepicker-calendar tbody tr:first-child td { padding-top:24px; }
	.ui-datepicker-calendar tbody a { text-align:center; }
	.ui-datepicker-calendar tbody .ui-datepicker-week-end:nth-child(1) a { color:#eb6100; }
</style>

<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.easing.1.3.js"></script>
<script src="/js/jquery-ui.js"></script>
<script src="/js/modernizr.custom.72169.js"></script>
<script src="/js/jquery.cookie.js"></script>
<script src="/js/custom.js"></script>
