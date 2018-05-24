<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
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

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y"); 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date("m"); 
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

	$date = $p_year ."-". $p_month;

	$sql = "SELECT SORT, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
	$rs = sqlsrv_query($dbConn,$sql);

	$i = 1;
	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN P.PRS_TEAM='". $record['TEAM'] ."' THEN ". $i ." ";
		$i++;
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN P.PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, P.PRS_NAME";

	if ($p_team != "")
	{
		$teamSQL = " AND P.PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team'))))";
	}

	$sql = "SELECT 
				COUNT(DISTINCT PRS_ID) 
			FROM 
				DF_PERSON P WITH(NOLOCK)
			WHERE 
				PRF_ID IN (1,2,3,4,5) $teamSQL AND PRS_ID NOT IN (15,22,24,87,102,148)";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 20;

	$sql = "SELECT 
				T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
					P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION
				FROM 
					DF_PERSON P WITH(NOLOCK)
				WHERE 
					P.PRF_ID IN (1,2,3,4,5) $teamSQL AND P.PRS_ID NOT IN (15,22,24,87,102,148)
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn, $sql);
	while ($record=sqlsrv_fetch_array($rs))
	{
		$arr_id .= $record['PRS_ID'] ."##";
		$arr_name .= $record['PRS_NAME'] ."##";
		$arr_team .= $record['PRS_TEAM'] ."##";
		$arr_position .= $record['PRS_POSITION'] ."##";
	}

	$id_ex = explode("##",$arr_id);
	$name_ex = explode("##",$arr_name);
	$team_ex = explode("##",$arr_team);
	$position_ex = explode("##",$arr_position);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function sSubmit(f)
	{
		f.target="_self";
		f.page.value = "1";
		f.action = "<?=CURRENT_URL?>";
		f.submit();
	}

	function excel_download()
	{
		var frm = document.form;
		frm.target = "hdnFrame";
		frm.action = "excel_pay.php";
		frm.submit();
	}
</script>
</head>

<body>
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
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
                                for ($i=$startYear; $i<=($nowYear); $i++)
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

                                    echo "<option value='".$j."'".$selected.">".$i."월</option>";
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

            <table class="table is-fullwidth is-hoverable is-resize">
                <colgroup>
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="*" />
                    <col width="10%"/>
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                    <col width="10%" />
                </colgroup>
                <thead>
                <tr>
                    <th class="has-text-centered">no.</th>
                    <th class="has-text-centered">이름</th>
                    <th class="has-text-centered">직급</th>
                    <th class="has-text-centered">부서</th>
                    <th class="has-text-centered">점심식비</th>
                    <th class="has-text-centered">저녁식비</th>
                    <th class="has-text-centered">간식비</th>
                    <th class="has-text-centered">파견교통비</th>
                    <th class="has-text-centered">수당 합계</th>
                </tr>
                </thead>
                <!-- 일반 리스트 -->
                <tbody class="list">
                <?
                $no = $total_cnt-($page-1)*$per_page;

                for ($i=0; $i<sizeof($id_ex); $i++)
                {
                if ($id_ex[$i] != "")
                {
                $sql = "SELECT 
						T.PAY1, T.PAY2, T.PAY3, T.PAY4, T.PAY5, T.PAY6
					FROM 
					(
						SELECT
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY1 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY1, --점심식비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY2 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY2, --저녁식비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY3 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY3, --간식비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY4 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY4, --교통비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY5 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY5, --파견교통비(출근)
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY6 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY6 --파견교통비(퇴근)
						FROM 
							DF_PERSON P WITH(NOLOCK)
						WHERE
							P.PRS_ID = $id_ex[$i]
					) T";
                $rs = sqlsrv_query($dbConn, $sql);

                $record = sqlsrv_fetch_array($rs);
                if (sizeof($record) > 0)
                {
                    $pay1 = $record['PAY1'];	//점심식비
                    $pay2 = $record['PAY2'];	//저녁식비
                    $pay3 = $record['PAY3'];	//간식비
                    $pay4 = $record['PAY4'];	//교통비
                    $pay5 = $record['PAY5'];	//파견교통비(출근)
                    $pay6 = $record['PAY6'];	//파견교통비(퇴근)

                    $pay_t = $pay5 + $pay6;

                    if ($pay1+$pay2+$pay3+$pay5+$pay6 > 0)
                    {
                        $pay_total = "\\". number_format($pay1*6000+$pay2*6000+$pay3*3000+$pay5*2000+$pay6*2000);
                    }
                    else
                    {
                        $pay_total = "";
                    }
                    ?>
                    <tr>
                        <td class="has-text-centered"><?=$no?></td>
                        <td class="has-text-centered"><?=$name_ex[$i]?></td>
                        <td class="has-text-centered"><?=$position_ex[$i]?></td>
                        <td class="has-text-centered"><?=$team_ex[$i]?></td>
                        <td class="has-text-centered"><?=$pay1?></td>
                        <td class="has-text-centered"><?=$pay2?></td>
                        <td class="has-text-centered"><?=$pay3?></td>
                        <td class="has-text-centered"><?=$pay5+$pay6?></td>
                        <td class="has-text-centered">
                            <?
                            if ($pay1+$pay2+$pay3+$pay5+$pay6 > 0)
                            {
                                //echo "\\". number_format($pay1*6000+$pay2*6000+$pay3*3000);
                                echo "\\". number_format($pay1*6000+$pay2*6000+$pay3*3000+$pay5*2000+$pay6*2000);
                            }
                            ?>
                        </td>
                    </tr>
                    <?
                }
                }
                    $no--;
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
