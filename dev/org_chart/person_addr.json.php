<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	header("Content-Type: application/json; charset=EUC-KR");

	$address = array();

/*	
	$members_ceo = array();
	$members_cso = array();
	$members_cco = array();
	$members_bs = array();
	$members_dm = array();
	$members_bx = array();
	$members_d1 = array();
	$members_d2 = array();
	$members_mg = array();
	$members_ix = array();
*/

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION2_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_POSITION2 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION1_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION1 ='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, PRS_NAME";

	$where = " WHERE PRF_ID IN (1,2,3,4,5)";

	$sql = "SELECT 
				PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION1, PRS_POSITION2, PRS_TEL, PRS_EXTENSION, PRS_MOBILE, PRS_EMAIL
			FROM DF_PERSON "
			. $where ." AND PRS_TEAM = 'CEO'"
			. $orderbycase;
	$rs = sqlsrv_query($dbConn, $sql);

	$i = 0;
	$members = "members_CEO";
	$members = array();
	While ($record = sqlsrv_fetch_array($rs))
	{
		$prs_login = urlencode($record['PRS_LOGIN']);
		$prs_name = urlencode($record['PRS_NAME']);
		$prs_team = urlencode($record['PRS_TEAM']);
		$prs_position1 = urlencode($record['PRS_POSITION1']);
		$prs_position2 = urlencode($record['PRS_POSITION2']);
		$prs_tel = $record['PRS_TEL'];
		$prs_extension = $record['PRS_EXTENSION'];
		$prs_mobile = $record['PRS_MOBILE'];
		$prs_email = $record['PRS_EMAIL'] ."@designfever.com";

		$prs_division = $prs_team;
		$prs_position = $prs_position2 ."/". $prs_position1;

		array_push($members,array("name"=>$prs_name,"division"=>$prs_division, "team"=>$prs_team,"position"=>$prs_position,"tel"=>$prs_mobile,"mail_addr"=>$prs_email,"ext_tel"=>$prs_extension,"direct_tel"=>$prs_tel));

		$i++;
	}

	array_push($address,array("group_name"=>"CEO", "members"=>$members));


	$TeamSql = "SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE STEP > 1 ORDER BY SORT";
	$TeamRs = sqlsrv_query($dbConn,$TeamSql);

	while($TeamRecord=sqlsrv_fetch_array($TeamRs))
	{
		$team = $TeamRecord['TEAM'];

		if ($team == "Design 1 Division")
		{
			$sql = "SELECT 
						PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION1, PRS_POSITION2, PRS_TEL, PRS_EXTENSION, PRS_MOBILE, PRS_EMAIL
					FROM DF_PERSON 
					WHERE PRS_TEAM = 'CEO' AND PRS_NAME = '박재형'";
			$rs = sqlsrv_query($dbConn, $sql);

			$i = 0;
			$members = "members_". $team;
			$members = array();
			While ($record = sqlsrv_fetch_array($rs))
			{
				$prs_login = urlencode($record['PRS_LOGIN']);
				$prs_name = urlencode($record['PRS_NAME']);
				$prs_team = urlencode($record['PRS_TEAM']);
				$prs_position1 = urlencode($record['PRS_POSITION1']);
				$prs_position2 = urlencode($record['PRS_POSITION2']);
				$prs_tel = $record['PRS_TEL'];
				$prs_extension = $record['PRS_EXTENSION'];
				$prs_mobile = $record['PRS_MOBILE'];
				$prs_email = $record['PRS_EMAIL'] ."@designfever.com";

				$prs_division = $prs_team;
				$prs_position = $prs_position2 ."/". $prs_position1;

				array_push($members,array("name"=>$prs_name,"division"=>$prs_division, "team"=>$prs_team,"position"=>$prs_position,"tel"=>$prs_mobile,"mail_addr"=>$prs_email,"ext_tel"=>$prs_extension,"direct_tel"=>$prs_tel));

				$i++;
			}
		}
		else
		{
			$sql = "SELECT 
						PRS_LOGIN, PRS_NAME, PRS_TEAM, PRS_POSITION1, PRS_POSITION2, PRS_TEL, PRS_EXTENSION, PRS_MOBILE, PRS_EMAIL
					FROM DF_PERSON "
					. $where ." AND PRS_TEAM = '$team'"
					. $orderbycase;
			$rs = sqlsrv_query($dbConn, $sql);

			$i = 0;
			$members = "members_". $team;
			$members = array();
			While ($record = sqlsrv_fetch_array($rs))
			{
				$prs_login = urlencode($record['PRS_LOGIN']);
				$prs_name = urlencode($record['PRS_NAME']);
				$prs_team = urlencode($record['PRS_TEAM']);
				$prs_position1 = urlencode($record['PRS_POSITION1']);
				$prs_position2 = urlencode($record['PRS_POSITION2']);
				$prs_tel = $record['PRS_TEL'];
				$prs_extension = $record['PRS_EXTENSION'];
				$prs_mobile = $record['PRS_MOBILE'];
				$prs_email = $record['PRS_EMAIL'] ."@designfever.com";

				$prs_division = $prs_team;
				$prs_position = $prs_position2 ."/". $prs_position1;

				array_push($members,array("name"=>$prs_name,"division"=>$prs_division, "team"=>$prs_team,"position"=>$prs_position,"tel"=>$prs_mobile,"mail_addr"=>$prs_email,"ext_tel"=>$prs_extension,"direct_tel"=>$prs_tel));

				$i++;
			}
		}

		array_push($address,array("group_name"=>urlencode($team), "members"=>$members));
	}

	$members = "members_경비실";
	$members = array();
	array_push($members,array("name"=>urlencode("소장님"),"division"=>"", "team"=>urlencode("경비실"),"position"=>"","tel"=>"","mail_addr"=>"","ext_tel"=>"313","direct_tel"=>""));
	array_push($address,array("group_name"=>urlencode("경비실"), "members"=>$members));

	$output = json_encode(array("address"=>$address));

	echo urldecode($output);

	exit;
?>