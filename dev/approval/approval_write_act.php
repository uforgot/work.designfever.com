<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null;
	$form_no = isset($_REQUEST['form_no']) ? $_REQUEST['form_no'] : null;

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  

	if ($type == "write") {
		
//		if ($form_no == "3" && $prf_id == "3" && $prs_id != "57") {
//			$status = "전결";
//		} else  {
			$status = "미결재"; 
//		}

		$type_title = "등록"; 
		$retUrl = "approval_my_list.php";

		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE DOC_NO Like '". date("ym") ."-%' ORDER BY DOC_NO DESC";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);

		if (sizeof($record) > 0) {
			$max_no = substr($record['DOC_NO'],5,4);
			$new_no = $max_no + 1;

			if (strlen($new_no) == 1) { $new_no = "000". $new_no; }
			else if (strlen($new_no) == 2) { $new_no = "00". $new_no; }
			else if (strlen($new_no) == 3) { $new_no = "0". $new_no; }

			$doc_no = date("ym") ."-". $new_no;
		} else {
			$doc_no = date("ym") ."-0001";
		}
	
	} else if ($type == "save") { 

		$status = "임시"; 
		$type_title = "임시저장"; 
		$retUrl = "approval_my_list_save.php";

		$sql = "SELECT TOP 1 DOC_NO FROM DF_APPROVAL WITH(NOLOCK) WHERE DOC_NO Like 'SAVE-". date("ym") ."-%' ORDER BY DOC_NO DESC";
		$rs = sqlsrv_query($dbConn, $sql);

		$record = sqlsrv_fetch_array($rs);

		if (sizeof($record) > 0) {
			$max_no = substr($record['DOC_NO'],10,4);
			$new_no = $max_no + 1;

			if (strlen($new_no) == 1) { $new_no = "000". $new_no; }
			else if (strlen($new_no) == 2) { $new_no = "00". $new_no; }
			else if (strlen($new_no) == 3) { $new_no = "0". $new_no; }

			$doc_no = "SAVE-". date("ym") ."-". $new_no;
		} else {
			$doc_no = "SAVE-". date("ym") ."-0001";
		}

	} else if ($type == "delete") {

		$type_title = "삭제"; 
		$retUrl = "approval_my_list_save.php";

		$sql = "UPDATE DF_APPROVAL SET 
					USE_YN = 'N'
				WHERE 
					DOC_NO = '$doc_no'";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false)
		{
?>
		<script language="javascript">
			alert("<?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}
		else
		{
?>
		<script language="javascript">
			top.location.href = "javascript:funSearch(top.document.form,'approval_my_list_save.php');";
		</script>
<?
		}

		exit;
	}

	$form_category = isset($_REQUEST['form_category']) ? $_REQUEST['form_category'] : "비용품의서(v2)"; 

	if ($form_category == "휴가계" )
	{
		$form_title = isset($_REQUEST['form_title']) ? $_REQUEST['form_title'] : "연차/프로젝트"; 
	}
	else
	{
		$form_title = $form_category; 
	}

	if ($form_title == "리프레쉬") {

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
			alert("리프레쉬 휴가는 최대 5일까지 사용 가능합니다.");
		</script>
<?
			exit;
		}
	}

	if ($form_title == "연차/프로젝트") {

		$vacation1 = isset($_REQUEST['vacation1']) ? $_REQUEST['vacation1'] : "";  
		$fr_year1 = isset($_REQUEST['fr_year1']) ? $_REQUEST['fr_year1'] : date("Y"); 
		$fr_month1 = isset($_REQUEST['fr_month1']) ? $_REQUEST['fr_month1'] : date("m"); 
		if (strlen($fr_month1) == 1) { $fr_month1 = "0". $fr_month1; }
		$fr_day1 = isset($_REQUEST['fr_day1']) ? $_REQUEST['fr_day1'] : date("d"); 
		if (strlen($fr_day1) == 1) { $fr_day1 = "0". $fr_day1; }
		$to_year1 = isset($_REQUEST['to_year1']) ? $_REQUEST['to_year1'] : date("Y"); 
		$to_month1 = isset($_REQUEST['to_month1']) ? $_REQUEST['to_month1'] : date("m"); 
		if (strlen($to_month1) == 1) { $to_month1 = "0". $to_month1; }
		$to_day1 = isset($_REQUEST['to_day1']) ? $_REQUEST['to_day1'] : date("d"); 
		if (strlen($to_day1) == 1) { $to_day1 = "0". $to_day1; }

		$fr_date1 = $fr_year1 ."-". $fr_month1 ."-". $fr_day1;
		$to_date1 = $to_year1 ."-". $to_month1 ."-". $to_day1;

		$vacation2 = isset($_REQUEST['vacation2']) ? $_REQUEST['vacation2'] : "";  
		$fr_year2 = isset($_REQUEST['fr_year2']) ? $_REQUEST['fr_year2'] : date("Y"); 
		$fr_month2 = isset($_REQUEST['fr_month2']) ? $_REQUEST['fr_month2'] : date("m"); 
		if (strlen($fr_month2) == 1) { $fr_month2 = "0". $fr_month2; }
		$fr_day2 = isset($_REQUEST['fr_day2']) ? $_REQUEST['fr_day2'] : date("d"); 
		if (strlen($fr_day2) == 1) { $fr_day2 = "0". $fr_day2; }
		$to_year2 = isset($_REQUEST['to_year2']) ? $_REQUEST['to_year2'] : date("Y"); 
		$to_month2 = isset($_REQUEST['to_month2']) ? $_REQUEST['to_month2'] : date("m"); 
		if (strlen($to_month2) == 1) { $to_month2 = "0". $to_month2; }
		$to_day2 = isset($_REQUEST['to_day2']) ? $_REQUEST['to_day2'] : date("d"); 
		if (strlen($to_day2) == 1) { $to_day2 = "0". $to_day2; }

		$fr_date2 = $fr_year2 ."-". $fr_month2 ."-". $fr_day2;
		$to_date2 = $to_year2 ."-". $to_month2 ."-". $to_day2;

		if ($vacation1 == "연차" && $vacation2 == "프로젝트") {
			if (($fr_date2 >= $fr_date1 && $fr_date2 <= $to_date1) || ($to_date2 >= $fr_date1 && $to_date2 <= $to_date1)) {
?>
			<script language="javascript">
				alert("연차와 프로젝트 휴가 일자가 겹칩니다.\n날짜를 다시 선택해 주세요.");
			</script>
<?
				exit;
			}

			if (($fr_date1 >= $fr_date2 && $fr_date1 <= $to_date2) || ($to_date1 >= $fr_date2 && $to_date1 <= $to_date2)) {
?>
			<script language="javascript">
				alert("연차와 프로젝트 휴가 일자가 겹칩니다.\n날짜를 다시 선택해 주세요.");
			</script>
<?
				exit;
			}
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

	if (strlen($up_day) == 1) { $up_day = "0". $up_day; }

	$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null;
	$team_name = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;

	$maxSize = 10*1024*1024;        // 업로드 파일 최대 크기 지정 (10MB)

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

	$myFile1_Err = $_FILES['file_1']['error'];
	$myFile2_Err = $_FILES['file_2']['error'];
	$myFile3_Err = $_FILES['file_3']['error'];

	$myFile1_Name = "";
	$myFile2_Name = "";
	$myFile3_Name = "";

	if ($myFile1_Err == "1" || $myFile2_Err == "1" || $myFile3_Err == "1") {
?>
		<script language="javascript">
			alert("파일용량이 너무 큽니다.\n10MB 이내의 파일을 다시 선택해 주세요.");
		</script>
<?
		exit;
	}

	if ($myFile1_Size > 0) {
		if ($myFile1_Size > $maxSize) {
?>
			<script language="javascript">
				alert("파일용량이 너무 큽니다.\n10MB 이내의 파일을 다시 선택해 주세요.");
			</script>
<?
			exit;
		} else {
			$myFile1_Type_check = explode('.',$myFile1_FileName);			
			$myFile1_Type = $myFile1_Type_check[count($myFile1_Type_check)-1];	//파일 확장자
			for ($i=0; $i<count($myFile1_Type_check)-1; $i++) {
				$myFile1_Name .= $myFile1_Type_check[$i];						//확장자 제외 파일명
			}

			$exist_flag = 0;
			if (file_exists(APPROVAL_DIR.$myFile1_Name.".".$myFile1_Type)) {	//파일 존재여부 체크
				$i = 1;
				while ($exist_flag != 1) {
					if (!file_exists(APPROVAL_DIR.$myFile1_Name."_".$i.".".$myFile1_Type)) {
						$exist_flag = 1;
						$myFile1_Real = $myFile1_Name."_".$i.".".$myFile1_Type;
					}
					$i++;
				}
			} else {
				$myFile1_Real = $myFile1_FileName;
			}

			move_uploaded_file($myFile1_Temp, APPROVAL_DIR.$myFile1_Real);	//파일 저장

		}
	}

	if ($myFile2_Size > 0) {
		if ($myFile2_Size > $maxSize) {
?>
			<script language="javascript">
				alert("파일용량이 너무 큽니다.\n10MB 이내의 파일을 다시 선택해 주세요.");
			</script>
<?
			exit;
		} else {
			$myFile2_Type_check = explode('.',$myFile2_FileName);			
			$myFile2_Type = $myFile2_Type_check[count($myFile2_Type_check)-1];	//파일 확장자
			for ($i=0; $i<count($myFile2_Type_check)-1; $i++)
			{
				$myFile2_Name .= $myFile2_Type_check[$i];						//확장자 제외 파일명
			}

			$exist_flag = 0;
			if (file_exists(APPROVAL_DIR.$myFile2_Name.".".$myFile2_Type)) {	//파일 존재여부 체크
				$i = 1;
				while ($exist_flag != 1) {
					if (!file_exists(APPROVAL_DIR.$myFile2_Name."_".$i.".".$myFile2_Type)) {
						$exist_flag = 1;
						$myFile2_Real = $myFile2_Name."_".$i.".".$myFile2_Type;
					}
					$i++;
				}
			} else {
				$myFile2_Real = $myFile2_FileName;
			}

			move_uploaded_file($myFile2_Temp, APPROVAL_DIR.$myFile2_Real);	//파일 저장

		}
	}

	if ($myFile3_Size > 0) {
		if ($myFile3_Size > $maxSize) {
?>
			<script language="javascript">
				alert("파일용량이 너무 큽니다.\n10MB 이내의 파일을 다시 선택해 주세요.");
			</script>
<?
			exit;
		} else {
			$myFile3_Type_check = explode('.',$myFile3_FileName);			
			$myFile3_Type = $myFile3_Type_check[count($myFile3_Type_check)-1];	//파일 확장자
			for ($i=0; $i<count($myFile3_Type_check)-1; $i++) {
				$myFile3_Name .= $myFile3_Type_check[$i];						//확장자 제외 파일명
			}

			$exist_flag = 0;
			if (file_exists(APPROVAL_DIR.$myFile3_Name.".".$myFile3_Type)) {	//파일 존재여부 체크
				$i = 1;
				while ($exist_flag != 1) {
					if (!file_exists(APPROVAL_DIR.$myFile3_Name."_".$i.".".$myFile3_Type)) {
						$exist_flag = 1;
						$myFile3_Real = $myFile3_Name."_".$i.".".$myFile3_Type;
					}
					$i++;
				}
			} else {
				$myFile3_Real = $myFile3_FileName;
			}

			move_uploaded_file($myFile3_Temp, APPROVAL_DIR.$myFile3_Real);	//파일 저장

		}
	}

	$myFile1_Real = str_replace("'","''",$myFile1_Real);
	$myFile2_Real = str_replace("'","''",$myFile2_Real);
	$myFile3_Real = str_replace("'","''",$myFile3_Real);

	if ($form_title == "연차/프로젝트") {

		$vacation = isset($_REQUEST['vacation1']) ? $_REQUEST['vacation1'] : "";  

		if ($vacation == "연차") {
		
			$fr_year = isset($_REQUEST['fr_year1']) ? $_REQUEST['fr_year1'] : date("Y"); 
			$fr_month = isset($_REQUEST['fr_month1']) ? $_REQUEST['fr_month1'] : date("m"); 
			if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
			$fr_day = isset($_REQUEST['fr_day1']) ? $_REQUEST['fr_day1'] : date("d"); 
			if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
			$to_year = isset($_REQUEST['to_year1']) ? $_REQUEST['to_year1'] : date("Y"); 
			$to_month = isset($_REQUEST['to_month1']) ? $_REQUEST['to_month1'] : date("m"); 
			if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
			$to_day = isset($_REQUEST['to_day1']) ? $_REQUEST['to_day1'] : date("d"); 
			if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

			$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
			$to_date = $to_year ."-". $to_month ."-". $to_day;

			$sql = "SELECT ISNULL(COUNT(*),0) FROM HOLIDAY WITH(NOLOCK) WHERE DATE BETWEEN '". str_replace("-","",$fr_date) ."' AND '". str_replace("-","",$to_date) ."' AND DATEKIND = 'BIZ'";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$days = $result[0];

			$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_APPROVAL WITH(NOLOCK)";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$seq = $result[0] + 1;

			$sql = "INSERT INTO DF_APPROVAL
					(SEQNO, DOC_NO, FORM_NO, FORM_CATEGORY, FORM_TITLE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, APPROVAL_DATE, REG_DATE, 
						TITLE, CONTENTS, START_DATE, END_DATE, USE_DAY, OPEN_YN, FILE_1, FILE_2, FILE_3, STATUS, PAYMENT_YN, REPLY_CNT, USE_YN)
					VALUES 
					('$seq','$doc_no','$form_no','$form_category','연차','$prs_id','$prs_login','$prs_name','$prs_team','$prs_position','$approval_date',getdate(),
						'$title','$contents','$fr_date','$to_date','$days','$open_yn','$myFile1_Real', '$myFile2_Real', '$myFile3_Real','$status','미지급','0','Y')";
			$rs = sqlsrv_query($dbConn, $sql);
			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 1. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}

		$vacation = isset($_REQUEST['vacation2']) ? $_REQUEST['vacation2'] : ""; 
		
		if ($vacation == "프로젝트") {

			$fr_year = isset($_REQUEST['fr_year2']) ? $_REQUEST['fr_year2'] : date("Y"); 
			$fr_month = isset($_REQUEST['fr_month2']) ? $_REQUEST['fr_month2'] : date("m"); 
			if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
			$fr_day = isset($_REQUEST['fr_day2']) ? $_REQUEST['fr_day2'] : date("d"); 
			if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
			$to_year = isset($_REQUEST['to_year2']) ? $_REQUEST['to_year2'] : date("Y"); 
			$to_month = isset($_REQUEST['to_month2']) ? $_REQUEST['to_month2'] : date("m"); 
			if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
			$to_day = isset($_REQUEST['to_day2']) ? $_REQUEST['to_day2'] : date("d"); 
			if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

			$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
			$to_date = $to_year ."-". $to_month ."-". $to_day;

			$sql = "SELECT ISNULL(COUNT(*),0) FROM HOLIDAY WITH(NOLOCK) WHERE DATE BETWEEN '". str_replace("-","",$fr_date) ."' AND '". str_replace("-","",$to_date) ."' AND DATEKIND = 'BIZ'";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$days = $result[0];

			$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_APPROVAL WITH(NOLOCK)";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$seq = $result[0] + 1;

			$sql = "INSERT INTO DF_APPROVAL
					(SEQNO, DOC_NO, FORM_NO, FORM_CATEGORY, FORM_TITLE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, APPROVAL_DATE, REG_DATE, 
						TITLE, CONTENTS, START_DATE, END_DATE, USE_DAY, OPEN_YN, FILE_1, FILE_2, FILE_3, STATUS, PAYMENT_YN, REPLY_CNT, USE_YN)
					VALUES 
					('$seq','$doc_no','$form_no','$form_category','프로젝트','$prs_id','$prs_login','$prs_name','$prs_team','$prs_position','$approval_date',getdate(),
						'$title','$contents','$fr_date','$to_date','$days','$open_yn','$myFile1_Real', '$myFile2_Real', '$myFile3_Real','$status','미지급','0','Y')";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false)
			{
?>
			<script language="javascript">
				alert("error 2 . <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}

	} else {

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

		if (strpos($form_title,"반차") == false ) {
			$sql = "SELECT ISNULL(COUNT(*),0) FROM HOLIDAY WITH(NOLOCK) WHERE DATE BETWEEN '". str_replace("-","",$fr_date) ."' AND '". str_replace("-","",$to_date) ."' AND DATEKIND = 'BIZ'";
			$rs = sqlsrv_query($dbConn,$sql);

			$result = sqlsrv_fetch_array($rs);
			$days = $result[0];
		} else {
			$days = 0.5;
			$to_date = $fr_date;
		}

		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_APPROVAL WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		$sql = "INSERT INTO DF_APPROVAL
				(SEQNO, DOC_NO, FORM_NO, FORM_CATEGORY, FORM_TITLE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, APPROVAL_DATE, REG_DATE,
					TITLE, CONTENTS, START_DATE, END_DATE, USE_DAY, OPEN_YN, FILE_1, FILE_2, FILE_3, STATUS, PAYMENT_YN, REPLY_CNT, USE_YN, PROJECT_NO, TEAM_NAME)
				VALUES 
				('$seq','$doc_no','$form_no','$form_category','$form_title','$prs_id','$prs_login','$prs_name','$prs_team','$prs_position','$approval_date',getdate(),
				'$title','$contents','$fr_date','$to_date','$days','$open_yn','$myFile1_Real', '$myFile2_Real', '$myFile3_Real','$status','미지급','0','Y','$project_no','$team_name')";
		$rs = sqlsrv_query($dbConn, $sql);

		if ($rs == false) {
?>
		<script language="javascript">
			alert("error 3. <?=$type_title?> 실패 하였습니다. 개발팀에 문의해 주세요.");
		</script>
<?
			exit;
		}

		if ($form_title == "비용품의서(v2)") {

			for ($i=0; $i<5; $i++) {
				${'type_'.$i}		= isset($_REQUEST['type_'.$i]) ? $_REQUEST['type_'.$i] : null;    
				${'money_'.$i}		= isset($_REQUEST['money_'.$i]) ? $_REQUEST['money_'.$i] : null;    
				${'tax_'.$i}		= isset($_REQUEST['tax_'.$i]) ? $_REQUEST['tax_'.$i] : null;    
				${'target_'.$i}		= isset($_REQUEST['target_'.$i]) ? $_REQUEST['target_'.$i] : null;    
				${'pay_type_'.$i}	= isset($_REQUEST['pay_type_'.$i]) ? $_REQUEST['pay_type_'.$i] : null;    
				${'pay_info_'.$i}	= isset($_REQUEST['pay_info_'.$i]) ? $_REQUEST['pay_info_'.$i] : null;    
				${'company_'.$i}	= isset($_REQUEST['company_'.$i]) ? $_REQUEST['company_'.$i] : null;     
				${'manager_'.$i}	= isset($_REQUEST['manager_'.$i]) ? $_REQUEST['manager_'.$i] : null;    
				${'contact_'.$i}	= isset($_REQUEST['contact_'.$i]) ? $_REQUEST['contact_'.$i] : null;    
				${'bank_name_'.$i}	= isset($_REQUEST['bank_name_'.$i]) ? $_REQUEST['bank_name_'.$i] : null;    
				${'bank_num_'.$i}	= isset($_REQUEST['bank_num_'.$i]) ? $_REQUEST['bank_num_'.$i] : null;    
				${'bank_user_'.$i}	= isset($_REQUEST['bank_user_'.$i]) ? $_REQUEST['bank_user_'.$i] : null;    
				${'pay_date_'.$i}	= isset($_REQUEST['pay_date_'.$i]) ? $_REQUEST['pay_date_'.$i] : null;    
				${'memo_'.$i}		= isset($_REQUEST['memo_'.$i]) ? $_REQUEST['memo_'.$i] : null;    
				${'idx_'.$i}		= isset($_REQUEST['idx_'.$i]) ? $_REQUEST['idx_'.$i] : 0;
			}

			$expense_type		= $type_0 ."##". $type_1 ."##". $type_2 ."##". $type_3 ."##". $type_4; 
			$expense_money		= $money_0 ."##". $money_1 ."##". $money_2 ."##". $money_3 ."##". $money_4; 
			$expense_tax		= $tax_0 ."##". $tax_1 ."##". $tax_2 ."##". $tax_3 ."##". $tax_4; 
			$expense_target		= $target_0 ."##". $target_1 ."##". $target_2 ."##". $target_3 ."##". $target_4; 
			$expense_pay_type	= $pay_type_0 ."##". $pay_type_1 ."##". $pay_type_2 ."##". $pay_type_3 ."##". $pay_type_4; 
			$expense_pay_info	= $pay_info_0 ."##". $pay_info_1 ."##". $pay_info_2 ."##". $pay_info_3 ."##". $pay_info_4; 
			$expense_company	= $company_0 ."##". $company_1 ."##". $company_2 ."##". $company_3 ."##". $company_4; 
			$expense_manager	= $manager_0 ."##". $manager_1 ."##". $manager_2 ."##". $manager_3 ."##". $manager_4; 
			$expense_contact	= $contact_0 ."##". $contact_1 ."##". $contact_2 ."##". $contact_3 ."##". $contact_4; 
			$expense_bank_name	= $bank_name_0 ."##". $bank_name_1 ."##". $bank_name_2 ."##". $bank_name_3 ."##". $bank_name_4; 
			$expense_bank_num	= $bank_num_0 ."##". $bank_num_1 ."##". $bank_num_2 ."##". $bank_num_3 ."##". $bank_num_4; 
			$expense_bank_user	= $bank_user_0 ."##". $bank_user_1 ."##". $bank_user_2 ."##". $bank_user_3 ."##". $bank_user_4; 
			$expense_pay_date	= $pay_date_0 ."##". $pay_date_1 ."##". $pay_date_2 ."##". $pay_date_3 ."##". $pay_date_4; 
			$expense_memo		= $memo_0 ."##". $memo_1 ."##". $memo_2 ."##". $memo_3 ."##". $memo_4; 
			$expense_idx		= $idx_0 ."##". $idx_1 ."##". $idx_2 ."##". $idx_3 ."##". $idx_4; 

			$expense_type_arr		= explode("##",$expense_type);
			$expense_money_arr		= explode("##",$expense_money);
			$expense_tax_arr		= explode("##",$expense_tax);
			$expense_target_arr		= explode("##",$expense_target);
			$expense_pay_type_arr	= explode("##",$expense_pay_type);
			$expense_pay_info_arr	= explode("##",$expense_pay_info);
			$expense_company_arr	= explode("##",$expense_company);
			$expense_manager_arr	= explode("##",$expense_manager);
			$expense_contact_arr	= explode("##",$expense_contact);
			$expense_bank_name_arr	= explode("##",$expense_bank_name);
			$expense_bank_num_arr	= explode("##",$expense_bank_num);
			$expense_bank_user_arr	= explode("##",$expense_bank_user);
			$expense_pay_date_arr	= explode("##",$expense_pay_date);
			$expense_memo_arr		= explode("##",$expense_memo);
			$expense_idx_arr		= explode("##",$expense_idx);

			for ($i=0;$i<5;$i++) {
				if ($expense_money_arr[$i]) {
					$money_num = str_replace(",","",$expense_money_arr[$i]);

					$sql = "INSERT INTO DF_PROJECT_EXPENSE_V2 (
								DOC_NO, PROJECT_NO, IDX, TYPE, MONEY, TAX, TARGET, 
								PAY_TYPE, PAY_INFO, COMPANY, MANAGER, CONTACT, 
								BANK_NAME, BANK_NUM, BANK_USER, PAY_DATE, MEMO, 
								LAST, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, REG_DATE
							) VALUES (
								'$doc_no','$project_no','$expense_idx_arr[$i]','$expense_type_arr[$i]','$money_num','$expense_tax_arr[$i]','$expense_target_arr[$i]',
								'$expense_pay_type_arr[$i]','$expense_pay_info_arr[$i]','$expense_company_arr[$i]','$expense_manager_arr[$i]','$expense_contact_arr[$i]',
								'$expense_bank_name_arr[$i]','$expense_bank_num_arr[$i]','$expense_bank_user_arr[$i]','$expense_pay_date_arr[$i]','$expense_memo_arr[$i]',
								'Y','$prs_id','$prs_login','$prs_name','$prs_position',getdate()
							)";
					$rs = sqlsrv_query($dbConn, $sql);

					if ($rs == false) {
?>
					<script language="javascript">
						alert("error 3_<?=$i?>. 결제금액 등록에 실패 하였습니다. 개발팀에 문의해 주세요.");
					</script>
<?
						exit;
					}
				}
			}
		} else if ($form_title == "입사승인계") {
			for ($i=0; $i<5; $i++) {
				$expense_type_arr[$i] = isset($_REQUEST['type_'.$i]) ? $_REQUEST['type_'.$i] : null;
				$expense_idx_arr[$i]  = isset($_REQUEST['idx_'.$i]) ? $_REQUEST['idx_'.$i] : null;

				// 정규직 폼
				if ($expense_type_arr[$i] == "A") {
					$expense_data1_arr[$i]	= isset($_REQUEST['name1_'.$i]) ? $_REQUEST['name1_'.$i] : null;
					$expense_data2_arr[$i]	= isset($_REQUEST['cause1_'.$i]) ? $_REQUEST['cause1_'.$i] : null;					
					$expense_data3_arr[$i]	= isset($_REQUEST['career_'.$i]) ? $_REQUEST['career_'.$i] : null;
					$expense_data4_arr[$i]	= isset($_REQUEST['birth_'.$i]) ? $_REQUEST['birth_'.$i] : null;
					$expense_data5_arr[$i]	= isset($_REQUEST['school_'.$i]) ? $_REQUEST['school_'.$i] : null;
					$expense_data6_arr[$i]	= isset($_REQUEST['major_'.$i]) ? $_REQUEST['major_'.$i] : null;
					$career_y				= isset($_REQUEST['career_y_'.$i]) ? $_REQUEST['career_y_'.$i] : null;
					$career_m				= isset($_REQUEST['career_m_'.$i]) ? $_REQUEST['career_m_'.$i] : null;
					$expense_data7_arr[$i]	= $career_y."-".$career_m;	
					$expense_data8_arr[$i]	= isset($_REQUEST['position_'.$i]) ? $_REQUEST['position_'.$i] : null;
					$expense_data9_arr[$i]	= isset($_REQUEST['rating_'.$i]) ? $_REQUEST['rating_'.$i] : null;
					$expense_data10_arr[$i]	= isset($_REQUEST['reader_'.$i]) ? $_REQUEST['reader_'.$i] : null;
					$join_y					= isset($_REQUEST['join_y_'.$i]) ? $_REQUEST['join_y_'.$i] : null;
					$join_m					= isset($_REQUEST['join_m_'.$i]) ? $_REQUEST['join_m_'.$i] : null;
					$join_d					= isset($_REQUEST['join_d_'.$i]) ? $_REQUEST['join_d_'.$i] : null;
					$expense_data11_arr[$i]	= $join_y."-".$join_m."-".$join_d;
				// 계약직 폼
				} else {
					$expense_data1_arr[$i]	= isset($_REQUEST['name2_'.$i]) ? $_REQUEST['name2_'.$i] : null;
					$expense_data2_arr[$i]	= isset($_REQUEST['cause2_'.$i]) ? $_REQUEST['cause2_'.$i] : null;
					$expense_data3_arr[$i]	= isset($_REQUEST['gubun_'.$i]) ? $_REQUEST['gubun_'.$i] : null;
					$expense_data4_arr[$i]	= isset($_REQUEST['relay_'.$i]) ? $_REQUEST['relay_'.$i] : null;
					$expense_data5_arr[$i]	= isset($_REQUEST['salary_h_'.$i]) ? str_replace(",","",$_REQUEST['salary_h_'.$i]) : null;
					$expense_data6_arr[$i]	= isset($_REQUEST['salary_m_'.$i]) ? str_replace(",","",$_REQUEST['salary_m_'.$i]) : null;
					$period1_y				= isset($_REQUEST['period1_y_'.$i]) ? $_REQUEST['period1_y_'.$i] : null;
					$period1_m				= isset($_REQUEST['period1_m_'.$i]) ? $_REQUEST['period1_m_'.$i] : null;
					$period1_d				= isset($_REQUEST['period1_d_'.$i]) ? $_REQUEST['period1_d_'.$i] : null;
					$expense_data7_arr[$i]	= $period1_y."-".$period1_m."-".$period1_d;	
					$period2_y				= isset($_REQUEST['period2_y_'.$i]) ? $_REQUEST['period2_y_'.$i] : null;
					$period2_m				= isset($_REQUEST['period2_m_'.$i]) ? $_REQUEST['period2_m_'.$i] : null;
					$period2_d				= isset($_REQUEST['period2_d_'.$i]) ? $_REQUEST['period2_d_'.$i] : null;
					$expense_data8_arr[$i]	= $period2_y."-".$period2_m."-".$period2_d;	
					$expense_data9_arr[$i]	= isset($_REQUEST['memo_'.$i]) ? $_REQUEST['memo_'.$i] : null;
				}
			}

			for ($i=0;$i<5;$i++) {
				if ($expense_data1_arr[$i]) {
					$sql = "INSERT INTO DF_APPROVAL_EXPANSION (
								DOC_NO, IDX, TYPE, DATA1, 
								DATA2, DATA3, DATA4, DATA5, DATA6, 
								DATA7, DATA8, DATA9, DATA10, DATA11,
								LAST, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, REG_DATE
							) VALUES (
								'$doc_no','$expense_idx_arr[$i]','$expense_type_arr[$i]', '$expense_data1_arr[$i]',
								'$expense_data2_arr[$i]','$expense_data3_arr[$i]','$expense_data4_arr[$i]','$expense_data5_arr[$i]','$expense_data6_arr[$i]',
								'$expense_data7_arr[$i]','$expense_data8_arr[$i]','$expense_data9_arr[$i]','$expense_data10_arr[$i]','$expense_data11_arr[$i]',
								'Y','$prs_id','$prs_login','$prs_name','$prs_position',getdate()
							)";
					$rs = sqlsrv_query($dbConn, $sql);

					if ($rs == false) {
?>
					<script language="javascript">
						alert("error 3_<?=$i?>. 신규사원 등록에 실패 하였습니다. 개발팀에 문의해 주세요.");
					</script>
<?
						exit;
					}
				}
			}
		}
	}

	//결재 등록
	$to = isset($_REQUEST['to']) ? $_REQUEST['to'] : null;
	$to_id = isset($_REQUEST['to_id']) ? $_REQUEST['to_id'] : null;

	$to_arr = explode(",",$to_id);

	for ($i=0;$i<sizeof($to_arr);$i++) {
		if ($to_arr[$i] != "") {
			$j = $i + 1;
			$sql = "INSERT INTO DF_APPROVAL_TO 
						(DOC_NO, A_PRS_ID, A_PRS_LOGIN, A_PRS_NAME, A_PRS_TEAM, A_PRS_POSITION, A_ORDER, A_STATUS) 
					SELECT 
						'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j', '미결재'
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
				alert("error 4. 결재라인 등록에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}
	}

	if ($type == "write") {
		$sql = "UPDATE DF_APPROVAL_TO SET 
					A_REG_DATE = getdate(), A_STATUS = '결재' 
				WHERE DOC_NO = '$doc_no' AND A_PRS_ID = '$prs_id' AND A_ORDER = '1'";
		$rs = sqlsrv_query($dbConn, $sql);
	
		if ($prs_position == "이사" || $prs_position == "대표")
		{
			if (sizeof($to_arr) <= 2)
			{
				$sql = "UPDATE DF_APPROVAL SET 
							STATUS = '전결' 
						WHERE DOC_NO = '$doc_no'";
				$rs = sqlsrv_query($dbConn, $sql);

				$retUrl = "approval_my_list_end.php";
			}
		}
	}

	//수신참조 등록
	$cc = isset($_REQUEST['cc']) ? $_REQUEST['cc'] : null;
	$cc_id = isset($_REQUEST['cc_id']) ? $_REQUEST['cc_id'] : null;

	$cc_arr = explode(",",$cc_id);

	$j = 0;
	for ($i=0;$i<sizeof($cc_arr);$i++) {
		if ($cc_arr[$i] != "") {
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

			if ($rs == false) {
?>
			<script language="javascript">
				alert("error 5. 수신참조 등록에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}
	}
	$j = $j + 1;

	// 팀장,실장,이사 = 대표님(22,87,148) 결재,수신참조에 없으면 수신참조
	if (in_array($prs_id,$approval_arr) || $prs_position == "이사")
	{
		// 경영지원팀은 예외 처리
		if (!in_array($prs_id,$business_arr)) {
			if (in_array("22",$to_arr) == false && in_array("22",$cc_arr) == false) {
				$sql = "INSERT INTO DF_APPROVAL_CC 
							(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
						SELECT 
							'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
						FROM 
							DF_PERSON WITH(NOLOCK)
						WHERE
							PRS_ID = '22'
						";
				$rs = sqlsrv_query($dbConn, $sql);
				$j = $j + 1;
			}
			if (in_array("87",$to_arr) == false && in_array("87",$cc_arr) == false) {
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
				$j = $j + 1;
			}
		}

		if (in_array("148",$to_arr) == false && in_array("148",$cc_arr) == false) {
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
	else
	{
		if ($form_category == "비용품의서(v2)" || ($form_category == "휴가계" && strstr($form_title,"프로젝트"))) 
		{
			// 경영지원팀은 예외 처리
			if (!in_array($prs_id,$business_arr)) {
				if (in_array("22",$to_arr) == false && in_array("22",$cc_arr) == false) {
					$sql = "INSERT INTO DF_APPROVAL_CC 
								(DOC_NO, C_PRS_ID, C_PRS_LOGIN, C_PRS_NAME, C_PRS_TEAM, C_PRS_POSITION, C_ORDER) 
							SELECT 
								'$doc_no', PRS_ID, PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION, '$j'
							FROM 
								DF_PERSON WITH(NOLOCK)
							WHERE
								PRS_ID = '22'
							";
					$rs = sqlsrv_query($dbConn, $sql);
					$j = $j + 1;
				}
				if (in_array("87",$to_arr) == false && in_array("87",$cc_arr) == false) {
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
					$j = $j + 1;
				}
			}

			if (in_array("148",$to_arr) == false && in_array("148",$cc_arr) == false) {
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
	}

	//동반자 등록
	$partner = isset($_REQUEST['partner']) ? $_REQUEST['partner'] : null;
	$partner_id = isset($_REQUEST['partner_id']) ? $_REQUEST['partner_id'] : null;

	$partner_arr = explode(",",$partner_id);

	for ($i=0;$i<sizeof($partner_arr);$i++) {
		if ($partner_arr[$i] != "") {
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

			if ($rs == false) {
?>
			<script language="javascript">
				alert("error 6. 동반자 등록에 실패 하였습니다. 개발팀에 문의해 주세요.");
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
