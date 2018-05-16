<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$p_date = isset($_POST['date']) ? $_POST['date'] : null;
	$p_id = isset($_POST['id']) ? $_POST['id'] : null;

	$p_login = isset($_POST['prs_login']) ? $_POST['prs_login'] : null;
	$p_name = isset($_POST['prs_name']) ? $_POST['prs_name'] : null;
	$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
	$p_memo = isset($_POST['memo']) ? $_POST['memo'] : null; 
	$p_flag = isset($_POST['flag']) ? $_POST['flag'] : null; 

	$gubun = "출퇴근";
	$p_gubun = isset($_POST['gubun']) ? $_POST['gubun'] : null; 
	$p_gubun1 = isset($_POST['gubun1']) ? $_POST['gubun1'] : null;
	$p_gubun1_hour = isset($_POST['gubun1_hour']) ? $_POST['gubun1_hour'] : null;
	$p_gubun1_minute = isset($_POST['gubun1_minute']) ? $_POST['gubun1_minute'] : null;
	$p_gubun2 = isset($_POST['gubun2']) ? $_POST['gubun2'] : null;
	$p_gubun2_hour = isset($_POST['gubun2_hour']) ? $_POST['gubun2_hour'] : null;
	$p_gubun2_minute = isset($_POST['gubun2_minute']) ? $_POST['gubun2_minute'] : null;

	$ip = REMOTE_IP;									//접속IP
	$now = date("YmdHis");								//입력 시간

	//휴가 병가 경조사 기타 결근 등 출퇴근 값 자동 (0000-2400)
	if ($p_gubun !="") 
	{
		$p_gubun1 = $p_gubun;
		$p_gubun1_hour = "00";
		$p_gubun1_minute = "00";
		$p_gubun2 = $p_gubun;
		$p_gubun2_hour = "24";
		$p_gubun2_minute = "00";
	}

	if ($p_gubun1_hour != "" && $p_gubun1_minute != "")
	{
		$checktime1 = str_replace("-","",$p_date) . $p_gubun1_hour . $p_gubun1_minute ."00";
	}
	if ($p_gubun2_hour != "" && $p_gubun2_minute != "")
	{
		$checktime2 = str_replace("-","",$p_date) . $p_gubun2_hour . $p_gubun2_minute ."00";
	}

	$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_CHECKTIME_REQUEST WITH(NOLOCK)";
	$rs = sqlsrv_query($dbConn,$sql);

	$result = sqlsrv_fetch_array($rs);
	$maxno = $result[0] + 1;

	$sql = "INSERT INTO DF_CHECKTIME_REQUEST
			(SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, CHECKIP, MEMO, REGDATE, STATUS, BST_FLAG)
			VALUES
			('$maxno','$p_id','$p_login','$p_name','$p_date','$gubun','$gubun1','$gubun2','$checktime1','$checktime2','$ip','$p_memo',getdate(),'ING', '$p_flag')"; // STATUS - 'ING', 'OK', 'CANCEL'

	$rs = sqlsrv_query($dbConn,$sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("error1. 수정요청 실패하였습니다. 개발팀에 문의하세요.");
	</script>
<?
		exit;
	} 
	else 
	{
		/*
		// 사원별 근태수정 카운트
		$sql = "SELECT COUNT(SEQNO) FROM DF_CHECKTIME_REQUEST_LOG WITH(NOLOCK) WHERE PRS_ID = '$p_id'";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$check = $result[0];

		if ($check == 0)
		{
			$sql = "INSERT INTO DF_CHECKTIME_REQUEST_LOG
					(PRS_ID, PRS_LOGIN, PRS_NAME, LAST_REGDATE, COUNT)
					VALUES
					('$p_id','$p_login','$p_name',getdate(),1)";

			$rs = sqlsrv_query($dbConn,$sql);
		}
		else
		{
			$sql = "UPDATE DF_CHECKTIME_REQUEST_LOG SET COUNT = COUNT + 1 WHERE PRS_ID = '$p_id'";
			$rs = sqlsrv_query($dbConn,$sql);
		}
		*/
	}
?>
	<script language="javascript">

		//alert("근태수정 요청이 정상적으로 접수 되었습니다");
		top.location.reload();

	</script>