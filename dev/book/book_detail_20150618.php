<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("��ϴ��,Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$board = isset($_REQUEST['board']) ? $_REQUEST['board'] : "book"; 

	if ($board == "happy" && in_array($prs_id,$happyLab_arr) == false) 
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ູ������ Ȱ���������Ը� ������ �Խ����Դϴ�.");
		history.back();
	</script>
<?
		exit;
	}

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$retUrl = "book_list.php?page=". $page ."&board=". $board;
	if ($subject != "")
	{
		$retUrl .= "&subject=". $subject;
	}
	if ($keyword != "")
	{
		$retUrl .= "&keyfield=". $keyfield ."&keyword=". $keyword;
	}

	if ($seqno == "")
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ش� ���� �������� �ʽ��ϴ�.");
		history.back();
	</script>
<?
		exit;
	}

	$searchSQL = " WHERE SEQNO = '$seqno'";
	
	if ($type == "")
	{
		$sql = "UPDATE DF_BOARD SET
					HIT = HIT + 1". $searchSQL;
		$rs = sqlsrv_query($dbConn,$sql);		
	}

	$sql = "SELECT
				PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(VARCHAR(16),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3, TMP1 
			FROM
				DF_BOARD WITH(NOLOCK)".	$searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
	{
		$book_id = $record['PRS_ID'];
		$book_name = $record['PRS_NAME'];
		$book_login = $record['PRS_LOGIN'];
		$book_team = $record['PRS_TEAM'];
		$book_position = $record['PRS_POSITION'];
		$book_title = $record['TITLE'];
		$book_contents = $record['CONTENTS'];
		$book_hit = $record['HIT'];
		$book_depth = $record['REP_DEPTH'];
		$book_notice = $record['NOTICE_YN'];
		$book_date = $record['REG_DATE'];
		$book_file1 = trim($record['FILE_1']);
		$book_file2 = trim($record['FILE_2']);
		$book_file3 = trim($record['FILE_3']);
		$book_tmp1 = trim($record['TMP1']);
	}
	else
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ش� ���� �������� �ʽ��ϴ�.");
		history.back();
	</script>
<?
		exit;
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function goEdit()//���� ����ȭ������...
	{
		var frm = document.form;
			frm.target = '_self';
			frm.type.value = 'modify';
			frm.action = 'book_write.php';
			frm.submit();
	}
	function goDel() //���� ����
	{
		if(!confirm("���� �Ͻðڽ��ϱ�?")){
			return;
		}
			else
		{
		  var frm = document.form;
		  frm.target = 'hdnFrame';
		  frm.type.value = 'delete';
		  frm.action = 'book_write_act.php';
		  frm.submit();
		}
	}
	function textcounter(field, countfield, maxlimit) { //��� textarea���ڼ� 200�� ���� ��ũ��Ʈ
		  tempstr = field.value;
		  countfield.value = maxlimit - tempstr.length;
			if (maxlimit - tempstr.length < 0) {
						 alert(maxlimit+"���ڸ� �ʰ��� �� �����ϴ�.");
						 //document.form.remlen.focus();       //��Ŀ���� �̵���Ű�� ���� ��� ���ڰ� ������
						 tempstr = tempstr.substring(0,maxlimit); //��Ŀ�� �̵� �� ���� �ڸ���
						 field.value = tempstr;
						 countfield.value = maxlimit - tempstr.length;
						 //document.form.reply_contents.focus();  //��Ŀ���� �Է»��ڷ� �ǵ�����
	   }
	 }
	 
	 
	function textcounter2(field, countfield, maxlimit) { //����Ǵ�� ���ڼ� 200�� ���� ��ũ��Ʈ
		  tempstr = field.value;
		  countfield.value = maxlimit - tempstr.length;
			if (maxlimit - tempstr.length < 0) {
						 alert(maxlimit+"���ڸ� �ʰ��� �� �����ϴ�.");
						 //document.form.remlen2.focus();       //��Ŀ���� �̵���Ű�� ���� ��� ���ڰ� ������
						 tempstr = tempstr.substring(0,maxlimit); //��Ŀ�� �̵� �� ���� �ڸ���
						 field.value = tempstr;
						 countfield.value = maxlimit - tempstr.length;
						 //document.form.reply_contents2.focus();  //��Ŀ���� �Է»��ڷ� �ǵ�����
	 }
	}
	 
	function writeReply(){ //��� �ޱ�
		var frm = document.form;
		if(frm.reply_contents.value.length < 1){
			alert("������ �Է����ּ���");
			frm.reply_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'write_reply';
		frm.action = 'book_reply_act.php';
		frm.submit();
	}; 

	function writeReply2(replyno,replyid){ //����� ��� �ޱ�  
		var reply_contents2 = document.getElementsByName("reply_contents2_"+replyno).item(0).value; //!!!!
		reply_contents2 = reply_contents2.replace(/(\n)/g, "<br>");	//����Ű�� br�� ġȯ(documentsbyname���� �ޱ� ������ ��ũ��Ʈ �ܿ��� ó��)
		
		var frm = document.form;
			frm.target = 'hdnFrame';
			frm.type.value = 'write_reply2';
			frm.action = 'book_reply_act.php?reply_no='+replyno+'&reply_id='+replyid+'&reply_contents2='+reply_contents2+'&keyfield='+frm.keyfield.value+'&keyword='+frm.keyword.value+'&page='+frm.page.value+'&seqno='+frm.seqno.value;
			frm.submit();
	}

	//��� ���� (��۹�ȣ)
	function mod_Reply(replyno){
		
		document.getElementById("c_reply_"+replyno).style.display = "none";
		document.getElementById("modify_c_reply_"+replyno).style.display = "block";
		document.getElementById("reply_contents_"+replyno).focus();

	}
	//��� ���� ���� (��۹�ȣ)
	function modifyReply(replyno){ 
		var frm = document.form;
		var modify_contents = document.getElementById("reply_contents_"+replyno); //!!!!

		if(modify_contents.value == ""){
			alert("������ �Է����ּ���");
			modify_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'modify_reply';
		frm.modify_contents.value = modify_contents.value;
		frm.action = 'book_reply_act.php?reply_no='+replyno;
		frm.submit();
	}

	//��� ���� //����� ��� ���� (��۹�ȣ, ����Ǵ�۹�ȣ)
	function mod_Reply2(replyno,r_replyno){
		
		document.getElementById("c_re_reply_"+replyno+"_"+r_replyno).style.display = "none";
		document.getElementById("modify_c_re_reply_"+replyno+"_"+r_replyno).style.display = "block";
		document.getElementById("reply_contents_"+replyno+"_"+r_replyno).focus();

	}
	//��� ���� ���� //����� ��� ���� (��۹�ȣ, ����Ǵ�۹�ȣ)
	function modifyReply2(replyno,r_replyno){ 
		var frm = document.form;
		var modify_contents = document.getElementById("reply_contents_"+replyno+"_"+r_replyno); //!!!!

		if(modify_contents.value == ""){
			alert("������ �Է����ּ���");
			modify_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'modify_reply2';
		frm.modify_contents.value = modify_contents.value;
		frm.action = 'book_reply_act.php?reply_no='+replyno+'&r_reply_no='+r_replyno;
		frm.submit();
	}

	function delReply(replyno){ //��� ����
		var frm = document.form;
		if(!confirm("����� ���� �Ͻðڽ��ϱ�?")){
			return;
		}
		else
		{		
			frm.target = 'hdnFrame';
			frm.type.value = 'delete_reply';
			frm.action = 'book_reply_act.php?reply_no='+replyno;
			frm.submit();
		}
	}

	function delReply2(replyno,r_replyno){ //����� ��� ����
		var frm = document.form;
		if(!confirm("����� ���� �Ͻðڽ��ϱ�?")){
			return;
		}
		else
		{		
			frm.target = 'hdnFrame';
			frm.type.value = 'delete_reply2';
			frm.action = 'book_reply_act.php?reply_no='+replyno+'&r_reply_no='+r_replyno;
			frm.submit();
		}
	}

	function view_reply(num){		//����Ǵ�۴ޱ� Ŭ���� ��Ÿ���� divâ
		
		var total = document.getElementById("add_comment_"+num);
		var focus = document.getElementById("reply_contents2_"+num);

		if(total.style.display=="none"){
			total.style.display="block";
			focus.focus();
		}else{
			total.style.display="none";
		}
	}
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form" onKeyDown="javascript:if (event.keyCode == 13) {funSearch();}">
<input type="hidden" name="board" value="<?=$board?>">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="subject" value="<?=$subject?>">
<input type="hidden" name="keyfield" value="<?=$keyfield?>">
<input type="hidden" name="keyword" value="<?=$keyword?>">
<input type="hidden" name="type" value="">						<!-- ��ϼ����������� -->
<input type="hidden" name="seqno" value="<?=$seqno?>">		<!-- �۹�ȣ -->
<input type="hidden" name="writer" value="<?=$book_login?>">	<!-- ���ۼ��� prs_login -->
<input type="hidden" name="writer_id" value="<?=$book_id?>">	<!-- ���ۼ��� prs_id -->
<input type="hidden" name="modify_contents" id="modify_contents">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">

			<p class="hello work_list">
		<? if ($board == "book") { ?>
			<a href="book_list.php?board=book"><strong>+  ȸ���Ȱ�鼭</strong></a>
			<a href="book_list.php?board=free">+  �����Խ���</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  ��ȣȸ�Խ���</a>
			<a href="book_list.php?board=edit">+  ���¼�����û</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  �ູ������</a>
			<? } ?>
		<? } else if ($board == "free") { ?>
			<a href="book_list.php?board=book">+  ȸ���Ȱ�鼭</a>
			<a href="book_list.php?board=free"><strong>+  �����Խ���</strong></a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  ��ȣȸ�Խ���</a>
			<a href="book_list.php?board=edit">+  ���¼�����û</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  �ູ������</a>
			<? } ?>
		<? } else if ($board == "ilab") { ?>
			<a href="book_list.php?board=book">+  ȸ���Ȱ�鼭</a>
			<a href="book_list.php?board=free">+  �����Խ���</a>
			<a href="book_list.php?board=ilab"><strong>+  iLab</strong></a>
			<a href="book_list.php?board=club">+  ��ȣȸ�Խ���</a>
			<a href="book_list.php?board=edit">+  ���¼�����û</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  �ູ������</a>
			<? } ?>
		<? } else if ($board == "club") { ?>
			<a href="book_list.php?board=book">+  ȸ���Ȱ�鼭</a>
			<a href="book_list.php?board=free">+  �����Խ���</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club"><strong>+  ��ȣȸ�Խ���</strong></a>
			<a href="book_list.php?board=edit">+  ���¼�����û</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  �ູ������</a>
			<? } ?>
		<? } else if ($board == "edit") { ?>
			<a href="book_list.php?board=book">+  ȸ���Ȱ�鼭</a>
			<a href="book_list.php?board=free">+  �����Խ���</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  ��ȣȸ�Խ���</a>
			<a href="book_list.php?board=edit"><strong>+  ���¼�����û</strong></a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  �ູ������</a>
			<? } ?>
		<? } else if ($board == "happy") { ?>
			<a href="book_list.php?board=book">+  ȸ���Ȱ�鼭</a>
			<a href="book_list.php?board=free">+  �����Խ���</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  ��ȣȸ�Խ���</a>
			<a href="book_list.php?board=edit">+  ���¼�����û</a>
			<a href="book_list.php?board=happy"><strong>+  �ູ������</strong></a>
		<? } ?>
			</p>
			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
					<div class="btn_left">
						<a href="<?=$retUrl?>"><img src="../img/btn_list.gif" alt="��Ϻ���" /></a>
					</div>
					<div class="btn_right btn_nomargin">
					<? if ($book_id == $prs_id || $prf_id == "4") {	//�ۼ��ڴ� ����,���� ����?>
						<a href="javascript:goDel()"><img src="../img/btn_del.gif" alt="�� ����" /></a> 
						<a href="javascript:goEdit()"><img src="../img/btn_modi.gif" alt="�� ����" /></a>
					<? } ?>
					</div>
				</div>
				<div class="board_view">
					<div class="view_head clearfix">
						<div class="fl">
							<div>
							<? if ($book_file1 != "" || $book_file2 != "" || $book_file3 != "") { ?>
								<span class="hasfile">÷������</span>
							<? } else { ?>
								<span class="nofile">nofile</span>
							<? } ?>
							<? if (($book_tmp1 != "") && ($board == "ilab")) { ?>
								<p>[<?=$book_tmp1?>] <?=$book_title?></p>
							<? } else { ?>
								<p><?=$book_title?></p>
							<? } ?>
							<? if ($book_depth != "0") { ?>
								<span class="hascomment">[<span><?=$book_depth?></span>]</span>
							<? } ?>
							</div>
							<input type="hidden" name ="rep_depth" value="<?=$book_depth?>">	<!-- ��۰��� -->
						</div>
						
						<div class="fr">
							<div>����� : <strong><span><?=$book_date?></span></strong></div>							
							<div>��ȸ�� : <strong><?=$book_hit?></strong></div>
							<div class="noline">�ۼ��� : <span><?=$book_team?></span>&nbsp;&nbsp;&nbsp;<strong><?=$book_position?>&nbsp;<?=$book_name?></strong></div>
						</div>
					</div>
					<div class="view_body">
						<div class="view_text">							
							<p><?=$book_contents?><br></p>
						</div>
						<div class="view_text">
						<? if ($book_file1 != "" || $book_file2 != "" || $book_file3 != "") { ?>					
							÷������ : 
							<div style="position:relative; left:100px; top:-25px;">
								<? if ($book_file1 != "") { ?><strong><a href="javascript:file_download('book','<?=$book_file1?>');"><?=$book_file1?></a></strong><br><? } ?>
								<? if ($book_file2 != "") { ?><strong><a href="javascript:file_download('book','<?=$book_file2?>');"><?=$book_file2?></a></strong><br><? } ?>
								<? if ($book_file3 != "") { ?><strong><a href="javascript:file_download('book','<?=$book_file3?>');"><?=$book_file3?></a></strong><? } ?>
							</div>
						<? } ?>
						</div>
						<!-- //�ش� �Խù��� ���� ��������� �����ְ� ������ ���� -->
						<div class="view_comment">
							<ul>
								<li>
									<!--��� for�� ������ ����Ѵ� -->
								<?
									$sql = "SELECT 
												SEQNO, PRS_ID, REPLYNO, R_PRS_ID, R_PRS_NAME, R_PRS_POSITION, R_CONTENTS, R_REPLY_DEPTH, CONVERT(VARCHAR(20),R_REG_DATE,120) AS R_REG_DATE, R_TMP_1, R_TMP_2
											FROM 
												DF_BOARD_REPLY WITH(NOLOCK)
											WHERE 
												SEQNO = '$seqno'
											ORDER BY 
												R_REG_DATE";
									$rs = sqlsrv_query($dbConn, $sql);
									while ($record=sqlsrv_fetch_array($rs))
									{
										$reply_seqno = $record['SEQNO'];
										$reply_id = $record['PRS_ID'];
										$reply_no = $record['REPLYNO'];
										$reply_r_id = $record['R_PRS_ID'];
										$reply_r_name = $record['R_PRS_NAME'];
										$reply_r_position = $record['R_PRS_POSITION'];
										$reply_r_contents = $record['R_CONTENTS'];
										$reply_r_depth = $record['R_REPLY_DEPTH'];
										$reply_r_date = $record['R_REG_DATE'];
										$reply_r_tmp1 = $record['R_TMP_1'];
										$reply_r_tmp2 = $record['R_TMP_2'];
							
										$replySQL = "SELECT 
														SEQNO, REPLYNO, R_PRS_ID, RR_PRS_ID, RR_PRS_NAME, RR_PRS_POSITION, RR_CONTENTS, RR_REPLY_DEPTH, CONVERT(VARCHAR(20),RR_REG_DATE,120) AS RR_REG_DATE, RR_TMP_1
													FROM 
														DF_BOARD_REPLY2 WITH(NOLOCK)
													WHERE
														SEQNO = '$reply_seqno' AND REPLYNO = '$reply_no'
													ORDER BY 
														RR_REG_DATE";
										$replyRs = sqlsrv_query($dbConn, $replySQL);
								?>
									<? if ($reply_r_tmp1 == "Y") { ?>
										<div class="c_wrap" style="border-top:1px solid #b2b2b2">
											<div id="c_reply_<?=$reply_no?>">
												<div class="c_writer"><span><?=$reply_r_position?>&nbsp;<?=$reply_r_name?></span></div>
												<div class="c_text"><span id="c_text_<?=$reply_no?>"><?=str_replace("\r\n","<br>",$reply_r_contents);?></span> <a href="javascript:view_reply(<?=$reply_no?>)" class="w_re">+ ��۾���</a></div>
												<div class="c_date">
												<!-- ������ ���� ���� / �۾��̴� ���� ���� �Ѵ� ���� -->
												<? if ($reply_r_id == $prs_id) { ?>
													<a href="javascript:delReply(<?=$reply_no?>)" class="cd">�ۻ���</a>
													<a href="javascript:mod_Reply('<?=$reply_no?>')" class="cw">�ۼ���</a>
												<? } else if ($prf_id == "4") { ?>
													<a href="javascript:delReply(<?=$reply_no?>)" class="cd">�ۻ���</a>
												<? } ?>
													<span class="t"><?=substr($reply_r_date,11,5)?></span>
													<span class="d"><?=substr($reply_r_date,0,10)?></span>
												</div>
											</div>
											<div id="modify_c_reply_<?=$reply_no?>" style="display:none;">
												<div class="c_writer"><span><?=$reply_r_position?>&nbsp;<?=$reply_r_name?></span></div>
												<div class="c_add_input">
													<textarea name="reply_contents_<?=$reply_no?>" id="reply_contents_<?=$reply_no?>" style="width:100%;height:100%;" size="400" length="10" onkeyup="textcounter(this.form.reply_contents_<?=$reply_no?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents_<?=$reply_no?>, this.form.remlen,200);"><?=$reply_r_contents?></textarea>
													<input type="hidden" readonly name="remlen" size="3" maxlength="3" value="200">
												</div>
												<div class="c_add_btn" id="reply_btn_<?=$reply_no?>"><a href="javascript:modifyReply('<?=$reply_no?>')">����</a></div>
											</div>
										</div>
									<? } else if ($reply_r_tmp1 == "N" && sqlsrv_has_rows($replyRs) > 0) { ?>
										<div class="c_wrap">
											<div class="c_writer"><span>&nbsp;</span></div>
											<div class="c_text"><font color="#8C8C8C">������ ����Դϴ�</font></div>
											<div class="c_date">
												<span class="t"></span>
												<span class="d"></span>
											</div>
										</div>
									<? } ?>
										<!--��� �� ��۴ޱ� div Ŭ���ú�����-->
										<div id="add_comment_<?=$reply_no?>" name="add_comment_<?=$reply_no?>" class="c_add_comment" style="display:none"> 
											<div class="c_writer"></div>
											<div class="c_add_input">
												<textarea name="reply_contents2_<?=$reply_no?>" id="reply_contents2_<?=$reply_no?>" style="width:100%;height:100%;" size="400" length="10" onkeyup="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);"></textarea>
												<input type="hidden" readonly name="remlen2" size="3" maxlength="3" value="200">
												<td>* 200���̳��� �ۼ����ּ���</td>
											</div>
											<div class="c_add_btn" id="c_reply_btn_<?=$reply_no?>"><a href="javascript:writeReply2('<?=$reply_no?>','<?=$reply_id?>')">���</a></div>
										</div>
								<?
										while($replyRecord = sqlsrv_fetch_array($replyRs))
										{
											$reply_r_seqno = $replyRecord['SEQNO'];
											$reply_rr_no = $replyRecord['REPLYNO'];
											$reply_r_id = $replyRecord['R_PRS_ID'];
											$reply_rr_id = $replyRecord['RR_PRS_ID'];
											$reply_rr_name = $replyRecord['RR_PRS_NAME'];
											$reply_rr_position = $replyRecord['RR_PRS_POSITION'];
											$reply_rr_contents = $replyRecord['RR_CONTENTS'];
											$reply_rr_depth = $replyRecord['RR_REPLY_DEPTH'];
											$reply_rr_date = $replyRecord['RR_REG_DATE'];
											$reply_rr_tmp_1 = $replyRecord['RR_TMP_1'];
								?>
										<!-- ����� ��� for������ ������ ����Ѵ� -->
										<div class="c_re_comment">
											<div id="c_re_reply_<?=$reply_rr_no?>_<?=$reply_rr_depth?>">
												<div class="c_re_writer"><span><?=$reply_rr_position?> <?=$reply_rr_name?></span></div>
												<div class="c_re_text" id="c_re_<?=$reply_rr_depth?>"><?=str_replace("\r\n","<br>",$reply_rr_contents);?></div>
												<div class="c_date">
												<!-- ������ ���� ���� / �۾��̴� ���� ���� �Ѵ� ���� -->
												<? if ($reply_rr_id == $prs_id) { ?>
													<a href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">�ۻ���</a>
													<a href="javascript:mod_Reply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cw">�ۼ���</a>
												<? } else if ($prf_id == "4") { ?>
													<a href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">�ۻ���</a>
												<? } ?>
													<span class="t"><?=substr($reply_rr_date,11,5)?></span>
													<span class="d"><?=substr($reply_rr_date,0,10)?></span>
												</div>
											</div>
											<div id="modify_c_re_reply_<?=$reply_rr_no?>_<?=$reply_rr_depth?>" style="display:none;">
												<div class="c_re_writer"><span><?=$reply_rr_position?> <?=$reply_rr_name?></span></div>
												<div class="c_re_input">
													<textarea name="reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>" id="reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>" style="width:85%;height:100%;" size="300" length="10" onkeyup="textcounter2(this.form.reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>, this.form.remlen2,200);"><?=$reply_rr_contents?></textarea>
													<input type="hidden" readonly name="remlen2" size="3" maxlength="3" value="200">
												</div>
												<div class="c_re_btn" id="c_reply_btn_<?=$reply_rr_no?>_<?=$reply_rr_depth?>"><a href="javascript:modifyReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')">����</a></div>
											</div>
										</div>
								<?
										}
									}
								?>
								</li>
							</ul>
						</div>
					</div>
					<div class="view_foot">
						<div class="c_writer"><label for="w_comment">��۴ޱ�</label></div>
						<div class="c_textare">
						<textarea name="reply_contents" id="w_comment" size="4" length="10" onkeyup="textcounter(this.form.reply_contents, this.form.remlen,200);" onkeydown="textcounter(this.form.reply_contents, this.form.remlen,200);"></textarea></div>
						<div class="c_btn"><a href="javascript:writeReply();">+ ����ۼ�</a></div>
						<input type="hidden" readonly name="remlen" size="3" maxlength="3" value="200">
						<br>
					</div>
				</div>
			</div>			
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
