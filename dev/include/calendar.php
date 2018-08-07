<?
	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "팀장" : "";	//팀장대리 판단
	$p_year = isset($_POST['year']) ? $_POST['year'] : null; 
	$p_month = isset($_POST['month']) ? $_POST['month'] : null; 

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }
	
	$Start = $p_year.$p_month."01";
	$Pre = date("Ymd",strtotime ("-1 month", strtotime($Start)));
	$Next = date("Ymd",strtotime ("+1 month", strtotime($Start)));

	$PreYear = substr($Pre,0,4);
	$PreMonth = substr($Pre,4,2);

	$NextYear = substr($Next,0,4);
	$NextMonth = substr($Next,4,2);

	$date = $p_year."-".$p_month;

	//정상출근,지각,휴가,근무일수,반차,평균출근시,평균출근분,평균퇴근시,평균퇴근분,총근무시간
	$sql = "EXEC SP_COMMUTING_LIST_01 '$prs_id','$date'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$commute_count = $record['COMMUTE'];			//정상출근
		$biz_commute_count = $record['COMMUTE'];		//평일 정상출근
		$lateness_count = $record['LATENESS'];			//지각
		$vacation_count = $record['VACATION'];			//휴가
		$commute_day = $record['COMMUTE_DATE'];			//근무일수
		$subvacation1_count = $record['SUBVACATION1'];	//오전반차
		$subvacation2_count = $record['SUBVACATION2'];	//오후반차
		$avgtime1 = $record['AVGTIME1'];				//평균출근시
		$avgminute1 = $record['AVGMINUTE1'];			//평균출근분
		$avgtime2 = $record['AVGTIME2'];				//평균퇴근시
		$avgminute2 = $record['AVGMINUTE2'];			//평균퇴근분
		$total_time = $record['TOTAL_TIME'];			//총근무시간시
		$total_minute = $record['TOTAL_MINUTE'];		//총근무시간분
		$over_time = $record['OVER_TIME'];				//초과근무시간시 - 하루 9시간 이상 근무한 내역에 대한 월 총합시간
		$over_minute = $record['OVER_MINUTE'];			//초과근무시간분 - 하루 9시간 이상 근무한 내역에 대한 월 총합시간
		$over_day = $record['OVER_DATE'];				//초과근무일
		$edit_count = $record['EDIT_COUNT'];			//근태수정 요청수

		$subvacation_count = $subvacation1_count + $subvacation2_count;

		if ($avgtime1 == "") { $avgtime1 = "0"; }
		if ($avgminute1 == "") { $avgminute1 = "0"; }
		if ($avgtime2 == "") { $avgtime2 = "0"; }
		if ($avgminute2 == "") { $avgminute2 = "0"; }
		if ($total_time == "") { $total_time = "0"; }
		if ($total_minute == "") { $total_minute = "0"; }
		if ($over_time == "") { $over_time = "0"; }
		if ($over_minute == "") { $over_minute = "0"; }

		if (strlen($avgtime1) == 1) { $avgtime1 = "0".$avgtime1; }
		if (strlen($avgminute1) == 1) { $avgminute1 = "0".$avgminute1; }
		if (strlen($avgtime2) == 1) { $avgtime2 = "0".$avgtime2; }
		if (strlen($avgminute2) == 1) { $avgminute2 = "0".$avgminute2; }
		if (strlen($total_time) == 1) { $total_time = "0".$total_time; }
		if (strlen($total_minute) == 1) { $total_minute = "0".$total_minute; }
		if (strlen($over_time) == 1) { $over_time = "0".$over_time; }
		if (strlen($over_minute) == 1) { $over_minute = "0".$over_minute; }
	}
?>

<script type="text/javascript">		
	$( document ).ready( function() {                		        
    	 var cal_cnt = $( '.day_num' ).length;       
   		$('#cal_value').attr('value', cal_cnt);   		
	}); 		
	function sSubmit(f)
	{
		f.target="_self";
		f.action="<?=CURRENT_URL?>";
		f.submit();
	}
	//전월보기
	function preMonth()
	{
	<? if ($p_year == $startYear && $p_month == "01") { ?>
		alert("제일 처음입니다.");
	<? } else { ?>
		var frm = document.form;		
		frm.year.value = "<?=$PreYear?>";
		frm.month.value = "<?=$PreMonth?>";
		frm.submit();
	<? } ?>
	}
	//다음월보기
	function nextMonth()
	{
		var frm = document.form;
		frm.year.value = "<?=$NextYear?>";
		frm.month.value = "<?=$NextMonth?>";
		frm.submit();
	 }
</script>
  <article class="tile is-child card">
  	<div class="calendar main-calendar">
  		<!--달력상단-->
	  		<div class="calendar-nav">
	          <div class="calendar-nav-previous-month">
	              <a href="javascript:preMonth();"  class="button is-text is-small is-primary">
	                  <i class="fa fa-chevron-left"></i>
	              </a>
	          </div>
	          <input type="hidden" name="year">
	          <input type="hidden" name="month">
	          <div>
	          		<span class="title is-6 has-text-white"><?=$p_year?>년<?=$p_month?>월</span>
	          </div>
	          <div class="calendar-nav-next-month">
	              <a href="javascript:nextMonth();" class="button is-text is-small is-primary">
	                  <i class="fa fa-chevron-right"></i>
	              </a>
	          </div>
	      </div>
	    <!--달력상단-->
	    
	    <!-- 날짜 출력부분-->	      	
			<div class="calendar-container">				
				<div class="calendar-header">
          <div class="calendar-date">Sun</div>
          <div class="calendar-date">Mon</div>
          <div class="calendar-date">Tue</div>
          <div class="calendar-date">Wed</div>
          <div class="calendar-date">Thu</div>
          <div class="calendar-date">Fri</div>
          <div class="calendar-date">Sat</div>
         </div>									
           <div class="calendar-body">
          	<input type="hidden" id="cal_value" name="cal_value" value="">  	<!--마지막뒤 빈공간 채우기용 값-->
                <?
									$count = 0;
									$lastday = 0;									
																																																					
									//달력 데이터
									$sql = "EXEC SP_COMMUTING_LIST_03 '$prs_id','$date'";
									$rs = sqlsrv_query($dbConn,$sql);
					
									while ($record = sqlsrv_fetch_array($rs))
									{
										$count++;
					
										$col_date = $record['DATE'];								//날짜
										$col_datekind = $record['DATEKIND'];				//공휴일 여부
										$col_day = $record['DAY'];									//요일
										$col_date_name = $record['DATE_NAME'];			//기념일
										$col_gubun = $record['GUBUN'];							//출퇴근구분
										$col_gubun1 = $record['GUBUN1'];						//출근구분
										$col_gubun2 = $record['GUBUN2'];						//퇴근구분
										$col_checktime1 = $record['CHECKTIME1'];		//출근시간
										$col_checktime2 = $record['CHECKTIME2'];		//퇴근시간
										$col_totaltime = $record['TOTALTIME'];			//근무시간										
										
				
										//시작일 체크 (시작일 앞 빈공간 채움)
											if ($count == 1)
											{																	
												switch($col_day)
												{
													case "SUN" :
														$day_cnt = 0;
														break;
													case "MON" :
														$day_cnt = 1;
														break;
													case "TUE" :
														$day_cnt = 2;
														break;
													case "WED" :
														$day_cnt = 3;
														break;
													case "THU" :
														$day_cnt = 4;
														break;
													case "FRI" :
														$day_cnt = 5;
														break;
													case "SAT" :
														$day_cnt = 6;
														break;
												}

												for ($i=0; $i<$day_cnt; $i++)
												{
													echo "<div class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div>";
													$lastday++;																									 									   																																																					
												}																										
											}											
										
											if ($col_date == $nowYear.$nowMonth.$nowDay) //오늘 날짜 표시
											{
												echo "<div class='calendar-date tooltip' data-tooltip='Today'><span class='day_num'><button class='date-item is-today'>". $count ."</span></button></div>";
											}											
											else if($col_date_name != "") //공휴일 표시
											{
												echo "<div class='calendar-date tooltip' data-tooltip='" .$col_date_name ."'><button class='date-item' style='background:#eeeeee'><span class='day_num' style='color:#ff0000;'>". $count ."</span></button></div>";
											}
											
											else
											{
												
												if ($col_day == "SAT" || $col_day == "SUN")	//일요일
												{
												echo "<div class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>". $count ."<br>".$col_date_name ."</span></button></div>";
												}else
												{
												echo "<div class='calendar-date'><button class='date-item'><span class='day_num'>". $count ."<br>".$col_date_name ."</span></button></div>";
												}
											}																						
											
											//$lastday ++;
											
											//마지막날 체크
											if ($lastday == 8)
											{
												$lastday = 1;
											}																						
											
						}//배열 끝
						
						/* 마지막날짜 뒷공간 갯수 구하기 */						
							$total_day= $count + $lastday;      //앞 빈공간 갯수 + 달력날짜출력값
							$cal_day = 0; 											//달력영역의 총갯수 max 42
							$blank_day= 0;											//마지막뒤 빈공간 갯수	
																					
						if($total_day <= 35){																					
								$cal_day=35; 													//7x5
								$blank_day= $cal_day - $total_day;		
								for ($i = 0; $i < $blank_day; $i++)
								{
									echo "<div class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div>";
								}
						}
						else
						{																	
								$cal_day = 42; 												//7x6
								$blank_day= $cal_day - $total_day;		
								for ($i = 0; $i < $blank_day; $i++)
								{
									echo "<div class='calendar-date is-disabled'><span class='day_num'><button class='date-item'></span></button></div>";
								}																		
						}
				?>				
     </div>			
		</div>	
	</div>
</article>

