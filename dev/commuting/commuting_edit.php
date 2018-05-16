<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : null; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null; 
	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null; 

	if ($seqno == "")
	{
		$md_prs_id = $prs_id;
		$md_date = "";
		$md_chk_gubun1 = "";
		$md_chk_gubun2 = "";
		$md_chk_off1 = "";
		$md_chk_off2 = "";
		$md_chk_off3 = "";
		$md_chk_off4 = "";
		$md_chk_off5 = "";
		$md_starttime = "";
		$md_endtime = "";
		$md_memo = "";
		$md_out_chk = "";
		$md_business_trip = "";
		$md_off1_seqno = "";
		$md_off2_seqno = "";
		$md_off3_seqno = "";
		$md_off4_seqno = "";
		$md_off5_seqno = "";
		$md_off1_starttime = "";
		$md_off1_endtime = "";
		$md_off2_starttime = "";
		$md_off2_endtime = "";
		$md_off3_starttime = "";
		$md_off3_endtime = "";
		$md_off4_starttime = "";
		$md_off4_endtime = "";
		$md_off5_starttime = "";
		$md_off5_endtime = "";
		$md_edit_ok = "N";
		$md_ok_date = "";
		$md_reg_date = "";

		if ($date != "")
		{
			$md_date = $date;
			$sql = "SELECT 
						CHECKTIME1, CHECKTIME2, OUT_CHK
					FROM 
						DF_CHECKTIME WITH(NOLOCK)
					WHERE
						DATE = '$date' AND PRS_ID = '$prs_id'";
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);
			if (sizeof($record) > 0)
			{
				$md_starttime = substr($record['CHECKTIME1'],8,4);
				$md_endtime = substr($record['CHECKTIME2'],8,4);
				$md_out_chk = $record['OUT_CHK'];
			}
		}
	}
	else
	{
		$sql = "SELECT 
					PRS_ID, DATE, CHK_GUBUN1, CHK_GUBUN2, CHK_OFF1, CHK_OFF2, CHK_OFF3, CHK_OFF4, CHK_OFF5, 
					STARTTIME, ENDTIME, MEMO, OUT_CHK, BUSINESS_TRIP,
					OFF_SEQNO1, OFF_SEQNO2, OFF_SEQNO3, OFF_SEQNO4, OFF_SEQNO5,
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
			$md_chk_off4 = $record['CHK_OFF5'];
			$md_starttime = $record['STARTTIME'];
			$md_endtime = $record['ENDTIME'];
			$md_memo = $record['MEMO'];
			$md_out_chk = $record['OUT_CHK'];
			$md_business_trip = $record['BUSINESS_TRIP'];
			$md_off1_seqno = $record['OFF_SEQNO1'];
			$md_off2_seqno = $record['OFF_SEQNO2'];
			$md_off3_seqno = $record['OFF_SEQNO3'];
			$md_off4_seqno = $record['OFF_SEQNO4'];
			$md_off5_seqno = $record['OFF_SEQNO5'];
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
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		//근무일 선택
		$("#date_hd").datepicker({
			onSelect: function (selectedDate) {
				$("#date").val( selectedDate.substring(6,10) +"-"+ selectedDate.substring(0,2) +"-"+ selectedDate.substring(3,5) );

				$("#form").attr("target","_self");
				$("#form").attr("action","commuting_edit.php"); 
				$("#form").submit();
			}
		});

		//파견 여부
		$("#exception").click(function(){
			if ($("#exception").is(":checked"))
			{
				$("#gubun1").val("1");
				$("#gubun1_hour").val("09");
				$("#gubun1_minute").val("00");
				$("#gubun2").val("2");
				$("#gubun2_hour").val("18");
				$("#gubun2_minute").val("00");
			}
			else
			{
				$("#gubun1").val("");
				$("#gubun1_hour").val("");
				$("#gubun1_minute").val("");
				$("#gubun2").val("");
				$("#gubun2_hour").val("");
				$("#gubun2_minute").val("");
			}
		});

		//요청
		$("#btnEdit").attr("style","cursor:pointer;").click(function(){
			if ($("#date").val() == "")
			{
				alert("수정할 근무일을 선택해 주세요.");
				$("#date").focus();
				return;	
			}
			if ($("#chk_gubun1").is(":checked") == false && $("#chk_gubun2").is(":checked") == false && $("#chk_off1").is(":checked") == false && $("#chk_off2").is(":checked") == false && $("#chk_off3").is(":checked") == false && $("#chk_off4").is(":checked") == false && $("#chk_off5").is(":checked") == false)
			{
				alert("수정할 항목을 하나 이상 선택해 주세요.");
				$("#chk_gubun1").focus();
				return;	
			}
			if ($("#chk_gubun1").is(":checked"))
			{
				if ($("#gubun1_hour").val() == "")
				{
					alert("출근시간(시)을 올바르게 선택해 주세요.");
					$("#gubun1_hour").focus();
					return;	
				}
				if ($("#gubun1_minute").val() == "")
				{
					alert("출근시간(분)을 올바르게 선택해 주세요.");
					$("#gubun1_minute").focus();
					return;	
				}
			}
			if ($("#chk_gubun2").is(":checked"))
			{
				if ($("#gubun2_hour").val() == "")
				{
					alert("퇴근시간(시)을 올바르게 선택해 주세요.");
					$("#gubun2_hour").focus();
					return;	
				}
				if ($("#gubun2_minute").val() == "")
				{
					alert("퇴근시간(분)을 올바르게 선택해 주세요.");
					$("#gubun2_minute").focus();
					return;	
				}
			}
			for (var i=1; i<=5; i++)
			{
				if ($("#chk_off"+i).is(":checked"))
				{
					if ($("#off"+i+"_start_hour").val() == "")
					{
						alert("외출시간(시)을 올바르게 선택해 주세요.");
						$("#off"+i+"_start_hour").focus();
						return;	
					}
					if ($("#off"+i+"_start_minute").val() == "")
					{
						alert("외출시간(분)을 올바르게 선택해 주세요.");
						$("#off"+i+"_start_minute").focus();
						return;	
					}
					if ($("#off"+i+"_end_hour").val() == "")
					{
						alert("복귀시간(시)을 올바르게 선택해 주세요.");
						$("#off"+i+"_end_hour").focus();
						return;	
					}
					if ($("#off"+i+"_end_minute").val() == "")
					{
						alert("복귀시간(분)을 올바르게 선택해 주세요.");
						$("#off"+i+"_end_minute").focus();
						return;	
					}
				}
			}
			if ($("#memo").val() == "")
			{
				alert("사유를 입력해 주세요.");
				$("#memo").focus();
				return;	
			}

			$("#form").attr("target","hdnFrame");
			$("#form").attr("action","commuting_edit_act.php"); 
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
				<div class="work_stats_search clearfix" style="float:right;">
					(* 수정 할 항목을 체크해 주세요.)
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
							<td></td>
							<td>
								<input type="text" name="date" id="date" style="width:100px;" readonly value="<?=$md_date?>">
								<? if ($seqno == "") { ?><input type="hidden" name="date_hd" id="date_hd" class="datepicker"><? } ?>
							</td>
							<td colspan="2">
								<input type="checkbox" name="out_chk" id="exception" value="Y"<? if ($md_out_chk == "Y") { ?> checked<? } ?>>파견 여부
							</td>
						</tr>
						<tr>
							<th rowspan="7" class="edit">*시간</th>
							<td>
								<input type="checkbox" name="chk_gubun1" id="chk_gubun1" value="Y"<? if ($md_chk_gubun1 == "Y") { ?> checked<? } ?>>
								<label for="chk_gubun1">출근</label>
							</td>
							<td>
								<select name='gubun1_hour' id="gubun1_hour">
									<option value="">--</option>
								<?
									for ($i=0; $i<=23; $i++)
									{
										if (strlen($i) == 1) { $j = "0".$i; }
										else { $j = $i; }
								?>
									<option value="<?=$j?>"<? if ($j == substr($md_starttime,0,2)) { echo " selected"; } ?>><?=$j?></option>
								<?
									}
								?>
								</select>
								&nbsp;: &nbsp;
								<select name='gubun1_minute' id="gubun1_minute">
									<option value="">--</option>
								<?
									for ($i=0; $i<=59; $i++)
									{
										if (strlen($i) == 1) { $j = "0".$i; }
										else { $j = $i; }
								?>
									<option value="<?=$j?>"<? if ($j == substr($md_starttime,2,2)) { echo " selected"; } ?>><?=$j?></option>
								<?
									}
								?>
								</select>
							</td>
							<td colspan="2"></td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="chk_gubun2" id="chk_gubun2" value="Y"<? if ($md_chk_gubun2 == "Y") { ?> checked<? } ?>>
								<label for="chk_gubun2">퇴근</label>
							</td>
							<td>
								<select name='gubun2_hour' id='gubun2_hour'>
									<option value="">--</option>
								<?
									for ($i=0; $i<=30; $i++)
									{
										if (strlen($i) == 1) { $j = "0".$i; }
										else { $j = $i; }
								?>
									<option value="<?=$j?>"<? if ($j == substr($md_endtime,0,2)) { echo " selected"; } ?>><?=$j?></option>
								<?
									}
								?>
								</select>
								&nbsp;: &nbsp;
								<select name='gubun2_minute' id='gubun2_minute'>
									<option value="">--</option>
								<?
									for ($i=0; $i<=59; $i++)
									{
										if (strlen($i) == 1) { $j = "0".$i; }
										else { $j = $i; }
								?>
									<option value="<?=$j?>"<? if ($j == substr($md_endtime,2,2)) { echo " selected"; } ?>><?=$j?></option>
								<?
									}
								?>
								</select>
							</td>
							<td colspan="2"></td>
						</tr>
			<? 
				if ($seqno == "")
				{
					$m = 1;
					if ($date != "")
					{

						$sql = "SELECT 
									SEQNO, STARTTIME, ENDTIME 
								FROM
									DF_CHECKTIME_OFF WITH(NOLOCK)
								WHERE
									DATE = '$date' AND PRS_ID = '$prs_id'
								ORDER BY 
									SEQNO";
						$rs = sqlsrv_query($dbConn, $sql);

						while ($record = sqlsrv_fetch_array($rs))
						{
							$off_seqno = $record['SEQNO'];
							$off_starttime = $record['STARTTIME'];
							$off_endtime = $record['ENDTIME'];
			?>
						<tr>
							<td>
								<input type="checkbox" name="chk_off<?=$m?>" id="chk_off<?=$m?>" value="Y">
								<label for="chk_off<?=$m?>">외출<?=$m?></label>
								<input type="hidden" name="off<?=$m?>_seqno" id="off<?=$m?>_seqno" value="<?=$off_seqno?>">
							</td>
							<td>
								<select name='off<?=$m?>_start_hour' id='off<?=$m?>_start_hour'>
									<option value="">--</option>
								<?
									for ($i=0; $i<=30; $i++)
									{
										if (strlen($i) == 1) { $j = "0".$i; }
										else { $j = $i; }
								?>
									<option value="<?=$j?>"<? if ($j == substr($off_starttime,0,2)) { echo " selected"; } ?>><?=$j?></option>
								<?
									}
								?>
								</select>
								&nbsp;: &nbsp;
								<select name='off<?=$m?>_start_minute' id='off<?=$m?>_start_minute'>
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
								</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;~
							</td>
							<td>
								<select name='off<?=$m?>_end_hour' id='off<?=$m?>_end_hour'>
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
								</select>
								&nbsp;: &nbsp;
								<select name='off<?=$m?>_end_minute' id='off<?=$m?>_end_minute'>
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
							<td></td>
						</tr>
			<?
							$m++;
						}
					}

					for ($n=$m; $n<6; $n++)
					{
			?>
						<tr>
							<td>
								<input type="checkbox" name="chk_off<?=$n?>" id="chk_off<?=$n?>" value="Y">
								<label for="chk_off<?=$n?>">외출<?=$n?></label>
								<input type="hidden" name="off<?=$n?>_seqno" id="off<?=$n?>_seqno">
							</td>
							<td>
								<select name='off<?=$n?>_start_hour' id='off<?=$n?>_start_hour'>
									<option value="">--</option>
								<?
									for ($i=0; $i<=30; $i++)
									{
										if (strlen($i) == 1) { $j = "0".$i; }
										else { $j = $i; }
								?>
									<option value="<?=$j?>"><?=$j?></option>
								<?
									}
								?>
								</select>
								&nbsp;: &nbsp;
								<select name='off<?=$n?>_start_minute' id='off<?=$n?>_start_minute'>
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
								</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;~
							</td>
							<td>
								<select name='off<?=$n?>_end_hour' id='off<?=$n?>_end_hour'>
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
								</select>
								&nbsp;: &nbsp;
								<select name='off<?=$n?>_end_minute' id='off<?=$n?>_end_minute'>
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
							<td></td>
						</tr>
			<?
					}	
				}
				else
				{
					for ($m=1; $m<6; $m++)
					{
						if ($m == 1)
						{
							$off_chk = $md_chk_off1;
							$off_seqno = $md_off1_seqno;
							$off_starttime = $md_off1_starttime;
							$off_endtime = $md_off1_endtime;
						}
						else if ($m == 2)
						{
							$off_chk = $md_chk_off2;
							$off_seqno = $md_off2_seqno;
							$off_starttime = $md_off2_starttime;
							$off_endtime = $md_off2_endtime;
						}
						else if ($m == 3)
						{
							$off_chk = $md_chk_off3;
							$off_seqno = $md_off3_seqno;
							$off_starttime = $md_off3_starttime;
							$off_endtime = $md_off3_endtime;
						}
						else if ($m == 4)
						{
							$off_chk = $md_chk_off4;
							$off_seqno = $md_off4_seqno;
							$off_starttime = $md_off4_starttime;
							$off_endtime = $md_off4_endtime;
						}
						else if ($m == 5)
						{
							$off_chk = $md_chk_off5;
							$off_seqno = $md_off5_seqno;
							$off_starttime = $md_off5_starttime;
							$off_endtime = $md_off5_endtime;
						}
			?>
						<tr>
							<td>
								<input type="checkbox" name="chk_off<?=$m?>" id="chk_off<?=$m?>"<? if ($off_chk == "Y") { ?> checked<? } ?> value="Y">
								<label for="chk_off<?=$m?>">외출<?=$m?></label>
								<input type="hidden" name="off<?=$m?>_seqno" id="off<?=$m?>_seqno" value="<?=$off_seqno?>">
							</td>
							<td>
								<select name='off<?=$m?>_start_hour' id='off<?=$m?>_start_hour'>
									<option value="">--</option>
								<?
									for ($i=0; $i<=30; $i++)
									{
										if (strlen($i) == 1) { $j = "0".$i; }
										else { $j = $i; }
								?>
									<option value="<?=$j?>"<? if ($j == substr($off_starttime,0,2)) { echo " selected"; } ?>><?=$j?></option>
								<?
									}
								?>
								</select>
								&nbsp;: &nbsp;
								<select name='off<?=$m?>_start_minute' id='off<?=$m?>_start_minute'>
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
								</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;~
							</td>
							<td>
								<select name='off<?=$m?>_end_hour' id='off<?=$m?>_end_hour'>
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
								</select>
								&nbsp;: &nbsp;
								<select name='off<?=$m?>_end_minute' id='off<?=$m?>_end_minute'>
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
							<td></td>
						</tr>
			<?
					}
				}
			?>
						<tr>
							<th class="edit">*사유</th>
							<td colspan="4">
								<textarea name="memo" id="memo" style="width:500px;height:60px;"><?=$md_memo?></textarea>
							</td>
						</tr>
					<? if ($seqno != "") { ?>
						<tr>
							<th class="edit">*수정요청일</th>
							<td colspan="4"><?=$md_reg_date?></td>
						</tr>
					<? } ?>
					<? if ($md_edit_ok == "Y") { ?>
						<tr>
							<th class="edit">*근태수정일</th>
							<td colspan="4"><?=$md_ok_date?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>

			
			<div class="edit_btn">
		<? if ($md_edit_ok == "N" && $md_prs_id == $prs_id)	{ ?>
				<span id="btnEdit"><span style="padding:10px 30px; border:3px solid #000; font-weight:bold; color:#000; background:#fff;">요청<? if ($seqno != "") { ?>수정<? } ?></span></span>
		<? } ?>
				<span id="btnCancel"><span style="padding:10px 30px; border:3px solid #000; font-weight:bold; color:#000; background:#fff;">취소</span></span>
			</div>
			
			
			</div>
		</div>

</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
