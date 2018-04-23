<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "6") 
	{ 
?>
	<script type="text/javascript">
		alert("탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	
	$board = isset($_REQUEST['board']) ? $_REQUEST['board'] : "default"; 
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$writer = isset($_REQUEST['writer']) ? $_REQUEST['writer'] : null;  
	$writer_id = isset($_REQUEST['writer_id']) ? $_REQUEST['writer_id'] : null;  
	$writer_name = isset($_REQUEST['writer_name']) ? $_REQUEST['writer_name'] : null;  
	$writer_team = isset($_REQUEST['writer_team']) ? $_REQUEST['writer_team'] : null;  
	$writer_position = isset($_REQUEST['writer_position']) ? $_REQUEST['writer_position'] : null;  

	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;  
	$notice_yn = isset($_REQUEST['notice_yn']) ? $_REQUEST['notice_yn'] : "N";  
	$contents = isset($_REQUEST['contents']) ? $_REQUEST['contents'] : null;  

	$title = str_replace("'","''",$title);
	$contents = str_replace("'","''",$contents);

	$filedel_1 = isset($_REQUEST['filedel_1']) ? $_REQUEST['filedel_1'] : "N";  
	$filedel_2 = isset($_REQUEST['filedel_2']) ? $_REQUEST['filedel_2'] : "N";  
	$filedel_3  = isset($_REQUEST['filedel_3']) ? $_REQUEST['filedel_3'] : "N";  

	$depth = 0;
	$hit = 0;
	$tmp1 = null;
	$tmp2 = 0;

	if ($type != "write")
	{
		if ($seqno == "")
		{
?>
	<script type="text/javascript">
		alert("해당 글이 존재하지 않습니다.");
		history.back();
	</script>
<?
			exit;
		}
	}

	if ($type != "delete")
	{
		if ($filedel_1 == "Y")
		{
			$sql = "SELECT FILE_1 FROM DF_BOARD WHERE SEQNO = $seqno";
			$rs = sqlsrv_query($dbConn, $sql);

			while ($record = sqlsrv_fetch_array($rs))
			{
				$delFile = $record[0];
				
				unlink(BOARD_DIR.$delFile);	//파일 삭제
			
				$sql1 = "UPDATE DF_BOARD SET FILE_1 = '' WHERE SEQNO = $seqno";
				$rs1 = sqlsrv_query($dbConn, $sql1);
			}
		}
		if ($filedel_2 == "Y")
		{
			$sql = "SELECT FILE_2 FROM DF_BOARD WHERE SEQNO = $seqno";
			$rs = sqlsrv_query($dbConn, $sql);

			while ($record = sqlsrv_fetch_array($rs))
			{
				$delFile = $record[0];
				
				unlink(BOARD_DIR.$delFile);	//파일 삭제
			
				$sql1 = "UPDATE DF_BOARD SET FILE_2 = '' WHERE SEQNO = $seqno";
				$rs1 = sqlsrv_query($dbConn, $sql1);
			}
		}
		if ($filedel_3 == "Y")
		{
			$sql = "SELECT FILE_3 FROM DF_BOARD WHERE SEQNO = $seqno";
			$rs = sqlsrv_query($dbConn, $sql);

			while ($record = sqlsrv_fetch_array($rs))
			{
				$delFile = $record[0];
				
				unlink(BOARD_DIR.$delFile);	//파일 삭제
			
				$sql1 = "UPDATE DF_BOARD SET FILE_3 = '' WHERE SEQNO = $seqno";
				$rs1 = sqlsrv_query($dbConn, $sql1);
			}
		}

		$maxSize = 10*1024*1024;        // 업로드 파일 최대 크기 지정 (10MB)

		//file Upload
		$myFile1_Real = "";
		$myFile2_Real = "";
		$myFile3_Real = "";

		$myFile1_FileName = $_FILES['file_1']['name'];
		$myFile2_FileName = $_FILES['file_2']['name'];
		$myFile3_FileName = $_FILES['file_3']['name'];

		$myFile1_Size = $_FILES['file_1']['size'];
		$myFile2_Size = $_FILES['file_2']['size'];
		$myFile3_Size = $_FILES['file_3']['size'];

		$myFile1_Temp = $_FILES['file_1']['tmp_name'];
		$myFile2_Temp = $_FILES['file_2']['tmp_name'];
		$myFile3_Temp = $_FILES['file_3']['tmp_name'];

		$myFile1_Err = $_FILES['file_1']['error'];
		$myFile2_Err = $_FILES['file_2']['error'];
		$myFile3_Err = $_FILES['file_3']['error'];

		$myFile1_Name = "";
		$myFile2_Name = "";
		$myFile3_Name = "";

		if ($myFile1_Err == "1" || $myFile2_Err == "1" || $myFile3_Err == "1") {
	?>
			<script language="javascript">
				alert("파일용량이 너무 큽니다.\n10MB 이내의 파일을 다시 선택해 주세요.");
			</script>
	<?
			exit;
		}

		if ($myFile1_Size > $maxSize || $myFile2_Size > $maxSize || $myFile3_Size > $maxSize)
		{
?>
			<script language="javascript">
				alert("파일용량이 너무 큽니다.\n10MB 이내의 파일을 다시 선택해 주세요.");
			</script>
<?
			exit;
		}

		if ($myFile1_Size > 0)
		{
			$myFile1_Type_check = explode('.',$myFile1_FileName);			
			$myFile1_Type = $myFile1_Type_check[count($myFile1_Type_check)-1];	//파일 확장자
			for ($i=0; $i<count($myFile1_Type_check)-1; $i++)
			{
				$myFile1_Name .= $myFile1_Type_check[$i];						//확장자 제외 파일명
			}

			$exist_flag = 0;
			if (file_exists(BOARD_DIR.$myFile1_Name.".".$myFile1_Type))		//파일 존재여부 체크
			{
				$i = 1;
				while ($exist_flag != 1)
				{
					if (!file_exists(BOARD_DIR.$myFile1_Name."_".$i.".".$myFile1_Type))
					{
						$exist_flag = 1;
						$myFile1_Real = $myFile1_Name."_".$i.".".$myFile1_Type;
					}
					$i++;
				}
			}
			else
			{
				$myFile1_Real = $myFile1_FileName;
			}

			move_uploaded_file($myFile1_Temp, BOARD_DIR.$myFile1_Real);	//파일 저장
		}

		if ($myFile2_Size > 0)
		{
			$myFile2_Type_check = explode('.',$myFile2_FileName);			
			$myFile2_Type = $myFile2_Type_check[count($myFile2_Type_check)-1];	//파일 확장자
			for ($i=0; $i<count($myFile2_Type_check)-1; $i++)
			{
				$myFile2_Name .= $myFile2_Type_check[$i];						//확장자 제외 파일명
			}

			$exist_flag = 0;
			if (file_exists(BOARD_DIR.$myFile2_Name.".".$myFile2_Type))		//파일 존재여부 체크
			{
				$i = 1;
				while ($exist_flag != 1)
				{
					if (!file_exists(BOARD_DIR.$myFile2_Name."_".$i.".".$myFile2_Type))
					{
						$exist_flag = 1;
						$myFile2_Real = $myFile2_Name."_".$i.".".$myFile2_Type;
					}
					$i++;
				}
			}
			else
			{
				$myFile2_Real = $myFile2_FileName;
			}

			move_uploaded_file($myFile2_Temp, BOARD_DIR.$myFile2_Real);	//파일 저장
		}

		if ($myFile3_Size > 0)
		{
			$myFile3_Type_check = explode('.',$myFile3_FileName);			
			$myFile3_Type = $myFile3_Type_check[count($myFile3_Type_check)-1];	//파일 확장자
			for ($i=0; $i<count($myFile3_Type_check)-1; $i++)
			{
				$myFile3_Name .= $myFile3_Type_check[$i];						//확장자 제외 파일명
			}

			$exist_flag = 0;
			if (file_exists(BOARD_DIR.$myFile3_Name.".".$myFile3_Type))		//파일 존재여부 체크
			{
				$i = 1;
				while ($exist_flag != 1)
				{
					if (!file_exists(BOARD_DIR.$myFile3_Name."_".$i.".".$myFile3_Type))
					{
						$exist_flag = 1;
						$myFile3_Real = $myFile3_Name."_".$i.".".$myFile3_Type;
					}
					$i++;
				}
			}
			else
			{
				$myFile3_Real = $myFile3_FileName;
			}

			move_uploaded_file($myFile3_Temp, BOARD_DIR.$myFile3_Real);	//파일 저장
		}
	}

	$myFile1_Real = str_replace("'","''",$myFile1_Real);
	$myFile2_Real = str_replace("'","''",$myFile2_Real);
	$myFile3_Real = str_replace("'","''",$myFile3_Real);

	if ($type == "write")
	{
		$type_title = "등록";
		$retUrl = "board_list.php?page=". $page;
		if ($board != "")
		{
			$retUrl .= "&board=". $board;
		}
		if ($keyword != "")
		{
			$retUrl .= "&keyfield=". $keyfield ."&keyword=". $keyword;
		}

		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_BOARD WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		$sql = "INSERT INTO DF_BOARD
				(SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, FILE_1, FILE_2, FILE_3, REP_DEPTH, NOTICE_YN, REG_DATE, TMP1, TMP2, TMP3)
				VALUES
				('$seq', '$writer_id', '$writer_name', '$writer', '$writer_team', '$writer_position', '$title', '$contents', '$hit', '$myFile1_Real', '$myFile2_Real', '$myFile3_Real', '$depth', '$notice_yn', getdate(), '$tmp1', '$tmp2', '$board')";
	}
	else if ($type == "modify")
	{
		$type_title = "수정";
		$retUrl = "board_detail.php?page=". $page ."&seqno=". $seqno;
		if ($board != "")
		{
			$retUrl .= "&board=". $board;
		}
		if ($keyword != "")
		{
			$retUrl .= "&keyfield=". $keyfield ."&keyword=". $keyword;
		}

		$sql = "UPDATE DF_BOARD SET 
					TITLE = '$title', 
					CONTENTS = '$contents',
					NOTICE_YN = '$notice_yn'"; 
		if ($myFile1_Real != "") { $sql .= ", FILE_1 = '$myFile1_Real'"; }
		if ($myFile2_Real != "") { $sql .= ", FILE_2 = '$myFile2_Real'"; }
		if ($myFile3_Real != "") { $sql .= ", FILE_3 = '$myFile3_Real'"; }
		$sql .= " WHERE 
					SEQNO = $seqno";
	}
	else if ($type == "delete")
	{
		$type_title = "삭제";
		$retUrl = "board_list.php?page=". $page;
		if ($board != "")
		{
			$retUrl .= "&board=". $board;
		}
		if ($keyword != "")
		{
			$retUrl .= "&keyfield=". $keyfield ."&keyword=". $keyword;
		}

		$sql = "SELECT FILE_1, FILE_2, FILE_3 FROM DF_BOARD WHERE SEQNO = $seqno";
		$rs = sqlsrv_query($dbConn, $sql);

		while ($record = sqlsrv_fetch_array($rs))
		{
			$delFile1 = $record[0];
			$delFile2 = $record[1];
			$delFile3 = $record[2];

			//파일 삭제
			if ($delFile1 != "") { unlink(BOARD_DIR.$delFile1); }
			if ($delFile2 != "") { unlink(BOARD_DIR.$delFile2); }
			if ($delFile3 != "") { unlink(BOARD_DIR.$delFile3); }
		}

		$sql = "DELETE FROM DF_BOARD_REPLY WHERE SEQNO = $seqno";
		$rs = sqlsrv_query($dbConn, $sql);

		$sql = "DELETE FROM DF_BOARD_REPLY2 WHERE SEQNO = $seqno";
		$rs = sqlsrv_query($dbConn, $sql);

		$sql = "DELETE FROM DF_BOARD WHERE SEQNO = $seqno";
	}

	$rs = sqlsrv_query($dbConn, $sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("<?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
	</script>
<?
		exit;
	}
	else
	{
?>
	<script language="javascript">
		alert("<?=$type_title?> 되었습니다.");
		parent.location.href = "<?=$retUrl?>";
	</script>
<?
	}
?>
