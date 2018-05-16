<?
require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
require_once CMN_PATH."/login_check.php";
?>

<?
$startYear = 2014;

$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "ING";

$p_no = isset($_REQUEST['no']) ? $_REQUEST['no'] : null;
$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null;
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "write";

if ($mode == "modify")
{
    $mode_title = "수정";

    if ($project_no == "")
    {
        ?>
        <script type="text/javascript">
            alert("해당 프로젝트가 존재하지 않습니다.");
            location.href="project_list.php?type=<?=$type?>";
        </script>
        <?
        exit;
    }

    $searchSQL = " WHERE PROJECT_NO = '$project_no'";

    $sql = "SELECT
					TITLE, CONTENTS, CONVERT(char(10),START_DATE,120) AS START_DATE, CONVERT(char(10),END_DATE,120) AS END_DATE, PROGRESS, PRS_ID
				FROM
					DF_PROJECT WITH(NOLOCK)". $searchSQL;
    $rs = sqlsrv_query($dbConn,$sql);

    $record = sqlsrv_fetch_array($rs);
    if (sqlsrv_has_rows($rs) > 0)
    {
        $title = $record['TITLE'];
        $contents = $record['CONTENTS'];
        $start_date = $record['START_DATE'];
        $end_date = $record['END_DATE'];
        $progress = $record['PROGRESS'];
        $write_id = $record['PRS_ID'];

        $total_time = datediff("d",$start_date,$end_date)+1;
        $p_now_time = datediff("d",$start_date,date("Y-m-d"))+1;

        if ($p_now_time < $total_time) { $time = $p_now_time; } else { $time = $total_time; }
        $time_bar = $time / $total_time * 100;
        if ($time_bar < 0) { $time_bar = 0; }
        $progress_bar = $progress;

        $fr_year = substr($start_date,0,4);
        $fr_month = substr($start_date,5,2);
        $fr_day = substr($start_date,8,2);
        $to_year = substr($end_date,0,4);
        $to_month = substr($end_date,5,2);
        $to_day = substr($end_date,8,2);
    }
    else
    {
        ?>
        <script type="text/javascript">
            alert("해당 프로젝트가 존재하지 않습니다.");
            location.href="project_list.php?type=<?=$type?>";
        </script>
        <?
        exit;
    }
}
else if ($mode == "write")
{
    $mode_title = "등록";

    $title = "";
    $contents = "";
    $start_date = date("Y-m-d");
    $end_date = date("Y-m-d");
    $progress = 0;
    $time_bar = 0;
    $progress_bar = 0;

    $fr_year = date("Y");
    $fr_month = date("m");
    $fr_day = date("d");
    $to_year = date("Y");
    $to_month = date("m");
    $to_day = date("d");
}
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/JavaScript">
    $(document).ready(function(){
        $("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
        $("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
        //날짜 지정
        $("#fr_year, #fr_month, #fr_day").change(function() {
            $("#fr_date").val($("#fr_month").val()+"/"+$("#fr_day").val()+"/"+$("#fr_year").val());
        });
        $("#fr_date").datepicker({
            onSelect: function (selectedDate) {
                $("#fr_year").val( selectedDate.substring(6,10) );
                $("#fr_month").val( selectedDate.substring(0,2) );
                $("#fr_day").val( selectedDate.substring(3,5) );

                var fr_date = $("#fr_year").val() +"/"+ $("#fr_month").val() +"/"+ $("#fr_day").val();
                var to_date = $("#to_year").val() +"/"+ $("#to_month").val() +"/"+ $("#to_day").val();
                var today = "<?=date("Y/m/d");?>";

                var all_days = DateDiff(fr_date,to_date) + 1;
                var now_days = DateDiff(fr_date,today) + 1;

                days = now_days / all_days * 100;
                if (today < fr_date)
                {
                    $("#time_bar").attr("style","width:0%");
                }
                else if (days > 100)
                {
                    $("#time_bar").attr("style","width:100%");
                }
                else
                {
                    if ((all_days > 0 && now_days > 0) && (to_date > fr_date))
                    {
                        $("#time_bar").attr("style","width:"+days+"%");
                    }
                    else
                    {
                        $("#time_bar").attr("style","width:0%;");
                    }
                }
            }
        });
        $("#to_year, #to_month, #to_day").change(function() {
            $("#to_date").val($("#to_month").val()+"/"+$("#to_day").val()+"/"+$("#to_year").val());
        });
        $("#to_date").datepicker({
            onSelect: function (selectedDate) {
                $("#to_year").val( selectedDate.substring(6,10) );
                $("#to_month").val( selectedDate.substring(0,2) );
                $("#to_day").val( selectedDate.substring(3,5) );

                var fr_date = $("#fr_year").val() +"/"+ $("#fr_month").val() +"/"+ $("#fr_day").val();
                var to_date = $("#to_year").val() +"/"+ $("#to_month").val() +"/"+ $("#to_day").val();
                var today = "<?=date("Y/m/d");?>";

                var all_days = DateDiff(fr_date,to_date) + 1;
                var now_days = DateDiff(fr_date,today) + 1;

                days = now_days / all_days * 100;
                if (today < fr_date)
                {
                    $("#time_bar").attr("style","width:0%");
                }
                else if (days > 100)
                {
                    $("#time_bar").attr("style","width:100%");
                }
                else
                {
                    if ((all_days > 0 && now_days > 0) && (to_date > fr_date))
                    {
                        $("#time_bar").attr("style","width:"+days+"%");
                    }
                    else
                    {
                        $("#time_bar").attr("style","width:0%;");
                    }
                }
            }
        });

        //기간 경과율 그래프 표시
        $.map(["#fr_year","#fr_month","#fr_day","#to_year","#to_month","#to_day"], function(n,i){
            $(n).change(function(){
                var fr_date = $("#fr_year").val() +"/"+ $("#fr_month").val() +"/"+ $("#fr_day").val();
                var to_date = $("#to_year").val() +"/"+ $("#to_month").val() +"/"+ $("#to_day").val();
                var today = "<?=date("Y/m/d");?>";

                var all_days = DateDiff(fr_date,to_date) + 1;
                var now_days = DateDiff(fr_date,today) + 1;

                days = now_days / all_days * 100;
                if (today < fr_date)
                {
                    $("#time_bar").attr("style","width:0%");
                }
                else if (days > 100)
                {
                    $("#time_bar").attr("style","width:100%");
                }
                else
                {
                    if ((all_days > 0 && now_days > 0) && (to_date > fr_date))
                    {
                        $("#time_bar").attr("style","width:"+days+"%");
                    }
                    else
                    {
                        $("#time_bar").attr("style","width:0%;");
                    }
                }
            });
        });
        //프로젝트 전체 진행률 그래프 표시
        $("#progress").change(function(){
            $("#progress_bar").attr("style","width:"+this.value+"%");
        });

        //DeteDiff
        DateDiff = function(date1,date2){
            var sDate = new Date(date1);
            var eDate = new Date(date2);
            var timeSpan = (eDate - sDate) / 86400000;	//하루를 밀리세컨트로 표현
            var daysApart = Math.abs(Math.round(timeSpan));
            return daysApart;
        };

        //파생 프로젝트 체크
        $("#connect").click(function(){
            if ($("#connect").is(":checked"))
            {
                $("#connectView").css("display","inline");
            }
            else
            {
                $("#connectView").css("display","none");
            }
        });

        //등록
        $("#btnWrite").attr("style","cursor:pointer;").click(function(){
            //내용 유효성 검사 할 부분
            if ($("#title").val() == "")
            {
                alert("프로젝트명을 입력해주세요");
                $("#title").focus();
                return;
            }
            if ($("#contents").val() == "")
            {
                alert("프로젝트 설명을 입력해주세요");
                $("#contents").focus();
                return;
            }
            if ($("#detail_view_1").val() == "")
            {
                alert("프로젝트 담당자를 선택해 주세요");
                $("#detail_view_1").focus();
                return;
            }
            if ($("#detail_detail_1").val() == "")
            {
                alert("프로젝트 상세업무를 입력해주세요");
                $("#detail_detail_1").focus();
                return;
            }

            $("#popup_ok").attr("style","display:inline;");
        });
        $("#popup_ok_ok").attr("style","cursor:pointer;").click(function(){
            $("#form").attr("target","hdnFrame");
            $("#form").attr("action","project_write_act.php");
            $("#form").submit();
        });
        $("#popup_ok_no").attr("style","cursor:pointer;").click(function(){
            $("#popup_ok").attr("style","display:none;");
        });
        $("#popup_ok_close").attr("style","cursor:pointer;").click(function(){
            $("#popup_ok").attr("style","display:none;");
        });
        //취소
        $("#btnCancel").attr("style","cursor:pointer;").click(function(){
            $("#popup_cancel").attr("style","display:inline;");
        });
        $("#popup_cancel_ok").attr("style","cursor:pointer;").click(function(){
            $("#form").attr("target","_self");
            <? if ($mode == "write") { ?>
            $("#form").attr("action","project_list.php");
            <? } else { ?>
            $("#form").attr("action","project_detail.php");
            <? } ?>
            $("#form").submit();
        });
        $("#popup_cancel_no").attr("style","cursor:pointer;").click(function(){
            $("#popup_cancel").attr("style","display:none;");
        });
        $("#popup_cancel_close").attr("style","cursor:pointer;").click(function(){
            $("#popup_cancel").attr("style","display:none;");
        });

        //프로젝트 상세업무 추가
        $("#addDetail").attr("style","cursor:pointer;").click(function(){
            addDetail('');
        });
        //프로젝트 상세업무 삭제
        /*
         $("#delDetail").attr("style","cursor:pointer;").click(function(){
         if ($("#rows").val() == 1 )
         {
         alert("프로젝트 상세업무는 최소 1개 이상 입력하셔야 합니다.");
         return;
         }

         $("#detail_"+$("#rows").val()).remove();
         $("#rows").val(Number($("#rows").val())-1);
         $("#real_rows").val(Number($("#real_rows").val())-1);
         });
         */
    });

    //프로젝트 상세업무 삭제
    function delDetail(detail_i) {
        if ($("#real_rows").val() == 1 )
        {
            alert("프로젝트 상세업무는 최소 1개 이상 입력하셔야 합니다.");
            return;
        }
        $("#detail_"+detail_i).remove();
        $("#real_rows").val(Number($("#real_rows").val())-1);
    }

    //담당자 선택
    function addPerson(detail_i) {
        //MM_openBrWindow('select_detail.php','','width=800 ,height=600,scrollbars=yes');
        $("#popup_select").attr("style","display:inline;");
    }

    //상세업무 프로젝트 참여율 그래프 표시
    function changeRate(detail_i) {
        $("#part_bar_"+detail_i).attr("style","width:"+$("#part_rate_"+detail_i).val()+"%");
    }

    //프로젝트 참여기간 추가
    function addTime(detail_i) {
        if ($("#TimeSize_"+detail_i).val() >= 5)
        {
            alert("프로젝트 참여기간은 최대 5개까지 추가 가능합니다.");
            return;
        }

        var i = Number($("#TimeSize_"+detail_i).val())+1;
        $("#TimeSize_"+detail_i).val(i);
        var DetailDiv = "";

        DetailDiv +="				<div id='addTime_"+detail_i+"_"+i+"' style='padding-top:5px;'>";
        DetailDiv +="				<select name='detail_fr_year_"+detail_i+"[]' id='detail_fr_year_"+detail_i+"_"+i+"'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "					<option value='<?=$i?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				<span>년</span>";
        DetailDiv += "				<select name='detail_fr_month_"+detail_i+"[]' id='detail_fr_month_"+detail_i+"_"+i+"'>";
        <?
        for ($i=1; $i<=12; $i++)
        {
        if ($i == Date("m")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				<span>월</span>";
        DetailDiv += "				<select name='detail_fr_day_"+detail_i+"[]' id='detail_fr_day_"+detail_i+"_"+i+"'>";
        <?
        for ($i=1; $i<=31; $i++)
        {
        if ($i == Date("d")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				<span>일</span>";
        DetailDiv += "				<input type='hidden' id='detail_fr_date_"+detail_i+"_"+i+"' class='datepicker'>";
        DetailDiv += "				<span>-</span>";
        DetailDiv += "				<select name='detail_to_year_"+detail_i+"[]' id='detail_to_year_"+detail_i+"_"+i+"'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "					<option value='<?=$i?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				<span>년</span>";
        DetailDiv += "				<select name='detail_to_month_"+detail_i+"[]' id='detail_to_month_"+detail_i+"_"+i+"'>";
        <?
        for ($i=1; $i<=12; $i++)
        {
        if ($i == Date("m")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				<span>월</span>";
        DetailDiv += "				<select name='detail_to_day_"+detail_i+"[]' id='detail_to_day_"+detail_i+"_"+i+"'>";
        <?
        for ($i=1; $i<=31; $i++)
        {
        if ($i == Date("d")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				<span>년</span>";
        DetailDiv += "				<input type='hidden' id='detail_to_date_"+detail_i+"_"+i+"' class='datepicker'>";
        DetailDiv += "				</div>";

        $("#TimeZone_"+detail_i).append(DetailDiv);

        $(document).find("#detail_fr_date_"+detail_i+"_"+i).removeClass('hasDatepicker').datepicker({
            onSelect: function (selectedDate) {
                $("#detail_fr_year_"+detail_i+"_"+i).val( selectedDate.substring(6,10) );
                $("#detail_fr_month_"+detail_i+"_"+i).val( selectedDate.substring(0,2) );
                $("#detail_fr_day_"+detail_i+"_"+i).val( selectedDate.substring(3,5) );
            }
        });
        $(document).find("#detail_to_date_"+detail_i+"_"+i).removeClass('hasDatepicker').datepicker({
            onSelect: function (selectedDate) {
                $("#detail_to_year_"+detail_i+"_"+i).val( selectedDate.substring(6,10) );
                $("#detail_to_month_"+detail_i+"_"+i).val( selectedDate.substring(0,2) );
                $("#detail_to_day_"+detail_i+"_"+i).val( selectedDate.substring(3,5) );
            }
        });
    }

    //프로젝트 참여기간 삭제
    function delTime(detail_i) {
        if ($("#TimeSize_"+detail_i).val() == 1 )
        {
            alert("프로젝트 참여기간은 최소 1개 이상 입력하셔야 합니다.");
            return;
        }

        $("#addTime_"+detail_i+"_"+$("#TimeSize_"+detail_i).val()).remove();
        $("#TimeSize_"+detail_i).val(Number($("#TimeSize_"+detail_i).val())-1);
    }
</script>
</head>
<body>
<div class="wrapper">
    <form name="form" id="form" method="post">
        <input type="hidden" name="page" id="page" value="<?=$page?>">
        <input type="hidden" name="type" id="type" value="<?=$type?>">
        <input type="hidden" name="no" id="no" value="<?=$no?>">
        <input type="hidden" name="name" id="name" value="<?=$name?>">
        <input type="hidden" name="project_no" id="project_no" value="<?=$project_no?>">
        <input type="hidden" name="mode" id="mode" value="<?=$mode?>">
        <? include INC_PATH."/top_menu.php"; ?>

        <div class="inner-home">
            <? include INC_PATH."/project_menu.php"; ?>

            <div id="ing" class="work_wrap clearfix">
                <div class="vacation_stats clearfix">
                    <table class="notable" width="100%">
                        <tbody><tr>
                            <th scope="row"><? if ($type == "ING") { ?>진행 프로젝트<? } else if ($type == "END") { ?>완료 프로젝트<? } ?></th>
                        </tr>
                        </tbody></table>
                </div>
                <div class="work_stats_search project clearfix">
                    <table class="notable" width="100%">
                        <tr>
                            <th scope="row" class="project"><label for="">프로젝트 등록</label></th>
                            <td></td>
                        </tr>
                    </table>

                </div>
                <div class="board_list">
                    <table class="notable work3 board_list"  style="width:100%">
                        <caption>게시판 리스트 테이블</caption>
                        <colgroup>
                            <col width="15%" />
                            <col width="*" />
                        </colgroup>

                        <tbody class="p_detail">
                        <? if ($mode == "modify" && $prf_id == "4") { ?>
                            <tr>
                                <td>프로젝트 번호 변경</td>
                                <td>
                                    <input type="checkbox" id="connect" name="connect" value="Y">프로젝트 번호를 변경하시는 경우 체크하고, 연결 프로젝트를 선택해 주세요.
                                    <select id="link" name="link">
                                        <option value="">===== 연결 프로젝트 선택 =====</option>
                                        <?
                                        $sql = "SELECT PROJECT_NO, TITLE FROM DF_PROJECT WITH(NOLOCK) WHERE USE_YN = 'Y' AND PROJECT_NO LIKE 'DF". Date("Y") ."_%' AND PROJECT_NO NOT LIKE '%-%' ORDER BY PROJECT_NO DESC";
                                        $rs = sqlsrv_query($dbConn,$sql);

                                        while ($record = sqlsrv_fetch_array($rs))
                                        {
                                            $link_project_no = $record['PROJECT_NO'];
                                            $link_project_title = $record['TITLE'];
                                            ?>
                                            <option value="<?=$link_project_no?>">[<?=$link_project_no?>] <?=$link_project_title?></option>
                                            <?
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        <? } ?>

                        <tr>
                            <td>프로젝트명</td>
                            <td><input id="title" name="title" class="df_textinput" type="text" style="width:85%; border:1px solid #000;" value="<?=$title?>"<? if ($mode == "modify" && ($write_id != $prs_id && $prf_id != "4")) { ?> readonly<? } ?> /><input id="title" name="title_prev" type="hidden" value="<?=$title?>" /></td>
                        </tr>
                        <? if ($mode == "write") { ?>
                            <tr>
                                <td>연결 프로젝트</td>
                                <td>
                                    <input type="checkbox" id="connect" name="connect" value="Y">이미 등록된 프로젝트의 파생 프로젝트인 경우 체크해 주세요.
                                    <div id="connectView" style="display:none;">
                                        <select id="link" name="link">
                                            <?
                                            $sql = "SELECT PROJECT_NO, TITLE FROM DF_PROJECT WITH(NOLOCK) WHERE USE_YN = 'Y' AND PROJECT_NO LIKE 'DF". Date("Y") ."_%' AND PROJECT_NO NOT LIKE '%-%' ORDER BY PROJECT_NO DESC";
                                            $rs = sqlsrv_query($dbConn,$sql);

                                            while ($record = sqlsrv_fetch_array($rs))
                                            {
                                                $link_project_no = $record['PROJECT_NO'];
                                                $link_project_title = $record['TITLE'];
                                                ?>
                                                <option value="<?=$link_project_no?>">[<?=$link_project_no?>] <?=$link_project_title?></option>
                                                <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        <? } ?>
                        <tr>
                            <td>프로젝트 설명</td>
                            <td><textarea cols="30" rows="10" name="contents" id="contents"><?=$contents?></textarea></td>
                        </tr>
                        <tr class="period">
                            <td>기간</td>
                            <td>
                                <select name="fr_year" id="fr_year">
                                    <?
                                    for ($i=$startYear; $i<=($fr_year+1); $i++)
                                    {
                                        if ($i == $fr_year)
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
                                <span>년</span>
                                <select name="fr_month" id="fr_month">
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

                                        if ($j == $fr_month)
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
                                <span>월</span>
                                <select name="fr_day" id="fr_day">
                                    <?
                                    for ($i=1; $i<=31; $i++)
                                    {
                                        if (strlen($i) == "1")
                                        {
                                            $j = "0".$i;
                                        }
                                        else
                                        {
                                            $j = $i;
                                        }

                                        if ($j == $fr_day)
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
                                <span>일</span>
                                <input type="hidden" id="fr_date" class="datepicker">
                                <span>-</span>
                                <select name="to_year" id="to_year">
                                    <?
                                    for ($i=$startYear; $i<=($to_year+1); $i++)
                                    {
                                        if ($i == $to_year)
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
                                <span>년</span>
                                <select name="to_month" id="to_month">
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

                                        if ($j == $to_month)
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
                                <span>월</span>
                                <select name="to_day" id="to_day">
                                    <?
                                    for ($i=1; $i<=31; $i++)
                                    {
                                        if (strlen($i) == "1")
                                        {
                                            $j = "0".$i;
                                        }
                                        else
                                        {
                                            $j = $i;
                                        }

                                        if ($j == $to_day)
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
                                <span>일</span>
                                <input type="hidden" id="to_date" class="datepicker">
                            </td>
                        </tr>
                        </tbody>

                    </table>

                </div>

                <div class="work_stats_search project not1st clearfix">
                    <table class="notable" width="100%">
                        <tr>
                            <th scope="row" class="project"><label for="">프로젝트 전체  진행률</label></th>
                            <td></td>
                        </tr>
                    </table>

                </div>
                <div class="board_list">
                    <table class="notable work3 board_list"  style="width:100%">
                        <caption>게시판 리스트 테이블</caption>
                        <colgroup>
                            <col width="15%" />
                            <col width="*" />
                        </colgroup>

                        <tbody class="p_detail">

                        <tr>
                            <td>기간 경과율</td>
                            <td>
                                <div class="p_bar"><span id="time_bar" style="width:<?=$time_bar?>%;"></span></div>
                            </td>
                        </tr>

                        <tr>
                            <td>프로젝트 전체 진행률</td>
                            <td>
                                <div class="p_bar"><span id="progress_bar" style="width:<?=$progress_bar?>%;"></span></div>
                                <select name="progress" id="progress" class="percentage">
                                    <?
                                    for($i=0; $i<=100; $i+=5)
                                    {
                                        ?>
                                        <option value="<?=$i?>"<? if ($progress == $i) { echo " selected"; } ?>><?=$i?>%</option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        </tbody>
                    </table>

                </div>

                <div class="work_stats_search project not1st clearfix">
                    <table class="notable" width="100%">
                        <tr>
                            <th scope="row" class="project"><label for="">프로젝트 상세업무</label></th>
                            <td style="color:red;">(담당자 변경을 원하시는 경우, 삭제 후 재등록 하셔야 합니다. 담당자 삭제는 해당 담당자 좌측 휴지통 아이콘을 클릭해 주세요. )</td>
                        </tr>
                    </table>
                </div>
                <?
                if ($mode == "modify")
                {
                    $sql = "SELECT
					PART, DETAIL, PART_RATE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, COUNT(*) AS CNT 
				FROM 
					DF_PROJECT_DETAIL WITH(NOLOCK) 
				WHERE
					PROJECT_NO = '$project_no' 
				GROUP BY PART, DETAIL, PART_RATE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, SORT
				ORDER BY CASE 
					WHEN PART = 'BM' THEN 1 
					WHEN PART = 'CD' THEN 2 
					WHEN PART = 'PM' THEN 3 
					WHEN PART = 'PL' THEN 4
					WHEN PART = '기획' THEN 5
					WHEN PART = '디자인' THEN 6
					WHEN PART = '모션' THEN 7
					WHEN PART = '개발(front-end)' THEN 8 
					WHEN PART = '개발(back-end)' THEN 9 END, SORT";
                    $rs = sqlsrv_query($dbConn,$sql);

                    $rows = sqlsrv_has_rows($rs);

                    $detail_i = 1;
                    if ($rows > 0)
                    {
                        while ($record=sqlsrv_fetch_array($rs))
                        {
                            $detail_part = $record['PART'];
                            $detail_detail = $record['DETAIL'];
                            $detail_part_rate = $record['PART_RATE'];
                            $detail_id = $record['PRS_ID'];
                            $detail_login = $record['PRS_LOGIN'];
                            $detail_name = $record['PRS_NAME'];
                            $detail_position = $record['PRS_POSITION'];
                            $detail_part_bar = $detail_part_rate;
                            $time_cnt = $record['CNT'];
                            ?>
                            <div class="board_list last" id="detail_<?=$detail_i?>" name="<?=$detail_i?>">
                                <table class="notable work3 board_list"  style="width:100%">
                                    <caption>게시판 리스트 테이블</caption>
                                    <colgroup>
                                        <col width="3%" />
                                        <col width="15%" />
                                        <col width="*" />
                                    </colgroup>

                                    <tbody class="p_detail border_b">

                                    <tr>
                                        <td rowspan="4" style="text-align:center;">
                                            <a href="javascript:delDetail('<?=$detail_i?>');"><img src="../img/project/btn_trash.gif" alt="제거" id="delDetail_<?=$detail_i?>" /></a>
                                        </td>
                                        <td>
                                            <select name="detail_part_<?=$detail_i?>" id="detail_part_<?=$detail_i?>" class="role" style="height:29px;">
                                                <option value="BM"<? if ($detail_part == "BM") { echo " selected"; } ?>>BM</option>
                                                <option value="CD"<? if ($detail_part == "CD") { echo " selected"; } ?>>CD</option>
                                                <option value="PM"<? if ($detail_part == "PM") { echo " selected"; } ?>>PM</option>
                                                <option value="PL"<? if ($detail_part == "PL") { echo " selected"; } ?>>PL</option>
                                                <option value="기획"<? if ($detail_part == "기획") { echo " selected"; } ?>>기획</option>
                                                <option value="디자인"<? if ($detail_part == "디자인") { echo " selected"; } ?>>디자인</option>
                                                <option value="모션"<? if ($detail_part == "모션") { echo " selected"; } ?>>모션</option>
                                                <option value="개발(front-end)"<? if ($detail_part == "개발(front-end)") { echo " selected"; } ?>>개발(front-end)</option>
                                                <option value="개발(back-end)"<? if ($detail_part == "개발(back-end)") { echo " selected"; } ?>>개발(back-end)</option>
                                            </select>
                                        </td>
                                        <td class="clearfix">
                                            <input type="text" class="df_textinput" style="width:85%; border:1px solid #000;" name="detail_view_<?=$detail_i?>" id="detail_view_<?=$detail_i?>" value="<?=$detail_position?> <?=$detail_name?>" readonly>
                                            <input type="hidden" name="detail_id_<?=$detail_i?>" id="detail_id_<?=$detail_i?>" value="<?=$detail_id?>">
                                            <input type="hidden" name="detail_login_<?=$detail_i?>" id="detail_login_<?=$detail_i?>" value="<?=$detail_login?>">
                                            <input type="hidden" name="detail_position_<?=$detail_i?>" id="detail_position_<?=$detail_i?>" value="<?=$detail_position?>">
                                            <input type="hidden" name="detail_name_<?=$detail_i?>" id="detail_name_<?=$detail_i?>" value="<?=$detail_name?>">
                                            <!--a href="javascript:addPerson('<?=$detail_i?>');" style="float:right;"><img src="../img/project/btn_onCharge.gif" alt="담당자 선택" /></a-->
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>작업명</td>
                                        <td><input type="text" name="detail_detail_<?=$detail_i?>" id="detail_detail_<?=$detail_i?>" value="<?=$detail_detail?>" maxlength="200" class="df_textinput" style="width:85%; border:1px solid #000;"></td>
                                    </tr>

                                    <tr>
                                        <td>프로젝트 참여율</td>
                                        <td>
                                            <div class="p_bar"><span id="part_bar_<?=$detail_i?>" style="width:<?=$detail_part_bar?>%;"></span></div>
                                            <select name="part_rate_<?=$detail_i?>" id="part_rate_<?=$detail_i?>" class="percentage" onchange="changeRate('<?=$detail_i?>');">
                                                <option value="0"<? if ($detail_part_rate == 0) { echo " selected"; } ?>>0%</option>
                                                <option value="10"<? if ($detail_part_rate == 10) { echo " selected"; } ?>>10%</option>
                                                <option value="20"<? if ($detail_part_rate == 20) { echo " selected"; } ?>>20%</option>
                                                <option value="30"<? if ($detail_part_rate == 30) { echo " selected"; } ?>>30%</option>
                                                <option value="40"<? if ($detail_part_rate == 40) { echo " selected"; } ?>>40%</option>
                                                <option value="50"<? if ($detail_part_rate == 50) { echo " selected"; } ?>>50%</option>
                                                <option value="60"<? if ($detail_part_rate == 60) { echo " selected"; } ?>>60%</option>
                                                <option value="70"<? if ($detail_part_rate == 70) { echo " selected"; } ?>>70%</option>
                                                <option value="80"<? if ($detail_part_rate == 80) { echo " selected"; } ?>>80%</option>
                                                <option value="90"<? if ($detail_part_rate == 90) { echo " selected"; } ?>>90%</option>
                                                <option value="100"<? if ($detail_part_rate == 100) { echo " selected"; } ?>>100%</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr class="period">
                                        <td>프로젝트 참여기간</td>
                                        <td class="clearfix" id="TimeZone_<?=$detail_i?>">
                                            <?
                                            $sql1 = "SELECT 
							CONVERT(char(10),START_DATE,102) AS START_DATE, CONVERT(char(10),END_DATE,102) AS END_DATE 
						FROM 
							DF_PROJECT_DETAIL WITH(NOLOCK) 
						WHERE
							PROJECT_NO = '$project_no' AND PRS_ID = '$detail_id'
						ORDER BY 
							SORT";
                                            $rs1 = sqlsrv_query($dbConn,$sql1);

                                            $t = 1;
                                            while ($record1=sqlsrv_fetch_array($rs1))
                                            {
                                            $detail_start_date = $record1['START_DATE'];
                                            $detail_end_date = $record1['END_DATE'];

                                            $detail_fr_year = substr($detail_start_date,0,4);
                                            $detail_fr_month = substr($detail_start_date,5,2);
                                            $detail_fr_day = substr($detail_start_date,8,2);
                                            $detail_to_year = substr($detail_end_date,0,4);
                                            $detail_to_month = substr($detail_end_date,5,2);
                                            $detail_to_day = substr($detail_end_date,8,2);
                                            ?>
                                            <script type="text/javascript">
                                                $(document).ready(function(){
                                                    $("#detail_fr_date_<?=$detail_i?>_<?=$t?>").val($("#detail_fr_month_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_fr_day_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_fr_year_<?=$detail_i?>_<?=$t?>").val());
                                                    $("#detail_to_date_<?=$detail_i?>_<?=$t?>").val($("#detail_to_month_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_to_day_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_to_year_<?=$detail_i?>_<?=$t?>").val());

                                                    $("#detail_fr_year_<?=$detail_i?>_<?=$t?>, #detail_fr_month_<?=$detail_i?>_<?=$t?>, #detail_fr_day_<?=$detail_i?>_<?=$t?>").change(function() {
                                                        $("#detail_fr_date_<?=$detail_i?>_<?=$t?>").val($("#detail_fr_month_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_fr_day_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_fr_year_<?=$detail_i?>_<?=$t?>").val());
                                                    });
                                                    $("#detail_to_year_<?=$detail_i?>_<?=$t?>, #detail_to_month_<?=$detail_i?>_<?=$t?>, #detail_to_day_<?=$detail_i?>_<?=$t?>").change(function() {
                                                        $("#detail_to_date_<?=$detail_i?>_<?=$t?>").val($("#detail_to_month_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_to_day_<?=$detail_i?>_<?=$t?>").val()+"/"+$("#detail_to_year_<?=$detail_i?>_<?=$t?>").val());
                                                    });

                                                    $("#detail_fr_date_<?=$detail_i?>_<?=$t?>").datepicker({
                                                        onSelect: function (selectedDate) {
                                                            $("#detail_fr_year_<?=$detail_i?>_<?=$t?>").val( selectedDate.substring(6,10) );
                                                            $("#detail_fr_month_<?=$detail_i?>_<?=$t?>").val( selectedDate.substring(0,2) );
                                                            $("#detail_fr_day_<?=$detail_i?>_<?=$t?>").val( selectedDate.substring(3,5) );
                                                        }
                                                    });
                                                    $("#detail_to_date_<?=$detail_i?>_<?=$t?>").datepicker({
                                                        onSelect: function (selectedDate) {
                                                            $("#detail_to_year_<?=$detail_i?>_<?=$t?>").val( selectedDate.substring(6,10) );
                                                            $("#detail_to_month_<?=$detail_i?>_<?=$t?>").val( selectedDate.substring(0,2) );
                                                            $("#detail_to_day_<?=$detail_i?>_<?=$t?>").val( selectedDate.substring(3,5) );
                                                        }
                                                    });
                                                });
                                            </script>
                                            <div id="addTime_<?=$detail_i?>_<?=$t?>"<? if ($t > 1) { echo " style='padding-top:5px;'"; } ?>>
                                                <select name="detail_fr_year_<?=$detail_i?>[]" id="detail_fr_year_<?=$detail_i?>_<?=$t?>">
                                                    <?
                                                    for ($i=$startYear; $i<=($detail_fr_year+1); $i++)
                                                    {
                                                        if ($i == $detail_fr_year)
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
                                                <span>년</span>
                                                <select name="detail_fr_month_<?=$detail_i?>[]" id="detail_fr_month_<?=$detail_i?>_<?=$t?>">
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

                                                        if ($j == $detail_fr_month)
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
                                                <span>월</span>
                                                <select name="detail_fr_day_<?=$detail_i?>[]" id="detail_fr_day_<?=$detail_i?>_<?=$t?>">
                                                    <?
                                                    for ($i=1; $i<=31; $i++)
                                                    {
                                                        if (strlen($i) == "1")
                                                        {
                                                            $j = "0".$i;
                                                        }
                                                        else
                                                        {
                                                            $j = $i;
                                                        }

                                                        if ($j == $detail_fr_day)
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
                                                <span>일</span>
                                                <input type="hidden" id="detail_fr_date_<?=$detail_i?>_<?=$t?>" class="datepicker">
                                                <span>-</span>
                                                <select name="detail_to_year_<?=$detail_i?>[]" id="detail_to_year_<?=$detail_i?>_<?=$t?>">
                                                    <?
                                                    for ($i=$startYear; $i<=($detail_to_year+1); $i++)
                                                    {
                                                        if ($i == $detail_to_year)
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
                                                <span>년</span>
                                                <select name="detail_to_month_<?=$detail_i?>[]" id="detail_to_month_<?=$detail_i?>_<?=$t?>">
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

                                                        if ($j == $detail_to_month)
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
                                                <span>월</span>
                                                <select name="detail_to_day_<?=$detail_i?>[]" id="detail_to_day_<?=$detail_i?>_<?=$t?>">
                                                    <?
                                                    for ($i=1; $i<=31; $i++)
                                                    {
                                                        if (strlen($i) == "1")
                                                        {
                                                            $j = "0".$i;
                                                        }
                                                        else
                                                        {
                                                            $j = $i;
                                                        }

                                                        if ($j == $detail_to_day)
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
                                                <span>일</span>
                                                <input type="hidden" id="detail_to_date_<?=$detail_i?>_<?=$t?>" class="datepicker">
                                                <input type="hidden" name="TimeSize_<?=$detail_i?>" id="TimeSize_<?=$detail_i?>" value="<?=$time_cnt?>">
                                                <? if ($t == 1) { ?>
                                                    <div class="btn_right plus" >
                                                        <a href="javascript:addTime('<?=$detail_i?>');"><img src="../img/project/btn_plus.gif" alt="추가" id="addTime" /></a>
                                                        <a href="javascript:delTime('<?=$detail_i?>');"><img src="../img/project/btn_minus.gif" alt="제거" id="delTime" /></a>
                                                    </div>
                                                <? } ?>
                                                <?
                                                $t++;
                                                }
                                                ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?
                            $rows = $detail_i;
                            $detail_i = $detail_i + 1;
                        }
                    }
                }
                else
                {
                    $rows = 1;

                    $detail_part = "PM";
                    $detail_detail = "";
                    $detail_part_rate = 0;
                    $detail_id = $prs_id;
                    $detail_login = $prs_login;
                    $detail_name = $prs_name;
                    $detail_team = $prs_team;
                    $detail_position = $prs_position;
                    $detail_start_date = date("Y-m-d");
                    $detail_end_date = date("Y-m-d");
                    $detail_part_bar = 0;

                    $detail_fr_year = substr($detail_start_date,0,4);
                    $detail_fr_month = substr($detail_start_date,5,2);
                    $detail_fr_day = substr($detail_start_date,8,2);
                    $detail_to_year = substr($detail_end_date,0,4);
                    $detail_to_month = substr($detail_end_date,5,2);
                    $detail_to_day = substr($detail_end_date,8,2);
                    ?>
                    <div class="board_list last" id="detail_<?=$rows?>" name="<?=$rows?>">
                        <table class="notable work3 board_list"  style="width:100%">
                            <caption>게시판 리스트 테이블</caption>
                            <colgroup>
                                <col width="15%" />
                                <col width="*" />
                            </colgroup>

                            <tbody class="p_detail border_b">

                            <tr>
                                <td>
                                    <select name="detail_part_<?=$rows?>" id="detail_part_<?=$rows?>" class="role" style="height:29px;">
                                        <option value="BM">BM</option>
                                        <option value="CD">CD</option>
                                        <option value="PM">PM</option>
                                        <option value="PL">PL</option>
                                        <option value="기획">기획</option>
                                        <option value="디자인">디자인</option>
                                        <option value="모션">모션</option>
                                        <option value="개발(front-end)">개발(front-end)</option>
                                        <option value="개발(back-end)">개발(back-end)</option>
                                    </select>
                                </td>
                                <td class="clearfix">
                                    <input type="text" class="df_textinput" style="width:85%; border:1px solid #000;" name="detail_view_<?=$rows?>" id="detail_view_<?=$rows?>" value="" readonly>
                                    <input type="hidden" name="detail_id_<?=$rows?>" id="detail_id_<?=$rows?>" value="">
                                    <input type="hidden" name="detail_login_<?=$rows?>" id="detail_login_<?=$rows?>" value="">
                                    <input type="hidden" name="detail_position_<?=$rows?>" id="detail_position_<?=$rows?>" value="">
                                    <input type="hidden" name="detail_name_<?=$rows?>" id="detail_name_<?=$rows?>" value="">
                                    <a href="javascript:addPerson('<?=$rows?>');" style="float:right;"><img src="../img/project/btn_onCharge.gif" alt="담당자 선택" /></a>
                                </td>
                            </tr>

                            <tr>
                                <td>작업명</td>
                                <td><input type="text" name="detail_detail_<?=$rows?>" id="detail_detail_<?=$rows?>" value="" maxlength="200" class="df_textinput" style="width:85%; border:1px solid #000;"></td>
                            </tr>

                            <tr>
                                <td>프로젝트 참여율</td>
                                <td>
                                    <div class="p_bar"><span id="part_bar_<?=$rows?>"></span></div>
                                    <select name="part_rate_<?=$rows?>" id="part_rate_<?=$rows?>" class="percentage" onchange="changeRate('<?=$rows?>');">
                                        <option value="0">0%</option>
                                        <option value="10">10%</option>
                                        <option value="20">20%</option>
                                        <option value="30">30%</option>
                                        <option value="40">40%</option>
                                        <option value="50">50%</option>
                                        <option value="60">60%</option>
                                        <option value="70">70%</option>
                                        <option value="80">80%</option>
                                        <option value="90">90%</option>
                                        <option value="100">100%</option>
                                    </select>
                                </td>
                            </tr>

                            <tr class="period">
                                <td>프로젝트 참여기간</td>
                                <td class="clearfix" id="TimeZone_<?=$rows?>">
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("#detail_fr_date_<?=$rows?>_1").val($("#detail_fr_month_<?=$rows?>_1").val()+"/"+$("#detail_fr_day_<?=$rows?>_1").val()+"/"+$("#detail_fr_year_<?=$rows?>_1").val());
                                            $("#detail_to_date_<?=$rows?>_1").val($("#detail_to_month_<?=$rows?>_1").val()+"/"+$("#detail_to_day_<?=$rows?>_1").val()+"/"+$("#detail_to_year_<?=$rows?>_1").val());

                                            $("#detail_fr_year_<?=$rows?>_1, #detail_fr_month_<?=$rows?>_1, #detail_fr_day_<?=$rows?>_1").change(function() {
                                                $("#detail_fr_date_<?=$rows?>_1").val($("#detail_fr_month_<?=$rows?>_1").val()+"/"+$("#detail_fr_day_<?=$rows?>_1").val()+"/"+$("#detail_fr_year_<?=$rows?>_1").val());
                                            });
                                            $("#detail_to_year_<?=$rows?>_1, #detail_to_month_<?=$rows?>_1, #detail_to_day_<?=$rows?>_1").change(function() {
                                                $("#detail_to_date_<?=$rows?>_1").val($("#detail_to_month_<?=$rows?>_1").val()+"/"+$("#detail_to_day_<?=$rows?>_1").val()+"/"+$("#detail_to_year_<?=$rows?>_1").val());
                                            });

                                            $("#detail_fr_date_<?=$rows?>_1").datepicker({
                                                onSelect: function (selectedDate) {
                                                    $("#detail_fr_year_<?=$rows?>_1").val( selectedDate.substring(6,10) );
                                                    $("#detail_fr_month_<?=$rows?>_1").val( selectedDate.substring(0,2) );
                                                    $("#detail_fr_day_<?=$rows?>_1").val( selectedDate.substring(3,5) );
                                                }
                                            });
                                            $("#detail_to_date_<?=$rows?>_1").datepicker({
                                                onSelect: function (selectedDate) {
                                                    $("#detail_to_year_<?=$rows?>_1").val( selectedDate.substring(6,10) );
                                                    $("#detail_to_month_<?=$rows?>_1").val( selectedDate.substring(0,2) );
                                                    $("#detail_to_day_<?=$rows?>_1").val( selectedDate.substring(3,5) );
                                                }
                                            });
                                        });
                                    </script>
                                    <select name="detail_fr_year_<?=$rows?>[]" id="detail_fr_year_<?=$rows?>_1">
                                        <?
                                        for ($i=$startYear; $i<=($detail_fr_year+1); $i++)
                                        {
                                            if ($i == $detail_fr_year)
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
                                    <span>년</span>
                                    <select name="detail_fr_month_<?=$rows?>[]" id="detail_fr_month_<?=$rows?>_1">
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

                                            if ($j == $detail_fr_month)
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
                                    <span>월</span>
                                    <select name="detail_fr_day_<?=$rows?>[]" id="detail_fr_day_<?=$rows?>_1">
                                        <?
                                        for ($i=1; $i<=31; $i++)
                                        {
                                            if (strlen($i) == "1")
                                            {
                                                $j = "0".$i;
                                            }
                                            else
                                            {
                                                $j = $i;
                                            }

                                            if ($j == $detail_fr_day)
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
                                    <span>일</span>
                                    <input type="hidden" id="detail_fr_date_<?=$rows?>_1" class="datepicker">
                                    <span>-</span>
                                    <select name="detail_to_year_<?=$rows?>[]" id="detail_to_year_<?=$rows?>_1">
                                        <?
                                        for ($i=$startYear; $i<=($detail_to_year+1); $i++)
                                        {
                                            if ($i == $detail_to_year)
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
                                    <span>년</span>
                                    <select name="detail_to_month_<?=$rows?>[]" id="detail_to_month_<?=$rows?>_1">
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

                                            if ($j == $detail_to_month)
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
                                    <span>월</span>
                                    <select name="detail_to_day_<?=$rows?>[]" id="detail_to_day_<?=$rows?>_1">
                                        <?
                                        for ($i=1; $i<=31; $i++)
                                        {
                                            if (strlen($i) == "1")
                                            {
                                                $j = "0".$i;
                                            }
                                            else
                                            {
                                                $j = $i;
                                            }

                                            if ($j == $detail_to_day)
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
                                    <span>일</span>
                                    <input type="hidden" id="detail_to_date_<?=$rows?>_1" class="datepicker">
                                    <div class="btn_right plus" >
                                        <a href="javascript:addTime('<?=$rows?>');"><img src="../img/project/btn_plus.gif" alt="추가" id="addTime" /></a>
                                        <a href="javascript:delTime('<?=$rows?>');"><img src="../img/project/btn_minus.gif" alt="제거" id="delTime" /></a>
                                        <input type="hidden" name="TimeSize_<?=$rows?>" id="TimeSize_<?=$rows?>" value="1">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <?
                }
                ?>
                <input type="hidden" name="rows" id="rows" value="<?=$rows?>">
                <input type="hidden" name="real_rows" id="real_rows" value="<?=$rows?>">
                <div id="DivaddDetail"></div>
                <div class="btn_bot clearfix">
                    <div class="btn_right plus2" >
                        <img src="../img/project/btn_plus.gif" alt="상세업무 추가" id="addDetail" />
                        <!--img src="../img/project/btn_trash.gif" alt="상세업무 제거" id="delDetail" /-->
                    </div>
                </div>

                <div class="project_reg clearfix">
                    <div class="btns_wrap btn_right" style="margin-top:0;">
                        <? if ($mode == "write") { ?>
                            <img src="../img/project/btn_register.gif" alt="등록" id="btnWrite" />
                        <? } else if ($mode == "modify") { ?>
                            <img src="../img/project/btn_change.gif" alt="수정" id="btnWrite" />
                        <? } ?>
                        <img src="../img/project/btn_cancle.gif" alt="취소" id="btnCancel" />
                    </div>
                </div>

            </div>
        </div>
    </form>
    <? include INC_PATH."/bottom.php"; ?>

    <div class="popups">
        <div class="cancle" id="popup_cancel" style="display:none;">
            <div class="pop_top">
                <p class="pop_title">취소</p>
                <span class="close" style="cursor:pointer;" id="popup_cancel_close">닫기</span>
            </div>
            <div class="pop_bot">
                <p>프로젝트 <?=$mode_title?>을 취소 하시겠습니까?</p>
                <div class="btns">
                    <img src="../img/btn_ok.gif" alt="확인" class="first" id="popup_cancel_ok" />
                    <img src="../img/project/btn_no.gif" alt="취소" id="popup_cancel_no" />
                </div>
            </div>
        </div>
        <div class="ok" id="popup_ok" style="display:none;">
            <div class="pop_top">
                <p class="pop_title"><?=$mode_title?></p>
                <span class="close" style="cursor:pointer;" id="popup_ok_close">닫기</span>
            </div>
            
            
            <div class="pop_bot">
                <p>프로젝트를 <?=$mode_title?> 하시겠습니까?</p>
                <div class="btns">
                    <img src="../img/btn_ok.gif" alt="확인" class="first" id="popup_ok_ok" />
                    <img src="../img/project/btn_no.gif" alt="취소" id="popup_ok_no" />
                </div>
            </div>
        </div>
        <div class="select" id="popup_select" style="display:none;">
            <form name="popup_form" id="popup_form" method="post">
                <div class="pop_top">
                    <p class="pop_title">담당자 선택</p>
                    <span class="close" style="cursor:pointer;" id="popup_select_close">닫기</span>
                </div>
                <div class="pop_bottom clearfix">
                    <div class="left section">
                        <div class="top">
                            <select name="sel" id="sel" class="role">
                                <option value="1">BM</option>
                                <option value="2">CD</option>
                                <option value="3">PM</option>
                                <option value="4">PL</option>
                                <option value="5">기획</option>
                                <option value="6">디자인</option>
                                <option value="7">모션</option>
                                <option value="8">개발(front-end)</option>
                                <option value="9">개발(back-end)</option>
                            </select>
                        </div>
                        <div class="mid clearfix">
                            <div class="left_area floatl">
                                <div class="search_area">
                                    <input id="search_name" name="search_name" class="df_textinput" type="text" style="width:100px; border:none;" />
                                    <img src="../img/project/btn_x.gif" alt="삭제" class="btn_x" id="resetBtn" />
                                </div>
                            </div>
                            <div class="right_area floatr">
                                <img src="../img/project/btn_search_pop.gif" alt="검색" id="searchBtn" />
                            </div>
                        </div>
                        <div class="bottom" id="person_list">
                            <?
                            $sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
                            $rs = sqlsrv_query($dbConn,$sql);

                            while($record=sqlsrv_fetch_array($rs))
                            {
                                $orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
                            }

                            $orderbycase = " ORDER BY CASE ". $orderby . " END, PRS_NAME";

                            $sql = "SELECT PRS_ID, PRS_LOGIN, PRS_POSITION, PRS_NAME FROM DF_PERSON WITH(NOLOCK) WHERE PRF_ID IN (1,2,3,4,5,7) AND PRS_ID NOT IN (102)". $orderbycase;
                            $rs = sqlsrv_query($dbConn,$sql);

                            $i = 0;
                            while($record=sqlsrv_fetch_array($rs))
                            {
                                $i++;

                                $id = $record['PRS_ID'];
                                $login = $record['PRS_LOGIN'];
                                $position = $record['PRS_POSITION'];
                                $name = $record['PRS_NAME'];
                                ?>
                                <input type="hidden" id="sel_id_<?=$i?>" value="<?=$id?>">
                                <input type="hidden" id="sel_login_<?=$i?>" value="<?=$login?>">
                                <input type="hidden" id="sel_position_<?=$i?>" value="<?=$position?>">
                                <input type="hidden" id="sel_name_<?=$i?>" value="<?=$name?>">
                                <p><input type="checkbox" id="check_<?=$i?>" name="check" title="<?=$id?>"><label for="check_<?=$i?>" style="cursor:pointer;"><?=$position?> <?=$name?></label></p>
                                <?
                            }
                            ?>
                            <input type="hidden" name="total" id="total" value="<?=$i?>">
                        </div>
                    </div>
                    <img src="../img/project/btn_select.gif" alt="선택" id="sel_click" class="sel" />
                    <div class="right section clearfix">
                        <div class="groups">

                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>BM</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_1" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_1" id="total_1" value="0">
                                    <ul id="list_1" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_1" id="check_list_1">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>CD</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_2" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_2" id="total_2" value="0">
                                    <ul id="list_2" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_2" id="check_list_2">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>PM</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_3" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_3" id="total_3" value="0">
                                    <ul id="list_3" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_3" id="check_list_3">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>PL</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_4" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_4" id="total_4" value="0">
                                    <ul id="list_4" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_4" id="check_list_4">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>기획</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_5" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_5" id="total_5" value="0">
                                    <ul id="list_5" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_5" id="check_list_5">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>디자인</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_6" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_6" id="total_6" value="0">
                                    <ul id="list_6" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_6" id="check_list_6">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>모션</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_7" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_7" id="total_7" value="0">
                                    <ul id="list_7" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_7" id="check_list_7">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>개발(front-end)</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_8" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_8" id="total_8" value="0">
                                    <ul id="list_8" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_8" id="check_list_8">
                                </div>
                            </div>
                            <div class="group">
                                <div class="group_top clearfix">
                                    <b>개발(back-end)</b>
                                    <img src="../img/project/btn_plus_pop.gif" alt="추가" class="floatr" id="add_9" name="addBtn" />
                                </div>
                                <div class="people clearfix">
                                    <input type="hidden" name="total_9" id="total_9" value="0">
                                    <ul id="list_9" style="padding:5px;">
                                    </ul>
                                    <input type="hidden" name="check_list_9" id="check_list_9">
                                </div>
                            </div>

                        </div>

                        <a href="javascript:list_up();"><img src="../img/project/btn_move_up.gif" alt="" /></a>
                        <a href="javascript:list_down();"><img src="../img/project/btn_move_dn.gif" alt="" /></a>
                        <input type="hidden" name="move_ul" id="move_ul">

                    </div>
                    <img src="../img/project/btn_accept.gif" alt="확인" id="popup_select_ok" class="accept" />
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<script type="text/JavaScript">
    var browserH = document.body.clientHeight;
    var popupH = $('.select').height();
    var cancleH = $('.cancle').height();
    var popTop = browserH/2;
    var popMarginTop = -(popupH/2);
    var cancleMarginTop = -(cancleH/2);

    $(document).ready(function(){
        $('.select').css({'top':popTop, 'margin-top':popMarginTop});
        $('.cancle, .ok').css({'top':popTop, 'margin-top':cancleMarginTop});

        //부모창 정보 불러오기
        var parent = $("#rows").val();
        var check = $("#total").val();

        for (var t=1; t<=parent; t++)
        {
            for (var c=1; c<=check; c++)
            {
                if ($("#detail_id_"+ t).val() == $("#check_"+c).attr("title"))
                {
                    var id = $("#check_"+c).attr("id");
                    $("#"+id).attr("disabled",true);
                    $("#"+id).attr("checked",true);
                }
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "BM" && $("#detail_id_"+ t).val() != "") {
                var k = 1;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "CD" && $("#detail_id_"+ t).val() != "") {
                var k = 2;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "PM" && $("#detail_id_"+ t).val() != "") {
                var k = 3;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "PL" && $("#detail_id_"+ t).val() != "") {
                var k = 4;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "기획" && $("#detail_id_"+ t).val() != "") {
                var k = 5;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "디자인" && $("#detail_id_"+ t).val() != "") {
                var k = 6;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "모션" && $("#detail_id_"+ t).val() != "") {
                var k = 7;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "개발(front-end)" && $("#detail_id_"+ t).val() != "") {
                var k = 8;
            }
            if ($("#detail_part_"+ t +" option:selected").val() == "개발(back-end)" && $("#detail_id_"+ t).val() != "") {
                var k = 9;
            }

            var n = Number($("#total_"+ k +"").val())+1;
            var DivList = "";

            DivList += "<li id='list_"+ k +"_"+ n +"' name='list_"+ k +"_'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_input' id='list_"+ k +"_input_"+ n +"' value='"+t+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_id' id='list_"+ k +"_id_"+ n +"' value='"+$("#detail_id_"+ t).val()+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_login' id='list_"+ k +"_login_"+ n +"' value='"+$("#detail_login_"+ t).val()+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_position' id='list_"+ k +"_position_"+ n +"' value='"+$("#detail_position_"+ t).val()+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_name' id='list_"+ k +"_name_"+ n +"' value='"+$("#detail_name_"+ t).val()+"'>";
            DivList += "	<p><span style='cursor:pointer' onclick=oneCheck('check_list_"+ k +"','list_"+ k +"_check','list_"+ k +"_"+ n +"');>" + $("#detail_position_"+ t).val() +" "+ $("#detail_name_"+ t).val() + "</span> <!--a href=javascript:oneDel('list_"+ k +"_"+ n +"','"+id+"','total_"+ k +"'); class='floatr'><img src='../img/project/btn_redx.gif' alt='삭제'></a--></p>";
            DivList += "</li>";
            $("#list_"+ k +"").append(DivList);
            $("#total_"+ k +"").val(n);
        }
        //담당자 선택
        $("#sel_click").attr("style","cursor:pointer;").click(function(){
            t = $("#sel").val();

            var total = $(":checkbox[name=check]:checked:enabled").length;
            if (total == 0)
            {
                alert("담당자를 선택해 주세요.");
                return;
            }

            var k = 0;
            var t_txt = "";
            if (t == 1) {	t_txt = "BM";	}
            if (t == 2) {	t_txt = "CD";	}
            if (t == 3) {	t_txt = "PM";	}
            if (t == 4) {	t_txt = "PL";	}
            if (t == 5) {	t_txt = "기획";	}
            if (t == 6) {	t_txt = "디자인";	}
            if (t == 7) {	t_txt = "모션";	}
            if (t == 8) {	t_txt = "개발(front-end)";	}
            if (t == 9) {	t_txt = "개발(back-end)";	}

            for (var j=1; j<=$("#rows").val(); j++)
            {
                if ($("#detail_part_"+ j +" option:selected").val() == t_txt)
                {
                    k = k + 1;
                }
            }
            var kids = $("#list_"+ t).children().length;

            if (k < kids+total)
            {
                alert(t_txt+"은 "+k+"명까지 선택 가능합니다.");
                return;
            }

            for (var i=1; i<=$("#total").val(); i++)
            {
                if ($("#check_"+ i).is(":checked") && $("#check_"+ i).is(":enabled"))
                {
                    var n = Number($("#total_"+t).val())+1;
                    var DivList = "";

                    DivList += "<li id='list_"+ t +"_"+ n +"' name='list_"+ t +"_'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_input' id='list_"+ t +"_input_"+ n +"' value=''>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_id' id='list_"+ t +"_id_"+ n +"' value='"+$("#sel_id_"+ i).val()+"'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_login' id='list_"+ t +"_login_"+ n +"' value='"+$("#sel_login_"+ i).val()+"'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_position' id='list_"+ t +"_position_"+ n +"' value='"+$("#sel_position_"+ i).val()+"'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_name' id='list_"+ t +"_name_"+ n +"' value='"+$("#sel_name_"+ i).val()+"'>";
                    DivList += "	<p><span style='cursor:pointer' onclick=oneCheck('check_list_"+ t +"','list_"+ t +"_check','list_"+ t +"_"+ n +"');>" + $("#sel_position_"+ i).val() +" "+ $("#sel_name_"+ i).val() + "</span> <!--a href=javascript:oneDel('list_"+ t +"_"+ n +"','check_"+ i +"','total_"+ t +"'); class='floatr'><img src='../img/project/btn_redx.gif' alt='삭제'></a--></p>";
                    DivList += "</li>";

                    $("#list_"+ t).append(DivList);

                    $("#check_"+ i).attr("disabled",true);
                    $("#total_"+ t).val(n);
                }
            }
        });

        //프로젝트 상세업무 추가
        $("[name=addBtn]").attr("style","cursor:pointer;").click(function(){
            addDetail($(this).attr("id").substr(4,1));
        });

        //검색
        $("#searchBtn").attr("style","cursor:pointer;").click(function(){
            $("#popup_form").attr("target","hdnFrame");
            $("#popup_form").attr("action","person_list.php");
            $("#popup_form").submit();
        });
        $("#search_name").keypress(function(e){
            if (e.keyCode == 13)
            {
                $("#popup_form").attr("target","hdnFrame");
                $("#popup_form").attr("action","person_list.php");
                $("#popup_form").submit();
            }
        });
        //취소
        $("#resetBtn").attr("style","cursor:pointer;").click(function(){
            $("#search_name").val("");
            $("#popup_form").attr("target","hdnFrame");
            $("#popup_form").attr("action","person_list.php");
            $("#popup_form").submit();
        });

        //창닫기
        $("#popup_select_close").attr("style","cursor:pointer;").click(function(){
            $("#popup_select").attr("style","display:none;");
        });
        //부모창 적용
        $("#popup_select_ok").attr("style","cursor:pointer;").click(function(){
            var total = $("#rows").val();

            for (var t=1; t<=total; t++)
            {
                if ($("#detail_view_"+t).val() == "")
                {
                    if ($("#detail_part_"+ t +" option:selected").val() == "BM") {
                        var k = 1;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "CD") {
                        var k = 2;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "PM") {
                        var k = 3;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "PL") {
                        var k = 4;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "기획") {
                        var k = 5;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "디자인") {
                        var k = 6;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "모션") {
                        var k = 7;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "개발(front-end)") {
                        var k = 8;
                    }
                    if ($("#detail_part_"+ t +" option:selected").val() == "개발(back-end)") {
                        var k = 9;
                    }

                    for (var i=1; i<=$("#list_"+ k +"").children().length; i++)
                    {
                        if ($("#list_"+ k +"_input_" + i).val() == "")
                        {
                            $("#detail_view_"+t).val($("#list_"+ k +"_position_" + i).val() +" "+ $("#list_"+ k +"_name_" + i).val());
                            $("#detail_id_"+t).val($("#list_"+ k +"_id_" + i).val());
                            $("#detail_login_"+t).val($("#list_"+ k +"_login_" + i).val());
                            $("#detail_position_"+t).val($("#list_"+ k +"_position_" + i).val());
                            $("#detail_name_"+t).val($("#list_"+ k +"_name_" + i).val());

                            $("#list_"+ k +"_input_" + i).val(t);

                            break;
                        }
                    }
                }
            }
            $("#popup_select").attr("style","display:none;");
        });
    });

    // 위로 이동
    function list_up()
    {
        var ul_id = $("#move_ul").val();
        var li_id = $("#"+ul_id).val();

        if (li_id == "")
        {
            alert("이동 할 담당자를 선택해 주세요");
            return;
        }
        else
        {
            // 위로 이동이 가능한지 확인
            var prev_item = $("#"+li_id).prev();

            if ($(prev_item).attr("id") == undefined) // id가 정의되어 있지 않다면 최상위 li 영역
                return;

            // 현재 선택된 li 를 제외시킨다.
            var selected_item = $("#"+li_id).detach();

            // 상위 li 다음에 삽입하여 위치를 교환시킨다.
            $(prev_item).before(selected_item);
        }
    }

    // 아래로 이동
    function list_down()
    {
        var ul_id = $("#move_ul").val();
        var li_id = $("#"+ul_id).val();

        if (li_id == "")
        {
            alert("이동 할 담당자를 선택해 주세요");
            return;
        }
        else
        {
            // 아래로 이동이 가능한지 확인
            var next_item = $("#"+li_id).next();

            if ($(next_item).attr("id") == undefined) // id가 정의되어 있지 않다면 최하위 li 영역
                return;

            // 현재 선택된 li 를 제외시킨다.
            var selected_item = $("#"+li_id).detach();

            // 하위 li 다음에 삽입하여 위치를 교환시킨다.
            $(next_item).after(selected_item);
        }
    }

    function oneCheck(a,b,c){
        $("#"+a).val(c);
        $("#"+c).parent().children().attr("style","background:#fff;");
        $("#"+c).attr("style","font-weight:bold;");
        $("#move_ul").val(a);
    }
    function oneDel(a,b,c){
        $("#"+a).remove();
        $("#"+b).attr("disabled",false);
        $("#"+b).attr("checked",false);
        $("#"+c).val(Number($("#"+c).val())-1);
    }

    function addDetail(type) {
        if ($("#real_rows").val() >= 30 )
        {
            alert("프로젝트 상세업무는 최대 30개까지 추가 가능합니다.");
            return;
        }

        $("#rows").val(Number($("#rows").val())+1);
        $("#real_rows").val(Number($("#real_rows").val())+1);

        var selected1, selected2, selected3, selected4, selected5, selected6, selected7, selected8, selected9
        if (type == "1") {	selected1 = " selected";	}
        if (type == "2") {	selected2 = " selected";	}
        if (type == "3") {	selected3 = " selected";	}
        if (type == "4") {	selected4 = " selected";	}
        if (type == "5") {	selected5 = " selected";	}
        if (type == "6") {	selected6 = " selected";	}
        if (type == "7") {	selected7 = " selected";	}
        if (type == "8") {	selected8 = " selected";	}
        if (type == "9") {	selected9 = " selected";	}

        var detail_i = $("#rows").val();
        var DetailDiv = "";

        DetailDiv +="<div class='board_list last' id='detail_"+detail_i+"' name='detail' title='"+detail_i+"'>";
        DetailDiv +="	<table class='notable work3 board_list'  style='width:100%'>";
        DetailDiv +="		<caption>게시판 리스트 테이블</caption>";
        DetailDiv +="		<colgroup>";
        DetailDiv +="			<col width='3%' />";
        DetailDiv +="			<col width='15%' />";
        DetailDiv +="			<col width='*' />";
        DetailDiv +="		</colgroup>";

        DetailDiv +="		<tbody class='p_detail border_b'>";

        DetailDiv +="			<tr>";
        DetailDiv +="				<td rowspan='4'>";
        DetailDiv +="					<a href=javascript:delDetail('"+detail_i+"');><img src='../img/project/btn_trash.gif' alt='제거' id='delDetail_"+detail_i+"' /></a>";
        DetailDiv +="				</td>";
        DetailDiv +="				<td>";
        DetailDiv +="					<select name='detail_part_"+detail_i+"' id='detail_part_"+detail_i+"' class='role' style='height:29px;'>";
        DetailDiv +="						<option value='BM'"+ selected1 +">BM</option>";
        DetailDiv +="						<option value='CD'"+ selected2 +">CD</option>";
        DetailDiv +="						<option value='PM'"+ selected3 +">PM</option>";
        DetailDiv +="						<option value='PL'"+ selected4 +">PL</option>";
        DetailDiv +="						<option value='기획'"+ selected5 +">기획</option>";
        DetailDiv +="						<option value='디자인'"+ selected6 +">디자인</option>";
        DetailDiv +="						<option value='모션'"+ selected7 +">모션</option>";
        DetailDiv +="						<option value='개발(front-end)'"+ selected8 +">개발(front-end)</option>";
        DetailDiv +="						<option value='개발(back-end)'"+ selected9 +">개발(back-end)</option>";
        DetailDiv +="					</select>";
        DetailDiv +="				</td>";
        DetailDiv +="				<td class='clearfix'>";
        DetailDiv +="					<input type='text' class='df_textinput' style='width:85%; border:1px solid #000;' name='detail_view_"+detail_i+"' id='detail_view_"+detail_i+"' value='' readonly>";
        DetailDiv +="					<input type='hidden' name='detail_id_"+detail_i+"' id='detail_id_"+detail_i+"' value=''>";
        DetailDiv +="					<input type='hidden' name='detail_login_"+detail_i+"' id='detail_login_"+detail_i+"' value=''>";
        DetailDiv +="					<input type='hidden' name='detail_position_"+detail_i+"' id='detail_position_"+detail_i+"' value=''>";
        DetailDiv +="					<input type='hidden' name='detail_name_"+detail_i+"' id='detail_name_"+detail_i+"' value=''>";
        DetailDiv +="					<a href=javascript:addPerson('"+detail_i+"'); style='float:right;'><img src='../img/project/btn_onCharge.gif' alt='담당자 선택' /></a>";
        DetailDiv +="				</td>";
        DetailDiv +="			</tr>";

        DetailDiv +="			<tr>";
        DetailDiv +="				<td>작업명</td>";
        DetailDiv +="				<td><input type='text' name='detail_detail_"+detail_i+"' id='detail_detail_"+detail_i+"' value='' maxlength='200' class='df_textinput' style='width:85%; border:1px solid #000;'></td>";
        DetailDiv +="			</tr>";

        DetailDiv +="			<tr>";
        DetailDiv +="				<td>프로젝트 참여율</td>";
        DetailDiv +="				<td>";
        DetailDiv +="					<div class='p_bar'><span id='part_bar_"+detail_i+"'></span></div>";
        DetailDiv +="					<select name='part_rate_"+detail_i+"' id='part_rate_"+detail_i+"' class='percentage' onchange=changeRate('"+detail_i+"');>";
        DetailDiv +="						<option value='0'>0%</option>";
        DetailDiv +="						<option value='10'>10%</option>";
        DetailDiv +="						<option value='20'>20%</option>";
        DetailDiv +="						<option value='30'>30%</option>";
        DetailDiv +="						<option value='40'>40%</option>";
        DetailDiv +="						<option value='50'>50%</option>";
        DetailDiv +="						<option value='60'>60%</option>";
        DetailDiv +="						<option value='70'>70%</option>";
        DetailDiv +="						<option value='80'>80%</option>";
        DetailDiv +="						<option value='90'>90%</option>";
        DetailDiv +="						<option value='100'>100%</option>";
        DetailDiv +="					</select>";
        DetailDiv +="				</td>";
        DetailDiv +="			</tr>";

        DetailDiv +="			<tr class='period'>";
        DetailDiv +="				<td>프로젝트 참여기간</td>";
        DetailDiv +="				<td class='clearfix' id='TimeZone_"+detail_i+"'>";
        DetailDiv +="					<select name='detail_fr_year_"+detail_i+"[]' id='detail_fr_year_"+detail_i+"_1'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "						<option value='<?=$i?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv +="					</select>";
        DetailDiv +="					<span>년</span>";
        DetailDiv +="					<select name='detail_fr_month_"+detail_i+"[]' id='detail_fr_month_"+detail_i+"_1'>";
        <?
        for ($i=1; $i<=12; $i++)
        {
        if ($i == Date("m")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "						<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv +="					</select>";
        DetailDiv +="					<span>월</span>";
        DetailDiv +="					<select name='detail_fr_day_"+detail_i+"[]' id='detail_fr_day_"+detail_i+"_1'>";
        <?
        for ($i=1; $i<=31; $i++)
        {
        if ($i == Date("d")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "						<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv +="					</select>";
        DetailDiv +="					<span>일</span>";
        DetailDiv +="					<input type='hidden' id='detail_fr_date_"+detail_i+"_1' class='datepicker'>";
        DetailDiv +="					<span>-</span>";
        DetailDiv +="					<select name='detail_to_year_"+detail_i+"[]' id='detail_to_year_"+detail_i+"_1'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "						<option value='<?=$i?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv +="					</select>";
        DetailDiv +="					<span>년</span>";
        DetailDiv +="					<select name='detail_to_month_"+detail_i+"[]' id='detail_to_month_"+detail_i+"_1'>";
        <?
        for ($i=1; $i<=12; $i++)
        {
        if ($i == Date("m")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "						<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv +="					</select>";
        DetailDiv +="					<span>월</span>";
        DetailDiv +="					<select name='detail_to_day_"+detail_i+"[]' id='detail_to_day_"+detail_i+"_1'>";
        <?
        for ($i=1; $i<=31; $i++)
        {
        if ($i == Date("d")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        }
        else
        {
            $j = $i;
        }
        ?>
        DetailDiv += "						<option value='<?=$j?>'<?=$selected?>><?=$i?></option>";
        <?
        }
        ?>
        DetailDiv +="					</select>";
        DetailDiv +="					<span>일</span>";
        DetailDiv +="					<input type='hidden' id='detail_to_date_"+detail_i+"_1' class='datepicker'>";
        DetailDiv +="					<div class='btn_right plus' >";
        DetailDiv +="						<a href=javascript:addTime('"+detail_i+"');><img src='../img/project/btn_plus.gif' alt='추가' id='addTime' /></a>";
        DetailDiv +="						<a href=javascript:delTime('"+detail_i+"');><img src='../img/project/btn_minus.gif' alt='제거' id='delTime' /></a>";
        DetailDiv +="						<input type='hidden' name='TimeSize_"+detail_i+"' id='TimeSize_"+detail_i+"' value='1'>";
        DetailDiv +="					</div>";
        DetailDiv +="				</td>";
        DetailDiv +="			</tr>";
        DetailDiv +="		</tbody>";
        DetailDiv +="	</table>";
        DetailDiv +="</div>";

        $("#DivaddDetail").append(DetailDiv);

        $(document).find("#detail_fr_date_"+detail_i+"_1").removeClass('hasDatepicker').datepicker({
            onSelect: function (selectedDate) {
                $("#detail_fr_year_"+detail_i+"_1").val( selectedDate.substring(6,10) );
                $("#detail_fr_month_"+detail_i+"_1").val( selectedDate.substring(0,2) );
                $("#detail_fr_day_"+detail_i+"_1").val( selectedDate.substring(3,5) );
            }
        });
        $(document).find("#detail_to_date_"+detail_i+"_1").removeClass('hasDatepicker').datepicker({
            onSelect: function (selectedDate) {
                $("#detail_to_year_"+detail_i+"_1").val( selectedDate.substring(6,10) );
                $("#detail_to_month_"+detail_i+"_1").val( selectedDate.substring(0,2) );
                $("#detail_to_day_"+detail_i+"_1").val( selectedDate.substring(3,5) );
            }
        });
    }
</script>
