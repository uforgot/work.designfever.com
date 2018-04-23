<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("��ϴ��,Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
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
		$type_title = "����";
		$type_btn = "modify_btn";

		if ($seqno == "")
		{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ش� ���� �������� �ʽ��ϴ�.");
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
		alert("�ش� ���� �������� �ʽ��ϴ�.");
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
		$type_title = "���";
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
			alert("ȸ�ǽ��� �������ּ���");
			frm.room_name.focus();
			return;
		}

		var s_time = frm.s_hour.value + ":" + frm.s_min.value;
		var e_time = frm.e_hour.value + ":" + frm.e_min.value;
		
		if(e_time <= s_time) {
			alert("ȸ�� ����ð��� �ùٸ��� ������ �ּ���.");
			frm.e_hour.focus();
			return;
		}

		if(frm.title.value == ""){
			alert("������ �Է����ּ���");
			frm.title.focus();
			return;
		}

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("������ <?=$type_title?> �Ͻðڽ��ϱ�")){
			frm.target = "hdnFrame";
			frm.action = 'booking_write_act.php'; 
			frm.submit();
		}
	}

	function funDelete()
	{
		var frm = document.form;

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("������ ���� �Ͻðڽ��ϱ�")){
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
<input type="hidden" name="type" value="<?=$type?>">						<!-- ��ϼ����������� -->
<input type="hidden" name="date" value="<?=$date?>">						<!-- ��¥ -->
<input type="hidden" name="seqno" value="<?=$seqno?>">						<!-- �۹�ȣ -->
<input type="hidden" name="writer" value="<?=$booking_login?>">				<!-- ���ۼ��� prs_login -->
<input type="hidden" name="writer_id" value="<?=$booking_id?>">				<!-- ���ۼ��� prs_id -->
<input type="hidden" name="writer_name" value="<?=$booking_name?>">			<!-- ���ۼ��� prs_name -->
<input type="hidden" name="writer_team" value="<?=$booking_team?>">			<!-- ���ۼ��� prs_team -->
<input type="hidden" name="writer_position" value="<?=$booking_position?>">	<!-- ���ۼ��� prs_position -->

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/booking_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="work_write">
					<div class="top_space2 clearfix">
						<div class="btn_left">
							<a href="booking_list.php?date=<?=$date?>"><img src="../img/btn_list.gif" alt="��Ϻ���" /></a>
						</div>
						<div class="btn_right btn_nomargin">
						<? if($type == "modify" && $booking_login == $prs_login) { ?>
							<a href="javascript:funDelete()"><img src="../img/btn_del.gif" alt="�� ����" /></a> 
						<? } ?>
						<? if ($type == "modify") { ?>																				
							<a href="javascript:funWrite()"><img src="../img/btn_modi.gif" alt="�� ����" /></a>
						<? } else if ($type == "write") { ?>																				
							<a href="javascript:funWrite()"><img src="../img/btn_register.jpg" alt="�� �ۼ�" /></a>
						<? } ?>
						</div>
					</div>
					<div id="bbs">
						<div class="name_section clearfix">
							<p class="left">��¥</p>
							<p class="right bold_face"><?=$date?></p>
						</div>
						<div class="name_section clearfix">
							<p class="left">������</p>
							<p class="right bold_face"><?=$booking_position?> <?=$booking_name?></p>
						</div>
						<div class="title_section clearfix">
							<p class="left">ȸ�ǽ�</p>
							<p class="right bold_face"><select name="room_name">
															<option value="">ȸ�ǽ� ����</option>
															<option value="">---------------</option>
															<option value="ROOM1" <?=$selected1['ROOM1']?>>ȸ�ǽ�1 (3F)</option>
															<option value="ROOM2" <?=$selected1['ROOM2']?>>ȸ�ǽ�2 (3F)</option>
															<option value="ROOM3" <?=$selected1['ROOM3']?>>ȸ�ǽ�3 (2F)</option>
															<option value="ROOM4" <?=$selected1['ROOM4']?>>ȸ�ǽ�4 (2F)</option>
															<option value="ROOM5" <?=$selected1['ROOM5']?>>ȸ�ǽ�5 (B1F)</option>
														</select></p>
						</div>
						<div class="title_section clearfix">
							<p class="left">�ð�</p>
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
														
														<a href="javascript:ShowPop('BookingDesc');" class="w_re">+ ������Ȳ</a>
														</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">����</p>
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
	// ȸ�ǽ� ���� ����Ʈ
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
		<p class="pop_title">ȸ�ǽǿ��� ��Ȳ (<?=$date?>)</p>
		<a href="javascript:HidePop('BookingDesc');" class="close">�ݱ�</a>
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
							<td>ȸ�ǽ�<?=$j?><?=$floor?></td>
					<?
						for($i=1441580400; $i<=1441635400; $i=$i+1800)
						{
							$time = date("H:i",$i);
							$mark = "&nbsp;";
							if($Data['ROOM'.$j][$time]['seqno']) {
								$mark = "<font color='orange'>��</font>";
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
