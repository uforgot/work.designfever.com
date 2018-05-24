<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>
<link rel="stylesheet" href="/assets/css/common.css" />
<link rel="stylesheet" href="/assets/css/jquery-ui.css" />
<link rel="stylesheet" href="/assets/css/style_20180406.css" />
<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 

	if ($doc_no == "")
	{
?>
<script type="text/javascript">
	alert("해당 문서가 존재하지 않습니다.");
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
				CONVERT(char(10),START_DATE,102) AS START_DATE, CONVERT(char(10),END_DATE,102) AS END_DATE, USE_DAY, FORM_CATEGORY, FORM_TITLE, FILE_1, FILE_2, FILE_3, PROJECT_NO  
			FROM 
				DF_APPROVAL WITH(NOLOCK)
			WHERE
				DOC_NO = '$doc_no' AND USE_YN = 'Y'
			ORDER BY 
				SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($category == "휴가계")
	{
		if ($count == 2)
		{
			$vacation = "";
			$v = 0;
			while ($record = sqlsrv_fetch_array($rs))
			{
				$form_title = $record['FORM_TITLE'];
				$title = $record['TITLE'];
				$contents = $record['CONTENTS'];
				$approval_date = $record['APPROVAL_DATE'];
				$id = $record['PRS_ID'];
				$team = $record['PRS_TEAM'];
				$position = $record['PRS_POSITION'];
				$name = $record['PRS_NAME'];
				$status = $record['STATUS'];
				$payment_yn = $record['PAYMENT_YN'];
				$open_yn = $record['OPEN_YN'];
				$start_date = $record['START_DATE'];
				$end_date = $record['END_DATE'];
				$use_day = $record['USE_DAY'];
				$form_category = $record['FORM_CATEGORY'];
				$form_title = $record['FORM_TITLE'];
				$file1 = $record['FILE_1'];
				$file2 = $record['FILE_2'];
				$file3 = $record['FILE_3'];

				if ($form_title == '연차') 
				{
					$vacation .= "연차 휴가&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ". $start_date ." - ". $end_date ." (". floatval($use_day) ."일)<br>";
				}
				else if ($form_title == '프로젝트')
				{
					$vacation .= "프로젝트 휴가 : ". $start_date ." - ". $end_date ." (". floatval($use_day) ."일)<br>";
				}
				
				$v++;
			}
			$form_title = "연프";
			$writer_team = $team;
			$writer_position = $position;
			$writer_name = $name;
		}
		else
		{
			$record = sqlsrv_fetch_array($rs);

			$title = $record['TITLE'];
			$contents = $record['CONTENTS'];
			$approval_date = $record['APPROVAL_DATE'];
			$id = $record['PRS_ID'];
			$team = $record['PRS_TEAM'];
			$position = $record['PRS_POSITION'];
			$name = $record['PRS_NAME'];
			$status = $record['STATUS'];
			$payment_yn = $record['PAYMENT_YN'];
			$open_yn = $record['OPEN_YN'];
			$start_date = $record['START_DATE'];
			$end_date = $record['END_DATE'];
			$use_day = $record['USE_DAY'];
			$form_category = $record['FORM_CATEGORY'];
			$form_title = $record['FORM_TITLE'];
			$file1 = $record['FILE_1'];
			$file2 = $record['FILE_2'];
			$file3 = $record['FILE_3'];

			$vacation .= $start_date ." - ". $end_date ." (". floatval($use_day) ."일)";

			$writer_team = $team;
			$writer_position = $position;
			$writer_name = $name;
		}
	}
	else
	{
		if ($count > 1)
		{
			while ($record = sqlsrv_fetch_array($rs))
			{
				$title = $record['TITLE'];
				$contents = $record['CONTENTS'];
				$approval_date = $record['APPROVAL_DATE'];
				$id = $record['PRS_ID'];
				$team = $record['PRS_TEAM'];
				$position = $record['PRS_POSITION'];
				$name = $record['PRS_NAME'];
				$status = $record['STATUS'];
				$payment_yn = $record['PAYMENT_YN'];
				$open_yn = $record['OPEN_YN'];
				$start_date = $record['START_DATE'];
				$end_date = $record['END_DATE'];
				$use_day = $record['USE_DAY'];
				$form_category = $record['FORM_CATEGORY'];
				$form_title = $record['FORM_TITLE'];
				$file1 = $record['FILE_1'];
				$file2 = $record['FILE_2'];
				$file3 = $record['FILE_3'];

				if ($form_category == "휴가계") {	$form_title = $form_title ." 휴가계";	}

				$vacation = $start_date ." - ". $end_date ." (". $use_day ."일)";

				$writer_team = $team;
				$writer_position = $position;
				$writer_name = $name;
			}
		}
		else
		{
			$record = sqlsrv_fetch_array($rs);
			
			$title = $record['TITLE'];
			$contents = $record['CONTENTS'];
			$approval_date = $record['APPROVAL_DATE'];
			$id = $record['PRS_ID'];
			$team = $record['PRS_TEAM'];
			$position = $record['PRS_POSITION'];
			$name = $record['PRS_NAME'];
			$status = $record['STATUS'];
			$payment_yn = $record['PAYMENT_YN'];
			$open_yn = $record['OPEN_YN'];
			$start_date = $record['START_DATE'];
			$end_date = $record['END_DATE'];
			$use_day = $record['USE_DAY'];
			$form_category = $record['FORM_CATEGORY'];
			$form_title = $record['FORM_TITLE'];
			$file1 = $record['FILE_1'];
			$file2 = $record['FILE_2'];
			$file3 = $record['FILE_3'];
			$project_no = $record['PROJECT_NO'];
			
			$vacation .= $start_date ." - ". $end_date ." (". $use_day ."일)";

			$writer_team = $team;
			$writer_position = $position;
			$writer_name = $name;

			$sql = "SELECT TITLE FROM DF_PROJECT WITH(NOLOCK) WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			$record = sqlsrv_fetch_array($rs);
			$project = $record['TITLE'];
		}
	}

	$contents = str_replace('"','\"',$contents);
	$contents = str_replace("\r","",$contents);
	$contents = str_replace("\n","",$contents);
?>

<? include INC_PATH."/top.php"; ?>
<script src="/js/approval.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

		log_html = "<a href=\"javascript:funLog('<?=$doc_no?>');\"><img src=\"/img/btn_approveLog.gif\" alt=\"결재로그\" /></a>";
		modify_html = "";

		<? if ($id == $prs_id && $status == "미결재") { ?>
			<? if ($form_category == "비용품의서" || $form_category == "프로젝트 관련품의서") { ?>	
				modify_html = modify_html + "<a href=\"javascript:funModify_old('<?=$doc_no?>');\"><img src=\"/img/btn_modify2.gif\" alt=\"수정\"></a>";
			<? } else { ?>
				modify_html = modify_html + "<a href=\"javascript:funModify('<?=$doc_no?>');\"><img src=\"/img/btn_modify2.gif\" alt=\"수정\"></a>";
			<? } ?>
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
		content_html = content_html + "					<th class=\"gray\">문서번호</th>";
		content_html = content_html + "					<td><?=$doc_no?></td>";
		content_html = content_html + "					<th class=\"gray\" rowspan=\"2\">결재</th>";
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
			$to_status_prev = "결재";

			$to_orderArr = "";
			$to_idArr = "";
			$to_nameArr = "";
			$to_positionArr = "";
			$to_dateArr = "";
			$to_statusArr = "";
			$to_signArr = "";
			$to_signpwdArr = "";
			$to_status_prevArr = "";
			while ($record = sqlsrv_fetch_array($rs))
			{
				$to_order = $record['A_ORDER'];
				$to_id = $record['A_PRS_ID'];
				$to_name = $record['A_PRS_NAME'];
				$to_position = $record['A_PRS_POSITION'];
				$to_date = $record['A_REG_DATE'];
				$to_status = $record['A_STATUS'];
				$to_sign = $record['PRS_SIGN'];
				$to_signpwd = $record['PRS_SIGNPWD'];

				$to_status_prevArr = $to_status_prevArr . "##". $to_status_prev;

				$to_orderArr = $to_orderArr ."##". $to_order;
				$to_idArr = $to_idArr ."##". $to_id;
				$to_nameArr = $to_nameArr ."##". $to_name;
				$to_positionArr = $to_positionArr ."##". $to_position;
				$to_dateArr = $to_dateArr ."##". $to_date;
				$to_statusArr = $to_statusArr ."##". $to_status;
				$to_signArr = $to_signArr ."##". $to_sign;
				$to_signpwdArr = $to_signpwdArr ."##". $to_signpwd;

				$to_status_prev = $to_status;

				$i++;
			}

			$to_orderThis = explode("##",$to_orderArr);
			$to_idThis = explode("##",$to_idArr);
			$to_nameThis = explode("##",$to_nameArr);
			$to_positionThis = explode("##",$to_positionArr);
			$to_dateThis = explode("##",$to_dateArr);
			$to_statusThis = explode("##",$to_statusArr);
			$to_signThis = explode("##",$to_signArr);
			$to_signpwdThis = explode("##",$to_signpwdArr);
			$to_status_prevThis = explode("##",$to_status_prevArr);
	
			$ArrCount = count($to_orderThis);
		?>
		content_html = content_html + "							<tr style=\"height:30px\">";
		<?
			for ($i=1; $i<$ArrCount; $i++)
			{
		?>
		content_html = content_html + "								<td width=\"20%\"<? if ($i == 5) {?> class=\"last\"<? } ?>><?=$to_positionThis[$i]?></td>";
		<?
			}
			for ($i=$ArrCount; $i<=5; $i++)
			{
		?>
		content_html = content_html + "								<td width=\"20%\"<? if ($i == 5) {?> class=\"last\"<? } ?>></td>";
		<?
			}
		?>
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr style=\"height:65px\">";
		<?
			for ($i=1; $i<$ArrCount; $i++)
			{
		?>
		content_html = content_html + "								<td<? if ($i == 5) {?> class=\"last\"<? } ?>>";
			<? if ($to_statusThis[$i] == "미결재") { ?>
				<? if ($to_idThis[$i] == $prs_id && $to_status_prevThis[$i] == "결재" && $status != "임시") { ?>
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
			for ($i=$ArrCount; $i<=5; $i++)
			{
		?>
		content_html = content_html + "								<td<? if ($i == 5) {?> class=\"last\"<? } ?>></td>";
		<?
			}
		?>
		content_html = content_html + "							</tr>";
		content_html = content_html + "							<tr style=\"height:30px\" class=\"last\">";
		<?
			for ($i=1; $i<$ArrCount; $i++)
			{
		?>
		content_html = content_html + "								<td<? if ($i == 5) {?> class=\"last\"<? } ?>><span class=\"signature_state\"><?=str_replace(".","/",substr($to_dateThis[$i],5,5))?><? if ($to_dateThis[$i] != "") { ?><? } ?><?=$to_statusThis[$i]?></span></td>";
		<?
			}
			for ($i=$ArrCount; $i<=5; $i++)
			{
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
		content_html = content_html + "					<th class=\"gray\">문서종류</th>";
		content_html = content_html + "					<td><?=$form_title?></td>";
		content_html = content_html + "			   </tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">기안일</th>";
		content_html = content_html + "					<td><?=substr($approval_date,0,4)?>년 <?=substr($approval_date,5,2)?>월 <?=substr($approval_date,8,2)?>일</td>";
		content_html = content_html + "					<th class=\"gray\">수신참조</th>";
		content_html = content_html + "					<td>";
		<?
			$sql = "SELECT C_PRS_ID, C_PRS_NAME, C_PRS_POSITION FROM DF_APPROVAL_CC WITH(NOLOCK) WHERE DOC_NO = '$doc_no' ORDER BY C_ORDER, C_PRS_POSITION";
			$rs = sqlsrv_query($dbConn, $sql);
	
			$i = 0;
			while ($record = sqlsrv_fetch_array($rs))
			{
				$cc_id = $record['C_PRS_ID'];
				$cc_name = $record['C_PRS_NAME'];
				$cc_position = $record['C_PRS_POSITION'];

				if ($cc_id == $prs_id) {
					$sql1 = "UPDATE DF_APPROVAL_CC SET C_READ_YN = 'Y', C_READ_DATE = getdate() WHERE DOC_NO = '$doc_no' AND C_PRS_ID = '$cc_id'";
					$rs1 = sqlsrv_query($dbConn, $sql1);
				}

				if ($i == 0)
				{
		?>
		content_html = content_html + "						<?=$cc_position?> <?=$cc_name?>";
		<?
				}
				else
				{
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
		content_html = content_html + "					<th class=\"gray\">부서</th>";
		content_html = content_html + "					<td colspan=\"3\"><?=getTeamInfo($writer_team)?></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">이름</th>";
		content_html = content_html + "					<td><?=$writer_position?> <?=$writer_name?></td>";
		content_html = content_html + "					<th class=\"gray\">공개여부</th>";
		content_html = content_html + "					<td><? if ($open_yn == "Y") { echo "공개"; } else { echo "비공개"; } ?></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">제목</th>";
		content_html = content_html + "					<td colspan=\"3\"><?=$title?></td>";
		content_html = content_html + "				</tr>";

		<? if ($form_category == "비용품의서" || $form_category == "프로젝트 관련품의서") { ?>			
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">프로젝트</th>";
		content_html = content_html + "					<td colspan=\"3\">";
			<? if (($id == $prs_id && $payment_yn == "미지급") || $prf_id == 4) { ?>
		content_html = content_html + "						<select name=\"project_no\" style=\"width:500px;height:30px;\">";
		content_html = content_html + "							<option value=\"\"></option>";
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
															while ($record = sqlsrv_fetch_array($rs))
															{
																$p_no = $record['PROJECT_NO'];
																$p_title= $record['TITLE'];
														?>
		content_html = content_html + "							<option value=\"<?=$p_no?>\"<? if ($project_no == $p_no) { echo ' selected'; } ?>>[<?=$p_no?>] <?=$p_title?></option>";
														<?
															}
														?>
		content_html = content_html + "						</select>";
		content_html = content_html + "						<a href=\"javascript:modifyExpense_old('project');\"><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
			<? } else { ?>
		content_html = content_html + "					<?=$project?>";
			<? } ?>
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";
		<? } else { ?>
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">기간</th>";
		content_html = content_html + "					<td colspan=\"3\"><?=$vacation?></td>";
		content_html = content_html + "				</tr>";
		<? } ?>

		<? if ($form_category == "외근계/파견계" || $form_category == "출장계") { ?>
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">동반자</th>";
		content_html = content_html + "					<td colspan=\"3\">";
			<?
				$sql = "SELECT P_PRS_ID, P_PRS_NAME, P_PRS_POSITION FROM DF_APPROVAL_PARTNER WITH(NOLOCK) WHERE DOC_NO = '$doc_no' ORDER BY P_ORDER";
				$rs = sqlsrv_query($dbConn, $sql);

				$i = 0;
				while ($record = sqlsrv_fetch_array($rs))
				{
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

		<? if ($form_category == "비용품의서" || $form_category == "프로젝트 관련품의서") { ?>
			<?
				$sql = "SELECT SUM(MONEY) AS MONEY FROM DF_PROJECT_EXPENSE WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND LAST = 'Y'";
				$rs = sqlsrv_query($dbConn, $sql);

				$record=sqlsrv_fetch_array($rs);

				$sum_money = $record['MONEY'];
			?>
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<th class=\"gray\">결제금액</th>";
		content_html = content_html + "					<td colspan=\"3\"><?=number_format($sum_money,0)?></td>";
		content_html = content_html + "				</tr>";
		content_html = content_html + "				<tr>";
		content_html = content_html + "					<td colspan=\"4\">";
			<?
				$sql = "SELECT 
							IDX, MEMO, MONEY, ACTUAL
						FROM 
							DF_PROJECT_EXPENSE WITH(NOLOCK)
						WHERE 
							DOC_NO = '$doc_no' AND LAST = 'Y'
						ORDER BY 
							IDX";
				$rs = sqlsrv_query($dbConn, $sql);

				$expense = 0;
				while ($record = sqlsrv_fetch_array($rs))
				{
					$expense_idx = $record['IDX'];
					$expense_memo = $record['MEMO'];
					$expense_money = number_format($record['MONEY'],0);
					$expense_actual = $record['ACTUAL'];

					if (($id == $prs_id && $payment_yn == "미지급") || $prf_id == 4) {
			?>
		content_html = content_html + "					<table width=\"100%\">";
		content_html = content_html + "						<tr>";
		content_html = content_html + "							<th>항목<?=$expense_idx+1?></th>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<input name=\"memo_<?=$expense_idx?>\" id=\"e_memo_<?=$expense_idx?>\" style=\"width:210px;height:23px;\" value=\"<?=$expense_memo?>\"></td>";
		content_html = content_html + "							</td>";
		content_html = content_html + "							<th>금액</th>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<input name=\"money_<?=$expense_idx?>\" id=\"e_money_<?=$expense_idx?>\" style=\"ime-mode:disabled;width:70px;height:23px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);\" value=\"<?=$expense_money?>\">";
		content_html = content_html + "							</td>";
		content_html = content_html + "							<th>실비여부</th>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<input type=\"checkbox\" name=\"actual_<?=$expense_idx?>\" value=\"Y\"<? if ($expense_actual == 'Y') { echo ' checked'; } ?>>";
		content_html = content_html + "							</td>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<a href=\"javascript:modifyExpense_old('<?=$expense_idx?>');\"><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
		content_html = content_html + "								<a href=\"javascript:deleteExpense_old('<?=$expense_idx?>');\"><img src=\"/img/btn_popup_delete.gif\" alt=\"\"></a>";
		content_html = content_html + "								<span style=\"vertical-align:baseline;\"><a href=\"javascript:funExpenseList_old('<?=$doc_no?>','<?=$expense_idx?>');\">[수정내역]</a></span>";
		content_html = content_html + "							</td>";
		content_html = content_html + "						</tr>";
		content_html = content_html + "					</table>";
			<?		} else { ?>
		content_html = content_html + "					<table width=\"100%\">";
		content_html = content_html + "						<colgroup>";
		content_html = content_html + "						<col width=\"10%\" />";
		content_html = content_html + "						<col width=\"45%\" />";
		content_html = content_html + "						<col width=\"10%\" />";
		content_html = content_html + "						<col width=\"15%\" />";
		content_html = content_html + "						<col width=\"10%\" />";
		content_html = content_html + "						<col width=\"10%\" />";
		content_html = content_html + "						</colgroup>";
		content_html = content_html + "						<tr>";
		content_html = content_html + "							<th>항목<?=$expense_idx+1?></th>";
		content_html = content_html + "							<td><?=$expense_memo?></td>";
		content_html = content_html + "							<th>금액</th>";
		content_html = content_html + "							<td><?=$expense_money?></td>";
		content_html = content_html + "							<th>실비여부</th>";
		content_html = content_html + "							<td><?=$expense_actual?></td>";
		content_html = content_html + "						</tr>";
		content_html = content_html + "					</table>";
			<?		} ?>
		<?		
					$expense = $expense + 1;
				} 
		?>
		<?
				if (($id == $prs_id && $payment_yn == "미지급") || $prf_id == 4) {
					for ($i=$expense; $i<5; $i++)
					{
		?>
		content_html = content_html + "					<table width=\"100%\">";
		content_html = content_html + "						<tr>";
		content_html = content_html + "							<th>항목<?=$i+1?></th>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<input name=\"memo_<?=$i?>\" id=\"e_memo_<?=$i?>\" style=\"width:210px;height:23px;\" value=\"\"></td>";
		content_html = content_html + "							</td>";
		content_html = content_html + "							<th>금액</th>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<input name=\"money_<?=$i?>\" id=\"e_money_<?=$i?>\" style=\"ime-mode:disabled;width:70px;height:23px;\" onKeyPress=\"if (event.keyCode < 48 || event.keyCode > 57) { event.returnValue = false; }\" onKeyup=\"javascript:checkThousand(this,this.value);\" value=\"\">";
		content_html = content_html + "							</td>";
		content_html = content_html + "							<th>실비여부</th>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<input type=\"checkbox\" name=\"actual_<?=$i?>\" value=\"Y\">";
		content_html = content_html + "							</td>";
		content_html = content_html + "							<td>";
		content_html = content_html + "								<a href=\"javascript:modifyExpense_old('<?=$i?>');\"><img src=\"/img/btn_popup_modify.gif\" alt=\"\"></a>";
		content_html = content_html + "								<span style=\"vertical-align:baseline;\"></span>";
		content_html = content_html + "							</td>";
		content_html = content_html + "						</tr>";
		content_html = content_html + "					</table>";
		<?
					}
				}
		?>
		<? } ?>
		content_html = content_html + "					</td>";
		content_html = content_html + "				</tr>";

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
			if ($file1 != "")
			{
		?>
		content_html = content_html + "				<tr class=\"attach-file<? if ($file2 == "" && $file3 == "") { ?> last<? } ?>\">";
		content_html = content_html + "					<th>첨부파일</th>";
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
			if ($file2 != "")
			{
		?>
		content_html = content_html + "				<tr class=\"attach-file<? if ($file3 == "") { ?> last<? } ?>\">";
		content_html = content_html + "					<th><? if ($file1 == "") { ?>첨부파일<? } ?></th>";
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
			if ($file3 != "")
			{
		?>
		content_html = content_html + "				<tr class=\"attach-file last\">";
		content_html = content_html + "					<th><? if ($file1 == "" && $file2 == "") { ?>첨부파일<? } ?></th>";
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

		<? if ($status != "임시") { ?>
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
				while ($record=sqlsrv_fetch_array($rs))
				{
					$reply_no = $record['R_SEQNO'];
					$reply_id = $record['R_PRS_ID'];
					$reply_name = $record['R_PRS_NAME'];
					$reply_position = $record['R_PRS_POSITION'];
					$reply_contents = $record['R_CONTENTS'];
					$reply_date = $record['R_REG_DATE'];

					$reply_contents = str_replace('"','\"',$reply_contents);
					$reply_contents = str_replace("\r\n","<br>",$reply_contents);

			?>
		content_html = content_html + "				<tr class=\"idea-view<? if ($reply == $total_reply) { ?> last<? } ?>\">";
		content_html = content_html + "					<th><? if ($reply == 1) { ?>의견보기<? } ?></th>";
		content_html = content_html + "					<td colspan=\"3\">";
		content_html = content_html + "						<div class=\"txt-idea\">";
		content_html = content_html + "							<span class=\"\" id=\"c_text_<?=$reply_no?>\"><?=$reply_contents?></span>";
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

			<? if ($payment_yn == "미지급" || ($payment_yn == "지급" && $prf_id == 4)) { ?>
		content_html = content_html + "				<tr class=\"idea-add\">";
		content_html = content_html + "					<th>의견등록</th>";
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
