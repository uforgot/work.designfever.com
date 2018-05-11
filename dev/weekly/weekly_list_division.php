<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	//팀선택 셀렉트박스 관련
	if (in_array($prs_position,$positionS_arr))
	{
		if (in_array($prs_team,array('CEO')))
		{
			$cur_team = "경영지원팀";
		}
		else
		{
			$cur_team = $prs_team;
		}
		$sel_view = 'Y';
	}
	else
	{
		$cur_team = $prs_team; //셀렉트박스 기본선택
		$sel_view = 'N';	   //셀렉트박스 노출여부
	}

	// 최종 주차 정보
	$sql = "SELECT MAX(WEEK_ORD) AS ORD FROM DF_WEEKLY";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$last_week = $record['ORD'];

	$week = isset($_REQUEST['week']) ? $_REQUEST['week'] : $last_week;
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : $cur_team;

	// 다음 주차 링크
	$sql = "SELECT MIN(WEEK_ORD) AS ORD FROM DF_WEEKLY WHERE WEEK_ORD > '$week'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$next_week = $record['ORD'];
	if($next_week) $next_link = "<a class='button is-text is-small is-primary' href='weekly_list_division.php?week=".$next_week."&team=".$team."'><i class='fa fa-chevron-right'></i></a>";
	else $next_link = "<a class='button is-text is-small is-primary'><i class='fa fa-chevron-right'></i></a>";


	// 이전 주차 링크
	$sql = "SELECT MAX(WEEK_ORD) AS ORD FROM DF_WEEKLY WHERE WEEK_ORD < '$week'";
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);
	$prev_week = $record['ORD'];
	if($prev_week) $prev_link = "<a class='button is-text is-small is-primary' href='weekly_list_division.php?week=".$prev_week."&team=".$team."'><i class='fa fa-chevron-left'></i></a>";
	else $prev_link = "<a class='button is-text is-small is-primary'><i class='fa fa-chevron-left'></i>asdf</a>";



	//참여프로젝트 리스트 추출
	$searchSQL = " WHERE WEEK_ORD LIKE '$week%' AND A.PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team'))))";

	$sql = "SELECT 
				A.SEQNO, A.MEMO, A.PRS_NAME, B.PROJECT_NO, B.THIS_WEEK_CONTENT, B.NEXT_WEEK_CONTENT, B.THIS_WEEK_RATIO, B.PRS_ID,
				(SELECT DISTINCT PART_RATE FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = B.PROJECT_NO AND PRS_ID = B.PRS_ID) PART_RATE
			FROM 
				DF_WEEKLY A WITH(NOLOCK) 
				INNER JOIN DF_WEEKLY_DETAIL B WITH(NOLOCK) 
				ON A.SEQNO = B.WEEKLY_NO
			$searchSQL
			ORDER BY
				B.PROJECT_NO DESC";

	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$list[$record['PROJECT_NO']][] = array
										(
											'id'=>$record['PRS_ID'],
											'name'=>$record['PRS_NAME'],
											'ratio'=>$record['PART_RATE'],
											'this_ratio'=>$record['THIS_WEEK_RATIO'],
											'this_content'=>$record['THIS_WEEK_CONTENT'],
											'next_content'=>$record['NEXT_WEEK_CONTENT']
										);

		//건의 및 기타사항
		if($record['MEMO'] && !$memo)
			$memo = nl2br(str_replace(" ", '&nbsp;',$record['MEMO']))."<br>";
	}

	//echo "<xmp>";
	//print_r($list);
	//echo "</xmp>";
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function weekSearch(val) {
		document.location.href = "./weekly_list_division.php?week=" + val + "&team=<?=$team?>";
	}

	function teamSearch(val) {
		if (!val)
		{
			alert('보고서 조회는 팀별로 가능합니다.\n팀명을 선택해 주세요!');
			return;
		}

		document.location.href = "./weekly_list_division.php?week=<?=$week?>&team=" + val;
	}
</script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>
<form name="form" method="post">
<input type="hidden" name="week" id="week" value="<?=$week?>">
<input type="hidden" name="team" id="team" value="<?=$team?>">
<? include INC_PATH."/weekly_menu.php"; ?>

    <!-- 본문 시작 -->
    <section class="section df-weekly">
        <div class="container">
            <nav class="level is-mobile">
                <div class="level-left">
                    <p class="buttons">
                        <a href="/weekly/weekly_list_team.php" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                            <span>목록</span>
                        </a>
                    </p>
                </div>
                <div class="level-right">
                    <?
                    if ($sel_view == 'Y')
                    {
                        ?>
                        <div class="control select">
                            <select name="team" onchange="javascript:teamSearch(this.value);">
                                <?
                                $selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
                                $selRs = sqlsrv_query($dbConn,$selSQL);

                                while ($selRecord = sqlsrv_fetch_array($selRs))
                                {
                                    $selStep = $selRecord['STEP'];
                                    $selTeam = $selRecord['TEAM'];

                                    if ($selStep == 2) {
                                        $selTeam2 = $selTeam;
                                    }
                                    else if ($selStep == 3) {
                                        $selTeam2 = "&nbsp;&nbsp;└ ". $selTeam;
                                    }
                                    ?>
                                    <option value="<?=$selTeam?>"<? if ($team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                        <?
                    }
                    ?>
                </div>
            </nav>
            <div class="content">
                <div class="calendar is-large">
                    <div class="calendar-nav">
                        <div class="calendar-nav-previous-month">
                            <?=$prev_link?>
                        </div>
                        <?$week_titile = substr($week,0,4)."년 ".substr($week,4,2)."월 ".substr($week,6,1)."주차 주간보고";?>
                        <div>
                            <span class="title is-size-5 has-text-white"><?=$week_titile?></span><br>
                            <span class="title is-7 has-text-white"><?=$team?></span>
                        </div>
                        <div class="calendar-nav-next-month">
                            <?=$next_link?>
                        </div>
                    </div>
                </div>
            </div>


                    <?
                    if (count($list)==0)
                    {
                        ?>
                        <div class="content">
                            <div class="calendar is-large">
                                <div class="calendar">
                                    <div>
                                       해당 정보가 없습니다.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?
                    }
                    else
                    {
                    foreach($list as $key1 => $val1)
                    {
                    $searchSQL = " WHERE PROJECT_NO = '".$key1."'";

                    $sql = "SELECT TITLE FROM DF_PROJECT $searchSQL";
                    $rs = sqlsrv_query($dbConn,$sql);
                    $record = sqlsrv_fetch_array($rs);

                    if($record)	$project_name = $record['TITLE'];
                    else if($key1 == "DF0000_ETC") $project_name = "기타업무";

                    $name = "";
                    $contents = "";
                    $line_cnt = count($val1);
                    $cnt = 1;

                    foreach($val1 as $key2 => $val2)
                    {
                        if($cnt < $line_cnt) $border = "border-bottom:1px solid #e3e3e3;";
                        else				 $border = "border-bottom:0px;";

//				if($key1 == "DF0000_ETC") $name .= $val2['name']." (".$val2['this_ratio']."%)<br>";
//				else					  $name .= $val2['name']." (".$val2['this_ratio']."%)<br>";

                        $contents .= "<tr>";
                        $contents .= "	<td>".$val2['name']." (".$val2['this_ratio']."%)"."</td>";
                        $contents .= "	<td>".nl2br(str_replace(" ", '&nbsp;',$val2['this_content']))."</td>";
                        $contents .= "	<td>".nl2br(str_replace(" ", '&nbsp;',$val2['next_content']))."</td>";
                        $contents .= "</tr>";

                        $cnt++;
                    }

                    //$contents = "<table style='width:100%;'>".$contents."</table>";
                    ?>
                <div class="content">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-header-title"><?=$project_name?></div>
                            </div>
                            <div class="card-content">
                                <table class="table is-report">
                                    <colgroup>
                                        <col width="14%">
                                        <col width="43%">
                                        <col width="43%">
                                    </colgroup>
                                    <thead>
                                    <th>참여자 (참여비율)</th>
                                    <th>금주 진행업무</th>
                                    <th>차주 진행업무</th>
                                    </thead>
                                    <?=$contents?>
                                </table>
                            </div>
                        </div>
                 </div>
                        <?
                      }
                }
                ?>


            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-title">건의 및 기타사항</div>
                    </div>
                    <div class="card-content">
                        <table class="table is-report">
                            <?=$memo?>
                        </table>
                    </div>
                </div>
            </div>

            <hr class="hr-strong">

            <nav class="level is-mobile">
                <div class="level-left">
                    <p class="buttons">
                        <a href="/weekly/weekly_list_team.php" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                            <span>목록</span>
                        </a>
                    </p>
                </div>

                <div class="level-right">

                </div>
            </nav>

        </div>
    </section>
    <!-- 본문 끌 -->
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
