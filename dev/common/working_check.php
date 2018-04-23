<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$now_date = date("Y-m-d");
	$yesterday_date = date("Y-m-d",strtotime ("-1 day"));

	$where = " AND PRF_ID IN (1,2,3,4,5,7)";

	$work_count = array("4F"=>0,"3F"=>0,"2F"=>0);

	function getMemberCommuting($prs_id, $date, $yesterday, $floor) {
		global $dbConn, $work_count;

		$flag = false;

		//정상출근,지각,휴가,근무일수,반차,평균출근시,평균출근분,평균퇴근시,평균퇴근분,총근무시간
		$sql = "EXEC SP_COMMUTING_MEMBER_02 '$prs_id','$date','$yesterday'";
		$rs = sqlsrv_query($dbConn,$sql);
		$record = sqlsrv_fetch_array($rs);

		if (sizeof($record) > 0)
		{
			$col_date = $record['DATE'];					//날짜
			$col_datekind = $record['DATEKIND'];			//공휴일 여부
			$col_gubun = $record['GUBUN'];					//출퇴근구분
			$col_gubun1 = $record['GUBUN1'];				//출근구분
			$col_gubun2 = $record['GUBUN2'];				//퇴근구분
			$col_checktime1 = $record['CHECKTIME1'];		//출근시간
			$col_checktime2 = $record['CHECKTIME2'];		//퇴근시간

			//출근시간
			$checktime1 = substr($col_checktime1,8,2) .":". substr($col_checktime1,10,2);
			if ($checktime1 == ":") { $checktime1 = ""; }

			if ($col_gubun1 == "1") {}			//출근
			else if ($col_gubun1 == "4") {}		//반차
			else if ($col_gubun1 == "6") {}		//외근
			else if ($col_gubun1 == "7") {}		//지각
			else if ($col_gubun1 == "8") {}		//반차
			else if ($col_gubun1 == "0")	//오후반차 제출. 출퇴근체크 X
			{
				$checktime1 = "";
			}
			else //휴가 - 출근/퇴근 시간 표시 안함 - 당일 00:00출근 23:59퇴근으로 설정되어 있음
			{
				$checktime1 = "";
			}

			//퇴근시간
			$checktime2 = substr($col_checktime2,8,2) .":". substr($col_checktime2,10,2);
			if ($checktime2 == ":") { $checktime2 = ""; }

			if ($col_gubun2 == "2" || $col_gubun2 == "3" || $col_gubun2 == "6" || $col_gubun2 == "9")
			{
				if ($col_gubun2 == "2" || $col_gubun2 == "3") {}	//퇴근
				else if ($col_gubun2 == "5") {}						//프로젝트 반차
				else if ($col_gubun2 == "6") {}						//외근	
				else if ($col_gubun2 == "9") {}						//반차
				else if ($col_gubun2 == "0") {}						//오전반차 제출. 출퇴근체크 X
			}
		}

		if(strlen($checktime1) > 1) $flag = true;
		if(strlen($checktime2) > 1) $flag = false;

		if($flag===true) {
			$work_count[$floor]++;
		}
	}

	// 3층
	$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) ";
	$sql.= "WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WHERE FLOOR = 3)". $where;
	$rs = sqlsrv_query($dbConn, $sql);

	While ($record = sqlsrv_fetch_array($rs))
	{
		$col_prs_id = $record['PRS_ID'];
		getMemberCommuting($col_prs_id, $now_date, $yesterday_date, '3F');
	}

	// 2층
	$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) ";
	$sql.= "WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WHERE FLOOR = 2)". $where;
	$rs = sqlsrv_query($dbConn, $sql);

	While ($record = sqlsrv_fetch_array($rs))
	{
		$col_prs_id = $record['PRS_ID'];
		getMemberCommuting($col_prs_id, $now_date, $yesterday_date, '2F');
	}	

	// 총합계
	foreach($work_count as $val) $work_count['TOT'] += $val;
?>