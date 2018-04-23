<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$now_date = date("Y-m-d");
	$yesterday_date = date("Y-m-d",strtotime ("-1 day"));

	$where = " AND PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN(102)";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby . " END, PRS_JOIN, PRS_NAME";

	function getMemberCommuting($prs_id, $date, $yesterday) {
		global $dbConn;

		$flag = false;

		//정상출근,지각,휴가,근무일수,반차,평균출근시,평균출근분,평균퇴근시,평균퇴근분,총근무시간
		$sql = "EXEC SP_COMMUTING_MEMBER_02 '$prs_id','$date','$yesterday'";
		$rs = sqlsrv_query($dbConn,$sql);
		$record = sqlsrv_fetch_array($rs);

		if (sizeof($record) > 0)
		{
			$col_date = $record['DATE'];					//날짜
			$col_datekind = $record['DATEKIND'];			//공휴일 여부
			$col_gubun = $record['GUBUN'];					//출퇴근구분
			$col_gubun1 = $record['GUBUN1'];				//출근구분
			$col_gubun2 = $record['GUBUN2'];				//퇴근구분
			$col_checktime1 = $record['CHECKTIME1'];		//출근시간
			$col_checktime2 = $record['CHECKTIME2'];		//퇴근시간

			//출근시간
			$checktime1 = substr($col_checktime1,8,2) .":". substr($col_checktime1,10,2);
			if ($checktime1 == ":") { $checktime1 = ""; }

			if ($col_gubun1 == "1") {}			//출근
			else if ($col_gubun1 == "4") {}		//반차
			else if ($col_gubun1 == "6") {}		//외근
			else if ($col_gubun1 == "7") {}		//지각
			else if ($col_gubun1 == "8") {}		//반차
			else if ($col_gubun1 == "10") 		//휴가 - 출근/퇴근 시간 표시 안함 - 당일 00:00출근 23:59퇴근으로 설정되어 있음
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "11")	//병가
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "12")	//경조사
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "13")	//기타
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "14")	//결근
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "15")	//교육/훈련
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "16")	//프로젝트 휴가
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "17")	//리프레시 휴가
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "18")	//무급 휴가
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "19")	//예비군
			{
				$checktime1 = "";
			}
			else if ($col_gubun1 == "0")	//오후반차 제출. 출퇴근체크 X
			{
				$checktime1 = "";
			}

			//퇴근시간
			$checktime2 = substr($col_checktime2,8,2) .":". substr($col_checktime2,10,2);
			if ($checktime2 == ":") { $checktime2 = ""; }

			if ($col_gubun2 == "2" || $col_gubun2 == "3" || $col_gubun2 == "5" || $col_gubun2 == "6" || $col_gubun2 == "9")
			{
				if ($col_gubun2 == "2" || $col_gubun2 == "3") {}	//퇴근
				else if ($col_gubun2 == "5") {}						//프로젝트 반차
				else if ($col_gubun2 == "6") {}						//외근	
				else if ($col_gubun2 == "9") {}						//반차
				else if ($col_gubun2 == "0") {}						//오전반차 제출. 출퇴근체크 X
			}
		}

		if(strlen($checktime1) > 1) $flag = true;
		if(strlen($checktime2) > 1) $flag = false;

		$icon = ($flag===true) ? "<font color=\"green\">●</font>" : "<font color=\"red\">●</font>";
		
		// 예외 대상
		$arr = array(15,22,24,87,148);
		if(in_array($prs_id,$arr)) $icon = "<font color=\"white\">○</font>";

		return $icon;
	}

	// 요일
	function getWeekName($index) {
		$week_name = array("일","월","화","수","목","금","토");

		return $week_name[$index];
	}
?>

<? include INC_PATH."/top.php"; ?>

</head>

<body>
<div class="wrapper">
<form method="post" name="form" id="form">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/org_menu.php"; ?>

			<div class="work_wrap clearfix">
			
				<div class="cal_top2 clearfix">
					<strong><?=date("Y")?></strong>년
					<strong><?=date("m")?></strong>월
					<strong><?=date("d")?></strong>일
					<strong><?=getWeekName(date("w"))?></strong>요일
					<strong><?=date("H:i:s")?></strong> 현재
				</div>

				<div style="padding:0 2.5% 5px 2.5%;">
					<span><font color="green">●</font> 출근 &nbsp;&nbsp;<font color="red">●</font> 퇴근/휴가</span>
				</div>

			<div class="tables">
				<table class="notable work_stats5 group" width="100%" id="4층">
					<thead>
						<tr>
							<th class="div">4 층</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats5" width="100%" id="digital experience division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">CEO</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CEO'";
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>									
							
							</td>
						</tr>

						<tr class="plural">
							<th class="teamname team">df lab</th>
							<th class="team">ix1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'df lab'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="5"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="5">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ix1'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="team">ix2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ix2'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="team">ixd</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'ixd'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>

				<table class="notable work_stats5 group" width="100%" id="3층">
					<thead>
						<tr>
							<th class="div">3 층</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats5" width="100%" id="digital experience division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">CSO</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CSO'";
		$rs = sqlsrv_query($dbConn, $sql);
		$record = sqlsrv_fetch_array($rs);
		$col_prs_id = $record['PRS_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_position = $record['PRS_POSITION'];

		$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>									
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team" style="border-bottom:0px;">brand experience team</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'brand experience team'". $where . $orderbycase;;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>									
							
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">digital experience division</th>
							<th class="team">dx1</th>
						</tr>
<?
//		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'digital experience division'". $where . $orderbycase;
//		$rs = sqlsrv_query($dbConn, $sql);
//
//		While ($record = sqlsrv_fetch_array($rs))
//		{
//			$col_prs_id = $record['PRS_ID'];
//			$col_prs_name = $record['PRS_NAME'];
//			$col_prs_position = $record['PRS_POSITION'];
//
//			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<!--ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul-->
							</td>
<?
//		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dx1'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="team">dx2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dx2'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">design2 division</th>
							<th class="team">design3</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'Design2 Division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="5">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design3'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="team">design4</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design4'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="team">design5</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design5'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">motion graphic division</th>
							<th class="team">mg1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'motion graphic division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'mg1'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="team">mg2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'mg2'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team" style="border-bottom:0px;">경영지원팀</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = '경영지원팀'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>									
							
							</td>
						</tr>
					</tbody>
				</table>
				<table class="notable work_stats5 group" width="100%" id="2층">
					<thead>
						<tr>
							<th class="div">2 층</th>
						</tr>
					</thead>
				</table>
				<table class="notable work_stats5" width="100%" id="digital marketing division">
					<tbody> 
						<tr class="plural">
							<th class="teamname team">CCO</th>
							<td class="list1 top">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'CCO'";
		$rs = sqlsrv_query($dbConn, $sql);
		$record = sqlsrv_fetch_array($rs);
		$col_prs_id = $record['PRS_ID'];
		$col_prs_name = $record['PRS_NAME'];
		$col_prs_position = $record['PRS_POSITION'];

		$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>									
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">digital marketing division</th>
							<th class="team">dm1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'digital marketing division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}	
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dm1'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>								
							</td>
						</tr>
						<tr class="plural">
							<th class="team">dm2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'dm2'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
			}
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">design1 division</th>
							<th class="team">design1</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'Design1 Division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader" rowspan="3"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader" rowspan="3">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design1'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr class="plural">
							<th class="team">design2</th>
						</tr>
						<tr>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'design2'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
							</td>
						</tr>
						<tr class="plural">
							<th class="teamname team">film & content division</th>
							<th class="team">fc</th>
						</tr>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'film & content division'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		if (sqlsrv_has_rows($rs) == 0)
		{
?>
						<tr>
							<td class="leader"></td>
<?
		}
		else
		{
			While ($record = sqlsrv_fetch_array($rs))
			{
				$col_prs_id = $record['PRS_ID'];
				$col_prs_name = $record['PRS_NAME'];
				$col_prs_position = $record['PRS_POSITION'];

				$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
						<tr>
							<td class="leader">
								<ul>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
								</ul>
							</td>
<?
			}
		}
?>
							<td class="list1">
								<ul>
<?
		$sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION, PRS_EXTENSION FROM DF_PERSON WITH(NOLOCK) WHERE PRS_TEAM = 'fc'". $where . $orderbycase;
		$rs = sqlsrv_query($dbConn, $sql);

		While ($record = sqlsrv_fetch_array($rs))
		{
			$col_prs_id = $record['PRS_ID'];
			$col_prs_name = $record['PRS_NAME'];
			$col_prs_position = $record['PRS_POSITION'];

			$lamp_icon = getMemberCommuting($col_prs_id, $now_date, $yesterday_date);
?>
									<li>
										<?=$lamp_icon?> <span><?=$col_prs_position?></span> <?=$col_prs_name?>
									</li>
<?
		}
?>
								</ul>
							</td>
						</tr>
						<tr>
							<th class="teamname team" style="border-bottom:0px;"></th>
							<td class="list1 top">
						</tr>
					</tbody>
				</table>

			</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>

<div class="person_pop_detail" id="popup" style="display:none;">

</div>
</div>
</body>
</html>