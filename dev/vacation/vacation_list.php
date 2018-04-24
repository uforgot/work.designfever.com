<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y"); 

	$sql = "EXEC SP_VACATION_LIST_01 '$prs_id','$year'";
	$rs = sqlsrv_query($dbConn, $sql);

	$record = sqlsrv_fetch_array($rs);
	$vacation_total = $record['VACATION_TOTAL'];
	$vacation1 = $record['VACATION1'];
	$vacation2 = $record['VACATION2'];
	$vacation3 = $record['VACATION3']/2;
	$nonvacation1 = $record['NONVACATION1'];
	$nonvacation2 = $record['NONVACATION2'];
	$nonvacation3 = $record['NONVACATION3']/2;
	$nonvacation4 = $record['NONVACATION4'];
	$nonvacation5 = $record['NONVACATION5'];

	$searchSQL = " WHERE (GUBUN1 IN (4,8,10,11,12,13,14,15,16,17,18,19,20,21) OR GUBUN2 IN (5,9)) AND CONVERT(char(4),DATE,102) = '$year' AND PRS_ID = '$prs_id'";

	$sql = "SELECT COUNT(DISTINCT SEQNO) FROM DF_CHECKTIME WITH(NOLOCK)". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT 
				T.SEQNO, T.DATE, T.GUBUN, T.GUBUN1, T.GUBUN2
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(ORDER BY DATE DESC) AS ROWNUM,
					SEQNO, DATE, GUBUN, GUBUN1, GUBUN2
				FROM 
					DF_CHECKTIME WITH(NOLOCK)
				$searchSQL
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>
<script src="/assets/js/vacation.js"></script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>		
<form name="form" method="post">
<input type="hidden" name="page" value="<?=$page?>">
<!-- 서브 네비게이션 시작 -->
<? include INC_PATH."/vacation_menu.php";?>
<!-- 본문 시작 -->
<section class="section is-resize">
    <div class="container">
        <div class="content">
            <div class="card">
                <div class="card-content">
                    <div class="content">
                        <div class="control select is-fullwidth">
                            <select name="year" onchange="javascript:funSearch(this.form);">
                              <? for ($i=2013; $i<=date("Y"); $i++) { ?>
																<option value="<?=$i?>"<? if ($i == $year) { echo " selected"; } ?>><?=$i?>년 휴가사용 현황</option>
															<? } ?>
                            </select>
                        </div>
                    </div>
    
                    <progress class="progress is-danger" value="19" max="100">19%</progress>
                </div>
                <div class="card-footer">
                    <div class="card-footer-item">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered">휴가 지급일수</div>
                            <div class="title is-size-4 has-text-centered">
                                <span class="has-text-info"><?=doubleval($vacation_total)?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer-item">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered">휴가 사용일</div>
                            <div class="title is-size-4 has-text-centered">
                                <span><? echo $vacation1+$vacation2+$vacation3; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer-item">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered">휴가 잔여일수</div>
                            <div class="title is-size-4 has-text-centered has-text-danger"><? echo $vacation_total - ($vacation1+$vacation2+$vacation3); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content">
            <div class="message">
                <div class="message-body">
                <div class="columns is-mobile is-multiline">
                    <div class="column is-one-quarter-mobile">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered is-vacation-title">
                                연차휴가
                            </div>
                            <div class="title is-size-6 has-text-centered"><?=$vacation1?></div>
                        </div>
                    </div>
                    <div class="column is-one-quarter-mobile">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered is-vacation-title">
                                병가
                            </div>
                            <div class="title is-size-6 has-text-centered"><?=$vacation2?></div>
                        </div>
                    </div>
                    <div class="column is-one-quarter-mobile">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered is-vacation-title">
                                반차
                            </div>
                            <div class="title is-size-6 has-text-centered"><?=$vacation3?></div>
                        </div>
                    </div>
                    <div class="column is-one-quarter-mobile">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered is-vacation-title">
                                리프레쉬
                            </div>
                            <div class="title is-size-6 has-text-centered"><?=$nonvacation1?></div>
                        </div>
                    </div>
                    <div class="column is-one-quarter-mobile m">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered is-vacation-title">
                                프로젝트
                            </div>
                            <div class="title is-size-6 has-text-centered"><?=$nonvacation2+$nonvacation3?></div>
                        </div>
                    </div>
                    <div class="column is-one-quarter-mobile">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered is-vacation-title">
                                경조사
                            </div>
                            <div class="title is-size-6 has-text-centered"><?=$nonvacation4?></div>
                        </div>
                    </div>
                    <div class="column is-one-quarter-mobile">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered is-vacation-title">
                                기타
                            </div>
                            <div class="title is-size-6 has-text-centered"><?=$nonvacation5?></div>
                        </div>
                    </div>
                    <div class="column is-one-quarter-mobile" style="margin:auto">
                        <div class="content has-text-centered">
                            <span class="tag is-large is-rounded is-danger"><? echo $vacation1+$vacation2+$vacation3+$nonvacation1+$nonvacation2+$nonvacation3+$nonvacation4+$nonvacation5?></span>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        
        <div class="content">
            <table class="table is-fullwidth is-hoverable">
                <colgroup>
                    <col width="8%">
                    <col width="*">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <thead>
                <tr>
                    <th><span class="is-hidden-mobile">No.</span></th>
                    <th>휴가일</th>
                    <th class="has-text-centered">종류</th>
                    <th class="has-text-centered">휴가계 작성여부</th>
                    <th class="has-text-centered">휴가계 승인여부</th>
                </tr>
                </thead>
                <!-- 일반 리스트 -->
                <tbody class="list">
                	<?
										$i = $total_cnt-($page-1)*$per_page;
										if ($i==0) 
										{
									?>
								<tr>
									<td colspan="5" style="has-text-centered">등록된 휴가계가 없습니다.</td>
								</tr>
								
								<!--                                
                <tr>
                    <td>3</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                2018-03-15
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button">연차휴가계</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">오전반차</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="button">연차휴가계</div>
                    </td>
                    <td class="has-text-centered">
                        결제
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                2018-03-15
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button is-static">자동반차</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">오전반차</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="button is-static">자동반차</div>
                    </td>
                    <td class="has-text-centered">-</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>
                        <div class="level is-mobile">
                            <div class="level-left">
                                2018-03-15
                            </div>
                            <div class="level-right is-hidden-tablet">
                                <div class="button is-static">자동반차</div>
                            </div>
                        </div>
                    </td>
                    <td class="has-text-centered">오전반차</td>
                    <td class="has-text-centered is-hidden-mobile">
                        <div class="button is-static">자동반차</div>
                    </td>
                    <td class="has-text-centered">-</td>
                </tr>
                -->
            <?
									}
									else
									{
										while ($record = sqlsrv_fetch_array($rs))
										{
											$seqno = $record['SEQNO'];
											$date = $record['DATE'];
											$gubun = $record['GUBUN'];
											$gubun1 = $record['GUBUN1'];
											$gubun2 = $record['GUBUN2'];
								
											$sql1 = "SELECT
														DOC_NO, TITLE, CONVERT(char(10),REG_DATE,102) AS REG_DATE, PRS_TEAM, PRS_POSITION, PRS_NAME, 
														START_DATE, END_DATE, USE_DAY, STATUS, FORM_CATEGORY, FORM_TITLE
													FROM 
														DF_APPROVAL WITH(NOLOCK)
													WHERE
														PRS_ID = '$prs_id' AND '$date' BETWEEN START_DATE AND END_DATE AND USE_YN = 'Y' AND FORM_CATEGORY = '휴가계'
													ORDER BY 
														SEQNO DESC";
											$rs1 = sqlsrv_query($dbConn,$sql1);
								
											if (sqlsrv_has_rows($rs1) > 0)
											{
												$j = 0;
												while ($record1 = sqlsrv_fetch_array($rs1))
												{
													$doc = "Y";
													$doc_no = $record1['DOC_NO'];
													$form_category = $record1['FORM_CATEGORY'];
								
													if ($j == 0) {
														$form_title = $record1['FORM_TITLE'];
														$approval = "<a class='button' href=\"javascript:funView('". $doc_no ."');\">". $record1['FORM_TITLE'] ." 휴가계</a>";
													} else {
														$form_title .= "/". $record1['FORM_TITLE'];
														$approval .= "/<a class='button' href=\"javascript:funView('". $doc_no ."');\">". $record1['FORM_TITLE'] ." 휴가계</a>";
													}
													$title = $record1['TITLE'];
													$reg_date = $record1['REG_DATE'];
													$team = $record1['PRS_TEAM'];
													$position = $record1['PRS_POSITION'];
													$name = $record1['PRS_NAME'];
													$start_date = $record1['START_DATE'];
													$end_date = $record1['END_DATE'];
													$use_day = $record1['USE_DAY'];
													if ($j == 0) {
														$status = $record1['STATUS'];
													} else {
														$status .= "/". $record1['STATUS'];
													}
								
													$j++;
												}
								
												if (($gubun2 == "5" || $gubun2 == "9") && $gubun1 == "8")
												{
													$form_title = "자동반차/". $form_title;
												}
											}
											else
											{
												$doc = "N";
												$doc_no = "";
												$form_category = "";
												$form_title = "";
												$title = "";
												$reg_date = "";
												$team = "";
												$position = "";
												$name = "";
												$start_date = "";
												$end_date = "";
												$use_day = "";
												$status = "";
								
												if ($gubun2 == "9") 
												{ 
													$form_title = "오후반차"; 
												}
												else if ($gubun2 == "5")
												{
													$form_title = "프로젝트 오후반차";
												}
												else
												{
													if ($gubun1 == "4") { $form_title = "프로젝트 오전반차"; }
													else if ($gubun1 == "8") { $form_title = "오전반차"; }
													else if ($gubun1 == "10") { $form_title = "연차"; }
													else if ($gubun1 == "11") { $form_title = "병가"; }
													else if ($gubun1 == "12") { $form_title = "경조사"; }
													else if ($gubun1 == "13") { $form_title = "기타"; }
													else if ($gubun1 == "14") { $form_title = "결근"; }
													else if ($gubun1 == "15") { $form_title = "교육/훈련"; }
													else if ($gubun1 == "16") { $form_title = "프로젝트"; }
													else if ($gubun1 == "17") { $form_title = "리프레시"; }
													else if ($gubun1 == "18") { $form_title = "무급"; }
													else if ($gubun1 == "19") { $form_title = "예비군"; }
												}
								
												if ($gubun1 == "8") { $approval = "<a class='button is-static' href=\"javascript:funView('". $doc_no ."');\">". $record1['FORM_TITLE'] ." 자동반차</a>"; } else { $approval = "미제출"; }																							 
											}
								?>																																									
			                <tr>
           								 <td><?=$i?></td>
           								 <td>
                        	 <div class="level is-mobile">
	                            <div class="level-left">
	                                <?=$date?>
	                            </div>
	                            <div class="level-right is-hidden-tablet">
	                                <div class="button is-static"><?=$approval?></div>
	                            </div>
                        	 </div>
			                    </td>
			                    <td class="has-text-centered"><?=$form_title?></td>
			                    <td class="has-text-centered is-hidden-mobile">
			                        <!--<div class="button is-static"><?=$approval?></div>-->
			                        <?=$approval?>
			                    </td>
			                    <td class="has-text-centered"><?=$status?></td>
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
    </div>
</section>
<? include INC_PATH."/bottom.php"; ?>
</form>
</body>
</html>
