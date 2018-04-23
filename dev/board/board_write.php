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
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$writer = isset($_REQUEST['writer']) ? $_REQUEST['writer'] : null;  
	$writer_id = isset($_REQUEST['writer_id']) ? $_REQUEST['writer_id'] : null;  

	if ($type == "modify")
	{
		$type_title = "수정";

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
	}
	else if ($type == "write")
	{
		$type_title = "작성";

		$board_id = $prs_id;
		$board_name = $prs_name;
		$board_login = $prs_login;
		$board_team = $prs_team;
		$board_position = $prs_position;
		$board_title = "";
		$board_contents = "";
		$board_notice = "";
		$board_file1 = "";
		$board_file2 = "";
		$board_file3 = "";
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript" src="/ckeditor/ckeditor.js" /></script>
<script type="text/JavaScript">
	window.onload=function(){
		CKEDITOR.replace('contents', {
			skin:'kama',
			enterMode:'2',
			shiftEnterMode:'3',
			filebrowserUploadUrl:'upload.php?type=files',
			filebrowserImageUploadUrl:'upload.php?type=images',
			filebrowserFlashUploadUrl:'upload.php?type=flash'
			}
		);
	};

	function funWrite()
	{
		var frm = document.form;
		var contents =  CKEDITOR.instances['contents'].getData();//ckeditor 붙인 본문 값 받기

		if(frm.title.value == ""){
			alert("제목을 입력해주세요");
			frm.title.focus();
			return;
		}
		if(contents==""){
			alert("내용을 입력해주세요");
			CKEDITOR.instances['contents'].focus();		//ckeditor 포커스 이동하는 부분
			return;    	
		}
			//내용 유효성 검사 할 부분
		if(confirm("게시글을 <?=$type_title?> 하시겠습니까")){
			frm.target ="hdnFrame";
			if (frm.announcement.checked){
				frm.notice_yn.value="Y";
			}else{
				frm.notice_yn.value="N";
			}
			frm.action = 'board_write_act.php'; 
			frm.submit();
		}
	}

	function addFile()
	{
		if (document.getElementById("file_D2").style.display == "none")
		{
			document.getElementById("file_D2").style.display = "";
		}
		else
		{
			if (document.getElementById("file_D3").style.display == "none")
			{
				document.getElementById("file_D3").style.display = "";
			}
			else
			{
				alert("파일 첨부는 최대 3개까지 가능합니다.");
			}
		}
	}

	function delFile(file)
	{
		document.getElementById("file_"+file).value = "";
		document.getElementById("attachment_"+file).value = "";
		document.getElementById("delfile_"+file).innerHTML = "";
		document.getElementById("filedel_"+file).value = "Y";
	}

	$(document).ready(function(){
		//선택된 파일명 표시
		$("#file_1").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_1").val(this.value);
			$("#delfile_1").html("&nbsp;&nbsp;&nbsp;<span>"+arr_str[arr_len-1]+"</span>&nbsp;&nbsp;<a href='javascript:delFile(1);' class='tag is-danger'>삭제</a>");
		});
		$("#file_2").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_2").val(this.value);
			$("#delfile_2").html("&nbsp;&nbsp;<span>"+arr_str[arr_len-1]+"</span>&nbsp;&nbsp;<a href='javascript:delFile(2);' class='tag is-danger'>삭제</a>");
		});
		$("#file_3").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_3").val(this.value);
			$("#delfile_3").html("&nbsp;&nbsp;<span>"+arr_str[arr_len-1]+"</span>&nbsp;&nbsp;<a href='javascript:delFile(3);' class='tag is-danger'>삭제</a>");
		});
	 });
</script>
</head>

<body>
<? include INC_PATH."/top_menu.php"; ?>
<form method="post" name="form" action="board_write_act.php" enctype="multipart/form-data">
<input type="hidden" name="board" value="<?=$board?>">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="keyfield" value="<?=$keyfield?>">
<input type="hidden" name="keyword" value="<?=$keyword?>">
<input type="hidden" name="type" value="<?=$type?>">						<!-- 등록수정삭제구분 -->
<input type="hidden" name="seqno" value="<?=$seqno?>">						<!-- 글번호 -->
<input type="hidden" name="writer" value="<?=$board_login?>">				<!-- 글작성자 prs_login -->
<input type="hidden" name="writer_id" value="<?=$board_id?>">				<!-- 글작성자 prs_id -->
<input type="hidden" name="writer_name" value="<?=$board_name?>">			<!-- 글작성자 prs_name -->
<input type="hidden" name="writer_team" value="<?=$board_team?>">			<!-- 글작성자 prs_team -->
<input type="hidden" name="writer_position" value="<?=$board_position?>">	<!-- 글작성자 prs_position -->
<input type="hidden" name="notice_yn">
<input type="hidden" name="filedel_1" id="filedel_1">
<input type="hidden" name="filedel_2" id="filedel_2">
<input type="hidden" name="filedel_3" id="filedel_3">
<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
                <a class="navbar-item is-tab is-active" href="board_list.php">공지사항</a>
            </div>
        </div>
    </nav>
<!-- 서브 네비게이션 끝-->
		
<!--본문 시작-->
<section class="section is-subpage">
    <div class="container">
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="board_list.php" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>목록</span>
                    </a>
                </p>
            </div>                        						
            
            <div class="level-right">
                <p class="buttons">
                    <a href="javascript:funWrite();" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>게시물 <?=$type_title?></span>
                    </a>
                </p>
            </div>
        </nav>
        
        <div class="field-group">
            <div class="field">
                <div class="control">
                    <input name="title" class="input is-large" type="text" placeholder="제목을 입력하세요" value="<?=$board_title?>">                                        
									<br><br>
										<? if ($board == "default") { ?>
											<input type="checkbox" name="announcement"<? if ($board_notice == "Y") { echo " checked"; } ?>>&nbsp;<span>상단 공지사항 등록</span> 										
										<? } ?>
									<br>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <textarea name="contents" class="textarea" placeholder="10 lines of textarea" style="width:100%; height:100%"><?=$board_contents?></textarea>										
                </div>
            </div>
        </div>
               
        	<!--파일업로드-->
        	 <div class="box">
            <div class="control has-add-button"><!-- 커스텀 클래스 : has-add-button -->          
            	
            	<!--파일1-->  	
                <div class="file has-name is-right is-fullwidth" id="file_D1" name="file_D1">
                    <label class="file-label">
                        <input type="file" id="file_1" name="file_1" class="file-input" >                        
                        <span class="file-cta">
                              <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                              </span>
                              <span class="file-label">파일찾기</span>
                        </span>
                        <input type="text" id="attachment_1" name="attachment_1"class="file-name" readonly>                                                          
                    </label>
                    <span class="buttons">
                        	<a href="javascript:addFile();" class="button tag icon is-medium">                        		
                          	<i class="fas fa-plus"></i>
                         </a>
                    </span>                                        
                </div>
                <div class="attached" id="delfile_1">
												<? if ($type == "modify" && $board_file1 != "") { ?>	
													&nbsp;&nbsp;<span><?=$board_file1?></span>
													&nbsp;&nbsp;<a href="javascript:delFile(1);" class="tag is-danger">삭제</a>
												<? } ?>
								</div>
            
              <!--파일2-->  	
                <div class="file has-name is-right is-fullwidth" id="file_D2" name="file_D2"<? if ($board_file2 == "") { ?> style="display:none;"<? } ?>>
                    <label class="file-label">
                        <input type="file" id="file_2" name="file_2" class="file-input" >                        
                        <span class="file-cta">
                              <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                              </span>
                              <span class="file-label">파일찾기</span>
                        </span>
                        <input type="text" id="attachment_2" name="attachment_2"class="file-name" readonly>                                                          
                    </label>
                    <span class="buttons">
                        	<a href="javascript:addFile();" class="button tag icon is-medium">                        		
                          	<i class="fas fa-plus"></i>
                         </a>
                    </span>                                        
                </div>
                <div class="attached" id="delfile_2">
												<? if ($type == "modify" && $board_file2 != "") { ?>	
													&nbsp;&nbsp;<span><?=$board_file2?></span>
													&nbsp;&nbsp;<a href="javascript:delFile(2);" class="tag is-danger">삭제</a>
												<? } ?>
								</div>
                
                <!--파일3-->  	
                <div class="file has-name is-right is-fullwidth" id="file_D3" name="file_D3"<? if ($board_file2 == "") { ?> style="display:none;"<? } ?>>
                    <label class="file-label">
                        <input type="file" id="file_3" name="file_3" class="file-input" >                        
                        <span class="file-cta">
                              <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                              </span>
                              <span class="file-label">파일찾기</span>
                        </span>
                        <input type="text" id="attachment_3" name="attachment_3"class="file-name" readonly>                                                          
                    </label>
                    <span class="buttons">
                        	<a href="javascript:addFile();" class="button tag icon is-medium">                        		
                          	<i class="fas fa-plus"></i>
                         </a>
                    </span>                                        
                </div>
                <div class="attached" id="delfile_3">
												<? if ($type == "modify" && $board_file3 != "") { ?>	
													&nbsp;&nbsp;<span><?=$board_file3?></span>
													&nbsp;&nbsp;<a href="javascript:delFile(3);" class="tag is-danger">삭제</a>
												<? } ?>
								</div>
                <p class="help is-dark">* 한번에 올릴 수 있는 파일 용량은 최대 10MB 입니다.</p>
            </div>    
        </div>

			<!--파일업로드-->								
			
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="board_list.php" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>목록</span>
                    </a>
                </p>
            </div>                        						            
            <div class="level-right">
                <p class="buttons">
                    <a href="javascript:funWrite();" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>게시물 <?=$type_title?></span>
                    </a>
                </p>
            </div>
        </nav>
        
        
    </div>
</section>
<!--본문 끝-->		
		
</form>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>