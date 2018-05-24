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
	<script type="text/javascript">
		alert("해당페이지는 임원,관리자만 확인 가능합니다.");
		location.href="/main.php";
	</script>
<?
		exit;
	}

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
?>

<? include INC_PATH."/top.php"; ?>
<!--기존에 쓰이던 CSS-->
<link rel="stylesheet" href="/assets/css/style_20180406.css" />
<link rel="stylesheet" href="/assets/css/jquery-ui.css" />
<!--기존에 쓰이던 CSS-->

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
</script>
<script src="/assets/js/approval.js"></script>

</head>
<body>
<form method="post" name="form">
	<? include INC_PATH."/top_menu.php"; ?>
    <? include INC_PATH."/commuting_menu.php"; ?>

    <section class="section df-commuting"">
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
                            $count = 0;
                            $lastday = 0;
                            $day_cnt = 0;
                            $pre_date = $Pre;

                            //달력 데이터 미체크
                            $sql = "SELECT 
							DATE, DATEKIND 
						FROM
							HOLIDAY
						WHERE
							DATE LIKE '". str_replace("-","",$date) ."%'
						ORDER BY 
							DATE						
				";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $not_date = $record['DATE'];
                                $not_datekind = $record['DATEKIND'];

                                if ($not_date < date("Ymd") && $not_datekind == "BIZ")
                                {
                                    $sql2 = "SELECT
									A.PRS_NAME
								FROM
									DF_PERSON A WITH(NOLOCK) LEFT OUTER JOIN 
									(
										SELECT
											DATE, PRS_ID
										FROM 
											DF_CHECKTIME WITH(NOLOCK)
										WHERE
											REPLACE(DATE,'-','') = '$not_date'
									) B
								ON
									A.PRS_ID = B.PRS_ID
								WHERE
									A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN (22,87,148,15,24,102) AND REPLACE(A.PRS_JOIN,'-','') < '$not_date'
									AND B.DATE IS NULL
								ORDER BY A.PRS_NAME
						";
                                    $rs2 = sqlsrv_query($dbConn,$sql2);

                                    if ($pre_not_date != $not_date) {
                                        $not_name .= "##";
                                    }

                                    while ($record2 = sqlsrv_fetch_array($rs2))
                                    {
                                        $not_prs_name = $record2['PRS_NAME'];

                                        $not_name .= "//".$not_prs_name;
                                    }
                                }
                                else
                                {
                                    $not_name .= "##";
                                }

                                $pre_not_date = $not_date;
                            }
                            $not_name = str_replace("##//","##",$not_name);
                            $not_name_arr = explode("##",$not_name);

                            //달력 데이터 자동반차
                            $sql = "SELECT 
							A.DATE AS DATE, B.PRS_NAME
						FROM 
							HOLIDAY A WITH(NOLOCK) FULL JOIN
							(
								SELECT
									DATE, PRS_NAME, PRS_ID 
								FROM DF_CHECKTIME WITH(NOLOCK) 
								WHERE 
									GUBUN1 = 8 AND DATE LIKE '". $date ."%' AND PRS_ID NOT IN (15,22,24,87,148,102)
									AND (MEMO3 IS NULL OR (SELECT FORM_TITLE FROM DF_APPROVAL WHERE DOC_NO = REPLACE(REPLACE(MEMO3,'전자결재 (',''),')','')) != '오전반차')
							) B
						ON
							A.DATE = REPLACE(B.DATE,'-','')
						WHERE 
							A.DATE LIKE '". str_replace("-","",$date) ."%'
						ORDER BY 
							A.DATE, B.PRS_NAME						
				";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $auto_date = $record['DATE'];
                                $auto_prs_name = $record['PRS_NAME'];

                                if ($pre_auto_date != $auto_date) {
                                    $auto_name .= "##".$auto_prs_name;
                                }
                                else {
                                    $auto_name .= "//".$auto_prs_name;
                                }

                                $pre_auto_date = $auto_date;
                            }

                            $auto_name_arr = explode("##",$auto_name);

                            //달력 데이터 휴가계
                            $sql = "SELECT 
							A.DATE AS DATE, B.DOC_NO, B.FORM_CATEGORY, B.FORM_TITLE, B.PRS_NAME
						FROM 
							(
								SELECT	
									DATE
								FROM 
									HOLIDAY WITH(NOLOCK)
								WHERE
									DATE LIKE '". str_replace("-","",$date) ."%'
							) A 
							FULL JOIN
							(
								SELECT 
									DOC_NO, FORM_CATEGORY, FORM_TITLE, PRS_NAME, START_DATE, END_DATE, STATUS 
								FROM 
									DF_APPROVAL WITH(NOLOCK)
								WHERE
									FORM_CATEGORY = '휴가계' 
									AND USE_YN = 'Y' AND STATUS IN ('미결재','진행중','결재','전결')
									AND (CONVERT(CHAR(7),START_DATE,120) <= '". $date ."' OR CONVERT(CHAR(7),END_DATE,120) >= '". $date ."') 
							) B
						ON
							A.DATE BETWEEN REPLACE(B.START_DATE,'-','') AND REPLACE(B.END_DATE,'-','')
						ORDER BY 
							A.DATE, 						
							CASE B.FORM_TITLE WHEN '연차' THEN 1 WHEN '프로젝트' THEN 2 WHEN '오전반차' THEN 3 WHEN '오후반차' THEN 4 
							WHEN '프로젝트 오전반차' THEN 5 WHEN '프로젝트 오후반차' THEN 6 WHEN '병가' THEN 7 WHEN '리프레쉬' THEN 8
							WHEN '무급' THEN 9 WHEN '경조사' THEN 10 WHEN '예비군/민방위' THEN 11 WHEN '기타' THEN 12 
							WHEN '휴가 소진시' THEN 13 WHEN '휴가 소진시 오전반차' THEN 14 WHEN '휴가 소진시 오후반차' THEN 15 
							WHEN '출산휴가' THEN 16 WHEN '육아휴직' THEN 17 END,					
							B.PRS_NAME
				";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $vac_date = $record['DATE'];					//날짜
                                $vac_doc_no = $record['DOC_NO'];				//문서번호
                                $vac_form_title = $record['FORM_TITLE'];		//휴가종류
                                $vac_prs_name = $record['PRS_NAME'];			//직원명

                                if ($pre_vac_date != $vac_date) {
                                    if ($vac_doc_no != "") {
                                        $vac_name .= "##<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $vac_doc_no ."');\">".$vac_prs_name ."(". $vac_form_title .")</a></span>";
                                    }
                                    else {
                                        $vac_name .= "##";
                                    }
                                }
                                else {
                                    $vac_name .= "//<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $vac_doc_no ."');\">".$vac_prs_name ."(". $vac_form_title .")</a></span>";
                                }
                                $pre_vac_date = $vac_date;
                            }

                            $vac_name_arr = explode("##",$vac_name);

                            //달력 데이터 조퇴계
                            $sql = "SELECT 
							A.DATE AS DATE, B.DOC_NO, B.FORM_CATEGORY, B.PRS_NAME
						FROM 
							(
								SELECT	
									DATE
								FROM 
									HOLIDAY WITH(NOLOCK)
								WHERE
									DATE LIKE '". str_replace("-","",$date) ."%'
							) A 
							FULL JOIN
							(
								SELECT 
									DOC_NO, FORM_CATEGORY, FORM_TITLE, PRS_NAME, START_DATE, END_DATE, STATUS 
								FROM 
									DF_APPROVAL WITH(NOLOCK)
								WHERE
									FORM_CATEGORY = '조퇴계' 
									AND USE_YN = 'Y' AND STATUS IN ('미결재','진행중','결재','전결')
									AND (CONVERT(CHAR(7),START_DATE,120) <= '". $date ."' OR CONVERT(CHAR(7),END_DATE,120) >= '". $date ."') 
							) B
						ON
							A.DATE BETWEEN REPLACE(B.START_DATE,'-','') AND REPLACE(B.END_DATE,'-','')
						ORDER BY 
							A.DATE, B.PRS_NAME
				";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $early_date = $record['DATE'];					//날짜
                                $early_doc_no = $record['DOC_NO'];				//문서번호
                                $early_prs_name = $record['PRS_NAME'];			//직원명

                                if ($pre_early_date != $early_date) {
                                    if ($early_doc_no != "") {
                                        $early_name .= "##<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $early_doc_no ."');\">".$early_prs_name ."</a></span>";
                                    }
                                    else {
                                        $early_name .= "##";
                                    }
                                }
                                else {
                                    $early_name .= "//<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $early_doc_no ."');\">".$early_prs_name ."</a></span>";
                                }
                                $pre_early_date = $early_date;
                            }

                            $early_name_arr = explode("##",$early_name);

                            //달력 데이터 외근계/파견계/출장계
                            $sql = "SELECT 
							A.DATE AS DATE, B.DOC_NO, B.FORM_CATEGORY, B.FORM_TITLE, B.PRS_NAME
						FROM 
							(
								SELECT	
									DATE
								FROM 
									HOLIDAY WITH(NOLOCK)
								WHERE
									DATE LIKE '". str_replace("-","",$date) ."%'
							) A 
							FULL JOIN
							(
								SELECT 
									DOC_NO, FORM_CATEGORY, FORM_TITLE, PRS_NAME, START_DATE, END_DATE, STATUS 
								FROM 
									DF_APPROVAL WITH(NOLOCK)
								WHERE
									(FORM_CATEGORY = '외근계/파견계' OR FORM_CATEGORY = '출장계') 
									AND USE_YN = 'Y' AND STATUS IN ('미결재','진행중','결재','전결')
									AND (CONVERT(CHAR(7),START_DATE,120) <= '". $date ."' OR CONVERT(CHAR(7),END_DATE,120) >= '". $date ."') 
							) B
						ON
							A.DATE BETWEEN REPLACE(B.START_DATE,'-','') AND REPLACE(B.END_DATE,'-','')
						ORDER BY 
							A.DATE, 
							CASE FORM_CATEGORY WHEN '외근계/파견계' THEN 1 WHEN '출장계' THEN 2 END,
							B.PRS_NAME
				";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $out_date = $record['DATE'];					//날짜
                                $out_doc_no = $record['DOC_NO'];				//문서번호
                                $out_prs_name = $record['PRS_NAME'];			//직원명

                                $sql2 = "SELECT P_PRS_NAME FROM DF_APPROVAL_PARTNER WITH(NOLOCK) WHERE DOC_NO = '". $out_doc_no ."' ORDER BY P_ORDER";
                                $rs2 = sqlsrv_query($dbConn,$sql2);

                                $with_name = "";
                                $with_no = 0;
                                while ($record2 = sqlsrv_fetch_array($rs2))
                                {
                                    if ($with_no == 0) {
                                        $with_name .= $record2["P_PRS_NAME"];
                                    }
                                    else {
                                        $with_name .= ",". $record2["P_PRS_NAME"];
                                    }

                                    $with_no++;
                                }

                                if ($pre_out_date != $out_date) {
                                    if ($out_doc_no != "") {
                                        if ($with_name == "") {
                                            $out_name .= "##<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $out_doc_no ."');\">".$out_prs_name ."</a></span>";
                                        }
                                        else {
                                            $out_name .= "##<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $out_doc_no ."');\">".$out_prs_name ."(". $with_name .")</a></span>";
                                        }
                                    }
                                    else {
                                        $out_name .= "##";
                                    }
                                }
                                else {
                                    if ($out_doc_no != "") {
                                        if ($with_name == "") {
                                            $out_name .= "//<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $out_doc_no ."');\">".$out_prs_name ."</a></span>";
                                        }
                                        else {
                                            $out_name .= "//<span class='is-size-7 has-text-grey'><a href=\"javascript:funView('". $out_doc_no ."');\">".$out_prs_name ."(". $with_name .")</a></span>";
                                        }
                                    }
                                    else {
                                        $out_name .= "##";
                                    }
                                }

                                $pre_out_date = $out_date;
                            }

                            $out_name_arr = explode("##",$out_name);

                            //달력 데이터
                            $sql = "SELECT
							DATE, DATEKIND, DAY, DATE_NAME 
						FROM
							HOLIDAY WITH(NOLOCK)
						WHERE
							DATE LIKE '". str_replace("-","",$date) ."%'
						ORDER BY 
							DATE
						";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $col_date = $record['DATE'];					//날짜
                                $col_datekind = $record['DATEKIND'];			//공휴일 여부
                                $col_day = $record['DAY'];						//요일
                                $col_date_name = $record['DATE_NAME'];			//기념일

                                if ($pre_date != $col_date) {
                                    $count++;
                                    $chk = "Y";
                                    $chg = "Y";
                                }
                                else {
                                    $chk = "N";
                                }

                                $pre_date = $col_date;

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

                                $div_class1 =""; //오늘
                                $div_class2 =""; //공휴일
                                $div_class3 =""; //휴가
                                $div_class4 =""; //마지막날
                                $calendar_events0 ="";
                                $calendar_events1 ="";
                                $calendar_events1_1 ="";
                                $calendar_events2 ="";
                                $calendar_events2_2 ="";
                                $calendar_events3 ="";
                                $calendar_events3_3 ="";
                                $calendar_events4 ="";
                                $calendar_events4_4 ="";
                                $calendar_events5 ="";
                                $calendar_events5_5 ="";
                                $calendar_events6 ="";
                                $calendar_events6_6 ="";



                                if ($col_date == $nowYear.$nowMonth.$nowDay)  { //오늘 날짜 표시
                                    $div_class1=" is-today";
                                }else if($col_date_name != "") { //공휴일 표시
                                    $div_class1="";
                                    $div_class2=" is-holiday";
                                    $div_class3="";
                                    $calendar_events0= "<p class='calendar-event is-dark'>". $col_date_name ."</p>";
                                } else if ($col_day == "SAT" || $col_day == "SUN") { //토 일요일
                                    $div_class1="";
                                    $div_class2=" is-holiday";
                                    $div_class3="";
                                }else{
                                    $div_class1="";
                                    $div_class2="";
                                    $div_class3="";
                                }

                                if ($col_datekind == "BIZ") {
                                    if ($not_name_arr[$count] != "") {
                                        $calendar_events1 ="<p class='calendar-event is-light'>근태미체크</p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events1_1 = str_replace("//","<br>",$not_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($auto_name_arr[$count] != "") {
                                        $calendar_events2 ="<p class='calendar-event is-danger'>자동반차 </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events2_2 = str_replace("//","<br>",$auto_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($vac_name_arr[$count] != "") {
                                        $calendar_events3 ="<p class='calendar-event is-primary'>휴가 </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events3_3 = str_replace("//","<br>",$vac_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($early_name_arr[$count] != "") {
                                        $calendar_events4 ="<p class='calendar-event is-light'>조퇴 </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events4_4 = str_replace("//","<br>",$early_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($out_name_arr[$count] != "") {
                                        $calendar_events5 ="<p class='calendar-event is-warning'>외근/파견/출장 </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events5_5 =str_replace("//","<br>",$out_name_arr[$count]) ."</span><br>";
                                    }
                                }
                                else {
                                    if ($out_name_arr[$count] != "") {
                                        $calendar_events5 ="<p class='calendar-event is-warning'>외근/파견/출장 </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events5_5 = str_replace("//","<br>",$out_name_arr[$count]) ."</span><br>";
                                    }
                                }


                                //마지막날 div 클래스 표시
                                if($end_day == $count) {
                                    $div_class4 = " is-last";
                                }
                                /*날짜 출력 부분*/
                                echo "<div class='calendar-date " . $div_class1 . $div_class2 . $div_class3 . $div_class4 ."'>
                                            <div class='date'>". $count ."</div>
                                                <div class='mark'>
                                                    ". $mark1 . $mark2 . $mark3 . $mark4 . $mark5 . $mark6 . "
                                                </div>
                                                <div class='calendar-events'>
                                                    ". $calendar_events0 . $calendar_events1 . $calendar_events1_1 . $calendar_events2 . $calendar_events2_2 . $calendar_events3 . $calendar_events3_3 ."
                                                    ". $calendar_events4 . $calendar_events4_4 . $calendar_events5 . $calendar_events5_5 . $calendar_events6 . $calendar_events6_6."                                                    
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
</form>
<? include INC_PATH."/bottom.php"; ?>

<div id="popDetail" class="approval-popup2" style="display:none;">
    <div class="title">
        <h3 class="aaa">결재문서 보기</h3>
        <a href="javascript:HidePop('Detail');"><img src="/img/btn_popup_close.gif" alt=""></a>
    </div>

    <div class="content-title ">
        <table class="" width="100%">
            <tr>
                <th scope="row" id="pop_detail_title"></th>
                <td style="float:right;" id="pop_detail_log"></td>
            </tr>
        </table>
    </div>

    <div class="content-wrap" id="pop_detail_content">

    </div>

    <div class="btn-wrap" id="pop_detail_modify">
    </div>
</div>

<div id="popLog" class="approval-popup4" style="display:none">
    <div class="pop_top">
        <p class="pop_title">결재로그</p>
        <a href="javascript:HidePop('Log');" class="close"><img src="/img/btn_popup_close.gif" alt="닫기">닫기</a>
    </div>
    <div class="pop_body" id="pop_log_body">
    </div>
</div>

</body>
</html>