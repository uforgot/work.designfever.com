<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 

	$where = " AND PRF_ID IN (1,2,3,4,5)";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION2_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_POSITION2 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION1_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION1 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, PRS_NAME";
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		//검색
		$("#team").change(function(){
			var team = $("#team").val().replace(/ /g,'');

			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>#"+team); 
			$("#form").submit();
		});
		//상세정보
		$("[name=person]").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","hdnFrame");
			$("#form").attr("action","person_detail.php?id="+$(this).attr("value")); 
			$("#form").submit();
			$("#popup").attr("style","display:inline;");
		//	alert($(this).children("input").val());
		});
		//상세정보 닫기
		$("[name=btnClose]").attr("style","cursor:pointer;").click(function(){
			$("#popup").attr("style","display:none;");
			
			$("#pop_img").empty();
			$("#pop_id").empty();
			$("#pop_name").empty();
			$("#pop_birth").empty();
			$("#pop_tel").empty();
			$("#pop_team").empty();
			$("#pop_position").empty();
			$("#pop_email").empty();
			$("#pop_extension").empty();
		});
		//엑셀 다운로드
		$("#btnExcel").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","hdnFrame");
			$("#form").attr("action","excel_person.php"); 
			$("#form").submit();
		});
	});
-->
</script>
</head>
<body>
<div class="wrapper">
<form method="post" name="form" id="form">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/org_menu.php"; ?>

			<div class="work_wrap clearfix">
			
				<div class="work_stats_search clearfix">
				
					<table class="notable" width="100%">
						<tr>
							<td>
								<select name="team" id="team">
									<option value=""<? if ($p_team == ""){ echo " selected"; } ?>>전직원</option>
							<?
								$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) ORDER BY SORT";
								$selRs = sqlsrv_query($dbConn,$selSQL);

								while ($selRecord = sqlsrv_fetch_array($selRs))
								{
									$selStep = $selRecord['STEP'];
									$selTeam = $selRecord['TEAM'];
									
									if ($selStep == 1) {
										$selTeam2 = $selTeam;
									}
									else if ($selStep == 2) {
										$selTeam2 = "&nbsp;&nbsp;└ ". $selTeam;
									}
									else if ($selStep == 3) {
										$selTeam2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└ ". $selTeam;
									}
							?>
									<option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
							<?
								}
							?>
								</select>
							</td>
							<td align="right">
							<? if ($prf_id == "4") { ?>
								<img src="../img/btn_excell.gif" alt="엑셀다운로드" id="btnExcel" class="btn_right" />
							<? } ?>
							</td>
						</tr>
					</table>		
				</div>
			<div class="tables">
		<a name="CEO">
				<table class="notable work_stats3 group" width="100%">
					<thead>
						<tr>
							<th class="div" style="border-bottom-width:1px; border-bottom-style:solid; border-bottom-color: #b2b2b2;">CEO (<span id="df_ceo_cnt">00</span>)</th>
						</tr>
					<thead>
					<tbody> 
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_POSITION2 = '대표' AND PRF_ID = 4";
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position1 = $record['PRS_POSITION1'];
			$col_prs_position2 = $record['PRS_POSITION2'];
			$col_prs_extension = $record['PRS_EXTENSION'];
			$col_file_img = $record['FILE_IMG'];

			$df_cnt['ceo']++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_name?></span><br>대표<br>(내선 <?=$col_prs_extension?>)
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
<?
	$divSql = "SELECT SEQNO, STEP, TEAM, (SELECT COUNT(*) FROM DF_TEAM_2018 WHERE R_SEQNO = A.SEQNO) AS CNT FROM DF_TEAM_2018 A WITH(NOLOCK) WHERE STEP <= 2 AND TEAM NOT IN ('CEO') ORDER BY SORT";
	$divRs = sqlsrv_query($dbConn, $divSql);
	
	$i = 0;

	while($divRecord = sqlsrv_fetch_array($divRs))
	{
		$div_seqno = $divRecord['SEQNO'];
		$div_step = $divRecord['STEP'];
		$div_team = $divRecord['TEAM'];
		$div_team_cnt = $divRecord['CNT'];

		$div_team2 = str_replace(" ","",$div_team);
?>
		<a name="<?=$div_team2?>">
<?
		if ($div_step == 1) 
		{
			$i++;
			$group_cnt[$i] = 0;
?>
				<table class="notable work_stats3 group" width="100%" id="<?=$div_team?>">
					<thead>
						<tr>
							<th class="div"><?=$div_team?> (<span id="df_group<?=$i?>_cnt">00</span>)</th>
						</tr>
					<thead>
				</table>
<?
		}
		else 
		{
			if ($div_team == "Design 1 Division")
			{
?>
				<table class="notable work_stats3" width="100%" id="<?=$div_team?>">
					<thead>
						<tr>
							<th class="team2" colspan="3"><?=$div_team?> (<span id="df_<?=$div_team2?>_cnt">00</span>)</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
<?
				$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CEO' AND PRS_NAME = '박재형'";
				$rs = sqlsrv_query($dbConn, $sql);

				$record = sqlsrv_fetch_array($rs);
					
				$div_prs_id = $record['PRS_ID'];
				$div_prs_name = $record['PRS_NAME'];
				$div_prs_position1 = $record['PRS_POSITION1'];
				$div_prs_position2 = $record['PRS_POSITION2'];
				$div_prs_extension = $record['PRS_EXTENSION'];
				$div_file_img = $record['FILE_IMG'];

				if ($div_prs_position1 == $div_prs_position2)
				{
					$div_prs_position = $div_prs_position2;
				}
				else
				{
					$div_prs_position = $div_prs_position2 ." / ". $div_prs_position1;
				}
?>

							<td class="leader">
								<ul>
									<li>
										<?=getProfileImg($div_file_img,78,'person',$div_prs_id);?>
										<br><br><span><?=$div_prs_name?></span><br><?=$div_prs_position?><br>(내선 <?=$div_prs_extension?>)
									</li>
								</ul>
							</td>
<?
				if ($div_team_cnt == 0)
				{
?>
							<td class="list1">
								<ul>
<?
					$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '". $div_team ."' $where AND PRS_POSITION2 = '매니저'". $orderbycase;
					$rs = sqlsrv_query($dbConn, $sql);

					While ($record = sqlsrv_fetch_array($rs))
					{
						$col_prs_id = $record['PRS_ID'];
						$col_prs_name = $record['PRS_NAME'];
						$col_prs_position1 = $record['PRS_POSITION1'];
						$col_prs_position2 = $record['PRS_POSITION2'];
						$col_prs_extension = $record['PRS_EXTENSION'];
						$col_file_img = $record['FILE_IMG'];

						if ($col_prs_position1 == $col_prs_position2)
						{
							$col_prs_position = $col_prs_position2;
						}
						else
						{
							$col_prs_position = $col_prs_position2 ." / ". $col_prs_position1;
						}

						$df_cnt[$div_team2]++;
						$group_cnt[$i]++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_name?></span><br><?=$col_prs_position?><br>(내선 <?=$col_prs_extension?>)
									</li>
<?
					}
?>								
								</ul>
							</td>
<?
				}
				else
				{
?>
							<td>
<?
					$teamSql = "SELECT SEQNO, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE STEP = 3 AND R_SEQNO = $div_seqno ORDER BY SORT";
					$teamRs = sqlsrv_query($dbConn, $teamSql);
					
					$j = 0;
					while($teamRecord = sqlsrv_fetch_array($teamRs))
					{
						$team_seqno = $teamRecord['SEQNO'];
						$team_step = $teamRecord['STEP'];
						$team_team = $teamRecord['TEAM'];
						$team_cnt = $teamRecord['CNT'];

						$team_team2 = str_replace(" ","",$team_team);
?>
							<a name="<?=$team_team2?>">
							<table class="notable work_stats3" width="100%" id="<?=$team_team?>">
								<thead>
									<tr>
										<th class="team" colspan="2"<? if ($j ==0) { ?> style="border-top-style:hidden;"<? } ?>><?=$team_team?> (<span id="df_<?=$team_team2?>_cnt">00</span>)</td>
									</tr>
								</thead>
								<tbody> 
									<tr>
<?
						$sql = "SELECT TOP 1 PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '". $team_team ."' AND PRS_POSITION2 IN ('실장','팀장')". $orderbycase;
						$rs = sqlsrv_query($dbConn, $sql);

						$record = sqlsrv_fetch_array($rs);
							
						$col_prs_id = $record['PRS_ID'];
						$col_prs_name = $record['PRS_NAME'];
						$col_prs_position1 = $record['PRS_POSITION1'];
						$col_prs_position2 = $record['PRS_POSITION2'];
						$col_prs_extension = $record['PRS_EXTENSION'];
						$col_file_img = $record['FILE_IMG'];

						if ($col_prs_position1 == $col_prs_position2)
						{
							$col_prs_position = $col_prs_position2;
						}
						else
						{
							$col_prs_position = $col_prs_position2 ." / ". $col_prs_position1;
						}

						$df_cnt[$div_team2]++;
						$df_cnt[$team_team2]++;
						$group_cnt[$i]++;
?>

										<td class="leader">
											<ul>
												<li>
													<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
													<br><br><span><?=$col_prs_name?></span><br><?=$col_prs_position?><br>(내선 <?=$col_prs_extension?>)
												</li>
											</ul>
										</td>
										<td class="list1">
											<ul>
<?
						$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '". $team_team ."' $where AND PRS_POSITION2 = '매니저'". $orderbycase;
						$rs = sqlsrv_query($dbConn, $sql);

						While ($record = sqlsrv_fetch_array($rs))
						{
							$col_prs_id = $record['PRS_ID'];
							$col_prs_name = $record['PRS_NAME'];
							$col_prs_position1 = $record['PRS_POSITION1'];
							$col_prs_position2 = $record['PRS_POSITION2'];
							$col_prs_extension = $record['PRS_EXTENSION'];
							$col_file_img = $record['FILE_IMG'];

							if ($col_prs_position1 == $col_prs_position2)
							{
								$col_prs_position = $col_prs_position2;
							}
							else
							{
								$col_prs_position = $col_prs_position2 ." / ". $col_prs_position1;
							}

							$df_cnt[$div_team2]++;
							$df_cnt[$team_team2]++;
							$group_cnt[$i]++;
?>
												<li>
													<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
													<br><br><span><?=$col_prs_name?></span><br><?=$col_prs_position?><br>(내선 <?=$col_prs_extension?>)
												</li>
<?
						}
?>								
											</ul>
										</td>
									</tr>
							</table>
<?
						$j++;
					}
?>
							</td>
<?
				}
			}
			else
			{
?>
				<table class="notable work_stats3" width="100%" id="<?=$div_team?>">
					<thead>
						<tr>
							<th class="team2" colspan="3"><?=$div_team?> (<span id="df_<?=$div_team2?>_cnt">00</span>)</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
<?
				$sql = "SELECT TOP 1 PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '". $div_team ."'". $orderbycase;
				$rs = sqlsrv_query($dbConn, $sql);

				$record = sqlsrv_fetch_array($rs);
					
				$div_prs_id = $record['PRS_ID'];
				$div_prs_name = $record['PRS_NAME'];
				$div_prs_position1 = $record['PRS_POSITION1'];
				$div_prs_position2 = $record['PRS_POSITION2'];
				$div_prs_extension = $record['PRS_EXTENSION'];
				$div_file_img = $record['FILE_IMG'];

				if ($div_prs_position1 == $div_prs_position2)
				{
					$div_prs_position = $div_prs_position2;
				}
				else
				{
					$div_prs_position = $div_prs_position2 ." / ". $div_prs_position1;
				}

				$df_cnt[$div_team2]++;
				$group_cnt[$i]++;
?>

							<td class="leader">
								<ul>
									<li>
										<?=getProfileImg($div_file_img,78,'person',$div_prs_id);?>
										<br><br><span><?=$div_prs_name?></span><br><?=$div_prs_position?><br>(내선 <?=$div_prs_extension?>)
									</li>
								</ul>
							</td>
<?
				if ($div_team_cnt == 0)
				{
?>
							<td class="list1">
								<ul>
<?
					$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '". $div_team ."' $where AND PRS_POSITION2 = '매니저'". $orderbycase;
					$rs = sqlsrv_query($dbConn, $sql);

					While ($record = sqlsrv_fetch_array($rs))
					{
						$col_prs_id = $record['PRS_ID'];
						$col_prs_name = $record['PRS_NAME'];
						$col_prs_position1 = $record['PRS_POSITION1'];
						$col_prs_position2 = $record['PRS_POSITION2'];
						$col_prs_extension = $record['PRS_EXTENSION'];
						$col_file_img = $record['FILE_IMG'];

						if ($col_prs_position1 == $col_prs_position2)
						{
							$col_prs_position = $col_prs_position2;
						}
						else
						{
							$col_prs_position = $col_prs_position2 ." / ". $col_prs_position1;
						}

						$df_cnt[$div_team2]++;
						$group_cnt[$i]++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_name?></span><br><?=$col_prs_position?><br>(내선 <?=$col_prs_extension?>)
									</li>
<?
					}
?>								
								</ul>
							</td>
<?
				}
				else
				{
?>
							<td>
<?
					$teamSql = "SELECT SEQNO, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE STEP = 3 AND R_SEQNO = $div_seqno ORDER BY SORT";
					$teamRs = sqlsrv_query($dbConn, $teamSql);
					
					$j = 0;
					while($teamRecord = sqlsrv_fetch_array($teamRs))
					{
						$team_seqno = $teamRecord['SEQNO'];
						$team_step = $teamRecord['STEP'];
						$team_team = $teamRecord['TEAM'];
						$team_cnt = $teamRecord['CNT'];

						$team_team2 = str_replace(" ","",$team_team);
?>
							<a name="<?=$team_team2?>">
							<table class="notable work_stats3" width="100%" id="<?=$team_team?>">
								<thead>
									<tr>
										<th class="team" colspan="2"<? if ($j ==0) { ?> style="border-top-style:hidden;"<? } ?>><?=$team_team?> (<span id="df_<?=$team_team2?>_cnt">00</span>)</td>
									</tr>
								</thead>
								<tbody> 
									<tr>
<?
						$sql = "SELECT TOP 1 PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '". $team_team ."' AND PRS_POSITION2 IN ('실장','팀장')". $orderbycase;
						$rs = sqlsrv_query($dbConn, $sql);

						$record = sqlsrv_fetch_array($rs);
							
						$col_prs_id = $record['PRS_ID'];
						$col_prs_name = $record['PRS_NAME'];
						$col_prs_position1 = $record['PRS_POSITION1'];
						$col_prs_position2 = $record['PRS_POSITION2'];
						$col_prs_extension = $record['PRS_EXTENSION'];
						$col_file_img = $record['FILE_IMG'];

						if ($col_prs_position1 == $col_prs_position2)
						{
							$col_prs_position = $col_prs_position2;
						}
						else
						{
							$col_prs_position = $col_prs_position2 ." / ". $col_prs_position1;
						}

						$df_cnt[$div_team2]++;
						$df_cnt[$team_team2]++;
						$group_cnt[$i]++;
?>

										<td class="leader">
											<ul>
												<li>
													<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
													<br><br><span><?=$col_prs_name?></span><br><?=$col_prs_position?><br>(내선 <?=$col_prs_extension?>)
												</li>
											</ul>
										</td>
										<td class="list1">
											<ul>
<?
						$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '". $team_team ."' $where AND PRS_POSITION2 = '매니저'". $orderbycase;
						$rs = sqlsrv_query($dbConn, $sql);

						While ($record = sqlsrv_fetch_array($rs))
						{
							$col_prs_id = $record['PRS_ID'];
							$col_prs_name = $record['PRS_NAME'];
							$col_prs_position1 = $record['PRS_POSITION1'];
							$col_prs_position2 = $record['PRS_POSITION2'];
							$col_prs_extension = $record['PRS_EXTENSION'];
							$col_file_img = $record['FILE_IMG'];

							if ($col_prs_position1 == $col_prs_position2)
							{
								$col_prs_position = $col_prs_position2;
							}
							else
							{
								$col_prs_position = $col_prs_position2 ." / ". $col_prs_position1;
							}

							$df_cnt[$div_team2]++;
							$df_cnt[$team_team2]++;
							$group_cnt[$i]++;
?>
												<li>
													<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
													<br><br><span><?=$col_prs_name?></span><br><?=$col_prs_position?><br>(내선 <?=$col_prs_extension?>)
												</li>
<?
						}
?>								
											</ul>
										</td>
									</tr>
							</table>
<?
						$j++;
					}
?>
							</td>
<?
				}
			}
?>
						</tr>
					</tbody>
				</table>
<?
		}
	}
?>
				<script type="text/javascript">
					$("#df_ceo_cnt").text("<?=$df_cnt['ceo']?>");	
				<?
					$teamSql = "SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) ORDER BY SORT";
					$teamRs = sqlsrv_query($dbConn, $teamSql);
					
					$i = 0;

					while($teamRecord = sqlsrv_fetch_array($teamRs))
					{
						$col_team = $teamRecord['TEAM'];
						$col_team2 = str_replace(" ","",$col_team);
				?>
					$("#df_<?=$col_team2?>_cnt").text("<?=$df_cnt[$col_team2]?>");	
				<?
					}
					$cnt1 = $df_cnt['CreativePlanningDivision'] + $df_cnt['CreativePlanning1Team'] + $df_cnt['CreativePlanning2Team'];
					$cnt2 = $df_cnt['Design1Division'] + $df_cnt['Design1Division1Team'];
					$cnt3 = $df_cnt['Design2Division'] + $df_cnt['Design2Division1Team'] + $df_cnt['Design2Division2Team'];
					$cnt4 = $df_cnt['MotionDivision'] + $df_cnt['Motion1Team'];
					$cnt5 = $df_cnt['VisualInteractionDevelopment'] + $df_cnt['VID1Team'] + $df_cnt['VID2Team'];
				?>
				<?
					for ($i=1; $i<=5; $i++)
					{
				?>
					$("#df_group<?=$i?>_cnt").text("<?=$group_cnt[$i]?>");	
				<?
					}
				?>
				</script>

				<table class="notable work_stats3" width="100%" id="경비실">
					<thead>
						<tr>
							<th class="team">경비실</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
							<td class="list1">
								<ul>
									<li>
										소장님 (내선 313)
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>

			</div>

				<table class="notable work_stats3" width="100%">
					<tbody> 
						<tr class="plural">
							<th class="team" style="border-bottom-style:hidden;"></th>
						</tr>
					</tbody>
				</table>
			</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>

<div class="person_pop_detail" id="popup" style="display:none;">

	<div class="person_pop work_team_pop" style="border:0px; margin-top:-170px;">
		<div class="pop_top">
			<p class="pop_title">개인 프로필</p>
			<span id="btnClose1" class="close" name="btnClose">닫기</span>
		</div>
		<div class="pop_body">
			<div class="prs_wrap">
				<table class="notable prs_table"  width="100%">
					<summary></summary>
					<colgroup><col width="25%" /><col width="20%" /><col width="*" /></colgroup>
					<tr>
						<td rowspan="9" class="img" id="pop_img"></th>
						<th>아이디</th>
						<td id="pop_id"></td>
					</tr>
					
					<tr>
						<th>이름</th>
						<td id="pop_name"></td>
					</tr>
					
					<tr>
						<th>생일</th>
						<td id="pop_birth"></td>
					</tr>

					<tr>
						<th>핸드폰</th>
						<td colspan="2" id="pop_mobile"></td>
					</tr>
					
					<tr>
						<th>소속부서</th>
						<td colspan="2" id="pop_team"></td>
					</tr>
					
					<tr>
						<th>직책 / 직급</th>
						<td colspan="2" id="pop_position"></td>
					</tr>

					<tr>
						<th>DF E-mail</th>
						<td colspan="2" id="pop_email"></td>
					</tr>

					<tr>
						<th>직통번호</th>
						<td colspan="2" id="pop_tel"></td>
					</tr>

					<tr class="last">
						<th>내선번호</th>
						<td colspan="2" id="pop_extension"></td>
					</tr>
				</table>
				
			</div>
			<div class="prs_btn">
				<img src="../img/btn_ok.gif" alt="ok" id="btnClose2" name="btnClose" />
			</div>
		</div>
	</div>

</div>
</div>
</body>
</html>