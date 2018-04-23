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

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  
	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  

	$nowYear = date("Y");

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
					PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, COMPANY, VISITOR, CAR_NO, PHONE, DATE, MEMO, S_TIME, E_TIME, CONVERT(VARCHAR(16),REG_DATE,120) AS REG_DATE
				FROM
					DF_VISIT WITH(NOLOCK)".$searchSQL;
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		if (sqlsrv_has_rows($rs) > 0)
		{
			$visit_id = $record['PRS_ID'];
			$visit_name = $record['PRS_NAME'];
			$visit_login = $record['PRS_LOGIN'];
			$visit_team = $record['PRS_TEAM'];
			$visit_position = $record['PRS_POSITION'];
			$visit_company = $record['COMPANY'];
			$visit_visitor = $record['VISITOR'];
			$visit_carno = $record['CAR_NO'];
			$visit_phone = $record['PHONE'];
			$visit_memo = $record['MEMO'];
			$visit_date = $record['DATE'];
			$visit_stime = $record['S_TIME'];
			$visit_regdate = $record['REG_DATE'];
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
		
		$stime_arr = explode(":",$visit_stime);
		$selected2[$stime_arr[0]] = "selected";
		$selected3[$stime_arr[1]] = "selected";
	}
	else if ($type == "write")
	{
		$type_title = "등록";
		$type_btn = "register_btn";

		$visit_id = $prs_id;
		$visit_name = $prs_name;
		$visit_login = $prs_login;
		$visit_team = $prs_team;
		$visit_position = $prs_position;
		$visit_company = "";
		$visit_visitor = "";
		$visit_carno = "";
		$visit_phone = "";
		$visit_memo = "";
		$visit_date = $date;
		$visit_stime = $time;
		
		$stime_arr = explode(":", $visit_stime);
		$selected2[$stime_arr[0]] = "selected";
		$selected3[$stime_arr[1]] = "selected";
	}

	$visit_year = substr($visit_date,0,4);
	$visit_month = substr($visit_date,5,2);
	$visit_day = substr($visit_date,8,2);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript" src="/ckeditor/ckeditor.js" /></script>
<script type="text/JavaScript">
	function funWrite()
	{
		var frm = document.form;

		if(frm.company.value == ""){
			alert("업체명을 입력해주세요");
			frm.company.focus();
			return;
		}

		if(frm.visitor.value == ""){
			alert("방문자명을 입력해주세요");
			frm.visitor.focus();
			return;
		}

		//내용 유효성 검사 할 부분
		if(confirm("방문예약을 <?=$type_title?> 하시겠습니까")){
			frm.target = "hdnFrame";
			frm.action = 'visit_write_act.php'; 
			frm.submit();
		}
	}

	function funDelete()
	{
		var frm = document.form;

		//내용 유효성 검사 할 부분
		if(confirm("방문예약을 삭제 하시겠습니까")){
			frm.type.value = "delete";
			frm.target = "hdnFrame";
			frm.action = 'visit_write_act.php'; 
			frm.submit();
		}
	}
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form" action="visit_write_act.php">
<input type="hidden" name="type" value="<?=$type?>">						<!-- 등록수정삭제구분 -->
<input type="hidden" name="date" value="<?=$visit_date?>">					<!-- 삭제시 리턴 날짜 -->
<input type="hidden" name="seqno" value="<?=$seqno?>">						<!-- 글번호 -->
<input type="hidden" name="writer" value="<?=$visit_login?>">				<!-- 글작성자 prs_login -->
<input type="hidden" name="writer_id" value="<?=$visit_id?>">				<!-- 글작성자 prs_id -->
<input type="hidden" name="writer_name" value="<?=$visit_name?>">			<!-- 글작성자 prs_name -->
<input type="hidden" name="writer_team" value="<?=$visit_team?>">			<!-- 글작성자 prs_team -->
<input type="hidden" name="writer_position" value="<?=$visit_position?>">	<!-- 글작성자 prs_position -->

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/booking_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="work_write">
					<div class="top_space2 clearfix">
						<div class="btn_left">
							<a href="visit_list.php?date=<?=$visit_date?>"><img src="../img/btn_list.gif" alt="목록보기" /></a>
						</div>
						<div class="btn_right btn_nomargin">
						<? if($type == "modify" && $visit_login == $prs_login) { ?>
							<a href="javascript:funDelete()"><img src="../img/btn_del.gif" alt="글 삭제" /></a> 
							<a href="javascript:funWrite()"><img src="../img/btn_modi.gif" alt="글 수정" /></a>
						<? } else if ($type == "write") { ?>																				
							<a href="javascript:funWrite()"><img src="../img/btn_register.jpg" alt="글 작성" /></a>
						<? } ?>
						</div>
					</div>
					<div id="bbs">
						<div class="title_section clearfix">
							<p class="left">예약자</p>
							<p class="right bold_face"><?=$visit_position?> <?=$visit_name?></p>
						</div>
						<div class="title_section clearfix">
							<p class="left">업체명</p>
							<p class="right bold_face">
								<input type="text" name="company" id="company" maxlength="80" class="t_field_s df_textinput2" value="<?=$visit_company?>">							
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">방문자</p>
							<p class="right bold_face">
								<input type="text" name="visitor" id="visitor" maxlength="50" class="t_field_s df_textinput2" value="<?=$visit_visitor?>">							
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">방문차량번호</p>
							<p class="right bold_face">
								<input type="text" name="carno" id="carno" maxlength="20" class="t_field_s df_textinput" value="<?=$visit_carno?>">							
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">연락처</p>
							<p class="right bold_face">
								<input type="text" name="phone" id="phone" maxlength="20" class="t_field_s df_textinput" value="<?=$visit_phone?>">
								('-' 포함 입력, 예: 010-123-4567)
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">방문일시</p>
							<p class="right bold_face">
									<select name="year" id="year">
									<?
										for ($i=$startYear; $i<=($nowYear+1); $i++) 
										{
											if ($i == $visit_year) 
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
									</select>년&nbsp;
									<select name="month" id="month">
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

											if ($j == $visit_month)
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
									</select>월&nbsp;
									<select name="day" id="day">
									<?
										for ($i=1; $i<=31; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == $visit_day)
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
									</select>일&nbsp;
									<input type="hidden" id="date" class="datepicker">							
									&nbsp;
									<select name="s_hour">
									<? 
										for($i=8;$i<=23;$i++) {
											$_i = str_pad($i,2,'0',STR_PAD_LEFT);	
									?>
										<option value="<?=$_i?>" <?=$selected2[$_i]?>><?=$_i?></option>
									<?
										}
									?>
									</select>시&nbsp; 
									<select name="s_min">
										<option value="00" <?=$selected3['00']?>>00</option>
										<option value="10" <?=$selected3['10']?>>10</option>
										<option value="20" <?=$selected3['20']?>>20</option>
										<option value="30" <?=$selected3['30']?>>30</option>
										<option value="40" <?=$selected3['40']?>>40</option>
										<option value="50" <?=$selected3['50']?>>50</option>
									</select>분&nbsp;
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">방문목적</p>
								<input type="text" name="memo" id="memo" maxlength="105" class="t_field df_textinput" value="<?=$visit_memo?>">
						</div>
					</div>
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>

</body>
</html>
