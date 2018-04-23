<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";

	$sql = "SELECT * FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4)";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$col_prs_id = $record['PRS_ID'];
		$col_prs_passwd = $record['PRS_PASSWD'];

		$hash_pwd = create_hash($col_prs_passwd); 

		$sql2 = "UPDATE DF_PERSON SET PRS_PWD = '$hash_pwd' WHERE PRS_ID = '$col_prs_id'";
		$rs2 = sqlsrv_query($dbConn,$sql2);
	}

?>