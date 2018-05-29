<?
require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
require_once CMN_PATH."/login_check.php";
require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>
<?
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "ING";

$p_no = isset($_REQUEST['no']) ? $_REQUEST['no'] : null;
$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

$p_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : "ING";
$p_fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : $nowYear;
$p_fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : $nowMonth;
if (strlen($p_fr_month) == 1) { $p_fr_month = "0". $p_fr_month; }
$p_fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : 1;
if (strlen($p_fr_day) == 1) { $p_fr_day = "0". $p_fr_day; }
$p_to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : $nowYear;
$p_to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : $nowMonth;
if (strlen($p_to_month) == 1) { $p_to_month = "0". $p_to_month; }
$p_to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d");
if (strlen($p_to_day) == 1) { $p_to_day = "0". $p_to_day; }

$p_fr_date = $p_fr_year ."-". $p_fr_month ."-". $p_fr_day;
$p_to_date = $p_to_year ."-". $p_to_month ."-". $p_to_day;

$project_no = isset($_REQUEST['project_no']) ? $_REQUEST['project_no'] : null;

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
				TITLE, CONTENTS, CONVERT(char(10),START_DATE,120) AS START_DATE, CONVERT(char(10),END_DATE,120) AS END_DATE, PROGRESS, STATUS, 
				PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION, CONVERT(char(10),REG_DATE,102) AS REG_DATE
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
    $status = $record['STATUS'];
    $id = $record['PRS_ID'];
    $login = $record['PRS_LOGIN'];
    $name = $record['PRS_NAME'];
    $position = $record['PRS_POSITION'];
    $reg_date = $record['REG_DATE'];

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
?>

<? include INC_PATH."/top.php"; ?>
<script type="text/JavaScript">
    $(document).ready(function(){
        //목록
        $("#btnList").attr("style","cursor:pointer;").click(function(){
            $("#form").attr("target","_self");
            <? if ($type == "TOTAL") { ?>
            $("#form").attr("action","project_total.php");
            <? } else { ?>
            $("#form").attr("action","project_list.php");
            <? } ?>
            $("#form").submit();
        });
        //수정
        $("#btnModify").attr("style","cursor:pointer;").click(function(){
            $("#mode").val("modify");
            $("#form").attr("target","_self");
            $("#form").attr("action","project_write.php");
            $("#form").submit();
        });
        //삭제
        $("#btnDelete").attr("style","cursor:pointer;").click(function(){
            $("#mode").val("delete");
            $("[name=mode_title]").html("삭제");
            //$("#popup_ok").attr("style","display:inline;");
            $("#popup_ok").addClass("modal is-active");
        });
        //프로젝트 완료
        $("#btnEND").attr("style","cursor:pointer;").click(function(){
            $("#mode").val("end");
            $("[name=mode_title]").html("완료");
            //$("#popup_ok").attr("style","display:inline;");
            $("#popup_ok").addClass("modal is-active");
        });
        //프로젝트 완료 취소
        $("#btnING").attr("style","cursor:pointer;").click(function(){
            $("#mode").val("ing");
            $("[name=mode_title]").html("완료 취소");
            //$("#popup_ok").attr("style","display:inline;");
            $("#popup_ok").addClass("modal is-active");
        });


        $("#btnList2").attr("style","cursor:pointer;").click(function(){
            $("#form").attr("target","_self");
            <? if ($type == "TOTAL") { ?>
            $("#form").attr("action","project_total.php");
            <? } else { ?>
            $("#form").attr("action","project_list.php");
            <? } ?>
            $("#form").submit();
        });
        //수정
        $("#btnModify2").attr("style","cursor:pointer;").click(function(){
            $("#mode").val("modify");
            $("#form").attr("target","_self");
            $("#form").attr("action","project_write.php");
            $("#form").submit();
        });
        //삭제
        $("#btnDelete2").attr("style","cursor:pointer;").click(function(){
            $("#mode").val("delete");
            $("[name=mode_title]").html("삭제");
            //$("#popup_ok").attr("style","display:inline;");
            $("#popup_ok").addClass("modal is-active");
        });

        $("#popup_ok_ok").attr("style","cursor:pointer;").click(function(){
            $("#form").attr("target","hdnFrame");
            $("#form").attr("action","project_write_act.php");
            $("#form").submit();
        });
        $("#popup_ok_no").attr("style","cursor:pointer;").click(function(){
            //$("#popup_ok").attr("style","display:none;");
            $("#popup_ok").removeClass("is-active");
        });
        $("#popup_ok_close").attr("style","cursor:pointer;").click(function(){
            //$("#popup_ok").attr("style","display:none;");
            $("#popup_ok").removeClass("is-active");
        });
    });
</script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>
<form name="form" id="form" method="post">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="type" id="type" value="<?=$type?>">
<input type="hidden" name="no" id="no" value="<?=$p_no?>">
<input type="hidden" name="name" id="name" value="<?=$p_name?>">
<input type="hidden" name="project_no" id="project_no" value="<?=$project_no?>">
<input type="hidden" name="status" id="status" value="<?=$p_status?>">
<input type="hidden" name="fr_year" id="fr_year" value="<?=$p_fr_year?>">
<input type="hidden" name="fr_month" id="fr_month" value="<?=$p_fr_month?>">
<input type="hidden" name="fr_day" id="fr_day" value="<?=$p_fr_day?>">
<input type="hidden" name="to_year" id="to_year" value="<?=$p_to_year?>">
<input type="hidden" name="to_month" id="to_month" value="<?=$p_to_month?>">
<input type="hidden" name="to_day" id="to_day" value="<?=$p_to_day?>">
<input type="hidden" name="mode" id="mode">
    <? include INC_PATH."/project_menu.php"; ?>
<!-- 본문 시작 -->
<section class="section is-resize">
    <div class="container">
        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a id="btnList" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>목록</span>
                    </a>
                </p>
            </div>
            <div class="level-right">
                 <p class="buttons">
         <? if ($type == "ING") { ?>
             <? if ($prf_id == "4") { ?>
                    <a id="btnDelete" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-times"></i>
                        </span>
                        <span>삭제</span>
                    </a>
                <? } else { ?>
                <span>삭제를 원하시면 경영지원팀 김숙진 팀장님께 문의 바랍니다.</span>&nbsp;&nbsp;&nbsp;
                <? } ?>
                    <a id="btnModify" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>수정</span>
                    </a>
            <? } else if ($type == "END") { ?>
                     <? if ($prf_id == "4") { ?>
                     <? } else { ?>
                         프로젝트 완료취소를 원하시면 경영지원팀 김숙진 팀장님께 문의 바랍니다.&nbsp;&nbsp;&nbsp;&nbsp;
                     <? } ?>
             <a id="btnModify" class="button is-danger">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                 <span>수정</span>
             </a>
         <? } ?>
                </p>
             </div>
        </nav>

        <hr class="hr-strong">

        <div class="content">
            <div class="columns is-column-marginless">
                <div class="column is-paddingless-bottom">
                    <p class="title is-size-3"><?=$title?> </p>
                    <p class="subtitle is-size-7">
                        [<?=$project_no?>]
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        작성자
                        <?=$position?> <?=$name?>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <?=$reg_date?>
                    </p>
                </div>
                <div class="column last-button">
                 <? if ($type == "ING") { ?>
                    <a id="btnEND" class="button is-info is-fullwidth">
                        <span class="icon is-small">
                            <i class="fas fa-check"></i>
                        </span>
                        <span>&nbsp;&nbsp;프로젝트 완료&nbsp;&nbsp;</span>
                    </a>
                 <? } else if ($type == "END") { ?>
                    <? if ($prf_id == "4") { ?>
                    <a id="btnING" class="button is-info is-fullwidth">
                        <span class="icon is-small">
                            <i class="fas fa-check"></i>
                        </span>
                        <span>&nbsp;&nbsp;프로젝트 완료 취소&nbsp;&nbsp;</span>
                    </a>
                    <? } else { ?>
                  <? } }?>
                </div>
            </div>
            <hr>
            <p style="white-space:pre-line;">
                <?=str_replace("\r\n","<br>",$contents);?>
            </p>
        </div>


        <div class="box">
            <!--<div class="columns">-->
            <div class="content">
                <div class="level is-mobile is-level-marginless">
                    <div class="level-left is-title-column">
                        <p class="title is-size-6">기간 경과율</p>
                    </div>
                    <div class="level-right">
                        <?=$time?>일 경과 / 총 <?=$total_time?>일
                    </div>
                </div>
                <progress class="progress is-small is-info" value="<?=$time_bar?>" max="100"></progress>
            </div>
            <hr>
            <div class="content">
                <div class="level is-mobile is-level-marginless">
                    <div class="level-left is-title-column">
                        <p class="title is-size-6">프로젝트 전체 진행율</p>
                    </div>
                    <div class="level-right">
                        <?=$progress?>% 진행
                    </div>
                </div>
                <progress class="progress is-small is-danger" value="<?=$progress_bar?>" max="100"></progress>
            </div>
            <!--</div>-->
        </div>

        <div class="content">
            <table class="table is-fullwidth is-hoverable is-resize">
                <colgroup>
                    <col width="11">
                    <col width="*">
                    <col width="15%">
                    <col width="10%">
                    <col width="15%">
                </colgroup>
                <thead>
                <tr>
                    <th><span class="is-hidden-mobile">No.</span></th>
                    <th>역할</th>
                    <th class="has-text-centered">이름</th>
                    <th class="has-text-centered">참여율</th>
                    <th class="has-text-centered">프로젝트기간 / 참여기간</th>
                </tr>
                </thead>
                <!-- 일반 리스트 -->
                <tbody class="list">
                <?
                $sql = "SELECT
				PART, DETAIL, PART_RATE, PRS_ID, PRS_LOGIN, PRS_NAME, PRS_POSITION 
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

                $i = 1;
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
                ?>
                <tr>
                    <td><?=$i?></td>
                    <td><?=$detail_part?> - <?=$detail_detail?></td>
                    <td class="has-text-centered"><?=$detail_position?> <?=$detail_name?></td>
                    <td class="has-text-centered"><?=$detail_part_rate?>%</td>
                    <td class="has-text-centered">
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

                        while ($record1=sqlsrv_fetch_array($rs1))
                        {
                            $detail_start_date = $record1['START_DATE'];
                            $detail_end_date = $record1['END_DATE'];
                            ?>
                        <span class="tag is-small is-small-middle"><?=str_replace("-",".",$start_date);?>-<?=str_replace("-",".",$end_date);?></span><br>
                        <span class="tag is-small is-small-middle"><?=$detail_start_date?>-<?=$detail_end_date?></span><br>
                            <?
                        }
                        ?>
                    </td>
                </tr>
                    <?
                    $i = $i + 1;
                }
                }
                ?>
                </tbody>
            </table>
        </div>

        <nav class="level is-mobile">
            <div class="level-left">
                <p class="buttons">
                    <a id="btnList2" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                        <span>목록</span>
                    </a>
                </p>
            </div>
            <div class="level-right">
                <p class="buttons">
                    <? if ($type == "ING") { ?>
                        <? if ($prf_id == "4") { ?>
                            <a id="btnDelete2" class="button is-danger">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>삭제</span>
                            </a>
                        <? } else { ?>
                            <span>삭제를 원하시면 경영지원팀 김숙진 팀장님께 문의 바랍니다.</span>&nbsp;&nbsp;&nbsp;
                        <? } ?>
                            <a id="btnModify2" class="button is-danger">
                                <span class="icon is-small">
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                                <span>수정</span>
                        </a>
                    <? } ?>
                </p>
            </div>
        </nav>

    </div>
</section>
<!-- 본문 끌 -->

<? include INC_PATH."/bottom.php"; ?>
<!--
    <div class="popups">
        <div class="ok" id="popup_ok" style="display:none;">
            <div class="pop_top">
                <p class="pop_title"><span name="mode_title"></span></p>
                <span class="close" style="cursor:pointer;" id="popup_ok_close">닫기</span>
            </div>
            <div class="pop_bot">
                <p>프로젝트를 <span name="mode_title"></span> 하시겠습니까?</p>
                <div class="btns">
                    <img src="../img/btn_ok.gif" alt="확인" class="first" id="popup_ok_ok" />
                    <img src="../img/project/btn_no.gif" alt="취소" id="popup_ok_no" />
                </div>
            </div>
        </div>
    </div>
-->

    <div id="popup_ok" class="modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">프로젝트 <span name="mode_title"></span></p>
                <a id="popup_ok_close" class="close"></a>
            </header>
            <section class="modal-card-body">
                프로젝트를 <span name="mode_title"></span> 하시겠습니까?
            </section>
            <footer class="modal-card-foot">
                    <a id="popup_ok_ok" class="button is-success">확인</a>&nbsp;&nbsp;&nbsp;
                    <a id="popup_ok_no" class="button is-error">취소</a>
            </footer>
        </div>
    </div>
</form>
</body>
</html>