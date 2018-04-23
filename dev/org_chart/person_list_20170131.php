<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 

	$where = " AND PRF_ID IN (1,2,3,4)";

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
<? if ($p_team == "" || $p_team == "CEO") { ?>
				<table class="notable work_stats3"  width="100%" id="ceo">
					<thead>
						<tr>
							<th class="div">CEO</th>
						</tr>
					</thead>
					<tbody> 
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CEO'";
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];
			$col_prs_extension = $record['PRS_EXTENSION'];
			$col_file_img = $record['FILE_IMG'];
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
<? if ($p_team == "" || $p_team == "CCO" || $p_team == "CSO") { ?>
				<table class="notable work_stats3"  width="100%" id="coo">
					<thead>
						<tr class="plural">
							<th class="teamname team" style="background:#e8e8e8;">CCO</th>
							<th class="team" style="background:#e8e8e8;">CSO</th>
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
	$teamSql = "SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
	$teamRs = sqlsrv_query($dbConn, $teamSql);
	while($teamRecord = sqlsrv_fetch_array($teamRs))
	{
		$col_team = $teamRecord['TEAM'];

		if ($p_team == "" || $p_team == $col_team) 
		{
?>
				<table class="notable work_stats3" width="100%" id="<?=$col_team?>">
					<thead>
						<tr>
							<th class="div" colspan="2"><?=$col_team?></td>
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
						T.ROWNUM BETWEEN 2 AND 15";
			$rs = sqlsrv_query($dbConn, $sql);

			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];
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