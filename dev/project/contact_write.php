<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
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

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null; 
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
					PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(VARCHAR(16),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3, TMP1, TMP4, TMP5, TMP6, TMP7, TMP8, TMP9
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
			$book_tmp1 = $record['TMP1'];
			$book_tmp4 = $record['TMP4'];
			$book_tmp5 = $record['TMP5'];
			$book_tmp6 = $record['TMP6'];
			$book_tmp7 = $record['TMP7'];
			$book_tmp8 = $record['TMP8'];
			$book_tmp9 = $record['TMP9'];
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
		$type_title = "���";

		$book_id = $prs_id;
		$book_name = $prs_name;
		$book_login = $prs_login;
		$book_team = $prs_team;
		$book_position = $prs_position;
		$book_title = "";
		$book_contents = "";
		$book_notice = "";
		$book_file1 = "";
		$book_file2 = "";
		$book_file3 = "";

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
			frm.target = "hdnFrame";
			frm.action = 'contact_write_act.php'; 
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

    function minusFile(file)
    {
        if (file == 2)
        {
            document.getElementById("file_D2").style.display = "none"
            document.getElementById("attachment_2").value="";
            delFile(2);

        }else if(file==3){
            document.getElementById("file_D3").style.display = "none"
            document.getElementById("attachment_3").value="";
            delFile(3);
        }else{}
    }

	$(document).ready(function(){
		//���õ� ���ϸ� ǥ��
		$("#file_1").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_1").val(this.value);
			$("#delfile_1").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(1);'><img src='../img/btn_delete.gif' alt='����' /></a>");
		});
		$("#file_2").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_2").val(this.value);
			$("#delfile_2").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(2);'><img src='../img/btn_delete.gif' alt='����' /></a>");
		});
		$("#file_3").change(function(){
			var str = this.value;
			var arr_str = str.split("\\");
			var arr_len = arr_str.length;
			$("#attachment_3").val(this.value);
			$("#delfile_3").html("<span>"+arr_str[arr_len-1]+"</span><a href='javascript:delFile(3);'><img src='../img/btn_delete.gif' alt='����' /></a>");
		});
	 });
</script>
</head>

<body>
<? include INC_PATH."/top_menu.php"; ?>
<form method="post" name="form" action="contact_write_act.php" enctype="multipart/form-data">
<input type="hidden" name="board" value="<?=$board?>">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="subject" value="<?=$subject?>">
<input type="hidden" name="keyfield" value="<?=$keyfield?>">
<input type="hidden" name="keyword" value="<?=$keyword?>">
<input type="hidden" name="type" value="<?=$type?>">						<!-- ��ϼ����������� -->
<input type="hidden" name="seqno" value="<?=$seqno?>">						<!-- �۹�ȣ -->
<input type="hidden" name="writer" value="<?=$book_login?>">				<!-- ���ۼ��� prs_login -->
<input type="hidden" name="writer_id" value="<?=$book_id?>">				<!-- ���ۼ��� prs_id -->
<input type="hidden" name="writer_name" value="<?=$book_name?>">			<!-- ���ۼ��� prs_name -->
<input type="hidden" name="writer_team" value="<?=$book_team?>">			<!-- ���ۼ��� prs_team -->
<input type="hidden" name="writer_position" value="<?=$book_position?>">	<!-- ���ۼ��� prs_position -->
<input type="hidden" name="notice_yn">
<input type="hidden" name="filedel_1" id="filedel_1">
<input type="hidden" name="filedel_2" id="filedel_2">
<input type="hidden" name="filedel_3" id="filedel_3">
<? include INC_PATH."/project_menu.php"; ?>
    <!--���� ����-->
    <section class="section is-subpage">
        <div class="container">
            <div class="content">
                <nav class="level is-mobile">
                    <div class="level-left">
                        <p class="buttons">
                            <a href="contact_list.php" class="button">
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
                            <input name="title" class="input is-large" type="text" placeholder="������Ʈ" value="<?=$book_title?>">
                            <br>
                            <br>
                        </div>
                        <div class="control">
                            <input name="title" class="input is-large" type="text" placeholder="������Ʈ" value="<?=$book_title?>">
                            <br>
                            <br>
                        </div>
                        <div class="control">
                            <input name="title" class="input is-large" type="text" placeholder="������Ʈ" value="<?=$book_title?>">
                            <br>
                            <br>
                        </div>
                        <div class="control">
                            <input name="title" class="input is-large" type="text" placeholder="������Ʈ" value="<?=$book_title?>">
                            <br>
                            <br>
                        </div>
                        <div class="control">
                            <input name="title" class="input is-large" type="text" placeholder="������Ʈ" value="<?=$book_title?>">
                            <br>
                            <br>
                        </div>
                        <div class="control">
                            <input name="title" class="input is-large" type="text" placeholder="������Ʈ" value="<?=$book_title?>">
                            <br>
                            <br>
                        </div>
                        <div class="control">
                            <input name="title" class="input is-large" type="text" placeholder="������Ʈ" value="<?=$book_title?>">
                            <br>
                            <br>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <textarea name="contents" class="textarea" placeholder="10 lines of textarea" style="width:100%; height:100%"><?=$book_contents?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="box">
                    <!--����1-->
                    <div class="columns is-mobile">
                        <div class="column">
                            <div class="file has-name is-fullwidth" id="file_D1" name="file_D1">
                                <label class="file-label">
                                    <input class="file-input" type="file" id="file_1" name="file_1">
                                <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">����ã��</span>
                                 </span>
                                    <input type="text" id="attachment_1" name="attachment_1"class="file-name" readonly>
                                </label>
                            </div>
                        </div>
                        <div class="column last-button">
                            <span class="buttons">
                                <a class="button is-fullwidth" href="javascript:addFile();">
                                    <span class="icon is-small">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="attached" id="delfile_1">
                        <? if ($type == "modify" && $book_file1 != "") { ?>
                            &nbsp;&nbsp;<span><?=$book_file1?></span>
                            &nbsp;&nbsp;<a href="javascript:delFile(1);" class="tag is-danger">����</a>
                        <? } ?>
                    </div>

                    <!--����2-->
                    <div class="columns is-mobile" id="file_D2" name="file_D2"<? if ($book_file2 == "") { ?> style="display:none;"<? } ?>>
                        <div class="column">
                            <div class="file has-name is-fullwidth" >
                                <label class="file-label">
                                    <input class="file-input" type="file" id="file_2" name="file_2">
                                <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">����ã��</span>
                                 </span>
                                    <input type="text" id="attachment_2" name="attachment_2"class="file-name" readonly>
                                </label>
                            </div>
                        </div>
                        <div class="column last-button">
                            <span class="buttons">
                                <a class="button is-fullwidth" href="javascript:minusFile(2);">
                                    <span class="icon is-small">
                                        <i class="fas fa-minus"></i>
                                    </span>
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="attached" id="delfile_2">
                        <? if ($type == "modify" && $book_file2 != "") { ?>
                            &nbsp;&nbsp;<span><?=$book_file2?></span>
                            &nbsp;&nbsp;<a href="javascript:delFile(2);" class="tag is-danger">����</a>
                        <? } ?>
                    </div>

                    <!--����3-->
                    <div class="columns is-mobile" id="file_D3" name="file_D3"<? if ($book_file3 == "") { ?> style="display:none;"<? } ?>>
                        <div class="column">
                            <div class="file has-name is-fullwidth" >
                                <label class="file-label">
                                    <input class="file-input" type="file" id="file_3" name="file_3">
                                <span class="file-cta">
                                        <span class="file-icon">
                                            <i class="fas fa-upload"></i>
                                        </span>
                                        <span class="file-label">����ã��</span>
                                 </span>
                                    <input type="text" id="attachment_3" name="attachment_3"class="file-name" readonly>
                                </label>
                            </div>
                        </div>
                        <div class="column last-button">
                            <span class="buttons">
                                <a class="button is-fullwidth" href="javascript:minusFile(3);">
                                    <span class="icon is-small">
                                        <i class="fas fa-minus"></i>
                                    </span>
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="attached" id="delfile_3">
                        <? if ($type == "modify" && $book_file3 != "") { ?>
                            &nbsp;&nbsp;<span><?=$book_file3?></span>
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
                        <a href="contact_list.php" class="button">
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
