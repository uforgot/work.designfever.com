<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>
<?
	$user_uuid = isset($_REQUEST['user_uuid']) ? $_REQUEST['user_uuid'] : null;	
	$today = date("Y-m-d");	
	$prs_id = "";
	$chk_yn ="";
//df_person ���� user_uuid�� ����� �������� ���� ���� ����
	$sql = "SELECT TOP 1 PRS_ID FROM DF_PERSON WITH(NOLOCK) WHERE PRS_BEACON = '$user_uuid' ORDER BY PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$prs_id = $record['PRS_ID'];		
	}	
//df_person���� ���� prs_id�� ����üũ ��Ī
	$sql = "SELECT TOP 1 * FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$prs_id' AND DATE = '$today'";	
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$chk_yn = "TRUE"; //��ٰ��� �����Ƿ� notification�ȶ�� 
	}else{
		$chk_yn = "FALSE"; //��ٰ��� �����Ƿ� notification ��� 
	}
		echo $chk_yn;
?>