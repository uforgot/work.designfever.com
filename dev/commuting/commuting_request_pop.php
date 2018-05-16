<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;
	$p_date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null;
	$p_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

	// �������� ��� ������
	$sql = "SELECT 
				GUBUN, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2
			FROM 
				DF_CHECKTIME WITH(NOLOCK)
			WHERE 
				PRS_ID = '$p_id' AND DATE = '$p_date'";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$md_gubun = $record['GUBUN'];
		$md_gubun1 = $record['GUBUN1'];
		$md_gubun2 = $record['GUBUN2'];
		$md_checktime1 = $record['CHECKTIME1'];
		$md_checktime2 = $record['CHECKTIME2'];
	}	

	// ���¼��� ��û ������
	$sql = "SELECT 
				TOP 1 *, CONVERT(CHAR(19), REGDATE, 20) as REGDATE, CONVERT(CHAR(19), OK_DATE, 20) as OK_DATE

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
		$rd_ok_date = $record['OK_DATE'];
		$rd_ok_name = $record['OK_NAME'];

		$status_str = array('ING'=>'ó����', 'CANCEL'=>'�ݷ�', 'OK'=>'����');
	} 

	if($mode == "VIEW") {
		$gubun1_disabled = " disabled='disabled'";
		$gubun2_disabled = " disabled='disabled'";
		$memo_disabled = " disabled";
	}

	if(!$rd_gubun1) $rd_gubun1 = $md_gubun1;
	if(!$rd_gubun2) $rd_gubun2 = $md_gubun2;
	if(!$rd_checktime1) $rd_checktime1 = $md_checktime1;
	if(!$rd_checktime2) $rd_checktime2 = $md_checktime2;
?>

<? include INC_PATH."/pop_top.php"; ?>

<script type="text/javascript">
	function modify(){
		var frm = document.form;
		var flg = false;

		//��ٽð��� ��ٽð� ���� ū ���
		/*
		if(frm.gubun1_hour.value + frm.gubun1_minute.value > frm.gubun2_hour.value + frm.gubun2_minute.value){
			alert("����ٽð� �Է��� �߸��Ǿ����ϴ�");
			frm.gubun1_hour.focus();
			return;
		}
		*/

		if ((!frm.gubun1_hour.value && !frm.gubun1_minute.value) 
			&& (!frm.gubun2_hour.value && !frm.gubun2_minute.value)) 
		{
			alert("������û �� ��/��ٽð��� �Է��� �ּ���");
			frm.gubun1_hour.focus();
			return;
		}
		if ((frm.gubun1_hour.value && !frm.gubun1_minute.value) 
			|| (!frm.gubun1_hour.value && frm.gubun1_minute.value))
		{
			alert("��ٽð��� ��Ȯ�ϰ� �Է��� �ּ���");
			frm.gubun1_hour.focus();
			return;
		}
		else if (frm.gubun1_hour.value && frm.gubun1_minute.value) 
		{
			frm.gubun1.value = "1";	
			flg = true;
		}
		if ((frm.gubun2_hour.value && !frm.gubun2_minute.value) 
			|| (!frm.gubun2_hour.value && frm.gubun2_minute.value))
		{
			alert("��ٽð��� ��Ȯ�ϰ� �Է��� �ּ���");
			frm.gubun2_hour.focus();
			return;
		}
		else if (frm.gubun2_hour.value && frm.gubun2_minute.value) 
		{
			frm.gubun2.value = "2";	
			flg = true;
		}

		// 11�� ���� ���, 17�� ���� ����� �ȳ� �޼���
		var gubun1_time = parseInt(frm.gubun1_hour.value+frm.gubun1_minute.value);
		var gubun2_time = parseInt(frm.gubun2_hour.value+frm.gubun2_minute.value);

		if (gubun1_time >= 1100 || gubun2_time <= 1700)
		{
			frm.flag.value = "N"; 
			/*
			if(confirm("11������ ��� �Ǵ� 17������ �����, ���¼�����û �Խ����� �̿��� �ּ���\n�ش� �Խ������� �̵� �Ͻðڽ��ϱ�?")) 
			{
				self.close();
				top.location.href = "/book/book_list.php?board=edit";
			}
			return;
			*/
		}

		if (flg)
		{
			if(!confirm("���¼����� ��û �Ͻðڽ��ϱ�?")) return;

			frm.target="hdnFrame";
			frm.action = 'commuting_request_act.php';
			frm.submit();
		} else {
			alert("��ٽð� �Ǵ� ��ٽð��� �Է��� �ּ���");
			frm.gubun1_hour.focus();
			return;
		}

	}

	function cancel() {
		parent.HidePop('DayEdit');
	}
</script>
</head>
<body style="min-width:280px;">
<form class="inlp" method='post' name='form'>
<input type="hidden" name="prs_login" value="<?=$prs_login?>">
<input type="hidden" name="prs_name" value="<?=$prs_name?>">
<input type="hidden" name="id" value="<?=$p_id?>">
<input type="hidden" name="date" value="<?=$p_date?>">
<input type="hidden" name="flag">
<input type="hidden" name="mode">
<!-- pop -->		 
	<div class="intra_pop2 work_team_pop" style="border:0px;">
		<div class="pop_body">
			<div class="edit_wrap" align="center">
				<table class="notable edit_table" width="80%">
					<summary></summary>
					<colgroup>
						<col width="25%" />
						<col width="30%" />
						<col width="45%" />
					</colgroup>

					<input type="hidden" name="gubun" value="">
					
					<tr>
						<td>
							<input type="hidden" name="gubun1" value=""> <b>���</b>
							<input type="hidden" name="gubun1_prev" value="<?=$rd_checktime1?>">
						</td>
						<td>
							<select name='gubun1_hour' <?=$gubun1_disabled?>>
								<option value="">--</option>
							<?
								for ($i=0; $i<=23; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($rd_checktime1,8,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='gubun1_minute' <?=$gubun1_disabled?>>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($rd_checktime1,10,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<!--select name='gubun2' style="width:100px;">
								<option value="">--</option>
								<option value="2"<? if ($md_gubun2 == "2") { echo " selected"; } ?>>���</option>
								<option value="3"<? if ($md_gubun2 == "3") { echo " selected"; } ?>>����ٹ�</option>
								<option value="6"<? if ($md_gubun2 == "6") { echo " selected"; } ?>>�ܱ�</option>
								<option value="9"<? if ($md_gubun2 == "9") { echo " selected"; } ?>>����</option>
								<option value="5"<? if ($md_gubun2 == "5") { echo " selected"; } ?>>������Ʈ ����</option>
							</select-->
							<input type="hidden" name="gubun2" value=""> <b>���</b>
							<input type="hidden" name="gubun2_prev" value="<?=$rd_checktime2?>">
						</td>
						<td>
							<select name='gubun2_hour' <?=$gubun2_disabled?>>
								<option value="">--</option>
							<?
								for ($i=0; $i<=48; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($rd_checktime2,8,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>&nbsp;&nbsp;&nbsp;:
						</td>
						<td>
							<select name='gubun2_minute' <?=$gubun2_disabled?>>
								<option value="">--</option>
							<?
								for ($i=0; $i<=59; $i++)
								{
									if (strlen($i) == 1) { $j = "0".$i; }
									else { $j = $i; }
							?>
								<option value="<?=$j?>"<? if ($j == substr($rd_checktime2,10,2)) { echo " selected"; } ?>><?=$j?></option>
							<?
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td><b>����</b></td>
						<td colspan="2"><textarea name="memo" <?=$memo_disabled?>><?=$rd_memo?></textarea></td>
					</tr>
					<tr>
						<td><b>���</b></td>
						<td colspan="2"><textarea name="answer" disabled><?=$rd_answer?></textarea></td>
					</tr>
				</table>
			</div>
			<div class="edit_bottom" style="text-align:center;">
			<? 
				if ($mode == "VIEW") 
				{
					if ($rd_status == "OK") 
					{
						echo "* ������ : ". $rd_ok_date. " (". $rd_ok_name. ")";
					}
					else if ($rd_status == "ING")
					{
						echo "* ��û�� : ". $rd_regdate. " (<label style='color:#FF0000'>". $status_str[$rd_status]. "</label>)";
					}
					else if ($rd_status == "CANCEL")
					{
						echo "* �ݷ��� : ". $rd_ok_date. " (". $rd_ok_name. ")";
					}
				} 
				else 
				{
					echo "* ���� ������� �ƴϸ�, �Խ��ǿ� ��û ���";
				}
			?>
			</div>
			<div class="edit_btn">
			<? if ($mode != "VIEW") { ?>
				<a href="javascript:modify();"><img src="../img/btn_ok.gif" alt="ok" /></a>
				<a href="javascript:cancel();"><img src="../img/btn_cancel.gif" alt="cancel" /></a>
			<? } else { ?>
				<a href="javascript:cancel();"><img src="../img/btn_ok.gif" alt="ok" /></a>
			<? } ?>
			</div>
		</div>
	</div>
<!-- //pop -->
</form>
<? include INC_PATH."/pop_bottom.php"; ?>
</body>
</html>
