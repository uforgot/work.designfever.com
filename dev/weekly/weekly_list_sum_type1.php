<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "����" : "";	//����븮 �Ǵ�

	//������ ����Ʈ�ڽ� ����
	if ($prs_position == '����' || $prs_position_tmp == '����')
	{
		$cur_team = $prs_team; //����Ʈ�ڽ� �⺻����
		$sel_view = 'N';	   //����Ʈ�ڽ� ���⿩��
		$sel_sql = "";		   //����Ʈ�ڽ� ����Ʈ
	}
	else if (in_array($prs_position,array('����','����')))
	{
		$sql = "SELECT 
					A.SEQNO, (SELECT TOP 1 TEAM FROM DF_TEAM_CODE WHERE R_SEQNO = A.SEQNO ORDER BY SORT) DEF_TEAM 
				FROM 
					DF_TEAM_CODE A 
				WHERE 
					A.TEAM = '$prs_team'";
		$rs = sqlsrv_query($dbConn,$sql);
		$record = sqlsrv_fetch_array($rs);

		$cur_team = $record['DEF_TEAM'];
		$sel_view = 'N';

		//[����ó��] �������� Interactive Lab�� ����
 		//if($prs_team == "Interactive Lab") {
		//	$sub_sql = " OR SEQNO = 18 OR R_SEQNO = 18"; 
		//}

		$sel_sql = "SELECT 
						STEP, TEAM 
					FROM 
						DF_TEAM_CODE 
					WHERE 
						SEQNO = ".$record['SEQNO']." OR R_SEQNO = ".$record['SEQNO']." $sub_sql
					ORDER BY SORT";
	}
	else if (in_array($prs_position,array('����','����','�̻�','��ǥ')))
	{
		$cur_team = '�濵������'; 
		$sel_view = 'N';
		$sel_sql = "SELECT 
						STEP, TEAM 
					FROM 
						DF_TEAM_CODE WITH(NOLOCK) 
					WHERE 
						SEQNO NOT IN(2,3,4,5) 
					ORDER BY SORT";
	}

	// �Ķ����	
	$week = isset($_REQUEST['week']) ? $_REQUEST['week'] : $log_weekly_create; 
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : $cur_team; 

	// ���� ���� ��ũ
	$sql = "SELECT MIN(WEEK_ORD) AS ORD FROM DF_WEEKLY WHERE WEEK_ORD > '$week'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$next_week = $record['ORD'];
	if($next_week) $next_link = "<a href='weekly_list_sum_type1.php?week=".$next_week."&team=".$team."'>��</a>";
	else $next_link = "��";
	
	// ���� ���� ��ũ
	$sql = "SELECT MAX(WEEK_ORD) AS ORD FROM DF_WEEKLY WHERE WEEK_ORD < '$week'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$prev_week = $record['ORD'];
	if($prev_week) $prev_link = "<a href='weekly_list_sum_type1.php?week=".$prev_week."&team=".$team."'>��</a>";
	else $prev_link = "��";

	//����������Ʈ ����Ʈ ����
	$this_month = substr($week,0,6);
	$prev_month = date("Ym",strtotime("-1 month",strtotime(date(substr($this_month,0,4)."-".substr($this_month,4,2)))));
	$next_month = date("Ym",strtotime("+1 month",strtotime(date(substr($this_month,0,4)."-".substr($this_month,4,2)))));

	$searchSQL = " WHERE SUBSTRING(CAST(WEEK_ORD AS CHAR(7)),1,6) IN('$prev_month','$this_month','$next_month')";

	$sql = "SELECT 
				B.PROJECT_NO, B.PRS_ID,
				(SELECT TOP 1 PRS_NAME FROM DF_PERSON WHERE PRS_ID = B.PRS_ID) PRS_NAME,
				(SELECT TOP 1 PART FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = B.PROJECT_NO AND PRS_ID = B.PRS_ID) PART,
				(SELECT TOP 1 PART_RATE FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = B.PROJECT_NO AND PRS_ID = B.PRS_ID) PART_RATE,
				(SELECT TOP 1 WEEK_AREA FROM DF_WEEKLY WHERE WEEK_ORD = '$week') WEEK_AREA
			FROM 
				DF_WEEKLY A WITH(NOLOCK) 
				INNER JOIN DF_WEEKLY_DETAIL B WITH(NOLOCK) 
				ON A.SEQNO = B.WEEKLY_NO
			$searchSQL
			GROUP BY B.PROJECT_NO, B.PRS_ID
			ORDER BY
				B.PROJECT_NO DESC, PART DESC, PRS_NAME ASC";
	$rs = sqlsrv_query($dbConn,$sql);

	$week_area = ""; // ���� ���� ����
	$proj_date = ""; // ������Ʈ ���� �Ⱓ(�迭)

	while ($record = sqlsrv_fetch_array($rs))
	{
		// ���� ������Ʈ �⺻ ����
//		if($record['PART'] || $record['PROJECT_NO'] == "DF0000_ETC") {
		if($record['PART']) {
			$list[$record['PROJECT_NO']][] = array
											(
												'id'=>$record['PRS_ID'],
												'name'=>$record['PRS_NAME'],
												'part'=>$record['PART'],
												'ratio'=>$record['PART_RATE']
											);
		}
		
		// ���� ���� ����
		if(!$week_area) {
			$week_area = $record['WEEK_AREA'];
		}

		// ���� ������Ʈ �Ⱓ ����
		$sql1 = "SELECT 
					CONVERT(char(10),START_DATE,23) start_date, CONVERT(char(10),END_DATE,23) end_date 
				FROM 
					DF_PROJECT_DETAIL 
				WHERE 
					PROJECT_NO = '".$record['PROJECT_NO']."' AND PRS_ID = '".$record['PRS_ID']."'";
		$rs1 = sqlsrv_query($dbConn,$sql1);

		while ($record1 = sqlsrv_fetch_array($rs1))
		{
			$proj_date[$record['PROJECT_NO']."^".$record['PRS_ID']][] = array
																		(
																			'start_date'=>$record1['start_date'],
																			'end_date'=>$record1['end_date']
																		);
		}
	}

	//echo "<xmp>";
	//print_r($list);
	//echo "</xmp>";
	//exit;

	//echo "<xmp>";
	//print_r($proj_date);
	//echo "</xmp>";
	//exit;

	// ���� ������,������
	$tmp_arr = explode("~", $week_area);
	$o_s_date = str_replace(".","-",$tmp_arr[0]);
	$o_e_date = str_replace(".","-",$tmp_arr[1]);	

	// �̹��� ��¥����
	$y1 = substr($week,0,4);
	$m1 = substr($week,4,2);
	$l1 = date('t',mktime(0,0,1,$m1,1,$y1));

	// ������ ��¥����
	$next_date = date("Ymd",strtotime("+1 month",strtotime(date($y1.'-'.$m1.'-1'))));
	$y2 = substr($next_date,0,4);
	$m2 = substr($next_date,4,2);
	$l2 = date('t',mktime(0,0,1,$m2,1,$y2));

	// �ش� ��¥�� ������Ʈ �����Ⱓ ���� �Ǵ�
	function isProjectDate($p_date_arr,$c_date) {
		$flag = false;

		for($i=0;$i<count($p_date_arr);$i++) {
			$p_s_date = $p_date_arr[$i]['start_date'];
			$p_e_date = $p_date_arr[$i]['end_date'];

			if($c_date >= $p_s_date && $c_date <= $p_e_date)
				$flag = true;
		}
	
		return $flag;			
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function weekSearch(val) {
		document.location.href = "./weekly_list_sum_type1.php?week=" + val + "&team=<?=$team?>";
	}

	function teamSearch(val) {
		if (!val)
		{
			alert('���� ��ȸ�� ������ �����մϴ�.\n������ ������ �ּ���!');
			return;
		}
		
		document.location.href = "./weekly_list_sum_type1.php?week=<?=$week?>&team=" + val;
	}
</script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post">
<input type="hidden" name="week" id="week" value="<?=$week?>">
<input type="hidden" name="team" id="team" value="<?=$team?>">

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<p class="hello work_list">
			<? if (in_array($prs_position,$positionA_arr)) { ?>
				<a href="#"><strong>+  �� �ְ�����</strong></a>
			<? } ?>
			<? if (in_array($prs_position,$positionS_arr)) { ?>
				<a href="javascript:alert('�غ��� �Դϴ�.');">+  ������Ȳ</a>
			<? } ?>
			</p>
			<div class="work_wrap clearfix">

				<div class="vacation_stats clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row">&nbsp;</th>
<!-- 							<th width="50%" scope="row">���� �ְ�����</th> -->
							<td>
								<?
									$week_titile = substr($week,0,4)."�� ".substr($week,4,2)."�� ".substr($week,6,1)."���� �ְ�����";
								?>
									<?=$prev_link?> <?=$week_titile?> <?=$next_link?>
								<?
									if ($sel_view == 'Y') 
									{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<select name="team" style="width:200px;" onchange="javascript:teamSearch(this.value);">
								<?
									$selSQL = $sel_sql;
									$selRs = sqlsrv_query($dbConn,$selSQL);

									while ($selRecord = sqlsrv_fetch_array($selRs))
									{
										$selStep = $selRecord['STEP'];
										$selTeam = $selRecord['TEAM'];

										if ($selStep == 3 || ($selTeam == '�濵������' || $selTeam == 'ȫ����'))
										{
											$selTeam1 = $selRecord['TEAM'];
										}  
										else
										{
											$selTeam1 = "";
										}

										$blank = "";
										for ($i=0;$i<=$selStep;$i++)
										{
											$blank .= "&nbsp;&nbsp;&nbsp;";
										}

										if ($selTeam == $team) 
										{ 
											$selected = " selected"; 
										}
										else
										{
											$selected = "";
										}
								?>
										<option value="<?=$selTeam1?>" <?=$selected?>><?=$blank?><?=$selTeam?></option>
								<?
									}
								?>
								</select>
								<?
									}
								?>
							</td>
						</tr>
					</table>
				</div>

				<table class="vacation notable work4 work_stats4" width="100%" style="margin-bottom:10px;">
					<caption>���� �ְ����� ���̺�</caption>
					<colgroup>
						<col width="*" />
						<col width="11%" />
						<col width="36%" />
						<col width="36%" />
					</colgroup>

					<thead>
						<tr style="border-right:0;">
							<th style="border-right:0;">������Ʈ</th>
							<th style="border-right:0;">����.������(��������)</th>
							<th style="border-right:0;"><?=$m1?> ��</th>
							<th style="border-right:0;"><?=$m2?> ��</th>
						</tr>
					</thead>

					<tbody>
<?
	if (count($list)==0) 
	{
?>
						<tr>
							<td colspan="6" class="bold">�ش� ������ �����ϴ�.</td>
						</tr>
<?
	}
	else
	{
		foreach($list as $key1 => $val1)
		{
			$searchSQL = " WHERE PROJECT_NO = '".$key1."'";

			$sql = "SELECT TITLE, STATUS FROM DF_PROJECT $searchSQL";
			$rs = sqlsrv_query($dbConn,$sql);									
			$record = sqlsrv_fetch_array($rs);

			if($record)	{
				$project_name = $record['TITLE'];
				if($record['STATUS'] == 'END')	$project_status = "<span style='font-style:italic;color:red;'>(����)</span> ";
				else							$project_status = "";
			} else if($key1 == "DF0000_ETC") {
				$project_name = "��Ÿ����";
				$project_status = "";
			}

			$name = "";
			$this_month = "";
			$next_month = "";
			$line_cnt = count($val1);
			$cnt = 1;

			foreach($val1 as $key2 => $val2)
			{
				if($key1 == "DF0000_ETC") 
				{
					$name .= "<div style='padding:1px 0 2px 1px;'><table cellpadding='0' cellspacing='0' class='name'><tr>";
					$name .= "<td>".$val2['name']."</td>";
					$name .= "</tr></table></div>";
				}
				else					  
				{
					$name .= "<div style='padding:1px 0 2px 1px;'><table cellpadding='0' cellspacing='0' class='name'><tr>";
					$name .= "<td>".$val2['part'].".".$val2['name']." (".$val2['ratio']."%)</td>";
					$name .= "</tr></table></div>";
				}

				// �̹��� �׷���
				$this_count = 0;
				$this_month .= "<div style='padding:1px 0 1px 1px;'><table cellpadding='0' cellspacing='0' class='graph'><tr>";
				for($i=1;$i<=$l1;$i++) {
					$c_date = date($y1.'-'.$m1.'-'.str_pad($i,2,'0',STR_PAD_LEFT));
					$this_week = date('w',strtotime($c_date));

					// ����, ��/�� ����
					if($this_week == 6)	{
						$color1 = "color:#0080ff;";
					} else if($this_week == 0) {
						$color1 = "color:#ff6262;";
					} else {
						$color1 = "";
					}
					
					// ���� ���� ����
					if($c_date == $o_s_date) {
						$color1 .= "border-left:1px solid #ffff00;";
					} else if($c_date == $o_e_date) {
						$color1 .= "border-right:1px solid #ffff00;";
					} else {
						$color1 .= "";
					}

					// ������Ʈ �������� üũ
					if(isProjectDate($proj_date[$key1."^".$val2['id']],$c_date)) {
						$graph1 = "background-color:orange;";
						$this_count++;
					} else {
						$graph1 = "";
					}

					$this_month .= "<td style='border-bottom:1px solid #e3e3e3;".$color1.$graph1."'>$i</td>";
				}
				$this_month .= "</tr></table></div>";

				// ������ �׷���
				$next_count = 0;
				$next_month .= "<div style='padding:1px 0 1px 1px;'><table cellpadding='0' cellspacing='0' class='graph'><tr>";
				for($j=1;$j<=$l2;$j++) {
					$c_date = date($y2.'-'.$m2.'-'.str_pad($j,2,'0',STR_PAD_LEFT));
					$next_week = date('w',strtotime($c_date));

					// ����, ��/�� ����
					if($next_week == 6) {
						$color2 = "color:#0080ff;";
					} else if($next_week == 0) {
						$color2 = "color:#ff6262;";
					} else {
						$color2 = "";
					}

					// ���� ���� ����
					if($c_date == $o_s_date) {
						$color2 .= "border-left:1px solid #ffff00;";
					} else if($c_date == $o_e_date) {
						$color2 .= "border-right:1px solid #ffff00;";
					} else {
						$color2 .= "";
					}

					// ������Ʈ �������� üũ
					if(isProjectDate($proj_date[$key1."^".$val2['id']],$c_date)) {
						$graph2 = "background-color:orange;";
						$next_count++;
					} else {
						$graph2 = "";
					}

					$next_month .= "<td style='border-bottom:1px solid #e3e3e3;".$color2.$graph2."'>$j</td>";
				}
				$next_month .= "</tr></table></div>";

				$cnt++;
			}
?>
						<!-- loop -->		
						<tr>
							<td><?=$project_status?><?=$project_name?></td>
							<td><?=$name?></td>
							<td><?=$this_month?></td>
							<td><?=$next_month?></td>
						</tr>
						<!-- loop -->		
<?
		}
	}
?>
					</tbody>
					<tfoot>

					</tfoot>					
				</table>

				<span style="padding-left:40px;">
					<b class="txt_left_p" style="margin-bottom:0px;">* �ְ����� �ۼ����� ���� ������ ��Ͽ� ��Ÿ���� �ʽ��ϴ�.</b>
				</span>

			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
