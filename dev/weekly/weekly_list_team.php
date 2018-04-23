<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/weekly_check.php";
?>

<?
	if ($prf_id != "2" && $prf_id != "3" && $prf_id != "4")
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("��/���� �̻� ���� ���� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	//������ ����Ʈ�ڽ� ����
	if (in_array($prs_position,$positionS_arr))
	{
		if (in_array($prs_team,array('CEO')))
		{
			$cur_team = "�濵������";
		}
		else
		{
			$cur_team = $prs_team;
		}
		$sel_view = 'Y';
	}
	else
	{
		$cur_team = $prs_team; //����Ʈ�ڽ� �⺻����
		$sel_view = 'N';	   //����Ʈ�ڽ� ���⿩��
	}
	
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y'); 
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : $cur_team; 

	$searchSQL = " WEEK_ORD LIKE '$year%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team'))))";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby . " END, PRS_NAME";

	$sql = "SELECT 
				COUNT(DISTINCT WEEK_ORD) 
			FROM 
				DF_WEEKLY WITH(NOLOCK) 
			WHERE". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 30;

	$sql = "SELECT 
				T.WEEK_ORD, T.WEEK_ORD_TOT, T.TITLE, T.COMPLETE_YN
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) AS ROWNUM, 
					WEEK_ORD, WEEK_ORD_TOT, TITLE, COMPLETE_YN
				FROM 
					DF_WEEKLY WITH(NOLOCK)
				WHERE". $searchSQL." 
				GROUP BY
					WEEK_ORD, WEEK_ORD_TOT, TITLE, COMPLETE_YN
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";		
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function yearSearch(val) {
		document.location.href = "./weekly_list_team.php?year=" + val + "&team=<?=$team?>";
	}

	function teamSearch(val) {
		if (!val)
		{
			alert('���� ��ȸ�� ������ �����մϴ�.\n������ ������ �ּ���!');
			return;
		}
		
		document.location.href = "./weekly_list_team.php?year=<?=$year?>&team=" + val;
	}

	function weeklyComplete(type,ord) {
		var frm = document.form;
		var str = '';

		if(type == 'complete') str = "�Ϸ�";
		else if(type == 'cancel') str = "���";

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("�� �ְ����� �ۼ��� " + str + " �Ͻðڽ��ϱ�")){
			frm.target = "hdnFrame";
			frm.action = 'weekly_write_act.php?type='+type+'&order='+ord; 
			frm.submit();
		}
	}
</script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="year" id="year" value="<?=$year?>">
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
								<select name="year" style="width:109px;" onchange="javascript:yearSearch(this.value);">
									<?
										for ($i=2014; $i<=date("Y"); $i++) 
										{
											if ($i == $year) 
											{ 
												$selected = " selected"; 
											}
											else
											{
												$selected = "";
											}
									?>
											<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
									<?
										}
									?>
								</select><span>��</span>

								<?
									if ($sel_view == 'Y') 
									{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<select name="team" style="width:200px;" onchange="javascript:teamSearch(this.value);">
								<?
									$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
									$selRs = sqlsrv_query($dbConn,$selSQL);

									while ($selRecord = sqlsrv_fetch_array($selRs))
									{
										$selStep = $selRecord['STEP'];
										$selTeam = $selRecord['TEAM'];
										
										if ($selStep == 2) {
											$selTeam2 = $selTeam;
										}
										else if ($selStep == 3) {
											$selTeam2 = "&nbsp;&nbsp;�� ". $selTeam;
										}
								?>
										<option value="<?=$selTeam?>"<? if ($team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
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
						<col width="5%" />
						<col width="15%" />
						<col width="*" />
						<col width="10%" />
						<col width="15%" />
					</colgroup>

					<thead>
						<tr>
							<th>����</th>
							<th>����</th>
							<th>����</th>
							<th>����</th>
							<th></th>
						</tr>
					</thead>

					<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="6" class="bold">�ش� ������ �����ϴ�.</td>
							</tr>
<?
	}
	else
	{
		while ($record = sqlsrv_fetch_array($rs))
		{
			$ord_tot = $record['WEEK_ORD_TOT'];
			$ord = $record['WEEK_ORD'];
			$comp_yn = $record['COMPLETE_YN'];
			
			//����� �̻�, ���� ���� ��ũ
			$title = "<a href='weekly_list_division.php?week=".$ord."&team=".$team."' target='_blank'>".$record['TITLE']."</a>";

			if ($comp_yn == 'Y')		$state = "�Ϸ�";
			else if ($comp_yn == 'N')	$state = "�ۼ���";

			//�ְ����� ����� ���� ����
			$searchSQL = " WHERE WEEK_ORD = '$ord' AND REG_DATE IS NOT NULL AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team'))))";
			//$searchSQL = " WHERE WEEK_ORD = '$ord' AND PRS_TEAM = '$team'";

			$per_sql = "SELECT 
							SEQNO, PRS_NAME 
					   FROM 
							DF_WEEKLY WITH(NOLOCK)
					   $searchSQL
					   $orderbycase";		
			$per_rs = sqlsrv_query($dbConn,$per_sql);

			$per_list = "";
			while ($per_record = sqlsrv_fetch_array($per_rs))
			{
				$per_seqno = $per_record['SEQNO'];	
				$per_name = $per_record['PRS_NAME'];	
				$per_list .= "<a href='weekly_write.php?type=modify&seqno=$per_seqno&win=new' target='_blank'>".$per_name."</a>&nbsp;&nbsp;";
			}
?>
							<!-- loop -->		
							<tr>
								<td><?=$ord_tot?></td>
								<td><?=$title?></td>
								<td style="text-align:left;"><?=$per_list?></td>
								<td><?=$state?></td>
								<td>
							<? 
								$cur_date = date("Y-m-d");
								$ndate = date("Y-m-d");
								$ydate = date("Y-m-d", strtotime("$cur_date -7 day"));

								$ninfo = getWeekInfo($ndate);
								$yinfo = getWeekInfo($ydate);

//								if (in_array($prs_id,$weekly_arr))
								if ($ord == $ninfo["cur_week"] || $ord == $yinfo["cur_week"]) 
								{ 
									if ($comp_yn == 'Y')
									{
							?> 
									<a href="javascript:weeklyComplete('cancel','<?=$ord?>');">[�� �ְ����� �Ϸ� ���]</a>
							<?		
									}
									else
									{
							?> 
									<a href="javascript:weeklyComplete('complete','<?=$ord?>');">[�� �ְ����� �Ϸ�]</a>
							<?		
									}
								}
							?>
								</td>
							</tr>
							<!-- loop -->		
<?
			$i--;
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

				<div class="page_num">
				<?=getPaging($total_cnt,$page,$per_page);?>
				</div>	

			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
