<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	//권한 체크
	if ($prf_id != "4")
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("해당페이지는 관리자만 확인 가능합니다.");
		location.href="vacation_list.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

	$p_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : null;
	$p_mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null;
	$p_vacation = isset($_REQUEST['vacation']) ? $_REQUEST['vacation'] : null;
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

	$fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : date("Y");
	$fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : date("m");
	if (strlen($fr_month) == 1) { $fr_month = "0". $fr_month; }
	$fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : 1;
	if (strlen($fr_day) == 1) { $fr_day = "0". $fr_day; }
	$to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : date("Y");
	$to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : date("m");
	if (strlen($to_month) == 1) { $to_month = "0". $to_month; }
	$to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : date("d");
	if (strlen($to_day) == 1) { $to_day = "0". $to_day; }

	$fr_date = $fr_year ."-". $fr_month ."-". $fr_day;
	$to_date = $to_year ."-". $to_month ."-". $to_day;

	$searchSQL = " WHERE USE_YN = 'Y' AND STATUS NOT IN ('임시') AND FORM_CATEGORY IN ('휴가계') AND CONVERT(char(10),REG_DATE,120) BETWEEN '$fr_date' AND '$to_date'";
	if ($p_status != "")
	{
		switch($p_status)
		{
			case "미결재" :
				$searchSQL .= " AND STATUS IN ('미결재','진행중')";
				break;
			case "기각" :
				$searchSQL .= " AND STATUS IN ('보류','기각')";
				break;
			case "결재" :
				$searchSQL .= " AND STATUS IN ('전결','결재')";
				break;
		}
	}
	if ($p_mode == "team")
	{
		if ($p_team != "")
		{
			//$searchSQL .= " AND PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$p_team')))";
            $searchSQL .= " AND PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team')))";
		}
	}
	else if ($p_mode == "vacation")
	{
		if ($p_vacation != "")
		{
			$searchSQL .= " AND FORM_TITLE LIKE '%". $p_vacation ."%'";
		}
	}
	if ($p_name != "")
	{
		$searchSQL .= " AND PRS_NAME = '$p_name'";
	}

	$sql = "SELECT COUNT(DISTINCT DOC_NO) FROM DF_APPROVAL WITH(NOLOCK)". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT
				T.DOC_NO, T.COUNT
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER(ORDER BY DOC_NO DESC) AS ROWNUM,
					DOC_NO, COUNT(SEQNO) AS COUNT
				FROM 
					DF_APPROVAL WITH(NOLOCK)
				$searchSQL
				GROUP BY 
					DOC_NO
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>
<!--기존에 쓰이던 CSS-->
<link rel="stylesheet" href="/assets/css/style_20180406.css" />
<link rel="stylesheet" href="/assets/css/jquery-ui.css" />
<!--기존에 쓰이던 CSS-->
<script type="text/javascript">
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
			}
		});
	});
</script>
<script src="/assets/js/vacation.js"></script>
</head>
<body onload="selCase(this.form)">
<? include INC_PATH."/top_menu.php"; ?>
<form name="form" method="post">
    <input type="hidden" name="page" value="<?=$page?>">
    <? include INC_PATH."/vacation_menu.php";?>
    <!-- 본문 시작 -->
    <section class="section df-vacation">
    <div class="container">
        <div class="content">
            <!--검색 영역-->
            <div class="card">
                <div class="card-content">
                    <div class="columns is-column-marginless">
                        <div class="column" style="display:inline-block;flex-grow:0;flex-basis:auto;">
                            <div class="field is-group">
                                <div class="control select">
                                    <select name="fr_year" id="fr_year">
                                        <?
                                        for ($i=$startYear; $i<=($fr_year+1); $i++)
                                        {
                                            if ($i == $fr_year)
                                            {  $selected = " selected"; }
                                            else
                                            { $selected = ""; }
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
                                            { $j = "0".$i; }
                                            else
                                            { $j = $i; }

                                            if ($j == $fr_month)
                                            { $selected = " selected"; }
                                            else
                                            { $selected = ""; }
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
                                            { $j = "0".$i; }
                                            else
                                            { $j = $i; }
                                            if ($j == $fr_day)
                                            { $selected = " selected"; }
                                            else
                                            { $selected = ""; }
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
                                            { $selected = " selected"; }
                                            else
                                            { $selected = ""; }
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
                                            { $j = "0".$i; }
                                            else
                                            { $j = $i; }
                                            if ($j == $to_month)
                                            { $selected = " selected"; }
                                            else
                                            { $selected = ""; }
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
                                            { $j = "0".$i; }
                                            else
                                            { $j = $i; }
                                            if ($j == $to_day)
                                            { $selected = " selected"; }
                                            else
                                            { $selected = "";}
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
                    <div class="columns is-column-marginless">
                        <div class="column">
                            <div class="field is-group">
                                <div class="control select">
                                    <select name="status">
                                        <option value="">승인여부 전체</option>
                                        <option value="미결재"<? if ($p_status == "미결재") { echo " selected"; } ?>>미결재</option>
                                        <option value="기각"<? if ($p_status == "기각") { echo " selected"; } ?>>기각</option>
                                        <option value="결재"<? if ($p_status == "결재") { echo " selected"; } ?>>결재</option>
                                    </select>
                                </div>
                                <div class="control select">
                                    <select name="mode" onChange="javascript:selCase(this.form);">
                                        <option value="">전체</option>
                                        <option value="team"<? if ($p_mode == "team") { echo " selected"; } ?>>부서</option>
                                        <option value="vacation"<? if ($p_mode == "vacation") { echo " selected"; } ?>>휴가</option>
                                    </select>
                                </div>
                                <div class="control select" id ="team_div">
                                    <select name="team" style="display:<? if ($p_mode == "team") { echo ""; } else { echo " none"; } ?>; ">
                                        <option value=""<? if ($p_team2 == ""){ echo " selected"; } ?>>전직원</option>
                                        <?
                                        //$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
                                        $selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
                                        $selRs = sqlsrv_query($dbConn,$selSQL);

                                        while ($selRecord = sqlsrv_fetch_array($selRs))
                                        {
                                            $selStep = $selRecord['STEP'];
                                            $selTeam = $selRecord['TEAM'];

                                            $blank = "";
                                            for ($i=3;$i<=$selStep;$i++)
                                            {
                                                $blank .= "&nbsp;&nbsp;&nbsp;";
                                            }
                                            ?>
                                            <option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$blank?><?=$selTeam?></option>
                                            <?
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="control select" id="vacation_div">
                                    <select name="vacation" style="display:<? if ($p_mode == "vacation") { echo ""; } else { echo " none"; } ?>;" >
                                        <option value="">전체</option>
                                        <option value="연차"<? if ($p_vacation == "연차") { echo " selected"; } ?>>연차</option>
                                        <option value="병가"<? if ($p_vacation == "병가") { echo " selected"; } ?>>병가</option>
                                        <option value="반차"<? if ($p_vacation == "반차") { echo " selected"; } ?>>반차</option>
                                        <option value="리프레쉬"<? if ($p_vacation == "리프레쉬") { echo " selected"; } ?>>리프레쉬</option>
                                        <option value="프로젝트"<? if ($p_vacation == "프로젝트") { echo " selected"; } ?>>프로젝트</option>
                                        <option value="무급"<? if ($p_vacation == "무급") { echo " selected"; } ?>>무급</option>
                                        <option value="경조사"<? if ($p_vacation == "경조사") { echo " selected"; } ?>>경조사</option>
                                        <option value="예비군"<? if ($p_vacation == "예비군") { echo " selected"; } ?>>예비군</option>
                                        <option value="기타"<? if ($p_vacation == "기타") { echo " selected"; } ?>>기타</option>
                                        <option value="휴가 소진시"<? if ($p_vacation == "휴가 소진시") { echo " selected"; } ?>>휴가 소진시</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field is-grouped">
                        <div class="control is-expanded">
                            <input class="input" type="text" type="text" name="name" value="<?=$p_name?>" placeholder="기안자">
                        </div>
                        <div class="control">
                            <a href="javascript:funSearch(this.form);" class="button is-link">
                                <span class="icon is-small">
                                    <i class="fas fa-search"></i>
                                </span>
                                <span>검색</span>
                            </a>
                        </div>
                        <div class="control">
                            <a href="vacation_member.php" class="button is-danger">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>초기화</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
      	<!--검색 영역-->
            <table class="table is-fullwidth is-hoverable is-resize">
                <colgroup>
                    <col width="8%">
                    <col width="*">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                </colgroup>
                <thead>
                <tr>
                    <th><span class="is-hidden-mobile">No.</span></th>
                    <th>기간 / 기안자</th>
                    <th class="has-text-centered">승인여부</th>
                    <th class="has-text-centered">종류</th>
                    <th class="has-text-centered">등록일</th>
                </tr>
                </thead>
                <!-- 일반 리스트 -->
                <tbody class="list">
                <?
                $i = $total_cnt-($page-1)*$per_page;
                if ($i==0)
                {
                    ?>
                    <tr>
                        <td colspan="5" class="has-text-centered">등록된 휴가계가 없습니다.</td>
                    </tr>
                    <?
                }
                else
                {
                    while ($record = sqlsrv_fetch_array($rs))
                    {
                        $doc_no = $record['DOC_NO'];
                        $count = $record['COUNT'];

                        $sql1 = "SELECT
						TITLE, CONVERT(char(10),REG_DATE,102) AS REG_DATE, PRS_TEAM, PRS_POSITION, PRS_NAME, 
						CONVERT(char(10),START_DATE,102) AS START_DATE, CONVERT(char(10),END_DATE,102) AS END_DATE, USE_DAY, STATUS, FORM_CATEGORY, FORM_TITLE
					FROM 
						DF_APPROVAL WITH(NOLOCK)
					$searchSQL
						AND DOC_NO = '$doc_no'
					ORDER BY 
						SEQNO";
                        $rs1 = sqlsrv_query($dbConn,$sql1);

                        if ($count == 2)
                        {
                            $vacation = "";
                            while ($record1 = sqlsrv_fetch_array($rs1))
                            {
                                $form_category = $record1['FORM_CATEGORY'];
                                $form_title = $record1['FORM_TITLE'];
                                $title = $record1['TITLE'];
                                $reg_date = $record1['REG_DATE'];
                                $team = $record1['PRS_TEAM'];
                                $position = $record1['PRS_POSITION'];
                                $name = $record1['PRS_NAME'];
                                $start_date = $record1['START_DATE'];
                                $end_date = $record1['END_DATE'];
                                $use_day = $record1['USE_DAY'];
                                $status = $record1['STATUS'];

                                $vacation .= $start_date ." - ". $end_date ." (". number_format($use_day,1) ."일)<br>";
                            }

                            $form_title = "연프";
                            ?>
                            <tr>
                                <td><?=$i?></td><td>
                                    <div class="level is-mobile">
                                        <div class="level-left">
                                            <div class="content">
                                                <span class="is-size-7"><a href="javascript:funView('<?=$doc_no?>');"><?=$vacation?></a></span>
                                                <br>
                                                <span><?=$team?> <?=$position?> <?=$name?></span>
                                            </div>
                                        </div>
                                        <div class="level-right is-hidden-tablet">
                                            <a href="javascript:funView('<?=$doc_no?>');" class="button"><?=$form_title?> 휴가계</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="has-text-centered"><?=$status?></td>
                                <td class="has-text-centered is-hidden-mobile">
                                    <div class="control has-text-centered">
                                        <a href="javascript:funView('<?=$doc_no?>');" class="button"><?=$form_title?> 휴가계</a>
                                    </div>
                                </td>
                                <td><?=$reg_date?></td>
                            </tr>
                            <?
                            }
                        else
                        {
                            $record1 = sqlsrv_fetch_array($rs1);

                            $form_category = $record1['FORM_CATEGORY'];
                            $form_title = $record1['FORM_TITLE'];
                            $title = $record1['TITLE'];
                            $reg_date = $record1['REG_DATE'];
                            $team = $record1['PRS_TEAM'];
                            $position = $record1['PRS_POSITION'];
                            $name = $record1['PRS_NAME'];
                            $start_date = $record1['START_DATE'];
                            $end_date = $record1['END_DATE'];
                            $use_day = $record1['USE_DAY'];
                            $status = $record1['STATUS'];
                            ?>

                            <tr>
                                <td><?=$i?></td><td>
                                    <div class="level is-mobile">
                                        <div class="level-left">
                                            <div class="content">
                                                <span class="is-size-7"><a href="javascript:funView('<?=$doc_no?>');"><?=$start_date?> - <?=$end_date?> (<?=number_format($use_day,1)?>일)</a></span>
                                                <br>
                                                <span><?=$team?> <?=$position?> <?=$name?></span>
                                            </div>
                                        </div>
                                        <div class="level-right is-hidden-tablet">
                                            <a href="javascript:funView('<?=$doc_no?>');" class="button"><?=$form_title?> 휴가계</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="has-text-centered"><?=$status?></td>
                                <td class="has-text-centered is-hidden-mobile">
                                    <div class="control has-text-centered">
                                        <a href="javascript:funView('<?=$doc_no?>');" class="button"><?=$form_title?> 휴가계</a>
                                    </div>
                                </td>
                                <td><?=$reg_date?></td>
                            </tr>
                            <?
                        }
                        $i--;
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <!--페이징처리-->
        <nav class="pagination" role="navigation" aria-label="pagination">
            <?=getPaging($total_cnt,$page,$per_page);?>
            </ul>
        </nav>
        <!--페이징처리-->
    </div>
</section>
<!-- 본문 끌 -->
<? include INC_PATH."/bottom.php"; ?>
</form>
<div id="popDetail" class="approval-popup2" style="display:none;">
    <div class="title">
        <h3 class="aaa">휴가계</h3>
        <a href="javascript:HidePop('Detail');"><img src="/img/btn_popup_close.gif" alt=""></a>
    </div>

    <div class="content-title" style="height:10px;">
    </div>

    <div class="content-wrap" id="pop_detail_content">

    </div>

    <div class="btn-wrap" id="pop_detail_modify">
    </div>
</div>
</body>
</html>
