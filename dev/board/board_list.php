<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
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
<? include INC_PATH."/top_menu.php"; ?>		
<form method="post" name="form" id="form" onKeyDown="javascript:if (event.keyCode == 13) {funSearch();}">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="board" id="board" value="<?=$board?>"> 
<!-- 서브 네비게이션 시작 -->
<div class="sub-menu-7">
    <nav class="navbar has-shadow is-size-7-mobile">
        <div class="container">
            <div class="navbar-tabs">
                <a class="navbar-item is-tab is-active" href="board_list.php">공지사항</a>
            </div>
        </div>
    </nav>
</div>
<!-- 서브 네비게이션 끝-->
<!-- 본문 시작 -->
<section class="section is-resize">
    <div class="container">
        <div class="columns is-vcentered">
            <!-- Left side -->
            <div class="column">
                <!-- todo 0413 구조 변경 -->
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
                            <button class="button is-link" id="btnSearch">
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>
                                <span>검색</span>
                            </button>
                        </div>
    
      									<div class="control is-hidden-tablet">
                             <button class="button is-link" >
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>                            
                        </div>
                        
    										<div class="control is-hidden-tablet">
                           <a href="board_write.php" class="button is-danger" >
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
                <a href="board_write.php" class="button is-danger">
                    <span class="icon is-small">
                        <i class="fas fa-pencil-alt"></i>
                    </span>
                    <span>게시물 작성</span>
                </a>
                </div>
            </div>
        </div>

        <table class="table is-fullwidth is-hoverable type-common">
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
            <!-- 상단 공지 -->
             <tbody class="notice">
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
            <tr>
                <td><span class="tag is-danger">공지</span></td>                
                <td>                							   								
                    <a href="javascript:funView(<?=$top_seqno?>);" style="cursor:hand">	                        
                        <span><?=getCutString($top_title,60);?></span>
                        <? if ($top_depth != "0") { ?>
													 <span class="tag is-rounded td-tag"><?=$top_depth?></span>
												<? } ?>
												<? if ($top_file1 != "" || $top_file2 != "" || $top_file3 != "") { ?> 첨부파일               																	                								
                 					<span class="icon is-small td-icon"><i class="fas fa-file"></i></span>
												<? }?>														
                    </a>
                </td>
                <td class="has-text-centered"><?=$top_position?>&nbsp;<?=$top_name?></td>
                <td class="has-text-centered"><?=$top_date?></td>
                <td class="has-text-centered"><?=$top_hit?></td>            
            <?
							}
							$i = $total_cnt-($page-1)*$per_page;

							if ($i == 0)
							{
						?>
						</tr>
            	<tr>
								<td colspan="5" style="has-text-centered">검색된 결과가 없습니다.</td>
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
					</tbody>                     
            <!-- 일반 리스트 -->            
            <tbody class="list">
            <tr>
                <td><?=$i?></td>
                <td>
                    <a href="javascript:funView(<?=$board_seqno?>);" style="cursor:hand">	
                        <span><?=getCutString($board_title,60);?></span>
                       <? if ($board_depth != "0") { ?>
													 <span class="tag is-rounded td-tag"><?=$board_depth?></span>
											 <? } ?>
				               <? if ($board_file1 != "" || $board_file2 != "" || $board_file3 != "") { ?>
													<span class="icon is-small td-icon"><i class="fas fa-file"></i></span>
											 <? } ?>                      
                    </a>
                </td>
                <td class="has-text-centered"><?=$board_position?>&nbsp;<?=$board_name?></td>
                <td class="has-text-centered"><?=$board_date?></td>
                <td class="has-text-centered"><?=$board_hit?></td>
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
</body>
</html>
