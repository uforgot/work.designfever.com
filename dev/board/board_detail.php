<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
?>

<?
	//���� üũ
	if ($prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
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

	//��� ���� (��۹�ȣ)
	function mod_Reply(replyno){
		
		document.getElementById("c_reply_"+replyno).style.display = "none";
		document.getElementById("modify_c_reply_"+replyno).style.display = "";

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
		frm.action = 'board_reply_act.php?reply_no='+replyno;
		frm.submit();
	}

	//��� ���� //����� ��� ���� (��۹�ȣ, ����Ǵ�۹�ȣ)
	function mod_Reply2(replyno,r_replyno){
		
		document.getElementById("c_re_reply_"+replyno+"_"+r_replyno).style.display = "none";
		document.getElementById("modify_c_re_reply_"+replyno+"_"+r_replyno).style.display = "";

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
		frm.action = 'board_reply_act.php?reply_no='+replyno+'&r_reply_no='+r_replyno;
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
<input type="hidden" name="type" value="">						<!-- ��ϼ����������� -->
<input type="hidden" name="seqno" value="<?=$seqno?>">		<!-- �۹�ȣ -->
<input type="hidden" name="writer" value="<?=$board_login?>">	<!-- ���ۼ��� prs_login -->
<input type="hidden" name="writer_id" value="<?=$board_id?>">	<!-- ���ۼ��� prs_id -->
<input type="hidden" name="modify_contents" id="modify_contents">
<? include INC_PATH."/top_menu.php"; ?>
    <!-- ���� �׺���̼� ���� -->
    <div class="sub-menu-7">
        <nav class="navbar has-shadow is-size-7-mobile">
            <div class="container">
                <div class="navbar-tabs">
                    <a class="navbar-item is-tab is-active" href="board_list.php">��������</a>
                </div>
            </div>
        </nav>
    </div>
    <!-- ���� �׺���̼� ��-->
<!-- ���� ���� -->
<section class="section is-subpage">
  <div class="container">
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="<?=$retUrl?>" class="button">
                    <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                     </span>
                    <span>���</span>	
                    </a>                    
                </p>
            </div>

            <div class="level-right">
            	<? if ($board_id == $prs_id || $prf_id == "4") {	?><!--�ۼ��ڿ� ������ ����,���� ����-->
                <p class="buttons">                	  
                	  <a href="javascript:goEdit()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>����</span>
                    </a>
                    <a href="javascript:goDel()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-times"></i>
                        </span>
                        <span>����</span>
                    </a>                	                    
              <? } ?>
                </p>
            </div>
        </nav>
       	<hr class="hr-strong">
       	
        <div class="content">
        	<!-- ���� ���� ��� -->            
            <h1>            
            	<? if ($board_notice == "Y") { ?> <!--<span class="tag is-danger">����</span>--> <? } ?>								            	
								<?=$board_title?>														
							<? if ($board_depth != "0") { ?> <span class="tag is-rounded td-tag"><?=$board_depth?></span> <? } ?>            	            	
							<? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?> &nbsp;<span class="icon is-small td-icon"><i class="fas fa-file"></i></span> <? } ?>																				
							
            </h1>
            <input type="hidden" name ="rep_depth" value="<?=$board_depth?>">	<!-- ��۰��� -->

            <p class="is-size-7">�ۼ���
                <?=$board_team?> <?=$board_position?> <?=$board_name?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                ��ȸ�� <?=$board_hit?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                �����  <?=$board_date?>
            </p>    
             	<hr>                                          
            <!-- ���� ���� -->            
            <p>
            	<?=$board_contents?>
 						</p>     
 						<!-- ���� ���� -->       
 					</div>     	 					
 					<br>
 					<br>
 					<br>                      
 					<!--÷������--> 
            <? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?>												 							
						<nav class="panel">
            	<p class="panel-heading">
              	  ÷�� ����
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
         <!-- ��� �ۼ�-->
        <article class="media">
            <figure class="media-left">
                <p class="image is-48x48  is-rounded-image">
                    <img src="/file/<?=$prs_img?>">
                </p>
            </figure>
            <div class="media-content">
                <div class="field">
                    <p class="control">
                        <textarea name="reply_contents" id="w_comment" onkeyup="textcounter(this.form.reply_contents, this.form.remlen,200);" onkeydown="textcounter(this.form.reply_contents, this.form.remlen,200);"class="textarea" placeholder="����� �Է����ּ���~"></textarea>
                    </p>
                </div>
                <div class="field">
                    <p class="control">                    
                        <a class="button" href="javascript:writeReply();">��� �ۼ�</a>
                    </p>
                </div>
            </div>
        </article>
                                       
        <!-- //�ش� �Խù��� ���� ��������� �����ְ� ������ ���� -->						
						<!--��� for�� ������ ����Ѵ� -->
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
							<!--������-->								
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
													<a href="javascript:view_reply(<?=$reply_no?>)" class="w_re"><small>���</small></a>
											</p>
										</div>
									</div>
									<div class="media-right">													
											<p class="buttons is-grouped has-addons">												
												<? if ($reply_r_id == $prs_id) { ?>
													<a href="javascript:mod_Reply('<?=$reply_no?>')" class="button is-small">����</a>
													<a href="javascript:delReply(<?=$reply_no?>)" class="button is-small">����</a>													
												<? } else if ($prf_id == "4") { ?>
													<a href="javascript:delReply(<?=$reply_no?>)" class="button is-small">����</a>
												<? } ?>													
											</p>	
									 </div>
								</article>			
															
								<!--��ۼ������� �۾�-->													
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
	                        <a id="reply_btn_<?=$reply_no?>" class="button" href="javascript:modifyReply('<?=$reply_no?>')">����</a>
	                    </p>
	                </div>	                
	            	</div>        																	 										
	            	
	            	<!--����� ���� �������-->
								<? } else if ($reply_r_tmp1 == "N" && sqlsrv_has_rows($replyRs) > 0) { ?>								
								<article class="media">
            			<div class="media-content">
                	<div class="content">
                    <p class="is-medium">
                        ������ ��� �Դϴ�
                    </p>
                	</div>
            			</div>        						        
							 <? } ?>		
							</article>										
																																																																						
								<!--���� �ۼ� layer-->								
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
                            <textarea class="textarea" placeholder="* 200���̳��� �ۼ����ּ���" name="reply_contents2_<?=$reply_no?>" id="w_comment" onkeyup="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);" onkeydown="textcounter2(this.form.reply_contents2_<?=$reply_no?>, this.form.remlen2,200);"></textarea>
                            <input type="hidden" readonly name="remlen2" size="3" maxlength="3" value="200">
                        	</p>
                    	</div>
			                </div>
			            </div>
			            <div class="media-right">
			                <p class="buttons is-grouped has-addons">			                    
			                    <a class="button is-small" href="javascript:writeReply2('<?=$reply_no?>','<?=$reply_id?>')">���</a>
			                </p>
			            </div>
			        </article>
																
							<!-- ���� ���-->
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
		                    	<a class="button is-small" href="javascript:mod_Reply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cw">����</a>
													<a class="button is-small" href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">����</a>													
												<? } else if ($prf_id == "4") { ?>
													<a  class="button is-small" href="javascript:delReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')" class="cd">����</a>
												<? } ?>													
		                </p>
		            </div>
		        	</article>				        	
		        	<!--���� ��������-->
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
		                   <span id="c_reply_btn_<?=$reply_rr_no?>_<?=$reply_rr_depth?>"><a href="javascript:modifyReply2('<?=$reply_rr_no?>','<?=$reply_rr_depth?>')">����</a></span>					                   	
		                </p>
		            </div>
		        	</article>
										
																		
								<?
										}
									}
								?>							
         
         <br>
         
       
     	
   	<!-- �ϴ� �������� ��Ϲ�ư-->
        <hr>
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="<?=$retUrl?>" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>���</span>
                    </a>
                </p>
            </div>
	    
            <div class="level-right">
            	<? if ($board_id == $prs_id || $prf_id == "4") {	//�ۼ��ڿ� ������ ����,���� ����?>
                <p class="buttons">
                	<a href="javascript:goEdit()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>����</span>
                    </a>                    
                   <a href="javascript:goDel()" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-times"></i>
                        </span>
                        <span>����</span>
                    </a>                	                    
              <? } ?>
                </p>
            </div>
        </nav>
     <!--�ϴ� -->   
    </div>
</section>
<!-- ���� �� -->
</form>
<? include INC_PATH."/bottom.php"; ?>				
</body>
</html>