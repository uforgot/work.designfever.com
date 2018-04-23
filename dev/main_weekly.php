<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
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
?>

<script type="text/javascript">
	var count = 0;

	//----�ð� ��� ��ũ��Ʈ 
	var days_k=new Array()
	days_k[0]="��";
	days_k[1]="��";
	days_k[2]="ȭ";
	days_k[3]="��";
	days_k[4]="��";
	days_k[5]="��";
	days_k[6]="��";

	var xmlHttp;
	function srvTime(){
		if(window.XMLHttpRequest){                          // IE �̿ܿ���  XMLHttpRequest ����
			req = new XMLHttpRequest();
		}else if(window.ActiveXObject){                     // IE����  XMLHttpRequest ����
			req = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlHttp = req;
		xmlHttp.open('HEAD',window.location.href.toString(),false);
		xmlHttp.setRequestHeader("Content-Type", "text/html");
		xmlHttp.send('');
		return xmlHttp.getResponseHeader("Date");
	}

	function realtimeClock() {
		document.form.rtcInput.value = getTimeStamp();
		document.form.rtcInput2.value = getTimeStamp2();
		setTimeout("realtimeClock()", 1000);
	}

	function getTimeStamp() { // 24�ð���
		var st = srvTime();	
		var d = new Date(st);
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
		var st = srvTime();	
		var d = new Date(st);
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

	function leadingZeros(n, digits) {
		var zero = '';
		n = n.toString();

		if (n.length < digits) {
		for (i = 0; i < digits - n.length; i++)
		  zero += '0';
		}
		return zero + n;
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

	$(document).ready(function(){
		realtimeClock();
<? if ($prs_team != "CreativeDa"){ ?>
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
<? } ?>
	});
</script>
</head>
<body>
<div class="wrapper">
<form method="post" name="form">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<p class="hello">+ <?=$prs_name ?> <?=$prs_position?>�� Hello!</p>
			<div class="home clearfix">
			<table class="notable" width="100%">
				<colgroup><col width="*" /><col width="22" /><col width="240" /></colgroup>
				<tr>
				<td valign="top">
					<table class="notable eok" width="100%">
					<colgroup><col width="60%" /><col width="40%" /></colgroup>
					<tr>
					<td>
						<div class="clearfix infos">
							
							<div class="pics"><?=getProfileImg($prs_img,138);?></div>
							
							<div class="inf-weekly">
<!--
								<span></span>
								<span></span>
							-->
								<span><?=$prs_team?></span>
								<span><?=$prs_position?> <?=$prs_name?></span>
								<span><a href="/member/modify.php" title="�����ʼ���"><img src="img/btn_modipro2.png" alt="ȸ����������" /></a></span>
								<!-- 2014. 9. 17 ���� weekly ��ư �߰�-->
								<span><?=getWeeklyBtn()?></span>
								<!-- -->
							</div>
						</div>
						<div class="clearfix below-info">
						<?
							$sql = "EXEC SP_MAIN_02 '$prs_id'";
							$rs = sqlsrv_query($dbConn,$sql);

							$record = sqlsrv_fetch_array($rs);
							if (sqlsrv_has_rows($rs) > 0)
							{
								$to_count = $record['TO_COUNT'];
								$save_count = $record['SAVE_COUNT'];
								$my_count = $record['MY_COUNT'];
								$cc_count = $record['CC_COUNT'];
							}				
						?>
							<div>������ ����<br /><span><a href="/approval/approval_to_list.php"><?=$to_count?></a></span></div>
							<div>���� ������ ��� ����<br /><span><a href="/approval/approval_my_list.php"><?=$my_count?></a></span></div>
							<div class="l">������ ����<br /><span><a href="/approval/approval_cc_list.php"><?=$cc_count?></a></span></div>
						</div>
					</td>
					<td valign="top">
						<!-- commute -->
						<div class="commutess">
							<a href="commuting/commuting_list.php"><img src="img/icon_plus.gif" alt="" /></a>
						</div>
						<div class="commutes">
							<p class="dates"><input type="text" size="30" width="100" height="20" name="rtcInput2" style="font-size:15px; font-weight:bold; color:#000; border:0; font-family:dotum, ����, gulim, ����" readonly></p>
							<img src="img/icon_watch.gif" width="35">&nbsp;&nbsp;&nbsp;<input type="text" width="50" height="20" name="rtcInput"style="font-size:30px; color:#000; border:0;" readonly><!--�ð� -->
								<br>
								<br>
								<br>
						<?
							if (REMOTE_IP != "112.217.176.42" && REMOTE_IP != "112.217.176.43" && REMOTE_IP != "220.71.63.87")
							{
						?>
							<div>! ����� üũ�� �系������ �����մϴ�.</div>
						<?
							}
							else
							{
						?>

							<div>
							<?
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
									$yesterday_memo1 = $record['YESTERDAY_MEMO1'];			//���� ��� ��������
									$yesterday_memo2 = $record['YESTERDAY_MEMO2'];			//���� ��� ��������
									$today_gubun1 = $record['TODAY_GUBUN1'];			//���� ���
									$today_gubun2 = $record['TODAY_GUBUN2'];			//���� ���
									$today_checktime1 = $record['TODAY_CHECKTIME1'];	//���� ���	
									$today_checktime2 = $record['TODAY_CHECKTIME2'];	//���� ���
									$today_totaltime = $record['TODAY_TOTALTIME'];	//���� �ٹ��ð�
									$today_memo1 = $record['TODAY_MEMO1'];			//���� ��� ��������
									$today_memo2 = $record['TODAY_MEMO2'];			//���� ��� ��������

									if ($yesterday_checktime2 == "0") { $yesterday_checktime2 = ""; }
									if ($today_checktime2 == "0") { $today_checktime2 = ""; }
								}
								
								//���� ��� ���� ���� ����~8�� ��� ó���� ���� ������
								//�ٹ��ð� ���
								if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $today_checktime1 == "" && date("G") < 8) 
								{
									$time_gubun = "before";
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
								else
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
								//���
								/* 
									���üũ�� �ȵ����� �ϴ� ������ ���üũ�� ���ƾ� �Ѵ� ������ 
										����1. ���� ���üũ�� �Ǿ��־�� ��
										����2. �����Ѿ� ������ ��ħ8�ñ����ϰ��
										����3. �����Ѿ� ���üũ�� �ȵǾ��־����
									�̶��� ���üũ�� �����ϰԲ� ���� ����
								*/
								if ($today_checktime2 != "")												//��� �ߺ�üũ
								{
									echo "<a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='���üũ' /></a> <span>". substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2) ."</span>";
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//���� ���üũ X, ���� ���üũ X
								{
									echo "<img src='img/icon_b.gif' alt='���üũ' /> <span>--:--</span>";
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ X, 08:00����
								{
									echo "<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����
								{
									echo "<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
								}
								else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����
								{
									echo "<img src='img/icon_b.gif' alt='���üũ' /> <span>--:--</span>	";
								}
								else if ($today_checktime1 != "" && $today_checktime2 == "")		//���üũ - ���� ���üũ O, ���� ���üũ X
								{
									echo "<a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='���üũ' /></a> <span>--:--</span>";
								}
								else															//���� ���üũ X, ���� ���üũ X
								{
									echo "<img src='img/icon_b.gif' alt='���üũ' /> <span>--:--</span>";
								}
							?>
							<div>
						<?
							}
						?>
						</div>		
						<input type="hidden" name="time_gubun" id="time_gubun" value="<?=$time_gubun?>">
					</td>
					</tr>
					
					<!--  �Խ��� �̸� ���� ����Ʈ �۾� -->
					<tr>
						<td valign="top">
							<!-- notice -->
							<div class="noticess">
								<a href="board/board_list.php"><img src="img/icon_plus.gif" alt="" /></a>
							</div>
							<div class="notice-list">
							<ul>
							<?
								//�������� ����Ʈ
								$sql = "SELECT TOP 5 
											SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, HIT, REP_DEPTH, REG_DATE, FILE_1, FILE_2, FILE_3 
										FROM 
											DF_BOARD WITH (NOLOCK) 
										WHERE 
											TMP3 = 'default' AND NOTICE_YN = 'Y' 
										ORDER BY 
											SEQNO DESC";
								$rs = sqlsrv_query($dbConn, $sql);

								while ($record=sqlsrv_fetch_array($rs))
								{
									$col_seqno = $record['SEQNO'];
									$col_prs_id = $record['PRS_ID'];
									$col_prs_name = trim($record['PRS_NAME']);
									$col_prs_login = trim($record['PRS_LOGIN']);
									$col_prs_team = trim($record['PRS_TEAM']);
									$col_prs_position = trim($record['PRS_POSITION']);
									$col_title = trim($record['TITLE']);
									$col_hit = $record['HIT'];
									$col_rep_depth = $record['REP_DEPTH'];
									$col_reg_date = $record['REG_DATE'];
									$col_file_1 = trim($record['FILE_1']);
									$col_file_2 = trim($record['FILE_2']);
									$col_file_3 = trim($record['FILE_3']);
							?>
								<li class="clearfix nott ">						
								<a href="javascript:funView('<?=$col_seqno?>','default');" style="cursor:hand">
									<p><img src='img/icon_comment2.gif'> <?=getCutString($col_title,40);?>
									<? if ($col_file_1 != "" || $col_file_2 != "" || $col_file_3 != "") { echo "<img src='img/icon_clip.gif'>"; } ?>
									<? if ($col_rep_depth != "0") { echo "[". $col_rep_depth ."]"; } ?>
									</p>
								</a>
									<span><?=$col_prs_position?> <?=$col_prs_name?></span>
								</li>
							<?
								}
							?>
							</ul>
							</div>
						</td>
						<td valign="top">
							<!-- up-date -->
							<div class="updatess">
								<a href="board/board_list.php"><img src="img/icon_plus.gif" alt="" /></a>
							</div>
							<div class="update-list">
							<ul>
							<?
								//�Խù� ����Ʈ
								$sql = "SELECT TOP 5 
											SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, HIT, REP_DEPTH, REG_DATE, FILE_1, FILE_2, FILE_3, TMP3  
										FROM 
											DF_BOARD WITH (NOLOCK) 
										WHERE 
											TMP3 IN ('default','book','free') AND NOTICE_YN IN ('N','') 
										ORDER BY 
											SEQNO DESC";
								$rs = sqlsrv_query($dbConn, $sql);

								while ($record=sqlsrv_fetch_array($rs))
								{
									$col_seqno = $record['SEQNO'];
									$col_prs_id = $record['PRS_ID'];
									$col_prs_name = trim($record['PRS_NAME']);
									$col_prs_login = trim($record['PRS_LOGIN']);
									$col_prs_team = trim($record['PRS_TEAM']);
									$col_prs_position = trim($record['PRS_POSITION']);
									$col_title = trim($record['TITLE']);
									$col_hit = $record['HIT'];
									$col_rep_depth = $record['REP_DEPTH'];
									$col_reg_date = $record['REG_DATE'];
									$col_file_1 = trim($record['FILE_1']);
									$col_file_2 = trim($record['FILE_2']);
									$col_file_3 = trim($record['FILE_3']);
									$col_tmp3 = trim($record['TMP3']);
							?>
								<li class="clearfix nott ">						
								<a href="javascript:funView('<?=$col_seqno?>','<?=$col_tmp3?>');" style="cursor:hand">
									<p><img src='img/icon_comment2.gif'> <?=getCutString($col_title,34);?>
									<? if ($col_file_1 != "" || $col_file_2 != "" || $col_file_3 != "") { echo "<img src='img/icon_clip.gif'>"; } ?>
									<? if ($col_rep_depth != "0") { echo "[". $col_rep_depth ."]"; } ?>
									</p>
								</a>
								</li>
							<?
								}
							?>
							</ul>
							</div>									
						</td>
					</tr>
					<!--  �Խ��� �̸� ���� ����Ʈ �۾� �� -->
					
				</table>
				</td>
				<td>&nbsp;</td>
				<td valign="top">
					<div class="s">
					<script language="javascript" src="http://connect.facebook.net/ko_KR/all.js"></script>
					
					<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Ffeverbook&amp;width=241&amp;height=470&amp;show_faces=true&amp;colorscheme=light&amp;stream=true&amp;border_color&amp;header=false&amp;" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:241px; height:470px;" allowTransparency="true"></iframe>
					
					</div>

					<!--  congratulation -->
					<div>
					<table width="100%">
						<tr>
							<td style="padding:10px 0 0 10px; color:#000; font-weight:bold;">+ �� ���� ����</td>
						</tr>
						<tr>
							<td style="padding:5px 10px 0 50px; color:#000;">
							<ul>
						<?
								$sql = "SELECT A.PRS_NAME, A.PRS_POSITION, A.PRS_BIRTH, A.PRS_BIRTH_TYPE, B.SOLAR_DATE, B.LUNAR_DATE
										FROM DF_PERSON A WITH(NOLOCK), LUNAR2SOLAR B WITH(NOLOCK)
										WHERE A.PRF_ID IN (1,2,3,4) AND A.PRS_ID NOT IN (102) AND B.SOLAR_DATE LIKE '". date("Y-m") ."%' 
											AND (
												(SUBSTRING(A.PRS_BIRTH,6,5) = SUBSTRING(B.SOLAR_DATE,6,5) AND A.PRS_BIRTH_TYPE = '���') 
												OR (SUBSTRING(A.PRS_BIRTH,6,5) = SUBSTRING(B.LUNAR_DATE,6,5) AND A.PRS_BIRTH_TYPE = '����'))
										ORDER BY B.SOLAR_DATE, 
												CASE 
													WHEN A.PRS_POSITION='��ǥ' THEN 1
													WHEN A.PRS_POSITION='�̻�' THEN 2
													WHEN A.PRS_POSITION='����' THEN 3
													WHEN A.PRS_POSITION='����' THEN 4
													WHEN A.PRS_POSITION='å��' THEN 5
													WHEN A.PRS_POSITION='�븮' THEN 6
													WHEN A.PRS_POSITION='����' THEN 7
													WHEN A.PRS_POSITION='����' THEN 8
													WHEN A.PRS_POSITION='���' THEN 9
													WHEN A.PRS_POSITION='����' THEN 10 END, A.PRS_NAME";
								$rs = sqlsrv_query($dbConn, $sql);

								$pre_solar = "";
								while ($record = sqlsrv_fetch_array($rs))
								{
									$col_solar = $record['SOLAR_DATE'];
									$col_lunar = $record['LUNAR_DATE'];
									$col_prs_name = $record['PRS_NAME'];
									$col_prs_position = $record['PRS_POSITION'];
									$col_prs_birth = $record['PRS_BIRTH'];
									$col_prs_birth_type = $record['PRS_BIRTH_TYPE'];

									if ($pre_solar != $col_solar)
									{
										echo "<li style='padding-top:5px;'>";
										echo substr($col_solar,8,2) ."�� : ";
									}
									else
									{
										echo "<li style='padding-top:2px;'>";
										echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									}

									echo $col_prs_position ." ". $col_prs_name;

									if ($col_prs_birth_type == "����")
									{
										echo " (�� ". str_replace("-",".",substr($col_prs_birth,5,5)) .")";
									}
									echo "</li>";

									$pre_solar = $col_solar;
								}
						?>
							</ul>
							</td>
						</tr>
					</table>
					</div>
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
			�� �ٹ��� ������ ���(���)�� ����������<br>
			��ϵ��� �ʾҽ��ϴ�. �����Ͽ� �ް� �����<br>
			����ϰų� �����ڿ��� ���� ��û�� ���ּ���.
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
			�� �ٹ��� �ٹ� �ð��� �̴� �Ǿ����ϴ�.<br>
			���ڰ��� �޴����� �������� �ۼ��� �ּ���.<br><br>
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
</body>
</html>