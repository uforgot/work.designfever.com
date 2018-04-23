<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$now_date = date("Y-m-d");
	$yesterday_date = date("Y-m-d",strtotime ("-1 day"));

	$where = " AND PRF_ID IN (1,2,3,4,5,7)";

	$work_count = array("4F"=>0,"3F"=>0,"2F"=>0);

	function getMemberCommuting($prs_id, $date, $yesterday, $floor) {
		global $dbConn, $work_count;

		$flag = false;

		//�������,����,�ް�,�ٹ��ϼ�,����,�����ٽ�,�����ٺ�,�����ٽ�,�����ٺ�,�ѱٹ��ð�
		$sql = "EXEC SP_COMMUTING_MEMBER_02 '$prs_id','$date','$yesterday'";
		$rs = sqlsrv_query($dbConn,$sql);
		$record = sqlsrv_fetch_array($rs);

		if (sizeof($record) > 0)
		{
			$col_date = $record['DATE'];					//��¥
			$col_datekind = $record['DATEKIND'];			//������ ����
			$col_gubun = $record['GUBUN'];					//����ٱ���
			$col_gubun1 = $record['GUBUN1'];				//��ٱ���
			$col_gubun2 = $record['GUBUN2'];				//��ٱ���
			$col_checktime1 = $record['CHECKTIME1'];		//��ٽð�
			$col_checktime2 = $record['CHECKTIME2'];		//��ٽð�

			//��ٽð�
			$checktime1 = substr($col_checktime1,8,2) .":". substr($col_checktime1,10,2);
			if ($checktime1 == ":") { $checktime1 = ""; }

			if ($col_gubun1 == "1") {}			//���
			else if ($col_gubun1 == "4") {}		//����
			else if ($col_gubun1 == "6") {}		//�ܱ�
			else if ($col_gubun1 == "7") {}		//����
			else if ($col_gubun1 == "8") {}		//����
			else if ($col_gubun1 == "0")	//���Ĺ��� ����. �����üũ X
			{
				$checktime1 = "";
			}
			else //�ް� - ���/��� �ð� ǥ�� ���� - ���� 00:00��� 23:59������� �����Ǿ� ����
			{
				$checktime1 = "";
			}

			//��ٽð�
			$checktime2 = substr($col_checktime2,8,2) .":". substr($col_checktime2,10,2);
			if ($checktime2 == ":") { $checktime2 = ""; }

			if ($col_gubun2 == "2" || $col_gubun2 == "3" || $col_gubun2 == "6" || $col_gubun2 == "9")
			{
				if ($col_gubun2 == "2" || $col_gubun2 == "3") {}	//���
				else if ($col_gubun2 == "5") {}						//������Ʈ ����
				else if ($col_gubun2 == "6") {}						//�ܱ�	
				else if ($col_gubun2 == "9") {}						//����
				else if ($col_gubun2 == "0") {}						//�������� ����. �����üũ X
			}
		}

		if(strlen($checktime1) > 1) $flag = true;
		if(strlen($checktime2) > 1) $flag = false;

		if($flag===true) {
			$work_count[$floor]++;
		}
	}

	// 3��
	$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) ";
	$sql.= "WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WHERE FLOOR = 3)". $where;
	$rs = sqlsrv_query($dbConn, $sql);

	While ($record = sqlsrv_fetch_array($rs))
	{
		$col_prs_id = $record['PRS_ID'];
		getMemberCommuting($col_prs_id, $now_date, $yesterday_date, '3F');
	}

	// 2��
	$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) ";
	$sql.= "WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WHERE FLOOR = 2)". $where;
	$rs = sqlsrv_query($dbConn, $sql);

	While ($record = sqlsrv_fetch_array($rs))
	{
		$col_prs_id = $record['PRS_ID'];
		getMemberCommuting($col_prs_id, $now_date, $yesterday_date, '2F');
	}	

	// ���հ�
	foreach($work_count as $val) $work_count['TOT'] += $val;
?>