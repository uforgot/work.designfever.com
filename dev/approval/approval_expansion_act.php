<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<? include INC_PATH."/top.php"; ?>

<?
	$doc_no = isset($_REQUEST['doc_no']) ? $_REQUEST['doc_no'] : null; 
	$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null; 
	$team_name = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 
	$idx = isset($_REQUEST['idx']) ? $_REQUEST['idx'] : null; 
	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null; 

	for ($i=0; $i<5; $i++) {
		$expansion_type_arr[$i] = isset($_REQUEST['type_'.$i]) ? $_REQUEST['type_'.$i] : null;
		$expansion_idx_arr[$i]  = isset($_REQUEST['idx_'.$i]) ? $_REQUEST['idx_'.$i] : null;

		// 정규직 폼
		if ($expansion_type_arr[$i] == "A") {
			$expansion_data1_arr[$i]	= isset($_REQUEST['name1_'.$i]) ? $_REQUEST['name1_'.$i] : null;
			$expansion_data2_arr[$i]	= isset($_REQUEST['cause1_'.$i]) ? $_REQUEST['cause1_'.$i] : null;					
			$expansion_data3_arr[$i]	= isset($_REQUEST['career_'.$i]) ? $_REQUEST['career_'.$i] : null;
			$expansion_data4_arr[$i]	= isset($_REQUEST['birth_'.$i]) ? $_REQUEST['birth_'.$i] : null;
			$expansion_data5_arr[$i]	= isset($_REQUEST['school_'.$i]) ? $_REQUEST['school_'.$i] : null;
			$expansion_data6_arr[$i]	= isset($_REQUEST['major_'.$i]) ? $_REQUEST['major_'.$i] : null;
			$career_y					= isset($_REQUEST['career_y_'.$i]) ? $_REQUEST['career_y_'.$i] : null;
			$career_m					= isset($_REQUEST['career_m_'.$i]) ? $_REQUEST['career_m_'.$i] : null;
			$expansion_data7_arr[$i]	= $career_y."-".$career_m;	
			$expansion_data8_arr[$i]	= isset($_REQUEST['position_'.$i]) ? $_REQUEST['position_'.$i] : null;
			$expansion_data9_arr[$i]	= isset($_REQUEST['rating_'.$i]) ? $_REQUEST['rating_'.$i] : null;
			$expansion_data10_arr[$i]	= isset($_REQUEST['reader_'.$i]) ? $_REQUEST['reader_'.$i] : null;
			$join_y						= isset($_REQUEST['join_y_'.$i]) ? $_REQUEST['join_y_'.$i] : null;
			$join_m						= isset($_REQUEST['join_m_'.$i]) ? $_REQUEST['join_m_'.$i] : null;
			$join_d						= isset($_REQUEST['join_d_'.$i]) ? $_REQUEST['join_d_'.$i] : null;
			$expansion_data11_arr[$i]	= $join_y."-".$join_m."-".$join_d;
		// 계약직 폼
		} else if ($expansion_type_arr[$i] == "B") {
			$expansion_data1_arr[$i]	= isset($_REQUEST['name2_'.$i]) ? $_REQUEST['name2_'.$i] : null;
			$expansion_data2_arr[$i]	= isset($_REQUEST['cause2_'.$i]) ? $_REQUEST['cause2_'.$i] : null;
			$expansion_data3_arr[$i]	= isset($_REQUEST['gubun_'.$i]) ? $_REQUEST['gubun_'.$i] : null;
			$expansion_data4_arr[$i]	= isset($_REQUEST['relay_'.$i]) ? $_REQUEST['relay_'.$i] : null;
			$expansion_data5_arr[$i]	= isset($_REQUEST['salary_h_'.$i]) ? str_replace(",","",$_REQUEST['salary_h_'.$i]) : null;
			$expansion_data6_arr[$i]	= isset($_REQUEST['salary_m_'.$i]) ? str_replace(",","",$_REQUEST['salary_m_'.$i]) : null;
			$period1_y					= isset($_REQUEST['period1_y_'.$i]) ? $_REQUEST['period1_y_'.$i] : null;
			$period1_m					= isset($_REQUEST['period1_m_'.$i]) ? $_REQUEST['period1_m_'.$i] : null;
			$period1_d					= isset($_REQUEST['period1_d_'.$i]) ? $_REQUEST['period1_d_'.$i] : null;
			$expansion_data7_arr[$i]	= $period1_y."-".$period1_m."-".$period1_d;	
			$period2_y					= isset($_REQUEST['period2_y_'.$i]) ? $_REQUEST['period2_y_'.$i] : null;
			$period2_m					= isset($_REQUEST['period2_m_'.$i]) ? $_REQUEST['period2_m_'.$i] : null;
			$period2_d					= isset($_REQUEST['period2_d_'.$i]) ? $_REQUEST['period2_d_'.$i] : null;
			$expansion_data8_arr[$i]	= $period2_y."-".$period2_m."-".$period2_d;	
			$expansion_data9_arr[$i]	= isset($_REQUEST['memo_'.$i]) ? $_REQUEST['memo_'.$i] : null;
		}
	}

	$retUrl = "approval_detail.php?doc_no=". $doc_no;

	if ($mode == "modify") {
		if ($idx == "team_name") {
			$sql = "UPDATE DF_APPROVAL SET TEAM_NAME = '$team_name' WHERE DOC_NO = '$doc_no'";
			$rs = sqlsrv_query($dbConn, $sql);

			if ($rs == false) {
?>
			<script language="javascript">
				alert("error 1_0_1. 채용부서 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
			</script>
<?
				exit;
			}
		}

		for ($i=0;$i<5;$i++) {
			// 정보가 있는 경우
			if ($expansion_data1_arr[$i] != "") {
				$sql = "SELECT 
							IDX, DATA1, DATA2, DATA3, DATA4, DATA5, 
							DATA6, DATA7, DATA8, DATA9, DATA10,	DATA11
						FROM 
							DF_APPROVAL_EXPANSION
						WHERE 
							DOC_NO = '$doc_no' AND IDX = '$i' AND LAST = 'Y'";
				$rs = sqlsrv_query($dbConn,$sql);
				$record = sqlsrv_fetch_array($rs);

				$type_org	= trim($record['TYPE']);			// 채용구분
				$name_org	= trim($record['DATA1']);			// 성명
				$cause_org	= trim($record['DATA2']);			// 총금액

				if ($type_org == "A") {
					$career_org	  = trim($record['DATA3']);		// 경력구분
					$birth_org	  = trim($record['DATA4']);		// 생년월일
					$school_org	  = trim($record['DATA5']);		// 최종학교
					$major_org	  = trim($record['DATA6']);		// 전공
					$career2_org  = trim($record['DATA7']);		// 경력기간
					$position_org = trim($record['DATA8']);		// 직급
					$rating_org	  = trim($record['DATA9']);		// 호봉
					$reader_org	  = trim($record['DATA10']);	// 직책
					$join_org	  = trim($record['DATA11']);	// 입사예정일
				} else if ($db_type == "B") {
					$gubun_org	  = trim($record['DATA3']);		// 채용구분
					$relay_org	  = trim($record['DATA4']);		// 중개업체
					$salary_h_org = trim($record['DATA5']);		// 시급
					$salary_m_org = trim($record['DATA6']);		// 월급
					$period1_org  = trim($record['DATA7']);		// 기간1
					$period2_org  = trim($record['DATA8']);		// 기간2
					$memo_org	  = trim($record['DATA9']);		// 기타
				}

				$db_idx	= trim($record['IDX']);					// IDX

				if (($type_org != $expansion_type_arr[$i] || $name_org != $expansion_data1_arr[$i] || $cause_org != $expansion_data2_arr[$i]) 
					|| ($type_org == "A" && $career_org != $expansion_data3_arr[$i] || $birth_org != $expansion_data4_arr[$i] || $school_org != $expansion_data5_arr[$i] 
					|| $major_org != $expansion_data6_arr[$i] || $career2_org != $expansion_data7_arr[$i] || $position_org != $expansion_data8_arr[$i] 
					|| $rating_org != $expansion_data9_arr[$i] || $reader_org != $expansion_data10_arr[$i] || $join_org != $expansion_data11_arr[$i]) 
					|| ($type_org == "B" && $gubun_org != $expansion_data3_arr[$i] || $relay_org != $expansion_data4_arr[$i] || $salary_h_org != $expansion_data5_arr[$i] 
					|| $salary_m_org != $expansion_data6_arr[$i] || $period1_org != $expansion_data7_arr[$i] || $period2_org != $expansion_data8_arr[$i] 
					|| $memo_org != $expansion_data9_arr[$i])) {

					$sql = "UPDATE DF_APPROVAL_EXPANSION SET LAST = 'N' WHERE DOC_NO = '$doc_no' AND IDX = '$i' AND LAST = 'Y'";
					$rs = sqlsrv_query($dbConn, $sql);

					if ($rs == false) {
?>
					<script language="javascript">
						alert("error 1_1_<?=$i?>. 사원정보 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
					</script>
<?
						exit;
					}

					if ($expansion_data1_arr[$i]) {
						$sql = "INSERT INTO DF_APPROVAL_EXPANSION (
									DOC_NO, IDX, TYPE, DATA1, DATA2, DATA3, DATA4, DATA5, DATA6, DATA7, DATA8, DATA9, DATA10, DATA11,
									LAST, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, REG_DATE
								) VALUES (
									'$doc_no','$i','$expansion_type_arr[$i]','$expansion_data1_arr[$i]','$expansion_data2_arr[$i]','$expansion_data3_arr[$i]',
									'$expansion_data4_arr[$i]','$expansion_data5_arr[$i]','$expansion_data6_arr[$i]','$expansion_data7_arr[$i]',
									'$expansion_data8_arr[$i]','$expansion_data9_arr[$i]','$expansion_data10_arr[$i]','$expansion_data11_arr[$i]',
									'Y','$prs_id','$prs_login','$prs_name','$prs_position',getdate()
								)";
						$rs = sqlsrv_query($dbConn, $sql);

						if ($rs == false) {
?>
						<script language="javascript">
							alert("error 1_2_<?=$i?>. 입사정보 수정에 실패 하였습니다. 개발팀에 문의해 주세요.");
						</script>
<?
							exit;
						}
					}
				}
			}
		}
?>
	<script language="javascript">
		alert("수정되었습니다.");
		location.href = "<?=$retUrl?>";
	</script>
<?
	} else if ($mode == "delete") {
		for ($i=0;$i<5;$i++) {
			if ($i == $idx) {
				$sql = "DELETE FROM DF_APPROVAL_EXPANSION WHERE DOC_NO = '$doc_no' AND IDX = '$i'";
				$rs = sqlsrv_query($dbConn, $sql);

				if ($rs == false) {
?>
				<script language="javascript">
					alert("error 2_1_<?=$i?>. 입사정보 삭제에 실패 하였습니다. 개발팀에 문의해 주세요.");
				</script>
<?
					exit;
				}
			} else if ($i > $idx) {
				$j = $i - 1;

				$sql = "UPDATE DF_APPROVAL_EXPANSION SET IDX = '$j' WHERE DOC_NO = '$doc_no' AND IDX = '$i'";
				$rs = sqlsrv_query($dbConn, $sql);

				if ($rs == false) {
?>
				<script language="javascript">
					alert("error 2_2_<?=$i?>. 입사정보 삭제에 실패 하였습니다. 개발팀에 문의해 주세요.");
				</script>
<?
					exit;
				}
			}
		}
?>
	<script language="javascript">
		alert("삭제되었습니다.");
		location.href = "<?=$retUrl?>";
	</script>
<?
	}
?>