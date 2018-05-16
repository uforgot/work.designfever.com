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
		echo "ī���ȣ�� �ʿ��մϴ�.";
		exit;
	}

	$now = date("YmdHis");								//���� ��� ����Ͻú���
	$now_time = substr($now,8,4);						//���� ��� �ð���
	$today = date("Y-m-d");								//���� ��¥
	$yesterday = date("Y-m-d",strtotime ("-1 day"));	//���� ��¥

	$ip = REMOTE_IP;									//����IP
	$gubun = "�����";
	$state = "����";

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

		$state = "����";
		$message = $col_prs_name ."��, ���üũ�� �Ǿ����ϴ�. ". date("Y-m-d H:i:s");
	}
	else
	{
		$state = "����";
		$message = "��ϵ��� ���� ī���Դϴ�.";
	}

	if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

	//���� ���üũ Ȯ��
	$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	$yesterday_checktime1 = $record['CHECKTIME1'];
	$yesterday_checktime2 = $record['CHECKTIME2'];

	//��� �ߺ�üũ Ȯ��
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
			echo "�̹� ��� üũ �ϼ̽��ϴ�.";
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

	if ($today_kind == "BIZ")	//����
	{

		//ź�±ٹ���
		//�������� ó�� ���� ���� �ٹ��ð� Ȯ��
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
	
	if ($state == "����") 
	{
		$maxno = 0;
		//��������or���Ĺ��� ����� ����
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
			$message = "���üũ �����Դϴ�. �������� ������ �ּ���.";
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
	$message = "��/��� üũ�� �系������ �����մϴ�.";
}

	echo $message;
?>