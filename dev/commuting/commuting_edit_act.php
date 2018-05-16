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
		$mode = "write";
	}
	else
	{
		$mode = "modify";
	}

	$md_date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null; 
	$md_chk_gubun1 = isset($_REQUEST['chk_gubun1']) ? $_REQUEST['chk_gubun1'] : "N"; 
	$md_chk_gubun2 = isset($_REQUEST['chk_gubun2']) ? $_REQUEST['chk_gubun2'] : "N"; 
	$md_chk_off1 = isset($_REQUEST['chk_off1']) ? $_REQUEST['chk_off1'] : "N";
	$md_chk_off2 = isset($_REQUEST['chk_off2']) ? $_REQUEST['chk_off2'] : "N";
	$md_chk_off3 = isset($_REQUEST['chk_off3']) ? $_REQUEST['chk_off3'] : "N";
	$md_chk_off4 = isset($_REQUEST['chk_off4']) ? $_REQUEST['chk_off4'] : "N";
	$md_chk_off5 = isset($_REQUEST['chk_off5']) ? $_REQUEST['chk_off5'] : "N";
	$md_gubun1_hour = isset($_REQUEST['gubun1_hour']) ? $_REQUEST['gubun1_hour'] : null;
	$md_gubun1_minute = isset($_REQUEST['gubun1_minute']) ? $_REQUEST['gubun1_minute'] : null;
	$md_gubun2_hour = isset($_REQUEST['gubun2_hour']) ? $_REQUEST['gubun2_hour'] : null;
	$md_gubun2_minute = isset($_REQUEST['gubun2_minute']) ? $_REQUEST['gubun2_minute'] : null;
	$md_memo = isset($_REQUEST['memo']) ? $_REQUEST['memo'] : null; 
	$md_out_chk = isset($_REQUEST['out_chk']) ? $_REQUEST['out_chk'] : "N"; 
	$md_off1_seqno = isset($_REQUEST['off1_seqno']) ? $_REQUEST['off1_seqno'] : null;
	$md_off2_seqno = isset($_REQUEST['off2_seqno']) ? $_REQUEST['off2_seqno'] : null;
	$md_off3_seqno = isset($_REQUEST['off3_seqno']) ? $_REQUEST['off3_seqno'] : null;
	$md_off4_seqno = isset($_REQUEST['off4_seqno']) ? $_REQUEST['off4_seqno'] : null;
	$md_off5_seqno = isset($_REQUEST['off5_seqno']) ? $_REQUEST['off5_seqno'] : null;
	$md_off1_start_hour = isset($_REQUEST['off1_start_hour']) ? $_REQUEST['off1_start_hour'] : null;
	$md_off1_start_minute = isset($_REQUEST['off1_start_minute']) ? $_REQUEST['off1_start_minute'] : null;
	$md_off1_end_hour = isset($_REQUEST['off1_end_hour']) ? $_REQUEST['off1_end_hour'] : null;
	$md_off1_end_minute = isset($_REQUEST['off1_end_minute']) ? $_REQUEST['off1_end_minute'] : null;
	$md_off2_start_hour = isset($_REQUEST['off2_start_hour']) ? $_REQUEST['off2_start_hour'] : null;
	$md_off2_start_minute = isset($_REQUEST['off2_start_minute']) ? $_REQUEST['off2_start_minute'] : null;
	$md_off2_end_hour = isset($_REQUEST['off2_end_hour']) ? $_REQUEST['off2_end_hour'] : null;
	$md_off2_end_minute = isset($_REQUEST['off2_end_minute']) ? $_REQUEST['off2_end_minute'] : null;
	$md_off3_start_hour = isset($_REQUEST['off3_start_hour']) ? $_REQUEST['off3_start_hour'] : null;
	$md_off3_start_minute = isset($_REQUEST['off3_start_minute']) ? $_REQUEST['off3_start_minute'] : null;
	$md_off3_end_hour = isset($_REQUEST['off3_end_hour']) ? $_REQUEST['off3_end_hour'] : null;
	$md_off3_end_minute = isset($_REQUEST['off3_end_minute']) ? $_REQUEST['off3_end_minute'] : null;
	$md_off4_start_hour = isset($_REQUEST['off4_start_hour']) ? $_REQUEST['off4_start_hour'] : null;
	$md_off4_start_minute = isset($_REQUEST['off4_start_minute']) ? $_REQUEST['off4_start_minute'] : null;
	$md_off4_end_hour = isset($_REQUEST['off4_end_hour']) ? $_REQUEST['off4_end_hour'] : null;
	$md_off4_end_minute = isset($_REQUEST['off4_end_minute']) ? $_REQUEST['off4_end_minute'] : null;
	$md_off5_start_hour = isset($_REQUEST['off5_start_hour']) ? $_REQUEST['off5_start_hour'] : null;
	$md_off5_start_minute = isset($_REQUEST['off5_start_minute']) ? $_REQUEST['off5_start_minute'] : null;
	$md_off5_end_hour = isset($_REQUEST['off5_end_hour']) ? $_REQUEST['off5_end_hour'] : null;
	$md_off5_end_minute = isset($_REQUEST['off5_end_minute']) ? $_REQUEST['off5_end_minute'] : null;

	$md_starttime = $md_gubun1_hour . $md_gubun1_minute;
	$md_endtime = $md_gubun2_hour . $md_gubun2_minute;
	$md_off1_starttime = $md_off1_start_hour . $md_off1_start_minute;
	$md_off1_endtime = $md_off1_end_hour . $md_off1_end_minute;
	$md_off2_starttime = $md_off2_start_hour . $md_off2_start_minute;
	$md_off2_endtime = $md_off2_end_hour . $md_off2_end_minute;
	$md_off3_starttime = $md_off3_start_hour . $md_off3_start_minute;
	$md_off3_endtime = $md_off3_end_hour . $md_off3_end_minute;
	$md_off4_starttime = $md_off4_start_hour . $md_off4_start_minute;
	$md_off4_endtime = $md_off4_end_hour . $md_off4_end_minute;
	$md_off5_starttime = $md_off5_start_hour . $md_off5_start_minute;
	$md_off5_endtime = $md_off5_end_hour . $md_off5_end_minute;

	if ($mode == "write")
	{
		$sql = "INSERT INTO DF_CHECKTIME_EDIT 
					(PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, PRS_TEAM, 
					DATE, CHK_GUBUN1, CHK_GUBUN2, CHK_OFF1, CHK_OFF2, CHK_OFF3, CHK_OFF4, CHK_OFF5, 
					OUT_CHK, MEMO, STARTTIME, ENDTIME, OFF_SEQNO1, OFF_SEQNO2, OFF_SEQNO3, OFF_SEQNO4, OFF_SEQNO5,
					OFF_STARTTIME1, OFF_ENDTIME1, OFF_STARTTIME2, OFF_ENDTIME2, OFF_STARTTIME3, OFF_ENDTIME3, 
					OFF_STARTTIME4, OFF_ENDTIME4, OFF_STARTTIME5, OFF_ENDTIME5, REG_DATE) 
				VALUES 
					('$prs_id','$prs_login','$prs_name','$prs_position','$prs_team',
					'$md_date','$md_chk_gubun1','$md_chk_gubun2','$md_chk_off1','$md_chk_off2','$md_chk_off3','$md_chk_off4','$md_chk_off5','$md_out_chk','$md_memo','$md_starttime','$md_endtime',
					'$md_off1_seqno','$md_off2_seqno','$md_off3_seqno','$md_off4_seqno','$md_off5_seqno',
					'$md_off1_starttime','$md_off1_endtime','$md_off2_starttime','$md_off2_endtime','$md_off3_starttime3','$md_off3_endtime',
					'$md_off4_starttime','$md_off4_endtime','$md_off5_starttime','$md_off5_endtime',getdate())";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script language="javascript">
			alert("error1. 저장 실패하였습니다. 개발팀에 문의하세요.");
		</script>
<?
			exit;
		}

		$retUrl = "commuting_edit_list.php?page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword;
	}
	else
	{
		$sql = "UPDATE DF_CHECKTIME_EDIT SET
					DATE = '$md_date',
					CHK_GUBUN1 = '$md_chk_gubun1',
					CHK_GUBUN2 = '$md_chk_gubun2',
					CHK_OFF1 = '$md_chk_off1',
					CHK_OFF2 = '$md_chk_off2',
					CHK_OFF3 = '$md_chk_off3',
					CHK_OFF4 = '$md_chk_off4',
					CHK_OFF5 = '$md_chk_off5',
					OUT_CHK = '$md_out_chk',
					MEMO = '$md_memo',
					STARTTIME = '$md_starttime',
					ENDTIME = '$md_endtime',
					OFF_SEQNO1 = '$md_off1_seqno',
					OFF_SEQNO2 = '$md_off2_seqno',
					OFF_SEQNO3 = '$md_off3_seqno',
					OFF_SEQNO4 = '$md_off4_seqno',
					OFF_SEQNO5 = '$md_off5_seqno',
					OFF_STARTTIME1 = '$md_off1_starttime',
					OFF_ENDTIME1 = '$md_off1_endtime',
					OFF_STARTTIME2 = '$md_off2_starttime',
					OFF_ENDTIME2 = '$md_off2_endtime',
					OFF_STARTTIME3 = '$md_off3_starttime',
					OFF_ENDTIME3 = '$md_off3_endtime',
					OFF_STARTTIME4 = '$md_off4_starttime',
					OFF_ENDTIME4 = '$md_off4_endtime',
					OFF_STARTTIME5 = '$md_off5_starttime',
					OFF_ENDTIME5 = '$md_off5_endtime',
					UPT_DATE = getdate() 
				WHERE 
					SEQNO = '$seqno'";
		$rs = sqlsrv_query($dbConn,$sql);

		if ($rs == false)
		{
?>
		<script language="javascript">
			alert("error2. 수정 실패하였습니다. 개발팀에 문의하세요.");
		</script>
<?
			exit;
		}

		$retUrl = "commuting_edit_detail.php?seqno=". $seqno ."&page=". $page ."&keyfield=". $keyfield ."&keyword=". $keyword;
	}
?>

	<script language="javascript">
		parent.location.href = "<?=$retUrl?>";
	</script>
