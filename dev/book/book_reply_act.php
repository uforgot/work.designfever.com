<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "6") 
	{ 
?>
	<script type="text/javascript">
		alert("탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	$board = isset($_REQUEST['board']) ? $_REQUEST['board'] : "book"; 
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$writer = isset($_REQUEST['writer']) ? $_REQUEST['writer'] : null;  
	$writer_id = isset($_REQUEST['writer_id']) ? $_REQUEST['writer_id'] : null;  

	$reply_no = isset($_REQUEST['reply_no']) ? $_REQUEST['reply_no'] : null;  
	$reply_contents = isset($_REQUEST['reply_contents']) ? $_REQUEST['reply_contents'] : null;  
	$reply_id = isset($_REQUEST['reply_id']) ? $_REQUEST['reply_id'] : null;  

	$reply_contents = str_replace("'","''",$reply_contents);

	$r_reply_no = isset($_REQUEST['r_reply_no']) ? $_REQUEST['r_reply_no'] : null;  
	$r_reply_contents = isset($_REQUEST['reply_contents2']) ? $_REQUEST['reply_contents2'] : null;  
	$r_reply_contents = str_replace("\r\n","<br>",$r_reply_contents);

	$r_reply_contents = str_replace("'","''",$r_reply_contents);

	$modify_contents =isset($_REQUEST['modify_contents']) ? $_REQUEST['modify_contents'] : null;

	$modify_contents = str_replace("'","''",$modify_contents);

	$depth = 0;
	$tmp1 = "Y";
	$tmp2 = 0;

	if ($type == "write_reply")
	{
		$type_title = "댓글 등록";
		$retUrl = "book_detail.php?board=". $board . "&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword ."&seqno=". $seqno ."&type=ret";

		$sql = "SELECT ISNULL(MAX(REPLYNO),0) FROM DF_BOARD_REPLY WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		$sql = "INSERT INTO DF_BOARD_REPLY
				(SEQNO, PRS_ID, REPLYNO, R_PRS_ID, R_PRS_NAME, R_PRS_POSITION, R_CONTENTS, R_REPLY_DEPTH, R_REG_DATE, R_TMP_1, R_TMP_2)
				VALUES
				('$seqno', '$writer_id', '$seq', '$prs_id', '$prs_name', '$prs_position', '$reply_contents', '$depth', getdate(), '$tmp1', '$tmp2')";
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

		$sql = "UPDATE DF_BOARD SET 
					REP_DEPTH = REP_DEPTH + 1
				WHERE 
					SEQNO = $seqno";
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
			parent.location.href = "<?=$retUrl?>";
		</script>
<?
	}
	else if ($type == "modify_reply")
	{
		$type_title = "댓글 수정";
		$retUrl = "book_detail.php?board=". $board . "&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword ."&seqno=". $seqno ."&type=ret";

		$sql = "UPDATE DF_BOARD_REPLY SET
					R_CONTENTS = '$modify_contents'
				WHERE 
					REPLYNO = $reply_no";
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
			parent.location.href = "<?=$retUrl?>";
		</script>
<?
	}
	else if ($type == "delete_reply")
	{
		$type_title = "댓글 삭제";
		$retUrl = "book_detail.php?board=". $board . "&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword ."&seqno=". $seqno ."&type=ret";

		$sql = "UPDATE DF_BOARD_REPLY SET
					R_TMP_1 = 'N'
				WHERE 
					REPLYNO = $reply_no";
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

		$sql = "UPDATE DF_BOARD SET 
					REP_DEPTH = REP_DEPTH - 1
				WHERE 
					SEQNO = $seqno";
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
			parent.location.href = "<?=$retUrl?>";
		</script>
<?
	}
	else if ($type == "write_reply2")
	{
		$type_title = "댓글 등록";
		$retUrl = "book_detail.php?board=". $board . "&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword ."&seqno=". $seqno ."&type=ret";

		$sql = "SELECT ISNULL(MAX(RR_REPLY_DEPTH),0) FROM DF_BOARD_REPLY2 WITH(NOLOCK) WHERE SEQNO = $seqno AND REPLYNO = $reply_no";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		$sql = "INSERT INTO DF_BOARD_REPLY2
				(SEQNO, PRS_ID, REPLYNO, R_PRS_ID, RR_PRS_ID, RR_PRS_NAME, RR_PRS_POSITION, RR_CONTENTS, RR_REPLY_DEPTH, RR_REG_DATE, RR_TMP_1, RR_TMP_2)
				VALUES
				('$seqno', '$writer_id', '$reply_no', '$reply_id', '$prs_id', '$prs_name', '$prs_position', '$r_reply_contents', '$seq', getdate(), '$tmp1', '$tmp2')";
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

		$sql = "UPDATE DF_BOARD_REPLY SET 
					R_REPLY_DEPTH = R_REPLY_DEPTH + 1
				WHERE 
					SEQNO = $seqno AND REPLYNO = $reply_no";

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
			parent.location.href = "<?=$retUrl?>";
		</script>
<?

		$sql = "UPDATE DF_BOARD SET 
					REP_DEPTH = REP_DEPTH + 1
				WHERE 
					SEQNO = $seqno";

		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error 3. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
?>
		<script language="javascript">
			parent.location.href = "<?=$retUrl?>";
		</script>
<?
	}
	else if ($type == "modify_reply2")
	{
		$type_title = "댓글 수정";
		$retUrl = "book_detail.php?board=". $board . "&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword ."&seqno=". $seqno ."&type=ret";

		$sql = "UPDATE DF_BOARD_REPLY2 SET
					RR_CONTENTS = '$modify_contents'
				WHERE 
					REPLYNO = $reply_no AND RR_REPLY_DEPTH = $r_reply_no";
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
			parent.location.href = "<?=$retUrl?>";
		</script>
<?
	}
	else if ($type == "delete_reply2")
	{
		$type_title = "댓글 삭제";
		$retUrl = "book_detail.php?board=". $board . "&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword ."&seqno=". $seqno ."&type=ret";

		$sql = "DELETE FROM DF_BOARD_REPLY2 WHERE REPLYNO = $reply_no AND RR_REPLY_DEPTH = $r_reply_no";
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

		$sql = "UPDATE DF_BOARD_REPLY SET 
					R_REPLY_DEPTH = R_REPLY_DEPTH - 1
				WHERE 
					SEQNO = $seqno AND REPLYNO = $reply_no";

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
			parent.location.href = "<?=$retUrl?>";
		</script>
<?

		$sql = "UPDATE DF_BOARD SET 
					REP_DEPTH = REP_DEPTH - 1
				WHERE 
					SEQNO = $seqno";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error 3. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
?>
		<script language="javascript">
			parent.location.href = "<?=$retUrl?>";
		</script>
<?
	}
?>

