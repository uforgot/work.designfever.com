<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//팀선택 셀렉트박스 관련
	if ($prs_position == '팀장' || $prs_position_tmp == '팀장')
	{
		$cur_team = $prs_team; //셀렉트박스 기본선택
		$sel_view = 'N';	   //셀렉트박스 노출여부
		$sel_sql = "";		   //셀렉트박스 리스트
	}
	else if (in_array($prs_position,array('차장','실장')))
	{
		$sql = "SELECT 
					A.SEQNO, (SELECT TOP 1 TEAM FROM DF_TEAM_CODE WHERE R_SEQNO = A.SEQNO ORDER BY SORT) DEF_TEAM 
				FROM 
					DF_TEAM_CODE A 
				WHERE 
					A.TEAM = '$prs_team'";
		$rs = sqlsrv_query($dbConn,$sql);
		$record = sqlsrv_fetch_array($rs);

		$cur_team = $record['DEF_TEAM'];
		$sel_view = 'Y';

		//[예외처리] 개발팀을 Interactive Lab에 포함
 		//if($prs_team == "Interactive Lab") {
		//	$sub_sql = " OR SEQNO = 18 OR R_SEQNO = 18"; 
		//}

		$sel_sql = "SELECT 
						STEP, TEAM 
					FROM 
						DF_TEAM_CODE 
					WHERE 
						SEQNO = ".$record['SEQNO']." OR R_SEQNO = ".$record['SEQNO']." $sub_sql
					ORDER BY SORT";
	}
	else if (in_array($prs_position,array('수석','부장','이사','대표')))
	{
		$cur_team = '경영지원팀'; 
		$sel_view = 'Y';
		$sel_sql = "SELECT 
						STEP, TEAM 
					FROM 
						DF_TEAM_CODE WITH(NOLOCK) 
					WHERE 
						SEQNO NOT IN(2,3,4,5) 
					ORDER BY SORT";
	}
	
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y'); 
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : $cur_team; 

	$searchSQL = " WEEK_ORD LIKE '$year%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM = '$team')";

	$sql = "SELECT 
				COUNT(DISTINCT WEEK_ORD) 
			FROM 
				DF_WEEKLY WITH(NOLOCK) 
			WHERE". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 30;

	$sql = "SELECT 
				T.WEEK_ORD, T.WEEK_ORD_TOT, T.TITLE, T.PRS_TEAM, T.COMPLETE_YN
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) AS ROWNUM, 
					WEEK_ORD, WEEK_ORD_TOT, TITLE, PRS_TEAM, COMPLETE_YN
				FROM 
					DF_WEEKLY WITH(NOLOCK)
				WHERE". $searchSQL." 
				GROUP BY
					WEEK_ORD, WEEK_ORD_TOT, TITLE, PRS_TEAM, COMPLETE_YN
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function yearSearch(val) {
		document.location.href = "./weekly_list_team.php?year=" + val + "&team=<?=$team?>";
	}

	function teamSearch(val) {
		if (!val)
		{
			alert('보고서 조회는 팀별로 가능합니다.\n팀명을 선택해 주세요!');
			return;
		}
		
		document.location.href = "./weekly_list_team.php?year=<?=$year?>&team=" + val;
	}
</script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="year" id="year" value="<?=$year?>">
<input type="hidden" name="team" id="team" value="<?=$team?>">

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/weekly_menu.php"; ?>

			<div class="work_wrap clearfix">

				<div class="vacation_stats clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row">&nbsp;</th>
<!-- 							<th width="50%" scope="row">팀원 주간보고서</th> -->
							<td>
								<select name="year" style="width:109px;" onchange="javascript:yearSearch(this.value);">
									<?
										for ($i=2014; $i<=date("Y"); $i++) 
										{
											if ($i == $year) 
											{ 
												$selected = " selected"; 
											}
											else
											{
												$selected = "";
											}
									?>
											<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
									<?
										}
									?>
								</select><span>년</span>

								<?
									if ($sel_view == 'Y') 
									{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<select name="team" style="width:200px;" onchange="javascript:teamSearch(this.value);">
								<?
									$selSQL = $sel_sql;
									$selRs = sqlsrv_query($dbConn,$selSQL);

									while ($selRecord = sqlsrv_fetch_array($selRs))
									{
										$selStep = $selRecord['STEP'];
										$selTeam = $selRecord['TEAM'];

										if ($selStep == 3 || ($selTeam == '경영지원팀' || $selTeam == '홍보팀' || $selTeam == 'brand experience team'))
										{
											$selTeam1 = $selRecord['TEAM'];
										}  
										else
										{
											$selTeam1 = "";
										}

										$blank = "";
										for ($i=0;$i<=$selStep;$i++)
										{
											$blank .= "&nbsp;&nbsp;&nbsp;";
										}

										if ($selTeam == $team) 
										{ 
											$selected = " selected"; 
										}
										else
										{
											$selected = "";
										}
								?>
										<option value="<?=$selTeam1?>" <?=$selected?>><?=$blank?><?=$selTeam?></option>
								<?
									}
								?>
								</select>
								<?
									}
								?>
							</td>
						</tr>
					</table>
				</div>


				<table class="vacation notable work1 work_stats" width="100%" style="margin-bottom:10px;">
					<caption>팀원 주간보고서 테이블</caption>
					<colgroup>
						<col width="5%" />
						<col width="30%" />
						<col width="*" />
						<col width="10%" />
					</colgroup>

					<thead>
						<tr>
							<th>주차</th>
							<th>제목</th>
							<th>팀원</th>
							<th>상태</th>
						</tr>
					</thead>

					<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="6" class="bold">해당 정보가 없습니다.</td>
							</tr>
<?
	}
	else
	{
		while ($record = sqlsrv_fetch_array($rs))
		{
			$ord_tot = $record['WEEK_ORD_TOT'];
			$ord = $record['WEEK_ORD'];
			$comp_yn = $record['COMPLETE_YN'];
			
			//실장급 이상, 보고서 취합 링크
			if (in_array($prs_position,$positionA_arr))
			{
				$title = "<a href='weekly_list_division.php?week=".$ord."&team=".$team."' target='_blank'>".$record['TITLE']."</a>";
			} else {
				$title = "<a href='weekly_list_division.php?week=".$ord."&team=".$team."' target='_blank'>".$record['TITLE']."</a>";
				//$title = $record['TITLE'];
			}

			if ($comp_yn == 'Y')		$state = "완료";
			else if ($comp_yn == 'N')	$state = "작성중";

			//주간보고 등록한 팀원 추출
			$searchSQL = " WHERE WEEK_ORD = '$ord' AND REG_DATE IS NOT NULL AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM = '$team')";
			//$searchSQL = " WHERE WEEK_ORD = '$ord' AND PRS_TEAM = '$team'";

			$orderbycase = "ORDER BY CASE WHEN PRS_POSITION='대표' THEN 1 WHEN PRS_POSITION='이사' THEN 2 WHEN PRS_POSITION='부장' THEN 3 WHEN PRS_POSITION='수석' THEN 4 WHEN PRS_POSITION='실장' THEN 5 WHEN PRS_POSITION='차장' THEN 6 WHEN PRS_POSITION='팀장' THEN 7 WHEN PRS_POSITION='과장' THEN 8 WHEN PRS_POSITION='책임' THEN 9 WHEN PRS_POSITION='대리' THEN 10 WHEN PRS_POSITION='선임' THEN 11 WHEN PRS_POSITION='주임' THEN 12 WHEN PRS_POSITION='사원' THEN 13 WHEN PRS_POSITION='인턴' THEN 14 END, PRS_NAME";

			$per_sql = "SELECT 
							SEQNO, PRS_NAME 
					   FROM 
							DF_WEEKLY WITH(NOLOCK)
					   $searchSQL
					   $orderbycase";								
			$per_rs = sqlsrv_query($dbConn,$per_sql);

			$per_list = "";
			while ($per_record = sqlsrv_fetch_array($per_rs))
			{
				$per_seqno = $per_record['SEQNO'];	
				$per_name = $per_record['PRS_NAME'];	
				$per_list .= "<a href='weekly_write.php?type=modify&seqno=$per_seqno&win=new' target='_blank'>".$per_name."</a>&nbsp;&nbsp;";
			}
?>
							<!-- loop -->		
							<tr>
								<td><?=$ord_tot?></td>
								<td><?=$title?></td>
								<td style="text-align:left;"><?=$per_list?></td>
								<td><?=$state?></td>
							</tr>
							<!-- loop -->		
<?
			$i--;
		}
	}
?>
					</tbody>
					<tfoot>

					</tfoot>					
				</table>
				<span style="padding-left:40px;">
					<b class="txt_left_p" style="margin-bottom:0px;">* 주간보고를 작성하지 않은 팀원은 목록에 나타나지 않습니다.</b>
				</span>

				<div class="page_num">
				<?=getPaging($total_cnt,$page,$per_page);?>
				</div>	

			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
