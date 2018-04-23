<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "person"; 
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null; 

	$teamSQL = "";
	$orderSQL = "ORDER BY newid()";

	if ($p_type == "person")
	{
		if ($p_name != "")
		{
			$teamSQL = " AND PRS_NAME = '$p_name'";
		}
	}
	else if ($p_type == "team")
	{
		switch($p_team)
		{
			case "Design1 Division" :
				$teamSQL = " AND PRS_TEAM IN ('Design1 Division','Design 1','Design 2')";
				break;
			case "Design2 Division" :
				$teamSQL = " AND PRS_TEAM IN ('Design2 Division','Design 3','Design 4')";
				break;
			case "Motion Graphic Division" :
				$teamSQL = " AND PRS_TEAM IN ('Motion Graphic Division','Motion Graphic 1','Motion Graphic 2')";
				break;
			case "Interactive Lab" :
				$teamSQL = " AND PRS_TEAM IN ('Interactive Lab','Interactive eXperience','Digital Publishing')";
				break;
			case "Development Lab" :
				$teamSQL = " AND PRS_TEAM IN ('Development Lab','Digital Development')";
				break;
			case "Digital Marketing Division" :
				$teamSQL = " AND PRS_TEAM IN ('Digital Marketing Division','Digital Marketing 1','Digital Marketing 2')";
				break;
			case "Digital eXperience Division" :
				$teamSQL = " AND PRS_TEAM IN ('Digital eXperience Division','Digital eXperience 1','Digital eXperience 2')";
				break;
			case "경영전략그룹" :
				$teamSQL = " AND PRS_TEAM IN ('경영전략그룹','경영지원팀','홍보팀')";
				break;
			case "Creative da" :
				$teamSQL = " AND PRS_TEAM IN ('Creative da','collection')";
				break;
			case "" : 
				$teamSQL = "";
				break;
			default :
				$teamSQL = " AND PRS_TEAM = '$p_team'";
				break;
		}

		$orderSQL = "ORDER BY PRS_NAME";
	}

	$sql = "SELECT COUNT(*) FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4)". $teamSQL ." AND PRS_LOGIN <> 'dfadmin'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT 
				PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_TEL, FILE_IMG
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(". $orderSQL .") AS ROWNUM,
					PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_TEL, FILE_IMG
				FROM 
					DF_PERSON WITH(NOLOCK)
				WHERE 
					PRF_ID IN (1,2,3,4) AND PRS_LOGIN <> 'dfadmin'". $teamSQL ."
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
<!--
	function sSubmit(f)
	{
		f.target = "_self";
		f.page.value = "1";
		f.submit();
	}

	function eSubmit(f)
	{
		if(event.keyCode ==13)
			sSubmit(f);
	}
-->
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<p class="hello work_list">
			<strong>+  조직도</strong>			
			<div class="work_wrap clearfix">
			
				<div class="work_stats_search clearfix">
				
					<table class="notable" width="100%">
						<tr>
							<td>
								<select name="type" onChange="sSubmit(this.form)">
									<option value="team"<? if ($p_type == "team"){ echo " selected"; } ?>>부서별</option>
									<option value="person"<? if ($p_type == "person"){ echo " selected"; } ?>>직원별</option>
								</select>
								<? if ($p_type == "team") { ?>
								<select name="team" onChange="sSubmit(this.form)">			
									<option value=""<? if ($p_team == ""){ echo " selected"; } ?>>전직원</option>
									<option value="Agency"<? if ($p_team == "Agency"){ echo " selected"; } ?>>Agency</option>
									<option value="Design1 Division"<? if ($p_team == "Design1 Division"){ echo " selected"; }?>>Design1 Division</option>
									<option value="Design 1"<? if ($p_team == "Design 1"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Design 1</option>
									<option value="Design 2"<? if ($p_team == "Design 2"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Design 2</option>
									<option value="Design2 Division"<? if ($p_team == "Design2 Division"){ echo " selected"; } ?>>Design2 Division</option>
									<option value="Design 3"<? if ($p_team == "Design 3"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Design 3</option>
									<option value="Design 4"<? if ($p_team == "Design 4"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Design 4</option>
									<option value="Motion Graphic Division"<? if ($p_team == "Motion Graphic Division"){ echo " selected"; }?>>Motion Graphic Division</option>
									<option value="Motion Graphic 1"<? if ($p_team == "Motion Graphic 1"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Motion Graphic 1</option>
									<option value="Motion Graphic 2"<? if ($p_team == "Motion Graphic 2"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Motion Graphic 2</option>
									<option value="Interactive Lab"<? if ($p_team == "Interactive Lab"){ echo " selected"; }?>>Interactive Lab</option>
									<option value="Interactive eXperience"<? if ($p_team == "Interactive eXperience"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Interactive eXperience</option>
									<option value="Digital Publishing"<? if ($p_team == "Digital Publishing"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Digital Publishing</option>
									<option value="Development Lab"<? if ($p_team == "Development Lab"){ echo " selected"; }?>>Development Lab</option>
									<option value="Digital Development"<? if ($p_team == "Digital Development"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Digital Development</option>
									<option value="Digital Marketing Division"<? if ($p_team == "Digital Marketing Division"){ echo " selected"; }?>>Digital Marketing Division</option>
									<option value="Digital Marketing 1"<? if ($p_team == "Digital Marketing 1"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Digital Marketing 1</option>
									<option value="Digital Marketing 2"<? if ($p_team == "Digital Marketing 2"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Digital Marketing 2</option>
									<option value="Digital eXperience Division"<? if ($p_team == "Digital eXperience Division"){ echo " selected"; }?>>Digital eXperience Division</option>
									<option value="Digital eXperience 1"<? if ($p_team == "Digital eXperience 1"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Digital eXperience 1</option>
									<option value="Digital eXperience 2"<? if ($p_team == "Digital eXperience 2"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;Digital eXperience 2</option>
									<option value="경영전략그룹"<? if ($p_team == "경영전략그룹"){ echo " selected"; }?>>경영전략그룹</option>
									<option value="경영지원팀"<? if ($p_team == "경영지원팀"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;경영지원팀</option>
									<option value="홍보팀"<? if ($p_team == "홍보팀"){ echo " selected"; }?>>&nbsp;&nbsp;&nbsp;홍보팀</option>
									<option value="Creative da"<? if ($p_team == "Creative da"){ echo " selected"; }?>>Creative da</option>
									<option value="collection"<? if ($p_team == "collection"){ echo " selected"; }?>>&nbsp;&nbsp;collection</option>
									<option value="Designfever Holdings"<? if ($p_team == "Designfever Holdings"){ echo " selected"; }?>>Designfever Holdings</option>
								</select>
								<? } else if ($p_type == "person") { ?>
									<input type="text" name="name" style="width:200px;" value="<?=$p_name?>" onkeypress="eSubmit(this.form);">
									<a href="javascript:sSubmit(this.form);"><img src="../img/btn_search.gif" alt="검색" /></a>
								<? } ?>
						</tr>
					</table>		
				</div>
				<table class="notable work1 work_stats"  width="100%">
					<caption>조직도</caption>
					
					<thead>
						<tr>
							<th width="10%">NO.</th>
							<th width="15%">이름</th>
							<th width="15%">부서</th>
							<th width="15%">직급</th>
							<th width="10%">연락처</th>
							<th width="35%">사진</th>
							
						</tr>
					</thead>

					<tbody> 
<?
	$i = 0;
	while ($record = sqlsrv_fetch_array($rs))
	{
		$col_prs_login = $record['PRS_LOGIN'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_team = $record['PRS_TEAM'];
		$col_prs_position = $record['PRS_POSITION'];
		$col_prs_tel = $record['PRS_TEL'];
		$col_file_img = $record['FILE_IMG'];
?>
						<tr>
							<td><?=($page-1)*$per_page+($i+1)?></td>
							<td class="bold"><?=$col_prs_name?></td>
							<td><?=$col_prs_team?></td>
							<td><?=$col_prs_position?></td>
							<td><?=$col_prs_tel?></td>
							<td>
							<? if ($col_file_img == "") { ?>
								<img src="/img/noimg.jpg" alt="" width="138" height="138" />
							<? } else { ?>
								<img src="/file/<?=$col_file_img?>" alt="" width="138" height="138" />
							<? } ?>
							</td>
						</tr>
<?
		$i++;
	}
?>
					</tbody>

				</table>
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