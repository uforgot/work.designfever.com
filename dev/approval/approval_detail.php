<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 

	if ($doc_no == "") {
?>
<script type="text/javascript">
	alert("�ش� ������ �������� �ʽ��ϴ�.");
</script>
<?
		exit;
	}

	$searchSQL = " WHERE DOC_NO = '$doc_no'";

	$sql = "SELECT ISNULL(COUNT(SEQNO),0) AS COUNT, FORM_CATEGORY FROM DF_APPROVAL WITH(NOLOCK) $searchSQL GROUP BY FORM_CATEGORY";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$count = $record['COUNT'];
	$category = $record['FORM_CATEGORY'];

	$sql = "SELECT
				FORM_TITLE, TITLE, CONTENTS, CONVERT(char(10),APPROVAL_DATE,102) AS APPROVAL_DATE, PRS_ID, PRS_TEAM, PRS_POSITION, PRS_NAME, STATUS, PAYMENT_YN, OPEN_YN, 
				CONVERT(char(10),START_DATE,102) AS START_DATE, CONVERT(char(10),END_DATE,102) AS END_DATE, USE_DAY, FORM_CATEGORY, FORM_TITLE, FILE_1, FILE_2, FILE_3, 
				PROJECT_NO, TEAM_NAME
			FROM 
				DF_APPROVAL WITH(NOLOCK)
			WHERE
				DOC_NO = '$doc_no' AND USE_YN = 'Y'
			ORDER BY 
				SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($category == "�ް���") {
		if ($count == 2) {
			$vacation = "";
			$v = 0;
			while ($record = sqlsrv_fetch_array($rs)) {
				$form_title		= $record['FORM_TITLE'];
				$title			= $record['TITLE'];
				$contents		= $record['CONTENTS'];
				$approval_date	= $record['APPROVAL_DATE'];
				$id				= $record['PRS_ID'];
				$team			= $record['PRS_TEAM'];
				$position		= $record['PRS_POSITION'];
				$name			= $record['PRS_NAME'];
				$status			= $record['STATUS'];
				$payment_yn		= $record['PAYMENT_YN'];
				$open_yn		= $record['OPEN_YN'];
				$start_date		= $record['START_DATE'];
				$end_date		= $record['END_DATE'];
				$use_day		= $record['USE_DAY'];
				$form_category	= $record['FORM_CATEGORY'];
				$form_title		= $record['FORM_TITLE'];
				$file1			= $record['FILE_1'];
				$file2			= $record['FILE_2'];
				$file3			= $record['FILE_3'];

				if ($form_title == '����') {
					$vacation .= "���� �ް�&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ". $start_date ." - ". $end_date ." (". floatval($use_day) ."��)<br>";
				} else if ($form_title == '������Ʈ') {
					$vacation .= "������Ʈ �ް� : ". $start_date ." - ". $end_date ." (". floatval($use_day) ."��)<br>";
				}
				
				$v++;
			}

			$form_title		 = "����";
			$writer_team	 = $team;
			$writer_position = $position;
			$writer_name	 = $name;
		} else {
			$record = sqlsrv_fetch_array($rs);

			$title			= $record['TITLE'];
			$contents		= $record['CONTENTS'];
			$approval_date	= $record['APPROVAL_DATE'];
			$id				= $record['PRS_ID'];
			$team			= $record['PRS_TEAM'];
			$position		= $record['PRS_POSITION'];
			$name			= $record['PRS_NAME'];
			$status			= $record['STATUS'];
			$payment_yn		= $record['PAYMENT_YN'];
			$open_yn		= $record['OPEN_YN'];
			$start_date		= $record['START_DATE'];
			$end_date		= $record['END_DATE'];
			$use_day		= $record['USE_DAY'];
			$form_category	= $record['FORM_CATEGORY'];
			$form_title		= $record['FORM_TITLE'];
			$file1			= $record['FILE_1'];
			$file2			= $record['FILE_2'];
			$file3			= $record['FILE_3'];

			$vacation		.= $start_date ." - ". $end_date ." (". floatval($use_day) ."��)";

			$writer_team	 = $team;
			$writer_position = $position;
			$writer_name	 = $name;
		}
	} else {
		if ($count > 1) {
			while ($record = sqlsrv_fetch_array($rs)) {
				$title			= $record['TITLE'];
				$contents		= $record['CONTENTS'];
				$approval_date	= $record['APPROVAL_DATE'];
				$id				= $record['PRS_ID'];
				$team			= $record['PRS_TEAM'];
				$position		= $record['PRS_POSITION'];
				$name			= $record['PRS_NAME'];
				$status			= $record['STATUS'];
				$payment_yn		= $record['PAYMENT_YN'];
				$open_yn		= $record['OPEN_YN'];
				$start_date		= $record['START_DATE'];
				$end_date		= $record['END_DATE'];
				$use_day		= $record['USE_DAY'];
				$form_category	= $record['FORM_CATEGORY'];
				$form_title		= $record['FORM_TITLE'];
				$file1			= $record['FILE_1'];
				$file2			= $record['FILE_2'];
				$file3			= $record['FILE_3'];

				if ($form_category == "�ް���") { $form_title = $form_title ." �ް���";	}

				$vacation		= $start_date ." - ". $end_date ." (". $use_day ."��)";

				$writer_team	 = $team;
				$writer_position = $position;
				$writer_name	 = $name;
			}
		} else {
			$record = sqlsrv_fetch_array($rs);
			
			$title			= $record['TITLE'];
			$contents		= $record['CONTENTS'];
			$approval_date	= $record['APPROVAL_DATE'];
			$id				= $record['PRS_ID'];
			$team			= $record['PRS_TEAM'];
			$position		= $record['PRS_POSITION'];
			$name			= $record['PRS_NAME'];
			$status			= $record['STATUS'];
			$payment_yn		= $record['PAYMENT_YN'];
			$open_yn		= $record['OPEN_YN'];
			$start_date		= $record['START_DATE'];
			$end_date		= $record['END_DATE'];
			$use_day		= $record['USE_DAY'];
			$form_category	= $record['FORM_CATEGORY'];
			$form_title		= $record['FORM_TITLE'];
			$file1			= $record['FILE_1'];
			$file2			= $record['FILE_2'];
			$file3			= $record['FILE_3'];
			$project_no		= $record['PROJECT_NO'];
			$team_name		= $record['TEAM_NAME'];
			
			$vacation		.= $start_date ." - ". $end_date ." (". $use_day ."��)";

			$writer_team	 = $team;
			$writer_position = $position;
			$writer_name	 = $name;

			$sql = "SELECT TITLE FROM DF_PROJECT WITH(NOLOCK) WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			$record = sqlsrv_fetch_array($rs);
			$project = $record['TITLE'];
		}
	}

	$contents = str_replace('"','\"',$contents);
	$contents = str_replace("\r","",$contents);
	$contents = str_replace("\n","",$contents);

	// ����ǥ�� ��ȯ
	$_n = array('0'=>'��','1'=>'��','2'=>'��','3'=>'��','4'=>'��');
?>

<? include INC_PATH."/top.php"; ?>

<script src="/js/approval.js"></script>
<script type="text/javascript">
	$(document).ready(function(){

		log_html = "<a href=\"javascript:funLog('<?=$doc_no?>');\"><img src=\"/img/btn_approveLog.gif\" alt=\"����α�\" /></a>";
		modify_html = "";

	// ������ư ���
	<? if ($id == $prs_id && $status == "�̰���") { ?>

		modify_html = modify_html + "<a href=\"javascript:funModify('<?=$doc_no?>');\"><img src=\"/img/btn_modify2.gif\" alt=\"����\"></a>";
		
	<? } ?>

		content_html = "";
		content_html = content_html + "		<form name=\"form2\" method=\"post\">";
		content_html = content_html + "		<input type=\"hidden\" name=\"doc_no\" value=\"<?=$doc_no?>\">";
		content_html = content_html + "		<input type=\"hidden\" name=\"type\" value=\"<?=$type?>\">";
		content_html = content_html + "		<table class=\"content-table\" width=\"100%\">";
		content_html = content_html + "			<colgroup>";
		content_html = content_html + "				<col width=\"13%\" />";
		content_html = content_html + "				<col width=\"37%\" />";
		content_html = content_html + "				<col width=\"13%\" />";
		content_html = content_html + "				<col width=\"37%\" />";
		content_html = content_html + "			</colgroup>";
		content_html = content_html + "			<tbody class=\"\">";
		content_html = content_html + "			   <tr>";
		content_html = content_html + "					<th class=\"gray\">������ȣ</th>";
		content_html = content_html + "					<td><?=$doc_no?></td>";
		content_html = content_html + "					<th class=\"gray\" rowspan=\"2\">����</th>";
		content_html = content_html + "					<td rowspan=\"2\" style=\"padding:0;\">";
		content_html = content_html + "						<table class=\"sign\" width=\"100%\">";
		<?
			$sql = "SELECT 
						A.A_ORDER, A.A_PRS_ID, A.A_PRS_NAME, A.A_PRS_POSITION, CONVERT(char(10),A.A_REG_DATE,102) AS A_REG_DATE, A.A_STATUS, B.PRS_SIGN, B.PRS_SIGNPWD 
					FROM 
						DF_APPROVAL_TO A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) 
					ON 
						A.A_PRS_ID = B.PRS_ID 
					WHERE 
						DOC_NO = '$doc_no' 
					ORDER BY 
						A_ORDER";
			$rs = sqlsrv_query($dbConn, $sql);

			$i = 0;
			$to_status_prev = "����";

			$to_orderArr	= "";
			$to_idArr		= "";
			$to_nameArr		= "";
			$to_positionArr = "";
			$to_dateArr		= "";
			$to_statusArr	= "";
			$to_signArr		= "";
			$to_signpwdArr	= "";
			$to_status_prevArr = "";

			while ($record = sqlsrv_fetch_array($rs)) {
				$to_order	 = $record['A_ORDER'];
				$to_id		 = $record['A_PRS_ID'];
				$to_name	 = $record['A_PRS_NAME'];
				$to_position = $record['A_PRS_POSITION'];
				$to_date	 = $record['A_REG_DATE'];
				$to_status	 = $record['A_STATUS'];
				$to_sign	 = $record['PRS_SIGN'];
				$to_signpwd	 = $record['PRS_SIGNPWD'];

				$to_status_prevArr = $to_status_prevArr . "##". $to_status_prev;

				$to_orderArr	= $to_orderArr ."##". $to_order;
				$to_idArr		= $to_idArr ."##". $to_id;
				$to_nameArr		= $to_nameArr ."##". $to_name;
				$to_positionArr = $to_positionArr ."##". $to_position;
				$to_dateArr		= $to_dateArr ."##". $to_date;
				$to_statusArr	= $to_statusArr ."##". $to_status;
				$to_signArr		= $to_signArr ."##". $to_sign;
				$to_signpwdArr	= $to_signpwdArr ."##". $to_signpwd;

				$to_status_prev = $to_status;

				$i++;
			}

			$to_orderThis		= explode("##",$to_orderArr);
			$to_idThis			= explode("##",$to_idArr);
			$to_nameThis		= explode("##",$to_nameArr);
			$to_positionThis	= explode("##",$to_positionArr);
			$to_dateThis		= explode("##",$to_dateArr);
			$to_statusThis		= explode("##",$to_statusArr);
			$to_signThis		= explode("##",$to_signArr);
			$to_signpwdThis		= explode("##",$to_signpwdArr);
			$to_status_prevThis = explode("##",$to_status_prevArr);
	
			$ArrCount = count($to_orderThis);
		?>
		content_html = content_html + "							<tr style=\"height:30px\">";
		<?
			for ($i=1; $i<$ArrCount; $i++) {
		?>
		content_html = content_html + "								<td width=\"20%\"<? if ($i == 5) {?> class=\"last\"<? } ?>><?=$to_positionThis[$i]?></td>";
		<?
			}
			
			for ($i=$ArrCount; $i<=5; $i++) {
		?>
		content_html = content_html + "								<td width=\"20%\"<? if ($i == 5) {?> class=\"last\"<? } ?>></td>";
		<?
			}
		?>
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr style=\"height:65px\">";
		<?
			for ($i=1; $i<$ArrCount; $i++) {
		?>
		content_html = content_html + "								<td<? if ($i == 5) {?> class=\"last\"<? } ?>>";
			<? if ($to_statusThis[$i] == "�̰���") { ?>
				<? if ($to_idThis[$i] == $prs_id && $to_status_prevThis[$i] == "����" && $status != "�ӽ�") { ?>
		content_html = content_html + "									<a href=\"javascript:funSign('<?=$doc_no?>','<?=$to_orderThis[$i]?>','<?=$to_signpwdThis[$i]?>');\"><img src=\"/img/state_approval.gif\" alt=\"\"></a>";
				<? } else { ?>
		content_html = content_html + "									<?=$to_nameThis[$i]?>";
				<? } ?>
			<? } else { ?>
				<? if ($to_signThis[$i] == "") { ?>
		content_html = content_html + "									<span class=\"signature\"><?=$to_nameThis[$i]?></span>";
				<? } else { ?>
		content_html = content_html + "									<img src=\"<?=PRS_URL . $to_signThis[$i]?>\" width=\"41\" height=\"41\"><br><?=$to_nameThis[$i]?>";
				<? } ?>
			<? } ?>
		content_html = content_html + "								</td>";
		<?
			}
			
			for ($i=$ArrCount; $i<=5; $i++) {
		?>
		content_html = content_html + "								<td<? if ($i == 5) {?> class=\"last\"<? } ?>></td>";
		<?
			}
		?>
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr style=\"height:30px\" class=\"last\">";
		<?
			for ($i=1; $i<$ArrCount; $i++) {
		?>
		content_html = content_html + "								<td<? if ($i == 5) {?> class=\"last\"<? } ?>><span class=\"signature_state\"><?=str_replace(".","/",substr($to_dateThis[$i],5,5))?><? if ($to_dateThis[$i] != "") { ?><? } ?><?=$to_statusThis[$i]?></span></td>";
		<?
			}

			for ($i=$ArrCount; $i<=5; $i++) {
		?>
		content_html = content_html + "								<td<? if ($i == 5) {?> class=\"last\"<? } ?>></td>";
		<?
			}
		?>
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "					</td>";
		content_html = content_html + "			   </tr>";
		content_html = content_html + "			   <tr>";
		content_html = content_html + "					<th class=\"gray\">��������</th>";
		content_html = content_html + "					<td><?=$form_title?></td>";
		content_html = content_html + "			   </tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�����</th>";
		content_html = content_html + "					<td><?=substr($approval_date,0,4)?>�� <?=substr($approval_date,5,2)?>�� <?=substr($approval_date,8,2)?>��</td>";
		content_html = content_html + "					<th class=\"gray\">��������</th>";
		content_html = content_html + "					<td>";
		<?
			$sql = "SELECT C_PRS_ID, C_PRS_NAME, C_PRS_POSITION FROM DF_APPROVAL_CC WITH(NOLOCK) WHERE DOC_NO = '$doc_no' ORDER BY C_ORDER, C_PRS_POSITION";
			$rs = sqlsrv_query($dbConn, $sql);
	
			$i = 0;
			while ($record = sqlsrv_fetch_array($rs)) {
				$cc_id = $record['C_PRS_ID'];
				$cc_name = $record['C_PRS_NAME'];
				$cc_position = $record['C_PRS_POSITION'];

				if ($cc_id == $prs_id) {
					$sql1 = "UPDATE DF_APPROVAL_CC SET C_READ_YN = 'Y', C_READ_DATE = getdate() WHERE DOC_NO = '$doc_no' AND C_PRS_ID = '$cc_id'";
					$rs1 = sqlsrv_query($dbConn, $sql1);
				}

				if ($i == 0) {
		?>
		content_html = content_html + "						<?=$cc_position?> <?=$cc_name?>";
		<?
				} else {
		?>
		content_html = content_html + "						, <?=$cc_position?> <?=$cc_name?>";
		<?
				}
				$i++;
			}
		?>	

		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�μ�</th>";
		content_html = content_html + "					<td colspan=\"3\"><?=getTeamInfo($writer_team)?></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�̸�</th>";
		content_html = content_html + "					<td><?=$writer_position?> <?=$writer_name?></td>";
		content_html = content_html + "					<th class=\"gray\">��������</th>";
		content_html = content_html + "					<td><? if ($open_yn == "Y") { echo "����"; } else { echo "�����"; } ?></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td colspan=\"3\"><?=$title?></td>";
		content_html = content_html + "				</tr>";

		<? if ($form_category == "���ǰ�Ǽ�(v2)") { ?>

		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">������Ʈ</th>";
		content_html = content_html + "					<td colspan=\"3\">";
			<? if (($id == $prs_id && $payment_yn == "������") || $prf_id == 4) { ?>
		content_html = content_html + "						<select name=\"project_no\" style=\"width:500px;height:30px;\">";
														<?
															$sql = "SELECT 
																	PROJECT_NO, TITLE
																FROM 
																	DF_PROJECT WITH(NOLOCK)
																WHERE
																	USE_YN = 'Y' AND STATUS = 'ING' AND COMPLETE = 'N'
																ORDER BY 
																	PROJECT_NO DESC";
															$rs = sqlsrv_query($dbConn,$sql);

															while ($record = sqlsrv_fetch_array($rs)) {
																$p_no = $record['PROJECT_NO'];
																$p_title= $record['TITLE'];
														?>
		content_html = content_html + "							<option value=\"<?=$p_no?>\"<? if ($project_no == $p_no) { echo ' selected'; } ?>>[<?=$p_no?>] <?=$p_title?></option>";
														<?
															}
														?>
		content_html = content_html + "						</select>";
		content_html = content_html + "						<a href=\"javascript:modifyExpense('project');\"><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
			<? } else { ?>
		content_html = content_html + "					<?=$project?>";
			<? } ?>
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";

			<?
				$sql = "SELECT COUNT(SEQNO) AS TOTAL, SUM(MONEY) AS MONEY FROM DF_PROJECT_EXPENSE_V2 WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND LAST = 'Y'";
				$rs = sqlsrv_query($dbConn, $sql);

				$record=sqlsrv_fetch_array($rs);

				$tot_count = $record['TOTAL'];
				$sum_money = $record['MONEY'];
			?>
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�ѱݾ�</th>";
		content_html = content_html + "					<td colspan=\"3\"><input type=\"text\" name=\"money_total\" id=\"money_total\" value=\"<?=number_format($sum_money,0)?>\" style=\"ime-mode:disabled;width:100px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);\" readonly> �� <span style=\"color:#777777\">(�ڵ��Է�)</span></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<td colspan=\"4\">";

		<?
			$sql = "SELECT 
						IDX, TYPE, MONEY, TAX, TARGET, PAY_TYPE, PAY_INFO, COMPANY, MANAGER, CONTACT, 
						BANK_NAME, BANK_NUM, BANK_USER, PAY_DATE, MEMO
					FROM 
						DF_PROJECT_EXPENSE_V2 WITH(NOLOCK)
					WHERE 
						DOC_NO = '$doc_no' AND LAST = 'Y'
					ORDER BY 
						IDX";
			$rs = sqlsrv_query($dbConn, $sql);

			$expense = 0;
			while ($record = sqlsrv_fetch_array($rs)) {
				$db_type		= $record['TYPE'];		// û��
				$db_moeny		= $record['MONEY'];		// �ѱݾ�
				$db_tax			= $record['TAX'];		// ����
				$db_target		= $record['TARGET'];	// ���
				$db_pay_type	= $record['PAY_TYPE'];	// �������
				$db_pay_info	= $record['PAY_INFO'];	// �������� �߰�
				$db_company		= $record['COMPANY'];	// ��ü��
				$db_manager		= $record['MANAGER'];	// �����
				$db_contact		= $record['CONTACT'];	// ����ó
				$db_bank_name	= $record['BANK_NAME'];	// �����
				$db_bank_num	= $record['BANK_NUM'];	// ���¹�ȣ
				$db_bank_user	= $record['BANK_USER'];	// �����ָ�
				$db_pay_date	= $record['PAY_DATE'];	// ��������
				$db_memo		= $record['MEMO'];		// Ȱ�볻��
				$db_idx			= $record['IDX'];		// IDX

				// ������� ��
				$display['B'] = "style='display:none;'";
				$display['C'] = "style='display:none;'";
				$display['P'] = "style='display:none;'";
				$display['A'] = "style='display:none;'";
				$display['H'] = "style='display:none;'";
				$display[$db_pay_type] = "style='display:;'";

				// �׸� ���ð�								
				$checked1[$db_type]		= "checked";	// û�� ���ð�
				$checked2[$db_tax]		= "checked";	// ���� ���ð�
				$checked3[$db_target]	= "checked";	// ��� ���ð�								
				$checked4[$db_pay_type] = "checked";	// ������� ���ð�
				$checked5[$db_pay_info] = "checked";	// �������� ���ð�
				$checked6[$db_pay_date] = "checked";	// �������� ���ð�

				// ���ǰ�Ǽ� ���� ������ư
				if (($id == $prs_id && $payment_yn == "������") || $prf_id == 4) {
					$disabled = "";
					$readonly = "";

					$btn_modify = "<a href='javascript:modifyExpense($db_idx);'><img src='/img/btn_popup_modify.gif' alt=''></a>";
					$btn_delete = "<a href='javascript:deleteExpense($db_idx);'><img src='/img/btn_popup_delete.gif' alt=''></a>";
					$btn_log	= "<span style='vertical-align:baseline;'><a href='javascript:funExpenseList(\\\"$doc_no\\\",$db_idx);'><u>�����α�</u></a></span>";
				} else {
					$disabled = "disabled";
					$readonly = "readonly";

					$btn_modify = "";
					$btn_delete = "";
					$btn_log	= "";
				}
		?>

		content_html = content_html + "		<table width=\"100%\" id=\"payment_<?=$expense?>\" <? if ($db_moeny == "" && $expense != 0) { ?> style=\"display:none;\"<? } ?>>";
		content_html = content_html + "		<tr <?=$style?>>";
		content_html = content_html + "			<th width=\"50\">�׸�<?=$_n[$db_idx]?><br><br><?=$btn_modify?><br><br><?=$btn_delete?><br><br><?=$btn_log?></th>";
		content_html = content_html + "			<td>";
		content_html = content_html + "				<table width=\"100%\">";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th width=\"100\" class=\"gray\">û��</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$expense?>\" value=\"1\" <? echo $checked1['1']; ?> onChange=\"alertType(this);\" <?=$disabled?>>�Ǻ�û�� ";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$expense?>\" value=\"2\" <? echo $checked1['2']; ?> onChange=\"alertType(this);\" <?=$disabled?>>��û�� ";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$expense?>\" value=\"3\" <? echo $checked1['3']; ?> onChange=\"alertType(this);\" <?=$disabled?>>�";
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">�ݾ�</th>";
		content_html = content_html + "					<td><input type=\"text\" name=\"money_<?=$expense?>\" class=\"money_\" style=\"ime-mode:disabled;width:100px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);sumPayment();\" value=\"<?=number_format($db_moeny,0)?>\" <?=$readoly?>> ��</td>"; 			
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "				<th class=\"gray\">�ΰ���</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$expense?>\" value=\"1\" <? echo $checked2['1']; ?> <?=$disabled?>>�鼼"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$expense?>\" value=\"2\" <? echo $checked2['2']; ?> <?=$disabled?>>�ΰ�������"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$expense?>\" value=\"3\" <? echo $checked2['3']; ?> <?=$disabled?>>�ΰ��� ����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$expense?>\" value=\"4\" <? echo $checked2['4']; ?> <?=$disabled?>>���������� ����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$expense?>\" value=\"5\" <? echo $checked2['5']; ?> <?=$disabled?>>���������� ����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">���</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$expense?>\" value=\"1\" <? echo $checked3['1']; ?> <?=$disabled?>>���ֻ� ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$expense?>\" value=\"2\" <? echo $checked3['2']; ?> <?=$disabled?>>�뿪(��������) ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$expense?>\" value=\"3\" <? echo $checked3['3']; ?> <?=$disabled?>>�̹���,����,���� ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$expense?>\" value=\"4\" <? echo $checked3['4']; ?> <?=$disabled?>>�̺�Ʈ��ǰ  ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$expense?>\" value=\"5\" <? echo $checked3['5']; ?> <?=$disabled?>>����� ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$expense?>\" value=\"6\" <? echo $checked3['6']; ?> <?=$disabled?>>��Ÿ";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\">";
		content_html = content_html + "					<th class=\"gray\">��������</th>";
		content_html = content_html + "					<td colspan=\"3\" style=\"padding-left:1px;\">";
		content_html = content_html + "						<table width=\"100%\">";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">�������</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<span data-no=\"<?=$expense?>\">";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$expense?>\" value=\"B\" <? echo $checked4['B']; ?> <?=$disabled?>>������ü";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$expense?>\" value=\"C\" <? echo $checked4['C']; ?> <?=$disabled?>>ī�����";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$expense?>\" value=\"P\" <? echo $checked4['P']; ?> <?=$disabled?>>���ΰ��";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$expense?>\" value=\"A\" <? echo $checked4['A']; ?> onChange=\"alertPaytype(this);\" <?=$disabled?>>�ڵ���ü";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$expense?>\" value=\"H\" <? echo $checked4['H']; ?> <?=$disabled?>>���ݰ���";
		content_html = content_html + "									</span>";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_B_<?=$expense?>\" <?=$display['B']?>>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">��꼭</td>";
		content_html = content_html + "								<td colspan=\"6\">";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"1\" <? echo $checked5['1']; ?> <?=$disabled?>>���ݰ�꼭 ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"2\" <? echo $checked5['2']; ?> <?=$disabled?>>��꼭 ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"3\" <? echo $checked5['3']; ?> <?=$disabled?>>�������������(���ݿ�����)";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">��ü��</td>";
		content_html = content_html + "								<td colspan=\"6\"><input type=\"text\" style=\"width:150px;\" name=\"company_<?=$expense?>\" maxlength=\"20\" value=\"<?=$db_company?>\"></td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">�����</td>";
		content_html = content_html + "								<td colspan=\"6\"><input type=\"text\" style=\"width:150px;\" name=\"manager_<?=$expense?>\" maxlength=\"10\" value=\"<?=$db_manager?>\"></td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">����ó</td>";
		content_html = content_html + "								<td colspan=\"6\"><input type=\"text\" style=\"width:150px;\" name=\"contact_<?=$expense?>\" maxlength=\"20\" value=\"<?=$db_contact?>\"></td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">��ü��������</td>";
		content_html = content_html + "								<td colspan=\"6\">";
		content_html = content_html + "											<div>����� <input type=\"text\" style=\"width:100px;\" name=\"bank_name_<?=$expense?>\" maxlength=\"10\" value=\"<?=$db_bank_name?>\">&nbsp;&nbsp;";
		content_html = content_html + "											���¹�ȣ <input type=\"text\" style=\"width:150px;\" name=\"bank_num_<?=$expense?>\" maxlength=\"30\" value=\"<?=$db_bank_num?>\"></div>";
		content_html = content_html + "											<div style=\"padding-top:5px;\">������ <input type=\"text\" style=\"width:100px;\" name=\"bank_user_<?=$expense?>\" maxlength=\"10\" value=\"<?=$db_bank_user?>\"></div>";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��������</td>";
		content_html = content_html + "								<td colspan=\"6\">";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_date_<?=$expense?>\" value=\"1\" <? echo $checked6['1']; ?> <?=$disabled?>>���� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_date_<?=$expense?>\" value=\"2\" <? echo $checked6['2']; ?> onChange=\"alertPaydate(this)\" <?=$disabled?>>���� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_date_<?=$expense?>\" value=\"3\" <? echo $checked6['3']; ?> <?=$disabled?>>��Ȱ��� �Ϸ� ���";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_C_<?=$expense?>\" <?=$display['C']?>>";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��������</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"4\" <? echo $checked5['4']; ?> <?=$disabled?>>�¶��� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"5\" <? echo $checked5['5']; ?> <?=$disabled?>>�湮����";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_P_<?=$expense?>\" <?=$display['P']?>>";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��������</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"6\" <? echo $checked5['6']; ?> <?=$disabled?>>����ī�� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"7\" <? echo $checked5['7']; ?> <?=$disabled?>>��������";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_A_<?=$expense?>\" <?=$display['A']?>>";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��ü����</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"9\" <? echo $checked5['9']; ?> <?=$disabled?>>5��"; 
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"10\" <? echo $checked5['10']; ?> <?=$disabled?>>10��"; 
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"11\" <? echo $checked5['11']; ?> <?=$disabled?>>15��"; 
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$expense?>\" value=\"12\" <? echo $checked5['12']; ?> <?=$disabled?>>20��";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_H_<?=$expense?>\" <?=$display['H']?>>";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">����</td>";
		content_html = content_html + "								<td>�濵������ Ȯ�� �� ���� ����</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\" style='border-bottom:0px;'>";
		content_html = content_html + "					<th class=\"gray\">Ȱ�볻��</th>";
		content_html = content_html + "					<td colspan=\"3\"><input type=\"text\" name=\"memo_<?=$expense?>\" value=\"<?=$db_memo?>\" style=\"width:450px;\"></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "			</td>";
		content_html = content_html + "		</tr>";
		content_html = content_html + "		</table>";
		<?		
				$expense = $expense + 1;

				unset($display);

				unset($checked1);
				unset($checked2);
				unset($checked3);
				unset($checked4);
				unset($checked5);
			} 
		?>
		<?
				if (($id == $prs_id && $payment_yn == "������") || $prf_id == 4) {
					for ($i=$expense; $i<5; $i++)
					{
						$btn_modify = "<a href='javascript:modifyExpense($i);'><img src='/img/btn_popup_modify.gif' alt=''></a>";
		?>
		content_html = content_html + "		<table width=\"100%\" id=\"payment_<?=$i?>\" <? if ($i != 0) { ?> style=\"display:none;\"<? } ?>>";
		content_html = content_html + "		<tr <?=$style?>>";
		content_html = content_html + "			<th width=\"50\">�׸�<?=$_n[$i]?><br><br><?=$btn_modify?></th>";
		content_html = content_html + "			<td>";
		content_html = content_html + "				<table width=\"100%\">";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th width=\"100\" class=\"gray\">û��</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$i?>\" value=\"1\" onChange=\"alertType(this);\">�Ǻ�û�� ";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$i?>\" value=\"2\" onChange=\"alertType(this);\">��û�� ";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$i?>\" value=\"3\" onChange=\"alertType(this);\">�";
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">�ݾ�</th>";
		content_html = content_html + "					<td><input type=\"text\" name=\"money_<?=$i?>\" class=\"money_\" style=\"ime-mode:disabled;width:100px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);sumPayment();\"> ��</td>"; 			
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "				<th class=\"gray\">�ΰ���</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$i?>\" value=\"1\">�鼼"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$i?>\" value=\"2\">�ΰ�������"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$i?>\" value=\"3\">�ΰ��� ����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$i?>\" value=\"4\">���������� ����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"tax_<?=$i?>\" value=\"5\">���������� ����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">���</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$i?>\" value=\"1\" >���ֻ� ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$i?>\" value=\"2\" >�뿪(��������) ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$i?>\" value=\"3\" >�̹���,����,���� ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$i?>\" value=\"4\" >�̺�Ʈ��ǰ  ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$i?>\" value=\"5\" >����� ";
		content_html = content_html + "						<input type=\"radio\" name=\"target_<?=$i?>\" value=\"6\" >��Ÿ";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\">";
		content_html = content_html + "					<th class=\"gray\">��������</th>";
		content_html = content_html + "					<td colspan=\"3\" style=\"padding-left:1px;\">";
		content_html = content_html + "						<table width=\"100%\">";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">�������</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<span data-no=\"<?=$i?>\">";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$i?>\" value=\"B\" checked >������ü";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$i?>\" value=\"C\" >ī�����";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$i?>\" value=\"P\" >���ΰ��";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$i?>\" value=\"A\" rtPaytype(this);\" <?=$disabled?>>�ڵ���ü";
		content_html = content_html + "										<input type=\"radio\" class=\"pay_type\" name=\"pay_type_<?=$i?>\" value=\"H\" >���ݰ���";
		content_html = content_html + "									</span>";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_B_<?=$i?>\">";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">��꼭</td>";
		content_html = content_html + "								<td colspan=\"6\">";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"1\" >���ݰ�꼭 ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"2\" >��꼭 ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"3\" >�������������(���ݿ�����)";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">��ü��</td>";
		content_html = content_html + "								<td colspan=\"6\"><input type=\"text\" style=\"width:150px;\" name=\"company_<?=$i?>\" maxlength=\"20\" ></td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">�����</td>";
		content_html = content_html + "								<td colspan=\"6\"><input type=\"text\" style=\"width:150px;\" name=\"manager_<?=$i?>\" maxlength=\"10\" ></td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">����ó</td>";
		content_html = content_html + "								<td colspan=\"6\"><input type=\"text\" style=\"width:150px;\" name=\"contact_<?=$i?>\" maxlength=\"20\" ></td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr>";
		content_html = content_html + "								<td class=\"gray2\">��ü��������</td>";
		content_html = content_html + "								<td colspan=\"6\">";
		content_html = content_html + "											<div>����� <input type=\"text\" style=\"width:100px;\" name=\"bank_name_<?=$i?>\" maxlength=\"10\" \">&nbsp;&nbsp;";
		content_html = content_html + "											���¹�ȣ <input type=\"text\" style=\"width:150px;\" name=\"bank_num_<?=$i?>\" ></div>";
		content_html = content_html + "											<div style=\"padding-top:5px;\">������ <input type=\"text\" style=\"width:100px;\" name=\"bank_user_<?=$i?>\" maxlength=\"10\"</div>";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��������</td>";
		content_html = content_html + "								<td colspan=\"6\">";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_date_<?=$i?>\" value=\"1\" >���� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_date_<?=$i?>\" value=\"2\" onChange=\"alertPaydate(this)\" >���� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_date_<?=$i?>\" value=\"3\" >��Ȱ��� �Ϸ� ���";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_C_<?=$i?>\" style=\"display:none;\">";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��������</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"4\" >�¶��� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"5\" >�湮����";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_P_<?=$i?>\" style=\"display:none;\">";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��������</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"6\" >����ī�� ";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"7\" >��������";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_A_<?=$i?>\" style=\"display:none;\">";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">��ü����</td>";
		content_html = content_html + "								<td>";
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"8\" >5��"; 
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"9\" >10��"; 
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"10\" >15��"; 
		content_html = content_html + "									<input type=\"radio\" name=\"pay_info_<?=$i?>\" value=\"11\" >20��";
		content_html = content_html + "								</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "						<table width=\"100%\" id=\"paytype_H_<?=$i?>\" style=\"display:none;\">";
		content_html = content_html + "							<tr style=\"border-bottom:0px;\">";
		content_html = content_html + "								<td class=\"gray2\">����</td>";
		content_html = content_html + "								<td>�濵������ Ȯ�� �� ���� ����</td>";
		content_html = content_html + "							</tr>";
		content_html = content_html + "						</table>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\" style='border-bottom:0px;'>";
		content_html = content_html + "					<th class=\"gray\">Ȱ�볻��</th>";
		content_html = content_html + "					<td colspan=\"3\"><input type=\"text\" name=\"memo_<?=$i?>\" style=\"width:450px;\"></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "			</td>";
		content_html = content_html + "		</tr>";
		content_html = content_html + "		</table>";

		<?
					}
				}
		?>

		content_html = content_html + "		<table width=\"100%\">";
		content_html = content_html + "		<tr style=\"border:0px;\">";
		content_html = content_html + "			<td>";
		content_html = content_html + "				<a href=\"javascript:addPayment();\" class=\"btn_plus\">�������� �߰� &nbsp;<img src=\"../img/btn_plus.jpg\" alt=\"�߰��ϱ�\"><br><br></a>";
		content_html = content_html + "			</td>";
		content_html = content_html + "		</tr>";
		content_html = content_html + "		</table>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";

		<? } else if ($form_category == "�Ի���ΰ�") { ?>

		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">ä��μ�</th>";
		content_html = content_html + "					<td colspan=\"3\">";
			<? if ($id == $prs_id || $prf_id == 4) { ?>
		content_html = content_html + "						<select name= \"team\" style=\"width:250px;height:30px;\">";
		content_html = content_html + "							<option value=\"\"></option>";
														<?
															$sql = "SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
															$rs = sqlsrv_query($dbConn,$sql);

															while ($record = sqlsrv_fetch_array($rs)) {
																$p_team = $record['TEAM'];
														?>
		content_html = content_html + "							<option value=\"<?=$p_team?>\"<? if ($team_name == $p_team) { echo ' selected'; } ?>><?=$p_team?></option>";
														<?
															}
														?>
		content_html = content_html + "						</select>";
		content_html = content_html + "						<a href=\"javascript:modifyExpansion('team_name');\"><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
			<? } else { ?>
		content_html = content_html + "					<?=$team_name?>";
			<? } ?>
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<td colspan=\"4\">";

		<?
			$sql = "SELECT 
						IDX, TYPE, DATA1, DATA2, DATA3, DATA4, DATA5, DATA6, DATA7, DATA8, DATA9, DATA10, DATA11
					FROM 
						DF_APPROVAL_EXPANSION WITH(NOLOCK)
					WHERE 
						DOC_NO = '$doc_no' AND LAST = 'Y'
					ORDER BY 
						IDX";
			$rs = sqlsrv_query($dbConn, $sql);

			$expansion = 0;
			while ($record = sqlsrv_fetch_array($rs)) {
				$db_type = $record['TYPE'];			// û��
				$db_idx	 = trim($record['IDX']);	// IDX
					
				// ������
				if ($db_type == "A") {
					$db_name1		= trim($record['DATA1']);				// ����
					$db_cause1		= trim($record['DATA2']);				// ä�����
					$db_career		= trim($record['DATA3']);				// ��±���
					$db_birth		= trim($record['DATA4']);				// �������
					$db_school		= trim($record['DATA5']);				// �����б�
					$db_major		= trim($record['DATA6']);				// ����
					$db_career_arr	= explode("-", trim($record['DATA7']));	// ��±Ⱓ
					$db_career_y	= trim($db_career_arr[0]);	
					$db_career_m	= trim($db_career_arr[1]);	
					$db_position	= trim($record['DATA8']);				// ����
					$db_rating		= trim($record['DATA9']);				// ȣ��
					$db_reader		= trim($record['DATA10']);				// ��å
					$db_join_arr	= explode("-", trim($record['DATA11']));// �Ի翹����
					$db_join_y		= trim($db_join_arr[0]);
					$db_join_m		= trim($db_join_arr[1]);
					$db_join_d		= trim($db_join_arr[2]);
					$db_name2		= null;
					$db_cause2		= null;
					$db_gubun		= null;
					$db_relay		= null;
					$db_salary_h	= null;
					$db_salary_m	= null;
					$db_period1_y	= null;
					$db_period1_m	= null;
					$db_period1_d	= null;
					$db_period2_y	= null;
					$db_period2_m	= null;
					$db_period2_d	= null;
					$db_memo		= null;

					// �׸� ���ð�								
					$checked1[$db_type]		= "checked";	// ä�뱸��
					$checked2[$db_career]	= "checked";	// ��±���
					$checked3[$db_position]	= "checked";	// ����								
					$checked4[$db_rating]	= "checked";	// ȣ��
					$checked5[$db_reader]	= "checked";	// ��å
				// �����
				} else if ($db_type == "B")	{
					$db_name2		= trim($record['DATA1']);				// ����
					$db_cause2		= trim($record['DATA2']);				// ä�����
					$db_gubun		= trim($record['DATA3']);				// ����
					$db_relay		= trim($record['DATA4']);				// �߰���ü
					$db_salary_h	= @number_format(trim($record['DATA5']));// �ñ�
					$db_salary_m	= @number_format(trim($record['DATA6']));// ����
					$db_period1_arr	= explode("-", trim($record['DATA7']));	// �Ⱓ1
					$db_period1_y	= $db_period1_arr[0];	
					$db_period1_m	= $db_period1_arr[1];	
					$db_period1_d	= $db_period1_arr[2];	
					$db_period2_arr	= explode("-", trim($record['DATA8']));	// �Ⱓ2
					$db_period2_y	= $db_period2_arr[0];	
					$db_period2_m	= $db_period2_arr[1];	
					$db_period2_d	= $db_period2_arr[2];	
					$db_memo		= trim($record['DATA9']);				// ��Ÿ
					$db_name1		= null;
					$db_cause1		= null;
					$db_career		= null;
					$db_birth		= null;
					$db_school		= null;
					$db_major		= null;
					$db_career_y	= null;
					$db_career_m	= null;
					$db_position	= null;
					$db_rating		= null;
					$db_reader		= null;
					$db_join_y		= null;
					$db_join_m		= null;
					$db_join_d		= null;

					// �׸� ���ð�								
					$checked1[$db_type]		= "checked";	// ä�뱸��
					$checked2[$db_gubun]	= "checked";	// ����
				}

				// ä�뱸�� ��
				$display['A'] = "style='display:none;'";
				$display['B'] = "style='display:none;'";
				$display[$db_type] = "style='display:;'";

				// �Ի���ΰ� ���� ������ư
				if ($id == $prs_id || $prf_id == 4) {
					$disabled = "";
					$readonly = "";

					$btn_modify = "<a href='javascript:modifyExpansion($db_idx);'><img src='/img/btn_popup_modify.gif' alt=''></a>";
					$btn_delete = "<a href='javascript:deleteExpansion($db_idx);'><img src='/img/btn_popup_delete.gif' alt=''></a>";
				} else {
					$disabled = "disabled";
					$readonly = "readonly";

					$btn_modify = "";
					$btn_delete = "";
				}
		?>

		content_html = content_html + "		<table width=\"100%\" id=\"employ_<?=$expansion?>\" <? if ($db_type == "" && $expansion != 0) { ?> style=\"display:none;\"<? } ?>>";
		content_html = content_html + "		<tr <?=$style?>>";
		content_html = content_html + "			<th width=\"50\">���<?=$_n[$db_idx]?><br><br><?=$btn_modify?><br><br><?=$btn_delete?></th>";
		content_html = content_html + "			<td>";
		content_html = content_html + "				<table width=\"100%\">";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th width=\"100\" class=\"gray\">ä�뱸��</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<span data-no=\"<?=$expansion?>\">";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$expansion?>\" class=\"employ_type\" value=\"A\" <? echo $checked1['A']; ?> onChange=\"alertType(this);\" <?=$disabled?>>������ ";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$expansion?>\" class=\"employ_type\" value=\"B\" <? echo $checked1['B']; ?> onChange=\"alertType(this);\" <?=$disabled?>>����� ";
		content_html = content_html + "						</span>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "				<table width=\"100%\" id=\"employtype_A_<?=$expansion?>\" <?=$display['A']?>>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "				<th class=\"gray\">ä�����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"cause1_<?=$expansion?>\" style=\"width:95%;\" value=\"<?=$db_cause1?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">��±���</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"career_<?=$expansion?>\" value=\"1\" <? echo $checked2['1']; ?> <?=$disabled?>>����";
		content_html = content_html + "						<input type=\"radio\" name=\"career_<?=$expansion?>\" value=\"2\" <? echo $checked2['2']; ?> <?=$disabled?>>���";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"name1_<?=$expansion?>\" maxlength=\"30\" style=\"width:90%;\" value=\"<?=$db_name1?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">�������</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"birth_<?=$expansion?>\" maxlength=\"30\" style=\"width:90%;\" value=\"<?=$db_birth?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�����б�</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"school_<?=$expansion?>\" maxlength=\"30\" style=\"width:90%;\" value=\"<?=$db_school?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"major_<?=$expansion?>\" maxlength=\"30\" style=\"width:90%;\" value=\"<?=$db_major?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th width=\"100\" class=\"gray\">�� ��±Ⱓ</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"career_y_<?=$expansion?>\" maxlength=\"3\" style=\"width:30px;\" value=\"<?=$db_career_y?>\" <?=$readonly?>>��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"career_m_<?=$expansion?>\" maxlength=\"2\" style=\"width:30px;\" value=\"<?=$db_career_m?>\" <?=$readonly?>>��";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">������ ����</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$expansion?>\" value=\"1\" <? echo $checked3['1']; ?> <?=$disabled?>>���"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$expansion?>\" value=\"2\" <? echo $checked3['2']; ?> <?=$disabled?>>����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$expansion?>\" value=\"3\" <? echo $checked3['3']; ?> <?=$disabled?>>�븮<br>"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$expansion?>\" value=\"4\" <? echo $checked3['4']; ?> <?=$disabled?>>����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$expansion?>\" value=\"5\" <? echo $checked3['5']; ?> <?=$disabled?>>����"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">������ ȣ��</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$expansion?>\" value=\"1\" <? echo $checked4['1']; ?> <?=$disabled?>>1ȣ��"; 
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$expansion?>\" value=\"2\" <? echo $checked4['2']; ?> <?=$disabled?>>2ȣ��"; 
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$expansion?>\" value=\"3\" <? echo $checked4['3']; ?> <?=$disabled?>>3ȣ��<br>"; 
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$expansion?>\" value=\"4\" <? echo $checked4['4']; ?> <?=$disabled?>>4ȣ��"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">��å</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"reader_<?=$expansion?>\" value=\"1\" <? echo $checked5['1']; ?> <?=$disabled?>>����";
		content_html = content_html + "						<input type=\"radio\" name=\"reader_<?=$expansion?>\" value=\"2\" <? echo $checked5['2']; ?> <?=$disabled?>>����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\" style='border-bottom:0px;'>";
		content_html = content_html + "					<th class=\"gray\">�Ի翹����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"join_y_<?=$expansion?>\" maxlength=\"4\" style=\"width:40px;\" value=\"<?=$db_join_y?>\" <?=$readonly?>>��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"join_m_<?=$expansion?>\" maxlength=\"2\" style=\"width:30px;\" value=\"<?=$db_join_m?>\" <?=$readonly?>>��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"join_d_<?=$expansion?>\" maxlength=\"2\" style=\"width:30px;\" value=\"<?=$db_join_d?>\" <?=$readonly?>>��";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "				<table width=\"100%\" id=\"employtype_B_<?=$expansion?>\" <?=$display['B']?>>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$expansion?>\" value=\"1\" <? echo $checked2['1']; ?> <?=$disabled?>>��ǽ� ����&nbsp;";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$expansion?>\" value=\"2\" <? echo $checked2['2']; ?> <?=$disabled?>>�ܱ�����(3�����̸�)&nbsp;";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$expansion?>\" value=\"3\" <? echo $checked2['3']; ?> <?=$disabled?>>�������<br>";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$expansion?>\" value=\"4\" <? echo $checked2['4']; ?> <?=$disabled?>>������ٹ� �� ������ ����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "				<th class=\"gray\">ä�����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"cause2_<?=$expansion?>\" style=\"width:95%;\" value=\"<?=$db_cause2?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"name2_<?=$expansion?>\" maxlength=\"30\" style=\"width:175px;\" value=\"<?=$db_name2?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�߰���ü</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"relay_<?=$expansion?>\" maxlength=\"30\" style=\"width:175px;\" value=\"<?=$db_relay?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�޿�</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						�ñ� <input type=\"text\" name=\"salary_h_<?=$expansion?>\" maxlength=\"10\" style=\"ime-mode:disabled;width:50px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);\" value=\"<?=$db_salary_h?>\" <?=$readonly?>>&nbsp;";
		content_html = content_html + "						���� <input type=\"text\" name=\"salary_m_<?=$expansion?>\" maxlength=\"10\" style=\"ime-mode:disabled;width:70px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);\" value=\"<?=$db_salary_m?>\" <?=$readonly?>>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�Ⱓ</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"period1_y_<?=$expansion?>\" maxlength=\"4\" style=\"width:40px;\" value=\"<?=$db_period1_y?>\" <?=$readonly?>>��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period1_m_<?=$expansion?>\" maxlength=\"2\" style=\"width:30px;\" value=\"<?=$db_period1_m?>\" <?=$readonly?>>��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period1_d_<?=$expansion?>\" maxlength=\"2\" style=\"width:30px;\" value=\"<?=$db_period1_d?>\" <?=$readonly?>>�� ����&nbsp;&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period2_y_<?=$expansion?>\" maxlength=\"4\" style=\"width:40px;\" value=\"<?=$db_period2_y?>\" <?=$readonly?>>��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period2_m_<?=$expansion?>\" maxlength=\"2\" style=\"width:30px;\" value=\"<?=$db_period2_m?>\" <?=$readonly?>>��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period2_d_<?=$expansion?>\" maxlength=\"2\" style=\"width:30px;\" value=\"<?=$db_period2_d?>\" <?=$readonly?>>�� ����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\" style='border-bottom:0px;'>";
		content_html = content_html + "				<th class=\"gray\">��Ÿ</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"memo_<?=$expansion?>\" maxlength=\"30\" style=\"width:95%;\" value=\"<?=$db_memo?>\" <?=$readonly?>>"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "			</td>";
		content_html = content_html + "		</tr>";
		content_html = content_html + "		</table>";
		<?		
				$expansion = $expansion + 1;

				unset($display);

				unset($checked1);
				unset($checked2);
				unset($checked3);
				unset($checked4);
				unset($checked5);
			} 
		?>
		<?
				if ($id == $prs_id || $prf_id == 4) {
					for ($i=$expansion; $i<5; $i++)
					{
						$btn_modify = "<a href='javascript:modifyExpansion($i);'><img src='/img/btn_popup_modify.gif' alt=''></a>";
		?>
		content_html = content_html + "		<table width=\"100%\" id=\"employ_<?=$i?>\" <? if ($i != 0) { ?> style=\"display:none;\"<? } ?>>";
		content_html = content_html + "		<tr <?=$style?>>";
		content_html = content_html + "			<th width=\"50\">���<?=$_n[$i]?><br><br><?=$btn_modify?></th>";
		content_html = content_html + "			<td>";
		content_html = content_html + "				<table width=\"100%\">";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th width=\"100\" class=\"gray\">ä�뱸��</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<span data-no=\"<?=$i?>\">";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$i?>\" class=\"employ_type\" value=\"A\" checked>������ ";
		content_html = content_html + "						<input type=\"radio\" name=\"type_<?=$i?>\" class=\"employ_type\" value=\"B\">����� ";
		content_html = content_html + "						</span>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "				<table width=\"100%\" id=\"employtype_A_<?=$i?>\">";
		content_html = content_html + "				<tr>";
		content_html = content_html + "				<th class=\"gray\">ä�����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"cause1_<?=$i?>\" style=\"width:95%;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">��±���</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"career_<?=$i?>\" value=\"1\">����";
		content_html = content_html + "						<input type=\"radio\" name=\"career_<?=$i?>\" value=\"2\">���";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"name1_<?=$i?>\" maxlength=\"30\" style=\"width:90%;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">�������</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"birth_<?=$i?>\" maxlength=\"30\" style=\"width:90%;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�����б�</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"school_<?=$i?>\" maxlength=\"30\" style=\"width:90%;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"text\" name=\"major_<?=$i?>\" maxlength=\"30\" style=\"width:90%;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th width=\"100\" class=\"gray\">�� ��±Ⱓ</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"career_y_<?=$i?>\" maxlength=\"3\" style=\"width:30px;\">��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"career_m_<?=$i?>\" maxlength=\"2\" style=\"width:30px;\">��";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">������ ����</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$i?>\" value=\"1\">���"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$i?>\" value=\"2\">����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$i?>\" value=\"3\">�븮<br>"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$i?>\" value=\"4\">����"; 
		content_html = content_html + "						<input type=\"radio\" name=\"position_<?=$i?>\" value=\"5\">����"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "					<th class=\"gray\">������ ȣ��</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$i?>\" value=\"1\">1ȣ��"; 
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$i?>\" value=\"2\">2ȣ��"; 
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$i?>\" value=\"3\">3ȣ��<br>"; 
		content_html = content_html + "						<input type=\"radio\" name=\"rating_<?=$i?>\" value=\"4\">4ȣ��"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">��å</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"reader_<?=$i?>\" value=\"1\">����";
		content_html = content_html + "						<input type=\"radio\" name=\"reader_<?=$i?>\" value=\"2\">����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\" style='border-bottom:0px;'>";
		content_html = content_html + "					<th class=\"gray\">�Ի翹����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"join_y_<?=$i?>\" maxlength=\"4\" style=\"width:40px;\">��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"join_m_<?=$i?>\" maxlength=\"2\" style=\"width:30px;\">��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"join_d_<?=$i?>\" maxlength=\"2\" style=\"width:30px;\">��";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "				<table width=\"100%\" id=\"employtype_B_<?=$i?>\" style=\"display:none;\">";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$i?>\" value=\"1\">��ǽ� ����&nbsp;";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$i?>\" value=\"2\">�ܱ�����(3�����̸�)&nbsp;";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$i?>\" value=\"3\">�������<br>";
		content_html = content_html + "						<input type=\"radio\" name=\"gubun_<?=$i?>\" value=\"4\">������ٹ� �� ������ ����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "				<th class=\"gray\">ä�����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"cause2_<?=$i?>\" style=\"width:95%;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">����</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"name2_<?=$i?>\" maxlength=\"30\" style=\"width:175px;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�߰���ü</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"relay_<?=$i?>\" maxlength=\"30\" style=\"width:175px;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�޿�</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						�ñ� <input type=\"text\" name=\"salary_h_<?=$i?>\" maxlength=\"10\" style=\"ime-mode:disabled;width:50px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);\">&nbsp;";
		content_html = content_html + "						���� <input type=\"text\" name=\"salary_m_<?=$i?>\" maxlength=\"10\" style=\"ime-mode:disabled;width:70px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);\">";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�Ⱓ</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"period1_y_<?=$i?>\" maxlength=\"4\" style=\"width:40px;\">��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period1_m_<?=$i?>\" maxlength=\"2\" style=\"width:30px;\">��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period1_d_<?=$i?>\" maxlength=\"2\" style=\"width:30px;\">�� ����&nbsp;&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period2_y_<?=$i?>\" maxlength=\"4\" style=\"width:40px;\">��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period2_m_<?=$i?>\" maxlength=\"2\" style=\"width:30px;\">��&nbsp;";
		content_html = content_html + "						<input type=\"text\" name=\"period2_d_<?=$i?>\" maxlength=\"2\" style=\"width:30px;\">�� ����";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr class=\"last\" style='border-bottom:0px;'>";
		content_html = content_html + "				<th class=\"gray\">��Ÿ</th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<input type=\"text\" name=\"memo_<?=$i?>\" maxlength=\"30\" style=\"width:95%;\">"; 
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				</table>";
		content_html = content_html + "			</td>";
		content_html = content_html + "		</tr>";
		content_html = content_html + "		</table>";

		<?
					}
				}
		?>

		content_html = content_html + "		<table width=\"100%\">";
		content_html = content_html + "		<tr style=\"border:0px;\">";
		content_html = content_html + "			<td>";
		content_html = content_html + "				<a href=\"javascript:addEmploy();\" class=\"btn_plus\">�Ի����� �߰� &nbsp;<img src=\"../img/btn_plus.jpg\" alt=\"�߰��ϱ�\"><br><br></a>";
		content_html = content_html + "			</td>";
		content_html = content_html + "		</tr>";
		content_html = content_html + "		</table>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";

		<? } else { ?>
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">�Ⱓ</th>";
		content_html = content_html + "					<td colspan=\"3\"><?=$vacation?></td>";
		content_html = content_html + "				</tr>";
		<? } ?>

		<? if ($form_category == "�ٰܱ�/�İ߰�" || $form_category == "�����") { ?>
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">������</th>";
		content_html = content_html + "					<td colspan=\"3\">";
			<?
				$sql = "SELECT P_PRS_ID, P_PRS_NAME, P_PRS_POSITION FROM DF_APPROVAL_PARTNER WITH(NOLOCK) WHERE DOC_NO = '$doc_no' ORDER BY P_ORDER";
				$rs = sqlsrv_query($dbConn, $sql);

				$i = 0;
				while ($record = sqlsrv_fetch_array($rs)) {
					$partner_id = $record['P_PRS_ID'];
					$partner_name = $record['P_PRS_NAME'];
					$partner_position = $record['P_PRS_POSITION'];

					if ($partner_id == $prs_id) {
						$sql1 = "UPDATE DF_APPROVAL_PARTNER SET P_READ_YN = 'Y', P_READ_DATE = getdate() WHERE DOC_NO = '$doc_no' AND P_PRS_ID = '$partner_id'";
						$rs1 = sqlsrv_query($dbConn, $sql1);
					}

					if ($i == 0) {
			?>
		content_html = content_html + "<?=$partner_position?> <?=$partner_name?>";					
			<?
					} else {
			?>
		content_html = content_html + ", <?=$partner_position?> <?=$partner_name?>";					
			<?
					}
					
					$i++;
				}
			?>
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		<? } ?>
		
		content_html = content_html + "		<tr><td colspan=\"4\"><div class=\"editor-txt\"><?=$contents?></div></td></tr>";
		content_html = content_html + "			</tbody>";
		content_html = content_html + "		</table>";
		content_html = content_html + "		<div class=\"editor-txt\"></div>";
		content_html = content_html + "		<table class=\"editor-table2\" width=\"100%\">";
		content_html = content_html + "			<colgroup>";
		content_html = content_html + "				<col width=\"10%\" />";
		content_html = content_html + "				<col width=\"90%\" />";
		content_html = content_html + "			</colgroup>";
		content_html = content_html + "			<tbody class=\"\">";

		<?
			if ($file1 != "") {
		?>
		content_html = content_html + "				<tr class=\"attach-file<? if ($file2 == "" && $file3 == "") { ?> last<? } ?>\">";
		content_html = content_html + "					<th>÷������</th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<div class=\"txt-idea\">";
		content_html = content_html + "							<span class=\"\"><?=$file1?></span>";
		content_html = content_html + "						</div>";
		content_html = content_html + "						<div class=\"btn-idea\">";
		content_html = content_html + "							<a href=\"javascript:file_download('approval',1,'<?=$doc_no?>');\"><img src=\"/img/btn_download.gif\" alt=\"\"></a>";
		content_html = content_html + "						</div>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		<?
			}

			if ($file2 != "") {
		?>
		content_html = content_html + "				<tr class=\"attach-file<? if ($file3 == "") { ?> last<? } ?>\">";
		content_html = content_html + "					<th><? if ($file1 == "") { ?>÷������<? } ?></th>";
		content_html = content_html + "					<td>";
		content_html = content_html + "						<div class=\"txt-idea\">";
		content_html = content_html + "							<span class=\"\"><?=$file2?></span>";
		content_html = content_html + "						</div>";
		content_html = content_html + "						<div class=\"btn-idea\">";
		content_html = content_html + "							<a href=\"javascript:file_download('approval',2,'<?=$doc_no?>');\"><img src=\"/img/btn_download.gif\" alt=\"\"></a>";
		content_html = content_html + "						</div>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		<?
			}

			if ($file3 != ""){
		?>
		content_html = content_html + "				<tr class=\"attach-file last\">";
		content_html = content_html + "					<th><? if ($file1 == "" && $file2 == "") { ?>÷������<? } ?></th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<div class=\"txt-idea\">";
		content_html = content_html + "							<span class=\"\"><?=$file3?></span>";
		content_html = content_html + "						</div>";
		content_html = content_html + "						<div class=\"btn-idea\">";
		content_html = content_html + "							<a href=\"javascript:file_download('approval',3,'<?=$doc_no?>');\"><img src=\"/img/btn_download.gif\" alt=\"\"></a>";
		content_html = content_html + "						</div>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		<?
			}
		?>

		<? if ($status != "�ӽ�") { ?>
			<?
				$sql = "SELECT COUNT(R_SEQNO) FROM DF_APPROVAL_REPLY WITH(NOLOCK) WHERE DOC_NO = '$doc_no'";
				$rs = sqlsrv_query($dbConn, $sql);

				$record = sqlsrv_fetch_array($rs);
				$total_reply = $record[0];

				$sql = "SELECT 
							R_SEQNO, R_PRS_ID, R_PRS_NAME, R_PRS_POSITION, CONVERT(char(10),R_REG_DATE,102) AS R_REG_DATE, R_CONTENTS
						FROM 
							DF_APPROVAL_REPLY WITH(NOLOCK)
						WHERE 
							DOC_NO = '$doc_no'
						ORDER BY 
							R_SEQNO DESC";
				$rs = sqlsrv_query($dbConn, $sql);

				$reply = 1;
				while ($record=sqlsrv_fetch_array($rs)) {
					$reply_no		= $record['R_SEQNO'];
					$reply_id		= $record['R_PRS_ID'];
					$reply_name		= $record['R_PRS_NAME'];
					$reply_position = $record['R_PRS_POSITION'];
					$reply_contents = $record['R_CONTENTS'];
					$reply_date		= $record['R_REG_DATE'];
		?>

		content_html = content_html + "				<tr class=\"idea-view<? if ($reply == $total_reply) { ?> last<? } ?>\">";
		content_html = content_html + "					<th><? if ($reply == 1) { ?>�ǰߺ���<? } ?></th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<div class=\"txt-idea\">";
		content_html = content_html + "							<span class=\"\" id=\"c_text_<?=$reply_no?>\"><?=str_replace("\r\n","<br>",$reply_contents);?></span>";
		content_html = content_html + "							<span class=\"floatr\" style=\"color:gray\"><?=$reply_date?> <?=$reply_position?> <?=$reply_name?></span>";
		content_html = content_html + "						</div>";
		content_html = content_html + "						<div class=\"btn-idea\">";
															<? if ($reply_id == $prs_id) { ?>
		content_html = content_html + "							<a href=\"javascript:mod_Reply('<?=$reply_no?>');\"><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
		content_html = content_html + "							<a href=\"javascript:delReply(<?=$reply_no?>);\" class=\"floatr\"><img src=\"/img/btn_popup_delete.gif\" alt=\"\"></a>";
															<? } else if ($prf_id == "4") { ?>
		content_html = content_html + "							<a href=\"javascript:delReply(<?=$reply_no?>);\" class=\"floatr\"><img src=\"/img/btn_popup_delete.gif\" alt=\"\"></a>";
																	<? } ?>
		content_html = content_html + "						</div>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
			<?
					$reply = $reply + 1;
				}
			?>

			<? if ($payment_yn == "������" || ($payment_yn == "����" && $prf_id == 4)) { ?>
		content_html = content_html + "				<tr class=\"idea-add\">";
		content_html = content_html + "					<th>�ǰߵ��</th>";
		content_html = content_html + "					<td colspan=\"3\"><input name=\"reply_contents\" id=\"w_comment\" onkeyup=\"textcounter(this.form.reply_contents, this.form.remlen,200);\" onkeydown=\"textcounter(this.form.reply_contents, this.form.remlen,200);\">";
		content_html = content_html + "						<input type=\"hidden\" readonly name=\"remlen\" size=\"3\" maxlength=\"3\" value=\"200\">";
		content_html = content_html + "						<div id=\"reply_btn\"><a href=\"javascript:writeReply();\"><img src=\"/img/btn_insert.gif\" alt=\"\"></a></div>";
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
			<? } ?>

		<? } ?>
		content_html = content_html + "			</tbody>";
		content_html = content_html + "		</table>";
		content_html = content_html + "		</form>";

		$("#pop_detail_title",top.document).text("<?=$form_title?>");
		$("#pop_detail_log",top.document).html(log_html);
		$("#pop_detail_content",top.document).html(content_html);
		$("#pop_detail_modify",top.document).html(modify_html);
		$("#popDetail",top.document).attr("style","display:inline");

		$("#popStatusDesc",top.document).attr("style","display:none");
		$("#popApproval",top.document).attr("style","display:none");
		$("#popLog",top.document).attr("style","display:none");
		$("#popDel",top.document).attr("style","display:none");
		$("#popReWrite",top.document).attr("style","display:none");
	});
</script>
</head>
<body>
</body>
</html>
