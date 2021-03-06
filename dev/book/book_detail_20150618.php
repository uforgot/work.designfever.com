<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("등록대기,탈퇴회원 이용불가 페이지입니다.");
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
		alert("행복연구소 활동위원에게만 공개된 게시판입니다.");
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
		alert("해당 글이 존재하지 않습니다.");
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
		alert("해당 글이 존재하지 않습니다.");
		history.back();
	</script>
<?
		exit;
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function goEdit()//본문 수정화면으로...
	{
		var frm = document.form;
			frm.target = '_self';
			frm.type.value = 'modify';
			frm.action = 'book_write.php';
			frm.submit();
	}
	function goDel() //본문 삭제
	{
		if(!confirm("삭제 하시겠습니까?")){
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
	function textcounter(field, countfield, maxlimit) { //댓글 textarea글자수 200자 제한 스크립트
		  tempstr = field.value;
		  countfield.value = maxlimit - tempstr.length;
			if (maxlimit - tempstr.length < 0) {
						 alert(maxlimit+"글자를 초과할 수 없습니다.");
						 //document.form.remlen.focus();       //포커스를 이동시키지 않을 경우 글자가 지워짐
						 tempstr = tempstr.substring(0,maxlimit); //포커스 이동 후 글자 자르기
						 field.value = tempstr;
						 countfield.value = maxlimit - tempstr.length;
						 //document.form.reply_contents.focus();  //포커스를 입력상자로 되돌리기
	   }
	 }
	 
	 
	function textcounter2(field, countfield, maxlimit) { //댓글의댓글 글자수 200자 제한 스크립트
		  tempstr = field.value;
		  countfield.value = maxlimit - tempstr.length;
			if (maxlimit - tempstr.length < 0) {
						 alert(maxlimit+"글자를 초과할 수 없습니다.");
						 //document.form.remlen2.focus();       //포커스를 이동시키지 않을 경우 글자가 지워짐
						 tempstr = tempstr.substring(0,maxlimit); //포커스 이동 후 글자 자르기
						 field.value = tempstr;
						 countfield.value = maxlimit - tempstr.length;
						 //document.form.reply_contents2.focus();  //포커스를 입력상자로 되돌리기
	 }
	}
	 
	function writeReply(){ //댓글 달기
		var frm = document.form;
		if(frm.reply_contents.value.length < 1){
			alert("내용을 입력해주세요");
			frm.reply_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'write_reply';
		frm.action = 'book_reply_act.php';
		frm.submit();
	}; 

	function writeReply2(replyno,replyid){ //댓글의 댓글 달기  
		var reply_contents2 = document.getElementsByName("reply_contents2_"+replyno).item(0).value; //!!!!
		reply_contents2 = reply_contents2.replace(/(\n)/g, "<br>");	//엔터키를 br로 치환(documentsbyname으로 받기 때문에 스크립트 단에서 처리)
		
		var frm = document.form;
			frm.target = 'hdnFrame';
			frm.type.value = 'write_reply2';
			frm.action = 'book_reply_act.php?reply_no='+replyno+'&reply_id='+replyid+'&reply_contents2='+reply_contents2+'&keyfield='+frm.keyfield.value+'&keyword='+frm.keyword.value+'&page='+frm.page.value+'&seqno='+frm.seqno.value;
			frm.submit();
	}

	//댓글 수정 (댓글번호)
	function mod_Reply(replyno){
		
		document.getElementById("c_reply_"+replyno).style.display = "none";
		document.getElementById("modify_c_reply_"+replyno).style.display = "block";
		document.getElementById("reply_contents_"+replyno).focus();

	}
	//댓글 수정 실행 (댓글번호)
	function modifyReply(replyno){ 
		var frm = document.form;
		var modify_contents = document.getElementById("reply_contents_"+replyno); //!!!!

		if(modify_contents.value == ""){
			alert("내용을 입력해주세요");
			modify_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'modify_reply';
		frm.modify_contents.value = modify_contents.value;
		frm.action = 'book_reply_act.php?reply_no='+replyno;
		frm.submit();
	}

	//댓글 수정 //댓글의 댓글 수정 (댓글번호, 댓글의댓글번호)
	function mod_Reply2(replyno,r_replyno){
		
		document.getElementById("c_re_reply_"+replyno+"_"+r_replyno).style.display = "none";
		document.getElementById("modify_c_re_reply_"+replyno+"_"+r_replyno).style.display = "block";
		document.getElementById("reply_contents_"+replyno+"_"+r_replyno).focus();

	}
	//댓글 수정 실행 //댓글의 댓글 수정 (댓글번호, 댓글의댓글번호)
	function modifyReply2(replyno,r_replyno){ 
		var frm = document.form;
		var modify_contents = document.getElementById("reply_contents_"+replyno+"_"+r_replyno); //!!!!

		if(modify_contents.value == ""){
			alert("내용을 입력해주세요");
			modify_contents.focus();
			return;
		}	
		frm.target = 'hdnFrame';
		frm.type.value = 'modify_reply2';
		frm.modify_contents.value = modify_contents.value;
		frm.action = 'book_reply_act.php?reply_no='+replyno+'&r_reply_no='+r_replyno;
		frm.submit();
	}

	function delReply(replyno){ //댓글 삭제
		var frm = document.form;
		if(!confirm("댓글을 삭제 하시겠습니까?")){
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

	function delReply2(replyno,r_replyno){ //댓글의 댓글 삭제
		var frm = document.form;
		if(!confirm("댓글을 삭제 하시겠습니까?")){
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

	function view_reply(num){		//댓글의댓글달기 클릭시 나타나는 div창
		
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
<input type="hidden" name="type" value="">						<!-- 등록수정삭제구분 -->
<input type="hidden" name="seqno" value="<?=$seqno?>">		<!-- 글번호 -->
<input type="hidden" name="writer" value="<?=$book_login?>">	<!-- 글작성자 prs_login -->
<input type="hidden" name="writer_id" value="<?=$book_id?>">	<!-- 글작성자 prs_id -->
<input type="hidden" name="modify_contents" id="modify_contents">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">

			<p class="hello work_list">
		<? if ($board == "book") { ?>
			<a href="book_list.php?board=book"><strong>+  회사생활백서</strong></a>
			<a href="book_list.php?board=free">+  자유게시판</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  동호회게시판</a>
			<a href="book_list.php?board=edit">+  근태수정요청</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  행복연구소</a>
			<? } ?>
		<? } else if ($board == "free") { ?>
			<a href="book_list.php?board=book">+  회사생활백서</a>
			<a href="book_list.php?board=free"><strong>+  자유게시판</strong></a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  동호회게시판</a>
			<a href="book_list.php?board=edit">+  근태수정요청</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  행복연구소</a>
			<? } ?>
		<? } else if ($board == "ilab") { ?>
			<a href="book_list.php?board=book">+  회사생활백서</a>
			<a href="book_list.php?board=free">+  자유게시판</a>
			<a href="book_list.php?board=ilab"><strong>+  iLab</strong></a>
			<a href="book_list.php?board=club">+  동호회게시판</a>
			<a href="book_list.php?board=edit">+  근태수정요청</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  행복연구소</a>
			<? } ?>
		<? } else if ($board == "club") { ?>
			<a href="book_list.php?board=book">+  회사생활백서</a>
			<a href="book_list.php?board=free">+  자유게시판</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club"><strong>+  동호회게시판</strong></a>
			<a href="book_list.php?board=edit">+  근태수정요청</a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  행복연구소</a>
			<? } ?>
		<? } else if ($board == "edit") { ?>
			<a href="book_list.php?board=book">+  회사생활백서</a>
			<a href="book_list.php?board=free">+  자유게시판</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  동호회게시판</a>
			<a href="book_list.php?board=edit"><strong>+  근태수정요청</strong></a>
			<? if (in_array($prs_id,$happyLab_arr) == true) { ?>
				<a href="book_list.php?board=happy">+  행복연구소</a>
			<? } ?>
		<? } else if ($board == "happy") { ?>
			<a href="book_list.php?board=book">+  회사생활백서</a>
			<a href="book_list.php?board=free">+  자유게시판</a>
			<a href="book_list.php?board=ilab">+  iLab</a>
			<a href="book_list.php?board=club">+  동호회게시판</a>
			<a href="book_list.php?board=edit">+  근태수정요청</a>
			<a href="book_list.php?board=happy"><strong>+  행복연구소</strong></a>
		<? } ?>
			</p>
			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
					<div class="btn_left">
						<a href="<?=$retUrl?>"><img src="../img/btn_list.gif" alt="목록보기" /></a>
					</div>
					<div class="btn_right btn_nomargin">
					<? if ($book_id == $prs_id || $prf_id == "4") {	//작성자는 수정,삭제 가능?>
						<a href="javascript:goDel()"><img src="../img/btn_del.gif" alt="글 삭제" /></a> 
						<a href="javascript:goEdit()"><img src="../img/btn_modi.gif" alt="글 수정" /></a>
					<? } ?>
					</div>
				</div>
				<div class="board_view">
					<div class="view_head clearfix">
						<div class="fl">
							<div>
							<? if ($book_file1 != "" || $book_file2 != "" || $book_file3 != "") { ?>
								<span class="hasfile">첨부파일</span>
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
							<input type="hidden" name ="rep_depth" value="<?=$book_depth?>">	<!-- 댓글개수 -->
						</div>
						
						<div class="fr">
							<div>등록일 : <strong><span><?=$book_date?></span></strong></div>							
							<div>조회수 : <strong><?=$book_hit?></strong></div>
							<div class="noline">작성자 : <span><?=$book_team?></span>&nbsp;&nbsp;&nbsp;<strong><?=$book_position?>&nbsp;<?=$book_name?></strong></div>
						</div>
					</div>
					<div class="view_body">
						<div class="view_text">							
							<p><?=$book_contents?><br></p>
						</div>
						<div class="view_text">
						<? if ($book_file1 != "" || $book_file2 != "" || $book_file3 != "") { ?>					
							첨부파일 : 
							<div style="position:relative; left:100px; top:-25px;">
								<? if ($book_file1 != "") { ?><strong><a href="javascript:file_download('book','<?=$book_file1?>');"><?=$book_file1?></a></strong><br><? } ?>
								<? if ($book_file2 != "") { ?><strong><a href="javascript:file_download('book','<?=$book_file2?>');"><?=$book_file2?></a></strong><br><? } ?>
								<? if ($book_file3 != "") { ?><strong><a href="javascript:file_download('book','<?=$book_file3?>');"><?=$book_file3?></a></strong><? } ?>
							</div>
						<? } ?>
						</div>
						<!-- //해당 게시물에 대한 댓글있을때 보여주고 없을땐 막음 -->
						<div class="view_comment">
							<ul>
								<li>
									<!--답글 for문 돌려서 출력한다 -->
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
												<div class="c_text"><span id="c_text_<?=$reply_no?>"><?=str_replace("\r\n","<br>",$reply_r_contents);?></span> <a href="javascript:view_reply(<?=$reply_no?>)" class="w_re">+ 댓글쓰기</a></div>
												<div class="c_date">
												<!-- 관리자 삭제 가능 / 글쓴이는 수정 삭제 둘다 가능 -->
												<? if ($reply_r_id == $prs_id) { ?>
													<a href="javascript:delReply(<?=$reply_no?>)" class="cd">글삭제</a>
													<a href="javascript:mod_Reply('<?=$reply_no?>')" class="cw">글수정</a>
												<? } else if ($prf_id == "4") { ?>
													<a href="javascript:delReply(<?=$reply_no?>)" class="cd">글삭제</a>
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
												<div class="c_add_btn" id="reply_btn_<?=$reply_no?>"><a href="javascript:modifyReply('<?=$reply_no?>')">수정</a></div>
											</div>
										</div>
									<? } else if ($reply_r_tmp1 == "N" && sqlsrv_has_rows($replyRs) > 0) { ?>
										<div class="c_wrap">
											<div class="c_writer"><span>&nbsp;</span></div>
											<div class="c_text"><font color="#8C8C8C">삭제된 댓글입니다</font></div>
											<div class="c_date">
												<span class="t"></span>
												<span class="d"></span>
											</div>
										</div>
									<? } ?>
										<!--답글 의 답글달기 div 클릭시보여짐-->
										<div id="add_comment_<?=$reply_no?>" name="add_comment_<?=$reply_no?>" class="c_add_comment" style="display:none"> 
											<div class="c_writer"></div>
											<div class="c_add_input">
												<textarea name="reply_contents2_<?=$reply_no?>" id="reply_contents2_<?=$reply_no?>" style="width:100%;height:100%;" size="400" length="10" onkeyup="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);"></textarea>
												<input type="hidden" readonly name="remlen2" size="3" maxlength="3" value="200">
												<td>* 200자이내로 작성해주세요</td>
											</div>
											<div class="c_add_btn" id="c_reply_btn_<?=$reply_no?>"><a href="javascript:writeReply2('<?=$reply_no?>','<?=$reply_id?>')">등록</a></div>
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
										<!-- 답글의 답글 for문으로 돌려서 출력한다 -->
										<div class="c_re_comment">
											<div id="c_re_reply_<?=$reply_rr_no?>_<?=$reply_rr_depth?>">
												<div class="c_re_writer"><span><?=$reply_rr_position?> <?=$reply_rr_name?></span></div>
												<div class="c_re_text" id="c_re_<?=$reply_rr_depth?>"><?=str_replace("\r\n","<br>",$reply_rr_contents);?></div>
												<div class="c_date">
												<!-- 관리자 삭제 가능 / 글쓴이는 수정 삭제 둘다 가능 -->
												<? if ($reply_rr_id == $prs_id) { ?>
													<a href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">글삭제</a>
													<a href="javascript:mod_Reply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cw">글수정</a>
												<? } else if ($prf_id == "4") { ?>
													<a href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">글삭제</a>
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
												<div class="c_re_btn" id="c_reply_btn_<?=$reply_rr_no?>_<?=$reply_rr_depth?>"><a href="javascript:modifyReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')">수정</a></div>
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
						<div class="c_writer"><label for="w_comment">댓글달기</label></div>
						<div class="c_textare">
						<textarea name="reply_contents" id="w_comment" size="4" length="10" onkeyup="textcounter(this.form.reply_contents, this.form.remlen,200);" onkeydown="textcounter(this.form.reply_contents, this.form.remlen,200);"></textarea></div>
						<div class="c_btn"><a href="javascript:writeReply();">+ 댓글작성</a></div>
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
