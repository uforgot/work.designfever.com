<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
//if (REMOTE_IP == "119.192.230.239")
if (REMOTE_IP == "")
{
	$card_no = isset($_REQUEST['card']) ? $_REQUEST['card'] : null;

	if ($card_no == "")
	{
		echo "카드번호가 필요합니다.";
		exit;
	}

	$now = date("YmdHis");								//오늘 출근 년월일시분초
	$now_time = substr($now,8,4);						//오늘 출근 시간분
	$today = date("Y-m-d");								//오늘 날짜
	$yesterday = date("Y-m-d",strtotime ("-1 day"));	//어제 날짜

	$ip = REMOTE_IP;									//접속IP
	$gubun = "출퇴근";
	$state = "정상";

	if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

	$col_prs_id = "";
	$col_prs_name = "";
	$col_prs_login = "";
	$col_prs_team = "";
	$col_prs_position = "";

	$sql = "SELECT TOP 1 
				A.PRS_ID, A.PRS_NAME, A.PRS_LOGIN, A.PRS_TEAM, A.PRS_POSITION
			FROM 
				DF_PERSON A WITH(NOLOCK) INNER JOIN DF_CARD B WITH(NOLOCK)
			ON 
				A.PRS_ID = B.PRS_ID
			WHERE 
				B.CARD_NO = '$card_no' AND A.PRF_ID IN (1,2,3,4)
			ORDER BY 
				A.PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$col_prs_id = $record['PRS_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_login = $record['PRS_LOGIN'];
		$col_prs_team = $record['PRS_TEAM'];
		$col_prs_position = $record['PRS_POSITION'];

		$state = "정상";
		$message = $col_prs_name ."님, 출근체크가 되었습니다. ". date("Y-m-d H:i:s");
	}
	else
	{
		$state = "오류";
		$message = "등록되지 않은 카드입니다.";
	}

	if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

	//전일 퇴근체크 확인
	$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	$yesterday_checktime1 = $record['CHECKTIME1'];
	$yesterday_checktime2 = $record['CHECKTIME2'];

	//출근 중복체크 확인
	$sql = "SELECT * FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE = '$today'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
	{
		$gubun1 = $record['GUBUN1'];
		$gubun2 = $record['GUBUN2'];
		$checktime1 = $record['CHECKTIME1'];
		$checktime2 = $record['CHECKTIME2'];

		//if ((($gubun1 == "4" || $gubun1 == "8") && $checktime1 == "") || ($gubun2 == "5" || $gubun2 == "9"))
		if ($checktime1 == "")
		{
		}
		else
		{
			echo "이미 출근 체크 하셨습니다.";
			exit;
		}
	}
	else
	{
		$gubun1 = "1";
		$gubun2 = "";
	}
	
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
				WHERE B.PRS_ID = '$col_prs_id' AND B.DATE = '$yesterday'";
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
	
	if ($state == "정상") 
	{
		$maxno = 0;
		//오전반차or오후반차 제출된 상태
		if (($gubun1 == "4" || $gubun1 == "8") && $checktime1 == "")
		{
			$sql = "UPDATE DF_CHECKTIME SET
						CHECKTIME1 = '$now', 
						CHECKIP1 = '$ip'
					WHERE 
						PRS_ID = '$col_prs_id' AND DATE = '$today'";
		}
		else if ($gubun2 == "5" || $gubun2 == "9")
		{
			$sql = "UPDATE DF_CHECKTIME SET
						GUBUN1 = '$time_gubun',
						CHECKTIME1 = '$now', 
						CHECKIP1 = '$ip'
					WHERE 
						PRS_ID = '$col_prs_id' AND DATE = '$today'";
		}
		else
		{
			$sql = "SELECT ISNULL(MAX(SEQNO),0)+1 FROM DF_CHECKTIME WITH(NOLOCK)";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$maxno = $result[0];

			$sql = "INSERT INTO DF_CHECKTIME
					(SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN, GUBUN1, CHECKTIME1, CHECKIP1, CARD, START_TIME, FLAG, REGDATE)
					VALUES
					('$maxno','$col_prs_id','$col_prs_login','$col_prs_name','$today','$gubun','$time_gubun','$now','$ip','$card_no','$start_time','card',getdate())";
		}
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
			$message = "출근체크 오류입니다. 개발팀에 문의해 주세요.";
		}
		else
		{
			if ($maxno > 0)
			{
				$sql2 = "SELECT GUBUN1, CHECKTIME1, START_TIME FROM DF_CHECKTIME WITH(NOLOCK) WHERE SEQNO = '$maxno'";
				$rs2 = sqlsrv_query($dbConn,$sql2);
			
				$record2 = sqlsrv_fetch_array($rs2);
				
				$chk_gubun1 = $record2['GUBUN1'];
				$chk_checktime1 = $record2['CHECKTIME1'];
				$chk_start_time = $record2['START_TIME'];

				if ($chk_gubun1 == "8" && substr($chk_checktime1,8,4) <= $chk_start_time)
				{
					$sql3 = "UPDATE DF_CHECKTIME SET GUBUN1 = '1', CHK = 'Y' WHERE SEQNO = '$maxno'";
					$rs3 = sqlsrv_query($dbConn,$sql3);
				}

				$sql2 = "SELECT COUNT(SEQNO) FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE = '$today'";
				$rs2 = sqlsrv_query($dbConn,$sql2);
			
				$record2 = sqlsrv_fetch_array($rs2);
				$check_cnt = $record2[0];

				if ($check_cnt > 1)
				{
					$sql3 = "DELETE FROM DF_CHECKTIME WHERE SEQNO = '$maxno'";
					$rs3 = sqlsrv_query($dbConn,$sql3);
				}
			}
		}
	}
}
else
{
	$message = "출/퇴근 체크는 사내에서만 가능합니다.";
}

	echo $message;
?>