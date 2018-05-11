<?
require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
require_once CMN_PATH."/login_check.php";
require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
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
                    $("#time_bar").attr("value","0");
                }
                else if (days > 100)
                {
                    $("#time_bar").attr("value","100");
                }
                else
                {
                    if ((all_days > 0 && now_days > 0) && (to_date > fr_date))
                    {
                        $("#time_bar").attr("value",days);
                    }
                    else
                    {
                        $("#time_bar").attr("value","0;");
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
                    $("#time_bar").attr("value","0");
                }
                else if (days > 100)
                {
                    $("#time_bar").attr("value","100");
                }
                else
                {
                    if ((all_days > 0 && now_days > 0) && (to_date > fr_date))
                    {
                        $("#time_bar").attr("value",days);
                    }
                    else
                    {
                        $("#time_bar").attr("value","0;");
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
                    $("#time_bar").attr("value","0");
                }
                else if (days > 100)
                {
                    $("#time_bar").attr("value","100");
                }
                else
                {
                    if ((all_days > 0 && now_days > 0) && (to_date > fr_date))
                    {
                        $("#time_bar").attr("value",days);
                    }
                    else
                    {
                        $("#time_bar").attr("value","0");
                    }
                }
            });
        });
        //프로젝트 전체 진행률 그래프 표시
        $("#progress").change(function(){
            $("#progress_bar").attr("value",this.value);
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
                $("#connectView").css("display","");
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

            $("#popup_ok").addClass("modal is-active");
        });
        $("#popup_ok_ok").attr("style","cursor:pointer;").click(function(){
            $("#form").attr("target","hdnFrame");
            $("#form").attr("action","project_write_act.php");
            $("#form").submit();
        });
        $("#popup_ok_no").attr("style","cursor:pointer;").click(function(){
            $("#popup_ok").removeClass("is-active");
        });
        $("#popup_ok_close").attr("style","cursor:pointer;").click(function(){
            $("#popup_ok").removeClass("is-active");
        });
        //취소
        $("#btnCancel").attr("style","cursor:pointer;").click(function(){
            $("#popup_cancel").addClass("is-active");
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
            $("#popup_cancel_no").removeClass("is-active");
        });
        $("#popup_cancel_close").attr("style","cursor:pointer;").click(function(){
            $("#popup_cancel_close").removeClass("is-active");
        });

        //프로젝트 상세업무 추가
        $("#addDetail").attr("style","cursor:pointer;").click(function(){
            addDetail('');
        });
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
        $("#popup_select").addClass("modal is-active");
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

        DetailDiv +="				<div class='columns' id='addTime_"+detail_i+"_"+i+"' >";
        DetailDiv +="                   <div class='column' style='display:inline-block;flex-grow:0;flex-basis:auto;'>";
        DetailDiv +="                       <div class='field is-group'>";
        DetailDiv +="                           <div class='control select'>";
        DetailDiv +="				            <select name='detail_fr_year_"+detail_i+"[]' id='detail_fr_year_"+detail_i+"_"+i+"'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "				                	<option value='<?=$i?>'<?=$selected?>><?=$i?>년</option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				</div>";
        DetailDiv +="                           <div class='control select'>";
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
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?>월</option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				</div>";
        DetailDiv +="                           <div class='control select'>";
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
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?>일</option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				</div>";
        DetailDiv +="                <div class='button'>";
        DetailDiv += "				<input type='hidden' id='detail_fr_date_"+detail_i+"_"+i+"' class='datepicker'>";
        DetailDiv += "				</div>";
        DetailDiv += "				</div>";
        DetailDiv += "				</div>";
        DetailDiv +="            <div class='column'>";
        DetailDiv +="                    <div class='control select'>";
        DetailDiv += "				<select name='detail_to_year_"+detail_i+"[]' id='detail_to_year_"+detail_i+"_"+i+"'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "					<option value='<?=$i?>'<?=$selected?>><?=$i?>년</option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				</div>";
        DetailDiv +="                           <div class='control select'>";
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
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?>월</option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				</div>";
        DetailDiv +="                           <div class='control select'>";
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
        DetailDiv += "					<option value='<?=$j?>'<?=$selected?>><?=$i?>일</option>";
        <?
        }
        ?>
        DetailDiv += "				</select>";
        DetailDiv += "				</div>";
        DetailDiv +="               <div class='button'>";
        DetailDiv += "				<input type='hidden' id='detail_to_date_"+detail_i+"_"+i+"' class='datepicker'>";
        DetailDiv += "				</div>";
        DetailDiv += "				</div>";
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
<form name="form" id="form" method="post">
    <? include INC_PATH."/top_menu.php"; ?>
    <input type="hidden" name="page" id="page" value="<?=$page?>">
    <input type="hidden" name="type" id="type" value="<?=$type?>">
    <input type="hidden" name="no" id="no" value="<?=$no?>">
    <input type="hidden" name="name" id="name" value="<?=$name?>">
    <input type="hidden" name="project_no" id="project_no" value="<?=$project_no?>">
    <input type="hidden" name="mode" id="mode" value="<?=$mode?>">
    <? include INC_PATH."/project_menu.php"; ?>

    <!-- 본문 시작 -->
    <section class="section df-project">
        <div class="container">
            <nav class="level is-mobile">
                <div class="level-left">
                    <p>
                        <span><? if ($type == "ING") { ?>진행 프로젝트<? } else if ($type == "END") { ?>완료 프로젝트<? } ?></span>
                    </p>
                </div></nav>



            <div class="content">
                <div class="field">
                    <div class="control">
                        <input id="title" name="title" class="input is-large" type="text" placeholder="프로젝트명" value="<?=$title?>"<? if ($mode == "modify" && ($write_id != $prs_id && $prf_id != "4")) { ?> readonly<? } ?> /><input id="title" name="title_prev" type="hidden" value="<?=$title?>" />
                    </div>
                </div>
                <? if ($mode == "modify" && $prf_id == "4") { ?>
                    <div class="columns is-column-marginless">
                        <div class="column is-half">
                            <div class="field is-grouped">
                                <input type="checkbox" id="connect" name="connect" value="Y" class="is-checkradio is-info" >
                                <label for="connect" class="is-size-7">
                                    * 프로젝트 번호를 변경하시는 경우 체크하고, 연결 프로젝트를 선택해 주세요.
                                </label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <div class="control select is-fullwidth">
                                    <select id="link" name="link">
                                        <option value="">연결 프로젝트 선택 </option>
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
                            </div>

                        </div>
                    </div>
                <? } ?>

                <? if ($mode == "write") { ?>
                    <div class="columns is-column-marginless">
                        <div class="column is-half">
                            <div class="field is-grouped">
                                <input class="is-checkradio is-info" type="checkbox" id="connect" name="connect" value="Y" >
                                <label for="connect" class="is-size-7">
                                    * 이미 등록된 프로젝트의 파생 프로젝트인 경우 체크해 주세요.
                                </label>
                            </div>
                        </div>
                        <div class="column">
                            <div class="field">
                                <div class="control select is-fullwidth" id="connectView" style="display:none">
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
                            </div>
                        </div>
                    </div>
                <? } ?>
                <div class="field">
                    <div class="control">
                        <textarea class="textarea" placeholder="프로젝트 설명" name="contents" id="contents" rows="4"><?=$contents?></textarea>
                    </div>
                </div>

                <div class="columns">
                    <div class="column" style="display:inline-block;flex-grow:0;flex-basis:auto;">
                        <div class="field is-group">
                            <div class="control select">
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

                                        echo "<option value='".$i."'".$selected.">".$i."년</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="control select">
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

                                        echo "<option value='".$j."'".$selected.">".$i."월</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="control select">
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

                                        echo "<option value='".$j."'".$selected.">".$i."일</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="button">
                                <input type="hidden" id="fr_date" class="datepicker">
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field is-group">
                            <div class="control select">
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

                                        echo "<option value='".$i."'".$selected.">".$i."년</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="control select">
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

                                        echo "<option value='".$j."'".$selected.">".$i."월</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="control select">
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

                                        echo "<option value='".$j."'".$selected.">".$i."일</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="button">
                                <input type="hidden" id="to_date" class="datepicker">
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="content">
                <div class="box">
                    <div class="columns">
                        <div class="column">
                            <div class="level is-mobile">
                                <div class="level-left is-title-column">
                                    <p class="title is-size-6">기간 경과율<br><br></p>
                                </div>
                                <div class="level-right">
                                    <div class="field">
                                        <div class="control select" style="display:none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <progress class="progress is-small" id="time_bar" value="<?=$time_bar?>" max="100">0%</progress>
                        </div>
                        <div class="column">
                            <div class="level is-mobile">
                                <div class="level-left is-title-column">
                                    <p class="title is-size-6">프로젝트 전체 진행율</p>
                                </div>
                                <div class="level-right">
                                    <div class="field">
                                        <div class="control select">
                                            <select name="progress" id="progress" id="progress">
                                                <?
                                                for($i=0; $i<=100; $i+=5)
                                                {
                                                    ?>
                                                    <option value="<?=$i?>"<? if ($progress == $i) { echo " selected"; } ?>><?=$i?>%</option>
                                                    <?
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <progress class="progress is-small" id="progress_bar" value="<?=$progress_bar?>" max="100"><?=$progress_bar?>%</progress>
                        </div>
                    </div>
                </div>



                <?
                if ($mode == "modify") {
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
                    $rs = sqlsrv_query($dbConn, $sql);

                    $rows = sqlsrv_has_rows($rs);

                    $detail_i = 1;
                    if ($rows > 0) {
                        while ($record = sqlsrv_fetch_array($rs)) {
                            $detail_part = $record['PART'];
                            $detail_detail = $record['DETAIL'];
                            $detail_part_rate = $record['PART_RATE'];$detail_id = $record['PRS_ID'];
                            $detail_login = $record['PRS_LOGIN'];
                            $detail_name = $record['PRS_NAME'];
                            $detail_position = $record['PRS_POSITION'];
                            $detail_part_bar = $detail_part_rate;
                            $time_cnt = $record['CNT'];
                            ?>
                            <div class="content">
                        <div class="notification is-bordered" id="detail_<?= $detail_i ?>" name="<?= $detail_i ?>">
                            <a class="delete" href="javascript:delDetail('<?= $detail_i ?>');" id="delDetail_<?= $detail_i ?>">
                            </a>
                            <div class="columns">
                                <div class="column is-one-fifth">
                                    <div class="field is-grouped">
                                        <div class="control select is-fullwidth">
                                            <select name="detail_part_<?= $detail_i ?>" id="detail_part_<?= $detail_i ?>">
                                                <option value="BM"<? if ($detail_part == "BM") {echo " selected";} ?>>BM</option>
                                                <option value="CD"<? if ($detail_part == "CD") {echo " selected";} ?>>CD</option>
                                                <option value="PM"<? if ($detail_part == "PM") {echo " selected";} ?>>PM</option>
                                                <option value="PL"<? if ($detail_part == "PL") {echo " selected";} ?>>PL</option>
                                                <option value="기획"<? if ($detail_part == "기획") {echo " selected";} ?>>기획</option>
                                                <option value="디자인"<? if ($detail_part == "디자인") {echo " selected";} ?>>디자인</option>
                                                <option value="모션"<? if ($detail_part == "모션") {echo " selected";} ?>>모션</option>
                                                <option value="개발(front-end)"<? if ($detail_part == "개발(front-end)") {echo " selected";} ?>>개발(front-end)</option>
                                                <option value="개발(back-end)"<? if ($detail_part == "개발(back-end)") {echo " selected";} ?>>개발(back-end)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field has-addons">
                                        <div class="control is-expanded">
                                            <input class="input" type="text" name="detail_view_<?= $detail_i ?>" id="detail_view_<?= $detail_i ?>" value="<?= $detail_position ?> <?= $detail_name ?>" readonly>
                                            <input type="hidden" name="detail_id_<?= $detail_i ?>" id="detail_id_<?= $detail_i ?>" value="<?= $detail_id ?>">
                                            <input type="hidden" name="detail_login_<?= $detail_i ?>" id="detail_login_<?= $detail_i ?>" value="<?= $detail_login ?>">
                                            <input type="hidden" name="detail_position_<?= $detail_i ?>" id="detail_position_<?= $detail_i ?>" value="<?= $detail_position ?>">
                                            <input type="hidden" name="detail_name_<?= $detail_i ?>" id="detail_name_<?= $detail_i ?>" value="<?= $detail_name ?>">
                                        </div>
                                        <div class="control"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input class="input" placeholder="작업명" type="text" name="detail_detail_<?= $detail_i ?>"
                                           id="detail_detail_<?= $detail_i ?>" value="<?= $detail_detail ?>" maxlength="200">
                                </div>
                            </div>

                            <div class="level is-mobile">
                                <div class="level-left is-title-column">
                                    <p class="title is-size-6">프로젝트 참여율</p>
                                </div>
                                <div class="level-right">
                                    <div class="field">
                                        <div class="control select">
                                            <select name="part_rate_<?= $detail_i ?>" id="part_rate_<?= $detail_i ?>"
                                                    onchange="changeRate('<?= $detail_i ?>');">
                                                <option value="0"<? if ($detail_part_rate == 0) {echo " selected";} ?>>0%</option>
                                                <option value="10"<? if ($detail_part_rate == 10) {echo " selected";} ?>>10%</option>
                                                <option value="20"<? if ($detail_part_rate == 20) {echo " selected";} ?>>20%</option>
                                                <option value="30"<? if ($detail_part_rate == 30) {echo " selected";} ?>>30%</option>
                                                <option value="40"<? if ($detail_part_rate == 40) {echo " selected";} ?>>40%</option>
                                                <option value="50"<? if ($detail_part_rate == 50) {echo " selected";} ?>>50%</option>
                                                <option value="60"<? if ($detail_part_rate == 60) {echo " selected";} ?>>60%</option>
                                                <option value="70"<? if ($detail_part_rate == 70) {echo " selected";} ?>>70%</option>
                                                <option value="80"<? if ($detail_part_rate == 80) {echo " selected";} ?>>80%</option>
                                                <option value="90"<? if ($detail_part_rate == 90) {echo " selected";} ?>>90%</option>
                                                <option value="100"<? if ($detail_part_rate == 100) {echo " selected";} ?>>100%</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box" id="TimeZone_<?= $detail_i ?>">

                            <?
                            $sql1 = "SELECT 
                                    CONVERT(char(10),START_DATE,102) AS START_DATE, CONVERT(char(10),END_DATE,102) AS END_DATE 
                                FROM 
                                    DF_PROJECT_DETAIL WITH(NOLOCK) 
                                WHERE
                                    PROJECT_NO = '$project_no' AND PRS_ID = '$detail_id'
                                ORDER BY 
                                    SORT";
                            $rs1 = sqlsrv_query($dbConn, $sql1);

                            $t = 1;
                            while ($record1 = sqlsrv_fetch_array($rs1)) {
                                $detail_start_date = $record1['START_DATE'];
                                $detail_end_date = $record1['END_DATE'];

                                $detail_fr_year = substr($detail_start_date, 0, 4);
                                $detail_fr_month = substr($detail_start_date, 5, 2);
                                $detail_fr_day = substr($detail_start_date, 8, 2);
                                $detail_to_year = substr($detail_end_date, 0, 4);
                                $detail_to_month = substr($detail_end_date, 5, 2);
                                $detail_to_day = substr($detail_end_date, 8, 2);
                                ?>
                                <div class="columns" id="addTime_<?= $detail_i ?>_<?=$t?>">
                                        <div class="column" style="display:inline-block;flex-grow:0;flex-basis:auto;">

                                            <script type="text/javascript">
                                                $(document).ready(function () {
                                                    $("#detail_fr_date_<?=$detail_i?>_<?=$t?>").val($("#detail_fr_month_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_fr_day_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_fr_year_<?=$detail_i?>_<?=$t?>").val());
                                                    $("#detail_to_date_<?=$detail_i?>_<?=$t?>").val($("#detail_to_month_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_to_day_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_to_year_<?=$detail_i?>_<?=$t?>").val());

                                                    $("#detail_fr_year_<?=$detail_i?>_<?=$t?>, #detail_fr_month_<?=$detail_i?>_<?=$t?>, #detail_fr_day_<?=$detail_i?>_<?=$t?>").change(function () {
                                                        $("#detail_fr_date_<?=$detail_i?>_<?=$t?>").val($("#detail_fr_month_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_fr_day_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_fr_year_<?=$detail_i?>_<?=$t?>").val());
                                                    });
                                                    $("#detail_to_year_<?=$detail_i?>_<?=$t?>, #detail_to_month_<?=$detail_i?>_<?=$t?>, #detail_to_day_<?=$detail_i?>_<?=$t?>").change(function () {
                                                        $("#detail_to_date_<?=$detail_i?>_<?=$t?>").val($("#detail_to_month_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_to_day_<?=$detail_i?>_<?=$t?>").val() + "/" + $("#detail_to_year_<?=$detail_i?>_<?=$t?>").val());
                                                    });

                                                    $("#detail_fr_date_<?=$detail_i?>_<?=$t?>").datepicker({
                                                        onSelect: function (selectedDate) {
                                                            $("#detail_fr_year_<?=$detail_i?>_<?=$t?>").val(selectedDate.substring(6, 10));
                                                            $("#detail_fr_month_<?=$detail_i?>_<?=$t?>").val(selectedDate.substring(0, 2));
                                                            $("#detail_fr_day_<?=$detail_i?>_<?=$t?>").val(selectedDate.substring(3, 5));
                                                        }
                                                    });
                                                    $("#detail_to_date_<?=$detail_i?>_<?=$t?>").datepicker({
                                                        onSelect: function (selectedDate) {
                                                            $("#detail_to_year_<?=$detail_i?>_<?=$t?>").val(selectedDate.substring(6, 10));
                                                            $("#detail_to_month_<?=$detail_i?>_<?=$t?>").val(selectedDate.substring(0, 2));
                                                            $("#detail_to_day_<?=$detail_i?>_<?=$t?>").val(selectedDate.substring(3, 5));
                                                        }
                                                    });
                                                });
                                            </script>
                                            <div class="field is-group">
                                                <div class="control select">
                                                    <select name="detail_fr_year_<?= $detail_i ?>[]"
                                                            id="detail_fr_year_<?= $detail_i ?>_<?= $t ?>">
                                                        <?
                                                        for ($i = $startYear; $i <= ($detail_fr_year + 1); $i++) {
                                                            if ($i == $detail_fr_year) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }

                                                            echo "<option value='" . $i . "'" . $selected . ">" . $i . "년</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="control select">
                                                    <select name="detail_fr_month_<?= $detail_i ?>[]"
                                                            id="detail_fr_month_<?= $detail_i ?>_<?= $t ?>">
                                                        <?
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            if (strlen($i) == "1") {
                                                                $j = "0" . $i;
                                                            } else {
                                                                $j = $i;
                                                            }

                                                            if ($j == $detail_fr_month) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }

                                                            echo "<option value='" . $j . "'" . $selected . ">" . $i . "월</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="control select">
                                                    <select name="detail_fr_day_<?= $detail_i ?>[]"
                                                            id="detail_fr_day_<?= $detail_i ?>_<?= $t ?>">
                                                        <?
                                                        for ($i = 1; $i <= 31; $i++) {
                                                            if (strlen($i) == "1") {
                                                                $j = "0" . $i;
                                                            } else {
                                                                $j = $i;
                                                            }

                                                            if ($j == $detail_fr_day) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }

                                                            echo "<option value='" . $j . "'" . $selected . ">" . $i . "일</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="button">
                                                    <input type="hidden" id="detail_fr_date_<?= $detail_i ?>_<?= $t ?>" class="datepicker">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <div class="field is-group">
                                                <div class="control select">
                                                    <select name="detail_to_year_<?= $detail_i ?>[]"
                                                            id="detail_to_year_<?= $detail_i ?>_<?= $t ?>">
                                                        <?
                                                        for ($i = $startYear; $i <= ($detail_to_year + 1); $i++) {
                                                            if ($i == $detail_to_year) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }

                                                            echo "<option value='" . $i . "'" . $selected . ">" . $i . "년</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="control select">
                                                    <select name="detail_to_month_<?= $detail_i ?>[]"
                                                            id="detail_to_month_<?= $detail_i ?>_<?= $t ?>">
                                                        <?
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            if (strlen($i) == "1") {
                                                                $j = "0" . $i;
                                                            } else {
                                                                $j = $i;
                                                            }

                                                            if ($j == $detail_to_month) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }

                                                            echo "<option value='" . $j . "'" . $selected . ">" . $i . "월</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="control select">
                                                    <select name="detail_to_day_<?= $detail_i ?>[]"
                                                            id="detail_to_day_<?= $detail_i ?>_<?= $t ?>">
                                                        <?
                                                        for ($i = 1; $i <= 31; $i++) {
                                                            if (strlen($i) == "1") {
                                                                $j = "0" . $i;
                                                            } else {
                                                                $j = $i;
                                                            }

                                                            if ($j == $detail_to_day) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }

                                                            echo "<option value='" . $j . "'" . $selected . ">" . $i . "일</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="button">
                                                    <input type="hidden" id="detail_to_date_<?= $detail_i ?>_<?= $t ?>"  class="datepicker">
                                                </div>
                                            </div>
                                        </div>
                                        <? if ($t == 1) { ?>
                                            <!-- <div class="column last-button">-->
                                            <span class="buttons">
                                        <a class="button is-fullwidth" href="javascript:addTime('<?= $detail_i ?>');" id="addTime">
                                            <span class="icon is-small">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                        </a>
                                    </span>&nbsp;
                                            <span class="buttons">
                                        <a class="button is-fullwidth" href="javascript:delTime('<?= $detail_i ?>');" id="delTime">
                                            <span class="icon is-small">
                                                <i class="fas fa-minus"></i>
                                            </span>
                                        </a>
                                    </span>
                                            <!--</div>-->
                                            <input type="hidden" name="TimeSize_<?=$rows?>" id="TimeSize_<?=$rows?>" value="1">
                                        <? } ?>

                                    </div>
                                    <input type="hidden" name="TimeSize_<?=$detail_i?>" id="TimeSize_<?=$detail_i?>" value="<?=$time_cnt?>">
                                    <?
                                    $t++;
                                    }?>
                                </div>
                                </div>

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


                <hr>
                <!--디폴트 작성-->
                <div class="content">
                    <div class="notification is-bordered"  id="detail_<?=$rows?>" name="<?=$rows?>">
                        <!--<a class="delete" href="javascript:delDetail('<?= $detail_i ?>');" id="delDetail_<?= $detail_i ?>"></a>-->
                        <div class="columns">
                            <div class="column is-one-fifth">
                                <div class="field is-grouped">
                                    <div class="control select is-fullwidth">
                                        <select name="detail_part_<?=$rows?>" id="detail_part_<?=$rows?>">
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
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="field has-addons">
                                    <div class="control is-expanded">
                                        <input class="input" type="text" name="detail_view_<?=$rows?>" value="" readonly id="detail_view_<?=$rows?>">
                                        <input type="hidden" name="detail_id_<?=$rows?>" id="detail_id_<?=$rows?>" value="">
                                        <input type="hidden" name="detail_login_<?=$rows?>" id="detail_login_<?=$rows?>" value="">
                                        <input type="hidden" name="detail_position_<?=$rows?>" id="detail_position_<?=$rows?>" value="">
                                        <input type="hidden" name="detail_name_<?=$rows?>" id="detail_name_<?=$rows?>" value="">
                                    </div>
                                    <div class="control">
                                        <a class="button" href="javascript:addPerson('<?=$rows?>');">
                                            <span>담당자 선택</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input class="input" type="text" placeholder="작업명" name="detail_detail_<?=$rows?>" id="detail_detail_<?=$rows?>" value="" maxlength="200">
                            </div>
                        </div>

                        <div class="level is-mobile">
                            <div class="level-left is-title-column">
                                <p class="title is-size-6">프로젝트 참여율</p>
                            </div>
                            <div class="level-right">
                                <div class="field">
                                    <div class="control select">
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
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="box" id="TimeZone_<?=$rows?>">
                            <div class="columns">
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
                                <div class="column" style="display:inline-block;flex-grow:0;flex-basis:auto;">
                                    <div class="field is-group">
                                        <div class="control select">
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

                                                    echo "<option value='".$i."'".$selected.">".$i."년</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="control select">
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

                                                    echo "<option value='".$j."'".$selected.">".$i."월</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="control select">
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

                                                    echo "<option value='".$j."'".$selected.">".$i."일</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="button">
                                            <input type="hidden" id="detail_fr_date_<?=$rows?>_1" class="datepicker">
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field is-group">
                                        <div class="control select">
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

                                                    echo "<option value='".$i."'".$selected.">".$i."년</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="control select">
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

                                                    echo "<option value='".$j."'".$selected.">".$i."월</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="control select">
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

                                                    echo "<option value='".$j."'".$selected.">".$i."일</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="button">
                                            <input type="hidden" id="detail_to_date_<?=$rows?>_1" class="datepicker">
                                        </div>
                                    </div>
                                </div>
                                <!--<div class="column last-button">-->
                            <span class="buttons">
                                <a class="button is-fullwidth" href="javascript:addTime('<?=$rows?>');">
                                    <span class="icon is-small">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </a>
                            </span>&nbsp;
                            <span class="buttons">
                                <a class="button is-fullwidth" href="javascript:delTime('<?=$rows?>');">
                                    <span class="icon is-small">
                                        <i class="fas fa-minus"></i>
                                    </span>
                                </a>
                            </span>
                                <input type="hidden" name="TimeSize_<?=$rows?>" id="TimeSize_<?=$rows?>" value="1">
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?
            }
            ?>

            <div class="content" id="DivaddDetail"></div>

            <hr>
            <input type="hidden" name="rows" id="rows" value="<?=$rows?>">
            <input type="hidden" name="real_rows" id="real_rows" value="<?=$rows?>">


            <div class="content">
                <a class="button is-fullwidth is-medium is-light is-bordered" id="addDetail">
                    <span class="icon is-small">
                        <i class="fas fa-user-plus"></i>
                    </span>
                    <span>담당자 추가하기</span>
                </a>
            </div>


            <nav class="level is-mobile">
                <div class="level-left">
                    <p class="buttons">
                        <a href="project_list.php" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                            <span>목록</span>
                        </a>
                    </p>
                </div>

                <div class="level-right">
                    <? if ($mode == "write") { ?>
                        <p class="buttons">
                            <a class="button is-danger" id="btnWrite">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                                <span>등록</span>
                            </a>
                        </p>
                    <? } else if ($mode == "modify") { ?>
                        <p class="buttons">
                            <a class="button is-danger" id="btnWrite">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                                <span>수정</span>
                            </a>
                        </p>
                    <? } ?>
                </div>
            </nav>

        </div>
        </div>
</form>


<!--new 팝업3-->
<div id="popup_cancel" class="modal" >
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title"><?=$mode_title?></p>
            <a class="close" id="popup_cancel_close"></a>
        </header>
        <section class="modal-card-body">
            <p>프로젝트 <?=$mode_title?>을 취소 하시겠습니까?</p>
        </section>
        <footer class="modal-card-foot">
            <a class="button is-success id="popup_cancel_ok" ">확인</button>&nbsp;&nbsp;
            <a class="button is-error" id="popup_cancel_no">취소</a>
        </footer>
    </div>
</div>

<div id="popup_ok" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title"><?=$mode_title?></p>
            <a class="close" id="popup_ok_close"></a>
        </header>
        <section class="modal-card-body">
            <p>프로젝트를 <?=$mode_title?> 하시겠습니까?</p>
        </section>
        <footer class="modal-card-foot">
            <a class="button is-success" id="popup_ok_ok">확인</a>&nbsp;&nbsp;
            <a class="button is-error" id="popup_ok_no">취소</a>
        </footer>
    </div>
</div>

<!--담당자 선택 -->
<div class="modal" id="popup_select">
    <form name="popup_form" id="popup_form" method="post">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title is-size-6">담당자 선택</p>
                <a class="delete" aria-label="close" id="popup_select_close"></a>
            </header>
            <section class="modal-card-body">
                <div class="columns">
                    <div class="column">
                        <div class="panel">
                            <div class="panel-heading">
                                <div class="field">
                                    <div class="control select is-small is-fullwidth">
                                        <select name="sel" id="sel">
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
                                </div>
                            </div>
                            <div class="panel-block">
                                <p class="control has-icons-left">
                                    <input id="search_name" name="search_name"  class="input is-small" type="text" placeholder="검색">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                    </span>
                                </p>
                            </div>
                            <div class="panel-block">
                                <div class="search-result" id="person_list">
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
                                        <div class="search-member">
                                            <input type="checkbox" id="check_<?=$i?>" name="check" title="<?=$id?>">
                                            <span class="is-size-7"><label for="check_<?=$i?>" style="cursor:pointer;"><?=$position?> <?=$name?></label></span>
                                        </div>
                                        <?
                                    }
                                    ?>
                                    <input type="hidden" name="total" id="total" value="<?=$i?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column last-button has-text-centered">
                        <a class="button" id="sel_click">
                            <span class="icon is-small">
                                <i class="fas fa-angle-right is-hidden-mobile"></i>
                                <i class="fas fa-angle-down is-hidden-tablet"></i>
                            </span>
                        </a>
                    </div>
                    <div class="column">
                        <div class="card is-shadowless is-bordered">
                            <div class="card-content">
                                <div class="member-result">

                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">BM</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_1" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_1" id="total_1" value="0">
                                    <p class="is-size-7" id="list_1" ></p>
                                    <input type="hidden" name="check_list_1" id="check_list_1">

                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">CD</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_2" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_2" id="total_2" value="0">
                                    <p class="is-size-7" id="list_2" ></p>
                                    <input type="hidden" name="check_list_2" id="check_list_2">

                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">PM</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_3" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_3" id="total_3" value="0">
                                    <p class="is-size-7" id="list_3" ></p>
                                    <input type="hidden" name="check_list_3" id="check_list_3">


                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">PL</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_4" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_4" id="total_4" value="0">
                                    <p class="is-size-7" id="list_4" ></p>
                                    <input type="hidden" name="check_list_4" id="check_list_4">

                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">기획</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_5" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_5" id="total_5" value="0">
                                    <p class="is-size-7" id="list_5" ></p>
                                    <input type="hidden" name="check_list_5" id="check_list_5">

                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">디자인</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_6" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_6" id="total_6" value="0">
                                    <p class="is-size-7" id="list_6" ></p>
                                    <input type="hidden" name="check_list_6" id="check_list_6">

                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">모션</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_7" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_7" id="total_7" value="0">
                                    <p class="is-size-7" id="list_7" ></p>
                                    <input type="hidden" name="check_list_7" id="check_list_7">

                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">개발(front-end)</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_8" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_8" id="total_8" value="0">
                                    <p class="is-size-7" id="list_8" ></p>
                                    <input type="hidden" name="check_list_8" id="check_list_8">


                                    <div class="member-title">
                                        <div class="columns is-mobile">
                                            <div class="column">
                                                <span class="title is-size-7">개발(back-end)</span>
                                            </div>
                                            <div class="column last-button">
                                                <a class="button is-small" id="add_9" name="addBtn" >
                                                    <span class="icon is-small">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="total_9" id="total_9" value="0">
                                    <p class="is-size-7" id="list_9" ></p>
                                    <input type="hidden" name="check_list_9" id="check_list_9">

                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="javascript:list_up();" class="card-footer-item is-size-7">
                                    위로 직원이동
                                    <span class="icon is-small">
                                        <i class="fas fa-angle-up"></i>
                                    </span>
                                </a>
                                <a href="javascript:list_down();" class="card-footer-item  is-size-7">
                                    아래로 직원이동
                                    <span class="icon is-small">
                                        <i class="fas fa-angle-down"></i>
                                    </span>
                                </a>
                                <input type="hidden" name="move_ul" id="move_ul">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <footer class="modal-card-foot">
                <a id="popup_select_ok" class="button is-primary">확인</a>
                <!--<a class="button">취소</a>-->
            </footer>
        </div>
</div>
<!--담당자 선택-->

</section>
<!-- 본문 끌 -->
<? include INC_PATH."/bottom.php"; ?>
</form>
</div>
</body>
</html>


<script type="text/JavaScript">


    $(document).ready(function(){
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

            DivList += "<p id='list_"+ k +"_"+ n +"' name='list_"+ k +"_'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_input' id='list_"+ k +"_input_"+ n +"' value='"+t+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_id' id='list_"+ k +"_id_"+ n +"' value='"+$("#detail_id_"+ t).val()+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_login' id='list_"+ k +"_login_"+ n +"' value='"+$("#detail_login_"+ t).val()+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_position' id='list_"+ k +"_position_"+ n +"' value='"+$("#detail_position_"+ t).val()+"'>";
            DivList += "	<input type='hidden' name='list_"+ k +"_name' id='list_"+ k +"_name_"+ n +"' value='"+$("#detail_name_"+ t).val()+"'>";
            DivList += "	<span style='cursor:pointer' onclick=oneCheck('check_list_"+ k +"','list_"+ k +"_check','list_"+ k +"_"+ n +"');>" + $("#detail_position_"+ t).val() +" "+ $("#detail_name_"+ t).val() + "</span> ";
            DivList += "</p>";
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

                    DivList += "<p id='list_"+ t +"_"+ n +"' name='list_"+ t +"_'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_input' id='list_"+ t +"_input_"+ n +"' value=''>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_id' id='list_"+ t +"_id_"+ n +"' value='"+$("#sel_id_"+ i).val()+"'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_login' id='list_"+ t +"_login_"+ n +"' value='"+$("#sel_login_"+ i).val()+"'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_position' id='list_"+ t +"_position_"+ n +"' value='"+$("#sel_position_"+ i).val()+"'>";
                    DivList += "	<input type='hidden' name='list_"+ t +"_name' id='list_"+ t +"_name_"+ n +"' value='"+$("#sel_name_"+ i).val()+"'>";
                    DivList += "	<span style='cursor:pointer' onclick=oneCheck('check_list_"+ t +"','list_"+ t +"_check','list_"+ t +"_"+ n +"');>" + $("#sel_position_"+ i).val() +" "+ $("#sel_name_"+ i).val() + "</span> ";
                    DivList += "</p>";

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
            $("#popup_select").removeClass("is-active");

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
            //$("#popup_select").attr("style","display:none;");
            $("#popup_select").removeClass("is-active");
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
        $("#"+c).attr("style","font-weight:999;");
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
//담당자 추가 html
        DetailDiv +="<div class='notification is-bordered' id='detail_"+detail_i+"' name='detail' title='"+detail_i+"'>";
        DetailDiv +="   <a class='delete' href=javascript:delDetail('"+detail_i+"'); id='delDetail_"+detail_i+"'></a>";
        DetailDiv +="	    <div class='columns'>";
        DetailDiv +="		    <div class='column is-one-fifth'>";
        DetailDiv +="		        <div class='field is-grouped'>";
        DetailDiv +="			        <div class='control select is-fullwidth'>";
        DetailDiv +="				    	<select name='detail_part_"+detail_i+"' id='detail_part_"+detail_i+"'>";
        DetailDiv +="					    	<option value='BM'"+ selected1 +">BM</option>";
        DetailDiv +="						    <option value='CD'"+ selected2 +">CD</option>";
        DetailDiv +="						    <option value='PM'"+ selected3 +">PM</option>";
        DetailDiv +="						    <option value='PL'"+ selected4 +">PL</option>";
        DetailDiv +="						    <option value='기획'"+ selected5 +">기획</option>";
        DetailDiv +="						    <option value='디자인'"+ selected6 +">디자인</option>";
        DetailDiv +="						    <option value='모션'"+ selected7 +">모션</option>";
        DetailDiv +="						    <option value='개발(front-end)'"+ selected8 +">개발(front-end)</option>";
        DetailDiv +="						    <option value='개발(back-end)'"+ selected9 +">개발(back-end)</option>";
        DetailDiv +="					    </select>";
        DetailDiv +="			        </div>";
        DetailDiv +="			    </div>";
        DetailDiv +="		    </div>";
        DetailDiv +="		    <div class='column'>";
        DetailDiv +="			    <div class='field has-addons'>";
        DetailDiv +="			        <div class='control is-expanded'>";
        DetailDiv +="					    <input class='input' type='text' name='detail_view_"+detail_i+"' id='detail_view_"+detail_i+"' value='' readonly>";
        DetailDiv +="					    <input type='hidden' name='detail_id_"+detail_i+"' id='detail_id_"+detail_i+"' value=''>";
        DetailDiv +="					    <input type='hidden' name='detail_login_"+detail_i+"' id='detail_login_"+detail_i+"' value=''>";
        DetailDiv +="					    <input type='hidden' name='detail_position_"+detail_i+"' id='detail_position_"+detail_i+"' value=''>";
        DetailDiv +="					    <input type='hidden' name='detail_name_"+detail_i+"' id='detail_name_"+detail_i+"' value=''>";
        DetailDiv +="				    </div>";
        DetailDiv +="			        <div class='control'>";
        DetailDiv +="				    	<a href=javascript:addPerson('"+detail_i+"'); class='button'>담당자 선택</a>";
        DetailDiv +="			        </div>";
        DetailDiv +="			    </div>";
        DetailDiv +="		    </div>";
        DetailDiv +="	    </div>";

        DetailDiv +="		<div class='field'>";
        DetailDiv +="			<div class='control'>";
        DetailDiv +="			    <input type='text' class='input' name='detail_detail_"+detail_i+"' id='detail_detail_"+detail_i+"' value='' maxlength='200'>";
        DetailDiv +="			</div>";
        DetailDiv +="		</div>";

        DetailDiv +="		<div class='level is-mobile'>";
        DetailDiv +="		    <div class='level-left is-title-column'>";
        DetailDiv +="		        <p class='title is-size-6'>프로젝트 참여율</p>";
        DetailDiv +="		    </div>";
        DetailDiv +="		    <div class='level-right'>";
        DetailDiv +="		        <div class='field'>";
        DetailDiv +="		            <div class='control select'>";
        DetailDiv +="					    <select name='part_rate_"+detail_i+"' id='part_rate_"+detail_i+"' class='percentage' onchange=changeRate('"+detail_i+"');>";
        DetailDiv +="						    <option value='0'>0%</option>";
        DetailDiv +="						    <option value='10'>10%</option>";
        DetailDiv +="						    <option value='20'>20%</option>";
        DetailDiv +="						    <option value='30'>30%</option>";
        DetailDiv +="						    <option value='40'>40%</option>";
        DetailDiv +="						    <option value='50'>50%</option>";
        DetailDiv +="						    <option value='60'>60%</option>";
        DetailDiv +="						    <option value='70'>70%</option>";
        DetailDiv +="						    <option value='80'>80%</option>";
        DetailDiv +="						    <option value='90'>90%</option>";
        DetailDiv +="						    <option value='100'>100%</option>";
        DetailDiv +="					    </select>";
        DetailDiv +="			        </div>";
        DetailDiv +="		        </div>";
        DetailDiv +="			</div>";
        DetailDiv +="		</div>";



        DetailDiv +="		<div class='box' id='TimeZone_"+detail_i+"'>";
        DetailDiv +="		    <div class='columns'>";
        DetailDiv +="		        <div class='column' style='display:inline-block;flex-grow:0;flex-basis:auto;'>";
        DetailDiv +="		            <div class='field is-group'>";
        DetailDiv +="		                <div class='control select'>";
        DetailDiv +="					        <select name='detail_fr_year_"+detail_i+"[]' id='detail_fr_year_"+detail_i+"_1'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "						        <option value='<?=$i?>'<?=$selected?>><?=$i?>년</option>";
        <?
        }
        ?>
        DetailDiv +="					        </select>";
        DetailDiv +="			            </div>";

        DetailDiv +="		                <div class='control select'>";
        DetailDiv +="					        <select name='detail_fr_month_"+detail_i+"[]' id='detail_fr_month_"+detail_i+"_1'>";
        <?
        for ($i=1; $i<=12; $i++)
        {
        if ($i == Date("m")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        } else {
            $j = $i;
        }
        ?>
        DetailDiv += "						        <option value='<?=$j?>'<?=$selected?>><?=$i?>월</option>";
        <?
        }
        ?>
        DetailDiv +="					    </select>";
        DetailDiv +="			       </div>";

        DetailDiv +="		           <div class='control select'>";
        DetailDiv +="					    <select name='detail_fr_day_"+detail_i+"[]' id='detail_fr_day_"+detail_i+"_1'>";
        <?
        for ($i=1; $i<=31; $i++)
        {
        if ($i == Date("d")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        } else {
            $j = $i;
        }
        ?>
        DetailDiv += "						    <option value='<?=$j?>'<?=$selected?>><?=$i?>일</option>";
        <?
        }
        ?>
        DetailDiv +="					    </select>";
        DetailDiv +="			       </div>";
        DetailDiv +="		           <div class='button'>";
        DetailDiv +="					    <input type='hidden' id='detail_fr_date_"+detail_i+"_1' class='datepicker'>";
        DetailDiv +="			       </div>";
        DetailDiv +="			    </div>";
        DetailDiv +="           </div>";

        DetailDiv +="               <div class='column'>";
        DetailDiv +="		            <div class='field is-group'>";
        DetailDiv +="		                <div class='control select'>";
        DetailDiv +="					        <select name='detail_to_year_"+detail_i+"[]' id='detail_to_year_"+detail_i+"_1'>";
        <?
        for ($i=$startYear; $i<=(Date("Y")+1); $i++)
        {
        if ($i == Date("Y")) {	$selected = " selected";	} else { $selected = "";	}
        ?>
        DetailDiv += "						        <option value='<?=$i?>'<?=$selected?>><?=$i?>년</option>";
        <? } ?>
        DetailDiv +="					        </select>";
        DetailDiv +="			            </div>";

        DetailDiv +="		                <div class='control select'>";
        DetailDiv +="					        <select name='detail_to_month_"+detail_i+"[]' id='detail_to_month_"+detail_i+"_1'>";
        <?
        for ($i=1; $i<=12; $i++)
        {
        if ($i == Date("m")) {	$selected = " selected";	} else { $selected = "";	}
        if (strlen($i) == '1')
        {
            $j = '0'.$i;
        } else {
            $j = $i;
        }
        ?>
        DetailDiv += "						                <option value='<?=$j?>'<?=$selected?>><?=$i?>월</option>";
        <? } ?>
        DetailDiv +="					        </select>";
        DetailDiv +="			            </div>";


        DetailDiv +="		                <div class='control select'>";
        DetailDiv +="					        <select name='detail_to_day_"+detail_i+"[]' id='detail_to_day_"+detail_i+"_1'>";
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
        DetailDiv += "						<option value='<?=$j?>'<?=$selected?>><?=$i?>일 </option>";
        <?
        }
        ?>
        DetailDiv +="					        </select>";
        DetailDiv +="			            </div>";

        DetailDiv +="		                <div class='button'>";
        DetailDiv +="					       <input type='hidden' id='detail_to_date_"+detail_i+"_1' class='datepicker'>";
        DetailDiv +="			            </div>";
        DetailDiv +="			        </div>";
        DetailDiv +="		        </div>";

        //DetailDiv +="				<div class='column last-button'>";
        DetailDiv +="				  <span class='buttons'>";
        DetailDiv +="						<a class='button' href=javascript:addTime('"+detail_i+"'); id='addTime'>";
        DetailDiv +="			                 <span class='icon is-small'>";
        DetailDiv +="			                    <i class='fas fa-plus'></i>";
        DetailDiv +="		                     </span>";
        DetailDiv +="		                </a>";
        DetailDiv +="		          </span>&nbsp;";
        DetailDiv +="				  <span class='buttons'>";
        DetailDiv +="						<a class='button' href=javascript:delTime('"+detail_i+"'); id='delTime'>";
        DetailDiv +="			                 <span class='icon is-small'>";
        DetailDiv +="			                    <i class='fas fa-minus'></i>";
        DetailDiv +="		                     </span>";
        DetailDiv +="		                </a>";
        DetailDiv +="		          </span>";
        DetailDiv +="				  <input type='hidden' name='TimeSize_"+detail_i+"' id='TimeSize_"+detail_i+"' value='1'>";
        //DetailDiv +="			    </div>";
        DetailDiv +="			</div>";
        DetailDiv +="		</div>";
//담당자 추가 html

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
