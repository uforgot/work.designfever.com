<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<? include INC_PATH."/pop_top.php"; ?>
<script type="text/javascript" src="/js/df_auth.js"></script>

<script type="text/JavaScript">
	<!--
	function fct_Login() {
		if (document.fmLogin.ID.value.length == 0) {
			alert("���̵� �Է��ϼ���.");
			document.fmLogin.ID.focus();
			return;
		}
		if (document.fmLogin.ID.value.length < 2) {
			alert("���̵�� 2�� �̻��̾�� �մϴ�.");
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
		<p class="pop_title">���̵� �ߺ�Ȯ��</p>
		<a href="javascript:self.close()" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
		<div class="id_check">
			<p>* ������ ���ڸ� �Է°���</p>
			<div class="clearfix">
				<dl>
					<dt><label for="#recheckid">���̵�</label></dt>
					<dd><input id="recheckid" class="df_textinput" type="text" name="ID" maxlength="12" onKeypress = "intNumber_Check();" onblur="fcHancheck();"/></dd> 
				</dl>
				<center><a href="javascript:fct_Login();"><img src="../img/btn_recheck.gif" alt="�ߺ�Ȯ��" /></a></center>
			</div>
		</div>
		
		<!--  
		<p class="intra_pop_info color_o">����Ͻ� �� �ִ� ���̵� �Դϴ�.</p>
		<div class="adit_btn">
			<a href="#"><img src="../img/btn_ok.gif" alt="Ȯ��" /></a>
			<a href="#"><img src="../img/btn_cancel.gif" alt="���" /></a>
		</div>
		-->
	</div>
</div>
</form>
</body>

</html>
