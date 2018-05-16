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
	$nowDay = date("d");

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 

	if ($p_year == "" || $p_month == "") {
		if ($nowDay < 10) {
			if ($nowMonth == 1) {
				$p_year = $nowYear - 1;	
				$p_month = 12;
			} 
			else {
				$p_year = $nowYear;	
				$p_month = $nowMonth - 1;
			}
		}
		else {
			$p_year = $nowYear;	
			$p_month = $nowMonth;
		}
	}

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

	$date = $p_year."-". $p_month;

	$sql = "SELECT COUNT(*) FROM HOLIDAY WITH(NOLOCK) WHERE DATE LIKE '". $p_year . $p_month ."%' AND DATEKIND = 'BIZ'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$biz_day = $record[0];

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby . " END, PRS_NAME";

	$teamSql = "SELECT 
				R.PRS_LOGIN, R.PRS_ID, R.PRS_NAME, R.PRS_TEAM, R.PRS_POSITION, 
				R.BIZ_COMMUTE, R.LAW_COMMUTE, R.LATENESS, R.VACATION1, R.VACATION2, R.VACATION3, R.REFRESH, R.PROJECT, R.NOMONEY, R.EDU, R.ETC, 
				R.COMMUTE_DATE, R.SUBVACATION1, R.SUBVACATION2, 
				R.AVGTIME1, R.AVGMINUTE1, R.AVGTIME2, R.AVGMINUTE2, R.AVG_TIME, R.AVG_MINUTE, 
				R.BIZ_TOTAL_TIME, R.BIZ_TOTAL_MINUTE, R.TOTAL_TIME, R.TOTAL_MINUTE, R.OVER_TIME, R.OVER_MINUTE, R.OVER_DATE, R.UNDER_TIME, R.UNDER_MINUTE, R.UNDER_DATE, 
				R.OFF_TIME, R.OFF_MINUTE, R.BIZ_OFF_TIME, R.BIZ_OFF_MINUTE, R.REAL_OVER, R.REAL_AVG, R.REAL_OFF, R.PAY  
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
					T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION, 
					T.BIZ_COMMUTE, T.LAW_COMMUTE, T.LATENESS, T.VACATION1, T.VACATION2, T.VACATION3, T.REFRESH, T.PROJECT, T.NOMONEY, T.EDU, T.ETC,
					T.COMMUTE_DATE, T.SUBVACATION1, T.SUBVACATION2, 
					T.AVGTIME1, T.AVGMINUTE1, T.AVGTIME2, T.AVGMINUTE2, T.AVG_TIME, T.AVG_MINUTE, T.BIZ_TOTAL_TIME, T.BIZ_TOTAL_MINUTE, T.TOTAL_TIME, T.TOTAL_MINUTE, 
					T.OVER_TIME, T.OVER_MINUTE, T.OVER_DATE, T.UNDER_TIME, T.UNDER_MINUTE, T.UNDER_DATE, 
					T.OFF_TIME, T.OFF_MINUTE, T.BIZ_OFF_TIME, T.BIZ_OFF_MINUTE, (T.REAL_OVER - (T.PAY * 60)) AS REAL_OVER, T.REAL_AVG, T.REAL_OFF, T.PAY  
				FROM
				(
					SELECT
						P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
						D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION1, D.VACATION2, D.VACATION3, D.REFRESH, D.PROJECT, D.NOMONEY, D.EDU, D.ETC, 
						D.COMMUTE_DATE, D.SUBVACATION1, D.SUBVACATION2, D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
						((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) / 60) AS AVG_TIME, 
						((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) % 60) AS AVG_MINUTE, 
						D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, D.TOTAL_TIME, D.TOTAL_MINUTE, 
						D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, D.UNDER_TIME, D.UNDER_MINUTE, D.UNDER_DATE, 
						D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, (D.REAL_OVER - (D.PAY * 60)) AS REAL_OVER, D.REAL_AVG, D.REAL_OFF, D.PAY  
					FROM 
						(
							SELECT 
								* 
							FROM 
								DF_PERSON A
							WHERE 
								PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,90,102) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
								AND (SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0
						) P 
						INNER JOIN 
						(
							SELECT 
								PRS_ID, 
								(SELECT COUNT(A.SEQNO) 
									FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
									WHERE A.GUBUN1 IN (1,6,7) AND A.GUBUN2 IN (2,3,6) AND A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $date ."%' AND B.DATEKIND = 'BIZ') AS BIZ_COMMUTE,
								(SELECT COUNT(A.SEQNO) 
									FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
									WHERE A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $date ."%' AND B.DATEKIND IN ('FIN','LAW')) AS LAW_COMMUTE,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (7) AND GUBUN2 IN (2,3,6) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS LATENESS,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (10) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS VACATION1,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (11) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS VACATION2,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (12) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS VACATION3,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (17) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS REFRESH,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (16) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS PROJECT,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (18) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS NOMONEY,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (15) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS EDU,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (13) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS ETC,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS COMMUTE_DATE,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (4,8) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS SUBVACATION1,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN2 IN (5,9) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS SUBVACATION2,
								(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
									WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGTIME1,
								(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGMINUTE1,
								(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGTIME2,
								(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
									FROM (
										SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGMINUTE2,
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600
									FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') 
									WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS BIZ_TOTAL_TIME,
								(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60
									FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') 
									WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS BIZ_TOTAL_MINUTE,
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_TIME,
								(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_MINUTE,
								(SELECT SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60) / 3600
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_TIME,
								(SELECT (SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60)) %3600 /60 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_MINUTE,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK)
									WHERE PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%' AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_DATE,
								(SELECT SUM(SUBSTRING(UNDERTIME, 1,2) * 3600 + SUBSTRING(UNDERTIME, 3,2) * 60) / 3600
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND UNDERTIME > '0000' AND UNDERTIME = '0000') AS UNDER_TIME,
								(SELECT (SUM(SUBSTRING(UNDERTIME, 1,2) * 3600 + SUBSTRING(UNDERTIME, 3,2) * 60)) %3600 /60 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND UNDERTIME > '0000' AND UNDERTIME = '0000') AS UNDER_MINUTE,
								(SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK)
									WHERE PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%' AND UNDERTIME > '0000' AND UNDERTIME = '0000') AS UNDER_DATE,
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 
									FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS OFF_TIME,
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) %3600 /60 
									FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS OFF_MINUTE,
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 
									FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
									WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS BIZ_OFF_TIME,
								(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) % 3600 /60
									FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
									WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS BIZ_OFF_MINUTE,
								((SELECT ISNULL(SUM(SUBSTRING(OVERTIME, 1,2) * 60 + SUBSTRING(OVERTIME, 3,2)),0)
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000')
								-(SELECT ISNULL(SUM(SUBSTRING(UNDERTIME, 1,2) * 60 + SUBSTRING(UNDERTIME, 3,2)),0)
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND UNDERTIME > '0000' AND OVERTIME = '0000')) AS REAL_OVER,
								(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9)) AS REAL_AVG,
								(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
									FROM DF_CHECKTIME_OFF WITH(NOLOCK) 
									WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000') AS REAL_OFF,
								((SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY1 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%')
								+(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY2 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%')) AS PAY 
							FROM DF_CHECKTIME C WITH(NOLOCK) 
							WHERE PRS_ID = C.PRS_ID
						) D
					ON
						P.PRS_ID = D.PRS_ID
					WHERE 
						@teamSql@
					GROUP BY 
						P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
						D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION1, D.VACATION2, D.VACATION3, D.REFRESH, D.PROJECT, D.NOMONEY, D.EDU, D.ETC, 
						D.COMMUTE_DATE, D.SUBVACATION1, D.SUBVACATION2, 
						D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, D.TOTAL_TIME, D.TOTAL_MINUTE, 
						D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, D.UNDER_TIME, D.UNDER_MINUTE, D.UNDER_DATE, 
						D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, D.REAL_OVER, D.REAL_AVG, D.REAL_OFF, D.PAY 
			) T
		) R
		$orderbycase";

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=근태통계_".$p_year.$p_month.".xls" );
?>

	<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=EUC-KR'>
	<style>
	<!--
	br{mso-data-placement:same-cell;}
	-->
	</style>
	<table border=0>
		<tr>
			<td colspan="8" style="font-size:12px;font-weight:bold;text-align:left;"><?=$p_year?>년 <?=$p_month?>월 근태현황</td>
			<td colspan="11" style="font-size:12px;font-weight:bold;text-align:right;">Working days = <?=$biz_day?> days / Working time = <?=$biz_day*9?></td>
		</tr>
	</table>
	<table border=1>
		<thead>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">부서</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">이름</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">직급</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>출근시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>퇴근시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>근무시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">총<br>근무시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">근무<br>일수</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">미만<br>근무일수</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">반차</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">휴가<br>(연차/병가)</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">경조사</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">리프레시<br>휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">프로젝트<br>휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">무급<br>휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">교육<br>/훈련</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">기타</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">휴일<br>출근</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">비고</td>
			</tr>
		</thead>
		<tbody>
<?
		$sql = "SELECT TEAM, STEP FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT2";
		$rs = sqlsrv_query($dbConn,$sql);

		$teamList = "";
		$stepList = "";
		$j = 0;
		while($record=sqlsrv_fetch_array($rs))
		{
			if ($j == 0)
			{
				$teamList .= "'". $record['TEAM'] ."'";
			}
			else
			{
				$teamList .= "##'". $record['TEAM'] ."'";
			}

			if ($j == 0)
			{
				$stepList .= "". $record['STEP'] ."";
			}
			else
			{
				$stepList .= "##". $record['STEP'] ."";
			}

			$j++;
		}

		$teamArr = explode("##",$teamList);
		$stepArr = explode("##",$stepList);

		$div_avgtime1 = 0;
		$div_avgminute1 = 0;
		$div_avgtime2 = 0;
		$div_avgminute2 = 0;
		$div_avg_time = 0;
		$div_avg_minute = 0;
		$div_total_time = 0;
		$div_total_minute = 0;
		$div_commute_day = 0;

		$div_avg_avgtime1 = 0;
		$div_avg_avgminute1 = 0;
		$div_avg_avgtime2 = 0;
		$div_avg_avgminute2 = 0;
		$div_avg_avg_time = 0;
		$div_avg_avg_minute = 0;
		$div_avg_total_time = 0;
		$div_avg_total_minute = 0;
		$div_avg_commute_day = 0;

		$div_under_day = 0;
		$div_subvacation1 = 0;
		$div_subvacation2 = 0;
		$div_vacation1 = 0;
		$div_vacation2 = 0;
		$div_vacation3 = 0;
		$div_refresh = 0;
		$div_project = 0;
		$div_nomoney = 0;
		$div_edu = 0;
		$div_etc = 0;
		$div_law_commute = 0;

		for ($i=0; $i<sizeof($teamArr); $i++)
		{
			$sql = "SELECT 
						COUNT(*) 
					FROM 
						DF_PERSON A WITH(NOLOCK) 
					WHERE 
						PRS_TEAM = ". $teamArr[$i] ." AND PRF_ID IN (1,2,3,4,5) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
						AND (SELECT COUNT(SEQNO) 
								FROM DF_CHECKTIME WITH(NOLOCK) 
								WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0
			";
			$rs = sqlsrv_query($dbConn,$sql);

			$record = sqlsrv_fetch_array($rs);
			$teamMember = $record[0];

			if ($stepArr[$i] == 2)
			{
				$sql = "SELECT 
							COUNT(*) 
						FROM 
							DF_PERSON A WITH(NOLOCK) 
						WHERE 
							PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = ". $teamArr[$i] ." OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = ". $teamArr[$i] .") OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = ". $teamArr[$i] .")))) 
							AND PRF_ID IN (1,2,3,4,5) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."' 
							AND (SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0";
				$rs = sqlsrv_query($dbConn,$sql);

				$record = sqlsrv_fetch_array($rs);
				$divMember = $record[0];
			}

			$sql = str_replace("@teamSql@"," P.PRS_TEAM = ". $teamArr[$i],$teamSql);
			$rs = sqlsrv_query($dbConn,$sql);

			$j = 0;

			$total_avgtime1 = 0;
			$total_avgminute1 = 0;
			$total_avgtime2 = 0;
			$total_avgminute2 = 0;
			$total_avg_time = 0;
			$total_avg_minute = 0;
			$total_total_time = 0;
			$total_total_minute = 0;
			$total_commute_day = 0;

			$avg_avgtime1 = 0;
			$avg_avgminute1 = 0;
			$avg_avgtime2 = 0;
			$avg_avgminute2 = 0;
			$avg_avg_time = 0;
			$avg_avg_minute = 0;
			$avg_total_time = 0;
			$avg_total_minute = 0;
			$avg_commute_day = 0;

			$total_under_day = 0;
			$total_subvacation1 = 0;
			$total_subvacation2 = 0;
			$total_vacation1 = 0;
			$total_vacation2 = 0;
			$total_vacation3 = 0;
			$total_refresh = 0;
			$total_project = 0;
			$total_nomoney = 0;
			$total_edu = 0;
			$total_etc = 0;
			$total_law_commute = 0;

//			if (sqlsrv_has_rows($rs) > 0)
//			{
				while ($record = sqlsrv_fetch_array($rs))
				{
					$team_login = $record['PRS_LOGIN'];
					$team_id = $record['PRS_ID'];
					$team_name = $record['PRS_NAME'];
					$team_team = $record['PRS_TEAM'];
					$team_position = $record['PRS_POSITION'];

					$biz_commute = $record['BIZ_COMMUTE'];	//평일정상출근
					$law_commute = $record['LAW_COMMUTE'];	//휴일출근
					$lateness = $record['LATENESS'];			//지각
					$vacation1 = $record['VACATION1'];				//연차
					$vacation2 = $record['VACATION2'];				//병가
					$vacation3 = $record['VACATION3'];				//경조사
					$refresh = $record['REFRESH'];					//리프레시휴가
					$project = $record['PROJECT'];					//프로젝트휴가
					$nomoney = $record['NOMONEY'];					//무급휴가
					$edu = $record['EDU'];							//교육/훈련
					$etc = $record['ETC'];							//기타
					$commute_day = $record['COMMUTE_DATE'];			//근무일수
					$subvacation1 = $record['SUBVACATION1'];	//오전반차
					$subvacation2 = $record['SUBVACATION2'];	//오후반차
					$avgtime1 = $record['AVGTIME1'];				//평균출근시
					$avgminute1 = $record['AVGMINUTE1'];			//평균출근분
					$avgtime2 = $record['AVGTIME2'];				//평균퇴근시
					$avgminute2 = $record['AVGMINUTE2'];			//평균퇴근분
					$avg_time = $record['AVG_TIME'];				//평균근무시간시
					$avg_minute = $record['AVG_MINUTE'];			//평균근무시간분
					$biz_total_time = $record['BIZ_TOTAL_TIME'];		//평일총근무시간시
					$biz_total_minuate = $record['BIZ_TOTAL_MINUTE'];	//평일총근무시간분
					$total_time = $record['TOTAL_TIME'];			//총근무시간시
					$total_minute = $record['TOTAL_MINUTE'];		//총근무시간분
					$over_time = $record['OVER_TIME'];				//초과근무시간시 - 하루 9시간 이상 근무한 내역에 대한 월 총합시간
					$over_minute = $record['OVER_MINUTE'];			//초과근무시간분 - 하루 9시간 이상 근무한 내역에 대한 월 총합시간
					$over_day = $record['OVER_DATE'];				//초과일수
					$under_time = $record['UNDER_TIME'];			//미만근무시간시
					$under_minute = $record['UNDER_MINUTE'];		//미만근무시간분
					$under_day = $record['UNDER_DATE'];				//미만일수
					$off_time = $record['OFF_TIME'];				//외출 시
					$off_minute = $record['OFF_MINUTE'];			//외출 분
					$biz_off_time = $record['BIZ_OFF_TIME'];		//평일외출 시
					$biz_off_minute = $record['BIZ_OFF_MINUTE'];	//평일외출 분
					$real_over = $record['REAL_OVER'];				//연장근무시간분단위
					$real_avg = $record['REAL_AVG'];				//평균근무시간분단위
					$real_off = $record['REAL_OFF'];				//평균외출시간분단위

					if ($avgtime1 == "") { $avgtime1 = "0"; }
					if ($avgminute1 == "") { $avgminute1 = "0"; }
					if ($avgtime2 == "") { $avgtime2 = "0"; }
					if ($avgminute2 == "") { $avgminute2 = "0"; }
					if ($avg_time == "") { $avg_time = "0"; }
					if ($avg_minute == "") { $avg_minute = "0"; }
					if ($biz_total_time == "") { $biz_total_time = "0"; }
					if ($biz_total_minute == "") { $biz_total_minute = "0"; }
					if ($total_time == "") { $total_time = "0"; }
					if ($total_minute == "") { $total_minute = "0"; }
					if ($over_time == "") { $over_time = "0"; }
					if ($over_minute == "") { $over_minute = "0"; }
					if ($off_time == "") { $off_time = "0"; }
					if ($off_minute == "") { $off_minute = "0"; }
					if ($biz_off_time == "") { $biz_off_time = "0"; }
					if ($biz_off_minute == "") { $biz_off_minute = "0"; }

					//외출시간 제외한 총 근무 시간 계산
					if ($off_time > 0 && $off_minute > 0)
					{
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
						if ($total_time == -1)
						{
							$total_time = 0;
						}
					}
					//

					if (substr($real_over,0,1) == "-") 
					{
						$flag1 = "-";
						$real_over = substr($real_over,1,strlen($real_over));
					}

					$over_time = intval($real_over / 60);
					$over_minute = $real_over % 60;

					$over = $over_time . $over_minute;
					$total = $total_time . $total_minute;

					if (strlen($avgtime1) == 1) { $avgtime1 = "0".$avgtime1; }
					if (strlen($avgminute1) == 1) { $avgminute1 = "0".$avgminute1; }
					if (strlen($avgtime2) == 1) { $avgtime2 = "0".$avgtime2; }
					if (strlen($avgminute2) == 1) { $avgminute2 = "0".$avgminute2; }
					if (strlen($avg_time) == 1) { $avg_time = "0".$avg_time; }
					if (strlen($avg_minute) == 1) { $avg_minute = "0".$avg_minute; }
					if (strlen($total_time) == 1) { $total_time = "0".$total_time; }
					if (strlen($total_minute) == 1) { $total_minute = "0".$total_minute; }
					if (strlen($over_time) == 1) { $over_time = "0".$over_time; }
					if (strlen($over_minute) == 1) { $over_minute = "0".$over_minute; }
?>
			<tr>
			<? if ($j == 0) { ?>
				<td rowspan="<?=$teamMember+1?>" style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$team_team?></td>
			<? } ?>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$team_name?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$team_position?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avgtime1?> : <?=$avgminute1?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avgtime2?> : <?=$avgminute2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avg_time?> : <?=$avg_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$total_time?> : <?=$total_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$commute_day?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$under_day?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$subvacation1+$subvacation2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$vacation1+$vacation2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$vacation3?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$refresh?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$project?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$nomoney?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$edu?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$etc?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$law_commute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"></td>
			</tr>
<?
					$total_avgtime1 = $total_avgtime1 + $avgtime1;
					$total_avgminute1 = $total_avgminute1 + $avgminute1;
					$total_avgtime2 = $total_avgtime2 + $avgtime2;
					$total_avgminute2 = $total_avgminute2 + $avgminute2;
					$total_avg_time = $total_avg_time + $avg_time;
					$total_avg_minute = $total_avg_minute + $avg_minute;
					$total_total_time = $total_total_time + $total_time;
					$total_total_minute = $total_total_minute + $total_minute;
					$total_commute_day = $total_commute_day + $commute_day;

					$total_under_day = $total_under_day + $under_day;
					$total_subvacation1 = $total_subvacation1 + $subvacation1;
					$total_subvacation2 = $total_subvacation2 + $subvacation2;
					$total_vacation1 = $total_vacation1 + $vacation1;
					$total_vacation2 = $total_vacation2 + $vacation2;
					$total_vacation3 = $total_vacation3 + $vacation3;
					$total_refresh = $total_refresh + $refresh;
					$total_project = $total_project + $project;
					$total_nomoney = $total_nomoney + $nomoney;
					$total_edu = $total_edu + $edu;
					$total_etc = $total_etc + $etc;
					$total_law_commute = $total_law_commute + $law_commute;

					$div_avgtime1 = $div_avgtime1 + $avgtime1;
					$div_avgminute1 = $div_avgminute1 + $avgminute1;
					$div_avgtime2 = $div_avgtime2 + $avgtime2;
					$div_avgminute2 = $div_avgminute2 + $avgminute2;
					$div_avg_time = $div_avg_time + $avg_time;
					$div_avg_minute = $div_avg_minute + $avg_minute;
					$div_total_time = $div_total_time + $div_time;
					$div_total_minute = $div_total_minute + $div_minute;
					$div_commute_day = $div_commute_day + $commute_day;

					$div_under_day = $div_under_day + $under_day;
					$div_subvacation1 = $div_subvacation1 + $subvacation1;
					$div_subvacation2 = $div_subvacation2 + $subvacation2;
					$div_vacation1 = $div_vacation1 + $vacation1;
					$div_vacation2 = $div_vacation2 + $vacation2;
					$div_vacation3 = $div_vacation3 + $vacation3;
					$div_refresh = $div_refresh + $refresh;
					$div_project = $div_project + $project;
					$div_nomoney = $div_nomoney + $nomoney;
					$div_edu = $div_edu + $edu;
					$div_etc = $div_etc + $etc;
					$div_law_commute = $div_law_commute + $law_commute;

					$j++;
				}
				
				if (strpos($teamList,$team_team) > 0 && $teamMember > 0) 
				{
					$total_avg1 = ($total_avgtime1 * 60 + $total_avgminute1) / $teamMember;
					$total_avg2 = ($total_avgtime2 * 60 + $total_avgminute2) / $teamMember;
					$total_avg = ($total_avg_time * 60 + $total_avg_minute) / $teamMember;
					$total_total = ($total_total_time * 60 + $total_total_minute) / $teamMember;

					$avg_avgtime1 = floor($total_avg1 / 60);
					$avg_avgminute1 = number_format($total_avg1 % 60,0);
					$avg_avgtime2 = floor($total_avg2 / 60);
					$avg_avgminute2 = number_format($total_avg2 % 60,0);
					$avg_avg_time = floor($total_avg / 60);
					$avg_avg_minute = number_format($total_avg % 60,0);
					$avg_total_time = floor($total_total / 60);
					$avg_total_minute = number_format($total_total % 60,0);
					$avg_commute_day = number_format($total_commute_day / $teamMember,1);

					if (strlen($avg_avgtime1) == 1) { $avg_avgtime1 = "0".$avg_avgtime1; }
					if (strlen($avg_avgminute1) == 1) { $avg_avgminute1 = "0".$avg_avgminute1; }
					if (strlen($avg_avgtime2) == 1) { $avg_avgtime2 = "0".$avg_avgtime2; }
					if (strlen($avg_avgminute2) == 1) { $avg_avgminute2 = "0".$avg_avgminute2; }
					if (strlen($avg_avg_time) == 1) { $avg_avg_time = "0".$avg_avg_time; }
					if (strlen($avg_avg_minute) == 1) { $avg_avg_minute = "0".$avg_avg_minute; }
					if (strlen($avg_total_time) == 1) { $avg_total_time = "0".$avg_total_time; }
					if (strlen($avg_total_minute) == 1) { $avg_total_minute = "0".$avg_total_minute; }
					if (strlen($avg_over_time) == 1) { $avg_over_time = "0".$avg_over_time; }
					if (strlen($avg_over_minute) == 1) { $avg_over_minute = "0".$avg_over_minute; }
?>
			<tr>
				<td colspan="2" style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;">Total</td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_avgtime1?> : <?=$avg_avgminute1?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_avgtime2?> : <?=$avg_avgminute2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_avg_time?> : <?=$avg_avg_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_total_time?> : <?=$avg_total_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_commute_day?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_under_day?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_subvacation1+$total_subvacation2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_vacation1+$total_vacation2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_vacation3?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_refresh?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_project?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_nomoney?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_edu?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_etc?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_law_commute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"></td>
			</tr>
<?
				}
//			}

			if ($stepArr[$i] == 2)
			{
				$div_avg1 = ($div_avgtime1 * 60 + $div_avgminute1) / $divMember;
				$div_avg2 = ($div_avgtime2 * 60 + $div_avgminute2) / $divMember;
				$div_avg = ($div_avg_time * 60 + $div_avg_minute) / $divMember;
				$div_total = ($div_total_time * 60 + $div_total_minute) / $divMember;

				$div_avg_avgtime1 = floor($div_avg1 / 60);
				$div_avg_avgminute1 = number_format($div_avg1 % 60,0);
				$div_avg_avgtime2 = floor($div_avg2 / 60);
				$div_avg_avgminute2 = number_format($div_avg2 % 60,0);
				$div_avg_avg_time = floor($div_avg / 60);
				$div_avg_avg_minute = number_format($div_avg % 60,0);
				$div_avg_total_time = floor($div_total / 60);
				$div_avg_total_minute = number_format($div_total % 60,0);
				$div_avg_commute_day = number_format($div_commute_day / $divMember,1);

				if (strlen($div_avg_avgtime1) == 1) { $div_avg_avgtime1 = "0".$div_avg_avgtime1; }
				if (strlen($div_avg_avgminute1) == 1) { $div_avg_avgminute1 = "0".$div_avg_avgminute1; }
				if (strlen($div_avg_avgtime2) == 1) { $div_avg_avgtime2 = "0".$div_avg_avgtime2; }
				if (strlen($div_avg_avgminute2) == 1) { $div_avg_avgminute2 = "0".$div_avg_avgminute2; }
				if (strlen($div_avg_avg_time) == 1) { $div_avg_avg_time = "0".$div_avg_avg_time; }
				if (strlen($div_avg_avg_minute) == 1) { $div_avg_avg_minute = "0".$div_avg_avg_minute; }
				if (strlen($div_avg_total_time) == 1) { $div_avg_total_time = "0".$div_avg_total_time; }
				if (strlen($div_avg_total_minute) == 1) { $div_avg_total_minute = "0".$div_avg_total_minute; }
				if (strlen($div_avg_over_time) == 1) { $div_avg_over_time = "0".$div_avg_over_time; }
				if (strlen($div_avg_over_minute) == 1) { $div_avg_over_minute = "0".$div_avg_over_minute; }
?>
			<tr>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=str_replace("'","",$teamArr[$i])?></td>
				<td colspan="2" style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;">Total</td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_avg_avgtime1?> : <?=$div_avg_avgminute1?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_avg_avgtime2?> : <?=$div_avg_avgminute2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_avg_avg_time?> : <?=$div_avg_avg_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_avg_total_time?> : <?=$div_avg_total_minute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_avg_commute_day?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_under_day?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_subvacation1+$div_subvacation2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_vacation1+$div_vacation2?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_vacation3?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_refresh?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_project?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_nomoney?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_edu?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_etc?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$div_law_commute?></td>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"></td>
			</tr>
<?
				$div_avgtime1 = 0;
				$div_avgminute1 = 0;
				$div_avgtime2 = 0;
				$div_avgminute2 = 0;
				$div_avg_time = 0;
				$div_avg_minute = 0;
				$div_total_time = 0;
				$div_total_minute = 0;
				$div_commute_day = 0;

				$div_avg_avgtime1 = 0;
				$div_avg_avgminute1 = 0;
				$div_avg_avgtime2 = 0;
				$div_avg_avgminute2 = 0;
				$div_avg_avg_time = 0;
				$div_avg_avg_minute = 0;
				$div_avg_total_time = 0;
				$div_avg_total_minute = 0;
				$div_avg_commute_day = 0;

				$div_under_day = 0;
				$div_subvacation1 = 0;
				$div_subvacation2 = 0;
				$div_vacation1 = 0;
				$div_vacation2 = 0;
				$div_vacation3 = 0;
				$div_refresh = 0;
				$div_project = 0;
				$div_nomoney = 0;
				$div_edu = 0;
				$div_etc = 0;
				$div_law_commute = 0;
			}
		}
?>
		</tbody>
	</table>
