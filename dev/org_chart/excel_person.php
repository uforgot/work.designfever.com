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

	$sheet->getColumnDimension('A')->setWidth(15);	
	$sheet->getColumnDimension('B')->setAutoSize(true);
	$sheet->getColumnDimension('C')->setWidth(10);	
	$sheet->getColumnDimension('D')->setWidth(10);		
	$sheet->getColumnDimension('E')->setAutoSize(true);	
	$sheet->getColumnDimension('F')->setAutoSize(true);
	$sheet->getColumnDimension('G')->setAutoSize(true);

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1','이름')
				->setCellValue('B1','팀명')
				->setCellValue('C1','직책')
				->setCellValue('D1','직급')
				->setCellValue('E1','내선번호')
				->setCellValue('F1','DF E-mail')
				->setCellValue('G1','핸드폰')
				->setCellValue('H1','생년월일');

	$orderby1 = "";
	$orderby2 = "";
	$orderby3 = "";
	$orderbycase = "";

	$sql = "SELECT SORT, TEAM FROM DF_TEAM_2018 WITH(NOLOCK) ORDER BY SORT";
	$sql = iconv('UTF-8','EUC-KR',$sql);
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby1 .= "WHEN PRS_TEAM ='". iconv('EUC-KR','UTF-8',$record['TEAM']) ."' THEN ". $record['SORT'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION2_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$sql = iconv('UTF-8','EUC-KR',$sql);
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby2 .= "WHEN PRS_POSITION2 ='". iconv('EUC-KR','UTF-8',$record['POSITION']) ."' THEN ". $record['SEQNO'] ." ";
	}

	$sql = "SELECT SEQNO, POSITION FROM DF_POSITION1_2018 WITH(NOLOCK) ORDER BY SEQNO";
	$sql = iconv('UTF-8','EUC-KR',$sql);
	$rs = sqlsrv_query($dbConn,$sql);

	while($record=sqlsrv_fetch_array($rs))
	{
		$orderby3 .= "WHEN PRS_POSITION1 ='". iconv('EUC-KR','UTF-8',$record['POSITION']) ."' THEN ". $record['SEQNO'] ." ";
	}

	$orderbycase .= " ORDER BY CASE ". $orderby1 . " END, CASE ". $orderby2 . " END, CASE ". $orderby3 . " END, PRS_NAME";

	$sql = "SELECT 
				PRS_ID, PRS_NAME, PRS_TEAM, PRS_POSITION1, PRS_POSITION2, PRS_EMAIL, PRS_EXTENSION, PRS_MOBILE, PRS_BIRTH, PRS_BIRTH_TYPE
			FROM 
				DF_PERSON WITH(NOLOCK)
			WHERE 
				PRF_ID IN (1,2,3,4) AND PRS_ID NOT IN (102)" . $orderbycase;

	$sql = iconv('UTF-8','EUC-KR',$sql);
	$rs = sqlsrv_query($dbConn, $sql);

	$i = 2;
	while ($record = sqlsrv_fetch_array($rs))
	{
		$col_id = $record['PRS_ID'];
		$col_name = iconv('EUC-KR','UTF-8',$record['PRS_NAME']);
		$col_team = iconv('EUC-KR','UTF-8',$record['PRS_TEAM']);
		$col_position1 = iconv('EUC-KR','UTF-8',$record['PRS_POSITION1']);
		$col_position2 = iconv('EUC-KR','UTF-8',$record['PRS_POSITION2']);
		$col_email = $record['PRS_EMAIL'];
		$col_extension = $record['PRS_EXTENSION'];
		$col_mobile = $record['PRS_MOBILE'];
		$col_birth = $record['PRS_BIRTH'];
		$col_birth_type = iconv('EUC-KR','UTF-8',$record['PRS_BIRTH_TYPE']);

		if ($col_birth_type == "음력") 
		{
			$col_birth = $col_birth ."(음)";
		}

		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i",$col_name)
					->setCellValue("B$i",$col_team)
					->setCellValue("C$i",$col_position2)
					->setCellValue("D$i",$col_position1)
					->setCellValue("E$i",$col_extension)
					->setCellValue("F$i",$col_email)
					->setCellValue("G$i",$col_mobile)
					->setCellValue("H$i",$col_birth);
		$i++;
	}

	$objPHPExcel->getActiveSheet()->setTitle("조직도");
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=조직도_'. date("Ym") .'.xls');
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
