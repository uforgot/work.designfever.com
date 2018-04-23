<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/weekly_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y'); 
	$weekday = date('w'); 
	$z_day = date('z'); 

	//if ($_REQUEST['year'] == "" && $z_day < 5 && $weekday < 5) { $year = $year - 1; }

	$searchSQL = " WHERE PRS_ID = '$prs_id' AND WEEK_ORD LIKE '$year%'";

	$sql = "SELECT COUNT(SEQNO) FROM DF_WEEKLY WITH(NOLOCK) ". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 12;

	$sql = "SELECT 
				T.SEQNO, T.WEEK_ORD, T.WEEK_ORD_TOT, T.WEEK_AREA, T.TITLE, T.COMPLETE_YN, T.REG_DATE
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) AS ROWNUM, 
					SEQNO, WEEK_ORD, WEEK_ORD_TOT, WEEK_AREA , TITLE, COMPLETE_YN, REG_DATE
				FROM 
					DF_WEEKLY WITH(NOLOCK)
				$searchSQL
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function yearSearch(val) {
		document.location.href = "./weekly_list.php?year=" + val;
	}
</script>
</head>
<body>
<div class="wrapper">
<form name="form" method="post">
<form name="form" id="form" method="post">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="year" id="year" value="<?=$year?>">

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
								<select name="year" style="width:109px;" onchange="javascript:yearSearch(this.value);">
									<?
										for ($i=2014; $i<=date("Y"); $i++) 
										{
											if ($i == $year) 
											{ 
												$selected = " selected"; 
											}
											else
											{
												$selected = "";
											}
									?>
											<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
									<?
										}
									?>
								</select><span>년</span>
							</td>
						</tr>
					</table>
				</div>


				<table class="vacation notable work1 work_stats" width="100%" style="margin-bottom:10px;">
					<caption>팀원 주간보고서 테이블</caption>
					<colgroup>
						<col width="5%" />
						<col width="20%" />
						<col width="*" />
						<col width="110" />
					</colgroup>

					<thead>
						<tr>
							<th>주차</th>
							<th>기간</th>
							<th>제목</th>
							<th></th>
						</tr>
					</thead>

					<tbody>
<?
	$i = $total_cnt-($page-1)*$per_page;
	if ($i==0) 
	{
?>
							<tr>
								<td colspan="6" class="bold">해당 정보가 없습니다.</td>
							</tr>
<?
	}
	else
	{
		while ($record = sqlsrv_fetch_array($rs))
		{
			$seqno = $record['SEQNO'];
			$ord = $record['WEEK_ORD'];
			$ord_tot = $record['WEEK_ORD_TOT'];
			$week_area = $record['WEEK_AREA'];
			$title = $record['TITLE'];
			$reg_date = $record['REG_DATE'];
			$complete_yn = $record['COMPLETE_YN'];

			//현재 주차일 경우
			if($ord == $winfo["cur_week"]) {
				$link = "weekly_write.php";
			//이전 주차일 경우
			} else {
				$link = "weekly_view.php";
			}

			if($complete_yn == 'Y') 
			{
				$write_btn = "";
				$write_txt = "<a href='./$link?type=modify&seqno=$seqno&page=$page'>". $title ."</a>";
			} 
			else
			{
				$link = "weekly_write.php";

				if(!$reg_date)
				{
					$write_btn = "<a href='./$link?type=write&seqno=$seqno&page=$page'><img src='/img/weekly/btn_list_write.png'></a>";
					$write_txt = "<a href='./$link?type=write&seqno=$seqno&page=$page'>". $title ."</a>";
				}
				else
				{
					$write_btn = "<a href='./$link?type=modify&seqno=$seqno&page=$page'><img src='/img/weekly/btn_list_modify.png'></a>";
					$write_txt = "<a href='./$link?type=modify&seqno=$seqno&page=$page'>". $title ."</a>";
				}
			}
?>
							<!-- loop -->						
							<tr>
								<td><?=$ord_tot?></td>
								<td><?=$week_area?></td>
								<td><?=$write_txt?></td>
								<td><?=$write_btn?></td>
							</tr>
							<!-- loop -->
<?
			$i--;
		}
	}
?>
					</tbody>
					<tfoot>

					</tfoot>					
				</table>
				<span style="padding-left:40px;">
					<b class="txt_left_p" style="margin-bottom:0px;">* 팀장이 주간보고 작성을 완료하면, 팀원이 작성한 주간보고의 수정기능은 없어집니다.</b>
				</span>

				<div class="page_num">
				<?=getPaging($total_cnt,$page,$per_page);?>
				</div>	

			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>

