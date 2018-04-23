<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	if ($prs_id != "") {
		header("Location:main.php"); 
	}

	$retUrl = isset($_GET['retUrl']) ? $_GET['retUrl'] : null; 
?>

<? include INC_PATH."/top.php"; ?>
<meta property="wb:webmaster" content="0bbb8ebfde17ab16" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />


<!-- add homescreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">

<meta name="apple-mobile-web-app-title" content="df intranet">
<link rel="shortcut icon" sizes="16x16" href="img/homescreen/icon-16x16.png">
<link rel="shortcut icon" sizes="196x196" href="img/homescreen/icon-196x196.png">
<link rel="apple-touch-icon-precomposed" href="img/homescreen/icon-152x152.png">

<link rel="stylesheet" type="text/css" href="/css/addtohomescreen.css">
<script src="/js/addtohomescreen.min.js"></script>
<!-- //add homescreen -->

<script type="text/javascript">
	


	var KEYBOARD_ENTER= 13;
	var id, pwd;
	var isMobile = false;
	var storageId, storagePw;

	$(document).ready( function(){
		init();
	});

	function init(){

		/**
		* modify 20170113 / do / 모바일일 때 추가
		*/
		

		//모바일 단말기 확인하여 홈페이지 이동
		var ua = window.navigator.userAgent.toLowerCase();
		if ( /iphone/.test(ua) || /android/.test(ua) || /opera/.test(ua) || /bada/.test(ua) ) {
			setMobile();
		}


		id = $('#user_id');
		pwd = $('.se');

		id.on( 'keypress', keypressId );
		pwd.on( 'keypress', keypressPwd );
		$(window).on( 'resize', resizeHandler );

		console.log(storageId)
		if(storageId == null || storageId == undefined){
			id.focus();
		}
		$(window).trigger( 'resize' );
	}

	function setMobile(){
		isMobile = true;
		$('<link rel="stylesheet" href="/css/mobile.css" />').insertAfter(".pagejs");


		storageId = localStorage.getItem("id");
		storagePw = localStorage.getItem("pw");

		if(storageId != null){
			$("#user_id").val(storageId)
		}

		if(storagePw != null){
			$("#user_pw").val(storagePw)
		}
	}

	function keypressId( $evt ) {
		switch( $evt.which ) {
			case KEYBOARD_ENTER : pwd.focus(); break;
		}
	}

	function keypressPwd( $evt ) {
		switch( $evt.which ) {
			case KEYBOARD_ENTER : loginCheck(); break;
		}
	}

	function resizeHandler() {
		resizeWrapper();
	}

	function resizeWrapper() {
		$('.wrapper_login').css( {height:$(window).height()-82} );		// Footer Height
	}

	function loginCheck() {
		var frm = document.form;
		/*
		if( frm.user_id.value.length < 4 || frm.user_id.value.length > 16 ) {
			alert("잘못된 아이디입니다. (4-16자리 가능)");
			frm.user_id.focus();
			return;
		}

		if( frm.user_pw.value.length < 4 || frm.user_pw.value.length > 16) {
			alert("잘못된 패스워드입니다. (4-16자리 가능)");
			frm.user_pw.focus();
			return;
		}
		*/
		frm.target	= "hdnFrame";
		frm.action	= "/member/login_act_tc.php";
		frm.submit();
	}
</script>
</head>

<body>
<div class="wrapper_login">
<form name="form" method="post">
<input type="hidden" name="retUrl" value="<?=$retUrl?>">
	<div style="position:absolute; z-index:10; width:300px; height:300px; left:50%; top:50%; margin:-300px 0 0 -200px">
		<!-- <div class="graphic"><img src="../img/logo_A_1.gif" alt="" /></div> -->
	</div>
	<!-- <p class="login_txt1"><img src="../img/txt_left.gif" alt="" /></p> -->
	<? if($prs_id == ""){ ?>
	<div class="login_area">
		<!-- <div class="logo"></div> -->
		<div class="logo"><img class="js-svg" src="/img/df_logo_new.svg" alt="" /></div>

		<p class="a1"><img src="../img/txt_please.gif" alt="" /></p>
		
		<div class="loginfo">
			<a href="javascript:loginCheck();"><img src="../img/bn_ok.gif" alt="" /></a>
			<input type="text" tabindex="1" name="user_id" id="user_id" /><br />
			<input type="password" tabindex="2" class="se" name="user_pw" id="user_pw"/>
		</div>

		<p class="a2" align="center">
			<!--input type="checkbox" name="commute_check" value="1"/> 출근체크 <a href="/member/join.php">+ 회원가입</a-->
			<a href="/member/join.php">+ 회원가입</a>
		</p>
	<? }else{ ?>
		<script>
			location.href="/timecard.php";
		</script>
	<? } ?>
	</div>
</form>	
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>