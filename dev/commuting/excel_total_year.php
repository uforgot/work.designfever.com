<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("해당페이지는 임원,관리자만 확인 가능합니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	$nowYear = date("Y");
	$nowMonth = date("m");

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 

	if ($p_year == "") {
		if ($nowMonth == 1) {
			$p_year = $nowYear - 1;	
		} 
		else {
			$p_year = $nowYear;	
		}
	}

	$sql = "SELECT SORT, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) ORDER BY SORT";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_TEAM='". $record['TEAM'] ."' THEN ". $record['SORT'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, PRS_NAME";

	$sql = "SELECT 
				R.PRS_LOGIN, R.PRS_ID, R.PRS_NAME, R.PRS_TEAM, R.PRS_POSITION, 
				R.AVGTIME1, R.AVGMINUTE1, R.AVGTIME2, R.AVGMINUTE2, R.AVG_TIME, R.AVG_MINUTE, 
				R.REAL_OVER, R.TOTAL_TIME, R.TOTAL_MINUTE, 
				R.BIZ_COMMUTE, R.LAW_COMMUTE, R.UNDER_COMMUTE, R.PAY, 
				R.VACATION, R.VACATION1, R.SUBVACATION1, R.VACATION2, R.SUBVACATION2, R.VACATION3, 
				R.OFF_TIME, R.OFF_MINUTE, R.REAL_OFF
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
					T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION, 
					T.AVGTIME1, T.AVGMINUTE1, T.AVGTIME2, T.AVGMINUTE2, T.AVG_TIME, T.AVG_MINUTE, 
					T.REAL_OVER, T.TOTAL_TIME, T.TOTAL_MINUTE, 
					T.BIZ_COMMUTE, T.LAW_COMMUTE, T.UNDER_COMMUTE, T.PAY, 
					T.VACATION, T.VACATION1, T.SUBVACATION1, T.VACATION2, T.SUBVACATION2, T.VACATION3, 
					T.OFF_TIME, T.OFF_MINUTE, T.REAL_OFF
				FROM
				(
					SELECT
						P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
						D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
						((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) / 60) AS AVG_TIME, 
						((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) % 60) AS AVG_MINUTE, 
						(D.REAL_OVER - (D.PAY * 60)) AS REAL_OVER, (D.TOTAL_TIME - D.BIZ_COMMUTE - D.PAY) AS TOTAL_TIME, D.TOTAL_MINUTE, 
						D.BIZ_COMMUTE, D.LAW_COMMUTE, D.UNDER_COMMUTE, D.PAY,
						D.VACATION, D.VACATION1, D.SUBVACATION1, D.VACATION2, D.SUBVACATION2, D.VACATION3, 
						D.OFF_TIME, D.OFF_MINUTE, D.REAL_OFF
					FROM 
						(
							SELECT 
								* 
							FROM 
								DF_PERSON A
							WHERE 
								PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,102,148) AND PRS_JOIN <= '". $p_year ."-12-31'
								AND (SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $p_year ."%') > 0
						) P 
						INNER JOIN 
						(
							SELECT 
								PRS_ID, 
								(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
									WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGTIME1, --평균 출근시간 시
								(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGMINUTE1, --평균 출근시간 분
								(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGTIME2, --평균 퇴근시간 시 
								(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGMINUTE2, --평균 퇴근시간 분 
								(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9)) AS REAL_AVG, --평균 근무시간 분단위 표시
								((SELECT ISNULL(SUM(SUBSTRING(OVERTIME, 1,2) * 60 + SUBSTRING(OVERTIME, 3,2)),0)
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000')
								-(SELECT ISNULL(SUM(SUBSTRING(UNDERTIME, 1,2) * 60 + SUBSTRING(UNDERTIME, 3,2)),0)
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND UNDERTIME > '0000' AND OVERTIME = '0000')) AS REAL_OVER, --연장 근무시간 분단위 표시
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $p_year ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_TIME, --총 근무시간 시
								(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $p_year ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_MINUTE, --총 근무시간 분	
								(SELECT COUNT(A.SEQNO) 
									FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
									WHERE A.GUBUN1 IN (1,6,7) AND A.GUBUN2 IN (2,3,6) AND A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $p_year ."%' AND B.DATEKIND = 'BIZ') AS BIZ_COMMUTE, --평일근무일
								(SELECT COUNT(A.SEQNO) 
									FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
									WHERE A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $p_year ."%' AND B.DATEKIND IN ('FIN','LAW')) AS LAW_COMMUTE, --휴일 근무일
								(SELECT COUNT(A.SEQNO) 
									FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
									WHERE A.UNDERTIME > '0000' AND A.OVERTIME = '0000' AND A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $p_year ."%' AND B.DATEKIND = 'BIZ') AS UNDER_COMMUTE, --정상근무 미만일
								((SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY1 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $p_year ."%')
								+(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY2 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $p_year ."%')) AS PAY, --식대지급횟수
								(SELECT VACATION_COUNT 
									FROM DF_VACATION WITH(NOLOCK) 
									WHERE PRS_ID=C.PRS_ID AND YEAR = '". $p_year ."') AS VACATION, --휴가 지급일
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (10,11,14) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $p_year ."%') AS VACATION1, --연차 
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE (GUBUN1 = 8 OR GUBUN2 = 9) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $p_year ."%') AS SUBVACATION1, --반차
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (16) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $p_year ."%') AS VACATION2, --프로젝트 휴가 
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE (GUBUN1 = 4 OR GUBUN2 = 5) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $p_year ."%') AS SUBVACATION2, --프로젝트 반차
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (12,13,15,17,18,19) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $p_year ."%') AS VACATION3, --기타 휴가 
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 
									FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $p_year ."%' AND PRS_ID = C.PRS_ID) AS OFF_TIME, --외출 시
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) %3600 /60 
									FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $p_year ."%' AND PRS_ID = C.PRS_ID) AS OFF_MINUTE, -- 외출 분 
								(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
									FROM DF_CHECKTIME_OFF WITH(NOLOCK) 
									WHERE DATE LIKE '". $p_year ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000') AS REAL_OFF --외출 분단위 표시
							FROM DF_CHECKTIME C WITH(NOLOCK) 
							WHERE PRS_ID = C.PRS_ID
						) D
					ON
						P.PRS_ID = D.PRS_ID
					GROUP BY 
						P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
						D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
						D.REAL_AVG, D.REAL_OVER, D.TOTAL_TIME, D.TOTAL_MINUTE, 
						D.BIZ_COMMUTE, D.LAW_COMMUTE, D.UNDER_COMMUTE, D.PAY, 
						D.VACATION, D.VACATION1, D.SUBVACATION1, D.VACATION2, D.SUBVACATION2, D.VACATION3,
						D.OFF_TIME, D.OFF_MINUTE, D.REAL_OFF
				) T
			) R
			$orderbycase";
	$rs = sqlsrv_query($dbConn,$sql);

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=근태통계_".$p_year.".xls" );
?>

	<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=EUC-KR'>
	<style>
	<!--
	br{mso-data-placement:same-cell;}
	-->
	</style>
	<table border=1>
		<thead>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="5">근무시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="4">근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="5">휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="2">기타</td>
			</tr>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">no.</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">이름</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">직급</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">부서</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>출근시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>퇴근시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>근무시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">연장<br>근무시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">총<br>근무시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평일<br>근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">휴일<br>근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">정상근무<br>미만일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">총<br>근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">연차</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">반차</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">프로젝트<br>휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">기타<br>휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">총<br>사용휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">총 근무시간<br>- 연장 근무시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">연장 근무시간<br>- 프로젝트 휴가</td>
			</tr>
		</thead>
		<tbody>
<?
		$no = 1;

		while ($record = sqlsrv_fetch_array($rs))
		{
			$login = $record['PRS_LOGIN'];
			$id = $record['PRS_ID'];
			$name = $record['PRS_NAME'];
			$team = $record['PRS_TEAM'];
			$position = $record['PRS_POSITION'];

			$avgtime1 = $record['AVGTIME1'];				//평균출근시
			$avgminute1 = $record['AVGMINUTE1'];			//평균출근분
			$avgtime2 = $record['AVGTIME2'];				//평균퇴근시
			$avgminute2 = $record['AVGMINUTE2'];			//평균퇴근분
			$avg_time = $record['AVG_TIME'];				//평균근무시간시
			$avg_minute = $record['AVG_MINUTE'];			//평균근무시간분
			$real_over = $record['REAL_OVER'];				//연장근무시간분단위
			$total_time = $record['TOTAL_TIME'];			//총근무시간시
			$total_minute = $record['TOTAL_MINUTE'];		//총근무시간분
			$biz_commute = $record['BIZ_COMMUTE'];			//평일근무일
			$law_commute = $record['LAW_COMMUTE'];			//휴일근무일
			$under_commute = $record['UNDER_COMMUTE'];		//정상근무 미만일
			$pay = $record['PAY'];							//식대지급횟수
			$vacation = $record['VACATION'];				//휴가지급일
			$vacation1 = $record['VACATION1'];				//연차
			$subvacation1 = $record['SUBVACATION1'];		//반차
			$vacation2 = $record['VACATION2'];				//프로젝트 휴가
			$subvacation2 = $record['SUBVACATION2'];		//프로젝트 반차
			$vacation3 = $record['VACATION3'];				//기타 휴가
			$off_time = $record['OFF_TIME'];				//외출 시
			$off_minute = $record['OFF_MINUTE'];			//외출 분
			$real_off = $record['REAL_OFF'];				//평균외출시간분단위

			if ($avgtime1 == "") { $avgtime1 = "0"; }
			if ($avgminute1 == "") { $avgminute1 = "0"; }
			if ($avgtime2 == "") { $avgtime2 = "0"; }
			if ($avgminute2 == "") { $avgminute2 = "0"; }
			if ($avg_time == "") { $avg_time = "0"; }
			if ($avg_minute == "") { $avg_minute = "0"; }
			if ($over == "") { $over = "0"; }
			if ($total_time == "") { $total_time = "0"; }
			if ($total_minute == "") { $total_minute = "0"; }
			if ($real_over == "") { $real_over = "0"; }
			if ($real_under == "") { $real_under = "0"; }
			if ($off_time == "") { $off_time = "0"; }
			if ($off_minute == "") { $off_minute = "0"; }
			if ($real_off == "") { $real_off = "0"; }

			//외출시간 제외한 총 근무 시간 계산
			if ($total_minute < $off_minute)
			{
				$total_minute = $total_minute - $off_minute + 60;
				$total_time = $total_time - $off_time - 1;
			}
			else
			{
				$total_minute = $total_minute - $off_minute;
				$total_time = $total_time - $off_time - 1;
			}
			//

			$real_biz_commute = $biz_commute + ($subvacation1 * 0.5) + ($subvacation2 * 0.5);		//평일근무일
			$total_commute = $real_biz_commute + $law_commute;										//총근무일
			$real_vacation = $vacation1 + ($subvacation1 * 0.5);									//연차
			$project_vacation = $vacation2 + ($subvacation2 * 0.5);									//프로젝트휴가
			$total_vacation = $real_vacation + $project_vacation + $vacation3;						//총사용휴가

			if (strlen($avgtime1) == 1) { $avgtime1 = "0".$avgtime1; }
			if (strlen($avgminute1) == 1) { $avgminute1 = "0".$avgminute1; }
			if (strlen($avgtime2) == 1) { $avgtime2 = "0".$avgtime2; }
			if (strlen($avgminute2) == 1) { $avgminute2 = "0".$avgminute2; }
			if (strlen($avg_time) == 1) { $avg_time = "0".$avg_time; }
			if (strlen($avg_minute) == 1) { $avg_minute = "0".$avg_minute; }
			if (strlen($total_time) == 1) { $total_time = "0".$total_time; }
			if (strlen($total_minute) == 1) { $total_minute = "0".$total_minute; }

			$flag1 = "&nbsp;";
			if (substr($real_over,0,1) == "-") 
			{
				$flag1 = "-";
				$real_over = substr($real_over,1,strlen($real_over));
			}

			$over_time = intval($real_over / 60);
			$over_minute = $real_over % 60;

			$over = $over_time . $over_minute;
			$total = $total_time . $total_minute;

			if ($total_minute >= $over_minute)
			{
				$etc1_time = $total_time - $over_time;
				$etc1_minute = $total_minute - $over_minute;
			}
			else if ($over_minute > $total_minute)
			{
				$etc1_time = $total_time - $over_time - 1;
				$etc1_minute = $total_minute + 60 - $over_minute;
			}
			else
			{
				$etc1_time = "0";
				$etc1_minute = "0";
			}

			$etc2_time = $over_time - ($project_vacation * 8);
			$flag2 = "&nbsp;";
			if (substr($etc2_time,0,1) == "-") 
			{
				$flag2 = "-";
				$etc2_time = substr($etc2_time,1,strlen($etc2_time));
			}
			if ($flag1 == "-")
			{
				$flag2 = "-";
			}

			if (strlen($over_time) == 1) { $over_time = "0".$over_time; }
			if (strlen($over_minute) == 1) { $over_minute = "0".$over_minute; }
			if (strlen($etc1_time) == 1) { $etc1_time = "0".$etc1_time; }
			if (strlen($etc1_minute) == 1) { $etc1_minute = "0".$etc1_minute; }
			if (strlen($etc2_time) == 1) { $etc2_time = "0".$etc2_time; }
			if (strlen($etc2_minute) == 1) { $etc2_minute = "0".$etc2_minute; }
?>
			<tr>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$no?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$name?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$position?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';border-right:2px solid #000;"><?=$team?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avgtime1?> : <?=$avgminute1?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avgtime2?> : <?=$avgminute2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avg_time?> : <?=$avg_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$flag1?><?=$over_time?> : <?=$over_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$total_time?> : <?=$total_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$real_biz_commute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$law_commute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$under_commute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$total_commute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=doubleval($vacation)?> / <?=$real_vacation?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$subvacation1?> / <?=$subvacation1 * 0.5?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$project_vacation?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$vacation3?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$total_vacation?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$etc1_time?> : <?=$etc1_minute;?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$flag2?><?=$etc2_time?> : <?=$over_minute?></td>
			</tr>
<?
			$no++;
		}
?>
		</tbody>
	</table>

