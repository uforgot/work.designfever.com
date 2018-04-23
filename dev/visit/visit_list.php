<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "����" : "";	//����븮 �Ǵ�

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

	//ȸ�ǽ� ���� ī��Ʈ
	$sql = "EXEC SP_VISIT_LIST_01 '$date'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$total = $record['TOTAL'];				//�� ����Ǽ�

		if ($total == "") { $total = "0"; }
	}

	// ȸ�ǽ� ���� ����Ʈ
	$listSQL = "SELECT
					SEQNO, PRS_NAME, COMPANY, VISITOR, CAR_NO, PHONE, DATE, MEMO, S_TIME, E_TIME, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE
				FROM 
					DF_VISIT WITH(NOLOCK)
				WHERE 
					DATE = '$date'
				ORDER BY 
					S_TIME";
	$listRs = sqlsrv_query($dbConn,$listSQL);

	while ($listRow = sqlsrv_fetch_array($listRs))
	{
		$visit_seqno = $listRow['SEQNO'];
		$visit_company = $listRow['COMPANY'];
		$visit_visitor = $listRow['VISITOR'];
		$visit_carno = $listRow['CAR_NO'];
		$visit_phone = $listRow['PHONE'];
		$visit_memo = $listRow['MEMO'];
		$visit_stime = $listRow['S_TIME'];
		$visit_name = $listRow['PRS_NAME'];

		$Data[] = array(
							"seqno"=>$visit_seqno, 
							"date"=>$date,
							"time"=>$visit_stime,
							"company"=>$visit_company,
							"visitor"=>$visit_visitor,
							"carno"=>$visit_carno,
							"phone"=>$visit_phone,
							"memo"=>$visit_memo,
							"writer"=>$visit_name
					);
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
	//��������
	function preDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$PrevDate?>";
		frm.submit();
	}
	//����������
	function nextDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$NextDate?>";
		frm.submit();
	 }

	//�Խù� �б�
	function funView(seqno)
	{
		$("#form").attr("target","_self");
		$("#form").attr("action","visit_write.php?type=modify&seqno="+seqno); 
		$("#form").submit();
	}
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form" id="form">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/booking_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="cal_top clearfix">
					<a href="javascript:preDay();" class="prev"><img src="../img/btn_prev.gif" alt="���Ϻ���" /></a>
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
					<span>��</span></div>
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
					<span>��</span></div>
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
					<span>��</span></div>
					<a href="javascript:nextDay();" class="next"><img src="../img/btn_next.gif" alt="�����Ϻ���" /></a>
				</div>
				<table class="notable work2" style="margin-bottom:50px;" width="100%">
					<summary></summary>
					<colgroup><col width="*" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /></colgroup>
					<tr>
						<th class="gray">�� �湮 ����Ǽ�</th>
					</tr>
					<tr>
						<td><?=$total?></td>
					</tr>
				</table>
			</div>
			<div class="calender_wrap clearfix">
				<div class="top_space3 clearfix">
					<a href="visit_write.php?type=write&date=<?=$date?>"><img src="../img/write.jpg" alt="�Խù� �ۼ�" id="btnWrite" class="btn_right" /></a>
				</div>

				<table class="notable work3 board_list" width="100%" style="margin-bottom:10px;">
					<caption>���� �ְ����� ���̺�</caption>
					<colgroup>
						<col width="12%" />
						<col width="12%" />
						<col width="10%" />
						<col width="10%" />
						<col width="12%" />
						<col width="*" />
					</colgroup>

					<thead>
						<tr>
							<th>�湮�Ͻ�</th>
							<th>��ü��</th> 
							<th>�湮�ڸ�</th>
							<th>�湮������ȣ</th>
							<th>����ó</th>
							<th>�޸�</th>
						</tr>
					</thead>
					<tbody>
					<?
						for($i=0; $i<count($Data); $i++)
						{
					?>
						<tr>
							<td width="12%" <?=$visit_btn?>><a href="javascript:funView('<?=$Data[$i]['seqno']?>');" style="cursor:hand"><?=$Data[$i]['date'].' '.$Data[$i]['time']?></a></td>
							<td width="12%" <?=$visit_btn?>><a href="javascript:funView('<?=$Data[$i]['seqno']?>');" style="cursor:hand"><?=$Data[$i]['company']?></a></td>
							<td width="10%" <?=$visit_btn?>><a href="javascript:funView('<?=$Data[$i]['seqno']?>');" style="cursor:hand"><?=$Data[$i]['visitor']?></a></td>
							<td width="10%" <?=$visit_btn?>><a href="javascript:funView('<?=$Data[$i]['seqno']?>');" style="cursor:hand"><?=$Data[$i]['carno']?></a></td>
							<td width="10%" <?=$visit_btn?>><a href="javascript:funView('<?=$Data[$i]['seqno']?>');" style="cursor:hand"><?=$Data[$i]['phone']?></a></td>
							<td width="*" <?=$visit_btn?> style="text-align:left;"><a href="javascript:funView('<?=$Data[$i]['seqno']?>');" style="cursor:hand"><?=$Data[$i]['memo']?></a></td>
						</tr>
					<?
						}
						
						if(count($Data) == 0) {
					?>
						<tr>
							<td width="100%" colspan="6">���� ����� �湮���� �����ϴ�.</td>
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