<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";
?>

<?
	//�ݿ��� ���� ���� ���(1:��~7:��)
	$BASIC_DOW = 5;

	//���� ��¥ �� ����
	$cur_date = date('Y-m-d');
	$cur_week = date("w");

	//���� �ۼ����� üũ �� �˾�(�ݿ��� üũ)
	if($cur_week == 4) 
	{
		//�������� ����
		$winfo = getWeekInfo($cur_date);

		$rs = chkWeekly($cur_date,"this");
		
		echo $rs;
	}
?>
