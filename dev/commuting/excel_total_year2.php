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
					PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,102,1482) AND PRS_TEAM = '$teamArr_ex[$i]'
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
	header( "Content-Disposition: attachment; filename=�������_���ϻ�_".$p_year.".xls" );
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
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">1��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">2��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">3��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">4��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">5��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">6��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">7��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">8��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">9��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">10��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">11��</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">12��</td>
			</tr>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">no.</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�̸�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;">�μ�</td>
			<? for ($i=1; $i<=12; $i++) { ?>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�ʰ�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�̸�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">�ް�</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">������</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">����</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">��Ÿ</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">���</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">��üũ</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;">��</td>
			<? } ?>
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
?>
			<tr>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$no?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$name_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$position_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';border-right:2px solid #000;"><?=$team_ex[$i]?></td>
<?
			for ($j=1; $j<=12; $j++)
			{
				if (strlen($j) == 1)
				{
					$date = $p_year ."-0". $j;
				}
				else
				{
					$date = $p_year ."-". $j;
				}

				$sql = "EXEC SP_EXCEL_TOTAL_YEAR_02 '$id_ex[$i]','$date'";
				$rs = sqlsrv_query($dbConn, $sql);

				while ($record = sqlsrv_fetch_array($rs)) 
				{
					$over = $record['OVER_DAY'];		//�ʰ�
					$under = $record['UNDER_DAY'];		//�̸�
					$count1 = $record['COUNT1'];		//�ް�
					$count2 = $record['COUNT2'];		//����
					$count3 = $record['COUNT3'];		//������
					$count4 = $record['COUNT4'];		//����
					$count5 = $record['COUNT5'];		//��Ÿ
					$count6 = $record['COUNT6'];		//���
					$checked = $record['CHECKED'];		//����üũ
					$allday = $record['ALLDAY'];		//�� �ٹ���
					$noncheck = $allday - $checked;
					$total = $over + $under + $count1 + $count2 + $count3 + $count4 + $count5 + $count6 + $noncheck;
?>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$over?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$under?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count3?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count4?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count5?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count6?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$noncheck?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';border-right:2px solid #000;"><?=$total?></td>
<?
				}
			}
?>
			</tr>
<?
		}
	}
?>
		</tbody>
		<tbody>
