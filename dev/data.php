<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
//step 1	- Query창에서 setp1 쿼리 먼저 실행 후 브라우저에서 이 페이지 실행

/*
DELETE FROM DF_CHECKTIME

INSERT INTO DF_CHECKTIME 
(SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN, GUBUN1, CHECKTIME1, CHECKIP1, MEMO1, MEMO2)
	SELECT 
		SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN1, GUBUN2, CHECKTIME, CHECKIP, MEMO1, MEMO2
	FROM DF_COMMUTE 
	ORDER BY SEQNO

UPDATE DF_CHECKTIME SET 
	GUBUN2 = A.GUBUN2_C2, 
	CHECKTIME2 = A.CHECKTIME_C2, 
	CHECKIP2 = A.CHECKIP_C2 
FROM DF_COMMUTE2 A 
WHERE DATE = A.DATE_C2 AND PRS_ID = A.PRS_ID_C2
*/
?>

<?
//step 2

	$sql = "SELECT 
				B.DATEKIND, CHECKTIME1, CHECKTIME2, A.DATE, SEQNO, GUBUN1, GUBUN2, 
				(SELECT OVERTIME FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE = CONVERT(CHAR(10),DATEADD(dd,-1,A.DATE),120) AND PRS_ID = A.PRS_ID) As YESTERDAY_OVERTIME, 
				(SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = REPLACE(CONVERT(CHAR(10),DATEADD(dd,-1,A.DATE),120),'-','') AND PRS_ID = A.PRS_ID) As YESTERDAY_KIND
			FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON B.DATE = REPLACE(A.DATE,'-','')
			WHERE A.DATE = '2013-11-20'
			ORDER BY PRS_ID, A.DATE";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$datekind = $record['DATEKIND'];
		$checktime1 = $record['CHECKTIME1'];
		$checktime2 = $record['CHECKTIME2'];
		$date = $record['DATE'];
		$seqno = $record['SEQNO'];
		$gubun1 = $record['GUBUN1'];
		$gubun2 = $record['GUBUN2'];
		$yesterday_overtime = $record['YESTERDAY_OVERTIME'];
		$yesterday_kind = $record['YESTERDAY_KIND'];

		if ($yesterday_overtime == "") { $yesterday_overtime = "0000"; }

		if ($checktime1 != "" && $checktime2 != "")
		{
			if (substr($checktime1,8,2) < "08")
			{
				$totalhour = substr($checktime2,8,2) - 8;
				$totalmin = substr($checktime2,10,2);
			}
			else
			{
				if (substr($checktime1,8,2) < "13" && $gubun1 == "8")
				{
					$totalhour = substr($checktime2,8,2) - 13;
					$totalmin = substr($checktime2,10,2);
				}
				else
				{
					if (substr($checktime2,10,2) < substr($checktime1,10,2))
					{
						$totalhour = substr($checktime2,8,2) - substr($checktime1,8,2) - 1;
						$totalmin = substr($checktime2,10,2) - substr($checktime1,10,2) + 60;
					}
					else
					{
						$totalhour = substr($checktime2,8,2) - substr($checktime1,8,2);
						$totalmin = substr($checktime2,10,2) - substr($checktime1,10,2);
					}
				}
			}
			if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
			if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
			$totaltime = $totalhour . $totalmin;

			if ($yesterday_kind == "LAW" || $yesterday_kind == "FIN") { $yesterday_overtime = "0000"; }
			$overtime = "0000";
			$undertime = "0000";
		
			$gubun = "3";

			if ($datekind == "BIZ")
			{
				//전날 근무시간과 비교
				if ($gubun1 == "8") 
				{
					if ($yesterday_overtime >= "0700") { $d_time = "0200"; }
					else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0300"; }
					else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0400"; }
					else { $d_time = "0500";}

					$d_time = "0500";
					if ($totaltime >= $d_time)
					{
						$overhour = substr($totaltime,0,2) - substr($d_time,0,2);
						$overmin = substr($totaltime,2,2);
						if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
						if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
						$overtime = $overhour . $overmin;

						$undertime = "0000";
					}
					else if ($totaltime < $d_time)
					{
						$gubun = "2";

						$underhour = substr($d_time,0,2) - substr($totaltime,0,2) - 1;
						$undermin = 60 - substr($totaltime,2,2);
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
				else if ($gubun2 == "9")
				{
					if ($yesterday_overtime >= "0700") { $d_time = "0000"; }
					else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0100"; }
					else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0200"; }
					else { $d_time = "0300";}

					$gubun = "9";
					if ($totaltime >= $d_time)
					{
						$overhour = substr($totaltime,0,2) - substr($d_time,0,2);
						$overmin = substr($totaltime,2,2);
						if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
						if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
						$overtime = $overhour . $overmin;

						$undertime = "0000";
					}
					else if ($totaltime < $d_time)
					{
						$underhour = substr($d_time,0,2) - substr($totaltime,0,2) - 1;
						$undermin = 60 - substr($totaltime,2,2);
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
					if ($yesterday_overtime >= "0700") { $d_time = "0600"; }
					else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0700"; }
					else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0800"; }
					else { $d_time = "0900";}

					if ($yesterday_overtime >= "0700")
					{
						if ($totaltime >= "0600")
						{
							$overhour = substr($totaltime,0,2) - substr($d_time,0,2);
							$overmin = substr($totaltime,2,2);
							if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
							if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
							$overtime = $overhour . $overmin;

							$undertime = "0000";
						}
						else if ($totaltime < "0600")
						{
							$gubun = "2";

							$underhour = substr($d_time,0,2) - substr($totaltime,0,2) - 1;
							$undermin = 60 - substr($totaltime,2,2);
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
					else if ($yesterday_overtime < "0500")
					{
						if ($totaltime >= "0900")
						{
							$overhour = substr($totaltime,0,2) - substr($d_time,0,2);
							$overmin = substr($totaltime,2,2);
							if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
							if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
							$overtime = $overhour . $overmin;

							$undertime = "0000";
						}
						else if ($totaltime < "0900")
						{
							$gubun = "2";

							$underhour = substr($d_time,0,2) - substr($totaltime,0,2) - 1;
							$undermin = 60 - substr($totaltime,2,2);
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

						if ($totaltime > $d_time)
						{
							if (substr($totaltime,2,2) < substr($d_time,2,2))
							{
								$overhour = substr($totaltime,0,2) - substr($d_time,0,2) - 1;
								$overmin = 60 + substr($totaltime,2,2) - substr($d_time,2,2);
							}
							else
							{
								$overhour = substr($totaltime,0,2) - substr($d_time,0,2);
								$overmin = substr($totaltime,2,2) - substr($d_time,2,2);
							}
							if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
							if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
							$overtime = $overhour . $overmin;

							$undertime = "0000";
						}
						else if ($totaltime < $d_time)
						{
							$gubun = "2";

							if (substr($d_time,2,2) < substr($totaltime,2,2))
							{
								$underhour = substr($d_time,0,2) - substr($totaltime,0,2) - 1;
								$undermin = 60 + substr($d_time,2,2) - substr($totaltime,2,2);
							}
							else
							{
								$underhour = substr($d_time,0,2) - substr($totaltime,0,2);
								$undermin = substr($d_time,2,2) - substr($totaltime,2,2);
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
							$gubun = "2";

							$overtime = "0000";
							$undertime = "0000";
						}
					}
				}
			}
			else
			{
				$overtime = $totaltime;
				$undertime = "0000";
			}
		}
		else
		{
			$totaltime = "0000";
			$overtime = "0000";
			$undertime = "0000";
		}

		if ($gubun1 == "10") { $gubun = "10"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "11") { $gubun = "11"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "12") { $gubun = "12"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "13") { $gubun = "13"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "14") { $gubun = "14"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "15") { $gubun = "15"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "16") { $gubun = "16"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "17") { $gubun = "17"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }
		if ($gubun1 == "18") { $gubun = "18"; $totaltime = "0000"; $overtime = "0000"; $undertime = "0000"; }

		$upsql = "UPDATE DF_CHECKTIME SET 
					TOTALTIME = '$totaltime', OVERTIME = '$overtime', UNDERTIME = '$undertime', GUBUN2 = '$gubun' 
				WHERE SEQNO = '$seqno'";
		$uprs = sqlsrv_query($dbConn,$upsql);
	}
?>

<?
//step 3
/*
UPDATE DF_CHECKTIME SET GUBUN2 = '' WHERE CHECKTIME2 IS NULL
*/
?>
