<?
	if ($prs_id == "") {
?>
		<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
		<script type="text/javascript">
			alert("로그인 상태가 아닙니다.");
			location.href="<?=LOGIN_URL.CURRENT_URL?>";
            parent.location.href="<?=LOGIN_URL.CURRENT_URL?>";
            parent.parent.location.href="<?=LOGIN_URL.CURRENT_URL?>";
		</script>
<?
		exit;
	}

	$sql = "SELECT PRS_NAME, PRS_LOGIN, PRF_ID, PRS_TEAM, PRS_POSITION, PRS_POSITION1, PRS_POSITION2, FILE_IMG, LOG_WEEKLY_CREATE FROM DF_PERSON WITH(NOLOCK) WHERE PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	if (sqlsrv_has_rows($rs) == 0)
	{
?>
		<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
		<script type="text/javascript">
			alert("로그인 상태가 아닙니다.");
			location.href="<?=LOGIN_URL.CURRENT_URL?>";
            parent.location.href="<?=LOGIN_URL.CURRENT_URL?>";
            parent.parent.location.href="<?=LOGIN_URL.CURRENT_URL?>";

		</script>
<?
		exit;
	}
	else
	{
		$record = sqlsrv_fetch_array($rs);

		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];
		$prf_id = $record['PRF_ID'];
		$prs_team = $record['PRS_TEAM'];
		$prs_position = $record['PRS_POSITION'];
		$prs_position1 = $record['PRS_POSITION1'];
		$prs_position2 = $record['PRS_POSITION2'];
		$prs_img = $record['FILE_IMG'];
		$log_weekly_create = $record['LOG_WEEKLY_CREATE'];
	}

	if ($prf_id == "6") {
?>
		<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
		<script type="text/javascript">
			alert("탈퇴회원 이용불가 페이지입니다.");
			location.href="/main.php";
            parent.location.href="/main.php";
            parent.parent.location.href="/main.php";
		</script>
<?
		exit;
	}
?>
