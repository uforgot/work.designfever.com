<?
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null;	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	echo $beacon;
?>

<?
	unset($_SESSION['SS_PRS_ID']);
	unset($_SESSION['SS_PRS_NAME']);
	unset($_SESSION['SS_ID']);
	unset($_SESSION['SS_PRF_ID']);
	unset($_SESSION['SS_PRS_TEAM']);
	unset($_SESSION['SS_PRS_POSITION']);
	unset($_SESSION['SS_USE_TIME']);
?>

<script type="text/javascript">
	alert("�α׾ƿ��Ǿ����ϴ�");	
	parent.location.href="index.php?beacon=<?=$beacon?>"; 
</script>
