<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("해당페이지는 임원,관리자만 확인 가능합니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}
	$nowYear = date("Y");

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 

	if ($p_year == "") $p_year = $nowYear;

	$date = $p_year;

	$selSQL = "SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
	$selRs = sqlsrv_query($dbConn,$selSQL);

	$teamArr = "";
	while ($selRecord = sqlsrv_fetch_array($selRs))
	{
		$selTeam = $selRecord['TEAM'];

		$teamArr .= $selTeam."##";
	}

	$teamArr_ex = explode("##",$teamArr);

	$id = "";
	$name = "";
	$team = "";
	$position = "";

	for ($i=0; $i<sizeof($teamArr_ex); $i++)
	{
		$sql = "SELECT 
					PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION
				FROM 
					DF_PERSON WITH(NOLOCK)
				WHERE 
					PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,102,148) AND PRS_TEAM = '$teamArr_ex[$i]'
				ORDER BY CASE 
						WHEN PRS_POSITION='대표' THEN 1
						WHEN PRS_POSITION='이사' THEN 2
						WHEN PRS_POSITION='실장' THEN 3
						WHEN PRS_POSITION='팀장' THEN 4
						WHEN PRS_POSITION='책임' THEN 5
						WHEN PRS_POSITION='대리' THEN 6
						WHEN PRS_POSITION='선임' THEN 7
						WHEN PRS_POSITION='주임' THEN 8
						WHEN PRS_POSITION='사원' THEN 9
						WHEN PRS_POSITION='인턴' THEN 10 END, PRS_NAME";
		$rs = sqlsrv_query($dbConn, $sql);

		while ($record=sqlsrv_fetch_array($rs))
		{
			$id = $id . $record['PRS_ID'] ."##";;
			$name = $name . $record['PRS_NAME'] ."##";
			$team = $team . $record['PRS_TEAM'] ."##";
			$position = $position . $record['PRS_POSITION'] ."##";
		}
	}

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=근태통계_종합_".$p_year.".xls" );
?>

	<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=EUC-KR'>
	<style>
	<!--
	br{mso-data-placement:same-cell;}
	-->
	</style>
	<table border=1>
		<thead>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="5">평일근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;" colspan="2">휴일근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;"></td>
			</tr>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">no.</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">이름</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">직급</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">부서</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>출근시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">평균<br>퇴근시간</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">출근일<br>합계</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">정상근무<br>미만 근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">정상근무<br>이상 근무일</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">출근일<br>합계</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">근무시간<br>합계</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">휴가/반차/기타<br>합계</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">비고</td>
			</tr>
		</thead>
		<tbody>
<?
	$id_ex = explode("##",$id);
	$name_ex = explode("##",$name);
	$team_ex = explode("##",$team);
	$position_ex = explode("##",$position);

	$no = 1;
	for ($i=0; $i<sizeof($id_ex); $i++)
	{
		if ($id_ex[$i] != "")
		{
			$sql = "EXEC SP_EXCEL_TOTAL_YEAR_01 '$id_ex[$i]','$date'";
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);
			if (sizeof($record) > 0)
			{
				$biz_avgtime1 = $record['BIZ_AVGTIME1'];		//평일 평균출근시
				$biz_avgminute1 = $record['BIZ_AVGMINUTE1'];	//평일 평균출근분
				$biz_avgtime2 = $record['BIZ_AVGTIME2'];		//평일 평균퇴근시
				$biz_avgminute2 = $record['BIZ_AVGMINUTE2'];	//평일 평균퇴근분
				$biz_commute = $record['BIZ_COMMUTE'];			//평일 출근일
				$biz_under = $record['BIZ_UNDER'];				//평일 9시간 미만 근무일
				$biz_over = $record['BIZ_OVER'];				//평일 9시간 초과 근무일
				$law_commute = $record['LAW_COMMUTE'];			//휴일 출근일
				$law_totaltime = $record['LAW_TOTALTIME'];		//휴일 근무시간시
				$law_totalminute = $record['LAW_TOTALMINUTE'];	//휴일 근무시간분
				$vacation = $record['VACATION'];			//휴가/반차/기타 합계
	
				if ($biz_avgtime1 == "") { $biz_avgtime1 = "0"; }
				if ($biz_avgminute1 == "") { $biz_avgminute1 = "0"; }
				if ($biz_avgtime2 == "") { $biz_avgtime2 = "0"; }
				if ($biz_avgminute2 == "") { $biz_avgminute2 = "0"; }
				if ($law_totaltime == "") { $law_totaltime = "0"; }
				if ($law_totalminute == "") { $law_totalminute = "0"; }

				if (strlen($biz_avgtime1) == 1) { $biz_avgtime1 = "0".$biz_avgtime1; }
				if (strlen($biz_avgminute1) == 1) { $biz_avgminute1 = "0".$biz_avgminute1; }
				if (strlen($biz_avgtime2) == 1) { $biz_avgtime2 = "0".$biz_avgtime2; }
				if (strlen($biz_avgminute2) == 1) { $biz_avgminute2 = "0".$biz_avgminute2; }
				if (strlen($law_totaltime) == 1) { $law_totaltime = "0".$law_totaltime; }
				if (strlen($law_totalminute) == 1) { $law_totalminute = "0".$law_totalminute; }
?>
			<tr>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$no?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$name_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$position_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$team_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_avgtime1?> : <?=$biz_avgminute1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_avgtime2?> : <?=$biz_avgminute2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_under?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$biz_over?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$law_commute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$law_totaltime?> : <?=$law_totalminute?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$vacation?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"></td>
			</tr>
<?
			}
		}
		$no++;
	}
?>
		</tbody>
	</table>

