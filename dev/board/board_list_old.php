<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//권한 체크
	if ($prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$board = isset($_REQUEST['board']) ? $_REQUEST['board'] : "default"; 
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$searchSQL = " WHERE TMP3 = '$board' AND NOTICE_YN = 'N'";

	if ($keyword == "")
	{
		$searchSQL .= "";
	}
	else if ( $keyfield == "ALL")
	{
		$searchSQL .= " AND (TITLE LIKE '%$keyword%' OR CONTENTS LIKE '%$keyword%' OR PRS_NAME LIKE '%$keyword%')";
	} 
	else if ($keyfield =="TITLE_CONTENTS") 
	{
		$searchSQL .= " AND (TITLE LIKE '%$keyword%' OR CONTENTS LIKE '%$keyword%')";
	}
	else
	{
		$searchSQL .= " AND $keyfield like '%$keyword%'";
	}
	$sql = "SELECT COUNT(*) FROM DF_BOARD WITH(NOLOCK)". $searchSQL ."";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;
	
	$sql = "SELECT
				SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, REG_DATE, FILE_1, FILE_2, FILE_3
			FROM
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY SEQNO DESC) AS ROWNUM, 
					SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3
				FROM 
					DF_BOARD WITH(NOLOCK)
				$searchSQL
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn,$sql);
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
			$("#keyfield").val("");
			$("#keyword").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});

		//등록
		$("#btnWrite").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#keyfield").val("");
			$("#keyword").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","board_write.php"); 
			$("#form").submit();
		});
	});

	//게시물 읽기
	function funView(seqno)
	{
		$("#form").attr("target","_self");
		$("#form").attr("action","board_detail.php?seqno="+seqno); 
		$("#form").submit();
	}
</script>

</head>

<body>
<div class="wrapper">
<form method="post" name="form" id="form" onKeyDown="javascript:if (event.keyCode == 13) {funSearch();}">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="board" id="board" value="<?=$board?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<p class="hello work_list">
			<a href="board_list.php?board=default"><strong>+  공지사항</strong></a>
			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row"><label for="">검색</label></th>
							<td>
								<select name="keyfield" id="keyfield" style="width:109px;">
									<option value="ALL"<? if ($keyfield == "ALL") { echo " selected"; } ?>>전체</option>
                                    <option value="TITLE_CONTENTS"<? if ($keyfield == "TITLE_CONTENTS") { echo " selected"; } ?>>제목+본문</option>
                                    <option value="TITLE"<? if ($keyfield == "TITLE") { echo " selected"; } ?>>제목</option>
                                    <option value="CONTENTS"<? if ($keyfield == "CONTENTS") { echo " selected"; } ?>>본문</option>
									<option value="PRS_NAME"<? if ($keyfield == "PRS_NAME") { echo " selected"; } ?>>작성자</option>
								</select>
								<input id="keyword" class="df_textinput" type="text" style="width:265px;" name ="keyword" value="<?=$keyword?>"/>
								<img src="../img/btn_search.gif" alt="검색" id="btnSearch" />
							<? if ($keyword != "") { ?>
								<img src="../img/btn_reset.gif" alt="리셋" id="btnReset" />
							<? } ?>
							</td>
						</tr>
					</table>
					<img src="../img/btn_write.gif" alt="게시물 작성" id="btnWrite" class="btn_right" />
				</div>
				<div class="board_list">
					<table class="notable work3 board_list"  width="100%">
						<caption>게시판 리스트 테이블</caption>
						<colgroup>
							<col width="5%" />
							<col width="*" />
							<col width="10%" />
							<col width="10%" />
							<col width="10%" />
						</colgroup>
						<thead>
							<tr>
								<th>No.</th>
								<th>제목</th>
								<th>작성자</th>
								<th>날짜</th>
								<th>조회수</th>
							</tr>
						</thead>
						<tbody>
						<!-- 공지사항 리스트 출력부분-->
						<?
							$topSQL = "SELECT
										SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3
									FROM 
										DF_BOARD WITH(NOLOCK)
									WHERE 
										TMP3 = '$board' AND NOTICE_YN = 'Y'
									ORDER BY 
										SEQNO DESC";
							$topRs = sqlsrv_query($dbConn,$topSQL);

							while ($topRecord = sqlsrv_fetch_array($topRs))
							{
								$top_seqno = $topRecord['SEQNO'];
								$top_id = $topRecord['PRS_ID'];
								$top_name = $topRecord['PRS_NAME'];
								$top_login = $topRecord['PRS_LOGIN'];
								$top_team = $topRecord['PRS_TEAM'];
								$top_position = $topRecord['PRS_POSITION'];
								$top_title = $topRecord['TITLE'];
								$top_contents = $topRecord['CONTENTS'];
								$top_hit = $topRecord['HIT'];
								$top_depth = $topRecord['REP_DEPTH'];
								$top_notice = $topRecord['NOTICE_YN'];
								$top_date = $topRecord['REG_DATE'];
								$top_file1 = trim($topRecord['FILE_1']);
								$top_file2 = trim($topRecord['FILE_2']);
								$top_file3 = trim($topRecord['FILE_3']);
						?>
							<tr class="important">
								<td>&nbsp;</td>
								<td class="board_list_title">
								<? if ($top_file1 != "" || $top_file2 != "" || $top_file3 != "") { ?>
									<span class="hasfile">첨부파일</span>
								<? } else { ?>
									<span class="nofile">nofile</span>
								<? } ?>
									<strong class="icon_notice">공지</strong>
									<a href="javascript:funView(<?=$top_seqno?>);" style="cursor:hand"><p><?=getCutString($top_title,60);?></p>
								<? if ($top_depth != "0") { ?>
									<span class="hasreply">[<span><?=$top_depth?></span>]</span>
								<? } ?>
									</a>
								</td>
								<td><?=$top_position?>&nbsp;<?=$top_name?></td>
								<td><?=$top_date?></td>
								<td class="bold"><?=$top_hit?></td>
							</tr>
						<?
							}

							$i = $total_cnt-($page-1)*$per_page;

							if ($i == 0)
							{
						?>
							<tr>
								<td colspan="5" class="bold">검색된 결과가 없습니다.</td>
							</tr>
						<?
							}
							else
							{
								while ($record = sqlsrv_fetch_array($rs))
								{
									$board_seqno = $record['SEQNO'];
									$board_id = $record['PRS_ID'];
									$board_name = $record['PRS_NAME'];
									$board_login = $record['PRS_LOGIN'];
									$board_team = $record['PRS_TEAM'];
									$board_position = $record['PRS_POSITION'];
									$board_title = $record['TITLE'];
									$board_contents = $record['CONTENTS'];
									$board_hit = $record['HIT'];
									$board_depth = $record['REP_DEPTH'];
									$board_notice = $record['NOTICE_YN'];
									$board_date = $record['REG_DATE'];
									$board_file1 = trim($record['FILE_1']);
									$board_file2 = trim($record['FILE_2']);
									$board_file3 = trim($record['FILE_3']);
						?>
						<!-- 일반 리스트 출력부분-->
							<tr>
								<td><?=$i?></td>
								<td class="board_list_title">
								<? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?>
									<span class="hasfile"><!==첨부파일</span>
								<? } else { ?>
									<span class="nofile"></span>
								<? } ?>
									<a href="javascript:funView(<?=$board_seqno?>);" style="cursor:hand"><p><?=getCutString($board_title,60);?></p>
								<? if ($board_depth != "0") { ?>
									<span class="hasreply">[<span><?=$board_depth?></span>]</span>
								<? } ?>
									</a>
								</td>
								<td><?=$board_position?>&nbsp;<?=$board_name?></td>
								<td><?=$board_date?></td>
								<td class="bold"><?=$board_hit?></td>
							</tr>
						<?
									$i--;
								}
							}
						?>					 
						</tbody>
					</table>							
					<?=getPaging($total_cnt,$page,$per_page);?>					
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
