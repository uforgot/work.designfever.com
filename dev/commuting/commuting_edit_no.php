<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : null; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null; 

	if ($seqno == "")
	{
?>
	<script type="text/javascript">
		alert("해당 요청이가 존재하지 않습니다.");
		self.close();
	</script>
<?
		exit;
	}

	$sql = "UPDATE DF_CHECKTIME_EDIT SET
				EDIT_OK = 'X',
				OK_DATE = getdate(),
				OK_PRS_ID = '$prs_id'
			WHERE 
				SEQNO = '$seqno'";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("error. 기각 실패하였습니다. 개발팀에 문의하세요.");
	</script>
<?
		exit;
	}

	$retUrl = "commuting_edit_detail.php?seqno=". $seqno ."&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword;
?>

	<script language="javascript">
		parent.location.href = "<?=$retUrl?>";
	</script>
