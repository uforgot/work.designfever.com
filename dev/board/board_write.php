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
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$writer = isset($_REQUEST['writer']) ? $_REQUEST['writer'] : null;  
	$writer_id = isset($_REQUEST['writer_id']) ? $_REQUEST['writer_id'] : null;  

	if ($type == "modify")
	{
		$type_title = "����";

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
	}
	else if ($type == "write")
	{
		$type_title = "�ۼ�";

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
		var contents =  CKEDITOR.instances['contents'].getData();//ckeditor ���� ���� �� �ޱ�

		if(frm.title.value == ""){
			alert("������ �Է����ּ���");
			frm.title.focus();
			return;
		}
		if(contents==""){
			alert("������ �Է����ּ���");
			CKEDITOR.instances['contents'].focus();		//ckeditor ��Ŀ�� �̵��ϴ� �κ�
			return;    	
		}
			//���� ��ȿ�� �˻� �� �κ�
		if(confirm("�Խñ��� <?=$type_title?> �Ͻðڽ��ϱ�")){
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
				alert("���� ÷�δ� �ִ� 3������ �����մϴ�.");
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
		//���õ� ���ϸ� ǥ��
		$("#file_1").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_1").val(this.value);
			$("#delfile_1").html("&nbsp;&nbsp;&nbsp;<span>"+arr_str[arr_len-1]+"</span>&nbsp;&nbsp;<a href='javascript:delFile(1);' class='tag is-danger'>����</a>");
		});
		$("#file_2").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_2").val(this.value);
			$("#delfile_2").html("&nbsp;&nbsp;<span>"+arr_str[arr_len-1]+"</span>&nbsp;&nbsp;<a href='javascript:delFile(2);' class='tag is-danger'>����</a>");
		});
		$("#file_3").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_3").val(this.value);
			$("#delfile_3").html("&nbsp;&nbsp;<span>"+arr_str[arr_len-1]+"</span>&nbsp;&nbsp;<a href='javascript:delFile(3);' class='tag is-danger'>����</a>");
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
<input type="hidden" name="type" value="<?=$type?>">						<!-- ��ϼ����������� -->
<input type="hidden" name="seqno" value="<?=$seqno?>">						<!-- �۹�ȣ -->
<input type="hidden" name="writer" value="<?=$board_login?>">				<!-- ���ۼ��� prs_login -->
<input type="hidden" name="writer_id" value="<?=$board_id?>">				<!-- ���ۼ��� prs_id -->
<input type="hidden" name="writer_name" value="<?=$board_name?>">			<!-- ���ۼ��� prs_name -->
<input type="hidden" name="writer_team" value="<?=$board_team?>">			<!-- ���ۼ��� prs_team -->
<input type="hidden" name="writer_position" value="<?=$board_position?>">	<!-- ���ۼ��� prs_position -->
<input type="hidden" name="notice_yn">
<input type="hidden" name="filedel_1" id="filedel_1">
<input type="hidden" name="filedel_2" id="filedel_2">
<input type="hidden" name="filedel_3" id="filedel_3">
<!-- ���� �׺���̼� ���� -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
                <a class="navbar-item is-tab is-active" href="board_list.php">��������</a>
            </div>
        </div>
    </nav>
<!-- ���� �׺���̼� ��-->
		
<!--���� ����-->
<section class="section is-subpage">
    <div class="container">
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="board_list.php" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>���</span>
                    </a>
                </p>
            </div>                        						
            
            <div class="level-right">
                <p class="buttons">
                    <a href="javascript:funWrite();" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>�Խù� <?=$type_title?></span>
                    </a>
                </p>
            </div>
        </nav>
        
        <div class="field-group">
            <div class="field">
                <div class="control">
                    <input name="title" class="input is-large" type="text" placeholder="������ �Է��ϼ���" value="<?=$board_title?>">                                        
									<br><br>
										<? if ($board == "default") { ?>
											<input type="checkbox" name="announcement"<? if ($board_notice == "Y") { echo " checked"; } ?>>&nbsp;<span>��� �������� ���</span> 										
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
               
        	<!--���Ͼ��ε�-->
        	 <div class="box">
            <div class="control has-add-button"><!-- Ŀ���� Ŭ���� : has-add-button -->          
            	
            	<!--����1-->  	
                <div class="file has-name is-right is-fullwidth" id="file_D1" name="file_D1">
                    <label class="file-label">
                        <input type="file" id="file_1" name="file_1" class="file-input" >                        
                        <span class="file-cta">
                              <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                              </span>
                              <span class="file-label">����ã��</span>
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
													&nbsp;&nbsp;<a href="javascript:delFile(1);" class="tag is-danger">����</a>
												<? } ?>
								</div>
            
              <!--����2-->  	
                <div class="file has-name is-right is-fullwidth" id="file_D2" name="file_D2"<? if ($board_file2 == "") { ?> style="display:none;"<? } ?>>
                    <label class="file-label">
                        <input type="file" id="file_2" name="file_2" class="file-input" >                        
                        <span class="file-cta">
                              <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                              </span>
                              <span class="file-label">����ã��</span>
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
													&nbsp;&nbsp;<a href="javascript:delFile(2);" class="tag is-danger">����</a>
												<? } ?>
								</div>
                
                <!--����3-->  	
                <div class="file has-name is-right is-fullwidth" id="file_D3" name="file_D3"<? if ($board_file2 == "") { ?> style="display:none;"<? } ?>>
                    <label class="file-label">
                        <input type="file" id="file_3" name="file_3" class="file-input" >                        
                        <span class="file-cta">
                              <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                              </span>
                              <span class="file-label">����ã��</span>
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
													&nbsp;&nbsp;<a href="javascript:delFile(3);" class="tag is-danger">����</a>
												<? } ?>
								</div>
                <p class="help is-dark">* �ѹ��� �ø� �� �ִ� ���� �뷮�� �ִ� 10MB �Դϴ�.</p>
            </div>    
        </div>

			<!--���Ͼ��ε�-->								
			
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a href="board_list.php" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>���</span>
                    </a>
                </p>
            </div>                        						            
            <div class="level-right">
                <p class="buttons">
                    <a href="javascript:funWrite();" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>�Խù� <?=$type_title?></span>
                    </a>
                </p>
            </div>
        </nav>
        
        
    </div>
</section>
<!--���� ��-->		
		
</form>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>