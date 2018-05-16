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
    $mark7 ="";
    $mark8 ="";
    $mark9 ="";
    $mark10 ="";

    $calendar_events1 =""; //��ٽð�
    $calendar_events2 =""; //��ٽð�
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
            <div class="content is-hidden-mobile">
                <div class="field is-grouped is-grouped-multiline">
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag">�� �ٹ��ð�</span>
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

                            if ($col_date == $nowYear.$nowMonth.$nowDay)  { //���� ��¥ ǥ��
                                $div_class1=" is-today";
                            }else if($col_date_name != "") { //������ ǥ��
                                $div_class2=" is-holiday";
                                $div_class3="";
                                $mark1 ="<span class='button is-small is-static'>". $col_date_name ."</span>";
                            } else if ($col_day == "SAT" || $col_day == "SUN") { //�� �Ͽ���
                                $div_class2=" is-holiday";
                                $div_class3="";
                                $mark1 = "";
                            }else if ($col_gubun1 == "10") {	//�ް� - ���/��� �ð� ǥ�� ���� - ���� 00:00��� 24:00������� �����Ǿ� ����
                                $div_class2= "";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>�ް�</span>";
                            }else if ($col_gubun1 == "11") {	//����
                                $div_class2= "";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>����</span>";
                            }else if ($col_gubun1 == "12") {	//������
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>������</span>";
                            }else if ($col_gubun1 == "13") { //��Ÿ
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>��Ÿ</span>";
                            }else if ($col_gubun1 == "14") { //���
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>���</span>";
                            }else if ($col_gubun1 == "15") {	//����/�Ʒ�
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>����/�Ʒ�</span>";
                            }else if ($col_gubun1 == "16") {	//������Ʈ �ް�
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>������Ʈ �ް�</span>";
                            }else if ($col_gubun1 == "17") {	//�������� �ް�
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>�������� �ް�</span>";
                            }else if ($col_gubun1 == "18") {	//���� �ް�
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>���� �ް�</span>";
                            }else if ($col_gubun1 == "19") {	//�ι���/����
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>�ι���/����</span>";
                            }else if ($col_gubun1 == "20") {	//����ް�
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>����ް�</span>";
                            }else if ($col_gubun1 == "21") {	//��������
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'>��������</span>";
                            }else if ($col_gubun1 == "0") {//���Ĺ��� ����. �����üũ X
                                $div_class2="";
                                $div_class3=" is-vacation";
                                $mark1="<span class='button is-small is-static'></span>";
                            }else{
                                $div_class1="";
                                $div_class2="";
                                $div_class3="";
                                $mark1 ="";
                            }

                            if ($col_gubun1 == "1")			//���
                            {
                               
                                if ($checktime1 != "" && substr($checktime1,0,2) < "08") {

                                } else  {

                                }

                            }

                            if($end_day == $count) { //�������� div Ŭ���� ǥ��
                                $div_class4 = " is-last";
                            }

                            //���¼��� ��ư �߰�(�ֱ� ������ ���)
                            if($interval->days <= 7 && $col_date <= $nowYear.$nowMonth.$nowDay)
                            {
                                if (!$col_edit_status)
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\">������û</a>";
                                }
                                else if ($col_edit_status == "ING")	// ��û��
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">��û��</a>";
                                }
                                else if ($col_edit_status == "CANCEL")	// �ݷ�
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">�ݷ�</a>";

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\">���û</a>";
                                }
                                else if ($col_edit_status == "OK")	// ����
                                {

                                    $mark2= "<a class='button is-small is-primary' href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','VIEW');\">����</a>";

                                    //echo "<a href=\"javascript:ShowPopCustom('DayEdit','".$prs_id."','".$p_year."','".$p_month."','".str_pad($count,"2","0",STR_PAD_LEFT)."','INSERT');\"><span class='day_edit_btn' style='left:45px; background:#000; color:#FFF;'>���û</span></a>";
                                }
                            }else{
                                $mark2="";
                            }

                            /*��¥ ��� �κ�*/
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

<!--���� ���� ��� �˾�-->
        <div class="modal">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title is-size-6">4�� 20�� ���� ����</p>
                    <button class="delete" aria-label="close"></button>
                </header>

                <section class="modal-card-body modal-commuting-modify">

                    <div class="content">
                        <div class="columns is-mobile">
                            <div class="column">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">���</label>
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
                                        <label class="label">���</label>
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
                                        <textarea class="textarea" placeholder="����" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <textarea class="textarea" placeholder="���" rows="2"></textarea>
                                    </div>
                                    <p class="help is-danger">
                                        * ���� ������� �ƴϸ�, �Խ��ǿ� ��û ���
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="cal_value" name="cal_value" value="">  	<!--�������� ����� ä���� ��-->
                </section>

                <footer class="modal-card-foot">
                    <button class="button is-primary">Ȯ��</button>
                    <button class="button">���</button>
                </footer>
            </div>
        </div>

    </section>
    <!-- ���� �� -->


    <? include INC_PATH."/bottom.php"; ?>
    </div>
</form>
</body>
</html>



