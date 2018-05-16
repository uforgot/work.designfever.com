<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prs_id != "79" && $prs_id != "102") 
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
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

	$date = $p_year."-". $p_month;

	$teamSQL = "";
	if ($p_team != "")
	{
		$teamSQL = " AND PRS_TEAM = '$p_team'";
	}
	else				//팀.실장
	{
		$teamSQL = " AND PRS_TEAM IN ('커뮤니케이션전략기획1실','디자인1실','모션그래픽스1실','비주얼 인터랙션 디벨롭먼트 1실')";
	}

	$id = "";
	$name = "";
	$team = "";
	$position = "";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$sql = "SELECT 
				PRS_LOGIN, PRS_ID, PRS_NAME
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE
				PRF_ID IN (1,2,3,4,5,7)
			". $teamSQL ."
			ORDER BY 
				CASE 
					WHEN PRS_TEAM = '커뮤니케이션전략기획1실' THEN 1 
					WHEN PRS_TEAM = '디자인1실' THEN 2
					WHEN PRS_TEAM = '모션그래픽스1실' THEN 3
					WHEN PRS_TEAM = '비주얼 인터랙션 디벨롭먼트 1실' THEN 4 END, CASE ". $orderby. " END, PRS_JOIN, PRS_NAME";

	$rs = sqlsrv_query($dbConn,$sql);
	while ($record = sqlsrv_fetch_array($rs))
	{
		$id = $id . $record['PRS_ID'] ."##";;
		$name = $name . $record['PRS_NAME'] ."##";
		$team = $team . $record['PRS_TEAM'] ."##";
		$position = $position . $record['PRS_POSITION'] ."##";
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
	header( "Content-Disposition: attachment; filename=df1근태현황_".$p_year.$p_month.".xls" );
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
			$col_totaltime_arr = "";
			while ($record = sqlsrv_fetch_array($rs))
			{
				$col_date = $record['DATE'];
				$col_datekind = $record['DATEKIND'];
				$col_gubun = $record['GUBUN'];
				$col_gubun1 = $record['GUBUN1'];
				$col_gubun2 = $record['GUBUN2'];
				$col_checktime1 = $record['CHECKTIME1'];
				$col_checktime2 = $record['CHECKTIME2'];
				$col_totaltime = $record['TOTALTIME'];

				$col_date_arr = $col_date_arr . substr($col_date,0,4) ."-". substr($col_date,4,2) ."-". substr($col_date,6,2) ."##";
				$col_datekind_arr = $col_datekind_arr . $col_datekind ."##";
				$col_gubun_arr = $col_gubun_arr . $col_gubun ."##";
				$col_gubun1_arr = $col_gubun1_arr . $col_gubun1 ."##";
				$col_gubun2_arr = $col_gubun2_arr . $col_gubun2 ."##";
				$col_checktime1_arr = $col_checktime1_arr . $col_checktime1 ."##";
				$col_checktime2_arr = $col_checktime2_arr . $col_checktime2 ."##";
				$col_totaltime_arr = $col_totaltime_arr . $col_totaltime ."##";
			}

			$col_date_ex = explode("##",$col_date_arr);
			$col_datekind_ex = explode("##",$col_datekind_arr);
			$col_gubun_ex = explode("##",$col_gubun_arr);
			$col_gubun1_ex = explode("##",$col_gubun1_arr);
			$col_gubun2_ex = explode("##",$col_gubun2_arr);
			$col_checktime1_ex = explode("##",$col_checktime1_arr);
			$col_checktime2_ex = explode("##",$col_checktime2_arr);
			$col_totaltime_ex = explode("##",$col_totaltime_arr);
?>
						<tr>
							<td rowspan="3" style='font-size:12px;font-weight:bold;text-align:center;background:#e0e0e0;border-right:1px solid #000;border-top:1px solid #000;'><?=$name_ex[$i]?></td>
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
						<tr>
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
						<tr class="line_down">
<?
			for ($j=0; $j<sizeof($col_date_ex); $j++)
			{
				if ($col_date_ex[$j] != "")
				{
					if ($col_gubun_ex[$j] == "출퇴근")
					{
						if ($col_totaltime_ex[$j] == "")
						{
							$prt_time = "-";
						}
						else
						{
							$prt_time = substr($col_totaltime_ex[$j],0,2) .":". substr($col_totaltime_ex[$j],2,2);
						}
					}
					else
					{
						$prt_time = "";
					}

?>
						<td style="font-size:12px;text-align:center;background:#ececec;mso-number-format:'\@';"><?=$prt_time;?></td>
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
