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
		$errMsg = "아이디가 존재하지 않습니다.";
		$login_clear = "id";
	}
	else
	{
		if ($user_pw != $col_prs_passwd) {
			$errMsg = "패스워드가 틀렸습니다. 다시 확인하여 주십시오.";
			$login_clear = "pwd";
		}
		else
		{
			if ($col_prf_id == "5") {
				$errMsg = "승인대기 상태입니다. 관리자 승인 후에 이용가능합니다.";
			}
			elseif ($col_prf_id == "6") {
				$errMsg = "탈퇴된 회원입니다. 관리자 승인 후에 이용가능합니다.";
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

/*##### 로그 저장 #################################################
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
				//출근 체크 로그인 경우 처리
				$now = date("YmdHis");								//오늘 출근 년월일시분초
				$now_time = substr($now,8,4);						//오늘 출근 시간분
				$today = date("Y-m-d");								//오늘 날짜
				$yesterday = date("Y-m-d",strtotime ("-1 day"));	//어제 날짜

				$ip = REMOTE_IP;									//접속IP
				$gubun = "출퇴근";

				if (substr($now_time,0,2) == "24") { $now_time = "00". substr($now_time,2,2); }

				//전일 퇴근체크 확인
				$sql = "SELECT TOP 1 CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$col_prs_id' AND DATE < '".date("Y-m-d")."' ORDER BY SEQNO DESC";
				$rs = sqlsrv_query($dbConn, $sql);

				$record = sqlsrv_fetch_array($rs);
				$yesterday_checktime1 = $record['CHECKTIME1'];
				$yesterday_checktime2 = $record['CHECKTIME2'];

				//출근 중복체크 확인
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
							alert("이미 출근 체크 하셨습니다.");
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

				if ($today_kind == "BIZ")	//평일
				{
					//탄력근무제
					//오전반차 처리 위한 전일 근무시간 확인
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
						if ($over_time >= "0700")						//근무시간9시간+연장근무+7시간 - 출근인정시간
						{
							$start_time = "1400";
						}
						else if ($over_time >= "0600")					//근무시간9시간+연장근무+6시간 - 출근인정시간
						{
							//$start_time = "1300";
							$start_time = "13". substr($over_time,2,2);
						}
						else if ($over_time >="0500")					//근무시간9시간+연장근무+5시간 - 출근인정시간
						{
							//$start_time = "1200";
							$start_time = "12". substr($over_time,2,2);
						}
						else if ($over_time >="0400")					//근무시간9시간+연장근무+4시간 - 출근인정시간
						{
							//$start_time = "1100";
							$start_time = "11". substr($over_time,2,2);
						}
						else
						{
							$start_time = "1100";					//출근인정시간대(0800~1100)
						}
					}
					else
					{
						if ($over_time >= "0900")						//휴일근무9시간
						{
							$start_time = "1400";
						}
						else if ($over_time >= "0800")					//휴일근무8시간
						{
							//$start_time = "1300";
							$start_time = "13". substr($over_time,2,2);
						}
						else if ($over_time >="0700")					//휴일근무7시간
						{
							//$start_time = "1200";
							$start_time = "12". substr($over_time,2,2);
						}
						else if ($over_time >="0600")					//휴일근무6시간 - 출근인정시간
						{
							//$start_time = "1100";
							$start_time = "11". substr($over_time,2,2);
						}
						else
						{
							$start_time = "1100";					//출근인정시간대(0800~1100)
						}
					}

					if ($now_time > $start_time)			//출근인정시간대(1) 이후 출근 오전반차(8)
					{
						$time_gubun = "8";
					}
					else
					{
						$time_gubun = "1";
					}
				}
				else	//주말,공휴일
				{
					$time_gubun = "1";
					$start_time = "";
				}
				//출근시간 예외 - 크리에이티브다 10시, 최가희 11시
				/*
				if ($col_prs_team == "Creative da") 
				{
					$C_da = "C1";

					if ($col_prs_name == "최가희")
					{
						$C_da = "C2";
					}

					$time_gubun = "1";
					if ($C_da = "C1" && $now_time > "1000")		//10시  이후 출근 - 지각(5)
					{
						$time_gubun = "5";
					}
					if ($C_da = "C2" && $now_time > "1100")		//11시  이후 출근 - 지각(5)
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
					alert("실패 하였습니다. 개발팀에 문의해 주세요.");
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
						alert("출근체크가 완료되었습니다.\n전근무일 퇴근체크가 되지 않았습니다.\n전근무일 퇴근시간을 경영지원팀에 알려주세요.");
					<? } else { ?>
						alert("출근체크가 완료되었습니다.");
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
					alert("출퇴근 체크는 CreativeDa에서만 가능합니다.");
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
