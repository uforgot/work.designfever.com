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

	$searchSQL = " WHERE A.STATUS IN  ('미결재','진행중') AND B.A_PRS_ID = '$prs_id' AND B.A_STATUS = '미결재' AND A.USE_YN = 'Y'";
	
	if ($p_category != "")
	{
		if ($p_category == "품의서")
		{
			$searchSQL .= " AND A.FORM_CATEGORY IN ('비용품의서','프로젝트 관련품의서')";
		}
		else
		{
			$searchSQL .= " AND A.FORM_CATEGORY = '$p_category'";
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

	$sql = "SELECT COUNT(DISTINCT A.DOC_NO) FROM DF_APPROVAL A WITH(NOLOCK) INNER JOIN DF_APPROVAL_TO B WITH(NOLOCK) ON A.DOC_NO = B.DOC_NO". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT 
				T.DOC_NO, T.FORM_CATEGORY, T.COUNT
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(ORDER BY A.DOC_NO DESC) AS ROWNUM,
					A.DOC_NO, A.FORM_CATEGORY, COUNT(A.SEQNO) AS COUNT
				FROM 
					DF_APPROVAL A WITH(NOLOCK) INNER JOIN DF_APPROVAL_TO B WITH(NOLOCK)
				ON 
					A.DOC_NO = B.DOC_NO
				$searchSQL
				GROUP BY 
					A.DOC_NO, A.FORM_CATEGORY
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

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
								<th scope="row">미결재문서</th>
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
						</table>
					</div>

					<div class="content-2">
						<span style="color:#777777;float:right;padding-bottom:5px;">※ 이전결재자 항목이 "<strong style='color:#eb6100;'>완료</strong>"인 문서를 확인하여 결재처리 요망</span>
						<table class="notable" width="100%">
							<caption>미결재문서 테이블</caption>
							<colgroup>
								<col width="50px" />
								<col width="70px" />
								<col width="70px" />
								<col width="120px" />
								<col width="*" />
								<col width="75px" />
								<col width="75px" />
								<col width="75px" />
								<col width="55px" />
							</colgroup>

							<thead>
								<tr>
									<th>no.</th>
									<th>문서번호</th>
									<th>기안일자</th>
									<th>문서종류</th>
									<th>문서명</th>
									<th>기안자</th>
									<th>이전결재자</th>
									<th>다음결재자</th>
									<th class="last">의견</th>
								</tr>
							</thead>

							<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="9" class="bold">해당 정보가 없습니다.</td>
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
							A.TITLE, CONVERT(char(10),A.APPROVAL_DATE,102) AS APPROVAL_DATE, A.PRS_TEAM, A.PRS_POSITION, A.PRS_NAME, A.STATUS, A.FORM_CATEGORY, A.FORM_TITLE, 
							CONVERT(char(10),B.A_REG_DATE,102) AS A_REG_DATE, B.A_STATUS, B.A_ORDER, 
							(SELECT TOP 1 A_STATUS FROM DF_APPROVAL_TO WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND A_ORDER < B.A_ORDER ORDER BY A_ORDER DESC) AS PREV_STATUS, 
							(SELECT TOP 1 A_PRS_POSITION FROM DF_APPROVAL_TO WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND A_ORDER > B.A_ORDER ORDER BY A_ORDER) AS NEXT_POSITION, 
							(SELECT TOP 1 A_PRS_NAME FROM DF_APPROVAL_TO WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND A_ORDER > B.A_ORDER ORDER BY A_ORDER) AS NEXT_NAME, 
							(SELECT ISNULL(COUNT(R_SEQNO),0) FROM DF_APPROVAL_REPLY WITH(NOLOCK) WHERE DOC_NO = '$doc_no') AS REPLY 
						FROM 
							DF_APPROVAL A WITH(NOLOCK) INNER JOIN DF_APPROVAL_TO B WITH(NOLOCK)
						ON
							A.DOC_NO = B.DOC_NO
						WHERE
							A.DOC_NO = '$doc_no' AND B.A_PRS_ID = '$prs_id'
						ORDER BY 
							A.SEQNO";
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
						$a_reg_date = $record1['A_REG_DATE'];
						$a_status = $record1['A_STATUS'];
						$a_order = $record1['A_ORDER'];
						$prev_status = $record1['PREV_STATUS'];
						$next_position = $record1['NEXT_POSITION'];
						$next_name = $record1['NEXT_NAME'];
						$reply = $record1['REPLY'];

						if ($a_status == "미결재") { $a_reg_date = "-"; } 
						if ($next_position == "" || $next_name == "") { $next = "-"; } else { $next = $next_position ." ". $next_name; }
					}

					// 이전 결재자 결재여부 체크
					if ($prev_status == "결재")			$prev = "<strong style=\"color:#eb6100;\">완료</strong>"; 
					else if ($prev_status == "기각")	$prev = "기각";
					else if ($prev_status == "전결")	$prev = "<strong>전결</strong>";
					else if ($prev_status == "미결재")	$prev = "진행중";
					else								$prev = "-";

					$form_title = "연프";
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?> 휴가계</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$prev?></td>
								<td><?=$next?></td>
								<td><?=$reply?> 개</td>
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
					$a_reg_date = $record1['A_REG_DATE'];
					$a_status = $record1['A_STATUS'];
					$a_order = $record1['A_ORDER'];
					$prev_status = $record1['PREV_STATUS'];
					$next_position = $record1['NEXT_POSITION'];
					$next_name = $record1['NEXT_NAME'];
					$reply = $record1['REPLY'];

					if ($a_status == "미결재") { $a_reg_date = "-"; } 
					if ($next_position == "" || $next_name == "") { $next = "-"; } else { $next = $next_position ." ". $next_name; }

					// 이전 결재자 결재여부 체크
					if ($prev_status == "결재")			$prev = "<strong style=\"color:#eb6100;\">완료</strong>"; 
					else if ($prev_status == "기각")	$prev = "기각";
					else if ($prev_status == "전결")	$prev = "<strong>전결</strong>";
					else if ($prev_status == "미결재")	$prev = "진행중";
					else								$prev = "-";
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?> 휴가계</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$prev?></td>
								<td><?=$next?></td>
								<td><?=$reply?> 개</td>
							</tr>
<?
				}
			}
			else
			{
				$sql1 = "SELECT TOP 1
							A.TITLE, CONVERT(char(10),A.APPROVAL_DATE,102) AS APPROVAL_DATE, A.PRS_TEAM, A.PRS_POSITION, A.PRS_NAME, A.STATUS, A.FORM_CATEGORY, A.FORM_TITLE, 
							CONVERT(char(10),B.A_REG_DATE,102) AS A_REG_DATE, B.A_STATUS, B.A_ORDER, 
							(SELECT TOP 1 A_STATUS FROM DF_APPROVAL_TO WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND A_ORDER < B.A_ORDER ORDER BY A_ORDER DESC) AS PREV_STATUS, 
							(SELECT TOP 1 A_PRS_POSITION FROM DF_APPROVAL_TO WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND A_ORDER > B.A_ORDER ORDER BY A_ORDER) AS NEXT_POSITION, 
							(SELECT TOP 1 A_PRS_NAME FROM DF_APPROVAL_TO WITH(NOLOCK) WHERE DOC_NO = '$doc_no' AND A_ORDER > B.A_ORDER ORDER BY A_ORDER) AS NEXT_NAME, 
							(SELECT ISNULL(COUNT(R_SEQNO),0) FROM DF_APPROVAL_REPLY WITH(NOLOCK) WHERE DOC_NO = '$doc_no') AS REPLY 
						FROM 
							DF_APPROVAL A WITH(NOLOCK) INNER JOIN DF_APPROVAL_TO B WITH(NOLOCK)
						ON
							A.DOC_NO = B.DOC_NO
						WHERE
							A.DOC_NO = '$doc_no' AND B.A_PRS_ID = '$prs_id'
						ORDER BY 
							A.SEQNO";
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
				$a_reg_date = $record1['A_REG_DATE'];
				$a_status = $record1['A_STATUS'];
				$a_order = $record1['A_ORDER'];
				$prev_status = $record1['PREV_STATUS'];
				$next_position = $record1['NEXT_POSITION'];
				$next_name = $record1['NEXT_NAME'];
				$reply = $record1['REPLY'];

				if ($a_status == "미결재") { $a_reg_date = "-"; } 
				if ($next_position == "" || $next_name == "") { $next = "-"; } else { $next = $next_position ." ". $next_name; }

				// 이전 결재자 결재여부 체크
				if ($prev_status == "결재")			$prev = "<strong style=\"color:#eb6100;\">완료</strong>"; 
				else if ($prev_status == "기각")	$prev = "기각";
				else if ($prev_status == "전결")	$prev = "<strong>전결</strong>";
				else if ($prev_status == "미결재")	$prev = "진행중";
				else								$prev = "-";
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?></td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$prev?></td>
								<td><?=$next?></td>
								<td><?=$reply?> 개</td>
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
		<h3 class="aaa">미결재문서 보기</h3>
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
	<input type="hidden" name="pwd" id="pwd">
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
