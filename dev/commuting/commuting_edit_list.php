<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : null; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	if ($prf_id == "4")
	{
		if ($keyword != "")
		{
			$searchSQL = " WHERE $keyfield like '%$keyword%'";
		}
	}
	else
	{
		$searchSQL = " WHERE PRS_ID = '$prs_id'";
	}
	$sql = "SELECT COUNT(*) FROM DF_CHECKTIME_EDIT WITH(NOLOCK)". $searchSQL ."";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;
	
	$sql = "SELECT
				SEQNO, PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, DATE, EDIT_OK, REG_DATE 
			FROM
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY SEQNO DESC) AS ROWNUM, 
					SEQNO, PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION, DATE, EDIT_OK, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE
				FROM 
					DF_CHECKTIME_EDIT WITH(NOLOCK)
				$searchSQL
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn,$sql);

	$per_page = 20;
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	$(document).ready(function(){
		//검색
		$("#btnSearch").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});
		//초기화
		$("#btnReset").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
		<? if ($prf_id == "4") { ?>
			$("#keyfield").val("");
			$("#keyword").val("");
		<? } ?>
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});
		//등록
		$("#btnWrite").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","_self");
			$("#form").attr("action","commuting_edit.php"); 
			$("#form").submit();
		});
		//보기
		$("[name=linkView]").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","_self");
			$("#form").attr("action","commuting_edit_detail.php?seqno="+$(this).attr("title")); 
			$("#form").submit();
		});
	});
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form" id="form">
<input type="hidden" name="page" value="<?=$page?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/commuting_menu.php"; ?>
			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
					<table class="notable" width="100%">
						<tr>
						<?	if ($prf_id == "4") {	?>
							<th scope="row">검색</th>
							<td>
								<select name="keyfield" id="keyfield" style="width:109px;">
									<option value="PRS_NAME">작성자</option>
								</select>
								<input id="keyword" class="df_textinput" type="text" style="width:265px;" name ="keyword" value="<?=$keyword?>"/>
								<img src="../img/btn_search.gif" alt="검색" id="btnSearch" />
								<img src="../img/btn_reset.gif" alt="리셋" id="btnReset" />
							</td>
						<?	} else {	?>
							<th></th>
							<td></td>
						<?	}	?>
							<td align="right">
								<img src="../img/write.jpg" alt="게시물 작성" id="btnWrite" class="btn_right" />
							</td>
						</tr>
					</table>
				</div>
				<table class="notable work1 work_stats"  width="100%">
					<caption>수정요청 테이블</caption>
					<colgroup>
						<col width="10%" />
						<col width="15%" />
						<col width="15%" />
						<col width="15%" />
						<col width="15%"/>
						<col width="15%" />
						<col width="15%" />
					</colgroup>
					<thead>
						<tr>
							<th>no.</th>
							<th>이름</th>
							<th>직급</th>
							<th>부서</th>
							<th>근무일</th>
							<th>수정여부</th>
							<th>작성일</th>
						</tr>
					</thead>
					<tbody>
					<?
						$i = $total_cnt-($page-1)*$per_page;
						if ($i == 0)
						{
					?>
						<tr>
							<td colspan="7" class="bold">해당 요청건이 없습니다.</td>
						</tr>
					<?
						}
						else
						{
							while ($record = sqlsrv_fetch_array($rs))
							{
								$edit_seqno = $record['SEQNO'];
								$edit_name = $record['PRS_NAME'];
								$edit_team = $record['PRS_TEAM'];
								$edit_position = $record['PRS_POSITION'];
								$edit_date = $record['DATE'];
								$edit_ok = $record['EDIT_OK'];
								$edit_regdate = $record['REG_DATE'];

								if ($edit_ok == "Y")
								{
									$edit_ok_txt = "적용완료";
								}
								else if ($edit_ok == "X")
								{
									$edit_ok_txt = "요청기각";
								}
					?>
						<tr>
							<td<? if ($edit_ok == "N") { ?> class="bold"<? } ?>><?=$i?></td>
							<td<? if ($edit_ok == "N") { ?> class="bold"<? } ?> name="linkView" title="<?=$edit_seqno?>"><?=$edit_name?></td>
							<td<? if ($edit_ok == "N") { ?> class="bold"<? } ?>><?=$edit_position?></td>
							<td<? if ($edit_ok == "N") { ?> class="bold"<? } ?>><?=$edit_team?></td>
							<td<? if ($edit_ok == "N") { ?> class="bold"<? } ?>><?=$edit_date?></td>
							<td<? if ($edit_ok == "N") { ?> class="bold"<? } ?>><?=$edit_ok_txt?></td>
							<td<? if ($edit_ok == "N") { ?> class="bold"<? } ?>><?=$edit_regdate?></td>
						</tr>
					<?
								$i--;
							}
						}
					?>
					</tbody>
				</table>
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
