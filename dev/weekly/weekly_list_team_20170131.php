<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
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
		$sel_view = 'Y';

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
		$sel_view = 'Y';
		$sel_sql = "SELECT 
						STEP, TEAM 
					FROM 
						DF_TEAM_CODE WITH(NOLOCK) 
					WHERE 
						SEQNO NOT IN(2,3,4,5) 
					ORDER BY SORT";
	}
	
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y'); 
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : $cur_team; 

	$searchSQL = " WEEK_ORD LIKE '$year%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM = '$team')";

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
				T.WEEK_ORD, T.WEEK_ORD_TOT, T.TITLE, T.PRS_TEAM, T.COMPLETE_YN
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) AS ROWNUM, 
					WEEK_ORD, WEEK_ORD_TOT, TITLE, PRS_TEAM, COMPLETE_YN
				FROM 
					DF_WEEKLY WITH(NOLOCK)
				WHERE". $searchSQL." 
				GROUP BY
					WEEK_ORD, WEEK_ORD_TOT, TITLE, PRS_TEAM, COMPLETE_YN
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
									$selSQL = $sel_sql;
									$selRs = sqlsrv_query($dbConn,$selSQL);

									while ($selRecord = sqlsrv_fetch_array($selRs))
									{
										$selStep = $selRecord['STEP'];
										$selTeam = $selRecord['TEAM'];

										if ($selStep == 3 || ($selTeam == '�濵������' || $selTeam == 'ȫ����' || $selTeam == 'brand experience team'))
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


				<table class="vacation notable work1 work_stats" width="100%" style="margin-bottom:10px;">
					<caption>���� �ְ����� ���̺�</caption>
					<colgroup>
						<col width="5%" />
						<col width="30%" />
						<col width="*" />
						<col width="10%" />
					</colgroup>

					<thead>
						<tr>
							<th>����</th>
							<th>����</th>
							<th>����</th>
							<th>����</th>
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
			if (in_array($prs_position,$positionA_arr))
			{
				$title = "<a href='weekly_list_division.php?week=".$ord."&team=".$team."' target='_blank'>".$record['TITLE']."</a>";
			} else {
				$title = "<a href='weekly_list_division.php?week=".$ord."&team=".$team."' target='_blank'>".$record['TITLE']."</a>";
				//$title = $record['TITLE'];
			}

			if ($comp_yn == 'Y')		$state = "�Ϸ�";
			else if ($comp_yn == 'N')	$state = "�ۼ���";

			//�ְ����� ����� ���� ����
			$searchSQL = " WHERE WEEK_ORD = '$ord' AND REG_DATE IS NOT NULL AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM = '$team')";
			//$searchSQL = " WHERE WEEK_ORD = '$ord' AND PRS_TEAM = '$team'";

			$orderbycase = "ORDER BY CASE WHEN PRS_POSITION='��ǥ' THEN 1 WHEN PRS_POSITION='�̻�' THEN 2 WHEN PRS_POSITION='����' THEN 3 WHEN PRS_POSITION='����' THEN 4 WHEN PRS_POSITION='����' THEN 5 WHEN PRS_POSITION='����' THEN 6 WHEN PRS_POSITION='����' THEN 7 WHEN PRS_POSITION='����' THEN 8 WHEN PRS_POSITION='å��' THEN 9 WHEN PRS_POSITION='�븮' THEN 10 WHEN PRS_POSITION='����' THEN 11 WHEN PRS_POSITION='����' THEN 12 WHEN PRS_POSITION='���' THEN 13 WHEN PRS_POSITION='����' THEN 14 END, PRS_NAME";

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
