<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	//권한 체크
	if ($prf_id == "7")
	{ 
?>
	<script type="text/javascript">
		location.href="/";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 
	$p_type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "team"; 
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null; 
	$p_sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : null;

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

	$teamSQL = "";
	if ($prf_id == "4")	//관리자, 임원
	{
		if ($p_type == "person")
		{
			$teamSQL = " AND PRS_NAME = '$p_name'";
		}
		else if ($p_type == "team")
		{
			if ($p_team != "")
			{
				$teamSQL = " AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team'))))";
			}
			else
			{
				if (!in_array($prs_team,array('CEO')))
				{
					$teamSQL = " AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$prs_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$prs_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$prs_team'))))";

					$p_team = $prs_team;
				}
			}
		}
	}
	else				//팀.실장
	{
		$teamSQL = " AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$prs_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$prs_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$prs_team'))))";
	}
	
	$date_arr = "";
	$day_arr = "";
	$sql = "SELECT DATE, DAY FROM HOLIDAY WITH(NOLOCK) WHERE DATE LIKE '". str_replace('-','',$date) ."%'";
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$date_arr = $date_arr . $record['DATE'] . "##";
		$day_arr = $day_arr . $record['DAY'] . "##";
	}

	$team_login = "";
	$team_id = "";
	$team_name = "";

	$sql = "SELECT COUNT(*) FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,5,7)". $teamSQL ." AND PRS_ID NOT IN (15,22,24,87,102,148)";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 15;

	$sql = "SELECT 
				PRS_LOGIN, PRS_ID, PRS_NAME,
				(SELECT COUNT(SEQNO) FROM DF_CHECKTIME_REQUEST WITH(NOLOCK)	WHERE PRS_ID = T.PRS_ID AND DATE LIKE '".$date."%') AS EDIT_LOG_CNT
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(ORDER BY PRS_NAME) AS ROWNUM,
					PRS_LOGIN, PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION 
				FROM 
					DF_PERSON WITH(NOLOCK)
				WHERE 
					PRF_ID IN (1,2,3,4,5,7)". $teamSQL ." AND PRS_ID NOT IN (15,22,24,87,102,148)
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";

	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$team_login = $team_login. $record['PRS_LOGIN'] . "##";
		$team_id = $team_id. $record['PRS_ID'] . "##";
		$team_name = $team_name. $record['PRS_NAME'] . "##";
		$team_edit_cnt = $team_edit_cnt. $record['EDIT_LOG_CNT'] . "##";
	}

	//팀별 근태수정 권한 설정
	if (1) // 현재 팀/실장 모두 수정 가능
	{
		$edit_auth = true;
	} 
	else
	{
		$edit_auth = false;
	}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
<!--
	function sSubmit(f)
	{
		f.target="_self";
		f.action="commuting_member.php";
		f.page.value = "1";
		f.submit();
	}

	function eSubmit(f)
	{
		if(event.keyCode ==13)
			sSubmit(f);
	}

	function transSort(val) 
	{
		var frm = document.form;

		if(val=="request" || val=="request-ing") {
			frm.action="commuting_member_request.php";
		} else {
			frm.action="commuting_member.php";
		}

		frm.target="_self";
		frm.year.value = "<?=$p_year?>";
		frm.month.value = "<?=$p_month?>";
		frm.page.value = "1";
		frm.submit();
	}

	//전월보기
	function preMonth()
	{
	<? if ($p_year == $startYear && $p_month == "01") { ?>
		alert("제일 처음입니다.");
	<? } else { ?>
		var frm = document.form;
		
		frm.target="_self";
		frm.action="commuting_member.php";
		frm.year.value = "<?=$PreYear?>";
		frm.month.value = "<?=$PreMonth?>";
		frm.page.value = "1";
		frm.submit();
	<? } ?>
	}

	//다음월보기
	function nextMonth()
	{
		var frm = document.form;

		frm.target="_self";
		frm.action="commuting_member.php";
		frm.year.value = "<?=$NextYear?>";
		frm.month.value = "<?=$NextMonth?>";
		frm.page.value = "1";
		frm.submit();
	 }
	//근태수정팝업 띄우기(관리자)
	<? if ($prf_id == "4") { ?>
	function goModify(date,checktime1,id,checktime2){
		//MM_openBrWindow('pop_modify.php?date='+date+'&checktime1='+checktime1+'&id='+id+'&checktime2='+checktime2,'','width=565 ,height=555,scrollbars=no, scrolling=no');
        $("#popDayEditFrm").attr("src","pop_modify.php?date="+date+'&checktime1='+checktime1+'&id='+id+'&checktime2='+checktime2);
        $("#popDayEdit").addClass("modal is-active");
	}
	<? } ?>

	//근태수정팝업 띄우기(팀/실장)
	function goModify2(date,checktime1,id,checktime2){
		//MM_openBrWindow('pop_modify2.php?date='+date+'&checktime1='+checktime1+'&id='+id+'&checktime2='+checktime2,'','width=565 ,height=645,scrollbars=no, scrolling=no');
        $("#popDayEditFrm").attr("src","pop_modify2.php?date="+date+'&checktime1='+checktime1+'&id='+id+'&checktime2='+checktime2);
        $("#popDayEdit").addClass("modal is-active");
	}

	function excel_download()
	{
		var frm = document.form;

		frm.target = "hdnFrame";
		frm.action = "excel_member.php";
		frm.submit();
	}

//근태수정팝업 띄우기
<? if ($prf_id == "4") { ?>
function ShowPopCustom(id,pid,y,m,d,mode)
{
    var date = y+"-"+m+"-"+d;
    $("#popDayEditFrm").attr("src","commuting_request_pop.php?mode="+mode+"&id="+pid+"&date="+date);
    $("#pop"+id).addClass("modal is-active");
    $("#t_month").text(m);
    $("#t_day").text(d);
}
<? } ?>

function ShowPopCustom2(id,pid,y,m,d,mode)
{
    var date = y+"-"+m+"-"+d;
    $("#popDayEditFrm").attr("src","commuting_request_pop.php?mode="+mode+"&id="+pid+"&date="+date);
    $("#pop"+id).addClass("modal is-active");
    $("#t_month").text(m);
    $("#t_day").text(d);
}


function closePop(id){
    $("#hdnFrame").attr("src","");
    $("#pop"+id).removeClass("is-active");
}
-->
</script>
</head>

<body>
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
	<? include INC_PATH."/top_menu.php"; ?>
			<? include INC_PATH."/commuting_menu.php"; ?>
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
                                        {
                                            $selected = " selected";
                                        }
                                        else
                                        {
                                            $selected = "";
                                        }

                                        echo "<option value='".$i."'".$selected.">".$i."년</option>";                                }
                                    ?>
					            </select>
                           </div>
                           <div class="control select">
                               <select name="month" value="<?=$p_month?>" onchange='sSubmit(this.form)'>
                                    <?
                                    for ($i=1; $i<=12; $i++)
                                    {
                                        if (strlen($i) == "1")
                                        {
                                            $j = "0".$i;
                                        }
                                        else
                                        {
                                            $j = $i;
                                        }

                                        if ($j == $p_month)
                                        {
                                            $selected = " selected";
                                        }
                                        else
                                        {
                                            $selected = "";
                                        }

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
          <div class="card">
          <div class="column">
            <div class="content">
                <div class="level">
                    <? if ($prf_id == "4") { ?>
                     <div class="level-left">
                         <div class="field is-grouped is-multiline">
                            <div class="control select">
                                <select name="type" onChange="sSubmit(this.form)">
                                    <option value="team"<? if ($p_type == "team"){ echo " selected"; } ?>>부서별</option>
                                    <option value="person"<? if ($p_type == "person"){ echo " selected"; } ?>>직원별</option>
                                </select>
                            </div>
                            <? if ($p_type == "team") { ?>
                            <div class="control select">
                                <select name="team" onChange="sSubmit(this.form)">
                                    <option value=""<? if ($p_team == ""){ echo " selected"; } ?>>전직원</option>
                                    <?
                                    $selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
                                    $selRs = sqlsrv_query($dbConn,$selSQL);

                                    while ($selRecord = sqlsrv_fetch_array($selRs))
                                    {
                                        $selStep = $selRecord['STEP'];
                                        $selTeam = $selRecord['TEAM'];

                                        if ($selStep == 2) {
                                            $selTeam2 = $selTeam;
                                        }
                                        else if ($selStep == 3) {
                                            $selTeam2 = "&nbsp;&nbsp;└ ". $selTeam;
                                        }
                                        ?>
                                        <option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </div>
                            <? } else if ($p_type == "person") { ?>
                                <div class="control">
                                    <input type="text" class="input" name="name" placeholder="직원명" value="<?=$p_name?>" onkeypress="eSubmit(this.form);">
                                </div>
                                <div class="control">
                                    <a class="button" href="javascript:sSubmit(this.form);">
                                        <span class="icon is-small" >
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <span>검색</span>
                                    </a>
                                </div>
                            <? } ?>
                            <div class="control select">
                                <select name="sort" onChange="transSort(this.value)">
                                    <option value="" selected>전체</option>
                                    <option value="request">근태수정요청</option>
                                    <option value="request-ing">근태수정요청(미처리)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="level-right is-hidden-mobile">
                        <a class="button" href="javascript:excel_download();">
                                <span class="icon is-small">
                                    <i class="fas fa-file-excel"></i>
                                </span>
                            <span>엑셀로 다운로드</span>
                        </a>
                    </div>
                    <? } else { ?>
                        <input type="hidden" name="type" value="">
                    <? } ?>
                </div>
            </div>
           </div>
          </div>
            <br>
            <div class="content">
                <div class="table-holder">
                    <table class="table is-member">
                        <thead>
                        <tr class="day">
                            <!-- 신형주 td 간격 추가 -->
                            <th class="first">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                            <!-- 신형주 td 간격 추가 -->
                            <th></th>
                            <?
                            $date_arr_ex = explode("##",$date_arr);
                            $day_arr_ex = explode("##",$day_arr);

                            for ($i=0; $i<sizeof($date_arr_ex); $i++)
                            {
                                echo "<th>". substr($date_arr_ex[$i],6,2) ."</th>";
                            }
                            ?>
                        </tr>
                        <tr class="week">
                            <th class="first">
                            </th><th>
                                <?
                                for ($i=0; $i<sizeof($date_arr_ex); $i++)
                                {
                                    if ($day_arr_ex[$i] == "SUN")
                                    {
                                        echo "<th class='sun'><font color='#ef0000'>". $day_arr_ex[$i] ."</font></th>";
                                    }
                                    else if ($day_arr_ex[$i] == "SAT")
                                    {
                                        echo "<th class='sat'>". $day_arr_ex[$i] ."</th>";
                                    }
                                    else
                                    {
                                        echo "<th>". $day_arr_ex[$i] ."</th>";
                                    }
                                }
                                ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        $team_login_ex = explode("##",$team_login);
                        $team_id_ex = explode("##",$team_id);
                        $team_name_ex = explode("##",$team_name);
                        $team_edit_cnt_ex = explode("##",$team_edit_cnt);

                        for ($i=0; $i<sizeof($team_id_ex); $i++)
                        {
                        if ($team_id_ex[$i] != "")
                        {

                        $sql = "EXEC SP_COMMUTING_MEMBER_03 '$team_id_ex[$i]','$date'";
                        $rs = sqlsrv_query($dbConn,$sql);

                        $col_edit_status_arr = "";
                        $col_edit_checktime1_arr = "";
                        $col_edit_checktime2_arr = "";
                        $col_edit_bst_flag_arr = "";

                        $col_date_arr = "";
                        $col_datekind_arr = "";
                        $col_gubun1_arr = "";
                        $col_gubun2_arr = "";
                        $col_checktime1_arr = "";
                        $col_checktime2_arr = "";
                        $col_totaltime_arr = "";
                        $col_overtime_arr = "";
                        $col_undertime_arr = "";
                        $col_pay1_arr = "";
                        $col_pay2_arr = "";
                        $col_pay3_arr = "";
                        $col_pay4_arr = "";
                        $col_pay5_arr = "";
                        $col_pay6_arr = "";
                        $col_out_arr = "";
                        $col_yesterday_overtime_arr = "";
                        $col_yesterday_datekind_arr = "";

                        while ($record = sqlsrv_fetch_array($rs))
                        {
                            // 근태수정 요청 정보
                            $col_edit_status = $record['EDIT_STATUS'];
                            $col_edit_status_arr = $col_edit_status_arr . $col_edit_status ."##";
                            $col_edit_checktime1 = $record['EDIT_CHECKTIME1'];
                            $col_edit_checktime1_arr = $col_edit_checktime1_arr . $col_edit_checktime1 ."##";
                            $col_edit_checktime2 = $record['EDIT_CHECKTIME2'];
                            $col_edit_checktime2_arr = $col_edit_checktime2_arr . $col_edit_checktime2 ."##";
                            $col_edit_bst_flag = $record['EDIT_BST_FLAG'];
                            $col_edit_bst_flag_arr = $col_edit_bst_flag_arr . $col_edit_bst_flag ."##";

                            $col_date = $record['DATE'];
                            $col_datekind = $record['DATEKIND'];
                            $col_gubun1 = $record['GUBUN1'];
                            $col_gubun2 = $record['GUBUN2'];
                            $col_checktime1 = $record['CHECKTIME1'];
                            $col_checktime2 = $record['CHECKTIME2'];
                            $col_totaltime = $record['TOTALTIME'];
                            $col_overtime = $record['OVERTIME'];
                            $col_undertime = $record['UNDERTIME'];
                            $col_pay1 = $record['PAY1'];
                            $col_pay2 = $record['PAY2'];
                            $col_pay3 = $record['PAY3'];
                            $col_pay4 = $record['PAY4'];
                            $col_pay5 = $record['PAY5'];
                            $col_pay6 = $record['PAY6'];
                            $col_out = $record['OUT_CHK'];
                            $col_yesterday_overtime = $record['YESTERDAY_OVERTIME'];
                            $col_yesterday_datekind = $record['YESTERDAY_DATEKIND'];

                            $col_date_arr = $col_date_arr . substr($col_date,0,4) ."-". substr($col_date,4,2) ."-". substr($col_date,6,2) ."##";
                            $col_datekind_arr = $col_datekind_arr . $col_datekind ."##";
                            $col_gubun1_arr = $col_gubun1_arr . $col_gubun1 ."##";
                            $col_gubun2_arr = $col_gubun2_arr . $col_gubun2 ."##";
                            $col_checktime1_arr = $col_checktime1_arr . $col_checktime1 ."##";
                            $col_checktime2_arr = $col_checktime2_arr . $col_checktime2 ."##";
                            $col_totaltime_arr = $col_totaltime_arr . $col_totaltime . "##";
                            $col_overtime_arr = $col_overtime_arr . $col_overtime ."##";
                            $col_undertime_arr = $col_undertime_arr . $col_undertime ."##";
                            $col_pay1_arr = $col_pay1_arr . $col_pay1 ."##";
                            $col_pay2_arr = $col_pay2_arr . $col_pay2 ."##";
                            $col_pay3_arr = $col_pay3_arr . $col_pay3 ."##";
                            $col_pay4_arr = $col_pay4_arr . $col_pay4 ."##";
                            $col_pay5_arr = $col_pay5_arr . $col_pay5 ."##";
                            $col_pay6_arr = $col_pay6_arr . $col_pay6 ."##";
                            $col_out_arr = $col_out_arr . $col_out ."##";
                            $col_yesterday_overtime_arr = $col_yesterday_overtime_arr . $col_yesterday_overtime ."##";
                            $col_yesterday_datekind_arr = $col_yesterday_datekind_arr . $col_yesterday_datekind ."##";
                        }

                        // 근태수정 요청 정보
                        $col_edit_status_ex = explode("##",$col_edit_status_arr);
                        $col_edit_checktime1_ex = explode("##",$col_edit_checktime1_arr);
                        $col_edit_checktime2_ex = explode("##",$col_edit_checktime2_arr);
                        $col_edit_bst_flag_ex = explode("##",$col_edit_bst_flag_arr);

                        $col_date_ex = explode("##",$col_date_arr);
                        $col_datekind_ex = explode("##",$col_datekind_arr);
                        $col_gubun1_ex = explode("##",$col_gubun1_arr);
                        $col_gubun2_ex = explode("##",$col_gubun2_arr);
                        $col_checktime1_ex = explode("##",$col_checktime1_arr);
                        $col_checktime2_ex = explode("##",$col_checktime2_arr);
                        $col_totaltime_ex = explode("##",$col_totaltime_arr);
                        $col_overtime_ex = explode("##",$col_overtime_arr);
                        $col_undertime_ex = explode("##",$col_undertime_arr);
                        $col_pay1_ex = explode("##",$col_pay1_arr);
                        $col_pay2_ex = explode("##",$col_pay2_arr);
                        $col_pay3_ex = explode("##",$col_pay3_arr);
                        $col_pay4_ex = explode("##",$col_pay4_arr);
                        $col_pay5_ex = explode("##",$col_pay5_arr);
                        $col_pay6_ex = explode("##",$col_pay6_arr);
                        $col_out_ex = explode("##",$col_out_arr);
                        $col_yesterday_overtime_ex = explode("##",$col_yesterday_overtime_arr);
                        $col_yesterday_datekind_ex = explode("##",$col_yesterday_datekind_arr);

                        // 근태수정 횟수
                        $edit_cnt_box = "";
                        if (in_array($prs_id,$positionC_arr) || $prf_id == "4")
                        {
                            if ($edit_auth && $team_edit_cnt_ex[$i] > 0)
                            {
                                $edit_cnt_box = "<div style='background-color:#ff0000; width:100%; position:absolute; bottom:0px'><font color='#ffffff'>근태수정<br>".number_format($team_edit_cnt_ex[$i])." 회</font></div>";
                            }
                        }
                        ?>
                        <tr class="line_up">
                            <td rowspan="2" class="name" style="width:50px;position:relative;"><?=$team_name_ex[$i]?><?=$edit_cnt_box?></td>
                            <td><span class="in">출</span></td>
                            <?
                            for ($j=0; $j<sizeof($col_date_ex); $j++)
                            {
                            if ($col_date_ex[$j] != "")
                            {
                            if ($col_checktime1_ex[$j] == "")
                            {
                                $prt_time = "-";
                            }
                            else
                            {
                                $prt_time = substr($col_checktime1_ex[$j],8,2) .":". substr($col_checktime1_ex[$j],10,2);
                            }

                            // 근태수정 요청 관련
                            $day_color = "";
                            $edit_flag = "";
                            if ($edit_auth && $col_edit_checktime1_ex[$j])
                            {
                                if ($col_edit_status_ex[$j] == "ING")
                                {
                                    // 경영지원팀 확인필요 요청건
                                    if ($col_edit_bst_flag_ex[$j] == "N") $day_color = " background-color:#0000cc";
                                    else $day_color = " background-color:#FF0000";
                                }
                                else if ($col_edit_status_ex[$j] == "CANCEL")
                                {
                                    $day_color = " background-color:#FF0000";
                                }
                                else if ($col_edit_status_ex[$j] == "OK")
                                {
                                    //$day_color = " style='background-color:#c0c0c0'";
                                    $edit_flag = "<span style='position:absolute; top:0px; left:0px;'><img src='../img/icon_left_top.gif' width='15'></span>";
                                }
                            }
                            ?>
                            <td valign="top" style="position:relative; <?=$day_color?>">
                                <?
                                if (in_array($prs_id,$positionC_arr) || $prf_id == "4")
                                {
                                    echo $edit_flag;
                                }

                                if ($prf_id == "4")
                                {
                                ?>
                                <a href="javascript:goModify('<?=$col_date_ex[$j]?>','<?=$col_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_checktime2_ex[$j]?>');">
                                    <?
                                    }
                                    // 근태수정 요청
                                    if ($edit_auth && $col_edit_status_ex[$j] == "ING" && $col_edit_checktime1_ex[$j])
                                    {
                                    $prt_time = substr($col_edit_checktime1_ex[$j],8,2) .":". substr($col_edit_checktime1_ex[$j],10,2);
                                    ?>
                                    <a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>요청</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
                                        <?
                                        }
                                        // 근태수정 반려
                                        else if ($edit_auth && $col_edit_status_ex[$j] == "CANCEL" && $col_edit_checktime1_ex[$j])
                                        {
                                        $prt_time = substr($col_edit_checktime1_ex[$j],8,2) .":". substr($col_edit_checktime1_ex[$j],10,2);
                                        ?>
                                        <a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>반려</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
                                            <?
                                            }
                                            else
                                            {
                                                // 출근체크가 정보가 없는 경우
                                                if ($prt_time == "-")
                                                {
                                                    echo $prt_time;
                                                }
                                                else
                                                {

                                                    if ($col_gubun1_ex[$j] == "10" || $col_gubun1_ex[$j] == "16" || $col_gubun1_ex[$j] == "17" || $col_gubun1_ex[$j] == "18") {			//휴가/프로젝트휴가/리프레시휴가/무급휴가
                                                        echo "<font color='#ff8800'>휴가</font>";
                                                    } else if ($col_gubun1_ex[$j] == "11") {	//병가
                                                        echo "<font color='#ff8800'>병가</font>";
                                                    } else if ($col_gubun1_ex[$j] == "12") {	//경조사
                                                        echo "<font color='#ff8800'>경조사</font>";
                                                    } else if ($col_gubun1_ex[$j] == "13" || $col_gubun1_ex[$j] == "20" || $col_gubun1_ex[$j] == "21") {	//기타/출산휴가/육아휴직
                                                        echo "<font color='#ff8800'>기타</font>";
                                                    } else if ($col_gubun1_ex[$j] == "14") {	//결근
                                                        echo "<font color='#ff8800'>결근</font>";
                                                    } else if ($col_gubun1_ex[$j] == "15") {	//교육
                                                        echo "<font color='#ff8800'>교육</font>";
                                                    } else if ($col_gubun1_ex[$j] == "19") {	//예비군
                                                        echo "<font color='#ff8800'>예비군</font>";
                                                        //						} else if ($col_gubun1_ex[$j] == "8") {		//반차
                                                        //							echo "<font color='#ff8800'>반차</font>";
                                                        //						} else if ($col_gubun1_ex[$j] == "7") {		//지각
                                                        //							echo "<font color='#ef0000'>". $prt_time ."</font>";
                                                    } else if ($col_gubun1_ex[$j] == "4" || $col_gubun1_ex[$j] == "8") {		//프로젝트 반차/반차 - 출근인정시간대 이후 출근 포함
                                                        echo "<font color='#ff8800'>반차</font><br><font color='#ef0000'>(". $prt_time .")</font>";
                                                    } else if ($col_gubun2_ex[$j] == "5" || $col_gubun2_ex[$j] == "9") {		//프로젝트 반차/반차 - 출근인정시간대 이후 출근 포함
                                                        echo "<font color='#ff8800'>반차</font><br><font color='#00aa00'>". $prt_time ."</font>";
                                                    } else {
                                                        if (substr($prt_time,0,2) < "08")		//출근인정시간대 이전 출근
                                                        {
                                                            echo "<font color='#00aa00'>08:00</font><br><font color='#0000cc'>(". $prt_time .")</a>";
                                                        }
                                                        else
                                                        {
                                                            echo "<font color='#00aa00'>". $prt_time ."</font>";
                                                        }
                                                    }
                                                }
                                            }

                                            if ($prf_id == "4")
                                            {
                                            ?>
                                        </a>
                                    <?
                                    }
                                    ?>
                            </td>
                                <?
                            }
                            }
                            ?>
                        </tr>
                            <tr class="line_down">
                                <td><span class="out">퇴</span></td>
                                <?
                                for ($j=0; $j<sizeof($col_date_ex); $j++)
                                {
                                    if ($col_date_ex[$j] != "")
                                    {

                                        if ($col_checktime2_ex[$j] == "")
                                        {
                                            $prt_time = "-";
                                        }
                                        else
                                        {
                                            $prt_time = substr($col_checktime2_ex[$j],8,2) .":". substr($col_checktime2_ex[$j],10,2);
                                        }

                                        // 근태수정 요청 관련
                                        $day_color = "";
                                        if ($edit_auth && $col_edit_checktime2_ex[$j])
                                        {
                                            if ($col_edit_status_ex[$j] == "ING")
                                            {
                                                // 경영지원팀 확인필요 요청건
                                                if ($col_edit_bst_flag_ex[$j] == "N") $day_color = " background-color:#0000cc";
                                                else $day_color = " background-color:#FF0000";
                                            }
                                            else if ($col_edit_status_ex[$j] == "CANCEL")
                                            {
                                                $day_color = " background-color:#FF0000";
                                            }
                                            else if ($col_edit_status_ex[$j] == "OK")
                                            {
                                                //$day_color = " style='background-color:#c0c0c0'";
                                            }
                                        }
                                        ?>
                                        <td valign="top" style="<?=$day_color?>">
                                            <?
                                            if ($col_datekind_ex[$j] == "BIZ" && ($col_gubun1_ex[$j] == "1" || $col_gubun1_ex[$j] == "6" || $col_gubun1_ex[$j] == "4" || $col_gubun2_ex[$j] == "5" || $col_gubun1_ex[$j] == "8" || $col_gubun2_ex[$j] == "9") && ($col_gubun2_ex[$j] == "2" || $col_gubun2_ex[$j] == "3" || $col_gubun2_ex[$j] == "6")) {

                                                $shift_time = "0";
                                                $shift_minute = "0";

                                                if ($col_yesterday_datekind_ex[$j] == "BIZ") {
                                                    if ($col_yesterday_overtime_ex[$j] >= "0700") {
                                                        $shift_time = "03";
                                                        $shift_minute = "00";
                                                    }
                                                    else if ($col_yesterday_overtime_ex[$j] >= "0600") {
                                                        $shift_time = "02";
                                                        $shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
                                                    }
                                                    else if ($col_yesterday_overtime_ex[$j] >= "0500") {
                                                        $shift_time = "01";
                                                        $shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
                                                    }
                                                    else if ($col_yesterday_overtime_ex[$j] >= "0400") {
                                                        $shift_time = "00";
                                                        $shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
                                                    }
                                                }
                                                else {
                                                    if ($col_yesterday_overtime_ex[$j] >= "0900") {
                                                        $shift_time = "03";
                                                        $shift_minute = "00";
                                                    }
                                                    else if ($col_yesterday_overtime_ex[$j] >= "0800") {
                                                        $shift_time = "02";
                                                        $shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
                                                    }
                                                    else if ($col_yesterday_overtime_ex[$j] >= "0700") {
                                                        $shift_time = "01";
                                                        $shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
                                                    }
                                                    else if ($col_yesterday_overtime_ex[$j] >= "0600") {
                                                        $shift_time = "00";
                                                        $shift_minute = substr($col_yesterday_overtime_ex[$j],2,2);
                                                    }
                                                }

                                                if (strlen($shift_time) == 1) { $shift_time = "0". $shift_time; }
                                                if (strlen($shift_minute) == 1) { $shift_minute = "0". $shift_minute; }

                                                if ($shift_time >= "01" || $shift_minute >= "01") { echo "<span style='font-size:11px; color:#666666;'>". $shift_time .":". $shift_minute ."</span><br>"; }
                                                else { echo "<br>"; }
                                            }
                                            else
                                            {
                                                echo "<br>";
                                            }

                                            if ($prf_id == "4")
                                            {
                                            ?>
                                            <a href="javascript:goModify('<?=$col_date_ex[$j]?>','<?=$col_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_checktime2_ex[$j]?>');">
                                                <?
                                                }

                                                // 근태수정 요청
                                                if ($edit_auth && $col_edit_status_ex[$j] == "ING" && $col_edit_checktime2_ex[$j])
                                                {
                                                $prt_time = substr($col_edit_checktime2_ex[$j],8,2) .":". substr($col_edit_checktime2_ex[$j],10,2);
                                                ?>
                                                <a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>요청</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
                                                    <?
                                                    }
                                                    // 근태수정 반려
                                                    else if ($edit_auth && $col_edit_status_ex[$j] == "CANCEL" && $col_edit_checktime2_ex[$j])
                                                    {
                                                    $prt_time = substr($col_edit_checktime2_ex[$j],8,2) .":". substr($col_edit_checktime2_ex[$j],10,2);
                                                    ?>
                                                    <a href="javascript:goModify2('<?=$col_date_ex[$j]?>','<?=$col_edit_checktime1_ex[$j]?>','<?=$team_id_ex[$i]?>','<?=$col_edit_checktime2_ex[$j]?>');"><font color='#ffffff'>반려</font><br><font color='#ffffff'>(<?=$prt_time?>)</font>
                                                        <?
                                                        }
                                                        else
                                                        {
                                                            // 퇴근체크가 정보가 없는 경우
                                                            if ($prt_time == "-")
                                                            {
                                                                echo $prt_time;
                                                            }
                                                            else
                                                            {
                                                                if ($col_gubun2_ex[$j] == "10" || $col_gubun2_ex[$j] == "16" || $col_gubun2_ex[$j] == "17" || $col_gubun2_ex[$j] == "18") {			//휴가/프로젝트휴가/리프레시휴가/무급휴가
                                                                    echo "-";
                                                                } else if ($col_gubun2_ex[$j] == "11") {	//병가
                                                                    echo "-";
                                                                } else if ($col_gubun2_ex[$j] == "12") {	//경조사
                                                                    echo "-";
                                                                } else if ($col_gubun2_ex[$j] == "13" || $col_gubun2_ex[$j] == "20" || $col_gubun2_ex[$j] == "21") {	//기타/출산휴가/육아휴직
                                                                    echo "-";
                                                                } else if ($col_gubun2_ex[$j] == "14") {	//결근
                                                                    echo "-";
                                                                } else if ($col_gubun2_ex[$j] == "15") {	//교육
                                                                    echo "-";
                                                                } else if ($col_gubun1_ex[$j] == "19") {	//예비군
                                                                    echo "-";
                                                                } else {
                                                                    echo $prt_time;

                                                                    if ($col_undertime_ex[$j] > "0000")
                                                                    {
                                                                        echo "<br><font color='#ef0000'>(". substr($col_undertime_ex[$j],0,2) .":". substr($col_undertime_ex[$j],2,2) .")</font>";
                                                                    }
                                                                    else
                                                                    {
                                                                        if ($col_overtime_ex[$j] > "0000")
                                                                        {
                                                                            echo "<br><font color='#0000cc'>(". substr($col_overtime_ex[$j],0,2) .":". substr($col_overtime_ex[$j],2,2) .")</font>";
                                                                        }
                                                                        else
                                                                        {
                                                                            echo "<br>(00:00)";
                                                                        }
                                                                    }

                                                                }
                                                            }
                                                        }

                                                        if ($prf_id == "4")
                                                        {
                                                        ?>
                                                    </a>
                                                <?
                                                $alarm = "";
                                                $out = "파견";

                                                if ($col_pay1_ex[$j] == "Y") { $alarm .= "점."; }
                                                if ($col_pay2_ex[$j] == "Y") { $alarm .= "저."; }
                                                if ($col_pay3_ex[$j] == "Y") { $alarm .= "간."; }
                                                if ($col_pay4_ex[$j] == "Y") { $alarm .= "교"; }
                                                if ($col_pay5_ex[$j] == "Y") { $out .= "(출)"; }
                                                if ($col_pay6_ex[$j] == "Y") { $out .= "(퇴)"; }

                                                echo "<br><span style='font-size:11px; color:#666666;'>". $alarm ."</span>";
                                                if ($col_out_ex[$j] == "Y") { echo "<br><span style='font-size:11px; color:#666666;'>". $out ."</span>"; }

                                                if ($col_gubun1_ex[$j] == "6" || $col_gubun2_ex[$j] == "6") {		//외근
                                                    echo "<br><span style='font-size:11px; color:#ff8800';'>외근</span>";
                                                }
                                                }
                                                ?>
                                        </td>
                                        <?
                                    }
                                }
                                ?>
                            </tr>
                            <?
                        }
                        }
                        ?>
                        </tbody>
                        <thead>
                        <tr class="day">
                            <th class="first"></th>
                            <th></th>
                            <?
                            $date_arr_ex = explode("##",$date_arr);
                            $day_arr_ex = explode("##",$day_arr);

                            for ($i=0; $i<sizeof($date_arr_ex); $i++)
                            {
                                echo "<th>". substr($date_arr_ex[$i],6,2) ."</th>";
                            }
                            ?>
                        </tr>
                        <tr class="week">
                            <th class="first"></td>
                            <th></th>
                            <?
                            for ($i=0; $i<sizeof($date_arr_ex); $i++)
                            {
                                if ($day_arr_ex[$i] == "SUN")
                                {
                                    echo "<th class='sun'><font color='#ef0000'>". $day_arr_ex[$i] ."</font></th>";
                                }
                                else if ($day_arr_ex[$i] == "SAT")
                                {
                                    echo "<th class='sat'>". $day_arr_ex[$i] ."</th>";
                                }
                                else
                                {
                                    echo "<th>". $day_arr_ex[$i] ."</th>";
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                    </table>
                    <div class="page_num">
                        <?=getPaging($total_cnt,$page,$per_page);?>
                    </div>
                </div>
            </div>
    </section>

    <form class="inlp" method='post' name='form'>
        <div class="modal" id="popDayEdit">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title is-size-6">팀원현황 상세변경</p>
                    <a class="delete" aria-label="close" href="javascript:closePop('DayEdit');" ></a>
                </header>
                <iframe class="table-holder" id="popDayEditFrm"  style="border-bottom-left-radius: 6px; border-bottom-right-radius: 6px;" height="400" scrolling="yes" frameborder="0"></iframe>
            </div>
        </div>
        </section>
    </form>

</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>