<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "TOTAL"; 

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

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
	$p_to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : $nowDay; 
	if (strlen($p_to_day) == 1) { $p_to_day = "0". $p_to_day; }

	$p_fr_date = $p_fr_year ."-". $p_fr_month ."-". $p_fr_day;
	$p_to_date = $p_to_year ."-". $p_to_month ."-". $p_to_day;

	$searchSQL = " WHERE A.USE_YN = 'Y' AND '$p_fr_date' <= CONVERT(char(10),A.END_DATE,120) AND '$p_to_date' >= CONVERT(char(10),A.START_DATE,120)";

	if ($status != "ALL")
	{
		$searchSQL .= " AND A.STATUS = '$p_status'";
	}
	if ($name != "")
	{
		$searchSQL .= " AND B.PRS_NAME = '$p_name'";
	}

	$sql = "SELECT 
				COUNT(*)
			FROM 
			(
				SELECT 
					B.PRS_ID, B.PRS_POSITION, B.PRS_NAME, B.PART, B.PART_RATE,
					A.PROJECT_NO, A.TITLE, CONVERT(char(10),A.START_DATE,102) AS P_START_DATE, CONVERT(char(10),A.END_DATE,102) AS P_END_DATE, A.STATUS
				FROM
					DF_PROJECT A WITH(NOLOCK) INNER JOIN DF_PROJECT_DETAIL B WITH(NOLOCK) ON A.PROJECT_NO = B.PROJECT_NO
				$searchSQL
				GROUP BY B.PRS_ID, B.PRS_POSITION, B.PRS_NAME, B.PART, B.PART_RATE, A.PROJECT_NO, A.TITLE, CONVERT(char(10),A.START_DATE,102), CONVERT(char(10),A.END_DATE,102), A.STATUS, B.SORT
			) T";
	$rs = sqlsrv_query($dbConn,$sql);
/*
	$sql = "SELECT 
				COUNT(*) 
			FROM 
				DF_PROJECT A WITH(NOLOCK) INNER JOIN DF_PROJECT_DETAIL B WITH(NOLOCK) ON A.PROJECT_NO = B.PROJECT_NO
			$searchSQL";
	$rs = sqlsrv_query($dbConn,$sql);
*/

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$orderbycase = "ORDER BY A.PROJECT_NO DESC, CASE 
						WHEN B.PART = 'PM' THEN 1 
						WHEN B.PART = 'PL' THEN 2
						WHEN B.PART = '기획' THEN 3
						WHEN B.PART = '디자인' THEN 4
						WHEN B.PART = '모션' THEN 5
						WHEN B.PART = '개발(front-end)' THEN 6
						WHEN B.PART = '개발(back-end)' THEN 7 END, B.SORT";

	$sql = "SELECT 
				T.PRS_ID, T.PRS_POSITION, T.PRS_NAME, T.PART, T.PART_RATE, T.PROJECT_NO, T.TITLE, T.P_START_DATE, T.P_END_DATE, T.STATUS
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER($orderbycase) AS ROWNUM, 
					B.PRS_ID, B.PRS_POSITION, B.PRS_NAME, B.PART, B.PART_RATE,
					A.PROJECT_NO, A.TITLE, CONVERT(char(10),A.START_DATE,102) AS P_START_DATE, CONVERT(char(10),A.END_DATE,102) AS P_END_DATE, A.STATUS
				FROM
					DF_PROJECT A WITH(NOLOCK) INNER JOIN DF_PROJECT_DETAIL B WITH(NOLOCK) ON A.PROJECT_NO = B.PROJECT_NO
				$searchSQL
				GROUP BY B.PRS_ID, B.PRS_POSITION, B.PRS_NAME, B.PART, B.PART_RATE, A.PROJECT_NO, A.TITLE, CONVERT(char(10),A.START_DATE,102), CONVERT(char(10),A.END_DATE,102), A.STATUS, B.SORT
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";								
	$rs = sqlsrv_query($dbConn,$sql);
?>

<? include INC_PATH."/top.php"; ?>

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

		//검색
		$(".df_textinput").keypress(function(e){
			if (e.keyCode == 13)
			{
				$("#page").val("1");
				$("#form").attr("target","_self");
				$("#form").attr("action","<?=CURRENT_URL?>"); 
				$("#form").submit();
			}
		});
		$("#btnSearch").attr("style","cursor:pointer;").click(function(){
			var fr = $("#fr_year").val() +"-"+ $("#fr_month").val() +"-"+ $("#fr_day").val();
			var to = $("#to_year").val() +"-"+ $("#to_month").val() +"-"+ $("#to_day").val();

			if (fr > to)
			{
				alert("프로젝트 기간을 확인해 주세요.");
				return;
			}
			$("#page").val("1");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});
		//초기화
		$("#btnReset").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#name").val("");
			$("#fr_year").val("<?=$nowYear?>");
			$("#fr_month").val("<?=$nowMonth?>");
			$("#fr_day").val("1");
			$("#to_year").val("<?=$nowYear?>");
			$("#to_month").val("<?=$nowMonth?>");
			$("#to_day").val("<?=$nowDay?>");
			$("#status").val("ING");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>"); 
			$("#form").submit();
		});

		//읽기
		$("[name=linkView]").attr("style","cursor:pointer;").click(function(){
			$("#form").attr("target","_self");
			$("#form").attr("action","project_detail.php?project_no="+$(this).attr("title")); 
			$("#form").submit();
		});
	});
</script>
</head>
<body>
<? include INC_PATH."/top_menu.php"; ?>
<form name="form" id="form" method="post">
<input type="hidden" name="page" id="page" value="<?=$page?>">
<input type="hidden" name="type" id="type" value="<?=$type?>">
<? include INC_PATH."/project_menu.php"; ?>

    <!-- 본문 시작 -->
    <section class="section is-resize">
        <div class="container">
            <div class="content">
                <div class="card">
                    <div class="card-content">
                        <div class="columns">
                            <div class="column" style="display:inline-block;flex-grow:0;flex-basis:auto;">
                                <div class="field is-group">
                                    <div class="control select">
                                        <select name="fr_year" id="fr_year">
                                            <?
                                            for ($i=$startYear; $i<=($nowYear+1); $i++)
                                            {
                                                if ($i == $p_fr_year)
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
                                        <select name="fr_month" id="fr_month">
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

                                                if ($j == $p_fr_month)
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
                                    <div class="control select">
                                        <select name="fr_day" id="fr_day">
                                            <?
                                            for ($i=1; $i<=31; $i++)
                                            {
                                                if (strlen($i) == "1")
                                                {
                                                    $j = "0".$i;
                                                }
                                                else
                                                {
                                                    $j = $i;
                                                }

                                                if ($j == $p_fr_day)
                                                {
                                                    $selected = " selected";
                                                }
                                                else
                                                {
                                                    $selected = "";
                                                }

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
                                            for ($i=$startYear; $i<=($nowYear+1); $i++)
                                            {
                                                if ($i == $p_to_year)
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
                                        <select name="to_month" id="to_month">
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

                                                if ($j == $p_to_month)
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
                                    <div class="control select">
                                        <select name="to_day" id="to_day">
                                            <?
                                            for ($i=1; $i<=31; $i++)
                                            {
                                                if (strlen($i) == "1")
                                                {
                                                    $j = "0".$i;
                                                }
                                                else
                                                {
                                                    $j = $i;
                                                }

                                                if ($j == $p_to_day)
                                                {
                                                    $selected = " selected";
                                                }
                                                else
                                                {
                                                    $selected = "";
                                                }

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

                        <div class="field is-grouped">

                            <div class="control">
                                <input id="name" name="name" nput class="input" type="text" placeholder="등록자" value="<?=$p_name?>">
                            </div>
                            <div class="control select">
                                <select name="status" id="status" class="circumstance">
                                    <option value="ALL"<? if ($p_status == "ALL") { echo " selected"; }?>>전체</option>
                                    <option value="ING"<? if ($p_status == "ING") { echo " selected"; }?>>진행중</option>
                                    <option value="END"<? if ($p_status == "END") { echo " selected"; }?>>완료</option>
                                </select>
                            </div>
                            <div class="control">
                                <button class="button is-link" id="btnSearch" >
                                <span class="icon is-small" >
                                    <i class="fas fa-search"></i>
                                </span>
                                    <span>검색</span>
                                </button>
                            </div>
                            <div class="control">
                                <button class="button is-danger" id="btnReset">
                                <span class="icon is-small">
                                    <i class="fas fa-times"></i>
                                </span>
                                    <span>초기화</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">

                <hr>
                <table class="table is-fullwidth is-hoverable is-resize">
                    <colgroup>
                        <col width="6%">
                        <col width="*">
                        <col width="10%">
                        <col width="15%">
                        <col width="8%">
                        <col width="20%">
                        <col width="8%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th><span class="is-hidden-mobile">No.</span></th>
                        <th>진행기간 / 프로젝트명</th>
                        <th class="has-text-centered">이름</th>
                        <th class="has-text-centered">역할</th>
                        <th class="has-text-centered">참여율</th>
                        <th class="has-text-centered">참여 기간</th>
                        <th class="has-text-centered">상태</th>
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
                            <td colspan="5" class="has-text-centered">해당 정보가 없습니다.</td>
                        </tr>
                        <?
                    }
                    else
                    {
                        while ($record = sqlsrv_fetch_array($rs))
                        {
                            $id = $record['PRS_ID'];
                            $position = $record['PRS_POSITION'];
                            $name = $record['PRS_NAME'];
                            $part = $record['PART'];
                            $part_rate = $record['PART_RATE'];
                            $project_no = $record['PROJECT_NO'];
                            $title = $record['TITLE'];
                            $start_date = $record['P_START_DATE'];
                            $end_date = $record['P_END_DATE'];
                            $status = $record['STATUS'];
                            ?>
                            <tr>

                                <td><?=$i?></td>
                                <td name="linkView"  title="<?=$project_no?>">
                                    <a><span class="is-size-7">[<?=$project_no?>] <?=$start_date?> - <?=$end_date?></span>
                                        <br>
                                        <span><?=$project_no?><?=getCutString($title,55);?></span>
                                    </a>
                                </td>
                                <td class="has-text-centered"><?=$position?> <?=$name?></td>
                                <td class="has-text-centered"><?=$part?></td>
                                <td class="has-text-centered"><?=$part_rate?>%</td>
                                <td class="has-text-centered">
                                    <?
                                    $sql1 = "SELECT 
						CONVERT(char(10),START_DATE,102) AS START_DATE, CONVERT(char(10),END_DATE,102) AS END_DATE 
					FROM 
						DF_PROJECT_DETAIL WITH(NOLOCK) 
					WHERE
						PROJECT_NO = '$project_no' AND PRS_ID = '$id'
					ORDER BY 
						SORT";
                                    $rs1 = sqlsrv_query($dbConn,$sql1);

                                    while ($record1=sqlsrv_fetch_array($rs1))
                                    {
                                        $start_date = $record1['START_DATE'];
                                        $end_date = $record1['END_DATE'];
                                        ?>
                                        <?=$start_date?>-<?=$end_date?><br>
                                        <?
                                    }
                                    ?>
                                </td>
                                <td class="has-text-centered"><? if ($status == "END") { echo "완료"; } else { echo "진행중"; } ?></td>
                            </tr>
                            </tr>
                            <?
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
    <? include INC_PATH."/bottom.php"; ?>
</form>
</body>
</html>

