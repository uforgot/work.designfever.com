<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/working_check.php";
	require_once CMN_PATH."/weekly_check.php";
?>

<? include INC_PATH."/top.php"; ?>

<?
	$alert_state1 = "none";

	$sql = "SELECT TOP 1 DATE FROM HOLIDAY WHERE DATE < '". str_replace('-','',date("Y-m-d")) ."' AND DATEKIND = 'BIZ' ORDER BY DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$prev_biz_date = $record['DATE'];

	$prev_biz_date = substr($prev_biz_date,0,4) ."-". substr($prev_biz_date,4,2) ."-". substr($prev_biz_date,6,2);

	$sql = "SELECT 
				DATE, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, TOTALTIME, OVERTIME, UNDERTIME
			FROM 
				DF_CHECKTIME WITH(NOLOCK) 
			WHERE 
				DATE = '$prev_biz_date' AND PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	
	if (sqlsrv_has_rows($rs) > 0)
	{
		$prev_biz_date = $record['DATE'];
		$prev_biz_gubun1 = $record['GUBUN1'];
		$prev_biz_gubun2 = $record['GUBUN2'];
		$prev_biz_checktime1 = $record['CHECKTIME1'];
		$prev_biz_checktime2 = $record['CHECKTIME2'];
		$prev_biz_totaltime = $record['TOTALTIME'];
		$prev_biz_overtime = $record['OVERTIME'];
		$prev_biz_undertime = $record['UNDERTIME'];

		if ($prev_biz_gubun1 == "" || $prev_biz_gubun2 == "")
		{
			$alert_state1 = "inline";
		}
	}
	else
	{
		$alert_state1 = "inline";
	}

	$alert_state2 = "none";

	$sql = "SELECT TOP 1 
				B.DATE, B.GUBUN1, B.GUBUN2, B.CHECKTIME1, B.CHECKTIME2, B.TOTALTIME, B.OVERTIME, B.UNDERTIME  
			FROM 
				HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
			ON 
				A.DATE = REPLACE(B.DATE,'-','') 
			WHERE 
				B.DATE < '". date("Y-m-d") ."' AND A.DATEKIND = 'BIZ' AND B.PRS_ID = '$prs_id' AND B.GUBUN1 IN (1,6,8) AND B.GUBUN2 IN (2,3,6,9) 
			ORDER BY 
				B.DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);

	$prev_biz_date = $record['DATE'];
	$prev_biz_gubun1 = $record['GUBUN1'];
	$prev_biz_gubun2 = $record['GUBUN2'];
	$prev_biz_checktime1 = $record['CHECKTIME1'];
	$prev_biz_checktime2 = $record['CHECKTIME2'];
	$prev_biz_totaltime = $record['TOTALTIME'];
	$prev_biz_overtime = $record['OVERTIME'];
	$prev_biz_undertime = $record['UNDERTIME'];

	if ($prev_biz_undertime > "0000")
	{
		$alert_state2 = "inline";
	}

	$alert_state3 = "none";

	$now = date("YmdHis");								//����
	$today = date("Y-m-d");								//���� ��¥
	$yesterday = date("Y-m-d",strtotime ("-1 day"));	//���� ��¥
	$next = date("Y-m-d",strtotime ("+1 day"));			//���� ��¥

	$yesterday_gubun1 = "";
	$yesterday_gubun2 = "";
	$yesterday_checktime1 = "";
	$yesterday_checktime2 = "";
	$yesterday_totaltime = "";
	$today_gubun1 = "";
	$today_gubun2 = "";
	$today_checktime1 = "";
	$today_checktime2 = "";
	$today_totaltime = "";
	$today_memo1 = "";
	$today_memo2 = "";
	$next_gubun1 = "";
	$next_gubun2 = "";
	$next_checktime1 = "";
	$next_checktime2 = "";

	$sql = "EXEC SP_MAIN_01 '$prs_id','$prs_name','$today','$yesterday','$next'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
	{
		$yesterday_gubun1 = $record['YESTERDAY_GUBUN1'];			//���� ���
		$yesterday_gubun2 = $record['YESTERDAY_GUBUN2'];			//���� ���
		$yesterday_checktime1 = $record['YESTERDAY_CHECKTIME1'];	//���� ���	
		$yesterday_checktime2 = $record['YESTERDAY_CHECKTIME2'];	//���� ���
		$yesterday_totaltime = $record['YESTERDAY_TOTALTIME'];		//���� �ٹ��ð�
		$yesterday_overtime = $record['YESTERDAY_OVERTIME'];		//���� ����ٹ��ð�
		$yesterday_memo1 = $record['yesterday_MEMO1'];				//���ڰ��� ����� or ���� ��� ��������
		$yesterday_memo2 = $record['yesterday_MEMO2'];				//���ڰ��� ������ or ���� ��� ��������
		$yesterday_memo3 = $record['yesterday_MEMO3'];				//���ڰ��� ��ȣ
		$today_gubun1 = $record['TODAY_GUBUN1'];					//���� ���
		$today_gubun2 = $record['TODAY_GUBUN2'];					//���� ���
		$today_checktime1 = $record['TODAY_CHECKTIME1'];			//���� ���	
		$today_checktime2 = $record['TODAY_CHECKTIME2'];			//���� ���
		$today_totaltime = $record['TODAY_TOTALTIME'];				//���� �ٹ��ð�
		$today_memo1 = $record['TODAY_MEMO1'];						//���ڰ��� ����� or ���� ��� ��������
		$today_memo2 = $record['TODAY_MEMO2'];						//���ڰ��� ������ or ���� ��� ��������
		$today_memo3 = $record['TODAY_MEMO3'];						//���ڰ��� ��ȣ

		if ($yesterday_checktime2 == "0") { $yesterday_checktime2 = ""; }
		if ($today_checktime2 == "0") { $today_checktime2 = ""; }
	}

	if ($yesterday_checktime1 == "" && $today_checktime1 == "")
	{
		$alert_state3 = "inline";
	}
	
	//���� ��� ���� ���� ����~8�� ��� ó���� ���� ������
	//�ٹ��ð� ���
	$time_gubun = "after";
	if ($today_checktime1 == "")
	{ 
		if (date("G") < 8) 
		{
			$time_gubun = "before";
		}
	}
	else
	{
		if ($today_gubun1 != "1" && $today_gubun1 != "6" && $today_gubun1 != "8" && $today_gubun1 != $today_gubun2)
//		if ($today_gubun1 != "1" && $today_gubun1 != "6" && $today_gubun1 != "8")
		{
			$time_gubun = "before";
		}
	}

	if ($time_gubun == "before")
	{
		$now2 = substr($now,0,8) . (24+substr($now,8,2)) . substr($now,10,4);
		
		if (substr($now2,10,2) < substr($yesterday_checktime1,10,2))
		{
			$totalhour = substr($now2,8,2) - substr($yesterday_checktime1,8,2) - 1;
			$totalmin = substr($now2,10,2) - substr($yesterday_checktime1,10,2) + 60;
		}
		else
		{
			$totalhour = substr($now2,8,2) - substr($yesterday_checktime1,8,2);
			$totalmin = substr($now2,10,2) - substr($yesterday_checktime1,10,2);
		}
		if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
		if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
		$totaltime = $totalhour . $totalmin;
	}
	else if ($time_gubun == "after")
	{
		$time_gubun = "after";
		
		if ($today_checktime1 == "")
		{
			$totaltime = "0000";
		}
		else
		{
			if (substr($now,10,2) < substr($today_checktime1,10,2))
			{
				$totalhour = substr($now,8,2) - substr($today_checktime1,8,2) - 1;
				$totalmin = substr($now,10,2) - substr($today_checktime1,10,2) + 60;
			}
			else
			{
				$totalhour = substr($now,8,2) - substr($today_checktime1,8,2);
				$totalmin = substr($now,10,2) - substr($today_checktime1,10,2);
			}
			if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
			if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
			$totaltime = $totalhour . $totalmin;
		}
	}

	$off_check = "N";

	$sql = "SELECT TOP 1 STARTTIME, ENDTIME FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE = '$today' AND PRS_ID = '$prs_id' ORDER BY SEQNO DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
	{
		$off_check = "Y";
		$last_off_starttime = $record['STARTTIME'];
		$last_off_endtime = $record['ENDTIME'];
	}
?>



<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

<style>
	.pace-done{min-width:0}
	.wrapper{padding-top:0;}
	.wrapper .inner-home {padding-bottom:5%;}
	.wrapper .hello{padding:12% 7% 9%;font-size:18px;}
	.wrapper .home{margin:2% 7% 0; padding: 7% 5% 7%; border-top:2px solid #000}
	.wrapper .home table {display:block; width:100%;}
	.commutes{margin:0;}
	.commutes p.dates {padding-bottom:5px; font-size:16px; letter-spacing:-0.2px;}
	.commutes div {line-height:1}
	.commutes div span {font-size:16px}
	.footer{margin:0 7%; padding:13% 0 0; width: auto; border-top: 2px solid #000}
	.footer img {display:block; width:60%;}

</style>



<script type="text/javascript">
	//----�ð� ��� ��ũ��Ʈ ����
	var days=new Array()
	days[0]="��";
	days[1]="��";
	days[2]="ȭ";
	days[3]="��";
	days[4]="��";
	days[5]="��";
	days[6]="��";

	var vRequest;

	function createRequest() {
		try {
			vRequest = new XMLHttpRequest();
		}
		catch (trymicrosoft) {
			try {
				vRequest = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (othermicrosoft) {
				try {
					vRequest = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (failed) {
					vRequest = null;
				}
			}
		}
	}

	function getTime() {
		vRequest.open("get", "getTime.php", true);
		vRequest.onreadystatechange = refrashTime;
		vRequest.send(null);
	}
	function refrashTime() {
		if(vRequest.readyState == 4) {
			var vResult = vRequest.responseText;
			// �����
			//alert(vResult);
			//document.form.timeContent.value = vResult;
			document.form.rtcInput.value = vResult.substr(13,2) + ' : ' + vResult.substr(16,2) + ' : ' + vResult.substr(19,2);
			document.form.rtcInput2.value = vResult.substr(0,4) + '�� ' + vResult.substr(5,2) + '�� ' + vResult.substr(8,2) + '�� ' + days[vResult.substr(11,1)] + '����';
		}
	}

	createRequest();
	var vTimer = setInterval('getTime()', '1000');



	function getTimeStamp() { // 24�ð���
		var d = new Date();
		var aaa= d.getDay()

		var s =
		//leadingZeros(d.getFullYear(), 4) + '�� ' + 
		//leadingZeros(d.getMonth() + 1, 2) + '�� ' + 
		//leadingZeros(d.getDate(), 2) + '�� ' +
		//leadingZeros(days_k[aaa]) + '���� ' +
		leadingZeros(d.getHours(), 2) + ' : ' +
		leadingZeros(d.getMinutes(), 2) + ' : ' +
		leadingZeros(d.getSeconds(), 2);

		return s;
	}

	function getTimeStamp2() { // 24�ð���
		var d = new Date();
		var aaa= d.getDay()

		var s =
		leadingZeros(d.getFullYear(), 4) + '�� ' + 
		leadingZeros(d.getMonth() + 1, 2) + '�� ' + 
		leadingZeros(d.getDate(), 2) + '�� ' +
		leadingZeros(days_k[aaa]) + '���� ';
		//leadingZeros(d.getHours(), 2) + ' : ' +
		//leadingZeros(d.getMinutes(), 2) + ' : ' +
		//leadingZeros(d.getSeconds(), 2);

		return s;
	}
	//----�ð� ��� ��ũ��Ʈ ��

	//��� üũ
	function go_office(){
		frm = document.form;
		frm.target	= "hdnFrame";
		frm.action = "commuting/commute_check_act.php";
		frm.submit();
		return;
	}

	//���� üũ
	function off_office(idx){
		frm = document.form;
		frm.target	= "hdnFrame";
		frm.action = "commuting/off_check_act.php?idx="+idx;
		frm.submit();
		return;
	}

	//��� üũ
	function leave_office(gubun,commute,working){
	/*
		var now = "<?=date("YmdHis");?>";
		var g = <?=date("G");?>;
		var commute = commute;

		if (g < 8)
		{
			now = Number(now+240000);
		}

		var today_hi = commute.substr(8,4);
		var now_hi = now.substr(8,4);

		if (working < '0900')
		{
			var today_m = commute.substr(4,2);
			var today_d = commute.substr(6,2);
			var today_h = commute.substr(8,2);
			var today_i = commute.substr(10,2);
			var now_h = now.substr(8,2);
			var now_i = now.substr(10,2);
			var working_h = working.substr(0,2);
			var working_i = working.substr(2,2);
			alert("1ȸ ������ �ۼ� ������Դϴ�.\n\n > <?=$prs_name?> "+today_m+"�� "+today_d+"�� \n\n ��� "+today_h+":"+today_i+"\n\n ��� "+now_h+":"+now_i+"\n\n �� �ٹ��ð� "+working_h+":"+working_i+"");
		}
	*/
		if (gubun == 1)
		{
			var msg = "���üũ�� �Ͻðڽ��ϱ�?";
		}
		else if (gubun == 2)
		{
			var msg = "�̹� ���üũ�� �ϼ̽��ϴ�. ���üũ�� �Ͻðڽ��ϱ�?";
		}
		else if (gubun == 3)
		{
			var msg = "��������� ���üũ�� �Ǿ����� �ʽ��ϴ�. \n\���üũ�� �Ͻðڽ��ϱ�? \n\(��ٽð��� ���� ��ٽð����ݿ��˴ϴ�.";
		}
		else if (gubun == 4)
		{
			var msg = "�̹� ���� ���üũ�� �ϼ̽��ϴ�.  \n\���üũ�� �Ͻðڽ��ϱ�? \n\(��ٽð��� ���� ��ٽð��� �ݿ��˴ϴ�.";
		}

		if(confirm(msg)){
			frm = document.form;
			frm.target	= "hdnFrame";
			frm.action = "commuting/commute_check_act2.php";
			frm.submit();
		}else{
			return;
		}
	}

	//���ΰԽù��б�
	function funView(seqno,type)
	{
		var goUrl;

		if (type == "default")
		{
			goUrl = "/board/board_detail.php?board=default&seqno="+seqno;
		}
		else
		{
			goUrl = "/book/book_detail.php?board="+ type + "&seqno="+seqno;
		}
		var frm = document.form;
		frm.target="_self";
		frm.action = goUrl;
		frm.submit();		
		
	}

	//�������� �ۼ�
	function go_weekly(){
		document.location.href="/weekly/weekly_list.php";
	}

<? if (!in_array($prs_position,$positionS_arr)){ ?>
	$(document).ready(function(){
	<? if ($alert_state1 == "inline"){ ?>
		if ($.cookie('check_todayView1') == "close")
		{
			$("#popAlert1").css("display","none");
		}
		else
		{
			$("#popAlert1").css("display","inline");
		}
	<? } else { ?>
		$("#popAlert1").css("display","none");
	<? } ?>
	<? if ($alert_state2 == "inline"){ ?>
		if ($.cookie('check_todayView2') == "close")
		{
			$("#popAlert2").css("display","none");
		}
		else
		{
			$("#popAlert2").css("display","inline");
		}
	<? } else { ?>
		$("#popAlert2").css("display","none");
	<? } ?>
	<? if ($alert_state3 == "inline"){ ?>
		if ($.cookie('check_todayView3') == "close")
		{
			$("#popAlert3").css("display","none");
		}
		else
		{
			$("#popAlert3").css("display","inline");
		}
	<? } else { ?>
		$("#popAlert3").css("display","none");
	<? } ?>
	});
<? } ?>
</script>
</head>
<body>
<div class="wrapper">
<form method="post" name="form">

		<div class="inner-home">
			<p class="hello"><?=$prs_name ?> <?=$prs_position?>�� Hello! 
			<!-- <? if ($prf_id != 7) { ?>(���� �ٹ��߻��: <u><?=$work_count['TOT']?></u>��)<? } ?> -->
			</p>
			<div class="home clearfix">
			<table class="notable" width="100%">
				<colgroup><col width="*" /></colgroup>
				<tr>
					<td valign="top">
						<!-- commute -->
					
						<div class="commutes">
							<input type="hidden" size="30" name="timeContent" style="font-size:15px; font-weight:bold; color:#000; border:0; font-family:dotum, ����, gulim, ����" readonly>
							<p class="dates"><input type="text" size="30" name="rtcInput2" style="font-size:16px; font-weight:bold; color:#000; border:0; font-family:dotum, ����, gulim, ����" readonly></p>
							<!-- <img src="img/icon_watch.gif" width="35">&nbsp;&nbsp;&nbsp; --><input type="text" size="10" name="rtcInput" style="font-size:47px; text-align:left; color:#000; border:none; padding:0;" readonly><!--�ð� -->
								<br>
								<br>
								<br>
					<?
						if ($today_gubun1 >= 10)
						{
					?>
							<div>! �ް��踦 �����ϼ̽��ϴ�.<br>�����üũ�� ���Ͻø� �ް��� ������ ��û�� �ּ���.</div>
					<?
						}
						else
						{
							if (REMOTE_IP == "119.192.230.239" || REMOTE_IP == "221.146.201.169")
							{
					?>

							<div>
							<?
							//���
								if ($time_gubun == "before")
								{
									if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "")
									{
										echo "<img src='img/icon_a.gif' alt='���üũ' /> <span>". substr($yesterday_checktime1,8,2) .":". substr($yesterday_checktime1,10,2) ."</span><br/>";
									}
									else
									{
										echo "<a href='javascript:go_office();' onClick='return !count++'><img src='img/icon_a.gif' alt='���üũ' /></a> <span>--:--</span><br/>";
									}
								}
								else
								{
									if ($today_checktime1 == "") 
									{
										echo "<a href='javascript:go_office();' onClick='return !count++'><img src='img/icon_a.gif' alt='���üũ' /></a> <span>--:--</span><br/>";
									}
									else
									{
										echo "<img src='img/icon_a.gif' alt='���üũ' /> <span>". substr($today_checktime1,8,2) .":". substr($today_checktime1,10,2) ."</span><br/>";
									}
								}
								/*
								if ($today_checktime1 == "") 
								{
									echo "<a href='javascript:go_office();' onClick='return !count++'><img src='img/icon_a.gif' alt='���üũ' /></a> <span>--:--</span><br/>";
								}
								else
								{
									echo "<img src='img/icon_a.gif' alt='���üũ' /> <span>". substr($today_checktime1,8,2) .":". substr($today_checktime1,10,2) ."</span><br/>";
								}
								*/	
									
								//����
								if ($off_check == "Y")
								{
									if ($last_off_endtime == "") 
									{
										echo "<a href='javascript:off_office(\"comeback\");'><span style='display:none; margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>����</span></a> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ --:--</span></a><br/>";
									}
									else
									{
										echo "<a href='javascript:off_office(\"goout\");'><span style='display:none; margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>����</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span></a><br/>";
									}
								}
								else
								{
									if ($today_checktime1 != "") 
									{
										echo "<a href='javascript:off_office(\"goout\");'><span style='display:none; margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>����</span></a><br/>";
									}
									else
									{
										echo "<span style='display:none; margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>����</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span><br/>";
									}
								}
								if ($off_check == "Y")
								{
									if ($last_off_endtime == "") 
									{
										$end_check = "N";
									}
									else
									{
										$end_check = "Y";
									}
								}
								else
								{
									$end_check = "Y";
								}
								

								//���
								/* 
									���üũ�� �ȵ����� �ϴ� ������ ���üũ�� ���ƾ� �Ѵ� ������ 
										����1. ���� ���üũ�� �Ǿ��ְ�
										����2. �����Ѿ� ��ħ8�� �����̰�
										����3. �����Ѿ� ���üũ�� �ȵǾ��ְ�
										����4. �����Ѿ� ���üũ�� �Ǿ� �־ �ް�����
									�̶��� ���üũ�� �����ϰԲ� ���� ����
								*/
								$gbArray1 = array(4,8,6,10,11,12,13,14,15,16,17,18,19,20,21);
								$gbArray2 = array(5,9);

								$chk_gb1 = in_array($today_gubun1,$gbArray1);
								$chk_gb2 = in_array($today_gubun2,$gbArray2);
								if ($end_check == "Y")
								{
									if ($today_checktime2 != "")												//��� �ߺ�üũ
									{
										echo "<a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='���üũ' /></a> <span>". substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2) ."</span>";
									}
									else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//���� ���üũ X, ���� ���üũ X
									{
										echo "<img src='img/icon_b.gif' alt='���üũ' /> <span>--:--</span>";
									}
									else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ X, 08:00����(����2)
									{
										echo "<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
									}
									else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����(����2)
									{
										echo "<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
									}
									else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����
									{
										echo "<img src='img/icon_b.gif' alt='���üũ' /> <span>--:--</span>	";
									}
									else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ X, 08:00����(����4)
									{
										echo "<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
									}
									else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ O, 08:00����(����4)
									{
										echo "<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
									}
									else if ($today_checktime1 != "" && $today_checktime2 == "")		//���üũ - ���� ���üũ O, ���� ���üũ X
									{
										echo "<a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='���üũ' /></a> <span>--:--</span>";
									}
									else															//���� ���üũ X, ���� ���üũ X
									{
										echo "<img src='img/icon_b.gif' alt='���üũ' /> <span>--:--</span>";
									}
								}
								else
								{
									echo "<img src='img/icon_b.gif' alt='���üũ' /> <span>--:--</span>";
								}
							?>
							</div>
					<?
							}
							else if (REMOTE_IP == "0" || REMOTE_IP == "0")
							{
					?>
							<div>
								<img src='img/icon_a.gif' alt='���üũ' /><span>
							<?
								//���
								if ($time_gubun == "before")
								{
									if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "")
									{
										echo substr($yesterday_checktime1,8,2) .":". substr($yesterday_checktime1,10,2);
									}
									else
									{
										echo "--:--";
									}
								}
								else
								{
									if ($today_checktime1 == "") 
									{
										echo "--:--";
									}
									else
									{
										echo substr($today_checktime1,8,2) .":". substr($today_checktime1,10,2);
									}
								}
					?>
								</span><br/>
								<img src='img/icon_b.gif' title='���üũ' /><span>
					<?
								//���
								if ($today_checktime2 != "")												//��� �ߺ�üũ
								{
									echo substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2);
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//���� ���üũ X, ���� ���üũ X
								{
									echo "--:--";
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ X, 08:00����
								{
									echo "--:--";
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����
								{
									echo "--:--";
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����
								{
									echo "--:--";
								}
								else if ($today_checktime1 != "" && $today_checktime2 == "")		//���üũ - ���� ���üũ O, ���� ���üũ X
								{
									echo "--:--";
								}
								else															//���� ���üũ X, ���� ���üũ X
								{
									echo "--:--";
								}
							?>
								</span>
							</div>
					<?
							}
							else
							{
					?>
							<div>! ����� üũ�� �系������ �����մϴ�.</div>
					<?
							}
						}
					?>
						</div>		
						<input type="hidden" name="time_gubun" id="time_gubun" value="<?=$time_gubun?>">
					</td>
					</tr>

				</table>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>

<div id="popAlert1" class="main-alert" style="display:none;">
	<div class="pop_top">
		<p class="pop_title">�˸�</p>
		<a href="javascript:HidePop('Alert1');" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
		<p class="intra_pop_info">
		<? if ($prf_id == 7) { ?>
			�� �ٹ��� ���(���)�� ���������� ��ϵ���<br>
			�ʾҽ��ϴ�. <br>
		<? } else { ?>
			�� �ٹ��� ���(���)�� ���������� ��ϵ���<br>
			�ʾҽ��ϴ�. �����Ͽ� �ް� ����� ����ϰų�<br>
			���¼�����û �Խ����� �̿��� �ּ���.
		<? } ?>
		</p>
		<p class="pop_notify">
			<input type="checkbox" id="check_todayView1" name="check_todayView1" style="vertical-align: middle;">
			<label for="check_todayView1" style="cursor:pointer;">���� �Ϸ� �� �̻� ���� �ʱ�</label>
		</p>
		<div class="adit_btn">
			<a href="javascript:CheckPop('check_todayView1','commuting');"><img src="img/btn_ok.gif" alt="Ȯ��"></a>
		</div>
	</div>
</div>

<div id="popAlert2" class="main-alert" style="display:none;">
	<div class="pop_top">
		<p class="pop_title">�˸�</p>
		<a href="javascript:HidePop('Alert2');" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
		<p class="intra_pop_info">
		<? if ($prf_id == 7) { ?>
			�� �ٹ��� �ٹ� �ð��� �̴� �Ǿ����ϴ�.<br><br>
		<? } else { ?>
			�� �ٹ��� �ٹ� �ð��� �̴� �Ǿ����ϴ�.<br>
			���ڰ��� �޴����� �������� �ۼ��� �ּ���.<br><br>
		<? } ?>
		</p>
		<p class="pop_notify">
			<input type="checkbox" id="check_todayView2" name="check_todayView2" style="vertical-align: middle;">
			<label for="check_todayView2" style="cursor:pointer;">���� �Ϸ� �� �̻� ���� �ʱ�</label>
		</p>
		<div class="adit_btn">
			<a href="javascript:CheckPop('check_todayView2','commuting');"><img src="img/btn_ok.gif" alt="Ȯ��"></a>
		</div>
	</div>
</div>

<div id="popAlert3" class="main-alert" style="display:none;">
	<div class="pop_top">
		<p class="pop_title">�˸�</p>
		<a href="javascript:HidePop('Alert3');" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
		<p class="intra_pop_info">
		<? if ($prf_id == 7) { ?>
			���� ��� üũ�� ���� �ʾҽ��ϴ�.<br><br>
		<? } else { ?>
			���� ��� üũ�� ���� �ʾҽ��ϴ�.<br>
			���¼�����û �Խ����� �̿��� �ּ���.<br><br>
		<? } ?>
		</p>
		<p class="pop_notify">
			<input type="checkbox" id="check_todayView3" name="check_todayView3" style="vertical-align: middle;">
			<label for="check_todayView3" style="cursor:pointer;">���� �Ϸ� �� �̻� ���� �ʱ�</label>
		</p>
		<div class="adit_btn">
		<? if ($prf_id == 7) { ?>
			<a href="javascript:CheckPop('check_todayView3','commuting');"><img src="img/btn_ok.gif" alt="Ȯ��"></a>
		<? } else { ?>
			<a href="javascript:CheckPop('check_todayView3','edit');"><img src="img/btn_ok.gif" alt="Ȯ��"></a>
		<? } ?>
		</div>
	</div>
</div>
</body>
</html>