<?	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";	
?>

<?
	$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null; 
	$user_pw = isset($_REQUEST['user_pw']) ? $_REQUEST['user_pw'] : null; 		
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null; 	

	$errMsg = "";
	$login_clear = "";

	$col_prs_id = "";
	$col_prf_id = "";
	$col_prs_name = "";
	$col_prs_team = "";
	$col_prs_position = "";
	$col_prs_email = "";
	$col_prs_tel = "";
	$col_prs_extension = "";
	$col_file_img = "";
	$col_prs_passwd = "";

	$sql = "SELECT 
				PRS_ID, PRF_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_EMAIL, PRS_TEL, PRS_EXTENSION, PRS_MEMO1, PRS_MEMO2, FILE_IMG, PRS_PASSWD
			FROM DF_PERSON WITH(NOLOCK) 
			WHERE PRS_LOGIN = '$user_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);

	if (sizeof($record) > 0)
	{
		$col_prs_id = $record['PRS_ID'];
		$col_prf_id = $record['PRF_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_team = $record['PRS_TEAM'];
		$col_prs_position = $record['PRS_POSITION'];
		$col_prs_email = $record['PRS_EMAIL'];
		$col_prs_tel = $record['PRS_TEL'];
		$col_prs_extension = $record['PRS_EXTENSION'];
		$col_file_img = $record['FILE_IMG'];
		$col_prs_passwd = $record['PRS_PASSWD'];
	}

	if ($col_prs_id == "") {
		$errMsg = "아이디가 존재하지 않습니다.";
		$login_clear = "id";
	}
	else
	{
		if ($user_pw != $col_prs_passwd) {
			$errMsg = "패스워드가 틀렸습니다. 다시 확인하여 주십시오.";
			$login_clear = "pwd";
		}
		else
		{
			if ($col_prf_id == "5") {
				$errMsg = "승인대기 상태입니다. 관리자 승인 후에 이용가능합니다.";
			}
			elseif ($col_prf_id == "6") {
				$errMsg = "탈퇴된 회원입니다. 관리자 승인 후에 이용가능합니다.";
			}
			else
			{
				// session 
				$_SESSION['SS_PRS_ID'] = $col_prs_id;

				$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
				$rs = sqlsrv_query($dbConn, $sql);

				$record = sqlsrv_fetch_array($rs);
				$checktime1 = $record['CHECKTIME1'];
				$checktime2 = $record['CHECKTIME2'];
			}
		}
	}

		if ($errMsg != "") {
?>
		<script type="text/javascript">
			alert("<? echo $errMsg?>");
		<?
			if ($login_clear == "id")
			{
		?>
				parent.document.form.user_id.value = "";
				parent.document.form.user_pw.value = "";
				parent.document.form.user_id.focus();
		<?
			}
			else if ($login_clear == "pwd")
			{
		?>
				parent.document.form.user_pw.value = "";
				parent.document.form.user_pw.focus();
		<?
			}
			
		?>			
		</script>
<?
	}
	else
	{
?>
		<script type="text/javascript">		
			parent.location.href="main.php?beacon=<?=$beacon?>"; 				
		</script>
<?
	}
?>