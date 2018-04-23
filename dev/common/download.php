<?
	ob_start();

	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";

	$menu = isset($_REQUEST['menu']) ? $_REQUEST['menu'] : null; 
	$file = isset($_REQUEST['file']) ? $_REQUEST['file'] : null; 
	$seq = isset($_REQUEST['seq']) ? $_REQUEST['seq'] : null; 

	if ($menu == "" || $file == "" || $seq == "") 
	{ 
?>
		<script type="text/javascript">
			alert("비정상적인 접근입니다.");
		</script>
<?
		exit;
	}

	if ($menu == "board")
	{
		$filepath = BOARD_DIR;

		$sql = "SELECT
					FILE_". $file ."
				FROM
					DF_BOARD WITH(NOLOCK)
				WHERE
					SEQNO = '". $seq ."'";

		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		if (sqlsrv_has_rows($rs) > 0)
		{
			$file = $record[0];
		}
	}
	else if ($menu == "book")
	{
		$filepath = BOOK_DIR;

		$sql = "SELECT
					FILE_". $file ."
				FROM
					DF_BOARD WITH(NOLOCK)
				WHERE
					SEQNO = '". $seq ."'";

		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		if (sqlsrv_has_rows($rs) > 0)
		{
			$file = $record[0];
		}
	}
	else if ($menu == "approval")
	{
		$filepath = APPROVAL_DIR;

		$sql = "SELECT
					FILE_". $file ."
				FROM
					DF_APPROVAL WITH(NOLOCK)
				WHERE
					DOC_NO = '". $seq ."'";

		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		if (sqlsrv_has_rows($rs) > 0)
		{
			$file = $record[0];
		}
	}

	if (file_exists($filepath.$file))
	{
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".str_replace(",", "_", $file));
		header("Content-Length: ".(string)(filesize($filepath.$file)));
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0");

		$fp = fopen($filepath.$file, "rb");
		while (!feof($fp))
		{
			echo fread($fp, 100*1024);
		}
		fclose($fp);
		flush();
	}

?>