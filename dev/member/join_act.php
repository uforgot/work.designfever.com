<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/KISA_SHA256.php";
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
<?
	if (!in_array(REMOTE_IP, $ok_ip_arr))
	{
?>
		<script language="javascript">
			alert("ȸ�������� �系������ �����մϴ�.");
		</script>
<?
		exit;
	}
?>

<?
	$maxSize = 1024*1024;        // ���ε� ���� �ִ� ũ�� ���� (200KB)

	$login = isset($_POST['login']) ? $_POST['login'] : null;					//���̵�
	$name = isset($_POST['name']) ? $_POST['name'] : null;						//�̸�

	$email = isset($_POST['email']) ? $_POST['email'] : null;						//DF E-mail

	$PassWd = isset($_POST['PassWd']) ? $_POST['PassWd'] : null;					// ��й�ȣ

	$new_pwd = kisa_sha256($PassWd);

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

	//�̹��� Upload
	$myFile_FileName = $_FILES['file_img']['name'];					//���� ���ϸ�
	$myFile_Size = $_FILES['file_img']['size'];						//���� ũ��
	$myFile_Temp = $_FILES['file_img']['tmp_name'];					//�ӽ� ���� ���ϸ�
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

	//insert - DF_PERSON
	$sql = "SELECT ISNULL(MAX(PRS_ID),0) FROM DF_PERSON WITH(NOLOCK)";
	$rs = sqlsrv_query($dbConn,$sql);

	$result = sqlsrv_fetch_array($rs);
	$seq = $result[0] + 1;

	//$prf_id = 5;
	if ($position == "�İ�/�����") 
	{
		$prf_id = 7;
	}
	else
	{
		$prf_id = 1;
	}

	$sql = "INSERT INTO DF_PERSON
				(PRS_ID, PRF_ID, PRS_NAME, PRS_LOGIN, PRS_PWD, PRS_TEAM, PRS_POSITION, PRS_EMAIL, PRS_MOBILE, PRS_TEL, PRS_E_TEL, PRS_ZIPCODE, PRS_ADDR1, PRS_ADDR2, PRS_ZIPCODE_NEW, PRS_ADDRESS_NEW, FILE_IMG, PRS_JOIN, PRS_BIRTH, PRS_BIRTH_TYPE, PRS_EXTENSION)
			VALUES
				('$seq','$prf_id','$name','$login','$new_pwd','$team','$position','$email','$mobile','$tel','$e_tel','$zipcode','$addr1','$addr2','$zipcode_new','$address_new','$myFile_Real','$join','$birth','$birth_type','$extension')";
	$rs = sqlsrv_query($dbConn,$sql);

	if ($rs == false)
	{
		echo $sql;
?>
		<script language="javascript">
			alert("��Ͻ��� �Ͽ����ϴ�. �������� ������ �ּ���.");
		</script>
<?
		exit;
	}
	else
	{
		//insert = DF_VACATION
		$sql = "SELECT MAX(YEAR) FROM DF_VACATION WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$max_year = $result[0];
		$this_year = date("Y");

		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_VACATION WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq2 = $result[0] + 1;

		if ($max_year == $this_year)
		{
			$sql = "INSERT INTO DF_VACATION
						(SEQNO, PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_LOGIN, YEAR, VACATION_COUNT, REG_DATE)
					VALUES
						('$seq2','$seq','$name','$team','$position','$login','".date("Y")."','0', '".date("Y-m-d")."')";
			$rs = sqlsrv_query($dbConn,$sql);
		}
		else
		{
			for ($i=(int)$this_year; $i<=$max_year; $i++)
			{
				$sql = "INSERT INTO DF_VACATION
							(SEQNO, PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_LOGIN, YEAR, VACATION_COUNT, REG_DATE)
						VALUES
							('$seq2','$seq','$name','$team','$position','$login','$i','0', '".date("Y-m-d")."')";
				$rs = sqlsrv_query($dbConn,$sql);

				$seq2 = $seq2 + 1;
			}

		}
?>
		<script language="javascript">
			alert("ȸ�������� �Ϸ�Ǿ����ϴ�.");
			parent.location.href="/";
		</script>
<?
	}
?>
