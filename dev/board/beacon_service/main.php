<!DOCTYPE html>
<html lang="en">
<head>
<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";	
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : "00000000-0000-0000-0000-000000000000";		//user_uuid	
?>
<!--///////////////////////////////////////�α��� üũ �κ�///////////////////////////////////////-->
<?	
	if ($prs_id == "") {
?>
	<script type="text/javascript">									
		location.href="index.php?beacon=<?=$beacon?>";
	</script>
<?
	exit;
	}	
	$sql = "SELECT PRS_NAME, PRS_LOGIN, PRF_ID, PRS_TEAM, PRS_POSITION, FILE_IMG, LOG_WEEKLY_CREATE FROM DF_PERSON WITH(NOLOCK) WHERE PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);	
	if (sqlsrv_has_rows($rs) == 0)
	{		
?>			
	<script type="text/javascript">										
		location.href="index.php?beacon=<?=$beacon?>"; 			
	</script>
<?
	}
	else
	{
		$record = sqlsrv_fetch_array($rs);
		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];
		$prf_id = $record['PRF_ID'];
		$prs_team = $record['PRS_TEAM'];
		$prs_position = $record['PRS_POSITION'];
		$prs_img = $record['FILE_IMG'];
		$log_weekly_create = $record['LOG_WEEKLY_CREATE'];
		$beacon = $beacon;
	}
	if ($prf_id == "5" || $prf_id == "6") {
?>			
		<script type="text/javascript">
			alert("��ϴ��,Ż��ȸ�� �̿�Ұ� �������Դϴ�.");							
			location.href="index.php?beacon=<?=$beacon?>"; 
		</script>
<?
	}
?>
<!--///////////////////////////////////////�α��� üũ �κ� ��///////////////////////////////////////-->

    <meta charset="euc-kr">
    <title>df workout</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" id="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0, minimum-scale=1.0,user-scalable=no,shrink-to-fit=no" />
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,600,800' rel='stylesheet' type='text/css'>
    <link href="asset/css/common.css" rel="stylesheet" type="text/css">
    <link href="asset/css/workout.css" rel="stylesheet" type="text/css">

    <script language="javascript" src="asset/js/log.js"></script>
    <script language="javascript" src="asset/js/vendor/jquery/jquery-1.11.0.min.js"></script>
    <script language="javascript" src="asset/js/vendor/jquery/jquery-ui.js"></script>
    <script language="javascript" src="asset/js/vendor/greensock/TweenMax.min.js"></script>
	<script language="javascript" src="asset/js/index.js" charset='euc-kr'></script>	
	
<script type="text/javascript">	
//���� ���� üũ��
function goChkHouse(beaconUUID){
		var frm = document.form;
		frm.target ="work";		
		frm.action = "commute_check_house.php?beacon="+beaconUUID;
		frm.submit();
	}
	
function logout()
{
	if(!confirm("�α׾ƿ��Ͻðڽ��ϱ�?")){
        return;
	}else{
		frm = document.form;
		frm.target	= "work";
		frm.action = "logout.php";
		frm.submit();
	}
}
</script>
	</head>
	<body>
	<form name="form" method="post" >
	<input type="hidden" name="beacon" value="<?=$beacon?>"></input>
	<div class="body-wrap">
		<div class="main-wrap">
			<div class="main">
				<div class="in-bt" id="in-bt">
					in
					<div class="underline"></div>
					<!--�ð�����ʿ�-->					
				</div>								
				<div class="out-bt" id="out-bt">
					out
					<div class="underline"></div>															
				</div>				
			</div>
			<div class="top-wrap">
				<div class="date">
					<!--2015.4.7-->
				</div>
				<div class="day">
					<!--MONDAY-->
				</div>
				<div class="time">
					<div class="hour">
						00
					</div>
					<div class="minute">
						00
					</div>
					<div class="second">
						00
					</div>
				</div>
			</div>
			<div class="bottom-wrap">
				<div class="login-bt">
				   <?=$prs_team?> <?=$prs_name?> <?=$prs_position?> <p id="logout"onclick="javascript:logout()">log out</p>
					<div class="underline"></div>
				</div>
				<div class="beacon">
					<div class="item" id="beacon-1">
						<div class="deactivated"><svg height="24" width="24"><circle cx="10" cy="10" r="10" stroke-width="0" fill="white" /></svg></div>
						<div class="activated"><svg height="24" width="24"><circle cx="10" cy="10" r="10" stroke-width="0" fill="yellow" /></svg></div>
					</div><br>
					<div class="item" id="beacon-2">
						<div class="deactivated"><svg height="24" width="24"><circle cx="10" cy="10" r="10" stroke-width="0" fill="white" /></svg></div>
						<div class="activated"><svg height="24" width="24"><circle cx="10" cy="10" r="10" stroke-width="0" fill="yellow" /></svg></div>
					</div>
					<div class="item" id="beacon-3">
						<div class="deactivated"><svg height="24" width="24"><circle cx="10" cy="10" r="10" stroke-width="0" fill="white" /></svg></div>
						<div class="activated"><svg height="24" width="24"><circle cx="10" cy="10" r="10" stroke-width="0" fill="yellow" /></svg></div>
					</div>

				</div>
				<div class="logo">
					<img src="asset/svg/logo.svg">
				</div>
			</div>
		</div>
	</div>
	<script language="javascript" src="asset/js/util.js"></script>
	
<?
	$today = date("Y-m-d");	
	$gubun1 = "";
	$gubun2 = "";
	$checktime1 = "";
	$checktime2 = "";
	$in_out ="";
		
	$sql = "SELECT TOP 1 
				PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE 
				--PRS_BEACON = '$beacon'
				PRS_ID = '$prs_id'
			ORDER BY 
				PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$prs_id = $record['PRS_ID'];
		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];
		$prs_team = $record['PRS_TEAM'];
		$prs_position = $record['PRS_POSITION'];		
		$state = "����";		
	}	

	$sql = "SELECT * FROM DF_CHECKTIME_HOUSE WITH(NOLOCK) WHERE PRS_ID = '$prs_id' AND DATE = '$today'";
	$rs = sqlsrv_query($dbConn,$sql);		
	$record = sqlsrv_fetch_array($rs);	
	if (sqlsrv_has_rows($rs) > 0) //��ٰ��� ������
	{
		$gubun1 = $record['GUBUN1'];
		$gubun2 = $record['GUBUN2'];
		$checktime1 = $record['CHECKTIME1'];
		$checktime2 = $record['CHECKTIME2'];		
		//echo "<script>DFWORKOUT.INDEX.getOut();</script>"; 
		echo "<script>DFWORKOUT.INDEX.getIn();</script>"; //üũ�ι�ư Ȱ��ȭ
		//echo "<script>alert('�������')</script>";
	}
	else {
		echo "<script>DFWORKOUT.INDEX.getIn();</script>"; //üũ�ι�ư Ȱ��ȭ
		//echo "<script>alert('��پ�����')</script>";
	}
?>
	</body>
	</html>
<iframe frameborder="0" width="1000" height="1000" name="work" id ="work"></iframe>
<script>	

function beacon_update_chk(){
	if(!confirm("��ϵ� ��Ⱑ �ƴϹǷ� ����� ������ȣ�� �ڵ����� ����մϴ�.\�ٸ� ��⿡�� ������ �������� �α��ν� ������ȣ�� �ٸ��Ƿ� üũ�ν� �������� ���� �� �ֽ��ϴ�.\n����Ͻðڽ��ϱ�?"))	
	{
		offBtn_in();
		offBtn_out();
		return;		
	}else{			
		frm = document.form;				
		frm.target="work";					
		frm.action = "updateBeacon.php?beacon=<?=$beacon?>&prs_id=<?=$prs_id?>"; 				
		frm.submit();		
	}
}

function onBtn_in(){DFWORKOUT.INDEX.onBtn_in();}
//function onBtn_out(){}

function offBtn_in(){DFWORKOUT.INDEX.offBtn_in();}
//function offBtn_out(){}



</script>
<?	
	//prs_person ���̺��� prs_beacon���� �޾ƿ� ���ܰ����� ���� ������ prs_beacon���� �ִ��� Ȯ��	
	$sql = "SELECT PRS_BEACON
			  FROM DF_PERSON WITH(NOLOCK)
			 WHERE PRS_BEACON = '$beacon'		
			   AND PRS_ID =  '$prs_id'
			 ORDER BY PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$prs_beacon = $record['PRS_BEACON'];	//df_person�� ����Ǿ��ִ� user_uuid
	}else{
		$prs_beacon = "";
	}
	
	if($beacon=="00000000-0000-0000-0000-000000000000"){	//�޾ƿ� ���ڰ� user_uuid�� ������� �߸��� �����̹Ƿ� ���� ȣ���Ѵ�.
?>
	<script type="text/javascript">				
		 alert("�߸��� �����Դϴ�. �����ڿ��� �����ϼ���.");				
		 location.href="about:blank()"; 				
	</script>
<?}
	
	/* �޾ƿ� ���ܰ��� null�̰ų� ������� ���ܰ� ��� */	
	if($prs_beacon==""){
	?>
	<script type="text/javascript">				
			beacon_update_chk();
	</script>
<?}	
		
	/*	���ܰ��� ������� */		
	else if($prs_beacon != "")
	 if($prs_beacon != "")
	//else if($prs_beacon != $beacon)
	{	
		/*������ ���� prs_beacon���� �޾ƿ� ���ܰ��� ��Ī�Ͽ� ��ġ���� ������ ����� ������ȣ�� �ٸ��ϴ� �����üũ�� �Ұ��մϴ� �����ڿ��� �����ϼ��� ��� */
		if($prs_beacon != $beacon)
		{
?>	
	<script type="text/javascript">		
		alert("����� ������ȣ�� ��ϵ� ������ȣ�� ���� �ʽ��ϴ�.");			
	</script>
<?		}
	}
?>	