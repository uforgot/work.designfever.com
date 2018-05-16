<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<? include INC_PATH."/top.php"; ?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null; 
	$team_name = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 
	$idx = isset($_REQUEST['idx']) ? $_REQUEST['idx'] : null; 
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null; 

	for ($i=0; $i<5; $i++) {
		${'type_'.$i}		= isset($_REQUEST['type_'.$i]) ? $_REQUEST['type_'.$i] : null;    
		${'money_'.$i}		= isset($_REQUEST['money_'.$i]) ? $_REQUEST['money_'.$i] : null;    
		${'tax_'.$i}		= isset($_REQUEST['tax_'.$i]) ? $_REQUEST['tax_'.$i] : null;    
		${'target_'.$i}		= isset($_REQUEST['target_'.$i]) ? $_REQUEST['target_'.$i] : null;    
		${'pay_type_'.$i}	= isset($_REQUEST['pay_type_'.$i]) ? $_REQUEST['pay_type_'.$i] : null;    
		${'pay_info_'.$i}	= isset($_REQUEST['pay_info_'.$i]) ? $_REQUEST['pay_info_'.$i] : null;    
		${'company_'.$i}	= isset($_REQUEST['company_'.$i]) ? $_REQUEST['company_'.$i] : null;     
		${'manager_'.$i}	= isset($_REQUEST['manager_'.$i]) ? $_REQUEST['manager_'.$i] : null;    
		${'contact_'.$i}	= isset($_REQUEST['contact_'.$i]) ? $_REQUEST['contact_'.$i] : null;    
		${'bank_name_'.$i}	= isset($_REQUEST['bank_name_'.$i]) ? $_REQUEST['bank_name_'.$i] : null;    
		${'bank_num_'.$i}	= isset($_REQUEST['bank_num_'.$i]) ? $_REQUEST['bank_num_'.$i] : null;    
		${'bank_user_'.$i}	= isset($_REQUEST['bank_user_'.$i]) ? $_REQUEST['bank_user_'.$i] : null;    
		${'pay_date_'.$i}	= isset($_REQUEST['pay_date_'.$i]) ? $_REQUEST['pay_date_'.$i] : null;    
		${'memo_'.$i}		= isset($_REQUEST['memo_'.$i]) ? $_REQUEST['memo_'.$i] : null;    
	}

	$retUrl = "approval_detail.php?doc_no=". $doc_no;

	if ($mode == "modify") {
		if ($idx == "project") {
			$sql = "UPDATE DF_APPROVAL SET PROJECT_NO = '$project_no' WHERE DOC_NO = '$doc_no'";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false) {
?>
			<script language="javascript">
				alert("error 1_0_1. 포로젝트 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "UPDATE DF_PROJECT_EXPENSE_V2 SET PROJECT_NO = '$project_no' WHERE DOC_NO = '$doc_no'";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false) {
?>
			<script language="javascript">
				alert("error 1_0_2. 포로젝트 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}

		$expense_type		= $type_0 ."##". $type_1 ."##". $type_2 ."##". $type_3 ."##". $type_4; 
		$expense_money		= $money_0 ."##". $money_1 ."##". $money_2 ."##". $money_3 ."##". $money_4; 
		$expense_tax		= $tax_0 ."##". $tax_1 ."##". $tax_2 ."##". $tax_3 ."##". $tax_4; 
		$expense_target		= $target_0 ."##". $target_1 ."##". $target_2 ."##". $target_3 ."##". $target_4; 
		$expense_pay_type	= $pay_type_0 ."##". $pay_type_1 ."##". $pay_type_2 ."##". $pay_type_3 ."##". $pay_type_4; 
		$expense_pay_info	= $pay_info_0 ."##". $pay_info_1 ."##". $pay_info_2 ."##". $pay_info_3 ."##". $pay_info_4; 
		$expense_company	= $company_0 ."##". $company_1 ."##". $company_2 ."##". $company_3 ."##". $company_4; 
		$expense_manager	= $manager_0 ."##". $manager_1 ."##". $manager_2 ."##". $manager_3 ."##". $manager_4; 
		$expense_contact	= $contact_0 ."##". $contact_1 ."##". $contact_2 ."##". $contact_3 ."##". $contact_4; 
		$expense_bank_name	= $bank_name_0 ."##". $bank_name_1 ."##". $bank_name_2 ."##". $bank_name_3 ."##". $bank_name_4; 
		$expense_bank_num	= $bank_num_0 ."##". $bank_num_1 ."##". $bank_num_2 ."##". $bank_num_3 ."##". $bank_num_4; 
		$expense_bank_user	= $bank_user_0 ."##". $bank_user_1 ."##". $bank_user_2 ."##". $bank_user_3 ."##". $bank_user_4; 
		$expense_pay_date	= $pay_date_0 ."##". $pay_date_1 ."##". $pay_date_2 ."##". $pay_date_3 ."##". $pay_date_4; 
		$expense_memo		= $memo_0 ."##". $memo_1 ."##". $memo_2 ."##". $memo_3 ."##". $memo_4; 

		$expense_type_arr		= explode("##",$expense_type);
		$expense_money_arr		= explode("##",$expense_money);
		$expense_tax_arr		= explode("##",$expense_tax);
		$expense_target_arr		= explode("##",$expense_target);
		$expense_pay_type_arr	= explode("##",$expense_pay_type);
		$expense_pay_info_arr	= explode("##",$expense_pay_info);
		$expense_company_arr	= explode("##",$expense_company);
		$expense_manager_arr	= explode("##",$expense_manager);
		$expense_contact_arr	= explode("##",$expense_contact);
		$expense_bank_name_arr	= explode("##",$expense_bank_name);
		$expense_bank_num_arr	= explode("##",$expense_bank_num);
		$expense_bank_user_arr	= explode("##",$expense_bank_user);
		$expense_pay_date_arr	= explode("##",$expense_pay_date);
		$expense_memo_arr		= explode("##",$expense_memo);

		for ($i=0;$i<5;$i++) {
			// 금액이 있는 경우
			if ($expense_money_arr[$i] != "") {
				$money_num = str_replace(",","",$expense_money_arr[$i]);

				$sql = "SELECT 
								PROJECT_NO, TYPE, MONEY, TAX, TARGET, 
								PAY_TYPE, PAY_INFO, COMPANY, MANAGER, CONTACT, 
								BANK_NAME, BANK_NUM, BANK_USER, PAY_DATE, MEMO 
						FROM 
								DF_PROJECT_EXPENSE_V2 WITH(NOLOCK) 
						WHERE 
								DOC_NO = '$doc_no' AND IDX = '$i' AND LAST = 'Y'";
				$rs = sqlsrv_query($dbConn,$sql);
				$record = sqlsrv_fetch_array($rs);

				$project = $record['PROJECT_NO'];
				$type_org =	$record['TYPE'];    
				$money_org = $record['MONEY'];    
				$tax_org = $record['TAX'];    
				$target_org	= $record['TARGET'];    
				$pay_type_org = $record['PAY_TYPE'];    
				$pay_info_org = $record['PAY_INFO'];    
				$company_org = $record['COMPANY'];     
				$manager_org = $record['MANAGER'];    
				$contact_org = $record['CONTACT'];    
				$bank_name_org = $record['BANK_NAME'];    
				$bank_num_org = $record['BANK_NUM'];    
				$bank_user_org = $record['BANK_USER'];    
				$pay_date_org = $record['PAY_DATE'];    
				$memo_org = $record['MEMO'];    

				if ($type_org != $expense_type_arr[$i] || $money_org != $money_num || $tax_org != $expense_tax_arr[$i] || $target_org != $expense_target_arr[$i] 
					|| $pay_type_org != $expense_pay_type_arr[$i] || $pay_info_org != $expense_pay_info_type_arr[$i] || $company_org != $expense_company_type_arr[$i] 
					|| $manager_org != $expense_manager_type_arr[$i] || $contact_org != $expense_contact_type_arr[$i] || $bank_name_org != $expense_bank_name_type_arr[$i] 
					|| $bank_num_org != $expense_bank_num_type_arr[$i] || $bank_user_org != $expense_bank_user_type_arr[$i] || $pay_date_org != $expense_pay_date_type_arr[$i]
					|| $memo_org != $expense_memo_type_arr[$i]) {

					$sql = "UPDATE DF_PROJECT_EXPENSE_V2 SET LAST = 'N' WHERE DOC_NO = '$doc_no' AND IDX = '$i' AND LAST = 'Y'";
					$rs = sqlsrv_query($dbConn, $sql);

					if ($rs == false) {
?>
					<script language="javascript">
						alert("error 1_1_<?=$i?>. 결제금액 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
					</script>
<?
						exit;
					}

					if ($money_num) {
						$sql = "INSERT INTO DF_PROJECT_EXPENSE_V2 (
									DOC_NO, PROJECT_NO, IDX, TYPE, MONEY, TAX, TARGET, 
									PAY_TYPE, PAY_INFO, COMPANY, MANAGER, CONTACT, 
									BANK_NAME, BANK_NUM, BANK_USER, PAY_DATE, MEMO, 
									LAST, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, REG_DATE
								) VALUES (
									'$doc_no','$project','$i','$expense_type_arr[$i]','$money_num','$expense_tax_arr[$i]','$expense_target_arr[$i]',
									'$expense_pay_type_arr[$i]','$expense_pay_info_arr[$i]','$expense_company_arr[$i]','$expense_manager_arr[$i]','$expense_contact_arr[$i]',
									'$expense_bank_name_arr[$i]','$expense_bank_num_arr[$i]','$expense_bank_user_arr[$i]','$expense_pay_date_arr[$i]','$expense_memo_arr[$i]',
									'Y','$prs_id','$prs_login','$prs_name','$prs_position',getdate()
								)";
						$rs = sqlsrv_query($dbConn, $sql);

						if ($rs == false) {
?>
						<script language="javascript">
							alert("error 1_2_<?=$i?>. 결제금액 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
						</script>
<?
							exit;
						}
					}
				}
			}
		}
?>
	<script language="javascript">
		alert("수정되었습니다.");
		location.href = "<?=$retUrl?>";
	</script>
<?
	} else if ($mode == "delete") {
		for ($i=0;$i<5;$i++) {
			if ($i == $idx) {
				$sql = "DELETE FROM DF_PROJECT_EXPENSE_V2 WHERE DOC_NO = '$doc_no' AND IDX = '$i'";
				$rs = sqlsrv_query($dbConn, $sql);

				if ($rs == false) {
?>
				<script language="javascript">
					alert("error 2_1_<?=$i?>. 결제금액 삭제에 실패 하였습니다. 개발팀에 문의해 주세요.");
				</script>
<?
					exit;
				}
			} else if ($i > $idx) {
				$j = $i - 1;

				$sql = "UPDATE DF_PROJECT_EXPENSE_V2 SET IDX = '$j' WHERE DOC_NO = '$doc_no' AND IDX = '$i'";
				$rs = sqlsrv_query($dbConn, $sql);

				if ($rs == false) {
?>
				<script language="javascript">
					alert("error 2_2_<?=$i?>. 결제금액 삭제에 실패 하였습니다. 개발팀에 문의해 주세요.");
				</script>
<?
					exit;
				}
			}
		}
?>
	<script language="javascript">
		alert("삭제되었습니다.");
		location.href = "<?=$retUrl?>";
	</script>
<?
	}
?>