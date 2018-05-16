<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_category = isset($_REQUEST['category']) ? $_REQUEST['category'] : null;
	$p_vacation = isset($_REQUEST['vacation']) ? $_REQUEST['vacation'] : null;
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : null; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : date("Y"); 
	$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : 1; 
	if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
	$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : 1; 
	if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
	$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : date("Y"); 
	$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : date("m"); 
	if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
	$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d"); 
	if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

	$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
	$to_date = $to_year ."-". $to_month ."-". $to_day;

	$searchSQL = " WHERE PRS_ID = '$prs_id' AND STATUS IN ('보류','기각') AND USE_YN = 'Y' AND CONVERT(char(10),REG_DATE,120) BETWEEN '$fr_date' AND '$to_date'";
	if ($p_category != "")
	{
		if ($p_category == "품의서")
		{
			$searchSQL .= " AND FORM_CATEGORY IN ('비용품의서(v2)','비용품의서','프로젝트 관련품의서')";
		}
		else
		{
			$searchSQL .= " AND FORM_CATEGORY = '$p_category'";
		}
	}
	if ($p_vacation != "")
	{
		if ($p_vacation == "기타") 
		{
			$searchSQL .= " AND FORM_TITLE IN ('기타','출산휴가','육아휴직','교육/훈련','무급')";
		}
		else
		{
			$searchSQL .= " AND FORM_TITLE LIKE '%". $p_vacation ."%'";
		}
	}

	if ($keyword != "")
	{
		if ($keyfield == "기안자")
		{
			$searchSQL .= " AND PRS_NAME = '$keyword'";
		}
		else if ($keyfield == "제목")
		{
			$searchSQL .= " AND TITLE Like '%". $keyword ."%'";
		}
		else if ($keyfield == "내용")
		{
			$searchSQL .= " AND CONTENTS Like '%". $keyword ."%'";
		}
	}

	$sql = "SELECT COUNT(DISTINCT DOC_NO) FROM DF_APPROVAL WITH(NOLOCK) ". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT 
				T.DOC_NO, T.FORM_CATEGORY, T.COUNT
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(ORDER BY DOC_NO DESC) AS ROWNUM,
					DOC_NO, FORM_CATEGORY, COUNT(SEQNO) AS COUNT
				FROM 
					DF_APPROVAL WITH(NOLOCK)
				$searchSQL
				GROUP BY 
					DOC_NO, FORM_CATEGORY
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		//날짜 지정
		$("#fr_year, #fr_month, #fr_day").change(function() {
			$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		});
		$("#fr_date").datepicker({
			onSelect: function (selectedDate) {
				$("#fr_year").val( selectedDate.substring(6,10) );
				$("#fr_month").val( selectedDate.substring(0,2) );
				$("#fr_day").val( selectedDate.substring(3,5) );
			}
		});
		$("#to_year, #to_month, #to_day").change(function() {
			$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		});
		$("#to_date").datepicker({
			onSelect: function (selectedDate) {
				$("#to_year").val( selectedDate.substring(6,10) );
				$("#to_month").val( selectedDate.substring(0,2) );
				$("#to_day").val( selectedDate.substring(3,5) );
			}
		});
	});
</script>
<script src="/js/approval.js"></script>
</head>

<body>
<div id="approval" class="wrapper">
<form name="form" method="post">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="type" value="<?=$type?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
		<? include INC_PATH."/approval_menu.php"; ?>

			<div class="tempStorage-wrap clearfix">
			<? include INC_PATH."/approval_menu2.php"; ?>

				<div class="content-wrap">
					<div class="title clearfix">
						<table class="notable " width="100%">
							<tr>
								<th scope="row">반려문서</th>
								<td style="float:right;">
									<a href="javascript:ShowPop('StatusDesc');"><img src="/img/btn_approveState.gif" alt="전자결재 상태표" /></a>
								</td>
							</tr>
						</table>
					</div>

					<div class="content-1">
						<table class="notable" width="100%">
							<tr class="a1">
								<th>검색</th>
								<td>
									<div class="btns">
										<a href="javascript:funSearch(this.form,'<?=CURRENT_PAGE?>');"><img src="/img/btn_search_p.gif" alt="검색" /></a>
										<a href="<?=CURRENT_URL?>"><img src="/img/btn_reset_p.gif" alt="검색 초기화" /></a>
									</div>
								</td>
							</tr>
							<tr>
								<th>결재문서양식</th>
								<td>
									<select name="category" onChange="javascript:selCase(this.form);" style="width:120px;">
										<option value="">전체</option>
										<option value="입사승인계"<? if ($p_category == "입사승인계") { echo " selected"; } ?>>입사승인계</option>
										<option value="품의서"<? if ($p_category == "품의서") { echo " selected"; } ?>>품의서</option>
										<option value="외근계/파견계"<? if ($p_category == "외근계/파견계") { echo " selected"; } ?>>외근계/파견계</option>
										<option value="휴가계"<? if ($p_category == "휴가계") { echo " selected"; } ?>>휴가계</option>
										<option value="출장계"<? if ($p_category == "출장계") { echo " selected"; } ?>>출장계</option>
										<option value="사유서"<? if ($p_category == "사유서") { echo " selected"; } ?>>사유서</option>
										<option value="시말서"<? if ($p_category == "시물서") { echo " selected"; } ?>>시말서</option>
										<option value="조퇴계"<? if ($p_category == "조퇴계") { echo " selected"; } ?>>조퇴계</option>
									</select>
									<select name="vacation" style="display:<? if ($p_category == "휴가계") { echo " inline"; } else { echo " none"; } ?>; width:120px;">
										<option value="">전체</option>
										<option value="연차"<? if ($p_vacation == "연차") { echo " selected"; } ?>>연차</option>
										<option value="병가"<? if ($p_vacation == "병가") { echo " selected"; } ?>>병가</option>
										<option value="반차"<? if ($p_vacation == "반차") { echo " selected"; } ?>>반차</option>
										<option value="리프레쉬"<? if ($p_vacation == "리프레쉬") { echo " selected"; } ?>>리프레쉬</option>
										<option value="프로젝트"<? if ($p_vacation == "프로젝트") { echo " selected"; } ?>>프로젝트</option>
										<option value="무급"<? if ($p_vacation == "무급") { echo " selected"; } ?>>무급</option>
										<option value="경조사"<? if ($p_vacation == "경조사") { echo " selected"; } ?>>경조사</option>
										<option value="예비군"<? if ($p_vacation == "예비군") { echo " selected"; } ?>>예비군/민방위</option>
										<option value="기타"<? if ($p_vacation == "기타") { echo " selected"; } ?>>기타</option>
										<option value="휴가 소진시"<? if ($p_vacation == "휴가 소진시") { echo " selected"; } ?>>휴가 소진시</option>
									</select>
								</td>
							</tr>
							<tr>
								<th>검색어</th>
								<td>
									<select name="keyfield" style="width:120px;">
										<option value="기안자"<? if ($keyfield=="기안자") { echo " selected"; } ?>>기안자</option>
										<option value="제목"<? if ($keyfield=="제목") { echo " selected"; } ?>>제목</option>
										<option value="내용"<? if ($keyfield=="내용") { echo " selected"; } ?>>내용</option>
									</select>
									<input id="keyword" type="text" name="keyword" value="<?=$keyword?>" style="width:213px;" /><br>
								</td>
							</tr>
							<tr class="period">
								<th>기안일자</th>
								<td class="last">
									<select name="fr_year" id="fr_year">
									<?
										for ($i=$startYear; $i<=($fr_year+1); $i++) 
										{
											if ($i == $fr_year) 
											{ 
												$selected = " selected"; 
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>년</span>
									<select name="fr_month" id="fr_month">
									<?
										for ($i=1; $i<=12; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == $fr_month)
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>월</span>
									<select name="fr_day" id="fr_day">
									<?
										for ($i=1; $i<=31; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == $fr_day)
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>일</span>
									<input type="hidden" id="fr_date" class="datepicker">
									<span>-</span>
									<select name="to_year" id="to_year">
									<?
										for ($i=$startYear; $i<=($to_year+1); $i++) 
										{
											if ($i == $to_year) 
											{ 
												$selected = " selected"; 
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>년</span>
									<select name="to_month" id="to_month">
									<?
										for ($i=1; $i<=12; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == $to_month)
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>월</span>
									<select name="to_day" id="to_day">
									<?
										for ($i=1; $i<=31; $i++) 
										{
											if (strlen($i) == "1") 
											{
												$j = "0".$i;
											}
											else
											{
												$j = $i;
											}

											if ($j == $to_day)
											{
												$selected = " selected";
											}
											else
											{
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>일</span>
									<input type="hidden" id="to_date" class="datepicker">
								</td>
							</tr>
						</table>
					</div>

					<div class="content-2">
						<table class="notable" width="100%">
							<caption>반려문서 테이블</caption>
							<colgroup>
								<col width="50px" />
								<col width="70px" />
								<col width="70px" />
								<col width="120px" />
								<col width="*" />
								<col width="80px" />
							</colgroup>

							<thead>
								<tr>
									<th>no.</th>
									<th>문서번호</th>
									<th>기안일자</th>
									<th>문서종류</th>
									<th>문서명</th>
									<th class="last">재상신</th>
								</tr>
							</thead>

							<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="6" class="bold">해당 정보가 없습니다.</td>
							</tr>
<?
	}
	else
	{
		while ($record = sqlsrv_fetch_array($rs))
		{
			$doc_no = $record['DOC_NO'];
			$count = $record['COUNT'];
			$category = $record['FORM_CATEGORY'];

			if ($category == "휴가계")
			{
				$sql1 = "SELECT
							TITLE, CONVERT(char(10),APPROVAL_DATE,102) AS APPROVAL_DATE, PRS_TEAM, PRS_POSITION, PRS_NAME, STATUS, FORM_CATEGORY, FORM_TITLE, 
							(SELECT ISNULL(COUNT(R_SEQNO),0) FROM DF_APPROVAL_REPLY WITH(NOLOCK) WHERE DOC_NO = '$doc_no') AS REPLY 
						FROM 
							DF_APPROVAL WITH(NOLOCK)
						WHERE
							DOC_NO = '$doc_no'
						ORDER BY 
							SEQNO";
				$rs1 = sqlsrv_query($dbConn,$sql1);

				$record1 = sqlsrv_fetch_array($rs1);
				
				if ($count == 2)
				{
					$vacation = "";
					while ($record1 = sqlsrv_fetch_array($rs1))
					{
						$form_category = $record1['FORM_CATEGORY'];
						$form_title = $record1['FORM_TITLE'];
						$title = $record1['TITLE'];
						$approval_date = $record1['APPROVAL_DATE'];
						$team = $record1['PRS_TEAM'];
						$position = $record1['PRS_POSITION'];
						$name = $record1['PRS_NAME'];
						$status = $record1['STATUS'];
						$reply = $record1['REPLY'];
					}
					$form_title = "연프";
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?> 휴가계</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td class="last">
									<a href="javascript:funReWrite('<?=$doc_no?>');"><img src="/img/btn_reWrittenReport.gif" alt="재상신"></a>
								</td>
							</tr>
<?
				}
				else
				{
					$form_category = $record1['FORM_CATEGORY'];
					$form_title = $record1['FORM_TITLE'];
					$title = $record1['TITLE'];
					$approval_date = $record1['APPROVAL_DATE'];
					$team = $record1['PRS_TEAM'];
					$position = $record1['PRS_POSITION'];
					$name = $record1['PRS_NAME'];
					$status = $record1['STATUS'];
					$reply = $record1['REPLY'];
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?> 휴가계</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td class="last">
									<a href="javascript:funReWrite('<?=$doc_no?>');"><img src="/img/btn_reWrittenReport.gif" alt="재상신"></a>
								</td>
							</tr>
							</tr>
<?
				}
			}
			else
			{
				$sql1 = "SELECT TOP 1
							TITLE, CONVERT(char(10),APPROVAL_DATE,102) AS APPROVAL_DATE, PRS_TEAM, PRS_POSITION, PRS_NAME, STATUS, FORM_CATEGORY, FORM_TITLE, 
							(SELECT ISNULL(COUNT(R_SEQNO),0) FROM DF_APPROVAL_REPLY WITH(NOLOCK) WHERE DOC_NO = '$doc_no') AS REPLY 
						FROM 
							DF_APPROVAL WITH(NOLOCK)
						WHERE
							DOC_NO = '$doc_no'
						ORDER BY 
							SEQNO";
				$rs1 = sqlsrv_query($dbConn,$sql1);

				$record1 = sqlsrv_fetch_array($rs1);

				$form_category = $record1['FORM_CATEGORY'];
				$form_title = $record1['FORM_TITLE'];
				$title = $record1['TITLE'];
				$approval_date = $record1['APPROVAL_DATE'];
				$team = $record1['PRS_TEAM'];
				$position = $record1['PRS_POSITION'];
				$name = $record1['PRS_NAME'];
				$status = $record1['STATUS'];
				$reply = $record1['REPLY'];
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?></td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td class="last">
									<a href="javascript:funReWrite('<?=$doc_no?>');"><img src="/img/btn_reWrittenReport.gif" alt="재상신"></a>
								</td>
							</tr>
<?
			}
			$i--;
		}
	}
?>
							</tbody>					
						</table>
					</div>

					<div class="page_num">
					<?=getPaging($total_cnt,$page,$per_page);?>
					</div>
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>

<div id="popStatusDesc" class="approval-popup1" style="display:none;">
	<div class="pop_top">
		<p class="pop_title">전자결재 상태표</p>
		<a href="javascript:HidePop('StatusDesc');" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<p><strong>미결재: </strong>결재를 하지 않은 상태</p>
		<p><strong>결 재: </strong>결재서류에 대해서 승인이 모두 완료된 상태</p>
		<p><strong>전 결: </strong>다음 결재자로 넘어가지 않고 승인(전자결재 종결)</p>
		<p><strong>보 류: </strong>다음 결재자로 넘어가지 않고 보류(전자결재 종결)</p>
		<p><strong>기 각: </strong>다음 결재자로 넘어가지 않고 미승인(전자결재 종결)</p>
		<p><strong>진행중: </strong>현재 전자결재가 진행중 입니다.</p>
	</div>
</div>

<div id="popDetail" class="approval-popup2" style="display:none;">
	<div class="title">
		<h3 class="aaa">반려문서 보기</h3>
		<a href="javascript:HidePop('Detail');"><img src="/img/btn_popup_close.gif" alt=""></a>
	</div>

	<div class="content-title ">
		<table class="" width="100%">
			<tr>
				<th scope="row" id="pop_detail_title"></th>
				<td style="float:right;" id="pop_detail_log"></td>
			</tr>
		</table>
	</div>

	<div class="content-wrap" id="pop_detail_content">

	</div>

	<div class="btn-wrap" id="pop_detail_modify">
	</div>
</div>

<div id="popApproval" class="approval-popup3" style="display:none">
	<div class="pop_top">
		<p class="pop_title">결재</p>
		<a href="javascript:HidePop('Approval');" class="close">닫기</a>
	</div>
	<div class="pop_body">
	<form name="form3" method="post">
	<input type="hidden" name="doc_no" id="doc_no">
	<input type="hidden" name="order" id="order">
	<input type="hidden" name="pwd" id="pwd" value="<?=$pwd?>">
		<span>
			<input type="radio" name="sign" id="signpwd1" value="결재" checked>
			<label for="signpwd1">결재</label>
		</span>
		<span>
			<input type="radio" name="sign" id="signpwd2" value="전결"> 
			<label for="signpwd2">전결</label>
		</span>
		<span>
			<input type="radio" name="sign" id="signpwd3" value="보류"> 
			<label for="signpwd3">보류</label>
		</span>
		<span>
			<input type="radio" name="sign" id="signpwd4" value="기각"> 
			<label for="signpwd4">기각</label>
		</span>
		<div class="edit_btn" id="approval_btn">
		</div>
	</form>
	</div>
</div>
<div id="popLog" class="approval-popup4" style="display:none">
	<div class="pop_top">
		<p class="pop_title">결재로그</p>
		<a href="javascript:HidePop('Log');" class="close">닫기</a>
	</div>
	<div class="pop_body" id="pop_log_body">
	</div>
</div>

<div ID="popReWrite" class="approval-popup6" style="display:none">
	<form name="form4" method="post">
	<input type="hidden" name="doc_no" id="doc_no">
	<div class="pop_top">
		<p class="pop_title">재상신</p>
		<a href="javascript:HidePop('ReWrite');" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<p class="intra_pop_info">해당 전자결재 문서를 재상신하시겠습니까?</p>
		<div class="edit_btn">
			<a href="javascript:funReWriteOk();"><img src="/img/btn_ok.gif" alt="확인"></a>
			<a href="javascript:HidePop('ReWrite');"><img src="/img/btn_cancel.gif" alt="취소"></a>
		</div>
	</div>
	</form>
</div>
<div id="popPassword" class="approval-popup7" style="display:none">
	<div class="pop_top">
		<p class="pop_title">결재</p>
		<a href="javascript:HidePop('Password');" class="close">닫기</a>
	</div>
	<div class="pop_body">
		<span>결재 비밀번호를 입력해 주세요.</span>
	<form name="form5" method="post">
	<input type="hidden" name="doc_no" id="doc_no2">
	<input type="hidden" name="order" id="order2">
	<input type="hidden" name="pwd" id="pwd2">
	<input type="hidden" name="sign" id="sign2">
		<span><input name="pwd_txt" id="pwd_txt" type="password"></span>
		<div class="adit_btn">
			<a href="javascript:funSignPwd();"><img src="/img/btn_ok.gif" alt="확인" /></a>
			<a href="javascript:HidePop('Password');"><img src="/img/btn_cancel.gif" alt="취소" /></a>
		</div>
	</div>
</div>

</body>
</html>
