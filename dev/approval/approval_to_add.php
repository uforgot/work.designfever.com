<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$max = isset($_REQUEST['max']) ? $_REQUEST['max'] : null; 
	$no = isset($_REQUEST['no']) ? $_REQUEST['no'] : null; 
?>

<? include INC_PATH."/pop_top.php"; ?>

<script type="text/javascript">
	//결재자 선택
	function selPerson(pos,name,id)
	{
		var frm = document.form;
		var pfrm = opener.document.form;
		var to_id = "";

		var pre_to_id = pfrm.to_id.value
		var arr_to_id = pre_to_id.split(",");

		for (var i=0; i<arr_to_id.length; i++ )
		{
			if (arr_to_id[i] != "" && arr_to_id[i] == id)
			{
				alert("이미 선택하셨습니다.");
				return;
			}
		}

		opener.document.all.to_div_<?=$no?>.innerHTML = pos+"<br>"+name;
		opener.document.all.to_btn_div_<?=$no?>.innerHTML = "";
		opener.document.all.to_btn_div_<?=$no?>.innerHTML = "<input type=\"button\" id=\"to_btn_<?=$no?>\" name=\"to_btn_<?=$i?>\" value=\"취소\" onClick=\"javascript:funPersonDel('to','<?=$no?>');\">";
		pfrm.to_id_<?=$no?>.value = id;

		for (var i=0; i<<?=$max?>; i++)
		{
			to_id = to_id + opener.document.getElementsByName("to_id_"+i)[0].value + ",";
		}
		pfrm.to_id.value = to_id;

		self.close();
	}
</script>
</head>
<body>
<div align="center">
<form name="form" method="post">
<input type="hidden" name="type" value="<?=$type?>">
	<table>
		<tr>
			<td align="center" valign="top">
				직원 리스트<br>
				<div style="height:300px; overflow-y:scroll; text-align:left;">
<?
	$sql = "SELECT 
				PRS_TEAM, PRS_POSITION, PRS_NAME, PRS_ID 
			FROM 
				DF_PERSON WITH(NOLOCK) 
			WHERE 
				PRF_ID IN (2,3,4) AND PRS_ID NOT IN (102,$prs_id)
			ORDER BY 
				CASE 
					WHEN PRS_TEAM = 'Agency' THEN 1 
					WHEN PRS_TEAM = '경영전략그룹' THEN 2 
					WHEN PRS_TEAM = '경영지원팀' THEN 3 
					WHEN PRS_TEAM = '홍보팀' THEN 4 
					WHEN PRS_TEAM = '전략기획그룹' THEN 5 
					WHEN PRS_TEAM = 'Digital Marketing Division' THEN 6 
					WHEN PRS_TEAM = 'Digital Marketing 1' THEN 7 
					WHEN PRS_TEAM = 'Digital Marketing 2' THEN 8 
					WHEN PRS_TEAM = 'Digital eXperience Division' THEN 9 
					WHEN PRS_TEAM = 'Digital eXperience 1' THEN 10 
					WHEN PRS_TEAM = 'Digital eXperience 2' THEN 11 
					WHEN PRS_TEAM = 'Design1 Division' THEN 12 
					WHEN PRS_TEAM = 'Design 1' THEN 13 
					WHEN PRS_TEAM = 'Design 2' THEN 14 
					WHEN PRS_TEAM = 'Design2 Division' THEN 15 
					WHEN PRS_TEAM = 'Design 3' THEN 16 
					WHEN PRS_TEAM = 'Design 4' THEN 17 
					WHEN PRS_TEAM = 'Motion Graphic Division' THEN 18 
					WHEN PRS_TEAM = 'Motion Graphic 1' THEN 19 
					WHEN PRS_TEAM = 'Motion Graphic 2' THEN 20 
					WHEN PRS_TEAM = 'Interactive Lab' THEN 21 
					WHEN PRS_TEAM = 'Interactive eXperience' THEN 22 
					WHEN PRS_TEAM = 'Digital Publishing' THEN 23 
					WHEN PRS_TEAM = 'Development Lab' THEN 24 
					WHEN PRS_TEAM = 'Digital Development' THEN 25  
					WHEN PRS_TEAM = 'Creative da' THEN 26 
					WHEN PRS_TEAM = 'Designfever Holdings' THEN 27 
				END, 
				CASE 
					WHEN PRS_POSITION='대표' THEN 1
					WHEN PRS_POSITION='이사' THEN 2
					WHEN PRS_POSITION='실장' THEN 3
					WHEN PRS_POSITION='팀장' THEN 4
					WHEN PRS_POSITION='책임' THEN 5
					WHEN PRS_POSITION='대리' THEN 6
					WHEN PRS_POSITION='선임' THEN 7
					WHEN PRS_POSITION='주임' THEN 8
					WHEN PRS_POSITION='사원' THEN 9
					WHEN PRS_POSITION='인턴' THEN 10 
				END, 
				PRS_NAME
			";
	$rs = sqlsrv_query($dbConn, $sql);

	while($record = sqlsrv_fetch_array($rs))
	{
		$team = $record['PRS_TEAM'];
		$position = $record['PRS_POSITION'];
		$name = $record['PRS_NAME'];
		$id = $record['PRS_ID'];

		if ($team != $team2)
		{
			echo $team ."<br>";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:selPerson('". $position ."','". $name ."','". $id ."');\">". $position ." ". $name ."</a><br>";

		$team2 = $team;
	}
?>
				</div>
			</td>
		</tr>
	</table>
	<input type="button" value="닫기" onClick="javascript:self.close();">
</form>
<? include INC_PATH."/pop_bottom.php"; ?>
</div>
</body>
</html>
