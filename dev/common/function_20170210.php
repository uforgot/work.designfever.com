<?
	//배열 출력
	function print_h($str) 
	{
		echo "<pre>";
		print_r($str);
		echo "</pre>";

		return false;
	}

	// 문자열 자르기
	function getCutString($str, $len) 
	{
		$tail = "...";
		$str = trim($str);
		$strlen = strlen($str);

		if ($strlen <= $len)
		{
			$result = $str;
		}
		else
		{
			$total = 0;
			for ($i=0; $i<$len; $i++)
			{
				$asc = ord(substr($str,$i,1));
				if ($asc >= 129) { $total++; }
			}
			if ($total%2 == 0)
			{
				$result = substr($str,0,$len);
			}
			else
			{
				$result = substr($str,0,$len+1);
			}

			$result = $result . $tail;
		}

		return $result;
	}

	//페이징
	function getPaging($total, $page, $per_page) 
	{

		$page_num=10;
		$current_url = CURRENT_PAGE;
		
		$result	= null;

		if ( $page == "" || $page < 1 ) $page = 1;
		$total_page	= ceil($total / $per_page);
		$total_block= ceil($total_page / $page_num);
		$block		= ceil($page / $page_num);
		$first_page	= ($block - 1) * $page_num;
		$last_page	= $block * $page_num;
		$prev_block	= $first_page;
		$next_block	= $last_page + 1;
		$first		= 1;
		$last		= $total_page;
		$prev		= $page - 1;
		$next		= $page + 1;
		$go_page 	= $first_page + 1;

//		if ( $total_page <= 1 )
//			return false;

		if ( $total_block <= $block ) $last_page = $total_page;

		//$result .= "<a href=javascript:Paging(this.form,'$first','$current_url'); class='p_first'><img src='/img/btn_first_page.gif' alt='첫 페이지' /></a>";

		if ( $block > 1 )
			$result .= "<a href=javascript:Paging(this.form,'$prev_block','$current_url');><img src='/img/btn_first_page.gif' alt='이전 10페이지' /></a>";
		
		if ( $page > 1 )
			$result .= "<a href=javascript:Paging(this.form,'$prev','$current_url'); class='p_prev'><img src='/img/btn_prev_page.gif' alt='이전 페이지' /></a>";

		for ( $go_page; $go_page <= $last_page; $go_page++ ) {
			if ( $page == $go_page )
				$result .= "<span>$go_page</span>";
			else
				$result .= "<a href=javascript:Paging(this.form,'$go_page','$current_url');>$go_page</a>";
		}

		if ( $page < $total_page )
			$result .= "<a href=javascript:Paging(this.form,'$next','$current_url'); class='p_next'><img src='/img/btn_next_page.gif' alt='다음 페이지' /></a>";
		
		if($block < $total_block)
			$result .= "<a href=javascript:Paging(this.form,'$next_block','$current_url');><img src='/img/btn_last_page.gif' alt='다음 10페이지' /></a>";

		//$result .= "<a href=javascript:Paging(this.form,'$last','$current_url'); class='p_last'><img src='/img/btn_last_page.gif' alt='마지막 페이지' /></a>";


		return $result;
	}

	//GET, POST 파라미터 추출
	function getParameter($except="") 
	{
		global $_GET, $_POST;

		$result		= null;
		$except		= explode(",", $except);
		$except[]	= "x";
		$except[]	= "y";

		foreach ( $_GET as $key => $val ) {
			if ( !in_array($key, $except) ) 
				$result	.= "$key=".htmlentities(urlencode($val))."&";
		}

		foreach( $_POST as $key => $val ) {
			if( !in_array($key, $except) ) 
				$result	.= "$key=".htmlentities(urlencode($val))."&";
		}
		
		$result = preg_replace("/&$/", "", $result);

		return $result;
	}

	//조직도 팀명
	function getTeamInfo($team)
	{
		$dbInfo = array("DataBase"=>"dfever","UID"=>"sa","PWD"=>"dfever321!@#");
		$dbConn = sqlsrv_connect("localhost",$dbInfo);

		$funcSQL = "SELECT R_SEQNO, STEP FROM DF_TEAM_CODE WITH(NOLOCK) WHERE TEAM = '$team'";
		$funcRs = sqlsrv_query($dbConn,$funcSQL);

		$funcRecord = sqlsrv_fetch_array($funcRs);
		$funcNo = $funcRecord['R_SEQNO'];
		$funcStep = $funcRecord['STEP'];

		if ($funcStep == 0) 
		{
			$result = $team;
		}
		else
		{
			for ($i=$funcStep; $i>1; $i--)
			{
				$funcSQL1 = "SELECT R_SEQNO, STEP, TEAM FROM DF_TEAM_CODE WITH(NOLOCK) WHERE SEQNO = $funcNo";
				$funcRs1 = sqlsrv_query($dbConn,$funcSQL1);

				$funcRecord1 = sqlsrv_fetch_array($funcRs1);
				$funcNo = $funcRecord1['R_SEQNO'];
				$funcStep = $funcRecord1['STEP'];
				$funcTeam = $funcRecord1['TEAM'];

				$result = $funcTeam ." > ". $result;
			}

			$result .= $team;
		}

		return $result;
	}

	//프로필
	function getProfileImg($file,$size,$name=false,$id=false)
	{
		if ($file == "")
		{
			$result = "<img src='/img/noimg.jpg' alt='profile_img' width='". $size ."' height='". $size ."' style='border:1px solid #000;' name='". $name ."' value='". $id ."' />";
		}
		else
		{
			$result = "<img src='/file/". $file ."' alt='profile_img' width='". $size ."' height='". $size ."' style='vertical-align:middle;' name='". $name ."' value='". $id ."' />";
		}
		return $result;
	}

	//특정주차 날짜범위
	function getWeekArea($_date) 
	{ // $week 0:일 ~ 6:토 
		$w = date("w",strtotime($_date)); // 시작날짜의 요일 번호 구하기 

		//금,토
		if ($w >=5 && $w <=6) {
			$add = $w - 5;
		//일,월,화,수,목
		} else {
			$add = $w + 2;
		}

		$s_date = date("Y-m-d", strtotime("$_date -$add day"));
		$e_date = date("Y-m-d", strtotime("$s_date +6 day"));

		$s_date = str_replace("-",".",$s_date);
		$e_date = str_replace("-",".",$e_date);

		return $s_date."~".$e_date; 
	} 

	//특정요일 카운트
	function getWeekCount($s_date, $e_date, $week) 
	{ // $week 0:일 ~ 6:토 
		$s = strtotime($s_date); 
		$e = strtotime($e_date); 
		$d = ceil(($e - $s) / 86400); // 두 날짜 사이의 일수 계산 

		$w = date("w",$s); // 시작날짜의 요일 번호 구하기 
		$g = $week - $w; 
		if($g < 0) $g = 7 + $g;   

		for($i=$g, $cnt=0; $i<=$d; $i+=7) $cnt++; 
		return $cnt; 
	} 

	//현재주차 추출
	/*
		$BASIC_DOW = 3; // 1(Mon) - 7(Sun) , 주차를 나누는 기준 요일
		$new_date = date('Y-m-d');
		$week_info = getWeekInfo($new_date);
	*/
	function getWeekInfo($_date)
	{
		global $BASIC_DOW;

		list($yy, $mm, $dd) = explode('-', $_date);
		
		$dow = date('N', mktime(0, 0, 0, $mm, 1, $yy));
		
		if ($dow <= $BASIC_DOW)
		{
			$diff = $BASIC_DOW - $dow;
			$srt_day = $diff+1;
		} else {
			$diff = 7-$dow;
			$srt_day = $diff + $BASIC_DOW + 1;
		}

		if ($dd < $srt_day)
		{
			$new_date = date('Y-m-d', mktime(0, 0, 0, $mm, 0, $yy));
			$tmp_arr = getWeekInfo($new_date);
			$cur_week = $tmp_arr['cur_week'];
		} else {
			$wom = ceil(($dd-($srt_day-1))/7);
			$cur_week = $yy.$mm.$wom; 
		}

		//해당년도 총 주차
		$tot_week = getWeekCount(date("Y-01-01"),$_date, $BASIC_DOW);
		//해당일의 주차범위  
		$str_week = getWeekArea($_date);

		return array("cur_week"=>$cur_week, "tot_week"=>$tot_week, "str_week"=>$str_week);
	}

	//주간보고 버튼
	function getWeeklyBtn() {
		global $prs_id;

		$week = date("w");

		//목,금 버튼 출력
		if($week >= 4 && $week <=5) {
			$result = "<a href='javascript:go_weekly();' title='주간보고서작성'><img src='img/btn_weekly.png' alt='주간보고서작성' /></a>";
		}

		return $result;
	}

	//주간보고 기본데이터 생성
	function setWeeklyData($winfo) 
	{
		global $prs_id, $prs_name, $prs_login, $prs_team, $prs_position;

		$dbInfo = array("DataBase"=>"dfever","UID"=>"sa","PWD"=>"dfever321!@#");
		$dbConn = sqlsrv_connect("localhost",$dbInfo);

		$order = $winfo['cur_week'];
		$order_tot = $winfo['tot_week'];
		$title = substr($order,4,2)."월 ".substr($order,6,1)."주차 주간보고";
		$week = $winfo['str_week'];
		$complete_yn = "N";

		//시퀀스 값 추출
		$sql = "SELECT ISNULL(MAX(SEQNO),0) FROM DF_WEEKLY WITH(NOLOCK)";
		$rs = sqlsrv_query($dbConn,$sql);

		$result = sqlsrv_fetch_array($rs);
		$seq = $result[0] + 1;

		// 기본 데이터 입력
		$sql = "INSERT INTO DF_WEEKLY
				(SEQNO, WEEK_ORD_TOT, WEEK_ORD, WEEK_AREA, TITLE, PRS_ID, PRS_NAME, PRS_LOGIN, PRS_TEAM, PRS_POSITION, COMPLETE_YN)
				VALUES
				('$seq', '$order_tot', '$order', '$week', '$title', '$prs_id', '$prs_name', '$prs_login', '$prs_team', '$prs_position', '$complete_yn')";
		$rs = sqlsrv_query($dbConn, $sql);

##### 로그 저장 #################################################
//$log_txt = "------------------------------------\r\n";
//$log_txt.= "Time: ".date("Y-m-d H:i:s")."\r\n";
//$log_txt.= "Order: ".$order."\r\n";
//$log_txt.= "Week: ".$week."\r\n";
//$log_txt.= "Name: ".$prs_name."\r\n";
//$log_txt.= "PRS_ID: ".$prs_id."\r\n";
//$log_txt.= "ETC①: ".$sql."\r\n";
#################################################################

		if ($rs != false)
		{
			$sql = "UPDATE DF_PERSON SET
					LOG_WEEKLY_CREATE = '$order' 
					WHERE 
					PRS_ID = '$prs_id'";
			$rs = sqlsrv_query($dbConn, $sql);

##### 로그 저장 #################################################
//$log_txt.= "ETC②: ".$sql."\r\n";
#################################################################

		}

##### 로그 저장 #################################################
//$log_txt.= "------------------------------------";

//$log_dir = $_SERVER["DOCUMENT_ROOT"]."/weekly/log/";   
//$log_file = fopen($log_dir."log_".substr($order,0,6)."_".$prs_name.".txt", "a");  
//fwrite($log_file, $log_txt."\r\n");  
//fclose($log_file);  
#################################################################

		return $rs;
	}

	//보고서 작성 여부 체크
	function chkWeekly($cur_date,$week) 
	{
		global $prs_id;

		$dbInfo = array("DataBase"=>"dfever","UID"=>"sa","PWD"=>"dfever321!@#");
		$dbConn = sqlsrv_connect("localhost",$dbInfo);

		if ($week == "prev") 
		{
			$ndate = date("Y-m-d", strtotime("$cur_date -1 day"));
		}
		else
		{
			$ndate= date("Y-m-d");
		}
		$winfo = getWeekInfo($ndate);
		$order = $winfo['cur_week'];

		$sql = "SELECT COUNT(A.SEQNO) AS CNT
				FROM DF_WEEKLY_DETAIL a, DF_WEEKLY b WITH(NOLOCK) 
				WHERE a.WEEKLY_NO = b.SEQNO AND b.PRS_ID = '$prs_id' AND b.WEEK_ORD = '$order' AND DATALENGTH(a.THIS_WEEK_CONTENT) > 0";
		$rs = sqlsrv_query($dbConn, $sql);
		$row = sqlsrv_fetch_array($rs);
		$thisWeekContentCnt = $row['CNT'];

		if ($thisWeekContentCnt > 0) 
		{
			$flag = true;
		}
		else
		{
			$flag = false;
		}

		return $flag;
	}

	//DATEDIFF
	function datediff($interval, $datefrom, $dateto, $using_timestamps=false) {
	/*
		$interval can be:
		yyyy - Number of full years
		q - Number of full quarters
		m - Number of full months
		y - Difference between day numbers
		(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
		d - Number of full days
		w - Number of full weekdays
		ww - Number of full weeks
		h - Number of full hours
		n - Number of full minutes
		s - Number of full seconds (default)
	*/
		if (!$using_timestamps) {
			$datefrom = strtotime($datefrom, 0);
			$dateto = strtotime($dateto, 0);
		}
		$difference = $dateto - $datefrom; // Difference in seconds
		switch($interval) {
			case 'yyyy': // Number of full years
				$years_difference = floor($difference / 31536000);
				if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
					$years_difference--;
				}
				if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
					$years_difference++;
				}
				$datediff = $years_difference;
				break;
		 
		case "q": // Number of full quarters		 
			$quarters_difference = floor($difference / 8035200);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$quarters_difference--;
			$datediff = $quarters_difference;
			break;
		 
		case "m": // Number of full months		 
			$months_difference = floor($difference / 2678400);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$months_difference--;
			$datediff = $months_difference;
			break;
		 
		case 'y': // Difference between day numbers		 
			$datediff = date("z", $dateto) - date("z", $datefrom);
			break;
		 
		case "d": // Number of full days		 
			$datediff = floor($difference / 86400);
			break;
		 
		case "w": // Number of full weekdays
			$days_difference = floor($difference / 86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $datefrom);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			if ($odd_days > 7) { // Sunday
				$days_remainder--;
			}
			if ($odd_days > 6) { // Saturday
				$days_remainder--;
			}
			$datediff = ($weeks_difference * 5) + $days_remainder;
			break;
		 
		case "ww": // Number of full weeks
			$datediff = floor($difference / 604800);
			break;
		 
		case "h": // Number of full hours
			$datediff = floor($difference / 3600);
			break;
		 
		case "n": // Number of full minutes		 
			$datediff = floor($difference / 60);
			break;
		 
		default: // Number of full seconds (default) 
			$datediff = $difference;
			break;
		}
		 
		return $datediff;	 
	}

	//비밀번호 암호화
	define('PBKDF2_COMPAT_HASH_ALGORITHM', 'SHA256');
	define('PBKDF2_COMPAT_ITERATIONS', 12000);
	define('PBKDF2_COMPAT_SALT_BYTES', 24);
	define('PBKDF2_COMPAT_HASH_BYTES', 24);
	// Calculates a hash from the given password.
	function create_hash($password, $force_compat = false)
	{
		// Generate the salt.
		
		if (function_exists('mcrypt_create_iv')) {
			$salt = base64_encode(mcrypt_create_iv(PBKDF2_COMPAT_SALT_BYTES, MCRYPT_DEV_URANDOM));
		} elseif (file_exists('/dev/urandom') && $fp = @fopen('/dev/urandom', 'r')) {
			$salt = base64_encode(fread($fp, PBKDF2_COMPAT_SALT_BYTES));
		} else {
			$salt = '';
			for ($i = 0; $i < PBKDF2_COMPAT_SALT_BYTES; $i += 2) {
				$salt .= pack('S', mt_rand(0, 65535));
			}
			$salt = base64_encode(substr($salt, 0, PBKDF2_COMPAT_SALT_BYTES));
		}
		
		// Determine the best supported algorithm and iteration count.
		
		$algo = strtolower(PBKDF2_COMPAT_HASH_ALGORITHM);
		$iterations = PBKDF2_COMPAT_ITERATIONS;
		if ($force_compat || !function_exists('hash_algos') || !in_array($algo, hash_algos())) {
			$algo = false;                         // This flag will be detected by pbkdf2_default()
			$iterations = round($iterations / 5);  // PHP 4 is very slow. Don't cause too much server load.
		}
		
		// Return format: algorithm:iterations:salt:hash
		
		$pbkdf2 = pbkdf2_default($algo, $password, $salt, $iterations, PBKDF2_COMPAT_HASH_BYTES);
		$prefix = $algo ? $algo : 'sha1';
		return $prefix . ':' . $iterations . ':' . $salt . ':' . base64_encode($pbkdf2);
	}
	// Checks whether a password matches a previously calculated hash
	function validate_password($password, $hash)
	{
		// Split the hash into 4 parts.
		
		$params = explode(':', $hash);
		if (count($params) < 4) return false;
		
		// Recalculate the hash and compare it with the original.
		
		$pbkdf2 = base64_decode($params[3]);
		$pbkdf2_check = pbkdf2_default($params[0], $password, $params[2], (int)$params[1], strlen($pbkdf2));
		return slow_equals($pbkdf2, $pbkdf2_check);
	}
	// Compares two strings $a and $b in length-constant time.
	function slow_equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0; 
	}
	// PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	// Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	// This implementation of PBKDF2 was originally created by https://defuse.ca
	// With improvements by http://www.variations-of-shadow.com
	function pbkdf2_default($algo, $password, $salt, $count, $key_length)
	{
		// Sanity check.
		
		if ($count <= 0 || $key_length <= 0) {
			trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
		}
		
		// Check if we should use the fallback function.
		
		if (!$algo) return pbkdf2_fallback($password, $salt, $count, $key_length);
		
		// Check if the selected algorithm is available.
		
		$algo = strtolower($algo);
		if (!function_exists('hash_algos') || !in_array($algo, hash_algos())) {
			if ($algo === 'sha1') {
				return pbkdf2_fallback($password, $salt, $count, $key_length);
			} else {
				trigger_error('PBKDF2 ERROR: Hash algorithm not supported.', E_USER_ERROR);
			}
		}
		
		// Use built-in function if available.
		
		if (function_exists('hash_pbkdf2')) {
			return hash_pbkdf2($algo, $password, $salt, $count, $key_length, true);
		}
		
		// Count the blocks.
		
		$hash_length = strlen(hash($algo, '', true));
		$block_count = ceil($key_length / $hash_length);
		
		// Hash it!
		
		$output = '';
		for ($i = 1; $i <= $block_count; $i++) {
			$last = $salt . pack('N', $i);                               // $i encoded as 4 bytes, big endian.
			$last = $xorsum = hash_hmac($algo, $last, $password, true);  // first iteration.
			for ($j = 1; $j < $count; $j++) {                            // The other $count - 1 iterations.
				$xorsum ^= ($last = hash_hmac($algo, $last, $password, true));
			}
			$output .= $xorsum;
		}
		
		// Truncate and return.
		
		return substr($output, 0, $key_length);
	}
	// Fallback function using sha1() and a pure-PHP implementation of HMAC.
	// The result is identical to the default function when used with SHA-1.
	// But it is approximately 1.6x slower than the hash_hmac() function of PHP 5.1.2+,
	// And approximately 2.3x slower than the hash_pbkdf2() function of PHP 5.5+.
	function pbkdf2_fallback($password, $salt, $count, $key_length)
	{
		// Count the blocks.
		
		$hash_length = 20;
		$block_count = ceil($key_length / $hash_length);
		
		// Prepare the HMAC key and padding.
		
		if (strlen($password) > 64) {
			$password = str_pad(sha1($password, true), 64, chr(0));
		} else {
			$password = str_pad($password, 64, chr(0));
		}
		
		$opad = str_repeat(chr(0x5C), 64) ^ $password;
		$ipad = str_repeat(chr(0x36), 64) ^ $password;
		
		// Hash it!
		
		$output = '';
		for ($i = 1; $i <= $block_count; $i++) {
			$last = $salt . pack('N', $i);
			$xorsum = $last = pack('H*', sha1($opad . pack('H*', sha1($ipad . $last))));
			for ($j = 1; $j < $count; $j++) {
				$last = pack('H*', sha1($opad . pack('H*', sha1($ipad . $last))));
				$xorsum ^= $last;
			}
			$output .= $xorsum;
		}
		
		// Truncate and return.
		
		return substr($output, 0, $key_length);
	}
?>