<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
if (in_array(REMOTE_IP, $ok_ip_arr))
//if (REMOTE_IP == "119.192.230.239")
{
	$now = date("YmdHis");								//���� ��� ����Ͻú���
	$now_time = substr($now,8,4);						//���� ��� �ð���
	$today = date("Y-m-d");								//���� ��¥
	$yesterday = date("Y-m-d",strtotime ("-1 day"));	//���� ��¥

	$ip = REMOTE_IP;									//����IP
	$gubun = "�����";

	if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

	//���� ���üũ Ȯ��
	$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	$yesterday_checktime1 = $record['CHECKTIME1'];
	$yesterday_checktime2 = $record['CHECKTIME2'];

	//��� �ߺ�üũ Ȯ��
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
		alert("�̹� ��� üũ �ϼ̽��ϴ�.");
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

	if ($today_kind == "BIZ")	//����
	{

		//ź�±ٹ���
		//�������� ó�� ���� ���� �ٹ��ð� Ȯ��
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
			if ($over_time >= "0700")						//�ٹ��ð�9�ð�+����ٹ�+7�ð� - ��������ð�
			{
				$start_time = "1400";
			}
			else if ($over_time >= "0600")					//�ٹ��ð�9�ð�+����ٹ�+6�ð� - ��������ð�
			{
				//$start_time = "1300";
				$start_time = "13". substr($over_time,2,2);
			}
			else if ($over_time >="0500")					//�ٹ��ð�9�ð�+����ٹ�+5�ð� - ��������ð�
			{
				//$start_time = "1200";
				$start_time = "12". substr($over_time,2,2);
			}
			else if ($over_time >="0400")					//�ٹ��ð�9�ð�+����ٹ�+4�ð� - ��������ð�
			{
				//$start_time = "1100";
				$start_time = "11". substr($over_time,2,2);
			}
			else
			{
				$start_time = "1100";					//��������ð���(0800~1100)
			}
		}
		else
		{
			if ($over_time >= "0900")						//���ϱٹ�9�ð� - ��������ð�
			{
				$start_time = "1400";
			}
			else if ($over_time >= "0800")					//���ϱٹ�8�ð� - ��������ð�
			{
				//$start_time = "1300";
				$start_time = "13". substr($over_time,2,2);
			}
			else if ($over_time >="0700")					//���ϱٹ�7�ð� - ��������ð�
			{
				//$start_time = "1200";
				$start_time = "12". substr($over_time,2,2);
			}
			else if ($over_time >="0600")					//���ϱٹ�6�ð� - ��������ð�
			{
				//$start_time = "1100";
				$start_time = "11". substr($over_time,2,2);
			}
			else
			{
				$start_time = "1100";					//��������ð���(0800~1100)
			}
		}

		if ($now_time > $start_time)			//��������ð���(1) ���� ��� ��������(8)
		{
			$time_gubun = "8";
			$pay1 = "N";
		}
		else
		{
			$time_gubun = "1";
		}
	}
	else	//�ָ�,������
	{
		$time_gubun = "1";
		$start_time = "";
	}

	//��������or���Ĺ��� ����� ����
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
		alert("���üũ �����Դϴ�. �������� ������ �ּ���.");
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
		alert("���üũ�� �Ϸ�Ǿ����ϴ�.\n���ٹ��� ���üũ�� ���� �ʾҽ��ϴ�.\n���ٹ��� ��ٽð��� �濵�������� �˷��ּ���.");
	<? } else { ?>
		alert("���üũ�� �Ϸ�Ǿ����ϴ�.");
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
		alert("����� üũ�� �系������ �����մϴ�.");
		parent.location.href="/main.php";
	</script>
<?
	exit;
}
?>