<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>
<?
	$beacon = isset($_REQUEST['beacon']) ? $_REQUEST['beacon'] : null;		
	$prs_id = isset($_REQUEST['prs_id']) ? $_REQUEST['prs_id'] : null;		

	$sql = "UPDATE DF_PERSON SET PRS_BEACON = '$beacon'
			WHERE PRS_ID = '$prs_id'";
	 $rs = sqlsrv_query($dbConn,$sql);
	 if ($rs == false)
	{		
		echo "<script>arent.goAlert(\"������Ʈ ����. �����ڿ��� �����ϼ���.\"); parent.document.location.reload(); </script>";
	}
	else
	{
		echo "<script>parent.goAlert(\"��������� ������Ʈ �Ǿ����ϴ�.\"); parent.document.location.reload(); </script>";		
		
	}
?>
