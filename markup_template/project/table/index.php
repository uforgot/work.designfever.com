<? require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php"; ?>
<?
//날짜
$rn = isset($_REQUEST['rn']) ? $_REQUEST['rn'] : 2;

//월별 현재 주 기준 전 주차 뽑아내기
$sql = "SELECT WEEK_AREA, WEEK_ORD
			  FROM (SELECT WEEK_AREA, WEEK_ORD, ROW_NUMBER() OVER(ORDER BY WEEK_ORD DESC) RN
					  FROM DF_WEEKLY
	                 GROUP BY WEEK_AREA, WEEK_ORD) AS A
             WHERE RN=".$rn."";
$rs = sqlsrv_query($dbConn,$sql);
$record = sqlsrv_fetch_array($rs);
$week_area = $record['WEEK_AREA'];
$week_1 = substr($week_area,0,4)."-".substr($week_area,5,2)."-".substr($week_area,8,2);
$week_2 = substr($week_area,11,4)."-".substr($week_area,16,2)."-".substr($week_area,19,2);
$last_week = $record['WEEK_ORD'];
$start_year= substr($week_area,0,4);
$this_year = date("Y");
?>
<!DOCTYPE html>
<html>
<head>
    <title>DESIGN FEVER INTRANET</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="//fonts.googleapis.com/earlyaccess/nanumgothic.css">
    <!--<link rel="stylesheet" href="/assets/css/jquery-ui.css" />-->
    <link rel="stylesheet" href="/assets/css/bulma.css">
    <!--<link href="./css/jquery.dataTables.min.css" rel="stylesheet">-->
    <!--<link href="./css/buttons.dataTables.min.css" rel="stylesheet">-->
    <!--<link href="./css/fixedColumns.dataTables.min.css" rel="stylesheet">-->
    <link href="./css/main.css" rel="stylesheet">
    <script>
         var week = <? echo $last_week?>;
    </script>
    <script src="js/vendor/jquery-3.3.1.min.js"></script>
    <script defer src="/assets/js/all.js"></script>
    <script type="text/javascript">
        //전월보기
        function preDay()
        {
            window.location.href="index.php?rn=<?=$rn + 1?>"
        }
        //다음월보기
        function nextDay()
        {
            window.location.href="index.php?rn=<?=$rn - 1?>"
        }
    </script>

</head>
<body>
<form method="post" name="form" id="form">
    <input type="hidden" name="rn" value="<?=$rn?>">
    <section class="section">
        <div class="card navbar">
            <div class="column">
                <div class="content">
                    <div class="calendar is-large">
                        <div class="calendar-nav">
                            <div class="calendar-nav-previous-month">
                                <div>
                                    <?if($start_year < $this_year){?> <!--올해 기준-->
                                        <!--예외처리-->
                                    <?}else{?>
                                        <a href="javascript:preDay();" class="button is-text is-small is-primary">
                                            <i class="fa fa-chevron-left"></i>
                                        </a>
                                    <?}?>
                                </div>
                            </div>
                            <div>
                                <span class="title has-text-white">(<?=$week_area?>) <?=substr($last_week,0,4)."년 ".substr($last_week,4,2)."월 ".substr($last_week,6,1)."주차 주간보고";?></span><br>
                            </div>
                            <div class="calendar-nav-next-month">
                                <div>
                                    <?if ($rn <= 2){?>
                                        <!--예외처리-->
                                    <?}else{?>
                                        <a href="javascript:nextDay();" class="button is-text is-small is-primary">
                                            <i class="fa fa-chevron-right"></i>
                                        </a>
                                    <?}?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="level">
            <div class="level-left">
                <div class="tabs is-boxed">
                    <ul>
                        <!--<li class="is-active btn-tab"><a>요약</a></li>
                        <li class="btn-tab"><a>자세히</a></li>-->
                    </ul>
                </div>
            </div>
            <div class="level-right">
                <? //if ($prf_id == "4") { ?>
                <a class="button" alt="엑셀다운로드" id="btnExcel">
					<span class="icon is-small">
						<i class="fas fa-file-excel"></i>
					</span>
                    <span>엑셀로 다운로드</span>
                </a>
                <? //} ?>
            </div>

        </div>

        <div class="table-area">
            <!--div class="container-tbl tbl-1">
                <table id="project_member_1" class="stripe row-border order-column display" style="width:100%">
                    <thead>

                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
            <div class="container-tbl tbl-2">
                <table id="project_member_2" class="stripe row-border order-column display" style="width:100%">
                    <thead>

                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div-->
        </div>

        <!-- Comments are just to fix whitespace with inline-block -->
        <div class="Spinner"><!--
    --><div class="Spinner-line Spinner-line--1"><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--left"></div><!--
        --></div><!--
        --><div class="Spinner-line-ticker"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--center"></div><!--
        --></div><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--right"></div><!--
        --></div><!--
    --></div><!--
    --><div class="Spinner-line Spinner-line--2"><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--left"></div><!--
        --></div><!--
        --><div class="Spinner-line-ticker"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--center"></div><!--
        --></div><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--right"></div><!--
        --></div><!--
    --></div><!--
    --><div class="Spinner-line Spinner-line--3"><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--left"></div><!--
        --></div><!--
        --><div class="Spinner-line-ticker"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--center"></div><!--
        --></div><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--right"></div><!--
        --></div><!--
    --></div><!--
    --><div class="Spinner-line Spinner-line--4"><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--left"></div><!--
        --></div><!--
        --><div class="Spinner-line-ticker"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--center"></div><!--
        --></div><!--
        --><div class="Spinner-line-cog"><!--
            --><div class="Spinner-line-cog-inner Spinner-line-cog-inner--right"></div><!--
        --></div><!--
    --></div><!--

--></div><!--/spinner -->

    </section>
</form>
<script src="js/vendor/jquery.dataTables.min.js"></script>
<script src="js/vendor/dataTables.buttons.min.js"></script>
<script src="js/vendor/buttons.print.min.js"></script>
<script src="js/vendor/dataTables.fixedColumns.min.js"></script>
<script src="js/main.js"></script>
<script>
    //엑셀 다운로드
    $("#btnExcel").attr("style", "cursor:pointer;").click(function () {
        $("#form").attr("target", "hdnFrame");
        $("#form").attr("action", "excel_download.php");
        $("#form").submit();
    });
</script>
<? include INC_PATH."/bottom.php"; ?>
</form>
</body>
</html>