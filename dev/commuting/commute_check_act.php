<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
if (in_array(REMOTE_IP, $ok_ip_arr))
//if (REMOTE_IP == "119.192.230.239")
{
	$now = date("YmdHis");								//오늘 출근 년월일시분초
	$now_time = substr($now,8,4);						//오늘 출근 시간분
	$today = date("Y-m-d");								//오늘 날짜
	$yesterday = date("Y-m-d",strtotime ("-1 day"));	//어제 날짜

	$ip = REMOTE_IP;									//접속IP
	$gubun = "출퇴근";

	if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

	//전일 퇴근체크 확인
	$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	$yesterday_checktime1 = $record['CHECKTIME1'];
	$yesterday_checktime2 = $record['CHECKTIME2'];

	//출근 중복체크 확인
	$sql = "SELECT * FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
	{
		$gubun1 = $record['GUBUN1'];
		$gubun2 = $record['GUBUN2'];
		$checktime1 = $record['CHECKTIME1'];
		$checktime2 = $record['CHECKTIME2'];

		if ((($gubun1 == "4" || $gubun1 == "8") && $checktime1 == "") || ($gubun2 == "5" || $gubun2 == "9"))
		{
		}
		else
		{
		// if ($checktime1 != "" && ($gubun1 == "1" || $gubun1 == "6"))
?>
	<script language="javascript">
		alert("이미 출근 체크 하셨습니다.");
		parent.location.href="/main.php";
	</script>
<?
		exit;
		}
	}
	else
	{
		$gubun1 = "1";
		$gubun2 = "";
	}

	$out_chk = "N";
	$pay1 = "N";
	$pay5 = "N";
	/*
	if (REMOTE_IP == "119.192.230.238") { 
		$out_chk = "Y"; 
		$pay1 = "Y";
		$pay5 = "Y";
	}
	*/
	
	$sql = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$today) ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$today_kind = $record['DATEKIND'];

	if ($today_kind == "BIZ")	//평일
	{

		//탄력근무제
		//오전반차 처리 위한 전일 근무시간 확인
		$sql = "SELECT 
					A.DATEKIND, B.CHECKTIME1, B.CHECKTIME2, B.GUBUN2, B.OVERTIME 
				FROM 
					HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
				ON A.DATE = REPLACE(B.DATE,'-','') 
				WHERE B.PRS_ID = '$prs_id' AND B.DATE = '$yesterday'";
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		if (sqlsrv_has_rows($rs) > 0)
		{
			$yesterday_kind = $record['DATEKIND'];
			$fr_checktime = substr($record['CHECKTIME'],8,4); 
			$to_checktime = substr($record['CHECKTIME2'],8,4); 
			$yesterday_gubun2 = $record['GUBUN2'];
			$over_time = $record['OVERTIME'];
		}
		else
		{
			$yesterday_kind = "";
			$yesterday_gubun2 = "";
			$over_time = "0000";
		}

		if ($yesterday_kind == "BIZ")
		{
			if ($over_time >= "0700")						//근무시간9시간+연장근무+7시간 - 출근인정시간
			{
				$start_time = "1400";
			}
			else if ($over_time >= "0600")					//근무시간9시간+연장근무+6시간 - 출근인정시간
			{
				//$start_time = "1300";
				$start_time = "13". substr($over_time,2,2);
			}
			else if ($over_time >="0500")					//근무시간9시간+연장근무+5시간 - 출근인정시간
			{
				//$start_time = "1200";
				$start_time = "12". substr($over_time,2,2);
			}
			else if ($over_time >="0400")					//근무시간9시간+연장근무+4시간 - 출근인정시간
			{
				//$start_time = "1100";
				$start_time = "11". substr($over_time,2,2);
			}
			else
			{
				$start_time = "1100";					//출근인정시간대(0800~1100)
			}
		}
		else
		{
			if ($over_time >= "0900")						//휴일근무9시간 - 출근인정시간
			{
				$start_time = "1400";
			}
			else if ($over_time >= "0800")					//휴일근무8시간 - 출근인정시간
			{
				//$start_time = "1300";
				$start_time = "13". substr($over_time,2,2);
			}
			else if ($over_time >="0700")					//휴일근무7시간 - 출근인정시간
			{
				//$start_time = "1200";
				$start_time = "12". substr($over_time,2,2);
			}
			else if ($over_time >="0600")					//휴일근무6시간 - 출근인정시간
			{
				//$start_time = "1100";
				$start_time = "11". substr($over_time,2,2);
			}
			else
			{
				$start_time = "1100";					//출근인정시간대(0800~1100)
			}
		}

		if ($now_time > $start_time)			//출근인정시간대(1) 이후 출근 오전반차(8)
		{
			$time_gubun = "8";
			$pay1 = "N";
		}
		else
		{
			$time_gubun = "1";
		}
	}
	else	//주말,공휴일
	{
		$time_gubun = "1";
		$start_time = "";
	}

	//오전반차or오후반차 제출된 상태
	if (($gubun1 == "4" || $gubun1 == "8") && $checktime1 == "")
	{
		$pay1 = "N";

		$sql = "UPDATE DF_CHECKTIME SET
					CHECKTIME1 = '$now', 
					CHECKIP1 = '$ip', 
					OUT_CHK = '$out_chk',
					PAY1 = '$pay1',
					PAY5 = '$pay5'
				WHERE 
					PRS_ID = '$prs_id' AND DATE = '$today'";
	}
	else if ($gubun2 == "5" || $gubun2 == "9")
	{
		$pay1 = "N";

		$sql = "UPDATE DF_CHECKTIME SET
					GUBUN1 = '$time_gubun',
					CHECKTIME1 = '$now', 
					CHECKIP1 = '$ip',
					OUT_CHK = '$out_chk',
					PAY1 = '$pay1',
					PAY5 = '$pay5'
				WHERE 
					PRS_ID = '$prs_id' AND DATE = '$today'";
	}
	else
	{
		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_CHECKTIME WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$maxno = $result[0] + 1;

		$sql = "INSERT INTO DF_CHECKTIME
				(SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN, GUBUN1, CHECKTIME1, CHECKIP1, START_TIME, FLAG, REGDATE, OUT_CHK, PAY1, PAY5)
				VALUES
				('$maxno','$prs_id','$prs_login','$prs_name','$today','$gubun','$time_gubun','$now','$ip','$start_time','button',getdate(),'$out_chk','$pay1','$pay5')";
	}
	$rs = sqlsrv_query($dbConn,$sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("출근체크 오류입니다. 개발팀에 문의해 주세요.");
		parent.location.href="/main.php";
	</script>
<?
	}
	else
	{
		$sql2 = "SELECT GUBUN1, CHECKTIME1, START_TIME FROM DF_CHECKTIME WITH(NOLOCK) WHERE SEQNO = '$maxno'";
		$rs2 = sqlsrv_query($dbConn,$sql2);
	
		$record2 = sqlsrv_fetch_array($rs2);
		
		$chk_gubun1 = $record2['GUBUN1'];
		$chk_checktime1 = $record2['CHECKTIME1'];
		$chk_start_time = $record2['START_TIME'];

		if ($chk_gubun1 == "8" && (int)substr($chk_checktime1,8,4) <= (int)$chk_start_time)
		{
			$sql3 = "UPDATE DF_CHECKTIME SET GUBUN1 = '1', CHK = 'Y' WHERE SEQNO = '$maxno'";
			$rs3 = sqlsrv_query($dbConn,$sql3);
		}		
?>
	<script language="javascript">
	<? if ($yesterday_checktime2 == "") { ?>
		alert("출근체크가 완료되었습니다.\n전근무일 퇴근체크가 되지 않았습니다.\n전근무일 퇴근시간을 경영지원팀에 알려주세요.");
	<? } else { ?>
		alert("출근체크가 완료되었습니다.");
	<? } ?>
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