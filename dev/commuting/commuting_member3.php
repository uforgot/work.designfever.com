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
		location.href="commuting_list.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 

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

	$sql = "SELECT COUNT(*) FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID = 7 OR PRS_ID = 211";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$sql = "SELECT 
				PRS_LOGIN, PRS_ID, PRS_NAME
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE
				PRF_ID = 7 OR PRS_ID = 211";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$team_login = $team_login. $record['PRS_LOGIN'] . "##";
		$team_id = $team_id. $record['PRS_ID'] . "##";
		$team_name = $team_name. $record['PRS_NAME'] . "##";
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
<!--
	function sSubmit(f)
	{
		f.target="_self";
		f.action="commuting_member3.php";
		f.page.value = "1";
		f.submit();
	}

	function eSubmit(f)
	{
		if(event.keyCode ==13)
			sSubmit(f);
	}

	//��������
	function preMonth()
	{
	<? if ($p_year == $startYear && $p_month == "01") { ?>
		alert("���� ó���Դϴ�.");
	<? } else { ?>
		var frm = document.form;
		
		frm.target="_self";
		frm.action="commuting_member3.php";
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
		frm.action="commuting_member3.php";
		frm.year.value = "<?=$NextYear?>";
		frm.month.value = "<?=$NextMonth?>";
		frm.page.value = "1";
		frm.submit();
	 }
	//���¼����˾� ����
	<? if ($prf_id == "4") { ?>
	function goModify(date,checktime1,id,checktime2){
		MM_openBrWindow('pop_modify.php?date='+date+'&checktime1='+checktime1+'&id='+id+'&checktime2='+checktime2,'','width=565 ,height=520,scrollbars=no, scrolling=no');
	}
	<? } ?>
-->
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="type" value="">
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

	for ($i=0; $i<sizeof($team_id_ex); $i++)
	{
		if ($team_id_ex[$i] != "")
		{

			$sql = "EXEC SP_COMMUTING_MEMBER_01 '$team_id_ex[$i]','$date'";
			$rs = sqlsrv_query($dbConn,$sql);

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
?>
						<tr class="line_up">
							<td rowspan="2" class="name"><?=$team_name_ex[$i]?></td>
							<td><span class="in">��</span></td>
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
?>
							<td valign="top">
<?
					if ($prf_id == "4")
					{
?>
							<a href="javascript:goModify('<?=$col_date_ex[$j]?>','<?=$col_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_checktime2_ex[$j]?>');">
<?
					}

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
						} else if ($col_gubun1_ex[$j] == "6") {		//�ܱ�
							echo "<font color='#ff8800'>�ܱ�</font><br><font color='#00aa00'>(". $prt_time .")</font>";
						} else if ($col_gubun2_ex[$j] == "6") {		//�ܱ�
							echo "<font color='#ff8800'>�ܱ�</font><br><font color='#00aa00'>". $prt_time ."</font>";
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
							<td><span class="out">��</span></td>
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
?>
							<td valign="top">
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

		// ���� �����(�ӽû���) /////////////
		//if($prs_login == "uforgot") {
		//	if($team_name_ex[$i] == "�Ѽ���" && $col_date_ex[$j] == "2013-10-14") {
		//		$prt_time = "-";
		//	}
		//}
		//////////////////////////////////////

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
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>