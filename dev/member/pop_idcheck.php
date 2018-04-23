<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<? include INC_PATH."/pop_top.php"; ?>
<script type="text/javascript" src="/js/df_auth.js"></script>

<script type="text/JavaScript">
	<!--
	function fct_Login() {
		if (document.fmLogin.ID.value.length == 0) {
			alert("아이디를 입력하세요.");
			document.fmLogin.ID.focus();
			return;
		}
		if (document.fmLogin.ID.value.length < 2) {
			alert("아이디는 2자 이상이어야 합니다.");
			document.fmLogin.ID.focus();
			return;
		}
		document.fmLogin.submit();
	}
	//-->
</script>
</head>
<body>
<form name="fmLogin" id="fmLogin" method="post" action="pop_idok.php">
<div class="intra_pop work_idcheck_pop">
	<div class="pop_top">
		<p class="pop_title">아이디 중복확인</p>
		<a href="javascript:self.close()" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<div class="id_check">
			<p>* 영문과 숫자만 입력가능</p>
			<div class="clearfix">
				<dl>
					<dt><label for="#recheckid">아이디</label></dt>
					<dd><input id="recheckid" class="df_textinput" type="text" name="ID" maxlength="12" onKeypress = "intNumber_Check();" onblur="fcHancheck();"/></dd> 
				</dl>
				<center><a href="javascript:fct_Login();"><img src="../img/btn_recheck.gif" alt="중복확인" /></a></center>
			</div>
		</div>
		
		<!--  
		<p class="intra_pop_info color_o">사용하실 수 있는 아이디 입니다.</p>
		<div class="adit_btn">
			<a href="#"><img src="../img/btn_ok.gif" alt="확인" /></a>
			<a href="#"><img src="../img/btn_cancel.gif" alt="취소" /></a>
		</div>
		-->
	</div>
</div>
</form>
</body>

</html>
