<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	unset($_SESSION['DF_PRS_ID']);
?>

<script type="text/javascript">
	alert("로그아웃되었습니다");
	parent.location.href = "/";
</script>
