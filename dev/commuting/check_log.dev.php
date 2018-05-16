<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";

	function transDatetime($datetime) {

		if($datetime) {
			$Y = substr($datetime, 0,4);
			$M = substr($datetime, 4,2);
			$D = substr($datetime, 6,2);
			$H = substr($datetime, 8,2);
			$I = substr($datetime, 10,2);

			$_datetime = $Y."-".$M."-".$D." ".$H.":".$I; 
		} else {
			$_datetime = "(��üũ)"; 
		}

		return $_datetime;
	}

	function transGubun($gubun) {

		if($gubun) {
			if ($gubun == "10" || $gubun == "16" || $gubun == "17" || $gubun == "18") {			//�ް�/������Ʈ�ް�/���������ް�/�����ް�
				$_gubun = "�ް�";
			} else if ($gubun == "11") {	//����
				$_gubun = "����";
			} else if ($gubun == "12") {	//������
				$_gubun = "������";
			} else if ($gubun == "13" || $gubun == "20" || $gubun == "21") {	//��Ÿ/����ް�/��������
				$_gubun = "��Ÿ";
			} else if ($gubun == "14") {	//���
				$_gubun = "���";
			} else if ($gubun == "15") {	//����
				$_gubun = "����";
			} else if ($gubun == "19") {	//����
				$_gubun = "����";
			} else if ($gubun == "4" || $gubun == "8") {		//������Ʈ ����/���� - ��������ð��� ���� ��� ����
				$_gubun = "����";
			} else if ($gubun == "5" || $gubun == "9") {		//������Ʈ ����/���� - ��������ð��� ���� ��� ����
				$_gubun = "����"; 
			} else if ($gubun == "6") {		//�ܱ�
				$_gubun = "�ܱ�";
			} else if ($gubun == "1") {
				$_gubun = "���";
			} else if ($gubun == "3") {
				$_gubun = "���";
			}
		} else {
			$_gubun = ""; 
		}

		return $_gubun;
	}

	$PRS_ID = '57';

	// ������û���� ���� ������
	$sql = "SELECT a.MEMO ,b.* FROM DF_CHECKTIME_REQUEST a, DF_CHECKTIME b WHERE a.DATE = b.DATE AND a.PRS_ID = b.PRS_ID AND a.PRS_ID = '".$PRS_ID."' AND (a.DATE >= '2017-01-01') ORDER BY a.DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);
?>
	�� "<font color='blue'>(������)</font>"�� �����ڰ� � ������ �ϱ� ������ ������ ������ �α��̸�, <br>
	"<font color='red'>(������)</font>"�� �濵���������� Ȯ��/�����Ͽ� ���� ���� �ݿ��� ������ �Դϴ�.
	<br><br>
	<table border="1" cellspacing="0" cellpadding="5">
		<tr>
			<td>����</td>
			<td>��¥</td>
			<td>����</td>
			<td>���</td>
			<td>����</td>
			<td>���</td>
			<td>��������</td>
			<td>����ó��</td>
		</tr>
<?
	$count = 0;
	while ($row = sqlsrv_fetch_array($rs))
	{
		// ������û���� LOG ������
		$sql2 = "SELECT * FROM DF_CHECKTIME_LOG WHERE DATE = '".$row['DATE']."' AND PRS_ID = '".$PRS_ID."' ORDER BY REGDATE";
		$rs2 = sqlsrv_query($dbConn,$sql2);
		$row2 = sqlsrv_fetch_array($rs2);


		if(!$row['CHECKTIME1']) $CHECKTIME1 = "";
		else $CHECKTIME1 = date('Y-m-d H:i',strtotime($row['CHECKTIME1']));

		if(!$row2['CHECKTIME1']) $CHECKTIME1_2 = "";
		else $CHECKTIME1_2 = date('Y-m-d H:i',strtotime($row2['CHECKTIME1']));

		$CHECKTIME2 = transDatetime($row['CHECKTIME2']);
		$CHECKTIME2_2 = transDatetime($row2['CHECKTIME2']);
		
		if(!$row2) {
			$row2['MEMO'] = "(����� ��Ͼ���)";
			$CHECKTIME1_2 = "";
			$CHECKTIME2_2 = "";
		}

		if($count%2) $style = "background-color:#e6e6e6";
		else  $style = "";
?>
		<tr style="<?=$style?>">
			<td><font color="blue">(������)</font></td>
			<td><?=$row['DATE']?></td>
			<td><?=transGubun($row2['GUBUN1'])?></td>
			<td><?=$CHECKTIME1_2?></td>
			<td><?=transGubun($row2['GUBUN2'])?></td>
			<td><?=$CHECKTIME2_2?></td>
			<td><?=$row2['MEMO']?></td>
			<td></td>
		</tr>
		<tr style="<?=$style?>">
			<td><font color="red">(������)</font></td>
			<td><?=$row['DATE']?></td>
			<td><?=transGubun($row['GUBUN1'])?></td>
			<td><?=$CHECKTIME1?></td>
			<td><?=transGubun($row['GUBUN2'])?></td>
			<td><?=$CHECKTIME2?></td>
			<td><?=$row['MEMO']?></td>
			<td><?=$row['MEMO1']?></td>
		</tr>

<?
		$count++;
	}
?>
	</table>
	<br>
<?
	echo "Total: ".$count;
?>

