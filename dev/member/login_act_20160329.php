<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
?>

<?
	$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null; 
	$user_pw = isset($_REQUEST['user_pw']) ? $_REQUEST['user_pw'] : null; 
	$commute = isset($_REQUEST['commute_check']) ? $_REQUEST['commute_check'] : null; 
	$retUrl = isset($_REQUEST['retUrl']) ? $_REQUEST['retUrl'] : null; 

	$errMsg = "";
	$login_clear = "";

	$col_prs_id = "";
	$col_prf_id = "";
	$col_prs_name = "";
	$col_prs_team = "";
	$col_prs_position = "";
	$col_prs_email = "";
	$col_prs_tel = "";
	$col_prs_extension = "";
	$col_file_img = "";
	$col_prs_passwd = "";

	$sql = "SELECT 
				PRS_ID, PRF_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, PRS_EMAIL, PRS_TEL, PRS_EXTENSION, PRS_MEMO1, PRS_MEMO2, FILE_IMG, PRS_PASSWD
			FROM DF_PERSON WITH(NOLOCK) 
			WHERE PRS_LOGIN = '$user_id'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);

	if (sizeof($record) > 0)
	{
		$col_prs_id = $record['PRS_ID'];
		$col_prf_id = $record['PRF_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_team = $record['PRS_TEAM'];
		$col_prs_position = $record['PRS_POSITION'];
		$col_prs_email = $record['PRS_EMAIL'];
		$col_prs_tel = $record['PRS_TEL'];
		$col_prs_extension = $record['PRS_EXTENSION'];
		$col_file_img = $record['FILE_IMG'];
		$col_prs_passwd = $record['PRS_PASSWD'];
	}

	if ($col_prs_id == "") {
		$errMsg = "���̵� �������� �ʽ��ϴ�.";
		$login_clear = "id";
	}
	else
	{
		if ($user_pw != $col_prs_passwd) {
			$errMsg = "�н����尡 Ʋ�Ƚ��ϴ�. �ٽ� Ȯ���Ͽ� �ֽʽÿ�.";
			$login_clear = "pwd";
		}
		else
		{
			if ($col_prf_id == "5") {
				$errMsg = "���δ�� �����Դϴ�. ������ ���� �Ŀ� �̿밡���մϴ�.";
			}
			elseif ($col_prf_id == "6") {
				$errMsg = "Ż��� ȸ���Դϴ�. ������ ���� �Ŀ� �̿밡���մϴ�.";
			}
			else
			{
				// session 
				$_SESSION['SS_PRS_ID'] = $col_prs_id;

				$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
				$rs = sqlsrv_query($dbConn, $sql);

				$record = sqlsrv_fetch_array($rs);
				$checktime1 = $record['CHECKTIME1'];
				$checktime2 = $record['CHECKTIME2'];

/*##### �α� ���� #################################################
$log_txt = "------------------------------------\r\n";
$log_txt.= "Login Time: ".date("Y-m-d H:i:s")."\r\n";

$log_dir = "./log/";
$log_file = fopen($log_dir."log_".date('Ym')."_".$col_prs_name.".txt", "a");  
fwrite($log_file, $log_txt."\r\n");  
fclose($log_file);  
#################################################################*/

			}
		}
	}

	if ($errMsg != "") {
?>
		<script type="text/javascript">
			alert("<? echo $errMsg?>");
		<?
			if ($login_clear == "id")
			{
		?>
				parent.document.form.user_id.value = "";
				parent.document.form.user_pw.value = "";
				parent.document.form.user_id.focus();
		<?
			}
			else if ($login_clear == "pwd")
			{
		?>
				parent.document.form.user_pw.value = "";
				parent.document.form.user_pw.focus();
		<?
			}
			else
			{
		?>
				parent.location.href = "login.php";
		<?
			}
		?>
		</script>
<?
	}
	else
	{
		if ($commute == "1")
		{
			if (REMOTE_IP == "220.71.63.87") 
			{
				//��� üũ �α��� ��� ó��
				$now = date("YmdHis");								//���� ��� ����Ͻú���
				$now_time = substr($now,8,4);						//���� ��� �ð���
				$today = date("Y-m-d");								//���� ��¥
				$yesterday = date("Y-m-d",strtotime ("-1 day"));	//���� ��¥

				$ip = REMOTE_IP;									//����IP
				$gubun = "�����";

				if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

				//���� ���üũ Ȯ��
				$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
				$rs = sqlsrv_query($dbConn, $sql);

				$record = sqlsrv_fetch_array($rs);
				$yesterday_checktime1 = $record['CHECKTIME1'];
				$yesterday_checktime2 = $record['CHECKTIME2'];

				//��� �ߺ�üũ Ȯ��
				$sql = "SELECT CHECKTIME1 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE = '$today'";
				$rs = sqlsrv_query($dbConn,$sql);

				$record = sqlsrv_fetch_array($rs);
				if (sqlsrv_has_rows($rs) > 0)
				{
					$gubun1 = $record['GUBUN1'];
					$gubun2 = $record['GUBUN2'];
					$checktime1 = $record['CHECKTIME1'];
					$checktime2 = $record['CHECKTIME2'];

					if ((($gubun1 == "4" || $gubun1 == "8") && $checktime1 == "") || ($gubun2 == "9" || $gubun2 == "5"))
					{
					}
					else
					{
?>
						<script language="javascript">
							alert("�̹� ��� üũ �ϼ̽��ϴ�.");
							parent.location.href="/main.php";
						</script>
<?
						exit;
					}
				}
				else
				{
					$gubun1 = "1";
					$gubun2 = "";
				}

				$sql = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = '". str_replace('-','',$today) ."'";
				$rs = sqlsrv_query($dbConn,$sql);

				$record = sqlsrv_fetch_array($rs);
				$today_kind = $record['DATEKIND'];

				if ($today_kind == "BIZ")	//����
				{
					//ź�±ٹ���
					//�������� ó�� ���� ���� �ٹ��ð� Ȯ��
					$sql = "SELECT 
								A.DATEKIND, B.CHECKTIME1, B.CHECKTIME2, B.GUBUN2, B.OVERTIME 
							FROM 
								HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
							ON A.DATE = REPLACE(B.DATE,'-','') 
							WHERE B.PRS_ID = '$col_prs_id' AND B.DATE = '$yesterday'";
					$rs = sqlsrv_query($dbConn,$sql);

					$record = sqlsrv_fetch_array($rs);
					if (sqlsrv_has_rows($rs) > 0)
					{
						$yesterday_kind = $record['DATEKIND'];
						$fr_checktime = substr($record['CHECKTIME'],8,4); 
						$to_checktime = substr($record['CHECKTIME2'],8,4); 
						$yesterday_gubun2 = $record['GUBUN2'];
						$over_time = $record['OVERTIME'];
					}
					else
					{
						$yesterday_kind = "";
						$yesterday_gubun2 = "";
						$over_time = "0000";
					}

					if ($yesterday_kind == "BIZ")
					{
						if ($over_time >= "0700")						//�ٹ��ð�9�ð�+����ٹ�+7�ð� - ��������ð�
						{
							$start_time = "1400";
						}
						else if ($over_time >= "0600")					//�ٹ��ð�9�ð�+����ٹ�+6�ð� - ��������ð�
						{
							//$start_time = "1300";
							$start_time = "13". substr($over_time,2,2);
						}
						else if ($over_time >="0500")					//�ٹ��ð�9�ð�+����ٹ�+5�ð� - ��������ð�
						{
							//$start_time = "1200";
							$start_time = "12". substr($over_time,2,2);
						}
						else if ($over_time >="0400")					//�ٹ��ð�9�ð�+����ٹ�+4�ð� - ��������ð�
						{
							//$start_time = "1100";
							$start_time = "11". substr($over_time,2,2);
						}
						else
						{
							$start_time = "1100";					//��������ð���(0800~1100)
						}
					}
					else
					{
						if ($over_time >= "0900")						//���ϱٹ�9�ð�
						{
							$start_time = "1400";
						}
						else if ($over_time >= "0800")					//���ϱٹ�8�ð�
						{
							//$start_time = "1300";
							$start_time = "13". substr($over_time,2,2);
						}
						else if ($over_time >="0700")					//���ϱٹ�7�ð�
						{
							//$start_time = "1200";
							$start_time = "12". substr($over_time,2,2);
						}
						else if ($over_time >="0600")					//���ϱٹ�6�ð� - ��������ð�
						{
							//$start_time = "1100";
							$start_time = "11". substr($over_time,2,2);
						}
						else
						{
							$start_time = "1100";					//��������ð���(0800~1100)
						}
					}

					if ($now_time > $start_time)			//��������ð���(1) ���� ��� ��������(8)
					{
						$time_gubun = "8";
					}
					else
					{
						$time_gubun = "1";
					}
				}
				else	//�ָ�,������
				{
					$time_gubun = "1";
					$start_time = "";
				}
				//��ٽð� ���� - ũ������Ƽ��� 10��, �ְ��� 11��
				/*
				if ($col_prs_team == "Creative da") 
				{
					$C_da = "C1";

					if ($col_prs_name == "�ְ���")
					{
						$C_da = "C2";
					}

					$time_gubun = "1";
					if ($C_da = "C1" && $now_time > "1000")		//10��  ���� ��� - ����(5)
					{
						$time_gubun = "5";
					}
					if ($C_da = "C2" && $now_time > "1100")		//11��  ���� ��� - ����(5)
					{
						$time_gubun = "5";
					}
				}
				*/

				if (($gubun1 == "4" || $gubun1 == "8") && $checktime1 == "")
				{
					$sql = "UPDATE DF_CHECKTIME SET
								CHECKTIME1 = '$now', 
								CHECKIP1 = '$ip'
							WHERE 
								PRS_ID = '$col_prs_id' AND DATE = '$today'";
				}
				else if ($gubun2 == "5" || $gubun2 == "9")
				{
					$sql = "UPDATE DF_CHECKTIME SET
								GUBUN1 = '$time_gubun',
								CHECKTIME1 = '$now', 
								CHECKIP1 = '$ip'
							WHERE 
								PRS_ID = '$col_prs_id' AND DATE = '$today'";
				}
				else
				{
					$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_CHECKTIME WITH(NOLOCK)";
					$rs = sqlsrv_query($dbConn,$sql);

					$result = sqlsrv_fetch_array($rs);
					$maxno = $result[0] + 1;

					$sql = "INSERT INTO DF_CHECKTIME
							(SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN, GUBUN1, CHECKTIME1, CHECKIP1, START_TIME, FLAG, REGDATE)
							VALUES
							('$maxno','$col_prs_id','$user_id','$col_prs_name','$today','$gubun','$time_gubun','$now','$ip','$start_time','login',getdate())";
				}
				$rs = sqlsrv_query($dbConn,$sql);

				if ($rs == false)
				{
?>
				<script language="javascript">
					alert("���� �Ͽ����ϴ�. �������� ������ �ּ���.");
					parent.location.href="/main.php";
				</script>
<?
				}
				else
				{
					$sql2 = "SELECT GUBUN1, CHECKTIME1, START_TIME FROM DF_CHECKTIME WITH(NOLOCK) WHERE SEQNO = '$maxno'";
					$rs2 = sqlsrv_query($dbConn,$sql2);
				
					$record2 = sqlsrv_fetch_array($rs2);
					
					$chk_gubun1 = $record2['GUBUN1'];
					$chk_checktime1 = $record2['CHECKTIME1'];
					$chk_start_time = $record2['START_TIME'];

					if ($chk_gubun1 == "8" && substr($chk_checktime1,8,4) <= $chk_start_time)
					{
						$sql2 = "UPDATE CHECKTIME SET GUBUN1 = '1', CHK = 'Y' WHERE SEQNO = '$maxno'";
						$rs2 = sqlsrv_query($dbConn,$sql2);
					}
?>
					<script language="javascript">
					<? if ($checktime2 == "") { ?>
						alert("���üũ�� �Ϸ�Ǿ����ϴ�.\n���ٹ��� ���üũ�� ���� �ʾҽ��ϴ�.\n���ٹ��� ��ٽð��� �濵�������� �˷��ּ���.");
					<? } else { ?>
						alert("���üũ�� �Ϸ�Ǿ����ϴ�.");
					<? } ?>
						parent.location.href="/main.php";
					</script>
<?
				}
			}
			else
			{
?>
				<script type="text/javascript">
					alert("����� üũ�� CreativeDa������ �����մϴ�.");
					parent.location.href = "/main.php";
				</script>
<?
			}
		}
		else
		{
?>
		<script type="text/javascript">
		<? if ($retUrl == "") { ?>
			parent.location.href="/main.php";
		<? } else { ?>
			parent.location.href="<?=$retUrl?>";
		<? } ?>
		</script>
<?
		}
	}
?>
