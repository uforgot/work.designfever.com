<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<? include INC_PATH."/top.php"; ?>

<?
	//권한 체크
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("해당페이지는 임원,관리자만 확인 가능합니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "payment";  

	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
/*
	$p_category = isset($_REQUEST['category']) ? $_REQUEST['category'] : null;
	$p_vacation = isset($_REQUEST['vacation']) ? $_REQUEST['vacation'] : null;
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
	$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : date("Y", strtotime("-2 days")); 
	$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : date("m", strtotime("-2 days")); 
	$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : date("d", strtotime("-2 days")); 
	$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : date("Y"); 
	$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : date("m"); 
	$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d"); 

	$retUrl = "approval_list_end.php?page=". $page ."&fr_year=". $fr_year ."&fr_month=". $fr_month ."&fr_day=". $fr_day ."&to_year=". $to_year ."&to_month=". $to_month ."&to_day=". $to_day;
	if ($p_category != "") {	
		$retUrl .= "&category=". $p_category;
	}
	if ($p_vacation != "") {	
		$retUrl .= "&vacation=". $p_vacation;
	}
	if ($p_name != "") {	
		$retUrl .= "&name=". $p_name;
	}
*/
	if ($type == "payment")
	{
		$sql = "UPDATE DF_APPROVAL SET 
					PAYMENT_YN = '지급' 
				WHERE DOC_NO = '$doc_no'";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script language="javascript">
			alert("지급 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
		else
		{
?>
		<script language="javascript">
			alert("지급 완료 되었습니다.");
			$("#payment_<?=$doc_no?>",parent.document).html("<img src=\"/img/state_okPay.gif\" alt=\"\">");
		</script>
<?
		}
	}
?>