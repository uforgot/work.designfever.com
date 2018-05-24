<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
?>

<?
	//���� üũ
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("�ش��������� �ӿ�,�����ڸ� Ȯ�� �����մϴ�.");
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
<!--������ ���̴� CSS-->
<link rel="stylesheet" href="/assets/css/style_20180406.css" />
<link rel="stylesheet" href="/assets/css/jquery-ui.css" />
<!--������ ���̴� CSS-->

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
                            $count = 0;
                            $lastday = 0;
                            $day_cnt = 0;
                            $pre_date = $Pre;

                            //�޷� ������ ��üũ
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

                            //�޷� ������ �ڵ�����
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
									AND (MEMO3 IS NULL OR (SELECT FORM_TITLE FROM DF_APPROVAL WHERE DOC_NO = REPLACE(REPLACE(MEMO3,'���ڰ��� (',''),')','')) != '��������')
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

                            //�޷� ������ �ް���
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
									FORM_CATEGORY = '�ް���' 
									AND USE_YN = 'Y' AND STATUS IN ('�̰���','������','����','����')
									AND (CONVERT(CHAR(7),START_DATE,120) <= '". $date ."' OR CONVERT(CHAR(7),END_DATE,120) >= '". $date ."') 
							) B
						ON
							A.DATE BETWEEN REPLACE(B.START_DATE,'-','') AND REPLACE(B.END_DATE,'-','')
						ORDER BY 
							A.DATE, 						
							CASE B.FORM_TITLE WHEN '����' THEN 1 WHEN '������Ʈ' THEN 2 WHEN '��������' THEN 3 WHEN '���Ĺ���' THEN 4 
							WHEN '������Ʈ ��������' THEN 5 WHEN '������Ʈ ���Ĺ���' THEN 6 WHEN '����' THEN 7 WHEN '��������' THEN 8
							WHEN '����' THEN 9 WHEN '������' THEN 10 WHEN '����/�ι���' THEN 11 WHEN '��Ÿ' THEN 12 
							WHEN '�ް� ������' THEN 13 WHEN '�ް� ������ ��������' THEN 14 WHEN '�ް� ������ ���Ĺ���' THEN 15 
							WHEN '����ް�' THEN 16 WHEN '��������' THEN 17 END,					
							B.PRS_NAME
				";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $vac_date = $record['DATE'];					//��¥
                                $vac_doc_no = $record['DOC_NO'];				//������ȣ
                                $vac_form_title = $record['FORM_TITLE'];		//�ް�����
                                $vac_prs_name = $record['PRS_NAME'];			//������

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

                            //�޷� ������ �����
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
									FORM_CATEGORY = '�����' 
									AND USE_YN = 'Y' AND STATUS IN ('�̰���','������','����','����')
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
                                $early_date = $record['DATE'];					//��¥
                                $early_doc_no = $record['DOC_NO'];				//������ȣ
                                $early_prs_name = $record['PRS_NAME'];			//������

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

                            //�޷� ������ �ٰܱ�/�İ߰�/�����
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
									(FORM_CATEGORY = '�ٰܱ�/�İ߰�' OR FORM_CATEGORY = '�����') 
									AND USE_YN = 'Y' AND STATUS IN ('�̰���','������','����','����')
									AND (CONVERT(CHAR(7),START_DATE,120) <= '". $date ."' OR CONVERT(CHAR(7),END_DATE,120) >= '". $date ."') 
							) B
						ON
							A.DATE BETWEEN REPLACE(B.START_DATE,'-','') AND REPLACE(B.END_DATE,'-','')
						ORDER BY 
							A.DATE, 
							CASE FORM_CATEGORY WHEN '�ٰܱ�/�İ߰�' THEN 1 WHEN '�����' THEN 2 END,
							B.PRS_NAME
				";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while ($record = sqlsrv_fetch_array($rs))
                            {
                                $out_date = $record['DATE'];					//��¥
                                $out_doc_no = $record['DOC_NO'];				//������ȣ
                                $out_prs_name = $record['PRS_NAME'];			//������

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

                            //�޷� ������
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
                                $col_date = $record['DATE'];					//��¥
                                $col_datekind = $record['DATEKIND'];			//������ ����
                                $col_day = $record['DAY'];						//����
                                $col_date_name = $record['DATE_NAME'];			//�����

                                if ($pre_date != $col_date) {
                                    $count++;
                                    $chk = "Y";
                                    $chg = "Y";
                                }
                                else {
                                    $chk = "N";
                                }

                                $pre_date = $col_date;

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



                                if ($col_date == $nowYear.$nowMonth.$nowDay)  { //���� ��¥ ǥ��
                                    $div_class1=" is-today";
                                }else if($col_date_name != "") { //������ ǥ��
                                    $div_class1="";
                                    $div_class2=" is-holiday";
                                    $div_class3="";
                                    $calendar_events0= "<p class='calendar-event is-dark'>". $col_date_name ."</p>";
                                } else if ($col_day == "SAT" || $col_day == "SUN") { //�� �Ͽ���
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
                                        $calendar_events1 ="<p class='calendar-event is-light'>���¹�üũ</p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events1_1 = str_replace("//","<br>",$not_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($auto_name_arr[$count] != "") {
                                        $calendar_events2 ="<p class='calendar-event is-danger'>�ڵ����� </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events2_2 = str_replace("//","<br>",$auto_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($vac_name_arr[$count] != "") {
                                        $calendar_events3 ="<p class='calendar-event is-primary'>�ް� </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events3_3 = str_replace("//","<br>",$vac_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($early_name_arr[$count] != "") {
                                        $calendar_events4 ="<p class='calendar-event is-light'>���� </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events4_4 = str_replace("//","<br>",$early_name_arr[$count]) ."</span><br>";
                                    }
                                    if ($out_name_arr[$count] != "") {
                                        $calendar_events5 ="<p class='calendar-event is-warning'>�ܱ�/�İ�/���� </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events5_5 =str_replace("//","<br>",$out_name_arr[$count]) ."</span><br>";
                                    }
                                }
                                else {
                                    if ($out_name_arr[$count] != "") {
                                        $calendar_events5 ="<p class='calendar-event is-warning'>�ܱ�/�İ�/���� </p><span class='is-size-7 has-text-grey'>";
                                        $calendar_events5_5 = str_replace("//","<br>",$out_name_arr[$count]) ."</span><br>";
                                    }
                                }


                                //�������� div Ŭ���� ǥ��
                                if($end_day == $count) {
                                    $div_class4 = " is-last";
                                }
                                /*��¥ ��� �κ�*/
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
</form>
<? include INC_PATH."/bottom.php"; ?>

<div id="popDetail" class="approval-popup2" style="display:none;">
    <div class="title">
        <h3 class="aaa">���繮�� ����</h3>
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
        <p class="pop_title">����α�</p>
        <a href="javascript:HidePop('Log');" class="close"><img src="/img/btn_popup_close.gif" alt="�ݱ�">�ݱ�</a>
    </div>
    <div class="pop_body" id="pop_log_body">
    </div>
</div>

</body>
</html>