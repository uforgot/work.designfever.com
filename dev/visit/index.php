<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	//require_once CMN_PATH."/login_check.php";
?>

<?
	$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d"); 
	$date_arr = explode("-",$date);
	$p_year = $date_arr[0];
	$p_month = $date_arr[1];
	$p_day = $date_arr[2];

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }
	if (strlen($p_day) == "1") { $p_day = "0".$p_day; }

	$NowDate = date("Y-m-d");
	$PrevDate = date("Y-m-d",strtotime ("-1 day", strtotime($date)));
	$NextDate = date("Y-m-d",strtotime ("+1 day", strtotime($date)));

	$startYear = 2013;
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>df 방문객 예약현황</title>
<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
<link rel="stylesheet" href="/css/common.css" />
<link rel="stylesheet" href="/css/style_20150318.css" />
<link rel="stylesheet" href="/css/jquery-ui.css" />
<style type="text/css">
	#ui-datepicker-div { padding:0 30px 0 30px; border:1px solid #000; }
	.ui-widget-content { border:0px; }
	.ui-widget-header { border:0px; }
	.ui-icon, .ui-widget-content .ui-icon { background-image: url("/img/ui-icons_222222_256x240.png"); }
	.ui-widget-header .ui-icon { background-image: url("/img/ui-icons_222222_256x240.png"); }
	.ui-state-hover .ui-icon, .ui-state-focus .ui-icon { background-image: url("/img/ui-icons_454545_256x240.png"); }
	.ui-state-active .ui-icon { background-image: url("/img/ui-icons_454545_256x240.png"); }
	.ui-datepicker-header { margin-top:9px; height:35px; }
	.ui-datepicker-calendar thead { border-top:1px solid #000; border-bottom:1px solid #000; }
	.ui-datepicker-calendar tbody { border-bottom:12px solid #fff; }
	.ui-datepicker-calendar tbody tr:first-child td { padding-top:24px; }
	.ui-datepicker-calendar tbody a { text-align:center; }
	.ui-datepicker-calendar tbody .ui-datepicker-week-end:nth-child(1) a { color:#eb6100; }
</style>

<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.easing.1.3.js"></script>
<script src="/js/jquery-ui.js"></script>
<script src="/js/modernizr.custom.72169.js"></script>
<script src="/js/jquery.cookie.js"></script>
<script src="/js/custom.js"></script>

<!-- PC, 모바일 분기 -->
<script type="text/javascript">
var browserType = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));

if(browserType) {
	window.location.href = "./mobile/";
} else {

}
</script>
<!-- //PC, 모바일 분기 -->

<!-- 페이지 로딩바 표시 -->
<link rel="stylesheet" href="../../css/page.css" />
<script src="../../js/page.js"></script>
<!-- //페이지 로딩바 표시 -->

<script type="text/javascript">
	$(document).ready(function() {
		var date = "<?=$date?>";
		var interval = 0;

		(function poll() {
			setTimeout(function() {
				$.ajax({ url: "./json.php?date=" + date, success: function(data) {
					$('span.total').html(data.total);
					$('tbody#list').html(data.list);

					interval = 10000;
					poll();
				}, dataType: "json"});
			}, interval);
		}());
	});

	function sSubmit(f)
	{	
		var frm = document.form1;
		frm.date.value = f.year.value + "-" + f.month.value + "-" + f.day.value;
		frm.submit();
	}
	//전월보기
	function preDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$PrevDate?>";
		frm.submit();
	}
	//다음월보기
	function nextDay()
	{
		var frm = document.form1;
		frm.date.value = "<?=$NextDate?>";
		frm.submit();
	 }

	//게시물 읽기
	function funView(seqno)
	{
		$("#form").attr("target","_self");
		$("#form").attr("action","visit_write.php?type=modify&seqno="+seqno); 
		$("#form").submit();
	}
</script>
</head>

<body>

<div class="wrapper">
	
	<!-- header -->
	<h1 class="logo"><a href="./index.php"><img class="js-svg" src="/img/df_logo_new.svg" alt="" /></a></h1>
	<div class="beyond"><img class="js-svg" src="../img/top_title.svg" alt="" /></div>
	<!-- //header -->

	<!-- wapper set open -->
	<div class="line1">
	<div class="line2">
	<div class="line4">
	<div class="line3">
	<!-- //wapper set open -->

		<ul class="gnb">

		</ul>	

		<form method="post" name="form" id="form">

		<div class="inner-home">
			<div class="work_wrap clearfix">
				<div class="cal_top clearfix">
					<a href="javascript:preDay();" class="prev"><img src="../img/btn_prev.gif" alt="전일보기" /></a>
					<div>
					<select name="year" onchange='sSubmit(this.form)'>
					<?
						for ($i=$startYear; $i<=($p_year+1); $i++) 
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
					<span>년</span></div>
					<div>
					<select name="month" onchange='sSubmit(this.form)'>
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
					<span>월</span></div>
					<div>
					<select name="day" onchange='sSubmit(this.form)'>
					<?
						$last_day = date("t", mktime(0, 0, 0, $p_month, '01', $p_year));

						for ($i=1; $i<=$last_day; $i++) 
						{
							if (strlen($i) == "1") 
							{
								$j = "0".$i;
							}
							else
							{
								$j = $i;
							}

							if ($j == $p_day)
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
					<span>일</span></div>
					<a href="javascript:nextDay();" class="next"><img src="../img/btn_next.gif" alt="다음일보기" /></a>
				</div>
				<table class="notable work2" style="margin-bottom:50px;" width="100%">
					<summary></summary>
					<colgroup><col width="*" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /></colgroup>
					<tr>
						<th class="gray">방문객 예약 현황</th>
					</tr>
					<tr>
						<td>총 <b><span class="total">0</span></b>건이 예약 되었습니다.</td>
					</tr>
				</table>
			</div>
			<div class="calender_wrap clearfix">
				<table class="notable work3 board_list" width="100%" style="margin-bottom:10px;">
					<caption>팀원 주간보고서 테이블</caption>
					<colgroup>
						<col width="5%" />
						<col width="12%" />
						<col width="12%" />
						<col width="10%" />
						<col width="10%" />
						<col width="12%" />
						<col width="*" />
					</colgroup>

					<thead>
						<tr>
							<th>No.</th>
							<th>방문일시</th>
							<th>업체명</th> 
							<th>방문자명</th>
							<th>방문차량번호</th>
							<th>연락처</th>
							<th>메모</th>
						</tr>
					</thead>
					<tbody id="list">

					</tbody>
				</table>

			</div>
		</div>
	
	</form>

	<form method="get" name="form1">
		<input type="hidden" name="date">
	</form>

	<!-- wapper set close -->
	</div>				
	</div>				
	</div>		
	</div>
	<!-- //wapper set close -->
	
	<!-- footer -->
	<div class="wrapper_login">
		<p class="footer"><img class="js-svg" src="/img/footerLogo.svg" alt="" /></p>
	</div>
	<!-- //footer -->

</div>

</body>

</html>