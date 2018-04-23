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

	$maxSize = 10*1024*1024;        // ���ε� ���� �ִ� ũ�� ���� (10MB)

	if ($Upload_Size > 0)
	{
		if ($Upload_Size > $maxSize)
		{
?>
			<script language="javascript">
				alert("���Ͽ뷮�� �ʹ� Ů�ϴ�.\n10MB �̳��� ������ �ٽ� ������ �ּ���.");
			</script>
<?
			exit;
		}
		else
		{
			$Upload_Type_check = explode('.',$Upload_FileName);			
			$Upload_Type = $Upload_Type_check[count($Upload_Type_check)-1];	//���� Ȯ����
			$Upload_Type = strtolower($Upload_Type);

			if ($type == "images")
			{
				$extArr = array('bmp','gif','jpeg','jpg','png');
				if (in_array($Upload_Type,$extArr) == false)
				{
					echo "�̹��� ���ϸ� ���ε� �����մϴ�.";
					return false;
				}
			}
			else if ($type == "flash")
			{
				$extArr = array('swf','flv');
				if (in_array($Upload_Type,$extArr) == false)
				{
					echo "�÷��� ���ϸ� ���ε� �����մϴ�.";
					return false;
				}
			}
			else
			{
				$extArr = array('7z','aiff','alz','asf','avi','bmp','csv','doc','docx','fla','flv','gif','gz','gzip','hwp','jpeg','jpg','mid','mov','mp3','mp4','mpc','mpeg','mpg','ods','odt','pdf','png','ppt','pptx','pxd','qt','ra','ram','rar','rm','rmi','rmvb','rtf','sdc','sitd','swf','sxc','sxw','tar','tgz','tif','tiff','txt','vsd','wav','wma','wmv','xls','xlsx','zip');
				if (in_array($Upload_Type,$extArr) == false)
				{
					echo "���ε� �� �� ���� ���� �����Դϴ�.";
					return false;
				}
			}

			for ($i=0; $i<count($Upload_Type_check)-1; $i++)
			{
				$Upload_Name .= $Upload_Type_check[$i];						//Ȯ���� ���� ���ϸ�
			}
			
			$exist_flag = 0;
			if (file_exists(BOOK_DIR.$type."/".$Upload_Name.".".$Upload_Type))		//���� ���翩�� üũ
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

			copy($Upload_Temp, $save_dir);	//���� ����
			echo "<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$save_url', '���ε�Ϸ�');</script>";
		}
	}
?>
