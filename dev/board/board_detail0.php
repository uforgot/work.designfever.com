<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<script type="text/javascript">
		alert("��ϴ��,Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$board = isset($_REQUEST['board']) ? $_REQUEST['board'] : "default";  
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$retUrl = "board_list.php?board=". $board ."&page=". $page;
	if ($keyword != "")
	{
		$retUrl = $retUrl ."&keyfield=". $keyfield ."&keyword=". $keyword;
	}

	if ($seqno == "")
	{
?>
	<script type="text/javascript">
		alert("�ش� ���� �������� �ʽ��ϴ�.");
		location.href="board_list.php";
	</script>
<?
		exit;
	}

	$searchSQL = " WHERE SEQNO = '$seqno'";
	
	$sql = "SELECT
				PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(VARCHAR(16),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3
			FROM
				DF_BOARD WITH(NOLOCK)".	$searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$board_id = $record['PRS_ID'];
		$board_name = $record['PRS_NAME'];
		$board_login = $record['PRS_LOGIN'];
		$board_team = $record['PRS_TEAM'];
		$board_position = $record['PRS_POSITION'];
		$board_title = $record['TITLE'];
		$board_contents = $record['CONTENTS'];
		$board_hit = $record['HIT'];
		$board_depth = $record['REP_DEPTH'];
		$board_notice = $record['NOTICE_YN'];
		$board_date = $record['REG_DATE'];
		$board_file1 = trim($record['FILE_1']);
		$board_file2 = trim($record['FILE_2']);
		$board_file3 = trim($record['FILE_3']);
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function goEdit()//���� ����ȭ������...
	{
		var frm = document.form;
			frm.target = '_self';
			frm.type.value = 'modify';
			frm.action = 'board_write.php';
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
		  frm.action = 'board_write_act.php';
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
		frm.action = 'board_reply_act.php';
		frm.submit();
	}; 

	function writeReply2(replyno,replyid){ //����� ��� �ޱ�  
		var reply_contents2 = document.getElementsByName("reply_contents2_"+replyno).item(0).value; //!!!!
		reply_contents2 = reply_contents2.replace(/(\n)/g, "<br>");	//����Ű�� br�� ġȯ(documentsbyname���� �ޱ� ������ ��ũ��Ʈ �ܿ��� ó��)
		
		var frm = document.form;
			frm.target = 'hdnFrame';
			frm.type.value = 'write_reply2';
			frm.action = 'board_reply_act.php?reply_no='+replyno+'&reply_id='+replyid+'&reply_contents2='+reply_contents2+'&keyfield='+frm.keyfield.value+'&keyword='+frm.keyword.value+'&page='+frm.page.value+'&seqno='+frm.seqno.value;
			frm.submit();
	}

	function mod_Reply(replyno){	//��� ���� (��۹�ȣ)
		var frm = document.form;
		MM_openBrWindow('board_reply.php?reply_no='+replyno+'&keyfield='+frm.keyfield.value+'&keyword='+frm.keyword.value+'&page='+frm.page.value+'&type=modify_reply','','width=560 ,height=300,left=800,top=350,scrollbars=no');		
	}

	function mod_Reply2(replyno,r_replyno){//����� ��� ���� (��۹�ȣ, ����Ǵ�۹�ȣ)
		var frm = document.form;
		MM_openBrWindow('board_reply.php?reply_no='+replyno+'&r_reply_no='+r_replyno+'&keyfield='+frm.keyfield.value+'&keyword='+frm.keyword.value+'&page='+frm.page.value+'&type=modify_reply2','','width=560 ,height=300,left=800,top=350,scrollbars=no');		
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
			frm.action = 'board_reply_act.php?reply_no='+replyno;
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
			frm.action = 'board_reply_act.php?reply_no='+replyno+'&r_reply_no='+r_replyno;
			frm.submit();
		}
	}

	function view_reply(num){		//����Ǵ�۴ޱ� Ŭ���� ��Ÿ���� divâ
		
		var total = document.getElementById("add_comment_"+num);

		if(total.style.display=="none"){
			total.style.display="block";
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
<input type="hidden" name="keyfield" value="<?=$keyfield?>">
<input type="hidden" name="keyword" value="<?=$keyword?>">
<input type="hidden" name="type" value="">						<!-- ��ϼ����������� -->
<input type="hidden" name="seqno" value="<?=$seqno?>">		<!-- �۹�ȣ -->
<input type="hidden" name="writer" value="<?=$board_login?>">	<!-- ���ۼ��� prs_login -->
<input type="hidden" name="writer_id" value="<?=$board_id?>">	<!-- ���ۼ��� prs_id -->
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">

			<p class="hello work_list">
			<? if ($board == "default") { ?>
			<a href="board_list.php?board=default"><strong>+  �Խ���</strong></a>
			<a href="board_list.php?board=intranet">+ ��Ʈ��� ����</a></p>
			<? } else if ($board == "intranet") { ?>
			<a href="board_list.php?board=default">+  �Խ���</a>
			<a href="board_list.php?board=intranet"><strong>+ ��Ʈ��� ����</strong></a></p>
			<? } ?>
			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
					<div class="btn_left">
						<a href="<?=$retUrl?>"><img src="../img/btn_list.gif" alt="��Ϻ���" /></a>
					</div>
					<? if ($board_login == $prs_login) {	//�ۼ��ڴ� ����,���� ����?>
						<a href="javascript:goDel()" class="btn_right"><img src="../img/btn_del.gif" alt="�� ����" /></a> 
						<a href="javascript:goEdit()" class="btn_right btn_nomargin"><img src="../img/btn_modi.gif" alt="�� ����" /></a>
					<? } else if ($prf_id == "4") {			//�����ڴ� ������ ����?>
						<a href="javascript:goDel()" class="btn_right"><img src="../img/btn_del.gif" alt="�� ����" /></a> 
					<? } ?>
				</div>
				<div class="board_view">
					<div class="view_head clearfix">
						<div class="fl">
							<div>
							<? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?>
								<span class="hasfile">÷������</span>
							<? } else { ?>
								<span class="nofile">nofile</span>
							<? } ?>
							<? if ($board_notice = "Y") { ?>
								<strong class="icon_notice">����</strong>
							<? } ?>
								<p><?=$board_title?></p>
							<? if ($board_depth != "0") { ?>
								<span class="hascomment">[<span><?=$board_depth?></span>]</span>
							<? } ?>
							</div>
							<input type="hidden" name ="rep_depth" value="<?=$board_depth?>">	<!-- ��۰��� -->
						</div>
						
						<div class="fr">
							<div>����� : <strong><span><?=$board_date?></span></strong></div>
								
							<div>��ȸ�� : <strong><?=$board_hit?></strong></div>
							<div class="noline">�ۼ��� : <span><?=$board_team?></span>&nbsp;&nbsp;&nbsp;<strong><?=$board_position?>&nbsp;<?=$board_name?></strong></div>
						</div>
					</div>
					<div class="view_body">
						<div class="view_text">							
							<p><html><head></head><body><?=$board_contents?></body></html><br></p>
						</div>
						<div class="view_text">
						<? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?>					
							÷������ : 
								<? if ($board_file1 != "") { ?><strong><a href="<?=BOARD_URL . $board_file1?>" target="_blank"><?=$board_file1?></a></strong>&nbsp;&nbsp;&nbsp;<? } ?>
								<? if ($board_file2 != "") { ?><strong><a href="<?=BOARD_URL . $board_file2?>" target="_blank"><?=$board_file2?></a></strong>&nbsp;&nbsp;&nbsp;<? } ?>
								<? if ($board_file3 != "") { ?><strong><a href="<?=BOARD_URL . $board_file3?>" target="_blank"><?=$board_file3?></a></strong><? } ?>
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
										<div class="c_writer"><span><?=$reply_r_position?>&nbsp;<?=$reply_r_name?></span></div>
										<div class="c_text"><?=str_replace("\r\n","<br>",$reply_r_contents);?> <a href="javascript:view_reply(<?=$reply_no?>)" class="w_re">+ ��۾���</a></div>
										<div class="c_date">
											<span class="t"><?=substr($reply_r_date,11,5)?></span>
											<span class="d"><?=substr($reply_r_date,0,10)?></span>
										<!-- ������ ���� ���� / �۾��̴� ���� ���� �Ѵ� ���� -->
										<? if ($reply_r_id == $prs_id) { ?>
											<a href="javascript:delReply(<?=$reply_no?>)" class="cd">�ۻ���</a>
											<a href="javascript:mod_Reply('<?=$reply_no?>')" class="cw">�ۼ���</a>
										<? } else if ($prf_id == "4") { ?>
											<a href="javascript:delReply(<?=$reply_no?>)" class="cd">�ۻ���</a>
										<? } ?>
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
											<div class="c_re_writer"><span><?=$reply_rr_position?> <?=$reply_rr_name?></span></div>
											<div class="c_re_text"><?=$reply_rr_contents?></div>
											<div class="c_date">
												<span class="t"><?=substr($reply_rr_date,11,5)?></span>
												<span class="d"><?=substr($reply_rr_date,0,10)?></span>
											<!-- ������ ���� ���� / �۾��̴� ���� ���� �Ѵ� ���� -->
											<? if ($reply_rr_id == $prs_id) { ?>
												<a href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">�ۻ���</a>
												<a href="javascript:mod_Reply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cw">�ۼ���</a>
											<? } else if ($prf_id == "4") { ?>
												<a href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">�ۻ���</a>
											<? } ?>
											</div>
										</div>
								<?
										}
								?>
										<!--��� �� ��۴ޱ� div Ŭ���ú�����-->
										<div id="add_comment_<?=$reply_no?>" name="add_comment_<?=$reply_no?>" class="c_add_comment" style="display:none"> 
											<div class="c_writer"></div>
											<div class="c_add_input">
												<textarea name="reply_contents2_<?=$reply_no?>" style="width:100%;height:100%;"id="w_comment" size="400" length="10" onkeyup="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);"></textarea>
												<input type="hidden" readonly name="remlen2" size="3" maxlength="3" value="200">
												<td>* 200���̳��� �ۼ����ּ���</td>
											</div>
											<div class="c_add_btn"><a href="javascript:writeReply2('<?=$reply_no?>','<?=$reply_id?>')">���</a></div>
										</div>
								<?
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
