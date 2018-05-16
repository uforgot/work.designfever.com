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

	$searchSQL = " WHERE A.STATUS IN  ('�̰���','������') AND B.A_PRS_ID = '$prs_id' AND B.A_STATUS = '�̰���' AND A.USE_YN = 'Y'";
	
	if ($p_category != "")
	{
		if ($p_category == "ǰ�Ǽ�")
		{
			$searchSQL .= " AND A.FORM_CATEGORY IN ('���ǰ�Ǽ�','������Ʈ ����ǰ�Ǽ�')";
		}
		else
		{
			$searchSQL .= " AND A.FORM_CATEGORY = '$p_category'";
		}
	}
	if ($p_vacation != "")
	{
		if ($p_vacation == "��Ÿ") 
		{
			$searchSQL .= " AND FORM_TITLE IN ('��Ÿ','����ް�','��������','����/�Ʒ�','����')";
		}
		else
		{
			$searchSQL .= " AND FORM_TITLE LIKE '%". $p_vacation ."%'";
		}
	}

	if ($keyword != "")
	{
		if ($keyfield == "�����")
		{
			$searchSQL .= " AND PRS_NAME = '$keyword'";
		}
		else if ($keyfield == "����")
		{
			$searchSQL .= " AND TITLE Like '%". $keyword ."%'";
		}
		else if ($keyfield == "����")
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
								<th scope="row">�̰��繮��</th>
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
						</table>
					</div>

					<div class="content-2">
						<span style="color:#777777;float:right;padding-bottom:5px;">�� ���������� �׸��� "<strong style='color:#eb6100;'>�Ϸ�</strong>"�� ������ Ȯ���Ͽ� ����ó�� ���</span>
						<table class="notable" width="100%">
							<caption>�̰��繮�� ���̺�</caption>
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
									<th>������ȣ</th>
									<th>�������</th>
									<th>��������</th>
									<th>������</th>
									<th>�����</th>
									<th>����������</th>
									<th>����������</th>
									<th class="last">�ǰ�</th>
								</tr>
							</thead>

							<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="9" class="bold">�ش� ������ �����ϴ�.</td>
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

			if ($category == "�ް���")
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

						if ($a_status == "�̰���") { $a_reg_date = "-"; } 
						if ($next_position == "" || $next_name == "") { $next = "-"; } else { $next = $next_position ." ". $next_name; }
					}

					// ���� ������ ���翩�� üũ
					if ($prev_status == "����")			$prev = "<strong style=\"color:#eb6100;\">�Ϸ�</strong>"; 
					else if ($prev_status == "�Ⱒ")	$prev = "�Ⱒ";
					else if ($prev_status == "����")	$prev = "<strong>����</strong>";
					else if ($prev_status == "�̰���")	$prev = "������";
					else								$prev = "-";

					$form_title = "����";
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?> �ް���</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$prev?></td>
								<td><?=$next?></td>
								<td><?=$reply?> ��</td>
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

					if ($a_status == "�̰���") { $a_reg_date = "-"; } 
					if ($next_position == "" || $next_name == "") { $next = "-"; } else { $next = $next_position ." ". $next_name; }

					// ���� ������ ���翩�� üũ
					if ($prev_status == "����")			$prev = "<strong style=\"color:#eb6100;\">�Ϸ�</strong>"; 
					else if ($prev_status == "�Ⱒ")	$prev = "�Ⱒ";
					else if ($prev_status == "����")	$prev = "<strong>����</strong>";
					else if ($prev_status == "�̰���")	$prev = "������";
					else								$prev = "-";
?>
							<tr>
								<td><?=$i?></td>
								<td><?=$doc_no?></td>
								<td><?=$approval_date?></td>
								<td><?=$form_title?> �ް���</td>
								<td><a href="javascript:funView('<?=$doc_no?>');"><?=$title?></a></td>
								<td><?=$position?> <?=$name?></td>
								<td><?=$prev?></td>
								<td><?=$next?></td>
								<td><?=$reply?> ��</td>
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

				if ($a_status == "�̰���") { $a_reg_date = "-"; } 
				if ($next_position == "" || $next_name == "") { $next = "-"; } else { $next = $next_position ." ". $next_name; }

				// ���� ������ ���翩�� üũ
				if ($prev_status == "����")			$prev = "<strong style=\"color:#eb6100;\">�Ϸ�</strong>"; 
				else if ($prev_status == "�Ⱒ")	$prev = "�Ⱒ";
				else if ($prev_status == "����")	$prev = "<strong>����</strong>";
				else if ($prev_status == "�̰���")	$prev = "������";
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
								<td><?=$reply?> ��</td>
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
		<h3 class="aaa">�̰��繮�� ����</h3>
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
