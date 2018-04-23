<?
	header("Content-Type: text/json; charset=UTF-8");

	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	//require_once CMN_PATH."/login_check.php";
?>

<?
	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d"); 
	$date_arr = explode("-",$date);
	$p_year = $date_arr[0];
	$p_month = $date_arr[1];
	$p_day = $date_arr[2];

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }
	if (strlen($p_day) == "1") { $p_day = "0".$p_day; }

	//회의실 예약 카운트
	$sql = "EXEC SP_VISIT_LIST_01 '$date'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$total = $record['TOTAL'];				//총 예약건수

		if ($total == "") { $total = "0"; }
	}

	// 회의실 예약 리스트
	$listSQL = "SELECT
					SEQNO, PRS_NAME, COMPANY, VISITOR, CAR_NO, PHONE, DATE, MEMO, S_TIME, E_TIME, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE
				FROM 
					DF_VISIT WITH(NOLOCK)
				WHERE 
					DATE = '$date'
				ORDER BY 
					S_TIME";
	$listRs = sqlsrv_query($dbConn,$listSQL);

	$i = 1;
	while ($listRow = sqlsrv_fetch_array($listRs))
	{
		$visit_seqno = $listRow['SEQNO'];
		$visit_company = iconv("EUC-KR","UTF-8",$listRow['COMPANY']);
		$visit_visitor = iconv("EUC-KR","UTF-8",$listRow['VISITOR']);
		$visit_carno = iconv("EUC-KR","UTF-8",$listRow['CAR_NO']);
		$visit_phone = iconv("EUC-KR","UTF-8",$listRow['PHONE']);
		$visit_memo = iconv("EUC-KR","UTF-8",$listRow['MEMO']);
		$visit_stime = iconv("EUC-KR","UTF-8",$listRow['S_TIME']);
		$visit_name = iconv("EUC-KR","UTF-8",$listRow['PRS_NAME']);

		if($i%2) $class1 = "odd";
		else	 $class1 = "even";

		if($visit_carno) $carno = "<br>(".$visit_carno.")";
		else			 $carno = "&nbsp;";

		$list .= "<tr>
					<td width=\"5%\" class=\"$class1\" rowspan=\"2\">".$i."</td>
					<td width=\"22%\" class=\"$class1\">".date("H시 i분",strtotime($visit_stime))."</td>
					<td width=\"27%\" class=\"$class1\">".$visit_company.$carno."</td>
					<td width=\"15%\" class=\"$class1\">".$visit_visitor."</td>
					<td width=\"*\" class=\"$class1\"><a href=\"tel:".$visit_phone."\"><u>".$visit_phone."</u></a></td>
				</tr>
				<tr>
					<td colspan=\"4\" class=\"$class1 memo\">+ ".$visit_memo." (담당자: ".$visit_name.")</td>
				</tr>\n";
		$i++;
	}

	if($total == 0) {
		$list = "<tr>
					<td width=\"100%\" colspan=\"5\">금일 예약된 방문객이 없습니다.</td>
				</tr>\n";
	}

	//echo "<xmp>";
	//echo $list;
	//echo "</xmp>";

	$data = array("total"=>$total,"list"=>$list);

	echo json_encode($data);

	exit;
?>
