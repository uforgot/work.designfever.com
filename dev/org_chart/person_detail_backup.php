<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null; 

	if ($id == "")
	{
?>
		<script type="text/javascript">
			alert("�ش� ���� ������ �����ϴ�.");
			self.close();
		</script>
<?
		exit;
	}

	$sql = "SELECT 
				PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEL, PRS_ZIPCODE, PRS_ADDR1, PRS_ADDR2, PRS_TEAM, PRS_POSITION, FILE_IMG, PRS_BIRTH, PRS_BIRTH_TYPE, PRS_JOIN 
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE
				PRS_ID = $id";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$col_prs_id = $record['PRS_ID'];
		$col_prs_login = $record['PRS_LOGIN'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_tel = $record['PRS_TEL'];
		$col_prs_zipcode = $record['PRS_ZIPCODE'];
		$col_prs_addr1 = $record['PRS_ADDR1'];
		$col_prs_addr2 = $record['PRS_ADDR2'];
		$col_prs_team = $record['PRS_TEAM'];
		$col_prs_position = $record['PRS_POSITION'];
		$col_file_img = $record['FILE_IMG'];
		$col_prs_birth = $record['PRS_BIRTH'];
		$col_prs_birth_type = $record['PRS_BIRTH_TYPE'];
		$col_prs_join = $record['PRS_JOIN'];

		$team = "Agency";
		if ($col_prs_team == "�濵������" || $col_prs_team == "ȫ����")
		{
			$team = $team ." > �濵�����׷� > ". $col_prs_team;
		}
		if ($col_prs_team == "Digital Marketing Division" || $col_prs_team == "Digital eXperience Division")
		{
			$team = $team ." > ������ȹ�׷� > ". $col_prs_team;
		}
		if ($col_prs_team == "Digital Marketing 1" || $col_prs_team == "Digital Marketing 2")
		{
			$team = $team ." > ������ű׷� > Digital Marketing Division > ". $col_prs_team;
		}
		if ($col_prs_team == "Digital eXperience 1" || $col_prs_team == "Digital eXperience 2")
		{
			$team = $team ." > ������ű׷� > Digital eXperience Division > ". $col_prs_team;
		}
		if ($col_prs_team == "Design1 Division" || $col_prs_team == "Design2 Division")
		{
			$team = $team ." > �����α׷� > ". $col_prs_team;
		}
		if ($col_prs_team == "Design 1" || $col_prs_team == "Design 2")
		{
			$team = $team ." > �����α׷� > Design1 Division > ". $col_prs_team;
		}
		if ($col_prs_team == "Design 3" || $col_prs_team == "Design 4")
		{
			$team = $team ." > �����α׷� > Design2 Division > ". $col_prs_team;
		}
		if ($col_prs_team == "Motion Graphic Division")
		{
			$team = $team ." > �����п���׷� > ". $col_prs_team;
		}
		if ($col_prs_team == "Motion Graphic 1" || $col_prs_team == "Motion Graphic 2")
		{
			$team = $team ." > �����п���׷� > Motion Graphic Division > ". $col_prs_team;
		}
		if ($col_prs_team == "Interactive Lab" || $col_prs_team == "Development Lab")
		{
			$team = $team ." > ������ű׷� > ". $col_prs_team;
		}
		if ($col_prs_team == "" || $col_prs_team == "")
		{
			$team = $team ." > ������ű׷� > ". $col_prs_team;
		}
		if ($col_prs_team == "Interactive eXperience" || $col_prs_team == "Digital Publishing")
		{
			$team = $team ." > ������ű׷� > Interactive Lab > ". $col_prs_team;
		}
		if ($col_prs_team == "Digital Development")
		{
			$team = $team ." > ������ű׷� > Development Lab > ". $col_prs_team;
		}
		if ($col_prs_team == "Creative da")
		{
			$team = $col_prs_team;
		}
	}
	else
	{
?>
		<script type="text/javascript">
			alert("�ش� ���� ������ �����ϴ�.");
			self.close();
		</script>
<?
		exit;
	}
?>

<? include INC_PATH."/pop_top.php"; ?>

</head>
<body>
<!-- pop -->		 
			<div class="intra_pop work_team_pop" style="border:0px; margin-top:-170px;">
				<div class="pop_top">
					<p class="pop_title">���� ������</p>
					<a href="javascript:self.close();" class="close">�ݱ�</a>
				</div>
				<div class="pop_body">
					<div class="prs_wrap">
						<table class="notable prs_table"  width="100%">
							<summary></summary>
							<colgroup><col width="30%" /><col width="20%" /><col width="*" /></colgroup>
							<tr>
								<td rowspan="5" class="img"><img src="<? if ($col_file_img == "") { ?>/img/noimg.jpg<? } else { ?>/file/<?=$col_file_img?><? } ?>" alt="" width="138" height="138" /></th>
								<th>���̵�</th>
								<td><?=$col_prs_login?></td>
							</tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<th>�̸�</th>
								<td><?=$col_prs_name?></td>
							</tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<th>����</th>
								<td colspan="2">
									<?=$col_prs_birth?>
									<? if ($col_prs_birth_type == "����") { echo "(". $col_prs_birth_type .")"; }	?></td>
							</tr>
						</table>
						<table class="notable prs_table"  width="100%">
							<summary></summary>
							<colgroup><col width="20%" /><col width="*" /></colgroup>
						<? if ($col_prs_id == $prs_id || $prf_id == "4") { ?>
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<th>�Ի���</th>
								<td colspan="2"><?=$col_prs_join?></td>
							</tr>
						<? } ?>
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<th>�ڵ���</th>
								<td colspan="2"><?=$col_prs_tel?></td>
							</tr>
						<? if ($col_prs_id == $prs_id || $prf_id == "4") { ?>
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<th>�����ּ�</th>
								<td colspan="2"><?=$col_prs_zipcode?><br><?=$col_prs_addr1?> <?=$col_prs_addr2?></td>
							</tr>
						<? } ?>
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<th>�ҼӺμ�</th>
								<td colspan="2"><?=$team?></td>
							</tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<th>����</th>
								<td colspan="2"><?=$col_prs_position?></td>
							</tr>
						</table>
					</div>
					<div class="prs_btn">
						<a href="javascript:self.close();"><img src="../img/btn_ok.gif" alt="ok" /></a>
					</div>
				</div>
			</div>
			<!-- //pop -->
<? include INC_PATH."/pop_bottom.php"; ?>
</body>
</html>
