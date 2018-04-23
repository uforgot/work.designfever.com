<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "팀장" : "";	//팀장대리 판단

	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d"); 
	$date_arr = explode("-",$date);
	$p_year = $date_arr[0];
	$p_month = $date_arr[1];
	$p_day = $date_arr[2];

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }
	if (strlen($p_day) == "1") { $p_day = "0".$p_day; }

	$NowDate = date("Y-m-d");
	$PrevDate = date("Y-m-d",strtotime ("-1 day", strtotime($date)));
	$NextDate = date("Y-m-d",strtotime ("+1 day", strtotime($date)));

	//회의실 예약 카운트
	$sql = "EXEC SP_BOOKING_LIST_01 '$date'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$total = $record['TOTAL'];				//총 예약건수
		$total_room1 = $record['TOTAL_ROOM1'];	//ROOM1 예약건수
		$total_room2 = $record['TOTAL_ROOM2'];	//ROOM2 예약건수
		$total_room3 = $record['TOTAL_ROOM3'];	//ROOM3 예약건수
		$total_room4 = $record['TOTAL_ROOM4'];	//ROOM4 예약건수
		$total_room5 = $record['TOTAL_ROOM5'];	//ROOM5 예약건수

		if ($total == "") { $total = "0"; }
		if ($total_room1 == "") { $total_room1 = "0"; }
		if ($total_room2 == "") { $total_room2 = "0"; }
		if ($total_room3 == "") { $total_room3 = "0"; }
		if ($total_room4 == "") { $total_room4 = "0"; }
		if ($total_room5 == "") { $total_room5 = "0"; }
	}

	// 회의실 예약 리스트
	$listSQL = "SELECT
					SEQNO, PRS_NAME, TITLE, ROOM, DATE, S_TIME, E_TIME, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE
				FROM 
					DF_BOOKING WITH(NOLOCK)
				WHERE 
					DATE = '$date'
				ORDER BY 
					ROOM, S_TIME";
	$listRs = sqlsrv_query($dbConn,$listSQL);

	while ($listRow = sqlsrv_fetch_array($listRs))
	{
		$booking_seqno = $listRow['SEQNO'];
		$booking_room = $listRow['ROOM'];
		$booking_stime = $listRow['S_TIME'];
		$booking_etime = $listRow['E_TIME'];
		$booking_title = $listRow['TITLE'];
		$booking_name = $listRow['PRS_NAME'];
		$booking_line = (strtotime($booking_etime)-strtotime($booking_stime))/1800;

		for($i=0;$i<$booking_line;$i++) {
			$booking_time = date("H:i",strtotime($booking_stime)+(1800*$i));
	
			if($i==0) {
				$booking_info = "<a href=\"./booking_write.php?type=modify&date=$date&seqno=$booking_seqno\"><span style='color:#000;font-weight:bold;'>".$booking_stime." ~ ".$booking_etime."</span>";
				$booking_info.= "&nbsp;(예약자: ".$booking_name.")";
				$booking_memo = "+ ".$booking_title;
				$booking_start = true;
			} else {
				$booking_info = "";
				$booking_memo = "";
				$booking_start = false;
			}

			$Data[$booking_room][$booking_time] = array(
														"seqno"=>$booking_seqno, 
														"info"=>$booking_info,
														"memo"=>$booking_memo,
														"start"=>$booking_start,
														"line"=>$booking_line
													);
		}
	}

	// 타임라인 출력 내용
	function getBookingInfo($info, $memo, $line) {
		if($line == 1) $len = 36;
		else if($line == 2) $len = 110;
		else if($line >= 3) $len = 190;

		$memo = getCutString($memo, $len);

		return $info."<br>".$memo."</a>";
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function sSubmit(f)
	{	
		var frm = document.form1;
		frm.date.value = f.year.value + "-" + f.month.value + "-" + f.day.value;
		frm.submit();
	}
	//전월보기
	function preDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$PrevDate?>";
		frm.submit();
	}
	//다음월보기
	function nextDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$NextDate?>";
		frm.submit();
	 }
</script>
</head>

<body>
<div class="wrapper">
<form method="get" name="form">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/booking_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="cal_top clearfix">
					<a href="javascript:preDay();" class="prev"><img src="../img/btn_prev.gif" alt="전일보기" /></a>
					<div>
					<select name="year" onchange='sSubmit(this.form)'>
					<?
						for ($i=$startYear; $i<=($p_year+1); $i++) 
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
					<select name="month" onchange='sSubmit(this.form)'>
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
					<div>
					<select name="day" onchange='sSubmit(this.form)'>
					<?
						$last_day = date("t", mktime(0, 0, 0, $p_month, '01', $p_year));

						for ($i=1; $i<=$last_day; $i++) 
						{
							if (strlen($i) == "1") 
							{
								$j = "0".$i;
							}
							else
							{
								$j = $i;
							}

							if ($j == $p_day)
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
					<span>일</span></div>
					<a href="javascript:nextDay();" class="next"><img src="../img/btn_next.gif" alt="다음일보기" /></a>
				</div>
				<table class="notable work2" style="margin-bottom:50px;" width="100%">
					<summary></summary>
					<colgroup><col width="*" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /></colgroup>
					<tr>
						<th class="gray">총 예약건수</th>
						<th>회의실1 (3F)</th>
						<th>회의실2 (3F)</th>
						<th>회의실3 (2F)</th>
						<th>회의실4 (2F)</th>
						<th>회의실5 (B1F)</th>
					</tr>
					<tr>
						<td><?=$total?></td>
						<td><?=$total_room1?></td> 
						<td><?=$total_room2?></td>
						<td><?=$total_room3?></td>
						<td><?=$total_room4?></td>
						<td><?=$total_room5?></td>
					</tr>
					<tr>
						<th>위치</th>
						<td><img src="../img/booking/room_01.jpg"></td> 
						<td><img src="../img/booking/room_02.jpg"></td>
						<td><img src="../img/booking/room_03.jpg"></td>
						<td><img src="../img/booking/room_04.jpg"></td>
						<td><img src="../img/booking/room_05.jpg"></td>
					</tr>
				</table>
			</div>
			<div class="calender_wrap clearfix">

				<table class="notable work5 project_detail board_list" width="100%" style="margin-bottom:10px;">
					<caption>팀원 주간보고서 테이블</caption>
					<colgroup>
						<col width="*" />
						<col width="18%" />
						<col width="18%" />
						<col width="18%" />
						<col width="18%" />
						<col width="18%" />
					</colgroup>

					<thead>
						<tr>
							<th>시간</th>
							<th>회의실1 (3F)</th>
							<th>회의실2 (3F)</th>
							<th>회의실3 (2F)</th>
							<th>회의실4 (2F)</th>
							<th>회의실5 (B1F)</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="booking_none">08:00<!--&nbsp;&nbsp;<? if($NowDate <= $date) { ?><a href="./booking_write.php?date=<?=$date?>&time=08:00"><img src="../img/project/btn_plus.gif" alt="추가" id="addTime"></a><? } else { ?>&nbsp;&nbsp;&nbsp;<? } ?>--></td>
							<td colspan="5" rowspan="33">
								<table class="notable work6 board_list" width="100%" style="margin-top:0px;">
							<?
								for($i=1441580400; $i<=1441635400; $i=$i+1800)
								{
									$time = date("H:i",$i);

									$room1_style = "booking_none";
									$room2_style = "booking_none";
									$room3_style = "booking_none";
									$room4_style = "booking_none";
									$room5_style = "booking_none";

									$room1_btn = "";
									$room2_btn = "";
									$room3_btn = "";
									$room4_btn = "";
									$room5_btn = "";

									$room1_info = "";
									$room2_info = "";
									$room3_info = "";
									$room4_info = "";
									$room5_info = "";

									if($Data['ROOM1'][$time]['seqno']) {
										$room1_style = "booking";
										if($Data['ROOM1'][$time]['start']) {
											$room1_style = " booking_first";
											$room1_line = $Data['ROOM1'][$time]['line'];
											$room1_info = getBookingInfo($Data['ROOM1'][$time]['info'],$Data['ROOM1'][$time]['memo'],$room1_line);
										}
									} else {
										if($NowDate <= $date) {
											$room1_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM1';\"";
											$room1_style .=" cursor";
										}
									}
									if($Data['ROOM2'][$time]['seqno']) {
										$room2_style = "booking";
										if($Data['ROOM2'][$time]['start']) {
											$room2_style = " booking_first";
											$room2_line = $Data['ROOM2'][$time]['line'];
											$room2_info = getBookingInfo($Data['ROOM2'][$time]['info'],$Data['ROOM2'][$time]['memo'],$room2_line);
										}
									} else {
										if($NowDate <= $date) {
											$room2_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM2';\"";
											$room2_style .=" cursor";
										}
									}
									if($Data['ROOM3'][$time]['seqno']) {
										$room3_style = "booking";
										if($Data['ROOM3'][$time]['start']) {
											$room3_style = " booking_first";
											$room3_line = $Data['ROOM3'][$time]['line'];
											$room3_info = getBookingInfo($Data['ROOM3'][$time]['info'],$Data['ROOM3'][$time]['memo'],$room3_line);
										}
									} else {
										if($NowDate <= $date) {
											$room3_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM3';\"";
											$room3_style .=" cursor";
										}
									}
									if($Data['ROOM4'][$time]['seqno']) {
										$room4_style = "booking";
										if($Data['ROOM4'][$time]['start']) {
											$room4_style = " booking_first";
											$room4_line = $Data['ROOM4'][$time]['line'];
											$room4_info = getBookingInfo($Data['ROOM4'][$time]['info'],$Data['ROOM4'][$time]['memo'],$room4_line);
										}
									} else {
										if($NowDate <= $date) {
											$room4_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM4';\"";
											$room4_style .=" cursor";
										}
									}
									if($Data['ROOM5'][$time]['seqno']) {
										$room5_style = "booking";
										if($Data['ROOM5'][$time]['start']) {
											$room5_style = " booking_first";
											$room5_line = $Data['ROOM5'][$time]['line'];
											$room5_info = getBookingInfo($Data['ROOM5'][$time]['info'],$Data['ROOM5'][$time]['memo'],$room5_line);
										}
									} else {
										if($NowDate <= $date) {
											$room5_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM5';\"";
											$room5_style .=" cursor";
										}
									}
							?>
									<tr>
										<td width="20%" class="<?=$room1_style?>" <?=$room1_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$room1_info?></div></td>
										<td width="20%" class="<?=$room2_style?>" <?=$room2_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$room2_info?></div></td>
										<td width="20%" class="<?=$room3_style?>" <?=$room3_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$room3_info?></div></td>
										<td width="20%" class="<?=$room4_style?>" <?=$room4_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$room4_info?></div></td>
										<td width="20%" class="<?=$room5_style?>" <?=$room5_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$room5_info?></div></td>
									</tr>
							<?
								}
							?>
								</table>					

							</td>
						</tr>

				<?
					for($i=1441582200; $i<=1441637200; $i=$i+1800)
					{
						$time = date("H:i",$i);
						
						/*
						if($NowDate > $date) {
							$booking_btn = "&nbsp;&nbsp;&nbsp;";
						} else {			
							$booking_btn = "<a href=\"./booking_write.php?date=$date&time=$time\"><img src=\"../img/project/btn_plus.gif\" alt=\"추가\" id=\"addTime\"></a>";
						}
						*/
				?>

						<tr>
							<td class="time"><?=$time?><!--&nbsp;&nbsp;<?=$booking_btn?>--></td>
						</tr>

				<?
					}
				?>
					</tbody>
				</table>

			</div>
		</div>
</form>

<form method="get" name="form1">
	<input type="hidden" name="date">
</form>

<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>