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
	<script type="text/javascript">
		alert("해당페이지는 임원,관리자만 확인 가능합니다.");
		location.href="commuting_list.php";
	</script>
<?
		exit;
	}
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 

	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "name"; 

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

	$date = $p_year ."-". $p_month;

	if ($p_year == $nowYear && $p_month == $nowMonth && $nowDay < 3) {
		$now = mktime(0,0,0,date("m"),1,date("Y"));
		$prevMonth = date("m",strtotime("-1 month",$now));
?>
		<script>
			alert("<?=$nowYear?>년 <?=$nowMonth?>월의 팀별 근태 통계는 3일 이후에 보실 수 있습니다.");
			location.href = "commuting_total_team.php?year=<?=$nowYear?>&month=<?=$prevMonth?>&team=<?=$p_team?>";
		</script>
<?
		exit;
	}
	else if ($p_year > $nowYear) {
?>
		<script>
			alert("<?=$nowYear?>년 이후의 팀별 근태 통계는 보실 수 없습니다.");
			location.href = "commuting_total_team.php?year=<?=$nowYear?>&month=<?=$nowMonth?>";
		</script>
<?
		exit;
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN P.PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	if ($p_team != "")
	{
		$teamSQL = " WHERE P.PRS_TEAM = '$p_team'";
	}

	switch($sort)
	{
		case "name" : 
			$orderbycase = "ORDER BY PRS_NAME";
			break;
		case "position" : 
			$orderbycase = "ORDER BY CASE ". $orderby . " END, PRS_NAME";
			break;
		case "avg" : 
			$orderbycase = "ORDER BY AVG_TIME DESC, AVG_MINUTE DESC, PRS_NAME";
			break;
		case "over" : 
			$orderbycase = "ORDER BY REAL_OVER DESC";
			break;
	}

	$sql = "SELECT 
				COUNT(DISTINCT P.PRS_ID) 
			FROM 
				DF_PERSON P WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
			ON 
				P.PRS_ID = B.PRS_ID 
			$teamSQL 
				AND P.PRS_ID NOT IN (15,22,24,87,102,148) AND P.PRF_ID IN (1,2,3,4,5)";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 20;

//	$sql = "EXEC SP_COMMUTING_TOTAL_TEAM_01 '$per_page','$page','$date','$p_team','$sort'";
		$sql = "SELECT 
					R.PRS_LOGIN, R.PRS_ID, R.PRS_NAME, R.PRS_TEAM, R.PRS_POSITION, 
					R.BIZ_COMMUTE, R.LAW_COMMUTE, R.LATENESS, R.VACATION, R.COMMUTE_DATE, R.SUBVACATION1, R.SUBVACATION2, 
					R.AVGTIME1, R.AVGMINUTE1, R.AVGTIME2, R.AVGMINUTE2, R.AVG_TIME, R.AVG_MINUTE, 
					R.BIZ_TOTAL_TIME, R.BIZ_TOTAL_MINUTE, R.TOTAL_TIME, R.TOTAL_MINUTE, R.OVER_TIME, R.OVER_MINUTE, R.OVER_DATE, 
					R.OFF_TIME, R.OFF_MINUTE, R.BIZ_OFF_TIME, R.BIZ_OFF_MINUTE, R.REAL_OVER, R.REAL_AVG, R.REAL_OFF, R.PAY  
				FROM 
				(
					SELECT 
						ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
						T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION, 
						T.BIZ_COMMUTE, T.LAW_COMMUTE, T.LATENESS, T.VACATION, T.COMMUTE_DATE, T.SUBVACATION1, T.SUBVACATION2, 
						T.AVGTIME1, T.AVGMINUTE1, T.AVGTIME2, T.AVGMINUTE2, T.AVG_TIME, T.AVG_MINUTE, 
						T.BIZ_TOTAL_TIME, T.BIZ_TOTAL_MINUTE, T.TOTAL_TIME, T.TOTAL_MINUTE, T.OVER_TIME, T.OVER_MINUTE, T.OVER_DATE, 
						T.OFF_TIME, T.OFF_MINUTE, T.BIZ_OFF_TIME, T.BIZ_OFF_MINUTE, (T.REAL_OVER - (T.PAY * 60)) AS REAL_OVER, T.REAL_AVG, T.REAL_OFF, T.PAY  
					FROM
					(
						SELECT
							P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
							D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION, D.COMMUTE_DATE, D.SUBVACATION1, D.SUBVACATION2, 
							D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
							((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) / 60) AS AVG_TIME, 
							((D.REAL_AVG - D.REAL_OFF - ((D.PAY + D.BIZ_COMMUTE) * 60)) / (D.BIZ_COMMUTE + D.LAW_COMMUTE + D.SUBVACATION1 + D.SUBVACATION2) % 60) AS AVG_MINUTE, 
							D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, 
							D.TOTAL_TIME, D.TOTAL_MINUTE, D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, 
							D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, (D.REAL_OVER - (D.PAY * 60)) AS REAL_OVER, D.REAL_AVG, D.REAL_OFF, D.PAY  
						FROM 
							(
								SELECT 
									* 
								FROM 
									DF_PERSON A
								WHERE 
									PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,90,102) AND SUBSTRING(PRS_JOIN,1,7) <= '". $date ."'
									AND (SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=A.PRS_ID AND DATE LIKE '". $date ."%') > 0
							) P 
							INNER JOIN 
							(
								SELECT 
									PRS_ID, 
									(SELECT COUNT(A.SEQNO) 
										FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
										WHERE A.GUBUN1 IN (1,4,6,7,8) AND A.GUBUN2 IN (2,3,5,6,9) AND A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $date ."%' AND B.DATEKIND = 'BIZ') AS BIZ_COMMUTE, --평일 정상출근
									(SELECT COUNT(A.SEQNO) 
										FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN HOLIDAY B WITH(NOLOCK) ON REPLACE(A.DATE,'-','') = B.DATE
										WHERE A.PRS_ID=C.PRS_ID AND A.DATE LIKE '". $date ."%' AND B.DATEKIND IN ('FIN','LAW')) AS LAW_COMMUTE, --휴일 근무일
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (7) AND GUBUN2 IN (2,3,6) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS LATENESS, --지각 
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (10,11,12,13,14,16,17,18,19,20,21) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS VACATION, --휴가 
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS COMMUTE_DATE, --근무일수
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN1 IN (4,8) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS SUBVACATION1, --오전반차
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE GUBUN2 IN (5,9) AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%') AS SUBVACATION2, --오후반차 
									(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGTIME1, --평균출근시
									(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME1, 9,2) * 3600 + SUBSTRING(CHECKTIME1, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
											WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,6,7) AND GUBUN2 IN (2,3,5,6,9) GROUP BY PRS_ID) Y) AS AVGMINUTE1, --평균출근분
									(SELECT DISTINCT (Y.ENTERTIME/Y.CNT) / 3600 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
											WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGTIME2, --평균퇴근시 
									(SELECT DISTINCT ((Y.ENTERTIME/Y.CNT) % 3600) / 60 
										FROM (
											SELECT PRS_ID , SUM(SUBSTRING(CHECKTIME2, 9,2) * 3600 + SUBSTRING(CHECKTIME2, 11,2) * 60) AS ENTERTIME , COUNT(*) AS CNT 
											FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
											WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,6) GROUP BY PRS_ID) Y) AS AVGMINUTE2, --평균퇴근분 
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') 
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS BIZ_TOTAL_TIME, --평일 총 근무시간 시
									(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','') 
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS BIZ_TOTAL_MINUTE, --평일 총 근무시간 분	
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_TIME, --총 근무시간 시
									(SELECT (SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60)) %3600 /60
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9) AND PRS_ID=C.PRS_ID) AS TOTAL_MINUTE, --총 근무시간 분	
									(SELECT SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60) / 3600
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_TIME, --초과 근무시간 시
									(SELECT (SUM(SUBSTRING(OVERTIME, 1,2) * 3600 + SUBSTRING(OVERTIME, 3,2) * 60)) %3600 /60 
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_MINUTE, --초과 근무시간 분
									(SELECT COUNT(SEQNO) 
										FROM DF_CHECKTIME WITH(NOLOCK)
										WHERE PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%' AND OVERTIME > '0000' AND UNDERTIME = '0000') AS OVER_DATE, --초과 근무일수
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 
										FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS OFF_TIME, --외출 시
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) %3600 /60 
										FROM DF_CHECKTIME_OFF WITH(NOLOCK) WHERE DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS OFF_MINUTE, -- 외출 분 					
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) / 3600 
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS BIZ_OFF_TIME, --평일 외출 시
									(SELECT SUM(SUBSTRING(TOTALTIME, 1,2) * 3600 + SUBSTRING(TOTALTIME, 3,2) * 60) % 3600 /60
										FROM HOLIDAY A WITH(NOLOCK) INNER JOIN DF_CHECKTIME_OFF B WITH(NOLOCK) ON A.DATE = REPLACE(B.DATE,'-','')
										WHERE A.DATEKIND = 'BIZ' AND B.DATE LIKE '". $date ."%' AND PRS_ID = C.PRS_ID) AS BIZ_OFF_MINUTE, --평일 외출 분
									((SELECT ISNULL(SUM(SUBSTRING(OVERTIME, 1,2) * 60 + SUBSTRING(OVERTIME, 3,2)),0)
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND OVERTIME > '0000' AND UNDERTIME = '0000')
									-(SELECT ISNULL(SUM(SUBSTRING(UNDERTIME, 1,2) * 60 + SUBSTRING(UNDERTIME, 3,2)),0)
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND UNDERTIME > '0000' AND OVERTIME = '0000')) AS REAL_OVER, --연장 근무시간 분단위 표시
									(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
										FROM DF_CHECKTIME WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000' AND GUBUN1 IN (1,4,6,7,8) AND GUBUN2 IN (2,3,5,6,9)) AS REAL_AVG, --평균 근무시간 분단위 표시
									(SELECT ISNULL(SUM(SUBSTRING(TOTALTIME, 1,2) * 60 + SUBSTRING(TOTALTIME, 3,2)),0)
										FROM DF_CHECKTIME_OFF WITH(NOLOCK) 
										WHERE DATE LIKE '". $date ."%' AND PRS_ID=C.PRS_ID AND TOTALTIME > '0000') AS REAL_OFF, --외출 분단위 표시
									((SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY1 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%')
									+(SELECT COUNT(SEQNO) FROM DF_CHECKTIME WHERE PAY2 = 'Y' AND PRS_ID=C.PRS_ID AND DATE LIKE '". $date ."%')) AS PAY --식대지급횟수
								FROM DF_CHECKTIME C WITH(NOLOCK) 
								WHERE PRS_ID = C.PRS_ID
							) D
						ON
							P.PRS_ID = D.PRS_ID
						$teamSQL
						GROUP BY 
							P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION, 
							D.BIZ_COMMUTE, D.LAW_COMMUTE, D.LATENESS, D.VACATION, D.COMMUTE_DATE, D.SUBVACATION1, D.SUBVACATION2, 
							D.AVGTIME1, D.AVGMINUTE1, D.AVGTIME2, D.AVGMINUTE2, 
							D.BIZ_TOTAL_TIME, D.BIZ_TOTAL_MINUTE, D.TOTAL_TIME, D.TOTAL_MINUTE, D.OVER_TIME, D.OVER_MINUTE, D.OVER_DATE, 
							D.OFF_TIME, D.OFF_MINUTE, D.BIZ_OFF_TIME, D.BIZ_OFF_MINUTE, D.REAL_OVER, D.REAL_AVG, D.REAL_OFF, D.PAY 
				) T
			) R
			WHERE
				R.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function searchType(){
		var frm = document.form;
		if("person"==frm.type.value){
			location.href = "commuting_total.php";
		}else if("team"==frm.type.value){
			location.href = "commuting_total_team.php";
		}
	}    

	function sSubmit(f)
	{
		f.target="_self";
		f.page.value = "1";
		f.sort.value = "name";
		f.action = "<?=CURRENT_URL?>";
		f.submit();
	}

	function excel_download()
	{
		var frm = document.form;
		frm.target = "hdnFrame";
		frm.action = "excel_total_month.php";
		frm.submit();
	}
	
	function chgSort(idx)
	{
		var frm = document.form;
		frm.target="_self";
		frm.page.value = "1";
		frm.sort.value = idx;
		frm.action = "<?=CURRENT_URL?>";
		frm.submit();
	}
</script>
</head>

<body>
<form method="post" name="form">
    <input type="hidden" name="page" value="<?=$page?>">
    <input type="hidden" name="sort" value="<?=$sort?>">
    <? include INC_PATH."/top_menu.php"; ?>
    <? include INC_PATH."/commuting_menu.php"; ?>
    <section class="section">
        <div class="container">
            <div class="columns is-vcentered">
                <!-- Left side -->
              <div class="card">
                <div class="column">
                    <!-- todo 0413 구조 변경 -->

                    <div class="field is-grouped">
                        <div class="control select">
                            <select name="type" onchange="javascript:searchType();">
                                <option value="person">직원별</option>
                                <option value="team" selected>부서별</option>
                            </select>
                        </div>
                        <div class="control select">
                            <select name="team" onChange="sSubmit(this.form)">
                                <option value=""<? if ($p_team == ""){ echo " selected"; } ?>>전직원</option>
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
                                    <option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                        <div class="control select">
                            <select name="year" value="<?=$p_year?>" onChange='sSubmit(this.form);'>
                                <?
                                for ($i=$startYear; $i<=($nowYear+1); $i++)
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
                            <select name="month" value="<?=$p_month?>" onChange='sSubmit(this.form);'>
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
                        <div class="control is-hidden-mobile">
                            <a href="javascript:sSubmit(this.form);" class="button is-link" id="btnSearch">
                                        <span class="icon is-small">
                                            <i class="fas fa-search"></i>
                                        </span>
                                <span>검색</span>
                            </a>
                        </div>
                    </div>
                    </div>
               </div>
                <div class="column is-hidden-mobile">
                    <div class="control has-text-right">
                        <a href="javascript:excel_download();" class="button">
                            <span class="icon is-small">
                                        <i class="fas fa-file-excel"></i>
                            </span>
                            <span>엑셀로 다운로드</span>
                        </a>
                    </div>
                </div>
              </div>

            <div class="field is-grouped">
                <div class="control is-hidden-mobile">
                    <a href="javascript:chgSort('name');" class="button is-link">
                        <span>이름순</span>
                    </a>
                </div>
                <div class="control is-hidden-mobile">
                    <a href="javascript:chgSort('position');" class="button is-link" id="btnSearch">
                        <span>직급순</span>
                    </a>
                </div>
                <div class="control is-hidden-mobile">
                    <a href="javascript:chgSort('avg');" class="button is-link">
                        <span>평균근무시간순</span>
                    </a>
                </div>
                <div class="control is-hidden-mobile">
                    <a href="javascript:chgSort('over');" class="button is-link">
                        <span>초과근무시간순</span>
                    </a>
                </div>
            </div>

                <table class="table is-fullwidth is-hoverable is-resize">
                    <colgroup>
                        <col width="4%" />
                        <col width="8%" />
                        <col width="6%" />
                        <col width="*%" />
                        <col width="6%"/>
                        <col width="6%" />
                        <col width="7%" />
                        <col width="7%" />
                        <col width="8%" />
                        <col width="8%" />
                        <col width="8%" />
                        <col width="8%" />
                        <col width="%" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="has-text-centered">no.</th>
                        <th class="has-text-centered">이름</th>
                        <th class="has-text-centered">직급</th>
                        <th class="has-text-centered">부서</th>
                        <th class="has-text-centered">정상<br>출근</th>
                        <th class="has-text-centered">휴가</th>
                        <th class="has-text-centered">반차</th>
                        <th class="has-text-centered">평균<br>출근시간</th>
                        <th class="has-text-centered">평균<br>퇴근시간</th>
                        <th class="has-text-centered">평균<br>근무시간</th>
                        <th class="has-text-centered">총<br>근무시간</th>
                        <th class="has-text-centered">총초과<Br>근무시간</th>
                        <th class="has-text-centered">초과<br>일수</th>
                    </tr>
                    </thead>
                    <!-- 일반 리스트 -->
                    <tbody class="list">
                    <?
                    $i = ($page-1)*$per_page+1;

                    while ($record = sqlsrv_fetch_array($rs))
                    {
                    $team_login = $record['PRS_LOGIN'];
                    $team_id = $record['PRS_ID'];
                    $team_name = $record['PRS_NAME'];
                    $team_team = $record['PRS_TEAM'];
                    $team_position = $record['PRS_POSITION'];

                    $biz_commute_count = $record['BIZ_COMMUTE'];	//평일정상출근
                    $lateness_count = $record['LATENESS'];			//지각
                    $vacation_count = $record['VACATION'];			//휴가
                    $commute_day = $record['COMMUTE_DATE'];			//근무일수
                    $subvacation1_count = $record['SUBVACATION1'];	//오전반차
                    $subvacation2_count = $record['SUBVACATION2'];	//오후반차
                    $avgtime1 = $record['AVGTIME1'];				//평균출근시
                    $avgminute1 = $record['AVGMINUTE1'];			//평균출근분
                    $avgtime2 = $record['AVGTIME2'];				//평균퇴근시
                    $avgminute2 = $record['AVGMINUTE2'];			//평균퇴근분
                    $avg_time = $record['AVG_TIME'];				//평균근무시간시
                    $avg_minute = $record['AVG_MINUTE'];			//평균근무시간분
                    $biz_total_time = $record['BIZ_TOTAL_TIME'];		//평일총근무시간시
                    $biz_total_minuate = $record['BIZ_TOTAL_MINUTE'];	//평일총근무시간분
                    $total_time = $record['TOTAL_TIME'];			//총근무시간시
                    $total_minute = $record['TOTAL_MINUTE'];		//총근무시간분
                    $over_time = $record['OVER_TIME'];				//초과근무시간시 - 하루 9시간 이상 근무한 내역에 대한 월 총합시간
                    $over_minute = $record['OVER_MINUTE'];			//초과근무시간분 - 하루 9시간 이상 근무한 내역에 대한 월 총합시간
                    $over_day = $record['OVER_DATE'];				//초과일수
                    $off_time = $record['OFF_TIME'];				//외출 시
                    $off_minute = $record['OFF_MINUTE'];			//외출 분
                    $biz_off_time = $record['BIZ_OFF_TIME'];		//평일외출 시
                    $biz_off_minute = $record['BIZ_OFF_MINUTE'];	//평일외출 분
                    $real_over = $record['REAL_OVER'];				//연장근무시간분단위
                    $real_avg = $record['REAL_AVG'];				//평균근무시간분단위
                    $real_off = $record['REAL_OFF'];				//평균외출시간분단위

                    $subvacation_count = $subvacation1_count + $subvacation2_count;

                    if ($avgtime1 == "") { $avgtime1 = "0"; }
                    if ($avgminute1 == "") { $avgminute1 = "0"; }
                    if ($avgtime2 == "") { $avgtime2 = "0"; }
                    if ($avgminute2 == "") { $avgminute2 = "0"; }
                    if ($avg_time == "") { $avg_time = "0"; }
                    if ($avg_minute == "") { $avg_minute = "0"; }
                    if ($biz_total_time == "") { $biz_total_time = "0"; }
                    if ($biz_total_minute == "") { $biz_total_minute = "0"; }
                    if ($total_time == "") { $total_time = "0"; }
                    if ($total_minute == "") { $total_minute = "0"; }
                    if ($over_time == "") { $over_time = "0"; }
                    if ($over_minute == "") { $over_minute = "0"; }
                    if ($off_time == "") { $off_time = "0"; }
                    if ($off_minute == "") { $off_minute = "0"; }
                    if ($biz_off_time == "") { $biz_off_time = "0"; }
                    if ($biz_off_minute == "") { $biz_off_minute = "0"; }

                    //외출시간 제외한 총 근무 시간 계산
                    if ($off_time > 0 && $off_minute > 0)
                    {
                        if ($total_minute < $off_minute)
                        {
                            $total_minute = $total_minute - $off_minute + 60;
                            $total_time = $total_time - $off_time - 1;
                        }
                        else
                        {
                            $total_minute = $total_minute - $off_minute;
                            $total_time = $total_time - $off_time - 1;
                        }
                        if ($total_time == -1)
                        {
                            $total_time = 0;
                        }
                    }
                    //

                    if (substr($real_over,0,1) == "-")
                    {
                        $flag1 = "-";
                        $real_over = substr($real_over,1,strlen($real_over));
                    }

                    $over_time = intval($real_over / 60);
                    $over_minute = $real_over % 60;

                    $over = $over_time . $over_minute;
                    $total = $total_time . $total_minute;

                    if (strlen($avgtime1) == 1) { $avgtime1 = "0".$avgtime1; }
                    if (strlen($avgminute1) == 1) { $avgminute1 = "0".$avgminute1; }
                    if (strlen($avgtime2) == 1) { $avgtime2 = "0".$avgtime2; }
                    if (strlen($avgminute2) == 1) { $avgminute2 = "0".$avgminute2; }
                    if (strlen($avg_time) == 1) { $avg_time = "0".$avg_time; }
                    if (strlen($avg_minute) == 1) { $avg_minute = "0".$avg_minute; }
                    if (strlen($total_time) == 1) { $total_time = "0".$total_time; }
                    if (strlen($total_minute) == 1) { $total_minute = "0".$total_minute; }
                    if (strlen($over_time) == 1) { $over_time = "0".$over_time; }
                    if (strlen($over_minute) == 1) { $over_minute = "0".$over_minute; }
                    ?>
                        <tr>
                            <td class="has-text-centered"><?=$i?></td>
                            <td class="has-text-centered"><?=$team_name?></td>
                            <td class="has-text-centered"><?=$team_position?></td>
                            <td class="has-text-centered"><?=$team_team?></td>
                            <td class="has-text-centered"><?=$biz_commute_count?></td>
                            <td class="has-text-centered"><?=$vacation_count?></td>
                            <td class="has-text-centered"><?=$subvacation_count?></td>
                            <td class="has-text-centered"><?=$avgtime1?> : <?=$avgminute1?></td>
                            <td class="has-text-centered"><?=$avgtime2?> : <?=$avgminute2?></td>
                            <td class="has-text-centered"><?=$avg_time?> : <?=$avg_minute?></td>
                            <td class="has-text-centered"><?=$total_time?> : <?=$total_minute?></td>
                            <td class="has-text-centered"><?=$over_time?> : <?=$over_minute?></td>
                            <td class="has-text-centered"><?=$over_day?></td>
                        </tr>
                <?
                        $i++;
                    }
                ?>
                    </tbody>
                </table>
            <!--페이징처리-->
            <nav class="pagination" role="navigation" aria-label="pagination">
                <?=getPaging($total_cnt,$page,$per_page);?>
                </ul>
            </nav>
            <!--페이징처리-->
        </div>
    </section>
</form>
<? include INC_PATH."/bottom.php"; ?>
</body>
</html>
