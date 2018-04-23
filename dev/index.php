<!DOCTYPE html>
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
<script type="text/javascript">
	


	var KEYBOARD_ENTER= 13;
	var id, pwd;
	var isMobile = false;
	var storageId, storagePw;

	$(document).ready( function(){
		init();
	});

	function init(){
		
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
		frm.target	= "hdnFrame";
		frm.action	= "/member/login_act.php";
		frm.submit();
	}
</script>
<body>
<form name="form" method="post">
<input type="hidden" name="retUrl" value="<?=$retUrl?>">
<? if($prs_id == ""){ ?>
<section class="hero is-dark is-fullheight">
    <div class="hero-body">
        <div class="container">
            <div class="columns">
                <div class="column is-one-third is-offset-one-third">
                    <div class="box">
                        <div class="image is-2by1">
                            <img src="img/df_logo_b.svg">
                        </div>
                        <hr/>
                        
                        <div class="field">
                            <div class="control has-icons-left has-icons-right">																	
                                <input class="input" type="text" placeholder="id" name="user_id" id="user_id">
                                <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                                <span class="icon is-small is-right"><i class="fas fa-check"></i></span>
                            </div>
                        </div>    
                        <div class="field">
                            <div class="control has-icons-left has-icons-right">
                                <input class="input" type="password" placeholder="password" name="user_pw" id="user_pw">
                                <span class="icon is-small is-left"><i class="fas fa-key"></i></span>
                                <span class="icon is-small is-right"><i class="fas fa-check"></i></span>
                            </div>
                        </div>
                        
                        <div class="field is-centered">
                            <a class="button is-medium is-fullwidth is-info" href="javascript:loginCheck();">								
                                <span>로그인</span>
                                <span class="icon is-small">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                            </a>
                        </div>                        
                        <div class="buttons is-right" >
                            <a class="button is-small is-text is-right" href="/member/join.php">
                                    <span class="icon is-small">
                                        <i class="fas fa-user-plus"></i>
                                    </span>
                                <span>회원가입</span>
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<? }else{ ?>
		<script>
			location.href="/main.php";
		</script>
	<? } ?>
</form>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>