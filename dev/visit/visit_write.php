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

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  
	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  

	$nowYear = date("Y");

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
		alert("�ش� ���� �������� �ʽ��ϴ�.");
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
		$type_title = "���";
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
			alert("��ü���� �Է����ּ���");
			frm.company.focus();
			return;
		}

		if(frm.visitor.value == ""){
			alert("�湮�ڸ��� �Է����ּ���");
			frm.visitor.focus();
			return;
		}

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("�湮������ <?=$type_title?> �Ͻðڽ��ϱ�")){
			frm.target = "hdnFrame";
			frm.action = 'visit_write_act.php'; 
			frm.submit();
		}
	}

	function funDelete()
	{
		var frm = document.form;

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("�湮������ ���� �Ͻðڽ��ϱ�")){
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
<input type="hidden" name="type" value="<?=$type?>">						<!-- ��ϼ����������� -->
<input type="hidden" name="date" value="<?=$visit_date?>">					<!-- ������ ���� ��¥ -->
<input type="hidden" name="seqno" value="<?=$seqno?>">						<!-- �۹�ȣ -->
<input type="hidden" name="writer" value="<?=$visit_login?>">				<!-- ���ۼ��� prs_login -->
<input type="hidden" name="writer_id" value="<?=$visit_id?>">				<!-- ���ۼ��� prs_id -->
<input type="hidden" name="writer_name" value="<?=$visit_name?>">			<!-- ���ۼ��� prs_name -->
<input type="hidden" name="writer_team" value="<?=$visit_team?>">			<!-- ���ۼ��� prs_team -->
<input type="hidden" name="writer_position" value="<?=$visit_position?>">	<!-- ���ۼ��� prs_position -->

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/booking_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="work_write">
					<div class="top_space2 clearfix">
						<div class="btn_left">
							<a href="visit_list.php?date=<?=$visit_date?>"><img src="../img/btn_list.gif" alt="��Ϻ���" /></a>
						</div>
						<div class="btn_right btn_nomargin">
						<? if($type == "modify" && $visit_login == $prs_login) { ?>
							<a href="javascript:funDelete()"><img src="../img/btn_del.gif" alt="�� ����" /></a> 
							<a href="javascript:funWrite()"><img src="../img/btn_modi.gif" alt="�� ����" /></a>
						<? } else if ($type == "write") { ?>																				
							<a href="javascript:funWrite()"><img src="../img/btn_register.jpg" alt="�� �ۼ�" /></a>
						<? } ?>
						</div>
					</div>
					<div id="bbs">
						<div class="title_section clearfix">
							<p class="left">������</p>
							<p class="right bold_face"><?=$visit_position?> <?=$visit_name?></p>
						</div>
						<div class="title_section clearfix">
							<p class="left">��ü��</p>
							<p class="right bold_face">
								<input type="text" name="company" id="company" maxlength="80" class="t_field_s df_textinput2" value="<?=$visit_company?>">							
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">�湮��</p>
							<p class="right bold_face">
								<input type="text" name="visitor" id="visitor" maxlength="50" class="t_field_s df_textinput2" value="<?=$visit_visitor?>">							
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">�湮������ȣ</p>
							<p class="right bold_face">
								<input type="text" name="carno" id="carno" maxlength="20" class="t_field_s df_textinput" value="<?=$visit_carno?>">							
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">����ó</p>
							<p class="right bold_face">
								<input type="text" name="phone" id="phone" maxlength="20" class="t_field_s df_textinput" value="<?=$visit_phone?>">
								('-' ���� �Է�, ��: 010-123-4567)
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">�湮�Ͻ�</p>
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
									</select>��&nbsp;
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
									</select>��&nbsp;
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
									</select>��&nbsp;
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
									</select>��&nbsp; 
									<select name="s_min">
										<option value="00" <?=$selected3['00']?>>00</option>
										<option value="10" <?=$selected3['10']?>>10</option>
										<option value="20" <?=$selected3['20']?>>20</option>
										<option value="30" <?=$selected3['30']?>>30</option>
										<option value="40" <?=$selected3['40']?>>40</option>
										<option value="50" <?=$selected3['50']?>>50</option>
									</select>��&nbsp;
							</p>
						</div>
						<div class="title_section clearfix">
							<p class="left">�湮����</p>
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
