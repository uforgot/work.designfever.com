<?
	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "����" : "";	//����븮 �Ǵ�
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

	//�������,����,�ް�,�ٹ��ϼ�,����,�����ٽ�,�����ٺ�,�����ٽ�,�����ٺ�,�ѱٹ��ð�
	$sql = "EXEC SP_COMMUTING_LIST_01 '$prs_id','$date'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$commute_count = $record['COMMUTE'];			//�������
		$biz_commute_count = $record['COMMUTE'];		//���� �������
		$lateness_count = $record['LATENESS'];			//����
		$vacation_count = $record['VACATION'];			//�ް�
		$commute_day = $record['COMMUTE_DATE'];			//�ٹ��ϼ�
		$subvacation1_count = $record['SUBVACATION1'];	//��������
		$subvacation2_count = $record['SUBVACATION2'];	//���Ĺ���
		$avgtime1 = $record['AVGTIME1'];				//�����ٽ�
		$avgminute1 = $record['AVGMINUTE1'];			//�����ٺ�
		$avgtime2 = $record['AVGTIME2'];				//�����ٽ�
		$avgminute2 = $record['AVGMINUTE2'];			//�����ٺ�
		$total_time = $record['TOTAL_TIME'];			//�ѱٹ��ð���
		$total_minute = $record['TOTAL_MINUTE'];		//�ѱٹ��ð���
		$over_time = $record['OVER_TIME'];				//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
		$over_minute = $record['OVER_MINUTE'];			//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
		$over_day = $record['OVER_DATE'];				//�ʰ��ٹ���
		$edit_count = $record['EDIT_COUNT'];			//���¼��� ��û��

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
	//��������
	function preMonth()
	{
	<? if ($p_year == $startYear && $p_month == "01") { ?>
		alert("���� ó���Դϴ�.");
	<? } else { ?>
		var frm = document.form;		
		frm.year.value = "<?=$PreYear?>";
		frm.month.value = "<?=$PreMonth?>";
		frm.submit();
	<? } ?>
	}
	//����������
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
  		<!--�޷»��-->
	  		<div class="calendar-nav">
	          <div class="calendar-nav-previous-month">
	              <a href="javascript:preMonth();"  class="button is-text is-small is-primary">
	                  <i class="fa fa-chevron-left"></i>
	              </a>
	          </div>
	          <input type="hidden" name="year">
	          <input type="hidden" name="month">
	          <div>
	          		<span class="title is-6 has-text-white"><?=$p_year?>��<?=$p_month?>��</span>
	          </div>
	          <div class="calendar-nav-next-month">
	              <a href="javascript:nextMonth();" class="button is-text is-small is-primary">
	                  <i class="fa fa-chevron-right"></i>
	              </a>
	          </div>
	      </div>
	    <!--�޷»��-->
	    
	    <!-- ��¥ ��ºκ�-->	      	
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
          	<input type="hidden" id="cal_value" name="cal_value" value="">  	<!--�������� ����� ä���� ��-->
                <?
									$count = 0;
									$lastday = 0;									
																																																					
									//�޷� ������
									$sql = "EXEC SP_COMMUTING_LIST_03 '$prs_id','$date'";
									$rs = sqlsrv_query($dbConn,$sql);
					
									while ($record = sqlsrv_fetch_array($rs))
									{
										$count++;
					
										$col_date = $record['DATE'];								//��¥
										$col_datekind = $record['DATEKIND'];				//������ ����
										$col_day = $record['DAY'];									//����
										$col_date_name = $record['DATE_NAME'];			//�����
										$col_gubun = $record['GUBUN'];							//����ٱ���
										$col_gubun1 = $record['GUBUN1'];						//��ٱ���
										$col_gubun2 = $record['GUBUN2'];						//��ٱ���
										$col_checktime1 = $record['CHECKTIME1'];		//��ٽð�
										$col_checktime2 = $record['CHECKTIME2'];		//��ٽð�
										$col_totaltime = $record['TOTALTIME'];			//�ٹ��ð�										
										
				
										//������ üũ (������ �� ����� ä��)
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
										
											if ($col_date == $nowYear.$nowMonth.$nowDay) //���� ��¥ ǥ��
											{
												echo "<div class='calendar-date tooltip' data-tooltip='Today'><span class='day_num'><button class='date-item is-today'>". $count ."</span></button></div>";
											}											
											else if($col_date_name != "") //������ ǥ��
											{
												echo "<div class='calendar-date tooltip' data-tooltip='" .$col_date_name ."'><button class='date-item' style='background:#eeeeee'><span class='day_num' style='color:#ff0000;'>". $count ."</span></button></div>";
											}
											
											else
											{
												
												if ($col_day == "SAT" || $col_day == "SUN")	//�Ͽ���
												{
												echo "<div class='calendar-date'><button class='date-item'><span class='day_num' style='color:#ff0000;'>". $count ."<br>".$col_date_name ."</span></button></div>";
												}else
												{
												echo "<div class='calendar-date'><button class='date-item'><span class='day_num'>". $count ."<br>".$col_date_name ."</span></button></div>";
												}
											}																						
											
											//$lastday ++;
											
											//�������� üũ
											if ($lastday == 8)
											{
												$lastday = 1;
											}																						
											
						}//�迭 ��
						
						/* ��������¥ �ް��� ���� ���ϱ� */						
							$total_day= $count + $lastday;      //�� ����� ���� + �޷³�¥��°�
							$cal_day = 0; 											//�޷¿����� �Ѱ��� max 42
							$blank_day= 0;											//�������� ����� ����	
																					
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

