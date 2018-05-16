<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : null; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null; 

	$p_chk_gubun1 = isset($_REQUEST['chk_gubun1']) ? $_REQUEST['chk_gubun1'] : null; 
	$p_chk_gubun2 = isset($_REQUEST['chk_gubun2']) ? $_REQUEST['chk_gubun2'] : null; 

	$p_gubun = "출퇴근";
	$p_gubun1 = isset($_REQUEST['gubun1']) ? $_REQUEST['gubun1'] : null;
	$p_gubun2 = isset($_REQUEST['gubun2']) ? $_REQUEST['gubun2'] : null;

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

	if ($p_chk_gubun1 == "Y" || $p_gubun1 == "")
	{
?>
	<script type="text/javascript">
		alert("출근 여부가 올바르지 않습니다.");
		self.close();
	</script>
<?
		exit;
	}

	if ($p_chk_gubun2 == "Y" || $p_gubun2 == "")
	{
?>
	<script type="text/javascript">
		alert("퇴근 여부가 올바르지 않습니다.");
		self.close();
	</script>
<?
		exit;
	}

	$sql = "SELECT 
				PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, PRS_TEAM,
				DATE, CHK_OFF1, CHK_OFF2, CHK_OFF3, CHK_OFF4, CHK_OFF5, 
				STARTTIME, ENDTIME, MEMO, OUT_CHK, BUSINESS_TRIP,
				OFF_STARTTIME1, OFF_ENDTIME1, OFF_STARTTIME2, OFF_ENDTIME2, OFF_STARTTIME3, OFF_ENDTIME3, 
				OFF_STARTTIME4, OFF_ENDTIME4, OFF_STARTTIME5, OFF_ENDTIME5, EDIT_OK, CONVERT(CHAR(20),REG_DATE,120) AS REG_DATE,  CONVERT(CHAR(20),OK_DATE,120) AS OK_DATE 
			FROM 
				DF_CHECKTIME_EDIT WITH(NOLOCK)
			WHERE 
				SEQNO = $seqno";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$p_id = $record['PRS_ID'];
		$p_login = $record['PRS_LOGIN'];
		$p_name = $record['PRS_NAME'];
		$p_position = $record['PRS_POSITION'];
		$p_team = $record['PRS_TEAM'];
		$p_date = $record['DATE'];
		$p_chk_off1 = $record['CHK_OFF1'];
		$p_chk_off2 = $record['CHK_OFF2'];
		$p_chk_off3 = $record['CHK_OFF3'];
		$p_chk_off4 = $record['CHK_OFF4'];
		$p_chk_off5 = $record['CHK_OFF5'];
		$p_starttime = $record['STARTTIME'];
		$p_endtime = $record['ENDTIME'];
		$p_memo = $record['MEMO'];
		$p_out_chk = $record['OUT_CHK'];
		$p_business_trip = $record['BUSINESS_TRIP'];
		$p_off1_starttime = $record['OFF_STARTTIME1'];
		$p_off1_endtime = $record['OFF_ENDTIME1'];
		$p_off2_starttime = $record['OFF_STARTTIME2'];
		$p_off2_endtime = $record['OFF_ENDTIME2'];
		$p_off3_starttime = $record['OFF_STARTTIME3'];
		$p_off3_endtime = $record['OFF_ENDTIME3'];
		$p_off4_starttime = $record['OFF_STARTTIME4'];
		$p_off4_endtime = $record['OFF_ENDTIME4'];
		$p_off5_starttime = $record['OFF_STARTTIME5'];
		$p_off5_endtime = $record['OFF_ENDTIME5'];
		$p_edit_ok = $record['EDIT_OK'];
		$p_ok_date = $record['OK_DATE'];
		$p_reg_date = $record['REG_DATE'];
	}

	$ip = REMOTE_IP;									//접속IP
	$memo = $prs_position ." ".  $prs_name;				//입력자 정보
	$now = date("YmdHis");								//입력 시간

	$yesterday = date("Y-m-d",strtotime($p_date ." -1 day"));	//어제 날짜
	$next = date("Y-m-d",strtotime($p_date ." +1 day"));		//내일 날짜

	$yesterday_gubun1 = "";
	$yesterday_gubun2 = "";
	$yesterday_checktime1 = "";
	$yesterday_checktime2 = "";
	$yesterday_totaltime = "";
	$today_gubun1 = "";
	$today_gubun2 = "";
	$today_checktime1 = "";
	$today_checktime2 = "";
	$today_totaltime = "";
	$today_memo1 = "";
	$today_memo2 = "";

	$gubun = $p_gubun;
	$gubun1 = $p_gubun1;
	$gubun2 = $p_gubun2;

	$pay1 = "N";
	$pay2 = "N";
	$pay3 = "N";
	$pay4 = "N";
	$pay5 = "N";
	$pay6 = "N";

	$sql = "SELECT 
				CHECKTIME1, CHECKTIME2
			FROM 
				DF_CHECKTIME WITH(NOLOCK)
			WHERE 
				DATE = '$p_date' AND PRS_ID = '$p_id'";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);

	$md = "N";
	if (sizeof($record) > 0)
	{
		$md = "Y";
		$checktime1 = $record['CHECKTIME1'];
		$checktime2 = $record['CHECKTIME2'];
	}

	if ($p_chk_gubun1 == "Y")
	{
		$checktime1 = str_replace("-","",$p_date) . $p_starttime ."00";
	}
	if ($p_chk_gubun2 == "Y")
	{
		$checktime2 = str_replace("-","",$p_date) . $p_endtime ."00";
	}

	$p_gubun1_hour = substr($p_starttime,0,2);
	$p_gubun1_minute = substr($p_starttime,2,2);
	$p_gubun2_hour = substr($p_endtime,0,2);
	$p_gubun2_minute = substr($p_endtime,2,2);

	$totaltime = "0000";
	$overtime = "0000";
	$undertime = "0000";
	if ($p_chk_gubun1 == "N")
	{
		$gubun1 = "";
	}
	if ($p_chk_gubun2 == "N")
	{
		$gubun2 = "";
	}

	$sql = "EXEC SP_MAIN_01 '$p_id','$p_name','$p_date','$yesterday','$next'";
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
	}

	//근무시간 계산
	if ($p_gubun1_hour < "08" && ($p_gubun1 == "1" || $p_gubun1 == "6"))
	{
		$p_gubun1_hour2 = "08";
		$p_gubun1_minute2 = "00";
	}
	else if ($p_gubun1_hour < "13" && ($p_gubun1 == "4" || $p_gubun1 == "8"))
	{
		$p_gubun1_hour2 = "13";
		$p_gubun1_minute2 = "00";
	}
	else
	{
		$p_gubun1_hour2 = $p_gubun1_hour;
		$p_gubun1_minute2 = $p_gubun1_minute;
	}

	if ($p_gubun2_minute < $p_gubun1_minute)
	{
		$totalhour = $p_gubun2_hour - $p_gubun1_hour - 1;
		$totalmin = $p_gubun2_minute - $p_gubun1_minute + 60;
	}
	else
	{
		$totalhour = $p_gubun2_hour - $p_gubun1_hour;
		$totalmin = $p_gubun2_minute - $p_gubun1_minute;
	}

	if ($p_gubun2_minute < $p_gubun1_minute2)
	{
		$totalhour2 = $p_gubun2_hour - $p_gubun1_hour2 - 1;
		$totalmin2 = $p_gubun2_minute - $p_gubun1_minute2 + 60;
	}
	else
	{
		$totalhour2 = $p_gubun2_hour - $p_gubun1_hour2;
		$totalmin2 = $p_gubun2_minute - $p_gubun1_minute2;
	}

	$totalhour2 = $totalhour2 - $today_off_time;
	$totalmin2 = $totalmin2 - $today_off_minute;

	if ($totalmin2 < 0) 
	{
		$totalhour2 = $totalhour2 - 1;
		$totalmin2 = $totalmin2 + 60;
	}

	if ($totalhour2 < 0)
	{
		$totalhour2 = 0;
		$totalmin2 = 0;
	}

	//외출시간 계산
	if ($p_chk_off == "Y")
	{
		$off_hour1 = substr($p_off_starttime,0,2);
		$off_minute1 = substr($p_off_starttime,2,2);
		$off_hour2 = substr($p_off_endtime,0,2);
		$off_minute2 = substr($p_off_endtime,2,2);

		if ($off_hour1 < "08") { $re_off_hour1 = "08"; $re_off_minute1 = "00"; } else { $re_off_hour1 = $off_hour1; $re_off_minute1 = $off_minute1; }
		if ($off_hour2 < "08") { $re_off_hour2 = "08"; $re_off_minute2 = "00"; } else { $re_off_hour2 = $off_hour2; $re_off_minute2 = $off_minute2; }

		if ($off_minute2 < $off_minute1)
		{
			$off_totalhour = $re_off_hour2 - $re_off_hour1 - 1;
			$off_totalmin = $re_off_minute2 - $re_off_minute1 + 60;
		}
		else
		{
			$off_totalhour = $re_off_hour2 - $re_off_hour1;
			$off_totalmin = $re_off_minute2 - $re_off_minute1;
		}

		if (strlen($off_totalhour) == 1) { $off_totalhour = "0". $off_totalhour; }
		if (strlen($off_totalmin) == 1) { $off_totalmin = "0". $off_totalmin; }

		$off_total = $off_totalhour.$off_totalmin;

		if (strlen($off_hour1) == 1) { $off_hour1 = "0". $off_hour1; }
		if (strlen($off_minute1) == 1) { $off_minute1 = "0". $off_minute1; }
		if (strlen($off_hour2) == 1) { $off_hour2 = "0". $off_hour2; }
		if (strlen($off_minute2) == 1) { $off_minute2 = "0". $off_minute2; }

		$off_time1 = $off_hour1 . $off_minute1;
		$off_time2 = $off_hour2 . $off_minute2;

		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_CHECKTIME_OFF WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$maxno = $result[0] + 1;

		$sql = "INSERT INTO DF_CHECKTIME_OFF
				(SEQNO, PRS_ID, PRS_LOGIN, DATE, STARTTIME, ENDTIME, TOTALTIME, CHECKIP1, REGDATE, MEMO1, MEMO2)
				VALUES
				('$maxno','$p_id','$p_login','$p_date','$off_time1','$off_time2','$off_total','$ip',getdate(),'$memo','$now')";
		$rs = sqlsrv_query($dbConn,$sql);
	}

	//외출시간 sum select
	$sql = "SELECT 
				ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600,0), 
				ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) %3600 /60,0) 
			FROM DF_CHECKTIME_OFF WHERE DATE='$p_date' AND PRS_ID='$p_id'";
	$rs = sqlsrv_query($dbConn,$sql);
	$result = sqlsrv_fetch_array($rs);

	$total_off_time = $result[0];
	$total_off_minute = $result[1];

	//totaltime2 재계산
	$totalhour2 = $totalhour2 - $total_off_time;
	$totalmin2 = $totalmin2 - $total_off_minute;

	if ($totalmin2 < 0) 
	{
		$totalhour2 = $totalhour2 - 1;
		$totalmin2 = $totalmin2 + 60;
	}

	if ($totalhour2 < 0)
	{
		$totalhour2 = 0;
		$totalmin2 = 0;
	}

	if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
	if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
	if (strlen($totalhour2) == 1) { $totalhour2 = "0". $totalhour2; }
	if (strlen($totalmin2) == 1) { $totalmin2 = "0". $totalmin2; }
	$totaltime = $totalhour . $totalmin;
	$totaltime2 = $totalhour2 . $totalmin2;

	$sql = "SELECT DATEKIND, DAY FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$p_date) ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$today_kind = $record['DATEKIND'];
	$today_day = $record['DAY'];

	$sql = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$yesterday) ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$yesterday_kind = $record['DATEKIND'];

	//전날 근무시간과 비교 - 오늘의 기준근로시간
	if ($yesterday_kind == "BIZ")
	{
		if ($yesterday_overtime >= "0700") { $d_time = "0600"; }
		else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0700"; }
		else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0800"; }
		else { $d_time = "0900";}

		$max_overtime = "0700";
		$min_overtime = "0400";
	}
	else
	{
		if ($yesterday_overtime >= "0900") { $d_time = "0600"; }
		else if ($yesterday_overtime >= "0800" && $yesterday_overtime < "0900" ) { $d_time = "0700"; }
		else if ($yesterday_overtime >= "0700" && $yesterday_overtime < "0800" ) { $d_time = "0800"; }
		else { $d_time = "0900";}

		$max_overtime = "0900";
		$min_overtime = "0600";
	}

	$overtime = "0000";
	$undertime = "0000";

	if ($p_gubun1 == "4" || $p_gubun1 == "8")	{	//오전반차 - 기준근로시간 5시간
		if ($yesterday_kind == "BIZ")
		{
			if ($yesterday_overtime >= "0700") { $d_time = "0200"; }
			else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0300"; }
			else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0400"; }
			else { $d_time = "0500";}

			$max_overtime = "0700";
			$min_overtime = "0400";
		}
		else
		{
			if ($yesterday_overtime >= "0900") { $d_time = "0200"; }
			else if ($yesterday_overtime >= "0800" && $yesterday_overtime < "0900" ) { $d_time = "0300"; }
			else if ($yesterday_overtime >= "0700" && $yesterday_overtime < "0800" ) { $d_time = "0400"; }
			else { $d_time = "0500";}

			$max_overtime = "0900";
			$min_overtime = "0600";
		}

		if ($today_kind == "BIZ")	//평일
		{
			//전날 근무시간과 비교
			if ($yesterday_overtime >= $max_overtime)
			{
				if ($totaltime2 >= $d_time)
				{
					if ($totaltime2 > $d_time) { $gubun2 = "3"; }

					$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
					$overmin = substr($totaltime2,2,2);
					if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
					if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
					$overtime = $overhour . $overmin;

					$undertime = "0000";
				}
				else if ($totaltime2 < $d_time)
				{
					$gubun2 = "2";

					$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
					$undermin = 60 - substr($totaltime2,2,2);

					if ($undermin == 60) 
					{  
						$underhour = $underhour + 1;
						$undermin = "00";
					}
					if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
					if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
					$undertime = $underhour . $undermin;

					$overtime = "0000";
				}
			}
			else if ($yesterday_overtime < $min_overtime)
			{
				if ($totaltime2 >= $d_time)
				{
					if ($totaltime2 > $d_time) { $gubun2 = "3"; }

					$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
					$overmin = substr($totaltime2,2,2);
					if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
					if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
					$overtime = $overhour . $overmin;

					$undertime = "0000";
				}
				else if ($totaltime2 < $d_time)
				{
					$gubun2 = "2";

					$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
					$undermin = 60 - substr($totaltime2,2,2);

					if ($undermin == 60) 
					{  
						$underhour = $underhour + 1;
						$undermin = "00";
					}
					if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
					if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
					$undertime = $underhour . $undermin;

					$overtime = "0000";
				}
			}
			else
			{
				$d_hour = substr($d_time,0,2) - 1;
				$d_min = 60 - substr($yesterday_overtime,2,2);

				if ($d_min == 60)
				{
					$d_hour = $d_hour + 1;
					$d_min = "00";
				}
				if (strlen($d_hour) == 1) { $d_hour = "0". $d_hour; }
				if (strlen($d_min) == 1) { $d_min = "0". $d_min; }
				$d_time = $d_hour . $d_min;

				if ($totaltime2 > $d_time)
				{
					if ($totaltime2 > $d_time) { $gubun2 = "3"; }

					if (substr($totaltime2,2,2) < substr($d_time,2,2))
					{
						$overhour = substr($totaltime2,0,2) - substr($d_time,0,2) - 1;
						$overmin = 60 + substr($totaltime2,2,2) - substr($d_time,2,2);
					}
					else
					{
						$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
						$overmin = substr($totaltime2,2,2) - substr($d_time,2,2);
					}
					if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
					if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
					$overtime = $overhour . $overmin;

					$undertime = "0000";
				}
				else if ($totaltime2 < $d_time)
				{
					$gubun2 = "2";

					if (substr($d_time,2,2) < substr($totaltime2,2,2))
					{
						$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
						$undermin = 60 + substr($d_time,2,2) - substr($totaltime2,2,2);
					}
					else
					{
						$underhour = substr($d_time,0,2) - substr($totaltime2,0,2);
						$undermin = substr($d_time,2,2) - substr($totaltime2,2,2);
					}

					if ($undermin == 60) 
					{  
						$underhour = $underhour + 1;
						$undermin = "00";
					}
					if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
					if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
					$undertime = $underhour . $undermin;

					$overtime = "0000";
				}
				else
				{
					$gubun2 = "2";

					$overtime = "0000";
					$undertime = "0000";
				}
			}
		}
		else
		{
			$gubun2 = "3";
			$overtime = $totaltime;
			$undertime = "0000";
		}
	}
	else
	{
		if ($yesterday_kind == "BIZ")
		{
			if ($yesterday_overtime >= "0700") { $d_time = "0600"; }
			else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0700"; }
			else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0800"; }
			else { $d_time = "0900";}

			$max_overtime = "0700";
			$min_overtime = "0400";
		}
		else
		{
			if ($yesterday_overtime >= "0900") { $d_time = "0600"; }
			else if ($yesterday_overtime >= "0800" && $yesterday_overtime < "0900" ) { $d_time = "0700"; }
			else if ($yesterday_overtime >= "0700" && $yesterday_overtime < "0800" ) { $d_time = "0800"; }
			else { $d_time = "0900";}

			$max_overtime = "0900";
			$min_overtime = "0600";
		}

		if ($today_kind == "BIZ")	//평일
		{
			//전날 근무시간과 비교
			if ($yesterday_overtime >= $max_overtime)
			{
				if ($totaltime2 >= $d_time)
				{
					if ($totaltime2 > $d_time) { $gubun2 = "3"; }

					$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
					$overmin = substr($totaltime2,2,2);
					if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
					if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
					$overtime = $overhour . $overmin;

					$undertime = "0000";
				}
				else if ($totaltime2 < $d_time)
				{
					$gubun2 = "2";

					$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
					$undermin = 60 - substr($totaltime2,2,2);

					if ($undermin == 60) 
					{  
						$underhour = $underhour + 1;
						$undermin = "00";
					}
					if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
					if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
					$undertime = $underhour . $undermin;

					$overtime = "0000";
				}
			}
			else if ($yesterday_overtime < $min_overtime)
			{
				if ($totaltime2 >= $d_time)
				{
					if ($totaltime2 > $d_time) { $gubun2 = "3"; }

					$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
					$overmin = substr($totaltime2,2,2);
					if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
					if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
					$overtime = $overhour . $overmin;

					$undertime = "0000";
				}
				else if ($totaltime2 < $d_time)
				{
					$gubun2 = "2";

					$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
					$undermin = 60 - substr($totaltime2,2,2);

					if ($undermin == 60) 
					{  
						$underhour = $underhour + 1;
						$undermin = "00";
					}
					if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
					if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
					$undertime = $underhour . $undermin;

					$overtime = "0000";
				}
			}
			else
			{
				$d_hour = substr($d_time,0,2) - 1;
				$d_min = 60 - substr($yesterday_overtime,2,2);

				if ($d_min == 60)
				{
					$d_hour = $d_hour + 1;
					$d_min = "00";
				}
				if (strlen($d_hour) == 1) { $d_hour = "0". $d_hour; }
				if (strlen($d_min) == 1) { $d_min = "0". $d_min; }
				$d_time = $d_hour . $d_min;

				if ($totaltime2 > $d_time)
				{
					if ($totaltime2 > $d_time) { $gubun2 = "3"; }

					if (substr($totaltime2,2,2) < substr($d_time,2,2))
					{
						$overhour = substr($totaltime2,0,2) - substr($d_time,0,2) - 1;
						$overmin = 60 + substr($totaltime2,2,2) - substr($d_time,2,2);
					}
					else
					{
						$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
						$overmin = substr($totaltime2,2,2) - substr($d_time,2,2);
					}
					if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
					if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
					$overtime = $overhour . $overmin;

					$undertime = "0000";
				}
				else if ($totaltime2 < $d_time)
				{
					$gubun2 = "2";

					if (substr($d_time,2,2) < substr($totaltime2,2,2))
					{
						$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
						$undermin = 60 + substr($d_time,2,2) - substr($totaltime2,2,2);
					}
					else
					{
						$underhour = substr($d_time,0,2) - substr($totaltime2,0,2);
						$undermin = substr($d_time,2,2) - substr($totaltime2,2,2);
					}

					if ($undermin == 60) 
					{  
						$underhour = $underhour + 1;
						$undermin = "00";
					}
					if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
					if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
					$undertime = $underhour . $undermin;

					$overtime = "0000";
				}
				else
				{
					$gubun2 = "2";

					$overtime = "0000";
					$undertime = "0000";
				}
			}
		}
		else
		{
			$gubun2 = "3";
			$overtime = $totaltime;
			$undertime = "0000";
		}
	}

	if ($today_kind == "BIZ")
	{
		if ($overtime >= "0300") { $pay2 = "Y"; }
		if ($overtime >= "0400") { $pay3 = "Y"; }
		if ($overtime >= "1000") { $pay4 = "Y"; }
		if ($overtime >= "0400" && substr($checktime2,8,4) >= "2400" && substr($checktime2,8,4) <= "3000") { $pay4 = "Y"; }
	
	}
	else
	{
		if ($yesterday_kind == "BIZ")
		{
			if ($yesterday_overtime >= "0700") { $overtime2 = $overtime + "0300"; }
			else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $overtime2 = $overtime + "0200"; }
			else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $overtime2 = $overtime + "0100"; }
			else { $overtime2 = $overtime;}
		}
		else
		{
			if ($yesterday_overtime >= "0800") { $overtime2 = $overtime + "0300"; }
			else if ($yesterday_overtime >= "0800" && $yesterday_overtime < "0900" ) { $overtime2 = $overtime + "0200"; }
			else if ($yesterday_overtime >= "0700" && $yesterday_overtime < "0800" ) { $overtime2 = $overtime + "0100"; }
			else { $overtime2 = $overtime;}
		}

		if ($overtime2 >= "0400") { $pay1 = "Y"; }
		if ($overtime2 >= "0600") { $pay2 = "Y"; }
		if ($overtime2 >= "0700") { $pay3 = "Y"; }
		if ($overtime2 >= "1000") { $pay4 = "Y"; }
		if ($overtime2 >= "0700" && substr($checktime2,8,4) >= "2400" && substr($checktime2,8,4) <= "3000") { $pay4 = "Y"; }
	}

	$out_chk = "N";
	$business_trip = "N";

	if ($p_out_chk == "Y") //파견
	{
		if ($gubun1 == "4" || $gubun1 == "8") {
			$pay1 = "N";
		} else {
			$pay1 = "Y";
		}
		$pay4 = "N";
		$out_chk = "Y";
		$pay5 = "Y";
		$pay6 = "Y";

		if ($gubun2 == null || $gubun2 == "") { $pay6 = "N"; }
	}

	if ($md == "Y")		// update
	{
		$sql = "INSERT INTO DF_CHECKTIME_LOG 
				SELECT *, getdate() 
				FROM DF_CHECKTIME
				WHERE PRS_ID = '$p_id' AND DATE = '$p_date'";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script language="javascript">
			alert("error1_1. 수정 실패하였습니다. 개발팀에 문의하세요.");
		</script>
<?
			exit;
		}

		$md_update = "";
		if ($p_chk_gubun1 == "Y") 
		{
			$md_update = $md_update . ", REGDATE = getdate()";
		}
		if ($p_chk_gubun2 == "Y") 
		{
			$md_update = $md_update . ", REGDATE2 = getdate()";
		}

		$sql = "UPDATE DF_CHECKTIME SET
					GUBUN = '$gubun', 
					GUBUN1 = '$gubun1', 
					GUBUN2 = '$gubun2', 
					CHECKTIME1 = '$checktime1', 
					CHECKTIME2 = '$checktime2', 
					TOTALTIME = '$totaltime', 
					OVERTIME = '$overtime', 
					UNDERTIME = '$undertime', 
					MEMO1 = '$memo',
					MEMO2 = '$now', 
					PAY1 = '$pay1', 
					PAY2 = '$pay2', 
					PAY3 = '$pay3', 
					PAY4 = '$pay4', 
					PAY5 = '$pay5',
					PAY6 = '$pay6',
					OUT_CHK = '$out_chk',
					BUSINESS_TRIP = '$business_trip'". $md_update ."
				WHERE PRS_ID = '$p_id' AND DATE = '$p_date'";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script language="javascript">
			alert("error2_1. 수정 실패하였습니다. 개발팀에 문의하세요.");
		</script>
<?
			exit;
		}
	}
	else				// insert
	{
		$sql = "SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$p_id' AND DATE = '$p_date'";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$check = $result[0];

		if ($check == 0)
		{
			$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_CHECKTIME WITH(NOLOCK)";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$maxno = $result[0] + 1;

			$sql = "INSERT INTO DF_CHECKTIME
					(SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, TOTALTIME, OVERTIME, UNDERTIME, CHECKIP1, CHECKIP2, MEMO1, MEMO2, PAY1, PAY2, PAY3, PAY4, PAY5, PAY6, OUT_CHK, BUSINESS_TRIP, FLAG, REGDATE)
					VALUES
					('$maxno','$p_id','$p_login','$p_name','$p_date','$gubun','$gubun1','$gubun2','$checktime1','$checktime2','$totaltime','$overtime','$undertime','$ip','$ip','$memo', '$now','$pay1','$pay2','$pay3','$pay4','$pay5','$pay6','$out_chk','$business_trip','admin',getdate())";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error2_2. 수정 실패하였습니다. 개발팀에 문의하세요.");
			</script>
<?
				exit;
			}
		}
	}

	//수정일 이후 근태기록 확인 및 수정
	//	- 출근인정시간대, 연장근무 등 재계산 / 주말까지
	//$sql = "SELECT TOP 1 DATE FROM DF_CHECKTIME WHERE PRS_ID = '$p_id' AND DATE LIKE '". substr($p_date,0,7) ."%' ORDER BY DATE DESC";
	$maxday = 0;

	$sql = "SELECT TOP 1 DATE FROM DF_CHECKTIME WHERE PRS_ID = '$p_id' AND DATE > '". $p_date ."' ORDER BY DATE";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($record = sqlsrv_fetch_array($rs))
	{
		$min_date = $record['DATE'];
		$minday = datediff("d",$p_date,$min_date);
	}

	$sql = "SELECT TOP 1 DATE FROM DF_CHECKTIME WHERE PRS_ID = '$p_id' AND DATE > '". $p_date ."' ORDER BY DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($record = sqlsrv_fetch_array($rs))
	{
		$max_date = $record['DATE'];
		$maxday = datediff("d",$p_date,$max_date);
	}

	if ($minday < 3 && $maxday > 0)
	{
		for ($i=1; $i<=$maxday; $i++)
		{
			$sql = "SELECT TOP $maxday 
						A.DATEKIND, B.DATE, B.GUBUN1, B.GUBUN2, B.CHECKTIME1, B.CHECKTIME2, B.TOTALTIME, B.OVERTIME, B.UNDERTIME, B.OUT_CHK, B.BUSINESS_TRIP,
						(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600,0) FROM DF_CHECKTIME_OFF WHERE DATE=B.DATE AND PRS_ID=$p_id) AS NEXT_OFFTIME, 
						(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) %3600 /60,0) FROM DF_CHECKTIME_OFF WHERE DATE=B.DATE AND PRS_ID=$p_id) AS NEXT_OFFMINUTE, 
						(SELECT DATEKIND FROM HOLIDAY WHERE DATE = DATEADD(d,-1,A.DATE)) AS PREV_DATEKIND, 
						(SELECT OVERTIME FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE = DATEADD(d,-1,B.DATE) AND PRS_ID=$p_id) AS PREV_OVERTIME 
					FROM 
						HOLIDAY A WITH(NOLOCK) FULL JOIN 
						(SELECT DATE, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, TOTALTIME, OVERTIME, UNDERTIME, OUT_CHK, BUSINESS_TRIP FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID=$p_id) B
					ON 
						A.DATE = REPLACE(B.DATE,'-','')
					WHERE 
						A.DATE = DATEADD(day,$i,'$p_date')
					ORDER BY A.DATE";

			$rs = sqlsrv_query($dbConn,$sql);

			while ($record = sqlsrv_fetch_array($rs))
			{
				$next_datekind = $record['DATEKIND'];
				$next_date = $record['DATE'];
				$next_gubun1 = $record['GUBUN1'];
				$next_gubun2 = $record['GUBUN2'];
				$next_checktime1 = $record['CHECKTIME1'];
				$next_checktime2 = $record['CHECKTIME2'];
				$next_totaltime = $record['TOTALTIME'];
				$next_overtime = $record['OVERTIME'];
				$next_undertime = $record['UNDERTIME'];
				$next_out_chk = $record['OUT_CHK'];
				$next_business_trip = $record['BUSINESS_TRIP'];
				$next_off_time =  $record['NEXT_OFFTIME'];
				$next_off_minute =  $record['NEXT_OFFMINUTE'];
				$prev_datekind = $record['PREV_DATEKIND'];
				$prev_overtime = $record['PREV_OVERTIME'];

				$next_pay1 = "N";
				$next_pay2 = "N";
				$next_pay3 = "N";
				$next_pay4 = "N";

				$next_totalhour = substr($next_totaltime,0,2) - $next_off_time;
				$next_totalmin = substr($next_totaltime,2,2) - $next_off_minute;

				if ($next_totalmin < 0) 
				{
					$next_totalhour = $next_totalhour - 1;
					$next_totalmin = $next_totalmin + 60;
				}

				if ($next_totalhour < 0)
				{
					$next_totalhour = 0;
					$next_totalmin = 0;
				}

				if (strlen($next_totalhour) == 1) { $next_totalhour = "0". $next_totalhour; }
				if (strlen($next_totalmin) == 1) { $next_totalmin = "0". $next_totalmin; }
				$next_totaltime = $next_totalhour . $next_totalmin;

				if ($next_totaltime > "0000")
				{
					if ($next_datekind == "BIZ")
					{
						if ($prev_datekind == "BIZ")
						{
							if ($prev_overtime >= "0700")						//근무시간9시간+연장근무+7시간 - 출근인정시간
							{
								$start_time = "1400";
							}
							else if ($prev_overtime >= "0600")					//근무시간9시간+연장근무+6시간 - 출근인정시간
							{
								//$start_time = "1300";
								$start_time = "13". substr($prev_overtime,2,2);
							}
							else if ($prev_overtime >="0500")					//근무시간9시간+연장근무+5시간 - 출근인정시간
							{
								//$start_time = "1200";
								$start_time = "12". substr($prev_overtime,2,2);
							}
							else if ($prev_overtime >="0400")					//근무시간9시간+연장근무+4시간 - 출근인정시간
							{
								//$start_time = "1100";
								$start_time = "11". substr($prev_overtime,2,2);
							}
							else
							{
								$start_time = "1100";					//출근인정시간대(0800~1100)
							}
						}
						else
						{
							if ($prev_overtime >= "0900")						//휴일근무9시간 - 출근인정시간
							{
								$start_time = "1400";
							}
							else if ($prev_overtime >= "0800")					//휴일근무8시간 - 출근인정시간
							{
								//$start_time = "1300";
								$start_time = "13". substr($prev_overtime,2,2);
							}
							else if ($prev_overtime >="0700")					//휴일근무7시간 - 출근인정시간
							{
								//$start_time = "1200";
								$start_time = "12". substr($prev_overtime,2,2);
							}
							else if ($prev_overtime >="0600")					//휴일근무6시간 - 출근인정시간
							{
								//$start_time = "1100";
								$start_time = "11". substr($prev_overtime,2,2);
							}
							else
							{
								$start_time = "1100";					//출근인정시간대(0800~1100)
							}
						}

						if (substr($next_checktime1,8,4) <= $start_time)			//출근인정시간대(1) 이후 출근 오전반차(8)
						{
							$next_gubun1 = "1";
						}
						else
						{
							$next_gubun1 = "8";
						}

						if ($next_gubun2 == "5" || $next_gubun2 == "9")	{	//오후반차 - 기준근로시간 3시간
							if ($prev_datekind == "BIZ")
							{
								if ($prev_overtime >= "0700") { $d_time = "0000"; }
								else if ($prev_overtime >= "0600" && $prev_overtime < "0700" ) { $d_time = "0100"; }
								else if ($prev_overtime >= "0500" && $prev_overtime < "0600" ) { $d_time = "0200"; }
								else { $d_time = "0300";}

								$max_overtime = "0700";
								$min_overtime = "0500";
							}
							else
							{
								if ($prev_overtime >= "0900") { $d_time = "0000"; }
								else if ($prev_overtime >= "0800" && $prev_overtime < "0900" ) { $d_time = "0100"; }
								else if ($prev_overtime >= "0700" && $prev_overtime < "0800" ) { $d_time = "0200"; }
								else { $d_time = "0300";}

								$max_overtime = "0900";
								$min_overtime = "0700";
							}

							//전날 근무시간과 비교
							if ($prev_overtime >= $max_overtime)
							{
								if ($next_totaltime >= $d_time)
								{
									$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
									$next_overmin = substr($next_totaltime,2,2);
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
									$next_undermin = 60 - substr($next_totaltime,2,2);

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
							}
							else if ($prev_overtime < $min_overtime)
							{
								if ($next_totaltime >= $d_time)
								{
									$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
									$next_overmin = substr($next_totaltime,2,2);
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
									$next_undermin = 60 - substr($next_totaltime,2,2);

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
							}
							else
							{
								$d_hour = substr($d_time,0,2) - 1;
								$d_min = 60 - substr($prev_overtime,2,2);

								if ($d_min == 60)
								{
									$d_hour = $d_hour + 1;
									$d_min = "00";
								}
								if (strlen($d_hour) == 1) { $d_hour = "0". $d_hour; }
								if (strlen($d_min) == 1) { $d_min = "0". $d_min; }
								$d_time = $d_hour . $d_min;

								if ($next_totaltime > $d_time)
								{
									if (substr($next_totaltime,2,2) < substr($d_time,2,2))
									{
										$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2) - 1;
										$next_overmin = 60 + substr($next_totaltime,2,2) - substr($d_time,2,2);
									}
									else
									{
										$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
										$next_overmin = substr($next_totaltime,2,2) - substr($d_time,2,2);
									}
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									if (substr($d_time,2,2) < substr($next_totaltime,2,2))
									{
										$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
										$next_undermin = 60 + substr($d_time,2,2) - substr($next_totaltime,2,2);
									}
									else
									{
										$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2);
										$next_undermin = substr($d_time,2,2) - substr($next_totaltime,2,2);
									}

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
								else
								{
									$next_overtime = "0000";
									$next_undertime = "0000";
								}
							}
						}
						else if ($next_gubun1 == "4" ||  $next_gubun1 == "8")	{	//오전반차 - 기준근로시간 5시간
							if ($prev_datekind == "BIZ")
							{
								if ($prev_overtime >= "0700") { $d_time = "0200"; }
								else if ($prev_overtime >= "0600" && $prev_overtime < "0700" ) { $d_time = "0300"; }
								else if ($prev_overtime >= "0500" && $prev_overtime < "0600" ) { $d_time = "0400"; }
								else { $d_time = "0500";}

								$max_overtime = "0700";
								$min_overtime = "0500";
							}
							else
							{
								if ($prev_overtime >= "0900") { $d_time = "0200"; }
								else if ($prev_overtime >= "0800" && $prev_overtime < "0900" ) { $d_time = "0300"; }
								else if ($prev_overtime >= "0700" && $prev_overtime < "0800" ) { $d_time = "0400"; }
								else { $d_time = "0500";}

								$max_overtime = "0900";
								$min_overtime = "0700";
							}

							//전날 근무시간과 비교
							if ($prev_overtime >= $max_overtime)
							{
								if ($next_totaltime >= $d_time)
								{
									if ($next_totaltime > $d_time) { $next_gubun2 = "3"; }

									$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
									$next_overmin = substr($next_totaltime,2,2);
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_gubun2 = "2";

									$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
									$next_undermin = 60 - substr($next_totaltime,2,2);

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
							}
							else if ($prev_overtime < $min_overtime)
							{
								if ($next_totaltime >= $d_time)
								{
									if ($next_totaltime > $d_time) { $next_gubun2 = "3"; }

									$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
									$next_overmin = substr($next_totaltime,2,2);
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_gubun2 = "2";

									$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
									$next_undermin = 60 - substr($next_totaltime,2,2);

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
							}
							else
							{
								$d_hour = substr($d_time,0,2) - 1;
								$d_min = 60 - substr($prev_overtime,2,2);

								if ($d_min == 60)
								{
									$d_hour = $d_hour + 1;
									$d_min = "00";
								}
								if (strlen($d_hour) == 1) { $d_hour = "0". $d_hour; }
								if (strlen($d_min) == 1) { $d_min = "0". $d_min; }
								$d_time = $d_hour . $d_min;

								if ($next_totaltime > $d_time)
								{
									if ($next_totaltime > $d_time) { $next_gubun2 = "3"; }

									if (substr($next_totaltime,2,2) < substr($d_time,2,2))
									{
										$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2) - 1;
										$next_overmin = 60 + substr($next_totaltime,2,2) - substr($d_time,2,2);
									}
									else
									{
										$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
										$next_overmin = substr($next_totaltime,2,2) - substr($d_time,2,2);
									}
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_gubun2 = "2";

									if (substr($d_time,2,2) < substr($next_totaltime,2,2))
									{
										$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
										$next_undermin = 60 + substr($d_time,2,2) - substr($next_totaltime,2,2);
									}
									else
									{
										$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2);
										$next_undermin = substr($d_time,2,2) - substr($next_totaltime,2,2);
									}

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
								else
								{
									$next_gubun2 = "2";

									$next_overtime = "0000";
									$next_undertime = "0000";
								}
							}
						}
						else
						{
							//전날 근무시간과 비교 - 오늘의 기준근로시간
							if ($prev_datekind == "BIZ")
							{
								if ($prev_overtime >= "0700") { $d_time = "0600"; }
								else if ($prev_overtime >= "0600" && $prev_overtime < "0700" ) { $d_time = "0700"; }
								else if ($prev_overtime >= "0500" && $prev_overtime < "0600" ) { $d_time = "0800"; }
								else { $d_time = "0900";}

								$max_overtime = "0700";
								$min_overtime = "0500";
							}
							else
							{
								if ($prev_overtime >= "0900") { $d_time = "0600"; }
								else if ($prev_overtime >= "0800" && $prev_overtime < "0900" ) { $d_time = "0700"; }
								else if ($prev_overtime >= "0700" && $prev_overtime < "0800" ) { $d_time = "0800"; }
								else { $d_time = "0900";}

								$max_overtime = "0900";
								$min_overtime = "0700";
							}

							//전날 근무시간과 비교
							if ($prev_overtime >= $max_overtime)
							{
								if ($next_totaltime >= $d_time)
								{
									if ($next_totaltime > $d_time) { $next_gubun2 = "3"; }

									$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
									$next_overmin = substr($next_totaltime,2,2);
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_gubun2 = "2";

									$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
									$next_undermin = 60 - substr($next_totaltime,2,2);

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
							}
							else if ($prev_overtime < $min_overtime)
							{
								if ($next_totaltime >= $d_time)
								{
									if ($next_totaltime > $d_time) { $next_gubun2 = "3"; }

									$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
									$next_overmin = substr($next_totaltime,2,2);
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_gubun2 = "2";

									$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
									$next_undermin = 60 - substr($next_totaltime,2,2);

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
							}
							else
							{
								$d_hour = substr($d_time,0,2) - 1;
								$d_min = 60 - substr($prev_overtime,2,2);

								if ($d_min == 60)
								{
									$d_hour = $d_hour + 1;
									$d_min = "00";
								}
								if (strlen($d_hour) == 1) { $d_hour = "0". $d_hour; }
								if (strlen($d_min) == 1) { $d_min = "0". $d_min; }
								$d_time = $d_hour . $d_min;

								if ($next_totaltime > $d_time)
								{
									if ($next_totaltime > $d_time) { $next_gubun2 = "3"; }

									if (substr($next_totaltime,2,2) < substr($d_time,2,2))
									{
										$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2) - 1;
										$next_overmin = 60 + substr($next_totaltime,2,2) - substr($d_time,2,2);
									}
									else
									{
										$next_overhour = substr($next_totaltime,0,2) - substr($d_time,0,2);
										$next_overmin = substr($next_totaltime,2,2) - substr($d_time,2,2);
									}
									if (strlen($next_overhour) == 1) { $next_overhour = "0". $next_overhour; }
									if (strlen($next_overmin) == 1) { $next_overmin = "0". $next_overmin; }
									$next_overtime = $next_overhour . $next_overmin;

									$next_undertime = "0000";
								}
								else if ($next_totaltime < $d_time)
								{
									$next_gubun2 = "2";

									if (substr($d_time,2,2) < substr($next_totaltime,2,2))
									{
										$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2) - 1;
										$next_undermin = 60 + substr($d_time,2,2) - substr($next_totaltime,2,2);
									}
									else
									{
										$next_underhour = substr($d_time,0,2) - substr($next_totaltime,0,2);
										$next_undermin = substr($d_time,2,2) - substr($next_totaltime,2,2);
									}

									if ($next_undermin == 60) 
									{  
										$next_underhour = $next_underhour + 1;
										$next_undermin = "00";
									}
									if (strlen($next_underhour) == 1) { $next_underhour = "0". $next_underhour; }
									if (strlen($next_undermin) == 1) { $next_undermin = "0". $next_undermin; }
									$next_undertime = $next_underhour . $next_undermin;

									$next_overtime = "0000";
								}
								else
								{
									$next_gubun2 = "2";

									$next_overtime = "0000";
									$next_undertime = "0000";
								}
							}
						}

						if ($next_overtime >= "0300") { $next_pay2 = "Y"; }
						if ($next_overtime >= "0400") { $next_pay3 = "Y"; }
						if ($next_overtime >= "1000") { $next_pay4 = "Y"; }
						if ($next_overtime >= "0400" && substr($next_checktime2,8,4) >= "2400" && substr($next_checktime2,8,4) <= "3000") { $next_pay4 = "Y"; }
					}
					else if ($next_datekind == "LAW" || $next_datekind == "FIN")
					{
						if ($prev_datekind == "BIZ")
						{
							if ($prev_overtime >= "0700") { $next_overtime2 = $next_overtime + "0300"; }
							else if ($prev_overtime >= "0600" && $prev_overtime < "0700" ) { $next_overtime2 = $next_overtime + "0200"; }
							else if ($prev_overtime >= "0500" && $prev_overtime < "0600" ) { $next_overtime2 = $next_overtime + "0100"; }
							else { $next_overtime2 = $next_overtime;}
						}
						else
						{
							if ($prev_overtime >= "0800") { $next_overtime2 = $next_overtime + "0300"; }
							else if ($prev_overtime >= "0800" && $prev_overtime < "0900" ) { $next_overtime2 = $next_overtime + "0200"; }
							else if ($prev_overtime >= "0700" && $prev_overtime < "0800" ) { $next_overtime2 = $next_overtime + "0100"; }
							else { $next_overtime2 = $next_overtime;}
						}

						if (strlen($next_overtime2) == 3) { $next_overtime2 = "0". $next_overtime2; }

						if ($next_overtime2 >= "0400") { $next_pay1 = "Y"; }
						if ($next_overtime2 >= "0600") { $next_pay2 = "Y"; }
						if ($next_overtime2 >= "0700") { $next_pay3 = "Y"; }
						if ($next_overtime2 >= "1000") { $next_pay4 = "Y"; }
						if ($next_overtime2 >= "0700" && substr($next_checktime2,8,4) >= "2400" && substr($next_checktime2,8,4) <= "3000") { $next_pay4 = "Y"; }

					}

					if ($next_business_trip == "Y") //출장
					{
						$next_gubun1 = "6";
						$next_gubun2 = "6";
						$next_overtime = "0000";
						$next_undertime = "0000";
						$next_pay1 = "N";
						$next_pay2 = "N";
						$next_pay3 = "N";
						$next_pay4 = "N";
					}

					if ($next_out_chk == "Y") //파견
					{
						if ($next_gubun1 == "4" || $next_gubun1 == "8") {
							$next_pay1 = "N";
						} else {
							$next_pay1 = "Y";
						}
						$next_pay4 = "N";
					}

					$up_sql = "UPDATE DF_CHECKTIME SET 
								GUBUN1 = '$next_gubun1', 
								GUBUN2 = '$next_gubun2', 
								OVERTIME = '$next_overtime', 
								UNDERTIME = '$next_undertime', 
								PAY1 = '$next_pay1', 
								PAY2 = '$next_pay2', 
								PAY3 = '$next_pay3', 
								PAY4 = '$next_pay4' 
							WHERE PRS_ID = '$p_id' AND DATE = '$next_date'";
					$up_rs = sqlsrv_query($dbConn,$up_sql);
				}

			}
		}
	}

	//근태 입력 확인
	$sql = "UPDATE DF_CHECKTIME_EDIT SET
				GUBUN = '$gubun', 
				CHK_GUBUN1 = '$p_chk_gubun1', 
				CHK_GUBUN2 = '$p_chk_gubun2', 
				GUBUN1 = '$gubun1', 
				GUBUN2 = '$gubun2' 
				EDIT_OK = 'Y',
				OK_DATE = getdate(),
				OK_PRS_ID = '$prs_id'
			WHERE 
				SEQNO = '$seqno'";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("error. 근태 적용 실패하였습니다. 개발팀에 문의하세요.");
	</script>
<?
		exit;
	}

	$retUrl = "commuting_edit_detail.php?seqno=". $seqno ."&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword;
?>

	<script language="javascript">
		parent.location.href = "<?=$retUrl?>";
	</script>
