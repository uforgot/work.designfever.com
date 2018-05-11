<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	$prs_position_tmp = (in_array($prs_id,$positionC_arr)) ? "팀장" : "";	//팀장대리 판단

	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d"); 
	$date_arr = explode("-",$date);
	$p_year = $date_arr[0];
	$p_month = $date_arr[1];
	$p_day = $date_arr[2];

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }
	if (strlen($p_day) == "1") { $p_day = "0".$p_day; }

	$NowDate = date("Y-m-d");
	$PrevDate = date("Y-m-d",strtotime ("-1 day", strtotime($date)));
	$NextDate = date("Y-m-d",strtotime ("+1 day", strtotime($date)));

	//회의실 예약 카운트
	$sql = "EXEC SP_BOOKING_LIST_01 '$date'";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	if (sizeof($record) > 0)
	{
		$total = $record['TOTAL'];				//총 예약건수
		$total_room1 = $record['TOTAL_ROOM1'];	//ROOM1 예약건수
		$total_room2 = $record['TOTAL_ROOM2'];	//ROOM2 예약건수
		$total_room3 = $record['TOTAL_ROOM3'];	//ROOM3 예약건수
		$total_room4 = $record['TOTAL_ROOM4'];	//ROOM4 예약건수
		$total_room5 = $record['TOTAL_ROOM5'];	//ROOM5 예약건수

		if ($total == "") { $total = "0"; }
		if ($total_room1 == "") { $total_room1 = "0"; }
		if ($total_room2 == "") { $total_room2 = "0"; }
		if ($total_room3 == "") { $total_room3 = "0"; }
		if ($total_room4 == "") { $total_room4 = "0"; }
		if ($total_room5 == "") { $total_room5 = "0"; }
	}

	// 회의실 예약 리스트
	$listSQL = "SELECT
					SEQNO, PRS_NAME, TITLE, ROOM, DATE, S_TIME, E_TIME, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE
				FROM 
					DF_BOOKING WITH(NOLOCK)
				WHERE 
					DATE = '$date'
				ORDER BY 
					ROOM, S_TIME";
	$listRs = sqlsrv_query($dbConn,$listSQL);

	while ($listRow = sqlsrv_fetch_array($listRs))
	{
		$booking_seqno = $listRow['SEQNO'];
		$booking_room = $listRow['ROOM'];
		$booking_stime = $listRow['S_TIME'];
		$booking_etime = $listRow['E_TIME'];
		$booking_title = $listRow['TITLE'];
		$booking_name = $listRow['PRS_NAME'];
		$booking_line = (strtotime($booking_etime)-strtotime($booking_stime))/1800;

		for($i=0;$i<$booking_line;$i++) {
			$booking_time = date("H:i",strtotime($booking_stime)+(1800*$i));
	
			if($i==0) {
				//$booking_info = "<a href=\"./booking_write.php?type=modify&date=$date&seqno=$booking_seqno\"><span style='color:#000;font-weight:bold;'>".$booking_stime." ~ ".$booking_etime."</span>";
                //$booking_info.= "&nbsp;(예약자: ".$booking_name.")";
                //$booking_memo = "+ ".$booking_title;
                $booking_info = "<a href='./booking_write.php?type=modify&date=$date&seqno=$booking_seqno'><span class='booking tooltip' data-tooltip='예약자 $booking_name'  style='height:12rem'><p>$booking_title</p></span></a>";
				$booking_start = true;
			} else {
				$booking_info = "";
				$booking_memo = "";
				$booking_start = false;
			}

			$Data[$booking_room][$booking_time] = array(
														"seqno"=>$booking_seqno, 
														"info"=>$booking_info,
														"memo"=>$booking_memo,
														"start"=>$booking_start,
														"line"=>$booking_line
													);
		}
	}

	// 타임라인 출력 내용
	function getBookingInfo($info, $memo, $line) {
		if($line == 1) $len = 36;
		else if($line == 2) $len = 110;
		else if($line >= 3) $len = 190;

		$memo = getCutString($memo, $len);

		return $info."<br>".$memo."</a>";
	}
?>
<!--<td class='booking tooltip' data-tooltip='예약자 정지민'  style='height:12rem'>
                                <p>+ Flexible UX 시나리오 내부 회의</p>
                            </td>
-->

<? include INC_PATH."/top.php"; ?>


<script type="text/javascript">
	function sSubmit(f)
	{	
		var frm = document.form1;
		frm.date.value = f.year.value + "-" + f.month.value + "-" + f.day.value;
		frm.submit();
	}
	//전월보기
	function preDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$PrevDate?>";
		frm.submit();
	}
	//다음월보기
	function nextDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$NextDate?>";
		frm.submit();
	 }
</script>
</head>
<body>
<form method="get" name="form">
    <? include INC_PATH."/top_menu.php"; ?>
    <? include INC_PATH."/booking_menu.php"; ?>

    <!-- 본문 시작 -->
    <section class="section df-booking">
        <div class="container">
            <div class="content">
                <div class="calendar is-large">
                    <div class="calendar-nav">
                        <div class="calendar-nav-previous-month">
                            <a href="javascript:preDay();" class="button is-text is-small is-primary">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                        </div>
                        <div>
                            <div class="field is-group">
                                <div class="control select">
                                    <select name="year" onchange='sSubmit(this.form)'>
                                        <?
                                        for ($i=$startYear; $i<=($p_year+1); $i++)
                                        {
                                            if ($i == $p_year)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$i."'".$selected.">".$i."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="month" onchange='sSubmit(this.form)'>
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

                                            echo "<option value='".$j."'".$selected.">".$i."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="day" onchange='sSubmit(this.form)'>
                                        <?
                                        $last_day = date("t", mktime(0, 0, 0, $p_month, '01', $p_year));

                                        for ($i=1; $i<=$last_day; $i++)
                                        {
                                            if (strlen($i) == "1")
                                            {
                                                $j = "0".$i;
                                            }
                                            else
                                            {
                                                $j = $i;
                                            }

                                            if ($j == $p_day)
                                            {
                                                $selected = " selected";
                                            }
                                            else
                                            {
                                                $selected = "";
                                            }

                                            echo "<option value='".$j."'".$selected.">".$i."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="calendar-nav-next-month">
                            <a href="javascript:nextDay();" class="button is-text is-small is-primary">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="hr-strong">

            <div class="content">
                <div class="columns">
                    <div class="column">
                        <table class="table is-fullwidth is-hoverable">
                            <thead>
                            <td>
                                <p><img src="/assets/images/room_01.jpg"></p>
                                <p class="title is-size-6">회의실 1</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                            $time = date("H:i",$i);

                            $room1_style = "booking_none";

                                    $room1_btn = "";
                                    $room1_info = "";

                                if($Data['ROOM1'][$time]['seqno']) {
                                    $room1_style = "booking";
                                    if($Data['ROOM1'][$time]['start']) {
                                        $room1_style = " booking_first";
                                        $room1_line = $Data['ROOM1'][$time]['line'];
                                        $room1_info = getBookingInfo($Data['ROOM1'][$time]['info'],$Data['ROOM1'][$time]['memo'],$room1_line);
                                    }
                                } else {
                                    if($NowDate <= $date) {
                                        $room1_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM1';\"";
                                        $room1_style .=" cursor";
                                    }
                                }
                            ?>
                                <tr>
                                    <!--<td width="20%" class="<?=$room1_style?>" <?=$room1_btn?> style="position:relative; cursor: pointer;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$time?><?=$room1_info?></div></td>-->
                                    <td style="position:relative; cursor: pointer;"><?=$time?><?=$room1_info?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="column">
                        <table class="table is-fullwidth is-hoverable">
                            <thead>
                            <td>
                                <p><img src="/assets/images/room_02.jpg"></p>
                                <p class="title is-size-6">회의실 1</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                            $time = date("H:i",$i);

                            $room2_style = "booking_none";
                            $room2_btn = "";
                            $room2_info = "";


                            if($Data['ROOM2'][$time]['seqno']) {
                                $room2_style = "booking";
                                if($Data['ROOM2'][$time]['start']) {
                                    $room2_style = " booking_first";
                                    $room2_line = $Data['ROOM2'][$time]['line'];
                                    $room2_info = getBookingInfo($Data['ROOM2'][$time]['info'],$Data['ROOM2'][$time]['memo'],$room2_line);
                                }
                            } else {
                                if($NowDate <= $date) {
                                    $room2_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM2';\"";
                                    $room2_style .=" cursor";
                                }
                            }
                            ?>
                            <tr>
                                <td width="20%" class="<?=$room2_style?>" <?=$room2_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$time?><?=$room2_info?></div></td>
                            </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="column">
                        <table class="table is-fullwidth is-hoverable">
                            <thead>
                            <td>
                                <p><img src="/assets/images/room_03.jpg"></p>
                                <p class="title is-size-6">회의실 1</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                            $time = date("H:i",$i);

                            $room3_style = "booking_none";
                            $room3_btn = "";
                            $room3_info = "";

                            if($Data['ROOM3'][$time]['seqno']) {
                                $room3_style = "booking";
                                if($Data['ROOM3'][$time]['start']) {
                                    $room3_style = " booking_first";
                                    $room3_line = $Data['ROOM3'][$time]['line'];
                                    $room3_info = getBookingInfo($Data['ROOM3'][$time]['info'],$Data['ROOM3'][$time]['memo'],$room3_line);
                                }
                            } else {
                                if($NowDate <= $date) {
                                    $room3_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM3';\"";
                                    $room3_style .=" cursor";
                                }
                            }

                            ?>
                            <tr>
                                <td width="20%" class="<?=$room3_style?>" <?=$room3_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$time?><?=$room3_info?></div></td>
                            </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="column">
                        <table class="table is-fullwidth is-hoverable">
                            <thead>
                            <td>
                                <p><img src="/assets/images/room_04.jpg"></p>
                                <p class="title is-size-6">회의실 1</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                            $time = date("H:i",$i);

                            $room4_style = "booking_none";
                            $room4_btn = "";
                            $room4_info = "";


                            if($Data['ROOM4'][$time]['seqno']) {
										$room4_style = "booking";
										if($Data['ROOM4'][$time]['start']) {
                                            $room4_style = " booking_first";
                                            $room4_line = $Data['ROOM4'][$time]['line'];
                                            $room4_info = getBookingInfo($Data['ROOM4'][$time]['info'],$Data['ROOM4'][$time]['memo'],$room4_line);
                                        }
									} else {
                                if($NowDate <= $date) {
                                    $room4_btn = "onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM4';\"";
                                    $room4_style .=" cursor";
                                }
                            }
                            ?>
                            <tr>
                                <td width="20%" class="<?=$room4_style?>" <?=$room4_btn?> style="position:relative;"><div style="position:absolute; z-index:10; top:1px; left:10px;"><?=$time?><?=$room4_info?></div></td>
                            </tr>
                            <? } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- 본문 끌 -->


</form>

<form method="get" name="form1">
	<input type="hidden" name="date">
</form>

<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>