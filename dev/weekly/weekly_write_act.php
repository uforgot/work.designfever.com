<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	
	$ret = isset($_REQUEST['ret']) ? $_REQUEST['ret'] : null;  
	if($ret == "view")	$ret_page = "weekly_view.php";
	else				$ret_page = "weekly_write.php";

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  
	$win = isset($_REQUEST['win']) ? $_REQUEST['win'] : null;  
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y"); 

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : null;  
	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;  
	$memo = isset($_REQUEST['memo']) ? $_REQUEST['memo'] : null;  	

	$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null;  
	$progress_this = isset($_REQUEST['progress_this']) ? $_REQUEST['progress_this'] : null;  
	$progress_next = isset($_REQUEST['progress_next']) ? $_REQUEST['progress_next'] : null;  
	$content_this = isset($_REQUEST['content_this']) ? $_REQUEST['content_this'] : null;  
	$content_next = isset($_REQUEST['content_next']) ? $_REQUEST['content_next'] : null;  	

	if ($type != "complete" && $type != "cancel" && $seqno == "")
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("해당 글이 존재하지 않습니다.");
		history.back();
	</script>
<?
		exit;
	}
	
	if ($type != "cancel") {

		//팀 주간보고 완료 체크
		$sql = "SELECT 
					COMPLETE_YN
				FROM 
					DF_WEEKLY WITH(NOLOCK)
				WHERE
					SEQNO = '$seqno' AND PRS_ID = '$prs_id'";								
		$rs = sqlsrv_query($dbConn,$sql);
		$record = sqlsrv_fetch_array($rs);

		$complete_yn = $record['COMPLETE_YN'];

		if ($complete_yn == 'Y')
		{
	?>
		<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
		<script type="text/javascript">
			alert("팀 주간보고 작성이 완료되어 진행할 수 없습니다.");
			history.back();
		</script>
	<?
			exit;
		}
	}

	if ($type == "write")
	{
		$type_title = "작성";
		$retUrl = "weekly_list.php?page=". $page;

		$memo = str_replace("'","''",$memo);

		$sql = "UPDATE DF_WEEKLY SET
					MEMO = '$memo', REG_DATE = getdate()
				WHERE 
					SEQNO = '$seqno' AND PRS_ID = '$prs_id'";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs != false) {
			for($i=0;$i<count($project_no)-1;$i++) {		
				$_project_no = $project_no[$i];
				$_project_title = $project_title[$i];
				$_progress_this = $progress_this[$i];
				$_progress_next = $progress_next[$i];
				$_content_this = str_replace("'","''",$content_this[$i]);
				$_content_next = str_replace("'","''",$content_next[$i]);

				$sql = "INSERT INTO DF_WEEKLY_DETAIL
							(WEEKLY_NO, PROJECT_NO, THIS_WEEK_CONTENT, NEXT_WEEK_CONTENT, THIS_WEEK_RATIO, NEXT_WEEK_RATIO, PRS_ID)
						VALUES
							('$seqno', '$_project_no', '$_content_this', '$_content_next', '$_progress_this', '$_progress_next', '$prs_id')";
				$rs = sqlsrv_query($dbConn, $sql);
			}
		}
	}
	else if ($type == "modify")
	{
		$type_title = "수정";
		$retUrl = $ret_page."?type=". $type."&seqno=". $seqno."&page=". $page."&win=". $win;

		$sql = "UPDATE DF_WEEKLY SET
					MEMO = '$memo', REG_DATE = getdate()
				WHERE 
					SEQNO = '$seqno' AND PRS_ID = '$prs_id'";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs != false) {

		// 팀 주간보고 작성완료 체크

			for($i=0;$i<count($project_no)-1;$i++) {		
				$_project_no = $project_no[$i];
				$_project_title = $project_title[$i];
				$_progress_this = $progress_this[$i];
				$_progress_next = $progress_next[$i];
				$_content_this = str_replace("'","''",$content_this[$i]);
				$_content_next = str_replace("'","''",$content_next[$i]);

				$sql = "IF EXISTS (SELECT SEQNO FROM DF_WEEKLY_DETAIL WHERE WEEKLY_NO = '$seqno' AND PROJECT_NO = '$_project_no')
							UPDATE DF_WEEKLY_DETAIL SET
								THIS_WEEK_CONTENT = '$_content_this', NEXT_WEEK_CONTENT = '$_content_next', THIS_WEEK_RATIO = '$_progress_this', NEXT_WEEK_RATIO = '$_progress_next'
							WHERE
								WEEKLY_NO = '$seqno' AND PROJECT_NO = '$_project_no'
						ELSE
							INSERT INTO DF_WEEKLY_DETAIL
								(WEEKLY_NO, PROJECT_NO, THIS_WEEK_CONTENT, NEXT_WEEK_CONTENT, THIS_WEEK_RATIO, NEXT_WEEK_RATIO, PRS_ID)
							VALUES
								('$seqno', '$_project_no', '$_content_this', '$_content_next', '$_progress_this', '$_progress_next', '$prs_id')";
				$rs = sqlsrv_query($dbConn, $sql);
			}
		}
	}
	else if ($type == "delete")
	{
		/*
		$type_title = "삭제";
		$retUrl = "weekly_list.php?page=". $page."&win=". $win;

		$sql = "DELETE FROM DF_WEEKLY_DETAIL WHERE WEEKLY_NO = $seqno";
		$rs = sqlsrv_query($dbConn, $sql);

		$sql = "DELETE FROM DF_WEEKLY WHERE SEQNO = $seqno AND PRS_ID = '$prs_id'";
		$rs = sqlsrv_query($dbConn, $sql);
		*/
	}
	else if ($type == "complete")
	{
		$type_title = "팀 주간보고서 작성 완료";
		//$retUrl = "weekly_list.php?page=". $page."&win=". $win;
		//$retUrl = $ret_page."?type=modify&seqno=". $seqno."&page=". $page."&win=". $win;
		$retUrl = "weekly_list_team.php?page=". $page."&year=". $year ."&team=". $prs_team;

		//팀원 보고서 제출 체크(보류)

		$sql = "UPDATE DF_WEEKLY SET
					COMPLETE_YN = 'Y', COMPLETE_DATE = getdate()
				WHERE 
					WEEK_ORD = '$order' AND PRS_TEAM = '$prs_team'";
		$rs = sqlsrv_query($dbConn, $sql);
	}
	else if ($type == "cancel")
	{
		$type_title = "팀 주간보고서 작성 취소";
		//$retUrl = "weekly_list.php?page=". $page."&win=". $win;
		//$retUrl = $ret_page."?type=modify&seqno=". $seqno."&page=". $page."&win=". $win;
		$retUrl = "weekly_list_team.php?page=". $page."&year=". $year ."&team=". $prs_team;

		$sql = "UPDATE DF_WEEKLY SET
					COMPLETE_YN = 'N'
				WHERE 
					WEEK_ORD = '$order' AND PRS_TEAM = '$prs_team'";
		$rs = sqlsrv_query($dbConn, $sql);
	}

	if ($rs == false)
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script language="javascript">
		alert("<?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
	</script>
<?
		exit;
	}
	else
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script language="javascript">
		alert("<?=$type_title?> 되었습니다.");
		parent.location.href = "<?=$retUrl?>";
	</script>
<?
	}
?>
