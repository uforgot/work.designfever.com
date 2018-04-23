<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id != "2" && $prf_id != "3" && $prf_id != "4") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("해당페이지는 팀장 이상만 확인 가능합니다.");
		location.href="vacation_list.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

	$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : date("Y"); 
	$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : date("m"); 
	if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
	$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : 1; 
	if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
	$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : date("Y"); 
	$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : date("m"); 
	if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
	$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d"); 
	if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

	$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
	$to_date = $to_year ."-". $to_month ."-". $to_day;

	$searchSQL = " WHERE PRF_ID IN (1,2,3,4,5)";

	if ($prf_id == "2")			//팀장
	{
		$searchSQL .=" AND PRS_TEAM = '$prs_team'";
	}
	else if ($prf_id == "3")	//실장
	{
		$searchSQL .= " AND PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$prs_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$prs_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$prs_team')))";
	}
	else if ($prf_id == "4")	//실장, 관리자, 임원
	{
		if ($p_team != "")
		{
			$searchSQL .= " AND PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team')))";
		}
	}

	if ($p_name != "") 
	{
		$searchSQL .= " AND PRS_NAME = '$name'";
	}

	$sql = "SELECT SORT, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) ORDER BY SORT";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_TEAM='". $record['TEAM'] ."' THEN ". $record['SORT'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, PRS_NAME";

	$sql = "SELECT COUNT(*) FROM DF_PERSON WITH(NOLOCK)". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 20;

	$sql = "SELECT 
				T.PRS_ID, T.PRS_TEAM, T.PRS_POSITION, T.PRS_NAME 
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
					PRS_ID, PRS_TEAM, PRS_POSITION, PRS_NAME 
				FROM 
					DF_PERSON WITH(NOLOCK)
				$searchSQL
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		//날짜 지정
		$("#fr_year, #fr_month, #fr_day").change(function() {
			$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		});
		$("#fr_date").datepicker({
			onSelect: function (selectedDate) {
				$("#fr_year").val( selectedDate.substring(6,10) );
				$("#fr_month").val( selectedDate.substring(0,2) );
				$("#fr_day").val( selectedDate.substring(3,5) );
			}
		});
		$("#to_year, #to_month, #to_day").change(function() {
			$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		});
		$("#to_date").datepicker({
			onSelect: function (selectedDate) {
				$("#to_year").val( selectedDate.substring(6,10) );
				$("#to_month").val( selectedDate.substring(0,2) );
				$("#to_day").val( selectedDate.substring(3,5) );
			}
		});
	});
</script>
<script src="/js/vacation.js"></script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post">
<input type="hidden" name="page" value="<?=$page?>">
	<? include INC_PATH."/top_menu.php"; ?>
		<div class="inner-home">
		<? include INC_PATH."/vacation_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="vacation_stats clearfix">
					<table class="notable " width="100%">
						<tr>
							<th scope="row">휴가통계</th>
						</tr>
					</table>
				</div>

				<div class="vacation_stats_search statistics_search clearfix">
					<table class="notable" width="100%">
						<tr class="a1">
							<th>검색</th>
							<td>
								<div class="btns">
									<a href="javascript:funSearch(this.form);"><img src="/img/btn_search_p.gif" alt="검색" /></a>
									<a href="vacation_total.php"><img src="/img/btn_reset_p.gif" alt="검색 초기화" /></a>
								</div>
							</td>
						</tr>
						<tr>
							<th>부서</th>
							<td>
								<select name="team" style="width:200px;">			
									<option value=""<? if ($p_team2 == ""){ echo " selected"; } ?>>전직원</option>
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
							</td>
						</tr>
						<tr>
							<th>이름</th>
							<td>
								<input id="" type="text" name="name" value="<?=$p_name?>"/>
							</td>
						</tr>
						<tr class="period">
							<th>휴가일자</th>
							<td class="last">
								<select name="fr_year" id="fr_year">
								<?
									for ($i=$startYear; $i<=($fr_year+1); $i++) 
									{
										if ($i == $fr_year) 
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
								<span>년</span>
								<select name="fr_month" id="fr_month">
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

										if ($j == $fr_month)
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
								<span>월</span>
								<select name="fr_day" id="fr_day">
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

										if ($j == $fr_day)
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
								<span>일</span>
								<input type="hidden" id="fr_date" class="datepicker">
								<span>-</span>
								<select name="to_year" id="to_year">
								<?
									for ($i=$startYear; $i<=($to_year+1); $i++) 
									{
										if ($i == $to_year) 
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
								<span>년</span>
								<select name="to_month" id="to_month">
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

										if ($j == $to_month)
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
								<span>월</span>
								<select name="to_day" id="to_day">
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

										if ($j == $to_day)
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
								<span>일</span>
								<input type="hidden" id="to_date" class="datepicker">
							</td>
						</tr>
					</table>
				</div>

				<table class="vacation notable statistics_list"  width="100%">
					<caption>휴가통계 테이블</caption>
					<colgroup>
						<col width="5%" />
						<col width="8%" />
						<col width="8%" />
						<col width="*" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
					</colgroup>
	
					<thead>
						<tr>
							<th>no.</th>
							<th>이름</th>
							<th>직급</th>
							<th>부서</th>
							<th>연차</th>
							<th>병가</th>
							<th>반차</th>
							<th>리프레쉬</th>
							<th>프로젝트</th>
							<th>경조사</th>
							<th>기타</th>
							<th class="last">합계</th>
						</tr>
					</thead>

					<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="12">등록된 휴가내역이 없습니다.</td>
							</tr>
<?
	}
	else
	{
		while ($record = sqlsrv_fetch_array($rs))
		{
			$id = $record['PRS_ID'];
			$team = $record['PRS_TEAM'];
			$position = $record['PRS_POSITION'];
			$name = $record['PRS_NAME'];

			$sql1 = "EXEC SP_VACATION_TOTAL_01 '$id','$fr_date','$to_date'";
			$rs1 = sqlsrv_query($dbConn, $sql1);

			$record1 = sqlsrv_fetch_array($rs1);
			$vacation_total = $record1['VACATION_TOTAL'];
			$vacation1 = $record1['VACATION1'];
			$vacation2 = $record1['VACATION2'];
			$vacation3 = $record1['VACATION3']/2;
			$nonvacation1 = $record1['NONVACATION1'];
			$nonvacation2 = $record1['NONVACATION2'];
			$nonvacation3 = $record1['NONVACATION3']/2;
			$nonvacation4 = $record1['NONVACATION4'];
			$nonvacation5 = $record1['NONVACATION5'];
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$name?></td>
								<td><?=$position?></td>
								<td><?=$team?></td>
								<td><?=$vacation1?></td>
								<td><?=$vacation2?></td>
								<td><?=$vacation3?></td>
								<td><?=$nonvacation1?></td>
								<td><?=$nonvacation2+$nonvacation3?></td>
								<td><?=$nonvacation4?></td>
								<td><?=$nonvacation5?></td>
								<td><? echo $vacation1+$vacation2+$vacation3+$nonvacation1+$nonvacation2+$nonvacation3+$nonvacation4+$nonvacation5; ?></td>
							</tr>
<?
			$i--;
		}
	}
?>
					</tbody>					
				</table>

					<div class="page_num">
					<?=getPaging($total_cnt,$page,$per_page);?>
					</div>
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
