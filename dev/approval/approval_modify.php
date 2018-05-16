<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "modify";
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null;

	if ($doc_no == "")
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ش� ������ �������� �ʽ��ϴ�.");
		self.close();
	</script>
<?
		exit;
	}

	$sql = "SELECT 
				FORM_CATEGORY, FORM_TITLE, TITLE, CONTENTS, OPEN_YN, FILE_1, FILE_2, FILE_3, 
				CONVERT(char(10),REG_DATE,120) AS REG_DATE, CONVERT(char(10),START_DATE,120) AS START_DATE, CONVERT(char(10),END_DATE,120) AS END_DATE, STATUS 
			FROM 
				DF_APPROVAL WITH(NOLOCK) 
			WHERE 
				DOC_NO = '$doc_no'";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);

	$form_category = $record['FORM_CATEGORY'];
	$form_title = $record['FORM_TITLE'];
	$title = $record['TITLE'];
	$contents = $record['CONTENTS'];
	$open_yn = $record['OPEN_YN'];
	$file_1 = $record['FILE_1'];
	$file_2 = $record['FILE_2'];
	$file_3 = $record['FILE_3'];
	$reg_date = $record['REG_DATE'];
	$start_date = $record['START_DATE'];
	$end_date = $record['END_DATE'];
	$status = $record['STATUS'];

	if ($form_title == "����" || $form_title == "������Ʈ") 
	{ 
		$form_title2 = "����/������Ʈ"; 
	} 
	else 
	{ 
		$form_title2 = $form_title; 
	}

	$sql = "SELECT FORM_NO, TO_COUNT, CC_COUNT FROM DF_APPROVAL_FORM WITH(NOLOCK) WHERE TITLE = '$form_title2'";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);

	$form_no = $record['FORM_NO'];
	$to_count = $record['TO_COUNT'];
	$cc_count = $record['CC_COUNT'];
?>

<? include INC_PATH."/pop_top.php"; ?>

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
	//����, ���� ������, ������ ����
	function funPersonAdd(type,no)
	{
		if (type == "to")
		{
			window.open('approval_to_add.php?max=<?=$to_count?>&no='+no,type,'width=600 ,height=400,scrollbars=no');
		}
		else if (type == "cc")
		{
			window.open('approval_cc_add.php?max=<?=$cc_count?>',type,'width=600 ,height=400,scrollbars=no');
		}
		else if (type == "partner")
		{
			window.open('approval_partner_add.php',type,'width=600 ,height=400,scrollbars=no');
		}
	}
	function funPersonDel(type,no)
	{
		if (type == "to")
		{
			document.getElementsByName("to_div_"+no)[0].innerHTML = "";
			document.getElementsByName("to_btn_div_"+no)[0].innerHTML = "";
			document.getElementsByName("to_btn_div_"+no)[0].innerHTML = "<input type=button id=to_btn_"+no+" name=to_btn_"+no+" value=���� onClick=javascript:funPersonAdd('to','"+no+"');>";
			document.getElementsByName("to_id_"+no)[0].value = "";

			var to_id = "";

			for (var i=0; i<<?=$to_count?>; i++)
			{
				to_id = to_id + document.getElementsByName("to_id_"+i)[0].value + ",";
			}
			document.form.to_id.value = to_id;
		}
	}

	//���
	function funWrite(type)
	{
		var frm = document.form;
		var contents =  CKEDITOR.instances['contents'].getData();//ckeditor ���� ���� �� �ޱ�

		if (type == "save")
		{
			var type_text = "�ӽ�����";
		}
		if (type == "write")
		{
			var type_text = "���";
		}

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
		if(confirm("<?=$form_category?>�� "+ type_text +" �Ͻðڽ��ϱ�")){
			frm.type.value = type;
			frm.target ="hdnFrame";
			frm.action = 'approval_modify_act.php'; 
			frm.submit();
		}
	}
	//���� ��� ����
	function selCase(f)
	{
		f.target="_self";
		f.action="<?=CURRENT_URL?>";
		f.submit();
	}
</script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post" enctype="multipart/form-data">
<input type="hidden" name="type">
	<? include INC_PATH."/top_menu.php"; ?>
		<div class="inner-home">
			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
				</div>
				<div class="board_list">
				<table class="notable work3 board_list" width="100%">
					<tr>
						<td colspan="4" style="text-align:center; font-weight:bold; font-size:20px; color:#000;"><?=$form_category?></td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:right;">
						<? if ($status == "�ӽ�") { ?>
							<input type="button" value="�ӽ�����" onClick="javascript:funWrite('save');">
						<? } ?>
							<input type="button" value="���" onClick="javascript:funWrite('write');">
							<input type="button" value="�ݱ�" onClick="javascript:self.close();">
						</td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">������ȣ</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;"><?=$doc_no?>
							<input type="hidden" name="doc_no" value="<?=$doc_no?>">
						</td>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;" rowspan="4">����</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:center;" rowspan="4">
						<table width="100%">
							<tr>
					<?
						$to = "";
						for ($i=0; $i<$to_count; $i++)
						{
							$j = $i + 1;
							$sql = "SELECT A_PRS_NAME, PRS_POSITION, A_PRS_ID FROM DF_APPROVAL_TO INNER JOIN DF_PERSON ON A_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' AND A_ORDER ='$j' AND PRF_ID IN (1,2,3,4,7)";
							$rs = sqlsrv_query($dbConn, $sql);

							$record = sqlsrv_fetch_array($rs);
							$rows = sqlsrv_has_rows($rs);
							if ($rows > 0)
							{
								$to_name = $record['A_PRS_NAME'];
								$to_position = $record['PRS_POSITION'];
								$to_id = $record['A_PRS_ID'];
							}
							else
							{
								$to_name = "";
								$to_position = "";
								$to_id = "";
							}
					?>
								<td align="center">
								<div id="to_div_<?=$i?>" name="to_div_<?=$i?>"><?=$to_position?><br><?=$to_name?></div>
					<?		if ($rows > 0) { ?>
								<div id="to_btn_div_<?=$i?>" name="to_btn_div_<?=$i?>"><input type="button" id="to_btn_<?=$i?>" name="to_btn_<?=$i?>" value="���" onClick="javascript:funPersonDel('to','<?=$i?>');"></div>	
					<?		} else { ?>
								<div id="to_btn_div_<?=$i?>" name="to_btn_div_<?=$i?>"><input type="button" id="to_btn_<?=$i?>" name="to_btn_<?=$i?>" value="����" onClick="javascript:funPersonAdd('to','<?=$i?>');"></div>
					<?		} ?>
								<input type="hidden" name="to_id_<?=$i?>" value="<?=$to_id?>">
								</td>
					<?
							$to = $to . $to_id .",";
						}
					?>
							</tr>
						</table>
						<input type="hidden" name="to_id" value="<?=$to?>">
						</td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">��������</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;"><?=$form_title2?>
							<input type="hidden" name="form_no" value="<?=$form_no?>">
							<input type="hidden" name="form_title" value="<?=$form_title2?>">
							<input type="hidden" name="form_category" value="<?=$form_category?>">
						</td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">�μ�</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;"><?=getTeamInfo($prs_team)?></td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">�����</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;">
						<?
							if ($type == "modify") 
							{
								echo substr($reg_date,0,4) ."�� ". substr($reg_date,5,2) ."�� ". substr($reg_date,8,2) ."��";
							}
							else
							{
								echo date("Y") ."�� ". date("m") ."�� ". date("d") ."��";
							}
						?>
						</td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">��������</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;">
							<select name="open_yn">
								<option value="Y">����</option>
								<option value="N">�����</option>
							</select>
						</td>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">��������</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;">
						<?
							$sql = "SELECT C_PRS_NAME, PRS_POSITION, C_PRS_ID FROM DF_APPROVAL_CC INNER JOIN DF_PERSON ON C_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' AND PRF_ID IN (1,2,3,4,7) ORDER BY C_ORDER";
							$rs = sqlsrv_query($dbConn, $sql);

							$i = 0;
							$cc = "";
							$c_id = "";
							while ($record = sqlsrv_fetch_array($rs))
							{
								$cc_name = $record['C_PRS_NAME'];
								$cc_position = $record['PRS_POSITION'];
								$cc_id = $record['C_PRS_ID'];

								if ($i == 0) {
									$cc = $cc_position ." ". $cc_name;
									$c_id = $cc_id;
								} else {
									$cc = $cc .", ". $cc_position ." ". $cc_name;
									$c_id = $c_id .", ". $cc_id;
								}
								
								$i++;
							}
						?>
							<input type="text" name="cc" style="width:60%;" value="<?=$cc?>" readonly> <input type="button" id="cc_btn" name="cc_btn" value="������ ����" onClick="javascript:funPersonAdd('cc');">
							<input type="hidden" name="cc_id" value="<?=$c_id?>">
						</td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">�̸�</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;" colspan="3"><?=$prs_position?> <?=$prs_name?></td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">����</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;" colspan="3">
							<input type="text" name="title" value="<?=$title?>" style="width:90%; height:20px;">
						</td>
					</tr>
				<? if ($form_category != "���ǰ�Ǽ�" && $form_category != "������Ʈ ����ǰ�Ǽ�") { ?>				
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">�Ⱓ</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;" colspan="3">
					<?
						if ($form_title2 == "����/������Ʈ") { 
							$sql = "SELECT 
										CONVERT(char(10),START_DATE,120) AS START_DATE, CONVERT(char(10),END_DATE,120) AS END_DATE 
									FROM 
										DF_APPROVAL WITH(NOLOCK)
									WHERE 
										DOC_NO = '$doc_no' AND FORM_TITLE = '����'";
							$rs = sqlsrv_query($dbConn,$sql);

							$rows = sqlsrv_has_rows($rs);
							$record = sqlsrv_fetch_array($rs);

							if ($rows > 0) 
							{
								$start_date = $record['START_DATE'];
								$end_date = $record['END_DATE'];
							}
							else
							{
								$start_date = date("Y-m-d");
								$end_date = date("Y-m-d");
							}
					?>
							<input type="checkbox" name="vacation1" value="����"<? if ($rows > 0) { echo " checked"; } ?>>����&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<select name="fr_year1">
							<? for ($i=2013; $i<=date("Y",strtotime("+1 year")); $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,0,4)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="fr_month1">
							<? for ($i=1; $i<=12; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,5,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="fr_day1">
							<? for ($i=1; $i<=31; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,8,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							&nbsp;&nbsp;~&nbsp;&nbsp;
							<select name="to_year1">
							<? for ($i=2013; $i<=date("Y",strtotime("+1 year")); $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,0,4)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="to_month1">
							<? for ($i=1; $i<=12; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,5,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="to_day1">
							<? for ($i=1; $i<=31; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,8,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<br>
					<?
							$sql = "SELECT 
										CONVERT(char(10),START_DATE,120) AS START_DATE, CONVERT(char(10),END_DATE,120) AS END_DATE 
									FROM 
										DF_APPROVAL WITH(NOLOCK)
									WHERE 
										DOC_NO = '$doc_no' AND FORM_TITLE = '������Ʈ'";
							$rs = sqlsrv_query($dbConn,$sql);

							$rows = sqlsrv_has_rows($rs);
							$record = sqlsrv_fetch_array($rs);

							if ($rows > 0) 
							{
								$start_date = $record['START_DATE'];
								$end_date = $record['END_DATE'];
							}
							else
							{
								$start_date = date("Y-m-d");
								$end_date = date("Y-m-d");
							}
					?>
							<input type="checkbox" name="vacation2" value="������Ʈ"<? if ($rows > 0) { echo " checked"; } ?>>������Ʈ&nbsp;&nbsp;&nbsp;&nbsp;
							<select name="fr_year2">
							<? for ($i=2013; $i<=date("Y",strtotime("+1 year")); $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,0,4)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="fr_month2">
							<? for ($i=1; $i<=12; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,5,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="fr_day2">
							<? for ($i=1; $i<=31; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,8,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							&nbsp;&nbsp;~&nbsp;&nbsp;
							<select name="to_year2">
							<? for ($i=2013; $i<=date("Y",strtotime("+1 year")); $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,0,4)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="to_month2">
							<? for ($i=1; $i<=12; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,5,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="to_day2">
							<? for ($i=1; $i<=31; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,8,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
					<? } else { ?>
							<select name="fr_year">
							<? for ($i=2013; $i<=date("Y",strtotime("+1 year")); $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,0,4)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="fr_month">
							<? for ($i=1; $i<=12; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,5,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="fr_day">
							<? for ($i=1; $i<=31; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($start_date,8,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							&nbsp;&nbsp;~&nbsp;&nbsp;
							<select name="to_year">
							<? for ($i=2013; $i<=date("Y",strtotime("+1 year")); $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,0,4)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="to_month">
							<? for ($i=1; $i<=12; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,5,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
							<select name="to_day">
							<? for ($i=1; $i<=31; $i++) { ?>
								<option value="<?=$i?>"<? if ($i == substr($end_date,8,2)) { echo " selected"; } ?>><?=$i?></option>
							<? } ?>
							</select>��
					<? } ?>
						</td>
					</tr>
				<? } ?>
				<? if ($form_category == "�ٰܱ�/�İ߰�" || $form_category == "�����") { ?>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">������</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;" colspan="3">
						<?
							$sql = "SELECT P_PRS_NAME, P_PRS_POSITION, P_PRS_ID FROM DF_APPROVAL_PARTNER INNER JOIN DF_PERSON ON P_PRS_ID = PRS_ID WHERE DOC_NO = '$doc_no' ORDER BY P_ORDER";
							$rs = sqlsrv_query($dbConn, $sql);

							$i = 0;
							$partner = "";
							$p_id = "";
							while ($record = sqlsrv_fetch_array($rs))
							{
								$partner_name = $record['P_PRS_NAME'];
								$partner_position = $record['PRS_POSITION'];
								$partner_id = $record['P_PRS_ID'];

								if ($i == 0) {
									$partner = $partner_position ." ". $partner_name;
									$p_id = $partner_id;
								} else {
									$partner = $partner .", ". $partner_position ." ". $partner_name;
									$p_id = $p_id .", ". $partner_id;
								}
								
								$i++;
							}
						?>
							<input type="text" name="partner" style="width:70%;" value="<?=$partner?>" readonly> <input type="button" id="partner_btn" name="partner_btn" value="������ ����" onClick="javascript:funPersonAdd('partner');">
							<input type="hidden" name="partner_id" value="<?=$p_id?>">
						</td>
					</tr>
				<? } ?>
					<tr>
						<td colspan="4">
							<textarea name="contents" style="width:100%;height:100%;"><?=$contents?></textarea>
						</td>
					</tr>
					<tr>
						<td style="background:#AAA; height:20px; width:10%; text-align:center; font-weight:bold;">÷������</td>
						<td style="background:#EEE; height:20px; width:40%; text-align:left; padding-left:10px;" colspan="3">
							<div class="clearfix">
							<? if ($file_1 != "") { ?>
								<?=$file_1?> <input type="checkbox" name="del_1" value="Y">����
							<? } ?>
							<input type="file" name="file_1">
							<br>
							<? if ($file_2 != "") { ?>
								<?=$file_2?> <input type="checkbox" name="del_2" value="Y">����
							<? } ?>
							<input type="file" name="file_2">
							<br>
							<? if ($file_3 != "") { ?>
								<?=$file_3?> <input type="checkbox" name="del_3" value="Y">����
							<? } ?>
							<input type="file" name="file_3">
							<p class="describtion">�� �ѹ��� �ø� �� �ִ� ���� �뷮�� <span>�ִ� 10MB</span> �Դϴ�.</p>
							</div>
						</td>
					</tr>
				</table>
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/pop_bottom.php"; ?>
</div>
</body>
</html>
