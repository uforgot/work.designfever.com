<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : null; 
	$pwd = isset($_REQUEST['pwd']) ? $_REQUEST['pwd'] : null; 

	if ($doc_no == "")
	{
?>
<script type="text/javascript">
	alert("�ش� ������ �������� �ʽ��ϴ�.");
</script>
<?
		exit;
	}
?>

<? include INC_PATH."/top.php"; ?>
<script src="/js/approval.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

		$("#doc_no",top.document).val("<?=$doc_no?>");
		$("#order",top.document).val("<?=$order?>");
		$("#pwd",top.document).val("<?=$pwd?>");
		$("#approval_btn",top.document).html("<a href=\"javascript:funSignOk();\"><img src=\"/img/btn_approve.gif\" alt=\"�����ϱ�\"></a>");

		var isPopup2 = false;
		var d = isPopup2 ? 'none' : 'block';
		var z = isPopup2 ? 0 : 987654;

		$("#popApproval",top.document).attr("style","display:inline; z-index:"+z);

		isPopup2 = !isPopup2;
	});
</script>
</head>
<body>
</body>
</html>
