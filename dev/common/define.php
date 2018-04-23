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
	define("SITE_URL"					, "http://dev3.designfever.com");							// ���� URL

	define("CURRENT_PAGE"				, basename($_SERVER["PHP_SELF"]));							// ���� PAGE
	define("CURRENT_URL"				, $REQUEST_URI);											// ���� URL
	define("CURRENT_FULL"				, SITE_URL.CURRENT_URL);									// ���� FULL URL
	define("ENCODE_CURRENT_URL"			, urlencode(CURRENT_FULL));									// ���� FULL URL Encoding

	define("REMOTE_IP"					, $_SERVER['REMOTE_ADDR']);									// ���� IP

	define("BOARD_DIR"					, "D:\work_v2/board_file/");								// �������� ���ε���
	define("BOARD_URL"					, "http://dev3.designfever.com/board_file/");				// �������� ���ε�URL

	define("BOOK_DIR"					, "D:\work_v2/book_file/");									// �Խ��� ���ε���
	define("BOOK_URL"					, "http://dev3.designfever.com/book_file/");				// �Խ��� ���ε�URL

	define("APPROVAL_DIR"				, "D:\work_v2/approval_file/");								// ���ڰ��� ���ε���
	define("APPROVAL_URL"				, "http://dev3.designfever.com/approval_file/");			// ���ڰ��� ���ε�URL

	define("PRS_DIR"					, "D:\work_v2/file/");										// ������/���� ���ε���
	define("PRS_URL"					, "http://dev3.designfever.com/file/");						// ������/���� ���ε�URL

	define("INC_PATH"					, "D:\work_v2/include");									// include ���
	define("CMN_PATH"					, "D:\work_v2/common");										// common ���
	define("CLS_PATH"					, "D:\work_v2/Classes");									// Classes ���
	define("JSON_PATH"					, "D:\work_v2/json");										// Json ���

	define("LOGIN_URL"					, "http://dev3.designfever.com/index.php?retUrl=");			// �α��� URL
?>
