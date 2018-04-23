<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 

	$where = " AND PRF_ID IN (1,2,3,4,5) AND PRS_LOGIN <> 'admin'";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby . " END, PRS_JOIN, PRS_NAME";
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		//검색
		$("#team").change(function(){
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
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
								$selSQL = "SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) ORDER BY SORT";
								$selRs = sqlsrv_query($dbConn,$selSQL);

								while ($selRecord = sqlsrv_fetch_array($selRs))
								{
									$selTeam = $selRecord['TEAM'];
							?>
									<option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$selTeam?></option>
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
<? if ($p_team == "" || $p_team == "CEO" || $p_team == "df1" || strpos($p_team,"1실") == true) { ?>
				<table class="notable work_stats3 group" width="100%" id="df1">
					<thead>
						<tr>
							<th class="div">df 1 (<span id="df1_tot">00</span>)</th>
						</tr>
					<thead>
				</table>
<? } ?>				
<? if ($p_team == "" || $p_team == "CEO" || $p_team == "df1") { ?>
				<table class="notable work_stats3" width="100%" id="CEO">
					<thead>
						<tr>
							<th class="team">CEO (<span id="df1_ceo_cnt">00</span>)</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_NAME = '노진영' AND PRF_ID = 4";
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];
			$col_prs_extension = $record['PRS_EXTENSION'];
			$col_file_img = $record['FILE_IMG'];

			$df1_cnt['ceo']++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
<? } ?>
<?
	$teamSql = "SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM LIKE '%1실%' AND VIEW_YN = 'Y' ORDER BY SORT";
	$teamRs = sqlsrv_query($dbConn, $teamSql);
	while($teamRecord = sqlsrv_fetch_array($teamRs))
	{
		$col_team = $teamRecord['TEAM'];

		if ($p_team == "" || $p_team == "df1" || $p_team == $col_team) 
		{
			$i++;
?>
				<table class="notable work_stats3" width="100%" id="<?=$col_team?>">
					<thead>
						<tr>
							<th class="team" colspan="2"><?=$col_team?> (<span id="df1_team<?echo $i?>_cnt">00</span>)</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
<?
			$sql = "SELECT TOP 1
						PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
					FROM
						DF_PERSON WITH(NOLOCK)
					WHERE 
						PRS_TEAM = '$col_team' $where
					$orderbycase";
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);

			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];
			$col_prs_extension = $record['PRS_EXTENSION'];
			$col_file_img = $record['FILE_IMG'];

			$df1_cnt[$col_team]++;
?>
							<td class="leader">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
							<td class="list1">
								<ul>
<?
			$sql = "SELECT
						PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
					FROM
					(
						SELECT 
							ROW_NUMBER() OVER($orderbycase) AS ROWNUM, 
							PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE 
						PRS_TEAM = '$col_team' $where
					) T
					WHERE
						T.ROWNUM BETWEEN 2 AND 20";
			$rs = sqlsrv_query($dbConn, $sql);

			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];

				$df1_cnt[$col_team]++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
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
		}
	}
?>
				<!-- DF1 인원 카운트 -->
				<script type="text/javascript">
					var cnt1 = "<?echo $df1_cnt['ceo']?>";
					var cnt2 = "<?echo $df1_cnt['커뮤니케이션전략기획1실']?>";
					var cnt3 = "<?echo $df1_cnt['디자인1실']?>";
					var cnt4 = "<?echo $df1_cnt['모션그래픽스1실']?>";
					var cnt5 = "<?echo $df1_cnt['비주얼 인터랙션 디벨롭먼트 1실']?>";
					var tot1 = parseInt(cnt1) + parseInt(cnt2) + parseInt(cnt3) + parseInt(cnt4) + parseInt(cnt5);

					$("#df1_tot").text(tot1);
					$("#df1_ceo_cnt").text(cnt1);
					$("#df1_team1_cnt").text(cnt2);
					$("#df1_team2_cnt").text(cnt3);
					$("#df1_team3_cnt").text(cnt4);
					$("#df1_team4_cnt").text(cnt5);
				</script>

			</div>
			<div class="tables">
<? if ($p_team == "" || $p_team == "CEO" || $p_team == "df2" || $p_team == "CCO" || $p_team == "CSO" || strpos($p_team,"2실") == true) { ?>
				<table class="notable work_stats3 group" width="100%" id="df2">
					<thead>
						<tr>
							<th class="div">df 2 (<span id="df2_tot">00</span>)</th>
						</tr>
					<thead>
				</table>
<? } ?>
<? if ($p_team == "" || $p_team == "CEO" || $p_team == "df2") { ?>
				<table class="notable work_stats3" width="100%" id="CEO">
					<thead>
						<tr>
							<th class="team">CEO (<span id="df2_ceo_cnt">00</span>)</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_NAME IN ('박재형','최동현') AND PRF_ID = 4";
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];
			$col_prs_extension = $record['PRS_EXTENSION'];
			$col_file_img = $record['FILE_IMG'];

			$df2_cnt['ceo']++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
<? } ?>
<? if ($p_team == "" || $p_team == "df2" || $p_team == "CCO" || $p_team == "CSO") { ?>
				<table class="notable work_stats3"  width="100%" id="coo">
					<thead>
						<tr class="plural">
							<th class="teamname team">CCO (<span id="df2_cco_cnt">00</span>)</th>
							<th class="team">CSO (<span id="df2_cso_cnt">00</span>)</th>
						</tr>
					</thead>
					<tbody> 
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CCO'";
		$rs = sqlsrv_query($dbConn, $sql);
		$record = sqlsrv_fetch_array($rs);
		$col_prs_id = $record['PRS_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_position = $record['PRS_POSITION'];
		$col_prs_extension = $record['PRS_EXTENSION'];
		$col_file_img = $record['FILE_IMG'];
		
		$df2_cnt['cco']++;
?>
						<tr>
							<td class="leader">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CSO'";
		$rs = sqlsrv_query($dbConn, $sql);
		$record = sqlsrv_fetch_array($rs);
		$col_prs_id = $record['PRS_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_position = $record['PRS_POSITION'];
		$col_prs_extension = $record['PRS_EXTENSION'];
		$col_file_img = $record['FILE_IMG'];

		$df2_cnt['cso']++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
<? } ?>

<?
	$teamSql = "SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM LIKE '%2실%' AND VIEW_YN = 'Y' ORDER BY SORT";
	$teamRs = sqlsrv_query($dbConn, $teamSql);
	while($teamRecord = sqlsrv_fetch_array($teamRs))
	{
		$col_team = $teamRecord['TEAM'];

		if ($p_team == "" || $p_team == "df2" || $p_team == $col_team) 
		{
			$j++;
?>
				<table class="notable work_stats3" width="100%" id="<?=$col_team?>">
					<thead>
						<tr>
							<th class="team" colspan="2"><?=$col_team?> (<span id="df2_team<?echo $j?>_cnt">00</span>)</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
<?
			$sql = "SELECT TOP 1
						PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
					FROM
						DF_PERSON WITH(NOLOCK)
					WHERE 
						PRS_TEAM = '$col_team' $where
					$orderbycase";
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);

			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];
			$col_prs_extension = $record['PRS_EXTENSION'];
			$col_file_img = $record['FILE_IMG'];

			$df2_cnt[$col_team]++;
?>
							<td class="leader">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
							<td class="list1">
								<ul>
<?
			$sql = "SELECT
						PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
					FROM
					(
						SELECT 
							ROW_NUMBER() OVER($orderbycase) AS ROWNUM, 
							PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE 
						PRS_TEAM = '$col_team' $where
					) T
					WHERE
						T.ROWNUM BETWEEN 2 AND 20";
			$rs = sqlsrv_query($dbConn, $sql);

			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];

				$df2_cnt[$col_team]++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
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
		}
	}
?>
				<!-- DF2 인원 카운트 -->
				<script type="text/javascript">
					var cnt1 = "<?echo $df2_cnt['ceo']?>";
					var cnt2 = "<?echo $df2_cnt['cco']?>";
					var cnt3 = "<?echo $df2_cnt['cso']?>";
					var cnt4 = "<?echo $df2_cnt['커뮤니케이션전략기획2실']?>";
					var cnt5 = "<?echo $df2_cnt['디자인2실 1팀']?>";
					var cnt6 = "<?echo $df2_cnt['디자인2실 2팀']?>";
					var cnt7 = "<?echo $df2_cnt['모션그래픽스2실']?>";
					var cnt8 = "<?echo $df2_cnt['비주얼 인터랙션 디벨롭먼트 2실']?>";
					var tot2 = parseInt(cnt1) + parseInt(cnt2) + parseInt(cnt3) + parseInt(cnt4) + parseInt(cnt5) + parseInt(cnt6) + parseInt(cnt7) + parseInt(cnt8);

					$("#df2_tot").text(tot2);
					$("#df2_ceo_cnt").text(cnt1);
					$("#df2_cco_cnt").text(cnt2);
					$("#df2_cso_cnt").text(cnt3);
					$("#df2_team1_cnt").text(cnt4);
					$("#df2_team2_cnt").text(cnt5);
					$("#df2_team3_cnt").text(cnt6);
					$("#df2_team4_cnt").text(cnt7);
					$("#df2_team5_cnt").text(cnt8);
				</script>

			</div>
			<div class="tables">
<? if ($p_team == "" || $p_team == "bst" || $p_team == "경영지원팀") { ?>
				<table class="notable work_stats3 group" width="100%" id="bst">
					<thead>
						<tr>
							<th class="div">df BST (<span id="bst_tot">00</span>)</th>
						</tr>
					<thead>
				</table>
				<table class="notable work_stats3" width="100%" id="경영지원팀">
					<thead>
						<tr>
							<th class="team" colspan="2">경영지원팀 (<span id="bst_team1_cnt">00</span>)</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
<?
			$sql = "SELECT TOP 1
						PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
					FROM
						DF_PERSON WITH(NOLOCK)
					WHERE 
						PRS_TEAM = '경영지원팀' $where
					$orderbycase";
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);

			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];
			$col_prs_extension = $record['PRS_EXTENSION'];
			$col_file_img = $record['FILE_IMG'];
			
			$bst_cnt['경영지원팀']++;
?>
							<td class="leader">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
							<td class="list1">
								<ul>
<?
			$sql = "SELECT
						PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
					FROM
					(
						SELECT 
							ROW_NUMBER() OVER($orderbycase) AS ROWNUM, 
							PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE 
						PRS_TEAM = '경영지원팀' $where
					) T
					WHERE
						T.ROWNUM BETWEEN 2 AND 20";
			$rs = sqlsrv_query($dbConn, $sql);

			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];

				$bst_cnt['경영지원팀']++;
?>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
<?
			}
?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
<? } ?>
				<!-- BST 인원 카운트 -->
				<script type="text/javascript">
					var cnt1 = "<?echo $bst_cnt['경영지원팀']?>";
					var tot3 = parseInt(cnt1);

					$("#bst_tot").text(tot3);
					$("#bst_team1_cnt").text(cnt1);
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
						<th>직급</th>
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