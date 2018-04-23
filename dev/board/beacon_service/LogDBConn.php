<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>
<?
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null;
	$major = isset($_REQUEST['major']) ? $_REQUEST['major'] : null;
	$minor= isset($_REQUEST['minor']) ? $_REQUEST['minor'] : null;
	$distance = isset($_REQUEST['distance']) ? $_REQUEST['distance'] : null;
	$user_uuid = isset($_REQUEST['user_uuid']) ? $_REQUEST['user_uuid'] : null;	
	$user_device = isset($_REQUEST['user_device']) ? $_REQUEST['user_device'] : null;	
	
	$prs_id = "";
	$prs_name = "";
	$prs_login = "";
	$beacon_location="";

	//df_person 에서 사용자 정보를 얻어오기 위해 user_uuid와 매칭 시킴
	$sql = "SELECT TOP 1 PRS_ID, PRS_NAME, PRS_LOGIN
			  FROM DF_PERSON WITH(NOLOCK)
			 WHERE PRS_BEACON = '$user_uuid'
			 ORDER BY PRS_ID DESC";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$prs_id = $record['PRS_ID'];
		$prs_name = $record['PRS_NAME'];
		$prs_login = $record['PRS_LOGIN'];				
	}	
	//df_beacon_log 비콘 위치 정보를 얻어오기 위해 beacon, major, minor와 매칭 시킴
	$sql = "SELECT TOP 1 BEACON_UUID, BEACON_MAJOR, BEACON_MINOR, BEACON_LOCATION
			  FROM DF_BEACON_INFO WITH(NOLOCK)
			 WHERE BEACON_UUID = '$beacon'
			   AND BEACON_MAJOR = '$major'
			   AND BEACON_MINOR = '$minor'
			 ORDER BY BEACON_UUID DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$beacon_location = $record['BEACON_LOCATION'];		
	}
		
//beacon LOG insert
	$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_BEACON_LOG WITH(NOLOCK)";
	$rs = sqlsrv_query($dbConn,$sql);

	$result = sqlsrv_fetch_array($rs);
	$maxno = $result[0] + 1;

		$sql = "INSERT INTO DF_BEACON_LOG
				(SEQNO, CHECK_DATETIME, USERID, BEACON_UUID, BEACON_DISTANCE, BEACON_MAJOR, BEACON_MINOR, BEACON_LOCATION, USER_UUID, USER_DEVICE, TMP1, TMP2)
				VALUES
				('$maxno',getdate(),'$prs_name','$beacon','$distance','$major','$minor','$beacon_location','$user_uuid','$user_device','','')";				
	
	$rs = sqlsrv_query($dbConn,$sql);
	if ($rs == false)
	{
		//echo "<script>goAlert(\"출근체크 오류입니다. 개발팀에 문의해 주세요.\");</script>";
	}
?>