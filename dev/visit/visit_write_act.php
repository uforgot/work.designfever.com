<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<script type="text/javascript">
		alert("등록대기,탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$writer = isset($_REQUEST['writer']) ? $_REQUEST['writer'] : null;  
	$writer_id = isset($_REQUEST['writer_id']) ? $_REQUEST['writer_id'] : null;  
	$writer_name = isset($_REQUEST['writer_name']) ? $_REQUEST['writer_name'] : null;  
	$writer_team = isset($_REQUEST['writer_team']) ? $_REQUEST['writer_team'] : null;  
	$writer_position = isset($_REQUEST['writer_position']) ? $_REQUEST['writer_position'] : null;  

	$company = isset($_REQUEST['company']) ? $_REQUEST['company'] : null;  
	$visitor = isset($_REQUEST['visitor']) ? $_REQUEST['visitor'] : null;  
	$carno = isset($_REQUEST['carno']) ? $_REQUEST['carno'] : null;  
	$phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : null;  
	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null;  
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null;  
	$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null;  
	$day = isset($_REQUEST['day']) ? $_REQUEST['day'] : null;  
	$s_hour = isset($_REQUEST['s_hour']) ? $_REQUEST['s_hour'] : null;  
	$s_min = isset($_REQUEST['s_min']) ? $_REQUEST['s_min'] : null;  

	$s_date = $year."-".$month."-".$day;
	$s_time = $s_hour.":".$s_min;

	if ($type != "write")
	{
		if ($seqno == "")
		{
?>
	<script type="text/javascript">
		alert("해당 글이 존재하지 않습니다.");
		history.back();
	</script>
<?
			exit;
		}
	}

	if ($type == "write")
	{
		$type_title = "등록";
		$retUrl = "visit_list.php?date=$s_date";

		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_VISIT WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		$sql = "INSERT INTO DF_VISIT
				(SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, COMPANY, VISITOR, CAR_NO, PHONE, DATE, MEMO, S_TIME, E_TIME, REG_DATE)
				VALUES
				('$seq', '$writer_id', '$writer_name', '$writer', '$writer_team', '$writer_position', '$company', '$visitor', '$carno', '$phone', '$s_date', '$memo', '$s_time', '$e_time', getdate())";
	}
	else if ($type == "modify")
	{
		$type_title = "수정";
		$retUrl = "visit_list.php?date=$s_date";

		$sql = "UPDATE DF_VISIT SET 
					COMPANY = '$company', 
					VISITOR = '$visitor',
					CAR_NO = '$carno',
					PHONE = '$phone',
					DATE = '$s_date',
					MEMO = '$memo',
					S_TIME = '$s_time',
					E_TIME = '$e_time',
					MOD_NAME = '$writer_name',
					MOD_DATE = getdate()"; 
		$sql .= " WHERE 
					SEQNO = $seqno";
	}
	else if ($type == "delete")
	{
		$type_title = "삭제";
		$retUrl = "visit_list.php?date=$date";

		$sql = "DELETE FROM DF_VISIT WHERE SEQNO = $seqno";
	}

	$rs = sqlsrv_query($dbConn, $sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("<?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
	</script>
<?
		exit;
	}
	else
	{
?>
	<script language="javascript">
		alert("<?=$type_title?> 되었습니다.");
		parent.location.href = "<?=$retUrl?>";
	</script>
<?
	}
?>
