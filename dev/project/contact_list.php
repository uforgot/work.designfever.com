
<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	//권한체크(근무사원)
	if ($prf_id == "5" || $prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("등록대기,탈퇴회원 이용불가 페이지입니다.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$board = "contact"; 

	//권한체크(의뢰프로젝트)
	if ($board == "contact" && (!in_array($prf_id,array("2","3","4")) && $prs_team != '경영지원팀')) 
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("현재 게시판의 사용 권한이 없습니다.");
		history.back();
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null; 
	$keyfield = isset($_REQUEST['keyfield']) ? $_REQUEST['keyfield'] : "ALL"; 
	$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : null; 

	$searchSQL = " WHERE TMP3 = '$board'";

	if ($subject != "")
	{
		$searchSQL .= " AND TMP1 = '$subject'";
	}

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
				SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, REG_DATE, FILE_1, FILE_2, FILE_3, TMP1 
			FROM
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY SEQNO DESC) AS ROWNUM, 
					SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, CONTENTS, HIT, REP_DEPTH, NOTICE_YN, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE, FILE_1, FILE_2, FILE_3, TMP1 
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
			$("#subject").val("");
			$("#keyfield").val("");
			$("#keyword").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});

		//등록
		$("#btnWrite").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#subject").val("");
			$("#keyfield").val("");
			$("#keyword").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","contact_write.php"); 
			$("#form").submit();
		});
	});

    function funWrite(){
        $("#page").val("1");
        $("#subject").val("");
        $("#keyfield").val("");
        $("#keyword").val("");
        $("#form").attr("target","_self");
        $("#form").attr("action","contact_write.php");
        $("#form").submit();
    }

	//게시물 읽기
	function funView(seqno)
	{
		$("#form").attr("target","_self");
		$("#form").attr("action","contact_detail.php?seqno="+seqno); 
		$("#form").submit();
	}
</script>
</head>

<body>
<? include INC_PATH."/top_menu.php"; ?>

<form method="post" name="form" id="form" onKeyDown="javascript:if (event.keyCode == 13) {funSearch();}">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="board" id="board" value="<?=$board?>">
<? include INC_PATH."/project_menu.php"; ?>
    <section class="section is-resize">
        <div class="container">
            <div class="columns is-vcentered">
                <!-- Left side -->
                <div class="column">
                    <div class="field is-grouped">
                        <div class="control select">
                            <select name="keyfield" id="keyfield" >
                                <option value="ALL"<? if ($keyfield == "ALL") { echo " selected"; } ?>>전체</option>
                                <option value="TITLE_CONTENTS"<? if ($keyfield == "TITLE_CONTENTS") { echo " selected"; } ?>>제목+본문</option>
                                <option value="TITLE"<? if ($keyfield == "TITLE") { echo " selected"; } ?>>제목</option>
                                <option value="CONTENTS"<? if ($keyfield == "CONTENTS") { echo " selected"; } ?>>본문</option>
                                <option value="PRS_NAME"<? if ($keyfield == "PRS_NAME") { echo " selected"; } ?>>작성자</option>
                            </select>
                        </div>

                        <div class="control is-expanded">
                            <input id="keyword" class="input" type="text" placeholder="" type="text" name ="keyword" value="<?=$keyword?>">
                        </div>
                        <div class="control is-hidden-mobile">
                            <a class="button is-link" id="btnSearch">
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>
                                <span>검색</span>
                            </a>
                        </div>

                        <div class="control is-hidden-tablet">
                            <a class="button is-link" >
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>
                            </a>
                        </div>

                        <div class="control is-hidden-tablet" >
                            <a href="javascript:funWrite();" class="button is-danger" id="btnWrite">
                                <span class="icon is-small">
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </a>
                        </div>

                    </div>
                </div>
                <!-- Right side -->
                <div class="column is-hidden-mobile">
                    <div class="control has-text-right">
                        <a href="javascript:funWrite();" class="button is-danger" id="btnWrite">
                    <span class="icon is-small">
                        <i class="fas fa-pencil-alt"></i>
                    </span>
                            <span>게시물 작성</span>
                        </a>
                    </div>
                </div>
            </div>

            <table class="table is-fullwidth is-hoverable is-resize">
                <colgroup>
                    <col width="8%">
                    <col width="*">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                </colgroup>
                <thead>
                <tr>
                    <th><span class="is-hidden-mobile">No.</span></th>
                    <th>제목</th>
                    <th class="has-text-centered">작성자</th>
                    <th class="has-text-centered">날짜</th>
                    <th class="has-text-centered">조회수</th>
                </tr>
                </thead>
                <tbody>

                <?

                $i = $total_cnt-($page-1)*$per_page;

                if ($i == 0)
                {
                    ?>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center;">검색된 결과가 없습니다.</td>
                    </tr>
                    <?
                }
                else
                {
                while ($record = sqlsrv_fetch_array($rs))
                {
                $book_seqno = $record['SEQNO'];
                $book_id = $record['PRS_ID'];
                $book_name = $record['PRS_NAME'];
                $book_login = $record['PRS_LOGIN'];
                $book_team = $record['PRS_TEAM'];
                $book_position = $record['PRS_POSITION'];
                $book_title = $record['TITLE'];
                $book_contents = $record['CONTENTS'];
                $book_hit = $record['HIT'];
                $book_depth = $record['REP_DEPTH'];
                $book_notice = $record['NOTICE_YN'];
                $book_date = $record['REG_DATE'];
                $book_file1 = trim($record['FILE_1']);
                $book_file2 = trim($record['FILE_2']);
                $book_file3 = trim($record['FILE_3']);
                $book_tmp1 = $record['TMP1'];
                ?>
                </tbody>
                <!-- 일반 리스트 -->
                <tbody class="list">
                <tr>
                    <td><?=$i?></td>
                    <td>
                        <a href="javascript:funView(<?=$book_seqno?>);" style="cursor:hand">
                            <? if ($board == "ilab" && $book_tmp1 != "") { ?>
                                <span>[<?=$book_tmp1?>] <?=getCutString($book_title,60);?></span>
                            <? } else { ?>
                                <span><?=getCutString($book_title,60);?></span>
                            <? } ?>
                            <? if ($book_depth != "0") { ?>
                                <span class="tag is-rounded td-tag"><?=$book_depth?></span>
                            <? } ?>
                            <? if ($book_file1 != "" || $book_file2 != "" || $book_file3 != "") { ?>
                                <span class="icon is-small td-icon"><i class="fas fa-file"></i></span>
                            <? } ?>
                        </a>
                    </td>
                    <td class="has-text-centered"><?=$book_position?>&nbsp;<?=$book_name?></td>
                    <td class="has-text-centered"><?=$book_date?></td>
                    <td class="has-text-centered"><?=$book_hit?></td>
                </tr>
                <?
                $i--;
                }
                }
                ?>
                </tbody>
            </table>
            <!--페이징처리-->
            <nav class="pagination" role="navigation" aria-label="pagination">
                <?=getPaging($total_cnt,$page,$per_page);?>
                </ul>
            </nav>
            <!--페이징처리-->
        </div>
    </section>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
