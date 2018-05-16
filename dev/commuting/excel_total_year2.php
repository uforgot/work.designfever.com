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
					PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,102,1482) AND PRS_TEAM = '$teamArr_ex[$i]'
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
	header( "Content-Disposition: attachment; filename=근태통계_평일상세_".$p_year.".xls" );
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
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;"></td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">1월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">2월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">3월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">4월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">5월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">6월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">7월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">8월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">9월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">10월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">11월</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;" colspan="10">12월</td>
			</tr>
			<tr>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">no.</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">이름</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">직급</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;">부서</td>
			<? for ($i=1; $i<=12; $i++) { ?>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">초과</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">미만</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">휴가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">병가</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">경조사</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">반차</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">기타</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">결근</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;">미체크</td>
				<td style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:2px solid #000;">합</td>
			<? } ?>
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
?>
			<tr>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$no?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$name_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$position_ex[$i]?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';border-right:2px solid #000;"><?=$team_ex[$i]?></td>
<?
			for ($j=1; $j<=12; $j++)
			{
				if (strlen($j) == 1)
				{
					$date = $p_year ."-0". $j;
				}
				else
				{
					$date = $p_year ."-". $j;
				}

				$sql = "EXEC SP_EXCEL_TOTAL_YEAR_02 '$id_ex[$i]','$date'";
				$rs = sqlsrv_query($dbConn, $sql);

				while ($record = sqlsrv_fetch_array($rs)) 
				{
					$over = $record['OVER_DAY'];		//초과
					$under = $record['UNDER_DAY'];		//미만
					$count1 = $record['COUNT1'];		//휴가
					$count2 = $record['COUNT2'];		//병가
					$count3 = $record['COUNT3'];		//경조사
					$count4 = $record['COUNT4'];		//반차
					$count5 = $record['COUNT5'];		//기타
					$count6 = $record['COUNT6'];		//결근
					$checked = $record['CHECKED'];		//근태체크
					$allday = $record['ALLDAY'];		//총 근무일
					$noncheck = $allday - $checked;
					$total = $over + $under + $count1 + $count2 + $count3 + $count4 + $count5 + $count6 + $noncheck;
?>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$over?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$under?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count1?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count2?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count3?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count4?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count5?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$count6?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$noncheck?></td>
				<td  style="font-size:12px;text-align:center;mso-number-format:'\@';border-right:2px solid #000;"><?=$total?></td>
<?
				}
			}
?>
			</tr>
<?
		}
	}
?>
		</tbody>
		<tbody>
