<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("��ϴ��,Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";  
	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$win = isset($_REQUEST['win']) ? $_REQUEST['win'] : null;  

	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "����" : "";	//����븮 �Ǵ�

	//�ؽ�Ʈ ����
	if ($type == "modify")	
	{
		$type_title1 = "��ȸ/����";
		$type_title2 = "����";
	}
	else if ($type == "write")	
	{
		$type_title1 = "�ۼ�";
		$type_title2 = "�ۼ�";
	}
	
	//���� �̸��� ������ ������ ��ȸ ����
	if (in_array($prs_position,$positionA_arr) || ($prs_position == '����' || $prs_position_tmp == '����'))
	{
		$searchSQL = " WHERE SEQNO = '$seqno'";								
	}
	else
	{
		$searchSQL = " WHERE SEQNO = '$seqno' AND PRS_ID = '$prs_id'";
	}

	//�ְ����� �⺻������ ����
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
		alert("�ش� ���� �������� �ʽ��ϴ�.");
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
		$weekly_complete_yn = $record['COMPLETE_YN'];							//����Ϸ� ����
		$weekly_edit_yn = ($weekly_prs_id == $prs_id) ? "Y" : "N";				//�����ۼ� ����

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

		//����������Ʈ ����Ʈ ����
		//$searchSQL = " WHERE B.PRS_ID = '$weekly_prs_id' AND A.STATUS = 'ING' AND A.END_DATE >= CONVERT(VARCHAR(10),GETDATE(),120) AND A.USE_YN = 'Y'";

		//$searchSQL = " WHERE B.PRS_ID = '$weekly_prs_id' AND DATEADD(DD,7,A.END_DATE) >= CONVERT(VARCHAR(10),GETDATE(),120) AND A.USE_YN = 'Y'";
		$searchSQL = " WHERE B.PRS_ID = '$weekly_prs_id' AND DATEADD(DD,7,A.END_DATE) >= DATEADD(DD,$add,GETDATE()) AND A.USE_YN = 'Y'"; // ���� �ۼ����� ����� ��������..

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
			alert("���� ��������� �������� ���� 100%�� �ƴմϴ�.");
			frm['progress_this[]'][0].focus();
			return;    	
		}

		if(totProgNext != 100) {
			alert("���� ��������� �������� ���� 100%�� �ƴմϴ�.");
			frm['progress_next[]'][0].focus();
			return;    	
		}

		if(chkProgThis >= 0) {
			alert("���������� �´� ���� ��������� �ۼ��� �ּ���.");
			frm['progress_this[]'][chkProgThis].focus();
			return;    				
		}

		if(chkProgNext >= 0) {
			alert("���������� �´� ���� ��������� �ۼ��� �ּ���.");
			frm['progress_next[]'][chkProgNext].focus();
			return;    				
		}

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("������ <?=$type_title2?> �Ͻðڽ��ϱ�")){
			frm.target = "hdnFrame";
			frm.action = 'weekly_write_act.php'; 
			frm.submit();
		}
	}

	function weeklyComplete(type) {
		var frm = document.form;
		var str = '';

		if(type == 'complete') str = "�Ϸ�";
		else if(type == 'cancel') str = "���";

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("�� �ְ����� �ۼ��� " + str + " �Ͻðڽ��ϱ�")){
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
<input type="hidden" name="type" value="<?=$type?>">			<!-- ��ϼ����������� -->
<input type="hidden" name="seqno" value="<?=$seqno?>">			<!-- �۹�ȣ -->
<input type="hidden" name="order" value="<?=$weekly_ord?>">		<!-- �������� -->
<input type="hidden" name="win" value="<?=$win?>">				<!-- ��â���¿��� -->

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/weekly_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="vacation_stats clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row"><?=$weekly_prs_nm?><br>(<?=$weekly_str?>) <?=$weekly_title?> <?=$type_title1?></th>
<!-- 						<th width="50%" scope="row">���� �ְ�����</th> -->
						</tr>
					</table>
				</div>
				<span style="padding-left:38px;">
					<b class="txt_left_p" style="margin-bottom:30px; margin-top:0px">
						- ���� ���� ������Ʈ�� ���� ���, ���� ���� ������Ʈ���� ���Ұ� �������� ����� �� �ְ������� �ۼ��� �ּ���.</br>
						- ������Ʈ �� �������� ���� 100% �Դϴ�.</br>
						- �� �ְ����� �ۼ��ϷḦ �� ��쿡�� �������� �ְ������ ������ �� �����ϴ�.</br>
					</b>
				</span>

<!-- ������Ʈ ����Ʈ ���� -->
<?
		$cnt = 0;
		while ($record = sqlsrv_fetch_array($rs))
		{
			$project_no = $record['PROJECT_NO'];
			$title = $record['TITLE'];
			$part = $record['PART'];

			//�ְ����� ����, ����
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
			//�ְ����� �ű� �ۼ�
			else if ($type == "write")
			{
				//������ ���� ������ȹ�� ���� ��������� �Ҵ�
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
				<!-- weekly routine ���� -->
				<div class="board_list" style="margin-bottom:40px;">
					<table class="notable work3 board_list"  style="width:100%">
						<caption>�Խ��� ����Ʈ ���̺�</caption>
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
								<td>���� ������� 
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
								<td style="font-weight:bold;">���� ������� 
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
				<!-- weekly routine ���� -->
<?
			$cnt++;
		}
?>
<!-- ������Ʈ ����Ʈ ���� -->

<!-- ��Ÿ���� �׸� ���� -->
<?
		$project_no_etc = "DF0000_ETC"; //��Ÿ������ �Ҵ��� ������Ʈ �ڵ�

		//�ְ����� ����, ����
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
		//�ְ����� �ű� �ۼ�
		else if ($type == "write")
		{
			//������ ���� ������ȹ�� ���� ��������� �Ҵ�
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
						<caption>�Խ��� ����Ʈ ���̺�</caption>
						<colgroup>
							<col width="49%" />
							<col width="2%" />
							<col width="*" />
						</colgroup>
						
						<tbody class="p_detail">
							<tr>
								<td style="font-weight:bold;" colspan="3">* ��Ÿ����(�濵������, ȫ����, ��Ÿ ����)</td>
								<input type="hidden" name="project_no[]" value="DF0000_ETC">
							</tr>
							<tr>
								<td>���� ������� 
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
								<td style="font-weight:bold;">���� ������� 
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

					<!-- �ʵ�迭 ó������ ���� �±� -->
					<input type="hidden" name="project_no[]">
					<input type="hidden" name="progress_this[]">
					<input type="hidden" name="progress_next[]">
					<input type="hidden" name="content_this[]">
					<input type="hidden" name="content_next[]">

				</div>
<!-- ��Ÿ���� �׸� ���� -->

<!-- (����)���ǻ��� �׸� ���� -->
<?
	if ($weekly_prs_pos == '����') {
?>
				<div class="board_list" style="margin-bottom:0px;">
					<table class="notable work3 board_list"  style="width:100%">
						<caption>�Խ��� ����Ʈ ���̺�</caption>
						<colgroup>
							<col width="100%" />
						</colgroup>
						
						<tbody class="p_detail">
							<tr>
								<td style="font-weight:bold;" colspan="3">* ���� �� ��Ÿ����</td>
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
<!-- (����)���ǻ��� �׸� ���� -->

				<div class="project_reg clearfix" style="margin-bottom:40px;">
					<div class="btns_wrap" style="float:left;margin-top:0px;">
					<? if (($prs_position == '����' ||  $prs_position_tmp == '����') && $weekly_edit_yn == 'Y') { ?> 
						<? if ($weekly_complete_yn != 'Y') { ?>						
						<a href="javascript:weeklyComplete('complete');"><img src="/img/weekly/btn_weekly_team.png" alt="�Ϸ�" id="btnComplete" style="cursor:pointer;"></a>					
						<? } else { ?>
						<a href="javascript:weeklyComplete('cancel');">[�� �ְ����� �Ϸ� ���]</a>
						<? } ?>
					<? } ?>
					</div>
					<div class="btns_wrap btn_right" style="margin-top:0px;">
						<? if ($weekly_complete_yn != 'Y' && $weekly_edit_yn == 'Y') { ?>						
						<a href="javascript:weeklyWrite();"><img src="/img/weekly/btn_save.gif" alt="���" id="btnWrite" style="cursor:pointer;"></a>
						<? } ?>
						<? if ($win == 'new') { ?>						
						<a href="javascript:window.close();"><img src="/img/weekly/btn_cancle.gif" alt="���" id="btnCancel" style="cursor:pointer;"></a>
						<? } else { ?>
						<a href="./weekly_list.php?page=<?=$page?>"><img src="/img/weekly/btn_cancle.gif" alt="���" id="btnCancel" style="cursor:pointer;"></a>
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
