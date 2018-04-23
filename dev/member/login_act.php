<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/KISA_SHA256.php";
?>

<?
	$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null; 
	$user_pw = isset($_REQUEST['user_pw']) ? $_REQUEST['user_pw'] : null; 

	//$commute = isset($_REQUEST['commute_check']) ? $_REQUEST['commute_check'] : null; 
	$retUrl = isset($_REQUEST['retUrl']) ? $_REQUEST['retUrl'] : null; 

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

	$new_pwd = kisa_sha256($user_pw);

	$sql = "SELECT 
				PRS_ID, PRF_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_EMAIL, PRS_TEL, PRS_EXTENSION, PRS_MEMO1, PRS_MEMO2, FILE_IMG, PRS_PWD
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
		$col_prs_pwd = $record['PRS_PWD'];
	}

	if ($col_prs_id == "") {
		$errMsg = "아이디가 존재하지 않습니다.";
		$login_clear = "id";
	}
	else
	{
		if ( $new_pwd == $col_prs_pwd) { 
			if ($col_prf_id == "6") {
				$errMsg = "탈퇴된 회원입니다. 관리자 승인 후에 이용가능합니다.";
			}
			else
			{
				// session 
				$_SESSION['DF_PRS_ID'] = $col_prs_id;
			}
		} else { 
			$errMsg = "패스워드가 틀렸습니다. 다시 확인하여 주십시오.(new)";
			$login_clear = "pwd";
		} 

	/*##### 로그 저장 #################################################
	$log_txt = "------------------------------------\r\n";
	$log_txt.= "Login Time: ".date("Y-m-d H:i:s")."\r\n";

	$log_dir = "./log/";
	$log_file = fopen($log_dir."log_".date('Ym')."_".$col_prs_name.".txt", "a");  
	fwrite($log_file, $log_txt."\r\n");  
	fclose($log_file);  
	#################################################################*/

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
			else
			{
		?>
				parent.location.href = "index.php";
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
		<? if ($retUrl == "") { ?>
			
			localStorage.setItem('id', '<?= $user_id ?>');
			localStorage.setItem('pw', '<?= $user_pw ?>');
			parent.location.href="/main.php";
		<? } else { ?>
			parent.location.href="<?=$retUrl?>";
		<? } ?>
		</script>
<?
	}
?>
