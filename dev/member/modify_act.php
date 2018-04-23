<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/KISA_SHA256.php";
?>

<?
	$maxSize = 1024*1024;        // ���ε� ���� �ִ� ũ�� ���� (1MB)

	$prs_id = isset($_POST['prs_id']) ? $_POST['prs_id'] : null;

	$PassWd = isset($_POST['PassWd']) ? $_POST['PassWd'] : null;								//�� ��й�ȣ
	$PassWdCon = isset($_POST['PassWdCon']) ? $_POST['PassWdCon'] : null;						//�� ��й�ȣ Ȯ��
	if ($PassWd != "") 
	{ 
		$new_pwd = kisa_sha256($PassWd);
		$pwdSql = "PRS_PWD = '$new_pwd'";	
	}

	$email = isset($_POST['email']) ? $_POST['email'] : null;						//DF E-mail

	$join1 = isset($_POST['join1']) ? $_POST['join1'] : null;						//�Ի���
	$join2 = isset($_POST['join2']) ? $_POST['join2'] : null;
	if (strlen($join2)==1) { $join2 = "0". $join2; }
	$join3 = isset($_POST['join3']) ? $_POST['join3'] : null;
	if (strlen($join3)==1) { $join3 = "0". $join3; }
	$join = $join1 ."-". $join2 ."-". $join3;

	$birth1 = isset($_POST['birth1']) ? $_POST['birth1'] : null;					//����
	$birth2 = isset($_POST['birth2']) ? $_POST['birth2'] : null;
	if (strlen($birth2)==1) { $birth2 = "0". $birth2; }
	$birth3 = isset($_POST['birth3']) ? $_POST['birth3'] : null;
	if (strlen($birth3)==1) { $birth3 = "0". $birth3; }
	$birth = $birth1 ."-". $birth2 ."-". $birth3;

	$birth_type = isset($_POST['birth_type']) ? $_POST['birth_type'] : null;		//���.����

	$mobile1 = isset($_POST['mobile1']) ? $_POST['mobile1'] : null;							//��ȭ��ȣ
	$mobile2 = isset($_POST['mobile2']) ? $_POST['mobile2'] : null;
	$mobile3 = isset($_POST['mobile3']) ? $_POST['mobile3'] : null;
	$mobile = $mobile1 ."-". $mobile2 ."-". $mobile3;

	$e_tel1 = isset($_POST['e_tel1']) ? $_POST['e_tel1'] : null;					//��󿬶���
	$e_tel2 = isset($_POST['e_tel2']) ? $_POST['e_tel2'] : null;
	$e_tel3 = isset($_POST['e_tel3']) ? $_POST['e_tel3'] : null;
	$e_tel = $e_tel1 ."-". $e_tel2 ."-". $e_tel3;

	$zipcode1 = isset($_POST['zipcode1']) ? $_POST['zipcode1'] : null;				//�����ȣ
	$zipcode2 = isset($_POST['zipcode2']) ? $_POST['zipcode2'] : null;
	$zipcode = $zipcode1 ."-". $zipcode2;

	$addr1 = isset($_POST['addr1']) ? $_POST['addr1'] : null;						//�ּ�
	$addr2 = isset($_POST['addr2']) ? $_POST['addr2'] : null;

	$zipcode_new = isset($_POST['zipcode_new']) ? $_POST['zipcode_new'] : null;		//�� �����ȣ
	$address_new = isset($_POST['address_new']) ? $_POST['address_new'] : null;		//�� �ּ�
	
	$team = isset($_POST['team']) ? $_POST['team'] : null;							//�μ�
	$position = isset($_POST['position']) ? $_POST['position'] : null;				//����

	$extension = isset($_POST['extension']) ? $_POST['extension'] : null;			//������ȣ

	$tel1 = isset($_POST['tel1']) ? $_POST['tel1'] : null;							//��ȭ��ȣ
	$tel2 = isset($_POST['tel2']) ? $_POST['tel2'] : null;
	$tel = "070-". $tel1 ."-". $tel2;

	$file_img = isset($_POST['file_img']) ? $_POST['file_img'] : null;				//���� �̹���
	$file_img2 = isset($_POST['file_img2']) ? $_POST['file_img2'] : null;			//�� �̹���
	$img_delete = isset($_POST['img_delete']) ? $_POST['img_delete'] : null;		//���� �̹��� ����

	//�̹��� Upload
	$myFile_FileName = $_FILES['file_img2']['name'];					//���� ���ϸ�
	$myFile_Size = $_FILES['file_img2']['size'];						//���� ũ��
	$myFile_Temp = $_FILES['file_img2']['tmp_name'];					//�ӽ� ���� ���ϸ�
	$myFile_Real = $file_img;
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

	if ($img_delete == "1" && $myFile_Real == $file_img) {
		$myFile_Real = "";
	}

	//update
	$sql = "UPDATE DF_PERSON SET ". $pwdSql ."
				PRS_TEAM = '$team',
				PRS_POSITION = '$position',
				PRS_EMAIL = '$email',
				PRS_MOBILE = '$mobile',
				PRS_TEL = '$tel',
				PRS_EXTENSION = '$extension',
				PRS_E_TEL = '$e_tel',
				PRS_ZIPCODE = '$zipcode',
				PRS_ADDR1 = '$addr1',
				PRS_ADDR2 = '$addr2',
				PRS_ZIPCODE_NEW = '$zipcode_new',
				PRS_ADDRESS_NEW = '$address_new',
				FILE_IMG = '$myFile_Real', 
				PRS_JOIN = '$join',
				PRS_BIRTH = '$birth',
				PRS_BIRTH_TYPE = '$birth_type' 
			WHERE PRS_ID = '$prs_id'";

	$rs = sqlsrv_query($dbConn,$sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("�������� �Ͽ����ϴ�. �������� ������ �ּ���.");
	</script>
<?
	}
	else
	{
		$sql = "UPDATE DF_VACATION SET
					PRS_TEAM = '$team',
					PRS_POSITION = '$position'
				WHERE PRS_ID = '$prs_id'";
		$rs = sqlsrv_query($dbConn,$sql);
?>
	<script language="javascript">
		alert("ȸ�������� �Ϸ�Ǿ����ϴ�.");
		parent.location.href="/main.php";
	</script>
<?
	}
?>