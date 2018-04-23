<?
//main.php에 있던 로직 잘라서 넣음 ksyang
	$alert_state1 = "none";

	$sql = "SELECT TOP 1 DATE FROM HOLIDAY WHERE DATE < '". str_replace('-','',date("Y-m-d")) ."' AND DATEKIND = 'BIZ' ORDER BY DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$prev_biz_date = $record['DATE'];

	$prev_biz_date = substr($prev_biz_date,0,4) ."-". substr($prev_biz_date,4,2) ."-". substr($prev_biz_date,6,2);

	$sql = "SELECT 
				DATE, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, TOTALTIME, OVERTIME, UNDERTIME
			FROM 
				DF_CHECKTIME WITH(NOLOCK) 
			WHERE 
				DATE = '$prev_biz_date' AND PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	
	if (sqlsrv_has_rows($rs) > 0)
	{
		$prev_biz_date = $record['DATE'];
		$prev_biz_gubun1 = $record['GUBUN1'];
		$prev_biz_gubun2 = $record['GUBUN2'];
		$prev_biz_checktime1 = $record['CHECKTIME1'];
		$prev_biz_checktime2 = $record['CHECKTIME2'];
		$prev_biz_totaltime = $record['TOTALTIME'];
		$prev_biz_overtime = $record['OVERTIME'];
		$prev_biz_undertime = $record['UNDERTIME'];

		$now_time = date("His");		

		if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

		if ($prev_biz_gubun1 == "")
		{
			$alert_state1 = "inline";
		}
		else if ($prev_biz_gubun2 == "" && substr($now_time,0,2) >= 8)
		{
			$alert_state1 = "inline";
		}
	}
	else
	{
		$alert_state1 = "inline";
	}

	$alert_state2 = "none";

	$sql = "SELECT TOP 1 
				B.DATE, B.GUBUN1, B.GUBUN2, B.CHECKTIME1, B.CHECKTIME2, B.TOTALTIME, B.OVERTIME, B.UNDERTIME  
			FROM 
				HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
			ON 
				A.DATE = REPLACE(B.DATE,'-','') 
			WHERE 
				B.DATE < '". date("Y-m-d") ."' AND A.DATEKIND = 'BIZ' AND B.PRS_ID = '$prs_id' AND B.GUBUN1 IN (1,6,8) AND B.GUBUN2 IN (2,3,6,9) 
			ORDER BY 
				B.DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);

	$prev_biz_date = $record['DATE'];
	$prev_biz_gubun1 = $record['GUBUN1'];
	$prev_biz_gubun2 = $record['GUBUN2'];
	$prev_biz_checktime1 = $record['CHECKTIME1'];
	$prev_biz_checktime2 = $record['CHECKTIME2'];
	$prev_biz_totaltime = $record['TOTALTIME'];
	$prev_biz_overtime = $record['OVERTIME'];
	$prev_biz_undertime = $record['UNDERTIME'];

	$sql = "SELECT 
				COUNT(*) 
			FROM 
				DF_APPROVAL 
			WHERE 
				PRS_ID = '$prs_id' AND START_DATE = '$prev_biz_date' AND END_DATE = '$prev_biz_date' AND FORM_CATEGORY IN ('사유서','조퇴계') AND USE_YN = 'Y'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);

	$prev_approval_cnt = $record[0];
	
	if ($prev_biz_undertime > "0000" && $prev_approval_cnt == 0)
	{
		$alert_state2 = "inline";
	}

	$alert_state3 = "none";

	$now = date("YmdHis");								//현재
	$today = date("Y-m-d");								//오늘 날짜
	$yesterday = date("Y-m-d",strtotime ("-1 day"));	//어제 날짜
	$next = date("Y-m-d",strtotime ("+1 day"));			//내일 날짜

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

	$sql = "EXEC SP_MAIN_01 '$prs_id','$prs_name','$today','$yesterday','$next'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
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

		if ($yesterday_checktime2 == "0") { $yesterday_checktime2 = ""; }
		if ($today_checktime2 == "0") { $today_checktime2 = ""; }
	}

	if ($yesterday_checktime1 == "" && $today_checktime1 == "")
	{
		$alert_state3 = "inline";
	}

	//전일 출근 이후 익일 자정~8시 퇴근 처리를 위한 구분자
	//근무시간 계산
	$time_gubun = "after";
	if ($today_checktime1 == "")
	{ 
		if (date("G") < 8) 
		{
			$time_gubun = "before";
		}
	}
	else
	{
		if ($today_gubun1 != "1" && $today_gubun1 != "6" && $today_gubun1 != "8" && $today_gubun1 != $today_gubun2)
//		if ($today_gubun1 != "1" && $today_gubun1 != "6" && $today_gubun1 != "8")
		{
			$time_gubun = "before";
		}
	}

	if ($time_gubun == "before")
	{
		$now2 = substr($now,0,8) . (24+substr($now,8,2)) . substr($now,10,4);
		
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
		if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
		if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
		$totaltime = $totalhour . $totalmin;
	}
	else if ($time_gubun == "after")
	{
		$time_gubun = "after";
		
		if ($today_checktime1 == "")
		{
			$totaltime = "0000";
		}
		else
		{
			if (substr($now,10,2) < substr($today_checktime1,10,2))
			{
				$totalhour = substr($now,8,2) - substr($today_checktime1,8,2) - 1;
				$totalmin = substr($now,10,2) - substr($today_checktime1,10,2) + 60;
			}
			else
			{
				$totalhour = substr($now,8,2) - substr($today_checktime1,8,2);
				$totalmin = substr($now,10,2) - substr($today_checktime1,10,2);
			}
			if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
			if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
			$totaltime = $totalhour . $totalmin;
		}
	}

	$off_check = "N";

	$sql = "SELECT TOP 1 STARTTIME, ENDTIME FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE = '$today' AND PRS_ID = '$prs_id' ORDER BY SEQNO DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
	{
		$off_check = "Y";
		$last_off_starttime = $record['STARTTIME'];
		$last_off_endtime = $record['ENDTIME'];
	}
//main.php에 있던 로직 잘라서 넣음 ksyang

$sql = "SELECT TOP 2 DATEKIND FROM HOLIDAY WHERE DATE <= '". str_replace('-','',date("Y-m-d")) ."' ORDER BY DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$i = 0;
	while ($record = sqlsrv_fetch_array($rs))
	{
		if ($i == 0)
		{
			$today_datekind = $record['DATEKIND'];
		}
		else
		{
			$yesterday_datekind = $record['DATEKIND'];
		}
		$i++;
	}

	$checkin = substr($today_checktime1,8,4);

	$for_checkout_m = 60 - substr($yesterday_overtime,2,2);
	if (strlen($for_checkout_m) == 1)
	{
		$for_checkout_m = "0".$for_checkout_m;
	}

	if ($today_checktime1 != "" && $today_datekind == "BIZ")
	{
		if ($today_gubun1 == 4 || $today_gubun1 == 8) 
		{
			if (substr($checkin,0,2) < "13") { $checkin = "1300"; }

			if ($yesterday_datekind == "BIZ")
			{
				if ($yesterday_overtime >= "0700")						//근무시간9시간+연장근무+7시간 - 퇴근가능시간
				{
					$for_checkout = "0200";
				}
				else if ($yesterday_overtime >= "0600")					//근무시간9시간+연장근무+6시간 - 퇴근가능시간
				{
					$for_checkout = "02". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0500")					//근무시간9시간+연장근무+5시간 - 퇴근가능시간
				{
					$for_checkout = "03". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0400")					//근무시간9시간+연장근무+4시간 - 퇴근가능시간
				{
					$for_checkout = "04". $for_checkout_m;
				}
				else
				{
					$for_checkout = "0500";					
				}
			}
			else
			{
				if ($yesterday_overtime >= "0900")						//근무시간9시간+연장근무+9시간 - 퇴근가능시간
				{
					$for_checkout = "0200";
				}
				else if ($yesterday_overtime >= "0800")					//근무시간9시간+연장근무+8시간 - 퇴근가능시간
				{
					$for_checkout = "02". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0700")					//근무시간9시간+연장근무+7시간 - 퇴근가능시간
				{
					$for_checkout = "03". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0600")					//근무시간9시간+연장근무+6시간 - 퇴근가능시간
				{
					$for_checkout = "04". $for_checkout_m;
				}
				else
				{
					$for_checkout = "0500";					
				}
			}
		}
		else if ($today_gubun2 == 5 || $today_gubun2 == 9)
		{
			if (substr($checkin,0,2) < "08") { $checkin = "0800"; }

			if ($yesterday_datekind == "BIZ")
			{
				if ($yesterday_overtime >= "0700")						//근무시간9시간+연장근무+7시간 - 퇴근가능시간
				{
					$for_checkout = "0000";
				}
				else if ($yesterday_overtime >= "0600")					//근무시간9시간+연장근무+6시간 - 퇴근가능시간
				{
					$for_checkout = "00". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0500")					//근무시간9시간+연장근무+5시간 - 퇴근가능시간
				{
					$for_checkout = "01". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0400")					//근무시간9시간+연장근무+4시간 - 퇴근가능시간
				{
					$for_checkout = "02". $for_checkout_m;
				}
				else
				{
					$for_checkout = "0300";					
				}
			}
			else
			{
				if ($yesterday_overtime >= "0900")						//근무시간9시간+연장근무+9시간 - 퇴근가능시간
				{
					$for_checkout = "0000";
				}
				else if ($yesterday_overtime >= "0800")					//근무시간9시간+연장근무+8시간 - 퇴근가능시간
				{
					$for_checkout = "00". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0700")					//근무시간9시간+연장근무+7시간 - 퇴근가능시간
				{
					$for_checkout = "01". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0600")					//근무시간9시간+연장근무+6시간 - 퇴근가능시간
				{
					$for_checkout = "02". $for_checkout_m;
				}
				else
				{
					$for_checkout = "0300";					
				}
			}
		}
		else
		{
			if (substr($checkin,0,2) < "08") { $checkin = "0800"; }

			if ($yesterday_datekind == "BIZ")
			{
				if ($yesterday_overtime >= "0700")						//근무시간9시간+연장근무+7시간 - 퇴근가능시간
				{
					$for_checkout = "0600";
				}
				else if ($yesterday_overtime >= "0600")					//근무시간9시간+연장근무+6시간 - 퇴근가능시간
				{
					$for_checkout = "06". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0500")					//근무시간9시간+연장근무+5시간 - 퇴근가능시간
				{
					$for_checkout = "07". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0400")					//근무시간9시간+연장근무+4시간 - 퇴근가능시간
				{
					$for_checkout = "08". $for_checkout_m;
				}
				else
				{
					$for_checkout = "0900";					
				}
			}
			else
			{
				if ($yesterday_overtime >= "0900")						//근무시간9시간+연장근무+9시간 - 퇴근가능시간
				{
					$for_checkout = "0600";
				}
				else if ($yesterday_overtime >= "0800")					//근무시간9시간+연장근무+8시간 - 퇴근가능시간
				{
					$for_checkout = "06". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0700")					//근무시간9시간+연장근무+7시간 - 퇴근가능시간
				{
					$for_checkout = "07". $for_checkout_m;
				}
				else if ($yesterday_overtime >="0600")					//근무시간9시간+연장근무+6시간 - 퇴근가능시간
				{
					$for_checkout = "08". $for_checkout_m;
				}
				else
				{
					$for_checkout = "0900";					
				}
			}
		}
	}


	$sql = "SELECT TOTALTIME FROM DF_CHECKTIME_OFF WHERE DATE = '". date("Y-m-d") ."' AND PRS_ID = '$prs_id' AND STARTTIME IS NOT NULL AND ENDTIME IS NOT NULL";
	$rs = sqlsrv_query($dbConn,$sql);

	$today_off = "0000";
	while($record = sqlsrv_fetch_array($rs))
	{
		$today_off_t = substr($today_off,0,2) + substr($record[0],0,2);
		$today_off_m = substr($today_off,2,2) + substr($record[0],2,2);

		if ($today_off_m >= 60)
		{
			$today_off_t = $today_off_t + 1;
			$today_off_m = $today_off_m - 60;
		}
		
		if (strlen($today_off_t) == 1)
		{
			$today_off_t = "0".$today_off_t;
		}
		if (strlen($today_off_m) == 1)
		{
			$today_off_m = "0".$today_off_m;
		}

		$today_off = $today_off_t . $today_off_m;
	}

	$checkout_t = substr($checkin,0,2) + substr($for_checkout,0,2) + $today_off_t;
	$checkout_m = substr($checkin,2,2) + substr($for_checkout,2,2) + $today_off_m;

	if ($checkout_m >= 120)
	{
		$checkout_t = $checkout_t + 2;
		$checkout_m = $checkout_m - 120;
	}
	else if ($checkout_m >= 60)
	{
		$checkout_t = $checkout_t + 1;
		$checkout_m = $checkout_m - 60;
	}
	
	if (strlen($checkout_t) == 1)
	{
		$checkout_t = "0".$checkout_t;
	}
	if (strlen($checkout_m) == 1)
	{
		$checkout_m = "0".$checkout_m;
	}

	$checkout = $checkout_t . $checkout_m;
  ?>