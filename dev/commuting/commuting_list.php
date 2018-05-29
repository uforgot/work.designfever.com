<?
    require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
    require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang

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

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
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
    //���¼����˾� ����
    function ShowPopCustom(id,pid,y,m,d,mode)
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


</script>
</head>

<body>
<form method="post" name="form">
    <? include INC_PATH."/top_menu.php"; ?>
    <? include INC_PATH."/commuting_menu.php"; ?>

    <!-- ���� ���� -->
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
                                        echo "<option value='".$i."'".$selected.">".$i."��</option>";
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
                                            echo "<option value='".$j."'".$selected.">".$i."��</option>";
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
                                    �������
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=$biz_commute_count?></div>
                            </div>
                        </div>
                        <div class="column is-one-quarter-mobile">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered is-vacation-title">
                                    �ް�
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=$vacation_count + ($subvacation_count * 0.5) ?></div>
                            </div>
                        </div>
                        <div class="column is-one-quarter-mobile">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered is-vacation-title">
                                    �ٹ��ϼ�
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=$commute_day?></div>
                            </div>
                        </div>
                        <div class="column is-one-quarter-mobile">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered is-vacation-title">
                                    ������û
                                </div>
                                <div class="title is-size-6 has-text-centered"><?=number_format($edit_count)?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="field is-grouped is-grouped-multiline">
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag"> �ٹ��ð� ����</span>
                            <span class="tag is-primary"><?=$total_time?> : <?=$total_minute?></span>
                        </div>
                    </div>
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag">��� ��ٽð�</span>
                            <span class="tag is-primary"><?=$avgtime1?> : <?=$avgminute1?></span>
                        </div>
                    </div>
                    <div class="control">

                        <div class="tags has-addons">
                            <span class="tag">��� ��ٽð�</span>
                            <span class="tag is-primary"><?=$avgtime2?> : <?=$avgminute2?></span>
                        </div>
                    </div>
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag">��� �ٹ��ð�</span>
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
                            <span class="tag">�ʰ� �ٹ��ð�</span>
                            <span class="tag is-primary"><?=$over_time?> : <?=$over_minute?></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- �޷� ���-->
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
                            $end_day = date("t", mktime(0, 0, 0, $p_month, 1, $p_year));//�ش���� ������ ��¥ ���ϱ�

                            $count = 0;
                            $lastday = 0;

                            $pre_checktime = "";	//���� ��ٽð��� ���� ó��
                            $pre_gubun2 = "";		//���� ���_������ ���� ó��
                            $pre_checktime_c2 = "";	//���� ��ٽð��� ���� ó��
                            $col_pre_gubun2_c2 = "";	//���� ���_������ ���� ó��

                            $worktime1 = "";	//��ٽð�
                            $worktime2 = "";	//��ٽð�

                            $day_cnt = 0;


                            //�޷� ������
                            $sql = "EXEC SP_COMMUTING_LIST_03 '$prs_id','$date'";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                        $count++;

                                        $col_date = $record['DATE'];								//��¥
                                        $col_datekind = $record['DATEKIND'];				        //������ ����
                                        $col_day = $record['DAY'];									//����
                                        $col_date_name = $record['DATE_NAME'];			            //�����
                                        $col_gubun = $record['GUBUN'];							    //����ٱ���
                                        $col_gubun1 = $record['GUBUN1'];						    //��ٱ���
                                        $col_gubun2 = $record['GUBUN2'];						    //��ٱ���
                                        $col_checktime1 = $record['CHECKTIME1'];		            //��ٽð�
                                        $col_checktime2 = $record['CHECKTIME2'];		            //��ٽð�
                                        $col_totaltime = $record['TOTALTIME'];			            //�ٹ��ð�
                                        $col_overtime = $record['OVERTIME'];			            //�ʰ��ٹ�
                                        $col_undertime = $record['UNDERTIME'];			            //�̸��ٹ�
                                        $col_pay1 = $record['PAY1'];					            //���ɽĺ�
                                        $col_pay2 = $record['PAY2'];					            //����ĺ�
                                        $col_pay3 = $record['PAY3'];					            //���ĺ�
                                        $col_pay4 = $record['PAY4'];					            //�߱ٱ����
                                        $col_pay5 = $record['PAY5'];					            //�İ߱����(���)
                                        $col_pay6 = $record['PAY6'];					            //�İ߱����(���)
                                        $col_out = $record['OUT_CHK'];					            //�İ߿���
                                        $col_off_time = $record['OFF_TIME'];			            //����ð���
                                        $col_off_minute = $record['OFF_MINUTE'];		            //����ð���
                                        $col_yesterday_overtime = $record['YESTERDAY_OVERTIME'];	//���� ����ٹ��ð�
                                        $col_yesterday_datekind = $record['YESTERDAY_DATEKIND'];	//���� �ٹ��� ����

                                        $col_edit_status = $record['EDIT_STATUS'];		//������û����

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

                                        //���۳�¥ �� ����� üũ (
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

                                            $div_class1 =""; //����
                                            $div_class2 =""; //������
                                            $div_class3 =""; //�ް�
                                            $div_class4 =""; //��������
                                                $mark1 ="";
                                                $mark2 ="";
                                                $mark3 ="";
                                                $mark4 ="";
                                                $mark5 ="";
                                                $mark6 ="";
                                                $mark6_1="";
                                                $mark6_2="";
                                                    $calendar_events1 =""; //��ٽð�
                                                    $calendar_events2 =""; //��ٽð�
                                                    $calendar_events3 ="";
                                                    $calendar_events4 ="";
                                                    $calendar_events5 ="";
                                                    $calendar_events6 ="";
                                                        $icon1="";
                                                        $icon2="";
                                                        $icon3="";
                                                        $icon4="";


                                if ($col_date == $nowYear.$nowMonth.$nowDay)  { //���� ��¥ ǥ��
                                        $div_class1=" is-today";
                                    }else if($col_date_name != "") { //������ ǥ��
                                        $div_class1="";
                                        $div_class2=" is-holiday";
                                        $div_class3="";
                                        $calendar_events4= "<p class='calendar-event is-dark'>". $col_date_name ."</p>";
                                    } else if ($col_day == "SAT" || $col_day == "SUN") { //�� �Ͽ���
                                        $div_class1="";
                                        $div_class2=" is-holiday";
                                        $div_class3="";
                                        $mark1 = "";
                                    }else if ($col_gubun1 == "10") {	//�ް� - ���/��� �ð� ǥ�� ���� - ���� 00:00��� 24:00������� �����Ǿ� ����
                                        $div_class1="";
                                        $div_class2= "";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>�ް�</span>";
                                    }else if ($col_gubun1 == "11") {	//����
                                        $div_class1="";
                                        $div_class2= "";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>����</span>";
                                    }else if ($col_gubun1 == "12") {	//������
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>������</span>";
                                    }else if ($col_gubun1 == "13") { //��Ÿ
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>��Ÿ</span>";
                                    }else if ($col_gubun1 == "14") { //���
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>���</span>";
                                    }else if ($col_gubun1 == "15") {	//����/�Ʒ�
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>����/�Ʒ�</span>";
                                    }else if ($col_gubun1 == "16") {	//������Ʈ �ް�
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>������Ʈ �ް�</span>";
                                    }else if ($col_gubun1 == "17") {	//�������� �ް�
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>�������� �ް�</span>";
                                    }else if ($col_gubun1 == "18") {	//���� �ް�
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>���� �ް�</span>";
                                    }else if ($col_gubun1 == "19") {	//�ι���/����
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>�ι���/����</span>";
                                    }else if ($col_gubun1 == "20") {	//����ް�
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>����ް�</span>";
                                    }else if ($col_gubun1 == "21") {	//��������
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'>��������</span>";
                                    }else if ($col_gubun1 == "0") {//���Ĺ��� ����. �����üũ X
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3=" is-vacation";
                                        $mark1="<span class='button is-small is-static'></span>";
                                    }else{
                                        $div_class1="";
                                        $div_class2="";
                                        $div_class3="";
                                        $mark1 ="";
                                    }

                                //������Ȳ
                                    //��������ð���(0800-1100) but, ���� ����ٷνð� 5�ð� ���� 1�ð��� �ִ� 3�ð����� ����(0800-1400)
                                    //1.��������ð��� - ��ٽð� ���
                                    //2.��������ð��� ���� ��� - ��ٽð� 1300 ǥ�� �������� ó��.���� ��ٽð� ���
                                    //3.��������ð��� ���� ��� - ��ٽð� 0800 ǥ��.���� ��ٽð� ���
                                    //����ٹ��ð�(0900) but, ���� ����ٷ� 5�ð� ���� 1�ð��� �ִ� 3�ð����� ����(0600)
                                    //4.����ٹ��ð� �̻� �ٹ� �� ��� - ��ٽð� ���.�ʰ��ٹ��ð� ���
                                    //5.����ٹ��ð� �̸� �ٹ� �� ��� - ��ٽð� ���.�����ٹ��ð� ���
                                    //�ް� ��(GUBUN2 = 10~18)�� �ð� �Ⱥ��̰Բ� ó��

                                    //��ٽð�
                                    $checktime1 = substr($col_checktime1,8,2) .":". substr($col_checktime1,10,2);
                                    if ($checktime1 == ":") { $checktime1 = ""; }

                                    if ($col_gubun1 == "1")			//���
                                    {

                                        if ($checktime1 != "" && substr($checktime1,0,2) < "08") {
                                            $calendar_events1 ="<p class='calendar-event is-light'>��� <font color='#00aa00'>08:00</font> $checktime1 </p>";
                                        } else  {
                                            $calendar_events1 ="<p class='calendar-event is-light'>��� $checktime1 </p>";
                                        }

                                    }else if ($col_gubun1 == "4")	//������Ʈ ���� ����
                                    {
                                        $calendar_events1 ="<p class='calendar-event is-primary'>������Ʈ �������� $checktime1 </p>";
                                    }
                                    else if ($col_gubun1 == "6")	//�ܱ�
                                    {
                                        $calendar_events1 ="<p class='calendar-event is-warning'>�ܱ� $checktime1 </p>";
                                    }
                                    else if ($col_gubun1 == "7")	//����
                                    {
                                        $calendar_events1 ="<p class='calendar-event is-danger'>���� $checktime1 </p>";
                                    }
                                    else if ($col_gubun1 == "8")	//���� ����
                                    {
                                        $calendar_events1 ="<p class='calendar-event is-primary'>���� ���� $checktime1 </p>";
                                    }else{
                                        $calendar_events1 ="";
                                    }

                                    //��ٽð�
                                    $checktime2 = substr($col_checktime2,8,2) .":". substr($col_checktime2,10,2);
                                    if ($checktime2 == ":") { $checktime2 = ""; }

                                    if ($col_gubun2 == "2" || $col_gubun2 == "3" || $col_gubun2 == "5" || $col_gubun2 == "6" || $col_gubun2 == "9" || $col_gubun2 == "0")
                                    {
                                        if ($col_gubun2 == "2" || $col_gubun2 == "3")
                                        {
                                            $calendar_events2= "<p class='calendar-event is-light'>��� $checktime2 </p>";
                                        } else if ($col_gubun2 == "5")	//������Ʈ ���� ����
                                        {
                                            $calendar_events2= "<p class='calendar-event is-primary'>������Ʈ ���Ĺ��� $checktime2 </p>";
                                        } else if ($col_gubun2 == "6")	//�ܱ�
                                        {
                                            $calendar_events2= "<p class='calendar-event is-warning'>�ܱ� $checktime2 </p>";
                                        } else if ($col_gubun2 == "9")	//���� ����
                                        {
                                            $calendar_events2= "<p class='calendar-event is-primary'>���� ���� $checktime2 </p>";
                                        } else if ($col_gubun2 == "0")	//�������� ����. �����üũ X
                                        {
                                            $calendar_events2= "<p class='calendar-event is-light'>��� $checktime2 </p>";
                                        }else{
                                            $calendar_events2="";
                                        }

                                        if ($checktime1 !== "" && $checktime2 !== "")
                                        {
                                            if ($col_undertime > "0000")
                                            {
                                                $calendar_events3= "<p class='calendar-event is-danger'>�̸��ٹ��ð� ". substr($col_undertime,0,2) .":". substr($col_undertime,2,2) ."</p>";
                                            } else {
                                                if ($col_overtime > "0000")
                                                {
                                                    $calendar_events3= "<p class='calendar-event is-success'>�ʰ� �ٹ��ð� ". substr($col_overtime,0,2) .":". substr($col_overtime,2,2) ."</p>";
                                                } else {
                                                    //$calendar_events3= "<p class='calendar-event is-success'>00:00</p>";
                                                    $calendar_events3= ""; //�ʰ� �ٹ��ð� ������� �׳� ��¾���
                                                }
                                            }
                                        }else{
                                            $calendar_events3= "";
                                        }
                                    }else{
                                        $calendar_events2= "";
                                        $calendar_events3= "";
                                    }

                                   //���� ���� ���� ����� �� �İ� ����� ���
                                    if ($col_pay1 == "Y") { $icon1 = " <span class='tag is-info tooltip' data-tooltip='����' ><span class='icon'><i class='fas fa-utensils'></i></span></span>"; }else{ $icon1=""; }		//����
                                    if ($col_pay2 == "Y") { $icon2 = " <span class='tag is-info tooltip' data-tooltip='����'><span class='icon'><i class='fas fa-utensils'></i></span></span>"; }else{ $icon2=""; }	//����
                                    if ($col_pay3 == "Y") { $icon3 = " <span class='tag is-info tooltip' data-tooltip='����'><span class='icon'><i class='fas fa-coffee'></i></span></span>"; }else{ $icon3=""; }		//����
                                    if ($col_pay4 == "Y") { $icon4 = " <span class='tag is-info tooltip' data-tooltip='�߱ٱ����'><span class='icon'><i class='fas fa-taxi'></i></span></span>"; }else{ $icon4=""; }		//�߱ٱ����
                                    if ($col_pay5 == "Y" && $col_pay6 == "Y") {
                                        $calendar_events6 = "<p class='calendar-event is-info'> �İ߱���� : (���) (���)</p>";
                                        }else if ($col_pay5 == "Y") {
                                        $calendar_events6 = "<p class='calendar-event is-info'> �İ߱���� : (���) </p>";
                                        }else if ($col_pay6 == "Y") {
                                        $calendar_events6 = "<p class='calendar-event is-info'> �İ߱���� : (���) </p>";
                                    }else{
                                        $calendar_events6 = "";
                                    }

                                   //����ð�
                                    if ($prf_id != 7) {
                                        if ($col_off_time > 0 || $col_off_minute > 0) {
                                            $calendar_events5 ="<p class='calendar-event is-warning'>�� ����ð� ". $col_off_time .":". $col_off_minute ."</p>";
                                        }else{
                                            $calendar_events5 ="";
                                        }
                                    }

                                   //�������� div Ŭ���� ǥ��
                                    if($end_day == $count) {
                                        $div_class4 = " is-last";
                                    }

                                    //���¼��� ��ư �߰�(�ֱ� ������ ���)
                                    if($interval->days <= 7 && $col_date <= $nowYear.$nowMonth.$nowDay)
                                    {
                                        if (!$col_edit_status)
                                        {
                                            $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\">������û</a>";
                                        } else if ($col_edit_status == "ING"){	// ��û��
                                            $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">��û��</a>";
                                        } else if ($col_edit_status == "CANCEL"){	// �ݷ�
                                            $mark2= "<a class='button is-small is-static' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">�ݷ�</a>";
                                            $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\">���û</a>";
                                        } else if ($col_edit_status == "OK"){	// ����
                                            $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">����</a>";
                                        } else {
                                            $mark2= "";
                                        }
                                    }else{
                                        $mark2="";
                                    }

                                    /*��¥ ��� �κ�*/
                                    echo "<div class='calendar-date " . $div_class1 . $div_class2 . $div_class3 . $div_class4 ."'>
                                            <div class='date'>". $count ."</div>
                                                <div class='mark'>
                                                    ". $mark1 . $mark2 . $mark3 . $mark4 . $mark5 . $mark6 . "
                                                </div>
                                                <div class='calendar-events'>
                                                    ". $calendar_events4 . $calendar_events1 . $calendar_events2 . $calendar_events3 . $calendar_events5 . $calendar_events6 ."
                                                    ". $icon1 . $icon2 . $icon3 . $icon4 ."
                                                </div>
                                               
                                           </div>";
                                     /*��¥ ��� �κ�*/

                            }//�迭 ��

                            /* ��������¥ �ް��� ���� ���ϱ� */
                            $total_day= $count + $lastday;      //�� ����� ���� + �޷³�¥��°�
                            $cal_day = 0; 											//�޷¿����� �Ѱ��� max 42
                            $blank_day= 0;											//�������� ����� ����
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
            <!--�޷� ��-->
        </div>
        <!--������ ��-->

<?
        $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;
        $p_date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null;
        $p_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

        // �������� ��� ������
        $sql = "SELECT 
				GUBUN, GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2
			FROM 
				DF_CHECKTIME WITH(NOLOCK)
			WHERE 
				PRS_ID = '$p_id' AND DATE = '$p_date'";
        $rs = sqlsrv_query($dbConn, $sql);

        $record = sqlsrv_fetch_array($rs);
        if (sizeof($record) > 0)
        {
            $md_gubun = $record['GUBUN'];
            $md_gubun1 = $record['GUBUN1'];
            $md_gubun2 = $record['GUBUN2'];
            $md_checktime1 = $record['CHECKTIME1'];
            $md_checktime2 = $record['CHECKTIME2'];
        }

        // ���¼��� ��û ������
        $sql = "SELECT 
				TOP 1 *, CONVERT(CHAR(19), REGDATE, 20) as REGDATE, CONVERT(CHAR(19), OK_DATE, 20) as OK_DATE

			FROM 
				DF_CHECKTIME_REQUEST WITH(NOLOCK)
			WHERE 
				PRS_ID = '$p_id' AND DATE = '$p_date'
			ORDER BY
				SEQNO DESC";
        $rs = sqlsrv_query($dbConn, $sql);

        $record = sqlsrv_fetch_array($rs);
        if (sizeof($record) > 0)
        {
            $rd_seqno = $record['SEQNO'];
            $rd_name = $record['PRS_NAME'];
            $rd_login = $record['PRS_LOGIN'];
            $rd_gubun = $record['GUBUN'];
            $rd_gubun1 = $record['GUBUN1'];
            $rd_gubun2 = $record['GUBUN2'];
            $rd_checktime1 = $record['CHECKTIME1'];
            $rd_checktime2 = $record['CHECKTIME2'];
            $rd_memo = $record['MEMO'];
            $rd_answer = $record['ANSWER'];
            $rd_regdate = $record['REGDATE'];
            $rd_status = $record['STATUS'];
            $rd_ok_date = $record['OK_DATE'];
            $rd_ok_name = $record['OK_NAME'];

            $status_str = array('ING'=>'ó����', 'CANCEL'=>'�ݷ�', 'OK'=>'����');
        }

        if($mode == "VIEW") {
            $gubun1_disabled = " disabled='disabled'";
            $gubun2_disabled = " disabled='disabled'";
            $memo_disabled = " disabled";
        }

        if(!$rd_gubun1) $rd_gubun1 = $md_gubun1;
        if(!$rd_gubun2) $rd_gubun2 = $md_gubun2;
        if(!$rd_checktime1) $rd_checktime1 = $md_checktime1;
        if(!$rd_checktime2) $rd_checktime2 = $md_checktime2;
        ?>
<!--���� ���� ��� �˾�-->
        <form class="inlp" method='post' name='form'>
        <input type="hidden" name="prs_login" value="<?=$prs_login?>">
        <input type="hidden" name="prs_name" value="<?=$prs_name?>">
        <input type="hidden" name="id" value="<?=$p_id?>">
        <input type="hidden" name="date" value="<?=$p_date?>">
        <input type="hidden" name="flag">
        <input type="hidden" name="mode">
        <input type="hidden" name="gubun" value="">
        <input type="hidden" name="gubun1" value="">
        <input type="hidden" name="gubun1_prev" value="<?=$rd_checktime1?>">
        <div class="modal" id="popDayEdit">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title is-size-6"><span id="t_month"></span>�� <span id="t_day"></span>&nbsp;���� ����</p>
                    <a class="delete" aria-label="close" href="javascript:closePop('DayEdit');" ></a>
                </header>
                <iframe id="popDayEditFrm"  style="border-bottom-left-radius: 6px; border-bottom-right-radius: 6px;" height="371" scrolling="no" frameborder="0"></iframe>
            </div>
        </div>
    </section>
    </form>
    <!-- ���� �� -->
 <? include INC_PATH."/bottom.php"; ?>
    </div>
</form>
</body>
</html>



