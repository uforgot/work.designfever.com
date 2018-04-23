<?
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null;		
	if ($prs_id == "") {
?>
		<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
		<script type="text/javascript">			
			location.href="index.php";
		</script>
<?
		exit;
	}

	$sql = "SELECT PRS_NAME, PRS_LOGIN, PRF_ID, PRS_TEAM, PRS_POSITION, FILE_IMG, LOG_WEEKLY_CREATE FROM DF_PERSON WITH(NOLOCK) WHERE PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	if (sqlsrv_has_rows($rs) == 0)
	{
?>
		<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
		<script type="text/javascript">			
			location.href="index.php";
		</script>
<?
	}
	else
	{
		$record = sqlsrv_fetch_array($rs);

		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];
		$prf_id = $record['PRF_ID'];
		$prs_team = $record['PRS_TEAM'];
		$prs_position = $record['PRS_POSITION'];
		$prs_img = $record['FILE_IMG'];
		$log_weekly_create = $record['LOG_WEEKLY_CREATE'];
		$beacon = $beacon;
	}

	if ($prf_id == "5" || $prf_id == "6") {
?>
		<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
		<script type="text/javascript">
			alert("등록대기,탈퇴회원 이용불가 페이지입니다.");			
			location.href="index.php";
		</script>
<?
	}
?>
