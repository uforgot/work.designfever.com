<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>
<?
	$user_uuid = isset($_REQUEST['user_uuid']) ? $_REQUEST['user_uuid'] : null;	
	$today = date("Y-m-d");	
	$prs_id = "";
	$chk_yn ="";
//df_person 에서 user_uuid로 사용자 가져오기 위해 쿼리 돌림
	$sql = "SELECT TOP 1 PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_BEACON = '$user_uuid' ORDER BY PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$prs_id = $record['PRS_ID'];		
	}	
//df_person에서 돌린 prs_id로 근태체크 매칭
	$sql = "SELECT TOP 1 * FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$prs_id' AND DATE = '$today'";	
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$chk_yn = "TRUE"; //출근값이 있으므로 notification안띄움 
	}else{
		$chk_yn = "FALSE"; //출근값이 없으므로 notification 띄움 
	}
		echo $chk_yn;
?>