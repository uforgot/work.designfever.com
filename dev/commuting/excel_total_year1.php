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

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 

	if ($p_year == "") $p_year = $nowYear;

	$date = $p_year;

	$selSQL = "SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
	$selRs = sqlsrv_query($dbConn,$selSQL);

	$teamArr = "";
	while ($selRecord = sqlsrv_fetch_array($selRs))
	{
		$selTeam = $selRecord['TEAM'];

		$teamArr .= $selTeam."##";
	}

	$teamArr_ex = explode("##",$teamArr);

	$id = "";
	$name = "";
	$team = "";
	$position = "";

	for ($i=0; $i<sizeof($teamArr_ex); $i++)
	{
		$sql = "SELECT 
					PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION
				FROM 
					DF_PERSON WITH(NOLOCK)
				WHERE 
					PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,102,148) AND PRS_TEAM = '$teamArr_ex[$i]'
				ORDER BY CASE 
						WHEN PRS_POSITION='��ǥ' THEN 1
						WHEN PRS_POSITION='�̻�' THEN 2
						WHEN PRS_POSITION='����' THEN 3
						WHEN PRS_POSITION='����' THEN 4
						WHEN PRS_POSITION='å��' THEN 5
						WHEN PRS_POSITION='�븮' THEN 6
						WHEN PRS_POSITION='����' THEN 7
						WHEN PRS_POSITION='����' THEN 8
						WHEN PRS_POSITION='���' THEN 9
						WHEN PRS_POSITION='����' THEN 10 END, PRS_NAME";
		$rs = sqlsrv_query($dbConn, $sql);

		while ($record=sqlsrv_fetch_array($rs))
		{
			$id = $id . $record['PRS_ID'] ."##";;
			$name = $name . $record['PRS_NAME'] ."##";
			$team = $team . $record['PRS_TEAM'] ."##";
			$position = $position . $record['PRS_POSITION'] ."##";
		}
	}

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=�������_����_".$p_year.".xls" );
?>

	<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=EUC-KR'>
	<style>
	<!--
	br{mso-data-placement:same-cell;}
	-->
	</style>
	<table border=1>
		<thead>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="5">���ϱٹ���</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="2">���ϱٹ���</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
			</tr>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">no.</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�̸�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�μ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���<br>��ٽð�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���<br>��ٽð�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�����<br>�հ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����ٹ�<br>�̸� �ٹ���</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����ٹ�<br>�̻� �ٹ���</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�����<br>�հ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�ٹ��ð�<br>�հ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�ް�/����/��Ÿ<br>�հ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���</td>
			</tr>
		</thead>
		<tbody>
<?
	$id_ex = explode("##",$id);
	$name_ex = explode("##",$name);
	$team_ex = explode("##",$team);
	$position_ex = explode("##",$position);

	$no = 1;
	for ($i=0; $i<sizeof($id_ex); $i++)
	{
		if ($id_ex[$i] != "")
		{
			$sql = "EXEC SP_EXCEL_TOTAL_YEAR_01 '$id_ex[$i]','$date'";
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);
			if (sizeof($record) > 0)
			{
				$biz_avgtime1 = $record['BIZ_AVGTIME1'];		//���� �����ٽ�
				$biz_avgminute1 = $record['BIZ_AVGMINUTE1'];	//���� �����ٺ�
				$biz_avgtime2 = $record['BIZ_AVGTIME2'];		//���� �����ٽ�
				$biz_avgminute2 = $record['BIZ_AVGMINUTE2'];	//���� �����ٺ�
				$biz_commute = $record['BIZ_COMMUTE'];			//���� �����
				$biz_under = $record['BIZ_UNDER'];				//���� 9�ð� �̸� �ٹ���
				$biz_over = $record['BIZ_OVER'];				//���� 9�ð� �ʰ� �ٹ���
				$law_commute = $record['LAW_COMMUTE'];			//���� �����
				$law_totaltime = $record['LAW_TOTALTIME'];		//���� �ٹ��ð���
				$law_totalminute = $record['LAW_TOTALMINUTE'];	//���� �ٹ��ð���
				$vacation = $record['VACATION'];			//�ް�/����/��Ÿ �հ�
	
				if ($biz_avgtime1 == "") { $biz_avgtime1 = "0"; }
				if ($biz_avgminute1 == "") { $biz_avgminute1 = "0"; }
				if ($biz_avgtime2 == "") { $biz_avgtime2 = "0"; }
				if ($biz_avgminute2 == "") { $biz_avgminute2 = "0"; }
				if ($law_totaltime == "") { $law_totaltime = "0"; }
				if ($law_totalminute == "") { $law_totalminute = "0"; }

				if (strlen($biz_avgtime1) == 1) { $biz_avgtime1 = "0".$biz_avgtime1; }
				if (strlen($biz_avgminute1) == 1) { $biz_avgminute1 = "0".$biz_avgminute1; }
				if (strlen($biz_avgtime2) == 1) { $biz_avgtime2 = "0".$biz_avgtime2; }
				if (strlen($biz_avgminute2) == 1) { $biz_avgminute2 = "0".$biz_avgminute2; }
				if (strlen($law_totaltime) == 1) { $law_totaltime = "0".$law_totaltime; }
				if (strlen($law_totalminute) == 1) { $law_totalminute = "0".$law_totalminute; }
?>
			<tr>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$no?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$name_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$position_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$team_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_avgtime1?> : <?=$biz_avgminute1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_avgtime2?> : <?=$biz_avgminute2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_under?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_over?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$law_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$law_totaltime?> : <?=$law_totalminute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$vacation?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"></td>
			</tr>
<?
			}
		}
		$no++;
	}
?>
		</tbody>
	</table>

