<!DOCTYPE html>
<html lang="en">
<head>
<?
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null;	
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	if ($prs_id != "") {
		header("Location:main.php"); 
	}
?>
<script type="text/javascript">	
function goAlert(text){
	alert(text);
}

function loginCheck() {
		var frm = document.form;		
		frm.target	= "work";
		frm.action	= "login_act.php";
		frm.submit();
	}	
</script>    
	<meta charset="euc-kr"/>
    <title>df workout</title>

    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0, minimum-scale=1.0,user-scalable=no,shrink-to-fit=no" />
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,600,800' rel='stylesheet' type='text/css'>
    <link href="asset/css/common.css" rel="stylesheet" type="text/css">
    <link href="asset/css/workout.css" rel="stylesheet" type="text/css">
</head>
<body>
	<input type="hidden" name="userUUID"></input>
	<form name="form" method="post">
    <div class="body-wrap">
        <div class="login-wrap">
            <div class="logo">
                <img src="asset/svg/logo.svg">
            </div>
            <div class="form-wrap">
                <input type="text" name="user_id" id="user_id">
                <input type="password" name="user_pw" id="user_pw">
				<input type="hidden" name="beacon" id="beacon" value="<?=$beacon?>">
            </div>
            <div class="login-bt" onclick="javascript:loginCheck()">
                log in
                <div class="underline"></div>
            </div>
        </div>
    </div>

</body>
</html>
<iframe frameborder="0" width="0" height="0" name="work"></iframe>