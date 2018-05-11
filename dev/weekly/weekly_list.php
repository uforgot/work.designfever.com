<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
	require_once CMN_PATH."/weekly_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y'); 
	$weekday = date('w'); 
	$z_day = date('z'); 

	//if ($_REQUEST['year'] == "" && $z_day < 5 && $weekday < 5) { $year = $year - 1; }

	$searchSQL = " WHERE PRS_ID = '$prs_id' AND WEEK_ORD LIKE '$year%'";

	$sql = "SELECT COUNT(SEQNO) FROM DF_WEEKLY WITH(NOLOCK) ". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 12;

	$sql = "SELECT 
				T.SEQNO, T.WEEK_ORD, T.WEEK_ORD_TOT, T.WEEK_AREA, T.TITLE, T.COMPLETE_YN, T.REG_DATE, T.PRS_ID
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) AS ROWNUM, 
					SEQNO, WEEK_ORD, WEEK_ORD_TOT, WEEK_AREA , TITLE, COMPLETE_YN, REG_DATE, PRS_ID
				FROM 
					DF_WEEKLY WITH(NOLOCK)
				$searchSQL
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function yearSearch(val) {
		document.location.href = "./weekly_list.php?year=" + val;
	}
</script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>
<form name="form" method="post">
<form name="form" id="form" method="post">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="year" id="year" value="<?=$year?>">
<? include INC_PATH."/weekly_menu.php"; ?>
            <!-- ���� ���� -->
            <section class="section df-weekly">
                <div class="container">
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
                    </div>
                    <div class="content">
                        <table class="table is-fullwidth is-hoverable is-resize">
                            <colgroup>
                                <col width="8%">
                                <col width="20%">
                                <col width="*%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                            <tr>
                                <th><span class="is-hidden-mobile">����</span></th>
                                <th>�Ⱓ</th>
                                <th class="has-text-centered">����</th>
                                <th class="has-text-centered"></th>
                            </tr>
                            </thead>
                            <!-- �Ϲ� ����Ʈ -->
                            <tbody class="list">
                            <?
                            $i = $total_cnt-($page-1)*$per_page;
                            if ($i==0)
                            {?>
                            <tr>
                                <td colspan="6" class="has-text-centered">�ش� ������ �����ϴ�.</td>
                            </tr>
                                <?
                            }
                            else
                            {
                            while ($record = sqlsrv_fetch_array($rs))
                            {
                            $seqno = $record['SEQNO'];
                            $ord = $record['WEEK_ORD'];
                            $ord_tot = $record['WEEK_ORD_TOT'];
                            $week_area = $record['WEEK_AREA'];
                            $title = $record['TITLE'];
                            $reg_date = $record['REG_DATE'];
                            $complete_yn = $record['COMPLETE_YN'];

                            $prs_id = $record['PRS_ID'];

                            //���� ������ ���
                            if($ord == $winfo["cur_week"]) {
                                $link = "weekly_write.php";
                                //���� ������ ���
                            } else {
                                $link = "weekly_view.php";
                            }

                            if($complete_yn == 'Y')
                            {
                                $write_btn = "";
                                $write_txt = "<a class='button' href='./$link?type=modify&seqno=$seqno&prs_id=$prs_id&page=$page'>". $title ."</a>";
                                //$write_txt = "<a class='button' href='./$link?type=modify&seqno=$seqno&page=$page'>". $title ."</a>";
                            }
                            else
                            {
                                $link = "weekly_write.php";

                                if(!$reg_date)
                                {
                                    $write_btn = "<a class='button is-info' href='./$link?type=write&seqno=$seqno&prs_id=$prs_id&page=$page'><span class='icon is-small'><i class='fas fa-pencil-alt'></i></span><span>�ۼ�</span></a>";
                                    $write_txt = "<a class='button' href='./$link?type=write&seqno=$seqno&prs_id=$prs_id&page=$page'>". $title ."</a>";
                                    //$write_btn = "<a class='button is-info' href='./$link?type=write&seqno=$seqno&page=$page'><span class='icon is-small'><i class='fas fa-pencil-alt'></i></span><span>�ۼ�</span></a>";
                                    //$write_txt = "<a class='button' href='./$link?type=write&seqno=$seqno&page=$page'>". $title ."</a>";
                                }
                                else
                                {
                                    $write_btn = "<a class='button is-danger' href='./$link?type=modify&seqno=$seqno&prs_id=$prs_id&page=$page'><span class='icon is-small'><i class='fas fa-pencil-alt'></i></span><span>����</span></a>";
                                    $write_txt = "<a class='button' href='./$link?type=modify&seqno=$seqno&prs_id=$prs_id&page=$page'>". $title ."</a>";
                                    //$write_btn = "<a class='button is-danger' href='./$link?type=modify&seqno=$seqno&page=$page'><span class='icon is-small'><i class='fas fa-pencil-alt'></i></span><span>����</span></a>";
                                    //$write_txt = "<a class='button' href='./$link?type=modify&seqno=$seqno&page=$page'>". $title ."</a>";
                                }
                            }
                            ?>
                            <tr>
                                <td><?=$ord_tot?></td>
                                <td>
                                    <?=$week_area?>
                                </td>
                                <td class="has-text-centered"><?=$write_txt?></td>
                                <td class="has-text-centered  is-hidden-mobile">
                                    <?=$write_btn?>
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

