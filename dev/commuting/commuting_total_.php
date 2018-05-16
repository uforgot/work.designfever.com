<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//���� üũ
	if ($prf_id != "4") 
	{ 
?>
	<script type="text/javascript">
		alert("�ش��������� �ӿ�,�����ڸ� Ȯ�� �����մϴ�.");
		location.href="commuting_list.php";
	</script>
<?
		exit;
	}

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; 

	$p_name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null; 
	$p_period = isset($_REQUEST['period']) ? $_REQUEST['period'] : "day"; 

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	$nameSQL = "";
	if ($p_name != "")
	{
		$nameSQL = " AND A.PRS_NAME Like '%". $p_name ."%'";
	}

	$team_login = "";
	$team_id = "";
	$team_name = "";
	$team_team = "";
	$team_position = "";
	$team_date = "";

	$per_page = 20;

	if ($p_period == "day")
	{
		$p_fr_year = isset($_REQUEST['fr_year']) ? $_REQUEST['fr_year'] : null; 
		$p_fr_month = isset($_REQUEST['fr_month']) ? $_REQUEST['fr_month'] : null; 
		$p_fr_day = isset($_REQUEST['fr_day']) ? $_REQUEST['fr_day'] : null; 
		$p_to_year = isset($_REQUEST['to_year']) ? $_REQUEST['to_year'] : null; 
		$p_to_month = isset($_REQUEST['to_month']) ? $_REQUEST['to_month'] : null; 
		$p_to_day = isset($_REQUEST['to_day']) ? $_REQUEST['to_day'] : null; 

		if ($p_fr_year == "") $p_fr_year = $nowYear;
		if ($p_fr_month == "") $p_fr_month = $nowMonth;
		if ($p_fr_day == "") $p_fr_day = $nowDay;
		if ($p_to_year == "") $p_to_year = $nowYear;
		if ($p_to_month == "") $p_to_month = $nowMonth;
		if ($p_to_day == "") $p_to_day = $nowDay;

		$fr_date = $p_fr_year ."-". $p_fr_month ."-". $p_fr_day;
		$to_date = $p_to_year ."-". $p_to_month ."-". $p_to_day;

		$sql = "SELECT 
					COUNT(*) 
				FROM 
					DF_PERSON A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
				ON 
					A.PRS_ID = B.PRS_ID 
				WHERE 
					A.PRF_ID IN (1,2,3,4)". $nameSQL ." AND A.PRS_ID NOT IN (102)
					AND B.DATE BETWEEN '$fr_date' AND '$to_date'";
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		$total_cnt = $record[0];

		$sql = "SELECT 
					T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION, T.DATE 
				FROM 
				(
					SELECT
						ROW_NUMBER() OVER(ORDER BY A.PRS_NAME, B.DATE) AS ROWNUM,
						A.PRS_LOGIN, A.PRS_ID, A.PRS_NAME, A.PRS_TEAM, A.PRS_POSITION, B.DATE 
					FROM 
						DF_PERSON A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK)
					ON
						A.PRS_ID = B.PRS_ID
					WHERE 
						A.PRF_ID IN (1,2,3,4) AND A.PRS_ID NOT IN (102) $nameSQL 
						AND B.DATE BETWEEN '$fr_date' AND '$to_date'
				) T
				WHERE
					T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	}
	else
	{
		$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
		$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 

		if ($p_year == "") $p_year = $nowYear;
		if ($p_month == "") $p_month = $nowMonth;

		if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

		$date = $p_year;
		if ($p_period == "month")
		{
			$date = $date ."-". $p_month;
		}
		$sql = "SELECT 
					COUNT(DISTINCT A.PRS_ID) 
				FROM 
					DF_PERSON A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK) 
				ON 
					A.PRS_ID = B.PRS_ID 
				WHERE 
					A.PRF_ID IN (1,2,3,4)". $nameSQL ." AND A.PRS_ID NOT IN (102)
					AND B.DATE LIKE '$date%'";
		$rs = sqlsrv_query($dbConn,$sql);

		$record = sqlsrv_fetch_array($rs);
		$total_cnt = $record[0];

		$sql = "SELECT 
					T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION 
				FROM 
				(
					SELECT
						ROW_NUMBER() OVER(ORDER BY A.PRS_NAME) AS ROWNUM,
						A.PRS_LOGIN, A.PRS_ID, A.PRS_NAME, A.PRS_TEAM, A.PRS_POSITION 
					FROM 
						DF_PERSON A WITH(NOLOCK) INNER JOIN DF_CHECKTIME B WITH(NOLOCK)
					ON
						A.PRS_ID = B.PRS_ID
					WHERE 
						A.PRF_ID IN (1,2,3,4) AND A.PRS_ID NOT IN (102) $nameSQL 
						AND B.DATE LIKE '$date%'
					GROUP BY 
						A.PRS_LOGIN, A.PRS_ID, A.PRS_NAME, A.PRS_TEAM, A.PRS_POSITION 
				) T
				WHERE
					T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	}
	$rs = sqlsrv_query($dbConn,$sql);

	while ($record = sqlsrv_fetch_array($rs))
	{
		$team_login = $team_login. $record['PRS_LOGIN'] . "##";
		$team_id = $team_id. $record['PRS_ID'] . "##";
		$team_name = $team_name. $record['PRS_NAME'] . "##";
		$team_team = $team_team. $record['PRS_TEAM'] . "##";
		$team_position = $team_position. $record['PRS_POSITION'] . "##";
		if ($p_period == "day") 
		{
			$team_date = $team_date. $record['DATE'] . "##";
		}
	}

	$team_login_ex = explode("##",$team_login);
	$team_id_ex = explode("##",$team_id);
	$team_name_ex = explode("##",$team_name);
	$team_team_ex = explode("##",$team_team);
	$team_position_ex = explode("##",$team_position);
	$team_date_ex = explode("##",$team_date);

?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function searchType(){
		var frm = document.form;
		if("team"==frm.type.value){
			location.href = "commuting_total_team.php";
		}else if("team"==frm.type.value){
			location.href = "commuting_total.php";
		}
	}    

	function sSubmit(f)
	{
		f.page.value = "1";
		f.target="_self";
		f.action = "commuting_total.php";
		f.submit();
	}

	function eSubmit(f)
	{
		if(event.keyCode ==13)
			sSubmit(f);
	}

	function excel_download()
	{
		var frm = document.form;
	<? if ($p_period == "month") { ?>
		frm.target = "hdnFrame";
		frm.action = "excel_total_month.php";
		frm.submit();
	<? } else { ?>
		frm.target = "hdnFrame";
		frm.action = "excel_total_year1.php";
		frm.submit();

		frm.target = "hdnFrame2";
		frm.action = "excel_total_year2.php";
		frm.submit();
	<? } ?>
	}
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<p class="hello work_list">
			<a href="commuting_list.php">+  ������Ȳ</a>
		<? if ($prf_id == "2" || $prf_id == "3" || $prf_id == "4") { ?>
			<a href="commuting_member.php">+  ���� ��Ȳ</a>
		<? } 
			if ($prf_id == "4") { ?>
			<a href="commuting_total.php"><strong>+  �������</strong></a>
		<? } ?>
			</p>

			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
					<table class="notable" width="100%" border=0>
						<tr>
							<th scope="row">�˻�</th>
							<td>
								<select name="type" onchange="javascript:searchType();" style="width:109px;">
	                         		<option value="person" selected>������</option>
	                         		<option value="team">�μ���</option>
								</select>
								<input type="text" name="name" style="width:109px; " value="<?=$p_name?>" onkeypress="eSubmit(this.form);">
								<select name="period"  onchange="javascript:sSubmit(this.form);" style="width:109px;"> 
									<option value="day"<? if ($p_period == "day") { echo " selected"; }?>>�Ϻ�</option>			
									<option value="month"<? if ($p_period == "month") { echo " selected"; }?>>����</option>			
									<option value="year"<? if ($p_period == "year") { echo " selected"; }?>>����</option>
								</select>
							<? if ($p_period == "day") { ?>
								<select name="fr_year" style="width:70px;">
								<?
									for ($i=2013; $i<=($nowYear+1); $i++) 
									{
										if ($i == $p_fr_year) 
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
								</select>��
								<select name="fr_month" style="width:70px;">
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

										echo "<option value='".$j."'".$selected.">".$i."</option>";
									}
								?>
								</select>��
								<select name="fr_day" style="width:70px;">
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

										echo "<option value='".$j."'".$selected.">".$i."</option>";
									}
								?>
								</select>��
								~&nbsp;
								<select name="to_year" style="width:70px;">
								<?
									for ($i=2013; $i<=($nowYear+1); $i++) 
									{
										if ($i == $p_to_year) 
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
								</select>��
								<select name="to_month" style="width:70px;">
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

										echo "<option value='".$j."'".$selected.">".$i."</option>";
									}
								?>
								</select>��
								<select name="to_day" style="width:70px;">
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

										echo "<option value='".$j."'".$selected.">".$i."</option>";
									}
								?>
								</select>��
							<? } else { ?>
								<select name="year" style="width:109px;">
								<?
									for ($i=2013; $i<=($nowYear+1); $i++) 
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
								</select>��
								<? if ($p_period == "month") { ?>
								<select name="month" style="width:109px;">
								<?
									for ($i=1; $i<=12; $i++) 
									{
										if ($i == $p_month) 
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
								</select>��
								<? } ?>
							<? } ?>
								<a href="javascript:sSubmit(this.form);"><img src="../img/btn_search.gif" alt="�˻�" /></a>
							</td>
							<td align="right">
							<? if ($p_period == "month" || $p_period == "year") { ?>
								<a href="javascript:excel_download();"><img src="../img/btn_excell.gif" alt="�����ٿ�ε�" /></a>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<? } ?>
							</td>
						</tr>
					</table>
				</div>
			<? if ($p_period == "day") { ?>
				<table class="notable work1 work_stats"  width="100%">
					<caption>������� �Ϻ� ���̺�</caption>
					<colgroup>
						<col width="5%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="15%" />
						<col width="7%" />
						<col width="7%" />
						<col width="17%" />
						<col width="8%" />
						<col width="8%" />
						<col width="9%" />
					</colgroup>
					<thead>
						<tr>
							<th>no.</th>
							<th>��¥</th>
							<th>�̸�</th>
							<th>����</th>
							<th>�μ�</th>
							<th>���</th>
							<th>���</th>
							<th>����</th>
							<th>�ѱٹ��ð�</th>
							<th>�ʰ��ٹ��ð�</th>
							<th>���</th>
						</tr>
					</thead>
					<tbody>
<?
	if (sizeof($team_id_ex) == 1)
	{
?>
						<tr>
							<td colspan="11" height="30" align="center">�˻��� �����Ͱ� �����ϴ�.</td>
						</tr>
<?
	}
	else
	{
		for ($i=0; $i<sizeof($team_id_ex); $i++)
		{

			$gubun1 = "";
			$gubun2 = "";
			$checktime1 = "";
			$checktime2 = "";
			$totaltime = "";
			$overtime = "";
			$undertime = "";
			$memo = "";

			$gubun1_ex = "";
			$gubun2_ex = "";
			$checktime1_ex = "";
			$checktime2_ex = "";
			$totaltime_ex = "";
			$overtime_ex = "";
			$undertime_ex = "";
			if ($team_id_ex[$i] != "")
			{

				$sql = "SELECT 
							GUBUN1, GUBUN2, CHECKTIME1, CHECKTIME2, TOTALTIME, OVERTIME, UNDERTIME 
						FROM 
							DF_CHECKTIME WITH(NOLOCK)
						WHERE 
							PRS_ID = '$team_id_ex[$i]' AND DATE = '$team_date_ex[$i]'";
				$rs = sqlsrv_query($dbConn,$sql);

				$record = sqlsrv_fetch_array($rs);
				if (sizeof($record) > 0)
				{
					$gubun1 = $record['GUBUN1'];				//��ٻ���
					$gubun2 = $record['GUBUN2'];				//��ٻ���
					$checktime1 = $record['CHECKTIME1'];		//��ٽð�
					$checktime2 = $record['CHECKTIME2'];		//��ٽð�
					$totaltime = $record['TOTALTIME'];			//�ѱٹ��ð�
					$overtime = $record['OVERTIME'];			//�ʰ��ٹ��ð�
					$undertime = $record['UNDERTIME'];			//�̸��ٹ��ð�
		
					if ($checktime1 == "") {
						$checktime1_ex = "-";
					} else { 
						$checktime1_ex = substr($checktime1,8,2) ." : ". substr($checktime1,10,2);				//��ٽð�
					}
					if ($checktime2 == "") {
						$checktime2_ex = "-";
					} else { 
						$checktime2_ex = substr($checktime2,8,2) ." : ". substr($checktime2,10,2);	//��ٽð�
					}

					$memo = "";		//���, �ѱٹ��ð�, �ʰ��ٹ��ð�
					if ($gubun1 == "10" || $gubun2 == "10") { $memo = "�ް�"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "11" || $gubun2 == "11") { $memo = "����"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "12" || $gubun2 == "12") { $memo = "������"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "13" || $gubun2 == "13") { $memo = "��Ÿ"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "14" || $gubun2 == "14") { $memo = "���"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "15" || $gubun2 == "15") { $memo = "����/�Ʒ�"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "16" || $gubun2 == "16") { $memo = "������Ʈ�ް�"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "17" || $gubun2 == "17") { $memo = "���������ް�"; $totaltime = "0000"; $overtime = "0000"; }
					if ($gubun1 == "18" || $gubun2 == "18") { $memo = "�����ް�"; $totaltime = "0000"; $overtime = "0000"; }

					if ($totaltime == "0000") {
						$totaltime_ex = "";
					} else {
						$totaltime_ex = substr($totaltime,0,2) .":". substr($totaltime,2,2);
					}
					if ($overtime == "0000") {
						$overtime_ex = "";
					} else {
						$overtime_ex = substr($overtime,0,2) .":". substr($overtime,2,2);
					}
					if ($undertime == "0000") {
						$undertime_ex = "";
					} else {
						$undertime_ex = substr($undertime,0,2) .":". substr($undertime,2,2);
					}

					//��ٻ���
					switch($gubun1)
					{
						case "1" :
							$gubun1_ex = "���";
							break;
						case "5" :
							$gubun1_ex = "����";
							break;
						case "6" :
							$gubun1_ex = "�ܱ�";
							break;
						case "8" :
							$gubun1_ex = "����";
							break;
						case "10" :
							$gubun1_ex = "�ް�";
							$checktime1_ex = "-";
							break;
						case "11" :
							$gubun1_ex = "����";
							$checktime1_ex = "-";
							break;
						case "12" :
							$gubun1_ex = "������";
							$checktime1_ex = "-";
							break;
						case "13" :
							$gubun1_ex = "��Ÿ";
							$checktime1_ex = "-";
							break;
						case "14" :
							$gubun1_ex = "���";
							$checktime1_ex = "-";
							break;
						case "15" :
							$gubun1_ex = "����/�Ʒ�";
							$checktime1_ex = "-";
							break;
						case "16" :
							$gubun1_ex = "������Ʈ�ް�";
							$checktime1_ex = "-";
							break;
						case "17" :
							$gubun1_ex = "���������ް�";
							$checktime1_ex = "-";
							break;
						case "18" :
							$gubun1_ex = "�����ް�";
							$checktime1_ex = "-";
							break;
						default : 
							$gubun1_ex = "";
							break;
					}

					//��ٻ���
					switch($gubun2)
					{
						case "2" :
							$gubun2_ex = "���";
							break;
						case "3" :
							$gubun2_ex = "����ٹ�";
							break;
						case "6" :
							$gubun2_ex = "�ܱ�";
							break;
						case "8" :
							$gubun2_ex = "����";
							break;
						case "10" :
							$gubun2_ex = "�ް�";
							$checktime2_ex = "-";
							break;
						case "11" :
							$gubun2_ex = "����";
							$checktime2_ex = "-";
							break;
						case "12" :
							$gubun2_ex = "������";
							$checktime2_ex = "-";
							break;
						case "13" :
							$gubun2_ex = "��Ÿ";
							$checktime2_ex = "-";
							break;
						case "14" :
							$gubun2_ex = "���";
							$checktime2_ex = "-";
							break;
						case "15" :
							$gubun2_ex = "����/�Ʒ�";
							$checktime2_ex = "-";
							break;
						case "16" :
							$gubun2_ex = "������Ʈ�ް�";
							$checktime2_ex = "-";
							break;
						case "17" :
							$gubun2_ex = "���������ް�";
							$checktime2_ex = "-";
							break;
						case "18" :
							$gubun2_ex = "�����ް�";
							$checktime2_ex = "-";
							break;
						default : 
							$gubun2_ex = "";
							break;
					}
				}
?>
						<tr>
							<td><?=($page-1)*$per_page+($i+1)?></td>
							<td><?=$team_date_ex[$i]?></td>
							<td class="bold"><?=$team_name_ex[$i]?></td>
							<td><?=$team_position_ex[$i]?></td>
							<td><?=$team_team_ex[$i]?></td>
							<td><?=$checktime1_ex?></td>
							<td><?=$checktime2_ex?></td>
							<td class="bold"><?=$gubun1_ex?> / <strong class="color_o"><?=$gubun2_ex?></strong></td>
							<td class="bold"><?=$totaltime_ex?></td>
							<td class="bold"><?=$overtime_ex?></td>
							<td><?=$memo?></td>
						</tr>
<?
			}
		}
	}
?>
					</tbody>
				</table>
			<? } else { ?>
				<table class="notable work1 work_stats"  width="100%">
					<caption>������� ����/���� ���̺�</caption>
					<colgroup>
						<col width="5%" />
						<col width="6%" />
						<col width="6%" />
						<col width="15%" />
						<col width="7%" />
						<col width="6%" />
						<col width="6%" />
						<col width="8%" />
						<col width="8%" />
						<col width="8%" />
						<col width="9%" />
						<col width="9%" />
						<col width="7%" />
						
					</colgroup>
					<thead>
						<tr>
							<th>no.</th>
							<th>�̸�</th>
							<th>����</th>
							<th>�μ�</th>
							<th>�������</th>
							<th>�ް�</th>
							<th>����</th>
							<th>�����ٽð�</th>
							<th>�����ٽð�</th>
							<th>��ձٹ��ð�</th>
							<th>�ѱٹ��ð�</th>
							<th>���ʰ��ٹ��ð�</th>
							<th>���ʰ��ϼ�</th>
						</tr>
					</thead>
					<tbody>
<?
	if (sizeof($team_id_ex) == 1)
	{
?>
						<tr>
							<td colspan="11" height="30" align="center">�˻��� �����Ͱ� �����ϴ�.</td>
						</tr>
<?
	}
	else
	{
		for ($i=0; $i<sizeof($team_id_ex); $i++)
		{
			$commute_count = "";
			$lateness_count = "";
			$vacation_count = "";
			$commute_day = "";
			$subvacation_count = "";
			$avgtime1 = "";
			$avgminute1 = "";
			$avgtime2 = "";
			$avgminute2 = "";
			$total_time = "";
			$total_minute = "";
			$over_time = "";
			$over_minute = "";
			$over_day = "";
			if ($team_id_ex[$i] != "")
			{

				$sql = "EXEC SP_COMMUTING_LIST_01 '$team_id_ex[$i]','$date'";
				$rs = sqlsrv_query($dbConn,$sql);

				$record = sqlsrv_fetch_array($rs);
				if (sizeof($record) > 0)
				{
					$commute_count = $record['COMMUTE'];			//�������
					$biz_commute_count = $record['BIZ_COMMUTE'];	//���� �������
					$lateness_count = $record['LATENESS'];			//����
					$vacation_count = $record['VACATION'];			//�ް�
					$commute_day = $record['COMMUTE_DATE'];			//�ٹ��ϼ�
					$subvacation1_count = $record['SUBVACATION1'];	//��������
					$subvacation2_count = $record['SUBVACATION2'];	//���Ĺ���
					$avgtime1 = $record['AVGTIME1'];				//�����ٽ�
					$avgminute1 = $record['AVGMINUTE1'];			//�����ٺ�
					$avgtime2 = $record['AVGTIME2'];				//�����ٽ�
					$avgminute2 = $record['AVGMINUTE2'];			//�����ٺ�
					$total_time = $record['TOTAL_TIME'];			//�ѱٹ��ð���
					$total_minute = $record['TOTAL_MINUTE'];		//�ѱٹ��ð���
					$over_time = $record['OVER_TIME'];				//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
					$over_minute = $record['OVER_MINUTE'];			//�ʰ��ٹ��ð��� - �Ϸ� 9�ð� �̻� �ٹ��� ������ ���� �� ���սð�
					$over_day = $record['OVER_DATE'];				//�ʰ��ٹ���

					$subvacation_count = $subvacation1_count + $subvacation2_count;

					if ($avgtime1 == "") { $avgtime1 = "0"; }
					if ($avgminute1 == "") { $avgminute1 = "0"; }
					if ($avgtime2 == "") { $avgtime2 = "0"; }
					if ($avgminute2 == "") { $avgminute2 = "0"; }
					if ($total_time == "") { $total_time = "0"; }
					if ($total_minute == "") { $total_minute = "0"; }
					if ($over_time == "") { $over_time = "0"; }
					if ($over_minute == "") { $over_minute = "0"; }

					if (strlen($avgtime1) == 1) { $avgtime1 = "0".$avgtime1; }
					if (strlen($avgminute1) == 1) { $avgminute1 = "0".$avgminute1; }
					if (strlen($avgtime2) == 1) { $avgtime2 = "0".$avgtime2; }
					if (strlen($avgminute2) == 1) { $avgminute2 = "0".$avgminute2; }
					if (strlen($total_time) == 1) { $total_time = "0".$total_time; }
					if (strlen($total_minute) == 1) { $total_minute = "0".$total_minute; }
					if (strlen($over_time) == 1) { $over_time = "0".$over_time; }
					if (strlen($over_minute) == 1) { $over_minute = "0".$over_minute; }
				}
?>
						<tr>
							<td><?=($page-1)*$per_page+($i+1)?></td>
							<td class="bold"><?=$team_name_ex[$i]?></td>
							<td><?=$team_position_ex[$i]?></td>
							<td><?=$team_team_ex[$i]?></td>
							<td class="bold"><?=$commute_count?></td>
							<td class="bold"><?=$vacation_count?></td>
							<td class="bold"><?=$subvacation_count?></td>
							<td><?=$avgtime1?> : <?=$avgminute1?></td>
							<td><?=$avgtime2?> : <?=$avgminute2?></td>
							<td>
							<?
								if ($avgtime1 == "00" && $avgminute1 == "00" && $avgtime2 == "00" && $avgminute2 == "00")
								{
									echo "00 : 00";
								}
								else
								{
									$atime1 = mktime($avgtime1,$avgminute1,"00");
									$atime2 = mktime($avgtime2,$avgminute2,"00");

									$avg_sec = abs($atime2-$atime1);
									
									$avg1 = intval($avg_sec/3600);
									if (strlen($avg1) == "1") { $avg1 = "0".$avg1; }
									$avg2 = ($avg_sec%3600) / 60;
									if (strlen($avg2) == "1") { $avg2 = "0".$avg2; }

									echo $avg1." : ".$avg2;
								}
							?>
							</td>
							<td><?=$total_time?> : <?=$total_minute?></td>
							<td>
							<!--$over_time : $over_minute-->
							<?
								$base_time = ($biz_commute_count * 9) + ($subvacation1_count * 5) + ($subvacation2_count * 3);
								$base = $base_time ."00";
								$total = $total_time . $total_minute;
								
								if ($total >= $base)
								{
									$over_time = $total_time - $base_time;

									if (strlen($over_time) == "1") { $over_time = "0".$over_time; }
									echo $over_time ." : ". $total_minute;
								}
								else
								{
									$under_time = $base_time - $total_time;
									$under_minute = 60 - $total_minute;

									if ($under_minute == 60)
									{
										$under_minute = "00";
									}
									else
									{
										$under_time = $under_time - 1;
									}

									if (strlen($under_time) == "1") { $under_time = "0".$under_time; }
									if (strlen($under_minute) == "1") { $under_minute = "0".$under_minute; }
									echo "-". $under_time ." : ". $under_minute;
								}

							?>
							</td>
							<td><?=$over_day?></td>
						</tr>
<?
			}
		}
	}
?>
					</tbody>
				</table>
			<? } ?>
				<div class="page_num">
				<?=getPaging($total_cnt,$page,$per_page);?>
				</div>
			</div>
		</div>
</form>
<? include INC_PATH."/bottom.php"; ?>
</div>
</body>
</html>
