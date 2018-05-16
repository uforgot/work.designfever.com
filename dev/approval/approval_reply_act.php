<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<? include INC_PATH."/top.php"; ?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write_reply";  

	$reply_no = isset($_REQUEST['reply_no']) ? $_REQUEST['reply_no'] : null;  
	$reply_contents = isset($_REQUEST['reply_contents']) ? $_REQUEST['reply_contents'] : null;  

	$reply_contents = str_replace("'","''",$reply_contents);

	$retUrl = "approval_detail.php?doc_no=". $doc_no;

	if ($type == "write_reply")
	{
		$type_title = "댓글 등록";

		$sql = "SELECT ISNULL(MAX(R_SEQNO),0) FROM DF_APPROVAL_REPLY WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		$sql = "INSERT INTO DF_APPROVAL_REPLY 
				(DOC_NO, R_SEQNO, R_PRS_ID, R_PRS_LOGIN, R_PRS_NAME, R_PRS_TEAM, R_PRS_POSITION, R_CONTENTS, R_REG_DATE)
				VALUES
				('$doc_no', '$seq', '$prs_id', '$prs_login', '$prs_name', '$prs_team', '$prs_position', '$reply_contents', getdate())";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error 1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}

		$sql = "UPDATE DF_APPROVAL SET 
					REPLY_CNT = REPLY_CNT + 1
				WHERE 
					DOC_NO = '$doc_no'";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error 2. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
?>
		<script language="javascript">
			location.href = "<?=$retUrl?>";
		</script>
<?
	}
	else if ($type == "modify_reply")
	{
		$type_title = "댓글 수정";

		$sql = "UPDATE DF_APPROVAL_REPLY SET
					R_CONTENTS = '$reply_contents'
				WHERE 
					R_SEQNO = $reply_no";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error 1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
?>
		<script language="javascript">
			location.href = "<?=$retUrl?>";
		</script>
<?
	}
	else if ($type == "delete_reply")
	{
		$type_title = "댓글 삭제";

		$sql = "DELETE FROM DF_APPROVAL_REPLY WHERE R_SEQNO = $reply_no";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error 1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.\n<?=$sql?>");
		</script>
<?
			exit;
		}

		$sql = "UPDATE DF_APPROVAL SET 
					REPLY_CNT = REPLY_CNT - 1
				WHERE 
					DOC_NO = '$doc_no'";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error 2. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
?>
		<script language="javascript">
			location.href = "<?=$retUrl?>";
		</script>
<?
	}
?>

