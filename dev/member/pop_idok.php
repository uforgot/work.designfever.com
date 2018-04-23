<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	$strLogin = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : null; 

	$strMSG = "";
	$strMSG1 = "";
	$returnMSG = "";

	$sql = "SELECT PRS_LOGIN FROM DF_PERSON WITH(NOLOCK) WHERE PRS_LOGIN = '$strLogin'";
	$rs = sqlsrv_query($dbConn, $sql);

	if (sqlsrv_has_rows($rs) > 0)
	{
		$strMSG = "중복된 아이디가 존재합니다.";
		$strMSG1= "다시한번 입력해주세요.";
		$returnMSG = "N";
	}
	else
	{
		$strMSG = "중복된 아이디가 존재하지 않습니다.";
		$strMSG1= "사용가능합니다.";
		$returnMSG = "Y";
	}
?>

<? include INC_PATH."/pop_top.php"; ?>

<script type="text/JavaScript">
	function fct_IDPut(strSelectedID) {
		
		var id_return = eval("opener.document.form.login");
		
		id_return.value = strSelectedID;
		opener.document.form.IdCheck.value="<?=$returnMSG?>";
		this.close();
		return;
	}

	function fct_back(){
		document.form1.submit();
	}
</script>
</head>
<body>
<form name="form1" method="post" action="pop_idcheck.php?tx_ID=">
<div class="intra_pop work_idcheck_pop">
	<div class="pop_top">
		<p class="pop_title">아이디 중복확인</p>
		<a href="javascript:self.close()" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<p class="intra_pop_info color_o"><?=$strMSG?><br><Br><?=$strMSG1?></p>
		<div class="edit_btn">
	<? if ($returnMSG == "N") { ?>
		<a href="javascript:fct_back();"><img src="../img/btn_recheck.gif" alt="중복확인" />
	<? } else { ?>
		<a href="javascript:fct_IDPut('<?=$strLogin?>')"><img src="../img/btn_ok.gif" alt="확인" /></a>
		<a href="javascript:self.close()"><img src="../img/btn_cancel.gif" alt="취소" /></a>
	<? } ?>
		</div>
	</div>
</div>
</form>
</body>
</html>
