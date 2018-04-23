<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	//권한 체크
	if ($prf_id != "4") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("해당페이지는 관리자만 확인 가능합니다.");
		location.href="vacation_list.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : null;
	$p_mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
	$p_vacation = isset($_REQUEST['vacation']) ? $_REQUEST['vacation'] : null;
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

	$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : date("Y"); 
	$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : date("m"); 
	if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
	$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : 1; 
	if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
	$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : date("Y"); 
	$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : date("m"); 
	if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
	$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d"); 
	if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

	$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
	$to_date = $to_year ."-". $to_month ."-". $to_day;

	$searchSQL = " WHERE USE_YN = 'Y' AND STATUS NOT IN ('임시') AND FORM_CATEGORY IN ('휴가계') AND CONVERT(char(10),REG_DATE,120) BETWEEN '$fr_date' AND '$to_date'";
	if ($p_status != "")
	{
		switch($p_status)
		{
			case "미결재" : 
				$searchSQL .= " AND STATUS IN ('미결재','진행중')";
				break;
			case "기각" : 
				$searchSQL .= " AND STATUS IN ('보류','기각')";
				break;
			case "결재" : 
				$searchSQL .= " AND STATUS IN ('전결','결재')";
				break;
		}
	}
	if ($p_mode == "team")
	{
		if ($p_team != "")
		{
			$searchSQL .= " AND PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team')))";
		}
	}
	else if ($p_mode == "vacation")
	{
		if ($p_vacation != "")
		{
			$searchSQL .= " AND FORM_TITLE LIKE '%". $p_vacation ."%'";
		}
	}
	if ($p_name != "") 
	{
		$searchSQL .= " AND PRS_NAME = '$p_name'";
	}

	$sql = "SELECT COUNT(DISTINCT DOC_NO) FROM DF_APPROVAL WITH(NOLOCK)". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT 
				T.DOC_NO, T.COUNT
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(ORDER BY DOC_NO DESC) AS ROWNUM,
					DOC_NO, COUNT(SEQNO) AS COUNT
				FROM 
					DF_APPROVAL WITH(NOLOCK)
				$searchSQL
				GROUP BY 
					DOC_NO
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		//날짜 지정
		$("#fr_year, #fr_month, #fr_day").change(function() {
			$("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
		});
		$("#fr_date").datepicker({
			onSelect: function (selectedDate) {
				$("#fr_year").val( selectedDate.substring(6,10) );
				$("#fr_month").val( selectedDate.substring(0,2) );
				$("#fr_day").val( selectedDate.substring(3,5) );
			}
		});
		$("#to_year, #to_month, #to_day").change(function() {
			$("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
		});
		$("#to_date").datepicker({
			onSelect: function (selectedDate) {
				$("#to_year").val( selectedDate.substring(6,10) );
				$("#to_month").val( selectedDate.substring(0,2) );
				$("#to_day").val( selectedDate.substring(3,5) );
			}
		});
	});
</script>
<script src="/assets/js/vacation.js"></script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>		
<form name="form" method="post">
<input type="hidden" name="page" value="<?=$page?>">
<? include INC_PATH."/vacation_menu.php";?>
<!-- 본문 시작 -->
<section class="section is-resize">
    <div class="container">
        <div class="content">
        	<!--검색 영역-->
            <div class="box">
                <div class="columns is-column-marginless">                	
                  <div class="column" style="display:inline-block;flex-grow:0;flex-basis:auto;">
										<div class="field is-group">
						    			
						    				<div class="control select">
							    					<select name="fr_year" id="fr_year">
															<?
																for ($i=$startYear; $i<=($fr_year+1); $i++) 
																{
																	if ($i == $fr_year) 
																	{  $selected = " selected"; }
																	else
																	{ $selected = ""; }
																	echo "<option value='".$i."'".$selected.">".$i."년</option>";
																}
															?>
														</select>
												</div>													
												<div class="control select">
														<select name="fr_month" id="fr_month">
														<?
															for ($i=1; $i<=12; $i++) 
															{
																if (strlen($i) == "1") 
																{ $j = "0".$i; }
																else
																{ $j = $i; }
						
																if ($j == $fr_month)
																{ $selected = " selected"; }
																else
																{ $selected = ""; }
																echo "<option value='".$j."'".$selected.">".$i."월</option>";
															}
														?>
														</select>
												</div>																
												<div class="control select">
															<select name="fr_day" id="fr_day">
															<?
																for ($i=1; $i<=31; $i++) 
																{
																	if (strlen($i) == "1") 
																	{ $j = "0".$i; }
																	else
																	{ $j = $i; }						
																	if ($j == $fr_day)
																	{ $selected = " selected"; }
																	else
																	{ $selected = ""; }						
																	echo "<option value='".$j."'".$selected.">".$i."일</option>";
																}
															?>
															</select>
												</div>														
												<!--<input type="hidden" id="fr_date" class="datepicker">-->
												<div class="button"></div>
														
										</div>
									</div>
									<div class="column">
										<div class="field is-group">
														
												<div class="control select">					
													<select name="to_year" id="to_year">
															<?
																for ($i=$startYear; $i<=($to_year+1); $i++) 
																{
																	if ($i == $to_year) 
																	{ $selected = " selected"; }
																	else
																	{ $selected = ""; }							
																	echo "<option value='".$i."'".$selected.">".$i."년</option>";
																}
															?>
															</select>
												</div>																
												<div class="control select">	
														<select name="to_month" id="to_month">
															<?
																for ($i=1; $i<=12; $i++) 
																{
																	if (strlen($i) == "1") 
																	{ $j = "0".$i; }
																	else
																	{ $j = $i; }							
																	if ($j == $to_month)
																	{ $selected = " selected"; }
																	else
																	{ $selected = ""; }
																	echo "<option value='".$j."'".$selected.">".$i."월</option>";
																}
															?>
															</select>
												</div>																
												<div class="control select">	
															<select name="to_day" id="to_day">
															<?
																for ($i=1; $i<=31; $i++) 
																{
																	if (strlen($i) == "1") 
																	{ $j = "0".$i; }
																	else
																	{ $j = $i; }							
																	if ($j == $to_day)
																	{ $selected = " selected"; }
																	else
																	{ $selected = "";}							
																	echo "<option value='".$j."'".$selected.">".$i."일</option>";
																}
															?>
															</select>
												</div>																
												<input type="hidden" id="to_date" class="datepicker">
											
														
										</div>
									</div>																							
                </div>
                <div class="columns is-column-marginless">
                    <div class="column">
                    	<div class="field is-group">
                        <div class="control select">
                            <select name="status">
                                <option value="">승인여부 전체</option>
																<option value="미결재"<? if ($p_status == "미결재") { echo " selected"; } ?>>미결재</option>
																<option value="기각"<? if ($p_status == "기각") { echo " selected"; } ?>>기각</option>
																<option value="결재"<? if ($p_status == "결재") { echo " selected"; } ?>>결재</option>
                            </select>
                        </div>
                     		<div class="control select">
						               	<select name="mode" onChange="javascript:selCase(this.form);">
															<option value="">전체</option>
														<	<option value="team"<? if ($p_mode == "team") { echo " selected"; } ?>>부서</option>
															<option value="vacation"<? if ($p_mode == "vacation") { echo " selected"; } ?>>휴가</option>
														</select>														
												</div>
											</div>												
											<div class="field">
												<div class="control select">		
														<select name="team" style="display:<? if ($p_mode == "team") { echo ""; } else { echo " none"; } ?>; ">			
															<option value=""<? if ($p_team2 == ""){ echo " selected"; } ?>>전직원</option>
														<?
																$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
																$selRs = sqlsrv_query($dbConn,$selSQL);
								
																while ($selRecord = sqlsrv_fetch_array($selRs))
																{
																	$selStep = $selRecord['STEP'];
																	$selTeam = $selRecord['TEAM'];
								
																	$blank = "";
																	for ($i=3;$i<=$selStep;$i++)
																	{
																		$blank .= "&nbsp;&nbsp;&nbsp;";
																	}
															?>
																	<option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$blank?><?=$selTeam?></option>
															<?
																}
														?>
														</select>			
												</div>
											</div>
												<div class="control select">		
														<select name="vacation" style="display:<? if ($p_mode == "vacation") { echo ""; } else { echo " none"; } ?>;" >
															<option value="">전체</option>
															<option value="연차"<? if ($p_vacation == "연차") { echo " selected"; } ?>>연차</option>
															<option value="병가"<? if ($p_vacation == "병가") { echo " selected"; } ?>>병가</option>
															<option value="반차"<? if ($p_vacation == "반차") { echo " selected"; } ?>>반차</option>
															<option value="리프레쉬"<? if ($p_vacation == "리프레쉬") { echo " selected"; } ?>>리프레쉬</option>
															<option value="프로젝트"<? if ($p_vacation == "프로젝트") { echo " selected"; } ?>>프로젝트</option>
															<option value="무급"<? if ($p_vacation == "무급") { echo " selected"; } ?>>무급</option>
															<option value="경조사"<? if ($p_vacation == "경조사") { echo " selected"; } ?>>경조사</option>
															<option value="예비군"<? if ($p_vacation == "예비군") { echo " selected"; } ?>>예비군</option>
															<option value="기타"<? if ($p_vacation == "기타") { echo " selected"; } ?>>기타</option>
															<option value="휴가 소진시"<? if ($p_vacation == "휴가 소진시") { echo " selected"; } ?>>휴가 소진시</option>															
													</select>												
												</div>													
                       </div>   
                    </div>                    
                </div>
                <div class="field is-grouped">
                    <div class="control is-expanded">
                        <input class="input" type="text" placeholder="">
                    </div>
                    <div class="control">
                        <a href="javascript:funSearch(this.form);" class="button is-link">
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>
                            <span>검색</span>
                        </a>
                    </div>
                    <div class="control">
                        <a href="vacation_member.php" class="button is-danger">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                            <span>초기화</span>
                        </a>
                    </div>
                </div>
            </div>
      	<!--검색 영역-->
            <table class="table is-fullwidth is-hoverable">
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
                    <th>기안일 / 기안자</th>
                    <th class="has-text-centered">승인여부</th>
                    <th class="has-text-centered">종류</th>
                    <th class="has-text-centered">등록일</th>
                </tr>
                </thead>
                <!-- 일반 리스트 -->
                <tbody class="list">
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                <br>
                                <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                <tr>
                    <td>488</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                <div class="content">
                                    <span class="is-size-7">2018.04.23 - 2018.04.23 (1.0일)</span>
                                    <br>
                                    <span>주임 곽병준</span>
                                </div>
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">미결제</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="control has-text-centered">
                            <a href="#" class="button">연차 휴가계</a>
                        </div>
                    </td>
                    <td class="has-text-centered">2018.04.19</td>
                </tr>
                
                </tbody>
            </table>
        </div>
        <nav class="pagination" role="navigation" aria-label="pagination">
            <a class="pagination-previous">이전</a>
            <a class="pagination-next">다음</a>
        
            <ul class="pagination-list ">
                <li>
                    <a class="pagination-link" aria-label="Goto page 1">1</a>
                </li>
                <li>
                    <span class="pagination-ellipsis">&hellip;</span>
                </li>
                <li>
                    <a class="pagination-link" aria-label="Goto page 45">45</a>
                </li>
                <li>
                    <a class="pagination-link is-current" aria-label="Page 46" aria-current="page">46</a>
                </li>
                <li>
                    <a class="pagination-link" aria-label="Goto page 47">47</a>
                </li>
                <li>
                    <span class="pagination-ellipsis">&hellip;</span>
                </li>
                <li>
                    <a class="pagination-link" aria-label="Goto page 86">86</a>
                </li>
            </ul>
        </nav>
    </div>
</section>
<!-- 본문 끌 -->
<? include INC_PATH."/bottom.php"; ?>
</form>
</body>
</html>
