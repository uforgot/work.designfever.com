<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//금요일 기준 주차 계산(1:월~7:일)
	$BASIC_DOW = 5;

	//금일 날짜 및 요일
	$cur_date = date('Y-m-d');
	$cur_week = date("w");

	//보고서 작성여부 체크 및 팝업(금요일 체크)
	if($cur_week == 4) 
	{
		//주차정보 추출
		$winfo = getWeekInfo($cur_date);

		$rs = chkWeekly($cur_date,"this");
		
		echo $rs;
	}
?>
