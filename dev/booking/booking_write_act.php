<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<script type="text/javascript">
		alert("��ϴ��,Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$writer = isset($_REQUEST['writer']) ? $_REQUEST['writer'] : null;  
	$writer_id = isset($_REQUEST['writer_id']) ? $_REQUEST['writer_id'] : null;  
	$writer_name = isset($_REQUEST['writer_name']) ? $_REQUEST['writer_name'] : null;  
	$writer_team = isset($_REQUEST['writer_team']) ? $_REQUEST['writer_team'] : null;  
	$writer_position = isset($_REQUEST['writer_position']) ? $_REQUEST['writer_position'] : null;  

	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;  
	$room_name = isset($_REQUEST['room_name']) ? $_REQUEST['room_name'] : null;  
	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null;  
	$s_hour = isset($_REQUEST['s_hour']) ? $_REQUEST['s_hour'] : null;  
	$s_min = isset($_REQUEST['s_min']) ? $_REQUEST['s_min'] : null;  
	$e_hour = isset($_REQUEST['e_hour']) ? $_REQUEST['e_hour'] : null;  
	$s_min = isset($_REQUEST['s_min']) ? $_REQUEST['s_min'] : null;  

	$title = str_replace("'","''",$title);
	$s_time = $s_hour.":".$s_min;
	$e_time = $e_hour.":".$e_min;

	if ($type != "write")
	{
		if ($seqno == "")
		{
?>
	<script type="text/javascript">
		alert("�ش� ���� �������� �ʽ��ϴ�.");
		history.back();
	</script>
<?
			exit;
		}
	}
	
	if($type != "delete")  {
		$retUrl = "booking_list.php?date=$date";

		// ������ ��� �ڽ��� ���ǿ��� ����
		if($type == "modify") $qry = " AND SEQNO != '$seqno'";

		// �μ�Ʈ ��, ����ð� �ߺ� üũ
		$sql = "SELECT COUNT(SEQNO) FROM DF_BOOKING WITH(NOLOCK)";
		$sql .= " WHERE ROOM = '$room_name' AND DATE = '$date' AND ((S_TIME <= '$s_time' AND E_TIME > '$s_time') OR (S_TIME < '$e_time' AND E_TIME >= '$e_time')) $qry";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$cnt = $result[0];

		if($cnt > 0) {
?>			
	<script language="javascript">
		alert("����ð��� �ߺ� �Ǿ����ϴ�. ������Ȳ�� Ȯ�� �� �ּ���.");
		//parent.location.href = "<?=$retUrl?>";
	</script>
<?
			exit;
		}
	}

	if ($type == "write")
	{
		$type_title = "���";
		$retUrl = "booking_list.php?date=$date";

		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_BOOKING WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		$sql = "INSERT INTO DF_BOOKING
				(SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, ROOM, DATE, S_TIME, E_TIME, REG_DATE)
				VALUES
				('$seq', '$writer_id', '$writer_name', '$writer', '$writer_team', '$writer_position', '$title', '$room_name', '$date', '$s_time', '$e_time', getdate())";
	}
	else if ($type == "modify")
	{
		$type_title = "����";
		$retUrl = "booking_list.php";

		$sql = "UPDATE DF_BOOKING SET 
					TITLE = '$title', 
					ROOM = '$room_name',
					DATE = '$date',
					S_TIME = '$s_time',
					E_TIME = '$e_time',
					MOD_NAME = '$writer_name',
					MOD_DATE = getdate()"; 
		$sql .= " WHERE 
					SEQNO = $seqno";
	}
	else if ($type == "delete")
	{
		$type_title = "����";
		$retUrl = "booking_list.php?date=$date";

		$sql = "DELETE FROM DF_BOOKING WHERE SEQNO = $seqno";
	}

	$rs = sqlsrv_query($dbConn, $sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("<?=$type_title?> ���� �Ͽ����ϴ�. �������� ������ �ּ���.");
	</script>
<?
		exit;
	}
	else
	{
?>
	<script language="javascript">
		alert("<?=$type_title?> �Ǿ����ϴ�.");
		parent.location.href = "<?=$retUrl?>";
	</script>
<?
	}
?>
