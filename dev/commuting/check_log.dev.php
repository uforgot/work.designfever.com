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
			$_datetime = "(미체크)"; 
		}

		return $_datetime;
	}

	function transGubun($gubun) {

		if($gubun) {
			if ($gubun == "10" || $gubun == "16" || $gubun == "17" || $gubun == "18") {			//휴가/프로젝트휴가/리프레시휴가/무급휴가
				$_gubun = "휴가";
			} else if ($gubun == "11") {	//병가
				$_gubun = "병가";
			} else if ($gubun == "12") {	//경조사
				$_gubun = "경조사";
			} else if ($gubun == "13" || $gubun == "20" || $gubun == "21") {	//기타/출산휴가/육아휴직
				$_gubun = "기타";
			} else if ($gubun == "14") {	//결근
				$_gubun = "결근";
			} else if ($gubun == "15") {	//교육
				$_gubun = "교육";
			} else if ($gubun == "19") {	//예비군
				$_gubun = "예비군";
			} else if ($gubun == "4" || $gubun == "8") {		//프로젝트 반차/반차 - 출근인정시간대 이후 출근 포함
				$_gubun = "반차";
			} else if ($gubun == "5" || $gubun == "9") {		//프로젝트 반차/반차 - 출근인정시간대 이후 출근 포함
				$_gubun = "반차"; 
			} else if ($gubun == "6") {		//외근
				$_gubun = "외근";
			} else if ($gubun == "1") {
				$_gubun = "출근";
			} else if ($gubun == "3") {
				$_gubun = "퇴근";
			}
		} else {
			$_gubun = ""; 
		}

		return $_gubun;
	}

	$PRS_ID = '57';

	// 수정요청건의 최종 데이터
	$sql = "SELECT a.MEMO ,b.* FROM DF_CHECKTIME_REQUEST a, DF_CHECKTIME b WHERE a.DATE = b.DATE AND a.PRS_ID = b.PRS_ID AND a.PRS_ID = '".$PRS_ID."' AND (a.DATE >= '2017-01-01') ORDER BY a.DATE DESC";
	$rs = sqlsrv_query($dbConn,$sql);
?>
	※ "<font color='blue'>(수정전)</font>"은 관리자가 어떤 수정을 하기 직전에 생성된 최초의 로그이며, <br>
	"<font color='red'>(수정후)</font>"는 경영지원팀에서 확인/조율하여 현재 최종 반영된 데이터 입니다.
	<br><br>
	<table border="1" cellspacing="0" cellpadding="5">
		<tr>
			<td>시점</td>
			<td>날짜</td>
			<td>구분</td>
			<td>출근</td>
			<td>구분</td>
			<td>퇴근</td>
			<td>수정사유</td>
			<td>최종처리</td>
		</tr>
<?
	$count = 0;
	while ($row = sqlsrv_fetch_array($rs))
	{
		// 수정요청건의 LOG 데이터
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
			$row2['MEMO'] = "(출퇴근 기록없음)";
			$CHECKTIME1_2 = "";
			$CHECKTIME2_2 = "";
		}

		if($count%2) $style = "background-color:#e6e6e6";
		else  $style = "";
?>
		<tr style="<?=$style?>">
			<td><font color="blue">(수정전)</font></td>
			<td><?=$row['DATE']?></td>
			<td><?=transGubun($row2['GUBUN1'])?></td>
			<td><?=$CHECKTIME1_2?></td>
			<td><?=transGubun($row2['GUBUN2'])?></td>
			<td><?=$CHECKTIME2_2?></td>
			<td><?=$row2['MEMO']?></td>
			<td></td>
		</tr>
		<tr style="<?=$style?>">
			<td><font color="red">(수정후)</font></td>
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

