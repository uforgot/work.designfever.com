<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<? include INC_PATH."/top.php"; ?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null; 
	$money_total = isset($_REQUEST['money_total']) ? str_replace(",","",$_REQUEST['money_total']) : null; 
	$vat_include = isset($_REQUEST['vat_include']) ? $_REQUEST['vat_include'] : null; 
	$idx = isset($_REQUEST['idx']) ? $_REQUEST['idx'] : null; 
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null; 

	$memo_0 = isset($_REQUEST['memo_0']) ? $_REQUEST['memo_0'] : null;    
	$money_0 = isset($_REQUEST['money_0']) ? $_REQUEST['money_0'] : null;   
	$actual_0 = isset($_REQUEST['actual_0']) ? $_REQUEST['actual_0'] : "N";

	$memo_1 = isset($_REQUEST['memo_1']) ? $_REQUEST['memo_1'] : null;    
	$money_1 = isset($_REQUEST['money_1']) ? $_REQUEST['money_1'] : null;   
	$actual_1 = isset($_REQUEST['actual_1']) ? $_REQUEST['actual_1'] : "N";

	$memo_2 = isset($_REQUEST['memo_2']) ? $_REQUEST['memo_2'] : null;    
	$money_2 = isset($_REQUEST['money_2']) ? $_REQUEST['money_2'] : null;   
	$actual_2 = isset($_REQUEST['actual_2']) ? $_REQUEST['actual_2'] : "N";

	$memo_3 = isset($_REQUEST['memo_3']) ? $_REQUEST['memo_3'] : null;    
	$money_3 = isset($_REQUEST['money_3']) ? $_REQUEST['money_3'] : null;   
	$actual_3 = isset($_REQUEST['actual_3']) ? $_REQUEST['actual_3'] : "N";

	$memo_4 = isset($_REQUEST['memo_4']) ? $_REQUEST['memo_4'] : null;    
	$money_4 = isset($_REQUEST['money_4']) ? $_REQUEST['money_4'] : null;   
	$actual_4 = isset($_REQUEST['actual_4']) ? $_REQUEST['actual_4'] : "N";
	
	$retUrl = "../old/approval_detail.php?doc_no=". $doc_no;

	if ($mode == "modify")
	{
		if ($idx == "project")
		{
			$sql = "UPDATE DF_APPROVAL SET PROJECT_NO = '$project_no' WHERE DOC_NO = '$doc_no'";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 1_0_1. 포로젝트 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "UPDATE DF_PROJECT_EXPENSE SET PROJECT_NO = '$project_no' WHERE DOC_NO = '$doc_no'";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 1_0_2. 포로젝트 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}
		else if ($idx == "money_total")
		{
			$sql = "UPDATE DF_APPROVAL SET MONEY_TOTAL = '$money_total', VAT_INCLUDE = '$vat_include' WHERE DOC_NO = '$doc_no'";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 1_0_3. 포로젝트 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}

		$memo = $memo_0 ."##". $memo_1 ."##". $memo_2 ."##". $memo_3 ."##". $memo_4; 
		$money = $money_0 ."##". $money_1 ."##". $money_2 ."##". $money_3 ."##". $money_4; 
		$actual = $actual_0 ."##". $actual_1 ."##". $actual_2 ."##". $actual_3 ."##". $actual_4; 

		$memo_arr = explode("##",$memo);
		$money_arr = explode("##",$money);
		$actual_arr = explode("##",$actual);

		for ($i=0;$i<5;$i++)
		{
			if ($money_arr[$i] != "")
			{
				$money_num = str_replace(",","",$money_arr[$i]);

				$sql = "SELECT PROJECT_NO, MONEY, MEMO, ACTUAL FROM DF_PROJECT_EXPENSE WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND IDX = '$i' AND LAST = 'Y'";
				$rs = sqlsrv_query($dbConn,$sql);
				$record = sqlsrv_fetch_array($rs);

				$project = $record['PROJECT_NO'];
				$money_org = $record['MONEY'];
				$memo_org = $record['MEMO'];
				$actual_org = $record['ACTUAL'];

				if ($money_org != $money_num || $memo_org != $memo_arr[$i] || $actual_org != $actual_arr[$i])
				{
					$sql = "UPDATE DF_PROJECT_EXPENSE SET LAST = 'N' WHERE DOC_NO = '$doc_no' AND IDX = '$i' AND LAST = 'Y'";
					$rs = sqlsrv_query($dbConn, $sql);

					if ($rs == false)
					{
?>
					<script language="javascript">
						alert("error 1_1_<?=$i?>. 결제금액 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
					</script>
<?
						exit;
					}

					$sql = "INSERT INTO DF_PROJECT_EXPENSE
							(DOC_NO, PROJECT_NO, IDX, MEMO, MONEY, ACTUAL, LAST, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, REG_DATE)
							VALUES 
							('$doc_no','$project','$i','$memo_arr[$i]','$money_num','$actual_arr[$i]','Y','$prs_id','$prs_login','$prs_name','$prs_position',getdate())";
					$rs = sqlsrv_query($dbConn, $sql);

					if ($rs == false)
					{
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
?>
	<script language="javascript">
		alert("수정되었습니다.");
		location.href = "<?=$retUrl?>";
	</script>
<?
	}
	else if ($mode == "delete")
	{
		for ($i=0;$i<5;$i++)
		{
			if ($i == $idx)
			{
				$sql = "DELETE FROM DF_PROJECT_EXPENSE WHERE DOC_NO = '$doc_no' AND IDX = '$i'";
				$rs = sqlsrv_query($dbConn, $sql);

				if ($rs == false)
				{
?>
				<script language="javascript">
					alert("error 2_1_<?=$i?>. 결제금액 삭제에 실패 하였습니다. 개발팀에 문의해 주세요.");
				</script>
<?
					exit;
				}
			}
			else if ($i > $idx)
			{
				$j = $i - 1;

				$sql = "UPDATE DF_PROJECT_EXPENSE SET IDX = '$j' WHERE DOC_NO = '$doc_no' AND IDX = '$i'";
				$rs = sqlsrv_query($dbConn, $sql);

				if ($rs == false)
				{
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