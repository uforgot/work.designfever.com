<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("등록대기,탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	
	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d"); 
	$time = isset($_REQUEST['time']) ? $_REQUEST['time'] : null; 
	$room = isset($_REQUEST['room']) ? $_REQUEST['room'] : null; 

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  
	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  

	if ($type == "modify")
	{
		$type_title = "수정";
		$type_btn = "modify_btn";

		if ($seqno == "")
		{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("해당 글이 존재하지 않습니다.");
		history.back();
	</script>
<?
			exit;
		}

		$searchSQL = " WHERE SEQNO = '$seqno'";
		
		$sql = "SELECT
					PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, ROOM, DATE, S_TIME, E_TIME, CONVERT(VARCHAR(16),REG_DATE,120) AS REG_DATE
				FROM
					DF_BOOKING WITH(NOLOCK)".$searchSQL;
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		if (sqlsrv_has_rows($rs) > 0)
		{
			$booking_id = $record['PRS_ID'];
			$booking_name = $record['PRS_NAME'];
			$booking_login = $record['PRS_LOGIN'];
			$booking_team = $record['PRS_TEAM'];
			$booking_position = $record['PRS_POSITION'];
			$booking_title = $record['TITLE'];
			$booking_room = $record['ROOM'];
			$booking_date = $record['DATE'];
			$booking_stime = $record['S_TIME'];
			$booking_etime = $record['E_TIME'];
			$booking_date = $record['REG_DATE'];
		}
		else
		{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("해당 글이 존재하지 않습니다.");
		history.back();
	</script>
<?
			exit;
		}
		
		$selected1[$booking_room] = "selected";
		$stime_arr = explode(":",$booking_stime);
		$selected2[$stime_arr[0]] = "selected";
		$selected3[$stime_arr[1]] = "selected";
		$etime_arr = explode(":",$booking_etime);
		$selected4[$etime_arr[0]] = "selected";
		$selected5[$etime_arr[1]] = "selected";
	}
	else if ($type == "write")
	{
		$type_title = "등록";
		$type_btn = "register_btn";

		$booking_id = $prs_id;
		$booking_name = $prs_name;
		$booking_login = $prs_login;
		$booking_team = $prs_team;
		$booking_position = $prs_position;
		$booking_title = "";
		$booking_room = $room;
		$booking_date = $date;
		$booking_stime = $time;
		$booking_etime = date("H:i",strtotime ("+30 minutes", strtotime($time)));
		
		$selected1[$booking_room] = "selected";

		$stime_arr = explode(":", $booking_stime);
		$selected2[$stime_arr[0]] = "selected";
		$selected3[$stime_arr[1]] = "selected";

		$etime_arr = explode(":", $booking_etime);
		$selected4[$etime_arr[0]] = "selected";
		$selected5[$etime_arr[1]] = "selected";
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript" src="/ckeditor/ckeditor.js" /></script>
<script type="text/JavaScript">
	function funWrite()
	{
		var frm = document.form;

		if(frm.room_name.value == ""){
			alert("회의실을 선택해주세요");
			frm.room_name.focus();
			return;
		}

		var s_time = frm.s_hour.value + ":" + frm.s_min.value;
		var e_time = frm.e_hour.value + ":" + frm.e_min.value;
		
		if(e_time <= s_time) {
			alert("회의 종료시간을 올바르게 지정해 주세요.");
			frm.e_hour.focus();
			return;
		}

		if(frm.title.value == ""){
			alert("내용을 입력해주세요");
			frm.title.focus();
			return;
		}

		//내용 유효성 검사 할 부분
		if(confirm("예약을 <?=$type_title?> 하시겠습니까")){
			frm.target = "hdnFrame";
			frm.action = 'booking_write_act.php'; 
			frm.submit();
		}
	}

	function funDelete()
	{
		var frm = document.form;

		//내용 유효성 검사 할 부분
		if(confirm("예약을 삭제 하시겠습니까")){
			frm.type.value = "delete";
			frm.target = "hdnFrame";
			frm.action = 'booking_write_act.php'; 
			frm.submit();
		}
	}
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form" action="booking_write_act.php">
<input type="hidden" name="type" value="<?=$type?>">						<!-- 등록수정삭제구분 -->
<input type="hidden" name="date" value="<?=$date?>">						<!-- 날짜 -->
<input type="hidden" name="seqno" value="<?=$seqno?>">						<!-- 글번호 -->
<input type="hidden" name="writer" value="<?=$booking_login?>">				<!-- 글작성자 prs_login -->
<input type="hidden" name="writer_id" value="<?=$booking_id?>">				<!-- 글작성자 prs_id -->
<input type="hidden" name="writer_name" value="<?=$booking_name?>">			<!-- 글작성자 prs_name -->
<input type="hidden" name="writer_team" value="<?=$booking_team?>">			<!-- 글작성자 prs_team -->
<input type="hidden" name="writer_position" value="<?=$booking_position?>">	<!-- 글작성자 prs_position -->

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/booking_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="work_write">
					<div class="top_space2 clearfix">
						<div class="btn_left">
							<a href="booking_list.php?date=<?=$date?>"><img src="../img/btn_list.gif" alt="목록보기" /></a>
						</div>
						<div class="btn_right btn_nomargin">
						<? if($type == "modify" && $booking_login == $prs_login) { ?>
							<a href="javascript:funDelete()"><img src="../img/btn_del.gif" alt="글 삭제" /></a> 
						<? } ?>
						<? if ($type == "modify") { ?>																				
							<a href="javascript:funWrite()"><img src="../img/btn_modi.gif" alt="글 수정" /></a>
						<? } else if ($type == "write") { ?>																				
							<a href="javascript:funWrite()"><img src="../img/btn_register.jpg" alt="글 작성" /></a>
						<? } ?>
						</div>
					</div>
					<div id="bbs">
						<div class="name_section clearfix">
							<p class="left">날짜</p>
							<p class="right bold_face"><?=$date?></p>
						</div>
						<div class="name_section clearfix">
							<p class="left">예약자</p>
							<p class="right bold_face"><?=$booking_position?> <?=$booking_name?></p>
						</div>
						<div class="title_section clearfix">
							<p class="left">회의실</p>
							<p class="right bold_face"><select name="room_name">
															<option value="">회의실 선택</option>
															<option value="">---------------</option>
															<option value="ROOM1" <?=$selected1['ROOM1']?>>회의실1 (3F)</option>
															<option value="ROOM2" <?=$selected1['ROOM2']?>>회의실2 (3F)</option>
															<option value="ROOM3" <?=$selected1['ROOM3']?>>회의실3 (2F)</option>
															<option value="ROOM4" <?=$selected1['ROOM4']?>>회의실4 (2F)</option>
															<option value="ROOM5" <?=$selected1['ROOM5']?>>회의실5 (B1F)</option>
														</select></p>
						</div>
						<div class="title_section clearfix">
							<p class="left">시간</p>
							<p class="right bold_face"><select name="s_hour">
														<? 
															for($i=8;$i<=23;$i++) {
																$_i = str_pad($i,2,'0',STR_PAD_LEFT);	
														?>
															<option value="<?=$_i?>" <?=$selected2[$_i]?>><?=$_i?></option>
														<?
															}
														?>
														</select>:&nbsp;
														<select name="s_min">
															<option value="00" <?=$selected3['00']?>>00</option>
															<option value="30" <?=$selected3['30']?>>30</option>
														</select>~&nbsp;
														<select name="e_hour">
														<? 
															for($j=8;$j<=23;$j++) {
																$_j = str_pad($j,2,'0',STR_PAD_LEFT);	
														?>
															<option value="<?=$_j?>" <?=$selected4[$_j]?>><?=$_j?></option>
														<?
															}
														?>
														</select>:&nbsp;
														<select name="e_min">
															<option value="00" <?=$selected5['00']?>>00</option>
															<option value="30" <?=$selected5['30']?>>30</option>
														</select>
														
														<a href="javascript:ShowPop('BookingDesc');" class="w_re">+ 예약현황</a>
														</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">내용</p>
								<input type="text" name="title" id="title" maxlength="105" class="t_field df_textinput" value="<?=$booking_title?>">
						</div>
					</div>
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
<?
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
	
			$Data[$booking_room][$booking_time] = array(
														"seqno"=>$booking_seqno 
													);
		}
	}
?>
<div id="popBookingDesc" class="booking-popup1" style="display:none;">
	<div class="pop_top">
		<p class="pop_title">회의실예약 현황 (<?=$date?>)</p>
		<a href="javascript:HidePop('BookingDesc');" class="close">닫기</a>
	</div>
	<div class="pop_body">

				<table class="notable work7 work7_team" width="100%">
					<summary></summary>
					<thead></thead>
					<tbody>
						<tr class="day">
						<th width="80">&nbsp;</th>
					<?
						for($i=1441580400; $i<=1441635400; $i=$i+1800)
						{
							$time = date("H:i",$i);
							echo "<th width='32' align=left>".$time."</th>";
						}
					?>
						<th>&nbsp;</th>
						</tr>
					</tbody>
				</table>
				<table class="notable work7 work7_team" width="100%">
					<summary></summary>
					<thead></thead>
					<tbody>
				<?
					for($j=1;$j<=5;$j++) {
						switch($j) {
							case "1": $floor = " (3F)"; break;
							case "2":
							case "3": 
							case "4": $floor = " (2F)"; break;
							case "5": $floor = " (B1F)"; break;
						}
				?>
						<tr>
							<td>회의실<?=$j?><?=$floor?></td>
					<?
						for($i=1441580400; $i<=1441635400; $i=$i+1800)
						{
							$time = date("H:i",$i);
							$mark = "&nbsp;";
							if($Data['ROOM'.$j][$time]['seqno']) {
								$mark = "<font color='orange'>●</font>";
							}
							echo "<td width='32'>$mark</td>";
						}
					?>
						</tr>
				<?
					}
				?>
					</tbody>
				</table>

	</div>
</div>
</body>
</html>
