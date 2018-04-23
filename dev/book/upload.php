<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;  

	$funcNum = isset($_GET['CKEditorFuncNum']) ? $_GET['CKEditorFuncNum'] : null;  
	$CKEditor = isset($_GET['CKEditor']) ? $_GET['CKEditor'] : null;
	$langCode = isset($_GET['langCode']) ? $_GET['langCode'] : null;

	$Upload_FileName = $_FILES['upload']['name'];
	$Upload_Size = $_FILES['upload']['size'];
	$Upload_Temp = $_FILES['upload']['tmp_name'];
	$Upload_Name = "";

	$maxSize = 10*1024*1024;        // 업로드 파일 최대 크기 지정 (10MB)

	if ($Upload_Size > 0)
	{
		if ($Upload_Size > $maxSize)
		{
?>
			<script language="javascript">
				alert("파일용량이 너무 큽니다.\n10MB 이내의 파일을 다시 선택해 주세요.");
			</script>
<?
			exit;
		}
		else
		{
			$Upload_Type_check = explode('.',$Upload_FileName);			
			$Upload_Type = $Upload_Type_check[count($Upload_Type_check)-1];	//파일 확장자
			$Upload_Type = strtolower($Upload_Type);

			if ($type == "images")
			{
				$extArr = array('bmp','gif','jpeg','jpg','png');
				if (in_array($Upload_Type,$extArr) == false)
				{
					echo "이미지 파일만 업로드 가능합니다.";
					return false;
				}
			}
			else if ($type == "flash")
			{
				$extArr = array('swf','flv');
				if (in_array($Upload_Type,$extArr) == false)
				{
					echo "플래시 파일만 업로드 가능합니다.";
					return false;
				}
			}
			else
			{
				$extArr = array('7z','aiff','alz','asf','avi','bmp','csv','doc','docx','fla','flv','gif','gz','gzip','hwp','jpeg','jpg','mid','mov','mp3','mp4','mpc','mpeg','mpg','ods','odt','pdf','png','ppt','pptx','pxd','qt','ra','ram','rar','rm','rmi','rmvb','rtf','sdc','sitd','swf','sxc','sxw','tar','tgz','tif','tiff','txt','vsd','wav','wma','wmv','xls','xlsx','zip');
				if (in_array($Upload_Type,$extArr) == false)
				{
					echo "업로드 할 수 없는 파일 유형입니다.";
					return false;
				}
			}

			for ($i=0; $i<count($Upload_Type_check)-1; $i++)
			{
				$Upload_Name .= $Upload_Type_check[$i];						//확장자 제외 파일명
			}
			
			$exist_flag = 0;
			if (file_exists(BOOK_DIR.$type."/".$Upload_Name.".".$Upload_Type))		//파일 존재여부 체크
			{
				$i = 1;
				while ($exist_flag != 1)
				{
					if (!file_exists(BOOK_DIR.$type."/".$Upload_Name."_".$i.".".$Upload_Type))
					{
						$exist_flag = 1;
						$Upload_Real = $Upload_Name."_".$i.".".$Upload_Type;
					}
					$i++;
				}
			}
			else
			{
				$Upload_Real = $Upload_FileName;
			}

			$save_url = BOOK_URL.$type."/".$Upload_Real;
			$save_dir = BOOK_DIR.$type."/".$Upload_Real;

			copy($Upload_Temp, $save_dir);	//파일 저장
			echo "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$save_url', '업로드완료');</script>";
		}
	}
?>
