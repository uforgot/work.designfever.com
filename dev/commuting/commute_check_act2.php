<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
if (in_array(REMOTE_IP, $ok_ip_arr))
//if (REMOTE_IP == "119.192.230.239")
{
	//출근은 한번만 체크 가능 
	//퇴근은 복수체크가능 (만약 이미 출근체크를 했을경우는 이미출근체크한 값을 받아서 업데이트 해준다)
	$time_gubun = isset($_REQUEST['time_gubun']) ? $_REQUEST['time_gubun'] : null;
	if ($time_gubun == "" && date("G") < 8) { $time_gubun = "before"; }
	//if (date("G") < 8) { $time_gubun = "before"; }

	$now = date("YmdHis");								//오늘 퇴근 년월일시분초

	$ip = REMOTE_IP;									//접속IP
	$gubun = "출퇴근";

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
	$next_gubun1 = "";
	$next_gubun2 = "";
	$next_checktime1 = "";
	$next_checktime2 = "";

	$out_chk = "N";
	$pay1 = "N";
	$pay2 = "N";
	$pay3 = "N";
	$pay4 = "N";
	$pay5 = "N";
	$pay6 = "N";

	//전일 출근 이후 익일 자정~8시 퇴근 처리를 위한 구분자
	//근무시간 계산
	/*
	if (substr($now,8,2) == "24")
	{
		$today = date("Y-m-d",strtotime ("-1 day"));		//오늘 날짜
		$today2 = date("Ymd",strtotime ("-1 day"));			//오늘 날짜
		$yesterday = date("Y-m-d",strtotime ("-2 day"));	//어제 날짜
		$yesterday2 = date("Ymd",strtotime ("-2 day"));		//어제 날짜
		$next = date("Y-m-d");								//내일 날짜
		$next2 = date("Ymd");								//내일 날짜

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
		}

		$time_gubun = "before";
		$now2 = $today2 . (substr($now,8,2)) . substr($now,10,4);

		if (substr($today_checktime1,8,2) == "00")
		{
			$now2 = substr($now,0,8) . "00" . substr($now,10,4);
			$totalhour = "00";
			$totalmin = substr($now2,10,2) - substr($today_checktime1,10,2);
			if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
			$totaltime = $totalhour . $totalmin;
		}
		else
		{
			if ($yesterday_checktime1 == "")
			{
				$totaltime = "0000";
			}
			else
			{
				if (substr($yesterday_checktime1,8,2) < "08")
				{
					$totalhour = substr($now2,8,2) - 8;
					$totalmin = substr($now2,10,2);
				}
				else
				{
					if (substr($yesterday_checktime1,8,2) < "13" && $yesterday_gubun1 == "8")
					{
						$totalhour = substr($now2,8,2) - 13;
						$totalmin = substr($now2,10,2);
					}
					else
					{
						if (substr($now2,10,2) < substr($yesterday_checktime1,10,2))
						{
							$totalhour = substr($now2,8,2) - substr($yesterday_checktime1,8,2) - 1;
							$totalmin = substr($now2,10,2) - substr($yesterday_checktime1,10,2) + 60;
						}
						else
						{
							$totalhour = substr($now2,8,2) - substr($yesterday_checktime1,8,2);
							$totalmin = substr($now2,10,2) - substr($yesterday_checktime1,10,2);
						}
					}
				}
				if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
				if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
				$totaltime = $totalhour . $totalmin;
			}
		}
	}
	*/
	if ($time_gubun == "before")
	{
		$today = date("Y-m-d",strtotime ("-1 day"));		//오늘 날짜
		$today2 = date("Ymd",strtotime ("-1 day"));			//오늘 날짜
		$yesterday = date("Y-m-d",strtotime ("-2 day"));	//어제 날짜
		$yesterday2 = date("Ymd",strtotime ("-2 day"));		//어제 날짜
		$next = date("Y-m-d");								//내일 날짜
		$next2 = date("Ymd");								//내일 날짜

		$now2 = $today2 . (24+substr($now,8,2)) . substr($now,10,4);

		$cur_week = date("w",strtotime ("-1 day"));			//오늘 요일
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

		$cur_week = date("w");								//오늘 요일
	}

	if (in_array($prs_position,$positionB_arr) && $prs_login != 'dfadmin' && $prf_id != 7) 
	{
		//보고서 작성여부 체크 및 팝업(금요일 체크)
		if($cur_week == 5) 
		{
			//금요일 기준 주차 계산(1:월~7:일)
			$BASIC_DOW = 1;

			//주차정보 추출
			$winfo = getWeekInfo($today);

			$rs = chkWeekly($today,"this");
			
			if ($rs == false)
			{
	?>
			<script language="javascript">
				alert("주간보고서를 작성하지 않으셨습니다.\n작성 후 퇴근해 주시기 바랍니다.");
			</script>
	<?
				exit;
			}
		}
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
		$yesterday_memo1 = $record['yesterday_MEMO1'];				//전자결재 상신자 or 어제 출근 수정정보
		$yesterday_memo2 = $record['yesterday_MEMO2'];				//전자결재 결재일 or 어제 퇴근 수정정보
		$yesterday_memo3 = $record['yesterday_MEMO3'];				//전자결재 번호
		$today_gubun1 = $record['TODAY_GUBUN1'];					//오늘 출근
		$today_gubun2 = $record['TODAY_GUBUN2'];					//오늘 퇴근
		$today_checktime1 = $record['TODAY_CHECKTIME1'];			//오늘 출근	
		$today_checktime2 = $record['TODAY_CHECKTIME2'];			//오늘 퇴근
		$today_totaltime = $record['TODAY_TOTALTIME'];				//오늘 근무시간
		$today_memo1 = $record['TODAY_MEMO1'];						//전자결재 상신자 or 오늘 출근 수정정보
		$today_memo2 = $record['TODAY_MEMO2'];						//전자결재 결재일 or 오늘 퇴근 수정정보
		$today_memo3 = $record['TODAY_MEMO3'];						//전자결재 번호
		$today_off_time = $record['TODAY_OFF_TIME'];				//오늘 외출시간시
		$today_off_minute = $record['TODAY_OFF_MINUTE'];			//오늘 외출시간분
		$today_outchk = $record['TODAY_OUTCHK'];					//오늘 파견여부
		$next_gubun1 = $record['NEXT_GUBUN1'];						//내일 출근
		$next_gubun2 = $record['NEXT_GUBUN2'];						//내일 퇴근
		$next_checktime1 = $record['NEXT_CHECKTIME1'];				//내일 출근	
		$next_checktime2 = $record['NEXT_CHECKTIME2'];				//내일 퇴근
	}

	if ($today_gubun1 >= 10)
	{
		$today = date("Y-m-d",strtotime ("-1 day"));		//오늘 날짜
		$today2 = date("Ymd",strtotime ("-1 day"));			//오늘 날짜
		$yesterday = date("Y-m-d",strtotime ("-2 day"));	//어제 날짜
		$yesterday2 = date("Ymd",strtotime ("-2 day"));		//어제 날짜
		$next = date("Y-m-d");								//내일 날짜
		$next2 = date("Ymd");								//내일 날짜

		$now2 = $today2 . (24+substr($now,8,2)) . substr($now,10,4);

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
			$yesterday_memo1 = $record['yesterday_MEMO1'];				//전자결재 상신자 or 어제 출근 수정정보
			$yesterday_memo2 = $record['yesterday_MEMO2'];				//전자결재 결재일 or 어제 퇴근 수정정보
			$yesterday_memo3 = $record['yesterday_MEMO3'];				//전자결재 번호
			$today_gubun1 = $record['TODAY_GUBUN1'];					//오늘 출근
			$today_gubun2 = $record['TODAY_GUBUN2'];					//오늘 퇴근
			$today_checktime1 = $record['TODAY_CHECKTIME1'];			//오늘 출근	
			$today_checktime2 = $record['TODAY_CHECKTIME2'];			//오늘 퇴근
			$today_totaltime = $record['TODAY_TOTALTIME'];				//오늘 근무시간
			$today_memo1 = $record['TODAY_MEMO1'];						//전자결재 상신자 or 오늘 출근 수정정보
			$today_memo2 = $record['TODAY_MEMO2'];						//전자결재 결재일 or 오늘 퇴근 수정정보
			$today_memo3 = $record['TODAY_MEMO3'];						//전자결재 번호
			$today_off_time = $record['TODAY_OFF_TIME'];				//오늘 외출시간시
			$today_off_minute = $record['TODAY_OFF_MINUTE'];			//오늘 외출시간분
			$today_outchk = $record['TODAY_OUTCHK'];					//오늘 파견여부
			$next_gubun1 = $record['NEXT_GUBUN1'];						//내일 출근
			$next_gubun2 = $record['NEXT_GUBUN2'];						//내일 퇴근
			$next_checktime1 = $record['NEXT_CHECKTIME1'];				//내일 출근	
			$next_checktime2 = $record['NEXT_CHECKTIME2'];				//내일 퇴근
		}
	}

	if ($today_checktime1 == "")
	{
		$totaltime = "0000";
	}
	else
	{
		if (substr($today_checktime1,8,2) < "08")
		{
			if (substr($now2,10,2) < substr($today_checktime1,10,2))
			{
				$totalhour = substr($now2,8,2) - substr($today_checktime1,8,2) - 1;
				$totalmin = substr($now2,10,2) - substr($today_checktime1,10,2) + 60;
			}
			else
			{
				$totalhour = substr($now2,8,2) - substr($today_checktime1,8,2);
				$totalmin = substr($now2,10,2) - substr($today_checktime1,10,2);
			}

			$totalhour2 = substr($now2,8,2) - 8;
			$totalmin2 = substr($now2,10,2);
		}
		else
		{
			if (substr($today_checktime1,8,2) < "13" && ($today_gubun1 == "4" || $today_gubun1 == "8"))
			{
				if (substr($now2,10,2) < substr($today_checktime1,10,2))
				{
					$totalhour = substr($now2,8,2) - substr($today_checktime1,8,2) - 1;
					$totalmin = substr($now2,10,2) - substr($today_checktime1,10,2) + 60;
				}
				else
				{
					$totalhour = substr($now2,8,2) - substr($today_checktime1,8,2);
					$totalmin = substr($now2,10,2) - substr($today_checktime1,10,2);
				}

				$totalhour2 = substr($now2,8,2) - 13;
				$totalmin2 = substr($now2,10,2);
			}
			else
			{
				if (substr($now2,10,2) < substr($today_checktime1,10,2))
				{
					$totalhour = substr($now2,8,2) - substr($today_checktime1,8,2) - 1;
					$totalmin = substr($now2,10,2) - substr($today_checktime1,10,2) + 60;
				}
				else
				{
					$totalhour = substr($now2,8,2) - substr($today_checktime1,8,2);
					$totalmin = substr($now2,10,2) - substr($today_checktime1,10,2);
				}
				$totalhour2 = $totalhour;
				$totalmin2 = $totalmin;
			}
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

		if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
		if (strlen($totalhour2) == 1) { $totalhour2 = "0". $totalhour2; }
		if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
		if (strlen($totalmin2) == 1) { $totalmin2 = "0". $totalmin2; }
		$totaltime = $totalhour . $totalmin;
		$totaltime2 = $totalhour2 . $totalmin2;
	}

	$sql = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$today) ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$today_kind = $record['DATEKIND'];

	$sql = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$yesterday) ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$yesterday_kind = $record['DATEKIND'];
	
	//근무시간 퇴근(2)/연장근무(3)/오후반차(9) 체크
	if ($today_gubun1 == "4" || $today_gubun1 == "8")	//오전반차
	{ 
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

		$gubun2 = "3";

		//전날 근무시간과 비교
		if ($yesterday_overtime >= $max_overtime)
		{
			if ($totaltime2 >= $d_time)
			{
				if ($totaltime2 == $d_time) { $gubun2 = "2"; }

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
				if ($totaltime2 == $d_time) { $gubun2 = "2"; }

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
	else if ($today_gubun2 == "5" || $today_gubun2 == "9")	//오후반차
	{ 
		if ($yesterday_kind == "BIZ")
		{
			if ($yesterday_overtime >= "0700") { $d_time = "0000"; }
			else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0100"; }
			else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0200"; }
			else { $d_time = "0300";}

			$max_overtime = "0700";
			$min_overtime = "0400";
		}
		else
		{
			if ($yesterday_overtime >= "0900") { $d_time = "0000"; }
			else if ($yesterday_overtime >= "0800" && $yesterday_overtime < "0900" ) { $d_time = "0100"; }
			else if ($yesterday_overtime >= "0700" && $yesterday_overtime < "0800" ) { $d_time = "0200"; }
			else { $d_time = "0300";}

			$max_overtime = "0900";
			$min_overtime = "0600";
		}

		$gubun2 = $today_gubun2;

		//전날 근무시간과 비교
		if ($yesterday_overtime >= $max_overtime)
		{
			if ($totaltime2 >= "0000")
			{
				$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
				$overmin = substr($totaltime2,2,2);
				if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
				if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
				$overtime = $overhour . $overmin;

				$undertime = "0000";
			}
			else if ($totaltime2 < "0000")
			{
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
			if ($totaltime2 >= "0300")
			{
				$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
				$overmin = substr($totaltime2,2,2);
				if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
				if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
				$overtime = $overhour . $overmin;

				$undertime = "0000";
			}
			else if ($totaltime2 < "0300")
			{
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
				$overtime = "0000";
				$undertime = "0000";
			}
		}
	}
	else
	{
		$gubun2 = "3";

		if ($today_kind == "BIZ")	//평일
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
			$overtime = "0000";
			$undertime = "0000";

			//전날 근무시간과 비교
			if ($yesterday_overtime >= $max_overtime)
			{
				if ($totaltime2 >= $d_time)
				{
					if ($totaltime2 == $d_time) { $gubun2 = "2"; }

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
					if ($totaltime2 == $d_time) { $gubun2 = "2"; }

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
			$overtime = $totaltime;
			$undertime = "0000";
		}
	}

	if ($today_gubun2 == "5" || $today_gubun2 == "9" || $today_gubun2 == "6") { $gubun2 = $today_gubun2; }

	if ($today_kind == "BIZ")
	{
		if ($overtime >= "0300") { $pay2 = "Y"; }
		if ($overtime >= "0400") { $pay3 = "Y"; }
		if ($overtime >= "1000") { $pay4 = "Y"; }
		if ($overtime >= "0400" && substr($now2,8,4) >= "2400" && substr($now2,8,4) <= "3000") { $pay4 = "Y"; }

		/*
		//파견처리
		if (REMOTE_IP == "119.192.230.238") { 
			$out_chk = "Y"; 
			$pay4 = "N";
			$pay5 = "Y";
			$pay6 = "Y";
		}
		*/
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
		if ($overtime2 >= "0700" && substr($now2,8,4) >= "2400" && substr($now2,8,4) <= "3000") { $pay4 = "Y"; }

		/*
		//파견처리
		if (REMOTE_IP == "119.192.230.238") { 
			$out_chk = "Y"; 
			$pay4 = "N";
			$pay5 = "Y";
			$pay6 = "Y";
		}
		*/
	}

	if ($today_outchk == "Y") 
	{
		$out_chk = "Y";
		$pay4 = "N";
		$pay5 = "Y";
		$pay6 = "Y";

		if ($today_gubun1 == "4" || $today_gubun1 == "8" || $today_gubun2 == "5" || $today_gubun2 == "9") {
			$pay1 = "N"; 
		}
		else
		{
			$pay1 = "Y"; 
		}
	}

	if ($today_checktime1 != "")	//오늘 출근체크 O
	{
		$sql = "UPDATE DF_CHECKTIME SET 
					GUBUN2 = '$gubun2', 
					CHECKTIME2 = '$now2', 
					TOTALTIME = '$totaltime', 
					OVERTIME = '$overtime', 
					UNDERTIME = '$undertime', 
					CHECKIP2 = '$ip', 
					OUT_CHK = '$out_chk', 
					PAY1 = '$pay1', 
					PAY2 = '$pay2', 
					PAY3 = '$pay3', 
					PAY4 = '$pay4', 
					PAY5 = '$pay5', 
					PAY6 = '$pay6', 
					REGDATE2 = getdate() 
				WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
		$rs = sqlsrv_query($dbConn,$sql);
		$error_no = 1;
	}
	else if ($today_checktime1 == "" && $yesterday_checktime1 != "")	//오늘 출근체크 X, 어제 출근체크 O
	{
//		$sql = "UPDATE DF_CHECKTIME SET 
//					GUBUN2 = '$gubun2', CHECKTIME2 = '$now2', TOTALTIME = '$totaltime', OVERTIME = '$overtime', UNDERTIME = '$undertime', CHECKIP2 = '$ip', 
//					PAY1 = '$pay1', PAY2 = '$pay2', PAY3 = '$pay3', PAY4 = '$pay4', REGDATE2 = getdate() 
//				WHERE PRS_ID = '$prs_id' AND DATE = '$yesterday'";
		$sql = "UPDATE DF_CHECKTIME SET 
					GUBUN2 = '$gubun2',
					CHECKTIME2 = '$now2', 
					TOTALTIME = '$totaltime', 
					OVERTIME = '$overtime', 
					UNDERTIME = '$undertime', 
					CHECKIP2 = '$ip', 
					OUT_CHK = '$out_chk', 
					PAY1 = '$pay1', 
					PAY2 = '$pay2', 
					PAY3 = '$pay3', 
					PAY4 = '$pay4', 
					PAY5 = '$pay5', 
					PAY6 = '$pay6', 
					REGDATE2 = getdate() 
				WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
		$rs = sqlsrv_query($dbConn,$sql);
		$error_no = 2;
	}
	else 
	{
?>
	<script language="javascript">
		alert("Error 3. 오늘 출근체크를 하지 않으셨습니다.\n출퇴근 정보를 경영지원팀에 알려주세요.");
		parent.location.href="/main.php";
	</script>
<?
		exit;
	}
	
	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("error<?=$error_no?>. 퇴근 체크 오류입니다. \nError 번호를 기억하셔서 개발팀에 문의해 주세요.");
		parent.location.href="/main.php";
	</script>
<?
	}
	else
	{
//	 혹시, 내일 출근 시간에 오늘 퇴근 시간 보다 이른 시간이 입력되어 있으면, 삭제
//		if ($next_checktime2 == "" && $next_gubun1 == "1" && substr($next_checktime1,8,6) > substr($now2,8,6))
//		{
//		}
?>
	<script language="javascript">
		alert("오늘 하루도 수고하셨습니다!! \n퇴근체크가 완료되었습니다.");
		parent.location.href="/main.php";
	</script>
<?
	}
}
else
{
?>
	<script language="javascript">
		alert("출퇴근 체크는 사내에서만 가능합니다.");
		parent.location.href="/main.php";
	</script>
<?
	exit;
}
?>