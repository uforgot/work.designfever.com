<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$signpwd = isset($_REQUEST['signpwd']) ? $_REQUEST['signpwd'] : "Y";

	$maxSize = 1*1024*1024;        // ���ε� ���� �ִ� ũ�� ���� (1MB)

	$sign = isset($_POST['sign']) ? $_POST['sign'] : null;				//���� �̹���

	//�̹��� Upload
	$myFile_FileName = $_FILES['sign']['name'];					//���� ���ϸ�
	$myFile_Size = $_FILES['sign']['size'];						//���� ũ��
	$myFile_Temp = $_FILES['sign']['tmp_name'];					//�ӽ� ���� ���ϸ�
	$myFile_Real = "";
	$myFile_Name = "";

	if ($myFile_Size > 0)
	{
		if ($myFile_Size > $maxSize)
		{
?>
			<script language="javascript">
				alert("���Ͽ뷮�� �ʹ� Ů�ϴ�.\n1MB �̳��� ������ �ٽ� ������ �ּ���.");
			</script>
<?
			exit;
		}
		else
		{
			$myFile_Type_check = explode('.',$myFile_FileName);			
			$myFile_Type = $myFile_Type_check[count($myFile_Type_check)-1];	//���� Ȯ����
			for ($i=0; $i<count($myFile_Type_check)-1; $i++)
			{
				$myFile_Name .= $myFile_Type_check[$i];						//Ȯ���� ���� ���ϸ�
			}

			$exist_flag = 0;
			if (file_exists(PRS_DIR.$myFile_Name.".".$myFile_Type))		//���� ���翩�� üũ
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

			copy($myFile_Temp, PRS_DIR.$myFile_Real);	//���� ����

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
		alert("��Ͻ��� �Ͽ����ϴ�. �������� ������ �ּ���.");
	</script>
<?
		exit;
	}
?>
	<script language="javascript">
		parent.location.href = "signature.php";
	</script>
