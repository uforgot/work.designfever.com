<?
	//�ݿ��� ���� ���� ���(1:��~7:��)
	$BASIC_DOW = 1;

	//���� ��¥ �� ����
	$cur_date = date('Y-m-d');
	$cur_week = date("w");

	/*/����� ��¥ ����
	if ($prs_id == 85) {
		$cur_date = date('2016-10-28');
		$cur_week = 5;
	}
	*/

	$ndate = $cur_date;

	//�������� ����
	$winfo = getWeekInfo($ndate);
	/*
	if ($prs_id == 85) {
		echo "�� ����: ".$winfo["tot_week"]."<br>";
		echo "�� ����: ".$winfo["cur_week"]."<br>";
		echo "�� ��: ".$winfo["str_week"]."<br>";
		//exit;
	}
	*/
	
	//�α����� �Ǿ� �ִ� ���
	if ($prs_login) {
		//�������� ����
		if (in_array($prs_position,$positionB_arr) && $prs_login != 'dfadmin') {
			//�ְ����� �⺻������ üũ �� ó��
			if (!$log_weekly_create || $log_weekly_create < $winfo["cur_week"]) 
			{
				$rs = setWeeklyData($winfo);

				if ($rs == false)
				{
	?>
				<script language="javascript">
					alert("ó���� ���� �Ͽ����ϴ�. �������� ������ �ּ���.");
					history.back();
				</script>
	<?
				exit;
				}
			}

			//���� ���� �ۼ����� üũ �� �˾�(������ üũ)
			if ($cur_week <= 1 || $cur_week >= 6)
//			if($cur_week == 1) 
			{
				$rs = chkWeekly($cur_date,"prev");
				
				if ($rs == false)
				{
	?>
	<!--
				<script language="javascript">
					alert("�� ������ �ְ������� �ۼ����� �����̽��ϴ�.\n�ż��ϰ� �ۼ��� �ֽñ� �ٶ��ϴ�.");
				</script>
	-->					
	<?
				}
			}
		} else {
			// ����� ���� �ְ����� �޴����� �Ǹ���Ʈ�� �̵�
			if ($_SERVER["PHP_SELF"] == "/weekly/weekly_list.php") {
				echo "<script> parent.location.href = './weekly_list_team.php'; </script>";
				exit;
			}
		}
	}
?>
