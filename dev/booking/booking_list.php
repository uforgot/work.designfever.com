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
					SEQNO,PRS_TEAM, PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME, TITLE, ROOM, DATE, S_TIME, E_TIME, CONVERT(CHAR(10),REG_DATE,120) AS REG_DATE
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
    $booking_position = $listRow['PRS_POSITION'];
    $booking_name = $listRow['PRS_NAME'];
    $booking_id = $listRow['PRS_ID'];
    $booking_login = $listRow['PRS_LOGIN'];
    $booking_team = $listRow['PRS_TEAM'];
    $booking_line = (strtotime($booking_etime)-strtotime($booking_stime))/1800;
    $booking_height= 2;
    for($i=0;$i<$booking_line;$i++) {
        $booking_time = date("H:i",strtotime($booking_stime)+(1800*$i));
        $booking_height = $booking_height++;
        if($i==0) { //예약수정
            $booking_info = "<p style='cursor:pointer' onclick=\"javascript:modPop('modify','$date','$booking_seqno','$booking_room','$booking_stime','$booking_etime','$booking_title','$booking_position','$booking_name','$booking_id','$booking_login','$booking_team')\"> + $booking_title</p>";
            //$booking_info = "<p style='cursor:pointer' onclick='location.href=\"./booking_write.php?type=modify&date=$date&seqno=$booking_seqno\"' class='booking tooltip' data-tooltip='예약자 $booking_name'> + $booking_title</p>";
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
            "line"=>$booking_line,
            "name"=>$booking_name
        );
    }
}

// 타임라인 출력 내용
function getBookingInfo($info, $memo, $line) {
    if($line == 1) $len = 36;
    else if($line == 2) $len = 110;
    else if($line >= 3) $len = 190;

    $memo = getCutString($memo, $len);

    return $info."".$memo."</a>";
}

$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d");
$time = isset($_REQUEST['time']) ? $_REQUEST['time'] : null;
$room = isset($_REQUEST['room']) ? $_REQUEST['room'] : null;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "write";
$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;

$booking_id = $prs_id;
$booking_name = $prs_name;
$booking_login = $prs_login;
$booking_team = $prs_team;
$booking_position = $prs_position;

?>
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

    function writePop(type,date,stime,room,etime){ //작성창
        $("#btnTxt").text('작성');
        $("#type").val('write');
        $("#date").val('<?=$date?>');
        $("#seqno").val('');
        $("#writer").val('<?=$prs_login?>');
        $("#writer_id").val('<?=$prs_id?>');
        $("#writer_name").val('<?=$prs_name?>');
        $("#writer_team").val('<?=$prs_team?>');
        $("#writer_position").val('<?=$prs_position?>');
        $("#title").val('');

        $("#b_date").text('<?=$date?>');
        $("#b_position").text('<?=$prs_position?>');
        $("#b_name").text('<?=$prs_name?>');

        var s_hour= stime.substring(0,2) //2013
        var s_min = stime.substring(3,5) //06
        var e_hour= etime.substring(0,2) //2013
        var e_min= etime.substring(3,5) //06
        $("#room_name").val(room).prop("selected", true);
        $("#s_hour").val(s_hour).prop("selected", true);
        $("#s_min").val(s_min).prop("selected", true);
        $("#e_hour").val(e_hour).prop("selected", true);
        $("#e_min").val(e_min).prop("selected", true);

        $("#inputPopUp").addClass("modal is-active");
    }

    function modPop(type,date,seqno,room,stime,etime,title,position,prs_name,prs_id,login,team) { //수정창
        $("#btnTxt").text('수정');
        $("#type").val('modify');
        $("#date").val(date);
        $("#seqno").val(seqno);
        $("#writer").val(login);
        $("#writer_id").val(prs_id);
        $("#writer_name").val(prs_name);
        $("#writer_team").val(team);
        $("#writer_position").val(position);
        $("#title").val(title);

        $("#b_date").text(date);
        $("#b_position").text(position);
        $("#b_name").text(prs_name);

        var s_hour = stime.substring(0, 2) //2013
        var s_min = stime.substring(3, 5) //06
        var e_hour = etime.substring(0, 2) //2013
        var e_min = etime.substring(3, 5) //06
        $("#room_name").val(room).prop("selected", true);
        $("#s_hour").val(s_hour).prop("selected", true);
        $("#s_min").val(s_min).prop("selected", true);
        $("#e_hour").val(e_hour).prop("selected", true);
        $("#e_min").val(e_min).prop("selected", true);

        //수정버튼 생성
        if($("#writer_id").val() == $("#prs_id").val())
        {
            $("#btnDel").css("display", "inline");
        }
        $("#inputPopUp").addClass("modal is-active");
    }

    function funWrite()
    {
        var frm = document.form_pop;

        if(frm.room_name.value == ""){
            alert("회의실을 선택해주세요");
            frm.room_name.focus();
            return;
        }

        var s_time = frm.s_hour.value + ":" + frm.s_min.value;
        var e_time = frm.e_hour.value + ":" + frm.e_min.value;

        if(e_time <= s_time) {
            alert("회의 종료시간을 올바르게 지정해 주세요.");
            frm.e_hour.focus();
            return;
        }

        if(frm.title.value == ""){
            alert("내용을 입력해주세요");
            frm.title.focus();
            return;
        }

        //내용 유효성 검사 할 부분
        if($("#btnTxt").text() == '수정') {
            if (confirm("예약을 수정 하시겠습니까")) {
                frm.target = "hdnFrame";
                frm.action = 'booking_write_act.php';
                frm.submit();
            }
        }else{
            if (confirm("예약을 등록 하시겠습니까")) {
                frm.target = "hdnFrame";
                frm.action = 'booking_write_act.php';
                frm.submit();
            }

        }
    }

    function funDelete()
    {
        var frm = document.form_pop;

        //내용 유효성 검사 할 부분
        if(confirm("예약을 삭제 하시겠습니까")){
            frm.type.value = "delete";
            frm.target = "hdnFrame";
            frm.action = 'booking_write_act.php';
            frm.submit();
        }
    }

    $(document).ready(function() {
        $("#popup_close").attr("style", "cursor:pointer;").click(function () {
            $("#inputPopUp").removeClass("is-active");
        });
    });
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

                                            echo "<option value='".$i."'".$selected.">".$i."년</option>";
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

                                            echo "<option value='".$j."'".$selected.">".$i."월</option>";
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

                                            echo "<option value='".$j."'".$selected.">".$i."일</option>";
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
                                <p><img src="/assets/images/room_03.jpg"></p>
                                <p class="title is-size-6">회의실 3 (2F)</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                                $room_height= 2; //예약된 시간 라인 height값 기본 2rem 시간 추가시 2rem씩 늘어남
                                $time = date("H:i",$i);
                                $room3_style = "";
                                $room3_btn = "";
                                $room3_info = "";
                                if($Data['ROOM3'][$time]['seqno']) {
                                    $room3_style = "none";
                                    if($Data['ROOM3'][$time]['start']) {
                                        $room3_style = "booking tooltip";
                                        $room3_line = $Data['ROOM3'][$time]['line'];
                                        $room3_info = getBookingInfo($Data['ROOM3'][$time]['info'],$Data['ROOM3'][$time]['memo'],$room3_line);
                                        $room_height= $room_height * $room3_line;
                                        $room3_tooltip="data-tooltip='예약자 ".$Data['ROOM3'][$time]['name']."'";
                                        $time="";
                                        ?>
                                        <tr>
                                            <td class="<?=$room3_style?>" <?=$room3_tooltip?> <?=$room3_btn?>style="height:<?=$room_height?>rem"><?=$time?><?=$room3_info?></td>
                                        </tr>
                                        <?
                                    }
                                    $time="";
                                    ?>
                                    <?
                                } else {
                                    if($NowDate <= $date) {
                                        //$room3_btn = "style='cursor:pointer'; onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM3';\"";
                                        $booking_etime = date("H:i",strtotime ("+30 minutes", strtotime($time)));
                                        $room3_btn = "style='cursor:pointer'; onclick=\"javascript:writePop('write','$date','$time','ROOM3','$booking_etime')\" ";
                                    }
                                    ?>
                                    <tr>
                                        <td class="<?=$room3_style?>" <?=$room3_btn?>><?=$time?><?=$room3_info?></td>
                                    </tr>
                                    <?
                                }
                                ?>

                            <? } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="column">
                        <table class="table is-fullwidth is-hoverable">
                            <thead>
                            <td>
                                <p><img src="/assets/images/room_04.jpg"></p>
                                <p class="title is-size-6">회의실 4 (2F)</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                                $room_height= 2; //예약된 시간 라인 height값 기본 2rem 시간 추가시 2rem씩 늘어남
                                $time = date("H:i",$i);
                                $room4_style = "";
                                $room4_btn = "";
                                $room4_info = "";
                                if($Data['ROOM4'][$time]['seqno']) {
                                    $room4_style = "none";
                                    if($Data['ROOM4'][$time]['start']) {
                                        $room4_style = "booking tooltip";
                                        $room4_line = $Data['ROOM4'][$time]['line'];
                                        $room4_info = getBookingInfo($Data['ROOM4'][$time]['info'],$Data['ROOM4'][$time]['memo'],$room4_line);
                                        $room_height = $room_height * $room4_line;
                                        $room4_tooltip="data-tooltip='예약자 ".$Data['ROOM4'][$time]['name']."'";
                                        $time="";
                                        ?>
                                        <tr>
                                            <td class="<?=$room4_style?>" <?=$room4_tooltip?> <?=$room4_btn?>style="height:<?=$room_height?>rem"><?=$time?><?=$room4_info?></td>
                                        </tr>
                                        <?
                                    }
                                    $time="";
                                    ?>
                                    <?
                                } else {
                                    if($NowDate <= $date) {
                                        $booking_etime = date("H:i",strtotime ("+30 minutes", strtotime($time)));
                                        //$room4_btn = "style='cursor:pointer'; onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM4';\"";
                                        $room4_btn = "style='cursor:pointer'; onclick=\"javascript:writePop('write','$date','$time','ROOM4','$booking_etime')\" ";
                                    }
                                    ?>
                                    <tr>
                                        <td class="<?=$room4_style?>" <?=$room4_btn?>><?=$time?><?=$room4_info?></td>
                                    </tr>
                                    <?
                                }
                                ?>

                            <? } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="column">
                        <table class="table is-fullwidth is-hoverable">
                            <thead>
                            <td>
                                <p><img src="/assets/images/room_01.jpg"></p>
                                <p class="title is-size-6">회의실 1 (3F)</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                                $room_height= 2; //예약된 시간 라인 height값 기본 2rem 시간 추가시 2rem씩 늘어남
                                $time = date("H:i",$i);
                                $room1_style = "";
                                $room1_btn = "";
                                $room1_info = "";
                                if($Data['ROOM1'][$time]['seqno']) {
                                    $room1_style = "none";
                                    if($Data['ROOM1'][$time]['start']) {
                                        $room1_style = "booking tooltip";
                                        $room1_line = $Data['ROOM1'][$time]['line'];
                                        $room1_info = getBookingInfo($Data['ROOM1'][$time]['info'],$Data['ROOM1'][$time]['memo'],$room1_line);
                                        $room_height = $room_height * $room1_line;
                                        $room1_tooltip="data-tooltip='예약자 ".$Data['ROOM1'][$time]['name']."'";
                                        $time="";
                                        ?>
                                        <tr>
                                            <td class="<?=$room1_style?>" <?=$room1_tooltip?> <?=$room1_btn?>style="height:<?=$room_height?>rem"><?=$time?><?=$room1_info?></td>
                                        </tr>
                                        <?
                                    }
                                    $time="";
                                    ?>
                                    <?
                                } else {
                                    if($NowDate <= $date) {
                                        $booking_etime = date("H:i",strtotime ("+30 minutes", strtotime($time)));
                                        //$room1_btn = "style='cursor:pointer'; onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM1';\"";
                                        $room1_btn = "style='cursor:pointer'; onclick=\"javascript:writePop('write','$date','$time','ROOM1','$booking_etime')\" ";
                                    }
                                    ?>
                                    <tr>
                                        <td class="<?=$room1_style?>" <?=$room1_btn?>><?=$time?><?=$room1_info?></td>
                                    </tr>
                                    <?
                                }
                                ?>

                            <? } ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="column">
                        <table class="table is-fullwidth is-hoverable">
                            <thead>
                            <td>
                                <p><img src="/assets/images/room_02.jpg"></p>
                                <p class="title is-size-6">회의실 2 (4F)</p>
                            </td>
                            </thead>
                            <tbody>
                            <?
                            for($i=1441580400; $i<=1441635400; $i=$i+1800)
                            {
                                $room_height= 2; //예약된 시간 라인 height값 기본 2rem 시간 추가시 2rem씩 늘어남
                                $time = date("H:i",$i);
                                $room2_style = "";
                                $room2_btn = "";
                                $room2_info = "";
                                if($Data['ROOM2'][$time]['seqno']) {
                                    $room2_style = "none";
                                    if($Data['ROOM2'][$time]['start']) {
                                        $room2_style = "booking tooltip";
                                        $room2_line = $Data['ROOM2'][$time]['line'];
                                        $room2_info = getBookingInfo($Data['ROOM2'][$time]['info'],$Data['ROOM2'][$time]['memo'],$room2_line);
                                        $room_height = $room_height * $room2_line;
                                        $room2_tooltip="data-tooltip='예약자 ".$Data['ROOM2'][$time]['name']."'";
                                        $time="";
                                        ?>
                                        <tr>
                                            <td class="<?=$room2_style?>" <?=$room2_tooltip?> <?=$room2_btn?>style="height:<?=$room_height?>rem"><?=$time?><?=$room2_info?></td>
                                        </tr>
                                        <?
                                    }
                                    $time="";
                                    ?>
                                    <?
                                } else {
                                    if($NowDate <= $date) {
                                        $booking_etime = date("H:i",strtotime ("+30 minutes", strtotime($time)));
                                        //$room2_btn = "style='cursor:pointer'; onclick=\"location.href='./booking_write.php?type=write&date=$date&time=$time&room=ROOM2';\"";
                                        $room2_btn = "style='cursor:pointer'; onclick=\"javascript:writePop('write','$date','$time','ROOM2','$booking_etime')\" ";
                                    }
                                    ?>
                                    <tr>
                                        <td class="<?=$room2_style?>" <?=$room2_btn?>><?=$time?><?=$room2_info?></td>
                                    </tr>
                                    <?
                                }
                                ?>

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

    <!-- 회의실 예약 레이어팝업 -->

    <form method="post" name="form_pop" action="booking_write_act.php">
        <input type="hidden" name="type" id="type" value="">						<!-- 등록수정삭제구분 -->
        <input type="hidden" name="date" value="">						            <!-- 날짜 -->
        <input type="hidden" name="seqno" id="seqno" value="">						<!-- 글번호 -->
        <input type="hidden" name="writer" id="writer" value="">				        <!-- 글작성자 prs_login -->
        <input type="hidden" name="writer_id" id="writer_id" value="">				    <!-- 글작성자 prs_id -->
        <input type="hidden" name="writer_name" id="writer_name" value="">			    <!-- 글작성자 prs_name -->
        <input type="hidden" name="writer_team" id="writer_team" value="">			    <!-- 글작성자 prs_team -->
        <input type="hidden" name="writer_position" id="writer_position" value="">	    <!-- 글작성자 prs_position -->
        <input type="hidden" name="date" id="date" value="">	                        <!-- 작성 날짜 -->
        <input type="hidden" name="prs_id" id="prs_id" value="<?=$prs_id?>">	        <!-- 자기자신과 글작성자비교를 위한 본인prs_id -->
        <div class="modal" id="inputPopUp">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title is-size-6"><span id="b_date"><?=$date?></span> <span id="b_position"><?=$booking_position?></span> <span id="b_name"><?=$booking_name?></span> </p>
                        <div class="column">
                            <div class="control select">
                                <select name="room_name" id="room_name">
                                    <option value="">회의실 선택</option>
                                    <option value="ROOM1">회의실1 (3F)</option>
                                    <option value="ROOM2">회의실2 (4F)</option>
                                    <option value="ROOM3">회의실3 (2F)</option>
                                    <option value="ROOM4">회의실4 (2F)</option>
                                </select>
                            </div>
                        </div>
                    <a class="delete" id="popup_close" aria-label="close"></a>
                </header>

                <section class="modal-card-body modal-booking">
                    <div class="content">

                        <div class="columns is-mobile">
                            <div class="column">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">시작</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field is-grouped">
                                            <div class="control select">
                                                <select name="s_hour" id="s_hour">
                                                    <?
                                                    for($i=8;$i<=23;$i++) {
                                                        $_i = str_pad($i,2,'0',STR_PAD_LEFT);
                                                        ?>
                                                        <option value="<?=$_i?>"><?=$_i?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="control select">
                                                <select name="s_min" id="s_min">
                                                    <option value="00">00</option>
                                                    <option value="30">30</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">종료</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field is-grouped">
                                            <div class="control select">
                                                <select name="e_hour" id="e_hour">
                                                    <?
                                                    for($j=8;$j<=23;$j++) {
                                                        $_j = str_pad($j,2,'0',STR_PAD_LEFT);
                                                        ?>
                                                        <option value="<?=$_j?>"><?=$_j?></option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="control select">
                                                <select name="e_min" id="e_min">
                                                    <option value="00">00</option>
                                                    <option value="30">30</option>
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
                                        <input class="input" type="text" placeholder="내용" name="title" id="title" maxlength="105" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <footer class="modal-card-foot" >
                        <a href="javascript:funWrite()" class="button is-primary"><span id="btnTxt"></span></a>
                        <a href="javascript:funDelete()" id="btnDel"class="button is-danger" style="display:none;">삭제</a>
                </footer>
            </div>
        </div>
        <!--팝업끝-->
    </form>


<form method="get" name="form1">
    <input type="hidden" name="date">
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>