<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
if (in_array(REMOTE_IP, $ok_ip_arr))
//if (REMOTE_IP == "119.192.230.239")
{
	$today = date("Y-m-d");					//오늘 날짜
	$nowtime = date("Hi");					//현재 시간분

	$ip = REMOTE_IP;						//접속IP

	$idx = isset($_REQUEST['idx']) ? $_REQUEST['idx'] : null;

	if ($idx == "" | $idx == null)
	{
?>
	<script language="javascript">
		alert("정상적으로 처리되지 않았습니다. 다시 시도해 주세요.");
		parent.location.href="/main.php";
	</script>
<?
		exit;
	}
	else
	{
		//현재 시간이 24시 이후인 경우 오늘 날짜를 어제로 수정
		if (substr($nowtime,0,2) >= "24") 
		{ 
			$nowtime = substr($nowtime,0,2) - 24 . substr($nowtime,2,2);
			if (strlen($nowtime) == 3) { $nowtime = "0" . $nowtime; }

			$today = date("Y-m-d",strtotime ("-1 day"));
		}

		//idx가 goout이면 insert, comeback이면 총 외출시간 계산해서 update
		if ($idx == "goout")
		{
			$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_CHECKTIME_OFF WITH(NOLOCK)";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$maxno = $result[0] + 1;

			$sql = "INSERT INTO DF_CHECKTIME_OFF
					(SEQNO, PRS_ID, PRS_LOGIN, DATE, STARTTIME, CHECKIP1, REGDATE)
					VALUES
					('$maxno','$prs_id','$prs_login','$today','$nowtime','$ip',getdate())";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("외출체크 오류입니다. 개발팀에 문의해 주세요.");
				parent.location.href="/main.php";
			</script>
<?
				exit;
			}
			else
			{
?>
			<script language="javascript">
				alert("외출체크가 완료되었습니다.\n복귀체크를 꼭 해주세요.");
				parent.location.href="/main.php";
			</script>
<?
				exit;
			}
		}
		else if ($idx == "comeback")
		{
			$sql = "SELECT TOP 1 SEQNO, STARTTIME FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE = '$today' AND PRS_ID = '$prs_id' ORDER BY SEQNO DESC";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("외출 내역이 없습니다. 개발팀에 문의해 주세요.");
				parent.location.href="/main.php";
			</script>
<?
				exit;
			}
			else
			{
				$result = sqlsrv_fetch_array($rs);
				$thisno = $result[0];
				$thisstart = $result[1];

				if (substr($thisstart,0,2) < "08") { $thisstart2 = "0800"; } else { $thisstart2 = $thisstart; }
				if (substr($nowtime,0,2) < "08") { $nowtime2 = "0800"; } else { $nowtime2 = $nowtime; }

				if ($thisstart2 == $nowtime2)
				{
					$sql = "DELETE FROM  DF_CHECKTIME_OFF
							WHERE SEQNO = '$thisno'";
				}
				else
				{

					if (substr($nowtime2,2,2) < substr($thisstart2,2,2))
					{
						$totalhour = substr($nowtime2,0,2) - substr($thisstart2,0,2) - 1;
						$totalmin = substr($nowtime2,2,2) - substr($thisstart2,2,2) + 60;
					}
					else

					{
						$totalhour = substr($nowtime2,0,2) - substr($thisstart2,0,2);
						$totalmin = substr($nowtime2,2,2) - substr($thisstart2,2,2);
					}

					if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
					if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }

					$totaltime = $totalhour.$totalmin;

					$sql = "UPDATE DF_CHECKTIME_OFF SET 
								ENDTIME = '$nowtime', 
								TOTALTIME = '$totaltime',
								CHECKIP2 = '$ip',
								REGDATE2 = getdate() 
							WHERE SEQNO = '$thisno'";
				}
			}
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("복귀체크 오류입니다. 개발팀에 문의해 주세요.");
				parent.location.href="/main.php";
			</script>
<?
				exit;
			}
			else
			{
?>
			<script language="javascript">
				alert("복귀체크가 완료되었습니다.");
				parent.location.href="/main.php";
			</script>
<?
				exit;
			}
		}
	}
}
else
{
?>
	<script language="javascript">
		alert("외출/복귀 체크는 사내에서만 가능합니다.");
		parent.location.href="/main.php";
	</script>
<?
}
?>