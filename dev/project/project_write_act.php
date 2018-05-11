<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<? include INC_PATH."/top.php"; ?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "ING"; 

	$no = isset($_REQUEST['no']) ? $_REQUEST['no'] : null; 
	$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

	$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null;
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "write";

	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;
	$title_prev = isset($_REQUEST['title_prev']) ? $_REQUEST['title_prev'] : null; // 변경전 프로젝트명
	$connect = isset($_REQUEST['connect']) ? $_REQUEST['connect'] : null; 
	$link = isset($_REQUEST['link']) ? $_REQUEST['link'] : null; 
	$contents = isset($_REQUEST['contents']) ? $_REQUEST['contents'] : null;
	$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : null;
	$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : null;
	$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : null;
	$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : null;
	$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : null;
	$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : null;
	$progress = isset($_REQUEST['progress']) ? $_REQUEST['progress'] : null;
	$rows = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : null;

	if ($mode == "write" || $mode == "modify")
	{
		if (checkdate($fr_month,$fr_day,$fr_year) == 0)
		{
?>
		<script type="text/javascript">
			alert("프로젝트 시작일 <?=$fr_year?>년 <?=$fr_month?>월 <?=$fr_day?>일은 존재하지 않는 날짜입니다.");
		</script>
<?
				exit;
		}
		if (checkdate($to_month,$to_day,$to_year) == 0)
		{
?>
		<script type="text/javascript">
			alert("프로젝트 종료일 <?=$to_year?>년 <?=$to_month?>월 <?=$to_day?>일은 존재하지 않는 날짜입니다.");
		</script>
<?
				exit;
		}
	}

	if (strlen($fr_month)==1) { $fr_month = "0". $fr_month; }
	if (strlen($fr_day)==1) { $fr_day = "0". $fr_day; }
	if (strlen($to_month)==1) { $to_month = "0". $to_month; }
	if (strlen($to_day)==1) { $to_day = "0". $to_day; }

	$start_date = $fr_year ."-". $fr_month ."-". $fr_day;
	$end_date = $to_year ."-". $to_month ."-". $to_day;

	$title = str_replace("'","''",$title);
	$title_prev = str_replace("'","''",$title_prev);
	$contents = str_replace("'","''",$contents);

	if ($mode != "write")
	{
		if ($project_no == "")
		{
?>
	<script type="text/javascript">
		alert("해당 프로젝트가 존재하지 않습니다.");
		location.href="project_list.php?type=<?=$type?>";
	</script>
<?
			exit;
		}
	}

	if ($mode == "write")
	{
		$type_title = "등록";
		$retUrl = "project_list.php";

		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_PROJECT WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		if ($connect != "" && $link != "")
		{
			$sql = "SELECT TOP 1 PROJECT_NO FROM DF_PROJECT WITH(NOLOCK) WHERE PROJECT_NO <> '$link' AND PROJECT_NO Like '". $link ."%' ORDER BY PROJECT_NO DESC";
			$rs = sqlsrv_query($dbConn,$sql);
			
			if (sqlsrv_has_rows($rs) > 0) 
			{
				$record = sqlsrv_fetch_array($rs);
				$top_no = $record['PROJECT_NO'];
				//$new_no = substr($top_no,11,1)+1;
				$new_no = str_replace($link."-","",$top_no) + 1;

				$project = $link ."-". $new_no;
			}
			else
			{
				$project = $link ."-1";
			}
		}
		else
		{
			$sql = "SELECT TOP 1 PROJECT_NO FROM DF_PROJECT WITH(NOLOCK) WHERE PROJECT_NO Like 'DF". date("Y") ."%' ORDER BY PROJECT_NO DESC";
			$rs = sqlsrv_query($dbConn,$sql);
			
			if (sqlsrv_has_rows($rs) > 0) 
			{
				$record = sqlsrv_fetch_array($rs);
				$top_no = $record['PROJECT_NO'];
				$new_no = substr($top_no,7,3)+1;
				if (strlen($new_no) == 1) { $new_no = "00". $new_no; }
				if (strlen($new_no) == 2) { $new_no = "0". $new_no; }

				$project = "DF". date("Y") ."_". $new_no;
			}
			else
			{
				$project = "DF". date("Y") ."_001";
			}
		}

		$sql = "INSERT INTO DF_PROJECT 
				(SEQNO, PROJECT_NO, TITLE, CONTENTS, START_DATE, END_DATE, PROGRESS, STATUS, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, REG_DATE, USE_YN)
				VALUES
				('$seq','$project','$title','$contents','$start_date','$end_date','$progress','ING','$prs_id','$prs_login','$prs_name','$prs_position',getdate(),'Y')";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}

		$j = 0;
		for ($i=1; $i<=$rows; $i++) 
		{
			$detail_part = isset($_REQUEST['detail_part_'. $i]) ? $_REQUEST['detail_part_'. $i] : null;
			$detail_id = isset($_REQUEST['detail_id_'. $i]) ? $_REQUEST['detail_id_'. $i] : null;
			$detail_login = isset($_REQUEST['detail_login_'. $i]) ? $_REQUEST['detail_login_'. $i] : null;
			$detail_team = isset($_REQUEST['detail_team_'. $i]) ? $_REQUEST['detail_team_'. $i] : null;
			$detail_position = isset($_REQUEST['detail_position_'. $i]) ? $_REQUEST['detail_position_'. $i] : null;
			$detail_name = isset($_REQUEST['detail_name_'. $i]) ? $_REQUEST['detail_name_'. $i] : null;
			$detail_detail = isset($_REQUEST['detail_detail_'. $i]) ? $_REQUEST['detail_detail_'. $i] : null;
			$detail_rate = isset($_REQUEST['part_rate_'. $i]) ? $_REQUEST['part_rate_'. $i] : null;
			$detail_fr_year = isset($_REQUEST['detail_fr_year_'. $i]) ? $_REQUEST['detail_fr_year_'. $i] : null;
			$detail_fr_month = isset($_REQUEST['detail_fr_month_'. $i]) ? $_REQUEST['detail_fr_month_'. $i] : null;
			$detail_fr_day = isset($_REQUEST['detail_fr_day_'. $i]) ? $_REQUEST['detail_fr_day_'. $i] : null;
			$detail_to_year = isset($_REQUEST['detail_to_year_'. $i]) ? $_REQUEST['detail_to_year_'. $i] : null;
			$detail_to_month = isset($_REQUEST['detail_to_month_'. $i]) ? $_REQUEST['detail_to_month_'. $i] : null;
			$detail_to_day = isset($_REQUEST['detail_to_day_'. $i]) ? $_REQUEST['detail_to_day_'. $i] : null;

			$detail_detail = str_replace("'","''",$detail_detail);

			if ($detail_id != "")
			{
				$j = $j + 1;

				for ($a=0; $a<5; $a++)
				{
					if (!empty($detail_fr_year[$a]))
					{
						$a_fr_year = $detail_fr_year[$a]; 
						$a_fr_month = $detail_fr_month[$a];
						$a_fr_day = $detail_fr_day[$a];
						$a_to_year = $detail_to_year[$a]; 
						$a_to_month = $detail_to_month[$a];
						$a_to_day = $detail_to_day[$a];

						if (checkdate($a_fr_month,$a_fr_day,$a_fr_year) == 0)
						{
					?>
						<script type="text/javascript">
							alert("<?=$detail_name?> <?=$detail_position?>의 <?=$a_fr_year?>년 <?=$a_fr_month?>월 <?=$a_fr_day?>일은 존재하지 않는 날짜입니다.");
						</script>
					<?
								exit;
						}
						if (checkdate($a_to_month,$a_to_day,$a_to_year) == 0)
						{
					?>
						<script type="text/javascript">
							alert("<?=$detail_name?> <?=$detail_position?>의 <?=$a_to_year?>년 <?=$a_to_month?>월 <?=$a_to_day?>일은 존재하지 않는 날짜입니다.");
						</script>
					<?
								exit;
						}

						if (strlen($a_fr_month)==1) { $a_fr_month = "0". $a_fr_month; }
						if (strlen($a_fr_day)==1) { $a_fr_day = "0". $a_fr_day; }
						if (strlen($a_to_month)==1) { $a_to_month = "0". $a_to_month; }
						if (strlen($a_to_day)==1) { $a_to_day = "0". $a_to_day; }

						$detail_start_date = $a_fr_year ."-". $a_fr_month ."-". $a_fr_day;
						$detail_end_date = $a_to_year ."-". $a_to_month ."-". $a_to_day;

						$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_PROJECT_DETAIL WITH(NOLOCK)";
						$rs = sqlsrv_query($dbConn,$sql);

						$result = sqlsrv_fetch_array($rs);
						$dseq = $result[0] + 1;

						$sql = "INSERT INTO DF_PROJECT_DETAIL 
								(SEQNO, PROJECT_NO, PART, DETAIL, START_DATE, END_DATE, PART_RATE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, SORT)
								VALUES
								('$dseq','$project','$detail_part','$detail_detail','$detail_start_date','$detail_end_date','$detail_rate','$detail_id','$detail_login',
									'$detail_name','$detail_position','$j')";
						$rs = sqlsrv_query($dbConn,$sql);

						if ($rs == false)
						{
		?>
						<script type="text/javascript">
							alert("Error2. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
						</script>
		<?
							exit;
						}
					}
				}
			}
		}
	}
	else if ($mode == "modify")
	{
		$type_title = "수정";

		if ($connect != "" && $link != "")
		{
			$sql = "SELECT TOP 1 PROJECT_NO FROM DF_PROJECT WITH(NOLOCK) WHERE PROJECT_NO <> '$link' AND PROJECT_NO Like '". $link ."%' ORDER BY PROJECT_NO DESC";
			$rs = sqlsrv_query($dbConn,$sql);
			
			if (sqlsrv_has_rows($rs) > 0) 
			{
				$record = sqlsrv_fetch_array($rs);
				$top_no = $record['PROJECT_NO'];
				//$new_no = substr($top_no,11,1)+1;
				$new_no = str_replace($link."-","",$top_no) + 1;

				$project = $link ."-". $new_no;
			}
			else
			{
				$project = $link ."-1";
			}

			$sql = "UPDATE DF_PROJECT SET PROJECT_NO = '$project' WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script type="text/javascript">
				alert("Error1_1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "UPDATE DF_PROJECT_DETAIL SET PROJECT_NO = '$project' WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script type="text/javascript">
				alert("Error1_2. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "UPDATE DF_PROJECT_EXPENSE SET PROJECT_NO = '$project' WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script type="text/javascript">
				alert("Error1_3. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "UPDATE DF_PROJECT_INCOME SET PROJECT_NO = '$project' WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script type="text/javascript">
				alert("Error1_4. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "UPDATE DF_APPROVAL SET PROJECT_NO = '$project' WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script type="text/javascript">
				alert("Error1_5. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "UPDATE DF_WEEKLY_DETAIL SET PROJECT_NO = '$project' WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);

			if ($rs == false)
			{
?>
			<script type="text/javascript">
				alert("Error1_6. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}

			$sql = "INSERT INTO DF_PROJECT_CHANGE 
					(ORG_PROJECT_NO, CHG_PROJECT_NO, CHG_DATE, PRS_ID, PRS_LOGIN, PRS_NAME)
					VALUES
					('$project_no','$project',getdate(),'$prs_id','$prs_login','$prs_name')";
			$rs = sqlsrv_query($dbConn,$sql);

			$project_no = $project;
		}

		$retUrl = "project_detail.php?page=". $page ."&type=". $type ."&no=". $no ."&name=". $name ."&project_no=". $project_no;

		$sql = "INSERT INTO DF_PROJECT_LOG 
				SELECT *, '$prs_id', '$prs_login', '$prs_name', '$prs_position', getdate() 
				FROM DF_PROJECT 
				WHERE PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);

		$sql = "UPDATE DF_PROJECT SET 
					TITLE = '$title', 
					CONTENTS = '$contents', 
					START_DATE = '$start_date', 
					END_DATE = '$end_date', 
					PROGRESS = '$progress' 
				WHERE 
					PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
		else
		{
			// 프로젝트명이 변경 된 경우
			if ($title != $title_prev) {
				// 참여자 리스트 추출
				$sql = "SELECT
							PRS_LOGIN, PRS_NAME
						FROM 
							DF_PROJECT_DETAIL WITH(NOLOCK) 
						WHERE
							PROJECT_NO = '$project_no'";
				$rs = sqlsrv_query($dbConn,$sql);

				while ($record=sqlsrv_fetch_array($rs))	{				
					$mailList .= $record['PRS_LOGIN'].'@designfever.com'.',';
				}

				// 참여자에게 일괄 메일발송 ///////////////////////////////////////////////////////////////
				if (true) {
					$nameFrom = "DF";
					$mailFrom = "system@designfever.com";
					$nameTo = "";
					$mailTo = substr($mailList,0,-1);
					$cc = "";
					$bcc = "";
					$subject = "[DF WORK] 프로젝트명이 변경 되었습니다.";
					$content = "<style type='text/css'>p {font-size:12px;}</style>";
					$content.= "<p>프로젝트명이 아래와 같이 변경 되었습니다.<br><br></p>";
					$content.= "<p><b>+ ".$title_prev." → ".$title."<br></p>
								<p><a href='http://work.designfever.com/project/project_detail.php?project_no=".$project_no."' target='_blank'>자세히 보기..</a><br></p>";

					$charset = "EUC-KR";
					$nameFrom = "=?$charset?B?".base64_encode($nameFrom)."?=";
					$nameTo = "=?$charset?B?".base64_encode($nameTo)."?=";
					$subject = "=?$charset?B?".base64_encode($subject)."?=";

					$header  = "Content-Type: text/html; charset=euc-kr\r\n";
					$header .= "MIME-Version: 1.0\r\n";
					$header .= "Return-Path: <". $mailFrom .">\r\n";
					$header .= "From: ". $nameFrom ." <". $mailFrom .">\r\n";
					$header .= "Reply-To: <". $mailFrom .">\r\n";
					if ($cc)  $header .= "Cc: ". $cc ."\r\n";
					if ($bcc) $header .= "Bcc: ". $bcc ."\r\n";

					$result = mail($mailTo, $subject, $content, $header, $mailFrom);
				}
				///////////////////////////////////////////////////////////////////////////////////////////
			}
		}

		$sql = "INSERT INTO DF_PROJECT_DETAIL_LOG 
				SELECT *, '$prs_id', '$prs_login', '$prs_name', '$prs_position', getdate() 
				FROM DF_PROJECT_DETAIL 
				WHERE PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);
	
		$sql = "DELETE FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);

		$j = 0;
		for ($i=1; $i<=$rows; $i++) 
		{
			$detail_part = isset($_REQUEST['detail_part_'. $i]) ? $_REQUEST['detail_part_'. $i] : null;
			$detail_id = isset($_REQUEST['detail_id_'. $i]) ? $_REQUEST['detail_id_'. $i] : null;
			$detail_login = isset($_REQUEST['detail_login_'. $i]) ? $_REQUEST['detail_login_'. $i] : null;
			$detail_team = isset($_REQUEST['detail_team_'. $i]) ? $_REQUEST['detail_team_'. $i] : null;
			$detail_position = isset($_REQUEST['detail_position_'. $i]) ? $_REQUEST['detail_position_'. $i] : null;
			$detail_name = isset($_REQUEST['detail_name_'. $i]) ? $_REQUEST['detail_name_'. $i] : null;
			$detail_detail = isset($_REQUEST['detail_detail_'. $i]) ? $_REQUEST['detail_detail_'. $i] : null;
			$detail_rate = isset($_REQUEST['part_rate_'. $i]) ? $_REQUEST['part_rate_'. $i] : null;

			$detail_fr_year = isset($_REQUEST['detail_fr_year_'. $i]) ? $_REQUEST['detail_fr_year_'. $i] : null;
			$detail_fr_month = isset($_REQUEST['detail_fr_month_'. $i]) ? $_REQUEST['detail_fr_month_'. $i] : null;
			$detail_fr_day = isset($_REQUEST['detail_fr_day_'. $i]) ? $_REQUEST['detail_fr_day_'. $i] : null;
			$detail_to_year = isset($_REQUEST['detail_to_year_'. $i]) ? $_REQUEST['detail_to_year_'. $i] : null;
			$detail_to_month = isset($_REQUEST['detail_to_month_'. $i]) ? $_REQUEST['detail_to_month_'. $i] : null;
			$detail_to_day = isset($_REQUEST['detail_to_day_'. $i]) ? $_REQUEST['detail_to_day_'. $i] : null;

			$detail_detail = str_replace("'","''",$detail_detail);

			if ($detail_id != "")
			{
				$j = $j + 1;

				for ($a=0; $a<5; $a++)
				{
					if (!empty($detail_fr_year[$a]))
					{
						$a_fr_year = $detail_fr_year[$a]; 
						$a_fr_month = $detail_fr_month[$a];
						$a_fr_day = $detail_fr_day[$a];
						$a_to_year = $detail_to_year[$a]; 
						$a_to_month = $detail_to_month[$a];
						$a_to_day = $detail_to_day[$a];

						if (checkdate($a_fr_month,$a_fr_day,$a_fr_year) == 0)
						{
					?>
						<script type="text/javascript">
							alert("<?=$detail_name?> <?=$detail_position?>의 <?=$a_fr_year?>년 <?=$a_fr_month?>월 <?=$a_fr_day?>일은 존재하지 않는 날짜입니다.");
						</script>
					<?
								exit;
						}
						if (checkdate($a_to_month,$a_to_day,$a_to_year) == 0)
						{
					?>
						<script type="text/javascript">
							alert("<?=$detail_name?> <?=$detail_position?>의 <?=$a_to_year?>년 <?=$a_to_month?>월 <?=$a_to_day?>일은 존재하지 않는 날짜입니다.");
						</script>
					<?
								exit;
						}

						if (strlen($a_fr_month)==1) { $a_fr_month = "0". $a_fr_month; }
						if (strlen($a_fr_day)==1) { $a_fr_day = "0". $a_fr_day; }
						if (strlen($a_to_month)==1) { $a_to_month = "0". $a_to_month; }
						if (strlen($a_to_day)==1) { $a_to_day = "0". $a_to_day; }

						$detail_start_date = $a_fr_year ."-". $a_fr_month ."-". $a_fr_day;
						$detail_end_date = $a_to_year ."-". $a_to_month ."-". $a_to_day;

						$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_PROJECT_DETAIL WITH(NOLOCK)";
						$rs = sqlsrv_query($dbConn,$sql);

						$result = sqlsrv_fetch_array($rs);
						$dseq = $result[0] + 1;

						$sql = "INSERT INTO DF_PROJECT_DETAIL 
								(SEQNO, PROJECT_NO, PART, DETAIL, START_DATE, END_DATE, PART_RATE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, SORT)
								VALUES
								('$dseq','$project_no','$detail_part','$detail_detail','$detail_start_date','$detail_end_date','$detail_rate','$detail_id','$detail_login',
									'$detail_name','$detail_position','$j')";
						$rs = sqlsrv_query($dbConn,$sql);

						if ($rs == false)
						{
		?>
						<script type="text/javascript">
							alert("Error2. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
						</script>
		<?
							exit;
						}
					}
				
				}
			}
/*
			if (strlen($detail_fr_month)==1) { $detail_fr_month = "0". $detail_fr_month; }
			if (strlen($detail_fr_day)==1) { $detail_fr_day = "0". $detail_fr_day; }
			if (strlen($detail_to_month)==1) { $detail_to_month = "0". $detail_to_month; }
			if (strlen($detail_to_day)==1) { $detail_to_day = "0". $detail_to_day; }

			$detail_start_date = $detail_fr_year ."-". $detail_fr_month ."-". $detail_fr_day;
			$detail_end_date = $detail_to_year ."-". $detail_to_month ."-". $detail_to_day;

			if ($detail_id != "")
			{
				$j = $j + 1;

				$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_PROJECT_DETAIL WITH(NOLOCK)";
				$rs = sqlsrv_query($dbConn,$sql);

				$result = sqlsrv_fetch_array($rs);
				$dseq = $result[0] + 1;

				$sql = "INSERT INTO DF_PROJECT_DETAIL 
						(SEQNO, PROJECT_NO, PART, DETAIL, START_DATE, END_DATE, PART_RATE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, SORT)
						VALUES
						('$dseq','$project_no','$detail_part','$detail_detail','$detail_start_date','$detail_end_date','$detail_rate','$detail_id','$detail_login',
							'$detail_name','$detail_position','$j')";
				$rs = sqlsrv_query($dbConn,$sql);

				if ($rs == false)
				{
?>
				<script type="text/javascript">
					alert("Error2. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
				</script>
<?
					exit;
				}
			}
*/
		}
	}
	else if ($mode == "delete")
	{
		$type_title = "삭제";
		$retUrl = "project_list.php?page=". $page ."&type=". $type ."&no=". $no ."&name=". $name;

		$sql = "INSERT INTO DF_PROJECT_LOG 
				SELECT *, '$prs_id', '$prs_login', '$prs_name', '$prs_position', getdate() 
				FROM DF_PROJECT 
				WHERE PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);

		$del_project_no = $project_no . "_";

		$sql = "UPDATE DF_PROJECT SET USE_YN = 'N', PROJECT_NO = '$del_project_no' WHERE PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
		else
		{
			$sql = "UPDATE DF_PROJECT_DETAIL SET PROJECT_NO = '$del_project_no' WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);
		}

	}
	else if ($mode == "end")
	{
		$type_title = "프로젝트 완료";
		$retUrl = "project_detail.php?page=". $page ."&type=END&no=". $no ."&name=". $name ."&project_no=". $project_no;

		$sql = "UPDATE DF_PROJECT SET STATUS = 'END' WHERE PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
		else
		{
			$sql = "INSERT INTO DF_PROJECT_LOG 
					SELECT *, '$prs_id', '$prs_login', '$prs_name', '$prs_position', getdate() 
					FROM DF_PROJECT 
					WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);
?>
		<script type="text/javascript">
			$("#btnEND").attr("disabled",true);
		</script>
<?
		}

	}
	else if ($mode == "ing")
	{
		$type_title = "프로젝트 완료 취소";
		$retUrl = "project_detail.php?page=". $page ."&type=ING&no=". $no ."&name=". $name ."&project_no=". $project_no;

		$sql = "UPDATE DF_PROJECT SET STATUS = 'ING' WHERE PROJECT_NO = '$project_no'";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script type="text/javascript">
			alert("Error1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
		else
		{
			$sql = "INSERT INTO DF_PROJECT_LOG 
					SELECT *, '$prs_id', '$prs_login', '$prs_name', '$prs_position', getdate() 
					FROM DF_PROJECT 
					WHERE PROJECT_NO = '$project_no'";
			$rs = sqlsrv_query($dbConn,$sql);
?>
		<script type="text/javascript">
			$("#btnEND").attr("disabled",true);
		</script>
<?
		}

	}
?>

	<script type="text/javascript">
		//alert("<?=$type_title?> 되었습니다.");
		parent.location.href = "<?=$retUrl?>";
	</script>
