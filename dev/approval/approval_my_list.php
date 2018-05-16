<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$searchSQL = " WHERE PRS_ID = '$prs_id' AND STATUS IN ('미결재','진행중') AND USE_YN = 'Y'";

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

<script src="/js/approval.js"></script>
<script>
	$(document).ready(function() {
		// 결제방법 선택처리
		$(document).on("change",".pay_type", function() {
			var type = $(this).val();
			var no = $(this).parent().data("no");

			$("#paytype_B_"+no).hide();
			$("#paytype_C_"+no).hide();
			$("#paytype_P_"+no).hide();
			$("#paytype_A_"+no).hide();
			$("#paytype_H_"+no).hide();
			$("#paytype_"+type+"_"+no).show();
		});

		// 입사구분 선택처리
		$(document).on("change",".employ_type", function() {
			var type = $(this).val();
			var no = $(this).parent().data("no");

			$("#employtype_A_"+no).hide();
			$("#employtype_B_"+no).hide();
			$("#employtype_"+type+"_"+no).show();
		});
	});

	// 총금액 합계 계산
	function sumPayment()
	{
		var frm = document.form2;

		var money_ = 0;
		$(".money_").each(function() {
			var won = $(this).val().replace(/,/g,"");
			if(won > 0)	money_ = money_ + parseInt(won);
		});		

		checkThousand(frm.money_total, String(money_));
	}

	// 청구 선택시 안내문
	function alertType(obj)
	{
		var val = obj.value;

		if(val == "1") {
			alert("클라이언트 청구 견적 포함시 선택.");
		} else if(val == "2") {
			alert("클라이언트 미청구시 선택.");
		} else if(val == "3") {
			<? if ($prs_team != "경영지원팀") { ?>
			alert("경영지원팀 전용코드 입니다(※타 부서에서는 사용불가)");
			obj.checked = false;
			<? } ?>
		}
	}

	// 지급일자 선택시 안내문
	function alertPaydate(obj)
	{
		var val = obj.value;

		if(val == "3") {
			if(!confirm("경영지원팀 결재 일정 확인 후, 상신 필.\n경영지원팀에 확인 하셨습니까?")) {
				obj.checked = false;
			}
		}
	}	
	
	// 자동이체 선택시 안내문
	function alertPaytype(obj)
	{
		var val = obj.value;

		if(val == "A") {
			if(!confirm("경영지원팀 결재 협의 후, 상신 필.\n경영지원팀과 협의 하셨습니까?")) {
				obj.checked = false;
			}
		}
	}

	// 결제정보 추가
	function addPayment()
	{
		if (document.getElementById("payment_1").style.display == "none") {
			document.getElementById("payment_1").style.display = "";
		} else {
			if (document.getElementById("payment_2").style.display == "none") {
				document.getElementById("payment_2").style.display = "";
			} else {
				if (document.getElementById("payment_3").style.display == "none") {
					document.getElementById("payment_3").style.display = "";
				} else {
					if (document.getElementById("payment_4").style.display == "none") {
						document.getElementById("payment_4").style.display = "";
					} else {
						alert("결제정보는 최대 5개까지 가능합니다.");
					}
				}
			}
		}
	}

	// 입사정보 추가
	function addEmploy()
	{
		if (document.getElementById("employ_1").style.display == "none") {
			document.getElementById("employ_1").style.display = "";
		} else {
			if (document.getElementById("employ_2").style.display == "none") {
				document.getElementById("employ_2").style.display = "";
			} else {
				if (document.getElementById("employ_3").style.display == "none") {
					document.getElementById("employ_3").style.display = "";
				} else {
					if (document.getElementById("employ_4").style.display == "none") {
						document.getElementById("employ_4").style.display = "";
					} else {
						alert("입사정보는 최대 5개까지 가능합니다.");
					}
				}
			}
		}
	}
</script>
</head>

<body>
<div id="approval" class="wrapper">
<form name="form" method="post">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="type" value="<?=$type?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
		<? include INC_PATH."/approval_menu.php"; ?>

			<div class="writtenReport-wrap clearfix">
			<? include INC_PATH."/approval_menu2.php"; ?>

				<div class="content-wrap">
					<div class="title clearfix">
						<table class="notable " width="100%">
							<tr>
								<th scope="row">상신문서</th>
							</tr>
						</table>
					</div>

					<table class="content-table" width="100%">
						<caption>상신문서 테이블</caption>
						<colgroup>
							<col width="50px" />
							<col width="75px" />
							<col width="75px" />
							<col width="120px" />
							<col width="*" />
							<col width="55px" />
							<col width="55px" />
							<col width="80px" />
						</colgroup>

						<thead>
							<tr>
								<th>no.</th>
								<th>문서번호</th>
								<th>기안일자</th>
								<th>문서종류</th>
								<th>문서명</th>
								<th>상태</th>
								<th>의견</th>
								<th>재상신</th>
							</tr>
						</thead>

						<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="8" class="bold">해당 정보가 없습니다.</td>
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
								<td><?=$status?></td>
								<td><?=$reply?> 개</td>
								<td class="last"><a href="javascript:funReWrite('<?=$doc_no?>');"><img src="/img/btn_reWrittenReport.gif" alt=""></a></td>
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
								<td><?=$status?></td>
								<td><?=$reply?> 개</td>
								<td class="last"><a href="javascript:funReWrite('<?=$doc_no?>');"><img src="/img/btn_reWrittenReport.gif" alt=""></a></td>
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
				$form_title = str_replace('(v2)','',$record1['FORM_TITLE']);
				$title = $record1['TITLE'];
				$approval_date = $record1['APPROVAL_DATE'];
				$team = $record1['PRS_TEAM'];
				$position = $record1['PRS_POSITION'];
				$name = $record1['PRS_NAME'];
				$status = $record1['STATUS'];
				$reply = $record1['REPLY'];

				// 비용품의서 버전 구분
				if($form_category == "비용품의서" || $form_category == "프로젝트 관련품의서") {		// 구버전 품의서인 경우
					$view_link = "<a href=\"javascript:funView_old('{$doc_no}');\">{$title}</a>";
				} else {							   
					$view_link = "<a href=\"javascript:funView('{$doc_no}');\">{$title}</a>";							   
				}
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?></td>
								<td><?=$view_link?></td>
								<td><?=$status?></td>
								<td><?=$reply?> 개</td>
								<td class="last"><a href="javascript:funReWrite('<?=$doc_no?>');"><img src="/img/btn_reWrittenReport.gif" alt=""></a></td>
							</tr>
<?
			}
			$i--;
		}
	}
?>
						</tbody>	
					</table>

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
		<h3 class="aaa">상신문서 보기</h3>
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
	<input type="hidden" name="doc_no" id="doc_no" value="<?=$doc_no?>">
	<input type="hidden" name="order" id="order" value="<?=$order?>">
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
	<input type="hidden" name="doc_no" id="re_doc_no">
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
