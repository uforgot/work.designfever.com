<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$now_date = date("Y-m-d");
	$yesterday_date = date("Y-m-d",strtotime ("-1 day"));

	$where = " AND PRF_ID IN (1,2,3,4,5)";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION2_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_POSITION2 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION1_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION1 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, PRS_NAME";

	function getMemberCommuting($prs_id, $date, $yesterday) {
		global $dbConn;

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
			else if ($col_gubun1 == "0")		//���Ĺ��� ����. �����üũ X
			{
				$checktime1 = "";
			}
			else						 		//�ް� - ���/��� �ð� ǥ�� ���� - ���� 00:00��� 23:59������� �����Ǿ� ����
			{
				$checktime1 = "";
			}

			//��ٽð�
			$checktime2 = substr($col_checktime2,8,2) .":". substr($col_checktime2,10,2);
			if ($checktime2 == ":") { $checktime2 = ""; }

			if ($col_gubun2 == "2" || $col_gubun2 == "3" || $col_gubun2 == "5" || $col_gubun2 == "6" || $col_gubun2 == "9")
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

		$icon = ($flag===true) ? "<font color=\"green\">��</font>" : "<font color=\"red\">��</font>";
		
		// ���� ���
		$arr = array(15,22,24,29,79,87,148);
		if(in_array($prs_id,$arr)) $icon = "<font color=\"white\">��</font>";

		return $icon;
	}

	// ����
	function getWeekName($index) {
		$week_name = array("��","��","ȭ","��","��","��","��");

		return $week_name[$index];
	}
?>

<? include INC_PATH."/top.php"; ?>

</head>

<body>
<div class="wrapper">
<form method="post" name="form" id="form">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/org_menu.php"; ?>

			<div class="work_wrap clearfix">
			
				<div class="cal_top2 clearfix">
					<strong><?=date("Y")?></strong>��
					<strong><?=date("m")?></strong>��
					<strong><?=date("d")?></strong>��
					<strong><?=getWeekName(date("w"))?></strong>����
					<strong><?=date("H:i:s")?></strong> ����
				</div>

				<div style="padding:0 2.5% 5px 2.5%;">
					<span><font color="green">��</font> ��� &nbsp;&nbsp;<font color="red">��</font> ����/�İ�/���/�ް�</span>
				</div>

			<div class="tables">
				<table class="notable work_stats5 group" width="100%" id="4F">
					<thead>
						<tr>
							<th class="div">4 ��</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats5" width="100%" id="CEO">
					<tbody> 
						<tr class="plural">
							<th class="team">CEO</th>
						</tr>
						<tr>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_POSITION2 = '��ǥ'";
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position1 = $record['PRS_POSITION1'];
			$col_prs_position2 = $record['PRS_POSITION2'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span>��ǥ</span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>									
							
							</td>
						</tr>
					</tbody>
				</table>

				<table class="notable work_stats5 group" width="100%" id="3F">
					<thead>
						<tr>
							<th class="div">3 ��</th>
						</tr>
					</thead>
				</table>
<?
	$teamSql = "SELECT 
					TEAM 
				FROM 
					DF_TEAM_2018 WITH(NOLOCK) 
				WHERE 
					STEP > 1 AND FLOOR = 3
				ORDER BY 
					SORT";
	$teamRs = sqlsrv_query($dbConn, $teamSql);
	while($teamRecord = sqlsrv_fetch_array($teamRs))
	{
		$col_team = $teamRecord['TEAM'];
?>
				<table class="notable work_stats5" width="100%" id="<>=$col_team?>">
					<tbody> 
						<tr class="plural">
							<th class="team"><?=$col_team?></th>
						</tr>
						<tr>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '$col_team'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>	
							</td>
						</tr>
					</tbody>
				</table>
<?								
	}
?>
				<table class="notable work_stats5 group" width="100%" id="2F">
					<thead>
						<tr>
							<th class="div">2 ��</th>
						</tr>
					</thead>
				</table>
<?
	$teamSql = "SELECT 
					TEAM 
				FROM 
					DF_TEAM_2018 WITH(NOLOCK) 
				WHERE 
					STEP > 1 AND FLOOR = 2
				ORDER BY 
					SORT";
	$teamRs = sqlsrv_query($dbConn, $teamSql);
	while($teamRecord = sqlsrv_fetch_array($teamRs))
	{
		$col_team = $teamRecord['TEAM'];
?>
				<table class="notable work_stats5" width="100%" id="<>=$col_team?>">
					<tbody> 
						<tr class="plural">
							<th class="team"><?=$col_team?></th>
						</tr>
						<tr>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '$col_team'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>	
							</td>
						</tr>
<?			if ($col_team == "LAB") {	?>
						<tr>
							<th class="teamname team" style="border-bottom:0px;"></th>
						</tr>
<?			}	?>
					</tbody>
				</table>
<?								
	}
?>

						
			</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>

<div class="person_pop_detail" id="popup" style="display:none;">

</div>
</div>
</body>
</html>