<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$form_no = isset($_REQUEST['form_no']) ? $_REQUEST['form_no'] : null;

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	if ($type == "write") 
	{ 
//		if ($form_no == "3" && $prf_id == "3" && $prs_id != "57") 
//		{
//			$status = "����";
//		}
//		else
//		{
			$status = "�̰���"; 
//		}
		$type_title = "���"; 
		$retUrl = "../approval_my_list.php";

		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE DOC_NO Like '". date("ym") ."-%' ORDER BY DOC_NO DESC";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);
		if (sizeof($record) > 0) 
		{
			$max_no = substr($record['DOC_NO'],5,4);
			$new_no = $max_no + 1;

			if (strlen($new_no) == 1) { $new_no = "000". $new_no; }
			else if (strlen($new_no) == 2) { $new_no = "00". $new_no; }
			else if (strlen($new_no) == 3) { $new_no = "0". $new_no; }

			$doc_no = date("ym") ."-". $new_no;
		}
		else
		{
			$doc_no = date("ym") ."-0001";
		}
	}
	else if ($type == "save") 
	{ 
		$status = "�ӽ�"; 
		$type_title = "�ӽ�����"; 
		$retUrl = "../approval_my_list_save.php";

		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE DOC_NO Like 'SAVE-". date("ym") ."-%' ORDER BY DOC_NO DESC";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);
		if (sizeof($record) > 0) 
		{
			$max_no = substr($record['DOC_NO'],10,4);
			$new_no = $max_no + 1;

			if (strlen($new_no) == 1) { $new_no = "000". $new_no; }
			else if (strlen($new_no) == 2) { $new_no = "00". $new_no; }
			else if (strlen($new_no) == 3) { $new_no = "0". $new_no; }

			$doc_no = "SAVE-". date("ym") ."-". $new_no;
		}
		else
		{
			$doc_no = "SAVE-". date("ym") ."-0001";
		}
	}

	$form_category = isset($_REQUEST['form_category']) ? $_REQUEST['form_category'] : "���ǰ�Ǽ�"; 
	if ($form_category == "�ް���" )
	{
		$form_title = isset($_REQUEST['form_title']) ? $_REQUEST['form_title'] : "����"; 
	}
	else
	{
		$form_title = $form_category; 
	}

	if ($form_title == "��������") 
	{
		$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : date("Y"); 
		$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : date("m"); 
		if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
		$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : date("d"); 
		if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
		$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : date("Y"); 
		$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : date("m"); 
		if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
		$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d"); 
		if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

		$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
		$to_date = $to_year ."-". $to_month ."-". $to_day;

		$sql = "SELECT ISNULL(COUNT(*),0) FROM HOLIDAY WITH(NOLOCK) WHERE DATE BETWEEN '". str_replace("-","",$fr_date) ."' AND '". str_replace("-","",$to_date) ."' AND DATEKIND = 'BIZ'";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$days = $result[0];

		if ($days > 5)
		{
?>
		<script language="javascript">
			alert("�������� �ް��� �ִ� 5�ϱ��� ��� �����մϴ�.");
		</script>
<?
			exit;
		}
	}

	$open_yn = isset($_REQUEST['open_yn']) ? $_REQUEST['open_yn'] : "Y";  
	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : $form_title;  
	$contents = isset($_REQUEST['contents']) ? $_REQUEST['contents'] : null;  

	$title = str_replace("'","''",$title);
	$contents = str_replace("'","''",$contents);

	$up_year = isset($_REQUEST['up_year']) ? $_REQUEST['up_year'] : date("Y"); 
	$up_month = isset($_REQUEST['up_month']) ? $_REQUEST['up_month'] : date("m"); 
	if (strlen($up_month) == 1) { $up_month = "0". $up_month; }
	$up_day = isset($_REQUEST['up_day']) ? $_REQUEST['up_day'] : date("d"); 
	$approval_date = $up_year ."-". $up_month ."-". $up_day;

	$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null;

	$maxSize = 10*1024*1024;        // ���ε� ���� �ִ� ũ�� ���� (10MB)

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

	$myFile1_Name = "";
	$myFile2_Name = "";
	$myFile3_Name = "";

	if ($myFile1_Size > 0)
	{
		if ($myFile1_Size > $maxSize)
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
			$myFile1_Type_check = explode('.',$myFile1_FileName);			
			$myFile1_Type = $myFile1_Type_check[count($myFile1_Type_check)-1];	//���� Ȯ����
			for ($i=0; $i<count($myFile1_Type_check)-1; $i++)
			{
				$myFile1_Name .= $myFile1_Type_check[$i];						//Ȯ���� ���� ���ϸ�
			}

			$exist_flag = 0;
			if (file_exists(APPROVAL_DIR.$myFile1_Name.".".$myFile1_Type))		//���� ���翩�� üũ
			{
				$i = 1;
				while ($exist_flag != 1)
				{
					if (!file_exists(APPROVAL_DIR.$myFile1_Name."_".$i.".".$myFile1_Type))
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

			move_uploaded_file($myFile1_Temp, APPROVAL_DIR.$myFile1_Real);	//���� ����

		}
	}

	if ($myFile2_Size > 0)
	{
		if ($myFile2_Size > $maxSize)
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
			$myFile2_Type_check = explode('.',$myFile2_FileName);			
			$myFile2_Type = $myFile2_Type_check[count($myFile2_Type_check)-1];	//���� Ȯ����
			for ($i=0; $i<count($myFile2_Type_check)-1; $i++)
			{
				$myFile2_Name .= $myFile2_Type_check[$i];						//Ȯ���� ���� ���ϸ�
			}

			$exist_flag = 0;
			if (file_exists(APPROVAL_DIR.$myFile2_Name.".".$myFile2_Type))		//���� ���翩�� üũ
			{
				$i = 1;
				while ($exist_flag != 1)
				{
					if (!file_exists(APPROVAL_DIR.$myFile2_Name."_".$i.".".$myFile2_Type))
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

			move_uploaded_file($myFile2_Temp, APPROVAL_DIR.$myFile2_Real);	//���� ����

		}
	}

	if ($myFile3_Size > 0)
	{
		if ($myFile3_Size > $maxSize)
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
			$myFile3_Type_check = explode('.',$myFile3_FileName);			
			$myFile3_Type = $myFile3_Type_check[count($myFile3_Type_check)-1];	//���� Ȯ����
			for ($i=0; $i<count($myFile3_Type_check)-1; $i++)
			{
				$myFile3_Name .= $myFile3_Type_check[$i];						//Ȯ���� ���� ���ϸ�
			}

			$exist_flag = 0;
			if (file_exists(APPROVAL_DIR.$myFile3_Name.".".$myFile3_Type))		//���� ���翩�� üũ
			{
				$i = 1;
				while ($exist_flag != 1)
				{
					if (!file_exists(APPROVAL_DIR.$myFile3_Name."_".$i.".".$myFile3_Type))
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

			move_uploaded_file($myFile3_Temp, APPROVAL_DIR.$myFile3_Real);	//���� ����

		}
	}

	$myFile1_Real = str_replace("'","''",$myFile1_Real);
	$myFile2_Real = str_replace("'","''",$myFile2_Real);
	$myFile3_Real = str_replace("'","''",$myFile3_Real);

	$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : date("Y"); 
	$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : date("m"); 
	if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
	$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : date("d"); 
	if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
	$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : date("Y"); 
	$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : date("m"); 
	if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
	$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d"); 
	if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

	$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
	$to_date = $to_year ."-". $to_month ."-". $to_day;

	if (strpos($form_title,"����") == false)
	{
		$sql = "SELECT ISNULL(COUNT(*),0) FROM HOLIDAY WITH(NOLOCK) WHERE DATE BETWEEN '". str_replace("-","",$fr_date) ."' AND '". str_replace("-","",$to_date) ."' AND DATEKIND = 'BIZ'";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$days = $result[0];
	}
	else
	{
		$days = 0.5;
		$to_date = $fr_date;
	}

	$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_APPROVAL WITH(NOLOCK)";
	$rs = sqlsrv_query($dbConn,$sql);

	$result = sqlsrv_fetch_array($rs);
	$seq = $result[0] + 1;

	$sql = "INSERT INTO DF_APPROVAL
			(SEQNO, DOC_NO, FORM_NO, FORM_CATEGORY, FORM_TITLE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, APPROVAL_DATE, REG_DATE, 
				TITLE, CONTENTS, START_DATE, END_DATE, USE_DAY, OPEN_YN, FILE_1, FILE_2, FILE_3, STATUS, PAYMENT_YN, REPLY_CNT, USE_YN, PROJECT_NO)
			VALUES 
			('$seq','$doc_no','$form_no','$form_category','$form_title','$prs_id','$prs_login','$prs_name','$prs_team','$prs_position','$approval_date',getdate(),
				'$title','$contents','$fr_date','$to_date','$days','$open_yn','$myFile1_Real', '$myFile2_Real', '$myFile3_Real','$status','������','0','Y','$project_no')";
	$rs = sqlsrv_query($dbConn, $sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("error 3. <?=$type_title?> ���� �Ͽ����ϴ�. �������� ������ �ּ���.");
	</script>
<?
		exit;
	}

	if (($form_title == "���ǰ�Ǽ�" || $form_title == "������Ʈ ����ǰ�Ǽ�") && $prf_id == 4)
	{
		$memo_0 = isset($_REQUEST['memo_0']) ? $_REQUEST['memo_0'] : null;    
		$money_0 = isset($_REQUEST['money_0']) ? $_REQUEST['money_0'] : null;   
		$actual_0 = isset($_REQUEST['actual_0']) ? $_REQUEST['actual_0'] : "N";
		$idx_0 = isset($_REQUEST['idx_0']) ? $_REQUEST['idx_0'] : 0;

		$memo_1 = isset($_REQUEST['memo_1']) ? $_REQUEST['memo_1'] : null;    
		$money_1 = isset($_REQUEST['money_1']) ? $_REQUEST['money_1'] : null;   
		$actual_1 = isset($_REQUEST['actual_1']) ? $_REQUEST['actual_1'] : "N";
		$idx_1 = isset($_REQUEST['idx_1']) ? $_REQUEST['idx_1'] : 0;

		$memo_2 = isset($_REQUEST['memo_2']) ? $_REQUEST['memo_2'] : null;    
		$money_2 = isset($_REQUEST['money_2']) ? $_REQUEST['money_2'] : null;   
		$actual_2 = isset($_REQUEST['actual_2']) ? $_REQUEST['actual_2'] : "N";
		$idx_2 = isset($_REQUEST['idx_2']) ? $_REQUEST['idx_2'] : 0;

		$memo_3 = isset($_REQUEST['memo_3']) ? $_REQUEST['memo_3'] : null;    
		$money_3 = isset($_REQUEST['money_3']) ? $_REQUEST['money_3'] : null;   
		$actual_3 = isset($_REQUEST['actual_3']) ? $_REQUEST['actual_3'] : "N";
		$idx_3 = isset($_REQUEST['idx_3']) ? $_REQUEST['idx_3'] : 0;

		$memo_4 = isset($_REQUEST['memo_4']) ? $_REQUEST['memo_4'] : null;    
		$money_4 = isset($_REQUEST['money_4']) ? $_REQUEST['money_4'] : null;   
		$actual_4 = isset($_REQUEST['actual_4']) ? $_REQUEST['actual_4'] : "N";
		$idx_4 = isset($_REQUEST['idx_4']) ? $_REQUEST['idx_4'] : 0;
		
		$expense_memo = $memo_0 ."##". $memo_1 ."##". $memo_2 ."##". $memo_3 ."##". $memo_4; 
		$expense_money = $money_0 ."##". $money_1 ."##". $money_2 ."##". $money_3 ."##". $money_4; 
		$expense_actual = $actual_0 ."##". $actual_1 ."##". $actual_2 ."##". $actual_3 ."##". $actual_4; 
		$expense_idx = $idx_0 ."##". $idx_1 ."##". $idx_2 ."##". $idx_3 ."##". $idx_4; 

		$expense_memo_arr = explode("##",$expense_memo);
		$expense_money_arr = explode("##",$expense_money);
		$expense_actual_arr = explode("##",$expense_actual);
		$expense_idx_arr = explode("##",$expense_idx);

		for ($i=0;$i<5;$i++)
		{
			if ($expense_money_arr[$i] != "")
			{
				$money = str_replace(",","",$expense_money_arr[$i]);

				$sql = "INSERT INTO DF_PROJECT_EXPENSE
						(DOC_NO, PROJECT_NO, IDX, MEMO, MONEY, ACTUAL, LAST, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, REG_DATE)
						VALUES 
						('$doc_no','$project_no','$expense_idx_arr[$i]','$expense_memo_arr[$i]','$money','$expense_actual_arr[$i]','Y','$prs_id','$prs_login','$prs_name','$prs_position',getdate())";
				$rs = sqlsrv_query($dbConn, $sql);

				if ($rs == false)
				{
?>
				<script language="javascript">
					alert("error 3_<?=$i?>. �����ݾ� ��Ͽ� ���� �Ͽ����ϴ�. �������� ������ �ּ���.");
				</script>
<?
					exit;
				}
			}
		}
	}

	//���� ���
	$to = isset($_REQUEST['to']) ? $_REQUEST['to'] : null;
	$to_id = isset($_REQUEST['to_id']) ? $_REQUEST['to_id'] : null;

	$to_arr = explode(",",$to_id);

	for ($i=0;$i<sizeof($to_arr);$i++)
	{
		if ($to_arr[$i] != "")
		{
			$j = $i + 1;
			$sql = "INSERT INTO DF_APPROVAL_TO 
						(DOC_NO, A_PRS_ID, A_PRS_LOGIN, A_PRS_NAME, A_PRS_TEAM, A_PRS_POSITION, A_ORDER, A_STATUS) 
					SELECT 
						'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j', '�̰���'
					FROM 
						DF_PERSON WITH(NOLOCK)
					WHERE
						PRS_ID = '$to_arr[$i]'
					";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 4. ������� ��Ͽ� ���� �Ͽ����ϴ�. �������� ������ �ּ���.");
			</script>
<?
				exit;
			}
		}
	}
	if ($type == "write") 
	{
		$sql = "UPDATE DF_APPROVAL_TO SET 
					A_REG_DATE = getdate(), A_STATUS = '����' 
				WHERE DOC_NO = '$doc_no' AND A_PRS_ID = '$prs_id' AND A_ORDER = '1'";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($prs_position == "�̻�" || $prs_position == "��ǥ")
		{
			if (sizeof($to_arr) <= 2)
			{
				$sql = "UPDATE DF_APPROVAL SET 
							STATUS = '����' 
						WHERE DOC_NO = '$doc_no'";
				$rs = sqlsrv_query($dbConn, $sql);

				$retUrl = "../approval_my_list_end.php";
			}
		}
	}

	//�������� ���
	$cc = isset($_REQUEST['cc']) ? $_REQUEST['cc'] : null;
	$cc_id = isset($_REQUEST['cc_id']) ? $_REQUEST['cc_id'] : null;

	$cc_arr = explode(",",$cc_id);

	$j = 0;
	for ($i=0;$i<sizeof($cc_arr);$i++)
	{
		if ($cc_arr[$i] != "")
		{
			$j = $i + 1;
			$sql = "INSERT INTO DF_APPROVAL_CC 
						(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
					SELECT 
						'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
					FROM 
						DF_PERSON WITH(NOLOCK)
					WHERE
						PRS_ID = '$cc_arr[$i]'
					";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 5. �������� ��Ͽ� ���� �Ͽ����ϴ�. �������� ������ �ּ���.");
			</script>
<?
				exit;
			}
		}
	}

	$j = $j + 1;
	if ($form_category == "�ٰܱ�" || $form_category == "�İ�/�����" || $form_category == "�����" || ($form_category == "�ް���" && strstr($form_title,"������Ʈ") == false))
	{
		//2�� �̻�Ե��� ��ǥ��(87,148) ����,���������� ������ ��������
		if ($prs_id == "15" || $prs_id == "24")
		{
			if (in_array("87",$to_arr) == false && in_array("87",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '87'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
			if (in_array("148",$to_arr) == false && in_array("148",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '148'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
		}
		//2�� ���� ��/������� ��ǥ��(87,148),�̻��(15,24) ����,���������� ������ �������� (������,������,�����,������,�ѿ���)
		if ($prs_id == "29" || $prs_id == "48" || $prs_id == "60" || $prs_id == "71" || $prs_id == "80" ) 
		{
			if (in_array("87",$to_arr) == false && in_array("87",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '87'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
			if (in_array("148",$to_arr) == false && in_array("148",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '148'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
			if (in_array("24",$to_arr) == false && in_array("24",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '24'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
			if (in_array("15",$to_arr) == false && in_array("15",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '15'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
		}
	}
	else if ($form_category == "������Ʈ ����ǰ�Ǽ�" || ($form_category == "�ް���" && strstr($form_title,"������Ʈ")) || $form_category == "���ǰ�Ǽ�" || $form_category == "������" || $form_category == "�ø���")
	{
		//2���� ��ǥ��(87,148),�̻��(15,24) ����,���������� ������ ��������
		if (strstr($prs_team,"2��"))
		{
			if (in_array("87",$to_arr) == false && in_array("87",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '87'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
			if (in_array("148",$to_arr) == false && in_array("148",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '148'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
			if (in_array("24",$to_arr) == false && in_array("24",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '24'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
			if (in_array("15",$to_arr) == false && in_array("15",$cc_arr) == false)
			{
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '15'
						";
				$rs = sqlsrv_query($dbConn, $sql);
			}
		}
			
	}

	//������ ���
	$partner = isset($_REQUEST['partner']) ? $_REQUEST['partner'] : null;
	$partner_id = isset($_REQUEST['partner_id']) ? $_REQUEST['partner_id'] : null;

	$partner_arr = explode(",",$partner_id);

	for ($i=0;$i<sizeof($partner_arr);$i++)
	{
		if ($partner_arr[$i] != "")
		{
			$j = $i + 1;
			$sql = "INSERT INTO DF_APPROVAL_PARTNER 
						(DOC_NO, P_PRS_ID, P_PRS_LOGIN, P_PRS_NAME, P_PRS_TEAM, P_PRS_POSITION, P_ORDER) 
					SELECT 
						'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
					FROM 
						DF_PERSON WITH(NOLOCK)
					WHERE
						PRS_ID = '$partner_arr[$i]'
					";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 6. ������ ��Ͽ� ���� �Ͽ����ϴ�. �������� ������ �ּ���.");
			</script>
<?
				exit;
			}
		}
	}
?>
	<script language="javascript">
		parent.location.href = "<?=$retUrl?>";
	</script>
