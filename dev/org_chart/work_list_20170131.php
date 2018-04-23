<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$now_date = date("Y-m-d");
	$yesterday_date = date("Y-m-d",strtotime ("-1 day"));

	$where = " AND PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN(102)";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby . " END, PRS_JOIN, PRS_NAME";

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
			else if ($col_gubun1 == "10") 		//�ް� - ���/��� �ð� ǥ�� ���� - ���� 00:00��� 23:59������� �����Ǿ� ����
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "11")	//����
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "12")	//������
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "13")	//��Ÿ
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "14")	//���
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "15")	//����/�Ʒ�
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "16")	//������Ʈ �ް�
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "17")	//�������� �ް�
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "18")	//���� �ް�
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "19")	//����
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "0")	//���Ĺ��� ����. �����üũ X
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
		$arr = array(15,22,24,87,148);
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
					<span><font color="green">��</font> ��� &nbsp;&nbsp;<font color="red">��</font> ���/�ް�</span>
				</div>

			<div class="tables">
				<table class="notable work_stats5 group" width="100%" id="4��">
					<thead>
						<tr>
							<th class="div">4 ��</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats5" width="100%" id="digital experience division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">CEO</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CEO'";
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

						<tr class="plural">
							<th class="teamname team">df lab</th>
							<th class="team">ix1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'df lab'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="5"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="5">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ix1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">ix2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ix2'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">ixd</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ixd'". $where . $orderbycase;
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

				<table class="notable work_stats5 group" width="100%" id="3��">
					<thead>
						<tr>
							<th class="div">3 ��</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats5" width="100%" id="digital experience division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">CSO</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CSO'";
		$rs = sqlsrv_query($dbConn, $sql);
		$record = sqlsrv_fetch_array($rs);
		$col_prs_id = $record['PRS_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_position = $record['PRS_POSITION'];

		$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>									
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team" style="border-bottom:0px;">brand experience team</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'brand experience team'". $where . $orderbycase;;
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
						<tr class="plural">
							<th class="teamname team">digital experience division</th>
							<th class="team">dx1</th>
						</tr>
<?
//		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'digital experience division'". $where . $orderbycase;
//		$rs = sqlsrv_query($dbConn, $sql);
//
//		While ($record = sqlsrv_fetch_array($rs))
//		{
//			$col_prs_id = $record['PRS_ID'];
//			$col_prs_name = $record['PRS_NAME'];
//			$col_prs_position = $record['PRS_POSITION'];
//
//			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<!--ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul-->
							</td>
<?
//		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dx1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">dx2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dx2'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="teamname team">design2 division</th>
							<th class="team">design3</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'Design2 Division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="5">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design3'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">design4</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design4'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">design5</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design5'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="teamname team">motion graphic division</th>
							<th class="team">mg1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'motion graphic division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'mg1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">mg2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'mg2'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="teamname team" style="border-bottom:0px;">�濵������</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '�濵������'". $where . $orderbycase;
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
				<table class="notable work_stats5 group" width="100%" id="2��">
					<thead>
						<tr>
							<th class="div">2 ��</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats5" width="100%" id="digital marketing division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">CCO</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CCO'";
		$rs = sqlsrv_query($dbConn, $sql);
		$record = sqlsrv_fetch_array($rs);
		$col_prs_id = $record['PRS_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_position = $record['PRS_POSITION'];

		$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>									
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">digital marketing division</th>
							<th class="team">dm1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'digital marketing division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}	
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dm1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">dm2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dm2'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
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
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">design1 division</th>
							<th class="team">design1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'Design1 Division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">design2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design2'". $where . $orderbycase;
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
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">film & content division</th>
							<th class="team">fc</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'film & content division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'fc'". $where . $orderbycase;
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
						<tr>
							<th class="teamname team" style="border-bottom:0px;"></th>
							<td class="list1 top">
						</tr>
					</tbody>
				</table>

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