<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$signpwd = isset($_REQUEST['signpwd']) ? $_REQUEST['signpwd'] : "Y";

	$maxSize = 1*1024*1024;        // 업로드 파일 최대 크기 지정 (1MB)

	$sign = isset($_POST['sign']) ? $_POST['sign'] : null;				//기존 이미지

	//이미지 Upload
	$myFile_FileName = $_FILES['sign']['name'];					//원본 파일명
	$myFile_Size = $_FILES['sign']['size'];						//원본 크기
	$myFile_Temp = $_FILES['sign']['tmp_name'];					//임시 저장 파일명
	$myFile_Real = "";
	$myFile_Name = "";

	if ($myFile_Size > 0)
	{
		if ($myFile_Size > $maxSize)
		{
?>
			<script language="javascript">
				alert("파일용량이 너무 큽니다.\n1MB 이내의 파일을 다시 선택해 주세요.");
			</script>
<?
			exit;
		}
		else
		{
			$myFile_Type_check = explode('.',$myFile_FileName);			
			$myFile_Type = $myFile_Type_check[count($myFile_Type_check)-1];	//파일 확장자
			for ($i=0; $i<count($myFile_Type_check)-1; $i++)
			{
				$myFile_Name .= $myFile_Type_check[$i];						//확장자 제외 파일명
			}

			$exist_flag = 0;
			if (file_exists(PRS_DIR.$myFile_Name.".".$myFile_Type))		//파일 존재여부 체크
			{
				$i = 0;
				while ($exist_flag != 1)
				{
					if (!file_exists(PRS_DIR.$myFile_Name."_".$i.".".$myFile_Type))
					{
						$exist_flag = 1;
						$myFile_Real = $myFile_Name."_".$i.".".$myFile_Type;
					}
					$i++;
				}
			}
			else
			{
				$myFile_Real = $myFile_FileName;
			}

			copy($myFile_Temp, PRS_DIR.$myFile_Real);	//파일 저장

		}
	}

	//update
	$sql = "UPDATE DF_PERSON SET 
				PRS_SIGNPWD = '$signpwd'";
	if ($myFile_Size > 0) { $sql .= ", PRS_SIGN = '$myFile_Real'"; }
	$sql .= " WHERE PRS_ID = '$prs_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("등록실패 하였습니다. 개발팀에 문의해 주세요.");
	</script>
<?
		exit;
	}
?>
	<script language="javascript">
		parent.location.href = "signature.php";
	</script>
