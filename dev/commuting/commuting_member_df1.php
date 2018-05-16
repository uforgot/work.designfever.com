<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prs_id != "79" && $prs_id != "102") 
	{ 
?>
	<script type="text/javascript">
		alert("해당페이지는 관리자만 확인 가능합니다.");
		location.href="commuting_list.php";
	</script>
<?
		exit;
	}

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 
	$p_type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "team"; 
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null; 

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

	$teamSQL = "";
	if ($p_team != "")
	{
		$teamSQL = " AND PRS_TEAM = '$p_team'";
	}
	else				//팀.실장
	{
		$teamSQL = " AND PRS_TEAM IN ('커뮤니케이션전략기획1실','디자인1실','모션그래픽스1실','비주얼 인터랙션 디벨롭먼트 1실')";
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

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$sql = "SELECT 
				PRS_LOGIN, PRS_ID, PRS_NAME
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE
				PRF_ID IN (1,2,3,4,7)
			". $teamSQL ."
			ORDER BY 
				CASE 
					WHEN PRS_TEAM = '커뮤니케이션전략기획1실' THEN 1 
					WHEN PRS_TEAM = '디자인1실' THEN 2
					WHEN PRS_TEAM = '모션그래픽스1실' THEN 3
					WHEN PRS_TEAM = '비주얼 인터랙션 디벨롭먼트 1실' THEN 4 END, CASE ". $orderby. " END, PRS_JOIN, PRS_NAME";

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
	function sSubmit(f)
	{
		f.target="_self";
		f.action="commuting_member_df1.php";
		f.page.value = "1";
		f.submit();
	}

	function eSubmit(f)
	{
		if(event.keyCode ==13)
			sSubmit(f);
	}

	//전월보기
	function preMonth()
	{
	<? if ($p_year == $startYear && $p_month == "01") { ?>
		alert("제일 처음입니다.");
	<? } else { ?>
		var frm = document.form;
		
		frm.target="_self";
		frm.action="commuting_member_df1.php";
		frm.year.value = "<?=$PreYear?>";
		frm.month.value = "<?=$PreMonth?>";
		frm.page.value = "1";
		frm.submit();
	<? } ?>
	}

	//다음월보기
	function nextMonth()
	{
		var frm = document.form;

		frm.target="_self";
		frm.action="commuting_member_df1.php";
		frm.year.value = "<?=$NextYear?>";
		frm.month.value = "<?=$NextMonth?>";
		frm.page.value = "1";
		frm.submit();
	 }

	function excel_download()
	{
		var frm = document.form;

		frm.target = "hdnFrame";
		frm.action = "excel_member_df1.php";
		frm.submit();
	}
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
					<a href="javascript:preMonth();" class="prev"><img src="../img/btn_prev.gif" alt="전월보기" /></a>
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
					<span>년</span></div>
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
					<span>월</span></div>
					<a href="javascript:nextMonth();" class="next"><img src="../img/btn_next.gif" alt="다음월보기" /></a>
				</div>
				<div class="work_team_info clearfix">
					<p><span class="in">출</span> 출근</p><p><span class="out">퇴</span> 퇴근</p><br><br>
					<table class="notable" width="100%">
						<tr>
							<td>
								<select name="team" onChange="sSubmit(this.form)">			
									<option value=""<? if ($p_team == ""){ echo " selected"; } ?>>전직원</option>
							<?
								$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' AND TEAM LIKE '%1실%' ORDER BY SORT";
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
							</td>
							<td align="right">
								<a href="javascript:excel_download();"><img src="../img/btn_excell.gif" alt="엑셀다운로드" /></a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</td>
						</tr>
					</table>
					<br>
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
							<td><span class="in">출</span></td>
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
					if ($prt_time == "-")
					{
						echo $prt_time;
					}
					else
					{
						if ($col_gubun1_ex[$j] == "10" || $col_gubun1_ex[$j] == "16" || $col_gubun1_ex[$j] == "17" || $col_gubun1_ex[$j] == "18") {			//휴가/프로젝트휴가/리프레시휴가/무급휴가
							echo "<font color='#ff8800'>휴가</font>";
						} else if ($col_gubun1_ex[$j] == "11") {	//병가
							echo "<font color='#ff8800'>병가</font>";
						} else if ($col_gubun1_ex[$j] == "12") {	//경조사
							echo "<font color='#ff8800'>경조사</font>";
						} else if ($col_gubun1_ex[$j] == "13") {	//기타
							echo "<font color='#ff8800'>기타</font>";
						} else if ($col_gubun1_ex[$j] == "14") {	//결근
							echo "<font color='#ff8800'>결근</font>";
						} else if ($col_gubun1_ex[$j] == "15") {	//교육
							echo "<font color='#ff8800'>교육</font>";
						} else if ($col_gubun1_ex[$j] == "19") {	//예비군
							echo "<font color='#ff8800'>예비군</font>";
//						} else if ($col_gubun1_ex[$j] == "8") {		//반차
//							echo "<font color='#ff8800'>반차</font>";
						} else if ($col_gubun1_ex[$j] == "6") {		//외근
							echo "<font color='#ff8800'>외근</font><br><font color='#00aa00'>(". $prt_time .")</font>";
						} else if ($col_gubun2_ex[$j] == "6") {		//외근
							echo "<font color='#ff8800'>외근</font><br><font color='#00aa00'>". $prt_time ."</font>";
//						} else if ($col_gubun1_ex[$j] == "7") {		//지각
//							echo "<font color='#ef0000'>". $prt_time ."</font>";
						} else if ($col_gubun1_ex[$j] == "4" || $col_gubun1_ex[$j] == "8") {		//프로젝트 반차/반차 - 출근인정시간대 이후 출근 포함
							echo "<font color='#ff8800'>반차</font><br><font color='#ef0000'>(". $prt_time .")</font>";
						} else if ($col_gubun2_ex[$j] == "5" || $col_gubun2_ex[$j] == "9") {		//프로젝트 반차/반차 - 출근인정시간대 이후 출근 포함
							echo "<font color='#ff8800'>반차</font><br><font color='#00aa00'>". $prt_time ."</font>";
						} else {
							if (substr($prt_time,0,2) < "08")		//출근인정시간대 이전 출근
							{
								echo "<font color='#00aa00'>08:00</font><br><font color='#0000cc'>(". $prt_time .")</a>";
							}
							else
							{
								echo "<font color='#00aa00'>". $prt_time ."</font>";
							}
						}
					}
?>
							</td>
<?
				}
			}
?>
						</tr>
						<tr class="line_down">
							<td><span class="out">퇴</span></td>
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

					if ($prt_time == "-")
					{
						echo $prt_time;
					}
					else
					{
						if ($col_gubun2_ex[$j] == "10" || $col_gubun2_ex[$j] == "16" || $col_gubun2_ex[$j] == "17" || $col_gubun2_ex[$j] == "18") {			//휴가/프로젝트휴가/리프레시휴가/무급휴가
							echo "-";
						} else if ($col_gubun2_ex[$j] == "11") {	//병가
							echo "-";
						} else if ($col_gubun2_ex[$j] == "12") {	//경조사
							echo "-";
						} else if ($col_gubun2_ex[$j] == "13") {	//기타
							echo "-";
						} else if ($col_gubun2_ex[$j] == "14") {	//결근
							echo "-";
						} else if ($col_gubun2_ex[$j] == "15") {	//교육
							echo "-";
						} else if ($col_gubun1_ex[$j] == "19") {	//예비군
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

					echo "<br><span style='font-size:11px; color:#666666;'>". substr($col_totaltime_ex[$j],0,2) .":". substr($col_totaltime_ex[$j],2,2) ."</span>";
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