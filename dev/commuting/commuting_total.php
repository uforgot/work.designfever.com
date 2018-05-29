<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
?>

<?
	//���� üũ
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("�ش��������� �ӿ�,�����ڸ� Ȯ�� �����մϴ�.");
		location.href="commuting_list.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null; 
	$p_period = isset($_REQUEST['period']) ? $_REQUEST['period'] : "day"; 

	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "name"; 

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	$nameSQL = "";
	if ($p_name != "")
	{
		$nameSQL .= " AND P.PRS_NAME Like '%". $p_name ."%'";
	}

	$team_login = "";
	$team_id = "";
	$team_name = "";
	$team_team = "";
	$team_position = "";
	$team_date = "";

	$per_page = 20;

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN P.PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	if ($p_period == "day")
	{
		$p_fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : null; 
		$p_fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : null; 
		$p_fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : null; 
		$p_to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : null; 
		$p_to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : null; 
		$p_to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : null; 

		if ($p_fr_year == "") $p_fr_year = $nowYear;
		if ($p_fr_month == "") $p_fr_month = $nowMonth;
		if ($p_fr_day == "") $p_fr_day = $nowDay;
		if ($p_to_year == "") $p_to_year = $nowYear;
		if ($p_to_month == "") $p_to_month = $nowMonth;
		if ($p_to_day == "") $p_to_day = $nowDay;

		$fr_date = $p_fr_year ."-". $p_fr_month ."-". $p_fr_day;
		$to_date = $p_to_year ."-". $p_to_month ."-". $p_to_day;

		$p_gubun1 = isset($_REQUEST['gubun1']) ? $_REQUEST['gubun1'] : null;
		$p_gubun2 = isset($_REQUEST['gubun2']) ? $_REQUEST['gubun2'] : null;

		$gubunSQL = "";
		if ($p_gubun1 != "" && $p_gubun2 != "")
		{
				$gubunSQL .=" AND (B.GUBUN1 = '$p_gubun1' OR B.GUBUN2 = '$p_gubun2')";
		}
		else
		{
			if ($p_gubun1 != "")
			{
				$gubunSQL .= " AND B.GUBUN1 = '$p_gubun1'";
			}
			if ($p_gubun2 != "")
			{
				$gubunSQL .= " AND B.GUBUN2 = '$p_gubun2'";
			}
		}

		switch($sort)
		{
			case "name" : 
				$orderbycase = "ORDER BY P.PRS_NAME";
				break;
			case "position" : 
				$orderbycase = "ORDER BY CASE ". $orderby . " END, P.PRS_NAME";
				break;
			case "checktime1" :
				$orderbycase = "ORDER BY B.CHECKTIME1, P.PRS_NAME";
				break;
		}

		$sql = "SELECT 
					COUNT(*) 
				FROM 
					DF_PERSON P WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
				ON 
					P.PRS_ID = B.PRS_ID 
				WHERE 
					P.PRF_ID IN (1,2,3,4,5)". $nameSQL . $gubunSQL ." AND P.PRS_ID NOT IN (15,22,24,87,102,148)
					AND B.DATE BETWEEN '$fr_date' AND '$to_date'";
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		$total_cnt = $record[0];

		$sql = "SELECT 
					T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION, T.DATE 
				FROM 
				(
					SELECT
						ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
						P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, B.DATE 
					FROM 
						DF_PERSON P WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK)
					ON
						P.PRS_ID = B.PRS_ID
					WHERE 
						P.PRF_ID IN (1,2,3,4,5) AND P.PRS_ID NOT IN (15,22,24,87,102,148) $nameSQL $gubunSQL
						AND B.DATE BETWEEN '$fr_date' AND '$to_date'
				) T
				WHERE
					T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
		$rs = sqlsrv_query($dbConn, $sql);
	}
	else
	{
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

		if ($p_year == $nowYear && $p_month == $nowMonth && $nowDay < 10) {
?>
			<script>
				alert("<?=$nowYear?>�� <?=$nowMonth?>���� ���� ���� 10�� ���Ŀ� ���� �� �ֽ��ϴ�.");
				location.href = "commuting_total.php?period=month";
			</script>
<?
			exit;
		}
		else {
			if ($p_year > $nowYear || ($p_year == $nowYear && $p_month > $nowMonth)) {
?>
			<script>
				alert("<?=$nowYear?>�� <?=$nowMonth?>�� ������ ���� ���� ���� ���� �� �����ϴ�.");
				location.href = "commuting_total.php?period=month";
			</script>
<?
			exit;
			}
		}

		if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

		$date = $p_year."-". $p_month;

		switch($sort)
		{
			case "name" : 
				$orderbycase = "ORDER BY PRS_NAME";
				break;
			case "position" : 
				$orderbycase = "ORDER BY CASE ". $orderby . " END, PRS_NAME";
				break;
			case "avg" : 
				$orderbycase = "ORDER BY AVG_TIME DESC, AVG_MINUTE DESC, PRS_NAME";
				break;
			case "over" : 
				$orderbycase = "ORDER BY REAL_OVER DESC";
				break;
		}

		$sql = "SELECT 
					COUNT(DISTINCT P.PRS_ID) 
				FROM 
					DF_PERSON P WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
				ON 
					P.PRS_ID = B.PRS_ID 
				WHERE 
					P.PRF_ID IN (1,2,3,4,5)". $nameSQL ." AND P.PRS_ID NOT IN (15,22,24,87,102,148)
					AND B.DATE LIKE '$date%'";
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		$total_cnt = $record[0];

		$sql = "SELECT 
					R.PRS_LOGIN, R.PRS_ID, R.PRS_NAME, R.PRS_TEAM, R.PRS_POSITION, 
					R.BIZ_COMMUTE, R.LAW_COMMUTE, R.LATENESS, R.VACATION, R.COMMUTE_DATE, R.SUBVACATION1, R.SUBVACATION2, 
					R.AVGTIME1, R.AVGMINUTE1, R.AVGTIME2, R.AVGMINUTE2, R.AVG_TIME, R.AVG_MINUTE, 
					R.BIZ_TOTAL_TIME, R.BIZ_TOTAL_MINUTE, R.TOTAL_TIME, R.TOTAL_MINUTE, R.OVER_TIME, R.OVER_MINUTE, R.OVER_DATE, 
					R.OFF_TIME, R.OFF_MINUTE, R.BIZ_OFF_TIME, R.BIZ_OFF_MINUTE, R.REAL_OVER, R.REAL_AVG, R.REAL_OFF, R.PAY  
				FROM 
				(
					SELECT 
						ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
						T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION, 
						T.BIZ_COMMUTE, T.LAW_COMMUTE, T.LATENESS, T.VACATION, T.COMMUTE_DATE, T.SUBVACATION1, T.SUBVACATION2, 
						T.AVGTIME1, T.AVGMINUTE1, T.AVGTIME2, T.AVGMINUTE2, T.AVG_TIME, T.AVG_MINUTE, 
						T.BIZ_TOTAL_TIME, T.BIZ_TOTAL_MINUTE, T.TOTAL_TIME, T.TOTAL_MINUTE, T.OVER_TIME, T.OVER_MINUTE, T.OVER_DATE, 
						T.OFF_TIME, T.OFF_MINUTE, T.BIZ_OFF_TIME, T.BIZ_OFF_MINUTE, (T.REAL_OVER - (T.PAY * 60)) AS REAL_OVER, T.REAL_AVG, T.REAL_OFF, T.PAY  
					FROM
					(
						SELECT
							P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
							D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION, D.COMMUTE_DATE, D.SUBVACATION1, D.SUBVACATION2, 
							D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
							((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) / 60) AS AVG_TIME, 
							((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) % 60) AS AVG_MINUTE, 
							D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, 
							D.TOTAL_TIME, D.TOTAL_MINUTE, D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, 
							D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, (D.REAL_OVER - (D.PAY * 60)) AS REAL_OVER, D.REAL_AVG, D.REAL_OFF, D.PAY  
						FROM 
							(
								SELECT 
									* 
								FROM 
									DF_PERSON A
								WHERE 
									PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,102,148) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
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
										WHERE A.GUBUN1 IN (1,6,7) AND A.GUBUN2 IN (2,3,6) AND A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $date ."%' AND B.DATEKIND = 'BIZ') AS BIZ_COMMUTE, --���� �������
									(SELECT COUNT(A.SEQNO) 
										FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
										WHERE A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $date ."%' AND B.DATEKIND IN ('FIN','LAW')) AS LAW_COMMUTE, --���� �ٹ���
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (7) AND GUBUN2 IN (2,3,6) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS LATENESS, --���� 
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (10,11,12,13,14,16,17,18,19,20,21) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS VACATION, --�ް� 
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS COMMUTE_DATE, --�ٹ��ϼ�
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (4,8) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS SUBVACATION1, --��������
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN2 IN (5,9) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS SUBVACATION2, --���Ĺ��� 
									(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGTIME1, --�����ٽ�
									(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
											WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGMINUTE1, --�����ٺ�
									(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
											WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGTIME2, --�����ٽ� 
									(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
											WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGMINUTE2, --�����ٺ� 
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') 
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS BIZ_TOTAL_TIME, --���� �� �ٹ��ð� ��
									(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') 
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS BIZ_TOTAL_MINUTE, --���� �� �ٹ��ð� ��	
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_TIME, --�� �ٹ��ð� ��
									(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_MINUTE, --�� �ٹ��ð� ��	
									(SELECT SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60) / 3600
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_TIME, --�ʰ� �ٹ��ð� ��
									(SELECT (SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60)) %3600 /60 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_MINUTE, --�ʰ� �ٹ��ð� ��
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK)
										WHERE PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%' AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_DATE, --�ʰ� �ٹ��ϼ�
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 
										FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS OFF_TIME, --���� ��
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) %3600 /60 
										FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS OFF_MINUTE, -- ���� �� 					
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS BIZ_OFF_TIME, --���� ���� ��
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) % 3600 /60
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS BIZ_OFF_MINUTE, --���� ���� ��
									((SELECT ISNULL(SUM(SUBSTRING(OVERTIME, 1,2) * 60 + SUBSTRING(OVERTIME, 3,2)),0)
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000')
									-(SELECT ISNULL(SUM(SUBSTRING(UNDERTIME, 1,2) * 60 + SUBSTRING(UNDERTIME, 3,2)),0)
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND UNDERTIME > '0000' AND OVERTIME = '0000')) AS REAL_OVER, --���� �ٹ��ð� �д��� ǥ��
									(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9)) AS REAL_AVG, --��� �ٹ��ð� �д��� ǥ��
									(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
										FROM DF_CHECKTIME_OFF WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000') AS REAL_OFF, --���� �д��� ǥ��
									((SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY1 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%')
									+(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY2 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%')) AS PAY --�Ĵ�����Ƚ��
								FROM DF_CHECKTIME C WITH(NOLOCK) 
								WHERE PRS_ID = C.PRS_ID
							) D
						ON
							P.PRS_ID = D.PRS_ID
						WHERE 
							P.PRF_ID IN (1,2,3,4,5) $nameSQL
						GROUP BY 
							P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
							D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION, D.COMMUTE_DATE, D.SUBVACATION1, D.SUBVACATION2, 
							D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
							D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, D.TOTAL_TIME, D.TOTAL_MINUTE, D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, 
							D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, D.REAL_OVER, D.REAL_AVG, D.REAL_OFF, D.PAY 
				) T
			) R
			WHERE
				R.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
		$rs = sqlsrv_query($dbConn,$sql);
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function searchType(){
		var frm = document.form;
		if("team"==frm.type.value){
			location.href = "commuting_total_team.php";
		}else if("team"==frm.type.value){
			location.href = "commuting_total.php";
		}
	}    

	function sSubmit(f)
	{
		f.page.value = "1";
		f.sort.value = "name";
		f.target="_self";
		f.action = "<?=CURRENT_URL?>";
		f.submit();
	}

	function eSubmit(f)
	{
		if(event.keyCode ==13)
			sSubmit(f);
	}

	function excel_download()
	{
		var frm = document.form;
	<? if ($p_period == "month") { ?>
		frm.target = "hdnFrame";
		frm.action = "excel_total_month.php";
		frm.submit();
	<? } ?>
	}

	function chgSort(idx)
	{
		var frm = document.form;
		frm.target="_self";
		frm.page.value = "1";
		frm.sort.value = idx;
		frm.action = "<?=CURRENT_URL?>";
		frm.submit();
	}
</script>
</head>
<body>
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="sort" value="<?=$sort?>">
	<? include INC_PATH."/top_menu.php"; ?>
			<? include INC_PATH."/commuting_menu.php"; ?>
            <section class="section">
                <div class="container">
                    <div class="columns is-vcentered">
                        <!-- Left side -->
                        <div class="card navbar-tabs">
                            <div class="column">
                            <!-- todo 0413 ���� ���� -->
                            <div class="field is-grouped">

                                <div class="control select">
                                    <select name="type" onchange="javascript:searchType();">
                                        <option value="person" selected>������</option>
                                        <option value="team">�μ���</option>
                                    </select>
                                </div>
                                <div class="control">
                                    <input type="text" name="name" class="input" placeholder="������" style="width:140px" value="<?=$p_name?>" onkeypress="eSubmit(this.form);">
                                </div>
                                <div class="control select">
                                    <select name="period"  onchange="javascript:sSubmit(this.form);" >
                                        <option value="day"<? if ($p_period == "day") { echo " selected"; }?>>�Ϻ�</option>
                                        <option value="month"<? if ($p_period == "month") { echo " selected"; }?>>����</option>
                                    </select>
                                </div>
                            <? if ($p_period == "day") { ?>
                                <div class="control select">
                                    <select name="fr_year">
                                        <?
                                        for ($i=$startYear; $i<=$nowYear; $i++)
                                        {
                                            if ($i == $p_fr_year)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$i."'".$selected.">".$i."��</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="Fr_Month">
                                        <?
                                        for ($i=1; $i<=12; $i++)
                                        {
                                            if (strlen($i) == "1")
                                            {
                                                $j = "0".$i;
                                            }
                                            else
                                            {
                                                $j = $i;
                                            }

                                            if ($j == $p_fr_month)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$j."'".$selected.">".$i."��</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="fr_day">
                                    <?
                                    for ($i=1; $i<=31; $i++)
                                    {
                                        if (strlen($i) == "1")
                                        {
                                            $j = "0".$i;
                                        }
                                        else
                                        {
                                            $j = $i;
                                        }

                                        if ($j == $p_fr_day)
                                        {
                                            $selected = " selected";
                                        }
                                        else
                                        {
                                            $selected = "";
                                        }

                                        echo "<option value='".$j."'".$selected.">".$i."��</option>";
                                    }
                                    ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="to_year">
                                        <?
                                        for ($i=$startYear; $i<=$nowYear; $i++)
                                        {
                                            if ($i == $p_to_year)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$i."'".$selected.">".$i."��</option>";
                                        }
                                        ?>
                                    </select>
                                 </div>
                                <div class="control select">
                                    <select name="to_month">
                                        <?
                                        for ($i=1; $i<=12; $i++)
                                        {
                                            if (strlen($i) == "1")
                                            {
                                                $j = "0".$i;
                                            }
                                            else
                                            {
                                                $j = $i;
                                            }

                                            if ($j == $p_to_month)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$j."'".$selected.">".$i."��</option>";
                                        }
                                        ?>
                                    </select>
                                  </div>
                                <div class="control select">
                                    <select name="to_day">
                                        <?
                                        for ($i=1; $i<=31; $i++)
                                        {
                                            if (strlen($i) == "1")
                                            {
                                                $j = "0".$i;
                                            }
                                            else
                                            {
                                                $j = $i;
                                            }

                                            if ($j == $p_to_day)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$j."'".$selected.">".$i."��</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="gubun1" style="width:130px;">
                                        <option value="">��ٻ���</option>
                                        <option value="1"<? if ($p_gubun1 == "1") { echo " selected"; } ?>>���</option>
                                        <option value="4"<? if ($p_gubun1 == "4") { echo " selected"; } ?>>������Ʈ��������</option>
                                        <option value="6"<? if ($p_gubun1 == "6") { echo " selected"; } ?>>�ܱ�</option>
                                        <option value="8"<? if ($p_gubun1 == "8") { echo " selected"; } ?>>��������</option>
                                        <option value="15"<? if ($p_gubun1 == "15") { echo " selected"; } ?>>����/�Ʒ�</option>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="gubun2" style="width:130px;">
                                        <option value="">��ٻ���</option>
                                        <option value="2"<? if ($p_gubun2 == "2") { echo " selected"; } ?>>���</option>
                                        <option value="3"<? if ($p_gubun2 == "3") { echo " selected"; } ?>>����ٹ�</option>
                                        <option value="5"<? if ($p_gubun2 == "5") { echo " selected"; } ?>>������Ʈ���Ĺ���</option>
                                        <option value="6"<? if ($p_gubun2 == "6") { echo " selected"; } ?>>�ܱ�</option>
                                        <option value="9"<? if ($p_gubun2 == "9") { echo " selected"; } ?>>���Ĺ���</option>
                                        <option value="15"<? if ($p_gubun2 == "15") { echo " selected"; } ?>>����/�Ʒ�</option>
                                    </select>
                                </div>
                            <? } else { ?>
                                <div class="control select">
                                    <select name="year">
                                        <?
                                        for ($i=$startYear; $i<=$nowYear; $i++)
                                        {
                                            if ($i == $p_year)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$i."'".$selected.">".$i."��</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                 <? if ($p_period == "month") { ?>
                                <div class="control select">
                                    <select name="month">
                                        <?
                                        for ($i=1; $i<=12; $i++)
                                        {
                                            if ($i == $p_month)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$i."'".$selected.">".$i."��</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                 <? }
                                } ?>
                                <div class="control">
                                    <a href="javascript:sSubmit(this.form);" class="button is-link" id="btnSearch">
                                        <span class="icon is-small">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <span>�˻�</span>
                                    </a>
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- Right side -->

                    </div>
                    <div class="field is-grouped">
                        <div class="control ">
                            <a href="javascript:chgSort('name');" class="button is-link">
                                <span>�̸���</span>
                            </a>
                        </div>
                        <div class="control ">
                            <a href="javascript:chgSort('position');" class="button is-link" id="btnSearch">
                                <span>���޼�</span>
                            </a>
                        </div>
                    <? if ($p_period == "day") { ?>
                        <div class="control ">
                            <a href="javascript:chgSort('checktime1');" class="button is-link">
                                <span>��ٽð���</span>
                            </a>
                        </div>
                    <? } else { ?>
                        <div class="control ">
                            <a href="javascript:chgSort('avg');" class="button is-link">
                                <span>��ձٹ��ð���</span>
                            </a>
                        </div>
                        <div class="control ">
                            <a href="javascript:chgSort('over');" class="button is-link">
                                <span>�ʰ��ٹ��ð���</span>
                            </a>
                        </div>
                    <? } ?>
                        <? if ($p_period == "month") { ?>
                            <div class="column is-hidden-mobile">
                                <div class="control has-text-right">
                                    <a href="javascript:excel_download();" class="button">
                                         <span class="icon is-small">
                                            <i class="fas fa-file-excel"></i>
                                        </span>
                                        <span>������ �ٿ�ε�</span>
                                    </a>
                                </div>
                            </div>
                        <? } ?>
                    </div>

            <? if ($p_period == "day") { ?>
                    <table class="table is-fullwidth is-hoverable is-resize">
                        <colgroup>
                            <col width="5%">
                            <col width="9%">
                            <col width="8%">
                            <col width="7%">
                            <col width="*%">
                            <col width="8%">
                            <col width="8%">
                            <col width="10%">
                            <col width="8%">
                            <col width="9%">
                            <col width="11%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="has-text-centered">No.</th>
                            <th class="has-text-centered">�̸�</th>
                            <th class="has-text-centered">��¥</th>
                            <th class="has-text-centered">����</th>
                            <th class="has-text-centered">�μ�</th>
                            <th class="has-text-centered">���</th>
                            <th class="has-text-centered">���</th>
                            <th class="has-text-centered">����</th>
                            <th class="has-text-centered">�ѱٹ��ð�</th>
                            <th class="has-text-centered">�ʰ��ٹ��ð�</th>
                            <th class="has-text-centered">���</th>
                        </tr>
                        </thead>
                        <!-- �Ϲ� ����Ʈ -->
                        <tbody class="list">
                    <?
                    while ($record = sqlsrv_fetch_array($rs))
                    {
                        $team_login = $team_login. $record['PRS_LOGIN'] . "##";
                        $team_id = $team_id. $record['PRS_ID'] . "##";
                        $team_name = $team_name. $record['PRS_NAME'] . "##";
                        $team_team = $team_team. $record['PRS_TEAM'] . "##";
                        $team_position = $team_position. $record['PRS_POSITION'] . "##";
                        if ($p_period == "day")
                        {
                            $team_date = $team_date. $record['DATE'] . "##";
                        }
                    }

                    $team_login_ex = explode("##",$team_login);
                    $team_id_ex = explode("##",$team_id);
                    $team_name_ex = explode("##",$team_name);
                    $team_team_ex = explode("##",$team_team);
                    $team_position_ex = explode("##",$team_position);
                    $team_date_ex = explode("##",$team_date);

                    if (sizeof($team_id_ex) == 1)
                    {
                    ?>
                        <tr>
                            <td colspan="11" height="30" class="has-text-centered">�˻��� �����Ͱ� �����ϴ�.</td>
                        </tr>
                        <?
                    }
                    else
                    {
                    for ($i=0; $i<sizeof($team_id_ex); $i++)
                    {

                    $gubun1 = "";
                    $gubun2 = "";
                    $checktime1 = "";
                    $checktime2 = "";
                    $totaltime = "";
                    $overtime = "";
                    $undertime = "";
                    $memo = "";

                    $gubun1_ex = "";
                    $gubun2_ex = "";
                    $checktime1_ex = "";
                    $checktime2_ex = "";
                    $totaltime_ex = "";
                    $overtime_ex = "";
                    $undertime_ex = "";
                    if ($team_id_ex[$i] != "")
                    {

                    $sql = "SELECT 
							GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, TOTALTIME, OVERTIME, UNDERTIME 
						FROM 
							DF_CHECKTIME WITH(NOLOCK)
						WHERE 
							PRS_ID = '$team_id_ex[$i]' AND DATE = '$team_date_ex[$i]'";
                    $rs = sqlsrv_query($dbConn,$sql);

                    $record = sqlsrv_fetch_array($rs);
                    if (sizeof($record) > 0)
                    {
                        $gubun1 = $record['GUBUN1'];				//��ٻ���
                        $gubun2 = $record['GUBUN2'];				//��ٻ���
                        $checktime1 = $record['CHECKTIME1'];		//��ٽð�
                        $checktime2 = $record['CHECKTIME2'];		//��ٽð�
                        $totaltime = $record['TOTALTIME'];			//�ѱٹ��ð�
                        $overtime = $record['OVERTIME'];			//�ʰ��ٹ��ð�
                        $undertime = $record['UNDERTIME'];			//�̸��ٹ��ð�

                        if ($checktime1 == "") {
                            $checktime1_ex = "-";
                        } else {
                            $checktime1_ex = substr($checktime1,8,2) ." : ". substr($checktime1,10,2);				//��ٽð�
                        }
                        if ($checktime2 == "") {
                            $checktime2_ex = "-";
                        } else {
                            $checktime2_ex = substr($checktime2,8,2) ." : ". substr($checktime2,10,2);	//��ٽð�
                        }

                        $memo = "";		//���, �ѱٹ��ð�, �ʰ��ٹ��ð�
                        if ($gubun1 == "10" || $gubun2 == "10") { $memo = "�ް�"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "11" || $gubun2 == "11") { $memo = "����"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "12" || $gubun2 == "12") { $memo = "������"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "13" || $gubun2 == "13") { $memo = "��Ÿ"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "14" || $gubun2 == "14") { $memo = "���"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "15" || $gubun2 == "15") { $memo = "����/�Ʒ�"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "16" || $gubun2 == "16") { $memo = "������Ʈ�ް�"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "17" || $gubun2 == "17") { $memo = "���������ް�"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "18" || $gubun2 == "18") { $memo = "�����ް�"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "19" || $gubun2 == "19") { $memo = "����"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "20" || $gubun2 == "20") { $memo = "����ް�"; $totaltime = "0000"; $overtime = "0000"; }
                        if ($gubun1 == "21" || $gubun2 == "21") { $memo = "��������"; $totaltime = "0000"; $overtime = "0000"; }

                        if ($totaltime == "0000") {
                            $totaltime_ex = "";
                        } else {
                            $totaltime_ex = substr($totaltime,0,2) .":". substr($totaltime,2,2);
                        }
                        if ($overtime == "0000") {
                            $overtime_ex = "";
                        } else {
                            $overtime_ex = substr($overtime,0,2) .":". substr($overtime,2,2);
                        }
                        if ($undertime == "0000") {
                            $undertime_ex = "";
                        } else {
                            $undertime_ex = substr($undertime,0,2) .":". substr($undertime,2,2);
                        }

                        //��ٻ���
                        switch($gubun1)
                        {
                            case "1" :
                                $gubun1_ex = "���";
                                break;
                            case "4" :
                                $gubun1_ex = "������Ʈ ����";
                                break;
                            case "6" :
                                $gubun1_ex = "�ܱ�";
                                break;
                            case "7" :
                                $gubun1_ex = "����";
                                break;
                            case "8" :
                                $gubun1_ex = "����";
                                break;
                            case "10" :
                                $gubun1_ex = "�ް�";
                                $checktime1_ex = "-";
                                break;
                            case "11" :
                                $gubun1_ex = "����";
                                $checktime1_ex = "-";
                                break;
                            case "12" :
                                $gubun1_ex = "������";
                                $checktime1_ex = "-";
                                break;
                            case "13" :
                                $gubun1_ex = "��Ÿ";
                                $checktime1_ex = "-";
                                break;
                            case "14" :
                                $gubun1_ex = "���";
                                $checktime1_ex = "-";
                                break;
                            case "15" :
                                $gubun1_ex = "����/�Ʒ�";
                                $checktime1_ex = "-";
                                break;
                            case "16" :
                                $gubun1_ex = "������Ʈ�ް�";
                                $checktime1_ex = "-";
                                break;
                            case "17" :
                                $gubun1_ex = "���������ް�";
                                $checktime1_ex = "-";
                                break;
                            case "18" :
                                $gubun1_ex = "�����ް�";
                                $checktime1_ex = "-";
                                break;
                            case "19" :
                                $gubun1_ex = "����";
                                $checktime1_ex = "-";
                                break;
                            case "20" :
                                $gubun1_ex = "����ް�";
                                $checktime1_ex = "-";
                                break;
                            case "21" :
                                $gubun1_ex = "��������";
                                $checktime1_ex = "-";
                                break;
                            default :
                                $gubun1_ex = "";
                                break;
                        }

                        //��ٻ���
                        switch($gubun2)
                        {
                            case "2" :
                                $gubun2_ex = "���";
                                break;
                            case "3" :
                                $gubun2_ex = "����ٹ�";
                                break;
                            case "5" :
                                $gubun2_ex = "������Ʈ ����";
                                break;
                            case "6" :
                                $gubun2_ex = "�ܱ�";
                                break;
                            case "9" :
                                $gubun2_ex = "����";
                                break;
                            case "10" :
                                $gubun2_ex = "�ް�";
                                $checktime2_ex = "-";
                                break;
                            case "11" :
                                $gubun2_ex = "����";
                                $checktime2_ex = "-";
                                break;
                            case "12" :
                                $gubun2_ex = "������";
                                $checktime2_ex = "-";
                                break;
                            case "13" :
                                $gubun2_ex = "��Ÿ";
                                $checktime2_ex = "-";
                                break;
                            case "14" :
                                $gubun2_ex = "���";
                                $checktime2_ex = "-";
                                break;
                            case "15" :
                                $gubun2_ex = "����/�Ʒ�";
                                $checktime2_ex = "-";
                                break;
                            case "16" :
                                $gubun2_ex = "������Ʈ�ް�";
                                $checktime2_ex = "-";
                                break;
                            case "17" :
                                $gubun2_ex = "���������ް�";
                                $checktime2_ex = "-";
                                break;
                            case "18" :
                                $gubun2_ex = "�����ް�";
                                $checktime2_ex = "-";
                                break;
                            case "19" :
                                $gubun2_ex = "����";
                                $checktime2_ex = "-";
                                break;
                            case "20" :
                                $gubun2_ex = "����ް�";
                                $checktime2_ex = "-";
                                break;
                            case "21" :
                                $gubun2_ex = "��������";
                                $checktime2_ex = "-";
                                break;
                            default :
                                $gubun2_ex = "";
                                break;
                        }
                    }
                    ?>
                        <tr>
                            <td class="has-text-centered"><?=($page-1)*$per_page+($i+1)?></td>
                            <td class="has-text-centered"><?=$team_name_ex[$i]?></td>
                            <td class="has-text-centered"><?=$team_date_ex[$i]?></td>
                            <td class="has-text-centered"><?=$team_position_ex[$i]?></td>
                            <td class="has-text-centered"><?=$team_team_ex[$i]?></td>
                            <td class="has-text-centered"><?=$checktime1_ex?></td>
                            <td class="has-text-centered"><?=$checktime2_ex?></td>
                            <td class="has-text-centered"><?=$gubun1_ex?> / <?=$gubun2_ex?></td>
                            <td class="has-text-centered"><?=$totaltime_ex?></td>
                            <td class="has-text-centered"><?=$overtime_ex?></td>
                            <td class="has-text-centered"><?=$memo?></td>
                        </tr>
                <?
                    }
                 } }
                ?>
                        </tbody>
                    </table>
            <? } else { ?>
                    <table class="table is-fullwidth is-hoverable is-resize">
                        <colgroup>
                            <col width="5%">
                            <col width="9%">
                            <col width="8%">
                            <col width="*%">
                            <col width="5%">
                            <col width="5%">
                            <col width="5%">
                            <col width="8%">
                            <col width="8%">
                            <col width="9%">
                            <col width="7%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="has-text-centered">No.</th>
                            <th class="has-text-centered">�̸�</th>
                            <th class="has-text-centered">����</th>
                            <th class="has-text-centered">�μ�</th>
                            <th class="has-text-centered">����ٹ�</th>
                            <th class="has-text-centered">�ް�</th>
                            <th class="has-text-centered">����</th>
                            <th class="has-text-centered">���<br>��ٽð�</th>
                            <th class="has-text-centered">���<br>��ٽð�</th>
                            <th class="has-text-centered">���<br>�ٹ��ð�</th>
                            <th class="has-text-centered">��<br>�ٹ��ð�</th>
                            <th class="has-text-centered">���ʰ�<br>�ٹ��ð�</th>
                            <th class="has-text-centered">���ʰ��ϼ�</th>
                        </tr>
                        </thead>
                        <!-- �Ϲ� ����Ʈ -->
                        <tbody class="list">
                        <?
                        if (sizeof($team_id_ex) == 1)
                        {
                        ?>
                        <tr>
                            <td colspan="11" height="30" align="center">�˻��� �����Ͱ� �����ϴ�.</td>
                        </tr>
                  <?
                    }
                    else
                    {
                        $i = ($page-1)*$per_page+1;
                    while ($record = sqlsrv_fetch_array($rs))
                    {
                        $team_login = $record['PRS_LOGIN'];
                        $team_id = $record['PRS_ID'];
                        $team_name = $record['PRS_NAME'];
                        $team_team = $record['PRS_TEAM'];
                        $team_position = $record['PRS_POSITION'];

                        $biz_commute_count = $record['BIZ_COMMUTE'];	//�����������
                        $lateness_count = $record['LATENESS'];			//����
                        $vacation_count = $record['VACATION'];			//�ް�
                        $commute_day = $record['COMMUTE_DATE'];			//�ٹ��ϼ�
                        $subvacation1_count = $record['SUBVACATION1'];	//��������
                        $subvacation2_count = $record['SUBVACATION2'];	//���Ĺ���
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
                        $off_time = $record['OFF_TIME'];				//���� ��
                        $off_minute = $record['OFF_MINUTE'];			//���� ��
                        $biz_off_time = $record['BIZ_OFF_TIME'];		//���Ͽ��� ��
                        $biz_off_minute = $record['BIZ_OFF_MINUTE'];	//���Ͽ��� ��
                        $real_over = $record['REAL_OVER'];				//����ٹ��ð��д���
                        $real_avg = $record['REAL_AVG'];				//��ձٹ��ð��д���
                        $real_off = $record['REAL_OFF'];				//��տ���ð��д���

                        $subvacation_count = $subvacation1_count + $subvacation2_count;

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
                            <td class="has-text-centered"><?=$i?></td>
                            <td class="has-text-centered"><?=$team_name?></td>
                            <td class="has-text-centered"><?=$team_position?></td>
                            <td class="has-text-centered"><?=$team_team?></td>
                            <td class="has-text-centered"><?=$biz_commute_count?></td>
                            <td class="has-text-centered"><?=$vacation_count?></td>
                            <td class="has-text-centered"><?=$subvacation_count?></td>
                            <td class="has-text-centered"><?=$avgtime1?> : <?=$avgminute1?></td>
                            <td class="has-text-centered"><?=$avgtime2?> : <?=$avgminute2?></td>
                            <td class="has-text-centered"><?=$avgtime2?> : <?=$avgminute2?></td>
                            <td class="has-text-centered"><?=$total_time?> : <?=$total_minute?></td>
                            <td class="has-text-centered"><?=$over_time?> : <?=$over_minute?></td>
                            <td class="has-text-centered"><?=$over_day?></td>
                        </tr>
                    <?
                            $i++;
                            }
                        }
                     ?>

                        </tbody>
                    </table>
            <? } ?>

                        <!--����¡ó��-->
                    <nav class="pagination" role="navigation" aria-label="pagination">
                        <?=getPaging($total_cnt,$page,$per_page);?>
                        </ul>
                    </nav>
                    <!--����¡ó��-->
                </div>
            </section>
</form>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>
