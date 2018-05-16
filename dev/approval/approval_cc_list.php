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

	$searchSQL = " WHERE B.C_PRS_ID = '$prs_id' AND A.USE_YN = 'Y' AND CONVERT(char(10),A.REG_DATE,120) BETWEEN '$fr_date' AND '$to_date'";
	
	if ($p_category != "") {
		if ($p_category == "ǰ�Ǽ�") {
			$searchSQL .= " AND A.FORM_CATEGORY IN ('���ǰ�Ǽ�(v2)','���ǰ�Ǽ�','������Ʈ ����ǰ�Ǽ�')";
		} else {
			$searchSQL .= " AND A.FORM_CATEGORY = '$p_category'";
		}
	}

	if ($p_vacation != "") {
		if ($p_vacation == "��Ÿ") {
			$searchSQL .= " AND FORM_TITLE IN ('��Ÿ','����ް�','��������','����/�Ʒ�','����')";
		} else {
			$searchSQL .= " AND FORM_TITLE LIKE '%". $p_vacation ."%'";
		}
	}

	if ($keyword != "") {
		if ($keyfield == "�����") {
			$searchSQL .= " AND PRS_NAME = '$keyword'";
		} else if ($keyfield == "����") {
			$searchSQL .= " AND TITLE Like '%". $keyword ."%'";
		} else if ($keyfield == "����") {
			$searchSQL .= " AND CONTENTS Like '%". $keyword ."%'";
		}
	}

	$sql = "SELECT COUNT(DISTINCT A.DOC_NO) FROM DF_APPROVAL A WITH(NOLOCK) INNER JOIN DF_APPROVAL_CC B WITH(NOLOCK) ON A.DOC_NO = B.DOC_NO". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT 
				T.DOC_NO, T.FORM_CATEGORY, T.COUNT, T.C_READ_YN
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(ORDER BY A.DOC_NO DESC) AS ROWNUM,
					A.DOC_NO, A.FORM_CATEGORY, COUNT(A.SEQNO) AS COUNT, B.C_READ_YN
				FROM 
					DF_APPROVAL A WITH(NOLOCK) INNER JOIN DF_APPROVAL_CC B WITH(NOLOCK)
				ON 
					A.DOC_NO = B.DOC_NO
				$searchSQL
				GROUP BY 
					A.DOC_NO, A.FORM_CATEGORY, B.C_READ_YN
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
		//��¥ ����
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
<script>
	$(document).ready(function() {
		// ������� ����ó��
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

		// �Ի籸�� ����ó��
		$(document).on("change",".employ_type", function() {
			var type = $(this).val();
			var no = $(this).parent().data("no");

			$("#employtype_A_"+no).hide();
			$("#employtype_B_"+no).hide();
			$("#employtype_"+type+"_"+no).show();
		});
	});

	// �ѱݾ� �հ� ���
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

	// û�� ���ý� �ȳ���
	function alertType(obj)
	{
		var val = obj.value;

		if(val == "1") {
			alert("Ŭ���̾�Ʈ û�� ���� ���Խ� ����.");
		} else if(val == "2") {
			alert("Ŭ���̾�Ʈ ��û���� ����.");
		} else if(val == "3") {
			<? if ($prs_team != "�濵������") { ?>
			alert("�濵������ �����ڵ� �Դϴ�(��Ÿ �μ������� ���Ұ�)");
			obj.checked = false;
			<? } ?>
		}
	}

	// �������� ���ý� �ȳ���
	function alertPaydate(obj)
	{
		var val = obj.value;

		if(val == "3") {
			if(!confirm("�濵������ ���� ���� Ȯ�� ��, ��� ��.\n�濵�������� Ȯ�� �ϼ̽��ϱ�?")) {
				obj.checked = false;
			}
		}
	}	
	
	// �ڵ���ü ���ý� �ȳ���
	function alertPaytype(obj)
	{
		var val = obj.value;

		if(val == "A") {
			if(!confirm("�濵������ ���� ���� ��, ��� ��.\n�濵�������� ���� �ϼ̽��ϱ�?")) {
				obj.checked = false;
			}
		}
	}

	// �������� �߰�
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
						alert("���������� �ִ� 5������ �����մϴ�.");
					}
				}
			}
		}
	}

	// �Ի����� �߰�
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
						alert("�Ի������� �ִ� 5������ �����մϴ�.");
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

			<div class="tempStorage-wrap clearfix">
			<? include INC_PATH."/approval_menu2.php"; ?>

				<div class="content-wrap">
					<div class="title clearfix">
						<table class="notable " width="100%">
							<tr>
								<th scope="row">���� ������</th>
								<td style="float:right;">
									<a href="javascript:ShowPop('StatusDesc');"><img src="/img/btn_approveState.gif" alt="���ڰ��� ����ǥ" /></a>
								</td>
							</tr>
						</table>
					</div>

					<div class="content-1">
						<table class="notable" width="100%">
							<tr class="a1">
								<th>�˻�</th>
								<td>
									<div class="btns">
										<a href="javascript:funSearch(this.form,'<?=CURRENT_PAGE?>');"><img src="/img/btn_search_p.gif" alt="�˻�" /></a>
										<a href="<?=CURRENT_URL?>"><img src="/img/btn_reset_p.gif" alt="�˻� �ʱ�ȭ" /></a>
									</div>
								</td>
							</tr>
							<tr>
								<th>���繮�����</th>
								<td>
									<select name="category" onChange="javascript:selCase(this.form);" style="width:120px;">
										<option value="">��ü</option>
										<option value="�Ի���ΰ�"<? if ($p_category == "�Ի���ΰ�") { echo " selected"; } ?>>�Ի���ΰ�</option>
										<option value="ǰ�Ǽ�"<? if ($p_category == "ǰ�Ǽ�") { echo " selected"; } ?>>ǰ�Ǽ�</option>
										<option value="�ٰܱ�/�İ߰�"<? if ($p_category == "�ٰܱ�/�İ߰�") { echo " selected"; } ?>>�ٰܱ�/�İ߰�</option>
										<option value="�ް���"<? if ($p_category == "�ް���") { echo " selected"; } ?>>�ް���</option>
										<option value="�����"<? if ($p_category == "�����") { echo " selected"; } ?>>�����</option>
										<option value="������"<? if ($p_category == "������") { echo " selected"; } ?>>������</option>
										<option value="�ø���"<? if ($p_category == "�ù���") { echo " selected"; } ?>>�ø���</option>
										<option value="�����"<? if ($p_category == "�����") { echo " selected"; } ?>>�����</option>
									</select>
									<select name="vacation" style="display:<? if ($p_category == "�ް���") { echo " inline"; } else { echo " none"; } ?>; width:120px;">
										<option value="">��ü</option>
										<option value="����"<? if ($p_vacation == "����") { echo " selected"; } ?>>����</option>
										<option value="����"<? if ($p_vacation == "����") { echo " selected"; } ?>>����</option>
										<option value="����"<? if ($p_vacation == "����") { echo " selected"; } ?>>����</option>
										<option value="��������"<? if ($p_vacation == "��������") { echo " selected"; } ?>>��������</option>
										<option value="������Ʈ"<? if ($p_vacation == "������Ʈ") { echo " selected"; } ?>>������Ʈ</option>
										<option value="����"<? if ($p_vacation == "����") { echo " selected"; } ?>>����</option>
										<option value="������"<? if ($p_vacation == "������") { echo " selected"; } ?>>������</option>
										<option value="����"<? if ($p_vacation == "����") { echo " selected"; } ?>>����/�ι���</option>
										<option value="��Ÿ"<? if ($p_vacation == "��Ÿ") { echo " selected"; } ?>>��Ÿ</option>
										<option value="�ް� ������"<? if ($p_vacation == "�ް� ������") { echo " selected"; } ?>>�ް� ������</option>
									</select>
								</td>
							</tr>
							<tr>
								<th>�˻���</th>
								<td>
									<select name="keyfield" style="width:120px;">
										<option value="�����"<? if ($keyfield=="�����") { echo " selected"; } ?>>�����</option>
										<option value="����"<? if ($keyfield=="����") { echo " selected"; } ?>>����</option>
										<option value="����"<? if ($keyfield=="����") { echo " selected"; } ?>>����</option>
									</select>
									<input id="keyword" type="text" name="keyword" value="<?=$keyword?>" style="width:213px;" /><br>
								</td>
							</tr>
							<tr class="period">
								<th>�������</th>
								<td class="last">
									<select name="fr_year" id="fr_year">
									<?
										for ($i=$startYear; $i<=($fr_year+1); $i++) {
											if ($i == $fr_year) { 
												$selected = " selected"; 
											} else {
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="fr_month" id="fr_month">
									<?
										for ($i=1; $i<=12; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == $fr_month) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="fr_day" id="fr_day">
									<?
										for ($i=1; $i<=31; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == $fr_day) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<input type="hidden" id="fr_date" class="datepicker">
									<span>-</span>
									<select name="to_year" id="to_year">
									<?
										for ($i=$startYear; $i<=($to_year+1); $i++) {
											if ($i == $to_year) { 
												$selected = " selected"; 
											} else {
												$selected = "";
											}

											echo "<option value='".$i."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="to_month" id="to_month">
									<?
										for ($i=1; $i<=12; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == $to_month) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<select name="to_day" id="to_day">
									<?
										for ($i=1; $i<=31; $i++) {
											if (strlen($i) == "1") {
												$j = "0".$i;
											} else {
												$j = $i;
											}

											if ($j == $to_day) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='".$j."'".$selected.">".$i."</option>";
										}
									?>
									</select>
									<span>��</span>
									<input type="hidden" id="to_date" class="datepicker">
								</td>
							</tr>
						</table>
					</div>

					<div class="content-2">
						<table class="notable" width="100%">
							<caption>�������� ���̺�</caption>
							<colgroup>
								<col width="50px" />
								<col width="70px" />
								<col width="70px" />
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
									<th>������ȣ</th>
									<th>�������</th>
									<th>�����</th>
									<th>��������</th>
									<th>������</th>
									<th>����</th>
									<th>�ǰ�</th>
									<th class="last">����</th>
								</tr>
							</thead>

							<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) {
?>
							<tr>
								<td colspan="8" class="bold">�ش� ������ �����ϴ�.</td>
							</tr>
<?
	} else {
		while ($record = sqlsrv_fetch_array($rs)) {
			$doc_no = $record['DOC_NO'];
			$count = $record['COUNT'];
			$category = $record['FORM_CATEGORY'];
			$read_yn = $record['C_READ_YN'];

			if ($category == "�ް���") {
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
				
				if ($count == 2) {
					$vacation = "";
					while ($record1 = sqlsrv_fetch_array($rs1)) {
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

					$form_title = "����";

					if ($read_yn == "N") { $title = "<strong>". $title ."</strong>"; }
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$form_title?> �ް���</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td><?=$status?></td>
								<td><?=$reply?> ��</td>
								<td class="last"></td>
							</tr>
<?
				} else {
					$form_category = $record1['FORM_CATEGORY'];
					$form_title = $record1['FORM_TITLE'];
					$title = $record1['TITLE'];
					$approval_date = $record1['APPROVAL_DATE'];
					$team = $record1['PRS_TEAM'];
					$position = $record1['PRS_POSITION'];
					$name = $record1['PRS_NAME'];
					$status = $record1['STATUS'];
					$reply = $record1['REPLY'];

					if ($read_yn == "N") { $title = "<strong>". $title ."</strong>"; }
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$form_title?> �ް���</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td><?=$status?></td>
								<td><?=$reply?> ��</td>
								<td class="last"></td>
							</tr>
<?
				}
			} else {
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

				// ���ǰ�Ǽ� ���� ����
				if($form_category == "���ǰ�Ǽ�" || $form_category == "������Ʈ ����ǰ�Ǽ�") {		// ������ ǰ�Ǽ��� ���
					$view_link = "<a href=\"javascript:funView_old('{$doc_no}');\">{$title}</a>";
				} else {							   
					$view_link = "<a href=\"javascript:funView('{$doc_no}');\">{$title}</a>";						   
				}
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$form_title?></td>
								<td><?=$view_link?></td>
								<td><?=$status?></td>
								<td><?=$reply?> ��</td>
								<td class="last">
								<? if ($payment_yn == "����") { ?>
									<img src="/img/state_check.gif" alt="">
								<? 
									} 
								?>
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
		<p class="pop_title">���ڰ��� ����ǥ</p>
		<a href="javascript:HidePop('StatusDesc');" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
		<p><strong>�̰���: </strong>���縦 ���� ���� ����</p>
		<p><strong>�� ��: </strong>���缭���� ���ؼ� ������ ��� �Ϸ�� ����</p>
		<p><strong>�� ��: </strong>���� �����ڷ� �Ѿ�� �ʰ� ����(���ڰ��� ����)</p>
		<p><strong>�� ��: </strong>���� �����ڷ� �Ѿ�� �ʰ� ����(���ڰ��� ����)</p>
		<p><strong>�� ��: </strong>���� �����ڷ� �Ѿ�� �ʰ� �̽���(���ڰ��� ����)</p>
		<p><strong>������: </strong>���� ���ڰ��簡 ������ �Դϴ�.</p>
	</div>
</div>

<div id="popDetail" class="approval-popup2" style="display:none;">
	<div class="title">
		<h3 class="aaa">�������� ����</h3>
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
		<p class="pop_title">����</p>
		<a href="javascript:HidePop('Approval');" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
	<form name="form3" method="post">
	<input type="hidden" name="doc_no" id="doc_no">
	<input type="hidden" name="order" id="order">
	<input type="hidden" name="pwd" id="pwd">
		<span>
			<input type="radio" name="sign" id="signpwd1" value="����" checked>
			<label for="signpwd1">����</label>
		</span>
		<span>
			<input type="radio" name="sign" id="signpwd2" value="����"> 
			<label for="signpwd2">����</label>
		</span>
		<span>
			<input type="radio" name="sign" id="signpwd3" value="����"> 
			<label for="signpwd3">����</label>
		</span>
		<span>
			<input type="radio" name="sign" id="signpwd4" value="�Ⱒ"> 
			<label for="signpwd4">�Ⱒ</label>
		</span>
		<div class="edit_btn" id="approval_btn">
		</div>
	</form>
	</div>
</div>
<div id="popLog" class="approval-popup4" style="display:none">
	<div class="pop_top">
		<p class="pop_title">����α�</p>
		<a href="javascript:HidePop('Log');" class="close">�ݱ�</a>
	</div>
	<div class="pop_body" id="pop_log_body">
	</div>
</div>
<div id="popPassword" class="approval-popup7" style="display:none">
	<div class="pop_top">
		<p class="pop_title">����</p>
		<a href="javascript:HidePop('Password');" class="close">�ݱ�</a>
	</div>
	<div class="pop_body">
		<span>���� ��й�ȣ�� �Է��� �ּ���.</span>
	<form name="form5" method="post">
	<input type="hidden" name="doc_no" id="doc_no2">
	<input type="hidden" name="order" id="order2">
	<input type="hidden" name="pwd" id="pwd2">
	<input type="hidden" name="sign" id="sign2">
		<span><input name="pwd_txt" id="pwd_txt" type="password"></span>
		<div class="adit_btn">
			<a href="javascript:funSignPwd();"><img src="/img/btn_ok.gif" alt="Ȯ��" /></a>
			<a href="javascript:HidePop('Password');"><img src="/img/btn_cancel.gif" alt="���" /></a>
		</div>
	</div>
</div>

</body>
</html>