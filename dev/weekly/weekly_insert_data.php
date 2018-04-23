<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";

	//금요일 기준 주차 계산(1:월~7:일)
	$BASIC_DOW = 5;

	//금일 날짜 및 요일
	//$cur_date = date('Y-m-d');
	//$cur_week = date("w");

	//$cur_date = date('2014-10-02');
	$cur_date = date('2014-10-09');
	$cur_week = 4;

	// 월요일~수요일, 목요일 기준 주차적용
	if($cur_week >= 1 && $cur_week <= 3) {
		$add = 4 - $cur_week;
		$ndate = date("Y-m-d", strtotime("$cur_date +$add day"));
	} else {
		$ndate = $cur_date;
	}

	//주차정보 추출
	$winfo = getWeekInfo($ndate);

	$sql = "SELECT PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION FROM DF_PERSON WHERE PRF_ID NOT IN (5,6) AND PRS_ID NOT IN (85,86,109)";								
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$prs_id = $record['PRS_ID'];
		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];
		$prs_team = $record['PRS_TEAM'];
		$prs_position = $record['PRS_POSITION'];

		//팀장이하 생성
		if (in_array($prs_position,$positionB_arr) && $prs_login != 'dfadmin') {
			$order = $winfo['cur_week'];
			$order_tot = $winfo['tot_week'];
			$title = substr($order,4,2)."월 ".substr($order,6,1)."주차 주간보고";
			$week = $winfo['str_week'];
			$complete_yn = "N";

			//시퀀스 값 추출
			$sql1 = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_WEEKLY WITH(NOLOCK)";
			$rs1 = sqlsrv_query($dbConn,$sql1);

			$result1 = sqlsrv_fetch_array($rs1);
			$seq = $result1[0] + 1;

			// 기본 데이터 입력
			$sql2 = "INSERT INTO DF_WEEKLY
						(SEQNO, WEEK_ORD_TOT, WEEK_ORD, WEEK_AREA, TITLE, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, COMPLETE_YN)
					VALUES
						('$seq', '$order_tot', '$order', '$week', '$title', '$prs_id', '$prs_name', '$prs_login', '$prs_team', '$prs_position', '$complete_yn')";
			//$rs2 = sqlsrv_query($dbConn, $sql2);
			echo "<br>".$sql2."<br>";


			if ($rs2 != false) {
				$sql3 = "UPDATE DF_PERSON SET
						LOG_WEEKLY_CREATE = '$order' 
						WHERE 
						PRS_ID = '$prs_id'";
				//$rs3 = sqlsrv_query($dbConn, $sql3);
				echo " + ".$sql3."<br>";

			}
		}
	}
?>
