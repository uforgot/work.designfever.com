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
	$nowMonth = date("m");

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

	$date = $p_year."-". $p_month;

	$selSQL = "SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
	$selRs = sqlsrv_query($dbConn,$selSQL);

	$teamArr = "";
	while ($selRecord = sqlsrv_fetch_array($selRs))
	{
		$selTeam = $selRecord['TEAM'];

		$teamArr .= $selTeam."##";
	}

	$teamArr_ex = explode("##",$teamArr);

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION2_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_POSITION2 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION1_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION1 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, PRS_NAME";

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
				$orderbycase";
		$rs = sqlsrv_query($dbConn, $sql);

		while ($record=sqlsrv_fetch_array($rs))
		{
			$id = $id . $record['PRS_ID'] ."##";;
			$name = $name . $record['PRS_NAME'] ."##";
			$team = $team . $record['PRS_TEAM'] ."##";
			$position = $position . $record['PRS_POSITION'] ."##";
		}
	}

	$date_arr = "";
	$day_arr = "";
	$sql = "SELECT DATE, DAY FROM HOLIDAY WITH(NOLOCK) WHERE DATE LIKE '". str_replace('-','',$date) ."%'";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$date_arr = $date_arr . $record['DATE'] . "##";
		$day_arr = $day_arr . $record['DAY'] . "##";
	}

	header( "Content-type: application/vnd.ms-excel;charset=EUC-KR");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=근태현황_".$p_year.$p_month.".xls" );
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
				<td rowspan="2" style="font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:1px solid #000;mso-number-format:'\@';"><?=$date?></td>
		<?
			$date_arr_ex = explode("##",$date_arr);
			$day_arr_ex = explode("##",$day_arr);

			for ($i=0; $i<sizeof($date_arr_ex); $i++)
			{
				if ($date_arr_ex[$i] != "")
				{
					echo "<td style='font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;'>". substr($date_arr_ex[$i],6,2) ."</td>";
				}
			}
		?>
			</tr>
			<tr>
		<?
			for ($i=0; $i<sizeof($date_arr_ex); $i++)
			{
				if ($date_arr_ex[$i] != "")
				{
					if ($day_arr_ex[$i] == "SUN")
					{
					echo "<td style='font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;color:#ef0000;'>". $day_arr_ex[$i] ."</font></td>";
					}
					else if ($day_arr_ex[$i] == "SAT") 
					{
					echo "<td style='font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;color:#0000cc;'>". $day_arr_ex[$i] ."</td>";
					}
					else
					{
					echo "<td style='font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;'>". $day_arr_ex[$i] ."</td>";
					}
				}
			}
		?>
			</tr>
		</thead>
		<tbody>
<?
	$id_ex = explode("##",$id);
	$name_ex = explode("##",$name);
	$ex = explode("##",$team);
	$position_ex = explode("##",$position);

	for ($i=0; $i<sizeof($id_ex); $i++)
	{
		if ($id_ex[$i] != "")
		{
			$sql = "EXEC SP_COMMUTING_MEMBER_01 '$id_ex[$i]','$date'";
			$rs = sqlsrv_query($dbConn,$sql);

			$col_date_arr = "";
			$col_datekind_arr = "";
			$col_gubun_arr = "";
			$col_gubun1_arr = "";
			$col_gubun2_arr = "";
			$col_checktime1_arr = "";
			$col_checktime2_arr = "";
			while ($record = sqlsrv_fetch_array($rs))
			{
				$col_date = $record['DATE'];
				$col_datekind = $record['DATEKIND'];
				$col_gubun = $record['GUBUN'];
				$col_gubun1 = $record['GUBUN1'];
				$col_gubun2 = $record['GUBUN2'];
				$col_checktime1 = $record['CHECKTIME1'];
				$col_checktime2 = $record['CHECKTIME2'];

				$col_date_arr = $col_date_arr . substr($col_date,0,4) ."-". substr($col_date,4,2) ."-". substr($col_date,6,2) ."##";
				$col_datekind_arr = $col_datekind_arr . $col_datekind ."##";
				$col_gubun_arr = $col_gubun_arr . $col_gubun ."##";
				$col_gubun1_arr = $col_gubun1_arr . $col_gubun1 ."##";
				$col_gubun2_arr = $col_gubun2_arr . $col_gubun2 ."##";
				$col_checktime1_arr = $col_checktime1_arr . $col_checktime1 ."##";
				$col_checktime2_arr = $col_checktime2_arr . $col_checktime2 ."##";
			}

			$col_date_ex = explode("##",$col_date_arr);
			$col_datekind_ex = explode("##",$col_datekind_arr);
			$col_gubun_ex = explode("##",$col_gubun_arr);
			$col_gubun1_ex = explode("##",$col_gubun1_arr);
			$col_gubun2_ex = explode("##",$col_gubun2_arr);
			$col_checktime1_ex = explode("##",$col_checktime1_arr);
			$col_checktime2_ex = explode("##",$col_checktime2_arr);
?>
						<tr>
							<td rowspan="2" style='font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:1px solid #000;border-top:1px solid #000;'><?=$name_ex[$i]?></td>
<?
			for ($j=0; $j<sizeof($col_date_ex); $j++)
			{
				if ($col_date_ex[$j] != "")
				{
					if ($col_gubun_ex[$j] == "출퇴근")
					{
						if ($col_checktime1_ex[$j] == "")
						{
							$prt_time = "-";
						}
						else
						{
							$prt_time = substr($col_checktime1_ex[$j],8,2) .":". substr($col_checktime1_ex[$j],10,2);
						}
					}
					else
					{
						$prt_time = "#";
					}

					if ($prt_time == "#")
					{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';border-top:1px solid #000;color:#00aa00;"><?=$col_gubun_ex[$j];?></td>
<?
					}
					else if ($col_gubun1_ex[$j] == "4" || $col_gubun1_ex[$j] == "8")
					{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';border-top:1px solid #000;color:#ef0000;"><?=$prt_time;?></td>
<?
					}
					else
					{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';border-top:1px solid #000;"><?=$prt_time;?></td>
<?
					}
				}
				else
				{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';border-top:1px solid #000;"></td>
<?
				}
			}
?>
						</tr>
						<tr class="line_down">
<?
			for ($j=0; $j<sizeof($col_date_ex); $j++)
			{
				if ($col_date_ex[$j] != "")
				{
					if ($col_gubun_ex[$j] == "출퇴근")
					{
						if ($col_checktime2_ex[$j] == "")
						{
							$prt_time = "-";
						}
						else
						{
							$prt_time = substr($col_checktime2_ex[$j],8,2) .":". substr($col_checktime2_ex[$j],10,2);
						}
					}
					else
					{
						$prt_time = "#";
					}

					if ($prt_time == "#")
					{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';color:#00aa00;"><?=$col_gubun_ex[$j];?></td>
<?
					}
					else if ($col_gubun2_ex[$j] == "5" || $col_gubun2_ex[$j] == "9")
					{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';color:#0000cc;"><?=$prt_time;?></td>
<?
					}
					else
					{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';"><?=$prt_time;?></td>
<?
					}
				}
				else
				{
?>
						<td style="font-size:12px;text-align:center;mso-number-format:'\@';"></td>
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
				</table>
