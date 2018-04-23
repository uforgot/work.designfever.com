<?php
	@ini_set("session.cookie_lifetime", 0);
	@ini_set("session.cache_expire", 36000);
	session_start();

	require_once "define.php";		// 설정화일

	require_once "db.php";			// DB
	require_once "function.php";	// function, class

	//유저 고유번호
	$prs_id = isset($_SESSION['DF_PRS_ID']) ? $_SESSION['DF_PRS_ID'] : null;

	//이사이상 정의 배열
	$positionS_arr = array('이사','대표');

	//실장이상 정의 배열
	$positionA_arr = array('실장','수석','부장','이사','대표');

	//팀장이하 정의 배열(주간보고서 작성)
	$positionB_arr = array('인턴','사원','주임','선임','대리','PD','책임','과장','팀장','차장');

	//2018 조직개편 주간보고서 실장 권한 (각 실별 최종 결재자)
	//$NoCommuting_arr = array('노진영','박재형','최동현','박수천','이주환');
	$NoCommuting_arr = array('22','87','148','15','24');

	//2018 조직개편 근태 팀/실장 권한
	//$positionC_arr = array('장승애','배유리','김득헌','오주헌','구경모','문지웅','유근주','김형곤','반승한','한성백','한영수','김숙진');
	$positionC_arr = array('308','324','60','48','59','164','191','71','95','85','80','26');

	//2018 조직개편 주간보고서 실장 권한 (각 실별 최종 결재자)
	//$weekly_arr = array('문지웅','한영수','김숙진');
	$weekly_arr = array('164','80','26');

	//2018 조직개편 전자결재 휴가 최종결재자
	//$approval_arr = array('장승애','배유리','김득헌','오주헌','구경모','문지웅','김형곤','반승한','한성백','한영수','김숙진');
	$approval_arr = array('308','324','60','48','59','164','71','95','85','80','26');

	//경영지원팀 
	//$business_arr = array('김자연','강기모','김숙진','윤다윤','임우리','김유리','김민경','안지혜','박현정','김명진','배유리','서효정');
	$happyLab_arr = array('161','151','26','172','193','190','118','128','189','112','28','197');

	//행복연구소 
	//$happyLab_arr = array('김숙진','김은경');
	$business_arr = array('26','290');

	//파트너멤버 
	$partner_arr = array('Creative Planning Division','Creative Planning 1 Team','Creative Planning 2 Team','Marketing Planning Division');

	//접근 허용 IP
	//회사 IP(Lexus VPN), 회사 IP, 지하 IP
	$ok_ip_arr = array('119.192.230.238','119.192.230.239','59.10.250.106');

/*
//정의
	//PRF_ID
	0 - 승인대기
	1 - 직원
	2 - 팀장
	3 - 실장		= 인사관리자
	4 - 관리자,임원	= 전체관리자
	5 - 수습
	6 - 퇴사자
	7 - 파견/계약직

	//GUNUN1(출근),GUBUN2(퇴근)
	1 - 출근
	2 - 퇴근
	3 - 연장근무
	4 - 프로젝트 오전반차
	5 - 프로젝트 오후반차
	6 - 외근
	7 - 지각
	8 - 오전반차
	9 - 오후반차
	10 - 휴가
	11 - 병가
	12 - 경조사
	13 - 기타
	14 - 결근,휴가소진시
	15 - 교육/훈련
	16 - 프로젝트휴가
	17 - 리프레시휴가
	18 - 무급휴가
	19 - 민방위/예비군
	20 - 출산휴가
	21 - 육아휴직
*/

	// 단축팀명 매칭 배열
	$_team_trans_arr = array(
							"CEO"=>"CEO",
							"CP 실"=>"Creative Planning Division",
							"CP 1팀"=>"Creative Planning 1 Team",
							"CP 2팀"=>"Creative Planning 2 Team",
							"MP 실"=>"Marketing Planning Division",
							"Design 1실"=>"Design 1 Division",
							"Design 1실 1팀"=>"Design 1 Division 1 Team",
							"Design 2실"=>"Design 2 Division",
							"Design 2실 1팀"=>"Design 2 Division 1 Team",
							"Design 2실 2팀"=>"Design 2 Division 2 Team",
							"Motion 실"=>"Motion Division",
							"Motion 1팀"=>"Motion 1 Team",
							"Art 실"=>"Art Division",
							"VID"=>"Visual Interaction Development",
							"VID 1팀"=>"VID 1 Team",
							"VID 2팀"=>"VID 2 Team",
							"LAB"=>"LAB",
							"BST 팀"=>"Business Support Team"
						);	

	// 디바이스 상태 배열
	$_status_arr = array (
							"0"=>"미배정", 
							"10"=>"공용사용", 
							"11"=>"개인사용", 
							"12"=>"대여중", 
							"13"=>"반출중", 
							"20"=>"고장 수리대기중", 
							"21"=>"고장 수리중", 
							"30"=>"사용불가(낙후/고장)", 
							"31"=>"폐기", 
							"32"=>"분실"
						);

	// 디바이스 권한 배열
	$_equip_auth_arr = array ("15", "79", "80", "26", "85", "109", "277", "290", "326");

?>