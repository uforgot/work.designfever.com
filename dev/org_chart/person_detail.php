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
			alert("해당 직원 정보가 없습니다.");
		</script>
<?
		exit;
	}

	$sql = "SELECT 
				PRF_ID, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_EMAIL, PRS_MOBILE, PRS_ZIPCODE, PRS_ADDR1, PRS_ADDR2, PRS_TEAM, PRS_POSITION1, PRS_POSITION2, PRS_TEL, PRS_EXTENSION, FILE_IMG, PRS_BIRTH, PRS_BIRTH_TYPE, PRS_JOIN, PRF_ID 
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE
				PRS_ID = $id";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$col_prf_id = $record['PRF_ID'];
		$col_prs_id = $record['PRS_ID'];
		$col_prs_login = $record['PRS_LOGIN'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_email = $record['PRS_EMAIL'];
		$col_prs_mobile = $record['PRS_MOBILE'];
		$col_prs_zipcode = $record['PRS_ZIPCODE'];
		$col_prs_addr1 = $record['PRS_ADDR1'];
		$col_prs_addr2 = $record['PRS_ADDR2'];
		$col_prs_team = $record['PRS_TEAM'];
		$col_prs_position1 = $record['PRS_POSITION1'];
		$col_prs_position2 = $record['PRS_POSITION2'];
		$col_prs_tel = $record['PRS_TEL'];
		$col_prs_extension = $record['PRS_EXTENSION'];
		$col_file_img = $record['FILE_IMG'];
		$col_prs_birth = $record['PRS_BIRTH'];
		$col_prs_birth_type = $record['PRS_BIRTH_TYPE'];
		$col_prs_join = $record['PRS_JOIN'];
		$col_prf_id = $record['PRF_ID'];
	}
	else
	{
?>
		<script type="text/javascript">
			alert("해당 직원 정보가 없습니다.");
		</script>
<?
		exit;
	}

	if ($col_prs_tel == "070--")
	{
		$col_prs_tel = "";
	}

	if ($col_prf_id == "5" || $col_prf_id == "7" || $col_prs_position == "인턴")
	{
		$col_prs_email = "";
	}
	else
	{
		$col_prs_email = $col_prs_email ."@designfever.com";
	}

	if ($col_prs_extension != "")
	{
		$col_prs_extension = "02-325-2767 (". $col_prs_extension .")";
	}
?>

<script type="text/javascript">
	parent.document.getElementById("pop_img").innerHTML = "<?=getProfileImg($col_file_img,138);?>";
	parent.document.getElementById("pop_id").innerHTML = "<?=$col_prs_login?>";
	parent.document.getElementById("pop_name").innerHTML = "<?=$col_prs_name?>";
	parent.document.getElementById("pop_birth").innerHTML = "<?=$col_prs_birth?><? if ($col_prs_birth_type == '음력') { echo '('. $col_prs_birth_type .')'; } ?>";
	parent.document.getElementById("pop_mobile").innerHTML = "<?=$col_prs_mobile?>";
	parent.document.getElementById("pop_team").innerHTML = "<?=$col_prs_team?>";
	parent.document.getElementById("pop_position").innerHTML = "<?=$col_prs_position2?> / <?=$col_prs_position1?>";
	parent.document.getElementById("pop_email").innerHTML = "<?=$col_prs_email?>";
	parent.document.getElementById("pop_tel").innerHTML = "<?=$col_prs_tel?>";
	parent.document.getElementById("pop_extension").innerHTML = "<?=$col_prs_extension?>";
</script>
