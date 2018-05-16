<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : null; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null; 

	if ($seqno == "")
	{
?>
	<script type="text/javascript">
		alert("해당 요청이가 존재하지 않습니다.");
		self.close();
	</script>
<?
		exit;
	}

	$sql = "SELECT 
				PRS_ID, DATE, CHK_GUBUN1, CHK_GUBUN2, CHK_OFF1, CHK_OFF2, CHK_OFF3, CHK_OFF4, CHK_OFF5, 
				GUBUN, GUBUN1, GUBUN2, STARTTIME, ENDTIME, MEMO, OUT_CHK, BUSINESS_TRIP,
				OFF_STARTTIME1, OFF_ENDTIME1, OFF_STARTTIME2, OFF_ENDTIME2, OFF_STARTTIME3, OFF_ENDTIME3, 
				OFF_STARTTIME4, OFF_ENDTIME4, OFF_STARTTIME5, OFF_ENDTIME5, 
				EDIT_OK, CONVERT(CHAR(20),REG_DATE,120) AS REG_DATE,  CONVERT(CHAR(20),OK_DATE,120) AS OK_DATE 
			FROM 
				DF_CHECKTIME_EDIT WITH(NOLOCK)
			WHERE 
				SEQNO = $seqno";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$md_prs_id = $record['PRS_ID'];
		$md_date = $record['DATE'];
		$md_chk_gubun1 = $record['CHK_GUBUN1'];
		$md_chk_gubun2 = $record['CHK_GUBUN2'];
		$md_chk_off1 = $record['CHK_OFF1'];
		$md_chk_off2 = $record['CHK_OFF2'];
		$md_chk_off3 = $record['CHK_OFF3'];
		$md_chk_off4 = $record['CHK_OFF4'];
		$md_chk_off5 = $record['CHK_OFF5'];
		$md_gubun = $record['GUBUN'];
		$md_gubun1 = $record['GUBUN1'];
		$md_gubun2 = $record['GUBUN2'];
		$md_starttime = $record['STARTTIME'];
		$md_endtime = $record['ENDTIME'];
		$md_memo = $record['MEMO'];
		$md_out_chk = $record['OUT_CHK'];
		$md_business_trip = $record['BUSINESS_TRIP'];
		$md_off1_starttime = $record['OFF_STARTTIME1'];
		$md_off1_endtime = $record['OFF_ENDTIME1'];
		$md_off2_starttime = $record['OFF_STARTTIME2'];
		$md_off2_endtime = $record['OFF_ENDTIME2'];
		$md_off3_starttime = $record['OFF_STARTTIME3'];
		$md_off3_endtime = $record['OFF_ENDTIME3'];
		$md_off4_starttime = $record['OFF_STARTTIME4'];
		$md_off4_endtime = $record['OFF_ENDTIME4'];
		$md_off5_starttime = $record['OFF_STARTTIME5'];
		$md_off5_endtime = $record['OFF_ENDTIME5'];
		$md_edit_ok = $record['EDIT_OK'];
		$md_ok_date = $record['OK_DATE'];
		$md_reg_date = $record['REG_DATE'];
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		//수정
		$("#btnEdit").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","_self");
			$("#form").attr("action","commuting_edit.php"); 
			$("#form").submit();
		});

		//근태적용
		$("#btnOk").attr("style","cursor:pointer;").click(function(){
			if ($("#chk_gubun1").is(":checked"))
			{
				if ($("#gubun1").val() == "")
				{
					alert("출근여부를 올바르게 선택해 주세요.");
					$("#gubun1").focus();
					return;	
				}
			}
			if ($("#chk_gubun2").is(":checked"))
			{
				if ($("#gubun2").val() == "")
				{
					alert("퇴근여부를 올바르게 선택해 주세요.");
					$("#gubun2").focus();
					return;	
				}
			}

			$("#form").attr("target","hdnFrame");
			$("#form").attr("action","commuting_edit_ok.php"); 
			$("#form").submit();
		});

		//기각
		$("#btnNo").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","hdnFrame");
			$("#form").attr("action","commuting_edit_no.php"); 
			$("#form").submit();
		});

		//취소
		$("#btnCancel").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","_self");
			$("#form").attr("action","commuting_edit_list.php"); 
			$("#form").submit();
		});

	});
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form" id="form">
<input type="hidden" name="seqno" id="seqno" value="<?=$seqno?>">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="keyfield" value="<?=$keyfield?>">
<input type="hidden" name="keyword" value="<?=$keyword?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/commuting_menu.php"; ?>
			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
				</div>
				<table class="notable work_edit"  width="100%" border=0>
					<caption>수정요청 테이블</caption>
					<colgroup>
						<col width="10%" />
						<col width="10%" />
						<col width="20%" />
						<col width="20%" />
						<col width="*" />
					</colgroup>
					<tbody>
						<tr>
							<th class="edit">*근무일</th>
							<td colspan="2"><?=$md_date?> <? if ($md_out_chk == "Y") { ?>(파견)<? } ?></td>
							<td colspan="2"></td>
						</tr>
						<tr>
							<th rowspan="7" class="edit">*시간</th>
							<td>
								<input type="checkbox" id="chk_gubun1" value="Y"<? if ($md_chk_gubun1 == "Y") { ?> checked<? } ?> disabled>출근
							</td>
							<td>
								<? if ($md_chk_gubun1 == "Y") { echo substr($md_starttime,0,2) .":". substr($md_starttime,2,2); } ?>
								<? if ($md_gubun1 == "1") { echo " (출근)"; } ?>
								<? if ($md_gubun1 == "4") { echo " (프로젝트 오전반차)"; } ?>
								<? if ($md_gubun1 == "6") { echo " (외근)"; } ?>
								<? if ($md_gubun1 == "8") { echo " (반차)"; } ?>
							</td>
							<td colspan="2">
							<? if ($prf_id == "4") { ?>
								<select name='gubun1' id="gubun1" style="width:100px;">
									<option value="">--</option>
									<option value="1"<? if ($md_gubun1 == "1") { echo " selected"; } ?>>출근</option>
									<option value="6"<? if ($md_gubun1 == "6") { echo " selected"; } ?>>외근</option>
									<option value="8"<? if ($md_gubun1 == "8") { echo " selected"; } ?>>오전반차</option>
									<option value="4"<? if ($md_gubun1 == "4") { echo " selected"; } ?>>프로젝트 오전반차</option>
								</select>
							<? } ?>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" id="chk_gubun2" value="Y"<? if ($md_chk_gubun2 == "Y") { ?> checked<? } ?> disabled>퇴근
							</td>
							<td>
								<? if ($md_chk_gubun2 == "Y") { echo substr($md_endtime,0,2) .":". substr($md_endtime,2,2); } ?>
								<? if ($md_gubun2 == "2") { echo " (정시퇴근)"; } ?>
								<? if ($md_gubun2 == "3") { echo " (연장근무)"; } ?>
								<? if ($md_gubun2 == "5") { echo " (오후반차)"; } ?>
								<? if ($md_gubun2 == "6") { echo " (외근)"; } ?>
								<? if ($md_gubun2 == "9") { echo " (프로젝트 오후반차)"; } ?>
							</td>
							<td colspan="2">
							<? if ($prf_id == "4") { ?>
								<select name='gubun2' id="gubun2" style="width:100px;">
									<option value="">--</option>
									<option value="2"<? if ($md_gubun2 == "2") { echo " selected"; } ?>>정시퇴근</option>
									<option value="3"<? if ($md_gubun2 == "3") { echo " selected"; } ?>>연장근무</option>
									<option value="6"<? if ($md_gubun2 == "6") { echo " selected"; } ?>>외근</option>
									<option value="9"<? if ($md_gubun2 == "9") { echo " selected"; } ?>>오후반차</option>
									<option value="5"<? if ($md_gubun2 == "5") { echo " selected"; } ?>>프로젝트 오후반차</option>
								</select>  
							<? } ?>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" value="Y"<? if ($md_chk_off1 == "Y") { ?> checked<? } ?> disabled>외출1
							</td>
							<td colspan="2">
								<? if ($md_chk_off1 == "Y") { echo substr($md_off1_starttime,0,2) .":". substr($md_off1_starttime,2,2) ." ~ ". substr($md_off1_endtime,0,2) .":". substr($md_off1_endtime,2,2); } ?>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" value="Y"<? if ($md_chk_off2 == "Y") { ?> checked<? } ?> disabled>외출2
							</td>
							<td colspan="2">
								<? if ($md_chk_off2 == "Y") { echo substr($md_off2_starttime,0,2) .":". substr($md_off2_starttime,2,2) ." ~ ". substr($md_off2_endtime,0,2) .":". substr($md_off2_endtime,2,2); } ?>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" value="Y"<? if ($md_chk_off3 == "Y") { ?> checked<? } ?> disabled>외출3
							</td>
							<td colspan="2">
								<? if ($md_chk_off3 == "Y") { echo substr($md_off3_starttime,0,2) .":". substr($md_off3_starttime,2,2) ." ~ ". substr($md_off3_endtime,0,2) .":". substr($md_off3_endtime,2,2); } ?>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" value="Y"<? if ($md_chk_off4 == "Y") { ?> checked<? } ?> disabled>외출4
							</td>
							<td colspan="2">
								<? if ($md_chk_off4 == "Y") { echo substr($md_off4_starttime,0,2) .":". substr($md_off4_starttime,2,2) ." ~ ". substr($md_off4_endtime,0,2) .":". substr($md_off4_endtime,2,2); } ?>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" value="Y"<? if ($md_chk_off5 == "Y") { ?> checked<? } ?> disabled>외출5
							</td>
							<td colspan="2">
								<? if ($md_chk_off5 == "Y") { echo substr($md_off5_starttime,0,2) .":". substr($md_off5_starttime,2,2) ." ~ ". substr($md_off5_endtime,0,2) .":". substr($md_off5_endtime,2,2); } ?>
							</td>
							<td></td>
						</tr>
						<tr>
							<th class="edit">*사유</th>
							<td colspan="4"><?=$md_memo?></td>
						</tr>
						<tr>
							<th class="edit">*수정요청일</th>
							<td colspan="4"><?=$md_reg_date?></td>
						</tr>
					<? if ($md_edit_ok == "Y") { ?>
						<tr>
							<th class="edit">*근태수정일</th>
							<td colspan="4"><?=$md_ok_date?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>

			
			<div class="edit_btn">
		<? if ($md_edit_ok == "N" && $md_prs_id == $prs_id) { ?>
				<span id="btnEdit"><span style="padding:10px 30px; border:3px solid #000; font-weight:bold; color:#000; background:#fff;">수정</span></span>
		<? } ?>
				<span id="btnCancel"><span style="padding:10px 30px; border:3px solid #000; font-weight:bold; color:#000; background:#fff;">목록보기</span></span>
		<? if ($md_edit_ok == "N" && $prf_id == "4") { ?>
				<span id="btnOk"><span style="padding:10px 30px; border:3px solid #000; font-weight:bold; color:#000; background:#fff;">근태적용</span></span>
				<span id="btnNo"><span style="padding:10px 30px; border:3px solid #000; font-weight:bold; color:#000; background:#fff;">요청기각</span></span>
		<? } ?>
			</div>
			
			
			</div>
		</div>

</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
