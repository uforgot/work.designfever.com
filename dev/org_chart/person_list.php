<?
require_once $_SERVER['DOCUMENT_ROOT'] . "/common/global.php";
require_once CMN_PATH . "/login_check.php";
require_once CMN_PATH . "/working_check.php";
require_once CMN_PATH . "/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>
<?

function getMemberCheck($prs_id, $date, $yesterday) {
    global $dbConn;

    $flag = false;
//정상출근,지각,휴가,근무일수,반차,평균출근시,평균출근분,평균퇴근시,평균퇴근분,총근무시간
    $sql = "EXEC SP_COMMUTING_MEMBER_02 '$prs_id','$date','$yesterday'";
    $rs = sqlsrv_query($dbConn,$sql);
    $record = sqlsrv_fetch_array($rs);

    if (sizeof($record) > 0)
    {
        $col_date = $record['DATE'];					//날짜
        $col_datekind = $record['DATEKIND'];			//공휴일 여부
        $col_gubun = $record['GUBUN'];					//출퇴근구분
        $col_gubun1 = $record['GUBUN1'];				//출근구분
        $col_gubun2 = $record['GUBUN2'];				//퇴근구분
        $col_checktime1 = $record['CHECKTIME1'];		//출근시간
        $col_checktime2 = $record['CHECKTIME2'];		//퇴근시간

//출근시간
        $checktime1 = substr($col_checktime1,8,2) .":". substr($col_checktime1,10,2);
        if ($checktime1 == ":") { $checktime1 = ""; }

        if ($col_gubun1 == "1") {}			//출근
        else if ($col_gubun1 == "4") {}		//반차
        else if ($col_gubun1 == "6") {}		//외근
        else if ($col_gubun1 == "7") {}		//지각
        else if ($col_gubun1 == "8") {}		//반차
        else if ($col_gubun1 == "0")		//오후반차 제출. 출퇴근체크 X
        {
            $checktime1 = "";
        }
        else						 		//휴가 - 출근/퇴근 시간 표시 안함 - 당일 00:00출근 23:59퇴근으로 설정되어 있음
        {
            $checktime1 = "";
        }

//퇴근시간
        $checktime2 = substr($col_checktime2,8,2) .":". substr($col_checktime2,10,2);
        if ($checktime2 == ":") { $checktime2 = ""; }

        if ($col_gubun2 == "2" || $col_gubun2 == "3" || $col_gubun2 == "5" || $col_gubun2 == "6" || $col_gubun2 == "9")
        {
            if ($col_gubun2 == "2" || $col_gubun2 == "3") {}	//퇴근
            else if ($col_gubun2 == "5") {}						//프로젝트 반차
            else if ($col_gubun2 == "6") {}						//외근
            else if ($col_gubun2 == "9") {}						//반차
            else if ($col_gubun2 == "0") {}						//오전반차 제출. 출퇴근체크 X
        }
    }

    if(strlen($checktime1) > 1) $flag = true;
    if(strlen($checktime2) > 1) $flag = false;

    $icon = ($flag===true) ? "<div class=\"notification is-white\">" : "<div class=\"notification is-absent\">";

// 예외 대상
    $arr = array(15,22,24,29,79,87,148);
    if(in_array($prs_id,$arr)) $icon = "<div class=\"notification is-white\">";

    return $icon;
}

$now_date = date("Y-m-d");
$yesterday_date = date("Y-m-d",strtotime ("-1 day"));
$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
$where = " AND PRF_ID IN (1,2,3,4,5)";
$sql = "SELECT SEQNO, POSITION FROM DF_POSITION2_2018 WITH(NOLOCK) ORDER BY SEQNO";
$rs = sqlsrv_query($dbConn, $sql);

while ($record = sqlsrv_fetch_array($rs)) {
    $orderby1 .= "WHEN PRS_POSITION2 ='" . $record['POSITION'] . "' THEN " . $record['SEQNO'] . " ";
}

$sql = "SELECT SEQNO, POSITION FROM DF_POSITION1_2018 WITH(NOLOCK) ORDER BY SEQNO";
$rs = sqlsrv_query($dbConn, $sql);

while ($record = sqlsrv_fetch_array($rs)) {
    $orderby2 .= "WHEN PRS_POSITION1 ='" . $record['POSITION'] . "' THEN " . $record['SEQNO'] . " ";
}

$orderbycase .= " ORDER BY CASE " . $orderby1 . " END, CASE " . $orderby2 . " END, PRS_NAME";




?>
<? include INC_PATH . "/top.php"; ?>
<script type="text/javascript">
    $(document).ready(function () {
        //검색
        $("#team").change(function(){
            var team = $("#team").val().replace(/ /g,'');

            $("#form").attr("target","_self");
            $("#form").attr("action","<?=CURRENT_URL?>#"+team);
            $("#form").submit();
        });

        //엑셀 다운로드
        $("#btnExcel").attr("style", "cursor:pointer;").click(function () {
            $("#form").attr("target", "hdnFrame");
            $("#form").attr("action", "excel_person.php");
            $("#form").submit();
        });
    });
</script>
</head>
<body>
<form method="post" name="form" id="form">
    <? include INC_PATH . "/top_menu.php"; ?>
    <? include INC_PATH . "/org_menu.php"; ?>
    <section class="section df-org">
        <div class="container">
            <div class="content">
                <div class="card">
                    <?
                    $sql = "SELECT COUNT(PRS_ID) AS CNT FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID NOT IN(6) AND PRS_ID NOT IN (281,102) AND PRS_POSITION NOT IN('이사','대표')";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT'];
                    }
                    ?>
                    <div class="card-content">
                        <progress class="progress is-danger" value="<?= $work_count['TOT'] ?>" max="<?=$cnt?>"><?=$cnt?>%</progress>
                    </div>
                    <div class="card-footer">
                        <div class="card-footer-item">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered">전체</div>
                                <div class="title is-size-4 has-text-centered">
                                    <span class="has-text-info"><?=$cnt?></span>

                                </div>
                            </div>
                        </div>
                        <div class="card-footer-item">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered">미출근</div>
                                <div class="title is-size-4 has-text-centered">
                                    <span><?= $cnt - $work_count['TOT'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer-item">
                            <div class="content" style="width:100%;">
                                <div class="is-size-7 has-text-centered">근무중</div>
                                <div class="title is-size-4 has-text-centered has-text-danger">
                                    <span><?= $work_count['TOT'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="level">
                    <div class="level-left">
                        <div class="control select is-fullwidth">
                            <select name="team" id="team">
                                <option value=""<? if ($p_team == "") {
                                    echo " selected";
                                } ?>>전직원
                                </option>
                                <?
                                $selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) ORDER BY SORT";
                                $selRs = sqlsrv_query($dbConn, $selSQL);

                                while ($selRecord = sqlsrv_fetch_array($selRs)) {
                                    $selStep = $selRecord['STEP'];
                                    $selTeam = $selRecord['TEAM'];

                                    if ($selStep == 1) {
                                        $selTeam2 = $selTeam;
                                    } else if ($selStep == 2) {
                                        $selTeam2 = "&nbsp;&nbsp;└ " . $selTeam;
                                    } else if ($selStep == 3) {
                                        $selTeam2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└ " . $selTeam;
                                    }
                                    ?>
                                    <option value="<?= $selTeam ?>"<? if ($p_team == $selTeam) {
                                        echo " selected";
                                    } ?>><?= $selTeam2 ?></option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="level-right is-hidden-mobile">
                        <? if ($prf_id == "4") { ?>
                            <a class="button" alt="엑셀다운로드" id="btnExcel">
                                <span class="icon is-small">
                                    <i class="fas fa-file-excel"></i>
                                </span>
                                <span>엑셀로 다운로드</span>
                            </a>
                        <? } ?>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="title is-size-5">
                    <a name="CEO" style="color:#000; cursor:default;">CEO
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG, PRS_EMAIL, PRS_MOBILE FROM DF_PERSON WITH(NOLOCK) WHERE PRS_POSITION2 = '대표' AND PRF_ID = 4";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <div class="tag"><?= $col_prs_extension ?></div>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?= $col_file_img ?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ 대표</span>
                                </p>
                                <p class="is-member-phone">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?= $col_prs_email ?>@designfever.com</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>

        <hr>

        <!-- 기획실-->
        <div class="content">
            <div class="title is-size-5">
                <a name="Planning" style="color:#000; cursor:default;">Planning</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Creative Planning Division','Creative Planning 1 Team','Creative Planning 2 Team', 'Marketing Planning Division')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
        </div>
        <?
        $sql = "select PRS_ID , PRS_NAME , PRS_TEAM , PRS_POSITION1, PRS_MOBILE, PRS_EMAIL, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID IN(5,4,3)   AND PRS_TEAM = 'Creative Planning Division'";
        $rs = sqlsrv_query($dbConn, $sql);
        While ($record = sqlsrv_fetch_array($rs)) {
            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
            $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
            ?>
            <div class="content">
                <div class="title is-size-6">
                    <a name="CreativePlanningDivision" style="color:#000; cursor:default;">Creative Planning Division</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Creative Planning Division','Creative Planning 1 Team','Creative Planning 2 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <!-- 실장 / 이사-->
                <div class="columns is-multiline">
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>

                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?= $col_prs_email ?>@designfever.com</span>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

        <!-- 팀장포함 팀원-->
        <div class="is-team">
            <div class="is-member-depth-1-line"></div>
            <div class="is-member-depth-1">
                <div class="title is-team-title is-size-6">
                    <a name="CreativePlanning1Team" style="color:#000; cursor:default;">Creative Planning 1 Team</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Creative Planning 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281) 
                                                      AND A.PRS_TEAM IN ('Creative Planning 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?=$col_prs_name?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?=$col_prs_mobile?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>
        <div class="is-member-depth-1">
            <div class="title is-team-title is-size-6">
                <a name="CreativePlanning2Team" style="color:#000; cursor:default;">Creative Planning 2 Team</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Creative Planning 2 Team')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>

            <div class="columns is-multiline">
                <?
                $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281)
                                                      AND A.PRS_TEAM IN ('Creative Planning 2 Team')  ORDER BY B.SEQNO, A.PRS_ID";

                $rs = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                ?>
                <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                    <!--<div class="notification is-white ">-->
                    <?=$div_style?>
                    <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                    <div class="columns is-mobile">
                        <div class="column member-photo">
                            <p class="image is-70x70 is-rounded-image">
                                <img src="/file/<?=$col_file_img?>">
                            </p>
                        </div>
                        <div class="column">
                            <p class="is-member-info">
                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                            </p>
                            <p class="is-member-phone">
                                <?=$col_prs_mobile?>
                            </p>
                            <p class="is-member-email">
                                <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
        </div>
        </div>
        </div>

        <!-- 팀장포함 팀원-->
        <?
        $sql = "select PRS_ID , PRS_NAME , PRS_TEAM , PRS_POSITION1, PRS_MOBILE, PRS_EMAIL, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID IN(5,4,3)   AND PRS_TEAM = 'Marketing Planning Division' ";
        $rs = sqlsrv_query($dbConn, $sql);
        While ($record = sqlsrv_fetch_array($rs)) {
            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
            $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
            ?>

            <div class="content">
                <div class="title is-size-6">
                    <a name="MarketingPlanningDivision" style="color:#000; cursor:default;">Marketing Planning Division</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Marketing Planning Division')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <!-- 실장 / 이사-->
                <div class="columns is-multiline">
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?= $col_prs_email ?>@designfever.com</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
        <div class="is-team">
            <div class="is-member-depth-1-line"></div>
            <div class="is-member-depth-1">
                <div class="title is-team-title is-size-6">
                    <a name="MarketingPlanningDivision" style="color:#000; cursor:default;">Marketing Planning Division</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Marketing Planning Division')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3) AND A.PRS_ID NOT IN(102,281)
                                                            AND A.PRS_TEAM IN ('Marketing Planning Division')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?=$col_prs_name?></span>
                                    <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?=$col_prs_mobile?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>
        </div>
        </div>
        <!-- 기획실 끝-->

        <hr>

        <!-- 디자인실 -->
        <div class="content">
            <div class="title is-size-5">
                <a name="Design" style="color:#000; cursor:default;">Design</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 1 Division','Design 1 Division 1 Team','Design 2 Division','Design 2 Division 1 Team','Design 2 Division 2 Team')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
        </div>
        <?
        $sql = "select PRS_ID , PRS_NAME , PRS_TEAM , PRS_POSITION1, PRS_MOBILE, PRS_EMAIL, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID IN(5,4,3)   AND PRS_NAME = '박재형'";
        $rs = sqlsrv_query($dbConn, $sql);
        While ($record = sqlsrv_fetch_array($rs)) {
            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
            $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
            ?>
            <div class="content">
                <div class="title is-size-6">
                    <a name="Design1Division" style="color:#000; cursor:default;">Design 1 Division</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 1 Division','Design 1 Division 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <!-- 실장 / 이사-->
                <div class="columns is-multiline">
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?= $col_prs_email ?>@designfever.com</span>
                                    <span class="tag is-small"><?= $col_prs_extension ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

        <!-- 팀장포함 팀원-->
        <div class="is-team">
            <div class="is-member-depth-1-line"></div>
            <div class="is-member-depth-1">
                <div class="title is-team-title is-size-6">
                    <a name="Design1Division1Team" style="color:#000; cursor:default;">Design 1 Division 1 Team</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 1 Division 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281) 
                                                                  AND A.PRS_TEAM IN ('Design 1 Division 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?=$col_prs_name?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?=$col_prs_mobile?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>
        </div>
        </div>
        <?
        $sql = "select PRS_ID , PRS_NAME , PRS_TEAM , PRS_POSITION1, PRS_MOBILE, PRS_EMAIL, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID IN(5,4,3)   AND PRS_TEAM = 'Design 2 Division'";
        $rs = sqlsrv_query($dbConn, $sql);
        While ($record = sqlsrv_fetch_array($rs)) {
            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
            $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
            ?>
            <div class="content">
                <div class="title is-size-6">
                    <a name="Design2Division" style="color:#000; cursor:default;">Design 2 Division</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 2 Division','Design 2 Division 1 Team','Design 2 Division 2 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <!-- 실장 / 이사-->
                <div class="columns is-multiline">
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?= $col_prs_email ?>@designfever.com</span>
                                    <span class="tag is-small"><?= $col_prs_extension ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        <? } ?>
        <!-- 팀장포함 팀원-->
        <div class="is-team">
            <div class="is-member-depth-1-line"></div>
            <div class="is-member-depth-1">
                <div class="title is-team-title is-size-6">
                    <a name="Design2Division1Team" style="color:#000; cursor:default;">Design 2 Division 1 Team</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 2 Division 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3) AND A.PRS_ID NOT IN(102,281) 
                                                                    AND A.PRS_TEAM IN ('Design 2 Division 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?=$col_prs_name?></span>
                                    <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?=$col_prs_mobile?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>
        <div class="is-member-depth-1">
            <div class="title is-team-title is-size-6">
                <a name="Design2Division2Team" style="color:#000; cursor:default;">Design 2 Division 2 Team</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 2 Division 2 Team')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>

            <div class="columns is-multiline">
                <?
                $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281)
                                                                  AND A.PRS_TEAM IN ('Design 2 Division 2 Team')  ORDER BY B.SEQNO, A.PRS_ID";

                $rs = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                ?>
                <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                    <!--<div class="notification is-white ">-->
                    <?=$div_style?>
                    <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                    <div class="columns is-mobile">
                        <div class="column member-photo">
                            <p class="image is-70x70 is-rounded-image">
                                <img src="/file/<?=$col_file_img?>">
                            </p>
                        </div>
                        <div class="column">
                            <p class="is-member-info">
                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                            </p>
                            <p class="is-member-phone">
                                <?=$col_prs_mobile?>
                            </p>
                            <p class="is-member-email">
                                <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
        </div>
        </div>
        <!-- 디자인실 끝-->

        <hr>

        <!-- 모션실-->
        <div class="content">
            <div class="title is-size-5">
                <a name="Motion" style="color:#000; cursor:default;">Motion</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Motion Division','Motion 1 Team', 'Art Division')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
        </div>
        <?
        $sql = "select PRS_ID , PRS_NAME , PRS_TEAM , PRS_POSITION1, PRS_POSITION2, PRS_MOBILE, PRS_EMAIL, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID IN(5,4,3)   AND PRS_TEAM = 'Motion Division'";
        $rs = sqlsrv_query($dbConn, $sql);
        While ($record = sqlsrv_fetch_array($rs)) {
            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
            $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
            ?>
            <div class="content">
                <div class="title is-size-6">
                    <a name="MotionDivision" style="color:#000; cursor:default;">Motion Division</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Motion Division','Motion 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <!-- 실장 / 이사-->
                <div class="columns is-multiline">
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?= $col_prs_email ?>@designfever.com</span>
                                    <span class="tag is-small"><?= $col_prs_extension ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

        <!-- 팀장포함 팀원-->
        <div class="is-team">
            <div class="is-member-depth-1-line"></div>
            <div class="is-member-depth-1">
                <div class="title is-team-title is-size-6">
                    <a name="Motion1Team" style="color:#000; cursor:default;">Motion 1 Team</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Motion 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281) 
                                                                            AND A.PRS_TEAM IN ('Motion 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?=$col_prs_name?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?=$col_prs_mobile?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>

        </div>
        <div class="content">
            <div class="title is-size-5">
                <a name="ArtDivision" style="color:#000; cursor:default;">Art Division</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Art Division')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
            <div class="columns is-multiline">
                <?
                $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281)
                                                                    AND A.PRS_TEAM IN ('Art Division')  ORDER BY B.SEQNO, A.PRS_ID";

                $rs = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                ?>
                <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                    <!--<div class="notification is-white ">-->
                    <?=$div_style?>
                    <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                    <div class="columns is-mobile">
                        <div class="column member-photo">
                            <p class="image is-70x70 is-rounded-image">
                                <img src="/file/<?=$col_file_img?>">
                            </p>
                        </div>
                        <div class="column">
                            <p class="is-member-info">
                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                <span class="title is-size-7"> / <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                            </p>
                            <p class="is-member-phone">
                                <?=$col_prs_mobile?>
                            </p>
                            <p class="is-member-email">
                                <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
        </div>
        </div>
        <!-- 모션실 끝 -->

        <hr>

        <!-- VID 실-->
        <div class="content">
            <div class="title is-size-5">
                <a name="Development" style="color:#000; cursor:default;">Development</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Visual Interaction Development','VID 1 Team', 'VID 2 Team','LAB')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
        </div>
        <?
        $sql = "select PRS_ID , PRS_NAME , PRS_TEAM , PRS_POSITION2, PRS_MOBILE, PRS_EMAIL, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID IN(5,4,3)   AND PRS_TEAM = 'Visual Interaction Development'";
        $rs = sqlsrv_query($dbConn, $sql);
        While ($record = sqlsrv_fetch_array($rs)) {
            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
            $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
            ?>
            <div class="content">
                <div class="title is-size-6">
                    <a name="VisualInteractionDevelopment" style="color:#000; cursor:default;">Visual Interaction Development</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Visual Interaction Development','VID 1 Team', 'VID 2 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <!-- 실장 / 이사-->
                <div class="columns is-multiline">
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?= $col_prs_email ?>@designfever.com</span>
                                    <span class="tag is-small"><?= $col_prs_extension ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

        <!-- 팀장포함 팀원-->
        <div class="is-team">
            <div class="is-member-depth-1-line"></div>
            <div class="is-member-depth-1">
                <div class="title is-team-title is-size-6">
                    <a name="VID1Team" style="color:#000; cursor:default;">VID 1 Team</a>
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('VID 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281) 
                                                                                      AND A.PRS_TEAM IN ('VID 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <!--<div class="notification is-white ">-->
                        <?=$div_style?>
                        <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-info">
                                    <span class="title is-size-6"><?=$col_prs_name?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-phone">
                                    <?=$col_prs_mobile?>
                                </p>
                                <p class="is-member-email">
                                    <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>
        <div class="is-member-depth-1">
            <div class="title is-team-title is-size-6">
                <a name="VID2Team" style="color:#000; cursor:default;">VID 2 Team</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('VID 2 Team')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>

            <div class="columns is-multiline">
                <?
                $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281)
                                                                                      AND A.PRS_TEAM IN ('VID 2 Team')  ORDER BY B.SEQNO, A.PRS_ID";

                $rs = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                ?>
                <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                    <!--<div class="notification is-white ">-->
                    <?=$div_style?>
                    <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                    <div class="columns is-mobile">
                        <div class="column member-photo">
                            <p class="image is-70x70 is-rounded-image">
                                <img src="/file/<?=$col_file_img?>">
                            </p>
                        </div>
                        <div class="column">
                            <p class="is-member-info">
                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                            </p>
                            <p class="is-member-phone">
                                <?=$col_prs_mobile?>
                            </p>
                            <p class="is-member-email">
                                <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
        </div>

        </div>


        <div class="content">
            <div class="title is-size-5">
                <a name="LAB" style="color:#000; cursor:default;">LAB</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('LAB')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
            <div class="columns is-multiline">
                <?
                $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281)
                                                                                    AND A.PRS_TEAM IN ('LAB')  ORDER BY B.SEQNO, A.PRS_ID";

                $rs = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                ?>
                <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                    <!--<div class="notification is-white ">-->
                    <?=$div_style?>
                    <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                    <div class="columns is-mobile">
                        <div class="column member-photo">
                            <p class="image is-70x70 is-rounded-image">
                                <img src="/file/<?=$col_file_img?>">
                            </p>
                        </div>
                        <div class="column">
                            <p class="is-member-info">
                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                            </p>
                            <p class="is-member-phone">
                                <?=$col_prs_mobile?>
                            </p>
                            <p class="is-member-email">
                                <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
        </div>
        </div>
        <!-- VID 끝 -->

        <!-- BST-->
        <div class="content">
            <div class="title is-size-5">
                <a name="BST" style="color:#000; cursor:default;">BST</a>
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Business Support Team')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
        </div>
        <div class="content">
            <!-- 팀장포함 팀원-->
            <div class="is-team">
                <div class="is-member-depth-1-line"></div>
                <div class="is-member-depth-1">
                    <div class="title is-team-title is-size-6">
                        <a name="BusinessSupportTeam" style="color:#000; cursor:default;"> Business Support Team</a>
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Business Support Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5) AND A.PRS_ID NOT IN(102,281) 
                                                                                          AND A.PRS_TEAM IN ('Business Support Team')  ORDER BY B.SEQNO, A.PRS_ID";
                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                        $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                        $div_style= getMemberCheck($col_prs_id, $now_date,$yesterday_date);
                        ?>
                        <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                            <!--<div class="notification is-white ">-->
                            <?=$div_style?>
                            <? if ($col_prs_extension !=""){?><div class="tag"><?= $col_prs_extension ?></div><?}?>
                            <div class="columns is-mobile">
                                <div class="column member-photo">
                                    <p class="image is-70x70 is-rounded-image">
                                        <img src="/file/<?=$col_file_img?>">
                                    </p>
                                </div>
                                <div class="column">
                                    <p class="is-member-info">
                                        <span class="title is-size-6"><?=$col_prs_name?></span>
                                        <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                    </p>
                                    <p class="is-member-phone">
                                        <?=$col_prs_mobile?>
                                    </p>
                                    <p class="is-member-email">
                                        <span class="is-size-7"><?=$col_prs_email?>@designfever.com</span>

                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <? } ?>
                </div>
            </div>
        </div>
        </div>

        <div class="content">
            <div class="columns is-multiline">
                <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                    <!--<div class="notification is-white ">-->
                    <?=$div_style?>
                    <div class="columns is-mobile">
                        <div class="column member-photo">
                            <p class="image is-70x70 is-rounded-image">
                            </p>
                        </div>
                        <div class="column">
                            <p class="is-member-info">
                                <span class="title is-size-6">소장님</span>
                            </p>
                            <p class="is-member-phone">
                            </p>
                            <p class="is-member-email">
                                <span class="tag is-small">401</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- BST 끝-->
        <hr>
    </section>
</form>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>
