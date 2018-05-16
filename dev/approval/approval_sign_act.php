<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/KISA_SHA256.php";
?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : null; 
	$pwd = isset($_REQUEST['pwd']) ? $_REQUEST['pwd'] : null; 
	$pwd_txt = isset($_REQUEST['pwd_txt']) ? $_REQUEST['pwd_txt'] : null; 
	$sign = isset($_REQUEST['sign']) ? $_REQUEST['sign'] : null; 
	$sign2 = $sign;

	$new_pwd = kisa_sha256($pwd_txt);

	$retUrl = "approval_detail.php?doc_no=". $doc_no;

	if ($pwd == "Y")
	{
		$sql = "SELECT PRS_PWD FROM DF_PERSON WITH(NOLOCK) WHERE PRS_ID = '$prs_id'";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);
		$prs_pwd = $record['PRS_PWD'];

		if ($new_pwd == $prs_pwd) { 
		}
		else
		{
?>
	<script type="text/javascript">
		alert("비밀번호가 잘못 입력되었습니다.");
	</script>
<?
			exit;
		}
	}

	$sql = "SELECT MAX(A_ORDER) AS MAX_ORDER FROM DF_APPROVAL_TO WITH(NOLOCK) WHERE DOC_NO = '$doc_no'";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	$max = $record['MAX_ORDER'];

	if ($order < $max && $sign2 == "결재") { $sign2 = "진행중"; }

	if ($doc_no == "")
	{
?>
<script type="text/javascript">
	alert("해당 문서가 존재하지 않습니다.");
</script>
<?
		exit;
	}

	$sql = "UPDATE DF_APPROVAL_TO SET 
				A_STATUS = '$sign', 
				A_REG_DATE = getdate()
			WHERE 
				DOC_NO = '$doc_no' AND A_ORDER = '$order'";
	$rs = sqlsrv_query($dbConn, $sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("error 1. 결재 실패 하였습니다.");
	</script>
<?
		exit;
	}

	$sql = "UPDATE DF_APPROVAL SET 
				STATUS = '$sign2' 
			WHERE 
				DOC_NO = '$doc_no'";
	$rs = sqlsrv_query($dbConn, $sql);

	if ($rs == false)
	{
?>
	<script language="javascript">
		alert("error 2. 결재 실패 하였습니다.");
	</script>
<?
		exit;
	}

	//근태 정보 입력
	if ($sign2 == "결재" || $sign2 == "전결")
	{
		$sql = "SELECT 
					FORM_CATEGORY, FORM_TITLE, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_POSITION
					, CONVERT(char(10),START_DATE,120) AS FR_DATE, CONVERT(char(10),END_DATE,120) AS TO_DATE, CONVERT(char(20),REG_DATE,120) AS APPROVAL_DATE
				FROM 
					DF_APPROVAL WITH(NOLOCK) 
				WHERE 
					DOC_NO = '$doc_no'
				ORDER BY 
					SEQNO";
		$rs = sqlsrv_query($dbConn, $sql);

		while ($record = sqlsrv_fetch_array($rs))
		{
			$form_category = $record['FORM_CATEGORY'];
			$form_title = $record['FORM_TITLE'];
			$p_id = $record['PRS_ID'];
			$p_name = $record['PRS_NAME'];
			$p_login = $record['PRS_LOGIN'];
			$p_position = $record['PRS_POSITION'];
			$fr_date = $record['FR_DATE'];
			$to_date = $record['TO_DATE'];
			$approval_date = $record['APPROVAL_DATE'];

			$business_trip = "N";

			if ($form_category == "출장계" || $form_category == "휴가계")
			{
				switch($form_title)
				{
					case "출장계" :
						$gubun = "출퇴근";
						$gubun1 = "6";
						$gubun2 = "6";
						$checktime1 = "0900";
						$checktime2 = "1800";
						$totaltime = "0900";
						$business_trip = "Y";
						break;
					case "휴가 소진시" :
						$gubun = "휴가";
						$gubun1 = "10";
						$gubun2 = "10";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "연차" :
						$gubun = "휴가";
						$gubun1 = "10";
						$gubun2 = "10";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "프로젝트" :
						$gubun = "P휴가";
						$gubun1 = "16";
						$gubun2 = "16";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "병가" :
						$gubun = "병가";
						$gubun1 = "11";
						$gubun2 = "11";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "오전반차" :
						$gubun = "출퇴근";
						$gubun1 = "8";
						$gubun2 = "";
						$checktime1 = "";
						$checktime2 = "";
						$totaltime = "0000";
						break;
					case "오후반차" :
						$gubun = "출퇴근";
						$gubun1 = "";
						$gubun2 = "9";
						$checktime1 = "";
						$checktime2 = "";
						$totaltime = "0000";
						break;
					case "휴가 소진시 오전반차" :
						$gubun = "출퇴근";
						$gubun1 = "8";
						$gubun2 = "";
						$checktime1 = "";
						$checktime2 = "";
						$totaltime = "0000";
						break;
					case "휴가 소진시 오후반차" :
						$gubun = "출퇴근";
						$gubun1 = "";
						$gubun2 = "9";
						$checktime1 = "";
						$checktime2 = "";
						$totaltime = "0000";
						break;
					case "프로젝트 오전반차" :
						$gubun = "출퇴근";
						$gubun1 = "4";
						$gubun2 = "";
						$checktime1 = "";
						$checktime2 = "";
						$totaltime = "0000";
						break;
					case "프로젝트 오후반차" :
						$gubun = "출퇴근";
						$gubun1 = "";
						$gubun2 = "5";
						$checktime1 = "";
						$checktime2 = "";
						$totaltime = "0000";
						break;
					case "리프레쉬" :
						$gubun = "R휴가";
						$gubun1 = "17";
						$gubun2 = "17";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "무급" :
						$gubun = "무급휴가";
						$gubun1 = "18";
						$gubun2 = "18";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "경조사" :
						$gubun = "경조사";
						$gubun1 = "12";
						$gubun2 = "12";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "예비군/민방위" :
						$gubun = "예비군";
						$gubun1 = "19";
						$gubun2 = "19";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "출산휴가" :
						$gubun = "출산휴가";
						$gubun1 = "20";
						$gubun2 = "20";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "육아휴직" :
						$gubun = "육아휴직";
						$gubun1 = "21";
						$gubun2 = "21";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
					case "기타" :
						$gubun = "기타";
						$gubun1 = "13";
						$gubun2 = "13";
						$checktime1 = "0000";
						$checktime2 = "2400";
						$totaltime = "0000";
						break;
				}
				$overtime = "0000";
				$undertime = "0000";
				$memo1 = $p_position . " " . $p_name;
				$memo2 = str_replace(' ','',str_replace('-','',str_replace(':','',substr($approval_date,0,19))));
				$memo3 = "전자결재 (". $doc_no .")";
				$pay1 = "N";
				$pay2 = "N";
				$pay3 = "N";
				$pay4 = "N";

				$chk_id = $p_id;
				$chk_login = $p_login;
				$chk_name = $p_name;
				
				if ($form_category == "출장계") 
				{
					$sql1 = "SELECT P_PRS_ID, P_PRS_LOGIN, P_PRS_NAME FROM DF_APPROVAL_PARTNER WITH(NOLOCK) WHERE DOC_NO = '$doc_no'";
					$rs1 = sqlsrv_query($dbConn,$sql1);

					while ($record1 = sqlsrv_fetch_array($rs1))
					{
						$chk_id = $chk_id .",". $record1['P_PRS_ID'];
						$chk_login = $chk_login .",". $record1['P_PRS_LOGIN'];
						$chk_name = $chk_name .",". $record1['P_PRS_NAME'];
					}
				}

				//$fr_date ~ $to_date insert
				$sql1 = "SELECT DATE, DATEKIND FROM HOLIDAY WITH(NOLOCK) 
							WHERE DATE BETWEEN '". str_replace('-','',$fr_date) ."' AND '". str_replace('-','',$to_date) ."' ORDER BY DATE";
				$rs1 = sqlsrv_query($dbConn,$sql1);

				while ($record1 = sqlsrv_fetch_array($rs1))
				{
					$date = $record1['DATE'];
					$datekind = $record1['DATEKIND'];

					$p_date = substr($date,0,4) ."-". substr($date,4,2) ."-". substr($date,6,2);

					if ($datekind == "BIZ")
					{
						$Arr_chk_id = explode(",",$chk_id);
						$Arr_chk_login = explode(",",$chk_login);
						$Arr_chk_name = explode(",",$chk_name);

						for ($i=0; $i<sizeof($Arr_chk_id); $i++)
						{
							$sql2 = "SELECT ISNULL(COUNT(SEQNO),0) FROM DF_CHECKTIME WITH(NOLOCK) WHERE PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";
							$rs2 = sqlsrv_query($dbConn,$sql2);

							$result = sqlsrv_fetch_array($rs2);
							$chk = $result[0];

							if ($chk == 0)
							{
								$sql3 = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_CHECKTIME WITH(NOLOCK)";
								$rs3 = sqlsrv_query($dbConn,$sql3);

								$record3 = sqlsrv_fetch_array($rs3);
								$maxno = $record3[0] + 1;

								if ($checktime1 != "") 
								{
									$date_checktime1 = $date . $checktime1 . "00";
								}
								else 
								{
									$date_checktime1 = "";
								}
								if ($checktime2 != "") 
								{
									$date_checktime2 = $date . $checktime2 . "00";
								}
								else 
								{
									$date_checktime2 = "";
								}

								$sql3 = "INSERT INTO DF_CHECKTIME
										(SEQNO, PRS_ID, PRS_LOGIN, PRS_NAME, DATE, GUBUN, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, TOTALTIME, OVERTIME, UNDERTIME, MEMO1, MEMO2, MEMO3, FLAG, BUSINESS_TRIP, REGDATE)
										VALUES
										('$maxno','$Arr_chk_id[$i]','$Arr_chk_login[$i]','$Arr_chk_name[$i]','$p_date','$gubun','$gubun1','$gubun2','$date_checktime1','$date_checktime2','$totaltime','$overtime','$undertime','$memo1', '$memo2','$memo3','approval','$business_trip',getdate())";
								$rs3 = sqlsrv_query($dbConn,$sql3);

								if ($rs3 == false)
								{
	?>
								<script language="javascript">
									alert("error3. 근태 정보 입력에 실패 하였습니다.");
								</script>
	<?
									exit;
								}
							}
							else
							{
								if ($gubun2 == "5" || $gubun2 == "9") 
								{
									$sql3 = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = REPLACE(DATEADD(D,-1,'$p_date'),'-','')";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									$record3 = sqlsrv_fetch_array($rs3);

									$yesterday_kind = $record3['DATEKIND'];

									$sql3 = "SELECT OVERTIME FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE = DATEADD(D,-1,'$p_date') AND PRS_ID = '$Arr_chk_id[$i]'";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									$record3 = sqlsrv_fetch_array($rs3);

									$yesterday_overtime = $record3['OVERTIME'];

									$sql3 = "SELECT CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE = '$p_date' AND PRS_ID = '$Arr_chk_id[$i]'";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									$record3 = sqlsrv_fetch_array($rs3);

									$today_checktime1 = $record3['CHECKTIME1'];
									$today_checktime2 = $record3['CHECKTIME2'];

									if ($today_checktime2 == "" )
									{
										$sql3 = "UPDATE DF_CHECKTIME SET 
													GUBUN = '$gubun', GUBUN2 = '$gubun2', MEMO1 = '$memo1', MEMO2 = '$memo2', MEMO3 = '$memo3' 
												WHERE 
													PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";
										$rs3 = sqlsrv_query($dbConn,$sql3);

										if ($rs3 == false)
										{
			?>
										<script language="javascript">
											alert("error3. 근태 정보 입력에 실패 하였습니다.");
										</script>
			<?
											exit;
										}
									}
									else
									{
										if ($yesterday_kind == "BIZ")
										{
											if ($yesterday_overtime >= "0700") { $d_time = "0000"; }
											else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0100"; }
											else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0200"; }
											else { $d_time = "0300";}

											$max_overtime = "0700";
											$min_overtime = "0400";
										}
										else
										{
											if ($yesterday_overtime >= "0900") { $d_time = "0000"; }
											else if ($yesterday_overtime >= "0800" && $yesterday_overtime < "0900" ) { $d_time = "0100"; }
											else if ($yesterday_overtime >= "0700" && $yesterday_overtime < "0800" ) { $d_time = "0200"; }
											else { $d_time = "0300";}

											$max_overtime = "0900";
											$min_overtime = "0600";
										}

										if (substr($today_checktime1,8,2) < "08")
										{
											if (substr($today_checktime2,10,2) < substr($today_checktime1,10,2))
											{
												$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2) - 1;
												$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2) + 60;
											}
											else
											{
												$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2);
												$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2);
											}

											$totalhour2 = substr($today_checktime2,8,2) - 8;
											$totalmin2 = substr($today_checktime2,10,2);
										}
										else
										{
											if (substr($today_checktime1,8,2) < "13" && $today_gubun1 == "8")
											{
												if (substr($today_checktime2,10,2) < substr($today_checktime1,10,2))
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2) - 1;
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2) + 60;
												}
												else
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2);
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2);
												}

												$totalhour2 = substr($today_checktime2,8,2) - 13;
												$totalmin2 = substr($today_checktime2,10,2);
											}
											else
											{
												if (substr($today_checktime2,10,2) < substr($today_checktime1,10,2))
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2) - 1;
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2) + 60;
												}
												else
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2);
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2);
												}
												$totalhour2 = $totalhour;
												$totalmin2 = $totalmin;
											}
										}
										if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
										if (strlen($totalhour2) == 1) { $totalhour2 = "0". $totalhour2; }
										if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
										if (strlen($totalmin2) == 1) { $totalmin2 = "0". $totalmin2; }
										$totaltime = $totalhour . $totalmin;
										$totaltime2 = $totalhour2 . $totalmin2;

										if ($yesterday_overtime >= $max_overtime)
										{
											if ($totaltime2 >= "0000")
											{
												$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
												$overmin = substr($totaltime2,2,2);
												if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
												if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
												$overtime = $overhour . $overmin;

												$undertime = "0000";
											}
											else if ($totaltime2 < "0000")
											{
												$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
												$undermin = 60 - substr($totaltime2,2,2);
												if ($undermin == 60) 
												{  
													$underhour = $underhour + 1;
													$undermin = "00";
												}
												if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
												if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
												$undertime = $underhour . $undermin;

												$overtime = "0000";
											}
										}
										else if ($yesterday_overtime < $min_overtime)
										{
											if ($totaltime2 >= "0300")
											{
												$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
												$overmin = substr($totaltime2,2,2);
												if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
												if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
												$overtime = $overhour . $overmin;

												$undertime = "0000";
											}
											else if ($totaltime2 < "0300")
											{
												$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
												$undermin = 60 - substr($totaltime2,2,2);
												if ($undermin == 60) 
												{  
													$underhour = $underhour + 1;
													$undermin = "00";
												}
												if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
												if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
												$undertime = $underhour . $undermin;

												$overtime = "0000";
											}
										}
										else
										{
											$d_hour = substr($d_time,0,2) - 1;
											$d_min = 60 - substr($yesterday_overtime,2,2);

											if ($d_min == 60)
											{
												$d_hour = $d_hour + 1;
												$d_min = "00";
											}
											if (strlen($d_hour) == 1) { $d_hour = "0". $d_hour; }
											if (strlen($d_min) == 1) { $d_min = "0". $d_min; }
											$d_time = $d_hour . $d_min;

											if ($totaltime2 > $d_time)
											{
												if (substr($totaltime2,2,2) < substr($d_time,2,2))
												{
													$overhour = substr($totaltime2,0,2) - substr($d_time,0,2) - 1;
													$overmin = 60 + substr($totaltime2,2,2) - substr($d_time,2,2);
												}
												else
												{
													$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
													$overmin = substr($totaltime2,2,2) - substr($d_time,2,2);
												}
												if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
												if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
												$overtime = $overhour . $overmin;

												$undertime = "0000";
											}
											else if ($totaltime2 < $d_time)
											{
												if (substr($d_time,2,2) < substr($totaltime2,2,2))
												{
													$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
													$undermin = 60 + substr($d_time,2,2) - substr($totaltime2,2,2);
												}
												else
												{
													$underhour = substr($d_time,0,2) - substr($totaltime2,0,2);
													$undermin = substr($d_time,2,2) - substr($totaltime2,2,2);
												}
												if ($undermin == 60) 
												{  
													$underhour = $underhour + 1;
													$undermin = "00";
												}
												if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
												if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
												$undertime = $underhour . $undermin;

												$overtime = "0000";
											}
											else
											{
												$overtime = "0000";
												$undertime = "0000";
											}
										}

										if ($overtime >= "0400") { $pay1 = "Y"; }
										if ($overtime >= "0600") { $pay2 = "Y"; }
										if ($overtime >= "0700") { $pay3 = "Y"; }
										if ($overtime >= "1000") { $pay4 = "Y"; }
										if ($overtime >= "0700" && substr($today_checktime2,8,4) >= "2400" && substr($today_checktime2,8,4) <= "3000") { $pay4 = "Y"; }

										$sql3 = "INSERT INTO DF_CHECKTIME_LOG 
												SELECT * 
												FROM DF_CHECKTIME
												WHERE PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";
										$rs3 = sqlsrv_query($dbConn,$sql3);

										$sql3 = "UPDATE DF_CHECKTIME SET 
													TOTALTIME = '$totaltime', OVERTIME = '$overtime', UNDERTIME = '$undertime', GUBUN = '$gubun', GUBUN2 = '$gubun2', 
													MEMO1 = '$memo1', MEMO2 = '$memo2', MEMO3 = '$memo3', PAY1 = '$pay1', PAY2 = '$pay2', PAY3 = '$pay3', PAY4 = '$pay4' 
												WHERE 
													PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";

										$rs3 = sqlsrv_query($dbConn,$sql3);

										if ($rs3 == false)
										{
			?>
										<script language="javascript">
											alert("error3. 근태 정보 입력에 실패 하였습니다.");
										</script>
			<?
											exit;
										}
									}
								}
								else if ($gubun1 == "4" || $gubun1 == "8") 
								{
									$sql3 = "SELECT DATEKIND FROM HOLIDAY WITH(NOLOCK) WHERE DATE = REPLACE(DATEADD(D,-1,'$p_date'),'-','')";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									$record3 = sqlsrv_fetch_array($rs3);

									$yesterday_kind = $record3['DATEKIND'];

									$sql3 = "SELECT OVERTIME FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE = DATEADD(D,-1,'$p_date') AND PRS_ID = '$Arr_chk_id[$i]'";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									$record3 = sqlsrv_fetch_array($rs3);

									$yesterday_overtime = $record3['OVERTIME'];
									if ($yesterday_overtime == "") { $yesterday_overtime = "0000"; }

									$sql3 = "SELECT CHECKTIME1, CHECKTIME2 FROM DF_CHECKTIME WITH(NOLOCK) WHERE DATE = '$p_date' AND PRS_ID = '$Arr_chk_id[$i]'";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									$record3 = sqlsrv_fetch_array($rs3);

									$today_checktime1 = $record3['CHECKTIME1'];
									$today_checktime2 = $record3['CHECKTIME2'];

									if ($today_checktime2 == "" )
									{
										$sql3 = "UPDATE DF_CHECKTIME SET 
													GUBUN = '$gubun', GUBUN1 = '$gubun1', MEMO1 = '$memo1', MEMO2 = '$memo2', MEMO3 = '$memo3' 
												WHERE 
													PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";
										$rs3 = sqlsrv_query($dbConn,$sql3);

										if ($rs3 == false)
										{
			?>
										<script language="javascript">
											alert("error3. 근태 정보 입력에 실패 하였습니다.");
										</script>
			<?
											exit;
										}
									}
									else
									{
										if ($yesterday_kind == "BIZ")
										{
											if ($yesterday_overtime >= "0700") { $d_time = "0200"; }
											else if ($yesterday_overtime >= "0600" && $yesterday_overtime < "0700" ) { $d_time = "0300"; }
											else if ($yesterday_overtime >= "0500" && $yesterday_overtime < "0600" ) { $d_time = "0400"; }
											else { $d_time = "0500";}

											$max_overtime = "0700";
											$min_overtime = "0400";
										}
										else
										{
											if ($yesterday_overtime >= "0900") { $d_time = "0200"; }
											else if ($yesterday_overtime >= "0800" && $yesterday_overtime < "0900" ) { $d_time = "0300"; }
											else if ($yesterday_overtime >= "0700" && $yesterday_overtime < "0800" ) { $d_time = "0400"; }
											else { $d_time = "0500";}

											$max_overtime = "0900";
											$min_overtime = "0600";
										}

										$gubun2 = "3";

										if (substr($today_checktime1,8,2) < "08")
										{
											if (substr($today_checktime2,10,2) < substr($today_checktime1,10,2))
											{
												$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2) - 1;
												$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2) + 60;
											}
											else
											{
												$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2);
												$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2);
											}

											$totalhour2 = substr($today_checktime2,8,2) - 8;
											$totalmin2 = substr($today_checktime2,10,2);
										}
										else
										{
											if (substr($today_checktime1,8,2) < "13" && $today_gubun1 == "8")
											{
												if (substr($today_checktime2,10,2) < substr($today_checktime1,10,2))
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2) - 1;
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2) + 60;
												}
												else
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2);
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2);
												}

												$totalhour2 = substr($today_checktime2,8,2) - 13;
												$totalmin2 = substr($today_checktime2,10,2);
											}
											else
											{
												if (substr($today_checktime2,10,2) < substr($today_checktime1,10,2))
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2) - 1;
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2) + 60;
												}
												else
												{
													$totalhour = substr($today_checktime2,8,2) - substr($today_checktime1,8,2);
													$totalmin = substr($today_checktime2,10,2) - substr($today_checktime1,10,2);
												}
												$totalhour2 = $totalhour;
												$totalmin2 = $totalmin;
											}
										}
										if (strlen($totalhour) == 1) { $totalhour = "0". $totalhour; }
										if (strlen($totalhour2) == 1) { $totalhour2 = "0". $totalhour2; }
										if (strlen($totalmin) == 1) { $totalmin = "0". $totalmin; }
										if (strlen($totalmin2) == 1) { $totalmin2 = "0". $totalmin2; }
										$totaltime = $totalhour . $totalmin;
										$totaltime2 = $totalhour2 . $totalmin2;

										if ($yesterday_overtime >= $max_overtime)
										{
											if ($totaltime2 >= "0000")
											{
												$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
												$overmin = substr($totaltime2,2,2);
												if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
												if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
												$overtime = $overhour . $overmin;

												$undertime = "0000";
											}
											else if ($totaltime2 < "0000")
											{
												$gubun2 = "2";

												$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
												$undermin = 60 - substr($totaltime2,2,2);
												if ($undermin == 60) 
												{  
													$underhour = $underhour + 1;
													$undermin = "00";
												}
												if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
												if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
												$undertime = $underhour . $undermin;

												$overtime = "0000";
											}
										}
										else if ($yesterday_overtime < $min_overtime)
										{
											if ($totaltime2 >= "0300")
											{
												$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
												$overmin = substr($totaltime2,2,2);
												if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
												if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
												$overtime = $overhour . $overmin;

												$undertime = "0000";
											}
											else if ($totaltime2 < "0300")
											{
												$gubun2 = "2";

												$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
												$undermin = 60 - substr($totaltime2,2,2);
												if ($undermin == 60) 
												{  
													$underhour = $underhour + 1;
													$undermin = "00";
												}
												if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
												if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
												$undertime = $underhour . $undermin;

												$overtime = "0000";
											}
										}
										else
										{
											$d_hour = substr($d_time,0,2) - 1;
											$d_min = 60 - substr($yesterday_overtime,2,2);

											if ($d_min == 60)
											{
												$d_hour = $d_hour + 1;
												$d_min = "00";
											}
											if (strlen($d_hour) == 1) { $d_hour = "0". $d_hour; }
											if (strlen($d_min) == 1) { $d_min = "0". $d_min; }
											$d_time = $d_hour . $d_min;

											if ($totaltime2 > $d_time)
											{
												if (substr($totaltime2,2,2) < substr($d_time,2,2))
												{
													$overhour = substr($totaltime2,0,2) - substr($d_time,0,2) - 1;
													$overmin = 60 + substr($totaltime2,2,2) - substr($d_time,2,2);
												}
												else
												{
													$overhour = substr($totaltime2,0,2) - substr($d_time,0,2);
													$overmin = substr($totaltime2,2,2) - substr($d_time,2,2);
												}
												if (strlen($overhour) == 1) { $overhour = "0". $overhour; }
												if (strlen($overmin) == 1) { $overmin = "0". $overmin; }
												$overtime = $overhour . $overmin;

												$undertime = "0000";
											}
											else if ($totaltime2 < $d_time)
											{
												$gubun2 = "2";

												if (substr($d_time,2,2) < substr($totaltime2,2,2))
												{
													$underhour = substr($d_time,0,2) - substr($totaltime2,0,2) - 1;
													$undermin = 60 + substr($d_time,2,2) - substr($totaltime2,2,2);
												}
												else
												{
													$underhour = substr($d_time,0,2) - substr($totaltime2,0,2);
													$undermin = substr($d_time,2,2) - substr($totaltime2,2,2);
												}
												if ($undermin == 60) 
												{  
													$underhour = $underhour + 1;
													$undermin = "00";
												}
												if (strlen($underhour) == 1) { $underhour = "0". $underhour; }
												if (strlen($undermin) == 1) { $undermin = "0". $undermin; }
												$undertime = $underhour . $undermin;

												$overtime = "0000";
											}
											else
											{
												$gubun2 = "2";

												$overtime = "0000";
												$undertime = "0000";
											}
										}

										if ($overtime >= "0400") { $pay1 = "Y"; }
										if ($overtime >= "0600") { $pay2 = "Y"; }
										if ($overtime >= "0700") { $pay3 = "Y"; }
										if ($overtime >= "1000") { $pay4 = "Y"; }
										if ($overtime >= "0700" && substr($today_checktime2,8,4) >= "2400" && substr($today_checktime2,8,4) <= "3000") { $pay4 = "Y"; }

										$sql3 = "INSERT INTO DF_CHECKTIME_LOG 
												SELECT * 
												FROM DF_CHECKTIME
												WHERE PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";
										$rs3 = sqlsrv_query($dbConn,$sql3);

										$sql3 = "UPDATE DF_CHECKTIME SET 
													TOTALTIME = '$totaltime', OVERTIME = '$overtime', UNDERTIME = '$undertime', GUBUN = '$gubun', GUBUN1 = '$gubun1', GUBUN2 = '$gubun2', 
													MEMO1 = '$memo1', MEMO2 = '$memo2', MEMO3 = '$memo3', PAY1 = '$pay1', PAY2 = '$pay2', PAY3 = '$pay3', PAY4 = '$pay4', FLAG = '$flag', BUSINESS_TRIP = '$business_trip' 
												WHERE 
													PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";

										$rs3 = sqlsrv_query($dbConn,$sql3);

										if ($rs3 == false)
										{
			?>
										<script language="javascript">
											alert("error3. 근태 정보 입력에 실패 하였습니다.");
										</script>
			<?
											exit;
										}
									}
								}
								else
								{
									$sql3 = "INSERT INTO DF_CHECKTIME_LOG 
											SELECT * 
											FROM DF_CHECKTIME
											WHERE PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									$sql3 = "UPDATE DF_CHECKTIME SET 
												GUBUN = '$gubun', GUBUN1 = '$gubun1', GUBUN2 = '$gubun2', MEMO1 = '$memo1', MEMO2 = '$memo2', MEMO3 = '$memo3' 
											WHERE 
												PRS_ID = '$Arr_chk_id[$i]' AND DATE = '$p_date'";
									$rs3 = sqlsrv_query($dbConn,$sql3);

									if ($rs3 == false)
									{
			?>
									<script language="javascript">
										alert("error3. 근태 정보 입력에 실패 하였습니다.");
									</script>
			<?
										exit;
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>
	<script language="javascript">
		top.location.href = "javascript:funView('<?=$doc_no?>');";
	</script>
