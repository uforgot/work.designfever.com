<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if (!in_array($prs_id,$positionC_arr) && $prf_id != "4")
	{ 
?>
	<script type="text/javascript">
		alert("해당페이지는 팀/실장 이상만 확인 가능합니다.");
		top.close();
	</script>
<?
		exit;
	}

	$p_date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null;
	$p_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
	$p_sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : null;

	$md_name = "";
	$md_login = "";
	$md_gubun = "";
	$md_gubun1 = "";
	$md_gubun2 = "";
	$md_checktime1 = "";
	$md_checktime2 = "";
	$md_memo1 = "";
	$md_memo2 = "";
	$md_memo3 = "";
	$md_flag = "";
	$md_pay1 = "";
	$md_pay2 = "";
	$md_pay3 = "";
	$md_pay4 = "";
	$md_pay5 = "";
	$md_pay6 = "";
	$md_out_chk = "";
	$md_business_trip = "";

	$sql = "SELECT 
				PRS_NAME, PRS_LOGIN, DATE, GUBUN, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, MEMO1, MEMO2, MEMO3, FLAG, PAY1, PAY2, PAY3, PAY4, PAY5, PAY6, OUT_CHK, BUSINESS_TRIP
			FROM 
				DF_CHECKTIME WITH(NOLOCK)
			WHERE 
				PRS_ID = '$p_id' AND DATE = '$p_date'";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$md_name = $record['PRS_NAME'];
		$md_login = $record['PRS_LOGIN'];
		$md_gubun = $record['GUBUN'];
		$md_gubun1 = $record['GUBUN1'];
		$md_gubun2 = $record['GUBUN2'];
		$md_checktime1 = $record['CHECKTIME1'];
		$md_checktime2 = $record['CHECKTIME2'];
		$md_memo1 = $record['MEMO1'];
		$md_memo2 = $record['MEMO2'];
		$md_memo3 = $record['MEMO3'];
		$md_flag = $record['FLAG'];
		$md_pay1 = $record['PAY1'];
		$md_pay2 = $record['PAY2'];
		$md_pay3 = $record['PAY3'];
		$md_pay4 = $record['PAY4'];
		$md_pay5 = $record['PAY5'];
		$md_pay6 = $record['PAY6'];
		$md_out_chk = $record['OUT_CHK'];
		$md_business_trip = $record['BUSINESS_TRIP'];

		$md = "Y";
	}
	else
	{
		$sql = "SELECT PRS_NAME, PRS_LOGIN FROM DF_PERSON WITH(NOLOCK) WHERE PRS_ID = '$p_id'";
		$rs1 = sqlsrv_query($dbConn, $sql);

		$record1 = sqlsrv_fetch_array($rs1);
		if (sizeof($record1) > 0)
		{
			$md_name = $record1['PRS_NAME'];
			$md_login = $record1['PRS_LOGIN'];

			$md = "N";
		}
	}

	if (substr($md_memo3,0,8) == "전자결재")
	{
		//전자결재 (1509-0001)
		$doc_no = substr($md_memo3,10,9);

		$sql = "SELECT 
					TOP 1 B.A_PRS_POSITION, B.A_PRS_NAME 
				FROM 
					DF_APPROVAL A INNER JOIN DF_APPROVAL_TO B 
				ON 
					A.DOC_NO = B.DOC_NO 
				WHERE 
					A.DOC_NO = '$doc_no' AND A.USE_YN = 'Y' 
				ORDER BY 
					B.A_ORDER DESC";
		$rs2 = sqlsrv_query($dbConn, $sql);

		$record2 = sqlsrv_fetch_array($rs2);
		if (sizeof($record2) > 0)
		{
			$doc_position = $record2['A_PRS_POSITION'];
			$doc_name = $record2['A_PRS_NAME'];
		}

		$md_memo1 = $doc_position ." ". $doc_name;
	}

	// 근태수정 요청 데이터 ///////////////////////////////////////////////////////////////////////
	$sql = "SELECT 
				TOP 1 *, 
				CONVERT(CHAR(19), REGDATE, 20) as REGDATE, CONVERT(CHAR(19), OK_DATE, 20) as OK_DATE, CONVERT(CHAR(19), BST_DATE, 20) as BST_DATE
			FROM 
				DF_CHECKTIME_REQUEST WITH(NOLOCK)
			WHERE 
				PRS_ID = '$p_id' AND DATE = '$p_date'
			ORDER BY
				SEQNO DESC";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$rd_seqno = $record['SEQNO'];
		$rd_name = $record['PRS_NAME'];
		$rd_login = $record['PRS_LOGIN'];
		$rd_gubun = $record['GUBUN'];
		$rd_gubun1 = $record['GUBUN1'];
		$rd_gubun2 = $record['GUBUN2'];
		$rd_checktime1 = $record['CHECKTIME1'];
		$rd_checktime2 = $record['CHECKTIME2'];
		$rd_memo = $record['MEMO'];
		$rd_answer = $record['ANSWER'];
		$rd_regdate = $record['REGDATE'];
		$rd_status = $record['STATUS'];
		$rd_bst_flag = $record['BST_FLAG'];		
		$rd_bst_date = $record['BST_DATE'];
		$rd_bst_name = $record['BST_NAME'];
		$rd_ok_date = $record['OK_DATE'];
		$rd_ok_name = $record['OK_NAME'];

		//if(!$md_gubun1) $md_gubun1 = $rd_gubun1;
		//if(!$md_gubun2) $md_gubun2 = $rd_gubun2;

		$md_gubun1 = $rd_gubun1;
		$md_gubun2 = $rd_gubun2;
		if ($rd_checktime1) {
			if ($md_checktime1) $md_checktime1_prev = "(변경 전: ".date("H:i", strtotime($md_checktime1)).")";
			$md_checktime1 = $rd_checktime1;
		}
		if($rd_checktime2) {
			if ($md_checktime2) $md_checktime2_prev = "(변경 전: ".date("H:i", strtotime($md_checktime2)).")";
			$md_checktime2 = $rd_checktime2;
		}
		if ($prf_id != "4") 
		{
			$gubun1_disabled = " disabled='disabled'";
			$gubun2_disabled = " disabled='disabled'";
		}
	} 
	else 
	{
?>
		<script language="javascript">
			alert("error1. 조회 실패하였습니다. 개발팀에 문의하세요.");
		</script>
<?
		exit;
	}
	///////////////////////////////////////////////////////////////////////////////////////////////
?>

<? include INC_PATH."/pop_top.php"; ?>

<script type="text/javascript">
	//승인
	function modify(){
			
		var frm = document.form;
		
		if (frm.gubun.value.length == 0)
		{
			if (frm.gubun1.value.length == 0 && frm.gubun2.value.length == 0)
			{
				alert("출퇴근항목을 올바르게 체크해 주세요.");
				frm.gubun.focus();
				return;	
			}
		}

		if (frm.gubun1.value.length != 0)
		{
			if (frm.gubun1_hour.value.length == 0)
			{
				alert("출근항목을 올바르게 체크해 주세요.");
				frm.gubun1_hour.focus();
				return;	
			}
			if (frm.gubun1_minute.value.length == 0)
			{
				alert("출근항목을 올바르게 체크해 주세요.");
				frm.gubun1_minute.focus();
				return;	
			}
		}

		if (frm.gubun2.value.length != 0)
		{
			if (frm.gubun2_hour.value.length == 0)
			{
				alert("퇴근항목을 올바르게 체크해 주세요.");
				frm.gubun2_hour.focus();
				return;	
			}
			if (frm.gubun2_minute.value.length == 0)
			{
				alert("퇴근항목을 올바르게 체크해 주세요.");
				frm.gubun2_minute.focus();
				return;	
			}
		}

		//출근시간이 퇴근시간 보다 큰 경우
		/*
		if(frm.gubun1_hour.value + frm.gubun1_minute.value > frm.gubun2_hour.value + frm.gubun2_minute.value){
			alert("출퇴근시간 입력이 잘못되었습니다");
			frm.gubun1_hour.focus();
			return;
		}
		*/

		if(!confirm("근태 수정요청을 승인 하시겠습니까?")) return;

		frm.target="hdnFrame";
		frm.action = 'pop_modify2_act.php';
		frm.submit();
	}
	//반려
	function cancel(){
		var frm = document.form;

		if(!confirm("근태 수정요청을 반려 하시겠습니까?")) return;		

		frm.target="hdnFrame";
		frm.action = 'pop_modify2_act.php';
		frm.mode.value="cancel";
		frm.submit();
	}
	//1차승인
	function check(){
		var frm = document.form;

		//if(!confirm("근태 수정요청의 1차승인을 하시겠습니까?")) return;
		if(!confirm("근태 수정요청의 승인을 하시겠습니까?")) return;	

		frm.target="hdnFrame";
		frm.action = 'pop_modify2_act.php';
		frm.mode.value="check";
		frm.submit();
	}

	function fcheck() {
		var frm = document.form;
		
		if(frm.gubun.value == "") {
			frm.gubun1.value = "";
			frm.gubun1_hour.value = "";
			frm.gubun1_minute.value = "";
			frm.gubun2.value = "";
			frm.gubun2_hour.value = "";
			frm.gubun2_minute.value = "";
		} else {
			frm.gubun1.value = "";
			frm.gubun1_hour.value = "00";
			frm.gubun1_minute.value = "00";
			frm.gubun2.value = "";
			frm.gubun2_hour.value = "24";
			frm.gubun2_minute.value = "00";
		}

	}

	function fexception1() {
		var frm = document.form;
		
		if(frm.exception1.checked == true) {
			frm.exception2.checked = false;
			frm.gubun1.value = "6";
			frm.gubun1_hour.value = "09";
			frm.gubun1_minute.value = "00";
			frm.gubun2.value = "6";
			frm.gubun2_hour.value = "18";
			frm.gubun2_minute.value = "00";
		} else {
			frm.gubun1.value = "";
			frm.gubun1_hour.value = "";
			frm.gubun1_minute.value = "";
			frm.gubun2.value = "";
			frm.gubun2_hour.value = "";
			frm.gubun2_minute.value = "";
		}
	}

	function fexception2() {
		var frm = document.form;
		
		if(frm.exception2.checked == true) {
			frm.exception1.checked = false;
			frm.gubun1.value = "1";
			frm.gubun1_hour.value = "09";
			frm.gubun1_minute.value = "00";
			frm.gubun2.value = "2";
			frm.gubun2_hour.value = "18";
			frm.gubun2_minute.value = "00";
		} else {
			frm.gubun1.value = "";
			frm.gubun1_hour.value = "";
			frm.gubun1_minute.value = "";
			frm.gubun2.value = "";
			frm.gubun2_hour.value = "";
			frm.gubun2_minute.value = "";
		}
	}

	function fdelete(){
		
		var frm = document.form;
		
		if(!confirm("근태내역을 삭제하시겠습니까?")){
			return;
		} else {
			frm.target="hdnFrame";
			frm.action = 'pop_modify2_act.php';
			frm.mode.value = "delete";
			frm.submit();
		}
	}

	function fdelete2(){
		
		var frm = document.form;
		
		if(!confirm("수정요청내역을 삭제하시겠습니까?")){
			return;
		} else {
			frm.target="hdnFrame";
			frm.action = 'pop_modify2_act.php';
			frm.mode.value = "delete_request";
			frm.submit();
		}
	}

	function fdelete_out(no){
		
		if(!confirm("해당 외출내역을 삭제하시겠습니까?")){
			return;
		} else {
			document.getElementById("off_hour1_"+no).value = "";
			document.getElementById("off_minute1_"+no).value = "";
			document.getElementById("off_hour2_"+no).value = "";
			document.getElementById("off_minute2_"+no).value = "";
		}
	}
</script>
</head>
<body>
<form class="inlp" method='post' name='form'>
<input type="hidden" name="prs_login" value="<?=$md_login?>">
<input type="hidden" name="prs_name" value="<?=$md_name?>">
<input type="hidden" name="id" value="<?=$p_id?>">
<input type="hidden" name="date" value="<?=$p_date?>">
<input type="hidden" name="sort" value="<?=$p_sort?>">
<input type="hidden" name="rd_seqno" value="<?=$rd_seqno?>"><!-- DF_CHECKTIME_REQUEST의 SEQNO -->
<input type="hidden" name="mode">
<input type="hidden" name="md" value="<?=$md?>">
<input type="hidden" name="md_checktime1" value="<?=$md_checktime1?>">
<input type="hidden" name="md_checktime2" value="<?=$md_checktime2?>">
<!-- pop -->		 
	<div class="intra_pop work_team_pop" style="border:0px; margin-top:-170px;">
		<div class="pop_top">
			<p class="pop_title">팀원현황 상세변경</p>
			<a href="javascript:self.close();" class="close">닫기</a>
		</div>

		<div class="pop_body">
			<p class="edit_title"><?=$md_name?> : <?=substr($p_date,5,2)?>월 <?=substr($p_date,8,2)?>일</p>
		<? if ($md_checktime1 != "" || $md_checktime2 != "") { ?>
			<p class="edit_del2"><a href="javascript:fdelete2();"><img src="/img/icon_del.gif"> 수정요청삭제</a> &nbsp;<a href="javascript:fdelete();"><img src="/img/icon_del.gif"> 근태기록삭제</a></p>
		<? } ?>
			<div class="edit_wrap">
				<table class="notable edit_table"  width="100%">
					<summary></summary>
					<colgroup>
						<col width="20%" />
						<col width="25%" />
						<col width="15%" />
						<col width="*" />
					</colgroup>
					<tr>
						<th rowspan="4" class="edit"><font color="#ffcc00">*변경</font></th>
						<td colspan="3">
							<input type="checkbox" name="exception1" value="Y"<? if ($md_business_trip == "Y") { ?> checked<? } ?> onclick="fexception1();">출장처리
							<input type="checkbox" name="exception2" value="Y"<? if ($md_out_chk == "Y") { ?> checked<? } ?> onclick="fexception2();">파견처리
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<select name='gubun' style="width:100px;" onchange="fcheck();">
								<option value="">--</option>
								<option value="10"<? if ($md_gubun1 == "10") { echo " selected"; } ?>>휴가</option>
								<option value="17"<? if ($md_gubun1 == "17") { echo " selected"; } ?>>리프레시 휴가</option>
								<option value="16"<? if ($md_gubun1 == "16") { echo " selected"; } ?>>프로젝트 휴가</option>
								<option value="18"<? if ($md_gubun1 == "18") { echo " selected"; } ?>>무급 휴가</option>
								<option value="11"<? if ($md_gubun1 == "11") { echo " selected"; } ?>>병가</option>
								<option value="12"<? if ($md_gubun1 == "12") { echo " selected"; } ?>>경조사</option>
								<option value="13"<? if ($md_gubun1 == "13") { echo " selected"; } ?>>기타</option>
								<option value="14"<? if ($md_gubun1 == "14") { echo " selected"; } ?>>결근</option>
								<option value="15"<? if ($md_gubun1 == "15") { echo " selected"; } ?>>교육/훈련</option>
								<option value="19"<? if ($md_gubun1 == "19") { echo " selected"; } ?>>예비군</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<select name='gubun1' style="width:100px;">
								<option value="">--</option>
								<option value="1"<? if ($md_gubun1 == "1" || $md_gubun1 == "5") { echo " selected"; } ?>>출근</option>
								<option value="6"<? if ($md_gubun1 == "6") { echo " selected"; } ?>>외근</option>
								<option value="8"<? if ($md_gubun1 == "8") { echo " selected"; } ?>>반차</option>
								<option value="4"<? if ($md_gubun1 == "4") { echo " selected"; } ?>>프로젝트 반차</option>
							</select>
						</td>
						<td>
							<select name='gubun1_hour'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=23; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($md_checktime1,8,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='gubun1_minute'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($md_checktime1,10,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>
							&nbsp;<?=$md_checktime1_prev?>
						</td>
					</tr>
					<tr>
						<td>
							<select name='gubun2' style="width:100px;">
								<option value="">--</option>
								<option value="2"<? if ($md_gubun2 == "2") { echo " selected"; } ?>>퇴근</option>
								<option value="3"<? if ($md_gubun2 == "3") { echo " selected"; } ?>>연장근무</option>
								<option value="6"<? if ($md_gubun2 == "6") { echo " selected"; } ?>>외근</option>
								<option value="9"<? if ($md_gubun2 == "9") { echo " selected"; } ?>>반차</option>
								<option value="5"<? if ($md_gubun2 == "5") { echo " selected"; } ?>>프로젝트 반차</option>
							</select>  
						</td>
						<td>
							<select name='gubun2_hour'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=48; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($md_checktime2,8,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='gubun2_minute'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($md_checktime2,10,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>
							&nbsp;<?=$md_checktime2_prev?>
						</td>
					</tr>
					<tr>
						<th class="edit"><font color="#ffcc00">*사유</font></th>
						<td colspan="4"><textarea name="memo" disabled><?=$rd_memo?></textarea></td>
					</tr>
					<tr>
						<th class="edit"><font color="#ffcc00">*결과</font></th>
						<td colspan="4"><textarea name="answer"><?=$rd_answer?></textarea></td>
					</tr>
				</table>
			</div>
			<div class="edit_bottom">
			<? 
				if ($rd_status == "ING") 
				{
					if ($rd_bst_flag == "N")
					{
						echo "경영지원팀 확인 : 진행중 <font color='#FF0000'>(※11시 이후 출근, 17시 이전 퇴근 건은 경영지원팀 확인 필요)</font>";					
					}
					else if($rd_bst_flag == "Y")
					{
						echo "경영지원팀 확인 : ". $rd_bst_date ." (" .$rd_bst_name.")";;					
					}
					else
					{
						echo "근태수정 요청일 : ". $rd_regdate;
					}
				} 
				else if ($rd_status == "CANCEL")
				{
					echo "근태수정 반려일 : ". $rd_ok_date ." (" .$rd_ok_name.")";
				}
			?>
			</div>
			<div class="edit_wrap">
				<table class="notable edit_table"  width="100%">
					<colgroup>
						<col width="20%" />
						<col width="15%" />
						<col width="15%" />
						<col width="15%" />
						<col width="15%" />
						<col width="20%" />
					</colgroup>
					<tr>
						<th rowspan="6" class="edit"><font color="#ffcc00">*외출</font></th>
						<td colspan="5"></td>
					</tr>
<?
	$sql = "SELECT
			SEQNO, STARTTIME, ENDTIME, TOTALTIME, MEMO1, MEMO2
		FROM 
			DF_CHECKTIME_OFF WITH(NOLOCK)
		WHERE 
			PRS_ID = '$p_id' AND DATE = '$p_date' AND TOTALTIME > '0000'";
	$rs = sqlsrv_query($dbConn, $sql);

	$m = 0;
	while ($record = sqlsrv_fetch_array($rs))
	{
		$off_seq = $record['SEQNO'];
		$off_starttime = $record['STARTTIME'];
		$off_endtime = $record['ENDTIME'];
		$off_totaltime = $record['TOTALTIME'];
		$off_memo1 = $record['MEMO1'];
		$off_memo2 = $record['MEMO2'];
?>					
					<tr>
						<td>
							<input type="hidden" name="off_seq_<?=$m?>" value="<?=$off_seq?>">
							<select name='off_hour1_<?=$m?>' id='off_hour1_<?=$m?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=48; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($off_starttime,0,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='off_minute1_<?=$m?>' id='off_minute1_<?=$m?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($off_starttime,2,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;~
						</td>
						<td>
							<select name='off_hour2_<?=$m?>' id='off_hour2_<?=$m?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=48; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($off_endtime,0,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='off_minute2_<?=$m?>' id='off_minute2_<?=$m?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($off_endtime,2,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>
						</td>
						<td style="vertical-align:bottom;">
							<a href="javascript:fdelete_out(<?=$m?>);"><img src="/img/icon_del.gif"> 삭제</a>
						</td>
					</tr>
<?
		$m++;
	}
	if ($m < 5) {
		for ($n = $m; $n< 5; $n++) {
?>
					<tr>
						<td>
							<input type="hidden" name="off_seq_<?=$n?>" value="">
							<select name='off_hour1_<?=$n?>' id='off_hour1_<?=$n?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=48; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='off_minute1_<?=$n?>' id='off_minute1_<?=$n?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;~
						</td>
						<td>
							<select name='off_hour2_<?=$n?>' id='off_hour2_<?=$n?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=48; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='off_minute2_<?=$n?>' id='off_minute2_<?=$n?>'>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"><?=$j?></option>
							<?
								}
							?>
							</select>
						</td>
						<td style="vertical-align:bottom;">
							<a href="javascript:fdelete_out(<?=$n?>);"><img src="/img/icon_del.gif"> 삭제</a>
						</td>
					</tr>
<?
		}
	}
?>
				</table>
			</div>
			<div class="edit_btn">
			<? 
				if ($rd_bst_flag == "N" || $rd_bst_flag == "Y") { 
					// 경영지원팀
					if ($prf_id == "4") {
						if ($rd_status == "ING" && $rd_bst_flag == "N") {	
			?>
				<a href="javascript:check();"><!--<img src="../img/btn_approval_step1.gif" alt="approval" />--><img src="../img/btn_ok.gif" alt="ok" /></a>
				<a href="javascript:cancel();"><img src="../img/btn_reject.gif" alt="reject" /></a>
				<a href="javascript:self.close();"><img src="../img/btn_cancel.gif" alt="cancel" /></a>
			<?
						} else {
			?>
				<a href="javascript:self.close();"><img src="../img/btn_ok.gif" alt="ok" /></a>
			<?
						}
					// 일반관리자
					} else {
						if ($rd_status == "ING" && $rd_bst_flag == "Y") {
			?>
				<a href="javascript:modify();"><img src="../img/btn_ok.gif" alt="ok" /></a>
				<a href="javascript:self.close();"><img src="../img/btn_cancel.gif" alt="cancel" /></a>			

			<?
						} else {
			?>
				<a href="javascript:self.close();"><img src="../img/btn_ok.gif" alt="ok" /></a>			
			<?
						}
					}
				} else {
					if ($rd_status == "ING") {
			?>
				<a href="javascript:modify();"><img src="../img/btn_ok.gif" alt="ok" /></a>
				<a href="javascript:cancel();"><img src="../img/btn_reject.gif" alt="reject" /></a>
				<a href="javascript:self.close();"><img src="../img/btn_cancel.gif" alt="cancel" /></a>
			<? 
					} else { 
			?>
				<a href="javascript:self.close();"><img src="../img/btn_ok.gif" alt="ok" /></a>
			<? 
					} 
				}
			?>
			</div>
		</div>
	</div>
<!-- //pop -->
</form>
<? include INC_PATH."/pop_bottom.php"; ?>
</body>
</html>
