<?
	//금요일 기준 주차 계산(1:월~7:일)
	$BASIC_DOW = 5;

	//금일 날짜 및 요일
	$cur_date = date('Y-m-d');
	$cur_week = date("w");

	/*/디버깅 날짜 설정
	if ($prs_id == 85) {
		$cur_date = date('2016-10-28');
		$cur_week = 5;
	}
	*/

	//월요일~수요일, 목요일 기준 주차적용
	if($cur_week >= 1 && $cur_week <= 3) {
		$add = 4 - $cur_week;
		$ndate = date("Y-m-d", strtotime("$cur_date +$add day"));
	} else {
		$ndate = $cur_date;
	}

	//주차정보 추출
	$winfo = getWeekInfo($ndate);
	/*
	if ($prs_id == 85) {
		echo "총 주차: ".$winfo["tot_week"]."<br>";
		echo "현 주차: ".$winfo["cur_week"]."<br>";
		echo "범 위: ".$winfo["str_week"]."<br>";
		//exit;
	}
	*/
	
	//로그인이 되어 있는 경우
	if ($prs_login) {
		//팀장이하 생성
		if (in_array($prs_position,$positionB_arr) && $prs_login != 'dfadmin') {
			//주간보고 기본데이터 체크 및 처리
			if (!$log_weekly_create || $log_weekly_create < $winfo["cur_week"]) 
			{
				$rs = setWeeklyData($winfo);

				if ($rs == false)
				{
	?>
				<script language="javascript">
					alert("처리에 실패 하였습니다. 개발팀에 문의해 주세요.");
					history.back();
				</script>
	<?
				exit;
				}
			}

			//전주 보고서 작성여부 체크 및 팝업(금요일 체크)
			if($cur_week == 5) 
			{
				$rs = chkWeekly($cur_date,"prev");
				
				if ($rs == false)
				{
	?>
				<script language="javascript">
					alert("전 주차의 주간보고서를 작성하지 않으셨습니다.\n신속하게 작성해 주시기 바랍니다.");
				</script>
	<?
				}
			}
		} else {
			// 팀장급 위는 주간보고서 메뉴에서 실리스트로 이동
			if ($_SERVER["PHP_SELF"] == "/weekly/weekly_list.php") {
				echo "<script> parent.location.href = './weekly_list_team.php'; </script>";
				exit;
			}
		}
	}
?>
