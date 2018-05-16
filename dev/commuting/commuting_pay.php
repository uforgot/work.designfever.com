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

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y"); 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date("m"); 
	$p_team = isset($_REQUEST['team']) ? $_REQUEST['team'] : null; 

	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

	$date = $p_year ."-". $p_month;

	$sql = "SELECT SORT, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
	$rs = sqlsrv_query($dbConn,$sql);

	$i = 1;
	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN P.PRS_TEAM='". $record['TEAM'] ."' THEN ". $i ." ";
		$i++;
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN P.PRS_POSITION='". $record['POSITION'] ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, P.PRS_NAME";

	if ($p_team != "")
	{
		$teamSQL = " AND P.PRS_ID IN (SELECT PRS_ID FROM DF_PERSON WHERE PRS_TEAM IN (SELECT TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team' OR R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team') OR R_SEQNO IN (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE R_SEQNO = (SELECT SEQNO FROM DF_TEAM_2018 WITH(NOLOCK) WHERE TEAM = '$p_team'))))";
	}

	$sql = "SELECT 
				COUNT(DISTINCT PRS_ID) 
			FROM 
				DF_PERSON P WITH(NOLOCK)
			WHERE 
				PRF_ID IN (1,2,3,4,5) $teamSQL AND PRS_ID NOT IN (15,22,24,87,102,148)";
	$rs = sqlsrv_query($dbConn,$sql);

	$record = sqlsrv_fetch_array($rs);
	$total_cnt = $record[0];

	$per_page = 20;

	$sql = "SELECT 
				T.PRS_LOGIN, T.PRS_ID, T.PRS_NAME, T.PRS_TEAM, T.PRS_POSITION
			FROM 
			(
				SELECT
					ROW_NUMBER() OVER($orderbycase) AS ROWNUM,
					P.PRS_LOGIN, P.PRS_ID, P.PRS_NAME, P.PRS_TEAM, P.PRS_POSITION
				FROM 
					DF_PERSON P WITH(NOLOCK)
				WHERE 
					P.PRF_ID IN (1,2,3,4,5) $teamSQL AND P.PRS_ID NOT IN (15,22,24,87,102,148)
			) T
			WHERE
				T.ROWNUM BETWEEN(($page-1) * $per_page)+1 AND ($page * $per_page)";
	$rs = sqlsrv_query($dbConn, $sql);
	while ($record=sqlsrv_fetch_array($rs))
	{
		$arr_id .= $record['PRS_ID'] ."##";
		$arr_name .= $record['PRS_NAME'] ."##";
		$arr_team .= $record['PRS_TEAM'] ."##";
		$arr_position .= $record['PRS_POSITION'] ."##";
	}

	$id_ex = explode("##",$arr_id);
	$name_ex = explode("##",$arr_name);
	$team_ex = explode("##",$arr_team);
	$position_ex = explode("##",$arr_position);
?>

<? include INC_PATH."/top.php"; ?>

<script type="text/javascript">
	function sSubmit(f)
	{
		f.target="_self";
		f.page.value = "1";
		f.action = "<?=CURRENT_URL?>";
		f.submit();
	}

	function excel_download()
	{
		var frm = document.form;
		frm.target = "hdnFrame";
		frm.action = "excel_pay.php";
		frm.submit();
	}
</script>
</head>

<body>
<div class="wrapper">
<form method="post" name="form">
<input type="hidden" name="page" value="<?=$page?>">
	<? include INC_PATH."/top_menu.php"; ?>

		<div class="inner-home">
			<? include INC_PATH."/commuting_menu.php"; ?>

			<div class="work_wrap clearfix">
				<div class="work_stats_search clearfix">
					<table class="notable" width="100%">
						<tr>
							<th scope="row">�˻�</th>
							<td>
								<select name="team" onChange="sSubmit(this.form)">			
									<option value=""<? if ($p_team == ""){ echo " selected"; } ?>>������</option>
							<?
								$selSQL = "SELECT STEP, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
								$selRs = sqlsrv_query($dbConn,$selSQL);

								while ($selRecord = sqlsrv_fetch_array($selRs))
								{
									$selStep = $selRecord['STEP'];
									$selTeam = $selRecord['TEAM'];
									
									if ($selStep == 2) {
										$selTeam2 = $selTeam;
									}
									else if ($selStep == 3) {
										$selTeam2 = "&nbsp;&nbsp;�� ". $selTeam;
									}

							?>
									<option value="<?=$selTeam?>"<? if ($p_team == $selTeam){ echo " selected"; } ?>><?=$selTeam2?></option>
							<?
								}
							?>
								</select>
								<select name="year" value="<?=$p_year?>" style="width:109px;" onChange='sSubmit(this.form);'>
								<?
									for ($i=$startYear; $i<=($nowYear); $i++) 
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
								<select name="month" value="<?=$p_month?>" style="width:109px;" onChange='sSubmit(this.form);'>
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
								</select>��
								<a href="javascript:sSubmit(this.form);"><img src="../img/btn_search.gif" alt="�˻�" /></a>
							</td>
							<td align="right">
								<a href="javascript:excel_download();"><img src="../img/btn_excell.gif" alt="�����ٿ�ε�" /></a>
							</td>
						</tr>
					</table>
				</div>
				<table class="notable work1 work_stats"  width="100%">
					<caption>������� ���̺�</caption>
					<colgroup>
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
						<col width="*" />
						<col width="10%"/>
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
						<col width="10%" />
					</colgroup>
					<thead>
						<tr>
							<th>no.</th>
							<th>�̸�</th>
							<th>����</th>
							<th>�μ�</th>
							<th>���ɽĺ�</th>
							<th>����ĺ�</th>
							<th>���ĺ�</th>
							<th>�İ߱����</th>
							<th>���� �հ�</th>
						</tr>
					</thead>
					<tbody>
<?
	$no = $total_cnt-($page-1)*$per_page;

	for ($i=0; $i<sizeof($id_ex); $i++)
	{
		if ($id_ex[$i] != "")
		{
			$sql = "SELECT 
						T.PAY1, T.PAY2, T.PAY3, T.PAY4, T.PAY5, T.PAY6
					FROM 
					(
						SELECT
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY1 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY1, --���ɽĺ�
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY2 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY2, --����ĺ�
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY3 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY3, --���ĺ�
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY4 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY4, --�����
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY5 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY5, --�İ߱����(���)
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY6 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY6 --�İ߱����(���)
						FROM 
							DF_PERSON P WITH(NOLOCK)
						WHERE
							P.PRS_ID = $id_ex[$i]
					) T";
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);
			if (sizeof($record) > 0)
			{
				$pay1 = $record['PAY1'];	//���ɽĺ�
				$pay2 = $record['PAY2'];	//����ĺ�
				$pay3 = $record['PAY3'];	//���ĺ�
				$pay4 = $record['PAY4'];	//�����
				$pay5 = $record['PAY5'];	//�İ߱����(���)
				$pay6 = $record['PAY6'];	//�İ߱����(���)

				$pay_t = $pay5 + $pay6;

				if ($pay1+$pay2+$pay3+$pay5+$pay6 > 0)
				{
					$pay_total = "\\". number_format($pay1*6000+$pay2*6000+$pay3*3000+$pay5*2000+$pay6*2000);
				}
				else
				{
					$pay_total = "";
				}
?>
						<tr>
							<td><?=$no?></td>
							<td class="bold"><?=$name_ex[$i]?></td>
							<td><?=$position_ex[$i]?></td>
							<td><?=$team_ex[$i]?></td>
							<td><?=$pay1?></td>
							<td><?=$pay2?></td>
							<td><?=$pay3?></td>
							<td><?=$pay5+$pay6?></td>
							<td>
							<?
								if ($pay1+$pay2+$pay3+$pay5+$pay6 > 0)
								{
									//echo "\\". number_format($pay1*6000+$pay2*6000+$pay3*3000);
									echo "\\". number_format($pay1*6000+$pay2*6000+$pay3*3000+$pay5*2000+$pay6*2000);
								}
							?>
							</td>
						</tr>
<?
			}
		}
		$no--;
	}
?>
					</tbody>
				</table>
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