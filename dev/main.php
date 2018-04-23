<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/working_check.php";
	require_once CMN_PATH."/weekly_check.php";
?>

<? include INC_PATH."/top.php"; ?>
<!-- 기존에 백단 로직 checkout_check.php 에 추가-->
<?	require_once CMN_PATH."/checkout_check.php";	?>

<script type="text/javascript">

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('clock').innerHTML =
    h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i}; // 숫자가 10보다 작을 경우 앞에 0을 붙여줌
    return i;
}
	//출근 체크
	function go_office(){
		frm = document.form;
		frm.target	= "hdnFrame";
		frm.action = "commuting/commute_check_act.php";
		frm.submit();
		return;
	}

	//외출 체크
	function off_office(idx){
		frm = document.form;
		frm.target	= "hdnFrame";
		frm.action = "commuting/off_check_act.php?idx="+idx;
		frm.submit();
		return;
	}

	//메인게시물읽기
	function funView(seqno,type)
	{
		var goUrl;

		if (type == "default")
		{
			goUrl = "/board/board_detail.php?board=default&seqno="+seqno;
		}
		else
		{
			goUrl = "/book/book_detail.php?board="+ type + "&seqno="+seqno;
		}
		var frm = document.form;
		frm.target="_self";
		frm.action = goUrl;
		frm.submit();		
		
	}

	//업무보고서 작성
	function go_weekly(){
		document.location.href="/weekly/weekly_list.php";
	}

<? if (!in_array($prs_position,$positionS_arr)){ ?>
	$(document).ready(function(){
	<? if ($alert_state1 == ""){ ?>
		if ($.cookie('check_todayView1') == "close")
		{
			//$("#popAlert1").css("display","none");
			//$("#popAlert1").attr('class','modal');
		}
		else
		{
			//$("#popAlert1").css("display","");			
			$("#popAlert1").attr('class','modal is-active');
		}
	<? } else { ?>
			//$("#popAlert1").css("display","none");
			//$("#popAlert1").attr('class','modal');
	<? } ?>
	
	<? if ($alert_state2 == "inline"){ ?>
		if ($.cookie('check_todayView2') == "close")
		{
			//$("#popAlert2").css("display","none");
			//$("#popAlert2").attr('class','modal');
		}
		else
		{
			//$("#popAlert2").css("display","");
			$("#popAlert2").attr('class','modal is-active');
		}
	<? } else { ?>
			//$("#popAlert2").css("display","none");
			//$("#popAlert2").attr('class','modal');
	<? } ?>
	
	<? if ($alert_state3 == ""){ ?>
		if ($.cookie('check_todayView3') == "close")
		{
			//$("#popAlert3").css("display","none");
			//$("#popAlert1").attr('class','modal');
		}
		else
		{
			//$("#popAlert3").css("display","");
			$("#popAlert1").attr('class','modal is-active');
		}
	<? } else { ?>
			//$("#popAlert3").css("display","none");
			//$("#popAlert2").attr('class','modal');
	<? } ?>
	});
<? } ?>
</script>
    
<body onload="startTime()">
<form method="post" name="form">
<? include INC_PATH."/top_menu.php"; ?>
<!-- 본문 시작 -->
<section class="section is-main">
    <div class="container">
        <div class="columns">
    
            <!-- mobile 용 프로필 부분 시작 -->
            <div class="column is-hidden-tablet">
    
                <div class="card">
                    <div class="card-content">
                        <article class="media">
                            <figure class="media-left">
                                <p class="image is-128x128">
                                    <?=getProfileImg($prs_img);?>                                    
                                </p>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <span class="title is-4"><?=$prs_name?></span>
                                        <br>
                                        <span class="title is-6"><?=$prs_position2?></span>
                                        <br>
                                        <span class="subtitle is-7"><?=$prs_team?>&nbsp;/&nbsp;<?=$prs_position1?></span>
                                    </p>
                                </div>
                                <nav class="buttons has-addons main-mobile-profile-buttons">
                                        <a class="button is-small" href="/member/modify.php">
                                            정보수정
                                        </a>                                                                            
                                        <a class="button is-small" href="/mail/mail-sign-generator.php" target="_blank">
                                            서명생성
                                        </a>              
                                        <!--주간업무작성 출력-->                          
                                        <? if (in_array($prs_position,$positionB_arr) && $prs_login != 'dfadmin' && $prf_id != 7) { ?>																													
																					<?=getWeeklyBtn_M()?>              																		
																				<? } ?>	
                                </nav>
                            </div>
                        </article>
                    </div>
                  <!--모바일용 결제 리스트-->
                  <? if ($prf_id != 7) { ?>							
                      <div class="card-footer">
											<?
											$sql = "EXEC SP_MAIN_02 '$prs_id'";
											$rs = sqlsrv_query($dbConn,$sql);

											$record = sqlsrv_fetch_array($rs);
										if (sqlsrv_has_rows($rs) > 0)
										{
											$to_count = $record['TO_COUNT'];
											$my_count = $record['MY_COUNT'];
											$cc_count = $record['CC_COUNT'];
											$partner_count = $record['PARTNER_COUNT'];
										}				
										?>                    
											 <a href="/approval/approval_to_list.php" class="card-footer-item">                        
	                        	<span class="icon is-small is-hidden-tablet-only">
	                                <i class="fas fa-check"></i>
	                            </span>
	                            &nbsp;
	                            <span><?=$to_count?></span>                                                                                
	                   	 </a>
	                     <a href="/approval/approval_my_list.php" class="card-footer-item">                        
	                        	<span class="icon is-small is-hidden-tablet-only">
	                                <i class="fas fa-arrow-up"></i>
	                            </span>
	                            &nbsp;
	                            <span><?=$my_count?></span>                                                    
	                     </a>
	                     <a href="/approval/approval_cc_list.php" class="card-footer-item">                        
	                        	<span class="icon is-small is-hidden-tablet-only">
	                                <i class="fas fa-dot-circle"></i>
	                            </span>
	                            &nbsp;
	                            <span><?=$cc_count?></span>                                                    
	                     </a>					
	                  <? } ?>																
                    </div>
                </div>
            </div>
            <!-- mobile 용 프로필 부분 끝 -->
            
            <!-- pc tablet 용 프로필 부분 시작 -->
            <div class="column is-one-fifth is-hidden-mobile">
    
                <!-- pc tablet 용 프로필 -->
                <div class="wrapper">
                    <div class="card">
                        <div class="card-content">
                            <div class="image is-fullwidth is-hidden-mobile">                                
                                 <?=getProfileImg($prs_img);?>                                
                            </div>
                            <hr class="is-hidden-mobile"/>
                            <div class="content">
                                <p class="title  is-4"><span><?=$prs_name?></span> <span class="title is-5">/ <?=$prs_position2?></span></p>
                                <p class="subtitle is-7"><?=$prs_team?><br><?=$prs_position1?></p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a class="card-footer-item" href="/member/modify.php"><span>정보수정</span></a>                            
                            <a class="card-footer-item" href="/mail/mail-sign-generator.php"><span>서명생성</span></a>                           
                        </div>
                    </div>
                </div>
                
                
                <div class="wrapper">                          	
                	<!--주간업무작성 출력-->     	                										
             	    <? if (in_array($prs_position,$positionB_arr) && $prs_login != 'dfadmin' && $prf_id != 7) { ?>									
										<div class="field">
											<?=getWeeklyBtn()?>             	    		
    								</div>												
									<? } ?>																		
									
									<!-- pc tablet 용 결제 정보 -->                
                    <div class="field has-addons">
                    	<? if ($prf_id != 7) { 											
											$sql = "EXEC SP_MAIN_02 '$prs_id'";
											$rs = sqlsrv_query($dbConn,$sql);

											$record = sqlsrv_fetch_array($rs);
											if (sqlsrv_has_rows($rs) > 0)
											{
												$to_count = $record['TO_COUNT'];
												$my_count = $record['MY_COUNT'];
												$cc_count = $record['CC_COUNT'];
												$partner_count = $record['PARTNER_COUNT'];
											}				
											?> 
                        <div class="control is-expanded">
                            <a class="button is-fullwidth is-info" href="/approval/approval_to_list.php">
                                <span class="icon is-small is-hidden-tablet-only">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span><?=$to_count?></span>
                            </a>
                        </div>
                        <div class="control is-expanded">
                            <a class="button is-fullwidth" href="/approval/approval_my_list.php">
                                <span class="icon is-small is-hidden-tablet-only">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                                <span><?=$my_count?></span>
                            </a>
                        </div>
                        <div class="control is-expanded">
                            <a class="button is-fullwidth" href="/approval/approval_cc_list.php">
                            <span class="icon is-small is-hidden-tablet-only">
                                <i class="fas fa-dot-circle"></i>
                            </span>
                                <span><?=$cc_count?></span>
                            </a>
                        </div>
                       <?}?> 
                       
                    </div>                  
                </div>
                
                
                <!--이달의 생일자-->
               <? if ($prf_id != 7) { ?>
                <div class="wrapper">
                    <article class="card">
                        <header class="card-header">
                            <p class="card-header-title">
                                <span class="icon is-small"><i class="fas fa-birthday-cake"></i></span>
                                <span class="main-card-title">이달의 생일</span>
                            </p>
                        </header>
                        <div class="card-content field is-grouped is-grouped-multiline">
    								<?
												$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
												$rs = sqlsrv_query($dbConn,$sql);
				
												while($record=sqlsrv_fetch_array($rs))
												{
													$orderby .= "WHEN A.PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
												}
				
												$sql = "SELECT A.PRS_NAME, A.PRS_POSITION, A.PRS_BIRTH, A.PRS_BIRTH_TYPE, B.SOLAR_DATE, B.LUNAR_DATE, A.FILE_IMG
														FROM DF_PERSON A WITH(NOLOCK), LUNAR2SOLAR B WITH(NOLOCK)
														WHERE A.PRF_ID IN (1,2,3,4) AND A.PRS_ID NOT IN (102) AND B.SOLAR_DATE LIKE '". date("Y-m") ."%' 
															AND (
																(SUBSTRING(A.PRS_BIRTH,6,5) = SUBSTRING(B.SOLAR_DATE,6,5) AND A.PRS_BIRTH_TYPE = '양력') 
																OR (SUBSTRING(A.PRS_BIRTH,6,5) = SUBSTRING(B.LUNAR_DATE,6,5) AND A.PRS_BIRTH_TYPE = '음력'))
														ORDER BY B.SOLAR_DATE, CASE ". $orderby . " END, A.PRS_NAME";
												$rs = sqlsrv_query($dbConn, $sql);
				
												$pre_solar = "";
												while ($record = sqlsrv_fetch_array($rs))
												{
													$col_solar = $record['SOLAR_DATE'];
													$col_lunar = $record['LUNAR_DATE'];
													$col_prs_name = $record['PRS_NAME'];
													$col_prs_position = $record['PRS_POSITION'];
													$col_prs_birth = $record['PRS_BIRTH'];
													$col_prs_birth_type = $record['PRS_BIRTH_TYPE'];
													$col_file_img = $record['FILE_IMG'];
				
													echo " <div class='birthday-profile'>
																	<p class='image is-rounded-image'>
                                    ". getProfileImg($col_file_img) ."
                                	</p>";													

													if ($col_prs_birth_type == "음력")
													{	
														echo "<span class='has-text-centered'>". $col_prs_name ." ". str_replace("-",".",substr($col_solar,5,5)) ." (". str_replace("-",".",substr($col_prs_birth,5,5)).")</span>";																										
													}else{
														echo "<span class='has-text-centered'>". $col_prs_name ."  ". str_replace("-",".",substr($col_prs_birth,5,5))."</span>";																	
													}																											
													echo "</div>";								
													
													$pre_solar = $col_solar;
												}
											?>		
                        </div>
                    </article>
                  <?}?>
                </div>                
            </div>
            <!-- pc tablet 용 프로필 부분 끝 -->            
            <!-- 우측 컨텐츠 시작 -->
            <div class="column">
                <div class="tile is-ancestor">
                    <div class="tile is-vertical">
                        <div class="tile">
 													<input type="hidden" name="time_gubun" id="time_gubun" value="<?=$time_gubun?>"> 													
 													<!--근태출력부분-->
                            <div class="tile is-parent is-vertical">
                                <article class="tile is-child card">                                    
                                    <div class="main-clock-content">                                        	
                                        <div class="clock-area">
                                  <?
																		if (!in_array($prs_id,$NoCommuting_arr))
																		{
																			if ($today_gubun1 >= 10)
																			{
																	?>
																					<div class="title has-text-centered has-text-white clock-txt">                                            	
																							 <p class="clock-txt-now">
																							 	<span id="clock"></span>		<!--시계-->		
																							 </p>
																							 			<p class="clock-txt-start"> 휴가계를 제출하셨습니다.<br>출퇴근체크를 원하시면 휴가계 삭제를 요청해 주세요.</p>																							 										 																									 
                                          </div>																			
																	<?
																		}
																			else
																			{
																				if (in_array(REMOTE_IP, $ok_ip_arr))
																				{
																	?>
																						
                                            <div class="title has-text-centered has-text-white clock-txt">                                            	
																							 <p class="clock-txt-now">																								
																							 	<span id="clock"></span>		<!--시계-->																					 	
																							 </p>											
																							 												
																				 <!--시간 출력 부분 로직-->
																				 	<? if (in_array(REMOTE_IP, $ok_ip_arr)){
																				 		
																				 		//출근 시간
																						if ($time_gubun == "before")
																						{	
																							if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "")
																							{																								
																								echo " <p class='clock-txt-start'>출근 : ". substr($yesterday_checktime1,8,2) .":". substr($yesterday_checktime1,10,2) ."";																																																																																
																							}
																							else
																							{}
																						}
																						else
																						{
																							if ($today_checktime1 == "") 
																							{}
																							else{																								
																									echo " <p class='clock-txt-start'>출근 : ". substr($today_checktime1,8,2) .":". substr($today_checktime1,10,2) ."";																											
																									}
																						}
																																											
																						//퇴근 시간
																						if ($today_checktime2 != "")												//퇴근 중복체크
																						{
																							echo "&nbsp;&nbsp;퇴근 : ";
																							echo substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2);
																						}
																							else if ($today_checktime1 == "" && $yesterday_checktime1 == ""){}			//오늘 출근체크 X, 어제 출근체크 X																							
																							else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before"){}	//어제 퇴근체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전																							
																							else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before"){}	//어제 퇴근 중복체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전																							
																							else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after"){}	//오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이후																							
																							else if ($today_checktime1 != "" && $today_checktime2 == ""){}		//퇴근체크 - 오늘 출근체크 O, 오늘 퇴근체크 X																						
																							else{}															//오늘 출근체크 X, 오늘 퇴근체크 X
																							
																							//외출 시간 출력																					
																						if ($off_check == "Y")
																						{
																							if ($last_off_endtime == "") 
																							{
																								//echo "<a href='javascript:off_office(\"comeback\");'><span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>복귀</span></a> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ --:--</span></a><br/>";																								
																								//echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."";					                                             		
																								echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ."";					                                            																										
                                            		echo "</p>";
																							}
																							else
																							{
																								//echo "<a href='javascript:off_office(\"goout\");'><span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>외출</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span></a><br/>";																								
																								//echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ."";						
																								echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."";					                                             		
                                            		echo "</p>";
																							}
																						}
																						else
																						{
																							if ($today_checktime1 != "") 
																							{}
																							else
																							{
																								//echo "<span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>외출</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span><br/>";																																																
                                            		//echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."";					 
                                            		//echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ."";	
                                            		echo "</p>";                                           		
																							}
																						}	
																																																															 		
																							
																						// 파견지 확인용 ip주소 분기처리	
																				 		}else if (REMOTE_IP == "0" || REMOTE_IP == "0")
																						{																				
																							//출근
																							if ($time_gubun == "before")
																							{	
																								if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "")
																								{																									
																									echo " <p class='clock-txt-start'>출근 : ". substr($yesterday_checktime1,8,2) .":". substr($yesterday_checktime1,10,2) ."";																																																														
																								}
																								else
																								{}
																						}else{
																							if ($today_checktime1 == "") 
																							{}
																							else
																							{																																																
																								echo " <p class='clock-txt-start'>출근 : ". substr($today_checktime1,8,2) .":". substr($today_checktime1,10,2) ."";																											
																							}
																						}
																																											
																						//퇴근
																						if ($today_checktime2 != "")												//퇴근 중복체크
																						{
																							echo "&nbsp;퇴근 : ";
																							echo substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2);
																							echo "</p>";
																						}
																						else if ($today_checktime1 == "" && $yesterday_checktime1 == ""){}			//오늘 출근체크 X, 어제 출근체크 X																						
																						else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before"){}	//어제 퇴근체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전																					
																						else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before"){}	//어제 퇴근 중복체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전
																						else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after"){}	//오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이후																						
																						else if ($today_checktime1 != "" && $today_checktime2 == ""){}		//퇴근체크 - 오늘 출근체크 O, 오늘 퇴근체크 X																						
																						else{}															//오늘 출근체크 X, 오늘 퇴근체크 X																																											
																																												
																						//외출 시간 출력																					
																						if ($off_check == "Y")
																						{
																							if ($last_off_endtime == "") 
																							{
																								//echo "<a href='javascript:off_office(\"comeback\");'><span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>복귀</span></a> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ --:--</span></a><br/>";																								
                                            		echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."";					
                                            		echo "</p>";
																							}
																							else
																							{
																								//echo "<a href='javascript:off_office(\"goout\");'><span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>외출</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span></a><br/>";																								
                                            		echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."";					
                                            		echo "</p>";
																							}
																						}
																						else
																						{
																							if ($today_checktime1 != "") 
																							{}
																							else
																							{
																								//echo "<span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>외출</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span><br/>";																								
                                            		echo " <p class='clock-txt-start'>외출 : ". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."";					
                                            		//echo "</p>";
																							}
																						}
																						?>																						
																						
																					</div>
																				<?
																					}
																					else
																					{
																					?>
																						<p class="clock-txt-start">출퇴근 체크는 사내에서만 가능합니다.</p>																					
																				<?}?>																							 			
                                            </div>
                                        </div>																																																																								
																				<div class="field has-addons clock-buttons">    																	
																					<?
																					//출근 버튼 출력 
																						if ($time_gubun == "before")
																						{
																							if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "")
																							{																								
																								echo	"<div class='control is-expanded'>
                                                				<a href='javascript:go_office();' onClick='return !count++' class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>출근</div>
                                                				</a>
                                            					</div>"; 
																							}
																							else
																							{																								
																								echo	"<div class='control is-expanded'>
                                                				<a href='javascript:go_office();' onClick='return !count++' class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>출근</div>
                                                				</a>
                                            					</div>"; 
																							}
																						}
																						else
																						{
																							if ($today_checktime1 == "") 
																							{																							
																								echo	"<div class='control is-expanded'>
                                                				<a href='javascript:go_office();' onClick='return !count++' class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>출근</div>
                                                				</a>
                                            					</div>";
																							}
																							else
																							{}
																						}
																						
																						if ($off_check == "Y")
																						{
																							if ($last_off_endtime == "") 
																							{
																								$end_check = "N";
																							}
																							else
																							{
																								$end_check = "Y";
																							}
																						}
																						else
																						{
																							$end_check = "Y";
																						}
																																								
																						//퇴근 버튼 출력
																						/* 
																							출근체크가 안됐으면 일단 무조건 퇴근체크도 막아야 한다 하지만 
																								조건1. 전일 출근체크가 되어있고
																								조건2. 자정넘어 아침8시 이전이고
																								조건3. 자정넘어 출근체크가 안되어있고
																								조건4. 자정넘어 출근체크가 되어 있어도 휴가여야
																							이때는 퇴근체크가 가능하게끔 로직 설정
																						*/
																						$gbArray1 = array(4,8,6,10,11,12,13,14,15,16,17,18,19,20,21);
																						$gbArray2 = array(5,9);
														
																						$chk_gb1 = in_array($today_gubun1,$gbArray1);
																						$chk_gb2 = in_array($today_gubun2,$gbArray2);
																						if ($end_check == "Y")
																						{
																							if ($today_checktime2 != "")												//퇴근 중복체크
																							{																						
																								echo	"<div class='control is-expanded'>
                                                				<a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."'); class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>퇴근</div>
                                                				</a>
                                            					</div>";
																							}
																							else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//오늘 출근체크 X, 어제 출근체크 X
																							{																								
																							}
																							else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건2)
																							{																								
																								echo	"<div class='control is-expanded'>
                                                				<a href='javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');' class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>퇴근</div>
                                                				</a>
                                            					</div>";
																							}
																							else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건2)
																							{
																							
																								echo	"<div class='control is-expanded'>
                                                				<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>퇴근</div>
                                                				</a>
                                            					</div>";
																							}
																							else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이후
																							{																								
																							}
																							else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건4)
																							{																								
																								echo	"<div class='control is-expanded'>
                                                				<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>퇴근</div>
                                                				</a>
                                            					</div>";
																							}
																							else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건4)
																							{																								
																								echo	"<div class='control is-expanded'>
                                                				<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>퇴근</div>
                                                				</a>
                                            					</div>";
																							}
																							else if ($today_checktime1 != "" && $today_checktime2 == "")		//퇴근체크 - 오늘 출근체크 O, 오늘 퇴근체크 X
																							{																								
																								echo	"<div class='control is-expanded'>
                                                				<a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."'); class='button is-fullwidth is-info clock-button'>
                                                    			<div class='title is-5 has-text-white'>퇴근</div>
                                                				</a>
                                            					</div>";
																							}
																							else	//오늘 출근체크 X, 오늘 퇴근체크 X
																							{}
																						}
																						
																						
																						//외출 버튼 출력
																						if ($off_check == "Y")
																						{
																							if ($last_off_endtime == "") 
																							{
																								//echo "<a href='javascript:off_office(\"comeback\");'><span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>복귀</span></a> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ --:--</span></a><br/>";
																								echo	"<div class='control is-expanded'>
                                                				<a href='javascript:off_office(\"comeback\");' class='button is-fullwidth is-primary clock-button'>
                                                    			<div class='title is-5 has-text-white'>외출</div>
                                                				</a>
                                            					</div>";
																							}
																							else
																							{
																								//echo "<a href='javascript:off_office(\"goout\");'><span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>외출</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span></a><br/>";
																								echo	"<div class='control is-expanded'>
                                                				<a href='javascript:off_office(\"goout\");' class='button is-fullwidth is-primary clock-button'>
                                                    			<div class='title is-5 has-text-white'>외출</div>
                                                				</a>
                                            					</div>";
																							}
																						}
																						else
																						{
																							if ($today_checktime1 != "") 
																							{
																								//echo "<a href='javascript:off_office(\"goout\");'><span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>외출</span></a><br/>";
																								echo	"<div class='control is-expanded'>
                                                				<a href='javascript:off_office(\"goout\");' class='button is-fullwidth is-primary clock-button'>
                                                    			<div class='title is-5 has-text-white'>외출</div>
                                                				</a>
                                            					</div>";
																							}
																							else
																							{
																								//echo "<span style='margin:0; padding:8px 34px 10px 34px; border:2px solid #000; font-weight:bold; font-size:14px; color:#000; background:#fff;'>외출</span> <span>". substr($last_off_starttime,0,2) .":". substr($last_off_starttime,2,2) ." ~ ". substr($last_off_endtime,0,2) .":". substr($last_off_endtime,2,2) ."</span><br/>";
																								echo	"<div class='control is-expanded'>
                                                				<a class='button is-fullwidth is-static clock-button'>
                                                    			<div class='title is-5 has-text-black'>외출</div>
                                                				</a>
                                            					</div>";
																							}
																						}																																																																							
																																				
																					?>
																					</div>
																		<?	}else{?>																		
																				<div class="title has-text-centered has-text-white clock-txt">                                            	
																							 <p class="clock-txt-now">
																							 	<span id="clock"></span>		<!--시계-->		
																							 </p>
																						 			<p class="clock-txt-start"> 출퇴근 체크는 사내에서만 가능합니다.</p>																							 										 																									 
                                          </div>																			
																				<?}
																				}
																			}
																		?>			
																		</div>
																		</div>															
                                   </div>           											
                                </article>
                            </div>               
                
                						<!--달력-->   
                						          						
                            <div class="tile is-parent">                            	                            		
                            <? include INC_PATH."/calendar.php"; ?>                           
                            </div>				                                                        
                            																																
                        </div>
                        <!--공지사항 리스트-->
                        <div class="tile">
                            <div class="tile is-parent">
                                <article class="tile is-child card">
                                    <header class="card-header">
                                        <p class="card-header-title">
                                            <span class="icon is-small">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </span>
                                            <span class="main-card-title">공지사항</span>
                                        </p>
                                        <a href="board/board_list.php">
                                        <p class="card-header-icon">
                                            <span class="icon is-small">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                        </p>
                                    	  </a>
                                    </header>
                                    <div class="card-content">
                                        <table class="table is-fullwidth is-hoverable is-marginless">
                                            <colgroup>
                                                <col width="*">
                                                <col width="30%">
                                            </colgroup>
                                            <tbody>
                                            		<?
																									//공지사항 리스트
																									$sql = "SELECT TOP 5 
																												SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, HIT, REP_DEPTH, REG_DATE, FILE_1, FILE_2, FILE_3 
																											FROM 
																												DF_BOARD WITH (NOLOCK) 
																											WHERE 
																												TMP3 = 'default' AND NOTICE_YN = 'Y' 
																											ORDER BY 
																												SEQNO DESC";
																									$rs = sqlsrv_query($dbConn, $sql);
																		
																									while ($record=sqlsrv_fetch_array($rs))
																									{
																										$col_seqno = $record['SEQNO'];
																										$col_prs_id = $record['PRS_ID'];
																										$col_prs_name = trim($record['PRS_NAME']);
																										$col_prs_login = trim($record['PRS_LOGIN']);
																										$col_prs_team = trim($record['PRS_TEAM']);
																										$col_prs_position = trim($record['PRS_POSITION']);
																										$col_title = trim($record['TITLE']);
																										$col_hit = $record['HIT'];
																										$col_rep_depth = $record['REP_DEPTH'];
																										$col_reg_date = $record['REG_DATE'];
																										$col_file_1 = trim($record['FILE_1']);
																										$col_file_2 = trim($record['FILE_2']);
																										$col_file_3 = trim($record['FILE_3']);
																								?>				
                                            <tr>
                                                <td>                                                	
                                                    <a href="javascript:funView('<?=$col_seqno?>','default');" style="cursor:hand">
                                                       <?=getCutString($col_title,45);?>
                                                       <? if ($col_file_1 != "" || $col_file_2 != "" || $col_file_3 != "") { echo "<span class='icon is-small td-icon'><i class='fas fa-file'></i></span>"; } ?>                                                                              
																											 <? if ($col_rep_depth != "0") { echo "<span class='tag is-rounded td-tag'>". $col_rep_depth ."</span>"; } ?>
                                                    </a>
                                                </td>
                                                <td class="has-text-right"><?=$col_prs_position?> <?=$col_prs_name?></td>
                                            </tr>      
                                            <?}?>                                      
                                            </tbody>                                                                                                                                    																
                                        </table>
                                    </div>
                                </article>
                            </div>
                        </div>
                        
                        <!--게시판 리스트-->
                        <div class="tile">
                            <div class="tile is-parent">
                                <article class="tile is-child card">
                                    <header class="card-header">
                                        <p class="card-header-title">
                                            <span class="icon is-small">
                                                <i class="fas fa-plus-circle"></i>
                                            </span>
                                            <span class="main-card-title">최신소식</span>
                                        </p>
                                        <a href="book/book_list.php">
                                        <p class="card-header-icon">
                                            <span class="icon is-small">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                        </p>
                                      	</a>
                                    </header>
                                    <div class="card-content">
                                        <table class="table is-fullwidth is-hoverable is-marginless">
                                            <colgroup>
                                                <col width="*">
                                                <col width="30%">
                                            </colgroup>
                                            <tbody>
                                            	<?
																								//게시물 리스트
																								if ($prf_id == 7) 
																								{
																									$sql = "SELECT TOP 5 
																												SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, HIT, REP_DEPTH, REG_DATE, FILE_1, FILE_2, FILE_3, TMP3  
																											FROM 
																												DF_BOARD WITH (NOLOCK) 
																											WHERE 
																												TMP3 = 'default' AND NOTICE_YN NOT IN ('Y') 
																											ORDER BY 
																												SEQNO DESC";
																								}
																								else
																								{
																									$sql = "SELECT TOP 5 
																												SEQNO, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, TITLE, HIT, REP_DEPTH, REG_DATE, FILE_1, FILE_2, FILE_3, TMP3  
																											FROM 
																												DF_BOARD WITH (NOLOCK) 
																											WHERE 
																												TMP3 IN ('default','book','free','club') AND NOTICE_YN NOT IN ('Y') 
																											ORDER BY 
																												SEQNO DESC";
																								}
																								$rs = sqlsrv_query($dbConn, $sql);
																
																								while ($record=sqlsrv_fetch_array($rs))
																								{
																									$col_seqno = $record['SEQNO'];
																									$col_prs_id = $record['PRS_ID'];
																									$col_prs_name = trim($record['PRS_NAME']);
																									$col_prs_login = trim($record['PRS_LOGIN']);
																									$col_prs_team = trim($record['PRS_TEAM']);
																									$col_prs_position = trim($record['PRS_POSITION']);
																									$col_title = trim($record['TITLE']);
																									$col_hit = $record['HIT'];
																									$col_rep_depth = $record['REP_DEPTH'];
																									$col_reg_date = $record['REG_DATE'];
																									$col_file_1 = trim($record['FILE_1']);
																									$col_file_2 = trim($record['FILE_2']);
																									$col_file_3 = trim($record['FILE_3']);
																									$col_tmp3 = trim($record['TMP3']);
																							?>
                                            <tr>
                                                <td>                                                	                                                	
                                                   	<a href="javascript:funView('<?=$col_seqno?>','<?=$col_tmp3?>');" style="cursor:hand">
                                                        <?=getCutString($col_title,45);?>                                                      
                                                        <? if ($col_file_1 != "" || $col_file_2 != "" || $col_file_3 != "") { echo "<span class='icon is-small td-icon'><i class='fas fa-file'></i></span>"; } ?>                                                                              
																											  <? if ($col_rep_depth != "0") { echo "<span class='tag is-rounded td-tag'>". $col_rep_depth ."</span>"; } ?>
                                                    </a>
                                                </td>
                                                <td class="has-text-right"><?=$col_prs_position?> <?=$col_prs_name?></td>
                                            </tr>
                                            	<?
																								}
																							?>
                                            </tbody>
                                        </table>
                                    </div>
                                </article>
                            </div>
                        </div>                        
                        
               <? if ($prf_id != 7) { ?>
                 <div class="tile is-hidden-tablet">
                            <div class="tile is-parent">
                                <article class="tile is-child card">
        
                                <header class="card-header">
                                    <p class="card-header-title">
                                        <span class="icon is-small"><i class="fas fa-birthday-cake"></i></span>
                                        <span class="main-card-title">이달의 생일</span>
                                    </p>
                                </header>
                                <div class="card-content field is-grouped is-grouped-multiline">
    								<?
												$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
												$rs = sqlsrv_query($dbConn,$sql);
				
												while($record=sqlsrv_fetch_array($rs))
												{
													$orderby .= "WHEN A.PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
												}
				
												$sql = "SELECT A.PRS_NAME, A.PRS_POSITION, A.PRS_BIRTH, A.PRS_BIRTH_TYPE, B.SOLAR_DATE, B.LUNAR_DATE, A.FILE_IMG
														FROM DF_PERSON A WITH(NOLOCK), LUNAR2SOLAR B WITH(NOLOCK)
														WHERE A.PRF_ID IN (1,2,3,4) AND A.PRS_ID NOT IN (102) AND B.SOLAR_DATE LIKE '". date("Y-m") ."%' 
															AND (
																(SUBSTRING(A.PRS_BIRTH,6,5) = SUBSTRING(B.SOLAR_DATE,6,5) AND A.PRS_BIRTH_TYPE = '양력') 
																OR (SUBSTRING(A.PRS_BIRTH,6,5) = SUBSTRING(B.LUNAR_DATE,6,5) AND A.PRS_BIRTH_TYPE = '음력'))
														ORDER BY B.SOLAR_DATE, CASE ". $orderby . " END, A.PRS_NAME";
												$rs = sqlsrv_query($dbConn, $sql);
				
												$pre_solar = "";
												while ($record = sqlsrv_fetch_array($rs))
												{
													$col_solar = $record['SOLAR_DATE'];
													$col_lunar = $record['LUNAR_DATE'];
													$col_prs_name = $record['PRS_NAME'];
													$col_prs_position = $record['PRS_POSITION'];
													$col_prs_birth = $record['PRS_BIRTH'];
													$col_prs_birth_type = $record['PRS_BIRTH_TYPE'];
													$col_file_img = $record['FILE_IMG'];
				
													echo " <div class='birthday-profile mobile'>
																	<p class='image is-rounded-image'>
                                    ". getProfileImg($col_file_img) ."
                                	</p>";													

													if ($col_prs_birth_type == "음력")
													{	
														echo "<span class='has-text-centered'>". $col_prs_name ." ". str_replace("-",".",substr($col_solar,5,5)) ." (". str_replace("-",".",substr($col_prs_birth,5,5)).")</span>";																										
													}else{
														echo "<span class='has-text-centered'>". $col_prs_name ."  ". str_replace("-",".",substr($col_prs_birth,5,5))."</span>";																	
													}																											
													echo "</div>";						
												}
											?>		
                            </div>
                            </article>
                        	</div>
                        </div>
                  <?}?>                                                                       
                        
                    </div>
    
                </div>
            </div>
        </div>
        <!-- 우측 컨텐츠 끝 -->
        
    </div>
</section>
<!-- 본문 끌 -->
</body>
</form>
<? include INC_PATH."/bottom.php"; ?>


<!--new 팝업1-->
<div id="popAlert1" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">알림</p>
     <a href="javascript:HidePop('Alert1');"><button class="delete" aria-label="close"></button></a>
    </header>
    <section class="modal-card-body" style="text-align:center">
      <? if ($prf_id == 7) { ?>
			전 근무일 출근(퇴근)이 정상적으로 기록되지 않았습니다. <br>
		<? } else { ?>
			전 근무일 출근(퇴근)이 정상적으로 기록되지 않았습니다.<br> 관련하여 휴가 기안을 상신하거나 근태수정요청 게시판을 이용해 주세요.
		<? } ?>
    </section>
    <footer class="modal-card-foot">
      <a href="javascript:CheckPop('check_todayView1','commuting');"><button class="button is-success">확인</button></a>
      <input type="checkbox" id="check_todayView1" name="check_todayView1" style="vertical-align: middle;">
			<label for="check_todayView1" style="cursor:pointer;">&nbsp;오늘 하루 더 이상 보지 않기</label>
    </footer>
  </div>
</div>


<!--new 팝업2-->
<div id="popAlert2" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">알림</p>
      <a href="javascript:HidePop('Alert2');"><button class="delete" aria-label="close"></button></a>
    </header>
    <section class="modal-card-body">
     <? if ($prf_id == 7) { ?>
			전 근무일 근무 시간이 미달 되었습니다.
		<? } else { ?>
			전 근무일 근무 시간이 미달 되었습니다.<br>
			전자결재 메뉴에서 사유서를 작성해 주세요.
		<? } ?>
    </section>
    <footer class="modal-card-foot">
      <a href="javascript:CheckPop('check_todayView2','commuting');"><button class="button is-success">확인</button></a>
      <input type="checkbox" id="check_todayView2" name="check_todayView2" style="vertical-align: middle;">
			<label for="check_todayView2" style="cursor:pointer;">&nbsp;오늘 하루 더 이상 보지 않기</label>
    </footer>
  </div>
</div>

<!--new 팝업3-->
<div id="popAlert3" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">알림</p>
      <a href="javascript:HidePop('Alert3');" class="close"><button class="delete" aria-label="close"></button></a>
    </header>
    <section class="modal-card-body">
      <? if ($prf_id == 7) { ?>
			금일 출근 체크가 되지 않았습니다.
		<? } else { ?>
			금일 출근 체크가 되지 않았습니다.<br>
			근태수정요청 게시판을 이용해 주세요.
		<? } ?>
    </section>
    <footer class="modal-card-foot">
    <? if ($prf_id == 7) { ?>
     <a href="javascript:CheckPop('check_todayView3','commuting');"><button class="button is-success">확인</button></a>
    <? } else { ?> 
    	<a href="javascript:CheckPop('check_todayView3','edit');"><button class="button is-success">확인</button></a>
    <?}?>	
      <input type="checkbox" id="check_todayView3" name="check_todayView3" style="vertical-align: middle;">
			<label for="check_todayView3" style="cursor:pointer;">&nbsp;오늘 하루 더 이상 보지 않기</label>
    </footer>
  </div>
</div>


</html>