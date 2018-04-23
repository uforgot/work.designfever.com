<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	$retUrl = isset($_GET['retUrl']) ? $_GET['retUrl'] : null; 
?>

<? include INC_PATH."/top.php"; ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("[name=user_id]").focus();
	});

	function loginCheck()
	{
		var frm = document.form;
		/*
		if(frm.user_id.value.length < 4 || frm.user_id.value.length > 16 )
		{
			alert("잘못된 아이디입니다. (4-16자리 가능)");
			frm.user_id.focus();
			return;
			
		}
		if(frm.user_pw.value.length < 4 || frm.user_pw.value.length > 16)
		{
			alert("잘못된 패스워드입니다. (4-16자리 가능)");
			frm.user_pw.focus();
			return;
		}
		*/
		frm.target	= "hdnFrame";
		frm.action	= "login_act.php";
		frm.submit();
	}

	function enterLogin()
	{
		if(event.keyCode ==13)
			loginCheck();
	}
</script>

</head>
<body>
<div class="wrapper_login">
<form name="form" method="post">
<input type="hidden" name="retUrl" value="<?=$retUrl?>">
	<div style="position:absolute; z-index:10; width:300px; height:300px; left:50%; top:50%; margin:-300px 0 0 -200px">
		<!--div class="graphic"><img src="../img/logo_A_1.gif" alt="" /></div-->
	</div>
	<p class="login_txt1"><img src="../img/txt_left.gif" alt="" /></p>
	<? if($prs_id == ""){ ?>
	<div class="login_area">
		<div class="logo"><img class="js-svg" src="/img/df_logo_new.svg" alt="" /></div>
		<p class="a1"><img src="../img/txt_please.gif" alt="" /></p>
		<div class="loginfo">
			<a href="javascript:loginCheck();"><img src="../img/bn_ok.gif" alt="" /></a>
			<input type="text" tabindex="1" name="user_id" onkeypress="enterLogin();"/><br />
			<input type="password" tabindex="2" class="se" name="user_pw" onkeypress="enterLogin();"//>
		</div>
		<p class="a2" align="center">
			<a href="/member/join.php">+ 회원가입</a>
		</p>

	<? }else{ ?>
		<script>
			location.href="../main.php";
		</script>
	<? } ?>
	</div>
</form>	
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
