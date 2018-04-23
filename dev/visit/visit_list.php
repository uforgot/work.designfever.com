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
	$sql = "EXEC SP_VISIT_LIST_01 '$date'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$total = $record['TOTAL'];				//총 예약건수

		if ($total == "") { $total = "0"; }
	}

	// 회의실 예약 리스트
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

	//게시물 읽기
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
						<th class="gray">총 방문 예약건수</th>
					</tr>
					<tr>
						<td><?=$total?></td>
					</tr>
				</table>
			</div>
			<div class="calender_wrap clearfix">
				<div class="top_space3 clearfix">
					<a href="visit_write.php?type=write&date=<?=$date?>"><img src="../img/write.jpg" alt="게시물 작성" id="btnWrite" class="btn_right" /></a>
				</div>

				<table class="notable work3 board_list" width="100%" style="margin-bottom:10px;">
					<caption>팀원 주간보고서 테이블</caption>
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
							<th>방문일시</th>
							<th>업체명</th> 
							<th>방문자명</th>
							<th>방문차량번호</th>
							<th>연락처</th>
							<th>메모</th>
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
							<td width="100%" colspan="6">금일 예약된 방문객이 없습니다.</td>
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