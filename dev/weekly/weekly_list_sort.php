<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	// 현재 주차 정보
	$winfo = getWeekInfo(date('Y-m-d'));

	$s_date = isset($_REQUEST['s_date'])?$_REQUEST['s_date']:$winfo['cur_week'];	// 시작일
	$e_date = isset($_REQUEST['e_date'])?$_REQUEST['e_date']:$winfo['cur_week'];	// 종료일

	$selected1[$s_date] = "selected";
	$selected2[$e_date] = "selected";	

	// 주차 정보
	$sql = "SELECT DISTINCT WEEK_ORD, WEEK_AREA FROM DF_WEEKLY ORDER BY WEEK_ORD DESC";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$select[] = array
						(
							'week_ord'=>$record['WEEK_ORD'],
							'week_area'=>$recoed['WEEK_AREA']
						);
	}

	//리스트 추출
	$searchSQL = " WHERE B.THIS_WEEK_RATIO > 0 AND (A.WEEK_ORD >= '$s_date' AND A.WEEK_ORD <= '$e_date')";

	$sql = "SELECT 
				A.WEEK_AREA, A.PRS_NAME, B.PROJECT_NO, B.THIS_WEEK_RATIO,
				(SELECT DISTINCT TITLE FROM DF_PROJECT WHERE PROJECT_NO = B.PROJECT_NO) PROJECT_NAME
			FROM 
				DF_WEEKLY A WITH(NOLOCK) 
				INNER JOIN DF_WEEKLY_DETAIL B WITH(NOLOCK) 
				ON A.SEQNO = B.WEEKLY_NO
			$searchSQL
			ORDER BY
				B.PROJECT_NO DESC, A.PRS_NAME, B.WEEKLY_NO DESC";

	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		if($record['PROJECT_NO'] == "DF0000_ETC") $record['PROJECT_NAME'] = "기타업무";

		$list[] = array
						(
							'week_area'=>$record['WEEK_AREA'],
							'name'=>$record['PRS_NAME'],
							'project_name'=>$record['PROJECT_NAME'],
							'this_ratio'=>$record['THIS_WEEK_RATIO']
						);
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function sort_list() 
	{
		var frm = document.form;	

		var s_date = frm.s_date.value;
		var e_date = frm.e_date.value;
		
		if(!s_date || !e_date) {
			alert("검색 시작일과 종료일을 선택해 주세요.");
			return;
		}

		if(s_date > e_date) {
			alert("검색 종료일이 시작일보다 이전입니다.");
			return;
		}

		frm.action = "weekly_list_sort.php";
		frm.submit();
	}

	function excel_download()
	{
		var frm = document.form;

		frm.target = "hdnFrame";
		frm.action = "weekly_list_sort_excel.php";
		frm.submit();
	}
</script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post">

	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/weekly_menu.php"; ?>

			<div class="work_wrap clearfix">

				<div class="vacation_stats clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row">&nbsp;</th>
<!-- 							<th width="50%" scope="row">팀원 주간보고서</th> -->
							<td>
								<select name="s_date">
									<!--<option value="">+ 시작일 +</option>-->
<?
							foreach($select as $key => $val)
							{
								$week_ord = $val['week_ord'];
								$y = substr($week_ord,0,4);
								$m = substr($week_ord,4,2);
								$w = substr($week_ord,6,1);
								$week_str = $y."년 ".$m."월 ".$w."주차";

								echo "<option value='".$week_ord."' ".$selected1[$week_ord].">".$week_str."</option>";
							}
?>
								</select>~&nbsp;
								<select name="e_date">
									<!--<option value="">+ 종료일 +</option>-->
<?
							foreach($select as $key => $val)
							{
								$week_ord = $val['week_ord'];
								$y = substr($week_ord,0,4);
								$m = substr($week_ord,4,2);
								$w = substr($week_ord,6,1);
								$week_str = $y."년 ".$m."월 ".$w."주차";

								echo "<option value='".$week_ord."' ".$selected2[$week_ord].">".$week_str."</option>";
							}
?>
								</select>
								<a href="javascript:sort_list();"><img src="../img/project/btn_search_p.gif" alt="검색"></a>
								<a href="javascript:excel_download();"><img src="../img/btn_excell.gif" alt="엑셀다운로드"></a>
							</td>
						</tr>
					</table>
				</div>


				<table class="vacation notable work1 work_stats" width="100%" style="margin-bottom:10px;">
					<caption>팀원 주간보고서 테이블</caption>
					<colgroup>
						<col width="10%" />
						<col width="18%" />
						<col width="15%" />
						<col width="*" />
						<col width="15%" />
					</colgroup>

					<thead>
						<tr>
							<th>번호</th>
							<th>기간</th>
							<th>성명</th>
							<th>프로젝트</th>
							<th>참여비율(%)</th>
						</tr>
					</thead>

					<tbody>
<?
	if (count($list)==0) 
	{
?>
						<tr>
							<td colspan="5" class="bold">해당 정보가 없습니다.</td>
						</tr>
<?
	}
	else
	{
		$cnt = count($list);

		foreach($list as $key => $val)
		{
			$border = "border-bottom:1px solid #e3e3e3;";

			$contents .= "<tr>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$cnt."</td>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$val['week_area']."</td>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$val['name']."</td>";
			$contents .= "	<td style='text-align:left;vertical-align:top;$border'>".$val['project_name']."</td>";
			$contents .= "	<td style='text-align:center;vertical-align:top;$border'>".$val['this_ratio']."</td>";
			$contents .= "</tr>";

			$cnt--;
		}

		echo $contents;
	}
?>
					</tbody>
					<tfoot>

					</tfoot>					
				</table>

			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
