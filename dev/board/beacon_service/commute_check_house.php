<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null;		
	
	
	$prs_id = "";
	$prs_name = "";
	$prs_login = "";
	$prs_team = "";
	$prs_position = "";

	$sql = "SELECT TOP 1 
				PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE 
				PRS_BEACON = '$beacon'
			ORDER BY 
				PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);	
	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$prs_id = $record['PRS_ID'];
		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];
		$prs_team = $record['PRS_TEAM'];
		$prs_position = $record['PRS_POSITION'];

		$state = "����";
		$message = "success";	
	}
	else
	{
		$state = "����";
		$message = "��ϵ��� ���� ��ȿŰ�Դϴ�.";
	}

	$time_gubun = isset($_REQUEST['time_gubun']) ? $_REQUEST['time_gubun'] : null;
	if ($time_gubun == "" && date("G") < 8) { $time_gubun = "before"; }
	//if (date("G") < 8) { $time_gubun = "before"; }

	$now = date("YmdHis");								//���� ��� ����Ͻú���

	$ip = REMOTE_IP;									//����IP
	$gubun = "�����";

	if ($time_gubun == "before")
	{
		$today = date("Y-m-d",strtotime ("-1 day"));		//���� ��¥
		$today2 = date("Ymd",strtotime ("-1 day"));			//���� ��¥
		$yesterday = date("Y-m-d",strtotime ("-2 day"));	//���� ��¥
		$yesterday2 = date("Ymd",strtotime ("-2 day"));		//���� ��¥
		$next = date("Y-m-d");								//���� ��¥
		$next2 = date("Ymd");								//���� ��¥

		$now2 = $today2 . (24+substr($now,8,2)) . substr($now,10,4);
	}
	else
	{
		$today = date("Y-m-d");								//���� ��¥
		$today2 = date("Ymd");								//���� ��¥
		$yesterday = date("Y-m-d",strtotime ("-1 day"));	//���� ��¥
		$yesterday2 = date("Ymd",strtotime ("-1 day"));		//���� ��¥
		$next = date("Y-m-d",strtotime ("+1 day"));			//���� ��¥
		$next2 = date("Ymd",strtotime ("+1 day"));			//���� ��¥

		$time_gubun = "after";
		$now2 = $now;	
	}

	$sql = "EXEC SP_MAIN_01 '$prs_id','$prs_name','$today','$yesterday','$next'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$yesterday_gubun1 = $record['YESTERDAY_GUBUN1'];			//���� ���
		$yesterday_gubun2 = $record['YESTERDAY_GUBUN2'];			//���� ���
		$yesterday_checktime1 = $record['YESTERDAY_CHECKTIME1'];	//���� ���	
		$yesterday_checktime2 = $record['YESTERDAY_CHECKTIME2'];	//���� ���
		$yesterday_totaltime = $record['YESTERDAY_TOTALTIME'];		//���� �ٹ��ð�
		$yesterday_overtime = $record['YESTERDAY_OVERTIME'];		//���� ����ٹ��ð�
		$yesterday_memo1 = $record['YESTERDAY_MEMO1'];				//���� ��� ��������
		$yesterday_memo2 = $record['YESTERDAY_MEMO2'];				//���� ��� ��������
		$today_gubun1 = $record['TODAY_GUBUN1'];					//���� ���
		$today_gubun2 = $record['TODAY_GUBUN2'];					//���� ���
		$today_checktime1 = $record['TODAY_CHECKTIME1'];			//���� ���	
		$today_checktime2 = $record['TODAY_CHECKTIME2'];			//���� ���
		$today_totaltime = $record['TODAY_TOTALTIME'];				//���� �ٹ��ð�
		$today_memo1 = $record['TODAY_MEMO1'];						//���� ��� ��������
		$today_memo2 = $record['TODAY_MEMO2'];						//���� ��� ��������
		$today_off_time = $record['TODAY_OFF_TIME'];				//���� ����ð���
		$today_off_minute = $record['TODAY_OFF_MINUTE'];			//���� ����ð���
		$next_gubun1 = $record['NEXT_GUBUN1'];						//���� ���
		$next_gubun2 = $record['NEXT_GUBUN2'];						//���� ���
		$next_checktime1 = $record['NEXT_CHECKTIME1'];				//���� ���	
		$next_checktime2 = $record['NEXT_CHECKTIME2'];				//���� ���
	}

	$sql = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$today) ."'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$today_kind = $record['DATEKIND'];

	$pay4 = "N";

	if ($today_kind == "BIZ")
	{
		if ($overtime >= "1000") { $pay4 = "Y"; }
		if ($overtime >= "0400" && substr($now2,8,4) >= "2400" && substr($now2,8,4) <= "3000") { $pay4 = "Y"; }
		$pay6 = "N";
	}
	else
	{
		if ($overtime2 >= "1000") { $pay4 = "Y"; }
		if ($overtime2 >= "0700" && substr($now2,8,4) >= "2400" && substr($now2,8,4) <= "3000") { $pay4 = "Y"; }
		$pay6 = "N";
	}

	if ($today_checktime1 != "")	//���� ���üũ O
	{
		$sql = "INSERT INTO DF_CHECKTIME_HOUSE
				 (PRS_ID, PRS_LOGIN, PRS_NAME, DATE, BEACON, REGDATE)
				 VALUES
				 ('$prs_id','$prs_login','$prs_name','$today','$beacon',getdate())";
		$rs = sqlsrv_query($dbConn,$sql);

		$sql = "UPDATE DF_CHECKTIME SET 
					PAY4 = '$pay4', PAY6 = '$pay6' 
				WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
		$rs = sqlsrv_query($dbConn,$sql);
		$error_no = 1;
	}
	else if ($today_checktime1 == "" && $yesterday_checktime1 != "")	//���� ���üũ X, ���� ���üũ O
	{
		$sql = "INSERT INTO DF_CHECKTIME_HOUSE
				 (PRS_ID, PRS_LOGIN, PRS_NAME, DATE, BEACON, REGDATE)
				 VALUES
				 ('$prs_id','$prs_login','$prs_name','$today','$beacon',getdate())";
		$rs = sqlsrv_query($dbConn,$sql);

		$sql = "UPDATE DF_CHECKTIME SET 
					PAY4 = '$pay4', PAY6 = '$pay6'
				WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
		$rs = sqlsrv_query($dbConn,$sql);
		$error_no = 2;
	}
	else 
	{
		echo "<script>alert('Error 3. ���� ���üũ�� ���� �����̽��ϴ�. ����� ������ �濵�������� �˷��ּ���.');</script>";
		exit;
	}
	
	if ($rs == false)
	{
		//echo "error". $error_no. ". ���� üũ �����Դϴ�. Error ��ȣ�� ����ϼż� �������� ������ �ּ���.";
		echo "<script>alert('error ���� üũ �����Դϴ�. Error ��ȣ�� ����ϼż� �������� ������ �ּ���.');</script>";
	}
	else
	{
		if ($message == "success") 
		{		
			echo "<script>alert('���� üũ�� �Ǿ����ϴ�.');</script>";
			exit;
		}
		else 
		{
			echo $message;
		}
		exit;
	}
?>