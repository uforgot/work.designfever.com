<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : null; 
	$pwd = isset($_REQUEST['pwd']) ? $_REQUEST['pwd'] : null; 
	$sign = isset($_REQUEST['sign']) ? $_REQUEST['sign'] : null; 

	if ($doc_no == "")
	{
?>
<script type="text/javascript">
	alert("해당 문서가 존재하지 않습니다.");
</script>
<?
		exit;
	}
?>

<? include INC_PATH."/top.php"; ?>
<script src="/js/approval.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

		$("#doc_no2",top.document).val("<?=$doc_no?>");
		$("#order2",top.document).val("<?=$order?>");
		$("#pwd2",top.document).val("<?=$pwd?>");
		$("#sign2",top.document).val("<?=$sign?>");
		
		var isPopup2 = false;
		var d = isPopup2 ? 'none' : 'block';
		var z = isPopup2 ? 0 : 987654;

		$("#popApproval",top.document).attr("style","display:none;");
		$("#popPassword",top.document).attr("style","display:inline; z-index:"+z);

		isPopup2 = !isPopup2;

		$("#pwd_txt",top.document).focus();
	});
</script>
</head>
<body>
</body>
</html>

