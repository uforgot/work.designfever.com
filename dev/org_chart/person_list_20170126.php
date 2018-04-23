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
								$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) ORDER BY SORT";
								$selRs = sqlsrv_query($dbConn,$selSQL);

								while ($selRecord = sqlsrv_fetch_array($selRs))
								{
									$selStep = $selRecord['STEP'];
									$selTeam = $selRecord['TEAM'];

									$blank = "";
									for ($i=0;$i<=$selStep;$i++)
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
<? if ($p_team == "" || $p_team == "CSO" || $p_team == "CCO") { ?>
				<table class="notable work_stats3"  width="100%" id="coo">
					<thead>
						<tr class="plural">
							<th class="teamname team" style="background:#e8e8e8;">CSO</th>
							<th class="team" style="background:#e8e8e8;">CCO</th>
						</tr>
					</thead>
					<tbody> 
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
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CCO'";
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
<? if ($p_team == "" || $p_team == "경영전략그룹" || $p_team == "경영지원팀") { ?>

				<table class="notable work_stats3 group" width="100%" id="경영전략그룹">
					<thead>
						<tr>
							<th class="div">경영전략그룹</th>
						</tr>
					<thead>
				</table>
				<table class="notable work_stats3" width="100%" id="경영지원팀">
					<thead>
						<tr>
							<th class="team">경영지원팀</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '경영지원팀'". $where . $orderbycase;
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
<? if ($p_team == "" || $p_team == "전략기획그룹" || $p_team == "digital marketing division" || $p_team == "dm1" || $p_team == "dm2" || $p_team == "digital experience division" || $p_team == "dx1" || $p_team == "dx2" || $p_team == "brand experience team") { ?>
				<table class="notable work_stats3 group" width="100%" id="전략기획그룹">
					<thead>
						<tr>
							<th class="div">전략기획그룹</th>
						</tr>
					</thead>
				</table>
<? } ?>
<? if ($p_team == "" || $p_team == "전략기획그룹" || $p_team == "digital marketing division" || $p_team == "dm1" || $p_team == "dm2") { ?>
				<table class="notable work_stats3" width="100%" id="digital marketing division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">digital marketing division</th>
							<th class="team">dm1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'digital marketing division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
<?
			}	
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dm1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">dm2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dm2'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
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
		}
?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
<? } ?>
<? if ($p_team == "" || $p_team == "전략기획그룹" || $p_team == "digital experience division" || $p_team == "dx1" || $p_team == "dx2") { ?>
				<table class="notable work_stats3" width="100%" id="digital experience division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">digital experience division</th>
							<th class="team">dx1</th>
						</tr>
<?
//		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'digital experience division'". $where . $orderbycase;
//		$rs = sqlsrv_query($dbConn, $sql);
//
//		While ($record = sqlsrv_fetch_array($rs))
//		{
//			$col_prs_id = $record['PRS_ID'];
//			$col_prs_name = $record['PRS_NAME'];
//			$col_prs_position = $record['PRS_POSITION'];
//			$col_prs_extension = $record['PRS_EXTENSION'];
//			$col_file_img = $record['FILE_IMG'];
?>
						<tr>
							<td class="leader" rowspan="3">
								<!--ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul-->
							</td>
<?
//		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dx1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">dx2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dx2'". $where . $orderbycase;
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



<? if ($p_team == "" || $p_team == "전략기획그룹" || $p_team == "brand experience team") { ?>
				<table class="notable work_stats3" width="100%" id="brand experience team">
					<thead>
						<tr>
							<th class="team">brand experience team</td>
						</tr>
					</thead>
					<tbody> 
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'brand experience team'". $where . $orderbycase;
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



<? if ($p_team == "" || $p_team == "디자인그룹" || $p_team == "design1 division" || $p_team == "design1" || $p_team == "design2" || $p_team == "design2 division" || $p_team == "design3" || $p_team == "design4" || $p_team == "design5") { ?>
				<table class="notable work_stats3 group" width="100%" id="디자인그룹">
					<thead>
						<tr>
							<th class="div">디자인그룹</th>
						</tr>
					</thead>
				</table>
<? } ?>
<? if ($p_team == "" || $p_team == "디자인그룹" || $p_team == "design1 division" || $p_team == "design1" || $p_team == "design2") { ?>
				<table class="notable work_stats3" width="100%" id="Design1 Division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">design1 division</th>
							<th class="team">design1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'Design1 Division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">design2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design2'". $where . $orderbycase;
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
							</td>
						</tr>
					</tbody>
				</table>
<? } ?>
<? if ($p_team == "" || $p_team == "디자인그룹" || $p_team == "design2 division" || $p_team == "design3" || $p_team == "design4" || $p_team == "design5") { ?>
				<table class="notable work_stats3" width="100%" id="design2 division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">design2 division</th>
							<th class="team">design3</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'Design2 Division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];
?>
						<tr>
							<td class="leader" rowspan="5">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design3'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">design4</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design4'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">design5</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design5'". $where . $orderbycase;
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
<? if ($p_team == "" || $p_team == "영상콘텐츠그룹" || $p_team == "motion graphic division" || $p_team == "mg1" || $p_team == "mg2" || $p_team == "film&contents division" || $p_team == "fc") { ?>
				<table class="notable work_stats3 group" width="100%" id="영상콘텐츠그룹">
					<thead>
						<tr>
							<th class="div">영상콘텐츠그룹</th>
						</tr>
					</thead>
				</table>
<? } ?>
<? if ($p_team == "" || $p_team == "영상콘텐츠그룹" || $p_team == "motion graphic division" || $p_team == "mg1" || $p_team == "mg2") { ?>
				<table class="notable work_stats3" width="100%" id="motion graphic division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">motion graphic division</th>
							<th class="team">mg1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'motion graphic division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'mg1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">mg2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'mg2'". $where . $orderbycase;
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
<? if ($p_team == "" || $p_team == "영상콘텐츠그룹" || $p_team == "film & content division" || $p_team == "fc") { ?>
				<table class="notable work_stats3" width="100%" id="film & content division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">film & content division</th>
							<th class="team">fc</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'film & content division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
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
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'fc'". $where . $orderbycase;
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
<? if ($p_team == "" || $p_team == "기술혁신그룹" || $p_team == "df lab" || $p_team == "ix1" || $p_team == "ix2" || $p_team == "ixd") { ?>
				<table class="notable work_stats3 group" width="100%" id="기술혁신그룹">
					<thead>
						<tr>
							<th class="div">기술혁신그룹</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats3" width="100%" id="df lab">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">df lab</th>
							<th class="team">ix1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'df lab'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="5"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];
				$col_prs_extension = $record['PRS_EXTENSION'];
				$col_file_img = $record['FILE_IMG'];
?>
						<tr>
							<td class="leader" rowspan="5">
								<ul>
									<li>
										<?=getProfileImg($col_file_img,78,'person',$col_prs_id);?>
										<br><br><span><?=$col_prs_position?></span> <?=$col_prs_name?><br>(내선 <?=$col_prs_extension?>)
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ix1'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">ix2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ix2'". $where . $orderbycase;
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
						<tr class="plural">
							<th class="team">ixd</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ixd'". $where . $orderbycase;
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