<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null;		
	
	
	$prs_id = "";
	$prs_name = "";
	$prs_login = "";
	$prs_team = "";
	$prs_position = "";

	$sql = "SELECT TOP 1 
				PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE 
				PRS_BEACON = '$beacon'
			ORDER BY 
				PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);	
	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$prs_id = $record['PRS_ID'];
		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];
		$prs_team = $record['PRS_TEAM'];
		$prs_position = $record['PRS_POSITION'];

		$state = "정상";
		$message = "success";	
	}
	else
	{
		$state = "오류";
		$message = "등록되지 않은 유효키입니다.";
	}

	$time_gubun = isset($_REQUEST['time_gubun']) ? $_REQUEST['time_gubun'] : null;
	if ($time_gubun == "" && date("G") < 8) { $time_gubun = "before"; }
	//if (date("G") < 8) { $time_gubun = "before"; }

	$now = date("YmdHis");								//오늘 퇴근 년월일시분초

	$ip = REMOTE_IP;									//접속IP
	$gubun = "출퇴근";

	if ($time_gubun == "before")
	{
		$today = date("Y-m-d",strtotime ("-1 day"));		//오늘 날짜
		$today2 = date("Ymd",strtotime ("-1 day"));			//오늘 날짜
		$yesterday = date("Y-m-d",strtotime ("-2 day"));	//어제 날짜
		$yesterday2 = date("Ymd",strtotime ("-2 day"));		//어제 날짜
		$next = date("Y-m-d");								//내일 날짜
		$next2 = date("Ymd");								//내일 날짜

		$now2 = $today2 . (24+substr($now,8,2)) . substr($now,10,4);
	}
	else
	{
		$today = date("Y-m-d");								//오늘 날짜
		$today2 = date("Ymd");								//오늘 날짜
		$yesterday = date("Y-m-d",strtotime ("-1 day"));	//어제 날짜
		$yesterday2 = date("Ymd",strtotime ("-1 day"));		//어제 날짜
		$next = date("Y-m-d",strtotime ("+1 day"));			//내일 날짜
		$next2 = date("Ymd",strtotime ("+1 day"));			//내일 날짜

		$time_gubun = "after";
		$now2 = $now;	
	}

	$sql = "EXEC SP_MAIN_01 '$prs_id','$prs_name','$today','$yesterday','$next'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$yesterday_gubun1 = $record['YESTERDAY_GUBUN1'];			//어제 출근
		$yesterday_gubun2 = $record['YESTERDAY_GUBUN2'];			//어제 퇴근
		$yesterday_checktime1 = $record['YESTERDAY_CHECKTIME1'];	//어제 출근	
		$yesterday_checktime2 = $record['YESTERDAY_CHECKTIME2'];	//어제 퇴근
		$yesterday_totaltime = $record['YESTERDAY_TOTALTIME'];		//어제 근무시간
		$yesterday_overtime = $record['YESTERDAY_OVERTIME'];		//어제 연장근무시간
		$yesterday_memo1 = $record['YESTERDAY_MEMO1'];				//어제 출근 수정정보
		$yesterday_memo2 = $record['YESTERDAY_MEMO2'];				//어제 퇴근 수정정보
		$today_gubun1 = $record['TODAY_GUBUN1'];					//오늘 출근
		$today_gubun2 = $record['TODAY_GUBUN2'];					//오늘 퇴근
		$today_checktime1 = $record['TODAY_CHECKTIME1'];			//오늘 출근	
		$today_checktime2 = $record['TODAY_CHECKTIME2'];			//오늘 퇴근
		$today_totaltime = $record['TODAY_TOTALTIME'];				//오늘 근무시간
		$today_memo1 = $record['TODAY_MEMO1'];						//오늘 출근 수정정보
		$today_memo2 = $record['TODAY_MEMO2'];						//오늘 퇴근 수정정보
		$today_off_time = $record['TODAY_OFF_TIME'];				//오늘 외출시간시
		$today_off_minute = $record['TODAY_OFF_MINUTE'];			//오늘 외출시간분
		$next_gubun1 = $record['NEXT_GUBUN1'];						//내일 출근
		$next_gubun2 = $record['NEXT_GUBUN2'];						//내일 퇴근
		$next_checktime1 = $record['NEXT_CHECKTIME1'];				//내일 출근	
		$next_checktime2 = $record['NEXT_CHECKTIME2'];				//내일 퇴근
	}

	$sql = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$today) ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$today_kind = $record['DATEKIND'];

	$pay4 = "N";

	if ($today_kind == "BIZ")
	{
		if ($overtime >= "1000") { $pay4 = "Y"; }
		if ($overtime >= "0400" && substr($now2,8,4) >= "2400" && substr($now2,8,4) <= "3000") { $pay4 = "Y"; }
		$pay6 = "N";
	}
	else
	{
		if ($overtime2 >= "1000") { $pay4 = "Y"; }
		if ($overtime2 >= "0700" && substr($now2,8,4) >= "2400" && substr($now2,8,4) <= "3000") { $pay4 = "Y"; }
		$pay6 = "N";
	}

	if ($today_checktime1 != "")	//오늘 출근체크 O
	{
		$sql = "INSERT INTO DF_CHECKTIME_HOUSE
				 (PRS_ID, PRS_LOGIN, PRS_NAME, DATE, BEACON, REGDATE)
				 VALUES
				 ('$prs_id','$prs_login','$prs_name','$today','$beacon',getdate())";
		$rs = sqlsrv_query($dbConn,$sql);

		$sql = "UPDATE DF_CHECKTIME SET 
					PAY4 = '$pay4', PAY6 = '$pay6' 
				WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
		$rs = sqlsrv_query($dbConn,$sql);
		$error_no = 1;
	}
	else if ($today_checktime1 == "" && $yesterday_checktime1 != "")	//오늘 출근체크 X, 어제 출근체크 O
	{
		$sql = "INSERT INTO DF_CHECKTIME_HOUSE
				 (PRS_ID, PRS_LOGIN, PRS_NAME, DATE, BEACON, REGDATE)
				 VALUES
				 ('$prs_id','$prs_login','$prs_name','$today','$beacon',getdate())";
		$rs = sqlsrv_query($dbConn,$sql);

		$sql = "UPDATE DF_CHECKTIME SET 
					PAY4 = '$pay4', PAY6 = '$pay6'
				WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
		$rs = sqlsrv_query($dbConn,$sql);
		$error_no = 2;
	}
	else 
	{
		echo "<script>alert('Error 3. 오늘 출근체크를 하지 않으셨습니다. 출퇴근 정보를 경영지원팀에 알려주세요.');</script>";
		exit;
	}
	
	if ($rs == false)
	{
		//echo "error". $error_no. ". 숙소 체크 오류입니다. Error 번호를 기억하셔서 개발팀에 문의해 주세요.";
		echo "<script>alert('error 숙소 체크 오류입니다. Error 번호를 기억하셔서 개발팀에 문의해 주세요.');</script>";
	}
	else
	{
		if ($message == "success") 
		{		
			echo "<script>alert('숙소 체크인 되었습니다.');</script>";
			exit;
		}
		else 
		{
			echo $message;
		}
		exit;
	}
?>