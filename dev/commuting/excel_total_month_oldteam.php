<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("�ش��������� �ӿ�,�����ڸ� Ȯ�� �����մϴ�.");
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
				R.PRS_LOGIN, R.PRS_ID, R.PRS_NAME, R.PRS_TEAM_Old, R.PRS_POSITION, 
				R.BIZ_COMMUTE, R.LAW_COMMUTE, R.LATENESS, R.VACATION1, R.VACATION2, R.VACATION3, R.REFRESH, R.PROJECT, R.NOMONEY, R.EDU, R.ETC, 
				R.COMMUTE_DATE, R.SUBVACATION1, R.SUBVACATION2, 
				R.AVGTIME1, R.AVGMINUTE1, R.AVGTIME2, R.AVGMINUTE2, R.AVG_TIME, R.AVG_MINUTE, 
				R.BIZ_TOTAL_TIME, R.BIZ_TOTAL_MINUTE, R.TOTAL_TIME, R.TOTAL_MINUTE, R.OVER_TIME, R.OVER_MINUTE, R.OVER_DATE, R.UNDER_TIME, R.UNDER_MINUTE, R.UNDER_DATE, 
				R.OFF_TIME, R.OFF_MINUTE, R.BIZ_OFF_TIME, R.BIZ_OFF_MINUTE, R.REAL_OVER, R.REAL_AVG, R.REAL_OFF, R.PAY  
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
					T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM_Old, T.PRS_POSITION, 
					T.BIZ_COMMUTE, T.LAW_COMMUTE, T.LATENESS, T.VACATION1, T.VACATION2, T.VACATION3, T.REFRESH, T.PROJECT, T.NOMONEY, T.EDU, T.ETC,
					T.COMMUTE_DATE, T.SUBVACATION1, T.SUBVACATION2, 
					T.AVGTIME1, T.AVGMINUTE1, T.AVGTIME2, T.AVGMINUTE2, T.AVG_TIME, T.AVG_MINUTE, T.BIZ_TOTAL_TIME, T.BIZ_TOTAL_MINUTE, T.TOTAL_TIME, T.TOTAL_MINUTE, 
					T.OVER_TIME, T.OVER_MINUTE, T.OVER_DATE, T.UNDER_TIME, T.UNDER_MINUTE, T.UNDER_DATE, 
					T.OFF_TIME, T.OFF_MINUTE, T.BIZ_OFF_TIME, T.BIZ_OFF_MINUTE, (T.REAL_OVER - (T.PAY * 60)) AS REAL_OVER, T.REAL_AVG, T.REAL_OFF, T.PAY  
				FROM
				(
					SELECT
						P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM_Old, P.PRS_POSITION, 
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
								PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN (15,22,24,87,90,102) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
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
									WHERE A.GUBUN1 IN (1,4,6,7,8) AND A.GUBUN2 IN (2,3,5,6,9) AND A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $date ."%' AND B.DATEKIND = 'BIZ') AS BIZ_COMMUTE,
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
						P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM_Old, P.PRS_POSITION, 
						D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION1, D.VACATION2, D.VACATION3, D.REFRESH, D.PROJECT, D.NOMONEY, D.EDU, D.ETC, 
						D.COMMUTE_DATE, D.SUBVACATION1, D.SUBVACATION2, 
						D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, D.TOTAL_TIME, D.TOTAL_MINUTE, 
						D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, D.UNDER_TIME, D.UNDER_MINUTE, D.UNDER_DATE, 
						D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, D.REAL_OVER, D.REAL_AVG, D.REAL_OFF, D.PAY 
			) T
		) R
		$orderbycase";

	$divSql = "SELECT TOP 1 
				R.BIZ_COMMUTE, R.LAW_COMMUTE, R.LATENESS, R.VACATION1, R.VACATION2, R.VACATION3, R.REFRESH, R.PROJECT, R.NOMONEY, R.EDU, R.ETC, 
				R.COMMUTE_DATE, R.SUBVACATION1, R.SUBVACATION2, R.AVGTIME1, R.AVGMINUTE1, R.AVGTIME2, R.AVGMINUTE2, R.AVG_TIME, R.AVG_MINUTE, R.BIZ_TOTAL_TIME, R.BIZ_TOTAL_MINUTE, 
				R.TOTAL_TIME, R.TOTAL_MINUTE, R.OVER_TIME, R.OVER_MINUTE, R.OVER_DATE, R.UNDER_TIME, R.UNDER_MINUTE, R.UNDER_DATE, R.OFF_TIME, R.OFF_MINUTE, R.BIZ_OFF_TIME, R.BIZ_OFF_MINUTE, 
				R.REAL_OVER, R.REAL_AVG, R.REAL_OFF, R.PAY 
			FROM 
			( 
				SELECT 
					T.BIZ_COMMUTE, T.LAW_COMMUTE, T.LATENESS, T.VACATION1, T.VACATION2, T.VACATION3, T.REFRESH, T.PROJECT, T.NOMONEY, T.EDU, T.ETC, 
					T.COMMUTE_DATE, T.SUBVACATION1, T.SUBVACATION2, T.AVGTIME1, T.AVGMINUTE1, T.AVGTIME2, T.AVGMINUTE2, T.AVG_TIME, T.AVG_MINUTE, T.BIZ_TOTAL_TIME, T.BIZ_TOTAL_MINUTE, 
					T.TOTAL_TIME, T.TOTAL_MINUTE, T.OVER_TIME, T.OVER_MINUTE, T.OVER_DATE, T.UNDER_TIME, T.UNDER_MINUTE, T.UNDER_DATE, T.OFF_TIME, T.OFF_MINUTE, T.BIZ_OFF_TIME, T.BIZ_OFF_MINUTE, 
					(T.REAL_OVER - (T.PAY * 60)) AS REAL_OVER, T.REAL_AVG, T.REAL_OFF, T.PAY 
				FROM 
				( 
					SELECT 
						D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION1, D.VACATION2, D.VACATION3, D.REFRESH, D.PROJECT, D.NOMONEY, D.EDU, D.ETC, D.COMMUTE_DATE, 
						D.SUBVACATION1, D.SUBVACATION2, D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
						((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) / 60) AS AVG_TIME, 
						((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) % 60) AS AVG_MINUTE, 
						D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, D.TOTAL_TIME, D.TOTAL_MINUTE, D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, D.UNDER_TIME, D.UNDER_MINUTE, D.UNDER_DATE, D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, (D.REAL_OVER - (D.PAY * 60)) AS REAL_OVER, 
						D.REAL_AVG, D.REAL_OFF, D.PAY 
					FROM 
					(
						SELECT 
							* 
						FROM 
							DF_PERSON A
						WHERE 
							PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN (15,22,24,87,90,102) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
							AND (SELECT COUNT(SEQNO) 
								FROM DF_CHECKTIME WITH(NOLOCK) 
								WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0
					) P 
					INNER JOIN 
					( 
						SELECT 
							PRS_ID, 
							(SELECT COUNT(A.SEQNO) FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE WHERE A.GUBUN1 IN (1,4,6,7,8) AND A.GUBUN2 IN (2,3,5,6,9) AND A.PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND A.DATE LIKE '". $date . "%' AND B.DATEKIND = 'BIZ') AS BIZ_COMMUTE, 
							(SELECT COUNT(A.SEQNO) FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE WHERE A.PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND A.DATE LIKE '". $date . "%' AND B.DATEKIND IN ('FIN','LAW')) AS LAW_COMMUTE, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (7) AND GUBUN2 IN (2,3,6) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS LATENESS, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (10) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS VACATION1, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (11) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS VACATION2, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (12) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS VACATION3, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (17) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS REFRESH, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (16) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS PROJECT, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (18) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS NOMONEY, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (15) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS EDU, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (13) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS ETC, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS COMMUTE_DATE, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN1 IN (4,8) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS SUBVACATION1, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE GUBUN2 IN (5,9) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') AS SUBVACATION2, 
							(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 FROM ( SELECT SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9)) Y) AS AVGTIME1, 
							(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 FROM ( SELECT SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9)) Y) AS AVGMINUTE1, 
							(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 FROM ( SELECT SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6)) Y) AS AVGTIME2, 
							(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 FROM ( SELECT SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6)) Y) AS AVGMINUTE2, 
							(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS BIZ_TOTAL_TIME, 
							(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60 FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS BIZ_TOTAL_MINUTE, 
							(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS TOTAL_TIME, 
							(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS TOTAL_MINUTE, 
							(SELECT SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60) / 3600 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_TIME, 
							(SELECT (SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60)) %3600 /60 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_MINUTE, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%' AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_DATE, 
							(SELECT SUM(SUBSTRING(UNDERTIME, 1,2) * 3600 + SUBSTRING(UNDERTIME, 3,2) * 60) / 3600 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND UNDERTIME > '0000' AND UNDERTIME = '0000') AS UNDER_TIME, 
							(SELECT (SUM(SUBSTRING(UNDERTIME, 1,2) * 3600 + SUBSTRING(UNDERTIME, 3,2) * 60)) %3600 /60 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND UNDERTIME > '0000' AND UNDERTIME = '0000') AS UNDER_MINUTE, 
							(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%' AND UNDERTIME > '0000' AND UNDERTIME = '0000') AS UNDER_DATE, 
							(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS OFF_TIME, 
							(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) %3600 /60 FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS OFF_MINUTE, 
							(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS BIZ_OFF_TIME, 
							(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) % 3600 /60 FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."')) AS BIZ_OFF_MINUTE, 
							((SELECT ISNULL(SUM(SUBSTRING(OVERTIME, 1,2) * 60 + SUBSTRING(OVERTIME, 3,2)),0) FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND OVERTIME > '0000' AND UNDERTIME = '0000') -(SELECT ISNULL(SUM(SUBSTRING(UNDERTIME, 1,2) * 60 + SUBSTRING(UNDERTIME, 3,2)),0) FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND UNDERTIME > '0000' AND OVERTIME = '0000')) AS REAL_OVER, 
							(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0) FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND TOTALTIME > '0000' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9)) AS REAL_AVG, 
							(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0) FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date . "%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND TOTALTIME > '0000') AS REAL_OFF, 
							((SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY1 = 'Y' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%') +(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY2 = 'Y' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM_Old IN (@divSql@) AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."') AND DATE LIKE '". $date . "%')) AS PAY 
						FROM DF_CHECKTIME C WITH(NOLOCK) 
						WHERE PRS_ID = C.PRS_ID 
					) D 
					ON P.PRS_ID = D.PRS_ID WHERE P.PRS_TEAM_Old IN (@divSql@) 
				) T 
			) R";

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=�������_".$p_year.$p_month.".xls" );
?>

	<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=EUC-KR'>
	<style>
	<!--
	br{mso-data-placement:same-cell;}
	-->
	</style>
	<table border=0>
		<tr>
			<td colspan="8" style="font-size:12px;font-weight:bold;text-align:left;"><?=$p_year?>�� <?=$p_month?>�� ������Ȳ</td>
			<td colspan="11" style="font-size:12px;font-weight:bold;text-align:right;">Working days = <?=$biz_day?> days / Working time = <?=$biz_day*9?></td>
		</tr>
	</table>
	<table border=1>
		<thead>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�μ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�̸�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���<br>��ٽð�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���<br>��ٽð�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���<br>�ٹ��ð�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">��<br>�ٹ��ð�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�ٹ�<br>�ϼ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�̸�<br>�ٹ��ϼ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�ް�<br>(����/����)</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">������</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">��������<br>�ް�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">������Ʈ<br>�ް�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����<br>�ް�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����<br>/�Ʒ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">��Ÿ</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����<br>���</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���</td>
			</tr>
		</thead>
		<tbody>
<?
		$teamList = "'dm1'##'dm2'##'digital marketing division'##'dx1'##'dx2'##'digital experience division'##'brand experience team'##'design1'##'design2'##'design1 division'##'design3'##'design4'##'design5'##'design2 division'##'fc'##'film & content division'##'mg1'##'mg2'##'motion graphic division'##'ix1'##'ix2'##'df lab'##'ixd'##'�濵������'";
		$teamList1 = "##dm1##dm2##dx1##dx2##brand experience team##design1##design2##design3##design4##design5##fc##mg1##mg2##ix1##ix2##ixd##�濵������";
		$teamList2 = "'dm1','dm2','digital marketing division'##'dx1','dx2','digital experience division'##'design1','design2','design1 division'##'design3','design4','design5','design2 division'##'fc','film & content division'##'mg1','mg2','motion graphic division'##'ix1','ix2','df lab'";

		$teamArr = explode("##",$teamList);
		$divArr = explode("##",$teamList2);

		for ($i=0; $i<sizeof($teamArr); $i++)
		{
			$sql = "SELECT 
						COUNT(*) 
					FROM 
						DF_PERSON A WITH(NOLOCK) 
					WHERE 
						PRS_TEAM_Old IN (". $teamArr[$i] . ") AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
						AND (SELECT COUNT(SEQNO) 
								FROM DF_CHECKTIME WITH(NOLOCK) 
								WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0
			";
			$rs = sqlsrv_query($dbConn,$sql);

			$record = sqlsrv_fetch_array($rs);
			$teamMember = $record[0];

			$sql = str_replace("@teamSql@"," P.PRS_TEAM_Old IN (". $teamArr[$i] .")",$teamSql);
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

			if (strpos($teamList1,str_replace("'","",$teamArr[$i])) > 0 && $teamMember == 0) 
			{
			}
			else if (sqlsrv_has_rows($rs) > 0)
			{
				while ($record = sqlsrv_fetch_array($rs))
				{
					$team_login = $record['PRS_LOGIN'];
					$team_id = $record['PRS_ID'];
					$team_name = $record['PRS_NAME'];
					$team_team = $record['PRS_TEAM_Old'];
					$team_position = $record['PRS_POSITION'];

					$biz_commute = $record['BIZ_COMMUTE'];	//�����������
					$law_commute = $record['LAW_COMMUTE'];	//�������
					$lateness = $record['LATENESS'];			//����
					$vacation1 = $record['VACATION1'];				//����
					$vacation2 = $record['VACATION2'];				//����
					$vacation3 = $record['VACATION3'];				//������
					$refresh = $record['REFRESH'];					//���������ް�
					$project = $record['PROJECT'];					//������Ʈ�ް�
					$nomoney = $record['NOMONEY'];					//�����ް�
					$edu = $record['EDU'];							//����/�Ʒ�
					$etc = $record['ETC'];							//��Ÿ
					$commute_day = $record['COMMUTE_DATE'];			//�ٹ��ϼ�
					$subvacation1 = $record['SUBVACATION1'];	//��������
					$subvacation2 = $record['SUBVACATION2'];	//���Ĺ���
					$avgtime1 = $record['AVGTIME1'];				//�����ٽ�
					$avgminute1 = $record['AVGMINUTE1'];			//�����ٺ�
					$avgtime2 = $record['AVGTIME2'];				//�����ٽ�
					$avgminute2 = $record['AVGMINUTE2'];			//�����ٺ�
					$avg_time = $record['AVG_TIME'];				//��ձٹ��ð���
					$avg_minute = $record['AVG_MINUTE'];			//��ձٹ��ð���
					$biz_total_time = $record['BIZ_TOTAL_TIME'];		//�����ѱٹ��ð���
					$biz_total_minuate = $record['BIZ_TOTAL_MINUTE'];	//�����ѱٹ��ð���
					$total_time = $record['TOTAL_TIME'];			//�ѱٹ��ð���
					$total_minute = $record['TOTAL_MINUTE'];		//�ѱٹ��ð���
					$over_time = $record['OVER_TIME'];				//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
					$over_minute = $record['OVER_MINUTE'];			//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
					$over_day = $record['OVER_DATE'];				//�ʰ��ϼ�
					$under_time = $record['UNDER_TIME'];			//�̸��ٹ��ð���
					$under_minute = $record['UNDER_MINUTE'];		//�̸��ٹ��ð���
					$under_day = $record['UNDER_DATE'];				//�̸��ϼ�
					$off_time = $record['OFF_TIME'];				//���� ��
					$off_minute = $record['OFF_MINUTE'];			//���� ��
					$biz_off_time = $record['BIZ_OFF_TIME'];		//���Ͽ��� ��
					$biz_off_minute = $record['BIZ_OFF_MINUTE'];	//���Ͽ��� ��
					$real_over = $record['REAL_OVER'];				//����ٹ��ð��д���
					$real_avg = $record['REAL_AVG'];				//��ձٹ��ð��д���
					$real_off = $record['REAL_OFF'];				//��տ���ð��д���

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

					//����ð� ������ �� �ٹ� �ð� ���
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
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$team_name?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$team_position?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avgtime1?> : <?=$avgminute1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avgtime2?> : <?=$avgminute2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$avg_time?> : <?=$avg_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$total_time?> : <?=$total_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$commute_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$under_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$subvacation1+$subvacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$vacation1+$vacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$vacation3?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$refresh?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$project?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$nomoney?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$edu?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$etc?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$law_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"></td>
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

					$j++;
				}
				
				if (strpos($teamList1,$team_team) > 0) 
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
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_avgtime1?> : <?=$avg_avgminute1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_avgtime2?> : <?=$avg_avgminute2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_avg_time?> : <?=$avg_avg_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_total_time?> : <?=$avg_total_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$avg_commute_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_under_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_subvacation1+$total_subvacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_vacation1+$total_vacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_vacation3?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_refresh?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_project?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_nomoney?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_edu?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_etc?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"><?=$total_law_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#ffead0;"></td>
			</tr>
<?
				}
				else
				{
					switch ($team_team)
					{
						case "digital marketing division" :
							$div = $divArr[0];
							break;
						case "digital experience division" :
							$div = $divArr[1];
							break;
						case "design1 division" : 
							$div = $divArr[2];
							break;
						case "design2 division" :
							$div = $divArr[3];
							break;
						case "film & content division" :
							$div = $divArr[4];
							break;
						case "motion graphic division" :
							$div = $divArr[5];
							break;
						case "df lab" :
							$div = $divArr[6];
							break;
					}

					$sql2 = "SELECT 
								COUNT(*) 
							FROM 
								DF_PERSON A WITH(NOLOCK) 
							WHERE 
								PRS_TEAM_Old IN (". $div . ") AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
								AND (SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0
					";
					$rs2 = sqlsrv_query($dbConn,$sql2);

					$record2 = sqlsrv_fetch_array($rs2);
					$divMember = $record2[0];

					$div_avgtime1 = 0;
					$div_avgminute1 = 0;
					$div_avgtime2 = 0;
					$div_avgminute2 = 0;
					$div_avg_time = 0;
					$div_avg_minute = 0;
					$div_total_time = 0;
					$div_total_minute = 0;
					$div_commute_day = 0;

					$div_under_day = 0;
					$div_subvacation1 = 0;
					$div_subvacation2 = 0;
					$div_vacation1 = 0;
					$div_vacation2 = 0;
					$div_vacation3 = 0;
					$div_refresh = 0;
					$div_project = 0;
					$div_nomoey = 0;
					$div_edu = 0;
					$div_etc = 0;
					$div_law_commute = 0;

					$sql2 = str_replace("@divSql@", $div, $divSql);
					$rs2 = sqlsrv_query($dbConn,$sql2);

					$record2 = sqlsrv_fetch_array($rs2);

					$team_team = $record2['PRS_TEAM_Old'];

					$div_biz_commute = $record2['BIZ_COMMUTE'];	//�����������
					$div_law_commute = $record2['LAW_COMMUTE'];	//�������
					$div_lateness = $record2['LATENESS'];			//����
					$div_vacation1 = $record2['VACATION1'];				//����
					$div_vacation2 = $record2['VACATION2'];				//����
					$div_vacation3 = $record2['VACATION3'];				//������
					$div_refresh = $record2['REFRESH'];					//���������ް�
					$div_project = $record2['PROJECT'];					//������Ʈ�ް�
					$div_nomoney = $record2['NOMONEY'];					//�����ް�
					$div_edu = $record2['EDU'];							//����/�Ʒ�
					$div_etc = $record2['ETC'];							//��Ÿ
					$div_commute_day = $record2['COMMUTE_DATE'];			//�ٹ��ϼ�
					$div_subvacation1 = $record2['SUBVACATION1'];	//��������
					$div_subvacation2 = $record2['SUBVACATION2'];	//���Ĺ���
					$div_avgtime1 = $record2['AVGTIME1'];				//�����ٽ�
					$div_avgminute1 = $record2['AVGMINUTE1'];			//�����ٺ�
					$div_avgtime2 = $record2['AVGTIME2'];				//�����ٽ�
					$div_avgminute2 = $record2['AVGMINUTE2'];			//�����ٺ�
					$div_avg_time = $record2['AVG_TIME'];				//��ձٹ��ð���
					$div_avg_minute = $record2['AVG_MINUTE'];			//��ձٹ��ð���
					$div_biz_total_time = $record2['BIZ_TOTAL_TIME'];		//�����ѱٹ��ð���
					$div_biz_total_minuate = $record2['BIZ_TOTAL_MINUTE'];	//�����ѱٹ��ð���
					$div_total_time = $record2['TOTAL_TIME'];			//�ѱٹ��ð���
					$div_total_minute = $record2['TOTAL_MINUTE'];		//�ѱٹ��ð���
					$div_over_time = $record2['OVER_TIME'];				//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
					$div_over_minute = $record2['OVER_MINUTE'];			//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
					$div_over_day = $record2['OVER_DATE'];				//�ʰ��ϼ�
					$div_under_time = $record2['UNDER_TIME'];			//�̸��ٹ��ð���
					$div_under_minute = $record2['UNDER_MINUTE'];		//�̸��ٹ��ð���
					$div_under_day = $record2['UNDER_DATE'];				//�̸��ϼ�
					$div_off_time = $record2['OFF_TIME'];				//���� ��
					$div_off_minute = $record2['OFF_MINUTE'];			//���� ��
					$div_biz_off_time = $record2['BIZ_OFF_TIME'];		//���Ͽ��� ��
					$div_biz_off_minute = $record2['BIZ_OFF_MINUTE'];	//���Ͽ��� ��
					$div_real_over = $record2['REAL_OVER'];				//����ٹ��ð��д���
					$div_real_avg = $record2['REAL_AVG'];				//��ձٹ��ð��д���
					$div_real_off = $record2['REAL_OFF'];				//��տ���ð��д���

					if ($div_avgtime1 == "") { $div_avgtime1 = "0"; }
					if ($div_avgminute1 == "") { $div_avgminute1 = "0"; }
					if ($div_avgtime2 == "") { $div_avgtime2 = "0"; }
					if ($div_avgminute2 == "") { $div_avgminute2 = "0"; }
					if ($div_avg_time == "") { $div_avg_time = "0"; }
					if ($div_avg_minute == "") { $div_avg_minute = "0"; }
					if ($div_biz_total_time == "") { $div_biz_total_time = "0"; }
					if ($div_biz_total_minute == "") { $div_biz_total_minute = "0"; }
					if ($div_total_time == "") { $div_total_time = "0"; }
					if ($div_total_minute == "") { $div_total_minute = "0"; }
					if ($div_over_time == "") { $div_over_time = "0"; }
					if ($div_over_minute == "") { $div_over_minute = "0"; }
					if ($div_off_time == "") { $div_off_time = "0"; }
					if ($div_off_minute == "") { $div_off_minute = "0"; }
					if ($div_biz_off_time == "") { $div_biz_off_time = "0"; }
					if ($div_biz_off_minute == "") { $div_biz_off_minute = "0"; }

					//����ð� ������ �� �ٹ� �ð� ���
					if ($div_off_time > 0 && $div_off_minute > 0)
					{
						if ($div_total_minute < $div_off_minute)
						{
							$div_total_minute = $div_total_minute - $div_off_minute + 60;
							$div_total_time = $div_total_time - $div_off_time - 1;
						}
						else
						{
							$div_total_minute = $div_total_minute - $div_off_minute;
							$div_total_time = $div_total_time - $div_off_time - 1;
						}
						if ($div_total_time == -1)
						{
							$div_total_time = 0;
						}
					}
					//

					if (substr($div_real_over,0,1) == "-") 
					{
						$div_flag1 = "-";
						$div_real_over = substr($div_real_over,1,strlen($div_real_over));
					}

					$div_total = ($div_total_time * 60 + $div_total_minute) / $divMember;

					$div_total_time = floor($div_total / 60);
					$div_total_minute = number_format($div_total % 60,0);

					$div_commute_day = number_format($div_commute_day / $divMember,1);

					if (strlen($div_avgtime1) == 1) { $div_avgtime1 = "0".$div_avgtime1; }
					if (strlen($div_avgminute1) == 1) { $div_avgminute1 = "0".$div_avgminute1; }
					if (strlen($div_avgtime2) == 1) { $div_avgtime2 = "0".$div_avgtime2; }
					if (strlen($div_avgminute2) == 1) { $div_avgminute2 = "0".$div_avgminute2; }
					if (strlen($div_avg_time) == 1) { $div_avg_time = "0".$div_avg_time; }
					if (strlen($div_avg_minute) == 1) { $div_avg_minute = "0".$div_avg_minute; }
					if (strlen($div_total_time) == 1) { $div_total_time = "0".$div_total_time; }
					if (strlen($div_total_minute) == 1) { $div_total_minute = "0".$div_total_minute; }
					if (strlen($div_over_time) == 1) { $div_over_time = "0".$div_over_time; }
					if (strlen($div_over_minute) == 1) { $div_over_minute = "0".$div_over_minute; }
?>
			<tr>
				<td colspan="2" style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;">Total</td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_avgtime1?> : <?=$div_avgminute1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_avgtime2?> : <?=$div_avgminute2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_avg_time?> : <?=$div_avg_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_total_time?> : <?=$div_total_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_commute_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_under_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_subvacation1+$div_subvacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_vacation1+$div_vacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_vacation3?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_refresh?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_project?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_nomoney?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_edu?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_etc?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_law_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"></td>
			</tr>
<?
				}
			}
			else
			{
				switch (str_replace("'","",$teamArr[$i]))
				{
					case "digital marketing division" :
						$div = $divArr[0];
						break;
					case "digital experience division" :
						$div = $divArr[1];
						break;
					case "design1 division" : 
						$div = $divArr[2];
						break;
					case "design2 division" :
						$div = $divArr[3];
						break;
					case "film & content division" :
						$div = $divArr[4];
						break;
					case "motion graphic division" :
						$div = $divArr[5];
						break;
					case "df lab" :
						$div = $divArr[6];
						break;
				}

				$sql2 = "SELECT 
							COUNT(*) 
						FROM 
							DF_PERSON A WITH(NOLOCK) 
						WHERE 
							PRS_TEAM_Old IN (". $div . ") AND PRF_ID IN (1,2,3,4) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
							AND (SELECT COUNT(SEQNO) 
									FROM DF_CHECKTIME WITH(NOLOCK) 
									WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0
				";
				$rs2 = sqlsrv_query($dbConn,$sql2);

				$record2 = sqlsrv_fetch_array($rs2);
				$divMember = $record2[0];

				$div_avgtime1 = 0;
				$div_avgminute1 = 0;
				$div_avgtime2 = 0;
				$div_avgminute2 = 0;
				$div_avg_time = 0;
				$div_avg_minute = 0;
				$div_total_time = 0;
				$div_total_minute = 0;
				$div_commute_day = 0;

				$div_under_day = 0;
				$div_subvacation1 = 0;
				$div_subvacation2 = 0;
				$div_vacation1 = 0;
				$div_vacation2 = 0;
				$div_vacation3 = 0;
				$div_refresh = 0;
				$div_project = 0;
				$div_nomoey = 0;
				$div_edu = 0;
				$div_etc = 0;
				$div_law_commute = 0;

				$sql2 = str_replace("@divSql@", $div, $divSql);
				$rs2 = sqlsrv_query($dbConn,$sql2);

				$record2 = sqlsrv_fetch_array($rs2);

				$team_team = $record2['PRS_TEAM_Old'];

				$div_biz_commute = $record2['BIZ_COMMUTE'];	//�����������
				$div_law_commute = $record2['LAW_COMMUTE'];	//�������
				$div_lateness = $record2['LATENESS'];			//����
				$div_vacation1 = $record2['VACATION1'];				//����
				$div_vacation2 = $record2['VACATION2'];				//����
				$div_vacation3 = $record2['VACATION3'];				//������
				$div_refresh = $record2['REFRESH'];					//���������ް�
				$div_project = $record2['PROJECT'];					//������Ʈ�ް�
				$div_nomoney = $record2['NOMONEY'];					//�����ް�
				$div_edu = $record2['EDU'];							//����/�Ʒ�
				$div_etc = $record2['ETC'];							//��Ÿ
				$div_commute_day = $record2['COMMUTE_DATE'];			//�ٹ��ϼ�
				$div_subvacation1 = $record2['SUBVACATION1'];	//��������
				$div_subvacation2 = $record2['SUBVACATION2'];	//���Ĺ���
				$div_avgtime1 = $record2['AVGTIME1'];				//�����ٽ�
				$div_avgminute1 = $record2['AVGMINUTE1'];			//�����ٺ�
				$div_avgtime2 = $record2['AVGTIME2'];				//�����ٽ�
				$div_avgminute2 = $record2['AVGMINUTE2'];			//�����ٺ�
				$div_avg_time = $record2['AVG_TIME'];				//��ձٹ��ð���
				$div_avg_minute = $record2['AVG_MINUTE'];			//��ձٹ��ð���
				$div_biz_total_time = $record2['BIZ_TOTAL_TIME'];		//�����ѱٹ��ð���
				$div_biz_total_minuate = $record2['BIZ_TOTAL_MINUTE'];	//�����ѱٹ��ð���
				$div_total_time = $record2['TOTAL_TIME'];			//�ѱٹ��ð���
				$div_total_minute = $record2['TOTAL_MINUTE'];		//�ѱٹ��ð���
				$div_over_time = $record2['OVER_TIME'];				//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
				$div_over_minute = $record2['OVER_MINUTE'];			//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
				$div_over_day = $record2['OVER_DATE'];				//�ʰ��ϼ�
				$div_under_time = $record2['UNDER_TIME'];			//�̸��ٹ��ð���
				$div_under_minute = $record2['UNDER_MINUTE'];		//�̸��ٹ��ð���
				$div_under_day = $record2['UNDER_DATE'];				//�̸��ϼ�
				$div_off_time = $record2['OFF_TIME'];				//���� ��
				$div_off_minute = $record2['OFF_MINUTE'];			//���� ��
				$div_biz_off_time = $record2['BIZ_OFF_TIME'];		//���Ͽ��� ��
				$div_biz_off_minute = $record2['BIZ_OFF_MINUTE'];	//���Ͽ��� ��
				$div_real_over = $record2['REAL_OVER'];				//����ٹ��ð��д���
				$div_real_avg = $record2['REAL_AVG'];				//��ձٹ��ð��д���
				$div_real_off = $record2['REAL_OFF'];				//��տ���ð��д���

				if ($div_avgtime1 == "") { $div_avgtime1 = "0"; }
				if ($div_avgminute1 == "") { $div_avgminute1 = "0"; }
				if ($div_avgtime2 == "") { $div_avgtime2 = "0"; }
				if ($div_avgminute2 == "") { $div_avgminute2 = "0"; }
				if ($div_avg_time == "") { $div_avg_time = "0"; }
				if ($div_avg_minute == "") { $div_avg_minute = "0"; }
				if ($div_biz_total_time == "") { $div_biz_total_time = "0"; }
				if ($div_biz_total_minute == "") { $div_biz_total_minute = "0"; }
				if ($div_total_time == "") { $div_total_time = "0"; }
				if ($div_total_minute == "") { $div_total_minute = "0"; }
				if ($div_over_time == "") { $div_over_time = "0"; }
				if ($div_over_minute == "") { $div_over_minute = "0"; }
				if ($div_off_time == "") { $div_off_time = "0"; }
				if ($div_off_minute == "") { $div_off_minute = "0"; }
				if ($div_biz_off_time == "") { $div_biz_off_time = "0"; }
				if ($div_biz_off_minute == "") { $div_biz_off_minute = "0"; }

				//����ð� ������ �� �ٹ� �ð� ���
				if ($div_off_time > 0 && $div_off_minute > 0)
				{
					if ($div_total_minute < $div_off_minute)
					{
						$div_total_minute = $div_total_minute - $div_off_minute + 60;
						$div_total_time = $div_total_time - $div_off_time - 1;
					}
					else
					{
						$div_total_minute = $div_total_minute - $div_off_minute;
						$div_total_time = $div_total_time - $div_off_time - 1;
					}
					if ($div_total_time == -1)
					{
						$div_total_time = 0;
					}
				}
				//

				if (substr($div_real_over,0,1) == "-") 
				{
					$div_flag1 = "-";
					$div_real_over = substr($div_real_over,1,strlen($div_real_over));
				}

				if ($divMember > 0)
				{
					$div_total = ($div_total_time * 60 + $div_total_minute) / $divMember;
				}
				else
				{
					$div_total = 0;
				}

				$div_total_time = floor($div_total / 60);
				$div_total_minute = number_format($div_total % 60,0);

				if ($divMember > 0)
				{
					$div_commute_day = number_format($div_commute_day / $divMember,1);
				}
				else
				{
					$div_commute_day = 0;
				}

				if (strlen($div_avgtime1) == 1) { $div_avgtime1 = "0".$div_avgtime1; }
				if (strlen($div_avgminute1) == 1) { $div_avgminute1 = "0".$div_avgminute1; }
				if (strlen($div_avgtime2) == 1) { $div_avgtime2 = "0".$div_avgtime2; }
				if (strlen($div_avgminute2) == 1) { $div_avgminute2 = "0".$div_avgminute2; }
				if (strlen($div_avg_time) == 1) { $div_avg_time = "0".$div_avg_time; }
				if (strlen($div_avg_minute) == 1) { $div_avg_minute = "0".$div_avg_minute; }
				if (strlen($div_total_time) == 1) { $div_total_time = "0".$div_total_time; }
				if (strlen($div_total_minute) == 1) { $div_total_minute = "0".$div_total_minute; }
				if (strlen($div_over_time) == 1) { $div_over_time = "0".$div_over_time; }
				if (strlen($div_over_minute) == 1) { $div_over_minute = "0".$div_over_minute; }
?>
			<tr>
				<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=str_replace("'","",$teamArr[$i])?></td>
				<td colspan="2" style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;">Total</td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_avgtime1?> : <?=$div_avgminute1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_avgtime2?> : <?=$div_avgminute2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_avg_time?> : <?=$div_avg_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_total_time?> : <?=$div_total_minute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_commute_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_under_day?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_subvacation1+$div_subvacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_vacation1+$div_vacation2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_vacation3?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_refresh?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_project?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_nomoney?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_edu?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_etc?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"><?=$div_law_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';background:#becdff;"></td>
			</tr>
<?
			}
		}
?>
		</tbody>
	</table>
