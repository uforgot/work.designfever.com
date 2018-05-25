<script type="text/javascript">
//퇴근 체크
	function leave_office(gubun,commute,working){

		if (gubun == 1)
		{
			var msg = "퇴근체크를 하시겠습니까?";
		}
		else if (gubun == 2)
		{
			var msg = "이미 퇴근체크를 하셨습니다. 퇴근체크를 하시겠습니까?";
		}
		else if (gubun == 3)
		{
			var msg = "전일출근후 퇴근체크가 되어있지 않습니다. \n\퇴근체크를 하시겠습니까? \n\(퇴근시간은 전일 퇴근시간에반영됩니다.";
		}
		else if (gubun == 4)
		{
			var msg = "이미 전일 퇴근체크를 하셨습니다.  \n\퇴근체크를 하시겠습니까? \n\(퇴근시간은 전일 퇴근시간에 반영됩니다.";
		}

		if(confirm(msg)){
			frm = document.form;
			frm.target	= "hdnFrame";
			frm.action = "/commuting/commute_check_act2.php";
			frm.submit();
		}else{
			return;
		}
	}
</script>
<div class="top">

 <nav id="navbar" class="navbar has-shadow is-fixed-top">
    <div class="container">
        <div class="navbar-brand">
            <div class="navbar-item is-size-7">
               <?=$prs_team?>&nbsp;/&nbsp;<strong><?=$prs_position2 ?> <?=$prs_name ?></strong>
           </div>
            <div class="navbar-burger burger" data-target="navbarExampleTransparentExample">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item is-size-7">
                    <? if ($today_gubun1 >= 10){?>
                        휴가계를 제출하셨습니다.<br>출퇴근체크를 원하시면 휴가계 삭제를 요청해 주세요.
                      <? }else if ($today_checktime1 != "" && $today_datekind == "BIZ") { ?>
                            퇴근 가능 시간은&nbsp;<strong><?=substr($checkout,0,2)?>:<?=substr($checkout,2,2)?> </strong>&nbsp;입니다.
                      <? }else{}

                       ?>
                 </div>
                <div class="navbar-item">
                    <div class="field is-grouped">
                    	<!--퇴근 하기 버튼 출력부분-->
                    	<?
                    if ($off_check == "Y")
                        {
                            if ($last_off_endtime == "")
                            { $end_check = "N"; }
                            else
                            { $end_check = "Y"; }
                        }
                        else{ $end_check = "Y"; }
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

                                if ($today_gubun1 >= 10){
                                 ?>
                                    <!--휴가일경우 버튼 막음-->
                                <? }else if ($today_checktime2 != "")												//퇴근 중복체크
                                {

                                    echo "<p class='control'>
                                            <a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                <span>퇴근하기</span>
                                            </a>
                                          </p>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건2)
                                {
                                    echo "<p class='control'>
                                                <a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                    <span>퇴근하기</span>
                                                </a>
                                        </p>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건2)
                                {
                                    echo "<p class='control'>
                                                <a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                    <span>퇴근하기</span>
                                                </a>
                                            </p>";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건4)
                                {
                                    echo "<p class='control'>
                                                <a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                    <span>퇴근하기</span>
                                                </a>
                                            </p>";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건4)
                                {
                                    echo "<p class='control'>
                                            <a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');class='button'>
                                                <span>퇴근하기</span>
                                            </a>
                                        </p>";
                                }
                                else if ($today_checktime1 != "" && $today_checktime2 == "")		//퇴근체크 - 오늘 출근체크 O, 오늘 퇴근체크 X
                                {
                                    echo "<p class='control'>
                                             <a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."'); class='button' >
                                                 <span>퇴근하기</span>
                                             </a>
                                        </p>";
                                }
                            }else if ($today_gubun1 >= 10)
                            {
                                echo "<p class='control'>                                                                                          
                                        </p>";
                            }
                    ?>

                        <p class="control">
                            <a class="button is-primary" href="javascript:logout();">
                                <span>로그아웃</span>
                             </a>
                        </p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </nav>




    <section class="section-fixed-margin"></section>

    <section class="hero is-link is-hidden-mobile">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-vcentered">
                    <div class="column">
                        <a href="/main.php"><img src="/assets/images/df_logo_w.svg" width="120"></a>
                    </div>
                    <div class="column is-narrow">
                        <span class="is-size-6 is-italic">a difference that matters.</span>
                    </div>
                </div>

            </div>
        </div>
        <div class="hero-foot">
            <div class="container">
                <nav class="tabs is-boxed is-fullwidth">
                     <ul>
	                   <?	if ($prf_id == 7) {	?>
											<li<? if (substr(CURRENT_URL,0,5) == "/main") { ?> class="is-active"<? } ?>><a href="/main.php">홈</a></li>
											<li<? if (substr(CURRENT_URL,0,10) == "/commuting") { ?> class="is-active"<? } ?>><a href="/commuting/commuting_list.php">근태현황</a></li>
											<li<? if (substr(CURRENT_URL,0,5) == "/book") { ?> class="is-active"<? } ?>><a href="/book/book_list.php?board=edit">근태수정요청</a></li>
											<li<? if (substr(CURRENT_URL,0,9) == "/approval") { ?> class="is-active"<? } ?>><a href="/approval/approval_my_list.php">전자결재</a></li>
											<li<? if (substr(CURRENT_URL,0,6) == "/board") { ?> class="is-active"<? } ?>><a href="/board/board_list.php">공지사항</a></li>
										<?	} else {	?>
											<li<? if (substr(CURRENT_URL,0,5) == "/main") { ?> class="is-active"<? } ?>><a href="/main.php">홈</a></li>
											<li<? if (substr(CURRENT_URL,0,10) == "/commuting") { ?> class="is-active"<? } ?>><a href="/commuting/commuting_list.php">근태</a></li>
											<li<? if (substr(CURRENT_URL,0,9) == "/vacation") { ?> class="is-active"<? } ?>><a href="/vacation/vacation_list.php">휴가</a></li>
											<li<? if (substr(CURRENT_URL,0,9) == "/approval") { ?> class="is-active"<? } ?>><a href="/approval/approval_my_list.php">전자결재</a></li>
											<li<? if (substr(CURRENT_URL,0,8) == "/project") { ?> class="is-active"<? } ?>><a href="/project/project_list.php">프로젝트</a></li>
											<!-- 2014.09.17 주간보고서 추가-->
											<li<? if (substr(CURRENT_URL,0,7) == "/weekly") { ?> class="is-active"<? } ?>><a href="/weekly/weekly_list.php">주간보고서</a></li>
											<!-- 2014.09.17 주간보고서 끝-->
											<li<? if (substr(CURRENT_URL,0,6) == "/board") { ?> class="is-active"<? } ?>><a href="/board/board_list.php">공지사항</a></li>
											<li<? if (substr(CURRENT_URL,0,6) == "/book/") { ?> class="is-active"<? } ?>><a href="/book/book_list.php">게시판</a></li>
											<li<? if (substr(CURRENT_URL,0,8) == "/booking" || substr(CURRENT_URL,0,6) == "/visit") { ?> class="is-active"<? } ?>><a href="/booking/booking_list.php">예약</a></li>
											<li<? if (substr(CURRENT_URL,0,5) == "/org_") { ?> class="is-active"<? } ?>><a href="/org_chart/person_list.php">조직도</a></li>
										<?	}	?>
                	  </ul>
                </nav>
         </div>
        </div>
    </section>

    <section class="hero is-link is-hidden-tablet">
        <div class="hero-body">
            <div class="container">
                <div class="level is-mobile">
                    <div class="level-item level-left">
                        <a href="/main.php"><img src="/assets/images/df_logo_w.svg" width="50" style="vertical-align:middle;"></a>
                    </div>
                    <div class="level-item level-right is-expanded">
                        <span class="is-italic has-text-right is-fullwidth">a difference that matters.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</div>