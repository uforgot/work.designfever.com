<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //��ٽð� ����� ���� �߰�(��������� ���� �����ҵ�) ksyang
?>

<?
	//���� üũ
	if ($prf_id == "6") 
	{ 
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("Ż��ȸ�� �̿�Ұ� �������Դϴ�.");
		location.href="../main.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "modify";  
	$seqno = isset($_REQUEST['seqno']) ? $_REQUEST['seqno'] : null;  
	$win = isset($_REQUEST['win']) ? $_REQUEST['win'] : null;  

	//�ؽ�Ʈ ����
	if ($type == "modify")	
	{
		$type_title1 = "��ȸ/����";
		$type_title2 = "����";
	}
	
	//���� �̸��� ������ ������ ��ȸ ����
	if (in_array($prs_id,$weekly_arr))
	{
		$searchSQL = " WHERE SEQNO = '$seqno'";								
	}
	else
	{
		$searchSQL = " WHERE SEQNO = '$seqno' AND PRS_ID = '$prs_id'";
	}

	//�ְ����� �⺻������ ����
	$sql = "SELECT 
				WEEK_ORD, WEEK_AREA, TITLE, MEMO, PRS_ID, PRS_NAME, PRS_POSITION, COMPLETE_YN, PRS_TEAM
			FROM 
				DF_WEEKLY WITH(NOLOCK)
			$searchSQL";								
	$rs = sqlsrv_query($dbConn,$sql);
	$record = sqlsrv_fetch_array($rs);

	if (!$seqno || !$record)
	{
?>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<script type="text/javascript">
		alert("�ش� ���� �������� �ʽ��ϴ�.");
		history.back();
	</script>
<?
		exit;
	} else {
		$weekly_ord = $record['WEEK_ORD'];
		$weekly_str = $record['WEEK_AREA'];
		$weekly_title = $record['TITLE'];
		$weekly_memo = $record['MEMO'];
		$weekly_prs_id = $record['PRS_ID'];
		$weekly_prs_nm = $record['PRS_NAME'];
		$weekly_prs_pos = $record['PRS_POSITION'];
		$weekly_complete_yn = $record['COMPLETE_YN'];							//����Ϸ� ����
		$weekly_edit_yn = ($weekly_prs_id == $prs_id) ? "Y" : "N";				//�����ۼ� ����
        $weekly_prs_team = $record['PRS_TEAM'];


		//����������Ʈ ����Ʈ ����
		$searchSQL = " WHERE A.SEQNO = '$seqno' AND A.PRS_ID = '$weekly_prs_id' AND B.PROJECT_NO <> 'DF0000_ETC'";

		$sql = "SELECT 
					A.SEQNO, B.PROJECT_NO,
					(SELECT TITLE FROM DF_PROJECT WHERE PROJECT_NO = B.PROJECT_NO) TITLE,
					(SELECT TOP 1 PART FROM DF_PROJECT_DETAIL WHERE PROJECT_NO = B.PROJECT_NO AND PRS_ID = A.PRS_ID) PART
				FROM 
					DF_WEEKLY A WITH(NOLOCK) 
					INNER JOIN DF_WEEKLY_DETAIL B WITH(NOLOCK) 
					ON A.SEQNO = B.WEEKLY_NO
				$searchSQL
				ORDER BY
					B.PROJECT_NO DESC";
		$rs = sqlsrv_query($dbConn,$sql);
	}
?>

<? include INC_PATH."/top.php"; ?>

<script src='../js/jquery.autosize.min.js'></script>

<script type="text/JavaScript">
	function weeklyWrite()
	{
		var frm = document.form;

		var cntProject = frm['project_no[]'].length - 1;
		var totProgThis = 0;
		var totProgNext = 0;
		var chkProgThis = -1;
		var chkProgNext = -1;

		for(i=0;i<cntProject;i++) {
			var tmpProgThis = parseInt(frm['progress_this[]'][i].value);
			var tmpProgNext = parseInt(frm['progress_next[]'][i].value);

			totProgThis = totProgThis + tmpProgThis;
			totProgNext = totProgNext + tmpProgNext;

			if(chkProgThis < 0 && (tmpProgThis > 0 && !frm['content_this[]'][i].value)) {
				chkProgThis = i;
			}
			if(chkProgNext < 0 && (tmpProgNext > 0 && !frm['content_next[]'][i].value)) {
				chkProgNext = i;
			}
		}

		if(totProgThis != 100) {
			alert("���� ��������� �������� ���� 100%�� �ƴմϴ�.");
			frm['progress_this[]'][0].focus();
			return;    	
		}

		if(totProgNext != 100) {
			alert("���� ��������� �������� ���� 100%�� �ƴմϴ�.");
			frm['progress_next[]'][0].focus();
			return;    	
		}

		if(chkProgThis >= 0) {
			alert("���������� �´� ���� ��������� �ۼ��� �ּ���.");
			frm['progress_this[]'][chkProgThis].focus();
			return;    				
		}

		if(chkProgNext >= 0) {
			alert("���������� �´� ���� ��������� �ۼ��� �ּ���.");
			frm['progress_next[]'][chkProgNext].focus();
			return;    				
		}

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("������ <?=$type_title2?> �Ͻðڽ��ϱ�")){
			frm.target = "hdnFrame";
			frm.action = 'weekly_write_act.php'; 
			frm.submit();
		}
	}

	function weeklyComplete(type) {
		var frm = document.form;
		var str = '';

		if(type == 'complete') str = "�Ϸ�";
		else if(type == 'cancel') str = "���";

		//���� ��ȿ�� �˻� �� �κ�
		if(confirm("�� �ְ����� �ۼ��� " + str + " �Ͻðڽ��ϱ�")){
			frm.target = "hdnFrame";
			frm.type.value = type;
			frm.action = 'weekly_write_act.php?ret=view'; 
			frm.submit();
		}
	}

	$(function(){
		$('.normal').autosize();
		//$('.animated').autosize();
	});
</script>
</head>

<body>

<form method="post" name="form" action="weekly_write_act.php">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="type" value="<?=$type?>">			<!-- ��ϼ����������� -->
<input type="hidden" name="seqno" value="<?=$seqno?>">			<!-- �۹�ȣ -->
<input type="hidden" name="order" value="<?=$weekly_ord?>">		<!-- �������� -->
<input type="hidden" name="win" value="<?=$win?>">				<!-- ��â���¿��� -->
    <? include INC_PATH . "/top_menu.php"; ?>
			<? include INC_PATH."/weekly_menu.php"; ?>

    <!-- ���� ���� -->
    <section class="section df-weekly">
        <div class="container">
            <nav class="level is-mobile">
                <div class="level-left">
                    <p class="buttons">
                        <a href="/weekly/weekly_list.php?page=<?= $page ?>" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                            <span>���</span>
                        </a>
                    </p>
                </div>


                <div class="level-right">
                    <p class="buttons">
                        <? if ($weekly_complete_yn != 'Y' && $weekly_edit_yn == 'Y') { ?>
                            <a href="javascript:weeklyWrite();" class="button is-info" id="btnWrite">
                                <span class="icon is-small">
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                                <span>�ۼ�</span>
                            </a>
                        <? } ?>
                        <? if ($win == 'new') { ?>
                            <a href="javascript:window.close();" class="button is-danger" id="btnCancel">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>���</span>
                            </a>
                        <? } else { ?>
                            <a href="./weekly_list.php?page=<?= $page ?>" class="button is-danger" id="btnCancel">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>���</span>
                            </a>
                        <? } ?>
                    </p>
                </div>
            </nav>

            <div class="content">
                <div class="calendar is-large">
                    <div class="calendar-nav">
                        <div class="calendar-nav-previous-month">
                        </div>
                        <div>
                                <span class="title is-size-5 has-text-white">(<?= $weekly_str ?>
                                    ) <?= $weekly_title ?> <?= $type_title1 ?></span><br>
                                <span class="title is-7 has-text-white"><?= $weekly_prs_team ?>
                                    - <?= $weekly_prs_nm ?></span>
                        </div>
                        <div class="calendar-nav-next-month">
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <p class="is-size-7">
                    - ���� ���� ������Ʈ�� ���� ���, ���� ���� ������Ʈ���� ���Ұ� �������� ����� �� �ְ� ������ �ۼ��� �ּ���.<br>
                    - ������Ʈ �� �������� ���� 100%�Դϴ�.<br>
                    - �� �ְ����� �ۼ��ϷḦ �� ��쿡�� �������� �ְ������ ������ �� �����ϴ�.<br>
                </p>
            </div>

            <!-- ������Ʈ ����Ʈ ���� -->
            <?
            $cnt = 0;
            while ($record = sqlsrv_fetch_array($rs)) {
                $project_no = $record['PROJECT_NO'];
                $title = $record['TITLE'];
                $part = $record['PART'];

                //�ְ����� ����, ����
                if ($type == "modify") {
                    $searchSQL1 = " WHERE WEEKLY_NO = '$seqno' AND PROJECT_NO = '$project_no'";

                    $sql1 = "SELECT
							THIS_WEEK_CONTENT, NEXT_WEEK_CONTENT, THIS_WEEK_RATIO, NEXT_WEEK_RATIO
						FROM
							DF_WEEKLY_DETAIL WITH(NOLOCK)
						$searchSQL1";
                    $rs1 = sqlsrv_query($dbConn, $sql1);

                    $record1 = sqlsrv_fetch_array($rs1);
                    if (sqlsrv_has_rows($rs1) > 0) {
                        $this_week_content = $record1['THIS_WEEK_CONTENT'];
                        $next_week_content = $record1['NEXT_WEEK_CONTENT'];
                        $this_week_ratio = $record1['THIS_WEEK_RATIO'];
                        $next_week_ratio = $record1['NEXT_WEEK_RATIO'];
                    } else {
                        $this_week_content = "";
                        $next_week_content = "";
                        $this_week_ratio = "";
                        $next_week_ratio = "";
                    }
                }
                ?>

                <!-- weekly routine ���� -->
                <div class="content">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-title">[<?= $project_no ?>] <?= $title ?> / <?= $part ?></div>
                            <input type="hidden" name="project_no[]" value="<?= $project_no ?>">
                        </div>
                        <div class="card-content">

                            <div class="columns">
                                <div class="column">
                                    <div class="columns is-mobile">
                                        <div class="column">
                                            ���� ���� ����
                                        </div>
                                        <div class="column last-button">
                                            <div class="field">
                                                <div class="control select">
                                                    <select name="progress_this[]" class="percentage">
                                                        <?
                                                        for ($i = 0; $i <= 100; $i = $i + 5) {
                                                            if ($i == $this_week_ratio) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }
                                                            echo "<option value='" . $i . "'" . $selected . ">" . $i . "%</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="control">
                                                <textarea class="textarea" placeholder="" name="content_this[]" rows="10"><?= $this_week_content ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="columns is-mobile">
                                        <div class="column">
                                            ���� ���� ����
                                        </div>
                                        <div class="column last-button">
                                            <div class="field">
                                                <div class="control select">
                                                    <select name="progress_next[]" class="percentage">
                                                        <?
                                                        for ($i = 0; $i <= 100; $i = $i + 5) {
                                                            if ($i == $next_week_ratio) {
                                                                $selected = " selected";
                                                            } else {
                                                                $selected = "";
                                                            }
                                                            echo "<option value='" . $i . "'" . $selected . ">" . $i . "%</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="control">
                                                <textarea class="textarea" name="content_next[]" placeholder="" rows="10"><?= $next_week_content ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?
                $cnt++;
            }
            ?>
            <!-- ������Ʈ ����Ʈ ���� -->

            <!-- ��Ÿ���� �׸� ���� -->
            <?
            $project_no_etc = "DF0000_ETC"; //��Ÿ������ �Ҵ��� ������Ʈ �ڵ�

            //�ְ����� ����, ����
            if ($type == "modify") {
                $searchSQL1 = " WHERE WEEKLY_NO = '$seqno' AND PROJECT_NO = '$project_no_etc'";

                $sql1 = "SELECT
						THIS_WEEK_CONTENT, NEXT_WEEK_CONTENT, THIS_WEEK_RATIO, NEXT_WEEK_RATIO
					FROM
						DF_WEEKLY_DETAIL WITH(NOLOCK)
					$searchSQL1";
                $rs1 = sqlsrv_query($dbConn, $sql1);

                $record1 = sqlsrv_fetch_array($rs1);
                if (sqlsrv_has_rows($rs1) > 0) {
                    $this_week_content = $record1['THIS_WEEK_CONTENT'];
                    $next_week_content = $record1['NEXT_WEEK_CONTENT'];
                    $this_week_ratio = $record1['THIS_WEEK_RATIO'];
                    $next_week_ratio = $record1['NEXT_WEEK_RATIO'];
                }
            }
            ?>
            <!-- ��Ÿ���� ���� -->
            <div class="content">
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-title">��Ÿ����(�濵������, ȫ����, ��Ÿ ����)</div>
                        <input type="hidden" name="project_no[]" value="DF0000_ETC">
                    </div>
                    <div class="card-content">

                        <div class="columns">
                            <div class="column">
                                <div class="columns is-mobile">
                                    <div class="column">
                                        ���� ���� ����
                                    </div>
                                    <div class="column last-button">
                                        <div class="field">
                                            <div class="control select">
                                                <select name="progress_this[]" class="percentage">
                                                    <?
                                                    for ($i = 0; $i <= 100; $i = $i + 5) {
                                                        if ($i == $this_week_ratio) {
                                                            $selected = " selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                        echo "<option value='" . $i . "'" . $selected . ">" . $i . "%</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                            <textarea class="textarea" placeholder="" name="content_this[]"
                                                      rows="10"><?= $this_week_content ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="columns is-mobile">
                                    <div class="column">
                                        ���� ���� ����
                                    </div>
                                    <div class="column last-button">
                                        <div class="field">
                                            <div class="control select">
                                                <select name="progress_next[]" class="percentage">
                                                    <?
                                                    for ($i = 0; $i <= 100; $i = $i + 5) {
                                                        if ($i == $next_week_ratio) {
                                                            $selected = " selected";
                                                        } else {
                                                            $selected = "";
                                                        }
                                                        echo "<option value='" . $i . "'" . $selected . ">" . $i . "%</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                            <textarea class="textarea" name="content_next[]" placeholder=""
                                                      rows="10"><?= $next_week_content ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- �ʵ�迭 ó������ ���� �±� -->
                <input type="hidden" name="project_no[]">
                <input type="hidden" name="progress_this[]">
                <input type="hidden" name="progress_next[]">
                <input type="hidden" name="content_this[]">
                <input type="hidden" name="content_next[]">
            </div>
            <!-- ��Ÿ���� �׸� ���� -->

            <!-- (����)���ǻ��� �׸� ���� -->
            <?
            if ($weekly_prs_pos == '����') {
                ?>

                <div class="content">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-title">���� �� ��Ÿ����</div>
                        </div>
                        <div class="card-content">
                            <div class="columns">
                                <div class="column">
                                    <div class="columns is-mobile">
                                        <div class="column last-button">
                                            <div class="field">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="control">
                                            <textarea class="textarea" placeholder="" name="memo" rows="10"><?= $weekly_memo ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?
            }
            ?>
            <!-- (����)���ǻ��� �׸� ���� -->

            <hr class="hr-strong">

            <nav class="level is-mobile">
                <div class="level-left">
                    <p class="buttons">
                        <a href="/weekly/weekly_list.php?page=<?= $page ?>" class="button">
                        <span class="icon is-small">
                            <i class="fas fa-bars"></i>
                        </span>
                            <span>���</span>
                        </a>
                    </p>
                </div>

                <div class="level-right">
                    <p class="buttons">
                        <? if ($weekly_complete_yn != 'Y' && $weekly_edit_yn == 'Y') { ?>
                            <a href="javascript:weeklyWrite();" class="button is-info" id="btnWrite">
                                <span class="icon is-small">
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                                <span>�ۼ�</span>
                            </a>
                        <? } ?>
                        <? if ($win == 'new') { ?>
                            <a href="javascript:window.close();" class="button is-danger" id="btnCancel">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>���</span>
                            </a>
                        <? } else { ?>
                            <a href="./weekly_list.php?page=<?= $page ?>" class="button is-danger" id="btnCancel">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span>���</span>
                            </a>
                        <? } ?>
                    </p>

                </div>
            </nav>

        </div>
    </section>
    <!-- ���� �� -->

    <? include INC_PATH . "/bottom.php"; ?>
    </div>
</form>
</body>
</html>
