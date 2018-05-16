<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if (!in_array($prs_id,$positionC_arr) && $prf_id != "4")
	{ 
?>
	<script type="text/javascript">
		alert("�ش��������� ��/���� �̻� Ȯ�� �����մϴ�.");
		location.href="/";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 
	$p_type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "team"; 
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null; 
	$p_sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : null;

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }
	
	$Start = $p_year.$p_month."01";
	$Pre = date("Ymd",strtotime ("-1 month", strtotime($Start)));
	$Next = date("Ymd",strtotime ("+1 month", strtotime($Start)));

	$PreYear = substr($Pre,0,4);
	$PreMonth = substr($Pre,4,2);

	$NextYear = substr($Next,0,4);
	$NextMonth = substr($Next,4,2);

	$date = $p_year."-".$p_month;
	$prev_date = date("Y-m", strtotime($Pre));

	$teamSQL = "";
	if ($prf_id == "4")	//������, �ӿ�
	{
		if ($p_type == "person")
		{
			$teamSQL = " AND A.PRS_NAME = '$p_name'";
		}
		else if ($p_type == "team" && $p_team != "")
		{
			$teamSQL = " AND A.PRS_TEAM = '$p_team'";
		}
	}
	else				//��.����
	{
		$teamSQL = " AND A.PRS_TEAM = '$prs_team'";
	}
	
	$statusSQL = "";
	if ($p_sort == "request-ing")
	{
		$statusSQL = " AND B.STATUS = 'ING'";
	}

	$date_arr = "";
	$day_arr = "";
	$sql = "SELECT DATE, DAY FROM HOLIDAY WITH(NOLOCK) WHERE DATE LIKE '". str_replace('-','',$date) ."%'";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$date_arr = $date_arr . $record['DATE'] . "##";
		$day_arr = $day_arr . $record['DAY'] . "##";
	}

	$team_login = "";
	$team_id = "";
	$team_name = "";

	$sql = "SELECT 
				A.PRS_LOGIN, A.PRS_ID, A.PRS_NAME, COUNT(B.PRS_ID)
			FROM 
				DF_PERSON A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_REQUEST B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
			WHERE 
				A.PRF_ID IN (1,2,3,4,7)". $teamSQL ." AND A.PRS_ID NOT IN (15,22,24,87,148,102) AND B.DATE LIKE '".$date."%'". $statusSQL ."
			GROUP BY 
				A.PRS_ID, A.PRS_LOGIN, A.PRS_NAME";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$team_login = $team_login. $record['PRS_LOGIN'] . "##";
		$team_id = $team_id. $record['PRS_ID'] . "##";
		$team_name = $team_name. $record['PRS_NAME'] . "##";
		$team_edit_cnt = $team_edit_cnt. $record['EDIT_LOG_CNT'] . "##";
	}

	// ������ ���¼�����û(��ó��) �Ǽ�
	$sql = "EXEC SP_COMMUTING_EDIT_TOTAL_01 '$prev_date'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$prev_ing_tot = $record['PREV_ING_TOT'];

	//���� ���¼��� ���� ����
	if (1) // ���� ��/���� ��� ���� ����
	{
		$edit_auth = true;
	} 
	else
	{
		$edit_auth = false;
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
<!--
	function sSubmit(f)
	{
		f.target="_self";
		f.action="commuting_member_request.php";
		f.page.value = "1";
		f.submit();
	}

	function eSubmit(f)
	{
		if(event.keyCode ==13)
			sSubmit(f);
	}

	function transSort(val) 
	{
		var frm = document.form;
	
		if(val=="request" || val=="request-ing") {
			frm.action="commuting_member_request.php";
		} else {
			frm.action="commuting_member.php";
		}

		frm.target="_self";
		frm.year.value = "<?=$p_year?>";
		frm.month.value = "<?=$p_month?>";
		frm.page.value = "1";
		frm.submit();
	}

	//��������
	function preMonth()
	{
	<? if ($p_year == $startYear && $p_month == "01") { ?>
		alert("���� ó���Դϴ�.");
	<? } else { ?>
		var frm = document.form;
		
		frm.target="_self";
		frm.action="commuting_member_request.php";
		frm.year.value = "<?=$PreYear?>";
		frm.month.value = "<?=$PreMonth?>";
		frm.page.value = "1";
		frm.submit();
	<? } ?>
	}

	//����������
	function nextMonth()
	{
		var frm = document.form;

		frm.target="_self";
		frm.action="commuting_member_request.php";
		frm.year.value = "<?=$NextYear?>";
		frm.month.value = "<?=$NextMonth?>";
		frm.page.value = "1";
		frm.submit();
	 }
	//���¼����˾� ����(������)
	<? if ($prf_id == "4") { ?>
	function goModify(date,checktime1,id,checktime2){
		MM_openBrWindow('pop_modify.php?date='+date+'&checktime1='+checktime1+'&id='+id+'&checktime2='+checktime2+'&sort=<?=$p_sort?>','','width=565 ,height=555,scrollbars=no, scrolling=no');
	}
	<? } ?>

	//���¼����˾� ����(��/����)
	function goModify2(date,checktime1,id,checktime2){
		MM_openBrWindow('pop_modify2.php?date='+date+'&checktime1='+checktime1+'&id='+id+'&checktime2='+checktime2+'&sort=<?=$p_sort?>','','width=565 ,height=645,scrollbars=no, scrolling=no');
	}

	function excel_download()
	{
		var frm = document.form;

		frm.target = "hdnFrame";
		frm.action = "excel_member.php";
		frm.submit();
	}
-->
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/commuting_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="cal_top clearfix">
					<a href="javascript:preMonth();" class="prev"><img src="../img/btn_prev.gif" alt="��������" /></a>
					<div>
					<select name="year" value="<?=$p_year?>" onchange='sSubmit(this.form)'>
					<?
						for ($i=$startYear; $i<=($nowYear+1); $i++) 
						{
							if ($i == $p_year) 
							{ 
								$selected = " selected"; 
							}
							else
							{
								$selected = "";
							}

							echo "<option value='".$i."'".$selected.">".$i."</option>";
						}
					?>
					</select>
					<span>��</span></div>
					<div>
					<select name="month" value="<?=$p_month?>" onchange='sSubmit(this.form)'>
					<?
						for ($i=1; $i<=12; $i++) 
						{
							if (strlen($i) == "1") 
							{
								$j = "0".$i;
							}
							else
							{
								$j = $i;
							}

							if ($j == $p_month)
							{
								$selected = " selected";
							}
							else
							{
								$selected = "";
							}

							echo "<option value='".$j."'".$selected.">".$i."</option>";
						}
					?>
					</select>
					<span>��</span></div>
					<a href="javascript:nextMonth();" class="next"><img src="../img/btn_next.gif" alt="����������" /></a>
				</div>
				<div class="work_team_info clearfix">
					<p><span class="in">��</span> ���</p><p><span class="out">��</span> ���</p><br><br>
					<? if ($prf_id == "4") { ?>
					<table class="notable" width="100%">
						<tr>
							<td>
								<select name="type" onChange="sSubmit(this.form)">
									<option value="team"<? if ($p_type == "team"){ echo " selected"; } ?>>�μ���</option>
									<option value="person"<? if ($p_type == "person"){ echo " selected"; } ?>>������</option>
								</select>
								<? if ($p_type == "team") { ?>
								<select name="team" onChange="sSubmit(this.form)">			
									<option value=""<? if ($p_team == ""){ echo " selected"; } ?>>������</option>
							<?
								$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
								$selRs = sqlsrv_query($dbConn,$selSQL);

								while ($selRecord = sqlsrv_fetch_array($selRs))
								{
									$selStep = $selRecord['STEP'];
									$selTeam = $selRecord['TEAM'];

									$blank = "";
									for ($i=3;$i<=$selStep;$i++)
									{
										$blank .= "&nbsp;&nbsp;&nbsp;";
									}
							?>
									<option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$blank?><?=$selTeam?></option>
							<?
								}
							?>
								</select>
								<? } else if ($p_type == "person") { ?>
									<input type="text" name="name" style="width:200px;" value="<?=$p_name?>" onkeypress="eSubmit(this.form);">
									<a href="javascript:sSubmit(this.form);"><img src="../img/btn_search.gif" alt="�˻�" /></a>
								<? } ?>

								<select name="sort" onChange="transSort(this.value)">
									<option value="">��ü</option>
									<option value="request" <?if($p_sort=="request"){echo "selected";}?>>���¼�����û</option>
									<option value="request-ing" <?if($p_sort=="request-ing"){echo "selected";}?>>���¼�����û(��ó��)</option>
								</select>

								<font style="color:#ef0000">�� ���� ���¼��� ��ó�� : <b><?=number_format($prev_ing_tot)?></b> ��</font>
							</td>
							<td align="right">
								<!--<a href="javascript:excel_download();"><img src="../img/btn_excell.gif" alt="�����ٿ�ε�" /></a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
							</td>
						</tr>
					</table>
					<br>
					<? } else { ?>
						<input type="hidden" name="type" value="">
					<? } ?>
				</div>
				<table class="notable work1 work_team"  width="100%">
					<summary></summary>
					<thead>
						<tr class="day">
							<th class="first"></th>
							<th></th>
					<?
						$date_arr_ex = explode("##",$date_arr);
						$day_arr_ex = explode("##",$day_arr);

						for ($i=0; $i<sizeof($date_arr_ex); $i++)
						{
							echo "<th>". substr($date_arr_ex[$i],6,2) ."</th>";
						}
					?>
						</tr>
						<tr class="week">
							<th class="first"></td>
							<th></th>
					<?
						for ($i=0; $i<sizeof($date_arr_ex); $i++)
						{
							if ($day_arr_ex[$i] == "SUN")
							{
							echo "<th class='sun'><font color='#ef0000'>". $day_arr_ex[$i] ."</font></th>";
							}
							else if ($day_arr_ex[$i] == "SAT") 
							{
							echo "<th class='sat'>". $day_arr_ex[$i] ."</th>";
							}
							else
							{
							echo "<th>". $day_arr_ex[$i] ."</th>";
							}
						}
					?>
						</tr>
					</thead>
					<tbody>
<?
	$team_login_ex = explode("##",$team_login);
	$team_id_ex = explode("##",$team_id);
	$team_name_ex = explode("##",$team_name);
	$team_edit_cnt_ex = explode("##",$team_edit_cnt);

	for ($i=0; $i<sizeof($team_id_ex); $i++)
	{
		if ($team_id_ex[$i] != "")
		{

			$sql = "EXEC SP_COMMUTING_MEMBER_03 '$team_id_ex[$i]','$date'";
			$rs = sqlsrv_query($dbConn,$sql);

			$col_edit_status_arr = "";
			$col_edit_checktime1_arr = "";
			$col_edit_checktime2_arr = "";
			$col_edit_bst_flag_arr = "";

			$col_date_arr = "";
			$col_datekind_arr = "";
			$col_gubun1_arr = "";
			$col_gubun2_arr = "";
			$col_checktime1_arr = "";
			$col_checktime2_arr = "";
			$col_totaltime_arr = "";
			$col_overtime_arr = "";
			$col_undertime_arr = "";
			$col_pay1_arr = "";
			$col_pay2_arr = "";
			$col_pay3_arr = "";
			$col_pay4_arr = "";
			$col_pay5_arr = "";
			$col_pay6_arr = "";
			$col_out_arr = "";
			$col_yesterday_overtime_arr = "";
			$col_yesterday_datekind_arr = "";

			while ($record = sqlsrv_fetch_array($rs))
			{
				// ���¼��� ��û ����
				$col_edit_status = $record['EDIT_STATUS'];
				$col_edit_status_arr = $col_edit_status_arr . $col_edit_status ."##";
				$col_edit_checktime1 = $record['EDIT_CHECKTIME1'];
				$col_edit_checktime1_arr = $col_edit_checktime1_arr . $col_edit_checktime1 ."##";
				$col_edit_checktime2 = $record['EDIT_CHECKTIME2'];
				$col_edit_checktime2_arr = $col_edit_checktime2_arr . $col_edit_checktime2 ."##";
				$col_edit_bst_flag = $record['EDIT_BST_FLAG'];
				$col_edit_bst_flag_arr = $col_edit_bst_flag_arr . $col_edit_bst_flag ."##";

				$col_date = $record['DATE'];
				$col_datekind = $record['DATEKIND'];
				$col_gubun1 = $record['GUBUN1'];
				$col_gubun2 = $record['GUBUN2'];
				$col_checktime1 = $record['CHECKTIME1'];
				$col_checktime2 = $record['CHECKTIME2'];
				$col_totaltime = $record['TOTALTIME'];
				$col_overtime = $record['OVERTIME'];
				$col_undertime = $record['UNDERTIME'];
				$col_pay1 = $record['PAY1'];
				$col_pay2 = $record['PAY2'];
				$col_pay3 = $record['PAY3'];
				$col_pay4 = $record['PAY4'];
				$col_pay5 = $record['PAY5'];
				$col_pay6 = $record['PAY6'];
				$col_out = $record['OUT_CHK'];
				$col_yesterday_overtime = $record['YESTERDAY_OVERTIME'];
				$col_yesterday_datekind = $record['YESTERDAY_DATEKIND'];

				$col_date_arr = $col_date_arr . substr($col_date,0,4) ."-". substr($col_date,4,2) ."-". substr($col_date,6,2) ."##";
				$col_datekind_arr = $col_datekind_arr . $col_datekind ."##";
				$col_gubun1_arr = $col_gubun1_arr . $col_gubun1 ."##";
				$col_gubun2_arr = $col_gubun2_arr . $col_gubun2 ."##";
				$col_checktime1_arr = $col_checktime1_arr . $col_checktime1 ."##";
				$col_checktime2_arr = $col_checktime2_arr . $col_checktime2 ."##";
				$col_totaltime_arr = $col_totaltime_arr . $col_totaltime . "##";
				$col_overtime_arr = $col_overtime_arr . $col_overtime ."##";
				$col_undertime_arr = $col_undertime_arr . $col_undertime ."##";
				$col_pay1_arr = $col_pay1_arr . $col_pay1 ."##";
				$col_pay2_arr = $col_pay2_arr . $col_pay2 ."##";
				$col_pay3_arr = $col_pay3_arr . $col_pay3 ."##";
				$col_pay4_arr = $col_pay4_arr . $col_pay4 ."##";
				$col_pay5_arr = $col_pay5_arr . $col_pay5 ."##";
				$col_pay6_arr = $col_pay6_arr . $col_pay6 ."##";
				$col_out_arr = $col_out_arr . $col_out ."##";
				$col_yesterday_overtime_arr = $col_yesterday_overtime_arr . $col_yesterday_overtime ."##";
				$col_yesterday_datekind_arr = $col_yesterday_datekind_arr . $col_yesterday_datekind ."##";
			}

			// ���¼��� ��û ����
			$col_edit_status_ex = explode("##",$col_edit_status_arr); 
			$col_edit_checktime1_ex = explode("##",$col_edit_checktime1_arr); 
			$col_edit_checktime2_ex = explode("##",$col_edit_checktime2_arr); 
			$col_edit_bst_flag_ex = explode("##",$col_edit_bst_flag_arr); 

			$col_date_ex = explode("##",$col_date_arr);
			$col_datekind_ex = explode("##",$col_datekind_arr);
			$col_gubun1_ex = explode("##",$col_gubun1_arr);
			$col_gubun2_ex = explode("##",$col_gubun2_arr);
			$col_checktime1_ex = explode("##",$col_checktime1_arr);
			$col_checktime2_ex = explode("##",$col_checktime2_arr);
			$col_totaltime_ex = explode("##",$col_totaltime_arr);
			$col_overtime_ex = explode("##",$col_overtime_arr);
			$col_undertime_ex = explode("##",$col_undertime_arr);
			$col_pay1_ex = explode("##",$col_pay1_arr);
			$col_pay2_ex = explode("##",$col_pay2_arr);
			$col_pay3_ex = explode("##",$col_pay3_arr);
			$col_pay4_ex = explode("##",$col_pay4_arr);
			$col_pay5_ex = explode("##",$col_pay5_arr);
			$col_pay6_ex = explode("##",$col_pay6_arr);
			$col_out_ex = explode("##",$col_out_arr);
			$col_yesterday_overtime_ex = explode("##",$col_yesterday_overtime_arr);
			$col_yesterday_datekind_ex = explode("##",$col_yesterday_datekind_arr);

			// ���¼��� Ƚ�� 
			$edit_cnt_box = "";
			if ($edit_auth && $team_edit_cnt_ex[$i] > 0)
			{
				$edit_cnt_box = "<div style='background-color:#ff0000; width:100%; position:absolute; bottom:0px'><font color='#ffffff'>���¼���<br>".number_format($team_edit_cnt_ex[$i])." ȸ</font></div>";
			}
?>
						<tr class="line_up">
							<td rowspan="2" class="name" style="position:relative;"><?=$team_name_ex[$i]?><?=$edit_cnt_box?></td>
							<td height="70"><span class="in">��</span></td>
<?
			for ($j=0; $j<sizeof($col_date_ex); $j++)
			{
				if ($col_date_ex[$j] != "")
				{
					if ($col_checktime1_ex[$j] == "")
					{
						$prt_time = "-";
					}
					else
					{
						$prt_time = substr($col_checktime1_ex[$j],8,2) .":". substr($col_checktime1_ex[$j],10,2);
					}

					// ���¼��� ��û ����
					$day_color = "";
					$edit_flag = "";
					if ($edit_auth && $col_edit_checktime1_ex[$j]) 
					{
						if ($col_edit_status_ex[$j] == "ING")
						{
							// �濵������ Ȯ���ʿ� ��û��
							if ($col_edit_bst_flag_ex[$j] == "N") $day_color = " background-color:#0000cc";
							else $day_color = " background-color:#FF0000";
						}
						else if ($col_edit_status_ex[$j] == "CANCEL")
						{
							$day_color = " background-color:#FF0000";
						}
						else if ($col_edit_status_ex[$j] == "OK")
						{
							//$day_color = " background-color:#c0c0c0";
							$edit_flag = "<span style='position:absolute; top:0px; left:0px;'><img src='../img/icon_left_top.gif' width='15'></span>";
						}
					} 
?>
							<td valign="top" style="position:relative; <?=$day_color?>"><?=$edit_flag?>
<?
					if ($prf_id == "4")
					{
?>
						<a href="javascript:goModify('<?=$col_date_ex[$j]?>','<?=$col_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_checktime2_ex[$j]?>');">
<?
					}
					// ���¼��� ��û
					if ($edit_auth && $col_edit_status_ex[$j] == "ING" && $col_edit_checktime1_ex[$j]) 
					{
						$prt_time = substr($col_edit_checktime1_ex[$j],8,2) .":". substr($col_edit_checktime1_ex[$j],10,2);
?>
						<a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>��û</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
<?							
					}						
					// ���¼��� �ݷ�
					else if ($edit_auth && $col_edit_status_ex[$j] == "CANCEL" && $col_edit_checktime1_ex[$j]) 
					{
							$prt_time = substr($col_edit_checktime1_ex[$j],8,2) .":". substr($col_edit_checktime1_ex[$j],10,2);
?>
						<a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>�ݷ�</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
<?
					}
					else
					{
						// ���üũ�� ������ ���� ���
						if ($prt_time == "-")
						{
							echo $prt_time;
						}
						else
						{

							if ($col_gubun1_ex[$j] == "10" || $col_gubun1_ex[$j] == "16" || $col_gubun1_ex[$j] == "17" || $col_gubun1_ex[$j] == "18") {			//�ް�/������Ʈ�ް�/���������ް�/�����ް�
								echo "<font color='#ff8800'>�ް�</font>";
							} else if ($col_gubun1_ex[$j] == "11") {	//����
								echo "<font color='#ff8800'>����</font>";
							} else if ($col_gubun1_ex[$j] == "12") {	//������
								echo "<font color='#ff8800'>������</font>";
							} else if ($col_gubun1_ex[$j] == "13" || $col_gubun1_ex[$j] == "20" || $col_gubun1_ex[$j] == "21") {	//��Ÿ/����ް�/��������
								echo "<font color='#ff8800'>��Ÿ</font>";
							} else if ($col_gubun1_ex[$j] == "14") {	//���
								echo "<font color='#ff8800'>���</font>";
							} else if ($col_gubun1_ex[$j] == "15") {	//����
								echo "<font color='#ff8800'>����</font>";
							} else if ($col_gubun1_ex[$j] == "19") {	//����
								echo "<font color='#ff8800'>����</font>";
	//						} else if ($col_gubun1_ex[$j] == "8") {		//����
	//							echo "<font color='#ff8800'>����</font>";
	//						} else if ($col_gubun1_ex[$j] == "7") {		//����
	//							echo "<font color='#ef0000'>". $prt_time ."</font>";
							} else if ($col_gubun1_ex[$j] == "4" || $col_gubun1_ex[$j] == "8") {		//������Ʈ ����/���� - ��������ð��� ���� ��� ����
								echo "<font color='#ff8800'>����</font><br><font color='#ef0000'>(". $prt_time .")</font>";
							} else if ($col_gubun2_ex[$j] == "5" || $col_gubun2_ex[$j] == "9") {		//������Ʈ ����/���� - ��������ð��� ���� ��� ����
								echo "<font color='#ff8800'>����</font><br><font color='#00aa00'>". $prt_time ."</font>";
							} else {
								if (substr($prt_time,0,2) < "08")		//��������ð��� ���� ���
								{
									echo "<font color='#00aa00'>08:00</font><br><font color='#0000cc'>(". $prt_time .")</a>";
								}
								else
								{
									echo "<font color='#00aa00'>". $prt_time ."</font>";
								}
							}
						}
					}

					if ($prf_id == "4")
					{
?>
							</a>
<?
					}
?>
							</td>
<?
				}
			}
?>
						</tr>
						<tr class="line_down">
							<td height="70"><span class="out">��</span></td>
<?
			for ($j=0; $j<sizeof($col_date_ex); $j++)
			{
				if ($col_date_ex[$j] != "")
				{

					if ($col_checktime2_ex[$j] == "")
					{
						$prt_time = "-";
					}
					else
					{
						$prt_time = substr($col_checktime2_ex[$j],8,2) .":". substr($col_checktime2_ex[$j],10,2);
					}

					// ���¼��� ��û ����
					$day_color = "";
					if ($edit_auth && $col_edit_checktime2_ex[$j]) 
					{
						if ($col_edit_status_ex[$j] == "ING")
						{
							// �濵������ Ȯ���ʿ� ��û��
							if ($col_edit_bst_flag_ex[$j] == "N") $day_color = " background-color:#0000cc";
							else $day_color = " background-color:#FF0000";
						}
						else if ($col_edit_status_ex[$j] == "CANCEL")
						{
							$day_color = " background-color:#FF0000";
						}
						else if ($col_edit_status_ex[$j] == "OK")
						{
							//$day_color = " background-color:#c0c0c0";
						}
					} 
?>
							<td valign="top" style="<?=$day_color?>">
<?
					if ($col_datekind_ex[$j] == "BIZ" && ($col_gubun1_ex[$j] == "1" || $col_gubun1_ex[$j] == "6" || $col_gubun1_ex[$j] == "4" || $col_gubun2_ex[$j] == "5" || $col_gubun1_ex[$j] == "8" || $col_gubun2_ex[$j] == "9") && ($col_gubun2_ex[$j] == "2" || $col_gubun2_ex[$j] == "3" || $col_gubun2_ex[$j] == "6")) {

						$shift_time = "0";
						$shift_minute = "0";

						if ($col_yesterday_datekind_ex[$j] == "BIZ") {
							if ($col_yesterday_overtime_ex[$j] >= "0700") {
								$shift_time = "03"; 
								$shift_minute = "00";
							}
							else if ($col_yesterday_overtime_ex[$j] >= "0600") {
								$shift_time = "02";
								$shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
							}
							else if ($col_yesterday_overtime_ex[$j] >= "0500") {
								$shift_time = "01";
								$shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
							}
							else if ($col_yesterday_overtime_ex[$j] >= "0400") {
								$shift_time = "00";
								$shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
							}
						} 
						else {
							if ($col_yesterday_overtime_ex[$j] >= "0900") {
								$shift_time = "03"; 
								$shift_minute = "00";
							}
							else if ($col_yesterday_overtime_ex[$j] >= "0800") {
								$shift_time = "02";
								$shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
							}
							else if ($col_yesterday_overtime_ex[$j] >= "0700") {
								$shift_time = "01";
								$shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
							}
							else if ($col_yesterday_overtime_ex[$j] >= "0600") {
								$shift_time = "00";
								$shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
							}
						}

						if (strlen($shift_time) == 1) { $shift_time = "0". $shift_time; }
						if (strlen($shift_minute) == 1) { $shift_minute = "0". $shift_minute; }
						
						if ($shift_time >= "01" || $shift_minute >= "01") { echo "<span style='font-size:11px; color:#666666;'>". $shift_time .":". $shift_minute ."</span><br>"; }
						else { echo "<br>"; }
					}
					else
					{
						echo "<br>";
					}

					if ($prf_id == "4")
					{
?>
						<a href="javascript:goModify('<?=$col_date_ex[$j]?>','<?=$col_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_checktime2_ex[$j]?>');">
<?
					}

					// ���¼��� ��û
					if ($edit_auth && $col_edit_status_ex[$j] == "ING" && $col_edit_checktime2_ex[$j]) 
					{
						$prt_time = substr($col_edit_checktime2_ex[$j],8,2) .":". substr($col_edit_checktime2_ex[$j],10,2);
?>
						<a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>��û</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
<?
					}
					// ���¼��� �ݷ�
					else if ($edit_auth && $col_edit_status_ex[$j] == "CANCEL" && $col_edit_checktime2_ex[$j]) 
					{
						$prt_time = substr($col_edit_checktime2_ex[$j],8,2) .":". substr($col_edit_checktime2_ex[$j],10,2);
?>
						<a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>�ݷ�</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
<?
					}
					else
					{	
						// ���üũ�� ������ ���� ���
						if ($prt_time == "-")
						{
							echo $prt_time;
						}
						else
						{
							if ($col_gubun2_ex[$j] == "10" || $col_gubun2_ex[$j] == "16" || $col_gubun2_ex[$j] == "17" || $col_gubun2_ex[$j] == "18") {			//�ް�/������Ʈ�ް�/���������ް�/�����ް�
								echo "-";
							} else if ($col_gubun2_ex[$j] == "11") {	//����
								echo "-";
							} else if ($col_gubun2_ex[$j] == "12") {	//������
								echo "-";
							} else if ($col_gubun2_ex[$j] == "13" || $col_gubun2_ex[$j] == "20" || $col_gubun2_ex[$j] == "21") {	//��Ÿ/����ް�/��������
								echo "-";
							} else if ($col_gubun2_ex[$j] == "14") {	//���
								echo "-";
							} else if ($col_gubun2_ex[$j] == "15") {	//����
								echo "-";
							} else if ($col_gubun1_ex[$j] == "19") {	//����
								echo "-";
							} else {
								echo $prt_time;

								if ($col_undertime_ex[$j] > "0000")
								{
									echo "<br><font color='#ef0000'>(". substr($col_undertime_ex[$j],0,2) .":". substr($col_undertime_ex[$j],2,2) .")</font>";
								}
								else
								{
									if ($col_overtime_ex[$j] > "0000")
									{
										echo "<br><font color='#0000cc'>(". substr($col_overtime_ex[$j],0,2) .":". substr($col_overtime_ex[$j],2,2) .")</font>";
									}
									else
									{
										echo "<br>(00:00)";
									}
								}

							}
						}
					}

					if ($prf_id == "4") 
					{
?>
							</a>
<?
						$alarm = "";
						$out = "�İ�";

						if ($col_pay1_ex[$j] == "Y") { $alarm .= "��."; }
						if ($col_pay2_ex[$j] == "Y") { $alarm .= "��."; }
						if ($col_pay3_ex[$j] == "Y") { $alarm .= "��."; }
						if ($col_pay4_ex[$j] == "Y") { $alarm .= "��"; }
						if ($col_pay5_ex[$j] == "Y") { $out .= "(��)"; }
						if ($col_pay6_ex[$j] == "Y") { $out .= "(��)"; }

						echo "<br><span style='font-size:11px; color:#666666;'>". $alarm ."</span>";
						if ($col_out_ex[$j] == "Y") { echo "<br><span style='font-size:11px; color:#666666;'>". $out ."</span>"; }
						
						if ($col_gubun1_ex[$j] == "6" || $col_gubun2_ex[$j] == "6") {		//�ܱ�
							echo "<br><span style='font-size:11px; color:#ff8800';'>�ܱ�</span>";
						}
					}
?>
							</td>
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
				</table>
				<div class="page_num">
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>