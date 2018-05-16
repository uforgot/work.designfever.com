<?
	require_once $_SERVER['DOCUMENT_ROOT']."/common/global.php";
	require_once CMN_PATH."/login_check.php";

	//권한 체크
	if ($prf_id != "4") 
	{
?>		
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
	<script type="text/javascript">
		alert("해당페이지는 임원,관리자만 확인 가능합니다.");
	</script>
<?
		exit;
	}

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/London');

	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

	/** Include PHPExcel */
	require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	$sheet = $objPHPExcel->getActiveSheet();
	$sheet->getDefaultStyle()->getFont()->setName('돋움')->setSize(10);
	 
	$sheet->getStyle("1")->getFont()->setBold(true); 
	$sheet->getStyle("1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 

	$sheet->getColumnDimension('A')->setWidth(5);	
	$sheet->getColumnDimension('B')->setWidth(15);	
	$sheet->getColumnDimension('C')->setWidth(10);	
	$sheet->getColumnDimension('D')->setAutoSize(true);		
	$sheet->getColumnDimension('E')->setWidth(10);	
	$sheet->getColumnDimension('F')->setWidth(10);
	$sheet->getColumnDimension('G')->setWidth(10);
	$sheet->getColumnDimension('H')->setWidth(15);
	$sheet->getColumnDimension('I')->setAutoSize(true);

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1','no.')
				->setCellValue('B1','이름')
				->setCellValue('C1','직급')
				->setCellValue('D1','부서')
				->setCellValue('E1','점심식비')
				->setCellValue('F1','저녁식비')
				->setCellValue('G1','간식비')
				->setCellValue('H1','파견교통비')
				->setCellValue('I1','수당합계');

	$nowYear = date("Y");
	$nowMonth = date("m");

	$p_year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null; 
	$p_month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null; 

	if ($p_year == "") $p_year = $nowYear;
	if ($p_month == "") $p_month = $nowMonth;

	if (strlen($p_month) == "1") { $p_month = "0".$p_month; }

	$date = $p_year."-". $p_month;

	$orderby1 = "";
	$orderby2 = "";
	$orderbycase = "";

	$sql = "SELECT SORT, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) WHERE VIEW_YN = 'Y' ORDER BY SORT";
	$sql = iconv('UTF-8','EUC-KR',$sql);
	$rs = sqlsrv_query($dbConn,$sql);

	$i = 1;
	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_TEAM='". iconv('EUC-KR','UTF-8',$record['TEAM']) ."' THEN ". $i ." ";
		$i++;
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION_CODE WITH(NOLOCK) ORDER BY SEQNO";
	$sql = iconv('UTF-8','EUC-KR',$sql);
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION='". iconv('EUC-KR','UTF-8',$record['POSITION']) ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, PRS_NAME";

	$id = "";
	$name = "";
	$team = "";
	$position = "";

	$sql = "SELECT 
				PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE 
				PRF_ID IN (1,2,3,4,5) AND PRS_ID NOT IN (15,22,24,87,102,148)". $orderbycase;
	$sql= iconv('UTF-8','EUC-KR',$sql);
	$rs = sqlsrv_query($dbConn, $sql);

	while ($record=sqlsrv_fetch_array($rs))
	{
		$id = $id . $record['PRS_ID'] ."##";;
		$name = $name . iconv('EUC-KR','UTF-8',$record['PRS_NAME']) ."##";
		$team = $team . iconv('EUC-KR','UTF-8',$record['PRS_TEAM']) ."##";
		$position = $position . iconv('EUC-KR','UTF-8',$record['PRS_POSITION']) ."##";
	}

	$id_ex = explode("##",$id);
	$name_ex = explode("##",$name);
	$team_ex = explode("##",$team);
	$position_ex = explode("##",$position);

	$no = 1;
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
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY1 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY1, --점심식비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY2 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY2, --저녁식비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY3 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY3, --간식비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY4 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY4, --교통비
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY5 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY5, --파견교통비(출근)
							(SELECT COUNT(A.SEQNO) 
								FROM DF_CHECKTIME A WITH(NOLOCK) INNER JOIN DF_PERSON B WITH(NOLOCK) ON A.PRS_ID = B.PRS_ID 
								WHERE A.PRS_ID = P.PRS_ID AND A.PAY6 = 'Y' AND A.DATE LIKE '". $date ."%') AS PAY6 --파견교통비(퇴근)
						FROM 
							DF_PERSON P WITH(NOLOCK)
						WHERE
							P.PRS_ID = $id_ex[$i]
					) T";
			$sql= iconv('UTF-8','EUC-KR',$sql);
			$rs = sqlsrv_query($dbConn, $sql);

			$record = sqlsrv_fetch_array($rs);
			if (sizeof($record) > 0)
			{
				$pay1 = $record['PAY1'];	//점심식비
				$pay2 = $record['PAY2'];	//저녁식비
				$pay3 = $record['PAY3'];	//간식비
				$pay4 = $record['PAY4'];	//교통비
				$pay5 = $record['PAY5'];	//파견교통비(출근)
				$pay6 = $record['PAY6'];	//파견교통비(퇴근)

				$pay_t = $pay5 + $pay6;

				if ($pay1+$pay2+$pay3+$pay5+$pay6 > 0)
				{
					$pay_total = "\\". number_format($pay1*6000+$pay2*6000+$pay3*3000+$pay5*2000+$pay6*2000);
				}
				else
				{
					$pay_total = "";
				}

				$j = $no+1;

				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A$j",$no)
							->setCellValue("B$j",$name_ex[$i])
							->setCellValue("C$j",$position_ex[$i])
							->setCellValue("D$j",$team_ex[$i])
							->setCellValue("E$j",$pay1)
							->setCellValue("F$j",$pay2)
							->setCellValue("G$j",$pay3)
							->setCellValue("H$j",$pay_t)
							->setCellValue("I$j",$pay_total);
			}
		}
		$no++;
	}
	$objPHPExcel->getActiveSheet()->setTitle("비용정산");
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=비용정산_'. date("Ym") .'.xls');
//	header("Content-Type:text/html; charset=euc-kr");
//	header("Content-Encoding:euc-kr");
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
?>
