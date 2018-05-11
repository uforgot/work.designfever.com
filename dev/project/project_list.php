<?
    require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
    require_once CMN_PATH."/login_check.php";
    require_once CMN_PATH."/checkout_check.php"; //퇴근시간 출력을 위해 추가(모든페이지 공통 들어가야할듯) ksyang
?>

<?
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "ING";

	$p_search = isset($_REQUEST['search']) ? $_REQUEST['search'] : null;
	$p_no = isset($_REQUEST['no']) ? $_REQUEST['no'] : null;
	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	if ($p_search == "")
	{
		$sql = "SELECT TOP 1 CONVERT(char(10),START_DATE,120) FROM DF_PROJECT WITH(NOLOCK) WHERE USE_YN = 'Y' AND STATUS = '$type' ORDER BY START_DATE";
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		$p_fr_date = $record[0];

		$p_fr_year = substr($p_fr_date,0,4);

		$p_fr_month = substr($p_fr_date,5,2);
		if (strlen($p_fr_month) == 1) { $p_fr_month = "0". $p_fr_month; }
		$p_fr_day = substr($p_fr_date,8,2);
		if (strlen($p_fr_day) == 1) { $p_fr_day = "0". $p_fr_day; }
	}
	else
	{
		$p_fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : $nowYear;
		$p_fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : $nowMonth;
		if (strlen($p_fr_month) == 1) { $p_fr_month = "0". $p_fr_month; }
		$p_fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : 1;
		if (strlen($p_fr_day) == 1) { $p_fr_day = "0". $p_fr_day; }

		$p_fr_date = $p_fr_year ."-". $p_fr_month ."-". $p_fr_day;
	}

	$p_to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : $nowYear;
	$p_to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : $nowMonth;
	if (strlen($p_to_month) == 1) { $p_to_month = "0". $p_to_month; }
	$p_to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : $nowDay;
	if (strlen($p_to_day) == 1) { $p_to_day = "0". $p_to_day; }

	$p_to_date = $p_to_year ."-". $p_to_month ."-". $p_to_day;

	$searchSQL = " WHERE USE_YN = 'Y' AND STATUS = '$type' AND '$p_fr_date' <= CONVERT(char(10),END_DATE,120) AND '$p_to_date' >= CONVERT(char(10),START_DATE,120)";

	if ($p_no != "")
	{
		$searchSQL .= " AND PROJECT_NO = '$p_no'";
	}
	if ($p_name != "")
	{
		$searchSQL .= " AND PRS_NAME = '$p_name'";
	}

	$sql = "SELECT COUNT(PROJECT_NO) FROM DF_PROJECT WITH(NOLOCK) ". $searchSQL;
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 10;

	$sql = "SELECT 
				T.PROJECT_NO, T.REG_DATE, T.TITLE, T.PRS_POSITION, T.PRS_NAME, T.START_DATE, T.END_DATE, T.PROGRESS, T.STATUS
			FROM 
			(
				SELECT 
					ROW_NUMBER() OVER(ORDER BY PROJECT_NO DESC) AS ROWNUM, 
					PROJECT_NO, CONVERT(char(10),REG_DATE,102) AS REG_DATE, TITLE, PRS_POSITION, PRS_NAME,
					CONVERT(char(10),START_DATE,102) AS START_DATE, CONVERT(char(10),END_DATE,102) AS END_DATE, PROGRESS, STATUS
				FROM 
					DF_PROJECT WITH(NOLOCK)
				$searchSQL
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
				$("#search").val("Y");
				$("#form").attr("target","_self");
				$("#form").attr("action","<?=CURRENT_URL?>");
				$("#form").submit();
			}
		});
		$("#btnSearch").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#form").attr("target","_self");
			$("#form").attr("action","<?=CURRENT_URL?>");
			$("#form").submit();
		});
		//초기화
		$("#btnReset").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#search").val("");
			$("#no").val("");
			$("#name").val("");
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

		//등록
		$("#btnWrite").attr("style","cursor:pointer;").click(function(){
			$("#page").val("1");
			$("#no").val("");
			$("#name").val("");
			$("#form").attr("target","_self");
			$("#form").attr("action","project_write.php");
			$("#form").submit();
		});

        //등록
        $("#btnWrite2").attr("style","cursor:pointer;").click(function(){
            $("#page").val("1");
            $("#no").val("");
            $("#name").val("");
            $("#form").attr("target","_self");
            $("#form").attr("action","project_write.php");
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
<input type="hidden" name="search" id="search">
<? include INC_PATH."/project_menu.php"; ?>

    <!-- 본문 시작 -->
    <section class="section df-project">
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
                                <input id="no" name="no" class="input" type="text" placeholder="프로젝트 번호" value="<?=$p_no?>">
                            </div>
                            <div class="control">
                                <input id="name" name="name" nput class="input" type="text" placeholder="등록자" value="<?=$p_name?>">
                            </div>
                            <div class="control">
                                <a class="button is-link" id="btnSearch" >
                                <span class="icon is-small" >
                                    <i class="fas fa-search"></i>
                                </span>
                                    <span>검색</span>
                                </a>
                            </div>
                            <div class="control">
                                <a class="button is-danger" id="btnReset">
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

                <div class="control is-hidden-tablet">
                    <a id="btnWrite" class="button is-danger is-fullwidth">
                        <span class="icon is-small">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                        <span>프로젝트 등록</span>
                    </a>
                </div>
                <div class="columns is-hidden-mobile">
                    <!-- Left side -->
                    <div class="column is-three-fifths">
                        <p class="is-size-7">
                            * 프로젝트가 완료된 이후에는 꼭 해당 프로젝트에 들어가셔서 <span class="tag is-info">프로젝트 완료</span> 처리 해주시기 바랍니다.<br>
                            * 프로젝트 번호는 관리자에 의해 변경될 수 있습니다.
                        </p>
                    </div>
                    <!-- Right side -->
                    <div class="column">
                        <div class="control has-text-right">
                            <a id="btnWrite2" class="button is-danger">
                                <span class="icon is-small">
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                                <span>프로젝트 등록</span>
                            </a>
                        </div>
                    </div>
                </div>
                <hr>
                <table class="table is-fullwidth is-hoverable">
                    <colgroup>
                        <col width="6%">
                        <col width="*">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th><span class="is-hidden-mobile">No.</span></th>
                        <th>프로젝트명</th>
                        <th class="has-text-centered">등록자</th>
                        <th class="has-text-centered">등록일</th>
                        <th class="has-text-centered">진행률</th>
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
                    $project_no = $record['PROJECT_NO'];
                    $reg_date = $record['REG_DATE'];
                    $title = $record['TITLE'];
                    $position = $record['PRS_POSITION'];
                    $name = $record['PRS_NAME'];
                    $start_date = $record['START_DATE'];
                    $end_date = $record['END_DATE'];
                    $progress = $record['PROGRESS'];
                    $status = $record['STATUS'];
                    ?>
                    <tr>

                        <td><?=$i?></td>
                        <td name="linkView"  title="<?=$project_no?>">
                                <a><span class="is-size-7">[<?=$project_no?>] <?=$start_date?> - <?=$end_date?></span>
                                <br>
                                <span>[<?=$project_no?>]&nbsp;<?=getCutString($title,55);?></span>
                                </a>
                        </td>
                        <td class="has-text-centered"><?=$position?> <?=$name?></td>
                        <td class="has-text-centered"><?=$reg_date?></td>
                        <td class="has-text-centered"><? if ($status == "END") { echo "완료"; } else { echo $progress ."%"; } ?></td>
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

