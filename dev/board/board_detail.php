<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	//권한 체크
	if ($prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("탈퇴회원 이용불가 페이지입니다.");
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

//	$retUrl = "board_list.php?board=". $board ."&page=". $page;
	$retUrl = "board_list.php?page=". $page;
	if ($keyword != "")
	{
		$retUrl = $retUrl ."&keyfield=". $keyfield ."&keyword=". $keyword;
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
				PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(VARCHAR(16),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3
			FROM
				DF_BOARD WITH(NOLOCK)".	$searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sqlsrv_has_rows($rs) > 0)
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
			frm.action = 'board_write.php';
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
		  frm.action = 'board_write_act.php';
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
		frm.action = 'board_reply_act.php';
		frm.submit();
	}; 

	function writeReply2(replyno,replyid){ //댓글의 댓글 달기  
		var reply_contents2 = document.getElementsByName("reply_contents2_"+replyno).item(0).value; //!!!!
		reply_contents2 = reply_contents2.replace(/(\n)/g, "<br>");	//엔터키를 br로 치환(documentsbyname으로 받기 때문에 스크립트 단에서 처리)		
		var frm = document.form;
			frm.target = 'hdnFrame';
			frm.type.value = 'write_reply2';
			frm.action = 'board_reply_act.php?reply_no='+replyno+'&reply_id='+replyid+'&reply_contents2='+reply_contents2+'&keyfield='+frm.keyfield.value+'&keyword='+frm.keyword.value+'&page='+frm.page.value+'&seqno='+frm.seqno.value;
			frm.submit();
	}

	//댓글 수정 (댓글번호)
	function mod_Reply(replyno){
		
		document.getElementById("c_reply_"+replyno).style.display = "none";
		document.getElementById("modify_c_reply_"+replyno).style.display = "";

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
		frm.action = 'board_reply_act.php?reply_no='+replyno;
		frm.submit();
	}

	//댓글 수정 //댓글의 댓글 수정 (댓글번호, 댓글의댓글번호)
	function mod_Reply2(replyno,r_replyno){
		
		document.getElementById("c_re_reply_"+replyno+"_"+r_replyno).style.display = "none";
		document.getElementById("modify_c_re_reply_"+replyno+"_"+r_replyno).style.display = "";

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
		frm.action = 'board_reply_act.php?reply_no='+replyno+'&r_reply_no='+r_replyno;
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
			frm.action = 'board_reply_act.php?reply_no='+replyno;
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
			frm.action = 'board_reply_act.php?reply_no='+replyno+'&r_reply_no='+r_replyno;
			frm.submit();
		}
	}

	function view_reply(num){		//댓글의댓글달기 클릭시 나타나는 div창
		
		var total = document.getElementById("add_comment_"+num);
		var focus = document.getElementById("reply_contents2_"+num);

		if(total.style.display=="none"){			
			total.style.display="";										 					
			focus.focus();			
		}else{			
			total.style.display="none";						
		}
	}
</script>
</head>

<body>
<form method="post" name="form" onKeyDown="javascript:if (event.keyCode == 13) {funSearch();}">
<input type="hidden" name="board" value="<?=$board?>">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="keyfield" value="<?=$keyfield?>">
<input type="hidden" name="keyword" value="<?=$keyword?>">
<input type="hidden" name="type" value="">						<!-- 등록수정삭제구분 -->
<input type="hidden" name="seqno" value="<?=$seqno?>">		<!-- 글번호 -->
<input type="hidden" name="writer" value="<?=$board_login?>">	<!-- 글작성자 prs_login -->
<input type="hidden" name="writer_id" value="<?=$board_id?>">	<!-- 글작성자 prs_id -->
<input type="hidden" name="modify_contents" id="modify_contents">
<? include INC_PATH."/top_menu.php"; ?>
    <!-- 서브 네비게이션 시작 -->
    <div class="sub-menu-7">
        <nav class="navbar has-shadow is-size-7-mobile">
            <div class="container">
                <div class="navbar-tabs">
                    <a class="navbar-item is-tab is-active" href="board_list.php">공지사항</a>
                </div>
            </div>
        </nav>
    </div>
    <!-- 서브 네비게이션 끝-->
<!-- 본문 시작 -->
<section class="section is-subpage">
  <div class="container">
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="<?=$retUrl?>" class="button">
                    <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                     </span>
                    <span>목록</span>	
                    </a>                    
                </p>
            </div>

            <div class="level-right">
            	<? if ($board_id == $prs_id || $prf_id == "4") {	?><!--작성자와 관리자 수정,삭제 가능-->
                <p class="buttons">                	  
                	  <a href="javascript:goEdit()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>수정</span>
                    </a>
                    <a href="javascript:goDel()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-times"></i>
                        </span>
                        <span>삭제</span>
                    </a>                	                    
              <? } ?>
                </p>
            </div>
        </nav>
       	<hr class="hr-strong">
       	
        <div class="content">
        	<!-- 본문 내용 출력 -->            
            <h1>            
            	<? if ($board_notice == "Y") { ?> <!--<span class="tag is-danger">공지</span>--> <? } ?>								            	
								<?=$board_title?>														
							<? if ($board_depth != "0") { ?> <span class="tag is-rounded td-tag"><?=$board_depth?></span> <? } ?>            	            	
							<? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?> &nbsp;<span class="icon is-small td-icon"><i class="fas fa-file"></i></span> <? } ?>																				
							
            </h1>
            <input type="hidden" name ="rep_depth" value="<?=$board_depth?>">	<!-- 댓글개수 -->

            <p class="is-size-7">작성자
                <?=$board_team?> <?=$board_position?> <?=$board_name?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                조회수 <?=$board_hit?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                등록일  <?=$board_date?>
            </p>    
             	<hr>                                          
            <!-- 본문 내용 -->            
            <p>
            	<?=$board_contents?>
 						</p>     
 						<!-- 본문 내용 -->       
 					</div>     	 					
 					<br>
 					<br>
 					<br>                      
 					<!--첨부파일--> 
            <? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?>												 							
						<nav class="panel">
            	<p class="panel-heading">
              	  첨부 파일
            	</p>            	            
            	<? if ($board_file1 != "") { ?>
            	<a class="panel-block" href="javascript:file_download('board',1,<?=$seqno?>);">
                <span class="panel-icon">
                  <i class="fas fa-file" aria-hidden="true"></i>
                </span>
              <?=$board_file1?>
            	</a>
            	<? } ?>
            	<? if ($board_file2 != "") { ?>
            	<a class="panel-block" href="javascript:file_download('board',2,<?=$seqno?>);">
                <span class="panel-icon">
                  <i class="fas fa-file" aria-hidden="true"></i>
                </span>
                <?=$board_file2?>
            	</a>
            	<? } ?>
            	<? if ($board_file3 != "") { ?>
            	<a class="panel-block" href="javascript:file_download('board',3,<?=$seqno?>);">
                <span class="panel-icon">
                  <i class="fas fa-file" aria-hidden="true"></i>
                </span>
                <?=$board_file3?>
            	</a>
            	<? } ?>
        	</nav>
						<? } ?>								
						<br>			
						
        <hr class="hr-strong">
         <!-- 댓글 작성-->
        <article class="media">
            <figure class="media-left">
                <p class="image is-48x48  is-rounded-image">
                    <img src="/file/<?=$prs_img?>">
                </p>
            </figure>
            <div class="media-content">
                <div class="field">
                    <p class="control">
                        <textarea name="reply_contents" id="w_comment" onkeyup="textcounter(this.form.reply_contents, this.form.remlen,200);" onkeydown="textcounter(this.form.reply_contents, this.form.remlen,200);"class="textarea" placeholder="댓글을 입력해주세요~"></textarea>
                    </p>
                </div>
                <div class="field">
                    <p class="control">                    
                        <a class="button" href="javascript:writeReply();">댓글 작성</a>
                    </p>
                </div>
            </div>
        </article>
                                       
        <!-- //해당 게시물에 대한 댓글있을때 보여주고 없을땐 막음 -->						
						<!--답글 for문 돌려서 출력한다 -->
								<?
									$sql = "SELECT A.SEQNO, A.PRS_ID, A.REPLYNO, A.R_PRS_ID, A.R_PRS_NAME, A.R_PRS_POSITION, A.R_CONTENTS, A.R_REPLY_DEPTH, CONVERT(VARCHAR(20),A.R_REG_DATE,120) AS R_REG_DATE, A.R_TMP_1, A.R_TMP_2,
																 B.FILE_IMG
  											    FROM DF_BOARD_REPLY A WITH(NOLOCK) 
 												   INNER JOIN DF_PERSON B 
    											    ON A.R_PRS_ID = B.PRS_ID
											     WHERE SEQNO = '$seqno'
												   ORDER BY R_REG_DATE";
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
										$reply_r_file_img = $record['FILE_IMG'];
							
										$replySQL = "SELECT A.SEQNO, A.REPLYNO, A.R_PRS_ID, A.RR_PRS_ID, A.RR_PRS_NAME, A.RR_PRS_POSITION, A.RR_CONTENTS, A.RR_REPLY_DEPTH, CONVERT(VARCHAR(20),A.RR_REG_DATE,120) AS RR_REG_DATE, A.RR_TMP_1,
				   															B.FILE_IMG 
  																FROM DF_BOARD_REPLY2 A WITH (NOLOCK)
  															 INNER JOIN DF_PERSON B 
     																ON A.RR_PRS_ID = B.PRS_ID 
																 WHERE SEQNO = '$reply_seqno' AND REPLYNO = '$reply_no'
																 ORDER BY RR_REG_DATE";
										$replyRs = sqlsrv_query($dbConn, $replySQL);
								?>
							<!--댓글출력-->								
									<? if ($reply_r_tmp1 == "Y") { ?>
									<article class="media" id="c_reply_<?=$reply_no?>">
            				<figure class="media-left">
                		<p class="image is-48x48 is-rounded-image">
                  		 <img src="/file/<?=$reply_r_file_img?>">
                		</p>
            			</figure>
										<div class="media-content">											
											<div class="content" >
											<p>
												<span class="is-size-7"><?=$reply_r_position?>&nbsp;<?=$reply_r_name?></span>&nbsp;&nbsp;&nbsp;
												<span class="is-size-7"><?=substr($reply_r_date,11,5)?></span>
												<span class="is-size-7"><?=substr($reply_r_date,0,10)?></span>
													<br>
														<span id="c_text_<?=$reply_no?>"><?=str_replace("\r\n","<br>",$reply_r_contents);?></span> 
													<br>
													<a href="javascript:view_reply(<?=$reply_no?>)" class="w_re"><small>댓글</small></a>
											</p>
										</div>
									</div>
									<div class="media-right">													
											<p class="buttons is-grouped has-addons">												
												<? if ($reply_r_id == $prs_id) { ?>
													<a href="javascript:mod_Reply('<?=$reply_no?>')" class="button is-small">수정</a>
													<a href="javascript:delReply(<?=$reply_no?>)" class="button is-small">삭제</a>													
												<? } else if ($prf_id == "4") { ?>
													<a href="javascript:delReply(<?=$reply_no?>)" class="button is-small">삭제</a>
												<? } ?>													
											</p>	
									 </div>
								</article>			
															
								<!--댓글수정영역 작업-->													
								<article class="media" id="modify_c_reply_<?=$reply_no?>" style="display:none;">   
									<figure class="media-left">
                	<p class="image is-48x48 is-rounded-image">
                  	 <img src="/file/<?=$reply_r_file_img?>">
                	</p>
            		</figure>         			
	            	<div class="media-content">
	                <div class="field">
	                    <p class="control">	                        
	                        <textarea class="textarea" name="reply_contents_<?=$reply_no?>" id="reply_contents_<?=$reply_no?>" onkeyup="textcounter(this.form.reply_contents_<?=$reply_no?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents_<?=$reply_no?>, this.form.remlen,200);"><?=$reply_r_contents?></textarea>
													<input type="hidden" readonly name="remlen" size="3" maxlength="3" value="200">
	                    </p>
	                </div>
	                <div class="field">
	                    <p class="control">
	                        <a id="reply_btn_<?=$reply_no?>" class="button" href="javascript:modifyReply('<?=$reply_no?>')">수정</a>
	                    </p>
	                </div>	                
	            	</div>        																	 										
	            	
	            	<!--댓글이 삭제 됐을경우-->
								<? } else if ($reply_r_tmp1 == "N" && sqlsrv_has_rows($replyRs) > 0) { ?>								
								<article class="media">
            			<div class="media-content">
                	<div class="content">
                    <p class="is-medium">
                        삭제된 댓글 입니다
                    </p>
                	</div>
            			</div>        						        
							 <? } ?>		
							</article>										
																																																																						
								<!--대댓글 작성 layer-->								
								<article class="media" style="padding-left:4rem; display:none" id="add_comment_<?=$reply_no?>" name="add_comment_<?=$reply_no?>">
            			<figure class="media-left">
                	<p class="image is-48x48 is-rounded-image">
                  	 <img src="/file/<?=$prs_img?>">
                	</p>
            			</figure>
			            <div class="media-content">
			                <div class="content">
			                  <div class="field">
                       	 <p class="control">                            
                            <textarea class="textarea" placeholder="* 200자이내로 작성해주세요" name="reply_contents2_<?=$reply_no?>" id="w_comment" onkeyup="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);"></textarea>
                            <input type="hidden" readonly name="remlen2" size="3" maxlength="3" value="200">
                        	</p>
                    	</div>
			                </div>
			            </div>
			            <div class="media-right">
			                <p class="buttons is-grouped has-addons">			                    
			                    <a class="button is-small" href="javascript:writeReply2('<?=$reply_no?>','<?=$reply_id?>')">등록</a>
			                </p>
			            </div>
			        </article>
																
							<!-- 대댓글 출력-->
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
											$reply_rr_file_img = $replyRecord['FILE_IMG'];
								?>
								<article class="media" style="padding-left:4rem;" id="c_re_reply_<?=$reply_rr_no?>_<?=$reply_rr_depth?>">
    	     			 	<figure class="media-left">
	      	          <p class="image is-48x48 is-rounded-image">
	                    <img src="/file/<?=$reply_rr_file_img?>">
	                	</p>
			           	</figure>
				          <div class="media-content">
				          	<div class="content">
				             <p>
				             	<span class="is-size-7"><?=$reply_rr_position?> <?=$reply_rr_name?></span>&nbsp;&nbsp;&nbsp;
											<span class="is-size-7"><?=substr($reply_rr_date,11,5)?></span>
											<span class="is-size-7"><?=substr($reply_rr_date,0,10)?></span>
				              <br>
				             <span id="c_re_<?=$reply_rr_depth?>"><?=str_replace("\r\n","<br>",$reply_rr_contents);?></span>
				              <br>				             
				             </p>
				            </div>
				         </div>
		             <div class="media-right">
		                <p class="buttons is-grouped has-addons">		                    
		                    <? if ($reply_rr_id == $prs_id) { ?>
		                    	<a class="button is-small" href="javascript:mod_Reply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cw">수정</a>
													<a class="button is-small" href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">삭제</a>													
												<? } else if ($prf_id == "4") { ?>
													<a  class="button is-small" href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">삭제</a>
												<? } ?>													
		                </p>
		            </div>
		        	</article>				        	
		        	<!--대댓글 수정영역-->
		        	<article class="media" id="modify_c_re_reply_<?=$reply_rr_no?>_<?=$reply_rr_depth?>" style="display:none; padding-left:4rem;">
    	     			 	<figure class="media-left">
	      	          <p class="image is-48x48 is-rounded-image">
	                    <img src="/file/<?=$reply_rr_file_img?>">
	                	</p>
			           	</figure>
				          <div class="media-content">
				          	<div class="content">
				             <p>				   
				             	<textarea class="textarea" name="reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>" id="reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>" onkeyup="textcounter2(this.form.reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents_<?=$reply_rr_no?>_<?=$reply_rr_depth?>, this.form.remlen2,200);"><?=$reply_rr_contents?></textarea>
											<input type="hidden" readonly name="remlen2" size="3" maxlength="3" value="200">          	
				             </p>
				            </div>
				         </div>
		             <div class="media-right">
		                <p class="buttons is-grouped has-addons">		                    
		                   <span id="c_reply_btn_<?=$reply_rr_no?>_<?=$reply_rr_depth?>"><a href="javascript:modifyReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')">수정</a></span>					                   	
		                </p>
		            </div>
		        	</article>
										
																		
								<?
										}
									}
								?>							
         
         <br>
         
       
     	
   	<!-- 하단 수정삭제 목록버튼-->
        <hr>
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="<?=$retUrl?>" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>목록</span>
                    </a>
                </p>
            </div>
	    
            <div class="level-right">
            	<? if ($board_id == $prs_id || $prf_id == "4") {	//작성자와 관리자 수정,삭제 가능?>
                <p class="buttons">
                	<a href="javascript:goEdit()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>수정</span>
                    </a>                    
                   <a href="javascript:goDel()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-times"></i>
                        </span>
                        <span>삭제</span>
                    </a>                	                    
              <? } ?>
                </p>
            </div>
        </nav>
     <!--하단 -->   
    </div>
</section>
<!-- 본문 끌 -->
</form>
<? include INC_PATH."/bottom.php"; ?>				
</body>
</html>