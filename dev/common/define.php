<?php
	@session_start();
	@session_cache_limiter("private");

	@extract($_GET);
	@extract($_POST);
	@extract($_SERVER);
	@extract($_SESSION);
	@extract($_REQUEST);

	define("HEAD_TITLE"					, "DESIGN FEVER INTRANET");									// <HEAD><TITLE>

	define("DOMAIN_NAME"				, "dev3.designfever.com");									// Domain
	define("SITE_URL"					, "http://dev3.designfever.com");							// 서비스 URL

	define("CURRENT_PAGE"				, basename($_SERVER["PHP_SELF"]));							// 현재 PAGE
	define("CURRENT_URL"				, $REQUEST_URI);											// 현재 URL
	define("CURRENT_FULL"				, SITE_URL.CURRENT_URL);									// 현재 FULL URL
	define("ENCODE_CURRENT_URL"			, urlencode(CURRENT_FULL));									// 현재 FULL URL Encoding

	define("REMOTE_IP"					, $_SERVER['REMOTE_ADDR']);									// 접속 IP

	define("BOARD_DIR"					, "D:\work_v2/board_file/");								// 공지사항 업로드경로
	define("BOARD_URL"					, "http://dev3.designfever.com/board_file/");				// 공지사항 업로드URL

	define("BOOK_DIR"					, "D:\work_v2/book_file/");									// 게시판 업로드경로
	define("BOOK_URL"					, "http://dev3.designfever.com/book_file/");				// 게시판 업로드URL

	define("APPROVAL_DIR"				, "D:\work_v2/approval_file/");								// 전자결재 업로드경로
	define("APPROVAL_URL"				, "http://dev3.designfever.com/approval_file/");			// 전자결재 업로드URL

	define("PRS_DIR"					, "D:\work_v2/file/");										// 프로필/서명 업로드경로
	define("PRS_URL"					, "http://dev3.designfever.com/file/");						// 프로필/서명 업로드URL

	define("INC_PATH"					, "D:\work_v2/include");									// include 경로
	define("CMN_PATH"					, "D:\work_v2/common");										// common 경로
	define("CLS_PATH"					, "D:\work_v2/Classes");									// Classes 경로
	define("JSON_PATH"					, "D:\work_v2/json");										// Json 경로

	define("LOGIN_URL"					, "http://dev3.designfever.com/index.php?retUrl=");			// 로그인 URL
?>
