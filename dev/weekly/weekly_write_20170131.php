<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("등록대기,탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  
	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$win = isset($_REQUEST['win']) ? $_REQUEST['win'] : null;  

	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "팀장" : "";	//팀장대리 판단

	//텍스트 구분
	if ($type == "modify")	
	{
		$type_title1 = "조회/수정";
		$type_title2 = "수정";
	}
	else if ($type == "write")	
	{
		$type_title1 = "작성";
		$type_title2 = "작성";
	}
	
	//팀장 미만은 본인의 보고서만 조회 가능
	if (in_array($prs_position,$positionA_arr) || ($prs_position == '팀장' || $prs_position_tmp == '팀장'))
	{
		$searchSQL = " WHERE SEQNO = '$seqno'";								
	}
	else
	{
		$searchSQL = " WHERE SEQNO = '$seqno' AND PRS_ID = '$prs_id'";
	}

	//주간보고 기본데이터 추출
	$sql = "SELECT 
				WEEK_ORD, WEEK_AREA, TITLE, MEMO, PRS_ID, PRS_NAME, PRS_POSITION, COMPLETE_YN
			FROM 
				DF_WEEKLY WITH(NOLOCK)
			$searchSQL";								
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);

	if (!$seqno || !$record)
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("해당 글이 존재하지 않습니다.");
		history.back();
	</script>
<?
		exit;
	} else {
		$weekly_ord = $record['WEEK_ORD'];
		$weekly_str = $record['WEEK_AREA'];
		$weekly_title = $record['TITLE'];
		$weekly_memo = $record['MEMO'];
		$weekly_prs_id = $record['PRS_ID'];
		$weekly_prs_nm = $record['PRS_NAME'];
		$weekly_prs_pos = $record['PRS_POSITION'];
		$weekly_complete_yn = $record['COMPLETE_YN'];							//팀장완료 여부
		$weekly_edit_yn = ($weekly_prs_id == $prs_id) ? "Y" : "N";				//본인작성 여부

		switch (date("N"))
		{
			case "1":	$add = "3"; break;
			case "2":	$add = "2"; break;
			case "3":	$add = "1"; break;
			case "4":	$add = "0"; break;
			case "5":	$add = "6"; break;
			case "6":	$add = "5"; break;
			case "7":	$add = "4"; break;
		} 

		//참여프로젝트 리스트 추출
		//$searchSQL = " WHERE B.PRS_ID = '$weekly_prs_id' AND A.STATUS = 'ING' AND A.END_DATE >= CONVERT(VARCHAR(10),GETDATE(),120) AND A.USE_YN = 'Y'";

		//$searchSQL = " WHERE B.PRS_ID = '$weekly_prs_id' AND DATEADD(DD,7,A.END_DATE) >= CONVERT(VARCHAR(10),GETDATE(),120) AND A.USE_YN = 'Y'";
		$searchSQL = " WHERE B.PRS_ID = '$weekly_prs_id' AND DATEADD(DD,7,A.END_DATE) >= DATEADD(DD,$add,GETDATE()) AND A.USE_YN = 'Y'"; // 정상 작성일인 목요일 기준으로..

		$sql = "SELECT 
					DISTINCT A.SEQNO, A.PROJECT_NO, A.TITLE, B.PART
				FROM 
					DF_PROJECT A WITH(NOLOCK) 
					INNER JOIN DF_PROJECT_DETAIL B WITH(NOLOCK) 
					ON A.PROJECT_NO = B.PROJECT_NO
				$searchSQL
				ORDER BY 
					A.PROJECT_NO DESC";
		$rs = sqlsrv_query($dbConn,$sql);
	}
?>

<? include INC_PATH."/top.php"; ?>

<script src='../js/jquery.autosize.min.js'></script>

<script type="text/JavaScript">
	function weeklyWrite()
	{
		var frm = document.form;

		var cntProject = frm['project_no[]'].length - 1;
		var totProgThis = 0;
		var totProgNext = 0;
		var chkProgThis = -1;
		var chkProgNext = -1;

		for(i=0;i<cntProject;i++) {
			var tmpProgThis = parseInt(frm['progress_this[]'][i].value);
			var tmpProgNext = parseInt(frm['progress_next[]'][i].value);

			totProgThis = totProgThis + tmpProgThis;
			totProgNext = totProgNext + tmpProgNext;

			if(chkProgThis < 0 && (tmpProgThis > 0 && !frm['content_this[]'][i].value)) {
				chkProgThis = i;
			}
			if(chkProgNext < 0 && (tmpProgNext > 0 && !frm['content_next[]'][i].value)) {
				chkProgNext = i;
			}
		}

		if(totProgThis != 100) {
			alert("금주 진행업무의 참여비율 합이 100%가 아닙니다.");
			frm['progress_this[]'][0].focus();
			return;    	
		}

		if(totProgNext != 100) {
			alert("차주 진행업무의 참여비율 합이 100%가 아닙니다.");
			frm['progress_next[]'][0].focus();
			return;    	
		}

		if(chkProgThis >= 0) {
			alert("참여비율에 맞는 금주 진행업무를 작성해 주세요.");
			frm['progress_this[]'][chkProgThis].focus();
			return;    				
		}

		if(chkProgNext >= 0) {
			alert("참여비율에 맞는 차주 진행업무를 작성해 주세요.");
			frm['progress_next[]'][chkProgNext].focus();
			return;    				
		}

		//내용 유효성 검사 할 부분
		if(confirm("보고서를 <?=$type_title2?> 하시겠습니까")){
			frm.target = "hdnFrame";
			frm.action = 'weekly_write_act.php'; 
			frm.submit();
		}
	}

	function weeklyComplete(type) {
		var frm = document.form;
		var str = '';

		if(type == 'complete') str = "완료";
		else if(type == 'cancel') str = "취소";

		//내용 유효성 검사 할 부분
		if(confirm("팀 주간보고서 작성을 " + str + " 하시겠습니까")){
			frm.target = "hdnFrame";
			frm.type.value = type;
			frm.action = 'weekly_write_act.php'; 
			frm.submit();
		}
	}

	$(function(){
		$('.normal').autosize();
		//$('.animated').autosize();
	});
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form" action="weekly_write_act.php">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="type" value="<?=$type?>">			<!-- 등록수정삭제구분 -->
<input type="hidden" name="seqno" value="<?=$seqno?>">			<!-- 글번호 -->
<input type="hidden" name="order" value="<?=$weekly_ord?>">		<!-- 주차정보 -->
<input type="hidden" name="win" value="<?=$win?>">				<!-- 새창오픈여부 -->

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/weekly_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="vacation_stats clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row"><?=$weekly_prs_nm?><br>(<?=$weekly_str?>) <?=$weekly_title?> <?=$type_title1?></th>
<!-- 						<th width="50%" scope="row">팀원 주간보고서</th> -->
						</tr>
					</table>
				</div>
				<span style="padding-left:38px;">
					<b class="txt_left_p" style="margin-bottom:30px; margin-top:0px">
						- 참여 중인 프로젝트가 없는 경우, 진행 중인 프로젝트에서 역할과 참여율을 등록한 후 주간보고서를 작성해 주세요.</br>
						- 프로젝트 별 참여율의 합은 100% 입니다.</br>
						- 팀 주간보고 작성완료를 한 경우에는 팀원들의 주간보고는 수정할 수 없습니다.</br>
					</b>
				</span>

<!-- 프로젝트 리스트 시작 -->
<?
		$cnt = 0;
		while ($record = sqlsrv_fetch_array($rs))
		{
			$project_no = $record['PROJECT_NO'];
			$title = $record['TITLE'];
			$part = $record['PART'];

			//주간보고 수정, 열람
			if ($type == "modify")
			{
				$searchSQL1 = " WHERE WEEKLY_NO = '$seqno' AND PROJECT_NO = '$project_no'";

				$sql1 = "SELECT
							THIS_WEEK_CONTENT, NEXT_WEEK_CONTENT, THIS_WEEK_RATIO, NEXT_WEEK_RATIO
						FROM
							DF_WEEKLY_DETAIL WITH(NOLOCK)
						$searchSQL1";
				$rs1 = sqlsrv_query($dbConn,$sql1);

				$record1 = sqlsrv_fetch_array($rs1);
				if (sqlsrv_has_rows($rs1) > 0)
				{
					$this_week_content = $record1['THIS_WEEK_CONTENT'];
					$next_week_content = $record1['NEXT_WEEK_CONTENT'];
					$this_week_ratio = $record1['THIS_WEEK_RATIO'];
					$next_week_ratio = $record1['NEXT_WEEK_RATIO'];
				}
				else
				{
					$this_week_content = "";
					$next_week_content = "";
					$this_week_ratio = "";
					$next_week_ratio = "";
				}
			}
			//주간보고 신규 작성
			else if ($type == "write")
			{
				//지난주 차주 업무계획을 금주 진행업무에 할당
				$searchSQL1 = " WHERE PROJECT_NO = '$project_no' AND PRS_ID = '$weekly_prs_id' AND WEEKLY_NO < $seqno ORDER BY WEEKLY_NO DESC";

				$sql1 = "SELECT
							TOP 1 NEXT_WEEK_CONTENT, NEXT_WEEK_RATIO
						FROM
							DF_WEEKLY_DETAIL WITH(NOLOCK)
						$searchSQL1";
				$rs1 = sqlsrv_query($dbConn,$sql1);

				$record1 = sqlsrv_fetch_array($rs1);
				if (sqlsrv_has_rows($rs1) > 0)
				{
					$this_week_content = $record1['NEXT_WEEK_CONTENT'];
					$next_week_content = "";
					$this_week_ratio = $record1['NEXT_WEEK_RATIO'];
					$next_week_ratio = "";
				}
				else
				{
					$this_week_content = "";
					$next_week_content = "";
					$this_week_ratio = "";
					$next_week_ratio = "";
				}
			}
?>
				<!-- weekly routine 시작 -->
				<div class="board_list" style="margin-bottom:40px;">
					<table class="notable work3 board_list"  style="width:100%">
						<caption>게시판 리스트 테이블</caption>
						<colgroup>
							<col width="49%" />
							<col width="2%" />
							<col width="*" />
						</colgroup>
						
						<tbody class="p_detail">
							<tr>
								<td style="font-weight:bold;" colspan="3">* [<?=$project_no?>] <?=$title?> / <?=$part?></td>
								<input type="hidden" name="project_no[]" value="<?=$project_no?>">
							</tr>
							<tr>
								<td>금주 진행업무 
									<select name="progress_this[]" class="percentage">
										<?
											for ($i=0; $i<=100; $i=$i+5) 
											{
												if ($i == $this_week_ratio) 
												{ 
													$selected = " selected"; 
												}
												else
												{
													$selected = "";
												}
												echo "<option value='".$i."'".$selected.">".$i."%</option>";
											}
										?>												
									</select></td>
								<td></td>
								<td style="font-weight:bold;">차주 진행업무 
									<select name="progress_next[]" class="percentage">
										<?
											for ($i=0; $i<=100; $i=$i+5) 
											{
												if ($i == $next_week_ratio) 
												{ 
													$selected = " selected"; 
												}
												else
												{
													$selected = "";
												}
												echo "<option value='".$i."'".$selected.">".$i."%</option>";
											}
										?>									
									</select></td>
							</tr>
							<tr style="vertical-align:top;">
								<td>
									<textarea cols="30" rows="10" name="content_this[]" style="width:96%" class='normal'><?=$this_week_content?></textarea></td>
								<td></td>
								<td><textarea cols="30" rows="10" name="content_next[]" style="width:96%" class='normal'><?=$next_week_content?></textarea></td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- weekly routine 종료 -->
<?
			$cnt++;
		}
?>
<!-- 프로젝트 리스트 종료 -->

<!-- 기타업무 항목 시작 -->
<?
		$project_no_etc = "DF0000_ETC"; //기타업무에 할당한 프로젝트 코드

		//주간보고 수정, 열람
		if ($type == "modify")
		{
			$searchSQL1 = " WHERE WEEKLY_NO = '$seqno' AND PROJECT_NO = '$project_no_etc'";

			$sql1 = "SELECT
						THIS_WEEK_CONTENT, NEXT_WEEK_CONTENT, THIS_WEEK_RATIO, NEXT_WEEK_RATIO
					FROM
						DF_WEEKLY_DETAIL WITH(NOLOCK)
					$searchSQL1";
			$rs1 = sqlsrv_query($dbConn,$sql1);

			$record1 = sqlsrv_fetch_array($rs1);
			if (sqlsrv_has_rows($rs1) > 0)
			{
				$this_week_content = $record1['THIS_WEEK_CONTENT'];
				$next_week_content = $record1['NEXT_WEEK_CONTENT'];
				$this_week_ratio = $record1['THIS_WEEK_RATIO'];
				$next_week_ratio = $record1['NEXT_WEEK_RATIO'];
			}
		}
		//주간보고 신규 작성
		else if ($type == "write")
		{
			//지난주 차주 업무계획을 금주 진행업무에 할당
			$searchSQL1 = " WHERE PROJECT_NO = '$project_no_etc' AND PRS_ID = '$weekly_prs_id' AND WEEKLY_NO < $seqno ORDER BY WEEKLY_NO DESC";

			$sql1 = "SELECT
						TOP 1 NEXT_WEEK_CONTENT, NEXT_WEEK_RATIO
					FROM
						DF_WEEKLY_DETAIL WITH(NOLOCK)
					$searchSQL1";
			$rs1 = sqlsrv_query($dbConn,$sql1);

			$record1 = sqlsrv_fetch_array($rs1);
			if (sqlsrv_has_rows($rs1) > 0)
			{
				$this_week_content = $record1['NEXT_WEEK_CONTENT'];
				$next_week_content = "";
				$this_week_ratio = $record1['NEXT_WEEK_RATIO'];
				$next_week_ratio = "";
			}
		}
?>
				<div class="board_list" style="margin-bottom:40px;">
					<table class="notable work3 board_list"  style="width:100%">
						<caption>게시판 리스트 테이블</caption>
						<colgroup>
							<col width="49%" />
							<col width="2%" />
							<col width="*" />
						</colgroup>
						
						<tbody class="p_detail">
							<tr>
								<td style="font-weight:bold;" colspan="3">* 기타업무(경영지원팀, 홍보팀, 기타 업무)</td>
								<input type="hidden" name="project_no[]" value="DF0000_ETC">
							</tr>
							<tr>
								<td>금주 진행업무 
									<select name="progress_this[]" class="percentage">
										<?
											for ($i=0; $i<=100; $i=$i+5) 
											{
												if ($i == $this_week_ratio) 
												{ 
													$selected = " selected"; 
												}
												else
												{
													$selected = "";
												}
												echo "<option value='".$i."'".$selected.">".$i."%</option>";
											}
										?>	
									</select></td>
								<td></td>
								<td style="font-weight:bold;">차주 진행업무 
									<select name="progress_next[]" class="percentage">
										<?
											for ($i=0; $i<=100; $i=$i+5) 
											{
												if ($i == $next_week_ratio) 
												{ 
													$selected = " selected"; 
												}
												else
												{
													$selected = "";
												}
												echo "<option value='".$i."'".$selected.">".$i."%</option>";
											}
										?>	
									</select></td>
							</tr>
							<tr style="vertical-align:top;">
								<td><textarea cols="30" rows="10" name="content_this[]" style="width:96%" class='normal'><?=$this_week_content?></textarea></td>
								<td></td>
								<td><textarea cols="30" rows="10" name="content_next[]" style="width:96%" class='normal'><?=$next_week_content?></textarea></td>
							</tr>
						</tbody>
					</table>

					<!-- 필드배열 처리위한 더미 태그 -->
					<input type="hidden" name="project_no[]">
					<input type="hidden" name="progress_this[]">
					<input type="hidden" name="progress_next[]">
					<input type="hidden" name="content_this[]">
					<input type="hidden" name="content_next[]">

				</div>
<!-- 기타업무 항목 종료 -->

<!-- (팀장)건의사항 항목 시작 -->
<?
	if ($weekly_prs_pos == '팀장') {
?>
				<div class="board_list" style="margin-bottom:0px;">
					<table class="notable work3 board_list"  style="width:100%">
						<caption>게시판 리스트 테이블</caption>
						<colgroup>
							<col width="100%" />
						</colgroup>
						
						<tbody class="p_detail">
							<tr>
								<td style="font-weight:bold;" colspan="3">* 건의 및 기타사항</td>
							</tr>
							<tr style="vertical-align:top;">
								<td><textarea cols="30" rows="10" name="memo" style="width:98%" class='normal'><?=$weekly_memo?></textarea></td>
							</tr>
						</tbody>
					</table>
				</div>
<?
	}
?>
<!-- (팀장)건의사항 항목 종료 -->

				<div class="project_reg clearfix" style="margin-bottom:40px;">
					<div class="btns_wrap" style="float:left;margin-top:0px;">
					<? if (($prs_position == '팀장' ||  $prs_position_tmp == '팀장') && $weekly_edit_yn == 'Y') { ?> 
						<? if ($weekly_complete_yn != 'Y') { ?>						
						<a href="javascript:weeklyComplete('complete');"><img src="/img/weekly/btn_weekly_team.png" alt="완료" id="btnComplete" style="cursor:pointer;"></a>					
						<? } else { ?>
						<a href="javascript:weeklyComplete('cancel');">[팀 주간보고서 완료 취소]</a>
						<? } ?>
					<? } ?>
					</div>
					<div class="btns_wrap btn_right" style="margin-top:0px;">
						<? if ($weekly_complete_yn != 'Y' && $weekly_edit_yn == 'Y') { ?>						
						<a href="javascript:weeklyWrite();"><img src="/img/weekly/btn_save.gif" alt="등록" id="btnWrite" style="cursor:pointer;"></a>
						<? } ?>
						<? if ($win == 'new') { ?>						
						<a href="javascript:window.close();"><img src="/img/weekly/btn_cancle.gif" alt="취소" id="btnCancel" style="cursor:pointer;"></a>
						<? } else { ?>
						<a href="./weekly_list.php?page=<?=$page?>"><img src="/img/weekly/btn_cancle.gif" alt="취소" id="btnCancel" style="cursor:pointer;"></a>
						<? } ?>
					</div>
				</div>

			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
