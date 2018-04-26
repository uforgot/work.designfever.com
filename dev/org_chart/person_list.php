<?
require_once $_SERVER['DOCUMENT_ROOT'] . "/common/global.php";
require_once CMN_PATH . "/login_check.php";
require_once CMN_PATH . "/working_check.php";
require_once CMN_PATH . "/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>
<?
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
            ?>
                <div class="card-content">
                    <progress class="progress is-danger" value="" max="<?=$cnt?>"><?=$cnt?>%</progress>
                </div>
                <div class="card-footer">
                    <div class="card-footer-item">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered">전체</div>
                            <div class="title is-size-4 has-text-centered">

                                <span class="has-text-info"><?=$cnt?></span>
            <? } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer-item">
                        <div class="content" style="width:100%;">
                            <div class="is-size-7 has-text-centered">미출근</div>
                            <div class="title is-size-4 has-text-centered">
                                <span>10</span>
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
                        <button class="button" alt="엑셀다운로드" id="btnExcel">
                                <span class="icon is-small">
                                    <i class="fas fa-file-excel"></i>
                                </span>
                            <span>엑셀로 다운로드</span>
                        </button>
                    <? } ?>
                </div>
            </div>
        </div>
        <a name="CEO" style="cursor: default">
        <div class="content">
            <div class="title is-size-5">
                CEO
            </div>
            <div class="columns is-multiline">
                <?
                $sql = "SELECT PRS_ID, PRS_NAME, PRS_POSITION1, PRS_POSITION2, PRS_EXTENSION, FILE_IMG, PRS_EMAIL, PRS_MOBILE FROM DF_PERSON WITH(NOLOCK) WHERE PRS_POSITION2 = '대표' AND PRF_ID = 4";
                $rs = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                    ?>
                    <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                        <div class="notification is-white is-bordered">
                            <div class="columns is-mobile">
                                <div class="column member-photo">
                                    <p class="image is-70x70 is-rounded-image">
                                        <img src="/file/<?= $col_file_img ?>">
                                    </p>
                                </div>
                                <div class="column">
                                    <p class="is-member-p">
                                        <span class="title is-size-6"><?= $col_prs_name ?></span>
                                        <span class="title is-size-7">/ 대표</span>
                                    </p>
                                    <p class="is-member-p">
                                        <?= $col_prs_mobile ?>
                                    </p>
                                    <p class="is-member-p">
                                        <span class="tag is-small"><?= $col_prs_email ?>@designfever.com</span>
                                        <span class="tag is-small"><?= $col_prs_extension ?></span>
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
        <a name="Planning"  style="cursor: default">
        <div class="content">
            <div class="title is-size-5">
                Planning
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

        ?>
            <a name="Creative Planning Division"  style="cursor: default">
        <div class="content">
            <div class="title is-size-6">
                Creative Planning Division
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
                    <div class="notification is-white is-bordered">
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-p">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-p">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-p">
                                    <span class="tag is-small"><?= $col_prs_email ?>@designfever.com</span>
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
                    <a name="Creative Planning 1 Team"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        Creative Planning 1 Team
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Creative Planning 1 Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281) 
                                   AND A.PRS_TEAM IN ('Creative Planning 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];

                        ?>
                        <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                            <div class="notification is-white is-bordered">
                                <div class="columns is-mobile">
                                    <div class="column member-photo">
                                        <p class="image is-70x70 is-rounded-image">
                                            <img src="/file/<?=$col_file_img?>">
                                        </p>
                                    </div>
                                    <div class="column">
                                        <p class="is-member-p">
                                            <span class="title is-size-6"><?=$col_prs_name?></span>
                                            <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                        </p>
                                        <p class="is-member-p">
                                            <?=$col_prs_mobile?>
                                        </p>
                                        <p class="is-member-p">
                                            <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                            <span class="tag is-small"><?=$col_prs_extension?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <? } ?>
                    </div>
                </div>
                <div class="is-member-depth-1">
                    <a name="Creative Planning 2 Team"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        Creative Planning 2 Team
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Creative Planning 2 Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>

                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281)
                                   AND A.PRS_TEAM IN ('Creative Planning 2 Team')  ORDER BY B.SEQNO, A.PRS_ID";

                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                        ?>
                        <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
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

        ?>
    <a name="Marketing Planning Division"  style="cursor: default">
    <div class="content">
            <div class="title is-size-6">
                Marketing Planning Division
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
                    <div class="notification is-white is-bordered">
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-p">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-p">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-p">
                                    <span class="tag is-small"><?= $col_prs_email ?>@designfever.com</span>
                                    <span class="tag is-small"><?= $col_prs_extension ?></span>
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
                <a name="Marketing Planning Division"  style="cursor: default">
                <div class="title is-team-title is-size-6">
                    Marketing Planning Division
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Marketing Planning Division')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,7) AND A.PRS_ID NOT IN(102,281)
                                   AND A.PRS_TEAM IN ('Marketing Planning Division')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                        ?>
                        <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                            <div class="notification is-white is-bordered">
                                <div class="columns is-mobile">
                                    <div class="column member-photo">
                                        <p class="image is-70x70 is-rounded-image">
                                            <img src="/file/<?=$col_file_img?>">
                                        </p>
                                    </div>
                                    <div class="column">
                                        <p class="is-member-p">
                                            <span class="title is-size-6"><?=$col_prs_name?></span>
                                            <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                        </p>
                                        <p class="is-member-p">
                                            <?=$col_prs_mobile?>
                                        </p>
                                        <p class="is-member-p">
                                            <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                            <span class="tag is-small"><?=$col_prs_extension?></span>
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
        <a name="Design"  style="cursor: default">
        <div class="content">
            <div class="title is-size-5">
                Design
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

        ?>
        <a name="Design 1 Division"  style="cursor: default">
        <div class="content">
            <div class="title is-size-6">
                Design 1 Division
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
                    <div class="notification is-white is-bordered">
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-p">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                </p>
                                <p class="is-member-p">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-p">
                                    <span class="tag is-small"><?= $col_prs_email ?>@designfever.com</span>
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
                    <a name="Design 1 Division 1 Team"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        Design 1 Division 1 Team
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 1 Division 1 Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281) 
                                   AND A.PRS_TEAM IN ('Design 1 Division 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];

                            ?>
                            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
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
            ?>
            <a name="Design 2 Division"  style="cursor: default">
            <div class="content">
                <div class="title is-size-6">
                    Design 2 Division
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
                        <div class="notification is-white is-bordered">
                            <div class="columns is-mobile">
                                <div class="column member-photo">
                                    <p class="image is-70x70 is-rounded-image">
                                        <img src="/file/<?=$col_file_img?>">
                                    </p>
                                </div>
                                <div class="column">
                                    <p class="is-member-p">
                                        <span class="title is-size-6"><?= $col_prs_name ?></span>
                                        <span class="title is-size-7">/ <?=$col_prs_position1?></span>
                                    </p>
                                    <p class="is-member-p">
                                        <?= $col_prs_mobile ?>
                                    </p>
                                    <p class="is-member-p">
                                        <span class="tag is-small"><?= $col_prs_email ?>@designfever.com</span>
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
                <a name="Design 2 Division 1 Team"  style="cursor: default">
                <div class="title is-team-title is-size-6">
                    Design 2 Division 1 Team
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 2 Division 1 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>
                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,7) AND A.PRS_ID NOT IN(102,281) 
                                   AND A.PRS_TEAM IN ('Design 2 Division 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                        ?>
                        <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                            <div class="notification is-white is-bordered">
                                <div class="columns is-mobile">
                                    <div class="column member-photo">
                                        <p class="image is-70x70 is-rounded-image">
                                            <img src="/file/<?=$col_file_img?>">
                                        </p>
                                    </div>
                                    <div class="column">
                                        <p class="is-member-p">
                                            <span class="title is-size-6"><?=$col_prs_name?></span>
                                            <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                        </p>
                                        <p class="is-member-p">
                                            <?=$col_prs_mobile?>
                                        </p>
                                        <p class="is-member-p">
                                            <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                            <span class="tag is-small"><?=$col_prs_extension?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                </div>
            </div>
            <div class="is-member-depth-1">
                <a name="Design 2 Division 2 Team"  style="cursor: default">
                <div class="title is-team-title is-size-6">
                    Design 2 Division 2 Team
                    <?
                    $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Design 2 Division 2 Team')";
                    $rs  = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $cnt = $record['CNT']; ?>
                        (<?=$cnt?>) <?}?>
                </div>

                <div class="columns is-multiline">
                    <?
                    $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281)
                                   AND A.PRS_TEAM IN ('Design 2 Division 2 Team')  ORDER BY B.SEQNO, A.PRS_ID";

                    $rs = sqlsrv_query($dbConn, $sql);
                    While ($record = sqlsrv_fetch_array($rs)) {
                        $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                        ?>
                        <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                            <div class="notification is-white is-bordered">
                                <div class="columns is-mobile">
                                    <div class="column member-photo">
                                        <p class="image is-70x70 is-rounded-image">
                                            <img src="/file/<?=$col_file_img?>">
                                        </p>
                                    </div>
                                    <div class="column">
                                        <p class="is-member-p">
                                            <span class="title is-size-6"><?=$col_prs_name?></span>
                                            <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                        </p>
                                        <p class="is-member-p">
                                            <?=$col_prs_mobile?>
                                        </p>
                                        <p class="is-member-p">
                                            <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                            <span class="tag is-small"><?=$col_prs_extension?></span>
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
        <a name="Motion"  style="cursor: default">
        <div class="content">
            <div class="title is-size-5">
                Motion
                <?
                $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Motion Division','Motion 1 Team', 'Art Division')";
                $rs  = sqlsrv_query($dbConn, $sql);
                While ($record = sqlsrv_fetch_array($rs)) {
                    $cnt = $record['CNT']; ?>
                    (<?=$cnt?>) <?}?>
            </div>
        </div>
        <?
        $sql = "select PRS_ID , PRS_NAME , PRS_TEAM , PRS_POSITION2, PRS_MOBILE, PRS_EMAIL, PRS_EXTENSION, FILE_IMG FROM DF_PERSON WITH (NOLOCK) WHERE PRF_ID IN(5,4,3)   AND PRS_TEAM = 'Motion Division'";
        $rs = sqlsrv_query($dbConn, $sql);
        While ($record = sqlsrv_fetch_array($rs)) {
        $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];

        ?>
        <a name="Motion Division"  style="cursor: default">
        <div class="content">
            <div class="title is-size-6">
                Motion Division
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
                    <div class="notification is-white is-bordered">
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-p">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?></span>
                                </p>
                                <p class="is-member-p">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-p">
                                    <span class="tag is-small"><?= $col_prs_email ?>@designfever.com</span>
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
                    <a name="Motion 1 Team"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        Motion 1 Team
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Motion 1 Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281) 
                                   AND A.PRS_TEAM IN ('Motion 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];

                            ?>
                            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
                <div class="is-member-depth-1">
                    <a name="Art Division"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        Art Division
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Art Division')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281)
                                   AND A.PRS_TEAM IN ('Art Division')  ORDER BY B.SEQNO, A.PRS_ID";

                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                            ?>
                            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
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
<!-- 모션실 끝 -->

<hr>

<!-- VID 실-->
        <a name="Development"  style="cursor: default">
        <div class="content">
            <div class="title is-size-5">
                Development
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

        ?>
        <a name="Visual Interaction Development"  style="cursor: default">
        <div class="content">
            <div class="title is-size-6">
                Visual Interaction Development
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
                    <div class="notification is-white is-bordered">
                        <div class="columns is-mobile">
                            <div class="column member-photo">
                                <p class="image is-70x70 is-rounded-image">
                                    <img src="/file/<?=$col_file_img?>">
                                </p>
                            </div>
                            <div class="column">
                                <p class="is-member-p">
                                    <span class="title is-size-6"><?= $col_prs_name ?></span>
                                    <span class="title is-size-7">/ <?=$col_prs_position2?></span>
                                </p>
                                <p class="is-member-p">
                                    <?= $col_prs_mobile ?>
                                </p>
                                <p class="is-member-p">
                                    <span class="tag is-small"><?= $col_prs_email ?>@designfever.com</span>
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
                    <a name="VID 1 Team"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        VID 1 Team
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('VID 1 Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281) 
                                   AND A.PRS_TEAM IN ('VID 1 Team')  ORDER BY B.SEQNO, A.PRS_ID";
                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];

                            ?>
                            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
                <div class="is-member-depth-1">
                    <a name="VID 2 Team"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                       VID 2 Team
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('VID 2 Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>

                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281)
                                   AND A.PRS_TEAM IN ('VID 2 Team')  ORDER BY B.SEQNO, A.PRS_ID";

                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                            ?>
                            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>

                <div class="is-member-depth-1">
                    <a name="LAB"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        LAB
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('LAB')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION 	 WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281)
                                   AND A.PRS_TEAM IN ('LAB')  ORDER BY B.SEQNO, A.PRS_ID";

                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];
                            ?>
                            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7"> <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
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
<!-- VID 끝 -->

<!-- BST-->
        <a name="BST"  style="cursor: default">
        <div class="content">
            <div class="title is-size-5">
                BST
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
                    <a name="Business Support Team"  style="cursor: default">
                    <div class="title is-team-title is-size-6">
                        Business Support Team
                        <?
                        $sql = "SELECT COUNT(PRS_ID)AS CNT FROM DF_PERSON  WHERE PRF_ID IN(5,4,3,2,1) AND PRS_TEAM IN ('Business Support Team')";
                        $rs  = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $cnt = $record['CNT']; ?>
                            (<?=$cnt?>) <?}?>
                    </div>
                    <div class="columns is-multiline">
                        <?
                        $sql = "SELECT A.PRS_ID , A.PRS_NAME , A.PRS_POSITION1, A.PRS_POSITION2, A.PRS_EXTENSION, A.FILE_IMG, A.PRS_EMAIL, A.PRS_MOBILE FROM DF_PERSON A WITH (NOLOCK) INNER JOIN DF_POSITION_CODE B WITH (NOLOCK) ON A.PRS_POSITION = B.POSITION WHERE A.PRF_ID IN (1,2,3,4,5,7) AND A.PRS_ID NOT IN(102,281) 
                                   AND A.PRS_TEAM IN ('Business Support Team')  ORDER BY B.SEQNO, A.PRS_ID";
                        $rs = sqlsrv_query($dbConn, $sql);
                        While ($record = sqlsrv_fetch_array($rs)) {
                            $col_prs_id = $record['PRS_ID']; $col_prs_name = $record['PRS_NAME']; $col_prs_position1 = $record['PRS_POSITION1']; $col_prs_position2 = $record['PRS_POSITION2']; $col_prs_extension = $record['PRS_EXTENSION']; $col_file_img = $record['FILE_IMG']; $col_prs_email = $record['PRS_EMAIL']; $col_prs_mobile = $record['PRS_MOBILE'];

                            ?>
                            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                                <div class="notification is-white is-bordered">
                                    <div class="columns is-mobile">
                                        <div class="column member-photo">
                                            <p class="image is-70x70 is-rounded-image">
                                                <img src="/file/<?=$col_file_img?>">
                                            </p>
                                        </div>
                                        <div class="column">
                                            <p class="is-member-p">
                                                <span class="title is-size-6"><?=$col_prs_name?></span>
                                                <span class="title is-size-7">/ <?=$col_prs_position2?> | <?=$col_prs_position1?></span>
                                            </p>
                                            <p class="is-member-p">
                                                <?=$col_prs_mobile?>
                                            </p>
                                            <p class="is-member-p">
                                                <span class="tag is-small"><?=$col_prs_email?>@designfever.com</span>
                                                <span class="tag is-small"><?=$col_prs_extension?></span>
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


        <div class="columns is-multiline">

            <div class="column is-one-third-desktop is-half-tablet is-one-quarter-fullhd">
                <div class="notification is-white is-bordered">
                    <div class="columns is-mobile">
                        <div class="column member-photo">
                            <p class="image is-70x70 is-rounded-image">
                            </p>
                        </div>
                        <div class="column">
                            <p class="is-member-p">
                                <span class="title is-size-6">소장님</span>
                            </p>
                            <p class="is-member-p">
                            </p>
                            <p class="is-member-p">
                                <span class="tag is-small">401</span>
                            </p>
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
