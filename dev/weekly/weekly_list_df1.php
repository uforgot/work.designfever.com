<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//������ ����Ʈ�ڽ� ����
	$cur_team = 'Ŀ�´����̼�������ȹ1��'; 
	$sel_view = 'Y';
	$sel_sql = "SELECT 
					TEAM 
				FROM 
					DF_TEAM_CODE WITH(NOLOCK) 
				WHERE 
					VIEW_YN = 'Y' AND TEAM LIKE '%1��%'
				ORDER BY SORT";
	
	//$last_week = 2014104;

	$week = isset($_REQUEST['week']) ? $_REQUEST['week'] : $last_week; 
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : $cur_team; 

	// ���� ���� ��ũ
	$sql = "SELECT MIN(WEEK_ORD) AS ORD FROM DF_WEEKLY WHERE WEEK_ORD > '$week'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$next_week = $record['ORD'];
	if($next_week) $next_link = "<a href='weekly_list_df1.php?week=".$next_week."&team=".$team."'>��</a>";
	else $next_link = "��";
	
	// ���� ���� ��ũ
	$sql = "SELECT MAX(WEEK_ORD) AS ORD FROM DF_WEEKLY WHERE WEEK_ORD < '$week'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$prev_week = $record['ORD'];
	if($prev_week) $prev_link = "<a href='weekly_list_df1.php?week=".$prev_week."&team=".$team."'>��</a>";
	else $prev_link = "��";

	//����������Ʈ ����Ʈ ����
	$searchSQL = " WHERE WEEK_ORD LIKE '$week%' AND PRS_TEAM = '$team'";

	$sql = "SELECT 
				A.SEQNO, A.MEMO, A.PRS_NAME, B.PROJECT_NO, B.THIS_WEEK_CONTENT, B.NEXT_WEEK_CONTENT, B.THIS_WEEK_RATIO, B.PRS_ID,
				(SELECT DISTINCT PART_RATE FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = B.PROJECT_NO AND PRS_ID = B.PRS_ID) PART_RATE
			FROM 
				DF_WEEKLY A WITH(NOLOCK) 
				INNER JOIN DF_WEEKLY_DETAIL B WITH(NOLOCK) 
				ON A.SEQNO = B.WEEKLY_NO
			$searchSQL
			ORDER BY
				B.PROJECT_NO DESC";

	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$list[$record['PROJECT_NO']][] = array
										(
											'id'=>$record['PRS_ID'],
											'name'=>$record['PRS_NAME'],
											'ratio'=>$record['PART_RATE'],
											'this_ratio'=>$record['THIS_WEEK_RATIO'],
											'this_content'=>$record['THIS_WEEK_CONTENT'],
											'next_content'=>$record['NEXT_WEEK_CONTENT']
										);

		//���� �� ��Ÿ����
		if($record['MEMO'] && !$memo) 
			$memo = nl2br(str_replace(" ", '&nbsp;',$record['MEMO']))."<br>";
	}

	//echo "<xmp>";
	//print_r($list);
	//echo "</xmp>";
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function weekSearch(val) {
		document.location.href = "./weekly_list_df1.php?week=" + val + "&team=<?=$team?>";
	}

	function teamSearch(val) {
		if (!val)
		{
			alert('���� ��ȸ�� ������ �����մϴ�.\n������ ������ �ּ���!');
			return;
		}
		
		document.location.href = "./weekly_list_df1.php?week=<?=$week?>&team=" + val;
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
			<? include INC_PATH."/weekly_menu.php"; ?>

			<div class="work_wrap clearfix">

				<div class="vacation_stats clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row">&nbsp;</th>
<!-- 							<th width="50%" scope="row">���� �ְ�����</th> -->
							<td>
								<?
									$week_titile = "[".$team."] ".substr($week,0,4)."�� ".substr($week,4,2)."�� ".substr($week,6,1)."���� �ְ�����";
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
										$selTeam = $selRecord['TEAM'];

										if ($selTeam == $team) 
										{ 
											$selected = " selected"; 
										}
										else
										{
											$selected = "";
										}
								?>
										<option value="<?=$selTeam?>" <?=$selected?>><?=$blank?><?=$selTeam?></option>
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


				<table class="vacation notable work1 work_stats" width="100%" style="margin-bottom:10px;">
					<caption>���� �ְ����� ���̺�</caption>
					<colgroup>
						<col width="*" />
						<col width="10%" />
						<col width="35%" />
						<col width="35%" />
					</colgroup>

					<thead>
						<tr>
							<th>������Ʈ</th>
							<th>������(��������)</th>
							<th>���� �������</th>
							<th>���� �������</th>
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

			$sql = "SELECT TITLE FROM DF_PROJECT $searchSQL";
			$rs = sqlsrv_query($dbConn,$sql);									
			$record = sqlsrv_fetch_array($rs);

			if($record)	$project_name = $record['TITLE'];
			else if($key1 == "DF0000_ETC") $project_name = "��Ÿ����";

			$name = "";
			$contents = "";
			$line_cnt = count($val1);
			$cnt = 1;

			foreach($val1 as $key2 => $val2)
			{
				if($cnt < $line_cnt) $border = "border-bottom:1px solid #e3e3e3;";
				else				 $border = "border-bottom:0px;";
				
				if($key1 == "DF0000_ETC") $name .= $val2['name']." (".$val2['this_ratio']."%)<br>";
				else					  $name .= $val2['name']." (".$val2['this_ratio']."%)<br>";

				$contents .= "<tr>";
				$contents .= "	<td width=50% style='text-align:left;vertical-align:top;$border'>".nl2br(str_replace(" ", '&nbsp;',$val2['this_content']))."</td>";
				$contents .= "	<td width=50% style='text-align:left;vertical-align:top;$border'>".nl2br(str_replace(" ", '&nbsp;',$val2['next_content']))."</td>";
				$contents .= "</tr>";

				$cnt++;
			}
			$contents = "<table style='width:100%;'>".$contents."</table>";
?>
							<!-- loop -->		
							<tr>
								<td><?=$project_name?></td>
								<td style="text-align:left;padding-left:20px;"><?=$name?> </td>
								<td colspan="2"><?=$contents?></td>
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

				<br>

				<table class="vacation notable work1 work_stats" width="100%" style="margin-bottom:10px;">
					<caption>���� �ְ����� ���̺�</caption>
					<colgroup>
						<col width="*" />
					</colgroup>

					<thead>
						<tr>
							<th>���� �� ��Ÿ����</th>
						</tr>
					</thead>

					<tbody>
						<tr>
							<td style='height:100px;text-align:left;vertical-align:top;'><?=$memo?></td>
						</tr>
					</tbody>
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
