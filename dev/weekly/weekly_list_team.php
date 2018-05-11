<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/weekly_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
?>

<?
	if ($prf_id != "2" && $prf_id != "3" && $prf_id != "4")
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("��/���� �̻� ���� ���� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	//������ ����Ʈ�ڽ� ����
	if (in_array($prs_position,$positionS_arr))
	{
		if (in_array($prs_team,array('CEO')))
		{
			$cur_team = "�濵������";
		}
		else
		{
			$cur_team = $prs_team;
		}
		$sel_view = 'Y';
	}
	else
	{
		$cur_team = $prs_team; //����Ʈ�ڽ� �⺻����
		$sel_view = 'N';	   //����Ʈ�ڽ� ���⿩��
	}
	
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y'); 
	$team = isset($_REQUEST['team']) ? $_REQUEST['team'] : $cur_team; 

	$searchSQL = " WEEK_ORD LIKE '$year%' AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team'))))";

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby .= "WHEN PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby . " END, PRS_NAME";

	$sql = "SELECT 
				COUNT(DISTINCT WEEK_ORD) 
			FROM 
				DF_WEEKLY WITH(NOLOCK) 
			WHERE". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 30;

	$sql = "SELECT 
				T.WEEK_ORD, T.WEEK_ORD_TOT, T.TITLE, T.COMPLETE_YN
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) AS ROWNUM, 
					WEEK_ORD, WEEK_ORD_TOT, TITLE, COMPLETE_YN
				FROM 
					DF_WEEKLY WITH(NOLOCK)
				WHERE". $searchSQL." 
				GROUP BY
					WEEK_ORD, WEEK_ORD_TOT, TITLE, COMPLETE_YN
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";		
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function yearSearch(val) {
		document.location.href = "./weekly_list_team.php?year=" + val + "&team=<?=$team?>";
	}

	function teamSearch(val) {
		if (!val)
		{
			alert('���� ��ȸ�� ������ �����մϴ�.\n������ ������ �ּ���!');
			return;
		}
		
		document.location.href = "./weekly_list_team.php?year=<?=$year?>&team=" + val;
	}

	function weeklyComplete(type,ord) {
		var frm = document.form;
		var str = '';

		if(type == 'complete') str = "�Ϸ�";
		else if(type == 'cancel') str = "���";

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("�� �ְ����� �ۼ��� " + str + " �Ͻðڽ��ϱ�")){
			frm.target = "hdnFrame";
			frm.action = 'weekly_write_act.php?type='+type+'&order='+ord; 
			frm.submit();
		}
	}
</script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>
<form name="form" method="post">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="year" id="year" value="<?=$year?>">
<input type="hidden" name="team" id="team" value="<?=$team?>">
<? include INC_PATH."/weekly_menu.php"; ?>

    <section class="section df-weekly">
        <div class="container">
            <div class="content">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <!-- Left side -->
                            <div class="column last-button">
                                <div class="field">
                                    <div class="control select">
                                        <select name="year" onchange="javascript:yearSearch(this.value);">
                                            <?
                                            for ($i=2014; $i<=date("Y"); $i++)
                                            {
                                                if ($i == $year)
                                                {
                                                    $selected = " selected";
                                                }
                                                else
                                                {
                                                    $selected = "";
                                                }
                                                ?>
                                                <option value="<?=$i?>" <?=$selected?>><?=$i?>��</option>
                                                <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Right side -->
                            <div class="column">
                                <div class="field">
                                    <div class="control select">
                                        <?
                                        if ($sel_view == 'Y')
                                        {
                                            ?>
                                        <select name="team" onchange="javascript:teamSearch(this.value);">
                                            <option>�μ�����</option>
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
                                                    $selTeam2 = "&nbsp;&nbsp;�� ". $selTeam;
                                                }
                                                ?>
                                                <option value="<?=$selTeam?>"<? if ($team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
                                                <?
                                            }
                                            ?>
                                        </select>
                                        <?
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="columns is-hidden-mobile">
                    <!-- Left side -->
                    <div class="column">
                        <p class="is-size-7">
                            * �ְ����� �ۼ����� ���� ������ ��Ͽ� ��Ÿ���� �ʽ��ϴ�.
                        </p>
                    </div>
                    <!-- Right side -->
                    <div class="column">

                    </div>
                </div>
                <hr>
            </div>


            <div class="content">
                <table class="table is-fullwidth is-hoverable is-resize">
                    <colgroup>
                        <col width="8%">
                        <col width="*">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th><span class="is-hidden-mobile">����</span></th>
                        <th>�ְ�����</th>
                        <th class="has-text-centered">����</th>
                        <th class="has-text-centered"></th>
                    </tr>
                    </thead>
                    <!-- �Ϲ� ����Ʈ -->
                    <tbody class="list">
                    <?
                    $i = $total_cnt-($page-1)*$per_page;
                    if ($i==0)
                    {
                        ?>
                        <tr>
                            <td colspan="6" class="has-text-centered">�ش� ������ �����ϴ�.</td>
                        </tr>
                        <?
                    }
                    else
                    {
                    while ($record = sqlsrv_fetch_array($rs))
                    {
                    $ord_tot = $record['WEEK_ORD_TOT'];
                    $ord = $record['WEEK_ORD'];
                    $comp_yn = $record['COMPLETE_YN'];

                    //����� �̻�, ���� ���� ��ũ
                    $title = "<a class='has-text-grey-light' href='weekly_list_division.php?week=".$ord."&team=".$team."' target='_blank'>".$record['TITLE']."</a>";

                    if ($comp_yn == 'Y')		$state = "�Ϸ�";
                    else if ($comp_yn == 'N')	$state = "�ۼ���";

                    //�ְ����� ����� ���� ����
                    $searchSQL = " WHERE WEEK_ORD = '$ord' AND REG_DATE IS NOT NULL AND PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$team'))))";
                    //$searchSQL = " WHERE WEEK_ORD = '$ord' AND PRS_TEAM = '$team'";

                    $per_sql = "SELECT 
							SEQNO, PRS_NAME ,PRS_ID
					   FROM 
							DF_WEEKLY WITH(NOLOCK)
					   $searchSQL
					   $orderbycase";
                    $per_rs = sqlsrv_query($dbConn,$per_sql);

                    $per_list = "";
                    while ($per_record = sqlsrv_fetch_array($per_rs))
                    {
                        $per_seqno = $per_record['SEQNO'];
                        $per_name = $per_record['PRS_NAME'];
                        $per_id = $per_record['PRS_ID'];
                        $per_list .= "<a href='weekly_write.php?type=modify&prs_id=$per_id&seqno=$per_seqno&win=new' target='_blank'>".$per_name."  </a>";
                    }
                    ?>

                    <tr>
                        <td><?=$ord_tot?></td>
                        <td>
                            <div class="columns is-mobile">
                                <div class="column">
                                    <span class="is-size-7"><?=$title?></span><br>
                                    <span class="has-text-link"><?=$per_list?></span>
                                </div>
                                <div class="column last-button is-hidden-tablet">
                                    <div class="button is-info">�Ϸ�</div>
                                </div>
                            </div>
                        </td>
                        <td class="has-text-centered"><?=$state?></td>
                        <td class="has-text-centered  is-hidden-mobile">
                            <?
                            $cur_date = date("Y-m-d");
                            $ndate = date("Y-m-d");
                            $ydate = date("Y-m-d", strtotime("$cur_date -7 day"));

                            $ninfo = getWeekInfo($ndate);
                            $yinfo = getWeekInfo($ydate);

                            //								if (in_array($prs_id,$weekly_arr))
                            if ($ord == $ninfo["cur_week"] || $ord == $yinfo["cur_week"])
                            {
                                if ($comp_yn == 'Y')
                                {
                                    ?>
                                    <a class='button is-danger' href="javascript:weeklyComplete('cancel','<?=$ord?>');">�� �ְ����� �Ϸ� ���</a>
                                    <?
                                }
                                else
                                {
                                    ?>
                                    <a class='button is-info' href="javascript:weeklyComplete('complete','<?=$ord?>');">&nbsp;&nbsp;&nbsp;&nbsp;�� �ְ����� �Ϸ�&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                    <?
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <?
                        $i--;
                    }
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <!--����¡ó��-->
            <nav class="pagination" role="navigation" aria-label="pagination">
                <?=getPaging($total_cnt,$page,$per_page);?>
                </ul>
            </nav>
            <!--����¡ó��-->
        </div>
    </section>
    <!-- ���� �� -->

    <? include INC_PATH."/bottom.php"; ?>
</form>
</body>
</html>