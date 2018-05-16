<?
require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
require_once CMN_PATH."/login_check.php";
require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang

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

    $div_class1 =""; //오늘
    $div_class2 =""; //공휴일
    $div_class3 =""; //휴가
    $div_class4 =""; //마지막날

    $mark1 ="";
    $mark2 ="";
    $mark3 ="";
    $mark4 ="";
    $mark5 ="";
    $mark6 ="";
    $mark7 ="";
    $mark8 ="";
    $mark9 ="";
    $mark10 ="";

    $calendar_events1 =""; //출근시간
    $calendar_events2 =""; //퇴근시간
    $calendar_events3 ="";
    $calendar_events4 ="";
    $calendar_events5 ="";
    $calendar_events6 ="";
    $calendar_events7 ="";
    $calendar_events8 ="";
    $calendar_events9 ="";
    $calendar_events10 ="";

    $icon1="";
    $icon2="";
    $icon3="";

}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
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
    //근태수정팝업 띄우기
    function ShowPopCustom(id,pid,y,m,d,mode)
    {
        var date = y+"-"+m+"-"+d;
        $("#t_month").text(m);
        $("#t_day").text(d);

        $("#popDayEditFrm").attr("src","commuting_request_pop.php?mode="+mode+"&id="+pid+"&date="+date);
        $("#pop"+id).attr("style","display:inline");
    }
</script>
</head>

<body>
<form method="post" name="form">
    <? include INC_PATH."/top_menu.php"; ?>
    <? include INC_PATH."/commuting_menu.php"; ?>

    <!-- 본문 시작 -->
    <section class="section df-commuting">
        <div class="container">
            <div class="content">
                <div class="calendar is-large">
                    <div class="calendar-nav">
                        <div class="calendar-nav-previous-month">
                            <a href="javascript:preMonth();" class="button is-text is-small is-primary">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                        </div>
                        <div><span class="title is-6 has-text-white">
                          <div class="control select">
                            <select name="year" value="<?=$p_year?>" onchange='sSubmit(this.form)'>
                                    <?
                                    for ($i=$startYear; $i<=($nowYear+1); $i++)
                                    {
                                        if ($i == $p_year)
                                        { $selected = " selected"; }
                                        else
                                        { $selected = ""; }
                                        echo "<option value='".$i."'".$selected.">".$i."년</option>";
                                    }
                                    ?>
                             </select>
                           </div>
                           <div class="control select">
                               <select name="month" value="<?=$p_month?>" onchange='sSubmit(this.form)'>
                                        <?
                                        for ($i=1; $i<=12; $i++)
                                        {
                                            if (strlen($i) == "1")
                                            { $j = "0".$i; }
                                            else
                                            { $j = $i; }
                                            if ($j == $p_month)
                                            { $selected = " selected"; }
                                            else
                                            { $selected = ""; }
                                            echo "<option value='".$j."'".$selected.">".$i."월</option>";
                                        }
                                        ?>
                                </select>
                           </div>
                            </span></div>
                        <div class="calendar-nav-next-month">
                            <a href="javascript:nextMonth();" class="button is-text is-small is-primary">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="box">
                    <div class="columns is-mobile is-multiline">
                        <div class="column is-one-quarter-mobile">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered is-vacation-title">
                                    정상출근
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=$biz_commute_count?></div>
                            </div>
                        </div>
                        <div class="column is-one-quarter-mobile">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered is-vacation-title">
                                    휴가
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=$vacation_count + ($subvacation_count * 0.5) ?></div>
                            </div>
                        </div>
                        <div class="column is-one-quarter-mobile">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered is-vacation-title">
                                    근무일수
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=$commute_day?></div>
                            </div>
                        </div>
                        <div class="column is-one-quarter-mobile">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered is-vacation-title">
                                    수정요청
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=number_format($edit_count)?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content is-hidden-mobile">
                <div class="field is-grouped is-grouped-multiline">
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag">총 근무시간</span>
                            <span class="tag is-primary"><?=$total_time?> : <?=$total_minute?></span>
                        </div>
                    </div>
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag">평균 출근시간</span>
                            <span class="tag is-primary"><?=$avgtime1?> : <?=$avgminute1?></span>
                        </div>
                    </div>
                    <div class="control">

                        <div class="tags has-addons">
                            <span class="tag">평균 퇴근시간</span>
                            <span class="tag is-primary"><?=$avgtime2?> : <?=$avgminute2?></span>
                        </div>
                    </div>
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag">평균 근무시간</span>
                            <span class="tag is-primary">
                                <?
                                if ($avgtime1 == "00" && $avgminute1 == "00" && $avgtime2 == "00" && $avgminute2 == "00")
                                {
                                    echo "00 : 00";
                                }
                                else
                                {
                                    $atime1 = mktime($avgtime1,$avgminute1,"00");
                                    $atime2 = mktime($avgtime2,$avgminute2,"00");

                                    $avg_sec = abs($atime2-$atime1);

                                    $avg1 = intval($avg_sec/3600);
                                    if (strlen($avg1) == "1") { $avg1 = "0".$avg1; }
                                    $avg2 = ($avg_sec%3600) / 60;
                                    if (strlen($avg2) == "1") { $avg2 = "0".$avg2; }

                                    echo $avg1." : ".$avg2;
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag">초과 근무시간</span>
                            <span class="tag is-primary"><?=$over_time?> : <?=$over_minute?></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 달력 출력-->
            <div class="content">
                <div class="calendar is-large">
                    <div class="calendar-container">
                        <div class="calendar-header is-hidden-mobile">
                            <div class="calendar-date">SUN</div>
                            <div class="calendar-date">MON</div>
                            <div class="calendar-date">TUE</div>
                            <div class="calendar-date">WED</div>
                            <div class="calendar-date">THU</div>
                            <div class="calendar-date">FRI</div>
                            <div class="calendar-date">SAT</div>
                        </div>
                        <div class="calendar-body">
                        <?
                            $end_day = date("t", mktime(0, 0, 0, $p_month, 1, $p_year));//해당월의 마지막 날짜 구하기

                            $count = 0;
                            $lastday = 0;

                            $pre_checktime = "";	//어제 출근시간을 위한 처리
                            $pre_gubun2 = "";		//어제 출근_구분을 위한 처리
                            $pre_checktime_c2 = "";	//어제 퇴근시간을 위한 처리
                            $col_pre_gubun2_c2 = "";	//어제 퇴근_구분을 위한 처리

                            $worktime1 = "";	//출근시간
                            $worktime2 = "";	//퇴근시간

                            $day_cnt = 0;


                            //달력 데이터
                            $sql = "EXEC SP_COMMUTING_LIST_03 '$prs_id','$date'";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $count++;

                                $col_date = $record['DATE'];								//날짜
                                $col_datekind = $record['DATEKIND'];				        //공휴일 여부
                                $col_day = $record['DAY'];									//요일
                                $col_date_name = $record['DATE_NAME'];			            //기념일
                                $col_gubun = $record['GUBUN'];							    //출퇴근구분
                                $col_gubun1 = $record['GUBUN1'];						    //출근구분
                                $col_gubun2 = $record['GUBUN2'];						    //퇴근구분
                                $col_checktime1 = $record['CHECKTIME1'];		            //출근시간
                                $col_checktime2 = $record['CHECKTIME2'];		            //퇴근시간
                                $col_totaltime = $record['TOTALTIME'];			            //근무시간
                                $col_overtime = $record['OVERTIME'];			            //초과근무
                                $col_undertime = $record['UNDERTIME'];			            //미만근무
                                $col_pay1 = $record['PAY1'];					            //점심식비
                                $col_pay2 = $record['PAY2'];					            //저녁식비
                                $col_pay3 = $record['PAY3'];					            //간식비
                                $col_pay4 = $record['PAY4'];					            //야근교통비
                                $col_pay5 = $record['PAY5'];					            //파견교통비(출근)
                                $col_pay6 = $record['PAY6'];					            //파견교통비(퇴근)
                                $col_out = $record['OUT_CHK'];					            //파견여부
                                $col_off_time = $record['OFF_TIME'];			            //외출시간시
                                $col_off_minute = $record['OFF_MINUTE'];		            //외출시간분
                                $col_yesterday_overtime = $record['YESTERDAY_OVERTIME'];	//전일 연장근무시간
                                $col_yesterday_datekind = $record['YESTERDAY_DATEKIND'];	//전일 근무일 구분

                                $col_edit_status = $record['EDIT_STATUS'];		//수정요청상태

                                if (strlen($col_off_time) == 1) { $col_off_time = "0".$col_off_time; }
                                if (strlen($col_off_minute) == 1) { $col_off_minute = "0".$col_off_minute; }

                                $checktime1 = substr($col_checktime1,8,2) .":". substr($col_checktime1,10,2);
                                if ($checktime1 == ":") { $checktime1 = ""; }

                                if (strlen($count) == 1)
                                { $replace_count = "0".$count; }
                                else
                                { $replace_count = $count; }

                                if (strlen($p_month) == 1)
                                { $replace_Month = "0".$nowMonth; }
                                else
                                { $replace_Month = $nowMonth; }

                                $dt1 = new DateTime($col_date);
                                $dt2 = new DateTime($nowYear.$nowMonth.$nowDay);
                                $interval = $dt1->diff($dt2);

                                //시작날짜 앞 빈공간 체크 (
                                if ($count == 1)
                                {
                                    switch($col_day)
                                    {
                                        case "SUN" : $day_cnt = 0; break;
                                        case "MON" : $day_cnt = 1; break;
                                        case "TUE" : $day_cnt = 2; break;
                                        case "WED" : $day_cnt = 3; break;
                                        case "THU" : $day_cnt = 4; break;
                                        case "FRI" : $day_cnt = 5; break;
                                        case "SAT" : $day_cnt = 6; break;
                                    }
                                    for ($i=0; $i<$day_cnt; $i++)
                                    {
                                        echo "<div class='calendar-date is-disabled'><div class='date'></div></div>";
                                        $lastday++;
                                    }
                                }

                            if ($col_date == $nowYear.$nowMonth.$nowDay)  { //오늘 날짜 표시
                                $div_class1=" is-today";
                            }else if($col_date_name != "") { //공휴일 표시
                                $div_class2=" is-holiday";
                                $div_class3="";
                                $mark1 ="<span class='button is-small is-static'>". $col_date_name ."</span>";
                            } else if ($col_day == "SAT" || $col_day == "SUN") { //토 일요일
                                $div_class2=" is-holiday";
                                $div_class3="";
                                $mark1 = "";
                            }else if ($col_gubun1 == "10") {	//휴가 - 출근/퇴근 시간 표시 안함 - 당일 00:00출근 24:00퇴근으로 설정되어 있음
                                $div_class2= "";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>휴가</span>";
                            }else if ($col_gubun1 == "11") {	//병가
                                $div_class2= "";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>병가</span>";
                            }else if ($col_gubun1 == "12") {	//경조사
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>경조사</span>";
                            }else if ($col_gubun1 == "13") { //기타
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>기타</span>";
                            }else if ($col_gubun1 == "14") { //결근
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>결근</span>";
                            }else if ($col_gubun1 == "15") {	//교육/훈련
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>교육/훈련</span>";
                            }else if ($col_gubun1 == "16") {	//프로젝트 휴가
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>프로젝트 휴가</span>";
                            }else if ($col_gubun1 == "17") {	//리프레시 휴가
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>리프레시 휴가</span>";
                            }else if ($col_gubun1 == "18") {	//무급 휴가
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>무급 휴가</span>";
                            }else if ($col_gubun1 == "19") {	//민방위/예비군
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>민방위/예비군</span>";
                            }else if ($col_gubun1 == "20") {	//출산휴가
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>출산휴가</span>";
                            }else if ($col_gubun1 == "21") {	//육아휴직
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>육아휴직</span>";
                            }else if ($col_gubun1 == "0") {//오후반차 제출. 출퇴근체크 X
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'></span>";
                            }else{
                                $div_class1="";
                                $div_class2="";
                                $div_class3="";
                                $mark1 ="";
                            }

                            if ($col_gubun1 == "1")			//출근
                            {
                               
                                if ($checktime1 != "" && substr($checktime1,0,2) < "08") {

                                } else  {

                                }

                            }

                            if($end_day == $count) { //마지막날 div 클래스 표시
                                $div_class4 = " is-last";
                            }

                            //근태수정 버튼 추가(최근 일주일 출력)
                            if($interval->days <= 7 && $col_date <= $nowYear.$nowMonth.$nowDay)
                            {
                                if (!$col_edit_status)
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\">수정요청</a>";
                                }
                                else if ($col_edit_status == "ING")	// 요청중
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">요청중</a>";
                                }
                                else if ($col_edit_status == "CANCEL")	// 반려
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">반려</a>";

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\">재요청</a>";
                                }
                                else if ($col_edit_status == "OK")	// 승인
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">승인</a>";

                                    //echo "<a href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\"><span class='day_edit_btn' style='left:45px; background:#000; color:#FFF;'>재요청</span></a>";
                                }
                            }else{
                                $mark2="";
                            }

                            /*날짜 출력 부분*/
                            echo "<div class='calendar-date " . $div_class1 . $div_class2 . $div_class3 . $div_class4 ."'>
                                    <div class='date'>". $count ."</div>
                                        <div class='mark'>
                                            ". $mark1 . $mark2 . $mark3 . $mark4 . $mark5 . $mark6 . $mark7 . $mark8 . $mark9 . $mark10 ."
                                        </div>
                                        <div class='calendar-events'>
                                            ". $calendar_events1 . $calendar_events2 . $calendar_events3 . $calendar_events4 . $calendar_events5 . $calendar_events6 . $calendar_events7 . $calendar_events8 . $calendar_events9 . $calendar_events10 ."
                                            ". $icon1 . $icon2 . $icon3 ."
                                        </div>
                                       
                                   </div>";
                             /*날짜 출력 부분*/

                            }//배열 끝

                            /* 마지막날짜 뒷공간 갯수 구하기 */
                            $total_day= $count + $lastday;      //앞 빈공간 갯수 + 달력날짜출력값
                            $cal_day = 0; 											//달력영역의 총갯수 max 42
                            $blank_day= 0;											//마지막뒤 빈공간 갯수
                            if($total_day <= 35){
                                $cal_day=35; 													//7x5
                                $blank_day= $cal_day - $total_day;
                                for ($i = 0; $i < $blank_day; $i++)
                                { echo "<div class='calendar-date is-disabled'><div class='date'></div></div>"; }
                            } else {
                                $cal_day = 42; 												//7x6
                                $blank_day= $cal_day - $total_day;
                                for ($i = 0; $i < $blank_day; $i++)
                                { echo "<div class='calendar-date is-disabled'><div class='date'></div></div>"; }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--달력 끝-->
        </div>
        <!--컨텐츠 끝-->

<!--근태 수정 모달 팝업-->
        <div class="modal">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title is-size-6">4월 20일 근태 수정</p>
                    <button class="delete" aria-label="close"></button>
                </header>

                <section class="modal-card-body modal-commuting-modify">

                    <div class="content">
                        <div class="columns is-mobile">
                            <div class="column">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">출근</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field is-grouped">
                                            <div class="control select">
                                                <select>
                                                    <option value="00">00</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10" selected="">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                </select>
                                            </div>
                                            <div class="control select">
                                                <select>
                                                    <option value="00">00</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10" selected="">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">퇴근</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field is-grouped">
                                            <div class="control select">
                                                <select>
                                                    <option value="00">00</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10" selected="">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                </select>
                                            </div>
                                            <div class="control select">
                                                <select>
                                                    <option value="00">00</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10" selected="">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                    <option value="13">13</option>
                                                    <option value="14">14</option>
                                                    <option value="15">15</option>
                                                    <option value="16">16</option>
                                                    <option value="17">17</option>
                                                    <option value="18">18</option>
                                                    <option value="19">19</option>
                                                    <option value="20">20</option>
                                                    <option value="21">21</option>
                                                    <option value="22">22</option>
                                                    <option value="23">23</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <textarea class="textarea" placeholder="사유" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <textarea class="textarea" placeholder="결과" rows="2"></textarea>
                                    </div>
                                    <p class="help is-danger">
                                        * 정상 출퇴근이 아니면, 게시판에 요청 요망
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="cal_value" name="cal_value" value="">  	<!--마지막뒤 빈공간 채우기용 값-->
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-primary">확인</button>
                    <button class="button">취소</button>
                </footer>
            </div>
        </div>

    </section>
    <!-- 본문 끌 -->


    <? include INC_PATH."/bottom.php"; ?>
    </div>
</form>
</body>
</html>



